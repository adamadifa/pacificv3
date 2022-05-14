<?php

namespace App\Http\Controllers;

use App\Models\Hargahpp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class HppController extends Controller
{
    public function index()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('hpp.index', compact('bulan'));
    }

    public function show(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $hpp = DB::table('master_barang')
            ->select('master_barang.kode_produk', 'nama_barang', 'harga_hpp')
            ->leftJoin(
                DB::raw("(
                SELECT kode_produk, harga_hpp
                FROM harga_hpp
                WHERE bulan = '$bulan' AND tahun='$tahun'
            ) hpp"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'hpp.kode_produk');
                }
            )
            ->orderBy('nama_barang')
            ->get();

        return view('hpp.show', compact('hpp'));
    }

    public function store(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kode_produk = $request->kode_produk;
        $harga_produk = $request->harga_hpp;
        $cekhpp = 0;
        for ($i = 0; $i < count($kode_produk); $i++) {
            $harga_hpp = !empty($harga_produk[$i]) ? str_replace('.', '', $harga_produk[$i]) : 0;
            if (!empty($harga_hpp)) {
                $detail[]   = [
                    'kode_produk' => $kode_produk[$i],
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'harga_hpp' => $harga_hpp
                ];

                $cekhpp++;
            }
        }

        //dd(count($harga_produk));

        DB::beginTransaction();
        try {
            DB::table('harga_hpp')->where('bulan', $bulan)->where('tahun', $tahun)->delete();
            if (!empty($cekhpp)) {
                $chunks = array_chunk($detail, 5);
                foreach ($chunks as $chunk) {
                    Hargahpp::insert($chunk);
                }
            }

            DB::commit();
            return redirect('/hpp?bulan=' . $bulan . '&tahun=' . $tahun)->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('/hpp?bulan=' . $bulan . '&tahun=' . $tahun)->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
        }
    }
}