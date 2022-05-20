<?php

namespace App\Http\Controllers;

use App\Models\Pemasukanmaintenance;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

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
        $query->select('pemasukan_bb.nobukti_pemasukan', 'tgl_pemasukan', 'pemasukan_bb.kode_supplier', 'nama_supplier');
        $query->leftjoin('supplier', 'pemasukan_bb.kode_supplier', '=', 'supplier.kode_supplier');
        $query->orderBy('tgl_pemasukan', 'desc');
        $pemasukanmtc  = $query->paginate(15);

        return view('pemasukanmtc.index', compact('pemasukanmtc'));
    }

    public function show(Request $request)
    {
        $nobukti_pemasukan = Crypt::decrypt($request->nobukti_pemasukan);
        $pemasukanmtc = DB::table('pemasukan_bb')
            ->join('supplier', 'pemasukan_bb.kode_supplier', '=', 'supplier.kode_supplier')
            ->where('nobukti_pemasukan', $nobukti_pemasukan)->first();
        $detail = DB::table('detail_pemasukan_bb')
            ->select('detail_pemasukan_bb.*', 'nama_barang', 'satuan')
            ->join('master_barang_pembelian', 'detail_pemasukan_bb.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('detail_pemasukan_bb.nobukti_pemasukan', $nobukti_pemasukan)->get();
        return view('pemasukanmtc.show', compact('detail', 'pemasukanmtc'));
    }

    public function delete($nobukti_pemasukan)
    {
        $nobukti_pemasukan  = Crypt::decrypt($nobukti_pemasukan);
        $hapus = DB::table('pemasukan_bb')->where('nobukti_pemasukan', $nobukti_pemasukan)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
        }
    }

    public function create()
    {
        $supplier = Supplier::orderBy('nama_supplier')->get();
        return view('pemasukanmtc.create', compact('supplier'));
    }

    public function cektemp()
    {
        $id_admin = Auth::user()->id;
        $cek = DB::table('detailpemasukan_temp_bb')->where('id_admin', $id_admin)->count();
        echo $cek;
    }

    public function getbarang()
    {
        $kode_barang = ['GA-002', 'GA-007', 'GA-558'];
        $barang = DB::table('master_barang_pembelian')
            ->whereIn('kode_barang', $kode_barang)
            ->orderBy('kode_barang')->get();
        return view('pemasukanmtc.getbarang', compact('barang'));
    }

    public function showtemp()
    {
        $id_admin = Auth::user()->id;
        $detail = DB::table('detailpemasukan_temp_bb')
            ->select('detailpemasukan_temp_bb.*', 'nama_barang', 'satuan')
            ->join('master_barang_pembelian', 'detailpemasukan_temp_bb.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('id_admin', $id_admin)->get();
        return view('pemasukanmtc.showtemp', compact('detail'));
    }


    public function storetemp(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $keterangan = $request->keterangan;
        $qty = $request->qty;
        $id_admin = Auth::user()->id;

        $cek = DB::table('detailpemasukan_temp_bb')->where('kode_barang', $kode_barang)->where('id_admin', $id_admin)->count();
        if ($cek > 0) {
            echo 1;
        } else {
            $data = [
                'kode_barang' => $kode_barang,
                'keterangan' => $keterangan,
                'qty' => $qty,
                'id_admin' => $id_admin
            ];
            $simpan = DB::table('detailpemasukan_temp_bb')->insert($data);
            if ($simpan) {
                echo 0;
            } else {
                echo 2;
            }
        }
    }

    public function deletetemp(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $id_admin = Auth::user()->id;
        $hapus = DB::table('detailpemasukan_temp_bb')->where('kode_barang', $kode_barang)->where('id_admin', $id_admin)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }
}