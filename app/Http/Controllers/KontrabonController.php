<?php

namespace App\Http\Controllers;

use App\Models\Kontrabon;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class KontrabonController extends Controller
{
    public function index(Request $request)
    {
        $query = Kontrabon::query();
        $query->selectRaw("kontrabon.no_kontrabon,no_dokumen,tgl_kontrabon,kategori,nama_supplier,totalbayar,tglbayar,jenisbayar");
        $query->join('supplier', 'kontrabon.kode_supplier', '=', 'supplier.kode_supplier');
        $query->leftJoin('historibayar_pembelian', 'kontrabon.no_kontrabon', '=', 'historibayar_pembelian.no_kontrabon');
        $query->leftJoin(
            DB::raw('(
                SELECT no_kontrabon,SUM(jmlbayar) as totalbayar
                FROM detail_kontrabon
                GROUP BY no_kontrabon
            ) detailkontrabon'),
            function ($join) {
                $join->on('kontrabon.no_kontrabon', '=', 'detailkontrabon.no_kontrabon');
            }
        );

        if (!empty($request->no_kontrabon)) {
            $query->where('kontrabon.no_kontrabon', $request->no_kontrabon);
        }

        if (!empty($request->no_dokumen)) {
            $query->where('kontrabon.no_dokumen', $request->no_dokumen);
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_kontrabon', [$request->dari, $request->sampai]);
        }
        if (!empty($request->kode_supplier)) {
            $query->where('kontrabon.kode_supplier', $request->kode_supplier);
        }

        if (!empty($request->status)) {
            if ($request->status == 1) {
                $query->whereNull('tglbayar');
            } else {
                $query->whereNotNull('tglbayar');
            }
        }

        if (!empty($request->kategori)) {
            $query->where('kategori', $request->kategori);
        }
        $query->orderBy('tgl_kontrabon', 'desc');
        $kontrabon = $query->paginate(15);
        $kontrabon->appends($request->all());
        $supplier = Supplier::orderBy('nama_supplier')->get();
        return view('kontrabon.index', compact('supplier', 'kontrabon'));
    }

    public function show(Request $request)
    {
        $no_kontrabon = Crypt::decrypt($request->no_kontrabon);
        $kontrabon = DB::table('kontrabon')
            ->select('kontrabon.*', 'nama_supplier')
            ->join('supplier', 'kontrabon.kode_supplier', '=', 'supplier.kode_supplier')
            ->where('no_kontrabon', $no_kontrabon)->first();
        $detailkontrabon = DB::table('detail_kontrabon')
            ->select('detail_kontrabon.*', 'tgl_pembelian')
            ->join('pembelian', 'detail_kontrabon.nobukti_pembelian', '=', 'pembelian.nobukti_pembelian')
            ->where('no_kontrabon', $no_kontrabon)->get();
        return view('kontrabon.show', compact('kontrabon', 'detailkontrabon'));
    }

    public function create()
    {
        return view('kontrabon.create');
    }
}
