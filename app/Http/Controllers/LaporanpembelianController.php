<?php

namespace App\Http\Controllers;

use App\Models\Barangpembelian;
use App\Models\Coa;
use App\Models\Detailkontrabon;
use App\Models\Detailpembelian;
use App\Models\Jurnalkoreksi;
use App\Models\Pembelian;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanpembelianController extends Controller
{
    public function index()
    {
        $departemen = DB::table('departemen')->where('status_pengajuan', 1)->get();
        $supplier = Supplier::orderBy('nama_supplier')->get();
        return view('pembelian.laporan.frm.lap_pembelian', compact('departemen', 'supplier'));
    }

    public function cetak_pembelian(Request $request)
    {
        $kode_dept = $request->kode_dept;
        $kode_supplier = $request->kode_supplier;
        $ppn = $request->ppn;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $supplier = DB::table('supplier')->where('kode_supplier', $kode_supplier)->first();
        $departemen = DB::table('departemen')->where('kode_dept', $kode_dept)->first();
        $query = Detailpembelian::query();
        $query->selectRaw("detail_pembelian.nobukti_pembelian,tgl_pembelian,pembelian.kode_supplier,nama_supplier,
        detail_pembelian.kode_barang,nama_barang,pembelian.kode_dept,nama_dept,detail_pembelian.keterangan,detail_pembelian.ket_penjualan,
        detail_pembelian.kode_akun,nama_akun,ppn,qty,harga,penyesuaian,detail_pembelian.status,detail_pembelian.kode_cabang,jenistransaksi,
        kategori_transaksi,
        date_format(pembelian.date_created, '%d %M %Y %H:%i:%s') as date_created,
        date_format(pembelian.date_updated, '%d %M %Y %H:%i:%s') as date_updated,
        date_format(detail_pembelian.date_created, '%d %M %Y %H:%i:%s') as detaildate_created,
        date_format(detail_pembelian.date_updated, '%d %M %Y %H:%i:%s') as detaildate_updated");
        $query->join('pembelian', 'detail_pembelian.nobukti_pembelian', '=', 'pembelian.nobukti_pembelian');
        $query->join('supplier', 'pembelian.kode_supplier', '=', 'supplier.kode_supplier');
        $query->join('departemen', 'pembelian.kode_dept', '=', 'departemen.kode_dept');
        $query->join('coa', 'detail_pembelian.kode_akun', '=', 'coa.kode_akun');
        $query->leftJoin('master_barang_pembelian', 'detail_pembelian.kode_barang', '=', 'master_barang_pembelian.kode_barang');
        $query->whereBetween('tgl_pembelian', [$dari, $sampai]);
        if (!empty($kode_supplier)) {
            $query->where('pembelian.kode_supplier', $kode_supplier);
        }

        if ($request->ppn != "-") {
            $query->where('pembelian.ppn', $request->ppn);
        }

        if (!empty($kode_dept)) {
            $query->where('pembelian.kode_dept', $kode_dept);
        }

        $query->orderBy('tgl_pembelian');
        $query->orderBy('detail_pembelian.nobukti_pembelian');
        $query->orderBy('detail_pembelian.status');
        $query->orderBy('detail_pembelian.no_urut');
        $pmb = $query->get();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Pembelian $dari-$sampai.xls");
        }
        return view('pembelian.laporan.cetak_pembelian', compact('dari', 'sampai', 'supplier', 'kode_dept', 'ppn', 'pmb', 'departemen'));
    }

    public function pembayaran()
    {
        $supplier = Supplier::orderBy('nama_supplier')->get();
        return view('pembelian.laporan.frm.lap_pembayaran', compact('supplier'));
    }

    public function cetak_pembayaran(Request $request)
    {
        $kode_supplier = $request->kode_supplier;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $supplier = DB::table('supplier')->where('kode_supplier', $kode_supplier)->first();
        $query = Detailkontrabon::query();
        $query->selectRaw("detail_kontrabon.no_kontrabon,detail_kontrabon.nobukti_pembelian,nama_supplier,tglbayar,date_format(historibayar_pembelian.log, '%d %M %Y %H:%i:%s') as log, date_format(historibayar_pembelian.date_updated, '%d %M %Y %H:%i:%s') as date_updated,
        SUM(IF( via = 'BCA', jmlbayar, 0)) AS bca,
        SUM(IF( via = 'BCA CV', jmlbayar, 0)) AS bca_cv,
        SUM(IF( via = 'BNI', jmlbayar, 0)) AS bni,
        SUM(IF( via = 'BNI CV', jmlbayar, 0)) AS bni_cv,
        SUM(IF( via = 'KAS', jmlbayar, 0)) AS kasbesar,
        SUM(IF( via = 'KAS KECIL', jmlbayar, 0)) AS kaskecil,
        SUM(IF( via = 'BNI MP VALLAS', jmlbayar, 0)) AS permata,
        SUM(IF( via = 'BNI MP', jmlbayar, 0)) AS bni_mp,
        SUM(IF( via = 'BCA MP', jmlbayar, 0)) AS bca_mp,
        SUM(IF( via = 'CASH', jmlbayar, 0)) AS cash,
        SUM(IF( via = 'BNI CV INDO', jmlbayar, 0)) AS bni_indo_pangan,
        SUM(IF( via = 'BNI VLS INDO', jmlbayar, 0)) AS bni_indo_vallas,
        SUM(IF( via = 'BCA  NEW', jmlbayar, 0)) AS bca_new,
        SUM(IF( master_bank.kode_cabang != 'PST', jmlbayar, 0)) AS lainlain,
        IFNULL(SUM(jmlbayar),0) as totalbayar");
        $query->join('historibayar_pembelian', 'detail_kontrabon.no_kontrabon', '=', 'historibayar_pembelian.no_kontrabon');
        $query->join('pembelian', 'detail_kontrabon.nobukti_pembelian', '=', 'pembelian.nobukti_pembelian');
        $query->join('supplier', 'pembelian.kode_supplier', '=', 'supplier.kode_supplier');
        $query->join('master_bank', 'historibayar_pembelian.via', '=', 'master_bank.kode_bank');
        $query->whereBetween('tglbayar', [$dari, $sampai]);
        if (!empty($kode_supplier)) {
            $query->where('pembelian.kode_supplier', $kode_supplier);
        }
        $query->groupByRaw("detail_kontrabon.no_kontrabon,detail_kontrabon.nobukti_pembelian,tglbayar,nama_supplier,historibayar_pembelian.log,historibayar_pembelian.date_updated");
        $pmb = $query->get();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Penjualan Pembayaran $dari-$sampai.xls");
        }
        return view('pembelian.laporan.cetak_pembayaran', compact('dari', 'sampai', 'supplier', 'pmb'));
    }

    public function rekapsupplier()
    {
        return view('pembelian.laporan.frm.lap_rekapsupplier');
    }

    public function cetak_rekapsupplier(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $query = Detailpembelian::query();
        $query->selectRaw('pembelian.kode_supplier,nama_supplier,
        (SUM( IF ( STATUS = "PMB", ((qty*harga)+penyesuaian), 0 ) )) as jumlah');
        $query->join('pembelian', 'detail_pembelian.nobukti_pembelian', '=', 'pembelian.nobukti_pembelian');
        $query->join('supplier', 'pembelian.kode_supplier', '=', 'supplier.kode_supplier');
        $query->whereBetween('tgl_pembelian', [$dari, $sampai]);
        $query->groupByRaw('pembelian.kode_supplier,nama_supplier');
        $pmb = $query->get();
        // /dd($pmb);
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekap Supplier $dari-$sampai.xls");
        }
        return view('pembelian.laporan.cetak_rekapsupplier', compact('dari', 'sampai', 'pmb'));
    }

    public function rekappembelian()
    {
        return view('pembelian.laporan.frm.lap_rekappembelian');
    }

    public function cetak_rekappembelian(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $jenis_barang = $request->jenis_barang;
        $sortby = $request->sortby;

        $query = Detailpembelian::query();
        $query->selectRaw("detail_pembelian.nobukti_pembelian,tgl_pembelian,pembelian.kode_supplier,nama_supplier,
        detail_pembelian.kode_barang,nama_barang,jenis_barang,pembelian.kode_dept,nama_dept,detail_pembelian.keterangan,
        detail_pembelian.kode_akun,nama_akun,ppn,qty,harga,penyesuaian");
        $query->join('pembelian', 'detail_pembelian.nobukti_pembelian', '=', 'pembelian.nobukti_pembelian');
        $query->join('supplier', 'pembelian.kode_supplier', '=', 'supplier.kode_supplier');
        $query->join('departemen', 'pembelian.kode_dept', '=', 'departemen.kode_dept');
        $query->join('coa', 'detail_pembelian.kode_akun', '=', 'coa.kode_akun');
        $query->join('master_barang_pembelian', 'detail_pembelian.kode_barang', '=', 'master_barang_pembelian.kode_barang');
        $query->whereBetween('tgl_pembelian', [$dari, $sampai]);
        if (!empty($jenis_barang)) {
            $query->where('jenis_barang', $jenis_barang);
        }
        if ($sortby == "supplier") {
            $query->orderBy('pembelian.kode_supplier');
        } else {
            $query->orderBy('jenis_barang');
            $query->orderBy('pembelian.kode_supplier');
        }

        $pmb = $query->get();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekap Pembelian $dari-$sampai.xls");
        }
        if ($sortby == "supplier") {
            return view('pembelian.laporan.cetak_rekappembelian_supplier', compact('dari', 'sampai', 'pmb', 'jenis_barang'));
        } else {
            return view('pembelian.laporan.cetak_rekappembelian_jenisbarang', compact('dari', 'sampai', 'pmb', 'jenis_barang'));
        }
    }

    public function kartuhutang()
    {
        $supplier = Supplier::orderBy('nama_supplier')->get();
        return view('pembelian.laporan.frm.lap_kartuhutang', compact('supplier'));
    }

    public function cetak_kartuhutang(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $jenishutang = $request->jenishutang;
        $jenislaporan = $request->jenislaporan;
        $kode_supplier = $request->kode_supplier;
        $supplier = Supplier::where('kode_supplier', $kode_supplier)->first();
        $coa = Coa::where('kode_akun', $jenishutang)->first();
        $query = Pembelian::query();
        $query->selectRaw("pembelian.nobukti_pembelian,tgl_pembelian,pembelian.kode_supplier,nama_supplier,pembelian.kode_akun,nama_akun,(IFNULL(IFNULL(totalhutang,0) + IFNULL(penyesuaianbulanlalu,0)+ IFNULL(penyesuaianbulanini,0),0))   as totalhutang,
        (IFNULL(IFNULL(totalhutang,0) + IFNULL(penyesuaianbulanlalu,0) - IFNULL(jmlbayarbulanlalu,0) ,0))   as sisapiutang,
        IFNULL(jmlbayarbulanlalu,0) as jmlbayarbulanlalu, IFNULL(jmlbayarbulanini,0) as jmlbayarbulanini,IFNULL(penyesuaianbulanlalu,0) as penyesuaianbulanlalu,IFNULL(penyesuaianbulanini,0) as penyesuaianbulanini ,pmbbulanini,kategori_transaksi");
        $query->join('supplier', 'pembelian.kode_supplier', '=', 'supplier.kode_supplier');
        $query->join('coa', 'pembelian.kode_akun', '=', 'coa.kode_akun');
        $query->leftJoin(
            DB::raw("(
            SELECT detail_pembelian.nobukti_pembelian, (SUM( IF ( STATUS = 'PMB', ((qty*harga)+penyesuaian), 0 ) ) - SUM( IF ( STATUS = 'PNJ',(qty*harga), 0 ) ) ) as totalhutang
            ,IF(tgl_pembelian BETWEEN '$dari' AND '$sampai',(SUM( IF ( STATUS = 'PMB', ((qty*harga)+penyesuaian), 0 ) ) - SUM( IF ( STATUS = 'PNJ',(qty*harga), 0 ) ) ),0) as pmbbulanini
            FROM detail_pembelian
            INNER JOIN pembelian ON detail_pembelian.nobukti_pembelian = pembelian.nobukti_pembelian
            GROUP BY nobukti_pembelian,tgl_pembelian
            ) detailpembelian"),
            function ($join) {
                $join->on('pembelian.nobukti_pembelian', '=', 'detailpembelian.nobukti_pembelian');
            }
        );

        $query->leftJoin(
            DB::raw("(
            SELECT nobukti_pembelian,SUM(IF(tglbayar<'$dari',jmlbayar,0)) as jmlbayarbulanlalu,
            SUM(IF(tglbayar BETWEEN '$dari' AND '$sampai',jmlbayar,0)) as jmlbayarbulanini
            FROM historibayar_pembelian hb
            INNER JOIN detail_kontrabon on hb.no_kontrabon = detail_kontrabon.no_kontrabon
            GROUP BY nobukti_pembelian
            ) historibayar"),
            function ($join) {
                $join->on('pembelian.nobukti_pembelian', '=', 'historibayar.nobukti_pembelian');
            }
        );

        $query->leftJoin(
            DB::raw("(
            SELECT nobukti_pembelian,(SUM(IF(tgl_jurnalkoreksi<'$dari' AND status_dk='K' AND kode_akun='2-1200'
            OR tgl_jurnalkoreksi<'$dari' AND status_dk='K' AND kode_akun='2-1300' ,(qty*harga),0)) - SUM(IF(tgl_jurnalkoreksi<'$dari' AND status_dk='D' AND kode_akun='2-1200'
            OR tgl_jurnalkoreksi<'$dari' AND status_dk='D' AND kode_akun='2-1300' ,(qty*harga),0))) as penyesuaianbulanlalu,
            (SUM(IF(tgl_jurnalkoreksi BETWEEN '$dari' AND '$sampai'  AND status_dk='K' AND kode_akun='2-1200'
            OR tgl_jurnalkoreksi BETWEEN '$dari' AND '$sampai'  AND status_dk='K' AND kode_akun='2-1300'  ,(qty*harga),0))-SUM(IF(tgl_jurnalkoreksi BETWEEN '$dari' AND '$sampai'  AND status_dk='D' AND kode_akun='2-1200'
            OR tgl_jurnalkoreksi BETWEEN '$dari' AND '$sampai'  AND status_dk='D' AND kode_akun='2-1300'  ,(qty*harga),0))) as penyesuaianbulanini
            FROM jurnal_koreksi jk
            GROUP BY nobukti_pembelian
            ) jurnalkoreksi"),
            function ($join) {
                $join->on('pembelian.nobukti_pembelian', '=', 'jurnalkoreksi.nobukti_pembelian');
            }
        );

        $query->where('tgl_pembelian', '<=', $sampai);
        $query->whereRaw("(IFNULL(IFNULL(totalhutang,0) + IFNULL(penyesuaianbulanlalu,0) - IFNULL(jmlbayarbulanlalu,0) ,0))  != 0");
        if (!empty($kode_supplier)) {
            $query->where('pembelian.kode_supplier', $kode_supplier);
        }

        if (!empty($jenishutang)) {
            $query->where('pembelian.kode_akun', $jenishutang);
        }
        $query->orWhere('tgl_pembelian', '<=', $sampai);
        $query->where('jmlbayarbulanini', '!=', 0);
        if (!empty($kode_supplier)) {
            $query->where('pembelian.kode_supplier', $kode_supplier);
        }

        if (!empty($jenishutang)) {
            $query->where('pembelian.kode_akun', $jenishutang);
        }

        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Cetak Kartu Hutang $dari-$sampai.xls");
        }
        if ($jenislaporan == 1) {
            $query->orderBy('tgl_pembelian');
            $query->orderBy('pembelian.nobukti_pembelian');
            $pmb = $query->get();
            return view('pembelian.laporan.cetak_kartuhutang', compact('dari', 'sampai', 'supplier', 'coa', 'pmb'));
        } else {
            $query->orderBy('kode_supplier');
            $pmb = $query->get();
            return view('pembelian.laporan.cetak_rekapkartuhutang', compact('dari', 'sampai', 'supplier', 'coa', 'pmb'));
        }
    }

    public function auh()
    {
        return view('pembelian.laporan.frm.lap_auh');
    }

    public function cetak_auh(Request $request)
    {
        $sampai = $request->tgl_auh;
        $pmb = DB::select("SELECT * FROM
        (SELECT detail_pembelian.nobukti_pembelian,pembelian.kode_supplier,nama_supplier,
        (SUM( IF ( STATUS = 'PMB', ((qty*harga)+penyesuaian), 0 ) ) - SUM( IF ( STATUS = 'PNJ',(qty*harga), 0 ) ) )-IFNULL(jmlbayar,0)+IFNULL(jmlpenyesuaian,0) as sisahutang,
        CASE
        WHEN  datediff('$sampai', tgl_pembelian) < 30  THEN
        (SUM( IF ( STATUS = 'PMB', ((qty*harga)+penyesuaian), 0 ) ) - SUM( IF ( STATUS = 'PNJ',(qty*harga), 0 ) ) )-IFNULL(jmlbayar,0)+IFNULL(jmlpenyesuaian,0) END as bulanberjalan,
        CASE
        WHEN datediff('$sampai', tgl_pembelian) < 60 AND datediff('$sampai', tgl_pembelian) >= 30  THEN
        (SUM( IF ( STATUS = 'PMB', ((qty*harga)+penyesuaian), 0 ) ) - SUM( IF ( STATUS = 'PNJ',(qty*harga), 0 ) ) )-IFNULL(jmlbayar,0)+IFNULL(jmlpenyesuaian,0) END as satubulan,
        CASE
        WHEN datediff('$sampai', tgl_pembelian) < 90 AND datediff('$sampai', tgl_pembelian) >= 60  THEN
        (SUM( IF ( STATUS = 'PMB', ((qty*harga)+penyesuaian), 0 ) ) - SUM( IF ( STATUS = 'PNJ',(qty*harga), 0 ) ) )-IFNULL(jmlbayar,0)+IFNULL(jmlpenyesuaian,0) END as duabulan,
        CASE
        WHEN datediff('$sampai', tgl_pembelian) >= 90  THEN
        (SUM( IF ( STATUS = 'PMB', ((qty*harga)+penyesuaian), 0 ) ) - SUM( IF ( STATUS = 'PNJ',(qty*harga), 0 ) ) )-IFNULL(jmlbayar,0)+IFNULL(jmlpenyesuaian,0) END as lebihtigabulan
        FROM detail_pembelian
        INNER JOIN pembelian ON detail_pembelian.nobukti_pembelian = pembelian.nobukti_pembelian
        INNER JOIN supplier ON pembelian.kode_supplier = supplier.kode_supplier
        LEFT JOIN (
        SELECT nobukti_pembelian,SUM(IF(tglbayar<='$sampai',jmlbayar,0)) as jmlbayar
        FROM historibayar_pembelian hb
        INNER JOIN detail_kontrabon on hb.no_kontrabon = detail_kontrabon.no_kontrabon
        GROUP BY nobukti_pembelian
        ) hb ON hb.nobukti_pembelian = detail_pembelian.nobukti_pembelian
        LEFT JOIN (
        SELECT nobukti_pembelian,(SUM(IF(tgl_jurnalkoreksi<'$sampai' AND status_dk='K' AND kode_akun='2-1200'
        OR tgl_jurnalkoreksi<'$sampai' AND status_dk='K' AND kode_akun='2-1300' ,(qty*harga),0)) - SUM(IF(tgl_jurnalkoreksi<'$sampai' AND status_dk='D' AND kode_akun='2-1200'
        OR tgl_jurnalkoreksi<'$sampai' AND status_dk='D' AND kode_akun='2-1300' ,(qty*harga),0)))  as jmlpenyesuaian
        FROM jurnal_koreksi jk
        GROUP BY nobukti_pembelian
        ) jk ON jk.nobukti_pembelian = detail_pembelian.nobukti_pembelian
        WHERE tgl_pembelian <='$sampai'
        GROUP BY detail_pembelian.nobukti_pembelian,pembelian.kode_supplier,nama_supplier,hb.jmlbayar,jk.jmlpenyesuaian,tgl_pembelian
        ORDER BY pembelian.kode_supplier ASC
        ) as kp WHERE sisahutang !=0");
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Analisa Umur Hutang.xls");
        }
        return view('pembelian.laporan.cetak_auh', compact('sampai', 'pmb'));
    }

    public function bahankemasan()
    {
        return view('pembelian.laporan.frm.lap_bahankemasan');
    }

    public function cetak_bahankemasan(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $jenis_barang = $request->jenis_barang;
        $query = Detailpembelian::query();
        $query->selectRaw("detail_pembelian.kode_barang,satuan,nama_barang,jenis_barang,SUM(qty) as totalqty,SUM((qty*harga)+penyesuaian) as totalharga");
        $query->join('pembelian', 'detail_pembelian.nobukti_pembelian', '=', 'pembelian.nobukti_pembelian');
        $query->join('supplier', 'pembelian.kode_supplier', '=', 'supplier.kode_supplier');
        $query->join('departemen', 'pembelian.kode_dept', '=', 'departemen.kode_dept');
        $query->join('coa', 'detail_pembelian.kode_akun', '=', 'coa.kode_akun');
        $query->join('master_barang_pembelian', 'detail_pembelian.kode_barang', '=', 'master_barang_pembelian.kode_barang');
        $query->whereBetween('tgl_pembelian', [$dari, $sampai]);
        if ($jenis_barang == "BAHAN") {
            $query->where('jenis_barang', 'BAHAN BAKU');
            $query->orWhereBetween('tgl_pembelian', [$dari, $sampai]);
            $query->where('jenis_barang', 'BAHAN TAMBAHAN');
        } else if ($jenis_barang == "KEMASAN") {
            $query->where('jenis_barang', 'KEMASAN');
        } else {
            $query->where('jenis_barang', 'BAHAN BAKU');
            $query->orWhereBetween('tgl_pembelian', [$dari, $sampai]);
            $query->where('jenis_barang', 'BAHAN TAMBAHAN');
            $query->orWhereBetween('tgl_pembelian', [$dari, $sampai]);
            $query->where('jenis_barang', 'KEMASAN');
        }
        $query->groupByRaw("detail_pembelian.kode_barang,satuan,nama_barang,jenis_barang");
        $pmb = $query->get();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Bahan Kemasan $dari-$sampai.xls");
        }
        return view('pembelian.laporan.cetak_bahankemasan', compact('dari', 'sampai', 'pmb', 'jenis_barang'));
    }

    public function rekapbahankemasan()
    {
        $jenis_barang = ['KEMASAN', 'BAHAN BAKU', 'Bahan Tambahan'];
        $supplier = Supplier::orderBy('kode_supplier')->get();
        $barang = Barangpembelian::whereIn('jenis_barang', $jenis_barang)->get();
        return view('pembelian.laporan.frm.lap_rekapbahankemasan', compact('supplier', 'barang'));
    }

    public function cetak_rekapbahankemasan(Request $request)
    {

        $dari = $request->dari;
        $sampai = $request->sampai;
        $kode_barang = $request->kode_barang;
        $kode_supplier = $request->kode_supplier;

        $barang = Barangpembelian::where('kode_barang', $kode_barang)->first();
        $supplier = Supplier::where('kode_supplier', $kode_supplier)->first();
        $query = Detailpembelian::query();
        $query->select(
            'detail_pembelian.nobukti_pembelian',
            'tgl_pembelian',
            'pembelian.kode_supplier',
            'nama_supplier',
            'nama_barang',
            'qty',
            'harga',
            'penyesuaian'
        );
        $query->join('pembelian', 'detail_pembelian.nobukti_pembelian', '=', 'pembelian.nobukti_pembelian');
        $query->join('supplier', 'pembelian.kode_supplier', '=', 'supplier.kode_supplier');
        $query->join('master_barang_pembelian', 'detail_pembelian.kode_barang', '=', 'master_barang_pembelian.kode_barang');
        $query->whereBetween('tgl_pembelian', [$dari, $sampai]);
        $query->where('detail_pembelian.kode_barang', $kode_barang);
        $query->orderBy('pembelian.kode_supplier');
        $query->orderBy('tgl_pembelian');
        $pmb = $query->get();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekap Bahan Kemasan $dari-$sampai.xls");
        }
        return view('pembelian.laporan.cetak_rekapbahankemasan', compact('dari', 'sampai', 'barang', 'supplier', 'pmb'));
    }

    public function jurnalkoreksi()
    {
        return view('pembelian.laporan.frm.lap_jurnalkoreksi');
    }

    public function cetak_jurnalkoreksi(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $query = Jurnalkoreksi::query();
        $query->leftJoin('pembelian', 'jurnal_koreksi.nobukti_pembelian', '=', 'pembelian.nobukti_pembelian');
        $query->leftJoin('supplier', 'pembelian.kode_supplier', '=', 'supplier.kode_supplier');
        $query->leftJoin('master_barang_pembelian', 'jurnal_koreksi.kode_barang', '=', 'master_barang_pembelian.kode_barang');
        $query->leftJoin('coa', 'jurnal_koreksi.kode_akun', '=', 'coa.kode_akun');
        $query->whereBetween('tgl_jurnalkoreksi', [$dari, $sampai]);
        $jurnalkoreksi = $query->get();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Jurnal Koreksi $dari-$sampai.xls");
        }
        return view('pembelian.laporan.cetak_jurnalkoreksi', compact('dari', 'sampai', 'jurnalkoreksi'));
    }

    public function rekapakun()
    {
        return view('pembelian.laporan.frm.lap_rekapakun');
    }

    public function cetak_rekapakun(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $ppn = $request->ppn;

        $query = Detailpembelian::query();
        $query->selectRaw("detail_pembelian.kode_akun AS kode_akun,jk.jurnaldebet,jk.jurnalkredit,coa.nama_akun,status,SUM((qty*harga)+penyesuaian) as total");
        $query->join('pembelian', 'detail_pembelian.nobukti_pembelian', '=', 'pembelian.nobukti_pembelian');
        $query->leftJoin('coa', 'detail_pembelian.kode_akun', '=', 'coa.kode_akun');
        $query->leftJoin(
            DB::raw("(
                SELECT kode_akun,
                SUM(IF(status_dk='D',(jurnal_koreksi.qty*jurnal_koreksi.harga),0)) as jurnaldebet,
                SUM(IF(status_dk='K',(jurnal_koreksi.qty*jurnal_koreksi.harga),0)) as jurnalkredit
                FROM jurnal_koreksi
                WHERE tgl_jurnalkoreksi BETWEEN '$dari' AND '$sampai'
                GROUP BY kode_akun
            ) jk"),
            function ($join) {
                $join->on('detail_pembelian.kode_akun', '=', 'jk.kode_akun');
            }
        );

        $query->whereBetween('tgl_pembelian', [$dari, $sampai]);
        if ($request->ppn != "-") {
            $query->where('pembelian.ppn', $request->ppn);
        }
        $query->groupByRaw("detail_pembelian.kode_akun,jk.jurnaldebet,jk.jurnalkredit,coa.nama_akun,status");
        $query->orderBy('detail_pembelian.kode_akun');
        $pmb = $query->get();




        // $query2 = Jurnalkoreksi::query();
        // $query2->selectRaw("jurnal_koreksi.kode_akun,nama_akun,
        // SUM(IF(status_dk='D',(jurnal_koreksi.qty*jurnal_koreksi.harga),0)) as jurnaldebet,
        // SUM(IF(status_dk='K',(jurnal_koreksi.qty*jurnal_koreksi.harga),0)) as jurnalkredit,
        // pmb,
        // pnj");
        // $query2->join('coa', 'jurnal_koreksi.kode_akun', '=', 'coa.kode_akun');
        // $query2->leftJoin(
        //     DB::raw("(
        //         SELECT pembelian.kode_akun,
        //         SUM(IF( STATUS = 'PMB',( detail_pembelian.qty * detail_pembelian.harga ) + penyesuaian, 0 )) AS pmb,
        //         SUM(IF( STATUS = 'PNJ',( detail_pembelian.qty * detail_pembelian.harga ) + penyesuaian, 0 )) AS pnj
        //         FROM pembelian
        //         INNER JOIN detail_pembelian ON pembelian.nobukti_pembelian=detail_pembelian.nobukti_pembelian
        //         WHERE tgl_pembelian BETWEEN '$dari' AND '$sampai'
        //         GROUP BY kode_akun
        //     ) dp"),
        //     function ($join) {
        //         $join->on('jurnal_koreksi.kode_akun', '=', 'dp.kode_akun');
        //     }
        // );
        // $query2->whereBetween('tgl_jurnalkoreksi', [$dari, $sampai]);
        // $query2->groupByRaw(' kode_akun,nama_akun,pnj,pmb');
        // $jurnalkoreksi = $query2->get();

        $hutang = DB::table('detail_pembelian')
            ->selectRaw("pembelian.kode_akun,nama_akun,IFNULL(jurnaldebet,0) as jurnaldebet,
            IFNULL(jurnalkredit,0) as jurnalkredit,
            SUM(IF( STATUS = 'PMB',( detail_pembelian.qty * detail_pembelian.harga ) + penyesuaian, 0 )) AS pmb,
            SUM(IF( STATUS = 'PNJ',( detail_pembelian.qty * detail_pembelian.harga ) + penyesuaian, 0 )) AS pnj")
            ->leftjoin('pembelian', 'detail_pembelian.nobukti_pembelian', '=', 'pembelian.nobukti_pembelian')
            ->leftjoin('coa', 'pembelian.kode_akun', '=', 'coa.kode_akun')
            ->leftJoin(
                DB::raw("(
                    SELECT kode_akun,
                    SUM(IF(status_dk='D',(jurnal_koreksi.qty*jurnal_koreksi.harga),0)) as jurnaldebet,
                    SUM(IF(status_dk='K',(jurnal_koreksi.qty*jurnal_koreksi.harga),0)) as jurnalkredit
                    FROM jurnal_koreksi
                    WHERE tgl_jurnalkoreksi BETWEEN '$dari' AND '$sampai'
                    GROUP BY kode_akun
                ) jk"),
                function ($join) {
                    $join->on('pembelian.kode_akun', '=', 'jk.kode_akun');
                }
            )
            ->whereBetween('tgl_pembelian', [$dari, $sampai])
            ->groupByRaw('pembelian.kode_akun,nama_akun,jurnaldebet,jurnalkredit')
            ->get();


        $akunpembelian = DB::table('detail_pembelian')
            ->select('detail_pembelian.kode_akun')
            ->join('pembelian', 'detail_pembelian.kode_akun', '=', 'pembelian.kode_akun')
            ->whereBetween('tgl_pembelian', [$dari, $sampai])
            ->groupBy('detail_pembelian.kode_akun')
            ->get();

        $akun_pmb = [];
        foreach ($akunpembelian as $d) {
            $akun_pmb[] = $d->kode_akun;
        }
        $jurnalkoreksi = DB::table('jurnal_koreksi')
            ->selectRaw("jurnal_koreksi.kode_akun,nama_akun,
            SUM(IF(status_dk='K',qty*harga,0)) as jurnalkredit,
            SUM(IF(status_dk='D',qty*harga,0)) as jurnaldebet")
            ->join('coa', 'jurnal_koreksi.kode_akun', 'coa.kode_akun')
            ->whereNotIn('jurnal_koreksi.kode_akun', $akun_pmb)
            ->whereNotIn('jurnal_koreksi.kode_akun', ['2-1200', '2-1300'])
            ->whereBetween('tgl_jurnalkoreksi', [$dari, $sampai])
            ->groupByRaw('jurnal_koreksi.kode_akun,nama_akun')
            ->get();



        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekap Akun $dari-$sampai.xls");
        }
        return view('pembelian.laporan.cetak_rekapakun', compact('dari', 'sampai', 'pmb', 'hutang'));
    }

    public function rekapkontrabon()
    {
        return view('pembelian.laporan.frm.lap_rekapkontrabon');
    }

    public function cetak_rekapkontrabon(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $query = Detailkontrabon::query();
        $query->selectRaw("no_dokumen,nama_supplier,SUM(jmlbayar) as jumlah,ppn,norekening");
        $query->leftJoin('kontrabon', 'detail_kontrabon.no_kontrabon', '=', 'kontrabon.no_kontrabon');
        $query->leftJoin('supplier', 'kontrabon.kode_supplier', '=', 'supplier.kode_supplier');
        $query->leftJoin('pembelian', 'detail_kontrabon.nobukti_pembelian', '=', 'pembelian.nobukti_pembelian');
        $query->whereBetween('tgl_kontrabon', [$dari, $sampai]);
        $query->where('ppn', 0);
        $query->groupByRaw("detail_kontrabon.no_kontrabon,no_dokumen,nama_supplier,ppn,norekening");
        $kb = $query->get();
        $query2 = Detailkontrabon::query();
        $query2->selectRaw("no_dokumen,nama_supplier,SUM(jmlbayar) as jumlah,ppn,norekening");
        $query2->leftJoin('kontrabon', 'detail_kontrabon.no_kontrabon', '=', 'kontrabon.no_kontrabon');
        $query2->leftJoin('supplier', 'kontrabon.kode_supplier', '=', 'supplier.kode_supplier');
        $query2->leftJoin('pembelian', 'detail_kontrabon.nobukti_pembelian', '=', 'pembelian.nobukti_pembelian');
        $query2->whereBetween('tgl_kontrabon', [$dari, $sampai]);
        $query2->where('ppn', 1);
        $query2->groupByRaw("detail_kontrabon.no_kontrabon,no_dokumen,nama_supplier,ppn,norekening");
        $pf = $query2->get();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekap Kontrabon $dari-$sampai.xls");
        }
        return view('pembelian.laporan.cetak_rekapkontrabon', compact('dari', 'sampai', 'kb', 'pf'));
    }
}
