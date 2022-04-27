<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Detailpemasukanproduksi;
use App\Models\Detailpengeluarnproduksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanproduksiController extends Controller
{
    public function mutasiproduksi()
    {
        $barang = Barang::orderBy('kode_produk')->get();
        return view('produksi.laporan.frm.lap_mutasiproduksi', compact('barang'));
    }

    public function cetak_mutasiproduksi(Request $request)
    {
        $kode_produk = $request->kode_produk;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $saldoawal = DB::table('detail_mutasi_produksi')
            ->selectRaw("SUM(IF( `inout` = 'IN', jumlah, 0)) AS jml_in,
            SUM(IF( `inout` = 'OUT', jumlah, 0)) AS jml_out,
            SUM(IF( `inout` = 'IN', jumlah, 0)) -SUM(IF( `inout` = 'OUT', jumlah, 0)) as saldo_awal")
            ->join('mutasi_produksi', 'detail_mutasi_produksi.no_mutasi_produksi', '=', 'mutasi_produksi.no_mutasi_produksi')
            ->where('tgl_mutasi_produksi', '<', $dari)
            ->where('kode_produk', $kode_produk)
            ->first();
        $mutasi = DB::table('detail_mutasi_produksi')
            ->join('mutasi_produksi', 'detail_mutasi_produksi.no_mutasi_produksi', '=', 'mutasi_produksi.no_mutasi_produksi')
            ->whereBetween('tgl_mutasi_produksi', [$dari, $sampai])
            ->where('detail_mutasi_produksi.kode_produk', $kode_produk)
            ->orderBy('tgl_mutasi_produksi')
            ->orderBy('inout')
            ->orderBy('shift')
            ->get();

        $produk = DB::table('master_barang')->where('kode_produk', $kode_produk)->first();
        return view('produksi.laporan.cetak_mutasiproduksi', compact('produk', 'saldoawal', 'mutasi', 'dari', 'sampai'));
    }

    public function rekapmutasiproduksi()
    {
        return view('produksi.laporan.frm.lap_rekapmutasiproduksi');
    }

    public function cetak_rekapmutasiproduksi(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $mutasi = DB::table('master_barang')
            ->selectRaw("
            master_barang.kode_produk,
            nama_barang,
            IFNULL(saldoawal,0) as saldoawal,
            IFNULL(jmlbpbj,0) as jmlbpbj,
            IFNULL(mutasi_in,0) as mutasi_in,
            IFNULL(jmlfsthp,0) as jmlfsthp,
            IFNULL(mutasi_out,0) as mutasi_out
            ")
            ->leftJoin(
                DB::raw("(
                SELECT
                kode_produk,
                IFNULL(SUM( IF ( `inout` = 'IN', jumlah, 0 ) ) - SUM( IF ( `inout` = 'OUT', jumlah, 0 ) ),0 )  as saldoawal
                FROM
                detail_mutasi_produksi d
                INNER JOIN mutasi_produksi ON d.no_mutasi_produksi = mutasi_produksi.no_mutasi_produksi
                WHERE tgl_mutasi_produksi < '$dari'
                GROUP BY kode_produk
            ) sa"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'sa.kode_produk');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT kode_produk,
			SUM(IF( jenis_mutasi = 'BPBJ', jumlah, 0 )) as jmlbpbj,
			SUM(IF( jenis_mutasi = 'FSTHP', jumlah, 0 )) as jmlfsthp,
			SUM(IF( jenis_mutasi = 'LAIN-LAIN' AND `inout` = 'IN', jumlah, 0 )) as mutasi_in,
			SUM(IF( jenis_mutasi = 'LAIN-LAIN' AND `inout` = 'OUT', jumlah, 0 )) as mutasi_out
            FROM
                detail_mutasi_produksi d
            INNER JOIN
                mutasi_produksi ON d.no_mutasi_produksi = mutasi_produksi.no_mutasi_produksi
            WHERE
                tgl_mutasi_produksi BETWEEN '$dari' AND '$sampai'
                GROUP BY kode_produk
            ) dm"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'dm.kode_produk');
                }
            )->get();

        return view('produksi.laporan.cetak_rekapmutasiproduksi', compact('dari', 'sampai', 'mutasi'));
    }

    public function pemasukanproduksi()
    {
        $barang = DB::table('master_barang_produksi')->orderBy('kode_barang')->get();
        return view('produksi.laporan.frm.lap_pemasukanproduksi', compact('barang'));
    }

    public function cetak_pemasukanproduksi(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $kode_barang = $request->kode_barang;
        $query = Detailpemasukanproduksi::query();
        $query->selectRaw('detail_pemasukan_gp.*,pemasukan_gp.kode_dept,tgl_pemasukan,nama_barang,satuan');
        $query->join('pemasukan_gp', 'detail_pemasukan_gp.nobukti_pemasukan', '=', 'pemasukan_gp.nobukti_pemasukan');
        $query->join('master_barang_produksi', 'detail_pemasukan_gp.kode_barang', '=', 'master_barang_produksi.kode_barang');
        $query->whereBetween('tgl_pemasukan', [$dari, $sampai]);
        if (!empty($kode_barang)) {
            $query->where('detail_pemasukan_gp.kode_barang', $kode_barang);
        }
        $query->orderBy('tgl_pemasukan');
        $query->orderBy('detail_pemasukan_gp.kode_barang');
        $query->orderBy('pemasukan_gp.nobukti_pemasukan');
        $pemasukan = $query->get();

        return view('produksi.laporan.cetak_pemasukanproduksi', compact('pemasukan', 'dari', 'sampai'));
    }

    public function pengeluaranproduksi()
    {
        $barang = DB::table('master_barang_produksi')->orderBy('kode_barang')->get();
        return view('produksi.laporan.frm.lap_pengeluaranproduksi', compact('barang'));
    }

    public function cetak_pengeluaranproduksi(Request $request)
    {
        $jenis_pengeluaran = $request->jenis_pengeluaran;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $kode_barang = $request->kode_barang;

        $query = Detailpengeluarnproduksi::query();
        $query->selectRaw("detail_pengeluaran_gp.*,pengeluaran_gp.kode_dept,tgl_pengeluaran,nama_barang,satuan");
        $query->join('pengeluaran_gp', 'detail_pengeluaran_gp.nobukti_pengeluaran', '=', 'pengeluaran_gp.nobukti_pengeluaran');
        $query->join('master_barang_produksi', 'detail_pengeluaran_gp.kode_barang', '=', 'master_barang_produksi.kode_barang');
        $query->whereBetween('tgl_pengeluaran', [$dari, $sampai]);
        if (!empty($kode_barang)) {
            $query->where('detail_pengeluaran_gp.kode_barang', $kode_barang);
        }
        if (!empty($jenis_pengeluaran)) {
            $query->where('pengeluaran_gp.kode_dept', $jenis_pengeluaran);
        }
        $query->orderBy('tgl_pengeluaran');
        $query->orderBy('detail_pengeluaran_gp.kode_barang');
        $query->orderBy('pengeluaran_gp.nobukti_pengeluaran');
        $pengeluaran = $query->get();
        return view('produksi.laporan.cetak_pengeluaranproduksi', compact('pengeluaran', 'dari', 'sampai'));
    }

    public function rekappersediaanbarangproduksi()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('produksi.laporan.frm.lap_rekappersediaanbarangproduksi', compact('bulan'));
    }

    public function cetak_rekappersediaanbarangproduksi(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $rekap = DB::table('master_barang_produksi')
            ->selectRaw("master_barang_produksi.kode_barang,
            master_barang_produksi.nama_barang,
            master_barang_produksi.satuan,
            master_barang_produksi.kode_kategori,
            sa.saldoawal,
            op.opname,
            gm.gudang,
            gm.seasoning,
            gm.trial,
            gk.pemakaian,
            gk.retur,
            gk.lainnya")
            ->leftJoin(
                DB::raw("(
                SELECT saldoawal_gp_detail.kode_barang,SUM( qty ) AS saldoawal FROM saldoawal_gp_detail
                INNER JOIN saldoawal_gp ON saldoawal_gp.kode_saldoawal=saldoawal_gp_detail.kode_saldoawal
                WHERE bulan = '$bulan' AND tahun = '$tahun' GROUP BY saldoawal_gp_detail.kode_barang
            ) sa"),
                function ($join) {
                    $join->on('master_barang_produksi.kode_barang', '=', 'sa.kode_barang');
                }
            )

            ->leftJoin(
                DB::raw("(
                SELECT opname_gp_detail.kode_barang,SUM( qty ) AS opname FROM opname_gp_detail
                INNER JOIN opname_gp ON opname_gp.kode_opname=opname_gp_detail.kode_opname
                WHERE bulan = '$bulan' AND tahun = '$tahun' GROUP BY opname_gp_detail.kode_barang
            ) op"),
                function ($join) {
                    $join->on('master_barang_produksi.kode_barang', '=', 'op.kode_barang');
                }
            )

            ->leftJoin(
                DB::raw("(
                SELECT
                detail_pemasukan_gp.kode_barang,
                SUM( IF( kode_dept = 'Gudang' , qty ,0 )) AS gudang,
                SUM( IF( kode_dept = 'Seasoning' , qty ,0 )) AS seasoning,
                SUM( IF( kode_dept = 'Trial' , qty ,0 )) AS trial
                FROM
                detail_pemasukan_gp
                INNER JOIN pemasukan_gp ON detail_pemasukan_gp.nobukti_pemasukan = pemasukan_gp.nobukti_pemasukan
                WHERE MONTH(tgl_pemasukan) = '$bulan' AND YEAR(tgl_pemasukan) = '$tahun'
                GROUP BY detail_pemasukan_gp.kode_barang
            ) gm"),
                function ($join) {
                    $join->on('master_barang_produksi.kode_barang', '=', 'gm.kode_barang');
                }
            )

            ->leftJoin(
                DB::raw("(
                SELECT
                detail_pengeluaran_gp.kode_barang,
                SUM( IF( kode_dept = 'Pemakaian' , qty ,0 )) AS pemakaian,
                SUM( IF( kode_dept = 'Retur Out' , qty ,0 )) AS retur,
                SUM( IF( kode_dept = 'Lainnya' , qty ,0 )) AS lainnya
                FROM detail_pengeluaran_gp
                INNER JOIN pengeluaran_gp ON detail_pengeluaran_gp.nobukti_pengeluaran = pengeluaran_gp.nobukti_pengeluaran
                WHERE MONTH(tgl_pengeluaran) = '$bulan' AND YEAR(tgl_pengeluaran) = '$tahun'
                GROUP BY detail_pengeluaran_gp.kode_barang
            ) gk"),
                function ($join) {
                    $join->on('master_barang_produksi.kode_barang', '=', 'gk.kode_barang');
                }
            )
            ->orderBy('kode_kategori')
            ->orderBy('nama_barang')
            ->get();
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('produksi.laporan.cetak_rekappersediaanbarangproduksi', compact('bulan', 'tahun', 'rekap', 'namabulan'));
    }
}
