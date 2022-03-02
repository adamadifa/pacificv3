<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Cabang;
use App\Models\Omancabang;
use Illuminate\Http\Request;
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

        $oman_cabang = $query->paginate(15);
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
}
