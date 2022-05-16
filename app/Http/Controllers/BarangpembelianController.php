<?php

namespace App\Http\Controllers;

use App\Models\Barangpembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Facades\DataTables;

class BarangpembelianController extends Controller
{
    public function index(Request $request)
    {
        $query = Barangpembelian::query();
        $query->select('master_barang_pembelian.*', 'kategori', 'nama_dept');
        $query->join('kategori_barang_pembelian', 'master_barang_pembelian.kode_kategori', '=', 'kategori_barang_pembelian.kode_kategori');
        $query->join('departemen', 'master_barang_pembelian.kode_dept', '=', 'departemen.kode_dept');
        if (!empty($request->jenis_barang)) {
            $query->where('jenis_barang', $request->jenis_barang);
        }

        if (!empty($request->kode_kategori)) {
            $query->where('master_barang_pembelian.kode_kategori', $request->kode_kategori);
        }
        if (Auth::user()->level == "admin gudang logistik") {
            $query->where('master_barang_pembelian.kode_dept', 'GDL');
        } elseif (Auth::user()->level == "admin gudang bahan") {
            $query->where('master_barang_pembelian.kode_dept', 'GDB');
        } elseif (Auth::user()->level == "general affair") {
            $query->where('master_barang_pembelian.kode_dept', 'GAF');
        } else {

            if (!empty($request->kode_dept)) {
                $query->where('master_barang_pembelian.kode_dept', $request->kode_dept);
            }
        }


        if ($request->nama_barang != "") {
            $query->where('nama_barang', 'like', '%' . $request->nama_barang . '%');
        }
        $barang_pembelian = $query->paginate(15);
        $barang_pembelian->appends($request->all());

        if (Auth::user()->level == "admin gudang logistik") {
            $kategori_barang_pembelian = DB::table('kategori_barang_pembelian')->where('kode_dept', 'GDL')->orderBy('kategori')->get();
        } else if (Auth::user()->level == "admin gudang bahan") {
            $kategori_barang_pembelian = DB::table('kategori_barang_pembelian')->where('kode_dept', 'GDB')->orderBy('kategori')->get();
        } else if (Auth::user()->level == "general affair") {
            $kategori_barang_pembelian = DB::table('kategori_barang_pembelian')->where('kode_dept', 'GAF')->orderBy('kategori')->get();
        } else {
            $kategori_barang_pembelian = DB::table('kategori_barang_pembelian')->orderBy('kategori')->get();
        }
        $departemen = DB::table('departemen')->where('nama_dept', 'NOT LIKE', '%CABANG%')->orderBy('nama_dept')->get();
        return view('barangpembelian.index', compact('barang_pembelian', 'kategori_barang_pembelian', 'departemen'));
    }

    public function create()
    {
        if (Auth::user()->level == "admin gudang logistik") {
            $kategori_barang_pembelian = DB::table('kategori_barang_pembelian')->where('kode_dept', 'GDL')->orderBy('kategori')->get();
        } else {
            $kategori_barang_pembelian = DB::table('kategori_barang_pembelian')->orderBy('kategori')->get();
        }
        $departemen = DB::table('departemen')->where('nama_dept', 'NOT LIKE', '%CABANG%')->orderBy('nama_dept')->get();
        return view('barangpembelian.create', compact('kategori_barang_pembelian', 'departemen'));
    }

    public function store(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $nama_barang = $request->nama_barang;
        $satuan = $request->satuan;
        $jenis_barang = $request->jenis_barang;
        $kode_kategori = $request->kode_kategori;
        $kode_dept = $request->kode_dept;

        $data = [
            'kode_barang' => $kode_barang,
            'nama_barang' => $nama_barang,
            'satuan' => $satuan,
            'jenis_barang' => $jenis_barang,
            'kode_kategori' => $kode_kategori,
            'kode_dept' => $kode_dept,
            'status' => 'Aktif'
        ];

        $cek = DB::table('master_barang_pembelian')->where('kode_barang', $kode_barang)->count();
        if (!empty($cek)) {
            return Redirect::back()->with(['warning' => 'Kode Barang Sudah Ada']);
        } else {
            $simpan = DB::table('master_barang_pembelian')->insert($data);
            if ($simpan) {
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } else {
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan Hubungi Tim IT']);
            }
        }
    }

    public function delete($kode_barang)
    {
        $kode_barang = Crypt::decrypt($kode_barang);
        $cekbarang = DB::table('detail_pembelian')->where('kode_barang', $kode_barang)->count();
        if (!empty($cekbarang)) {
            return Redirect::back()->with(['warning' => 'Data Barang Tidak Dapat Dihapus Karena Sudah memiliki Transaksi']);
        } else {

            $hapus = DB::table('master_barang_pembelian')->where('kode_barang', $kode_barang)->delete();
            if ($hapus) {
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan ']);
            } else {
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT ']);
            }
        }
    }

    public function edit($kode_barang)
    {
        $barang_pembelian = Barangpembelian::where('kode_barang', $kode_barang)->first();
        $kategori_barang_pembelian = DB::table('kategori_barang_pembelian')->orderBy('kategori')->get();
        $departemen = DB::table('departemen')->where('nama_dept', 'NOT LIKE', '%CABANG%')->orderBy('nama_dept')->get();
        return view('barangpembelian.edit', compact('barang_pembelian', 'kategori_barang_pembelian', 'departemen'));
    }

    public function update(Request $request, $kode_barang)
    {
        $kode_barang = Crypt::decrypt($kode_barang);
        $nama_barang = $request->nama_barang;
        $satuan = $request->satuan;
        $jenis_barang = $request->jenis_barang;
        $kode_kategori = $request->kode_kategori;
        $kode_dept = $request->kode_dept;
        $status = $request->status;
        $data = [
            'nama_barang' => $nama_barang,
            'satuan' => $satuan,
            'jenis_barang' => $jenis_barang,
            'kode_kategori' => $kode_kategori,
            'kode_dept' => $kode_dept,
            'status' => $status
        ];

        $update = DB::table('master_barang_pembelian')->where('kode_barang', $kode_barang)->update($data);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate, Hubungi Tim IT !']);
        }
    }

    public function getbarang($kode_dept)
    {
        return view('barangpembelian.getbarang', compact('kode_dept'));
    }

    public function json($kode_dept)
    {

        $query = Barangpembelian::query();
        $query->where('kode_dept', $kode_dept);
        $barang = $query;
        return DataTables::of($barang)
            ->addColumn('action', function ($barang) {
                return '<a href="#"
                kode_barang="' . $barang->kode_barang . '" nama_barang ="' . $barang->nama_barang . '"
                jenis_barang ="' . $barang->jenis_barang . '"
                ><i class="feather icon-external-link success"></i></a>';
            })
            ->toJson();
    }

    public function getbarangpembelianbykategori(Request $request)
    {
        $kode_kategori = $request->kode_kategori;
        $barang = DB::table('master_barang_pembelian')->where('kode_kategori', $kode_kategori)->orderBy('nama_barang')->get();
        echo "<option value=''>Semua Barang</option>";
        foreach ($barang as $d) {
            echo "<option value='$d->kode_barang'>$d->nama_barang</option>";
        }
    }
}