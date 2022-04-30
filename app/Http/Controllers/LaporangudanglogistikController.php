<?php

namespace App\Http\Controllers;

use App\Models\Detailpemasukangudanglogistik;
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
        return view('gudanglogistik.laporan.cetak_pemasukan', compact('dari', 'sampai', 'pemasukan', 'kategori', 'barang'));
    }

    public function pengeluaran()
    {
        return view('gudanglogistik.laporan.frm.pengeluaran');
    }
}