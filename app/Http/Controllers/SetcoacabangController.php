<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Coa;
use App\Models\Setcoacabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class SetcoacabangController extends Controller
{
    public function index(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $kategori = $request->kategori;
        $query = Setcoacabang::query();
        $query->join('coa', 'set_coa_cabang.kode_akun', '=', 'coa.kode_akun');
        $query->where('kode_cabang', $kode_cabang);
        $query->where('kategori', $kategori);
        $query->orderBy('set_coa_cabang.kode_akun');
        $setcoa = $query->get();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('setcoacabang.index', compact('setcoa', 'cabang'));
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $hapus = DB::table('set_coa_cabang')->where('id_setakuncabang', $id)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['success' => 'Data Gagal Dihapus, Hubungi Tim IT']);
        }
    }

    public function create()
    {
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $coa = Coa::orderBy('kode_akun')->get();
        return view('setcoacabang.create', compact('cabang', 'coa'));
    }

    public function store(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $kategori = $request->kategori;
        $kode_akun = $request->kode_akun;

        $data = [
            'kode_cabang' => $kode_cabang,
            'kategori' => $kategori,
            'kode_akun' => $kode_akun
        ];

        $cek = DB::table('set_coa_cabang')->where('kode_cabang', $kode_cabang)->where('kategori', $kategori)->where('kode_akun', $kode_akun)->count();
        if ($cek > 0) {
            return Redirect::back()->with(['warning' => 'Data Sudah Ada ']);
        } else {
            $simpan = DB::table('set_coa_cabang')->insert($data);
            if ($simpan) {
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } else {
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
            }
        }
    }
}
