<?php

namespace App\Http\Controllers;

use App\Models\Mutasiproduksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class BpbjController extends Controller
{
    public function index(Request $request)
    {
        $query = Mutasiproduksi::query();
        $query->where('jenis_mutasi', 'BPBJ');
        if (!empty($request->tanggal)) {
            $query->where('tgl_mutasi_produksi', $request->tanggal);
        }
        $query->orderBy('tgl_mutasi_produksi', 'desc');
        $query->orderBy('time_stamp', 'desc');
        $bpbj = $query->paginate(15);
        $bpbj->appends($request->all());
        return view('bpbj.index', compact('bpbj'));
    }
    public function show(Request $request)
    {
        $no_mutasi_produksi = Crypt::decrypt($request->no_mutasi_produksi);
        $bpbj = DB::table('mutasi_produksi')->where('no_mutasi_produksi', $no_mutasi_produksi)->first();
        $detail = DB::table('detail_mutasi_produksi')
            ->select('detail_mutasi_produksi.*', 'nama_barang')
            ->join('master_barang', 'detail_mutasi_produksi.kode_produk', '=', 'master_barang.kode_produk')
            ->where('no_mutasi_produksi', $no_mutasi_produksi)
            ->orderBy('shift')
            ->get();
        return view('bpbj.show', compact('bpbj', 'detail'));
    }

    public function delete($no_mutasi_produksi)
    {
        $no_mutasi_produksi = Crypt::decrypt($no_mutasi_produksi);
        $hapus = DB::table('mutasi_produksi')->where('no_mutasi_produksi', $no_mutasi_produksi)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
        }
    }
}
