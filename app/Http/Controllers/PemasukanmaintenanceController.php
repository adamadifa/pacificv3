<?php

namespace App\Http\Controllers;

use App\Models\Pemasukanmaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class PemasukanmaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Pemasukanmaintenance::query();
        if (!empty($request->nobukti_pemasukan)) {
            $query->where('nobukti_pemasukan', $request->nobukti_pemasukan);
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_pemasukan', [$request->dari, $request->sampai]);
        }
        $query->select('pemasukan_bb.nobukti_pemasukan', 'tgl_pembelian', 'pemasukan_bb.kode_supplier', 'nama_supplier');
        $query->leftjoin('supplier', 'pemasukan_bb.kode_supplier', '=', 'supplier.kode_supplier');
        $query->orderBy('tgl_pemasukan', 'desc');
        $pemasukanmtc  = $query->paginate(15);

        return view('pemasukanmtc.index', compact('pemasukanmtc'));
    }

    public function show(Request $request)
    {
        $nobukti_pemasukan = Crypt::decrypt($request->nobukti_pemasukan);
        $pemasukanmtc = DB::table('pemasukan_bb')->where('nobukti_pemasukan', $nobukti_pemasukan)->first();
        $detail = DB::table('detail_pemasukan_bb')
            ->select('detail_pemasukan_bb.*', 'nama_barang', 'satuan')
            ->join('master_barang_pembelian', 'detail_pemasukan_bb.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->join('supplier', 'pemasukan_bb.kode_supplier', '=', 'supplier.kode_supplier')
            ->where('nobukti_pemasukan', $nobukti_pemasukan)->get();
        return view('pemasukanmtc.show', compact('detail', 'pemasukanmtc'));
    }
}