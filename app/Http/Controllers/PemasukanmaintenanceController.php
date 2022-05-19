<?php

namespace App\Http\Controllers;

use App\Models\Pemasukanmaintenance;
use Illuminate\Http\Request;

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
}