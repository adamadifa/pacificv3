<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Cabang;
use App\Models\Komisitargetqtydetail;
use App\Models\Salesman;
use App\Models\Targetkomisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class TargetkomisiController extends Controller
{
    protected $cabang;
    public function __construct()
    {
        // Fetch the Site Settings object
        $this->middleware(function ($request, $next) {
            $this->cabang = Auth::user()->kode_cabang;
            return $next($request);
        });


        View::share('cabang', $this->cabang);
    }
    public function index(Request $request)
    {
        $tahunini = date("Y");
        $query = Targetkomisi::query();
        $query->select('komisi_target.kode_target', 'bulan', 'tahun', 'kp', 'mm', 'em', 'direktur');
        if ($this->cabang == "PCF") {
            $query->leftJoin(
                DB::raw("(
                    SELECT kode_target,
                    SUM(IF(kp IS NULL,1,0)) as kp,
                    SUM(IF(mm IS NULL,1,0)) as mm,
                    SUM(IF(em IS NULL,1,0)) as em,
                    SUM(IF(direktur IS NULL,1,0)) as direktur
                    FROM komisi_target_qty_detail
                    INNER JOIN karyawan ON komisi_target_qty_detail.id_karyawan = karyawan.id_karyawan
                    GROUP BY kode_target
                ) detail"),
                function ($join) {
                    $join->on('komisi_target.kode_target', '=', 'detail.kode_target');
                }
            );
        } else {
            $query->leftJoin(
                DB::raw("(
                    SELECT kode_target,
                    SUM(IF(kp IS NULL,1,0)) as kp,
                    SUM(IF(mm IS NULL,1,0)) as mm,
                    SUM(IF(em IS NULL,1,0)) as em,
                    SUM(IF(direktur IS NULL,1,0)) as direktur
                    FROM komisi_target_qty_detail
                    INNER JOIN karyawan ON komisi_target_qty_detail.id_karyawan = karyawan.id_karyawan
                    WHERE karyawan.kode_cabang ='$this->cabang'
                    GROUP BY kode_target
                ) detail"),
                function ($join) {
                    $join->on('komisi_target.kode_target', '=', 'detail.kode_target');
                }
            );
        }
        if (!empty($request->tahun)) {
            $query->where('tahun', $request->tahun);
            lockyear($request->tahun);
        } else {
            $query->where('tahun', $tahunini);
        }

        $query->orderBy('bulan');
        $target = $query->get();

        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('targetkomisi.index', compact('target', 'bulan'));
    }

    public function detailapprovecabang(Request $request)
    {
        $detail = DB::table('komisi_target_qty_detail')
            ->selectRaw('kode_cabang,SUM(IF(kp IS NULL,1,0)) as kp')
            ->join('karyawan', 'komisi_target_qty_detail.id_karyawan', '=', 'karyawan.id_karyawan')
            ->where('kode_target', $request->kode_target)
            ->groupBy('kode_cabang')
            ->get();

        return view('targetkomisi.detailapprovecabang', compact('detail'));
    }

    public function create(Request $request)
    {
        $kode_target = $request->kode_target;
        $cabangaktif = $this->cabang;
        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('targetkomisi.create', compact('kode_target', 'cabang', 'cabangaktif'));
    }

    public function getlisttarget(Request $request)
    {
        $listtarget = DB::table('karyawan')
            ->selectRaw('karyawan.id_karyawan,nama_karyawan,ab,ar,ase,bb,cg,cgg,dep,ds,sp,cg5,sc,sp8,sp500,br20,p1000')
            ->leftJoin(
                DB::raw("(
                SELECT id_karyawan,
                SUM(IF(kode_produk='AB',jumlah_target,0)) as ab,
                SUM(IF(kode_produk='AR',jumlah_target,0)) as ar,
                SUM(IF(kode_produk='AS',jumlah_target,0)) as ase,
                SUM(IF(kode_produk='BB',jumlah_target,0)) as bb,
                SUM(IF(kode_produk='CG',jumlah_target,0)) as cg,
                SUM(IF(kode_produk='CGG',jumlah_target,0)) as cgg,
                SUM(IF(kode_produk='DEP',jumlah_target,0)) as dep,
                SUM(IF(kode_produk='DS',jumlah_target,0)) as ds,
                SUM(IF(kode_produk='SP',jumlah_target,0)) as sp,
                SUM(IF(kode_produk='CG5',jumlah_target,0)) as cg5,
                SUM(IF(kode_produk='SC',jumlah_target,0)) as sc,
                SUM(IF(kode_produk='SP8',jumlah_target,0)) as sp8,
                SUM(IF(kode_produk='SP500',jumlah_target,0)) as sp500,
                SUM(IF(kode_produk='BR20',jumlah_target,0)) as br20,
                SUM(IF(kode_produk='P1000',jumlah_target,0)) as p1000
                FROM komisi_target_qty_detail
                WHERE kode_target = '$request->kode_target'
                GROUP BY id_karyawan
            ) detailtarget"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'detailtarget.id_karyawan');
                }
            )
            ->where('kode_cabang', $request->kode_cabang)
            ->where('karyawan.status_aktif_sales', '1')
            ->where('karyawan.nama_karyawan', '!=', '-')
            ->get();

        $cektarget = DB::table('komisi_target_qty_detail')
            ->selectRaw('kode_target, SUM(IF(kp IS NULL,1,0)) as kp')
            ->groupBy('kode_target')
            ->where('kode_target', $request->kode_target)
            ->first();
        return view('targetkomisi.getlisttarget', compact('listtarget', 'cektarget'));
    }

    public function store(Request $request)
    {
        $kode_target = $request->kode_target;
        $id_karyawan = $request->id_karyawan;
        $kode_produk = $request->kode_produk;
        $jmltarget = $request->jmltarget;

        $cek = DB::table('komisi_target_qty_detail')->where('kode_target', $kode_target)->where('id_karyawan', $id_karyawan)->where('kode_produk', $kode_produk)->count();
        if (empty($cek)) {
            echo 1;
            $data = [
                'kode_target' => $kode_target,
                'id_karyawan' => $id_karyawan,
                'kode_produk' => $kode_produk,
                'jumlah_target' => $jmltarget
            ];

            DB::table('komisi_target_qty_detail')->insert($data);
        } else {
            echo 2;
            $dataupdate = [
                'jumlah_target' => $jmltarget
            ];
            DB::table('komisi_target_qty_detail')->where('kode_target', $kode_target)->where('id_karyawan', $id_karyawan)->where('kode_produk', $kode_produk)->update($dataupdate);
        }
    }

    public function generatecashin($kode_target)
    {
        $kode_target = Crypt::decrypt($kode_target);
        $target = DB::table('komisi_target')->where('kode_target', $kode_target)->first();
        $tanggal = $target->tahun . "-" . $target->bulan . "-01";
        if ($tanggal > "2021-12-31") {
            $detailtarget = DB::table('komisi_target_qty_detail')
                ->selectRaw('komisi_target_qty_detail.kode_target,komisi_target_qty_detail.id_karyawan,ROUND(SUM((jumlah_target*harga_dus) - ((jumlah_target*harga_dus) * 0.025))) as targetcashin')
                ->join('karyawan', 'komisi_target_qty_detail.id_karyawan', '=', 'karyawan.id_karyawan')

                ->join('barang', function ($join) {
                    $join->on('komisi_target_qty_detail.kode_produk', '=', 'barang.kode_produk')
                        ->on('karyawan.kode_cabang', '=', 'barang.kode_cabang')
                        ->on('karyawan.kategori_salesman', '=', 'barang.kategori_harga');
                })
                ->where('kode_target', $kode_target)
                ->groupBy('komisi_target_qty_detail.kode_target', 'komisi_target_qty_detail.id_karyawan')
                ->get();
        }
        $berhasil = 0;
        $gagal = 0;

        $berhasilupdate = 0;
        $gagalupdate = 0;
        DB::beginTransaction();
        try {
            foreach ($detailtarget as $d) {
                $kode_target = $d->kode_target;
                $id_karyawan = $d->id_karyawan;
                $jumlah_cashin = $d->targetcashin;
                $cek = DB::table('komisi_target_cashin_detail')->where('kode_target', $kode_target)->where('id_karyawan', $id_karyawan)->count();
                if (empty($cek)) {
                    $data = [
                        'kode_target' => $kode_target,
                        'id_karyawan' => $id_karyawan,
                        'jumlah_target_cashin' => $jumlah_cashin
                    ];

                    $simpantarget = DB::table('komisi_target_cashin_detail')->insert($data);
                    if ($simpantarget) {
                        $berhasil += 1;
                        $gagal += 0;
                    } else {
                        $berhasil += 0;
                        $gagal += 1;
                    }
                } else {
                    $dataupdate = [
                        'jumlah_target_cashin' => $jumlah_cashin
                    ];
                    $updatetarget = DB::table('komisi_target_cashin_detail')->where('kode_target', $kode_target)->where('id_karyawan', $id_karyawan)->update($dataupdate);
                    if ($updatetarget) {
                        $berhasilupdate += 1;
                        $gagalupdate += 0;
                    } else {
                        $berhasilupdate += 0;
                        $gagalupdate += 1;
                    }
                }
            }
            DB::commit();
            //die;
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update  ']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return  Redirect::back()->with(['warning' => 'Data Gagal Di Update Hubungi Tim IT']);
        }
    }

    public function show(Request $request)
    {

        $query = Komisitargetqtydetail::query();

        $query->selectRaw("komisi_target_qty_detail.id_karyawan,nama_karyawan,kode_cabang,jumlah_target_cashin,
            SUM(IF(kode_produk ='AB',jumlah_target,0)) as 'AB',
            SUM(IF(kode_produk ='AR',jumlah_target,0)) as 'AR',
            SUM(IF(kode_produk ='AS',jumlah_target,0)) as 'AS',
            SUM(IF(kode_produk ='BB',jumlah_target,0)) as 'BB',
            SUM(IF(kode_produk ='CG',jumlah_target,0)) as 'CG',
            SUM(IF(kode_produk ='CG5',jumlah_target,0)) as 'CG5',
            SUM(IF(kode_produk ='DEP',jumlah_target,0)) as 'DEP',
            SUM(IF(kode_produk ='DK',jumlah_target,0)) as 'DK',
            SUM(IF(kode_produk ='DS',jumlah_target,0)) as 'DS',
            SUM(IF(kode_produk ='SP',jumlah_target,0)) as 'SP',
            SUM(IF(kode_produk ='SC',jumlah_target,0)) as 'SC',
            SUM(IF(kode_produk ='SP8',jumlah_target,0)) as 'SP8',
            SUM(IF(kode_produk ='SP500',jumlah_target,0)) as 'SP500',
            SUM(IF(kode_produk ='BR20',jumlah_target,0)) as 'BR20',
            SUM(IF(kode_produk ='P1000',jumlah_target,0)) as 'P1000'
            ");
        $query->join('komisi_target', 'komisi_target_qty_detail.kode_target', '=', 'komisi_target.kode_target');
        $query->join('karyawan', 'komisi_target_qty_detail.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->leftJoin('komisi_target_cashin_detail', function ($join) {
            $join->on('komisi_target_qty_detail.kode_target', '=', 'komisi_target_cashin_detail.kode_target')
                ->on('komisi_target_qty_detail.id_karyawan', '=', 'komisi_target_cashin_detail.id_karyawan');
        });
        $query->where('komisi_target_qty_detail.kode_target', $request->kode_target);
        $query->groupByRaw('komisi_target_qty_detail.id_karyawan,nama_karyawan,kode_cabang,jumlah_target_cashin');

        if (Auth::user()->kode_cabang != "PCF") {
            $query->where('karyawan.kode_cabang', Auth::user()->kode_cabang);
        } else {
            $wilayah = Auth::user()->wilayah;
            if (!empty($wilayah)) {
                $wilayah_user = unserialize($wilayah);
                $query->whereIn('karyawan.kode_cabang', $wilayah_user);
            }
        }



        $query->orderBy('karyawan.kode_cabang');
        $query->orderBy('nama_karyawan');
        $target = $query->get();
        $kodetarget = $request->kode_target;
        return view('targetkomisi.show', compact('target', 'kodetarget'));
    }

    public function loadkoreksitarget(Request $request)
    {
        $kode_target = $request->kode_target;
        $kode_produk = $request->kode_produk;
        $id_karyawan = $request->id_karyawan;
        $salesman = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->first();
        $target = DB::table('komisi_target_qty_detail')
            ->select('komisi_target_qty_detail.*')
            ->where('kode_target', $kode_target)
            ->where('kode_produk', $kode_produk)
            ->where('komisi_target_qty_detail.id_karyawan', $id_karyawan)
            ->first();

        return view('targetkomisi.koreksitarget', compact('target', 'kode_target', 'kode_produk', 'id_karyawan', 'salesman'));
    }

    public function update(Request $request)
    {
        $kode_target = $request->kode_target;
        $kode_produk = $request->kode_produk;
        $id_karyawan = $request->id_karyawan;
        $jmltarget = $request->jmltarget;
        DB::beginTransaction();
        try {
            $cek = DB::table('komisi_target_qty_detail')->where('kode_target', $kode_target)->where('id_karyawan', $id_karyawan)->where('kode_produk', $kode_produk)->count();
            if (empty($cek)) {
                $cekapprove = DB::table('komisi_target_qty_detail')->where('kode_target', $kode_target)->where('id_karyawan', $id_karyawan)->first();
                $data = [
                    'kode_target' => $kode_target,
                    'id_karyawan' => $id_karyawan,
                    'kode_produk' => $kode_produk,
                    'jumlah_target' => $jmltarget,
                    'kp' => $cekapprove->kp,
                    'mm' => $cekapprove->mm,
                    'em' => $cekapprove->em,
                    'direktur' => $cekapprove->direktur
                ];
                DB::table('komisi_target_qty_detail')->insert($data);
            } else {
                $data = [
                    'jumlah_target' => $jmltarget
                ];
                DB::table('komisi_target_qty_detail')->where('kode_target', $kode_target)->where('kode_produk', $kode_produk)->where('id_karyawan', $id_karyawan)->update($data);
            }

            $target = DB::table('komisi_target')->where('kode_target', $kode_target)->first();
            $tanggal = $target->tahun . "-" . $target->bulan . "-01";
            if ($tanggal > "2021-12-31") {
                $cashin = DB::table('komisi_target_qty_detail')
                    ->selectRaw('komisi_target_qty_detail.kode_target,komisi_target_qty_detail.id_karyawan,ROUND(SUM((jumlah_target*harga_dus) - ((jumlah_target*harga_dus) * 0.025))) as targetcashin')
                    ->join('karyawan', 'komisi_target_qty_detail.id_karyawan', '=', 'karyawan.id_karyawan')

                    ->join('barang', function ($join) {
                        $join->on('komisi_target_qty_detail.kode_produk', '=', 'barang.kode_produk')
                            ->on('karyawan.kode_cabang', '=', 'barang.kode_cabang')
                            ->on('karyawan.kategori_salesman', '=', 'barang.kategori_harga');
                    })
                    ->where('kode_target', $kode_target)
                    ->where('komisi_target_qty_detail.id_karyawan', $id_karyawan)
                    ->groupBy('komisi_target_qty_detail.kode_target', 'komisi_target_qty_detail.id_karyawan')
                    ->first();

                $jumlah_cashin = $cashin->targetcashin;
                $datacashin = [
                    'jumlah_target_cashin' => $jumlah_cashin
                ];

                DB::table('komisi_target_cashin_detail')->where('kode_target', $kode_target)->where('id_karyawan', $id_karyawan)->update($datacashin);
            }
            DB::commit();
            //die;
            echo 0;
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            echo 1;
        }
    }

    public function approvetarget($kode_target, $kode_cabang)
    {
        $kode_target = Crypt::decrypt($kode_target);
        $kode_cabang = Crypt::decrypt($kode_cabang);
        $time = date("Y-m-d H:i:s");
        $level_user = Auth::user()->level;
        if ($level_user == "kepala penjualan") {
            $field1 = "kp";
            $field2 = "time_kp";
        } else if ($level_user == "manager marketing") {
            $field1 = "mm";
            $field2 = "time_mm";
        } else if ($level_user == "general manager") {
            $field1 = "em";
            $field2 = "time_mm";
        } else if ($level_user == "Administrator") {
            $field1 = "direktur";
            $field2 = "time_direktur";
        }

        $data = [
            $field1 => 1,
            $field2 => $time
        ];

        if ($level_user == "kepala penjualan") {
            $update = DB::table('komisi_target_qty_detail')
                ->join('karyawan', 'komisi_target_qty_detail.id_karyawan', '=', 'karyawan.id_karyawan')
                ->join('komisi_target', 'komisi_target_qty_detail.kode_target', '=', 'komisi_target.kode_target')
                ->where('komisi_target_qty_detail.kode_target', $kode_target)
                ->where('karyawan.kode_cabang', $kode_cabang)
                ->update($data);
        } else {
            $update = DB::table('komisi_target_qty_detail')
                ->join('karyawan', 'komisi_target_qty_detail.id_karyawan', '=', 'karyawan.id_karyawan')
                ->join('komisi_target', 'komisi_target_qty_detail.kode_target', '=', 'komisi_target.kode_target')
                ->where('komisi_target_qty_detail.kode_target', $kode_target)
                ->update($data);
        }


        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di Approve  ']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Approve Hubungi Tim IT  ']);
        }
    }

    public function canceltarget($kode_target, $kode_cabang)
    {
        $kode_target = Crypt::decrypt($kode_target);
        $kode_cabang = Crypt::decrypt($kode_cabang);
        $time = date("Y-m-d H:i:s");
        $level_user = Auth::user()->level;
        if ($level_user == "kepala penjualan") {
            $field1 = "kp";
            $field2 = "time_kp";
        } else if ($level_user == "manager marketing") {
            $field1 = "mm";
            $field2 = "time_mm";
        } else if ($level_user == "general manager") {
            $field1 = "em";
            $field2 = "time_mm";
        } else if ($level_user == "Administrator") {
            $field1 = "direktur";
            $field2 = "time_direktur";
        }

        $data = [
            $field1 => NULL,
            $field2 => $time
        ];

        if ($level_user == "kepala penjualan") {
            $update = DB::table('komisi_target_qty_detail')
                ->join('karyawan', 'komisi_target_qty_detail.id_karyawan', '=', 'karyawan.id_karyawan')
                ->join('komisi_target', 'komisi_target_qty_detail.kode_target', '=', 'komisi_target.kode_target')
                ->where('komisi_target_qty_detail.kode_target', $kode_target)
                ->where('karyawan.kode_cabang', $kode_cabang)
                ->update($data);
        } else {
            $update = DB::table('komisi_target_qty_detail')
                ->join('karyawan', 'komisi_target_qty_detail.id_karyawan', '=', 'karyawan.id_karyawan')
                ->join('komisi_target', 'komisi_target_qty_detail.kode_target', '=', 'komisi_target.kode_target')
                ->where('komisi_target_qty_detail.kode_target', $kode_target)
                ->update($data);
        }


        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di Cancel  ']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Cancel Hubungi Tim IT  ']);
        }
    }

    public function laporankomisi()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('targetkomisi.laporan.frm.lap_komisi', compact('cabang', 'bulan'));
    }

    public function rekapkomisi()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('targetkomisi.laporan.frm.lap_rekapkomisi', compact('bulan'));
    }


    public function cetakkomisi(Request $request)
    {
        $cabang = $request->kode_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        lockyear($tahun);
        $aturankomisi = $request->aturankomisi;
        $dari = $tahun . "-" . $bulan  . "-01";
        $hariini = date("Y-m-d");
        $tglkomisi = $tahun . "-" . $bulan . "-01";
        $sampai = date('Y-m-t', strtotime($dari));

        if ($hariini < $sampai) {
            $sampai = $hariini;
        } else {
            $sampai = $sampai;
        }


        $lastmonth = date('Y-m-d', strtotime(date($dari) . '- 1 month'));
        $lastdate = explode("-", $lastmonth);
        $bulanlast = $lastdate[1] + 0;
        $tahunlast = $lastdate[0];
        if ($bulanlast == 1) {
            $blnlast1 = 12;
            $thnlast1 = $tahun - 1;
        } else {
            $blnlast1 = $bulanlast - 1;
            $thnlast1 = $tahun;
        }


        if ($bulan == 12) {
            $bln = 1;
            $thn = $tahun + 1;
        } else {
            $bln = $bulan + 1;
            $thn = $tahun;
        }


        $ceknextBulan = DB::table('setoran_pusat')->where('omset_bulan', $bulan)->where('omset_tahun', $tahun)
            ->whereRaw('MONTH(tgl_diterimapusat) = ' . $bln)
            ->whereRaw('YEAR(tgl_diterimapusat) = ' . $thn)
            ->where('kode_cabang', $cabang)
            ->orderBy('tgl_diterimapusat', 'desc')
            ->first();
        if ($ceknextBulan ==  null) {
            $end = date("Y-m-t", strtotime($dari));
        } else {
            $end = $ceknextBulan->tgl_diterimapusat;
        }

        $produk = Barang::orderBy('kode_produk')->get();
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $cbg = DB::table('cabang')->where('kode_cabang', $cabang)->first();

        $query = Salesman::query();
        $query->selectRaw('
            karyawan.id_karyawan,nama_karyawan,kategori_salesman,
            target_BB_DP,
            BB,
            retur_BB,
            DEP,
            retur_DEP,
            target_DS,
            DS,
            retur_DS,
            SP8,
            retur_SP8,
            target_SP,
            SP,
            retur_SP,
            SP500,
            retur_SP500,
            target_SC,
            SC,
            retur_SC,
            target_AR,
            AR,
            retur_AR,
            target_AB_AS_CG5,
            AB,
            retur_AB,
            `AS`,
            retur_AS,
            CG5,
            retur_CG5,
            realisasi_cashin,
            sisapiutangsaldo + sisapiutang as sisapiutang,
            cashin_jt
            ');
        $query->join(
            DB::raw("(
                    SELECT  id_karyawan,
                    SUM(IF(kategori_komisi='KKQ01',jumlah_target,0)) as target_BB_DP,
                    SUM(IF(kategori_komisi='KKQ02',jumlah_target,0)) as target_DS,
                    SUM(IF(kategori_komisi='KKQ03',jumlah_target,0)) as target_SP,
                    SUM(IF(kategori_komisi='KKQ04',jumlah_target,0)) as target_AR,
                    SUM(IF(kategori_komisi='KKQ05',jumlah_target,0)) as target_AB_AS_CG5,
                    SUM(IF(kategori_komisi='KKQ06',jumlah_target,0)) as target_SC
                    FROM
                    komisi_target_qty_detail k_detail
                    INNER JOIN komisi_target ON k_detail.kode_target = komisi_target.kode_target
                    INNER JOIN master_barang ON k_detail.kode_produk = master_barang.kode_produk
                    WHERE bulan ='$bulan' AND tahun='$tahun'
                    GROUP BY id_karyawan
                    ) komisi"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'komisi.id_karyawan');
            }
        );
        $query->leftJoin(
            DB::raw("(
                    SELECT historibayar.id_karyawan,SUM(bayar) as cashin_jt
                    FROM historibayar
                    INNER JOIN penjualan ON historibayar.no_fak_penj = penjualan.no_fak_penj
                    WHERE tglbayar BETWEEN '$dari' AND '$sampai' AND status_bayar IS NULL
                    AND datediff(tglbayar, tgltransaksi) > 15
                    GROUP BY historibayar.id_karyawan
                ) hbjt"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'hbjt.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT
                    salesbarunew,IFNULL(SUM(penjualan.total),0) - SUM(IFNULL( totalretur, 0 )) - SUM(IFNULL( totalbayar, 0 )) AS sisapiutang
                FROM
                penjualan
                LEFT JOIN (
                    SELECT
                        pj.no_fak_penj,IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,karyawan.nama_karyawan AS nama_sales,
                        IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                    FROM
                        penjualan pj
                    INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                    LEFT JOIN (
                        SELECT
                            id_move,
                            no_fak_penj,
                            move_faktur.id_karyawan AS salesbaru,
                            karyawan.kode_cabang AS cabangbaru
                        FROM
                            move_faktur
                        INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                        WHERE id_move IN ( SELECT max( id_move ) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj )
                        ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj )
                ) pjmove ON ( penjualan.no_fak_penj = pjmove.no_fak_penj )
                LEFT JOIN (
                    SELECT
                        retur.no_fak_penj AS no_fak_penj,
                        SUM(IFNULL( subtotal_pf, 0 ) - IFNULL( subtotal_gb, 0 )) AS totalretur
                    FROM
                        retur
                    WHERE
                        tglretur BETWEEN '$dari' AND '$sampai'
                    GROUP BY
                        retur.no_fak_penj
                ) r ON ( penjualan.no_fak_penj = r.no_fak_penj )
                LEFT JOIN (
                    SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar
                    FROM historibayar
                    WHERE tglbayar BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                ) hb ON ( penjualan.no_fak_penj = hb.no_fak_penj )

            WHERE penjualan.tgltransaksi BETWEEN '$dari' AND '$sampai' AND jenistransaksi = 'kredit' AND datediff( '$sampai', penjualan.tgltransaksi ) > 15
            GROUP BY
                salesbarunew

            ) penj"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'penj.salesbarunew');
            }
        );


        $query->leftJoin(
            DB::raw("(
                SELECT
                    salesbarunew,IFNULL( SUM(jumlah ), 0 ) - SUM(IFNULL( totalretur, 0 )) - SUM(IFNULL( totalbayar, 0 )) AS sisapiutangsaldo
                FROM
                saldoawal_piutang_faktur spf
                INNER JOIN penjualan ON spf.no_fak_penj = penjualan.no_fak_penj
                LEFT JOIN (
                        SELECT
                            pj.no_fak_penj,IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,karyawan.nama_karyawan AS nama_sales,
                            IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                        FROM
                            penjualan pj
                        INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                        LEFT JOIN (
                            SELECT
                                id_move,
                                no_fak_penj,
                                move_faktur.id_karyawan AS salesbaru,
                                karyawan.kode_cabang AS cabangbaru
                            FROM
                                move_faktur
                            INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                            WHERE id_move IN ( SELECT max( id_move ) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj )
                            ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj )
                    ) pjmove ON ( penjualan.no_fak_penj = pjmove.no_fak_penj )
                    LEFT JOIN (
                        SELECT
                            retur.no_fak_penj AS no_fak_penj,
                            SUM(IFNULL( subtotal_pf, 0 ) - IFNULL( subtotal_gb, 0 )) AS totalretur
                        FROM
                            retur
                        WHERE
                            tglretur BETWEEN '$dari' AND '$sampai'
                        GROUP BY
                            retur.no_fak_penj
                    ) r ON ( penjualan.no_fak_penj = r.no_fak_penj )
                    LEFT JOIN (
                        SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar
                        FROM historibayar
                        WHERE tglbayar BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                    ) hb ON ( penjualan.no_fak_penj = hb.no_fak_penj )
                WHERE
                    datediff( '$sampai', penjualan.tgltransaksi ) > 15 AND bulan = '$bulan' AND tahun = '$tahun'
                GROUP BY
                    salesbarunew
        ) spf"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'spf.salesbarunew');
            }
        );

        $query->leftJoin(
            DB::raw("(
            SELECT karyawan.id_karyawan,
            (IFNULL(jml_belumsetorbulanlalu,0)+IFNULL(totalsetoran,0)) + IFNULL(jml_gmlast,0) - IFNULL(jml_gmnow,0) - IFNULL(jml_belumsetorbulanini,0) as realisasi_cashin
            FROM karyawan
            LEFT JOIN (
                SELECT id_karyawan,jumlah as jml_belumsetorbulanlalu FROM belumsetor_detail
                INNER JOIN belumsetor ON belumsetor_detail.kode_saldobs = belumsetor.kode_saldobs
                WHERE bulan='$bulanlast' AND tahun='$tahunlast'
            ) bs ON (karyawan.id_karyawan = bs.id_karyawan)

            LEFT JOIN (
                SELECT id_karyawan, SUM(lhp_tunai+lhp_tagihan) as totalsetoran FROM setoran_penjualan WHERE tgl_lhp BETWEEN '$dari' AND '$sampai' GROUP BY id_karyawan
            ) sp ON (karyawan.id_karyawan = sp.id_karyawan)

            LEFT JOIN (
                SELECT
                IFNULL(hb.id_karyawan,giro.id_karyawan) as id_karyawan,
                SUM( jumlah ) AS jml_gmlast
                FROM
                giro
                INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                LEFT JOIN ( SELECT id_giro,id_karyawan FROM historibayar GROUP BY id_giro,id_karyawan ) AS hb ON giro.id_giro = hb.id_giro
                WHERE
                MONTH ( tgl_giro ) = '$bulanlast'
                AND YEAR ( tgl_giro ) = '$tahunlast'
                AND omset_tahun = '$tahun'
                AND omset_bulan = '$bulan'
                OR  MONTH ( tgl_giro ) = '$blnlast1'
                AND YEAR ( tgl_giro ) = '$thnlast1'
                AND omset_tahun = '$tahun'
                AND omset_bulan = '$bulan'
                GROUP BY
                IFNULL( hb.id_karyawan, giro.id_karyawan )
            ) gmlast ON (karyawan.id_karyawan = gmlast.id_karyawan)
            LEFT JOIN (
            SELECT
                IFNULL(hb.id_karyawan,giro.id_karyawan) as id_karyawan,
                SUM( jumlah ) AS jml_gmnow
            FROM
                giro
                INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                LEFT JOIN ( SELECT id_giro,id_karyawan, tglbayar FROM historibayar GROUP BY id_giro, tglbayar,id_karyawan ) AS hb ON giro.id_giro = hb.id_giro
            WHERE
                tgl_giro >= '$dari'
                AND tgl_giro <= '$sampai' AND tglbayar IS NULL AND omset_bulan = '0' AND omset_tahun = ''
                OR  tgl_giro >= '$dari'
                AND tgl_giro <= '$sampai' AND tglbayar >= '$end'
                AND omset_bulan > '$bulan'
                AND omset_tahun >= '$tahun'
            GROUP BY
                giro.id_karyawan,
                hb.id_karyawan
            ) gmnow ON (karyawan.id_karyawan = gmnow.id_karyawan)

            LEFT JOIN (
                SELECT belumsetor_detail.id_karyawan, SUM(jumlah) as jml_belumsetorbulanini
                FROM belumsetor_detail
                INNER JOIN belumsetor ON belumsetor_detail.kode_saldobs = belumsetor.kode_saldobs
                WHERE bulan ='$bulan' AND tahun ='$tahun' GROUP BY id_karyawan
            ) bsnow ON (karyawan.id_karyawan = bsnow.id_karyawan)
            ) hb"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'hb.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
            SELECT salesbarunew,
            SUM(IF(kode_produk = 'AB' AND promo !='1' OR kode_produk = 'AB' AND promo IS NULL,jumlah,0)) as AB,
            SUM(IF(kode_produk = 'AR' AND promo !='1' OR kode_produk = 'AR' AND promo IS NULL,jumlah,0)) as AR,
            SUM(IF(kode_produk = 'AS' AND promo !='1' OR kode_produk = 'AS' AND promo IS NULL ,jumlah,0)) as `AS`,
            SUM(IF(kode_produk = 'BB' AND promo !='1' OR kode_produk = 'BB' AND promo IS NULL,jumlah,0)) as BB,
            SUM(IF(kode_produk = 'CG' AND promo !='1' OR kode_produk = 'CG' AND promo IS NULL,jumlah,0)) as CG,
            SUM(IF(kode_produk = 'CGG' AND promo !='1' OR kode_produk = 'CGG' AND promo IS NULL,jumlah,0)) as CGG,
            SUM(IF(kode_produk = 'DEP' AND promo !='1' OR kode_produk = 'DEP' AND promo IS NULL,jumlah,0)) as DEP,
            SUM(IF(kode_produk = 'DK' AND promo !='1' OR kode_produk = 'DK' AND promo IS NULL,jumlah,0)) as DK,
            SUM(IF(kode_produk = 'DS' AND promo !='1' OR kode_produk = 'DS' AND promo IS NULL,jumlah,0)) as DS,
            SUM(IF(kode_produk = 'SP' AND promo !='1' OR kode_produk = 'SP' AND promo IS NULL,jumlah,0)) as SP,
            SUM(IF(kode_produk = 'BBP' AND promo !='1' OR kode_produk = 'BBP' AND promo IS NULL,jumlah,0)) as BBP,
            SUM(IF(kode_produk = 'SPP' AND promo !='1' OR kode_produk = 'SPP' AND promo IS NULL,jumlah,0)) as SPP,
            SUM(IF(kode_produk = 'CG5' AND promo !='1' OR kode_produk = 'CG5' AND promo IS NULL,jumlah,0)) as CG5,
            SUM(IF(kode_produk = 'SP8' AND promo !='1' OR kode_produk = 'SP8' AND promo IS NULL,jumlah,0)) as SP8,
            SUM(IF(kode_produk = 'SC' AND promo !='1' OR kode_produk = 'SC' AND promo IS NULL,jumlah,0)) as SC,
            SUM(IF(kode_produk = 'SP500' AND promo !='1' OR kode_produk = 'SP500' AND promo IS NULL,jumlah,0)) as SP500
            FROM detailpenjualan
            INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
            INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang

           LEFT JOIN (
            SELECT pj.no_fak_penj,
            IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
            IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
            FROM penjualan pj
            INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
            LEFT JOIN (
                SELECT
                id_move,no_fak_penj,
                move_faktur.id_karyawan as salesbaru,
                karyawan.kode_cabang  as cabangbaru
                FROM move_faktur
                INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                WHERE id_move IN (SELECT max(id_move) FROM move_faktur WHERE tgl_move <= '$dari' GROUP BY no_fak_penj)
            ) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
           ) pjmove ON (penjualan.no_fak_penj = pjmove.no_fak_penj)


            WHERE  status_lunas ='1' AND tgl_pelunasan BETWEEN '$dari' AND '$sampai'
            GROUP BY salesbarunew
        ) realisasi"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'realisasi.salesbarunew');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT penjualan.id_karyawan,
                SUM(IF(kode_produk = 'AB',jumlah,0)) as retur_AB,
                SUM(IF(kode_produk = 'AR',jumlah,0)) as retur_AR,
                SUM(IF(kode_produk = 'AS',jumlah,0)) as `retur_AS`,
                SUM(IF(kode_produk = 'BB',jumlah,0)) as retur_BB,
                SUM(IF(kode_produk = 'CG' ,jumlah,0)) as retur_CG,
                SUM(IF(kode_produk = 'CGG',jumlah,0)) as retur_CGG,
                SUM(IF(kode_produk = 'DEP',jumlah,0)) as retur_DEP,
                SUM(IF(kode_produk = 'DK',jumlah,0)) as retur_DK,
                SUM(IF(kode_produk = 'DS',jumlah,0)) as retur_DS,
                SUM(IF(kode_produk = 'SP',jumlah,0)) as retur_SP,
                SUM(IF(kode_produk = 'BBP',jumlah,0)) as retur_BBP,
                SUM(IF(kode_produk = 'SPP',jumlah,0)) as retur_SPP,
                SUM(IF(kode_produk = 'CG5',jumlah,0)) as retur_CG5,
                SUM(IF(kode_produk = 'SP8',jumlah,0)) as retur_SP8,
                SUM(IF(kode_produk = 'SC',jumlah,0)) as retur_SC,
                SUM(IF(kode_produk = 'SP500',jumlah,0)) as retur_SP500
                FROM detailretur
                INNER JOIN retur ON detailretur.no_retur_penj = retur.no_retur_penj
                INNER JOIN penjualan ON retur.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN barang ON detailretur.kode_barang = barang.kode_barang
                WHERE  status_lunas ='1' AND tgl_pelunasan BETWEEN '$dari' AND '$sampai'
                GROUP BY penjualan.id_karyawan
            ) returpf"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'returpf.id_karyawan');
            }
        );

        if (Auth::user()->id == 27) {
            $query->whereIn('kode_cabang', ['BDG', 'PWK']);
        } else {
            $query->where('kode_cabang', $cabang);
        }
        $query->where('nama_karyawan', '!=', '');
        $komisi = $query->get();
        $nmbulan  = $namabulan[$bulan];
        return view('targetkomisi.laporan.cetak_komisi_lpu', compact('komisi', 'cbg', 'nmbulan', 'tahun', 'produk', 'bulan', 'cabang'));
    }


    public function cetakkomisimaret2023($cabang, $bulan, $tahun, $aturankomisi, $dari, $hariini, $sampai)
    {
        $lastmonth = date('Y-m-d', strtotime(date($dari) . '- 1 month'));
        $lastdate = explode("-", $lastmonth);
        $bulanlast = $lastdate[1] + 0;
        $tahunlast = $lastdate[0];
        if ($bulanlast == 1) {
            $blnlast1 = 12;
            $thnlast1 = $tahun - 1;
        } else {
            $blnlast1 = $bulanlast - 1;
            $thnlast1 = $tahun;
        }


        if ($bulan == 12) {
            $bln = 1;
            $thn = $tahun + 1;
        } else {
            $bln = $bulan + 1;
            $thn = $tahun;
        }


        $ceknextBulan = DB::table('setoran_pusat')->where('omset_bulan', $bulan)->where('omset_tahun', $tahun)
            ->whereRaw('MONTH(tgl_diterimapusat) = ' . $bln)
            ->whereRaw('YEAR(tgl_diterimapusat) = ' . $thn)
            ->where('kode_cabang', $cabang)
            ->orderBy('tgl_diterimapusat', 'desc')
            ->first();
        if ($ceknextBulan ==  null) {
            $end = date("Y-m-t", strtotime($dari));
        } else {
            $end = $ceknextBulan->tgl_diterimapusat;
        }


        $produk = Barang::orderBy('kode_produk')->get();
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $cbg = DB::table('cabang')->where('kode_cabang', $cabang)->first();

        $query = Salesman::query();
        $query->selectRaw('
            karyawan.id_karyawan,nama_karyawan,kategori_salesman,status_komisi,
            target_BB_DP,
            BB,
            retur_BB,
            DEP,
            retur_DEP,
            target_DS,
            DS,
            retur_DS,
            SP8,
            retur_SP8,
            target_SP,
            SP,
            retur_SP,
            SP500,
            retur_SP500,
            target_SC,
            SC,
            retur_SC,
            target_AR,
            AR,
            retur_AR,
            target_AB_AS_CG5,
            AB,
            retur_AB,
            `AS`,
            retur_AS,
            CG5,
            retur_CG5,
            realisasi_cashin,
            IFNULL(sisapiutangsaldo,0) + IFNULL(sisapiutang,0) as sisapiutang,
            cashin_jt,
            potongankomisi,
            komisifix,
            ket_potongan,
            ket_komisifix,
            jmlpelanggan,
            jmltrans
        ');

        $query->leftJoin(
            DB::raw("(
                SELECT id_sales, COUNT(kode_pelanggan) as jmlpelanggan
                FROM pelanggan
                WHERE status_pelanggan = '1' AND DATE(time_stamps) <= '$sampai'
                GROUP BY id_sales
            ) pelangganaktif"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'pelangganaktif.id_sales');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT id_karyawan, COUNT(DISTINCT(kode_pelanggan)) as jmltrans
                FROM penjualan
                WHERE tgltransaksi BETWEEN '$dari' AND '$sampai'
                GROUP BY id_karyawan
            ) pelanggantrans"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'pelanggantrans.id_karyawan');
            }
        );
        $query->join(
            DB::raw("(
                SELECT  id_karyawan,
                SUM(IF(kategori_komisi='KKQ01',jumlah_target,0)) as target_BB_DP,
                SUM(IF(kategori_komisi='KKQ02',jumlah_target,0)) as target_DS,
                SUM(IF(kategori_komisi='KKQ03',jumlah_target,0)) as target_SP,
                SUM(IF(kategori_komisi='KKQ04',jumlah_target,0)) as target_AR,
                SUM(IF(kategori_komisi='KKQ05',jumlah_target,0)) as target_AB_AS_CG5,
                SUM(IF(kategori_komisi='KKQ06',jumlah_target,0)) as target_SC
                FROM
                komisi_target_qty_detail k_detail
                INNER JOIN komisi_target ON k_detail.kode_target = komisi_target.kode_target
                INNER JOIN master_barang ON k_detail.kode_produk = master_barang.kode_produk
                WHERE bulan ='$bulan' AND tahun='$tahun'
                GROUP BY id_karyawan
                ) komisi"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'komisi.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT historibayar.id_karyawan,SUM(bayar) as cashin_jt
                FROM historibayar
                INNER JOIN penjualan ON historibayar.no_fak_penj = penjualan.no_fak_penj
                WHERE tglbayar BETWEEN '$dari' AND '$sampai' AND status_bayar IS NULL
                AND datediff(tglbayar, tgltransaksi) > 15
                GROUP BY historibayar.id_karyawan
            ) hbjt"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'hbjt.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT
                    salesbarunew,IFNULL(SUM(penjualan.total),0) - SUM(IFNULL( totalretur, 0 )) - SUM(IFNULL( totalbayar, 0 )) AS sisapiutang
                FROM
                penjualan
                INNER JOIN pelanggan ON penjualan.kode_pelanggan = pelanggan.kode_pelanggan
                LEFT JOIN (
                    SELECT
                        pj.no_fak_penj,IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,karyawan.nama_karyawan AS nama_sales,
                        IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                    FROM
                        penjualan pj
                    INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                    LEFT JOIN (
                        SELECT
                            id_move,
                            no_fak_penj,
                            move_faktur.id_karyawan AS salesbaru,
                            karyawan.kode_cabang AS cabangbaru
                        FROM
                            move_faktur
                        INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                        WHERE id_move IN ( SELECT max( id_move ) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj )
                        ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj )
                ) pjmove ON ( penjualan.no_fak_penj = pjmove.no_fak_penj )
                LEFT JOIN (
                    SELECT
                        retur.no_fak_penj AS no_fak_penj,
                        SUM(IFNULL( subtotal_pf, 0 ) - IFNULL( subtotal_gb, 0 )) AS totalretur
                    FROM
                        retur
                    WHERE
                        tglretur BETWEEN '$dari' AND '$sampai'
                    GROUP BY
                        retur.no_fak_penj
                ) r ON ( penjualan.no_fak_penj = r.no_fak_penj )
                LEFT JOIN (
                    SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar
                    FROM historibayar
                    WHERE tglbayar BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                ) hb ON ( penjualan.no_fak_penj = hb.no_fak_penj )

            WHERE penjualan.tgltransaksi BETWEEN '$dari' AND '$sampai' AND jenistransaksi = 'kredit' AND datediff( '$sampai', penjualan.tgltransaksi ) > IFNULL(pelanggan.jatuhtempo+1,15)
            GROUP BY
                salesbarunew

            ) penj"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'penj.salesbarunew');
            }
        );


        $query->leftJoin(
            DB::raw("(
                SELECT
                    salesbarunew,IFNULL( SUM(jumlah ), 0 ) - SUM(IFNULL( totalretur, 0 )) - SUM(IFNULL( totalbayar, 0 )) AS sisapiutangsaldo
                FROM
                saldoawal_piutang_faktur spf
                INNER JOIN penjualan ON spf.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN pelanggan ON penjualan.kode_pelanggan = pelanggan.kode_pelanggan
                LEFT JOIN (
                        SELECT
                            pj.no_fak_penj,IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,karyawan.nama_karyawan AS nama_sales,
                            IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                        FROM
                            penjualan pj
                        INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                        LEFT JOIN (
                            SELECT
                                id_move,
                                no_fak_penj,
                                move_faktur.id_karyawan AS salesbaru,
                                karyawan.kode_cabang AS cabangbaru
                            FROM
                                move_faktur
                            INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                            WHERE id_move IN ( SELECT max( id_move ) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj )
                            ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj )
                    ) pjmove ON ( penjualan.no_fak_penj = pjmove.no_fak_penj )
                    LEFT JOIN (
                        SELECT
                            retur.no_fak_penj AS no_fak_penj,
                            SUM(IFNULL( subtotal_pf, 0 ) - IFNULL( subtotal_gb, 0 )) AS totalretur
                        FROM
                            retur
                        WHERE
                            tglretur BETWEEN '$dari' AND '$sampai'
                        GROUP BY
                            retur.no_fak_penj
                    ) r ON ( penjualan.no_fak_penj = r.no_fak_penj )
                    LEFT JOIN (
                        SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar
                        FROM historibayar
                        WHERE tglbayar BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                    ) hb ON ( penjualan.no_fak_penj = hb.no_fak_penj )
                WHERE
                    datediff( '$sampai', penjualan.tgltransaksi ) > IFNULL(pelanggan.jatuhtempo+1,15) AND bulan = '$bulan' AND tahun = '$tahun'
                GROUP BY
                    salesbarunew
            ) spf"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'spf.salesbarunew');
            }
        );

        if ($aturankomisi == 2) {
            $query->leftJoin(
                DB::raw("(
                SELECT karyawan.id_karyawan,
                (IFNULL(jml_belumsetorbulanlalu,0)+IFNULL(totalsetoran,0)) + IFNULL(jml_gmlast,0) - IFNULL(jml_gmnow,0) - IFNULL(jml_belumsetorbulanini,0) as realisasi_cashin
                FROM karyawan
                LEFT JOIN (
                    SELECT id_karyawan,jumlah as jml_belumsetorbulanlalu FROM belumsetor_detail
                    INNER JOIN belumsetor ON belumsetor_detail.kode_saldobs = belumsetor.kode_saldobs
                    WHERE bulan='$bulanlast' AND tahun='$tahunlast'
                ) bs ON (karyawan.id_karyawan = bs.id_karyawan)

                LEFT JOIN (
                    SELECT id_karyawan, SUM(lhp_tunai+lhp_tagihan) as totalsetoran FROM setoran_penjualan WHERE tgl_lhp BETWEEN '$dari' AND '$sampai' GROUP BY id_karyawan
                ) sp ON (karyawan.id_karyawan = sp.id_karyawan)

                LEFT JOIN (
                    SELECT
                    IFNULL(hb.id_karyawan,giro.id_karyawan) as id_karyawan,
                    SUM( jumlah ) AS jml_gmlast
                    FROM
                    giro
                    INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                    LEFT JOIN ( SELECT id_giro,id_karyawan FROM historibayar GROUP BY id_giro,id_karyawan ) AS hb ON giro.id_giro = hb.id_giro
                    WHERE
                    MONTH ( tgl_giro ) = '$bulanlast'
                    AND YEAR ( tgl_giro ) = '$tahunlast'
                    AND omset_tahun = '$tahun'
                    AND omset_bulan = '$bulan'
                    OR  MONTH ( tgl_giro ) = '$blnlast1'
                    AND YEAR ( tgl_giro ) = '$thnlast1'
                    AND omset_tahun = '$tahun'
                    AND omset_bulan = '$bulan'
                    GROUP BY
                    IFNULL( hb.id_karyawan, giro.id_karyawan )
                ) gmlast ON (karyawan.id_karyawan = gmlast.id_karyawan)
                LEFT JOIN (
                SELECT
                    IFNULL(hb.id_karyawan,giro.id_karyawan) as id_karyawan,
                    SUM( jumlah ) AS jml_gmnow
                FROM
                    giro
                    INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                    LEFT JOIN ( SELECT id_giro,id_karyawan, tglbayar FROM historibayar GROUP BY id_giro, tglbayar,id_karyawan ) AS hb ON giro.id_giro = hb.id_giro
                WHERE
                    tgl_giro >= '$dari'
                    AND tgl_giro <= '$sampai' AND tglbayar IS NULL AND omset_bulan = '0' AND omset_tahun = ''
                    OR  tgl_giro >= '$dari'
                    AND tgl_giro <= '$sampai' AND tglbayar >= '$end'
                    AND omset_bulan > '$bulan'
                    AND omset_tahun >= '$tahun'
                GROUP BY
                IFNULL( hb.id_karyawan, giro.id_karyawan )
                ) gmnow ON (karyawan.id_karyawan = gmnow.id_karyawan)

                LEFT JOIN (
                    SELECT belumsetor_detail.id_karyawan, SUM(jumlah) as jml_belumsetorbulanini
                    FROM belumsetor_detail
                    INNER JOIN belumsetor ON belumsetor_detail.kode_saldobs = belumsetor.kode_saldobs
                    WHERE bulan ='$bulan' AND tahun ='$tahun' GROUP BY id_karyawan
                ) bsnow ON (karyawan.id_karyawan = bsnow.id_karyawan)
                ) hb"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'hb.id_karyawan');
                }
            );
        } else if ($aturankomisi == 3) {
            $query->leftJoin(
                DB::raw("(
                    SELECT historibayar.id_karyawan,SUM(bayar) as realisasi_cashin
                    FROM historibayar WHERE tglbayar BETWEEN '$dari' AND '$sampai' AND status_bayar IS NULL
                    GROUP BY historibayar.id_karyawan
                ) hb"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'hb.id_karyawan');
                }
            );
        } else if ($aturankomisi == 4) {
            $query->leftJoin(
                DB::raw("(
                    SELECT historibayar.id_karyawan,SUM(bayar) as realisasi_cashin
                    FROM historibayar
                    INNER JOIN penjualan ON historibayar.no_fak_penj = penjualan.no_fak_penj
                    WHERE tglbayar BETWEEN '$dari' AND '$sampai' AND status_bayar IS NULL
                    AND datediff(tglbayar, tgltransaksi) <= 14
                    GROUP BY historibayar.id_karyawan
                ) hb"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'hb.id_karyawan');
                }
            );
        }

        $query->leftJoin(
            DB::raw("(
            SELECT
                salesbarunew,
                SUM(IF(kode_produk = 'AB' AND promo !='1' OR kode_produk = 'AB' AND promo IS NULL,jumlah,0)) as AB,
                SUM(IF(kode_produk = 'AR' AND promo !='1' OR kode_produk = 'AR' AND promo IS NULL,jumlah,0)) as AR,
                SUM(IF(kode_produk = 'AS' AND promo !='1' OR kode_produk = 'AS' AND promo IS NULL ,jumlah,0)) as `AS`,
                SUM(IF(kode_produk = 'BB' AND promo !='1' OR kode_produk = 'BB' AND promo IS NULL,jumlah,0)) as BB,
                SUM(IF(kode_produk = 'CG' AND promo !='1' OR kode_produk = 'CG' AND promo IS NULL,jumlah,0)) as CG,
                SUM(IF(kode_produk = 'CGG' AND promo !='1' OR kode_produk = 'CGG' AND promo IS NULL,jumlah,0)) as CGG,
                SUM(IF(kode_produk = 'DEP' AND promo !='1' OR kode_produk = 'DEP' AND promo IS NULL,jumlah,0)) as DEP,
                SUM(IF(kode_produk = 'DK' AND promo !='1' OR kode_produk = 'DK' AND promo IS NULL,jumlah,0)) as DK,
                SUM(IF(kode_produk = 'DS' AND promo !='1' OR kode_produk = 'DS' AND promo IS NULL,jumlah,0)) as DS,
                SUM(IF(kode_produk = 'SP' AND promo !='1' OR kode_produk = 'SP' AND promo IS NULL,jumlah,0)) as SP,
                SUM(IF(kode_produk = 'BBP' AND promo !='1' OR kode_produk = 'BBP' AND promo IS NULL,jumlah,0)) as BBP,
                SUM(IF(kode_produk = 'SPP' AND promo !='1' OR kode_produk = 'SPP' AND promo IS NULL,jumlah,0)) as SPP,
                SUM(IF(kode_produk = 'CG5' AND promo !='1' OR kode_produk = 'CG5' AND promo IS NULL,jumlah,0)) as CG5,
                SUM(IF(kode_produk = 'SP8' AND promo !='1' OR kode_produk = 'SP8' AND promo IS NULL,jumlah,0)) as SP8,
                SUM(IF(kode_produk = 'SC' AND promo !='1' OR kode_produk = 'SC' AND promo IS NULL,jumlah,0)) as SC,
                SUM(IF(kode_produk = 'SP500' AND promo !='1' OR kode_produk = 'SP500' AND promo IS NULL,jumlah,0)) as SP500
                FROM detailpenjualan
                INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang

                LEFT JOIN (
                    SELECT pj.no_fak_penj,
                        IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
                        IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
                    FROM penjualan pj
                    INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                LEFT JOIN (
                    SELECT
                        id_move,no_fak_penj,
                        move_faktur.id_karyawan as salesbaru,
                        karyawan.kode_cabang  as cabangbaru
                    FROM move_faktur
                    INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                    WHERE id_move IN (SELECT max(id_move) FROM move_faktur WHERE tgl_move <= '$dari' GROUP BY no_fak_penj)
                ) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
            ) pjmove ON (penjualan.no_fak_penj = pjmove.no_fak_penj)
            WHERE  status_lunas ='1' AND tgl_pelunasan BETWEEN '$dari' AND '$sampai'
            GROUP BY salesbarunew
            ) realisasi"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'realisasi.salesbarunew');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT penjualan.id_karyawan,
                SUM(IF(kode_produk = 'AB',jumlah,0)) as retur_AB,
                SUM(IF(kode_produk = 'AR',jumlah,0)) as retur_AR,
                SUM(IF(kode_produk = 'AS',jumlah,0)) as `retur_AS`,
                SUM(IF(kode_produk = 'BB',jumlah,0)) as retur_BB,
                SUM(IF(kode_produk = 'CG' ,jumlah,0)) as retur_CG,
                SUM(IF(kode_produk = 'CGG',jumlah,0)) as retur_CGG,
                SUM(IF(kode_produk = 'DEP',jumlah,0)) as retur_DEP,
                SUM(IF(kode_produk = 'DK',jumlah,0)) as retur_DK,
                SUM(IF(kode_produk = 'DS',jumlah,0)) as retur_DS,
                SUM(IF(kode_produk = 'SP',jumlah,0)) as retur_SP,
                SUM(IF(kode_produk = 'BBP',jumlah,0)) as retur_BBP,
                SUM(IF(kode_produk = 'SPP',jumlah,0)) as retur_SPP,
                SUM(IF(kode_produk = 'CG5',jumlah,0)) as retur_CG5,
                SUM(IF(kode_produk = 'SP8',jumlah,0)) as retur_SP8,
                SUM(IF(kode_produk = 'SC',jumlah,0)) as retur_SC,
                SUM(IF(kode_produk = 'SP500',jumlah,0)) as retur_SP500
                FROM detailretur
                INNER JOIN retur ON detailretur.no_retur_penj = retur.no_retur_penj
                INNER JOIN penjualan ON retur.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN barang ON detailretur.kode_barang = barang.kode_barang

                WHERE  status_lunas ='1' AND tgl_pelunasan BETWEEN '$dari' AND '$sampai'
                GROUP BY penjualan.id_karyawan
            ) returpf"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'returpf.id_karyawan');
            }
        );



        $query->leftJoin(
            DB::raw("(
                SELECT id_karyawan,jumlah as potongankomisi,keterangan as ket_potongan
                FROM komisi_potongan
                WHERE bulan = '$bulan' AND tahun='$tahun'
            ) potongankomisi"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'potongankomisi.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT id_karyawan,jumlah as komisifix, keterangan as ket_komisifix
                FROM komisi_akhir
                WHERE bulan = '$bulan' AND tahun='$tahun'
            ) komisiakhir"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'komisiakhir.id_karyawan');
            }
        );

        if (Auth::user()->id == 27 || $cabang == "BDG" && Auth::user()->kode_cabang == "PCF") {
            $query->whereIn('kode_cabang', ['BDG', 'PWK']);
        } else {
            $query->where('kode_cabang', $cabang);
        }
        $query->where('nama_karyawan', '!=', '');
        $komisi = $query->get();

        $nmbulan  = $namabulan[$bulan];

        $kodekp = 'KP' . $cabang;
        $kodespv = 'SPV' . $cabang;

        $potongankp = DB::table('komisi_potongan')->where('id_karyawan', $kodekp)
            ->where('bulan', $bulan)->where('tahun', $tahun)
            ->first();

        $komisiakhir = DB::table('komisi_akhir')->where('id_karyawan', $kodekp)
            ->where('bulan', $bulan)->where('tahun', $tahun)
            ->first();


        $supervisorcabang = ['BDG', 'TSM'];
        if (in_array($cabang, $supervisorcabang)) {
            $potonganspv = DB::table('komisi_potongan')->where('id_karyawan', $kodespv)
                ->where('bulan', $bulan)->where('tahun', $tahun)
                ->first();

            $komisiakhirspv = DB::table('komisi_akhir')->where('id_karyawan', $kodespv)
                ->where('bulan', $bulan)->where('tahun', $tahun)
                ->first();
        } else {
            $potonganspv = null;
            $komisiakhirspv = null;
        }

        if (isset($_POST['export'])) {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Komisi $time.xls");
        }


        return view('targetkomisi.laporan.cetak_komisi_maret2023', compact('komisi', 'cbg', 'nmbulan', 'tahun', 'produk', 'bulan', 'cabang', 'potongankp', 'komisiakhir', 'supervisorcabang', 'potonganspv', 'komisiakhirspv'));
    }


    public function cetakkomisijuni2023($cabang, $bulan, $tahun, $aturankomisi, $dari, $hariini, $sampai)
    {
        //$dari = '2023-06-31';
        $lastmonth = date('Y-m-d', strtotime(date($dari) . '- 1 month'));
        $enddate = date('Y-m-t', strtotime($dari));
        //dd($lastdateofmonth);
        $last3month = date('Y-m-d', strtotime('-2 month', strtotime($enddate)));
        $date = explode("-", $last3month);
        $startdate = $date[0] . "-" . $date[1] . "-01";
        $lastdate = explode("-", $lastmonth);
        $bulanlast = $lastdate[1] + 0;
        $tahunlast = $lastdate[0];
        if ($bulanlast == 1) {
            $blnlast1 = 12;
            $thnlast1 = $tahun - 1;
        } else {
            $blnlast1 = $bulanlast - 1;
            $thnlast1 = $tahun;
        }


        if ($bulan == 12) {
            $bln = 1;
            $thn = $tahun + 1;
        } else {
            $bln = $bulan + 1;
            $thn = $tahun;
        }


        $ceknextBulan = DB::table('setoran_pusat')->where('omset_bulan', $bulan)->where('omset_tahun', $tahun)
            ->whereRaw('MONTH(tgl_diterimapusat) = ' . $bln)
            ->whereRaw('YEAR(tgl_diterimapusat) = ' . $thn)
            ->where('kode_cabang', $cabang)
            ->orderBy('tgl_diterimapusat', 'desc')
            ->first();
        if ($ceknextBulan ==  null) {
            $end = date("Y-m-t", strtotime($dari));
        } else {
            $end = $ceknextBulan->tgl_diterimapusat;
        }


        $produk = Barang::orderBy('kode_produk')->get();
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $cbg = DB::table('cabang')->where('kode_cabang', $cabang)->first();

        $query = Salesman::query();
        $query->selectRaw('
            karyawan.id_karyawan,nama_karyawan,kategori_salesman,status_komisi,
            target_BB_DP,
            BB,
            retur_BB,
            DEP,
            retur_DEP,
            target_DS,
            DS,
            retur_DS,
            SP8,
            retur_SP8,
            target_SP,
            SP,
            retur_SP,
            SP500,
            retur_SP500,
            target_SC,
            SC,
            retur_SC,
            target_AR,
            AR,
            retur_AR,
            target_AB_AS_CG5,
            AB,
            retur_AB,
            `AS`,
            retur_AS,
            CG5,
            retur_CG5,
            realisasi_cashin,
            IFNULL(sisapiutangsaldo,0) + IFNULL(sisapiutang,0) as sisapiutang,
            cashin_jt,
            potongankomisi,
            komisifix,
            ket_potongan,
            ket_komisifix,
            jmlpelanggan,
            jmltrans,
            jmltigasku,
            jmlkunjungan,
            jmlsesuaijadwal,
            jmltranspenjualan
        ');

        $query->leftJoin(
            DB::raw("(
                SELECT id_sales, COUNT(DISTINCT(penjualan.kode_pelanggan)) as jmlpelanggan
                FROM penjualan
                INNER JOIN pelanggan ON penjualan.kode_pelanggan = pelanggan.kode_pelanggan
                WHERE tgltransaksi BETWEEN '$startdate' AND '$enddate' AND nama_pelanggan != 'BATAL'
                GROUP BY id_sales
            ) pelangganaktif"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'pelangganaktif.id_sales');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT karyawan.id_karyawan,
                IFNULL(jmlkunjungan,0) as jmlkunjungan,
                IFNULL(jmlsesuaijadwal,0) as jmlsesuaijadwal
                FROM karyawan
                LEFT JOIN (
                    SELECT
                    penjualan.id_karyawan,
                    COUNT(no_fak_penj) as jmlkunjungan,
                    COUNT(
                    CASE WHEN
                    DAYNAME(tgltransaksi)='Monday' AND pelanggan.hari like '%Senin%' OR
                    DAYNAME(tgltransaksi)='Tuesday' AND pelanggan.hari like '%Selasa%' OR
                    DAYNAME(tgltransaksi)='Wednesday' AND pelanggan.hari like '%Rabu%' OR
                    DAYNAME(tgltransaksi)='Thursday' AND pelanggan.hari like '%Kamis%' OR
                    DAYNAME(tgltransaksi)='Friday' AND pelanggan.hari like '%Jumat%' OR
                    DAYNAME(tgltransaksi)='Saturday' AND pelanggan.hari like '%Sabtu%' OR
                    DAYNAME(tgltransaksi)='Sunday' AND pelanggan.hari like '%Minggu%'  THEN  penjualan.no_fak_penj END ) as jmlsesuaijadwal
                    FROM
                    `penjualan`
                    INNER JOIN `pelanggan` ON `penjualan`.`kode_pelanggan` = `pelanggan`.`kode_pelanggan`
                    INNER JOIN `karyawan` ON `penjualan`.`id_karyawan` = `karyawan`.`id_karyawan`
                    WHERE `tgltransaksi` BETWEEN '$dari' AND '$sampai' AND `nama_pelanggan` != 'BATAL'
                    GROUP BY
                            penjualan.id_karyawan
                ) kunjungan ON (karyawan.id_karyawan = kunjungan.id_karyawan)
            ) kunjungan"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'kunjungan.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
            SELECT karyawan.id_karyawan,COUNT(jml_sku) as jmltigasku
            FROM karyawan
            LEFT JOIN (
            SELECT penjualan.id_karyawan,COUNT(DISTINCT(kode_sku)) as jml_sku
            FROM detailpenjualan
            INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
            INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
            INNER JOIN master_barang ON barang.kode_produk = master_barang.kode_produk
            WHERE tgltransaksi BETWEEN '$dari' AND '$sampai' AND promo IS NULL
            GROUP BY penjualan.kode_pelanggan,penjualan.id_karyawan
            ORDER BY penjualan.kode_pelanggan
            ) sku ON (karyawan.id_karyawan = sku.id_karyawan)
            WHERE jml_sku >= 3 GROUP BY karyawan.id_karyawan
            ) sku"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'sku.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT id_karyawan,
                COUNT(DISTINCT(kode_pelanggan)) as jmltrans,
                COUNT(no_fak_penj) as jmltranspenjualan
                FROM penjualan
                WHERE tgltransaksi BETWEEN '$dari' AND '$sampai'
                GROUP BY id_karyawan
            ) pelanggantrans"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'pelanggantrans.id_karyawan');
            }
        );
        $query->join(
            DB::raw("(
                SELECT  id_karyawan,
                SUM(IF(kategori_komisi='KKQ01',jumlah_target,0)) as target_BB_DP,
                SUM(IF(kategori_komisi='KKQ02',jumlah_target,0)) as target_DS,
                SUM(IF(kategori_komisi='KKQ03',jumlah_target,0)) as target_SP,
                SUM(IF(kategori_komisi='KKQ04',jumlah_target,0)) as target_AR,
                SUM(IF(kategori_komisi='KKQ05',jumlah_target,0)) as target_AB_AS_CG5,
                SUM(IF(kategori_komisi='KKQ06',jumlah_target,0)) as target_SC
                FROM
                komisi_target_qty_detail k_detail
                INNER JOIN komisi_target ON k_detail.kode_target = komisi_target.kode_target
                INNER JOIN master_barang ON k_detail.kode_produk = master_barang.kode_produk
                WHERE bulan ='$bulan' AND tahun='$tahun'
                GROUP BY id_karyawan
                ) komisi"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'komisi.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT historibayar.id_karyawan,SUM(bayar) as cashin_jt
                FROM historibayar
                INNER JOIN penjualan ON historibayar.no_fak_penj = penjualan.no_fak_penj
                WHERE tglbayar BETWEEN '$dari' AND '$sampai' AND status_bayar IS NULL
                AND datediff(tglbayar, tgltransaksi) > 15
                GROUP BY historibayar.id_karyawan
            ) hbjt"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'hbjt.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT
                    salesbarunew,IFNULL(SUM(penjualan.total),0) - SUM(IFNULL( totalretur, 0 )) - SUM(IFNULL( totalbayar, 0 )) AS sisapiutang
                FROM
                penjualan
                INNER JOIN pelanggan ON penjualan.kode_pelanggan = pelanggan.kode_pelanggan
                LEFT JOIN (
                    SELECT
                        pj.no_fak_penj,IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,karyawan.nama_karyawan AS nama_sales,
                        IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                    FROM
                        penjualan pj
                    INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                    LEFT JOIN (
                        SELECT
                            id_move,
                            no_fak_penj,
                            move_faktur.id_karyawan AS salesbaru,
                            karyawan.kode_cabang AS cabangbaru
                        FROM
                            move_faktur
                        INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                        WHERE id_move IN ( SELECT max( id_move ) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj )
                        ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj )
                ) pjmove ON ( penjualan.no_fak_penj = pjmove.no_fak_penj )
                LEFT JOIN (
                    SELECT
                        retur.no_fak_penj AS no_fak_penj,
                        SUM(IFNULL( subtotal_pf, 0 ) - IFNULL( subtotal_gb, 0 )) AS totalretur
                    FROM
                        retur
                    WHERE
                        tglretur BETWEEN '$dari' AND '$sampai'
                    GROUP BY
                        retur.no_fak_penj
                ) r ON ( penjualan.no_fak_penj = r.no_fak_penj )
                LEFT JOIN (
                    SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar
                    FROM historibayar
                    WHERE tglbayar BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                ) hb ON ( penjualan.no_fak_penj = hb.no_fak_penj )

            WHERE penjualan.tgltransaksi BETWEEN '$dari' AND '$sampai' AND jenistransaksi = 'kredit' AND datediff( '$sampai', penjualan.tgltransaksi ) > IFNULL(pelanggan.jatuhtempo+1,15)
            GROUP BY
                salesbarunew

            ) penj"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'penj.salesbarunew');
            }
        );


        $query->leftJoin(
            DB::raw("(
                SELECT
                    salesbarunew,IFNULL( SUM(jumlah ), 0 ) - SUM(IFNULL( totalretur, 0 )) - SUM(IFNULL( totalbayar, 0 )) AS sisapiutangsaldo
                FROM
                saldoawal_piutang_faktur spf
                INNER JOIN penjualan ON spf.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN pelanggan ON penjualan.kode_pelanggan = pelanggan.kode_pelanggan
                LEFT JOIN (
                        SELECT
                            pj.no_fak_penj,IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,karyawan.nama_karyawan AS nama_sales,
                            IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                        FROM
                            penjualan pj
                        INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                        LEFT JOIN (
                            SELECT
                                id_move,
                                no_fak_penj,
                                move_faktur.id_karyawan AS salesbaru,
                                karyawan.kode_cabang AS cabangbaru
                            FROM
                                move_faktur
                            INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                            WHERE id_move IN ( SELECT max( id_move ) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj )
                            ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj )
                    ) pjmove ON ( penjualan.no_fak_penj = pjmove.no_fak_penj )
                    LEFT JOIN (
                        SELECT
                            retur.no_fak_penj AS no_fak_penj,
                            SUM(IFNULL( subtotal_pf, 0 ) - IFNULL( subtotal_gb, 0 )) AS totalretur
                        FROM
                            retur
                        WHERE
                            tglretur BETWEEN '$dari' AND '$sampai'
                        GROUP BY
                            retur.no_fak_penj
                    ) r ON ( penjualan.no_fak_penj = r.no_fak_penj )
                    LEFT JOIN (
                        SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar
                        FROM historibayar
                        WHERE tglbayar BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                    ) hb ON ( penjualan.no_fak_penj = hb.no_fak_penj )
                WHERE
                    datediff( '$sampai', penjualan.tgltransaksi ) > IFNULL(pelanggan.jatuhtempo+1,15) AND bulan = '$bulan' AND tahun = '$tahun'
                GROUP BY
                    salesbarunew
            ) spf"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'spf.salesbarunew');
            }
        );

        if ($aturankomisi == 2) {
            $query->leftJoin(
                DB::raw("(
                SELECT karyawan.id_karyawan,
                (IFNULL(jml_belumsetorbulanlalu,0)+IFNULL(totalsetoran,0)) + IFNULL(jml_gmlast,0) - IFNULL(jml_gmnow,0) - IFNULL(jml_belumsetorbulanini,0) as realisasi_cashin
                FROM karyawan
                LEFT JOIN (
                    SELECT id_karyawan,jumlah as jml_belumsetorbulanlalu FROM belumsetor_detail
                    INNER JOIN belumsetor ON belumsetor_detail.kode_saldobs = belumsetor.kode_saldobs
                    WHERE bulan='$bulanlast' AND tahun='$tahunlast'
                ) bs ON (karyawan.id_karyawan = bs.id_karyawan)

                LEFT JOIN (
                    SELECT id_karyawan, SUM(lhp_tunai+lhp_tagihan) as totalsetoran FROM setoran_penjualan WHERE tgl_lhp BETWEEN '$dari' AND '$sampai' GROUP BY id_karyawan
                ) sp ON (karyawan.id_karyawan = sp.id_karyawan)

                LEFT JOIN (
                    SELECT
                    IFNULL(hb.id_karyawan,giro.id_karyawan) as id_karyawan,
                    SUM( jumlah ) AS jml_gmlast
                    FROM
                    giro
                    INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                    LEFT JOIN ( SELECT id_giro,id_karyawan FROM historibayar GROUP BY id_giro,id_karyawan ) AS hb ON giro.id_giro = hb.id_giro
                    WHERE
                    MONTH ( tgl_giro ) = '$bulanlast'
                    AND YEAR ( tgl_giro ) = '$tahunlast'
                    AND omset_tahun = '$tahun'
                    AND omset_bulan = '$bulan'
                    OR  MONTH ( tgl_giro ) = '$blnlast1'
                    AND YEAR ( tgl_giro ) = '$thnlast1'
                    AND omset_tahun = '$tahun'
                    AND omset_bulan = '$bulan'
                    GROUP BY
                    IFNULL( hb.id_karyawan, giro.id_karyawan )
                ) gmlast ON (karyawan.id_karyawan = gmlast.id_karyawan)
                LEFT JOIN (
                SELECT
                    IFNULL(hb.id_karyawan,giro.id_karyawan) as id_karyawan,
                    SUM( jumlah ) AS jml_gmnow
                FROM
                    giro
                    INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                    LEFT JOIN ( SELECT id_giro,id_karyawan, tglbayar FROM historibayar GROUP BY id_giro, tglbayar,id_karyawan ) AS hb ON giro.id_giro = hb.id_giro
                WHERE
                    tgl_giro >= '$dari'
                    AND tgl_giro <= '$sampai' AND tglbayar IS NULL AND omset_bulan = '0' AND omset_tahun = ''
                    OR  tgl_giro >= '$dari'
                    AND tgl_giro <= '$sampai' AND tglbayar >= '$end'
                    AND omset_bulan > '$bulan'
                    AND omset_tahun >= '$tahun'
                GROUP BY
                IFNULL( hb.id_karyawan, giro.id_karyawan )
                ) gmnow ON (karyawan.id_karyawan = gmnow.id_karyawan)

                LEFT JOIN (
                    SELECT belumsetor_detail.id_karyawan, SUM(jumlah) as jml_belumsetorbulanini
                    FROM belumsetor_detail
                    INNER JOIN belumsetor ON belumsetor_detail.kode_saldobs = belumsetor.kode_saldobs
                    WHERE bulan ='$bulan' AND tahun ='$tahun' GROUP BY id_karyawan
                ) bsnow ON (karyawan.id_karyawan = bsnow.id_karyawan)
                ) hb"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'hb.id_karyawan');
                }
            );
        } else if ($aturankomisi == 3) {
            $query->leftJoin(
                DB::raw("(
                    SELECT historibayar.id_karyawan,SUM(bayar) as realisasi_cashin
                    FROM historibayar WHERE tglbayar BETWEEN '$dari' AND '$sampai' AND status_bayar IS NULL
                    GROUP BY historibayar.id_karyawan
                ) hb"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'hb.id_karyawan');
                }
            );
        } else if ($aturankomisi == 4) {
            $query->leftJoin(
                DB::raw("(
                    SELECT historibayar.id_karyawan,SUM(bayar) as realisasi_cashin
                    FROM historibayar
                    INNER JOIN penjualan ON historibayar.no_fak_penj = penjualan.no_fak_penj
                    WHERE tglbayar BETWEEN '$dari' AND '$sampai' AND status_bayar IS NULL
                    AND datediff(tglbayar, tgltransaksi) <= 14
                    GROUP BY historibayar.id_karyawan
                ) hb"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'hb.id_karyawan');
                }
            );
        }

        $query->leftJoin(
            DB::raw("(
            SELECT
                salesbarunew,
                SUM(IF(kode_produk = 'AB' AND promo !='1' OR kode_produk = 'AB' AND promo IS NULL,jumlah,0)) as AB,
                SUM(IF(kode_produk = 'AR' AND promo !='1' OR kode_produk = 'AR' AND promo IS NULL,jumlah,0)) as AR,
                SUM(IF(kode_produk = 'AS' AND promo !='1' OR kode_produk = 'AS' AND promo IS NULL ,jumlah,0)) as `AS`,
                SUM(IF(kode_produk = 'BB' AND promo !='1' OR kode_produk = 'BB' AND promo IS NULL,jumlah,0)) as BB,
                SUM(IF(kode_produk = 'CG' AND promo !='1' OR kode_produk = 'CG' AND promo IS NULL,jumlah,0)) as CG,
                SUM(IF(kode_produk = 'CGG' AND promo !='1' OR kode_produk = 'CGG' AND promo IS NULL,jumlah,0)) as CGG,
                SUM(IF(kode_produk = 'DEP' AND promo !='1' OR kode_produk = 'DEP' AND promo IS NULL,jumlah,0)) as DEP,
                SUM(IF(kode_produk = 'DK' AND promo !='1' OR kode_produk = 'DK' AND promo IS NULL,jumlah,0)) as DK,
                SUM(IF(kode_produk = 'DS' AND promo !='1' OR kode_produk = 'DS' AND promo IS NULL,jumlah,0)) as DS,
                SUM(IF(kode_produk = 'SP' AND promo !='1' OR kode_produk = 'SP' AND promo IS NULL,jumlah,0)) as SP,
                SUM(IF(kode_produk = 'BBP' AND promo !='1' OR kode_produk = 'BBP' AND promo IS NULL,jumlah,0)) as BBP,
                SUM(IF(kode_produk = 'SPP' AND promo !='1' OR kode_produk = 'SPP' AND promo IS NULL,jumlah,0)) as SPP,
                SUM(IF(kode_produk = 'CG5' AND promo !='1' OR kode_produk = 'CG5' AND promo IS NULL,jumlah,0)) as CG5,
                SUM(IF(kode_produk = 'SP8' AND promo !='1' OR kode_produk = 'SP8' AND promo IS NULL,jumlah,0)) as SP8,
                SUM(IF(kode_produk = 'SC' AND promo !='1' OR kode_produk = 'SC' AND promo IS NULL,jumlah,0)) as SC,
                SUM(IF(kode_produk = 'SP500' AND promo !='1' OR kode_produk = 'SP500' AND promo IS NULL,jumlah,0)) as SP500
                FROM detailpenjualan
                INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang

                LEFT JOIN (
                    SELECT pj.no_fak_penj,
                        IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
                        IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
                    FROM penjualan pj
                    INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                LEFT JOIN (
                    SELECT
                        id_move,no_fak_penj,
                        move_faktur.id_karyawan as salesbaru,
                        karyawan.kode_cabang  as cabangbaru
                    FROM move_faktur
                    INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                    WHERE id_move IN (SELECT max(id_move) FROM move_faktur WHERE tgl_move <= '$dari' GROUP BY no_fak_penj)
                ) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
            ) pjmove ON (penjualan.no_fak_penj = pjmove.no_fak_penj)
            WHERE  status_lunas ='1' AND tgl_pelunasan BETWEEN '$dari' AND '$sampai'
            GROUP BY salesbarunew
            ) realisasi"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'realisasi.salesbarunew');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT penjualan.id_karyawan,
                SUM(IF(kode_produk = 'AB',jumlah,0)) as retur_AB,
                SUM(IF(kode_produk = 'AR',jumlah,0)) as retur_AR,
                SUM(IF(kode_produk = 'AS',jumlah,0)) as `retur_AS`,
                SUM(IF(kode_produk = 'BB',jumlah,0)) as retur_BB,
                SUM(IF(kode_produk = 'CG' ,jumlah,0)) as retur_CG,
                SUM(IF(kode_produk = 'CGG',jumlah,0)) as retur_CGG,
                SUM(IF(kode_produk = 'DEP',jumlah,0)) as retur_DEP,
                SUM(IF(kode_produk = 'DK',jumlah,0)) as retur_DK,
                SUM(IF(kode_produk = 'DS',jumlah,0)) as retur_DS,
                SUM(IF(kode_produk = 'SP',jumlah,0)) as retur_SP,
                SUM(IF(kode_produk = 'BBP',jumlah,0)) as retur_BBP,
                SUM(IF(kode_produk = 'SPP',jumlah,0)) as retur_SPP,
                SUM(IF(kode_produk = 'CG5',jumlah,0)) as retur_CG5,
                SUM(IF(kode_produk = 'SP8',jumlah,0)) as retur_SP8,
                SUM(IF(kode_produk = 'SC',jumlah,0)) as retur_SC,
                SUM(IF(kode_produk = 'SP500',jumlah,0)) as retur_SP500
                FROM detailretur
                INNER JOIN retur ON detailretur.no_retur_penj = retur.no_retur_penj
                INNER JOIN penjualan ON retur.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN barang ON detailretur.kode_barang = barang.kode_barang

                WHERE  status_lunas ='1' AND tgl_pelunasan BETWEEN '$dari' AND '$sampai'
                GROUP BY penjualan.id_karyawan
            ) returpf"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'returpf.id_karyawan');
            }
        );



        $query->leftJoin(
            DB::raw("(
                SELECT id_karyawan,jumlah as potongankomisi,keterangan as ket_potongan
                FROM komisi_potongan
                WHERE bulan = '$bulan' AND tahun='$tahun'
            ) potongankomisi"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'potongankomisi.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT id_karyawan,jumlah as komisifix, keterangan as ket_komisifix
                FROM komisi_akhir
                WHERE bulan = '$bulan' AND tahun='$tahun'
            ) komisiakhir"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'komisiakhir.id_karyawan');
            }
        );

        if (Auth::user()->id == 27 || $cabang == "BDG" && Auth::user()->kode_cabang == "PCF") {
            $query->whereIn('kode_cabang', ['BDG', 'PWK']);
        } else {
            $query->where('kode_cabang', $cabang);
        }
        $query->where('nama_karyawan', '!=', '');
        $komisi = $query->get();

        $nmbulan  = $namabulan[$bulan];

        $kodekp = 'KP' . $cabang;
        $kodespv = 'SPV' . $cabang;

        $potongankp = DB::table('komisi_potongan')->where('id_karyawan', $kodekp)
            ->where('bulan', $bulan)->where('tahun', $tahun)
            ->first();

        $komisiakhir = DB::table('komisi_akhir')->where('id_karyawan', $kodekp)
            ->where('bulan', $bulan)->where('tahun', $tahun)
            ->first();


        $supervisorcabang = ['BDG', 'TSM'];
        if (in_array($cabang, $supervisorcabang)) {
            $potonganspv = DB::table('komisi_potongan')->where('id_karyawan', $kodespv)
                ->where('bulan', $bulan)->where('tahun', $tahun)
                ->first();

            $komisiakhirspv = DB::table('komisi_akhir')->where('id_karyawan', $kodespv)
                ->where('bulan', $bulan)->where('tahun', $tahun)
                ->first();
        } else {
            $potonganspv = null;
            $komisiakhirspv = null;
        }

        if (isset($_POST['export'])) {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Komisi $time.xls");
        }


        return view('targetkomisi.laporan.cetak_komisi_juni2023', compact('komisi', 'cbg', 'nmbulan', 'tahun', 'produk', 'bulan', 'cabang', 'potongankp', 'komisiakhir', 'supervisorcabang', 'potonganspv', 'komisiakhirspv', 'startdate', 'enddate'));
    }

    public function cetaklaporankomisi(Request $request)
    {
        $cabang = $request->kode_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $aturankomisi = $request->aturankomisi;
        $dari = $tahun . "-" . $bulan . "-01";
        $hariini = date("Y-m-d");
        $tglkomisi = $tahun . "-" . $bulan . "-01";
        $sampai = date('Y-m-t', strtotime($dari));
        if ($hariini < $sampai) {
            $sampai = $hariini;
        } else {
            $sampai = $sampai;
        }


        //dd($dari);

        // echo $dari;
        // die;
        if ($dari >= '2023-2-01' and $dari < '2023-6-01') {
            echo 1;
            die;
            return $this->cetakkomisimaret2023($cabang, $bulan, $tahun, $aturankomisi, $dari, $hariini, $sampai);
        } elseif ($dari >= '2023-6-01' and $dari < '2023-07-01') {
            echo 2;
            die;
            return $this->cetakkomisijuni2023($cabang, $bulan, $tahun, $aturankomisi, $dari, $hariini, $sampai);
        } elseif ($dari >= '2023-07-01' and $dari < '2023-10-01') {
            echo 3;
            die;
            return $this->cetakkomisijuli2023($cabang, $bulan, $tahun, $aturankomisi, $dari, $hariini, $sampai);
        } elseif ($dari >= '2023-10-1' and $dari < '2023-12-1') {
            // echo 4;
            // die;
            return $this->cetakkomisioktober2023($cabang, $bulan, $tahun, $aturankomisi, $dari, $hariini, $sampai);
        } elseif ($dari >= '2024-01-01') {
            // echo 5;
            // die;
            return $this->cetakkomisijanuari2024($cabang, $bulan, $tahun, $aturankomisi, $dari, $hariini, $sampai);
        }
        die;
        $lastmonth = date('Y-m-d', strtotime(date($dari) . '- 1 month'));
        $lastdate = explode("-", $lastmonth);
        $bulanlast = $lastdate[1] + 0;
        $tahunlast = $lastdate[0];
        if ($bulanlast == 1) {
            $blnlast1 = 12;
            $thnlast1 = $tahun - 1;
        } else {
            $blnlast1 = $bulanlast - 1;
            $thnlast1 = $tahun;
        }


        if ($bulan == 12) {
            $bln = 1;
            $thn = $tahun + 1;
        } else {
            $bln = $bulan + 1;
            $thn = $tahun;
        }


        $ceknextBulan = DB::table('setoran_pusat')->where('omset_bulan', $bulan)->where('omset_tahun', $tahun)
            ->whereRaw('MONTH(tgl_diterimapusat) = ' . $bln)
            ->whereRaw('YEAR(tgl_diterimapusat) = ' . $thn)
            ->where('kode_cabang', $cabang)
            ->orderBy('tgl_diterimapusat', 'desc')
            ->first();
        if ($ceknextBulan ==  null) {
            $end = date("Y-m-t", strtotime($dari));
        } else {
            $end = $ceknextBulan->tgl_diterimapusat;
        }


        $produk = Barang::orderBy('kode_produk')->get();
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $cbg = DB::table('cabang')->where('kode_cabang', $cabang)->first();


        if ($tglkomisi < '2023-02-01') {


            $driver = DB::table('driver_helper')
                ->selectRaw("driver_helper.id_driver_helper,nama_driver_helper,kategori,IFNULL(jml_driver,0) as jml_driver,driver_helper.ratio as ratiodefault,
            driver_helper.ratio_helper as ratiohelperdefault,
            ratioaktif,ratiohelperaktif,ratioterakhir,ratiohelperterakhir")
                ->join(
                    DB::raw("(
                        SELECT id_driver,ROUND(SUM(jml_penjualan),2) as jml_driver
                        FROM detail_dpb
                        INNER JOIN dpb ON detail_dpb.no_dpb = dpb.no_dpb
                        WHERE tgl_pengambilan BETWEEN '$dari' AND '$sampai' GROUP BY id_driver
                    ) driver"),
                    function ($join) {
                        $join->on('driver_helper.id_driver_helper', '=', 'driver.id_driver');
                    }
                )
                ->leftJoin(
                    DB::raw("(
                    SELECT id,set_ratio_komisi.ratio as ratioaktif,set_ratio_komisi.ratio_helper as ratiohelperaktif
                    FROM set_ratio_komisi
                    INNER JOIN driver_helper ON set_ratio_komisi.id = driver_helper.id_driver_helper
                    WHERE bulan = '$bulan' AND tahun = '$tahun' AND kode_cabang='$cabang'
                    ) ratio"),
                    function ($join) {
                        $join->on('driver_helper.id_driver_helper', '=', 'ratio.id');
                    }
                )
                ->leftJoin(
                    DB::raw("(
                    SELECT id,set_ratio_komisi.ratio as ratioterakhir,set_ratio_komisi.ratio_helper as ratiohelperterakhir
                    FROM set_ratio_komisi
                    INNER JOIN driver_helper ON set_ratio_komisi.id = driver_helper.id_driver_helper
                    WHERE kode_cabang ='$cabang' AND tgl_berlaku IN (SELECT max(tgl_berlaku) FROM set_ratio_komisi)
                    ) lastratio"),
                    function ($join) {
                        $join->on('driver_helper.id_driver_helper', '=', 'lastratio.id');
                    }
                )
                ->where('kode_cabang', $cabang)
                ->orderBy('kategori')
                ->orderBy('nama_driver_helper')
                ->get();

            $helper = DB::table('driver_helper')
                ->selectRaw("driver_helper.id_driver_helper,nama_driver_helper,kategori,IFNULL(jml_helper,0) + IFNULL(jml_helper_2,0) + IFNULL(jml_helper_3,0) as jml_helper,driver_helper.ratio as ratiodefault,
            driver_helper.ratio_helper as ratiohelperdefault,
            ratioaktif,ratiohelperaktif,ratioterakhir,ratiohelperterakhir")
                ->leftJoin(
                    DB::raw("(
                    SELECT id_helper,ROUND(SUM(jml_penjualan),2) as jml_helper
                    FROM detail_dpb
                    INNER JOIN dpb ON detail_dpb.no_dpb = dpb.no_dpb
                    WHERE tgl_pengambilan BETWEEN '$dari' AND '$sampai' GROUP BY id_helper
                    ) helper"),
                    function ($join) {
                        $join->on('driver_helper.id_driver_helper', '=', 'helper.id_helper');
                    }
                )
                ->leftJoin(
                    DB::raw("(
                    SELECT id_helper_2,ROUND(SUM(jml_penjualan),2) as jml_helper_2
                    FROM detail_dpb
                    INNER JOIN dpb ON detail_dpb.no_dpb = dpb.no_dpb
                    WHERE tgl_pengambilan BETWEEN '$dari' AND '$sampai' GROUP BY id_helper_2
                    ) helper2"),
                    function ($join) {
                        $join->on('driver_helper.id_driver_helper', '=', 'helper2.id_helper_2');
                    }
                )

                ->leftJoin(
                    DB::raw("(
                    SELECT id_helper_3,ROUND(SUM(jml_penjualan),2) as jml_helper_3
                    FROM detail_dpb
                    INNER JOIN dpb ON detail_dpb.no_dpb = dpb.no_dpb
                    WHERE tgl_pengambilan BETWEEN '$dari' AND '$sampai' GROUP BY id_helper_3
                    ) helper3"),
                    function ($join) {
                        $join->on('driver_helper.id_driver_helper', '=', 'helper3.id_helper_3');
                    }
                )

                ->leftJoin(
                    DB::raw("(
                    SELECT id,set_ratio_komisi.ratio as ratioaktif,set_ratio_komisi.ratio_helper as ratiohelperaktif
                    FROM set_ratio_komisi
                    INNER JOIN driver_helper ON set_ratio_komisi.id = driver_helper.id_driver_helper
                    WHERE bulan = '$bulan' AND tahun = '$tahun' AND kode_cabang='$cabang'
                    ) ratio"),
                    function ($join) {
                        $join->on('driver_helper.id_driver_helper', '=', 'ratio.id');
                    }
                )

                ->leftJoin(
                    DB::raw("(
                    SELECT id,set_ratio_komisi.ratio as ratioterakhir,set_ratio_komisi.ratio_helper as ratiohelperterakhir
                    FROM set_ratio_komisi
                    INNER JOIN driver_helper ON set_ratio_komisi.id = driver_helper.id_driver_helper
                    WHERE kode_cabang ='$cabang' AND tgl_berlaku IN (SELECT max(tgl_berlaku) FROM set_ratio_komisi)
                    ) lastratio"),
                    function ($join) {
                        $join->on('driver_helper.id_driver_helper', '=', 'lastratio.id');
                    }
                )
                ->where('kode_cabang', $cabang)
                ->whereRaw('(IFNULL(jml_helper,0) + IFNULL(jml_helper_2,0) + IFNULL(jml_helper_3,0)) != 0.00')
                ->orderBy('kategori')
                ->orderBy('nama_driver_helper')
                ->get();

            $tunaikredit = DB::table('detailpenjualan')
                ->selectRaw("IFNULL(FLOOR(SUM( IF ( kode_produk = 'AB', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'AR', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'AS', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'BB', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'CG', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'CGG', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'DB', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'DEP', detailpenjualan.jumlah/isipcsdus,NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'DK', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'DS', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'SP', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'BBP', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'SPP', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'CG5', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'SC', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'SP8', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) AS total")
                ->join('barang', 'detailpenjualan.kode_barang', '=', 'barang.kode_barang')
                ->join('penjualan', 'detailpenjualan.no_fak_penj', '=', 'penjualan.no_fak_penj')
                ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
                ->where('promo', '!=', 1)
                ->whereBetween('tgltransaksi', [$dari, $sampai])
                ->where('karyawan.kode_cabang', $cabang)
                ->orWhereNull('promo')
                ->whereBetween('tgltransaksi', [$dari, $sampai])
                ->where('karyawan.kode_cabang', $cabang)
                ->first();

            $gudang = DB::table('driver_helper')
                ->selectRaw("id_driver_helper,nama_driver_helper ,driver_helper.ratio as ratiodefault,ratioaktif,ratioterakhir")
                ->leftJoin(
                    DB::raw("(
                    SELECT id,set_ratio_komisi.ratio as ratioaktif
                    FROM set_ratio_komisi
                    INNER JOIN driver_helper ON set_ratio_komisi.id = driver_helper.id_driver_helper
                    WHERE bulan = '$bulan' AND tahun = '$tahun' AND kode_cabang='$cabang'
                    ) ratio"),
                    function ($join) {
                        $join->on('driver_helper.id_driver_helper', '=', 'ratio.id');
                    }
                )

                ->leftJoin(
                    DB::raw("(
                    SELECT id,set_ratio_komisi.ratio as ratioterakhir
                    FROM set_ratio_komisi
                    INNER JOIN driver_helper ON set_ratio_komisi.id = driver_helper.id_driver_helper
                    WHERE kode_cabang ='$cabang' AND tgl_berlaku IN (SELECT max(tgl_berlaku) FROM set_ratio_komisi)
                    ) lastratio"),
                    function ($join) {
                        $join->on('driver_helper.id_driver_helper', '=', 'lastratio.id');
                    }
                )
                ->where('kode_cabang', $cabang)
                ->where('kategori', 'GUDANG')
                ->get();
            //dd($helper);
        } else {
            $driver = null;
            $helper = null;
            $gudang = null;
            $tunaikredit = null;
        }

        $tglsetkomisi = "2022-05-01";
        $tglsetkomisi2 = "2022-09-01";
        $tglsetkomisiljt = "2023-2-01";
        // //dd($tglkomisi);
        // dd($tglkomisi >= $tglsetkomisiljt);
        if ($tglkomisi >= $tglsetkomisi) {
            $query = Salesman::query();
            $query->selectRaw('
            karyawan.id_karyawan,nama_karyawan,kategori_salesman,status_komisi,
            target_BB_DP,
            BB,
            retur_BB,
            DEP,
            retur_DEP,
            target_DS,
            DS,
            retur_DS,
            SP8,
            retur_SP8,
            target_SP,
            SP,
            retur_SP,
            SP500,
            retur_SP500,
            target_SC,
            SC,
            retur_SC,
            target_AR,
            AR,
            retur_AR,
            target_AB_AS_CG5,
            AB,
            retur_AB,
            `AS`,
            retur_AS,
            CG5,
            retur_CG5,
            realisasi_cashin,
            IFNULL(sisapiutangsaldo,0) + IFNULL(sisapiutang,0) as sisapiutang,
            cashin_jt,
            potongankomisi,
            komisifix,
            ket_potongan,
            ket_komisifix
            ');
            $query->join(
                DB::raw("(
                    SELECT  id_karyawan,
                    SUM(IF(kategori_komisi='KKQ01',jumlah_target,0)) as target_BB_DP,
                    SUM(IF(kategori_komisi='KKQ02',jumlah_target,0)) as target_DS,
                    SUM(IF(kategori_komisi='KKQ03',jumlah_target,0)) as target_SP,
                    SUM(IF(kategori_komisi='KKQ04',jumlah_target,0)) as target_AR,
                    SUM(IF(kategori_komisi='KKQ05',jumlah_target,0)) as target_AB_AS_CG5,
                    SUM(IF(kategori_komisi='KKQ06',jumlah_target,0)) as target_SC
                    FROM
                    komisi_target_qty_detail k_detail
                    INNER JOIN komisi_target ON k_detail.kode_target = komisi_target.kode_target
                    INNER JOIN master_barang ON k_detail.kode_produk = master_barang.kode_produk
                    WHERE bulan ='$bulan' AND tahun='$tahun'
                    GROUP BY id_karyawan
                    ) komisi"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'komisi.id_karyawan');
                }
            );
            $query->leftJoin(
                DB::raw("(
                    SELECT historibayar.id_karyawan,SUM(bayar) as cashin_jt
                    FROM historibayar
                    INNER JOIN penjualan ON historibayar.no_fak_penj = penjualan.no_fak_penj
                    WHERE tglbayar BETWEEN '$dari' AND '$sampai' AND status_bayar IS NULL
                    AND datediff(tglbayar, tgltransaksi) > 15
                    GROUP BY historibayar.id_karyawan
                ) hbjt"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'hbjt.id_karyawan');
                }
            );

            if ($tglkomisi >= $tglsetkomisiljt) {
                $query->leftJoin(
                    DB::raw("(
                        SELECT
                            salesbarunew,IFNULL(SUM(penjualan.total),0) - SUM(IFNULL( totalretur, 0 )) - SUM(IFNULL( totalbayar, 0 )) AS sisapiutang
                        FROM
                        penjualan
                        LEFT JOIN (
                            SELECT
                                pj.no_fak_penj,IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,karyawan.nama_karyawan AS nama_sales,
                                IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                            FROM
                                penjualan pj
                            INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                            LEFT JOIN (
                                SELECT
                                    id_move,
                                    no_fak_penj,
                                    move_faktur.id_karyawan AS salesbaru,
                                    karyawan.kode_cabang AS cabangbaru
                                FROM
                                    move_faktur
                                INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                                WHERE id_move IN ( SELECT max( id_move ) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj )
                                ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj )
                        ) pjmove ON ( penjualan.no_fak_penj = pjmove.no_fak_penj )
                        LEFT JOIN (
                            SELECT
                                retur.no_fak_penj AS no_fak_penj,
                                SUM(IFNULL( subtotal_pf, 0 ) - IFNULL( subtotal_gb, 0 )) AS totalretur
                            FROM
                                retur
                            WHERE
                                tglretur BETWEEN '$dari' AND '$sampai'
                            GROUP BY
                                retur.no_fak_penj
                        ) r ON ( penjualan.no_fak_penj = r.no_fak_penj )
                        LEFT JOIN (
                            SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar
                            FROM historibayar
                            WHERE tglbayar BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                        ) hb ON ( penjualan.no_fak_penj = hb.no_fak_penj )

                    WHERE penjualan.tgltransaksi BETWEEN '$dari' AND '$sampai' AND jenistransaksi = 'kredit' AND datediff( '$sampai', penjualan.tgltransaksi ) > 15
                    GROUP BY
                        salesbarunew

                    ) penj"),
                    function ($join) {
                        $join->on('karyawan.id_karyawan', '=', 'penj.salesbarunew');
                    }
                );


                $query->leftJoin(
                    DB::raw("(
                        SELECT
                            salesbarunew,IFNULL( SUM(jumlah ), 0 ) - SUM(IFNULL( totalretur, 0 )) - SUM(IFNULL( totalbayar, 0 )) AS sisapiutangsaldo
                        FROM
                        saldoawal_piutang_faktur spf
                        INNER JOIN penjualan ON spf.no_fak_penj = penjualan.no_fak_penj
                        LEFT JOIN (
                                SELECT
                                    pj.no_fak_penj,IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,karyawan.nama_karyawan AS nama_sales,
                                    IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                                FROM
                                    penjualan pj
                                INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                                LEFT JOIN (
                                    SELECT
                                        id_move,
                                        no_fak_penj,
                                        move_faktur.id_karyawan AS salesbaru,
                                        karyawan.kode_cabang AS cabangbaru
                                    FROM
                                        move_faktur
                                    INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                                    WHERE id_move IN ( SELECT max( id_move ) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj )
                                    ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj )
                            ) pjmove ON ( penjualan.no_fak_penj = pjmove.no_fak_penj )
                            LEFT JOIN (
                                SELECT
                                    retur.no_fak_penj AS no_fak_penj,
                                    SUM(IFNULL( subtotal_pf, 0 ) - IFNULL( subtotal_gb, 0 )) AS totalretur
                                FROM
                                    retur
                                WHERE
                                    tglretur BETWEEN '$dari' AND '$sampai'
                                GROUP BY
                                    retur.no_fak_penj
                            ) r ON ( penjualan.no_fak_penj = r.no_fak_penj )
                            LEFT JOIN (
                                SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar
                                FROM historibayar
                                WHERE tglbayar BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                            ) hb ON ( penjualan.no_fak_penj = hb.no_fak_penj )
                        WHERE
                            datediff( '$sampai', penjualan.tgltransaksi ) > 15 AND bulan = '$bulan' AND tahun = '$tahun'
                        GROUP BY
                            salesbarunew
                ) spf"),
                    function ($join) {
                        $join->on('karyawan.id_karyawan', '=', 'spf.salesbarunew');
                    }
                );
            } else {
                $query->leftJoin(
                    DB::raw("(
                        SELECT salesbarunew,SUM((ifnull(penjualan.total,0) - (ifnull(totalpf_last,0)-ifnull(totalgb_last,0)))-ifnull(totalbayar,0)) as sisapiutang, 0 as sisapiutangsaldo
                        FROM penjualan
                        LEFT JOIN (
                            SELECT pj.no_fak_penj,
                            IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
                            IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
                            FROM penjualan pj
                            INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                            LEFT JOIN (
                                SELECT
                                id_move,no_fak_penj,
                                move_faktur.id_karyawan as salesbaru,
                                karyawan.kode_cabang  as cabangbaru
                                FROM move_faktur
                                INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                                WHERE id_move IN (SELECT max(id_move) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj)
                            ) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
                        ) pjmove ON (penjualan.no_fak_penj = pjmove.no_fak_penj)

                        LEFT JOIN (
                            SELECT retur.no_fak_penj AS no_fak_penj,
                            sum(retur.subtotal_gb) AS totalgb_last,
                            sum(retur.subtotal_pf) AS totalpf_last
                            FROM
                            retur
                            WHERE tglretur <= '$sampai'
                            GROUP BY
                            retur.no_fak_penj
                        ) r ON (penjualan.no_fak_penj = r.no_fak_penj)

                        LEFT JOIN (
                            SELECT no_fak_penj,sum( historibayar.bayar ) AS totalbayar
                            FROM historibayar
                            WHERE tglbayar <= '$sampai'
                            GROUP BY no_fak_penj
                            ) hblalu ON (penjualan.no_fak_penj = hblalu.no_fak_penj)
                        WHERE tgltransaksi <= '$sampai' AND (ifnull(penjualan.total,0) - (ifnull(totalpf_last,0)-ifnull(totalgb_last,0)))-ifnull(totalbayar,0) !=0 AND datediff('$sampai', penjualan.tgltransaksi) > 15
                        AND penjualan.jenistransaksi ='kredit'
                        GROUP BY salesbarunew
                    ) penj"),
                    function ($join) {
                        $join->on('karyawan.id_karyawan', '=', 'penj.salesbarunew');
                    }
                );
            }

            if ($aturankomisi == 2) {
                $query->leftJoin(
                    DB::raw("(
                    SELECT karyawan.id_karyawan,
                    (IFNULL(jml_belumsetorbulanlalu,0)+IFNULL(totalsetoran,0)) + IFNULL(jml_gmlast,0) - IFNULL(jml_gmnow,0) - IFNULL(jml_belumsetorbulanini,0) as realisasi_cashin
                    FROM karyawan
                    LEFT JOIN (
                        SELECT id_karyawan,jumlah as jml_belumsetorbulanlalu FROM belumsetor_detail
                        INNER JOIN belumsetor ON belumsetor_detail.kode_saldobs = belumsetor.kode_saldobs
                        WHERE bulan='$bulanlast' AND tahun='$tahunlast'
                    ) bs ON (karyawan.id_karyawan = bs.id_karyawan)

                    LEFT JOIN (
                        SELECT id_karyawan, SUM(lhp_tunai+lhp_tagihan) as totalsetoran FROM setoran_penjualan WHERE tgl_lhp BETWEEN '$dari' AND '$sampai' GROUP BY id_karyawan
                    ) sp ON (karyawan.id_karyawan = sp.id_karyawan)

                    LEFT JOIN (
                        SELECT
                        IFNULL(hb.id_karyawan,giro.id_karyawan) as id_karyawan,
                        SUM( jumlah ) AS jml_gmlast
                        FROM
                        giro
                        INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                        LEFT JOIN ( SELECT id_giro,id_karyawan FROM historibayar GROUP BY id_giro,id_karyawan ) AS hb ON giro.id_giro = hb.id_giro
                        WHERE
                        MONTH ( tgl_giro ) = '$bulanlast'
                        AND YEAR ( tgl_giro ) = '$tahunlast'
                        AND omset_tahun = '$tahun'
                        AND omset_bulan = '$bulan'
                        OR  MONTH ( tgl_giro ) = '$blnlast1'
                        AND YEAR ( tgl_giro ) = '$thnlast1'
                        AND omset_tahun = '$tahun'
                        AND omset_bulan = '$bulan'
                        GROUP BY
                        IFNULL( hb.id_karyawan, giro.id_karyawan )
                    ) gmlast ON (karyawan.id_karyawan = gmlast.id_karyawan)
                    LEFT JOIN (
                    SELECT
                        IFNULL(hb.id_karyawan,giro.id_karyawan) as id_karyawan,
                        SUM( jumlah ) AS jml_gmnow
                    FROM
                        giro
                        INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                        LEFT JOIN ( SELECT id_giro,id_karyawan, tglbayar FROM historibayar GROUP BY id_giro, tglbayar,id_karyawan ) AS hb ON giro.id_giro = hb.id_giro
                    WHERE
                        tgl_giro >= '$dari'
                        AND tgl_giro <= '$sampai' AND tglbayar IS NULL AND omset_bulan = '0' AND omset_tahun = ''
                        OR  tgl_giro >= '$dari'
                        AND tgl_giro <= '$sampai' AND tglbayar >= '$end'
                        AND omset_bulan > '$bulan'
                        AND omset_tahun >= '$tahun'
                    GROUP BY
                        giro.id_karyawan,
                        hb.id_karyawan
                    ) gmnow ON (karyawan.id_karyawan = gmnow.id_karyawan)

                    LEFT JOIN (
                        SELECT belumsetor_detail.id_karyawan, SUM(jumlah) as jml_belumsetorbulanini
                        FROM belumsetor_detail
                        INNER JOIN belumsetor ON belumsetor_detail.kode_saldobs = belumsetor.kode_saldobs
                        WHERE bulan ='$bulan' AND tahun ='$tahun' GROUP BY id_karyawan
                    ) bsnow ON (karyawan.id_karyawan = bsnow.id_karyawan)
                    ) hb"),
                    function ($join) {
                        $join->on('karyawan.id_karyawan', '=', 'hb.id_karyawan');
                    }
                );
            } else if ($aturankomisi == 3) {
                $query->leftJoin(
                    DB::raw("(
                        SELECT historibayar.id_karyawan,SUM(bayar) as realisasi_cashin
                        FROM historibayar WHERE tglbayar BETWEEN '$dari' AND '$sampai' AND status_bayar IS NULL
                        GROUP BY historibayar.id_karyawan
                    ) hb"),
                    function ($join) {
                        $join->on('karyawan.id_karyawan', '=', 'hb.id_karyawan');
                    }
                );
            } else if ($aturankomisi == 4) {
                $query->leftJoin(
                    DB::raw("(
                        SELECT historibayar.id_karyawan,SUM(bayar) as realisasi_cashin
                        FROM historibayar
                        INNER JOIN penjualan ON historibayar.no_fak_penj = penjualan.no_fak_penj
                        WHERE tglbayar BETWEEN '$dari' AND '$sampai' AND status_bayar IS NULL
                        AND datediff(tglbayar, tgltransaksi) <= 14
                        GROUP BY historibayar.id_karyawan
                    ) hb"),
                    function ($join) {
                        $join->on('karyawan.id_karyawan', '=', 'hb.id_karyawan');
                    }
                );
            }

            if ($tglkomisi >= $tglsetkomisi2) {
                $query->leftJoin(
                    DB::raw("(
                    SELECT salesbarunew,
                    SUM(IF(kode_produk = 'AB' AND promo !='1' OR kode_produk = 'AB' AND promo IS NULL,jumlah,0)) as AB,
                    SUM(IF(kode_produk = 'AR' AND promo !='1' OR kode_produk = 'AR' AND promo IS NULL,jumlah,0)) as AR,
                    SUM(IF(kode_produk = 'AS' AND promo !='1' OR kode_produk = 'AS' AND promo IS NULL ,jumlah,0)) as `AS`,
                    SUM(IF(kode_produk = 'BB' AND promo !='1' OR kode_produk = 'BB' AND promo IS NULL,jumlah,0)) as BB,
                    SUM(IF(kode_produk = 'CG' AND promo !='1' OR kode_produk = 'CG' AND promo IS NULL,jumlah,0)) as CG,
                    SUM(IF(kode_produk = 'CGG' AND promo !='1' OR kode_produk = 'CGG' AND promo IS NULL,jumlah,0)) as CGG,
                    SUM(IF(kode_produk = 'DEP' AND promo !='1' OR kode_produk = 'DEP' AND promo IS NULL,jumlah,0)) as DEP,
                    SUM(IF(kode_produk = 'DK' AND promo !='1' OR kode_produk = 'DK' AND promo IS NULL,jumlah,0)) as DK,
                    SUM(IF(kode_produk = 'DS' AND promo !='1' OR kode_produk = 'DS' AND promo IS NULL,jumlah,0)) as DS,
                    SUM(IF(kode_produk = 'SP' AND promo !='1' OR kode_produk = 'SP' AND promo IS NULL,jumlah,0)) as SP,
                    SUM(IF(kode_produk = 'BBP' AND promo !='1' OR kode_produk = 'BBP' AND promo IS NULL,jumlah,0)) as BBP,
                    SUM(IF(kode_produk = 'SPP' AND promo !='1' OR kode_produk = 'SPP' AND promo IS NULL,jumlah,0)) as SPP,
                    SUM(IF(kode_produk = 'CG5' AND promo !='1' OR kode_produk = 'CG5' AND promo IS NULL,jumlah,0)) as CG5,
                    SUM(IF(kode_produk = 'SP8' AND promo !='1' OR kode_produk = 'SP8' AND promo IS NULL,jumlah,0)) as SP8,
                    SUM(IF(kode_produk = 'SC' AND promo !='1' OR kode_produk = 'SC' AND promo IS NULL,jumlah,0)) as SC,
                    SUM(IF(kode_produk = 'SP500' AND promo !='1' OR kode_produk = 'SP500' AND promo IS NULL,jumlah,0)) as SP500
                    FROM detailpenjualan
                    INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                    INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang

                   LEFT JOIN (
                    SELECT pj.no_fak_penj,
                    IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
                    IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
                    FROM penjualan pj
                    INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                    LEFT JOIN (
                        SELECT
                        id_move,no_fak_penj,
                        move_faktur.id_karyawan as salesbaru,
                        karyawan.kode_cabang  as cabangbaru
                        FROM move_faktur
                        INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                        WHERE id_move IN (SELECT max(id_move) FROM move_faktur WHERE tgl_move <= '$dari' GROUP BY no_fak_penj)
                    ) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
                   ) pjmove ON (penjualan.no_fak_penj = pjmove.no_fak_penj)


                    WHERE  status_lunas ='1' AND tgl_pelunasan BETWEEN '$dari' AND '$sampai'
                    GROUP BY salesbarunew
                ) realisasi"),
                    function ($join) {
                        $join->on('karyawan.id_karyawan', '=', 'realisasi.salesbarunew');
                    }
                );
            } else {

                $query->leftJoin(
                    DB::raw("(
                    SELECT penjualan.id_karyawan,
                    SUM(IF(kode_produk = 'AB' AND promo !='1' OR kode_produk = 'AB' AND promo IS NULL,jumlah,0)) as AB,
                    SUM(IF(kode_produk = 'AR' AND promo !='1' OR kode_produk = 'AR' AND promo IS NULL,jumlah,0)) as AR,
                    SUM(IF(kode_produk = 'AS' AND promo !='1' OR kode_produk = 'AS' AND promo IS NULL ,jumlah,0)) as `AS`,
                    SUM(IF(kode_produk = 'BB' AND promo !='1' OR kode_produk = 'BB' AND promo IS NULL,jumlah,0)) as BB,
                    SUM(IF(kode_produk = 'CG' AND promo !='1' OR kode_produk = 'CG' AND promo IS NULL,jumlah,0)) as CG,
                    SUM(IF(kode_produk = 'CGG' AND promo !='1' OR kode_produk = 'CGG' AND promo IS NULL,jumlah,0)) as CGG,
                    SUM(IF(kode_produk = 'DEP' AND promo !='1' OR kode_produk = 'DEP' AND promo IS NULL,jumlah,0)) as DEP,
                    SUM(IF(kode_produk = 'DK' AND promo !='1' OR kode_produk = 'DK' AND promo IS NULL,jumlah,0)) as DK,
                    SUM(IF(kode_produk = 'DS' AND promo !='1' OR kode_produk = 'DS' AND promo IS NULL,jumlah,0)) as DS,
                    SUM(IF(kode_produk = 'SP' AND promo !='1' OR kode_produk = 'SP' AND promo IS NULL,jumlah,0)) as SP,
                    SUM(IF(kode_produk = 'BBP' AND promo !='1' OR kode_produk = 'BBP' AND promo IS NULL,jumlah,0)) as BBP,
                    SUM(IF(kode_produk = 'SPP' AND promo !='1' OR kode_produk = 'SPP' AND promo IS NULL,jumlah,0)) as SPP,
                    SUM(IF(kode_produk = 'CG5' AND promo !='1' OR kode_produk = 'CG5' AND promo IS NULL,jumlah,0)) as CG5,
                    SUM(IF(kode_produk = 'SP8' AND promo !='1' OR kode_produk = 'SP8' AND promo IS NULL,jumlah,0)) as SP8,
                    SUM(IF(kode_produk = 'SC' AND promo !='1' OR kode_produk = 'SC' AND promo IS NULL,jumlah,0)) as SC,
                    SUM(IF(kode_produk = 'SP500' AND promo !='1' OR kode_produk = 'SP500' AND promo IS NULL,jumlah,0)) as SP500
                    FROM detailpenjualan
                    INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                    INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang


                    WHERE  status_lunas ='1' AND tgl_pelunasan BETWEEN '$dari' AND '$sampai'
                    GROUP BY penjualan.id_karyawan
                ) realisasi"),
                    function ($join) {
                        $join->on('karyawan.id_karyawan', '=', 'realisasi.id_karyawan');
                    }
                );
            }

            $query->leftJoin(
                DB::raw("(
                    SELECT penjualan.id_karyawan,
                    SUM(IF(kode_produk = 'AB',jumlah,0)) as retur_AB,
                    SUM(IF(kode_produk = 'AR',jumlah,0)) as retur_AR,
                    SUM(IF(kode_produk = 'AS',jumlah,0)) as `retur_AS`,
                    SUM(IF(kode_produk = 'BB',jumlah,0)) as retur_BB,
                    SUM(IF(kode_produk = 'CG' ,jumlah,0)) as retur_CG,
                    SUM(IF(kode_produk = 'CGG',jumlah,0)) as retur_CGG,
                    SUM(IF(kode_produk = 'DEP',jumlah,0)) as retur_DEP,
                    SUM(IF(kode_produk = 'DK',jumlah,0)) as retur_DK,
                    SUM(IF(kode_produk = 'DS',jumlah,0)) as retur_DS,
                    SUM(IF(kode_produk = 'SP',jumlah,0)) as retur_SP,
                    SUM(IF(kode_produk = 'BBP',jumlah,0)) as retur_BBP,
                    SUM(IF(kode_produk = 'SPP',jumlah,0)) as retur_SPP,
                    SUM(IF(kode_produk = 'CG5',jumlah,0)) as retur_CG5,
                    SUM(IF(kode_produk = 'SP8',jumlah,0)) as retur_SP8,
                    SUM(IF(kode_produk = 'SC',jumlah,0)) as retur_SC,
                    SUM(IF(kode_produk = 'SP500',jumlah,0)) as retur_SP500
                    FROM detailretur
                    INNER JOIN retur ON detailretur.no_retur_penj = retur.no_retur_penj
                    INNER JOIN penjualan ON retur.no_fak_penj = penjualan.no_fak_penj
                    INNER JOIN barang ON detailretur.kode_barang = barang.kode_barang

                    WHERE  status_lunas ='1' AND tgl_pelunasan BETWEEN '$dari' AND '$sampai'
                    GROUP BY penjualan.id_karyawan
                ) returpf"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'returpf.id_karyawan');
                }
            );



            $query->leftJoin(
                DB::raw("(
                    SELECT id_karyawan,jumlah as potongankomisi,keterangan as ket_potongan
                    FROM komisi_potongan
                    WHERE bulan = '$bulan' AND tahun='$tahun'
                ) potongankomisi"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'potongankomisi.id_karyawan');
                }
            );

            $query->leftJoin(
                DB::raw("(
                    SELECT id_karyawan,jumlah as komisifix, keterangan as ket_komisifix
                    FROM komisi_akhir
                    WHERE bulan = '$bulan' AND tahun='$tahun'
                ) komisiakhir"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'komisiakhir.id_karyawan');
                }
            );

            if (Auth::user()->id == 27) {
                $query->whereIn('kode_cabang', ['BDG', 'PWK']);
            } else {
                $query->where('kode_cabang', $cabang);
            }
            $query->where('nama_karyawan', '!=', '');
            $komisi = $query->get();
        }
        $nmbulan  = $namabulan[$bulan];

        $kodekp = 'KP' . $cabang;
        $potongankp = DB::table('komisi_potongan')->where('id_karyawan', $kodekp)
            ->where('bulan', $bulan)->where('tahun', $tahun)
            ->first();

        $komisiakhir = DB::table('komisi_akhir')->where('id_karyawan', $kodekp)
            ->where('bulan', $bulan)->where('tahun', $tahun)
            ->first();
        if (isset($_POST['export'])) {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Komisi $time.xls");
        }

        if ($bulan == 7 && $tahun == 2022) {
            echo 1;
            die;
            return view('targetkomisi.laporan.cetak_komisi_juli', compact('komisi', 'cbg', 'nmbulan', 'tahun', 'produk', 'driver', 'helper', 'gudang', 'tunaikredit', 'bulan', 'cabang'));
        } elseif ($bulan == 8 && $tahun == 2022) {
            echo 2;
            die;
            return view('targetkomisi.laporan.cetak_komisi_agustus', compact('komisi', 'cbg', 'nmbulan', 'tahun', 'produk', 'driver', 'helper', 'gudang', 'tunaikredit', 'bulan', 'cabang'));
        } elseif ($bulan == 9 && $tahun == 2022) {
            echo 3;
            die;
            return view('targetkomisi.laporan.cetak_komisi_september', compact('komisi', 'cbg', 'nmbulan', 'tahun', 'produk', 'driver', 'helper', 'gudang', 'tunaikredit', 'bulan', 'cabang'));
        } elseif ($bulan < 7 && $tahun <= 2022) {
            echo 4;
            die;
            return view('targetkomisi.laporan.cetak_komisi_juni', compact('komisi', 'cbg', 'nmbulan', 'tahun', 'produk', 'driver', 'helper', 'gudang', 'tunaikredit', 'bulan', 'cabang'));
        } else {
            echo 5;
            die;
            return view('targetkomisi.laporan.cetak_komisi_lpu', compact('komisi', 'cbg', 'nmbulan', 'tahun', 'produk', 'driver', 'helper', 'gudang', 'tunaikredit', 'bulan', 'cabang', 'potongankp', 'komisiakhir'));
        }
    }

    public function laporankomisidriverhelper()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        return view('targetkomisi.laporan.frm.lap_komisidriverhelper', compact('cabang'));
    }

    public function cetakkomisidriverhelper(Request $request)
    {
        $cabang = $request->kode_cabang;
        $dari = $request->dari;
        lockreport($dari);
        $sampai = $request->sampai;
        $tanggal = explode("-", $dari);
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];


        $cbg = Cabang::where('kode_cabang', $cabang)->first();
        $driver = DB::table('driver_helper')
            ->selectRaw("driver_helper.id_driver_helper,nama_driver_helper,kategori,IFNULL(jml_driver,0) as jml_driver,driver_helper.ratio as ratiodefault,
            driver_helper.ratio_helper as ratiohelperdefault,
            ratioaktif,ratiohelperaktif,ratioterakhir,ratiohelperterakhir")
            ->join(
                DB::raw("(
                        SELECT id_driver,ROUND(SUM(jml_penjualan),2) as jml_driver
                        FROM detail_dpb
                        INNER JOIN dpb ON detail_dpb.no_dpb = dpb.no_dpb
                        WHERE tgl_pengambilan BETWEEN '$dari' AND '$sampai' GROUP BY id_driver
                    ) driver"),
                function ($join) {
                    $join->on('driver_helper.id_driver_helper', '=', 'driver.id_driver');
                }
            )
            ->leftJoin(
                DB::raw("(
                    SELECT id,set_ratio_komisi.ratio as ratioaktif,set_ratio_komisi.ratio_helper as ratiohelperaktif
                    FROM set_ratio_komisi
                    INNER JOIN driver_helper ON set_ratio_komisi.id = driver_helper.id_driver_helper
                    WHERE bulan = '$bulan' AND tahun = '$tahun' AND kode_cabang='$cabang'
                    ) ratio"),
                function ($join) {
                    $join->on('driver_helper.id_driver_helper', '=', 'ratio.id');
                }
            )
            ->leftJoin(
                DB::raw("(
                    SELECT id,set_ratio_komisi.ratio as ratioterakhir,set_ratio_komisi.ratio_helper as ratiohelperterakhir
                    FROM set_ratio_komisi
                    INNER JOIN driver_helper ON set_ratio_komisi.id = driver_helper.id_driver_helper
                    WHERE kode_cabang ='$cabang' AND tgl_berlaku IN (SELECT max(tgl_berlaku) FROM set_ratio_komisi)
                    ) lastratio"),
                function ($join) {
                    $join->on('driver_helper.id_driver_helper', '=', 'lastratio.id');
                }
            )
            ->where('kode_cabang', $cabang)
            ->orderBy('kategori')
            ->orderBy('nama_driver_helper')
            ->get();

        $helper = DB::table('driver_helper')
            ->selectRaw("driver_helper.id_driver_helper,nama_driver_helper,kategori,IFNULL(jml_helper,0) + IFNULL(jml_helper_2,0) + IFNULL(jml_helper_3,0) as jml_helper,driver_helper.ratio as ratiodefault,
            driver_helper.ratio_helper as ratiohelperdefault,
            ratioaktif,ratiohelperaktif,ratioterakhir,ratiohelperterakhir")
            ->leftJoin(
                DB::raw("(
                    SELECT id_helper,ROUND(SUM(jml_helper),2) as jml_helper
                    FROM dpb
                    WHERE tgl_pengambilan BETWEEN '$dari' AND '$sampai' GROUP BY id_helper
                    ) helper"),
                function ($join) {
                    $join->on('driver_helper.id_driver_helper', '=', 'helper.id_helper');
                }
            )
            ->leftJoin(
                DB::raw("(
                    SELECT id_helper_2,ROUND(SUM(jml_helper_2),2) as jml_helper_2
                    FROM dpb
                    WHERE tgl_pengambilan BETWEEN '$dari' AND '$sampai' GROUP BY id_helper_2
                    ) helper2"),
                function ($join) {
                    $join->on('driver_helper.id_driver_helper', '=', 'helper2.id_helper_2');
                }
            )

            ->leftJoin(
                DB::raw("(
                    SELECT id_helper_3,ROUND(SUM(jml_helper_3),2) as jml_helper_3
                    FROM dpb
                    WHERE tgl_pengambilan BETWEEN '$dari' AND '$sampai' GROUP BY id_helper_3
                    ) helper3"),
                function ($join) {
                    $join->on('driver_helper.id_driver_helper', '=', 'helper3.id_helper_3');
                }
            )

            ->leftJoin(
                DB::raw("(
                    SELECT id,set_ratio_komisi.ratio as ratioaktif,set_ratio_komisi.ratio_helper as ratiohelperaktif
                    FROM set_ratio_komisi
                    INNER JOIN driver_helper ON set_ratio_komisi.id = driver_helper.id_driver_helper
                    WHERE bulan = '$bulan' AND tahun = '$tahun' AND kode_cabang='$cabang'
                    ) ratio"),
                function ($join) {
                    $join->on('driver_helper.id_driver_helper', '=', 'ratio.id');
                }
            )

            ->leftJoin(
                DB::raw("(
                    SELECT id,set_ratio_komisi.ratio as ratioterakhir,set_ratio_komisi.ratio_helper as ratiohelperterakhir
                    FROM set_ratio_komisi
                    INNER JOIN driver_helper ON set_ratio_komisi.id = driver_helper.id_driver_helper
                    WHERE kode_cabang ='$cabang' AND tgl_berlaku IN (SELECT max(tgl_berlaku) FROM set_ratio_komisi)
                    ) lastratio"),
                function ($join) {
                    $join->on('driver_helper.id_driver_helper', '=', 'lastratio.id');
                }
            )
            ->where('kode_cabang', $cabang)
            ->whereRaw('(IFNULL(jml_helper,0) + IFNULL(jml_helper_2,0) + IFNULL(jml_helper_3,0)) != 0.00')
            ->orderBy('kategori')
            ->orderBy('nama_driver_helper')
            ->get();

        $tunaikredit = DB::table('detailpenjualan')
            ->selectRaw("IFNULL(FLOOR(SUM( IF ( kode_produk = 'AB', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'AR', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'AS', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'BB', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'CG', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'CGG', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'DB', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'DEP', detailpenjualan.jumlah/isipcsdus,NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'DK', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'DS', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'SP', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'BBP', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'SPP', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'CG5', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'SC', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) +
            IFNULL(FLOOR(SUM( IF ( kode_produk = 'SP8', detailpenjualan.jumlah/isipcsdus, NULL ) )),0) AS total")
            ->join('barang', 'detailpenjualan.kode_barang', '=', 'barang.kode_barang')
            ->join('penjualan', 'detailpenjualan.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->where('promo', '!=', 1)
            ->whereBetween('tgltransaksi', [$dari, $sampai])
            ->where('karyawan.kode_cabang', $cabang)
            ->orWhereNull('promo')
            ->whereBetween('tgltransaksi', [$dari, $sampai])
            ->where('karyawan.kode_cabang', $cabang)
            ->first();

        $gudang = DB::table('driver_helper')
            ->selectRaw("id_driver_helper,nama_driver_helper ,driver_helper.ratio as ratiodefault,ratioaktif,ratioterakhir")
            ->leftJoin(
                DB::raw("(
                    SELECT id,set_ratio_komisi.ratio as ratioaktif
                    FROM set_ratio_komisi
                    INNER JOIN driver_helper ON set_ratio_komisi.id = driver_helper.id_driver_helper
                    WHERE bulan = '$bulan' AND tahun = '$tahun' AND kode_cabang='$cabang'
                    ) ratio"),
                function ($join) {
                    $join->on('driver_helper.id_driver_helper', '=', 'ratio.id');
                }
            )

            ->leftJoin(
                DB::raw("(
                    SELECT id,set_ratio_komisi.ratio as ratioterakhir
                    FROM set_ratio_komisi
                    INNER JOIN driver_helper ON set_ratio_komisi.id = driver_helper.id_driver_helper
                    WHERE kode_cabang ='$cabang' AND tgl_berlaku IN (SELECT max(tgl_berlaku) FROM set_ratio_komisi)
                    ) lastratio"),
                function ($join) {
                    $join->on('driver_helper.id_driver_helper', '=', 'lastratio.id');
                }
            )
            ->where('kode_cabang', $cabang)
            ->where('kategori', 'GUDANG')
            ->get();

        if (isset($_POST['export'])) {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Komisi Driver Helper $time.xls");
        }
        return view('targetkomisi.laporan.cetak_komisi_driverhelper', compact('cbg', 'dari', 'sampai', 'driver', 'helper', 'gudang', 'tunaikredit'));
    }

    public function laporaninsentif()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('targetkomisi.laporan.frm.lap_insentif', compact('bulan', 'cabang'));
    }

    public function cetaklaporaninsentif(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        lockyear($tahun);
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $dari = $tahun . "-" . $bulan . "-01";
        $hariini = date('Y-m-d');
        $sampai = date('Y-m-t', strtotime($dari));
        if ($hariini < $sampai) {
            $sampai = $hariini;
        } else {
            $sampai = $sampai;
        }

        $cbg = Auth::user()->kode_cabang;
        $kode_cabang = $request->kode_cabang;
        $cabang = DB::table('cabang')->where('kode_cabang', $cbg)->first();
        $lastmonth = date('Y-m-d', strtotime(date($dari) . '- 1 month'));
        $lastdate = explode("-", $lastmonth);
        $bulanlast = $lastdate[1] + 0;
        $tahunlast = $lastdate[0];
        if ($bulanlast == 1) {
            $blnlast1 = 12;
            $thnlast1 = $tahun - 1;
        } else {
            $blnlast1 = $bulanlast - 1;
            $thnlast1 = $tahun;
        }

        if ($bulan == 12) {
            $bln = 1;
            $thn = $tahun + 1;
        } else {
            $bln = $bulan + 1;
            $thn = $tahun;
        }

        $query = Cabang::query();
        $query->selectRaw("cabang.kode_cabang,nama_cabang,(IFNULL(jml_belumsetorbulanlalu,0) + IFNULL(totalsetoran,0) + IFNULL(jml_gmlast,0) - IFNULL(jml_gmnow,0) - IFNULL(jml_belumsetorbulanini,0)) as cashin,
        IFNULL(sisapiutangsaldo,0) + IFNULL(sisapiutang,0) as sisapiutang
        ,lamalpc,jam_lpc,cashin_jt");
        $query->leftJoin(
            DB::raw("(
                SELECT belumsetor.kode_cabang,SUM(jumlah) as jml_belumsetorbulanlalu
                FROM belumsetor_detail
                INNER JOIN belumsetor ON belumsetor_detail.kode_saldobs = belumsetor.kode_saldobs
                WHERE bulan='$bulanlast' AND tahun='$tahunlast'
                GROUP BY belumsetor.kode_cabang
            ) bs"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'bs.kode_cabang');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT kode_cabang, SUM(lhp_tunai+lhp_tagihan) as totalsetoran
                FROM setoran_penjualan
                WHERE tgl_lhp BETWEEN '$dari' AND '$sampai' GROUP BY kode_cabang
            ) sp"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'sp.kode_cabang');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT
                karyawan.kode_cabang,
                SUM( jumlah ) AS jml_gmlast
                FROM
                giro
                INNER JOIN karyawan ON giro.id_karyawan = karyawan.id_karyawan
                INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                LEFT JOIN ( SELECT id_giro FROM historibayar GROUP BY id_giro ) AS hb ON giro.id_giro = hb.id_giro
                WHERE
                MONTH ( tgl_giro ) = '$bulanlast'
                AND YEAR ( tgl_giro ) = '$tahunlast'
                AND omset_tahun = '$tahun'
                AND omset_bulan = '$bulan'
                OR MONTH ( tgl_giro ) = '$blnlast1'
                AND YEAR ( tgl_giro ) = '$thnlast1'
                AND omset_tahun = '$tahun'
                AND omset_bulan = '$bulan'
                GROUP BY
                karyawan.kode_cabang
            ) gmlast"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'gmlast.kode_cabang');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT
                karyawan.kode_cabang,
                SUM( jumlah ) AS jml_gmnow
                FROM
                giro
                INNER JOIN karyawan ON giro.id_karyawan = karyawan.id_karyawan
                INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                LEFT JOIN (
                SELECT kode_cabang,MAX(tgl_diterimapusat) as tgl_diterimapusat
                FROM setoran_pusat
                WHERE omset_bulan = '$bulan' AND omset_tahun ='$thn'
                AND MONTH(tgl_diterimapusat) = '$bln' AND YEAR(tgl_diterimapusat) = '$thn'
                GROUP BY kode_cabang
                ) nexttgl ON (karyawan.kode_cabang = nexttgl.kode_cabang)
                LEFT JOIN ( SELECT id_giro, tglbayar FROM historibayar GROUP BY id_giro, tglbayar ) AS hb ON giro.id_giro = hb.id_giro
                WHERE
                tgl_giro >= '$dari'
                AND tgl_giro <= '$sampai' AND tglbayar IS NULL AND omset_bulan='0' AND omset_tahun='' OR tgl_giro>= '$dari'
                AND tgl_giro <= '$sampai' AND tglbayar>= IFNULL(tgl_diterimapusat,'$sampai')
                AND omset_bulan > '$bulan'
                AND omset_tahun >= '$tahun'
                GROUP BY
                karyawan.kode_cabang
            ) gmnow"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'gmnow.kode_cabang');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT kode_cabang, SUM(jumlah) as jml_belumsetorbulanini
                FROM belumsetor_detail
                INNER JOIN belumsetor ON belumsetor_detail.kode_saldobs = belumsetor.kode_saldobs
                WHERE bulan ='$bulan' AND tahun ='$tahun' GROUP BY kode_cabang
            ) bsnow"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'bsnow.kode_cabang');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT kode_cabang,datediff(tgl_lpc,'$sampai') as lamalpc,jam_lpc
                FROM lpc
                WHERE bulan ='$bulan' AND tahun = '$tahun'
            ) app_lpc"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'app_lpc.kode_cabang');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT karyawan.kode_cabang,SUM(bayar) as cashin_jt
                FROM historibayar
                INNER JOIN penjualan ON historibayar.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN karyawan ON historibayar.id_karyawan = karyawan.id_karyawan
                WHERE tglbayar BETWEEN '$dari' AND '$sampai' AND status_bayar IS NULL
                AND datediff(tglbayar, tgltransaksi) > 15
                GROUP BY karyawan.kode_cabang
            ) hbjt"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'hbjt.kode_cabang');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT
                    cabangbarunew,IFNULL(SUM(penjualan.total),0) - SUM(IFNULL( totalretur, 0 )) - SUM(IFNULL( totalbayar, 0 )) AS sisapiutang
                FROM
                penjualan
                INNER JOIN pelanggan ON penjualan.kode_pelanggan = pelanggan.kode_pelanggan
                LEFT JOIN (
                    SELECT
                        pj.no_fak_penj,IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,karyawan.nama_karyawan AS nama_sales,
                        IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                    FROM
                        penjualan pj
                    INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                    LEFT JOIN (
                        SELECT
                            id_move,
                            no_fak_penj,
                            move_faktur.id_karyawan AS salesbaru,
                            karyawan.kode_cabang AS cabangbaru
                        FROM
                            move_faktur
                        INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                        WHERE id_move IN ( SELECT max( id_move ) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj )
                        ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj )
                ) pjmove ON ( penjualan.no_fak_penj = pjmove.no_fak_penj )
                LEFT JOIN (
                    SELECT
                        retur.no_fak_penj AS no_fak_penj,
                        SUM(IFNULL( subtotal_pf, 0 ) - IFNULL( subtotal_gb, 0 )) AS totalretur
                    FROM
                        retur
                    WHERE
                        tglretur BETWEEN '$dari' AND '$sampai'
                    GROUP BY
                        retur.no_fak_penj
                ) r ON ( penjualan.no_fak_penj = r.no_fak_penj )
                LEFT JOIN (
                    SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar
                    FROM historibayar
                    WHERE tglbayar BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                ) hb ON ( penjualan.no_fak_penj = hb.no_fak_penj )

            WHERE penjualan.tgltransaksi BETWEEN '$dari' AND '$sampai' AND jenistransaksi = 'kredit'
            AND datediff( '$sampai', penjualan.tgltransaksi ) > 30
            AND penjualan.id_karyawan NOT IN ('SGRT01','SGRT02')
            GROUP BY
                cabangbarunew

            ) penj"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'penj.cabangbarunew');
            }
        );


        $query->leftJoin(
            DB::raw("(
                SELECT
                    cabangbarunew,IFNULL( SUM(jumlah ), 0 ) - SUM(IFNULL( totalretur, 0 )) - SUM(IFNULL( totalbayar, 0 )) AS sisapiutangsaldo
                FROM
                saldoawal_piutang_faktur spf
                INNER JOIN penjualan ON spf.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN pelanggan ON penjualan.kode_pelanggan = pelanggan.kode_pelanggan
                LEFT JOIN (
                        SELECT
                            pj.no_fak_penj,IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,karyawan.nama_karyawan AS nama_sales,
                            IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                        FROM
                            penjualan pj
                        INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                        LEFT JOIN (
                            SELECT
                                id_move,
                                no_fak_penj,
                                move_faktur.id_karyawan AS salesbaru,
                                karyawan.kode_cabang AS cabangbaru
                            FROM
                                move_faktur
                            INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                            WHERE id_move IN ( SELECT max( id_move ) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj )
                            ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj )
                    ) pjmove ON ( penjualan.no_fak_penj = pjmove.no_fak_penj )
                    LEFT JOIN (
                        SELECT
                            retur.no_fak_penj AS no_fak_penj,
                            SUM(IFNULL( subtotal_pf, 0 ) - IFNULL( subtotal_gb, 0 )) AS totalretur
                        FROM
                            retur
                        WHERE
                            tglretur BETWEEN '$dari' AND '$sampai'
                        GROUP BY
                            retur.no_fak_penj
                    ) r ON ( penjualan.no_fak_penj = r.no_fak_penj )
                    LEFT JOIN (
                        SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar
                        FROM historibayar
                        WHERE tglbayar BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                    ) hb ON ( penjualan.no_fak_penj = hb.no_fak_penj )
                WHERE
                    datediff( '$sampai', penjualan.tgltransaksi ) > 30
                    AND penjualan.id_karyawan NOT IN ('SGRT01','SGRT02')
                    AND bulan = '$bulan' AND tahun = '$tahun'
                GROUP BY
                cabangbarunew
            ) spf"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'spf.cabangbarunew');
            }
        );
        if ($bulan < 9 and $tahun <= 2022) {
            $query->where('cabang.kode_cabang', '!=', 'GRT');
        }
        if ($cbg != "PCF") {
            $query->where('cabang.kode_cabang', $kode_cabang);
        }
        $insentif = $query->get();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Insentif $dari-$sampai.xls");
        }
        if ($bulan >= 10 && $tahun == 2022) {
            echo "A";
            return view('targetkomisi.laporan.cetak_insentif_oktober2022', compact('insentif', 'cabang', 'namabulan', 'bulan', 'tahun'));
        } elseif ($bulan == 9 && $tahun == 2022) {
            echo "B";
            return view('targetkomisi.laporan.cetak_insentif_september2022', compact('insentif', 'cabang', 'namabulan', 'bulan', 'tahun'));
        } else if ($bulan >= 5 && $tahun == 2022) {
            echo "C";
            return view('targetkomisi.laporan.cetak_insentif_mei2022', compact('insentif', 'cabang', 'namabulan', 'bulan', 'tahun'));
        } else {
            echo "D";
            return view('targetkomisi.laporan.cetak_insentif', compact('insentif', 'cabang', 'namabulan', 'bulan', 'tahun'));
        }
    }

    public function getrealisasitargetsales(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $id_karyawan = Auth::user()->id_salesman;

        $realisasitarget = DB::table('komisi_target_qty_detail')
            ->selectRaw('komisi_target_qty_detail.*,nama_barang,realisasi,isipcsdus')
            ->join('komisi_target', 'komisi_target_qty_detail.kode_target', '=', 'komisi_target.kode_target')
            ->join('master_barang', 'komisi_target_qty_detail.kode_produk', '=', 'master_barang.kode_produk')
            ->leftJoin(
                DB::raw("(
                SELECT kode_produk,
                SUM(jumlah) as realisasi
                FROM detailpenjualan
                INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                LEFT JOIN (
                SELECT pj.no_fak_penj,
                IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
                IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
                FROM penjualan pj
                INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                    LEFT JOIN (
                        SELECT MAX(id_move) as id_move,no_fak_penj,move_faktur.id_karyawan as salesbaru,karyawan.kode_cabang as cabangbaru
                        FROM move_faktur
                        INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                        WHERE tgl_move <= '$dari'
                        GROUP BY no_fak_penj,move_faktur.id_karyawan,karyawan.kode_cabang
                    ) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
                ) pjmove ON (penjualan.no_fak_penj = pjmove.no_fak_penj)


                WHERE tgltransaksi BETWEEN '$dari' AND '$sampai' AND promo != 1 AND salesbarunew = '$id_karyawan'
                OR tgltransaksi BETWEEN '$dari' AND '$sampai' AND promo IS NULL AND salesbarunew = '$id_karyawan'
                GROUP BY kode_produk
            ) realisasi"),
                function ($join) {
                    $join->on('komisi_target_qty_detail.kode_produk', '=', 'realisasi.kode_produk');
                }
            )
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('komisi_target_qty_detail.id_karyawan', $id_karyawan)
            ->where('jumlah_target', '!=', 0)
            ->get();

        return view('targetkomisi.getrealisasitarget', compact('realisasitarget'));
    }

    public function inputpotongankomisi(Request $request)
    {
        $id_karyawan = $request->id_karyawan;
        $kode = substr($id_karyawan, 0, 2);
        $karyawan = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->first();
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $cek = DB::table('komisi_potongan')->where('id_karyawan', $id_karyawan)->where('bulan', $bulan)->where('tahun', $tahun)->first();
        return view('targetkomisi.inputpotongankomisi', compact('id_karyawan', 'karyawan', 'bulan', 'tahun', 'cek', 'kode', 'id_karyawan'));
    }


    public function inputkomisiakhir(Request $request)
    {
        $id_karyawan = $request->id_karyawan;
        $karyawan = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->first();
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $cek = DB::table('komisi_akhir')->where('id_karyawan', $id_karyawan)->where('bulan', $bulan)->where('tahun', $tahun)->first();
        return view('targetkomisi.inputkomisiakhir', compact('id_karyawan', 'karyawan', 'bulan', 'tahun', 'cek'));
    }

    public function storepotongankomisi(Request $request)
    {
        $id_karyawan = $request->id_karyawan;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $potongan = $request->potongan;
        $keterangan = $request->keterangan;
        $cek = DB::table('komisi_potongan')->where('id_karyawan', $id_karyawan)->where('bulan', $bulan)->where('tahun', $tahun)->count();
        if ($cek > 0) {
            $update = DB::table('komisi_potongan')->where('id_karyawan', $id_karyawan)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->update([
                    'jumlah' => $potongan,
                    'keterangan' => $keterangan
                ]);
            if ($update) {
                echo 0;
            } else {
                echo 1;
            }
        } else {
            $simpan = DB::table('komisi_potongan')->insert([
                'id_karyawan' => $id_karyawan,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'jumlah' => $potongan,
                'keterangan' => $keterangan
            ]);
            if ($simpan) {
                echo 0;
            } else {
                echo 1;
            }
        }
    }


    public function storekomisiakhir(Request $request)
    {
        $id_karyawan = $request->id_karyawan;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $komisiakhir = $request->komisiakhir;
        $keterangan = $request->keterangan;
        $cek = DB::table('komisi_akhir')->where('id_karyawan', $id_karyawan)->where('bulan', $bulan)->where('tahun', $tahun)->count();
        if ($cek > 0) {
            $update = DB::table('komisi_akhir')->where('id_karyawan', $id_karyawan)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->update([
                    'jumlah' => $komisiakhir,
                    'keterangan' => $keterangan
                ]);
            if ($update) {
                echo 0;
            } else {
                echo 1;
            }
        } else {
            $simpan = DB::table('komisi_akhir')->insert([
                'id_karyawan' => $id_karyawan,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'jumlah' => $komisiakhir,
                'keterangan' => $keterangan
            ]);
            if ($simpan) {
                echo 0;
            } else {
                echo 1;
            }
        }
    }

    public function cekapprovekomisi(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kode_cabang = $request->kode_cabang;
        $level = $request->level;

        if ($level == "mm") {
            $cek = DB::table('komisi_approve')
                ->where('kode_cabang', $kode_cabang)
                ->where('bulan', $bulan)
                ->whereNotNull($level)
                ->where('tahun', $tahun)->count();
        } else if ($level == "gm") {
            $cek = DB::table('komisi_approve')
                ->where('kode_cabang', $kode_cabang)
                ->where('bulan', $bulan)
                ->whereNotNull('mm')
                ->whereNull('gm')
                ->where('tahun', $tahun)->count();
        } else if ($level == "dirut") {
            $cek = DB::table('komisi_approve')
                ->where('kode_cabang', $kode_cabang)
                ->where('bulan', $bulan)
                ->whereNotNull('gm')
                ->whereNull('dirut')
                ->where('tahun', $tahun)->count();
        }


        echo $cek;
    }

    public function approvekomisi(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kode_cabang = $request->kode_cabang;
        $level = $request->level;


        $cek = DB::table('komisi_approve')
            ->where('kode_cabang', $kode_cabang)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)->count();

        if (empty($cek)) {
            $data = [
                'kode_cabang' => $kode_cabang,
                'bulan' => $bulan,
                'tahun' => $tahun,
                $level => 1
            ];

            $simpan = DB::table('komisi_approve')->insert($data);
        } else {
            $data = [
                $level => 1
            ];

            $simpan = DB::table('komisi_approve')
                ->where('kode_cabang', $kode_cabang)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->update($data);
        }

        if ($simpan) {
            echo 0;
        } else {
            echo 1;
        }
    }


    public function getqrcode(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kode_cabang = $request->kode_cabang;
        $lvl = $request->level;

        $cek = DB::table('komisi_approve')
            ->where('kode_cabang', $kode_cabang)
            ->where('bulan', $bulan)
            ->whereNotNull($lvl)
            ->where('tahun', $tahun)->first();

        if ($cek != null) {
            return view('targetkomisi.getqrcode', compact('cek', 'lvl', 'bulan', 'tahun', 'kode_cabang'));
        } else {
            echo "<span class='badge bg-warning'>Waiting</span>";
        }
    }


    public function cancelkomisi(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kode_cabang = $request->kode_cabang;
        $level = $request->level;


        $data = [
            $level => null
        ];

        $update = DB::table('komisi_approve')
            ->where('kode_cabang', $kode_cabang)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->update($data);

        if ($update) {
            echo 0;
        } else {
            echo 1;
        }
    }


    public function cekbatal(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kode_cabang = $request->kode_cabang;
        $level = $request->level;

        if ($level == "mm") {
            $cek = DB::table('komisi_approve')
                ->where('kode_cabang', $kode_cabang)
                ->where('bulan', $bulan)
                ->whereNotNull('mm')
                ->whereNull('gm')
                ->where('tahun', $tahun)->count();
        } else if ($level == "gm") {
            $cek = DB::table('komisi_approve')
                ->where('kode_cabang', $kode_cabang)
                ->where('bulan', $bulan)
                ->whereNotNull('gm')
                ->whereNull('dirut')
                ->where('tahun', $tahun)->count();
        } else if ($level == "dirut") {
            $cek = DB::table('komisi_approve')
                ->where('kode_cabang', $kode_cabang)
                ->where('bulan', $bulan)
                ->whereNotNull('dirut')
                ->where('tahun', $tahun)->count();
        }

        echo $cek;
    }

    public function komisiapprove()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('targetkomisi.komisiapprove', compact('bulan'));
    }

    public function getapprovekomisi(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $approvekomisi =  DB::table('cabang')
            ->select('cabang.kode_cabang', 'nama_cabang', 'mm', 'gm', 'dirut', 'bulan', 'tahun')
            ->leftJoin(
                DB::raw("(
               SELECT kode_cabang,mm,gm,dirut,bulan,tahun
               FROM komisi_approve
               WHERE bulan = '$bulan' AND tahun = '$tahun'
            ) approve"),
                function ($join) {
                    $join->on('cabang.kode_cabang', '=', 'approve.kode_cabang');
                }
            )
            ->get();

        return view('targetkomisi.getapprovekomisi', compact('approvekomisi', 'bulan', 'tahun'));
    }


    public function cetakrekapkomisi(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        lockyear($tahun);
        $dari = $tahun . "-" . $bulan . "-01";
        $hariini = date("Y-m-d");
        $tglkomisi = $tahun . "-" . $bulan . "-01";
        $sampai = date('Y-m-t', strtotime($dari));
        if ($hariini < $sampai) {
            $sampai = $hariini;
        } else {
            $sampai = $sampai;
        }


        $lastmonth = date('Y-m-d', strtotime(date($dari) . '- 1 month'));
        $lastdate = explode("-", $lastmonth);
        $bulanlast = $lastdate[1] + 0;
        $tahunlast = $lastdate[0];
        if ($bulanlast == 1) {
            $blnlast1 = 12;
            $thnlast1 = $tahun - 1;
        } else {
            $blnlast1 = $bulanlast - 1;
            $thnlast1 = $tahun;
        }


        if ($bulan == 12) {
            $bln = 1;
            $thn = $tahun + 1;
        } else {
            $bln = $bulan + 1;
            $thn = $tahun;
        }


        $ceknextBulan = DB::table('setoran_pusat')->where('omset_bulan', $bulan)->where('omset_tahun', $tahun)
            ->whereRaw('MONTH(tgl_diterimapusat) = ' . $bln)
            ->whereRaw('YEAR(tgl_diterimapusat) = ' . $thn)
            ->orderBy('tgl_diterimapusat', 'desc')
            ->first();
        if ($ceknextBulan ==  null) {
            $end = date("Y-m-t", strtotime($dari));
        } else {
            $end = $ceknextBulan->tgl_diterimapusat;
        }
        $query = Salesman::query();
        $query->selectRaw('
            karyawan.id_karyawan,
            nama_karyawan,
            sub_cabang as kode_cabang,
            kategori_salesman,
            status_komisi,
            target_BB_DP,
            BB + DEP as BBDP,
            IF(IF(target_BB_DP = 0,0,(IFNULL(BB,0) + IFNULL(DEP,0)) / target_BB_DP ) > 1,40,IF(target_BB_DP = 0,0,(IFNULL(BB,0) + IFNULL(DEP,0)) / target_BB_DP ) * 40) as poinBBDP,


            target_DS,
            SP8,
            IF(IF(target_DS = 0,0,(IFNULL(SP8,0)) / target_DS ) > 1,10,IF(target_DS = 0,0,(IFNULL(SP8,0)) / target_DS ) * 10) as poinSP8,

            target_SP,
            SP + SP500 as SPSP500,
            IF(IF(target_SP = 0,0,(IFNULL(SP,0) + IFNULL(SP500,0)) / target_SP ) > 1,15,IF(target_SP = 0,0,(IFNULL(SP,0) + IFNULL(SP500,0)) / target_SP ) * 15) as poinSPSP500,

            target_AR,
            AR,
            IF(IF(target_AR = 0,0,(IFNULL(AR,0)) / target_AR ) > 1,12.5,IF(target_AR = 0,0,(IFNULL(AR,0)) / target_AR ) * 12.5) as poinAR,

            target_AB_AS_CG5,
            `AS` + AB as ASAB,
            IF(IF(target_AB_AS_CG5 = 0,0,(IFNULL(`AS`,0) + IFNULL(AB,0)) / target_AB_AS_CG5 ) > 1,10,IF(target_AB_AS_CG5 = 0,0,(IFNULL(`AS`,0) + IFNULL(AB,0)) / target_AB_AS_CG5 ) * 10) as poinASAB,

            target_SC,
            SC,
            IF(IF(target_SC = 0,0,(IFNULL(SC,0)) / target_SC ) > 1,12.5,IF(target_SC = 0,0,(IFNULL(SC,0)) / target_SC ) * 12.5) as poinSC,
            realisasi_cashin,
            sisapiutangsaldo + sisapiutang as sisapiutang,
            potongankomisi,
            komisifix
        ');
        $query->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');
        $query->join(
            DB::raw("(
                    SELECT  id_karyawan,
                    SUM(IF(kategori_komisi='KKQ01',jumlah_target,0)) as target_BB_DP,
                    SUM(IF(kategori_komisi='KKQ02',jumlah_target,0)) as target_DS,
                    SUM(IF(kategori_komisi='KKQ03',jumlah_target,0)) as target_SP,
                    SUM(IF(kategori_komisi='KKQ04',jumlah_target,0)) as target_AR,
                    SUM(IF(kategori_komisi='KKQ05',jumlah_target,0)) as target_AB_AS_CG5,
                    SUM(IF(kategori_komisi='KKQ06',jumlah_target,0)) as target_SC
                    FROM
                    komisi_target_qty_detail k_detail
                    INNER JOIN komisi_target ON k_detail.kode_target = komisi_target.kode_target
                    INNER JOIN master_barang ON k_detail.kode_produk = master_barang.kode_produk
                    WHERE bulan ='$bulan' AND tahun='$tahun'
                    GROUP BY id_karyawan
                    ) komisi"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'komisi.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
                    SELECT historibayar.id_karyawan,SUM(bayar) as cashin_jt
                    FROM historibayar
                    INNER JOIN penjualan ON historibayar.no_fak_penj = penjualan.no_fak_penj
                    WHERE tglbayar BETWEEN '$dari' AND '$sampai' AND status_bayar IS NULL
                    AND datediff(tglbayar, tgltransaksi) > 15
                    GROUP BY historibayar.id_karyawan
                ) hbjt"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'hbjt.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT
                    salesbarunew,IFNULL(SUM(penjualan.total),0) - SUM(IFNULL( totalretur, 0 )) - SUM(IFNULL( totalbayar, 0 )) AS sisapiutang
                FROM
                penjualan
                LEFT JOIN (
                    SELECT
                        pj.no_fak_penj,IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,karyawan.nama_karyawan AS nama_sales,
                        IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                    FROM
                        penjualan pj
                    INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                    LEFT JOIN (
                        SELECT
                            id_move,
                            no_fak_penj,
                            move_faktur.id_karyawan AS salesbaru,
                            karyawan.kode_cabang AS cabangbaru
                        FROM
                            move_faktur
                        INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                        WHERE id_move IN ( SELECT max( id_move ) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj )
                        ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj )
                ) pjmove ON ( penjualan.no_fak_penj = pjmove.no_fak_penj )
                LEFT JOIN (
                    SELECT
                        retur.no_fak_penj AS no_fak_penj,
                        SUM(IFNULL( subtotal_pf, 0 ) - IFNULL( subtotal_gb, 0 )) AS totalretur
                    FROM
                        retur
                    WHERE
                        tglretur BETWEEN '$dari' AND '$sampai'
                    GROUP BY
                        retur.no_fak_penj
                ) r ON ( penjualan.no_fak_penj = r.no_fak_penj )
                LEFT JOIN (
                    SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar
                    FROM historibayar
                    WHERE tglbayar BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                ) hb ON ( penjualan.no_fak_penj = hb.no_fak_penj )

            WHERE penjualan.tgltransaksi BETWEEN '$dari' AND '$sampai' AND jenistransaksi = 'kredit' AND datediff( '$sampai', penjualan.tgltransaksi ) > 15
            GROUP BY
                salesbarunew

            ) penj"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'penj.salesbarunew');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT
                    salesbarunew,IFNULL( SUM(jumlah ), 0 ) - SUM(IFNULL( totalretur, 0 )) - SUM(IFNULL( totalbayar, 0 )) AS sisapiutangsaldo
                FROM
                saldoawal_piutang_faktur spf
                INNER JOIN penjualan ON spf.no_fak_penj = penjualan.no_fak_penj
                LEFT JOIN (
                        SELECT
                            pj.no_fak_penj,IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,karyawan.nama_karyawan AS nama_sales,
                            IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                        FROM
                            penjualan pj
                        INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                        LEFT JOIN (
                            SELECT
                                id_move,
                                no_fak_penj,
                                move_faktur.id_karyawan AS salesbaru,
                                karyawan.kode_cabang AS cabangbaru
                            FROM
                                move_faktur
                            INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                            WHERE id_move IN ( SELECT max( id_move ) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj )
                            ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj )
                    ) pjmove ON ( penjualan.no_fak_penj = pjmove.no_fak_penj )
                    LEFT JOIN (
                        SELECT
                            retur.no_fak_penj AS no_fak_penj,
                            SUM(IFNULL( subtotal_pf, 0 ) - IFNULL( subtotal_gb, 0 )) AS totalretur
                        FROM
                            retur
                        WHERE
                            tglretur BETWEEN '$dari' AND '$sampai'
                        GROUP BY
                            retur.no_fak_penj
                    ) r ON ( penjualan.no_fak_penj = r.no_fak_penj )
                    LEFT JOIN (
                        SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar
                        FROM historibayar
                        WHERE tglbayar BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                    ) hb ON ( penjualan.no_fak_penj = hb.no_fak_penj )
                WHERE
                    datediff( '$sampai', penjualan.tgltransaksi ) > 15 AND bulan = '$bulan' AND tahun = '$tahun'
                GROUP BY
                    salesbarunew
        ) spf"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'spf.salesbarunew');
            }
        );


        $query->leftJoin(
            DB::raw("(
            SELECT karyawan.id_karyawan,
            (IFNULL(jml_belumsetorbulanlalu,0)+IFNULL(totalsetoran,0)) + IFNULL(jml_gmlast,0) - IFNULL(jml_gmnow,0) - IFNULL(jml_belumsetorbulanini,0) as realisasi_cashin
            FROM karyawan
            LEFT JOIN (
                SELECT id_karyawan,jumlah as jml_belumsetorbulanlalu FROM belumsetor_detail
                INNER JOIN belumsetor ON belumsetor_detail.kode_saldobs = belumsetor.kode_saldobs
                WHERE bulan='$bulanlast' AND tahun='$tahunlast'
            ) bs ON (karyawan.id_karyawan = bs.id_karyawan)

            LEFT JOIN (
                SELECT id_karyawan, SUM(lhp_tunai+lhp_tagihan) as totalsetoran FROM setoran_penjualan WHERE tgl_lhp BETWEEN '$dari' AND '$sampai' GROUP BY id_karyawan
            ) sp ON (karyawan.id_karyawan = sp.id_karyawan)

            LEFT JOIN (
                SELECT
                IFNULL(hb.id_karyawan,giro.id_karyawan) as id_karyawan,
                SUM( jumlah ) AS jml_gmlast
                FROM
                giro
                INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                LEFT JOIN ( SELECT id_giro,id_karyawan FROM historibayar GROUP BY id_giro,id_karyawan ) AS hb ON giro.id_giro = hb.id_giro
                WHERE
                MONTH ( tgl_giro ) = '$bulanlast'
                AND YEAR ( tgl_giro ) = '$tahunlast'
                AND omset_tahun = '$tahun'
                AND omset_bulan = '$bulan'
                OR  MONTH ( tgl_giro ) = '$blnlast1'
                AND YEAR ( tgl_giro ) = '$thnlast1'
                AND omset_tahun = '$tahun'
                AND omset_bulan = '$bulan'
                GROUP BY
                IFNULL( hb.id_karyawan, giro.id_karyawan )
            ) gmlast ON (karyawan.id_karyawan = gmlast.id_karyawan)
            LEFT JOIN (
            SELECT
                IFNULL(hb.id_karyawan,giro.id_karyawan) as id_karyawan,
                SUM( jumlah ) AS jml_gmnow
            FROM
                giro
                INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                LEFT JOIN ( SELECT id_giro,id_karyawan, tglbayar FROM historibayar GROUP BY id_giro, tglbayar,id_karyawan ) AS hb ON giro.id_giro = hb.id_giro
            WHERE
                tgl_giro >= '$dari'
                AND tgl_giro <= '$sampai' AND tglbayar IS NULL AND omset_bulan = '0' AND omset_tahun = ''
                OR  tgl_giro >= '$dari'
                AND tgl_giro <= '$sampai' AND tglbayar >= '$end'
                AND omset_bulan > '$bulan'
                AND omset_tahun >= '$tahun'
            GROUP BY
                giro.id_karyawan,
                hb.id_karyawan
            ) gmnow ON (karyawan.id_karyawan = gmnow.id_karyawan)

            LEFT JOIN (
                SELECT belumsetor_detail.id_karyawan, SUM(jumlah) as jml_belumsetorbulanini
                FROM belumsetor_detail
                INNER JOIN belumsetor ON belumsetor_detail.kode_saldobs = belumsetor.kode_saldobs
                WHERE bulan ='$bulan' AND tahun ='$tahun' GROUP BY id_karyawan
            ) bsnow ON (karyawan.id_karyawan = bsnow.id_karyawan)
            ) hb"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'hb.id_karyawan');
            }
        );


        $query->leftJoin(
            DB::raw("(
            SELECT salesbarunew,
            SUM(IF( kode_produk = 'AB' AND promo != '1' OR kode_produk = 'AB' AND promo IS NULL, jumlah/isipcsdus, 0 )) AS AB,
            SUM(IF( kode_produk = 'AR' AND promo != '1' OR kode_produk = 'AR' AND promo IS NULL, jumlah/isipcsdus, 0 )) AS AR,
            SUM(IF( kode_produk = 'AS' AND promo != '1' OR kode_produk = 'AS' AND promo IS NULL, jumlah/isipcsdus, 0 )) AS `AS`,
            SUM(IF( kode_produk = 'BB' AND promo != '1' OR kode_produk = 'BB' AND promo IS NULL, jumlah/isipcsdus, 0 )) AS BB,
            SUM(IF( kode_produk = 'CG' AND promo != '1' OR kode_produk = 'CG' AND promo IS NULL, jumlah/isipcsdus, 0 )) AS CG,
            SUM(IF( kode_produk = 'CGG' AND promo != '1' OR kode_produk = 'CGG' AND promo IS NULL, jumlah/isipcsdus, 0 )) AS CGG,
            SUM(IF( kode_produk = 'DEP' AND promo != '1' OR kode_produk = 'DEP' AND promo IS NULL, jumlah/isipcsdus, 0 )) AS DEP,
            SUM(IF( kode_produk = 'DK' AND promo != '1' OR kode_produk = 'DK' AND promo IS NULL, jumlah/isipcsdus, 0 )) AS DK,
            SUM(IF( kode_produk = 'DS' AND promo != '1' OR kode_produk = 'DS' AND promo IS NULL, jumlah/isipcsdus, 0 )) AS DS,
            SUM(IF( kode_produk = 'SP' AND promo != '1' OR kode_produk = 'SP' AND promo IS NULL, jumlah/isipcsdus, 0 )) AS SP,
            SUM(IF( kode_produk = 'BBP' AND promo != '1' OR kode_produk = 'BBP' AND promo IS NULL, jumlah/isipcsdus, 0 )) AS BBP,
            SUM(IF( kode_produk = 'SPP' AND promo != '1' OR kode_produk = 'SPP' AND promo IS NULL, jumlah/isipcsdus, 0 )) AS SPP,
            SUM(IF( kode_produk = 'CG5' AND promo != '1' OR kode_produk = 'CG5' AND promo IS NULL, jumlah/isipcsdus, 0 )) AS CG5,
            SUM(IF( kode_produk = 'SP8' AND promo != '1' OR kode_produk = 'SP8' AND promo IS NULL, jumlah/isipcsdus, 0 )) AS SP8,
            SUM(IF( kode_produk = 'SC' AND promo != '1' OR kode_produk = 'SC' AND promo IS NULL, jumlah/isipcsdus, 0 )) AS SC,
            SUM(IF( kode_produk = 'SP500' AND promo != '1' OR kode_produk = 'SP500' AND promo IS NULL, jumlah/isipcsdus, 0 )) AS SP500
            FROM detailpenjualan
            INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
            INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang

            LEFT JOIN (
                SELECT pj.no_fak_penj,
                IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
                IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
                FROM penjualan pj
                INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                LEFT JOIN (
                    SELECT
                    id_move,no_fak_penj,
                    move_faktur.id_karyawan as salesbaru,
                    karyawan.kode_cabang  as cabangbaru
                    FROM move_faktur
                    INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                    WHERE id_move IN (SELECT max(id_move) FROM move_faktur WHERE tgl_move <= '$dari' GROUP BY no_fak_penj)
                ) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
            ) pjmove ON (penjualan.no_fak_penj = pjmove.no_fak_penj)
            WHERE  status_lunas ='1' AND tgl_pelunasan BETWEEN '$dari' AND '$sampai'
            GROUP BY salesbarunew
        ) realisasi"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'realisasi.salesbarunew');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT id_karyawan,jumlah as potongankomisi
                FROM komisi_potongan
                WHERE bulan = '$bulan' AND tahun='$tahun'
            ) potongankomisi"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'potongankomisi.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT id_karyawan,jumlah as komisifix
                FROM komisi_akhir
                WHERE bulan = '$bulan' AND tahun='$tahun'
            ) komisiakhir"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'komisiakhir.id_karyawan');
            }
        );

        $query->where('nama_karyawan', '!=', '');
        $query->orderByRaw('sub_cabang,karyawan.id_karyawan');
        $komisi = $query->get();



        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $nmbulan  = $namabulan[$bulan];
        $cabang = Cabang::orderBy('kode_cabang')->get();

        $potongankomisikp = DB::table('komisi_potongan')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->whereRaw('LEFT(id_karyawan,2)="KP"')
            ->get();

        $komisiakhirkp = DB::table('komisi_akhir')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->whereRaw('LEFT(id_karyawan,2)="KP"')
            ->get();

        $supervisorcabang = ['BDG', 'TSM'];

        $potongankomisispv = DB::table('komisi_potongan')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->whereRaw('LEFT(id_karyawan,2)="SP"')
            ->get();

        $komisiakhirspv = DB::table('komisi_akhir')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->whereRaw('LEFT(id_karyawan,2)="SP"')
            ->get();



        return view('targetkomisi.laporan.cetak_rekap', compact('komisi', 'nmbulan', 'tahun', 'bulan', 'cabang', 'potongankomisikp', 'komisiakhirkp', 'supervisorcabang', 'potongankomisispv', 'komisiakhirspv'));
    }



    public function cetakkomisijuli2023($cabang, $bulan, $tahun, $aturankomisi, $dari, $hariini, $sampai)
    {
        //$dari = '2023-06-31';
        $lastmonth = date('Y-m-d', strtotime(date($dari) . '- 1 month'));
        $enddate = date('Y-m-t', strtotime($dari));
        if (date("d", strtotime($enddate)) == 31) {
            $enddate = date("Y-m", strtotime($enddate)) . "-30";
        }
        //dd($lastdateofmonth);
        $last3month = date('Y-m-d', strtotime('-2 month', strtotime($enddate)));
        $date = explode("-", $last3month);
        $startdate = $date[0] . "-" . $date[1] . "-01";
        $lastdate = explode("-", $lastmonth);
        $bulanlast = $lastdate[1] + 0;
        $tahunlast = $lastdate[0];
        if ($bulanlast == 1) {
            $blnlast1 = 12;
            $thnlast1 = $tahun - 1;
        } else {
            $blnlast1 = $bulanlast - 1;
            $thnlast1 = $tahun;
        }


        if ($bulan == 12) {
            $bln = 1;
            $thn = $tahun + 1;
        } else {
            $bln = $bulan + 1;
            $thn = $tahun;
        }


        $ceknextBulan = DB::table('setoran_pusat')->where('omset_bulan', $bulan)->where('omset_tahun', $tahun)
            ->whereRaw('MONTH(tgl_diterimapusat) = ' . $bln)
            ->whereRaw('YEAR(tgl_diterimapusat) = ' . $thn)
            ->where('kode_cabang', $cabang)
            ->orderBy('tgl_diterimapusat', 'desc')
            ->first();
        if ($ceknextBulan ==  null) {
            $end = date("Y-m-t", strtotime($dari));
        } else {
            $end = $ceknextBulan->tgl_diterimapusat;
        }


        $produk = Barang::orderBy('kode_produk')->get();
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $cbg = DB::table('cabang')->where('kode_cabang', $cabang)->first();

        $query = Salesman::query();
        $query->selectRaw('
            karyawan.id_karyawan,nama_karyawan,kategori_salesman,status_komisi,
            target_BB_DP,
            BB,
            retur_BB,
            DEP,
            retur_DEP,
            target_DS,
            DS,
            retur_DS,
            SP8,
            retur_SP8,
            target_SP,
            SP,
            retur_SP,
            SP500,
            retur_SP500,
            target_SC,
            SC,
            retur_SC,
            target_AR,
            AR,
            retur_AR,
            target_AB_AS_CG5,
            AB,
            retur_AB,
            `AS`,
            retur_AS,
            CG5,
            retur_CG5,
            realisasi_cashin,
            IFNULL(sisapiutangsaldo,0) + IFNULL(sisapiutang,0) as sisapiutang,
            cashin_jt,
            potongankomisi,
            komisifix,
            ket_potongan,
            ket_komisifix,
            jmlpelanggan,
            jmltrans,
            jmltigasku,
            jmlkunjungan,
            jmlsesuaijadwal,
            jmltranspenjualan
        ');

        $query->leftJoin(
            DB::raw("(
                SELECT id_sales, COUNT(DISTINCT(penjualan.kode_pelanggan)) as jmlpelanggan
                FROM penjualan
                INNER JOIN pelanggan ON penjualan.kode_pelanggan = pelanggan.kode_pelanggan
                WHERE tgltransaksi BETWEEN '$startdate' AND '$enddate' AND nama_pelanggan != 'BATAL'
                GROUP BY id_sales
            ) pelangganaktif"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'pelangganaktif.id_sales');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT karyawan.id_karyawan,
                IFNULL(jmlkunjungan,0) as jmlkunjungan,
                IFNULL(jmlsesuaijadwal,0) as jmlsesuaijadwal
                FROM karyawan
                LEFT JOIN (
                    SELECT
                    penjualan.id_karyawan,
                    COUNT(no_fak_penj) as jmlkunjungan,
                    COUNT(
                    CASE WHEN
                    DAYNAME(tgltransaksi)='Monday' AND pelanggan.hari like '%Senin%' OR
                    DAYNAME(tgltransaksi)='Tuesday' AND pelanggan.hari like '%Selasa%' OR
                    DAYNAME(tgltransaksi)='Wednesday' AND pelanggan.hari like '%Rabu%' OR
                    DAYNAME(tgltransaksi)='Thursday' AND pelanggan.hari like '%Kamis%' OR
                    DAYNAME(tgltransaksi)='Friday' AND pelanggan.hari like '%Jumat%' OR
                    DAYNAME(tgltransaksi)='Saturday' AND pelanggan.hari like '%Sabtu%' OR
                    DAYNAME(tgltransaksi)='Sunday' AND pelanggan.hari like '%Minggu%'  THEN  penjualan.no_fak_penj END ) as jmlsesuaijadwal
                    FROM
                    `penjualan`
                    INNER JOIN `pelanggan` ON `penjualan`.`kode_pelanggan` = `pelanggan`.`kode_pelanggan`
                    INNER JOIN `karyawan` ON `penjualan`.`id_karyawan` = `karyawan`.`id_karyawan`
                    WHERE `tgltransaksi` BETWEEN '$dari' AND '$sampai' AND `nama_pelanggan` != 'BATAL'
                    GROUP BY
                            penjualan.id_karyawan
                ) kunjungan ON (karyawan.id_karyawan = kunjungan.id_karyawan)
            ) kunjungan"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'kunjungan.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
            SELECT karyawan.id_karyawan,COUNT(jml_sku) as jmltigasku
            FROM karyawan
            LEFT JOIN (
            SELECT penjualan.id_karyawan,COUNT(DISTINCT(kode_sku)) as jml_sku
            FROM detailpenjualan
            INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
            INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
            INNER JOIN master_barang ON barang.kode_produk = master_barang.kode_produk
            WHERE tgltransaksi BETWEEN '$dari' AND '$sampai' AND promo IS NULL
            GROUP BY penjualan.kode_pelanggan,penjualan.id_karyawan
            ORDER BY penjualan.kode_pelanggan
            ) sku ON (karyawan.id_karyawan = sku.id_karyawan)
            WHERE jml_sku >= 3 GROUP BY karyawan.id_karyawan
            ) sku"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'sku.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT id_karyawan,
                COUNT(DISTINCT(kode_pelanggan)) as jmltrans,
                COUNT(no_fak_penj) as jmltranspenjualan
                FROM penjualan
                WHERE tgltransaksi BETWEEN '$dari' AND '$sampai'
                GROUP BY id_karyawan
            ) pelanggantrans"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'pelanggantrans.id_karyawan');
            }
        );
        $query->join(
            DB::raw("(
                SELECT  id_karyawan,
                SUM(IF(kategori_komisi='KKQ01',jumlah_target,0)) as target_BB_DP,
                SUM(IF(kategori_komisi='KKQ02',jumlah_target,0)) as target_DS,
                SUM(IF(kategori_komisi='KKQ03',jumlah_target,0)) as target_SP,
                SUM(IF(kategori_komisi='KKQ04',jumlah_target,0)) as target_AR,
                SUM(IF(kategori_komisi='KKQ05',jumlah_target,0)) as target_AB_AS_CG5,
                SUM(IF(kategori_komisi='KKQ06',jumlah_target,0)) as target_SC
                FROM
                komisi_target_qty_detail k_detail
                INNER JOIN komisi_target ON k_detail.kode_target = komisi_target.kode_target
                INNER JOIN master_barang ON k_detail.kode_produk = master_barang.kode_produk
                WHERE bulan ='$bulan' AND tahun='$tahun'
                GROUP BY id_karyawan
                ) komisi"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'komisi.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT historibayar.id_karyawan,SUM(bayar) as cashin_jt
                FROM historibayar
                INNER JOIN penjualan ON historibayar.no_fak_penj = penjualan.no_fak_penj
                WHERE tglbayar BETWEEN '$dari' AND '$sampai' AND status_bayar IS NULL
                AND datediff(tglbayar, tgltransaksi) > 15
                GROUP BY historibayar.id_karyawan
            ) hbjt"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'hbjt.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT
                    salesbarunew,IFNULL(SUM(penjualan.total),0) - SUM(IFNULL( totalretur, 0 )) - SUM(IFNULL( totalbayar, 0 )) AS sisapiutang
                FROM
                penjualan
                INNER JOIN pelanggan ON penjualan.kode_pelanggan = pelanggan.kode_pelanggan
                LEFT JOIN (
                    SELECT
                        pj.no_fak_penj,IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,karyawan.nama_karyawan AS nama_sales,
                        IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                    FROM
                        penjualan pj
                    INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                    LEFT JOIN (
                        SELECT
                            id_move,
                            no_fak_penj,
                            move_faktur.id_karyawan AS salesbaru,
                            karyawan.kode_cabang AS cabangbaru
                        FROM
                            move_faktur
                        INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                        WHERE id_move IN ( SELECT max( id_move ) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj )
                        ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj )
                ) pjmove ON ( penjualan.no_fak_penj = pjmove.no_fak_penj )
                LEFT JOIN (
                    SELECT
                        retur.no_fak_penj AS no_fak_penj,
                        SUM(IFNULL( subtotal_pf, 0 ) - IFNULL( subtotal_gb, 0 )) AS totalretur
                    FROM
                        retur
                    WHERE
                        tglretur BETWEEN '$dari' AND '$sampai'
                    GROUP BY
                        retur.no_fak_penj
                ) r ON ( penjualan.no_fak_penj = r.no_fak_penj )
                LEFT JOIN (
                    SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar
                    FROM historibayar
                    WHERE tglbayar BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                ) hb ON ( penjualan.no_fak_penj = hb.no_fak_penj )

            WHERE penjualan.tgltransaksi BETWEEN '$dari' AND '$sampai' AND jenistransaksi = 'kredit' AND datediff( '$sampai', penjualan.tgltransaksi ) > 30
            GROUP BY
                salesbarunew

            ) penj"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'penj.salesbarunew');
            }
        );


        $query->leftJoin(
            DB::raw("(
                SELECT
                    salesbarunew,IFNULL( SUM(jumlah ), 0 ) - SUM(IFNULL( totalretur, 0 )) - SUM(IFNULL( totalbayar, 0 )) AS sisapiutangsaldo
                FROM
                saldoawal_piutang_faktur spf
                INNER JOIN penjualan ON spf.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN pelanggan ON penjualan.kode_pelanggan = pelanggan.kode_pelanggan
                LEFT JOIN (
                        SELECT
                            pj.no_fak_penj,IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,karyawan.nama_karyawan AS nama_sales,
                            IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                        FROM
                            penjualan pj
                        INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                        LEFT JOIN (
                            SELECT
                                id_move,
                                no_fak_penj,
                                move_faktur.id_karyawan AS salesbaru,
                                karyawan.kode_cabang AS cabangbaru
                            FROM
                                move_faktur
                            INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                            WHERE id_move IN ( SELECT max( id_move ) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj )
                            ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj )
                    ) pjmove ON ( penjualan.no_fak_penj = pjmove.no_fak_penj )
                    LEFT JOIN (
                        SELECT
                            retur.no_fak_penj AS no_fak_penj,
                            SUM(IFNULL( subtotal_pf, 0 ) - IFNULL( subtotal_gb, 0 )) AS totalretur
                        FROM
                            retur
                        WHERE
                            tglretur BETWEEN '$dari' AND '$sampai'
                        GROUP BY
                            retur.no_fak_penj
                    ) r ON ( penjualan.no_fak_penj = r.no_fak_penj )
                    LEFT JOIN (
                        SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar
                        FROM historibayar
                        WHERE tglbayar BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                    ) hb ON ( penjualan.no_fak_penj = hb.no_fak_penj )
                WHERE
                    datediff( '$sampai', penjualan.tgltransaksi ) > 30 AND bulan = '$bulan' AND tahun = '$tahun'
                GROUP BY
                    salesbarunew
            ) spf"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'spf.salesbarunew');
            }
        );

        if ($aturankomisi == 2) {
            $query->leftJoin(
                DB::raw("(
                SELECT karyawan.id_karyawan,
                (IFNULL(jml_belumsetorbulanlalu,0)+IFNULL(totalsetoran,0)) + IFNULL(jml_gmlast,0) - IFNULL(jml_gmnow,0) - IFNULL(jml_belumsetorbulanini,0) as realisasi_cashin
                FROM karyawan
                LEFT JOIN (
                    SELECT id_karyawan,jumlah as jml_belumsetorbulanlalu FROM belumsetor_detail
                    INNER JOIN belumsetor ON belumsetor_detail.kode_saldobs = belumsetor.kode_saldobs
                    WHERE bulan='$bulanlast' AND tahun='$tahunlast'
                ) bs ON (karyawan.id_karyawan = bs.id_karyawan)

                LEFT JOIN (
                    SELECT id_karyawan, SUM(lhp_tunai+lhp_tagihan) as totalsetoran FROM setoran_penjualan WHERE tgl_lhp BETWEEN '$dari' AND '$sampai' GROUP BY id_karyawan
                ) sp ON (karyawan.id_karyawan = sp.id_karyawan)

                LEFT JOIN (
                    SELECT
                    IFNULL(hb.id_karyawan,giro.id_karyawan) as id_karyawan,
                    SUM( jumlah ) AS jml_gmlast
                    FROM
                    giro
                    INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                    LEFT JOIN ( SELECT id_giro,id_karyawan FROM historibayar GROUP BY id_giro,id_karyawan ) AS hb ON giro.id_giro = hb.id_giro
                    WHERE
                    MONTH ( tgl_giro ) = '$bulanlast'
                    AND YEAR ( tgl_giro ) = '$tahunlast'
                    AND omset_tahun = '$tahun'
                    AND omset_bulan = '$bulan'
                    OR  MONTH ( tgl_giro ) = '$blnlast1'
                    AND YEAR ( tgl_giro ) = '$thnlast1'
                    AND omset_tahun = '$tahun'
                    AND omset_bulan = '$bulan'
                    GROUP BY
                    IFNULL( hb.id_karyawan, giro.id_karyawan )
                ) gmlast ON (karyawan.id_karyawan = gmlast.id_karyawan)
                LEFT JOIN (
                SELECT
                    IFNULL(hb.id_karyawan,giro.id_karyawan) as id_karyawan,
                    SUM( jumlah ) AS jml_gmnow
                FROM
                    giro
                    INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                    LEFT JOIN ( SELECT id_giro,id_karyawan, tglbayar FROM historibayar GROUP BY id_giro, tglbayar,id_karyawan ) AS hb ON giro.id_giro = hb.id_giro
                WHERE
                    tgl_giro >= '$dari'
                    AND tgl_giro <= '$sampai' AND tglbayar IS NULL AND omset_bulan = '0' AND omset_tahun = ''
                    OR  tgl_giro >= '$dari'
                    AND tgl_giro <= '$sampai' AND tglbayar >= '$end'
                    AND omset_bulan > '$bulan'
                    AND omset_tahun >= '$tahun'
                GROUP BY
                IFNULL( hb.id_karyawan, giro.id_karyawan )
                ) gmnow ON (karyawan.id_karyawan = gmnow.id_karyawan)

                LEFT JOIN (
                    SELECT belumsetor_detail.id_karyawan, SUM(jumlah) as jml_belumsetorbulanini
                    FROM belumsetor_detail
                    INNER JOIN belumsetor ON belumsetor_detail.kode_saldobs = belumsetor.kode_saldobs
                    WHERE bulan ='$bulan' AND tahun ='$tahun' GROUP BY id_karyawan
                ) bsnow ON (karyawan.id_karyawan = bsnow.id_karyawan)
                ) hb"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'hb.id_karyawan');
                }
            );
        } else if ($aturankomisi == 3) {
            $query->leftJoin(
                DB::raw("(
                    SELECT historibayar.id_karyawan,SUM(bayar) as realisasi_cashin
                    FROM historibayar WHERE tglbayar BETWEEN '$dari' AND '$sampai' AND status_bayar IS NULL
                    GROUP BY historibayar.id_karyawan
                ) hb"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'hb.id_karyawan');
                }
            );
        } else if ($aturankomisi == 4) {
            $query->leftJoin(
                DB::raw("(
                    SELECT historibayar.id_karyawan,SUM(bayar) as realisasi_cashin
                    FROM historibayar
                    INNER JOIN penjualan ON historibayar.no_fak_penj = penjualan.no_fak_penj
                    WHERE tglbayar BETWEEN '$dari' AND '$sampai' AND status_bayar IS NULL
                    AND datediff(tglbayar, tgltransaksi) <= 14
                    GROUP BY historibayar.id_karyawan
                ) hb"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'hb.id_karyawan');
                }
            );
        }

        $query->leftJoin(
            DB::raw("(
            SELECT
                salesbarunew,
                SUM(IF(kode_produk = 'AB' AND promo !='1' OR kode_produk = 'AB' AND promo IS NULL,jumlah,0)) as AB,
                SUM(IF(kode_produk = 'AR' AND promo !='1' OR kode_produk = 'AR' AND promo IS NULL,jumlah,0)) as AR,
                SUM(IF(kode_produk = 'AS' AND promo !='1' OR kode_produk = 'AS' AND promo IS NULL ,jumlah,0)) as `AS`,
                SUM(IF(kode_produk = 'BB' AND promo !='1' OR kode_produk = 'BB' AND promo IS NULL,jumlah,0)) as BB,
                SUM(IF(kode_produk = 'CG' AND promo !='1' OR kode_produk = 'CG' AND promo IS NULL,jumlah,0)) as CG,
                SUM(IF(kode_produk = 'CGG' AND promo !='1' OR kode_produk = 'CGG' AND promo IS NULL,jumlah,0)) as CGG,
                SUM(IF(kode_produk = 'DEP' AND promo !='1' OR kode_produk = 'DEP' AND promo IS NULL,jumlah,0)) as DEP,
                SUM(IF(kode_produk = 'DK' AND promo !='1' OR kode_produk = 'DK' AND promo IS NULL,jumlah,0)) as DK,
                SUM(IF(kode_produk = 'DS' AND promo !='1' OR kode_produk = 'DS' AND promo IS NULL,jumlah,0)) as DS,
                SUM(IF(kode_produk = 'SP' AND promo !='1' OR kode_produk = 'SP' AND promo IS NULL,jumlah,0)) as SP,
                SUM(IF(kode_produk = 'BBP' AND promo !='1' OR kode_produk = 'BBP' AND promo IS NULL,jumlah,0)) as BBP,
                SUM(IF(kode_produk = 'SPP' AND promo !='1' OR kode_produk = 'SPP' AND promo IS NULL,jumlah,0)) as SPP,
                SUM(IF(kode_produk = 'CG5' AND promo !='1' OR kode_produk = 'CG5' AND promo IS NULL,jumlah,0)) as CG5,
                SUM(IF(kode_produk = 'SP8' AND promo !='1' OR kode_produk = 'SP8' AND promo IS NULL,jumlah,0)) as SP8,
                SUM(IF(kode_produk = 'SC' AND promo !='1' OR kode_produk = 'SC' AND promo IS NULL,jumlah,0)) as SC,
                SUM(IF(kode_produk = 'SP500' AND promo !='1' OR kode_produk = 'SP500' AND promo IS NULL,jumlah,0)) as SP500
                FROM detailpenjualan
                INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang

                LEFT JOIN (
                    SELECT pj.no_fak_penj,
                        IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
                        IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
                    FROM penjualan pj
                    INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                LEFT JOIN (
                    SELECT
                        id_move,no_fak_penj,
                        move_faktur.id_karyawan as salesbaru,
                        karyawan.kode_cabang  as cabangbaru
                    FROM move_faktur
                    INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                    WHERE id_move IN (SELECT max(id_move) FROM move_faktur WHERE tgl_move <= '$dari' GROUP BY no_fak_penj)
                ) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
            ) pjmove ON (penjualan.no_fak_penj = pjmove.no_fak_penj)
            WHERE  status_lunas ='1' AND tgl_pelunasan BETWEEN '$dari' AND '$sampai'
            GROUP BY salesbarunew
            ) realisasi"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'realisasi.salesbarunew');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT penjualan.id_karyawan,
                SUM(IF(kode_produk = 'AB',jumlah,0)) as retur_AB,
                SUM(IF(kode_produk = 'AR',jumlah,0)) as retur_AR,
                SUM(IF(kode_produk = 'AS',jumlah,0)) as `retur_AS`,
                SUM(IF(kode_produk = 'BB',jumlah,0)) as retur_BB,
                SUM(IF(kode_produk = 'CG' ,jumlah,0)) as retur_CG,
                SUM(IF(kode_produk = 'CGG',jumlah,0)) as retur_CGG,
                SUM(IF(kode_produk = 'DEP',jumlah,0)) as retur_DEP,
                SUM(IF(kode_produk = 'DK',jumlah,0)) as retur_DK,
                SUM(IF(kode_produk = 'DS',jumlah,0)) as retur_DS,
                SUM(IF(kode_produk = 'SP',jumlah,0)) as retur_SP,
                SUM(IF(kode_produk = 'BBP',jumlah,0)) as retur_BBP,
                SUM(IF(kode_produk = 'SPP',jumlah,0)) as retur_SPP,
                SUM(IF(kode_produk = 'CG5',jumlah,0)) as retur_CG5,
                SUM(IF(kode_produk = 'SP8',jumlah,0)) as retur_SP8,
                SUM(IF(kode_produk = 'SC',jumlah,0)) as retur_SC,
                SUM(IF(kode_produk = 'SP500',jumlah,0)) as retur_SP500
                FROM detailretur
                INNER JOIN retur ON detailretur.no_retur_penj = retur.no_retur_penj
                INNER JOIN penjualan ON retur.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN barang ON detailretur.kode_barang = barang.kode_barang

                WHERE  status_lunas ='1' AND tgl_pelunasan BETWEEN '$dari' AND '$sampai'
                GROUP BY penjualan.id_karyawan
            ) returpf"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'returpf.id_karyawan');
            }
        );



        $query->leftJoin(
            DB::raw("(
                SELECT id_karyawan,jumlah as potongankomisi,keterangan as ket_potongan
                FROM komisi_potongan
                WHERE bulan = '$bulan' AND tahun='$tahun'
            ) potongankomisi"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'potongankomisi.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT id_karyawan,jumlah as komisifix, keterangan as ket_komisifix
                FROM komisi_akhir
                WHERE bulan = '$bulan' AND tahun='$tahun'
            ) komisiakhir"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'komisiakhir.id_karyawan');
            }
        );

        if (Auth::user()->id == 27 || $cabang == "BDG" && Auth::user()->kode_cabang == "PCF") {
            $query->whereIn('kode_cabang', ['BDG', 'PWK']);
        } else {
            $query->where('kode_cabang', $cabang);
        }
        $query->where('nama_karyawan', '!=', '');
        $komisi = $query->get();

        $nmbulan  = $namabulan[$bulan];

        $kodekp = 'KP' . $cabang;
        $kodespv = 'SPV' . $cabang;

        $potongankp = DB::table('komisi_potongan')->where('id_karyawan', $kodekp)
            ->where('bulan', $bulan)->where('tahun', $tahun)
            ->first();

        $komisiakhir = DB::table('komisi_akhir')->where('id_karyawan', $kodekp)
            ->where('bulan', $bulan)->where('tahun', $tahun)
            ->first();


        $supervisorcabang = ['BDG', 'TSM'];
        if (in_array($cabang, $supervisorcabang)) {
            $potonganspv = DB::table('komisi_potongan')->where('id_karyawan', $kodespv)
                ->where('bulan', $bulan)->where('tahun', $tahun)
                ->first();

            $komisiakhirspv = DB::table('komisi_akhir')->where('id_karyawan', $kodespv)
                ->where('bulan', $bulan)->where('tahun', $tahun)
                ->first();
        } else {
            $potonganspv = null;
            $komisiakhirspv = null;
        }

        if (isset($_POST['export'])) {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Komisi $time.xls");
        }


        return view('targetkomisi.laporan.cetak_komisi_juli2023', compact('komisi', 'cbg', 'nmbulan', 'tahun', 'produk', 'bulan', 'cabang', 'potongankp', 'komisiakhir', 'supervisorcabang', 'potonganspv', 'komisiakhirspv', 'startdate', 'enddate'));
    }


    public function cetakkomisioktober2023($cabang, $bulan, $tahun, $aturankomisi, $dari, $hariini, $sampai)
    {
        //$dari = '2023-06-31';
        $lastmonth = date('Y-m-d', strtotime(date($dari) . '- 1 month'));
        $enddate = date('Y-m-t', strtotime($dari));
        if (date("d", strtotime($enddate)) == 31) {
            $enddate = date("Y-m", strtotime($enddate)) . "-30";
        }
        //dd($lastdateofmonth);
        $last3month = date('Y-m-d', strtotime('-2 month', strtotime($enddate)));
        $date = explode("-", $last3month);
        $startdate = $date[0] . "-" . $date[1] . "-01";
        $lastdate = explode("-", $lastmonth);
        $bulanlast = $lastdate[1] + 0;
        $tahunlast = $lastdate[0];
        if ($bulanlast == 1) {
            $blnlast1 = 12;
            $thnlast1 = $tahun - 1;
        } else {
            $blnlast1 = $bulanlast - 1;
            $thnlast1 = $tahun;
        }


        if ($bulan == 12) {
            $bln = 1;
            $thn = $tahun + 1;
        } else {
            $bln = $bulan + 1;
            $thn = $tahun;
        }


        $ceknextBulan = DB::table('setoran_pusat')->where('omset_bulan', $bulan)->where('omset_tahun', $tahun)
            ->whereRaw('MONTH(tgl_diterimapusat) = ' . $bln)
            ->whereRaw('YEAR(tgl_diterimapusat) = ' . $thn)
            ->where('kode_cabang', $cabang)
            ->orderBy('tgl_diterimapusat', 'desc')
            ->first();
        if ($ceknextBulan ==  null) {
            $end = date("Y-m-t", strtotime($dari));
        } else {
            $end = $ceknextBulan->tgl_diterimapusat;
        }


        $produk = Barang::orderBy('kode_produk')->get();
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $cbg = DB::table('cabang')->where('kode_cabang', $cabang)->first();

        $query = Salesman::query();
        $query->selectRaw('
            karyawan.id_karyawan,nama_karyawan,kategori_salesman,status_komisi,
            target_BB_DP,
            BB,
            retur_BB,
            DEP,
            retur_DEP,
            target_DS,
            DS,
            retur_DS,
            SP8,
            retur_SP8,
            target_SP,
            SP,
            retur_SP,
            SP500,
            retur_SP500,
            target_SC,
            SC,
            retur_SC,
            target_AR,
            AR,
            retur_AR,
            target_AB_AS_CG5,
            AB,
            retur_AB,
            `AS`,
            retur_AS,
            CG5,
            retur_CG5,
            realisasi_cashin,
            IFNULL(sisapiutangsaldo,0) + IFNULL(sisapiutang,0) as sisapiutang,
            cashin_jt,
            potongankomisi,
            komisifix,
            ket_potongan,
            ket_komisifix,
            jmlpelanggan,
            jmltrans,
            jmltigasku,
            jmlkunjungan,
            jmlsesuaijadwal,
            jmltranspenjualan
        ');

        $query->leftJoin(
            DB::raw("(
                SELECT id_sales, COUNT(DISTINCT(penjualan.kode_pelanggan)) as jmlpelanggan
                FROM penjualan
                INNER JOIN pelanggan ON penjualan.kode_pelanggan = pelanggan.kode_pelanggan
                WHERE tgltransaksi BETWEEN '$startdate' AND '$enddate' AND nama_pelanggan != 'BATAL'
                GROUP BY id_sales
            ) pelangganaktif"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'pelangganaktif.id_sales');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT id_karyawan,
                COUNT(DISTINCT(kode_pelanggan)) as jmltrans,
                COUNT(no_fak_penj) as jmltranspenjualan
                FROM penjualan
                WHERE tgltransaksi BETWEEN '$dari' AND '$sampai'
                GROUP BY id_karyawan
            ) pelanggantrans"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'pelanggantrans.id_karyawan');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT karyawan.id_karyawan,
                IFNULL(jmlkunjungan,0) as jmlkunjungan,
                IFNULL(jmlsesuaijadwal,0) as jmlsesuaijadwal
                FROM karyawan
                LEFT JOIN (
                    SELECT
                    penjualan.id_karyawan,
                    COUNT(no_fak_penj) as jmlkunjungan,
                    COUNT(
                    CASE WHEN
                    DAYNAME(tgltransaksi)='Monday' AND pelanggan.hari like '%Senin%' OR
                    DAYNAME(tgltransaksi)='Tuesday' AND pelanggan.hari like '%Selasa%' OR
                    DAYNAME(tgltransaksi)='Wednesday' AND pelanggan.hari like '%Rabu%' OR
                    DAYNAME(tgltransaksi)='Thursday' AND pelanggan.hari like '%Kamis%' OR
                    DAYNAME(tgltransaksi)='Friday' AND pelanggan.hari like '%Jumat%' OR
                    DAYNAME(tgltransaksi)='Saturday' AND pelanggan.hari like '%Sabtu%' OR
                    DAYNAME(tgltransaksi)='Sunday' AND pelanggan.hari like '%Minggu%'  THEN  penjualan.no_fak_penj END ) as jmlsesuaijadwal
                    FROM
                    `penjualan`
                    INNER JOIN `pelanggan` ON `penjualan`.`kode_pelanggan` = `pelanggan`.`kode_pelanggan`
                    INNER JOIN `karyawan` ON `penjualan`.`id_karyawan` = `karyawan`.`id_karyawan`
                    WHERE `tgltransaksi` BETWEEN '$dari' AND '$sampai' AND `nama_pelanggan` != 'BATAL'
                    GROUP BY
                            penjualan.id_karyawan
                ) kunjungan ON (karyawan.id_karyawan = kunjungan.id_karyawan)
            ) kunjungan"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'kunjungan.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
            SELECT karyawan.id_karyawan,COUNT(jml_sku) as jmltigasku
            FROM karyawan
            LEFT JOIN (
            SELECT penjualan.id_karyawan,COUNT(DISTINCT(kode_sku)) as jml_sku
            FROM detailpenjualan
            INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
            INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
            INNER JOIN master_barang ON barang.kode_produk = master_barang.kode_produk
            WHERE tgltransaksi BETWEEN '$dari' AND '$sampai' AND promo IS NULL
            GROUP BY penjualan.kode_pelanggan,penjualan.id_karyawan
            ORDER BY penjualan.kode_pelanggan
            ) sku ON (karyawan.id_karyawan = sku.id_karyawan)
            WHERE jml_sku >= 3 GROUP BY karyawan.id_karyawan
            ) sku"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'sku.id_karyawan');
            }
        );


        $query->join(
            DB::raw("(
                SELECT  id_karyawan,
                SUM(IF(kategori_komisi='KKQ01',jumlah_target,0)) as target_BB_DP,
                SUM(IF(kategori_komisi='KKQ02',jumlah_target,0)) as target_DS,
                SUM(IF(kategori_komisi='KKQ03',jumlah_target,0)) as target_SP,
                SUM(IF(kategori_komisi='KKQ04',jumlah_target,0)) as target_AR,
                SUM(IF(kategori_komisi='KKQ05',jumlah_target,0)) as target_AB_AS_CG5,
                SUM(IF(kategori_komisi='KKQ06',jumlah_target,0)) as target_SC
                FROM
                komisi_target_qty_detail k_detail
                INNER JOIN komisi_target ON k_detail.kode_target = komisi_target.kode_target
                INNER JOIN master_barang ON k_detail.kode_produk = master_barang.kode_produk
                WHERE bulan ='$bulan' AND tahun='$tahun'
                GROUP BY id_karyawan
                ) komisi"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'komisi.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT historibayar.id_karyawan,SUM(bayar) as cashin_jt
                FROM historibayar
                INNER JOIN penjualan ON historibayar.no_fak_penj = penjualan.no_fak_penj
                WHERE tglbayar BETWEEN '$dari' AND '$sampai' AND status_bayar IS NULL
                AND datediff(tglbayar, tgltransaksi) > 15
                GROUP BY historibayar.id_karyawan
            ) hbjt"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'hbjt.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT
                    salesbarunew,IFNULL(SUM(penjualan.total),0) - SUM(IFNULL( totalretur, 0 )) - SUM(IFNULL( totalbayar, 0 )) AS sisapiutang
                FROM
                penjualan
                INNER JOIN pelanggan ON penjualan.kode_pelanggan = pelanggan.kode_pelanggan
                LEFT JOIN (
                    SELECT
                        pj.no_fak_penj,IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,karyawan.nama_karyawan AS nama_sales,
                        IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                    FROM
                        penjualan pj
                    INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                    LEFT JOIN (
                        SELECT
                            id_move,
                            no_fak_penj,
                            move_faktur.id_karyawan AS salesbaru,
                            karyawan.kode_cabang AS cabangbaru
                        FROM
                            move_faktur
                        INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                        WHERE id_move IN ( SELECT max( id_move ) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj )
                        ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj )
                ) pjmove ON ( penjualan.no_fak_penj = pjmove.no_fak_penj )
                LEFT JOIN (
                    SELECT
                        retur.no_fak_penj AS no_fak_penj,
                        SUM(IFNULL( subtotal_pf, 0 ) - IFNULL( subtotal_gb, 0 )) AS totalretur
                    FROM
                        retur
                    WHERE
                        tglretur BETWEEN '$dari' AND '$sampai'
                    GROUP BY
                        retur.no_fak_penj
                ) r ON ( penjualan.no_fak_penj = r.no_fak_penj )
                LEFT JOIN (
                    SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar
                    FROM historibayar
                    WHERE tglbayar BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                ) hb ON ( penjualan.no_fak_penj = hb.no_fak_penj )

            WHERE penjualan.tgltransaksi BETWEEN '$dari' AND '$sampai' AND jenistransaksi = 'kredit' AND datediff( '$sampai', penjualan.tgltransaksi ) > 30
            GROUP BY
                salesbarunew

            ) penj"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'penj.salesbarunew');
            }
        );


        $query->leftJoin(
            DB::raw("(
                SELECT
                    salesbarunew,IFNULL( SUM(jumlah ), 0 ) - SUM(IFNULL( totalretur, 0 )) - SUM(IFNULL( totalbayar, 0 )) AS sisapiutangsaldo
                FROM
                saldoawal_piutang_faktur spf
                INNER JOIN penjualan ON spf.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN pelanggan ON penjualan.kode_pelanggan = pelanggan.kode_pelanggan
                LEFT JOIN (
                        SELECT
                            pj.no_fak_penj,IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,karyawan.nama_karyawan AS nama_sales,
                            IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                        FROM
                            penjualan pj
                        INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                        LEFT JOIN (
                            SELECT
                                id_move,
                                no_fak_penj,
                                move_faktur.id_karyawan AS salesbaru,
                                karyawan.kode_cabang AS cabangbaru
                            FROM
                                move_faktur
                            INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                            WHERE id_move IN ( SELECT max( id_move ) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj )
                            ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj )
                    ) pjmove ON ( penjualan.no_fak_penj = pjmove.no_fak_penj )
                    LEFT JOIN (
                        SELECT
                            retur.no_fak_penj AS no_fak_penj,
                            SUM(IFNULL( subtotal_pf, 0 ) - IFNULL( subtotal_gb, 0 )) AS totalretur
                        FROM
                            retur
                        WHERE
                            tglretur BETWEEN '$dari' AND '$sampai'
                        GROUP BY
                            retur.no_fak_penj
                    ) r ON ( penjualan.no_fak_penj = r.no_fak_penj )
                    LEFT JOIN (
                        SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar
                        FROM historibayar
                        WHERE tglbayar BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                    ) hb ON ( penjualan.no_fak_penj = hb.no_fak_penj )
                WHERE
                    datediff( '$sampai', penjualan.tgltransaksi ) > 30 AND bulan = '$bulan' AND tahun = '$tahun'
                GROUP BY
                    salesbarunew
            ) spf"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'spf.salesbarunew');
            }
        );

        if ($aturankomisi == 2) {
            $query->leftJoin(
                DB::raw("(
                SELECT karyawan.id_karyawan,
                (IFNULL(jml_belumsetorbulanlalu,0)+IFNULL(totalsetoran,0)) + IFNULL(jml_gmlast,0) - IFNULL(jml_gmnow,0) - IFNULL(jml_belumsetorbulanini,0) as realisasi_cashin
                FROM karyawan
                LEFT JOIN (
                    SELECT id_karyawan,jumlah as jml_belumsetorbulanlalu FROM belumsetor_detail
                    INNER JOIN belumsetor ON belumsetor_detail.kode_saldobs = belumsetor.kode_saldobs
                    WHERE bulan='$bulanlast' AND tahun='$tahunlast'
                ) bs ON (karyawan.id_karyawan = bs.id_karyawan)

                LEFT JOIN (
                    SELECT id_karyawan, SUM(lhp_tunai+lhp_tagihan) as totalsetoran FROM setoran_penjualan WHERE tgl_lhp BETWEEN '$dari' AND '$sampai' GROUP BY id_karyawan
                ) sp ON (karyawan.id_karyawan = sp.id_karyawan)

                LEFT JOIN (
                    SELECT
                    IFNULL(hb.id_karyawan,giro.id_karyawan) as id_karyawan,
                    SUM( jumlah ) AS jml_gmlast
                    FROM
                    giro
                    INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                    LEFT JOIN ( SELECT id_giro,id_karyawan FROM historibayar GROUP BY id_giro,id_karyawan ) AS hb ON giro.id_giro = hb.id_giro
                    WHERE
                    MONTH ( tgl_giro ) = '$bulanlast'
                    AND YEAR ( tgl_giro ) = '$tahunlast'
                    AND omset_tahun = '$tahun'
                    AND omset_bulan = '$bulan'
                    OR  MONTH ( tgl_giro ) = '$blnlast1'
                    AND YEAR ( tgl_giro ) = '$thnlast1'
                    AND omset_tahun = '$tahun'
                    AND omset_bulan = '$bulan'
                    GROUP BY
                    IFNULL( hb.id_karyawan, giro.id_karyawan )
                ) gmlast ON (karyawan.id_karyawan = gmlast.id_karyawan)
                LEFT JOIN (
                SELECT
                    IFNULL(hb.id_karyawan,giro.id_karyawan) as id_karyawan,
                    SUM( jumlah ) AS jml_gmnow
                FROM
                    giro
                    INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                    LEFT JOIN ( SELECT id_giro,id_karyawan, tglbayar FROM historibayar GROUP BY id_giro, tglbayar,id_karyawan ) AS hb ON giro.id_giro = hb.id_giro
                WHERE
                    tgl_giro >= '$dari'
                    AND tgl_giro <= '$sampai' AND tglbayar IS NULL AND omset_bulan = '0' AND omset_tahun = ''
                    OR  tgl_giro >= '$dari'
                    AND tgl_giro <= '$sampai' AND tglbayar >= '$end'
                    AND omset_bulan > '$bulan'
                    AND omset_tahun >= '$tahun'
                GROUP BY
                IFNULL( hb.id_karyawan, giro.id_karyawan )
                ) gmnow ON (karyawan.id_karyawan = gmnow.id_karyawan)

                LEFT JOIN (
                    SELECT belumsetor_detail.id_karyawan, SUM(jumlah) as jml_belumsetorbulanini
                    FROM belumsetor_detail
                    INNER JOIN belumsetor ON belumsetor_detail.kode_saldobs = belumsetor.kode_saldobs
                    WHERE bulan ='$bulan' AND tahun ='$tahun' GROUP BY id_karyawan
                ) bsnow ON (karyawan.id_karyawan = bsnow.id_karyawan)
                ) hb"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'hb.id_karyawan');
                }
            );
        } else if ($aturankomisi == 3) {
            $query->leftJoin(
                DB::raw("(
                    SELECT historibayar.id_karyawan,SUM(bayar) as realisasi_cashin
                    FROM historibayar WHERE tglbayar BETWEEN '$dari' AND '$sampai' AND status_bayar IS NULL
                    GROUP BY historibayar.id_karyawan
                ) hb"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'hb.id_karyawan');
                }
            );
        } else if ($aturankomisi == 4) {
            $query->leftJoin(
                DB::raw("(
                    SELECT historibayar.id_karyawan,SUM(bayar) as realisasi_cashin
                    FROM historibayar
                    INNER JOIN penjualan ON historibayar.no_fak_penj = penjualan.no_fak_penj
                    WHERE tglbayar BETWEEN '$dari' AND '$sampai' AND status_bayar IS NULL
                    AND datediff(tglbayar, tgltransaksi) <= 14
                    GROUP BY historibayar.id_karyawan
                ) hb"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'hb.id_karyawan');
                }
            );
        }

        $query->leftJoin(
            DB::raw("(
            SELECT
                salesbarunew,
                SUM(IF(kode_produk = 'AB' AND promo !='1' OR kode_produk = 'AB' AND promo IS NULL,jumlah,0)) as AB,
                SUM(IF(kode_produk = 'AR' AND promo !='1' OR kode_produk = 'AR' AND promo IS NULL,jumlah,0)) as AR,
                SUM(IF(kode_produk = 'AS' AND promo !='1' OR kode_produk = 'AS' AND promo IS NULL ,jumlah,0)) as `AS`,
                SUM(IF(kode_produk = 'BB' AND promo !='1' OR kode_produk = 'BB' AND promo IS NULL,jumlah,0)) as BB,
                SUM(IF(kode_produk = 'CG' AND promo !='1' OR kode_produk = 'CG' AND promo IS NULL,jumlah,0)) as CG,
                SUM(IF(kode_produk = 'CGG' AND promo !='1' OR kode_produk = 'CGG' AND promo IS NULL,jumlah,0)) as CGG,
                SUM(IF(kode_produk = 'DEP' AND promo !='1' OR kode_produk = 'DEP' AND promo IS NULL,jumlah,0)) as DEP,
                SUM(IF(kode_produk = 'DK' AND promo !='1' OR kode_produk = 'DK' AND promo IS NULL,jumlah,0)) as DK,
                SUM(IF(kode_produk = 'DS' AND promo !='1' OR kode_produk = 'DS' AND promo IS NULL,jumlah,0)) as DS,
                SUM(IF(kode_produk = 'SP' AND promo !='1' OR kode_produk = 'SP' AND promo IS NULL,jumlah,0)) as SP,
                SUM(IF(kode_produk = 'BBP' AND promo !='1' OR kode_produk = 'BBP' AND promo IS NULL,jumlah,0)) as BBP,
                SUM(IF(kode_produk = 'SPP' AND promo !='1' OR kode_produk = 'SPP' AND promo IS NULL,jumlah,0)) as SPP,
                SUM(IF(kode_produk = 'CG5' AND promo !='1' OR kode_produk = 'CG5' AND promo IS NULL,jumlah,0)) as CG5,
                SUM(IF(kode_produk = 'SP8' AND promo !='1' OR kode_produk = 'SP8' AND promo IS NULL,jumlah,0)) as SP8,
                SUM(IF(kode_produk = 'SC' AND promo !='1' OR kode_produk = 'SC' AND promo IS NULL,jumlah,0)) as SC,
                SUM(IF(kode_produk = 'SP500' AND promo !='1' OR kode_produk = 'SP500' AND promo IS NULL,jumlah,0)) as SP500
                FROM detailpenjualan
                INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang

                LEFT JOIN (
                    SELECT pj.no_fak_penj,
                        IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
                        IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
                    FROM penjualan pj
                    INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                LEFT JOIN (
                    SELECT
                        id_move,no_fak_penj,
                        move_faktur.id_karyawan as salesbaru,
                        karyawan.kode_cabang  as cabangbaru
                    FROM move_faktur
                    INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                    WHERE id_move IN (SELECT max(id_move) FROM move_faktur WHERE tgl_move <= '$dari' GROUP BY no_fak_penj)
                ) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
            ) pjmove ON (penjualan.no_fak_penj = pjmove.no_fak_penj)
            WHERE  status_lunas ='1' AND tgl_pelunasan BETWEEN '$dari' AND '$sampai'
            GROUP BY salesbarunew
            ) realisasi"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'realisasi.salesbarunew');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT penjualan.id_karyawan,
                SUM(IF(kode_produk = 'AB',jumlah,0)) as retur_AB,
                SUM(IF(kode_produk = 'AR',jumlah,0)) as retur_AR,
                SUM(IF(kode_produk = 'AS',jumlah,0)) as `retur_AS`,
                SUM(IF(kode_produk = 'BB',jumlah,0)) as retur_BB,
                SUM(IF(kode_produk = 'CG' ,jumlah,0)) as retur_CG,
                SUM(IF(kode_produk = 'CGG',jumlah,0)) as retur_CGG,
                SUM(IF(kode_produk = 'DEP',jumlah,0)) as retur_DEP,
                SUM(IF(kode_produk = 'DK',jumlah,0)) as retur_DK,
                SUM(IF(kode_produk = 'DS',jumlah,0)) as retur_DS,
                SUM(IF(kode_produk = 'SP',jumlah,0)) as retur_SP,
                SUM(IF(kode_produk = 'BBP',jumlah,0)) as retur_BBP,
                SUM(IF(kode_produk = 'SPP',jumlah,0)) as retur_SPP,
                SUM(IF(kode_produk = 'CG5',jumlah,0)) as retur_CG5,
                SUM(IF(kode_produk = 'SP8',jumlah,0)) as retur_SP8,
                SUM(IF(kode_produk = 'SC',jumlah,0)) as retur_SC,
                SUM(IF(kode_produk = 'SP500',jumlah,0)) as retur_SP500
                FROM detailretur
                INNER JOIN retur ON detailretur.no_retur_penj = retur.no_retur_penj
                INNER JOIN penjualan ON retur.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN barang ON detailretur.kode_barang = barang.kode_barang

                WHERE  status_lunas ='1' AND tgl_pelunasan BETWEEN '$dari' AND '$sampai'
                GROUP BY penjualan.id_karyawan
            ) returpf"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'returpf.id_karyawan');
            }
        );



        $query->leftJoin(
            DB::raw("(
                SELECT id_karyawan,jumlah as potongankomisi,keterangan as ket_potongan
                FROM komisi_potongan
                WHERE bulan = '$bulan' AND tahun='$tahun'
            ) potongankomisi"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'potongankomisi.id_karyawan');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT id_karyawan,jumlah as komisifix, keterangan as ket_komisifix
                FROM komisi_akhir
                WHERE bulan = '$bulan' AND tahun='$tahun'
            ) komisiakhir"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'komisiakhir.id_karyawan');
            }
        );



        if (Auth::user()->id == 27 || $cabang == "BDG" && Auth::user()->kode_cabang == "PCF") {
            $query->whereIn('kode_cabang', ['BDG', 'PWK']);
        } else {
            $query->where('kode_cabang', $cabang);
        }
        $query->where('nama_karyawan', '!=', '');

        $komisi = $query->get();

        $nmbulan  = $namabulan[$bulan];

        $kodekp = 'KP' . $cabang;
        $kodespv = 'SPV' . $cabang;

        $potongankp = DB::table('komisi_potongan')->where('id_karyawan', $kodekp)
            ->where('bulan', $bulan)->where('tahun', $tahun)
            ->first();

        $komisiakhir = DB::table('komisi_akhir')->where('id_karyawan', $kodekp)
            ->where('bulan', $bulan)->where('tahun', $tahun)
            ->first();


        $supervisorcabang = ['BDG', 'TSM'];
        if (in_array($cabang, $supervisorcabang)) {
            $potonganspv = DB::table('komisi_potongan')->where('id_karyawan', $kodespv)
                ->where('bulan', $bulan)->where('tahun', $tahun)
                ->first();

            $komisiakhirspv = DB::table('komisi_akhir')->where('id_karyawan', $kodespv)
                ->where('bulan', $bulan)->where('tahun', $tahun)
                ->first();
        } else {
            $potonganspv = null;
            $komisiakhirspv = null;
        }

        if (isset($_POST['export'])) {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Komisi $time.xls");
        }


        return view('targetkomisi.laporan.cetak_komisi_oktober2023', compact('komisi', 'cbg', 'nmbulan', 'tahun', 'produk', 'bulan', 'cabang', 'potongankp', 'komisiakhir', 'supervisorcabang', 'potonganspv', 'komisiakhirspv', 'startdate', 'enddate'));
    }


    public function cetakkomisijanuari2024($cabang, $bulan, $tahun, $aturankomisi, $dari, $hariini, $sampai)
    { //$dari = '2023-06-31';
        $lastmonth = date('Y-m-d', strtotime(date($dari) . '- 1 month'));

        $enddate = date('Y-m-t', strtotime($dari));
        if (date("d", strtotime($enddate)) == 31) {
            $enddate = date("Y-m", strtotime($enddate)) . "-30";
        }
        //dd($lastdateofmonth);
        $last3month = date('Y-m-d', strtotime('-2 month', strtotime($enddate)));
        $date = explode("-", $last3month);
        $startdate = $date[0] . "-" . $date[1] . "-01";
        $lastdate = explode("-", $lastmonth);
        $bulanlast = $lastdate[1] + 0;
        $tahunlast = $lastdate[0];

        $lastmonth_dari = $tahunlast . "-" . $bulanlast . "-01";
        $lastmonth_sampai = date("Y-m-t", strtotime($lastmonth_dari));

        //dd($lastmonth_dari . "/" . $lastmonth_sampai);
        if ($bulanlast == 1) {
            $blnlast1 = 12;
            $thnlast1 = $tahun - 1;
        } else {
            $blnlast1 = $bulanlast - 1;
            $thnlast1 = $tahun;
        }




        if ($bulan == 12) {
            $bln = 1;
            $thn = $tahun + 1;
        } else {
            $bln = $bulan + 1;
            $thn = $tahun;
        }


        $ceknextBulan = DB::table('setoran_pusat')->where('omset_bulan', $bulan)->where('omset_tahun', $tahun)
            ->whereRaw('MONTH(tgl_diterimapusat) = ' . $bln)
            ->whereRaw('YEAR(tgl_diterimapusat) = ' . $thn)
            ->where('kode_cabang', $cabang)
            ->orderBy('tgl_diterimapusat', 'desc')
            ->first();
        if ($ceknextBulan ==  null) {
            $end = date("Y-m-t", strtotime($dari));
        } else {
            $end = $ceknextBulan->tgl_diterimapusat;
        }

        $kategori_komisi = DB::table('kategori_komisi')->get();

        $select_target = "";
        $select_realisasi_qty = "";

        $field_target = "";
        $field_realisasi_qty = "";
        foreach ($kategori_komisi as $d) {
            $field_target .= "target_" . $d->kode_kategori . ",";
            $field_realisasi_qty .= "realisasi_qty_" . $d->kode_kategori . ",";

            $select_target .= "SUM(IF(kategori_komisi='$d->kode_kategori',jumlah_target,0)) as target_" . $d->kode_kategori . ",";
            $select_realisasi_qty .= "SUM(IF(kategori_komisi = '$d->kode_kategori' AND promo !='1' OR kategori_komisi = '$d->kode_kategori' AND promo IS NULL,jumlah/barang.isipcsdus,0)) as realisasi_qty_" . $d->kode_kategori . ",";
        }

        $select_qty_produk = "";
        $field_qty_produk = "";
        $produk = Barang::orderBy('kode_produk')->get();
        foreach ($produk as $p) {
            $field_qty_produk .= "`qty_" . $p->kode_produk . "`,";
            $select_qty_produk .= "SUM(IF(kode_produk='$p->kode_produk',jumlah/isipcsdus,0)) as `qty_" . $p->kode_produk . "`,";
        }
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $cbg = DB::table('cabang')->where('kode_cabang', $cabang)->first();
        $query = Salesman::query();
        $query->selectRaw("
            $field_target
            $field_realisasi_qty
            $field_qty_produk
            realisasi_cashin,
            realisasipenjvsavg,
            realisasi_jmlpelanggantrans,
            jmlkunjungan,
            jmlsesuaijadwal,
            IFNULL(sisapiutangsaldo,0) + IFNULL(sisapiutang,0) as sisapiutang,
            karyawan.id_karyawan,nama_karyawan,kategori_salesman,status_komisi
        ");
        //Target
        $query->join(
            DB::raw("(
                SELECT
                $select_target
                id_karyawan
                FROM
                komisi_target_qty_detail k_detail
                INNER JOIN komisi_target ON k_detail.kode_target = komisi_target.kode_target
                INNER JOIN master_barang ON k_detail.kode_produk = master_barang.kode_produk
                WHERE bulan ='$bulan' AND tahun='$tahun'
                GROUP BY id_karyawan
                ) komisi"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'komisi.id_karyawan');
            }
        );


        //Realisasi Sell Out
        $query->leftJoin(
            DB::raw("(
            SELECT
                $select_realisasi_qty
                salesbarunew
                FROM detailpenjualan
                INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                INNER JOIN master_barang ON barang.kode_produk = master_barang.kode_produk
                LEFT JOIN (
                    SELECT pj.no_fak_penj,
                        IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
                        IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
                    FROM penjualan pj
                    INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                LEFT JOIN (
                    SELECT
                        id_move,no_fak_penj,
                        move_faktur.id_karyawan as salesbaru,
                        karyawan.kode_cabang  as cabangbaru
                    FROM move_faktur
                    INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                    WHERE id_move IN (SELECT max(id_move) FROM move_faktur WHERE tgl_move <= '$dari' GROUP BY no_fak_penj)
                ) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
            ) pjmove ON (penjualan.no_fak_penj = pjmove.no_fak_penj)
            WHERE  status_lunas ='1' AND tgl_pelunasan BETWEEN '$dari' AND '$sampai'
            GROUP BY salesbarunew
            ) realisasi"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'realisasi.salesbarunew');
            }
        );


        //Reqlisasi Qty Kendaraann
        $query->leftjoin(
            DB::raw("(
                SELECT
                $select_qty_produk
                id_karyawan
                FROM
                detailpenjualan
                INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                WHERE tgltransaksi BETWEEN '$dari' AND '$sampai' AND promo != 1 OR
                tgltransaksi BETWEEN '$dari' AND '$sampai' AND promo IS NULL
                GROUP BY id_karyawan
                ) realisasiqtykendaraan"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'realisasiqtykendaraan.id_karyawan');
            }
        );
        //Realsasi Cashin
        $query->leftJoin(
            DB::raw("(
            SELECT karyawan.id_karyawan,
            (IFNULL(jml_belumsetorbulanlalu,0)+IFNULL(totalsetoran,0)) + IFNULL(jml_gmlast,0) - IFNULL(jml_gmnow,0) - IFNULL(jml_belumsetorbulanini,0) as realisasi_cashin
            FROM karyawan
            LEFT JOIN (
                SELECT id_karyawan,jumlah as jml_belumsetorbulanlalu FROM belumsetor_detail
                INNER JOIN belumsetor ON belumsetor_detail.kode_saldobs = belumsetor.kode_saldobs
                WHERE bulan='$bulanlast' AND tahun='$tahunlast'
            ) bs ON (karyawan.id_karyawan = bs.id_karyawan)

            LEFT JOIN (
                SELECT id_karyawan, SUM(lhp_tunai+lhp_tagihan) as totalsetoran FROM setoran_penjualan WHERE tgl_lhp BETWEEN '$dari' AND '$sampai' GROUP BY id_karyawan
            ) sp ON (karyawan.id_karyawan = sp.id_karyawan)

            LEFT JOIN (
                SELECT
                IFNULL(hb.id_karyawan,giro.id_karyawan) as id_karyawan,
                SUM( jumlah ) AS jml_gmlast
                FROM
                giro
                INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                LEFT JOIN ( SELECT id_giro,id_karyawan FROM historibayar GROUP BY id_giro,id_karyawan ) AS hb ON giro.id_giro = hb.id_giro
                WHERE
                MONTH ( tgl_giro ) = '$bulanlast'
                AND YEAR ( tgl_giro ) = '$tahunlast'
                AND omset_tahun = '$tahun'
                AND omset_bulan = '$bulan'
                OR  MONTH ( tgl_giro ) = '$blnlast1'
                AND YEAR ( tgl_giro ) = '$thnlast1'
                AND omset_tahun = '$tahun'
                AND omset_bulan = '$bulan'
                GROUP BY
                IFNULL( hb.id_karyawan, giro.id_karyawan )
            ) gmlast ON (karyawan.id_karyawan = gmlast.id_karyawan)
            LEFT JOIN (
            SELECT
                IFNULL(hb.id_karyawan,giro.id_karyawan) as id_karyawan,
                SUM( jumlah ) AS jml_gmnow
            FROM
                giro
                INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                LEFT JOIN ( SELECT id_giro,id_karyawan, tglbayar FROM historibayar GROUP BY id_giro, tglbayar,id_karyawan ) AS hb ON giro.id_giro = hb.id_giro
            WHERE
                tgl_giro >= '$dari'
                AND tgl_giro <= '$sampai' AND tglbayar IS NULL AND omset_bulan = '0' AND omset_tahun = ''
                OR  tgl_giro >= '$dari'
                AND tgl_giro <= '$sampai' AND tglbayar >= '$end'
                AND omset_bulan > '$bulan'
                AND omset_tahun >= '$tahun'
            GROUP BY
            IFNULL( hb.id_karyawan, giro.id_karyawan )
            ) gmnow ON (karyawan.id_karyawan = gmnow.id_karyawan)

            LEFT JOIN (
                SELECT belumsetor_detail.id_karyawan, SUM(jumlah) as jml_belumsetorbulanini
                FROM belumsetor_detail
                INNER JOIN belumsetor ON belumsetor_detail.kode_saldobs = belumsetor.kode_saldobs
                WHERE bulan ='$bulan' AND tahun ='$tahun' GROUP BY id_karyawan
            ) bsnow ON (karyawan.id_karyawan = bsnow.id_karyawan)
            ) hb"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'hb.id_karyawan');
            }
        );


        $query->leftJoin(
            DB::raw("(
                SELECT
                    salesbarunew,IFNULL(SUM(penjualan.total),0) - SUM(IFNULL( totalretur, 0 )) - SUM(IFNULL( totalbayar, 0 )) AS sisapiutang
                FROM
                penjualan
                INNER JOIN pelanggan ON penjualan.kode_pelanggan = pelanggan.kode_pelanggan
                LEFT JOIN (
                    SELECT
                        pj.no_fak_penj,IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,karyawan.nama_karyawan AS nama_sales,
                        IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                    FROM
                        penjualan pj
                    INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                    LEFT JOIN (
                        SELECT
                            id_move,
                            no_fak_penj,
                            move_faktur.id_karyawan AS salesbaru,
                            karyawan.kode_cabang AS cabangbaru
                        FROM
                            move_faktur
                        INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                        WHERE id_move IN ( SELECT max( id_move ) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj )
                        ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj )
                ) pjmove ON ( penjualan.no_fak_penj = pjmove.no_fak_penj )
                LEFT JOIN (
                    SELECT
                        retur.no_fak_penj AS no_fak_penj,
                        SUM(IFNULL( subtotal_pf, 0 ) - IFNULL( subtotal_gb, 0 )) AS totalretur
                    FROM
                        retur
                    WHERE
                        tglretur BETWEEN '$dari' AND '$sampai'
                    GROUP BY
                        retur.no_fak_penj
                ) r ON ( penjualan.no_fak_penj = r.no_fak_penj )
                LEFT JOIN (
                    SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar
                    FROM historibayar
                    WHERE tglbayar BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                ) hb ON ( penjualan.no_fak_penj = hb.no_fak_penj )

            WHERE penjualan.tgltransaksi BETWEEN '$dari' AND '$sampai' AND jenistransaksi = 'kredit' AND datediff( '$sampai', penjualan.tgltransaksi ) > 30
            GROUP BY
                salesbarunew

            ) penj"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'penj.salesbarunew');
            }
        );


        $query->leftJoin(
            DB::raw("(
                SELECT
                    salesbarunew,IFNULL( SUM(jumlah ), 0 ) - SUM(IFNULL( totalretur, 0 )) - SUM(IFNULL( totalbayar, 0 )) AS sisapiutangsaldo
                FROM
                saldoawal_piutang_faktur spf
                INNER JOIN penjualan ON spf.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN pelanggan ON penjualan.kode_pelanggan = pelanggan.kode_pelanggan
                LEFT JOIN (
                        SELECT
                            pj.no_fak_penj,IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,karyawan.nama_karyawan AS nama_sales,
                            IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                        FROM
                            penjualan pj
                        INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                        LEFT JOIN (
                            SELECT
                                id_move,
                                no_fak_penj,
                                move_faktur.id_karyawan AS salesbaru,
                                karyawan.kode_cabang AS cabangbaru
                            FROM
                                move_faktur
                            INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                            WHERE id_move IN ( SELECT max( id_move ) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj )
                            ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj )
                    ) pjmove ON ( penjualan.no_fak_penj = pjmove.no_fak_penj )
                    LEFT JOIN (
                        SELECT
                            retur.no_fak_penj AS no_fak_penj,
                            SUM(IFNULL( subtotal_pf, 0 ) - IFNULL( subtotal_gb, 0 )) AS totalretur
                        FROM
                            retur
                        WHERE
                            tglretur BETWEEN '$dari' AND '$sampai'
                        GROUP BY
                            retur.no_fak_penj
                    ) r ON ( penjualan.no_fak_penj = r.no_fak_penj )
                    LEFT JOIN (
                        SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar
                        FROM historibayar
                        WHERE tglbayar BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                    ) hb ON ( penjualan.no_fak_penj = hb.no_fak_penj )
                WHERE
                    datediff( '$sampai', penjualan.tgltransaksi ) > 30 AND bulan = '$bulan' AND tahun = '$tahun'
                GROUP BY
                    salesbarunew
            ) spf"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'spf.salesbarunew');
            }
        );

        //Penjualan VS AVG
        $query->leftjoin(
            DB::raw("(
                SELECT karyawan.id_karyawan,COUNT(kode_pelanggan) as realisasipenjvsavg
                FROM karyawan
                LEFT JOIN (
                SELECT penjualan.id_karyawan,penjualan.kode_pelanggan,SUM(total) as totalpenjualanbulanini,totalpenjualanbulanlalu
                FROM penjualan
                LEFT JOIN (
                    SELECT penjualan.kode_pelanggan, SUM(total) as totalpenjualanbulanlalu
                    FROM penjualan
                    WHERE tgltransaksi BETWEEN '$lastmonth_dari' AND '$lastmonth_sampai'
                    GROUP BY kode_pelanggan
                ) penjlalu ON (penjualan.kode_pelanggan = penjlalu.kode_pelanggan)
                WHERE tgltransaksi BETWEEN '$dari' AND '$sampai' AND totalpenjualanbulanlalu IS NOT NULL
                GROUP BY penjualan.kode_pelanggan,penjualan.id_karyawan,totalpenjualanbulanlalu
                HAVING (SUM(total) >= totalpenjualanbulanlalu) ) jmlpelanggan ON (karyawan.id_karyawan = jmlpelanggan.id_karyawan)
                GROUP BY karyawan.id_karyawan
            ) penjualanvsavg"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'penjualanvsavg.id_karyawan');
            }
        );


        //Realisasi OA
        $query->leftjoin(
            DB::raw("(
                SELECT penjualan.id_karyawan,COUNT(DISTINCT kode_pelanggan) as realisasi_jmlpelanggantrans
                FROM penjualan
                WHERE tgltransaksi BETWEEN '$dari' AND '$sampai'
                GROUP BY penjualan.id_karyawan
            ) oa"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'oa.id_karyawan');
            }
        );


        //Realisasi Routing
        $query->leftJoin(
            DB::raw("(
                SELECT
                penjualan.id_karyawan,
                COUNT(no_fak_penj) as jmlkunjungan,
                COUNT(
                CASE WHEN
                DAYNAME(tgltransaksi)='Monday' AND IFNULL(ajuan_routing.hari,pelanggan.hari) like '%Senin%' OR
                DAYNAME(tgltransaksi)='Tuesday' AND IFNULL(ajuan_routing.hari,pelanggan.hari) like '%Selasa%' OR
                DAYNAME(tgltransaksi)='Wednesday' AND IFNULL(ajuan_routing.hari,pelanggan.hari) like '%Rabu%' OR
                DAYNAME(tgltransaksi)='Thursday' AND IFNULL(ajuan_routing.hari,pelanggan.hari) like '%Kamis%' OR
                DAYNAME(tgltransaksi)='Friday' AND IFNULL(ajuan_routing.hari,pelanggan.hari) like '%Jumat%' OR
                DAYNAME(tgltransaksi)='Saturday' AND IFNULL(ajuan_routing.hari,pelanggan.hari) like '%Sabtu%' OR
                DAYNAME(tgltransaksi)='Sunday' AND IFNULL(ajuan_routing.hari,pelanggan.hari) like '%Minggu%'  THEN  penjualan.no_fak_penj END ) as jmlsesuaijadwal
                FROM
                `penjualan`
                INNER JOIN `pelanggan` ON `penjualan`.`kode_pelanggan` = `pelanggan`.`kode_pelanggan`
                LEFT JOIN (
                    SELECT kode_pelanggan,hari
                    FROM pengajuan_routing WHERE no_pengajuan IN (SELECT MAX(no_pengajuan) as no_pengajuan FROM pengajuan_routing
                    WHERE tgl_pengajuan <= '$sampai' AND status = 1  GROUP BY kode_pelanggan)
                ) ajuan_routing ON (pelanggan.kode_pelanggan = ajuan_routing.kode_pelanggan)
                INNER JOIN `karyawan` ON `penjualan`.`id_karyawan` = `karyawan`.`id_karyawan`
                WHERE `tgltransaksi` BETWEEN '$dari' AND '$sampai' AND `nama_pelanggan` != 'BATAL'
                GROUP BY
                    penjualan.id_karyawan
            ) kunjungan"),
            function ($join) {
                $join->on('karyawan.id_karyawan', '=', 'kunjungan.id_karyawan');
            }
        );


        // if (Auth::user()->id == 27 || $cabang == "BDG" && Auth::user()->kode_cabang == "PCF") {
        //     $query->whereIn('kode_cabang', ['BDG', 'PWK']);
        // } else {
        //     $query->where('kode_cabang', $cabang);
        // }

        $query->where('kode_cabang', $cabang);
        $query->where('nama_karyawan', '!=', '');
        $query->where('karyawan.id_karyawan', '!=', 'SKLT09');
        $query->where('status_aktif_sales', 1);
        $komisi = $query->get();
        $nmbulan  = $namabulan[$bulan];
        if (isset($_POST['export'])) {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Komisi $time.xls");
        }
        return view('targetkomisi.laporan.cetak_komisi_januari2024', compact(
            'komisi',
            'cbg',
            'nmbulan',
            'tahun',
            'produk',
            'bulan',
            'cabang',
            'startdate',
            'enddate',
            'kategori_komisi'
        ));
    }



    public function cetakinsentifomjanuari2024(Request $request)
    {



        $bulan = $request->bulan <= 9 ? "0" . $request->bulan :  $request->bulan;
        $tahun = $request->tahun;
        lockyear($tahun);
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $dari = $tahun . "-" . $bulan . "-01";


        $hariini = date('Y-m-d');
        $sampai = date('Y-m-t', strtotime($dari));
        if ($hariini < $sampai) {
            $sampai = $hariini;
        } else {
            $sampai = $sampai;
        }

        $lastmonth = date('Y-m-d', strtotime(date($dari) . '- 1 month'));
        $enddate = date('Y-m-t', strtotime($lastmonth));
        // if (date("d", strtotime($enddate)) == 31) {
        //     $enddate = date("Y-m", strtotime($enddate)) . "-30";
        // }


        //dd($lastdateofmonth);
        // $last3month = date('Y-m-d', strtotime('-3 month', strtotime($sampai)));
        $bulansampai = date('m', strtotime($sampai));
        $tahunsampai = date('Y', strtotime($sampai));
        $startmonth = $bulansampai - 3;
        $startyear = $tahunsampai;
        if ($startmonth <= 0) {
            $startmonth = $startmonth + 12;
            $startyear = $startyear - 1;
        } else {
            $startmonth = $startmonth;
            $startyear = $startyear;
        }

        $startmonth = $startmonth < 10 ? "0" . $startmonth : $startmonth;
        // $date = explode("-", $last3month);
        // dd($last3month);
        $startdate = $startyear . "-" . $startmonth . "-01";

        // dd($startdate . "-" . $enddate);

        $cbg = Auth::user()->kode_cabang;
        $kode_cabang = $request->kode_cabang;
        $cabang = DB::table('cabang')->where('kode_cabang', $cbg)->first();
        $lastmonth = date('Y-m-d', strtotime(date($dari) . '- 1 month'));
        $lastdate = explode("-", $lastmonth);
        $bulanlast = $lastdate[1] + 0;
        $tahunlast = $lastdate[0];
        if ($bulanlast == 1) {
            $blnlast1 = 12;
            $thnlast1 = $tahun - 1;
        } else {
            $blnlast1 = $bulanlast - 1;
            $thnlast1 = $tahun;
        }

        if ($bulan == 12) {
            $bln = 1;
            $thn = $tahun + 1;
        } else {
            $bln = $bulan + 1;
            $thn = $tahun;
        }

        $start_last_date = $tahunlast . "-" . $bulanlast . "-01";
        $end_last_date = date("Y-m-t", strtotime($start_last_date));
        //dd($bulanlast . "-" . $tahunlast);

        $produk = DB::table('master_barang')->where('status', 1)
            ->orderBy('kode_produk');
        $select_mutasi = "";
        $field_mutasi = "";
        $select_total_retur = "";
        $field_total_retur = "";

        $bulan = $bulan * 1;
        foreach ($produk->get() as $d) {

            $field_mutasi .= "retur_" . $d->kode_produk . ",reject_mobil_" . $d->kode_produk . ",reject_pasar_" . $d->kode_produk . ",reject_gudang_" . $d->kode_produk . ",repack_" . $d->kode_produk . ",";
            $field_total_retur .= "totalretur_" . $d->kode_produk . ",";

            $select_mutasi .= "
                SUM(IF(dmc.kode_produk='$d->kode_produk' AND jenis_mutasi = 'RETUR',jumlah/isipcsdus,0)) as retur_" . $d->kode_produk . ",
                SUM(IF(dmc.kode_produk='$d->kode_produk' AND jenis_mutasi = 'REJECT MOBIL',jumlah/isipcsdus,0)) as reject_mobil_" . $d->kode_produk . ",
                SUM(IF(dmc.kode_produk='$d->kode_produk' AND jenis_mutasi = 'REJECT PASAR',jumlah/isipcsdus,0)) as reject_pasar_" . $d->kode_produk . ",
                SUM(IF(dmc.kode_produk='$d->kode_produk' AND jenis_mutasi = 'REJECT GUDANG',jumlah/isipcsdus,0)) as reject_gudang_" . $d->kode_produk . ",
                SUM(IF(dmc.kode_produk='$d->kode_produk' AND jenis_mutasi = 'REPACK',jumlah/isipcsdus,0)) as repack_" . $d->kode_produk . ",";

            $select_total_retur .= "SUM(IF(kode_produk='$d->kode_produk',detailretur.subtotal,0)) as totalretur_" . $d->kode_produk . ",";
        }
        $query = Cabang::query();
        $query->selectRaw("
        $field_mutasi
        $field_total_retur
        cabang.kode_cabang,nama_cabang,
        ROUND(jmlpengambilan/jmlkapasitas * 100) as ratio_kendaraan,
        jmlpengambilan,jmlkapasitas,
        ROUND(penjualanbulanberjalan/penjualanbulanlalu*100) as ratio_penjualan,
        ROUND(jmlsesuaijadwal/jmlkunjungan *100) as ratio_routing,
        lama_lpc,jam_lpc,
        (IFNULL(jml_belumsetorbulanlalu,0) + IFNULL(totalsetoran,0) + IFNULL(jml_gmlast,0) - IFNULL(jml_gmnow,0) - IFNULL(jml_belumsetorbulanini,0)) as realisasi_cashin,
        IFNULL(sisapiutangsaldo,0) + IFNULL(sisapiutang,0) as sisapiutang,
        IFNULL(jmlbiaya,0) + IFNULL(ROUND(jmllogistik),0)  + IFNULL(ROUND(jmlpenggunaanbahan),0) as totalbiaya,penjualanbulanberjalan,jmlpelanggan,jmltrans");
        $query->leftjoin(
            DB::raw("(
                SELECT karyawan.kode_cabang,SUM(kapasitas) as jmlkapasitas , SUM(jmlpengambilan)  as jmlpengambilan
                FROM dpb
                INNER JOIN karyawan ON dpb.id_karyawan = karyawan.id_karyawan
                INNER JOIN kendaraan ON dpb.no_kendaraan = kendaraan.no_polisi
                LEFT JOIN (
                    SElECT detail_dpb.no_dpb,
                        SUM(jml_pengambilan) as jmlpengambilan
                        FROM detail_dpb
                        GROUP BY detail_dpb.no_dpb
                )	pengambilan ON (dpb.no_dpb = pengambilan.no_dpb)

                WHERE tgl_pengambilan BETWEEN '$dari' AND '$sampai' AND no_kendaraan !='ZL'
                GROUP BY karyawan.kode_cabang
            ) kendaraan"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'kendaraan.kode_cabang');
            }
        );

        $query->leftjoin(
            DB::raw("(
                SELECT karyawan.kode_cabang,
                SUM(IF(tgltransaksi BETWEEN '$start_last_date' AND '$end_last_date' , penjualan.total,0)) as penjualanbulanlalu,
                SUM(IF(tgltransaksi BETWEEN '$dari' AND '$sampai' , penjualan.total,0)) as penjualanbulanberjalan
                FROM penjualan
                INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                WHERE tgltransaksi BETWEEN '$start_last_date' AND '$sampai'
                GROUP BY karyawan.kode_cabang
            ) penjualan"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'penjualan.kode_cabang');
            }
        );


        $query->leftjoin(
            DB::raw("(
                SELECT
                karyawan.kode_cabang,
                COUNT(no_fak_penj) as jmlkunjungan,
                COUNT(
                CASE WHEN
                DAYNAME(tgltransaksi)='Monday' AND IFNULL(ajuan_routing.hari,pelanggan.hari) like '%Senin%' OR
                DAYNAME(tgltransaksi)='Tuesday' AND IFNULL(ajuan_routing.hari,pelanggan.hari) like '%Selasa%' OR
                DAYNAME(tgltransaksi)='Wednesday' AND IFNULL(ajuan_routing.hari,pelanggan.hari) like '%Rabu%' OR
                DAYNAME(tgltransaksi)='Thursday' AND IFNULL(ajuan_routing.hari,pelanggan.hari) like '%Kamis%' OR
                DAYNAME(tgltransaksi)='Friday' AND IFNULL(ajuan_routing.hari,pelanggan.hari) like '%Jumat%' OR
                DAYNAME(tgltransaksi)='Saturday' AND IFNULL(ajuan_routing.hari,pelanggan.hari) like '%Sabtu%' OR
                DAYNAME(tgltransaksi)='Sunday' AND IFNULL(ajuan_routing.hari,pelanggan.hari) like '%Minggu%'  THEN  penjualan.no_fak_penj END ) as jmlsesuaijadwal
                FROM
                `penjualan`
                INNER JOIN `pelanggan` ON `penjualan`.`kode_pelanggan` = `pelanggan`.`kode_pelanggan`
                LEFT JOIN (
                        SELECT kode_pelanggan,hari
                        FROM pengajuan_routing WHERE no_pengajuan IN (SELECT MAX(no_pengajuan) as no_pengajuan FROM pengajuan_routing
                        WHERE tgl_pengajuan <= '$sampai' AND status = 1  GROUP BY kode_pelanggan)
                ) ajuan_routing ON (pelanggan.kode_pelanggan = ajuan_routing.kode_pelanggan)
                INNER JOIN `karyawan` ON `penjualan`.`id_karyawan` = `karyawan`.`id_karyawan`
                WHERE `tgltransaksi` BETWEEN '$dari' AND '$sampai' AND `nama_pelanggan` != 'BATAL'
                GROUP BY
                karyawan.kode_cabang
            ) routing"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'routing.kode_cabang');
            }
        );


        $query->leftjoin(
            DB::raw("(
                SELECT kode_cabang,datediff(tgl_lpc,'$sampai') as lama_lpc,jam_lpc
                FROM lpc
                WHERE bulan ='$bulan' AND tahun = '$tahun'
            ) lpc"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'lpc.kode_cabang');
            }
        );


        $query->leftJoin(
            DB::raw("(
                SELECT belumsetor.kode_cabang,SUM(jumlah) as jml_belumsetorbulanlalu
                FROM belumsetor_detail
                INNER JOIN belumsetor ON belumsetor_detail.kode_saldobs = belumsetor.kode_saldobs
                WHERE bulan='$bulanlast' AND tahun='$tahunlast'
                GROUP BY belumsetor.kode_cabang
            ) bs"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'bs.kode_cabang');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT kode_cabang, SUM(lhp_tunai+lhp_tagihan) as totalsetoran
                FROM setoran_penjualan
                WHERE tgl_lhp BETWEEN '$dari' AND '$sampai' GROUP BY kode_cabang
            ) sp"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'sp.kode_cabang');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT
                karyawan.kode_cabang,
                SUM( jumlah ) AS jml_gmlast
                FROM
                giro
                INNER JOIN karyawan ON giro.id_karyawan = karyawan.id_karyawan
                INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                LEFT JOIN ( SELECT id_giro FROM historibayar GROUP BY id_giro ) AS hb ON giro.id_giro = hb.id_giro
                WHERE
                MONTH ( tgl_giro ) = '$bulanlast'
                AND YEAR ( tgl_giro ) = '$tahunlast'
                AND omset_tahun = '$tahun'
                AND omset_bulan = '$bulan'
                OR MONTH ( tgl_giro ) = '$blnlast1'
                AND YEAR ( tgl_giro ) = '$thnlast1'
                AND omset_tahun = '$tahun'
                AND omset_bulan = '$bulan'
                GROUP BY
                karyawan.kode_cabang
            ) gmlast"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'gmlast.kode_cabang');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT
                karyawan.kode_cabang,
                SUM( jumlah ) AS jml_gmnow
                FROM
                giro
                INNER JOIN karyawan ON giro.id_karyawan = karyawan.id_karyawan
                INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                LEFT JOIN (
                SELECT kode_cabang,MAX(tgl_diterimapusat) as tgl_diterimapusat
                FROM setoran_pusat
                WHERE omset_bulan = '$bulan' AND omset_tahun ='$thn'
                AND MONTH(tgl_diterimapusat) = '$bln' AND YEAR(tgl_diterimapusat) = '$thn'
                GROUP BY kode_cabang
                ) nexttgl ON (karyawan.kode_cabang = nexttgl.kode_cabang)
                LEFT JOIN ( SELECT id_giro, tglbayar FROM historibayar GROUP BY id_giro, tglbayar ) AS hb ON giro.id_giro = hb.id_giro
                WHERE
                tgl_giro >= '$dari'
                AND tgl_giro <= '$sampai' AND tglbayar IS NULL AND omset_bulan='0' AND omset_tahun='' OR tgl_giro>= '$dari'
                AND tgl_giro <= '$sampai' AND tglbayar>= IFNULL(tgl_diterimapusat,'$sampai')
                AND omset_bulan > '$bulan'
                AND omset_tahun >= '$tahun'
                GROUP BY
                karyawan.kode_cabang
            ) gmnow"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'gmnow.kode_cabang');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT kode_cabang, SUM(jumlah) as jml_belumsetorbulanini
                FROM belumsetor_detail
                INNER JOIN belumsetor ON belumsetor_detail.kode_saldobs = belumsetor.kode_saldobs
                WHERE bulan ='$bulan' AND tahun ='$tahun' GROUP BY kode_cabang
            ) bsnow"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'bsnow.kode_cabang');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT
                    cabangbarunew,IFNULL(SUM(penjualan.total),0) - SUM(IFNULL( totalretur, 0 )) - SUM(IFNULL( totalbayar, 0 )) AS sisapiutang
                FROM
                penjualan
                INNER JOIN pelanggan ON penjualan.kode_pelanggan = pelanggan.kode_pelanggan
                LEFT JOIN (
                    SELECT
                        pj.no_fak_penj,IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,karyawan.nama_karyawan AS nama_sales,
                        IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                    FROM
                        penjualan pj
                    INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                    LEFT JOIN (
                        SELECT
                            id_move,
                            no_fak_penj,
                            move_faktur.id_karyawan AS salesbaru,
                            karyawan.kode_cabang AS cabangbaru
                        FROM
                            move_faktur
                        INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                        WHERE id_move IN ( SELECT max( id_move ) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj )
                        ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj )
                ) pjmove ON ( penjualan.no_fak_penj = pjmove.no_fak_penj )
                LEFT JOIN (
                    SELECT
                        retur.no_fak_penj AS no_fak_penj,
                        SUM(IFNULL( subtotal_pf, 0 ) - IFNULL( subtotal_gb, 0 )) AS totalretur
                    FROM
                        retur
                    WHERE
                        tglretur BETWEEN '$dari' AND '$sampai'
                    GROUP BY
                        retur.no_fak_penj
                ) r ON ( penjualan.no_fak_penj = r.no_fak_penj )
                LEFT JOIN (
                    SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar
                    FROM historibayar
                    WHERE tglbayar BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                ) hb ON ( penjualan.no_fak_penj = hb.no_fak_penj )

            WHERE penjualan.tgltransaksi BETWEEN '$dari' AND '$sampai' AND jenistransaksi = 'kredit'
            AND datediff( '$sampai', penjualan.tgltransaksi ) > 30
            AND penjualan.id_karyawan NOT IN ('SGRT01','SGRT02')
            GROUP BY
                cabangbarunew

            ) penj"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'penj.cabangbarunew');
            }
        );


        $query->leftJoin(
            DB::raw("(
                SELECT
                    cabangbarunew,IFNULL( SUM(jumlah ), 0 ) - SUM(IFNULL( totalretur, 0 )) - SUM(IFNULL( totalbayar, 0 )) AS sisapiutangsaldo
                FROM
                saldoawal_piutang_faktur spf
                INNER JOIN penjualan ON spf.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN pelanggan ON penjualan.kode_pelanggan = pelanggan.kode_pelanggan
                LEFT JOIN (
                        SELECT
                            pj.no_fak_penj,IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,karyawan.nama_karyawan AS nama_sales,
                            IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                        FROM
                            penjualan pj
                        INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                        LEFT JOIN (
                            SELECT
                                id_move,
                                no_fak_penj,
                                move_faktur.id_karyawan AS salesbaru,
                                karyawan.kode_cabang AS cabangbaru
                            FROM
                                move_faktur
                            INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                            WHERE id_move IN ( SELECT max( id_move ) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj )
                            ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj )
                    ) pjmove ON ( penjualan.no_fak_penj = pjmove.no_fak_penj )
                    LEFT JOIN (
                        SELECT
                            retur.no_fak_penj AS no_fak_penj,
                            SUM(IFNULL( subtotal_pf, 0 ) - IFNULL( subtotal_gb, 0 )) AS totalretur
                        FROM
                            retur
                        WHERE
                            tglretur BETWEEN '$dari' AND '$sampai'
                        GROUP BY
                            retur.no_fak_penj
                    ) r ON ( penjualan.no_fak_penj = r.no_fak_penj )
                    LEFT JOIN (
                        SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar
                        FROM historibayar
                        WHERE tglbayar BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                    ) hb ON ( penjualan.no_fak_penj = hb.no_fak_penj )
                WHERE
                    datediff( '$sampai', penjualan.tgltransaksi ) > 30
                    AND penjualan.id_karyawan NOT IN ('SGRT01','SGRT02')
                    AND bulan = '$bulan' AND tahun = '$tahun'
                    AND penjualan.no_fak_penj NOT IN ('BTNA000933','BTNC000540','BTNC000659')
                GROUP BY
                cabangbarunew
            ) spf"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'spf.cabangbarunew');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT costratio_biaya.kode_cabang,SUM(jumlah) as jmlbiaya
                FROM costratio_biaya
                WHERE tgl_transaksi BETWEEN '$dari' AND '$sampai'
                GROUP BY costratio_biaya.kode_cabang
            ) costratio"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'costratio.kode_cabang');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT kode_cabang,
                SUM(qty *
                CASE
                WHEN saldoawal.hargasaldoawal IS NULL THEN pemasukan.hargapemasukan
                WHEN pemasukan.hargapemasukan IS NULL THEN saldoawal.hargasaldoawal
                ELSE (saldoawal.totalsa + pemasukan.totalpemasukan) / (saldoawal.qtysaldoawal + pemasukan.qtypemasukan) END ) as jmllogistik
                FROM
                detail_pengeluaran
                INNER JOIN pengeluaran ON detail_pengeluaran.nobukti_pengeluaran = pengeluaran.nobukti_pengeluaran
                INNER JOIN master_barang_pembelian ON detail_pengeluaran.kode_barang = master_barang_pembelian.kode_barang
                LEFT JOIN (
                        SELECT saldoawal_gl_detail.kode_barang,SUM(saldoawal_gl_detail.harga) AS hargasaldoawal,SUM( qty ) AS qtysaldoawal,SUM(saldoawal_gl_detail.harga*qty) AS
                        totalsa FROM saldoawal_gl_detail
                        INNER JOIN saldoawal_gl ON saldoawal_gl.kode_saldoawal_gl=saldoawal_gl_detail.kode_saldoawal_gl
                        WHERE bulan = '$bulan' AND tahun = '$tahun'
                        GROUP BY saldoawal_gl_detail.kode_barang
                ) saldoawal ON (detail_pengeluaran.kode_barang = saldoawal.kode_barang)

                LEFT JOIN (
                        SELECT detail_pemasukan.kode_barang,SUM( penyesuaian ) AS penyesuaian,SUM( qty ) AS qtypemasukan,SUM( harga ) AS hargapemasukan,SUM(detail_pemasukan.harga * qty) AS totalpemasukan FROM
                        detail_pemasukan
                        INNER JOIN pemasukan ON detail_pemasukan.nobukti_pemasukan = pemasukan.nobukti_pemasukan
                        WHERE MONTH(tgl_pemasukan) = '$bulan' AND YEAR(tgl_pemasukan) = '$tahun'
                        GROUP BY detail_pemasukan.kode_barang
                ) pemasukan ON (detail_pengeluaran.kode_barang = pemasukan.kode_barang)
                WHERE tgl_pengeluaran BETWEEN  '$dari' AND '$sampai' AND kode_cabang IS NOT NULL AND master_barang_pembelian.kode_kategori = 'K001'
                GROUP BY detail_pengeluaran.kode_cabang
            ) logistik"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'logistik.kode_cabang');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT kode_cabang,
                SUM(
                    CASE
                    WHEN satuan = 'KG' THEN qty_berat * 1000
                    WHEN satuan = 'Liter' THEN qty_berat * 1000 * IFNULL((SELECT harga FROM harga_minyak WHERE bulan ='$bulan' AND tahun = '$tahun'),0)
                    ELSE qty_unit
                    END
                    *
                    CASE
                    WHEN satuan ='KG' THEN (harga +totalharga + IF(qtypengganti2=0,(qtypengganti2*1000) * 0,( (qtypengganti2 *1000) * (IF(qtypemb2=0,(harga / (qtyberatsa *1000)),totalharga / (qtypemb2*1000))))) + IF(qtylainnya2=0,(qtylainnya2*1000) * 0,( (qtylainnya2 *1000) * (IF(qtypemb2=0,(harga / (qtyberatsa *1000)),totalharga / (qtypemb2*1000)))))) / ( (qtyberatsa*1000) + (qtypemb2 * 1000) + (qtylainnya2*1000) + (qtypengganti2*1000))
                    ELSE
                    (harga + totalharga + IF(qtylainnya1=0,qtylainnya1*0,qtylainnya1 * IF(qtylainnya1=0,0,IF(qtypemb1=0,harga/qtyunitsa,totalharga/qtypemb1 ))) + IF(qtypengganti1=0,qtypengganti1*0,qtypengganti1 * IF(qtypengganti1=0,0,IF(qtypemb1=0,harga/qtyunitsa,totalharga/qtypemb1 )))) / (qtyunitsa + qtypemb1 + qtylainnya1 + qtypengganti1)
                    END
                ) as jmlpenggunaanbahan
                FROM detail_pengeluaran_gb
                INNER JOIN pengeluaran_gb ON detail_pengeluaran_gb.nobukti_pengeluaran = pengeluaran_gb.nobukti_pengeluaran
                INNER JOIN master_barang_pembelian ON detail_pengeluaran_gb.kode_barang = master_barang_pembelian.kode_barang
                LEFT JOIN (
                    SELECT
                        detail_pemasukan_gb.kode_barang,
                        SUM( IF( departemen = 'Pembelian' , qty_unit ,0 )) AS qtypemb1,
                        SUM( IF( departemen = 'Lainnya' , qty_unit ,0 )) AS qtylainnya1,
                        SUM( IF( departemen = 'Retur Pengganti' , qty_unit ,0 )) AS qtypengganti1,

                        SUM( IF( departemen = 'Pembelian' , qty_berat ,0 )) AS qtypemb2,
                        SUM( IF( departemen = 'Lainnya' , qty_berat ,0 )) AS qtylainnya2,
                        SUM( IF( departemen = 'Retur Pengganti' , qty_berat ,0 )) AS qtypengganti2,
                        SUM( (IF( departemen = 'Pembelian' , qty_berat ,0 )) + (IF( departemen = 'Lainnya' , qty_berat ,0 ))) AS pemasukanqtyberat
                        FROM
                        detail_pemasukan_gb
                        INNER JOIN pemasukan_gb ON detail_pemasukan_gb.nobukti_pemasukan = pemasukan_gb.nobukti_pemasukan
                        WHERE MONTH(tgl_pemasukan) = '$bulan' AND YEAR(tgl_pemasukan) = '$tahun'
                        GROUP BY detail_pemasukan_gb.kode_barang
                ) gm ON (detail_pengeluaran_gb.kode_barang = gm.kode_barang)


                LEFT JOIN (
                        SELECT SUM((qty*harga)+penyesuaian) as totalharga,kode_barang
                        FROM detail_pembelian
                        INNER JOIN pembelian ON detail_pembelian.nobukti_pembelian = pembelian.nobukti_pembelian
                        WHERE MONTH(tgl_pembelian) = '$bulan' AND YEAR(tgl_pembelian) = '$tahun'
                        GROUP BY kode_barang

                ) dp ON (detail_pengeluaran_gb.kode_barang = dp.kode_barang)

                LEFT JOIN (
                        SELECT kode_barang,harga
                        FROM saldoawal_harga_gb
                        WHERE bulan = '$bulan' AND tahun = '$tahun'
                        GROUP BY kode_barang,harga
                ) hrgsa ON (detail_pengeluaran_gb.kode_barang = hrgsa.kode_barang)


                LEFT JOIN (
                        SELECT saldoawal_gb_detail.kode_barang,
                        SUM( qty_unit ) AS qtyunitsa,
                        SUM( qty_berat ) AS qtyberatsa
                        FROM saldoawal_gb_detail
                        INNER JOIN saldoawal_gb ON saldoawal_gb.kode_saldoawal_gb=saldoawal_gb_detail.kode_saldoawal_gb
                        WHERE bulan = '$bulan' AND tahun = '$tahun' GROUP BY saldoawal_gb_detail.kode_barang
                ) sa ON (detail_pengeluaran_gb.kode_barang = sa.kode_barang)

                WHERE tgl_pengeluaran BETWEEN '$dari' AND '$sampai' AND kode_cabang IS NOT NULL
                GROUP BY kode_cabang
            ) penggunaanbahan"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'penggunaanbahan.kode_cabang');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT pelanggan.kode_cabang, COUNT(DISTINCT(penjualan.kode_pelanggan)) as jmlpelanggan
                FROM penjualan
                INNER JOIN pelanggan ON penjualan.kode_pelanggan = pelanggan.kode_pelanggan
                INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                WHERE tgltransaksi BETWEEN '$startdate' AND '$enddate' AND nama_pelanggan != 'BATAL'
                GROUP BY pelanggan.kode_cabang
            ) pelangganaktif"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'pelangganaktif.kode_cabang');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT karyawan.kode_cabang,
                COUNT(DISTINCT(kode_pelanggan)) as jmltrans,
                COUNT(no_fak_penj) as jmltranspenjualan
                FROM penjualan
                INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                WHERE tgltransaksi BETWEEN '$dari' AND '$sampai'
                GROUP BY karyawan.kode_cabang
            ) pelanggantrans"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'pelanggantrans.kode_cabang');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT
                $select_mutasi
                kode_cabang
                FROM detail_mutasi_gudang_cabang dmc
                INNER JOIN master_barang ON dmc.kode_produk = master_barang.kode_produk
                INNER JOIN mutasi_gudang_cabang mc ON dmc.no_mutasi_gudang_cabang = mc.no_mutasi_gudang_cabang
                WHERE tgl_mutasi_gudang_cabang BETWEEN '$dari' AND '$sampai'
                GROUP BY kode_cabang
             ) mutasicabang"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'mutasicabang.kode_cabang');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT
                $select_total_retur
                karyawan.kode_cabang
                FROM detailretur
                INNER JOIN barang ON detailretur.kode_barang = barang.kode_barang
                INNER JOIN retur ON detailretur.no_retur_penj = retur.no_retur_penj
                INNER JOIN penjualan ON retur.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                WHERE tglretur BETWEEN '$dari' AND '$sampai'
                GROUP BY kode_cabang
             ) hargeretur"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'hargeretur.kode_cabang');
            }
        );
        if (!empty($request->kode_cabang)) {
            $query->where('cabang.kode_cabang', $kode_cabang);
        }
        $query->where('cabang.kode_cabang', '!=', 'PST');
        $insentif = $query->get();

        if (isset($_POST['export'])) {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Insentif OM $time.xls");
        }
        return view('targetkomisi.laporan.cetak_insentif_januari_2024', compact(
            'namabulan',
            'cabang',
            'bulan',
            'tahun',
            'cabang',
            'insentif',
            'produk'
        ));
    }
}
