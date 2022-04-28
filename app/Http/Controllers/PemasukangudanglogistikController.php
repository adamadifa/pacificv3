<?php

namespace App\Http\Controllers;

use App\Models\Pemasukangudanglogistik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PemasukangudanglogistikController extends Controller
{
    public function index(Request $request)
    {
        $query = Pemasukangudanglogistik::query();
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_pemasukan', [$request->dari, $request->sampai]);
        }

        if (!empty($request->nobukti_pemasukan)) {
            $query->where('nobukti_pemasukan', $request->nobukti_pemasukan);
        }

        $query->orderBy('tgl_pemasukan', 'desc');
        $pemasukan = $query->paginate(15);
        $pemasukan->appends($request->all());

        return view('pemasukangudanglogistik.index', compact('pemasukan'));
    }

    public function show(Request $request)
    {
        $nobukti_pemasukan = Crypt::decrypt($request->nobukti_pemasukan);
        $pemasukan = DB::table('pemasukan')->where('nobukti_pemasukan', $nobukti_pemasukan)
            ->select('pemasukan.*', 'pembelian.kode_supplier', 'nama_supplier')
            ->leftJoin('pembelian', 'pemasukan.nobukti_pemasukan', '=', 'pembelian.nobukti_pembelian')
            ->leftJoin('supplier', 'pembelian.kode_supplier', '=', 'supplier.kode_supplier')
            ->first();
        $detail = DB::table('detail_pemasukan')
            ->select('detail_pemasukan.*', 'nama_barang', 'satuan')
            ->join('master_barang_pembelian', 'detail_pemasukan.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('nobukti_pemasukan', $nobukti_pemasukan)->get();
        return view('pemasukangudanglogistik.show', compact('detail', 'pemasukan'));
    }

    public function delete($nobukti_pemasukan)
    {
        $nobukti_pemasukan  = Crypt::decrypt($nobukti_pemasukan);
        $hapus = DB::table('pemasukan')->where('nobukti_pemasukan', $nobukti_pemasukan)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dibatalkan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dibatalkan, Hubungi Tim IT']);
        }
    }
}
