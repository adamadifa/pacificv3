<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Cabang;
use App\Models\Omancabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class OmancabangController extends Controller
{
    public function index(Request $request)
    {
        $bulansekarang = date("m");
        $tahunsekarang = date("Y");
        $query = Omancabang::query();
        if (!empty($request->bulan)) {
            $query->where('bulan', $request->bulan);
        } else {
            $query->where('bulan', $bulansekarang);
        }

        if (!empty($request->tahun)) {
            $query->where('tahun', $request->tahun);
        } else {
            $query->where('tahun', $tahunsekarang);
        }

        if (!empty($request->kode_cabang)) {
            $query->where('kode_cabang', $request->kode_cabang);
        }

        $oman_cabang = $query->paginate(12);
        $oman_cabang->appends($request->all());
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('omancabang.index', compact('oman_cabang', 'cabang', 'bulan'));
    }

    public function create()
    {
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $produk = Barang::orderBy('kode_produk')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('omancabang.create', compact('cabang', 'produk', 'bulan'));
    }

    public function edit($no_order)
    {
        $no_order = Crypt::decrypt($no_order);
        $dataoman = DB::table('oman_cabang')->where('no_order', $no_order)->first();
        $m1 = DB::table('detail_oman_cabang')->where('no_order', $no_order)->where('mingguke', 1)->first();
        $m2 = DB::table('detail_oman_cabang')->where('no_order', $no_order)->where('mingguke', 2)->first();
        $m3 = DB::table('detail_oman_cabang')->where('no_order', $no_order)->where('mingguke', 3)->first();
        $m4 = DB::table('detail_oman_cabang')->where('no_order', $no_order)->where('mingguke', 4)->first();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $produk = DB::table('master_barang')
            ->select('master_barang.kode_produk', 'nama_barang', 'mingguke_1', 'mingguke_2', 'mingguke_3', 'mingguke_4')
            ->leftJoin(
                DB::raw("(
                    SELECT kode_produk,
                    SUM(IF(mingguke='1',jumlah,0)) as mingguke_1,
                    SUM(IF(mingguke='2',jumlah,0)) as mingguke_2,
                    SUM(IF(mingguke='3',jumlah,0)) as mingguke_3,
                    SUM(IF(mingguke='4',jumlah,0)) as mingguke_4
                    FROM detail_oman_cabang
                    WHERE no_order = '$no_order'
                    GROUP BY kode_produk
            ) oman"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'oman.kode_produk');
                }
            )
            ->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('omancabang.edit', compact('cabang', 'produk', 'bulan', 'dataoman', 'm1', 'm2', 'm3', 'm4'));
    }

    public function cekomancabang(Request $request)
    {
        $cekomancabang = DB::table('oman_cabang')
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->where('kode_cabang', $request->kode_cabang)
            ->count();
        echo $cekomancabang;
    }

    public function store(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kode_cabang = $request->kode_cabang;
        $no_order  = "OMAN" . $kode_cabang . $bulan . $tahun;
        $jumproduk = $request->jumproduk;

        DB::beginTransaction();
        try {
            $data = array(
                'no_order' => $no_order,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'kode_cabang' => $kode_cabang,
            );

            $cekoman = DB::table('oman_cabang')->where('no_order', $no_order)->count();
            $cekoman = 0;
            if (empty($cekoman)) {
                DB::table('oman_cabang')->insert($data);
                for ($i = 1; $i <= $jumproduk; $i++) {
                    $kodeproduk = "kode_produk" . $i;
                    $kode_produk = $request->$kodeproduk;
                    for ($m = 1; $m <= 4; $m++) {
                        $jml = "jml" . $i . "m" . $m;
                        $darim = "darim" . $m;
                        $sampaim = "sampaim" . $m;
                        $d = $request->$darim;
                        $s = $request->$sampaim;
                        $dari = $tahun . "-" . $bulan . "-" . $d;
                        $sampai = $tahun . "-" . $bulan . "-" . $s;
                        $jml = $request->$jml;
                        $data_oman   = array(
                            'no_order' => $no_order,
                            'kode_produk' => $kode_produk,
                            'mingguke' => $m,
                            'dari' => $dari,
                            'sampai' => $sampai,
                            'jumlah' => (empty($jml)) ? 0 : $jml
                        );
                        echo $darim . "-" . $dari . "<br>";
                        //die;
                        DB::table('detail_oman_cabang')->insert($data_oman);
                    }
                }
            }
            DB::commit();
            //die;
            return redirect('/omancabang')->with(['success' => 'Data Berhasil Disimpan ']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return  redirect('/omancabang')->with(['warning' => 'Data Gagal Disimpan Hubungi Tim IT']);
        }
    }


    public function update($no_order, Request $request)
    {
        $no_order = Crypt::decrypt($no_order);
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $jumproduk = $request->jumproduk;
        DB::beginTransaction();
        try {
            for ($i = 1; $i <= $jumproduk; $i++) {
                $kodeproduk = "kode_produk" . $i;
                $kode_produk = $request->$kodeproduk;
                for ($m = 1; $m <= 4; $m++) {
                    $jml = "jml" . $i . "m" . $m;
                    $darim = "darim" . $m;
                    $sampaim = "sampaim" . $m;
                    $d = $request->$darim;
                    $s = $request->$sampaim;
                    $dari = $tahun . "-" . $bulan . "-" . $d;
                    $sampai = $tahun . "-" . $bulan . "-" . $s;
                    $jml = $request->$jml;
                    $data_oman   = array(
                        'dari' => $dari,
                        'sampai' => $sampai,
                        'jumlah' => (empty($jml)) ? 0 : $jml
                    );
                    //echo $darim . "-" . $dari . "<br>";
                    //die;
                    DB::table('detail_oman_cabang')
                        ->where('mingguke', $m)
                        ->where('no_order', $no_order)
                        ->where('kode_produk', $kode_produk)
                        ->update($data_oman);
                }
            }
            DB::commit();
            //die;
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update ']);
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            return  Redirect::back()->with(['warning' => 'Data Gagal Di Update Hubungi Tim IT']);
        }
    }

    public function delete($no_order)
    {
        $no_order = Crypt::decrypt($no_order);
        $hapus = DB::table('oman_cabang')->where('no_order', $no_order)->delete();
        if ($hapus) {
            return redirect('/omancabang')->with(['success' => 'Data Berhasil Di Hapus ']);
        } else {
            return redirect('/omancabang')->with(['success' => 'Data Gagal Di Hapus ']);
        }
    }

    public function show(Request $request)
    {
        $no_order = $request->no_order;
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $dataoman = DB::table('oman_cabang')->where('no_order', $request->no_order)->first();
        $m1 = DB::table('detail_oman_cabang')->where('no_order', $no_order)->where('mingguke', 1)->first();
        $m2 = DB::table('detail_oman_cabang')->where('no_order', $no_order)->where('mingguke', 2)->first();
        $m3 = DB::table('detail_oman_cabang')->where('no_order', $no_order)->where('mingguke', 3)->first();
        $m4 = DB::table('detail_oman_cabang')->where('no_order', $no_order)->where('mingguke', 4)->first();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $produk = DB::table('master_barang')
            ->select('master_barang.kode_produk', 'nama_barang', 'mingguke_1', 'mingguke_2', 'mingguke_3', 'mingguke_4')
            ->leftJoin(
                DB::raw("(
                    SELECT kode_produk,
                    SUM(IF(mingguke='1',jumlah,0)) as mingguke_1,
                    SUM(IF(mingguke='2',jumlah,0)) as mingguke_2,
                    SUM(IF(mingguke='3',jumlah,0)) as mingguke_3,
                    SUM(IF(mingguke='4',jumlah,0)) as mingguke_4
                    FROM detail_oman_cabang
                    WHERE no_order = '$no_order'
                    GROUP BY kode_produk
            ) oman"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'oman.kode_produk');
                }
            )
            ->get();
        return view('omancabang.show', compact('dataoman', 'produk', 'm1', 'm2', 'm3', 'm4', 'bulan'));
    }

    public function getomancabang(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $produk = DB::table('master_barang')
            ->select('master_barang.kode_produk', 'nama_barang', 'mingguke_1', 'mingguke_2', 'mingguke_3', 'mingguke_4')
            ->leftJoin(
                DB::raw("(
                    SELECT kode_produk,
                    SUM(IF(mingguke='1',jumlah,0)) as mingguke_1,
                    SUM(IF(mingguke='2',jumlah,0)) as mingguke_2,
                    SUM(IF(mingguke='3',jumlah,0)) as mingguke_3,
                    SUM(IF(mingguke='4',jumlah,0)) as mingguke_4
                    FROM detail_oman_cabang
                    INNER JOIN oman_cabang ON detail_oman_cabang.no_order = oman_cabang.no_order
                    WHERE bulan ='$bulan' AND tahun='$tahun'
                    GROUP BY kode_produk
            ) oman"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'oman.kode_produk');
                }
            )
            ->get();

        return view('omancabang.getomancabang', compact('produk'));
    }
}