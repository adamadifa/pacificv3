<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use PDOException;

class CabangController extends Controller
{
    public function index()
    {
        $cabang = Cabang::orderBy('kode_cabang', 'asc')->get();
        return view('cabang.index', compact('cabang'));
    }

    public function create()
    {
        return view('cabang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_cabang' => 'required|max:3|min:3',
            'nama_cabang' => 'required',
            'alamat_cabang' => 'required',
            'telepon' => 'required|numeric'
        ]);

        $simpan = DB::table('cabang')
            ->insert([
                'kode_cabang' => $request->kode_cabang,
                'nama_cabang' => $request->nama_cabang,
                'alamat_cabang' => $request->alamat_cabang,
                'telepon' => $request->telepon
            ]);

        if ($simpan) {
            return redirect('/cabang')->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return redirect('/cabang')->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function update(Request $request, $kode_cabang)
    {
        $kode_cabang = Crypt::decrypt($kode_cabang);
        $request->validate([
            'nama_cabang' => 'required',
            'alamat_cabang' => 'required',
            'telepon' => 'required|numeric'
        ]);

        $simpan = DB::table('cabang')
            ->where('kode_cabang', $kode_cabang)
            ->update([
                'nama_cabang' => $request->nama_cabang,
                'alamat_cabang' => $request->alamat_cabang,
                'telepon' => $request->telepon
            ]);

        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
        }
    }

    public function edit($kode_cabang)
    {
        $kode_cabang = Crypt::decrypt($kode_cabang);
        $data = Cabang::where('kode_cabang', $kode_cabang)->first();
        return view('cabang.edit', compact('data'));
    }

    public function delete($kode_cabang)
    {
        $kode_cabang = Crypt::decrypt($kode_cabang);

        try {
            $hapus = DB::table('cabang')
                ->where('kode_cabang', $kode_cabang)
                ->delete();

            if ($hapus) {
                return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
            } else {
                return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
            }
        } catch (PDOException $e) {
            $errorcode = $e->getCode();
            if ($errorcode == 23000) {
                return Redirect::back()->with(['warning' => 'Data Tidak Dapat Dihapus Karena Sudah Memiliki Transaksi']);
            }
        }
    }


    public function getcabang(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $cabang = Cabang::where('kode_cabang', $kode_cabang)->get();
        //$type = $request->type;
        echo "<option value=''>Pilih Cabang</option>";
        foreach ($cabang as $d) {
            if ($kode_cabang == $d->kode_cabang) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            echo "<option $selected value='$d->kode_cabang'>$d->nama_cabang</option>";
        }
    }
}
