<?php

namespace App\Http\Controllers;

use App\Models\Angkutan;
use App\Models\Barang;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporangudangjadiController extends Controller
{
    public function persediaan()
    {
        $barang = Barang::orderBy('kode_produk')->get();
        return view('gudangjadi.laporan.frm.lap_persediaan', compact('barang'));
    }

    public function cetak_persediaan(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        lockreport($dari);
        $kode_produk = $request->kode_produk;
        $mutasi = DB::table('detail_mutasi_gudang')
            ->selectRaw("detail_mutasi_gudang.no_mutasi_gudang,tgl_mutasi_gudang,jenis_mutasi,mutasi_gudang_jadi.keterangan,detail_mutasi_gudang.kode_produk,jumlah,kode_cabang,`inout`")
            ->join('mutasi_gudang_jadi', 'detail_mutasi_gudang.no_mutasi_gudang', '=', 'mutasi_gudang_jadi.no_mutasi_gudang')
            ->leftJoin('permintaan_pengiriman', 'mutasi_gudang_jadi.no_permintaan_pengiriman', '=', 'permintaan_pengiriman.no_permintaan_pengiriman')
            ->join('master_barang', 'detail_mutasi_gudang.kode_produk', '=', 'master_barang.kode_produk')
            ->whereBetween('tgl_mutasi_gudang', [$dari, $sampai])
            ->where('detail_mutasi_gudang.kode_produk', $kode_produk)
            ->orderBy('tgl_mutasi_gudang')
            ->orderBy('mutasi_gudang_jadi.time_stamp')
            ->get();

        $saldoawal = DB::table('detail_mutasi_gudang')
            ->selectRaw("SUM(IF( `inout` = 'IN', jumlah, 0)) AS jml_in,
        SUM(IF( `inout` = 'OUT', jumlah, 0)) AS jml_out,
        SUM(IF( `inout` = 'IN', jumlah, 0)) -SUM(IF( `inout` = 'OUT', jumlah, 0)) as saldo_awal")
            ->join('mutasi_gudang_jadi', 'detail_mutasi_gudang.no_mutasi_gudang', '=', 'mutasi_gudang_jadi.no_mutasi_gudang')
            ->where('tgl_mutasi_gudang', '<', $dari)
            ->where('kode_produk', $kode_produk)
            ->first();

        $produk = Barang::where('kode_produk', $kode_produk)->first();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Persediaan Gudang Jadi.xls");
        }
        return view('gudangjadi.laporan.cetak_persediaan', compact('dari', 'sampai', 'saldoawal', 'mutasi', 'produk'));
    }

    public function rekappersediaan()
    {
        return view('gudangjadi.laporan.frm.lap_rekappersediaan');
    }

    public function cetak_rekappersediaan(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        lockreport($dari);
        $mutasi = DB::table('master_barang')
            ->selectRaw("master_barang.kode_produk,
                nama_barang,
                saldoawal,
                jmlfsthp,
                jmlrepack,
                jmlreject,
                jmllainlain_in,
                jmllainlain_out,
                jmlsuratjalan")
            ->leftJoin(
                DB::raw("(
                    SELECT
                        kode_produk,
                        IFNULL(SUM( IF ( `inout` = 'IN', jumlah, 0 ) ) -
                        SUM( IF ( `inout` = 'OUT', jumlah, 0 ) ),0) as saldoawal
                    FROM
                        detail_mutasi_gudang d
                    INNER JOIN mutasi_gudang_jadi ON d.no_mutasi_gudang = mutasi_gudang_jadi.no_mutasi_gudang
                    WHERE tgl_mutasi_gudang < '$dari'
                    GROUP BY kode_produk
                ) sa"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'sa.kode_produk');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT
                kode_produk,
                SUM(IF(jenis_mutasi = 'FSTHP' ,jumlah,0)) as jmlfsthp,
                SUM(IF(jenis_mutasi = 'REPACK',jumlah,0)) as jmlrepack,
                SUM(IF(jenis_mutasi = 'REJECT',jumlah,0)) as jmlreject,
                SUM(IF(jenis_mutasi = 'LAINLAIN' AND  `inout` ='IN',jumlah,0)) as jmllainlain_in,
                SUM(IF(jenis_mutasi = 'LAINLAIN' AND  `inout` ='OUT',jumlah,0)) as jmllainlain_out,
                SUM(IF(jenis_mutasi = 'SURAT JALAN',jumlah,0)) as jmlsuratjalan
                FROM
                    detail_mutasi_gudang d
                INNER JOIN mutasi_gudang_jadi
                ON d.no_mutasi_gudang = mutasi_gudang_jadi.no_mutasi_gudang
                WHERE
                tgl_mutasi_gudang BETWEEN '$dari' AND '$sampai' GROUP BY kode_produk
            ) mutasi"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'mutasi.kode_produk');
                }
            )
            ->get();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekap  Persediaan Gudang Jadi.xls");
        }
        return view('gudangjadi.laporan.cetak_rekappersediaan', compact('dari', 'sampai', 'mutasi'));
    }

    public function rekaphasilproduksi()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('gudangjadi.laporan.frm.lap_rekaphasilproduksi', compact('bulan'));
    }

    public function cetak_rekaphasilproduksi(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        lockyear($tahun);
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $minggu1 = "'" . $tahun . "-" . $bulan . "-01'" . " AND '" . $tahun . "-" . $bulan . "-07'";
        $minggu2 = "'" . $tahun . "-" . $bulan . "-08'" . " AND '" . $tahun . "-" . $bulan . "-14'";
        $minggu3 = "'" . $tahun . "-" . $bulan . "-15'" . " AND '" . $tahun . "-" . $bulan . "-21'";
        $minggu4 = "'" . $tahun . "-" . $bulan . "-22'" . " AND '" . $tahun . "-" . $bulan . "-31'";
        $mutasi = DB::table('master_barang')
            ->selectRaw("master_barang.kode_produk,nama_barang,minggu1,minggu2,minggu3,minggu4")
            ->leftJoin(
                DB::raw("(
                SELECT
                kode_produk,
                IFNULL(SUM( IF ( `jenis_mutasi` = 'FSTHP' AND tgl_mutasi_gudang BETWEEN $minggu1, jumlah, 0 ) ),0) as minggu1,
                IFNULL(SUM( IF ( `jenis_mutasi` = 'FSTHP' AND tgl_mutasi_gudang BETWEEN $minggu2, jumlah, 0 ) ),0) as minggu2,
                IFNULL(SUM( IF ( `jenis_mutasi` = 'FSTHP' AND tgl_mutasi_gudang BETWEEN $minggu3, jumlah, 0 ) ),0) as minggu3,
                IFNULL(SUM( IF ( `jenis_mutasi` = 'FSTHP' AND tgl_mutasi_gudang BETWEEN $minggu4, jumlah, 0 ) ),0) as minggu4
                FROM
                    detail_mutasi_gudang d
                INNER JOIN mutasi_gudang_jadi ON d.no_mutasi_gudang = mutasi_gudang_jadi.no_mutasi_gudang
                WHERE tgl_mutasi_gudang BETWEEN '$dari' AND '$sampai'
                GROUP BY kode_produk
            ) produksi"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'produksi.kode_produk');
                }
            )
            ->get();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekap  Hasil Produksi.xls");
        }
        return view('gudangjadi.laporan.cetak_rekaphasilproduksi', compact('dari', 'sampai', 'mutasi'));
    }

    public function rekappengeluaran()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('gudangjadi.laporan.frm.lap_rekappengeluaran', compact('bulan'));
    }

    public function cetak_rekappengeluaran(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        lockyear($tahun);
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $minggu1 = "'" . $tahun . "-" . $bulan . "-01'" . " AND '" . $tahun . "-" . $bulan . "-07'";
        $minggu2 = "'" . $tahun . "-" . $bulan . "-08'" . " AND '" . $tahun . "-" . $bulan . "-14'";
        $minggu3 = "'" . $tahun . "-" . $bulan . "-15'" . " AND '" . $tahun . "-" . $bulan . "-21'";
        $minggu4 = "'" . $tahun . "-" . $bulan . "-22'" . " AND '" . $tahun . "-" . $bulan . "-31'";
        $mutasi = DB::table('master_barang')
            ->selectRaw("master_barang.kode_produk,nama_barang,minggu1,minggu2,minggu3,minggu4")
            ->leftJoin(
                DB::raw("(
                SELECT
                kode_produk,
                IFNULL(SUM( IF ( `jenis_mutasi` = 'SURAT JALAN' AND tgl_mutasi_gudang BETWEEN $minggu1, jumlah, 0 ) ),0) as minggu1,
                IFNULL(SUM( IF ( `jenis_mutasi` = 'SURAT JALAN' AND tgl_mutasi_gudang BETWEEN $minggu2, jumlah, 0 ) ),0) as minggu2,
                IFNULL(SUM( IF ( `jenis_mutasi` = 'SURAT JALAN' AND tgl_mutasi_gudang BETWEEN $minggu3, jumlah, 0 ) ),0) as minggu3,
                IFNULL(SUM( IF ( `jenis_mutasi` = 'SURAT JALAN' AND tgl_mutasi_gudang BETWEEN $minggu4, jumlah, 0 ) ),0) as minggu4
                FROM
                    detail_mutasi_gudang d
                INNER JOIN mutasi_gudang_jadi ON d.no_mutasi_gudang = mutasi_gudang_jadi.no_mutasi_gudang
                WHERE tgl_mutasi_gudang BETWEEN '$dari' AND '$sampai'
                GROUP BY kode_produk
            ) produksi"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'produksi.kode_produk');
                }
            )
            ->get();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekap Pengeluaran Gudang Jadi.xls");
        }
        return view('gudangjadi.laporan.cetak_rekappengeluaran', compact('dari', 'sampai', 'mutasi'));
    }

    public function realisasikiriman()
    {
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('gudangjadi.laporan.frm.lap_realisasikiriman', compact('bulan', 'cabang'));
    }

    public function cetak_realisasikiriman(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        lockyear($tahun);
        $kode_cabang = $request->kode_cabang;
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        if (!empty($kode_cabang)) {
            $cabang = "AND kode_cabang = '" . $kode_cabang . "' ";
        } else {
            $cabang = "";
        }
        $rekap = DB::table('master_barang')
            ->selectRaw('master_barang.kode_produk,nama_barang,permintaan,target,realisasi')
            ->leftJoin(
                DB::raw("(
                SELECT
                    kode_produk,
                    IFNULL(SUM(jumlah),0) as permintaan
                FROM
                    detail_permintaan_pengiriman dp
                INNER JOIN permintaan_pengiriman pp ON dp.no_permintaan_pengiriman = pp.no_permintaan_pengiriman
                WHERE tgl_permintaan_pengiriman BETWEEN '$dari' AND '$sampai'" . $cabang . "
                GROUP BY kode_produk
            ) permintaanpengiriman"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'permintaanpengiriman.kode_produk');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT
                    kode_produk,
                    IFNULL(SUM(target_bulan),0) as target
                FROM
                    target_pengiriman tp
                WHERE bulan = '$bulan'  AND tahun = '$tahun'" . $cabang . "
                GROUP BY kode_produk
            ) targetpengiriman"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'targetpengiriman.kode_produk');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT
                    kode_produk,
                    IFNULL(SUM( IF ( `jenis_mutasi` = 'SURAT JALAN', jumlah, 0 ) ),0) as realisasi
                FROM
                    detail_mutasi_gudang d
                INNER JOIN mutasi_gudang_jadi ON d.no_mutasi_gudang = mutasi_gudang_jadi.no_mutasi_gudang
                INNER JOIN permintaan_pengiriman pp ON mutasi_gudang_jadi.no_permintaan_pengiriman = pp.no_permintaan_pengiriman
                WHERE tgl_mutasi_gudang BETWEEN '$dari' AND '$sampai'" . $cabang . "
                GROUP BY kode_produk
            ) realisasikiriman"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'realisasikiriman.kode_produk');
                }
            )
            ->get();
        $cbg = Cabang::where('kode_cabang', $kode_cabang)->first();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Realisasi Kiriman Gudang Jadi.xls");
        }
        return view('gudangjadi.laporan.cetak_realisasikiriman', compact('cbg', 'rekap', 'dari', 'sampai'));
    }

    public function realisasioman()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('gudangjadi.laporan.frm.lap_realisasioman', compact('bulan'));
    }

    public function cetak_realisasioman(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        lockyear($tahun);
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $produk = Barang::orderBy('kode_produk')->get();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Realisasi OMAN $dari-$sampai.xls");
        }
        return view('gudangjadi.laporan.cetak_realisasioman', compact('dari', 'sampai', 'produk', 'cabang', 'bulan', 'tahun'));
    }

    public function angkutan()
    {
        return view('gudangjadi.laporan.frm.lap_angkutan');
    }

    public function cetak_angkutan(Request $request)
    {
        $angkutan = $request->angkutan;
        $dari = $request->dari;
        lockreport($dari);
        $sampai = $request->sampai;
        $query = Angkutan::query();
        $query->join('mutasi_gudang_jadi', 'angkutan.no_surat_jalan', '=', 'mutasi_gudang_jadi.no_dok');
        $query->whereBetween('tgl_mutasi_gudang', [$dari, $sampai]);
        if (!empty($angkutan)) {
            $query->where('angkutan', $angkutan);
        }
        $rekap = $query->get();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Angkutan.xls");
        }
        return view('gudangjadi.laporan.cetak_angkutan', compact('dari', 'sampai', 'rekap', 'angkutan'));
    }
}
