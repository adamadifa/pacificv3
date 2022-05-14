<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Hargaawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HargaawalController extends Controller
{
    public function index()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('hargaawal.index', compact('bulan', 'cabang'));
    }

    public function show(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kode_cabang = $request->kode_cabang;

        $hargaawal = DB::table('master_barang')
            ->select('master_barang.kode_produk', 'nama_barang', 'harga_awal')
            ->leftJoin(
                DB::raw("(
                SELECT kode_produk, harga_awal
                FROM harga_awal
                WHERE bulan = '$bulan' AND tahun='$tahun' AND lokasi='$kode_cabang'
            ) hargaawal"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'hargaawal.kode_produk');
                }
            )
            ->orderBy('nama_barang')
            ->get();

        return view('hargaawal.show', compact('hargaawal'));
    }

    public function store(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kode_cabang = $request->kode_cabang;
        $kode_produk = $request->kode_produk;
        $hargaawal = $request->harga_awal;
        $cek = 0;
        for ($i = 0; $i < count($kode_produk); $i++) {
            $harga_awal = !empty($hargaawal[$i]) ? str_replace('.', '', $hargaawal[$i]) : 0;
            if (!empty($harga_awal)) {
                $detail[]   = [
                    'kode_produk' => $kode_produk[$i],
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'lokasi' => $kode_cabang,
                    'harga_awal' => $harga_awal
                ];

                $cek++;
            }
        }

        //dd(count($harga_produk));
        //dd($detail);
        DB::beginTransaction();
        try {
            DB::table('harga_awal')->where('bulan', $bulan)->where('tahun', $tahun)->delete();
            if (!empty($cek)) {
                $chunks = array_chunk($detail, 5);
                foreach ($chunks as $chunk) {
                    Hargaawal::insert($chunk);
                }
            }

            DB::commit();
            return redirect('/hargaawal?bulan=' . $bulan . '&tahun=' . $tahun . '&kode_cabang=' . $kode_cabang)->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('/hargaawal?bulan=' . $bulan . '&tahun=' . $tahun . '&kode_cabang=' . $kode_cabang)->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
        }
    }
}