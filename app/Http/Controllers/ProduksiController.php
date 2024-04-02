<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduksiController extends Controller
{
    public function analytics()
    {
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $hariini = explode("-", date("Y-m-d"));
        $bulanini = $hariini[1] + 0;
        $bulan = $namabulan[$bulanini];
        $tahun = date("Y");
        // $bulan = 12;
        // $tahun = 2021;

        $dari = $tahun . "-" . $bulanini . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $permintaan = DB::table('permintaan_produksi')
            ->selectRaw("permintaan_produksi.no_permintaan,tgl_permintaan,permintaan_produksi.no_order,bulan,tahun")
            ->join('oman', 'permintaan_produksi.no_order', '=', 'oman.no_order')
            ->where('bulan', $bulanini)
            ->where('tahun', $tahun)
            ->where('permintaan_produksi.status', 1)
            ->first();
        $detail = DB::table('detail_permintaan_produksi')
            ->selectRaw("detail_permintaan_produksi.kode_produk,nama_barang,oman_mkt,stok_gudang,buffer_stok,jmlrealisasi")
            ->join('master_barang', 'detail_permintaan_produksi.kode_produk', '=', 'master_barang.kode_produk')
            ->join('permintaan_produksi', 'detail_permintaan_produksi.no_permintaan', '=', 'permintaan_produksi.no_permintaan')
            ->join('oman', 'permintaan_produksi.no_order', '=', 'oman.no_order')
            ->leftJoin(
                DB::raw("(
                    SELECT kode_produk, SUM(jumlah) as jmlrealisasi FROM detail_mutasi_produksi
                    INNER JOIN mutasi_produksi  ON detail_mutasi_produksi.no_mutasi_produksi = mutasi_produksi.no_mutasi_produksi
                    WHERE jenis_mutasi = 'BPBJ'
                    AND tgl_mutasi_produksi BETWEEN '$dari' AND '$sampai'
                    GROUP BY kode_produk
                ) mutasiproduksi"),
                function ($join) {
                    $join->on('detail_permintaan_produksi.kode_produk', '=', 'mutasiproduksi.kode_produk');
                }
            )
            ->where('bulan', $bulanini)
            ->where('tahun', $tahun)
            ->get();

        //dd($detail);
        return view('produksi.analytics', compact('bulan', 'permintaan', 'detail', 'namabulan', 'tahun'));
    }

    public function loadrekapproduksi(Request $request)
    {
        $tahun = $request->tahun;
        $detail = DB::table('detail_mutasi_produksi')
            ->selectRaw("kode_produk,
            SUM(IF(MONTH(tgl_mutasi_produksi)=1 AND jenis_mutasi='BPBJ',jumlah,0)) as januari,
            SUM(IF(MONTH(tgl_mutasi_produksi)=2 AND jenis_mutasi='BPBJ',jumlah,0)) as februari,
            SUM(IF(MONTH(tgl_mutasi_produksi)=3 AND jenis_mutasi='BPBJ',jumlah,0)) as maret,
            SUM(IF(MONTH(tgl_mutasi_produksi)=4 AND jenis_mutasi='BPBJ',jumlah,0)) as april,
            SUM(IF(MONTH(tgl_mutasi_produksi)=5 AND jenis_mutasi='BPBJ',jumlah,0)) as mei,
            SUM(IF(MONTH(tgl_mutasi_produksi)=6 AND jenis_mutasi='BPBJ',jumlah,0)) as juni,
            SUM(IF(MONTH(tgl_mutasi_produksi)=7 AND jenis_mutasi='BPBJ',jumlah,0)) as juli,
            SUM(IF(MONTH(tgl_mutasi_produksi)=8 AND jenis_mutasi='BPBJ',jumlah,0)) as agustus,
            SUM(IF(MONTH(tgl_mutasi_produksi)=9 AND jenis_mutasi='BPBJ',jumlah,0)) as september,
            SUM(IF(MONTH(tgl_mutasi_produksi)=10 AND jenis_mutasi='BPBJ',jumlah,0)) as oktober,
            SUM(IF(MONTH(tgl_mutasi_produksi)=11 AND jenis_mutasi='BPBJ',jumlah,0)) as november,
            SUM(IF(MONTH(tgl_mutasi_produksi)=12 AND jenis_mutasi='BPBJ',jumlah,0)) as desember")
            ->join('mutasi_produksi', 'detail_mutasi_produksi.no_mutasi_produksi', '=', 'mutasi_produksi.no_mutasi_produksi')
            ->whereRaw('YEAR(tgl_mutasi_produksi) =' . $tahun)
            ->groupBy('kode_produk')
            ->orderBy('kode_produk')
            ->get();

        return view('produksi.loadrekapproduksi', compact('detail'));
    }

    public function loadgrafikproduksi(Request $request)
    {
        $tahun = $request->tahun;
        $detail = DB::table('detail_mutasi_produksi')
            ->selectRaw("kode_produk,
            SUM(IF(MONTH(tgl_mutasi_produksi)=1 AND jenis_mutasi='BPBJ',jumlah,0)) as januari,
            SUM(IF(MONTH(tgl_mutasi_produksi)=2 AND jenis_mutasi='BPBJ',jumlah,0)) as februari,
            SUM(IF(MONTH(tgl_mutasi_produksi)=3 AND jenis_mutasi='BPBJ',jumlah,0)) as maret,
            SUM(IF(MONTH(tgl_mutasi_produksi)=4 AND jenis_mutasi='BPBJ',jumlah,0)) as april,
            SUM(IF(MONTH(tgl_mutasi_produksi)=5 AND jenis_mutasi='BPBJ',jumlah,0)) as mei,
            SUM(IF(MONTH(tgl_mutasi_produksi)=6 AND jenis_mutasi='BPBJ',jumlah,0)) as juni,
            SUM(IF(MONTH(tgl_mutasi_produksi)=7 AND jenis_mutasi='BPBJ',jumlah,0)) as juli,
            SUM(IF(MONTH(tgl_mutasi_produksi)=8 AND jenis_mutasi='BPBJ',jumlah,0)) as agustus,
            SUM(IF(MONTH(tgl_mutasi_produksi)=9 AND jenis_mutasi='BPBJ',jumlah,0)) as september,
            SUM(IF(MONTH(tgl_mutasi_produksi)=10 AND jenis_mutasi='BPBJ',jumlah,0)) as oktober,
            SUM(IF(MONTH(tgl_mutasi_produksi)=11 AND jenis_mutasi='BPBJ',jumlah,0)) as november,
            SUM(IF(MONTH(tgl_mutasi_produksi)=12 AND jenis_mutasi='BPBJ',jumlah,0)) as desember")
            ->join('mutasi_produksi', 'detail_mutasi_produksi.no_mutasi_produksi', '=', 'mutasi_produksi.no_mutasi_produksi')
            ->whereRaw('YEAR(tgl_mutasi_produksi) =' . $tahun)
            ->groupBy('kode_produk')
            ->orderBy('kode_produk')
            ->get();

        return view('produksi.loadgrafikproduksi', compact('detail'));
    }
}
