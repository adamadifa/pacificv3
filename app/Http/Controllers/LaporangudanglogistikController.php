<?php

namespace App\Http\Controllers;

use App\Models\Barangpembelian;
use App\Models\Cabang;
use App\Models\Detailpemasukangudanglogistik;
use App\Models\Detailpengeluarangudanglogistik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporangudanglogistikController extends Controller
{
    public function pemasukan()
    {
        $kategori = DB::table('kategori_barang_pembelian')->get();
        return view('gudanglogistik.laporan.frm.lap_pemasukan', compact('kategori'));
    }

    public function cetak_pemasukan(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $kode_kategori = $request->kode_kategori;
        $kode_barang = $request->kode_barang;

        $query = Detailpemasukangudanglogistik::query();
        $query->selectRaw(" gk.nama_supplier,
        detail_pemasukan.penyesuaian,
        pemasukan.tgl_pemasukan,
        master_barang_pembelian.kode_barang,
        satuan,keterangan,nama_barang,
        kategori,nama_akun,coa.kode_akun,
        harga,qty,pemasukan.nobukti_pemasukan");
        $query->join('pemasukan', 'detail_pemasukan.nobukti_pemasukan', '=', 'pemasukan.nobukti_pemasukan');
        $query->join('master_barang_pembelian', 'detail_pemasukan.kode_barang', '=', 'master_barang_pembelian.kode_barang');
        $query->join('kategori_barang_pembelian', 'master_barang_pembelian.kode_kategori', '=', 'kategori_barang_pembelian.kode_kategori');
        $query->join('coa', 'detail_pemasukan.kode_akun', '=', 'coa.kode_akun');
        $query->leftJoin(
            DB::raw("(
                        SELECT pembelian.nobukti_pembelian,nama_supplier FROM pembelian
                        INNER JOIN supplier ON pembelian.kode_supplier = supplier.kode_supplier
                        GROUP BY nobukti_pembelian,nama_supplier
                    ) gk"),
            function ($join) {
                $join->on('pemasukan.nobukti_pemasukan', '=', 'gk.nobukti_pembelian');
            }
        );

        $query->whereBetween('pemasukan.tgl_pemasukan', [$dari, $sampai]);
        $query->where('master_barang_pembelian.status', 'Aktif');
        $query->where('master_barang_pembelian.kode_dept', 'GDL');
        if (!empty($request->kode_kategori)) {
            $query->where('master_barang_pembelian.kode_kategori', $kode_kategori);
        }

        if (!empty($request->kode_barang)) {
            $query->where('detail_pemasukan.kode_barang', $kode_barang);
        }
        $query->orderBy('pemasukan.tgl_pemasukan');
        $query->orderBy('detail_pemasukan.kode_barang');
        $query->orderBy('pemasukan.nobukti_pemasukan');
        $pemasukan = $query->get();

        $kategori = DB::table('kategori_barang_pembelian')->where('kode_kategori', $kode_kategori)->first();
        $barang = DB::table('master_barang_pembelian')->where('kode_barang', $kode_barang)->first();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekap Pemasukan.xls");
        }
        return view('gudanglogistik.laporan.cetak_pemasukan', compact('dari', 'sampai', 'pemasukan', 'kategori', 'barang'));
    }

    public function pengeluaran()
    {
        $departemen = DB::table('departemen')->where('status_pengajuan', '!=', 2)->get();
        $kategori = DB::table('kategori_barang_pembelian')->get();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('gudanglogistik.laporan.frm.lap_pengeluaran', compact('kategori', 'cabang', 'departemen'));
    }

    public function cetak_pengeluaran(Request $request)
    {
        $kode_dept = $request->kode_dept;
        $kode_cabang = $request->kode_cabang;
        $kode_kategori = $request->kode_kategori;
        $kode_barang = $request->kode_barang;
        $dari = $request->dari;
        $sampai = $request->sampai;

        $query = Detailpengeluarangudanglogistik::query();
        $query->selectRaw("tgl_pengeluaran,detail_pengeluaran.*,nama_dept,nama_barang,satuan,nama_cabang");
        $query->join('pengeluaran', 'detail_pengeluaran.nobukti_pengeluaran', '=', 'pengeluaran.nobukti_pengeluaran');
        $query->leftJoin('cabang', 'detail_pengeluaran.kode_cabang', '=', 'cabang.kode_cabang');
        $query->leftJoin('departemen', 'pengeluaran.kode_dept', '=', 'departemen.kode_dept');
        $query->leftJoin('master_barang_pembelian', 'detail_pengeluaran.kode_barang', '=', 'master_barang_pembelian.kode_barang');
        $query->leftJoin('kategori_barang_pembelian', 'master_barang_pembelian.kode_kategori', '=', 'kategori_barang_pembelian.kode_kategori');
        $query->whereBetween('tgl_pengeluaran', [$dari, $sampai]);
        $query->where('master_barang_pembelian.status', 'Aktif');
        $query->where('master_barang_pembelian.kode_dept', "GDL");
        if (!empty($kode_dept)) {
            $query->where('pengeluaran.kode_dept', $kode_dept);
            $query->where('detail_pengeluaran.kode_cabang', '');
            $query->orWhere('pengeluaran.kode_dept', $kode_dept);
            $query->whereNull('detail_pengeluaran.kode_cabang');
        }

        if (!empty($kode_cabang)) {
            $query->where('detail_pengeluaran.kode_cabang', $kode_cabang);
        }

        if (!empty($kode_kategori)) {
            $query->where('master_barang_pembelian.kode_kategori', $kode_kategori);
        }

        if (!empty($kode_barang)) {
            $query->where('detail_pengeluaran.kode_barang', $kode_barang);
        }

        $query->orderBy('tgl_pengeluaran');
        $query->orderBy('pengeluaran.nobukti_pengeluaran');
        $pengeluaran = $query->get();


        $departemen = DB::table('departemen')->where('kode_dept', $kode_dept)->first();
        $cabang = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
        $kategori = DB::table('kategori_barang_pembelian')->where('kode_kategori', $kode_kategori)->first();
        $barang = DB::table('master_barang_pembelian')->where('kode_barang', $kode_barang)->first();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Pengeluaran.xls");
        }
        return view('gudanglogistik.laporan.cetak_pengeluaran', compact('departemen', 'cabang', 'kategori', 'barang', 'pengeluaran', 'dari', 'sampai'));
    }

    public function persediaan()
    {
        $kategori = DB::table('kategori_barang_pembelian')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('gudanglogistik.laporan.frm.lap_persediaan', compact('bulan', 'kategori'));
    }

    public function cetak_persediaan(Request $request)
    {
        $kode_kategori = $request->kode_kategori;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $query  = Barangpembelian::query();
        $query->selectRaw("master_barang_pembelian.kode_barang,
        master_barang_pembelian.nama_barang,
        kategori_barang_pembelian.kode_kategori,
        kategori_barang_pembelian.kategori,
        master_barang_pembelian.satuan,
        sa.qtysaldoawal,
        sa.totalsa,
        sa.hargasaldoawal,
        gm.totalpemasukan,
        gm.penyesuaian,
        gm.qtypemasukan,
        gm.hargapemasukan,
        op.qtyopname,
        gk.qtypengeluaran");
        $query->join('kategori_barang_pembelian', 'master_barang_pembelian.kode_kategori', '=', 'kategori_barang_pembelian.kode_kategori');
        $query->leftJoin(
            DB::raw("(
                SELECT saldoawal_gl_detail.kode_barang,SUM(saldoawal_gl_detail.harga) AS hargasaldoawal,SUM( qty ) AS qtysaldoawal,SUM(saldoawal_gl_detail.harga*qty) AS
                totalsa FROM saldoawal_gl_detail
                INNER JOIN saldoawal_gl ON saldoawal_gl.kode_saldoawal_gl=saldoawal_gl_detail.kode_saldoawal_gl
                WHERE bulan = '$bulan' AND tahun = '$tahun'
                GROUP BY saldoawal_gl_detail.kode_barang
            ) sa"),
            function ($join) {
                $join->on('master_barang_pembelian.kode_barang', '=', 'sa.kode_barang');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT opname_gl_detail.kode_barang,SUM( qty ) AS qtyopname FROM opname_gl_detail
                INNER JOIN opname_gl ON opname_gl.kode_opname_gl=opname_gl_detail.kode_opname_gl
                WHERE bulan = '$bulan' AND tahun = '$tahun'
                GROUP BY opname_gl_detail.kode_barang
            ) op"),
            function ($join) {
                $join->on('master_barang_pembelian.kode_barang', '=', 'op.kode_barang');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT detail_pemasukan.kode_barangd,SUM( penyesuaian ) AS penyesuaian,SUM( qty ) AS qtypemasukan,SUM( harga ) AS hargapemasukan,SUM(detail_pemasukan.harga * qty) AS totalpemasukan FROM
                detail_pemasukan
                INNER JOIN pemasukan ON detail_pemasukan.nobukti_pemasukan = pemasukan.nobukti_pemasukan
                WHERE MONTH(tgl_pemasukan) = '$bulan' AND YEAR(tgl_pemasukan) = '$tahun'
                GROUP BY detail_pemasukan.kode_barang
            ) gm"),
            function ($join) {
                $join->on('master_barang_pembelian.kode_barang', '=', 'gm.kode_barang');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT detail_pengeluaran.kode_barang,SUM( qty ) AS qtypengeluaran FROM detail_pengeluaran
                INNER JOIN pengeluaran ON detail_pengeluaran.nobukti_pengeluaran = pengeluaran.nobukti_pengeluaran
                WHERE MONTH(tgl_pengeluaran) = '$bulan' AND YEAR(tgl_pengeluaran) = '$tahun'
                GROUP BY detail_pengeluaran.kode_barang
            ) gk"),
            function ($join) {
                $join->on('master_barang_pembelian.kode_barang', '=', 'gk.kode_barang');
            }
        );

        $query->where('master_barang_pembelian.kode_dept', 'GDL');
        $query->where('master_barang_pembelian.status', 'Aktif');
        if (!empty($kode_kategori)) {
            $query->where('master_barang_pembelian.kode_kategori', $kode_kategori);
        }

        $query->orderBy('nama_barang');
        $persediaan = $query->get();
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $kat = DB::table('kategori_barang_pembelian')->where('kode_kategori', $kode_kategori)->first();
        $kategori = $kode_kategori;
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Persediaan.xls");
        }
        return view('gudanglogistik.laporan.cetak_persediaan', compact('persediaan', 'kategori', 'bulan', 'tahun', 'namabulan', 'kategori', 'kat'));
    }

    public function persediaanopname()
    {
        $kategori = DB::table('kategori_barang_pembelian')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('gudanglogistik.laporan.frm.lap_persediaanopname', compact('bulan', 'kategori'));
    }

    public function cetak_persediaanopname(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kode_kategori = $request->kode_kategori;
        $kategori = $kode_kategori;
        $kat = DB::table('kategori_barang_pembelian')->where('kode_kategori', $kode_kategori)->first();

        $query  = Barangpembelian::query();
        $query->selectRaw("master_barang_pembelian.kode_barang,
        master_barang_pembelian.nama_barang,
        kategori_barang_pembelian.kode_kategori,
        kategori_barang_pembelian.kategori,
        master_barang_pembelian.satuan,
        sa.qtysaldoawal,
        sa.totalsa,
        sa.hargasaldoawal,
        gm.totalpemasukan,
        gm.penyesuaian,
        gm.qtypemasukan,
        gm.hargapemasukan,
        op.qtyopname,
        gk.qtypengeluaran");
        $query->join('kategori_barang_pembelian', 'master_barang_pembelian.kode_kategori', '=', 'kategori_barang_pembelian.kode_kategori');
        $query->leftJoin(
            DB::raw("(
                SELECT saldoawal_gl_detail.kode_barang,SUM(saldoawal_gl_detail.harga) AS hargasaldoawal,SUM( qty ) AS qtysaldoawal,SUM(saldoawal_gl_detail.harga*qty) AS
                totalsa FROM saldoawal_gl_detail
                INNER JOIN saldoawal_gl ON saldoawal_gl.kode_saldoawal_gl=saldoawal_gl_detail.kode_saldoawal_gl
                WHERE bulan = '$bulan' AND tahun = '$tahun'
                GROUP BY saldoawal_gl_detail.kode_barang
            ) sa"),
            function ($join) {
                $join->on('master_barang_pembelian.kode_barang', '=', 'sa.kode_barang');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT opname_gl_detail.kode_barang,SUM( qty ) AS qtyopname FROM opname_gl_detail
                INNER JOIN opname_gl ON opname_gl.kode_opname_gl=opname_gl_detail.kode_opname_gl
                WHERE bulan = '$bulan' AND tahun = '$tahun'
                GROUP BY opname_gl_detail.kode_barang
            ) op"),
            function ($join) {
                $join->on('master_barang_pembelian.kode_barang', '=', 'op.kode_barang');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT detail_pemasukan.kode_barang,SUM( penyesuaian ) AS penyesuaian,SUM( qty ) AS qtypemasukan,SUM( harga ) AS hargapemasukan,SUM(detail_pemasukan.harga * qty) AS totalpemasukan FROM
                detail_pemasukan
                INNER JOIN pemasukan ON detail_pemasukan.nobukti_pemasukan = pemasukan.nobukti_pemasukan
                WHERE MONTH(tgl_pemasukan) = '$bulan' AND YEAR(tgl_pemasukan) = '$tahun'
                GROUP BY detail_pemasukan.kode_barang
            ) gm"),
            function ($join) {
                $join->on('master_barang_pembelian.kode_barang', '=', 'gm.kode_barang');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT detail_pengeluaran.kode_barang,SUM( qty ) AS qtypengeluaran FROM detail_pengeluaran
                INNER JOIN pengeluaran ON detail_pengeluaran.nobukti_pengeluaran = pengeluaran.nobukti_pengeluaran
                WHERE MONTH(tgl_pengeluaran) = '$bulan' AND YEAR(tgl_pengeluaran) = '$tahun'
                GROUP BY detail_pengeluaran.kode_barang
            ) gk"),
            function ($join) {
                $join->on('master_barang_pembelian.kode_barang', '=', 'gk.kode_barang');
            }
        );

        $query->where('master_barang_pembelian.kode_dept', 'GDL');
        $query->where('master_barang_pembelian.status', 'Aktif');
        if (!empty($kode_kategori)) {
            $query->where('master_barang_pembelian.kode_kategori', $kode_kategori);
        }
        $a = 0;
        $query->orderBy('nama_barang');
        $persediaan = $query->get();
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Opname.xls");
        }
        return view('gudanglogistik.laporan.cetak_persediaanopname', compact('persediaan', 'kategori', 'bulan', 'tahun', 'namabulan', 'kategori', 'kat'));
    }
}
