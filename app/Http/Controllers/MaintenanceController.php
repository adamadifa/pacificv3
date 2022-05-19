<?php

namespace App\Http\Controllers;

use App\Models\Detailpembelian;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class MaintenanceController extends Controller
{
    public function pembelian(Request $request)
    {
        $query = Detailpembelian::query();
        if (!empty($request->nobukti_pembelian)) {
            $query->where('detail_pembelian.nobukti_pembelian', $request->nobukti_pembelian);
        }
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('pembelian.tgl_pembelian', [$request->dari, $request->sampai]);
        }
        $query->select('detail_pembelian.nobukti_pembelian', 'pembelian.tgl_pembelian', 'nama_supplier', 'kode_dept', 'pemasukan_bb.nobukti_pemasukan as cek');
        $query->join('pembelian', 'detail_pembelian.nobukti_pembelian', '=', 'pembelian.nobukti_pembelian');
        $query->join('supplier', 'pembelian.kode_supplier', '=', 'supplier.kode_supplier');
        $query->leftJoin('pemasukan_bb', 'detail_pembelian.nobukti_pembelian', '=', 'pemasukan_bb.nobukti_pemasukan');
        $query->where('pembelian.kode_dept', 'GAF');
        $query->where('detail_pembelian.kode_akun', '1-1505');
        $query->where('pembelian.tgl_pembelian', '>', '2021-02-01');
        $query->whereNull('pemasukan_bb.nobukti_pemasukan');
        $query->orderBy('pembelian.tgl_pembelian', 'desc');
        $query->orderBy('nobukti_pembelian', 'desc');
        $query->groupByRaw('detail_pembelian.nobukti_pembelian,pembelian.tgl_pembelian,nama_supplier,kode_dept,pemasukan_bb.nobukti_pemasukan');
        $pembelian = $query->paginate(15);

        return view('maintenance.pembelian', compact('pembelian'));
    }

    public function showpembelian(Request $request)
    {
        $nobukti_pembelian = $request->nobukti_pembelian;
        $pembelian = DB::table('pembelian')
            ->select('pembelian.*', 'nama_supplier', 'nama_dept')
            ->join('supplier', 'pembelian.kode_supplier', '=', 'supplier.kode_supplier')
            ->join('departemen', 'pembelian.kode_dept', '=', 'departemen.kode_dept')
            ->where('nobukti_pembelian', $nobukti_pembelian)
            ->first();
        $detailpembelian = DB::table('detail_pembelian')
            ->select('detail_pembelian.*', 'nama_barang')
            ->join('master_barang_pembelian', 'detail_pembelian.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('nobukti_pembelian', $nobukti_pembelian)
            ->where('detail_pembelian.status', 'PMB')
            ->get();

        return view('maintenance.showpembelian', compact('pembelian', 'detailpembelian'));
    }

    public function storepembelian($nobukti_pembelian)
    {
        $nobukti_pembelian = Crypt::decrypt($nobukti_pembelian);
        $pembelian = DB::table('pembelian')->where('nobukti_pembelian', $nobukti_pembelian)->first();
        $data = [
            'nobukti_pemasukan' => $nobukti_pembelian,
            'tgl_pemasukan' => $pembelian->tgl_pembelian,
            'tgl_pembelian' => $pembelian->tgl_pembelian,
            'status' => 1,
            'kode_supplier' => $pembelian->kode_supplier
        ];
        $detail = DB::table('detail_pembelian')->where('nobukti_pembelian', $nobukti_pembelian)->get();
        DB::beginTransaction();
        try {
            DB::table('pemasukan_bb')->insert($data);
            foreach ($detail as $d) {
                $data_detail = [
                    'nobukti_pemasukan' => $nobukti_pembelian,
                    'kode_barang' => $d->kode_barang,
                    'qty' => $d->qty,
                    'penyesuaian' => $d->penyesuaian,
                    'keterangan' => $d->keterangan
                ];

                DB::table('detail_pemasukan_bb')->insert($data_detail);
            }
            DB::commit();
            return Redirect::back()->with(['success' => 'Data  Berhasil di Simpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data  Gagal di Simpan, Hubungi Tim IT']);
        }
    }
}