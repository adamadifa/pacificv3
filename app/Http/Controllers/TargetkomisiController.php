<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Cabang;
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
}
