<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Cabang;
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
        } else {
            $query->where('tahun', $tahunini);
        }
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
            ->selectRaw('karyawan.id_karyawan,nama_karyawan,ab,ar,ase,bb,cg,cgg,dep,ds,sp,cg5,sc,sp8')
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
                SUM(IF(kode_produk='SP8',jumlah_target,0)) as sp8
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
            $data = [
                'kode_target' => $kode_target,
                'id_karyawan' => $id_karyawan,
                'kode_produk' => $kode_produk,
                'jumlah_target' => $jmltarget
            ];

            DB::table('komisi_target_qty_detail')->insert($data);
        } else {
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

        $target = DB::table('komisi_target_qty_detail')
            ->selectRaw("komisi_target_qty_detail.id_karyawan,nama_karyawan,kode_cabang,jumlah_target_cashin,
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
            SUM(IF(kode_produk ='SP8',jumlah_target,0)) as 'SP8'")
            ->join('komisi_target', 'komisi_target_qty_detail.kode_target', '=', 'komisi_target.kode_target')
            ->join('karyawan', 'komisi_target_qty_detail.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin('komisi_target_cashin_detail', function ($join) {
                $join->on('komisi_target_qty_detail.kode_target', '=', 'komisi_target_cashin_detail.kode_target')
                    ->on('komisi_target_qty_detail.id_karyawan', '=', 'komisi_target_cashin_detail.id_karyawan');
            })
            ->where('komisi_target_qty_detail.kode_target', $request->kode_target)
            ->groupByRaw('komisi_target_qty_detail.id_karyawan,nama_karyawan,kode_cabang,jumlah_target_cashin')
            ->orderBy('karyawan.kode_cabang')
            ->orderBy('nama_karyawan')
            ->get();
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
        $update = DB::table('komisi_target_qty_detail')
            ->join('karyawan', 'komisi_target_qty_detail.id_karyawan', '=', 'karyawan.id_karyawan')
            ->join('komisi_target', 'komisi_target_qty_detail.kode_target', '=', 'komisi_target.kode_target')
            ->where('komisi_target_qty_detail.kode_target', $kode_target)
            ->where('karyawan.kode_cabang', $kode_cabang)
            ->update($data);

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
        $update = DB::table('komisi_target_qty_detail')
            ->join('karyawan', 'komisi_target_qty_detail.id_karyawan', '=', 'karyawan.id_karyawan')
            ->join('komisi_target', 'komisi_target_qty_detail.kode_target', '=', 'komisi_target.kode_target')
            ->where('komisi_target_qty_detail.kode_target', $kode_target)
            ->where('karyawan.kode_cabang', $kode_cabang)
            ->update($data);

        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di Cancel  ']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Cancel Hubungi Tim IT  ']);
        }
    }

    public function laporankomisi()
    {
        $cabang = DB::table('cabang')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('targetkomisi.laporan.frm.lap_komisi', compact('cabang', 'bulan'));
    }

    public function cetaklaporankomisi(Request $request)
    {
        $cabang = $request->kode_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $aturankomisi = $request->aturankomisi;
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date('Y-m-t', strtotime($dari));
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
        if ($bulan >= 5 && $tahun >= 2022) {
            $query = Salesman::query();
            $query->selectRaw('
            karyawan.id_karyawan,nama_karyawan,kategori_salesman,
            target_BB_DP,
            BB,
            DEP,
            target_DS,
            DS,
            SP8,
            target_SP,
            SP,
            target_SC,
            SC,
            target_AR,
            AR,
            target_AB_AS_CG5,
            AB,
            `AS`,
            CG5,
            realisasi_cashin,
            sisapiutang
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
                    SELECT salesbarunew,SUM((ifnull(penjualan.total,0) - (ifnull(totalpf_last,0)-ifnull(totalgb_last,0)))-ifnull(totalbayar,0)) as sisapiutang
                    FROM penjualan
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
                        WHERE tgl_move <= '$sampai'
                        GROUP BY no_fak_penj,move_faktur.id_karyawan,karyawan.kode_cabang
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
                        giro.id_karyawan,
                        SUM( jumlah ) AS jml_gmlast
                        FROM
                        giro
                        INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                        LEFT JOIN ( SELECT id_giro FROM historibayar GROUP BY id_giro ) AS hb ON giro.id_giro = hb.id_giro
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
                        id_karyawan
                    ) gmlast ON (karyawan.id_karyawan = gmlast.id_karyawan)
                    LEFT JOIN (
                    SELECT
                        giro.id_karyawan,
                        SUM( jumlah ) AS jml_gmnow
                    FROM
                        giro
                        INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                        LEFT JOIN ( SELECT id_giro, tglbayar FROM historibayar GROUP BY id_giro, tglbayar ) AS hb ON giro.id_giro = hb.id_giro
                    WHERE
                        tgl_giro >= '$dari'
                        AND tgl_giro <= '$sampai' AND tglbayar IS NULL AND omset_bulan = '0' AND omset_tahun = ''
                        OR  tgl_giro >= '$dari'
                        AND tgl_giro <= '$sampai' AND tglbayar >= '$end'
                        AND omset_bulan > '$bulan'
                        AND omset_tahun >= '$tahun'
                    GROUP BY
                        giro.id_karyawan
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
            } else {
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
            }
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
                    SUM(IF(kode_produk = 'SC' AND promo !='1' OR kode_produk = 'SC' AND promo IS NULL,jumlah,0)) as SC
                    FROM detailpenjualan
                    INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                    INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                    LEFT JOIN (
                    SELECT no_fak_penj,max(tglbayar) as lastpayment
                    FROM historibayar
                    GROUP BY no_fak_penj
                    ) hb ON (hb.no_fak_penj = penjualan.no_fak_penj)
                    WHERE  status_lunas ='1' AND lastpayment BETWEEN '$dari' AND '$sampai'
                    GROUP BY penjualan.id_karyawan
                ) realisasi"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'realisasi.id_karyawan');
                }
            );

            $query->where('kode_cabang', $cabang);
            $query->where('nama_karyawan', '!=', '');
            $komisi = $query->get();
        }
        $nmbulan  = $namabulan[$bulan];
        if (isset($_POST['export'])) {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Komisi $time.xls");
        }
        return view('targetkomisi.laporan.cetak_komisi_lpu', compact('komisi', 'cbg', 'nmbulan', 'tahun', 'produk', 'driver', 'helper', 'gudang', 'tunaikredit', 'bulan', 'cabang'));
    }

    public function laporaninsentif()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('targetkomisi.laporan.frm.lap_insentif', compact('bulan'));
    }

    public function cetaklaporaninsentif(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date('Y-m-t', strtotime($dari));
        $cbg = Auth::user()->kode_cabang;
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
        $query->selectRaw("cabang.kode_cabang,nama_cabang,(IFNULL(jml_belumsetorbulanlalu,0) + IFNULL(totalsetoran,0) + IFNULL(jml_gmlast,0) - IFNULL(jml_gmnow,0) - IFNULL(jml_belumsetorbulanini,0)) as cashin,sisapiutang,lamalpc");
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
                SELECT kode_cabang,datediff(tgl_lpc,'$sampai') as lamalpc
                FROM lpc
                WHERE bulan ='$bulan' AND tahun = '$tahun'
            ) app_lpc"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'app_lpc.kode_cabang');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT cabangbarunew,SUM((ifnull(penjualan.total,0) - (ifnull(totalpf_last,0)-ifnull(totalgb_last,0)))-ifnull(totalbayar,0)) as sisapiutang
                FROM penjualan
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
                    WHERE tgl_move <= '$sampai'
                    GROUP BY no_fak_penj,move_faktur.id_karyawan,karyawan.kode_cabang
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
                GROUP BY cabangbarunew
            ) penj"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'penj.cabangbarunew');
            }
        );
        $query->where('cabang.kode_cabang', '!=', 'GRT');
        if ($cbg != "PCF") {
            $query->where('cabang.kode_cabang', $cbg);
        }
        $insentif = $query->get();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Insentif $dari-$sampai.xls");
        }
        return view('targetkomisi.laporan.cetak_insentif', compact('insentif', 'cabang', 'namabulan', 'bulan', 'tahun'));
    }
}
