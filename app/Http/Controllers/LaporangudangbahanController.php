<?php

namespace App\Http\Controllers;

use App\Models\Barangpembelian;
use App\Models\Detailpemasukangudangbahan;
use App\Models\Detailpengeluarangudangbahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporangudangbahanController extends Controller
{
    public function pemasukan()
    {
        $barang = DB::table('master_barang_pembelian')->where('kode_dept', 'GDB')->orderBy('nama_barang')->get();
        return view('gudangbahan.laporan.frm.lap_pemasukan', compact('barang'));
    }

    public function cetak_pemasukan(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $kode_barang = $request->kode_barang;

        $query = Detailpemasukangudangbahan::query();
        $query->select('detail_pemasukan_gb.*', 'tgl_pemasukan', 'nama_barang');
        $query->join('pemasukan_gb', 'detail_pemasukan_gb.nobukti_pemasukan', '=', 'pemasukan_gb.nobukti_pemasukan');
        $query->join('master_barang_pembelian', 'detail_pemasukan_gb.kode_barang', '=', 'master_barang_pembelian.kode_barang');
        $query->whereBetween('tgl_pemasukan', [$dari, $sampai]);
        if (!empty($kode_barang)) {
            $query->where('detail_pemasukan_gb.kode_barang', $kode_barang);
        }

        //$query->orderBy('nama_barang');
        $query->orderBy('tgl_pemasukan');
        $query->orderBy('detail_pemasukan_gb.kode_barang');
        $query->orderBy('detail_pemasukan_gb.nobukti_pemasukan');
        $pemasukan = $query->get();
        $barang = Barangpembelian::where('kode_barang', $kode_barang)->first();
        return view('gudangbahan.laporan.cetak_pemasukan', compact('dari', 'sampai', 'pemasukan', 'barang'));
    }

    public function pengeluaran()
    {
        $barang = DB::table('master_barang_pembelian')->where('kode_dept', 'GDB')->orderBy('nama_barang')->get();
        return view('gudangbahan.laporan.frm.lap_pengeluaran', compact('barang'));
    }

    public function cetak_pengeluaran(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $kode_dept = $request->kode_dept;
        $kode_barang = $request->kode_barang;
        $unit = $request->unit;

        $barang = Barangpembelian::where('kode_barang', $kode_barang)->first();
        $query = Detailpengeluarangudangbahan::query();
        $query->select('detail_pengeluaran_gb.*', 'tgl_pengeluaran', 'nama_barang', 'satuan', 'pengeluaran_gb.kode_dept', 'unit');
        $query->join('pengeluaran_gb', 'detail_pengeluaran_gb.nobukti_pengeluaran', '=', 'pengeluaran_gb.nobukti_pengeluaran');
        $query->join('master_barang_pembelian', 'detail_pengeluaran_gb.kode_barang', '=', 'master_barang_pembelian.kode_barang');

        $query->whereBetween('tgl_pengeluaran', [$dari, $sampai]);

        if (!empty($kode_dept)) {
            $query->where('pengeluaran_gb.kode_dept', $kode_dept);
        }

        if (!empty($unit)) {
            $query->where('pengeluaran_gb.unit', $unit);
        }

        if (!empty($kode_barang)) {
            $query->where('detail_pengeluaran_gb.kode_barang', $kode_barang);
        }

        $query->orderBy('tgl_pengeluaran');
        $query->orderBy('detail_pengeluaran_gb.nobukti_pengeluaran');
        $pengeluaran = $query->get();

        $barang = Barangpembelian::where('kode_barang', $kode_barang)->first();

        return view('gudangbahan.laporan.cetak_pengeluaran', compact('dari', 'sampai', 'kode_dept', 'pengeluaran', 'barang'));
    }

    public function persediaan()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('gudangbahan.laporan.frm.lap_persediaan', compact('bulan'));
    }

    public function cetak_persediaan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kode_kategori = $request->kode_kategori;
        $persediaan = DB::table('master_barang_pembelian')
            ->selectRaw("master_barang_pembelian.kode_barang,
            master_barang_pembelian.nama_barang,
            master_barang_pembelian.satuan,
            master_barang_pembelian.jenis_barang,
            sa.qtyunitsa,
            sa.qtyberatsa,
            op.qtyunitop,
            op.qtyberatop,
            gm.qtypemb1,
            gm.qtylainnya1,
            gm.qtypemb2,
            gm.qtylainnya2,
            gm.qtypengganti2,
            gm.qtypengganti1,
            gk.qtyprod3,
            gk.qtyseas3,
            gk.qtypdqc3,
            gk.qtysus3,
            gk.qtycabang3,
            gk.qtylain3,
            gk.qtyprod4,
            gk.qtyseas4,
            gk.qtypdqc4,
            gk.qtysus4,
            gk.qtycabang4,
            gk.qtylain4,
            hrgsa.harga,
            dp.totalharga")
            ->leftJoin(
                DB::raw("(
                SELECT saldoawal_gb_detail.kode_barang,
                SUM( qty_unit ) AS qtyunitsa,
                SUM( qty_berat ) AS qtyberatsa
                FROM saldoawal_gb_detail
                INNER JOIN saldoawal_gb ON saldoawal_gb.kode_saldoawal_gb=saldoawal_gb_detail.kode_saldoawal_gb
                WHERE bulan = '$bulan' AND tahun = '$tahun' GROUP BY saldoawal_gb_detail.kode_barang
            ) sa"),
                function ($join) {
                    $join->on('master_barang_pembelian.kode_barang', '=', 'sa.kode_barang');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT opname_gb_detail.kode_barang,
                SUM( qty_unit ) AS qtyunitop,
                SUM( qty_berat ) AS qtyberatop
                FROM opname_gb_detail
                INNER JOIN opname_gb ON opname_gb.kode_opname_gb=opname_gb_detail.kode_opname_gb
                WHERE bulan = '$bulan' AND tahun = '$tahun' GROUP BY opname_gb_detail.kode_barang
            ) op"),
                function ($join) {
                    $join->on('master_barang_pembelian.kode_barang', '=', 'op.kode_barang');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT SUM((qty*harga)+penyesuaian) as totalharga,kode_barang
                FROM detail_pembelian
                INNER JOIN pembelian ON detail_pembelian.nobukti_pembelian = pembelian.nobukti_pembelian
                WHERE MONTH(tgl_pembelian) = '$bulan' AND YEAR(tgl_pembelian) = '$tahun'
                GROUP BY kode_barang
            ) dp"),
                function ($join) {
                    $join->on('master_barang_pembelian.kode_barang', '=', 'dp.kode_barang');
                }
            )

            ->leftJoin(
                DB::raw("(
                SELECT kode_barang,harga
                FROM saldoawal_harga_gb
                WHERE bulan = '$bulan' AND tahun = '$tahun'
                GROUP BY kode_barang,harga
            ) hrgsa"),
                function ($join) {
                    $join->on('master_barang_pembelian.kode_barang', '=', 'hrgsa.kode_barang');
                }
            )



            ->leftJoin(
                DB::raw("(
                SELECT
                detail_pemasukan_gb.kode_barang,
                SUM( IF( departemen = 'Pembelian' , qty_unit ,0 )) AS qtypemb1,
                SUM( IF( departemen = 'Lainnya' , qty_unit ,0 )) AS qtylainnya1,
                SUM( IF( departemen = 'Retur Pengganti' , qty_unit ,0 )) AS qtypengganti1,

                SUM( IF( departemen = 'Pembelian' , qty_berat ,0 )) AS qtypemb2,
                SUM( IF( departemen = 'Lainnya' , qty_berat ,0 )) AS qtylainnya2,
                SUM( IF( departemen = 'Retur Pengganti' , qty_berat ,0 )) AS qtypengganti2,
                SUM( (IF( departemen = 'Pembelian' , qty_berat ,0 )) + (IF( departemen = 'Lainnya' , qty_berat ,0 ))) AS pemasukanqtyberat
                FROM
                detail_pemasukan_gb
                INNER JOIN pemasukan_gb ON detail_pemasukan_gb.nobukti_pemasukan = pemasukan_gb.nobukti_pemasukan
                WHERE MONTH(tgl_pemasukan) = '$bulan' AND YEAR(tgl_pemasukan) = '$tahun'
                GROUP BY detail_pemasukan_gb.kode_barang
            ) gm"),
                function ($join) {
                    $join->on('master_barang_pembelian.kode_barang', '=', 'gm.kode_barang');
                }
            )

            ->leftJoin(
                DB::raw("(
                SELECT
                detail_pengeluaran_gb.kode_barang,
                SUM( IF( pengeluaran_gb.kode_dept = 'Produksi' , qty_unit ,0 )) AS qtyprod3,
                SUM( IF( pengeluaran_gb.kode_dept = 'Seasoning' , qty_unit ,0 )) AS qtyseas3,
                SUM( IF( pengeluaran_gb.kode_dept = 'PDQC' , qty_unit ,0 )) AS qtypdqc3,
                SUM( IF( pengeluaran_gb.kode_dept = 'Susut' , qty_unit ,0 )) AS qtysus3,
                SUM( IF( pengeluaran_gb.kode_dept = 'Lainnya' , qty_unit ,0 )) AS qtylain3,
                SUM( IF( pengeluaran_gb.kode_dept = 'Cabang' , qty_unit ,0 )) AS qtycabang3,

                SUM( IF( pengeluaran_gb.kode_dept = 'Produksi' , qty_berat ,0 )) AS qtyprod4,
                SUM( IF( pengeluaran_gb.kode_dept = 'Seasoning' , qty_berat ,0 )) AS qtyseas4,
                SUM( IF( pengeluaran_gb.kode_dept = 'PDQC' , qty_berat ,0 )) AS qtypdqc4,
                SUM( IF( pengeluaran_gb.kode_dept = 'Susut' , qty_berat ,0 )) AS qtysus4,
                SUM( IF( pengeluaran_gb.kode_dept = 'Lainnya' , qty_berat ,0 )) AS qtylain4,
                SUM( IF( pengeluaran_gb.kode_dept = 'Cabang' , qty_berat ,0 )) AS qtycabang4
                FROM detail_pengeluaran_gb
                INNER JOIN pengeluaran_gb ON detail_pengeluaran_gb.nobukti_pengeluaran = pengeluaran_gb.nobukti_pengeluaran
                WHERE MONTH(tgl_pengeluaran) = '$bulan' AND YEAR(tgl_pengeluaran) = '$tahun'
                GROUP BY detail_pengeluaran_gb.kode_barang
            ) gk"),
                function ($join) {
                    $join->on('master_barang_pembelian.kode_barang', '=', 'gk.kode_barang');
                }
            )

            ->where('master_barang_pembelian.kode_dept', 'GDB')
            ->where('master_barang_pembelian.kode_kategori', $kode_kategori)
            ->orderBy('jenis_barang')
            ->orderByRaw('MID(4,3,master_barang_pembelian.kode_barang)')
            ->orderBy('urutan')
            ->get();

        $kategori = DB::table('kategori_barang_pembelian')->where('kode_kategori', $kode_kategori)->first();
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('gudangbahan.laporan.cetak_persediaan', compact('bulan', 'tahun', 'persediaan', 'kategori', 'namabulan'));
    }

    public function kartugudang()
    {
        $barang = Barangpembelian::where('kode_dept', 'GDB')->orderBy('nama_barang')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('gudangbahan.laporan.frm.lap_kartugudang', compact('bulan', 'barang'));
    }

    public function cetak_kartugudang(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $dari = $tahun . "-" . $bulan . "-01";
        $ceknextbulan = DB::table('pengeluaran_gb')
            ->select('tgl_pengeluaran')
            ->whereRaw('MONTH(tgl_pengeluaran)=' . $bulan)
            ->whereRaw('YEAR(tgl_pengeluaran)=' . $tahun)
            ->orderBy('tgl_pengeluaran', 'desc')
            ->first();
        if ($ceknextbulan == null) {
            $sampai = date("Y-m-t", strtotime($dari));
        } else {
            $sampai = $ceknextbulan->tgl_pengeluaran;
        }

        $tglakhirpenerimaan = date("Y-m-t", strtotime($dari));

        $saldoawal = DB::table('saldoawal_gb_detail')
            ->selectRaw("SUM( qty_unit ) AS qtyunitsa,
            SUM( qty_berat ) AS qtyberatsa")
            ->join('saldoawal_gb', 'saldoawal_gb_detail.kode_saldoawal_gb', '=', 'saldoawal_gb.kode_saldoawal_gb')
            ->where('bulan', $bulan)->where('tahun', $tahun)
            ->where('kode_barang', $kode_barang)
            ->first();

        $barang = DB::table('master_barang_pembelian')->where('kode_barang', $kode_barang)->first();

        return view('gudangbahan.laporan.cetak_kartugudang', compact('dari', 'sampai', 'barang', 'saldoawal', 'kode_barang'));
    }

    public function rekappersediaan()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('gudangbahan.laporan.frm.lap_rekappersediaan', compact('bulan'));
    }

    public function cetak_rekappersediaan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kode_kategori = $request->kode_kategori;

        $persediaan = DB::table('master_barang_pembelian')
            ->selectRaw("master_barang_pembelian.kode_barang,
            master_barang_pembelian.nama_barang,
            master_barang_pembelian.satuan,
            master_barang_pembelian.jenis_barang,
            sa.qtyunitsa,
            sa.qtyberatsa,
            op.qtyunitop,
            op.qtyberatop,
            gm.qtypemb1,
            gm.qtylainnya1,
            gm.qtypemb2,
            gm.qtylainnya2,
            gm.qtypengganti2,
            gm.qtypengganti1,
            gk.qtyprod3,
            gk.qtyseas3,
            gk.qtypdqc3,
            gk.qtysus3,
            gk.qtycabang3,
            gk.qtylain3,
            gk.qtyprod4,
            gk.qtyseas4,
            gk.qtypdqc4,
            gk.qtysus4,
            gk.qtycabang4,
            gk.qtylain4,
            hrgsa.harga,
            dp.totalharga")
            ->leftJoin(
                DB::raw("(
                SELECT saldoawal_gb_detail.kode_barang,
                SUM( qty_unit ) AS qtyunitsa,
                SUM( qty_berat ) AS qtyberatsa
                FROM saldoawal_gb_detail
                INNER JOIN saldoawal_gb ON saldoawal_gb.kode_saldoawal_gb=saldoawal_gb_detail.kode_saldoawal_gb
                WHERE bulan = '$bulan' AND tahun = '$tahun' GROUP BY saldoawal_gb_detail.kode_barang
            ) sa"),
                function ($join) {
                    $join->on('master_barang_pembelian.kode_barang', '=', 'sa.kode_barang');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT opname_gb_detail.kode_barang,
                SUM( qty_unit ) AS qtyunitop,
                SUM( qty_berat ) AS qtyberatop
                FROM opname_gb_detail
                INNER JOIN opname_gb ON opname_gb.kode_opname_gb=opname_gb_detail.kode_opname_gb
                WHERE bulan = '$bulan' AND tahun = '$tahun' GROUP BY opname_gb_detail.kode_barang
            ) op"),
                function ($join) {
                    $join->on('master_barang_pembelian.kode_barang', '=', 'op.kode_barang');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT SUM((qty*harga)+penyesuaian) as totalharga,kode_barang
                FROM detail_pembelian
                INNER JOIN pembelian ON detail_pembelian.nobukti_pembelian = pembelian.nobukti_pembelian
                WHERE MONTH(tgl_pembelian) = '$bulan' AND YEAR(tgl_pembelian) = '$tahun'
                GROUP BY kode_barang
            ) dp"),
                function ($join) {
                    $join->on('master_barang_pembelian.kode_barang', '=', 'dp.kode_barang');
                }
            )

            ->leftJoin(
                DB::raw("(
                SELECT kode_barang,harga
                FROM saldoawal_harga_gb
                WHERE bulan = '$bulan' AND tahun = '$tahun'
                GROUP BY kode_barang,harga
            ) hrgsa"),
                function ($join) {
                    $join->on('master_barang_pembelian.kode_barang', '=', 'hrgsa.kode_barang');
                }
            )



            ->leftJoin(
                DB::raw("(
                SELECT
                detail_pemasukan_gb.kode_barang,
                SUM( IF( departemen = 'Pembelian' , qty_unit ,0 )) AS qtypemb1,
                SUM( IF( departemen = 'Lainnya' , qty_unit ,0 )) AS qtylainnya1,
                SUM( IF( departemen = 'Retur Pengganti' , qty_unit ,0 )) AS qtypengganti1,

                SUM( IF( departemen = 'Pembelian' , qty_berat ,0 )) AS qtypemb2,
                SUM( IF( departemen = 'Lainnya' , qty_berat ,0 )) AS qtylainnya2,
                SUM( IF( departemen = 'Retur Pengganti' , qty_berat ,0 )) AS qtypengganti2,
                SUM( (IF( departemen = 'Pembelian' , qty_berat ,0 )) + (IF( departemen = 'Lainnya' , qty_berat ,0 ))) AS pemasukanqtyberat
                FROM
                detail_pemasukan_gb
                INNER JOIN pemasukan_gb ON detail_pemasukan_gb.nobukti_pemasukan = pemasukan_gb.nobukti_pemasukan
                WHERE MONTH(tgl_pemasukan) = '$bulan' AND YEAR(tgl_pemasukan) = '$tahun'
                GROUP BY detail_pemasukan_gb.kode_barang
            ) gm"),
                function ($join) {
                    $join->on('master_barang_pembelian.kode_barang', '=', 'gm.kode_barang');
                }
            )

            ->leftJoin(
                DB::raw("(
                SELECT
                detail_pengeluaran_gb.kode_barang,
                SUM( IF( pengeluaran_gb.kode_dept = 'Produksi' , qty_unit ,0 )) AS qtyprod3,
                SUM( IF( pengeluaran_gb.kode_dept = 'Seasoning' , qty_unit ,0 )) AS qtyseas3,
                SUM( IF( pengeluaran_gb.kode_dept = 'PDQC' , qty_unit ,0 )) AS qtypdqc3,
                SUM( IF( pengeluaran_gb.kode_dept = 'Susut' , qty_unit ,0 )) AS qtysus3,
                SUM( IF( pengeluaran_gb.kode_dept = 'Lainnya' , qty_unit ,0 )) AS qtylain3,
                SUM( IF( pengeluaran_gb.kode_dept = 'Cabang' , qty_unit ,0 )) AS qtycabang3,

                SUM( IF( pengeluaran_gb.kode_dept = 'Produksi' , qty_berat ,0 )) AS qtyprod4,
                SUM( IF( pengeluaran_gb.kode_dept = 'Seasoning' , qty_berat ,0 )) AS qtyseas4,
                SUM( IF( pengeluaran_gb.kode_dept = 'PDQC' , qty_berat ,0 )) AS qtypdqc4,
                SUM( IF( pengeluaran_gb.kode_dept = 'Susut' , qty_berat ,0 )) AS qtysus4,
                SUM( IF( pengeluaran_gb.kode_dept = 'Lainnya' , qty_berat ,0 )) AS qtylain4,
                SUM( IF( pengeluaran_gb.kode_dept = 'Cabang' , qty_berat ,0 )) AS qtycabang4
                FROM detail_pengeluaran_gb
                INNER JOIN pengeluaran_gb ON detail_pengeluaran_gb.nobukti_pengeluaran = pengeluaran_gb.nobukti_pengeluaran
                WHERE MONTH(tgl_pengeluaran) = '$bulan' AND YEAR(tgl_pengeluaran) = '$tahun'
                GROUP BY detail_pengeluaran_gb.kode_barang
            ) gk"),
                function ($join) {
                    $join->on('master_barang_pembelian.kode_barang', '=', 'gk.kode_barang');
                }
            )

            ->where('master_barang_pembelian.kode_dept', 'GDB')
            ->where('master_barang_pembelian.kode_kategori', $kode_kategori)
            ->orderBy('jenis_barang')
            ->orderByRaw('MID(4,3,master_barang_pembelian.kode_barang)')
            ->orderBy('urutan')
            ->get();

        $kategori = DB::table('kategori_barang_pembelian')->where('kode_kategori', $kode_kategori)->first();
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('gudangbahan.laporan.cetak_rekappersediaan', compact('bulan', 'tahun', 'persediaan', 'kategori', 'namabulan'));
    }
}