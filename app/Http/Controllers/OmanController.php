<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Oman;
use App\Models\Omancabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class OmanController extends Controller
{
    public function index(Request $request)
    {
        $bulansekarang = date("m");
        $tahunsekarang = date("Y");
        $query = Oman::query();

        $query->select('oman.*', 'no_permintaan');
        $query->leftJoin('permintaan_produksi', 'oman.no_order', '=', 'permintaan_produksi.no_order');
        if (!empty($request->tahun)) {
            $query->where('tahun', $request->tahun);
        } else {
            $query->where('tahun', $tahunsekarang);
        }


        $oman_marketing = $query->get();


        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('oman.index', compact('oman_marketing', 'bulan'));
    }


    public function create()
    {
        $produk = Barang::orderBy('kode_produk')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('oman.create', compact('produk', 'bulan'));
    }

    public function cekoman(Request $request)
    {
        $cekoman = DB::table('oman')
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->count();
        echo $cekoman;
    }

    public function store(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $tgl_order = $request->tgl_order;
        $no_order  = "OMAN" . $bulan . $tahun;
        $jumproduk = $request->jumproduk;

        DB::beginTransaction();
        try {
            $data = array(
                'no_order' => $no_order,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'tgl_order' => $tgl_order,
            );

            $cekoman = DB::table('oman')->where('no_order', $no_order)->count();
            $cekoman = 0;
            if (empty($cekoman)) {
                DB::table('oman')->insert($data);
                DB::table('oman_cabang')->where('bulan', $bulan)->where('tahun', $tahun)->update([
                    'status' => 1
                ]);
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
                        DB::table('detail_oman')->insert($data_oman);
                    }
                }
            }
            DB::commit();
            //die;
            return redirect('/oman')->with(['success' => 'Data Berhasil Disimpan ']);
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            return  redirect('/oman')->with(['warning' => 'Data Gagal Disimpan Hubungi Tim IT']);
        }
    }


    public function edit($no_order)
    {
        $no_order = Crypt::decrypt($no_order);
        $dataoman = DB::table('oman')->where('no_order', $no_order)->first();
        $m1 = DB::table('detail_oman')->where('no_order', $no_order)->where('mingguke', 1)->first();
        $m2 = DB::table('detail_oman')->where('no_order', $no_order)->where('mingguke', 2)->first();
        $m3 = DB::table('detail_oman')->where('no_order', $no_order)->where('mingguke', 3)->first();
        $m4 = DB::table('detail_oman')->where('no_order', $no_order)->where('mingguke', 4)->first();
        $produk = DB::table('master_barang')
            ->select('master_barang.kode_produk', 'nama_barang', 'mingguke_1', 'mingguke_2', 'mingguke_3', 'mingguke_4')
            ->leftJoin(
                DB::raw("(
                    SELECT kode_produk,
                    SUM(IF(mingguke='1',jumlah,0)) as mingguke_1,
                    SUM(IF(mingguke='2',jumlah,0)) as mingguke_2,
                    SUM(IF(mingguke='3',jumlah,0)) as mingguke_3,
                    SUM(IF(mingguke='4',jumlah,0)) as mingguke_4
                    FROM detail_oman
                    WHERE no_order ='$no_order'
                    GROUP BY kode_produk
            ) oman"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'oman.kode_produk');
                }
            )
            ->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('oman.edit', compact('produk', 'bulan', 'dataoman', 'm1', 'm2', 'm3', 'm4'));
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
                    DB::table('detail_oman')
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
        $oman = DB::table('oman')->where('no_order', $no_order)->first();
        $bulan = $oman->bulan;
        $tahun = $oman->tahun;
        DB::beginTransaction();
        try {
            DB::table('oman')->where('no_order', $no_order)->delete();
            DB::table('oman_cabang')->where('bulan', $bulan)->where('tahun', $tahun)->update(['status' => 0]);
            DB::commit();
            //die;
            return Redirect::back()->with(['success' => 'Data Berhasil Di Hapus ']);
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            return  Redirect::back()->with(['warning' => 'Data Gagal Di Hapus Hubungi Tim IT']);
        }
    }

    public function show(Request $request)
    {
        $no_order = $request->no_order;
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $dataoman = DB::table('oman')->where('no_order', $request->no_order)->first();
        $m1 = DB::table('detail_oman')->where('no_order', $no_order)->where('mingguke', 1)->first();
        $m2 = DB::table('detail_oman')->where('no_order', $no_order)->where('mingguke', 2)->first();
        $m3 = DB::table('detail_oman')->where('no_order', $no_order)->where('mingguke', 3)->first();
        $m4 = DB::table('detail_oman')->where('no_order', $no_order)->where('mingguke', 4)->first();
        $produk = DB::table('master_barang')
            ->select('master_barang.kode_produk', 'nama_barang', 'mingguke_1', 'mingguke_2', 'mingguke_3', 'mingguke_4')
            ->leftJoin(
                DB::raw("(
                    SELECT kode_produk,
                    SUM(IF(mingguke='1',jumlah,0)) as mingguke_1,
                    SUM(IF(mingguke='2',jumlah,0)) as mingguke_2,
                    SUM(IF(mingguke='3',jumlah,0)) as mingguke_3,
                    SUM(IF(mingguke='4',jumlah,0)) as mingguke_4
                    FROM detail_oman
                    WHERE no_order = '$no_order'
                    GROUP BY kode_produk
            ) oman"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'oman.kode_produk');
                }
            )
            ->get();
        return view('oman.show', compact('dataoman', 'produk', 'm1', 'm2', 'm3', 'm4', 'bulan'));
    }
}