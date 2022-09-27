<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Pasar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class Pasarcontroller extends Controller
{
    public function index(Request $request)
    {
        $query = Pasar::query();
        if (!empty($request->nama_pasar)) {
            $query->where('nama_pasar', 'like', '%' . $request->nama_pasar . '%');
        }
        if (Auth::user()->kode_cabang == "PCF") {
            if (!empty($request->kode_cabang)) {
                $query->where('kode_cabang', $request->kode_cabang);
            }
        } else {
            // if (Auth::user()->kode_cabang == "GRT") {
            //     $query->where('kode_cabang', 'TSM');
            // } else {
            //     $query->where('kode_cabang', Auth::user()->kode_cabang);
            // }

            $query->where('kode_cabang', Auth::user()->kode_cabang);
        }
        $query->orderBy('id', 'desc');

        $pasar = $query->paginate(15);
        $pasar->appends($request->all());

        if (Auth::user()->kode_cabang != 'PCF') {
            if (Auth::user()->kode_cabang == 'GRT') {
                $cabang = Cabang::orderBy('kode_cabang')->where('kode_cabang', 'TSM')->get();
            } else {
                $cabang = Cabang::orderBy('kode_cabang')->where('kode_cabang', Auth::user()->kode_cabang)->get();
            }
            $option = "Pilih Cabang";
        } else {
            $cabang = Cabang::orderBy('kode_cabang')->get();
            $option = "Semua Cabang";
        }
        return view('pasar.index', compact('pasar', 'cabang', 'option'));
    }

    public function create()
    {
        if (Auth::user()->kode_cabang != 'PCF') {
            // if (Auth::user()->kode_cabang == 'GRT') {
            //     $cabang = Cabang::orderBy('kode_cabang')->where('kode_cabang', 'TSM')->get();
            // } else {
            //     $cabang = Cabang::orderBy('kode_cabang')->where('kode_cabang', Auth::user()->kode_cabang)->get();
            // }
            $cbg = new Cabang();
            $cabang = $cbg->getCabang($this->cabang);
            $option = "Pilih Cabang";
        } else {
            $cabang = Cabang::orderBy('kode_cabang')->get();
            $option = "Semua Cabang";
        }
        return view('pasar.create', compact('cabang'));
    }

    public function store(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $nama_pasar = $request->nama_pasar;
        $data = [
            'kode_cabang' => $kode_cabang,
            'nama_pasar' => $nama_pasar
        ];
        $simpan = DB::table('master_pasar')->insert($data);

        if ($simpan) {
            return redirect('/pasar')->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return redirect('/pasar')->with(['warning' => 'Data Gagal Disimpan ,Hubungi Tim IT']);
        }
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $hapus = DB::table('master_pasar')->where('id', $id)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus ,Hubungi Tim IT']);
        }
    }
}
