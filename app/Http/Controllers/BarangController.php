<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Harga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class BarangController extends Controller
{
    public function index()
    {
        $barang = DB::table('master_barang')->orderBy('urutan', 'asc')->get();

        return view('barang.index', compact('barang'));
    }

    public function create()
    {
        $kategorikomisi = DB::table('master_barang')
            ->select('kategori_komisi')
            ->whereNotNull('kategori_komisi')
            ->groupBy('kategori_komisi')
            ->get();
        return view('barang.create', compact('kategorikomisi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_produk' => 'required',
            'nama_barang' => 'required',
            'kategori_jenisproduk' => 'required',
            'jenis_produk' => 'required',
            'satuan' => 'required',
            'kategori_komisi' => 'required',
            'isipcsdus' => 'required|numeric',
            'isipack' => 'required|numeric',
            'isipcs' => 'required|numeric',
        ]);
        $urut = DB::table('master_barang')
            ->select('urutan')
            ->orderBy('urutan', 'desc')
            ->first();
        $no_urut = $urut->urutan + 1;
        $simpan = DB::table('master_barang')->insert([
            'kode_produk' => $request->kode_produk,
            'nama_barang' => $request->nama_barang,
            'kategori_jenisproduk' => $request->kategori_jenisproduk,
            'jenis_produk' => $request->jenis_produk,
            'satuan' => $request->satuan,
            'kategori_komisi' => $request->kategori_komisi,
            'isipcsdus' => $request->isipcsdus,
            'isipack' => $request->isipack,
            'isipcs' => $request->isipcs,
            'urutan' => $no_urut
        ]);

        if ($simpan) {
            return redirect('/barang')->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return redirect('/barang')->with(['warning' => 'Data Gagal Disimpan']);
        }
    }


    public function edit($kode_produk)
    {
        $kode_produk = Crypt::decrypt($kode_produk);
        $data = DB::table('master_barang')->where('kode_produk', $kode_produk)->first();
        $kategorikomisi = DB::table('master_barang')
            ->select('kategori_komisi')
            ->whereNotNull('kategori_komisi')
            ->groupBy('kategori_komisi')
            ->get();
        return view('barang.edit', compact('data', 'kategorikomisi'));
    }

    public function update(Request $request, $kode_produk)
    {
        $kode_produk = Crypt::decrypt($kode_produk);
        $request->validate([
            'kode_produk' => 'required',
            'nama_barang' => 'required',
            'kategori_jenisproduk' => 'required',
            'jenis_produk' => 'required',
            'satuan' => 'required',
            'kategori_komisi' => 'required',
            'isipcsdus' => 'required|numeric',
            'isipack' => 'required|numeric',
            'isipcs' => 'required|numeric',
        ]);

        $simpan = DB::table('master_barang')
            ->where('kode_produk', $kode_produk)
            ->update([
                'nama_barang' => $request->nama_barang,
                'kategori_jenisproduk' => $request->kategori_jenisproduk,
                'jenis_produk' => $request->jenis_produk,
                'satuan' => $request->satuan,
                'kategori_komisi' => $request->kategori_komisi,
                'isipcsdus' => $request->isipcsdus,
                'isipack' => $request->isipack,
                'isipcs' => $request->isipcs,
            ]);

        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
        }
    }

    public function delete($kode_produk)
    {
        $kode_produk = Crypt::decrypt($kode_produk);
        $hapus = DB::table('master_barang')
            ->where('kode_produk', $kode_produk)
            ->delete();

        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }
}
