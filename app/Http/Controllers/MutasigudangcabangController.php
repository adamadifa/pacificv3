<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MutasigudangcabangController extends Controller
{
    public function getsaldogudangcabang(Request $request)
    {
        $cabang = $request->kode_cabang;
        $status = $request->status;
        $gettanggal = DB::table('saldoawal_bj')->where('kode_cabang', $request->kode_cabang)->where('status', $request->status)
            ->orderBy('tanggal', 'desc')
            ->first();
        $tahun = $gettanggal->tahun;
        $bulan = $gettanggal->bulan;
        $hari  = "1";
        $tanggal = $tahun . "-" . $bulan . "-" . $hari;
        $saldo = DB::table('master_barang')
            ->selectRaw("master_barang.kode_produk,
        nama_barang,
        isipcsdus,
        isipack,
        isipcs,
        satuan,
        jumlah AS sabulanlalu,
        sisamutasi,
        buffer,
        totalpengembalian,totalpengambilan,
        IFNULL( jumlah, 0 ) + IFNULL( sisamutasi, 0 )  AS saldoakhir")
            ->leftJoin(
                DB::raw("(
                SELECT
                kode_produk,
                jumlah
                FROM
                    saldoawal_bj_detail
                    INNER JOIN saldoawal_bj ON saldoawal_bj_detail.kode_saldoawal = saldoawal_bj.kode_saldoawal
                WHERE
                    status = '$status'
                    AND kode_cabang = '$cabang' AND bulan = '$bulan' AND tahun ='$tahun'
                ) sa"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'sa.kode_produk');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT
                    kode_produk,
                    SUM( IF ( inout_good = 'IN', jumlah, 0 ) ) - SUM( IF ( inout_good = 'OUT', jumlah, 0 ) ) AS sisamutasi
                FROM
                    detail_mutasi_gudang_cabang
                    INNER JOIN mutasi_gudang_cabang ON detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang = mutasi_gudang_cabang.no_mutasi_gudang_cabang
                WHERE
                    tgl_mutasi_gudang_cabang BETWEEN '$tanggal' AND CURDATE()
                    AND kode_cabang = '$cabang'
                AND `jenis_mutasi` = 'SURAT JALAN'
                OR tgl_mutasi_gudang_cabang BETWEEN '$tanggal' AND CURDATE()
                    AND kode_cabang = '$cabang'
                AND `jenis_mutasi` = 'TRANSIT IN'
                OR tgl_mutasi_gudang_cabang BETWEEN '$tanggal' AND CURDATE()
                    AND kode_cabang = '$cabang'
                AND `jenis_mutasi` = 'TRANSIT OUT'
                OR tgl_mutasi_gudang_cabang BETWEEN '$tanggal' AND CURDATE()
                    AND kode_cabang = '$cabang'
                AND `jenis_mutasi` = 'REJECT GUDANG'
                OR tgl_mutasi_gudang_cabang BETWEEN '$tanggal' AND CURDATE()
                    AND kode_cabang = '$cabang'
                AND `jenis_mutasi` = 'REJECT PASAR'
                OR tgl_mutasi_gudang_cabang BETWEEN '$tanggal' AND CURDATE()
                    AND kode_cabang = '$cabang'
                AND `jenis_mutasi` = 'REPACK'
                OR tgl_mutasi_gudang_cabang BETWEEN '$tanggal' AND CURDATE()
                    AND kode_cabang = '$cabang'
                AND `jenis_mutasi` = 'PENYESUAIAN'
                GROUP BY detail_mutasi_gudang_cabang.kode_produk
                ) mutasi"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'mutasi.kode_produk');
                }
            )

            ->leftJoin(
                DB::raw("(
                SELECT kode_produk,jumlah as buffer
                FROM detail_bufferstok
                INNER JOIN buffer_stok ON detail_bufferstok.kode_bufferstok = buffer_stok.kode_bufferstok
                WHERE kode_cabang='$cabang'
                ) bf"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'bf.kode_produk');
                }
            )

            ->leftJoin(
                DB::raw("(
                SELECT
                    kode_produk,
                    SUM(jml_pengambilan) as totalpengambilan,
                SUM(jml_pengembalian) as totalpengembalian
                FROM
                    detail_dpb
                    INNER JOIN dpb ON detail_dpb.no_dpb = dpb.no_dpb
                WHERE
                tgl_pengambilan BETWEEN '$tanggal' AND CURDATE()
                    AND kode_cabang = '$cabang' GROUP BY kode_produk
                ) dpb"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'dpb.kode_produk');
                }
            )
            ->orderBy('master_barang.nama_barang')
            ->get();

        return view('mutasi_gudang_cabang.dashboard.saldogudangcabang', compact('saldo'));
    }

    public function getsaldogudangcabangbs(Request $request)
    {
        $cabang = $request->kode_cabang;
        $status = $request->status;
        $gettanggal = DB::table('saldoawal_bj')->where('kode_cabang', $request->kode_cabang)->where('status', $request->status)
            ->orderBy('tanggal', 'desc')
            ->first();
        $tahun = $gettanggal->tahun;
        $bulan = $gettanggal->bulan;
        $hari  = "1";
        $tanggal = $tahun . "-" . $bulan . "-" . $hari;
        $saldo = DB::table('master_barang')
            ->selectRaw("master_barang.kode_produk,
            nama_barang,
            isipcsdus,
            isipack,
            isipcs,
            satuan,
            jumlah AS sabulanlalu,
            sisamutasi,
            IFNULL( jumlah, 0 ) + IFNULL( sisamutasi, 0 ) AS saldoakhir")
            ->leftJoin(
                DB::raw("(
                SELECT
                kode_produk,
                jumlah
                FROM
                    saldoawal_bj_detail
                    INNER JOIN saldoawal_bj ON saldoawal_bj_detail.kode_saldoawal = saldoawal_bj.kode_saldoawal
                WHERE
                    status = '$status'
                    AND kode_cabang = '$cabang' AND bulan = '$bulan' AND tahun ='$tahun'
                ) sa"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'sa.kode_produk');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT kode_produk,
                SUM( IF ( inout_bad = 'IN', jumlah, 0 ) ) - SUM( IF ( inout_bad = 'OUT', jumlah, 0 ) ) AS sisamutasi
                FROM
                    detail_mutasi_gudang_cabang
                    INNER JOIN mutasi_gudang_cabang ON detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang = mutasi_gudang_cabang.no_mutasi_gudang_cabang
                WHERE
                    tgl_mutasi_gudang_cabang BETWEEN '$tanggal' AND CURDATE()
                    AND kode_cabang = '$cabang'
                GROUP BY detail_mutasi_gudang_cabang.kode_produk
                ) mutasi"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'mutasi.kode_produk');
                }
            )
            ->orderBy('master_barang.nama_barang')
            ->get();

        return view('mutasi_gudang_cabang.dashboard.saldogudangcabangbs', compact('saldo'));
    }
}
