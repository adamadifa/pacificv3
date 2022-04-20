<?php

namespace App\Http\Controllers;

use App\Models\Detailkontrabon;
use App\Models\Detailpembelian;
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
        if ($sortby == "supplier") {
            return view('pembelian.laporan.cetak_rekappembelian_supplier', compact('dari', 'sampai', 'pmb', 'jenis_barang'));
        } else {
            return view('pembelian.laporan.cetak_rekappembelian_jenisbarang', compact('dari', 'sampai', 'pmb', 'jenis_barang'));
        }
    }
}
