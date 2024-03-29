<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\Retur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CoaController extends Controller
{
    public function index()
    {
        $coa = DB::table('coa')->orderBy('kode_akun')->get();
        return view('coa.index', compact('coa'));
    }

    public function edit($kode_akun)
    {
        $kode_akun = Crypt::decrypt($kode_akun);
        $akun = DB::table('coa')->where('kode_akun', $kode_akun)->first();
        $coa = Coa::orderBy('kode_akun')->get();
        $kategori = DB::table('kategori_cost_ratio')->orderBy('kode_kategori')->get();
        return view('coa.edit', compact('akun', 'coa', 'kategori'));
    }

    public function store(Request $request)
    {
        $kode_akun = $request->kode_akun;
        $nama_akun = $request->nama_akun;
        $sub_akun = $request->sub_akun;
        $kode_kategori = $request->kode_kategori;
        $cek_subakun = DB::table('coa')->where('kode_akun', $sub_akun)->first();
        $level = $cek_subakun->level + 1;
        $data = [
            'kode_akun' => $kode_akun,
            'nama_akun' => $nama_akun,
            'sub_akun' => $sub_akun,
            'level' => $level,
            'kode_kategori' => $kode_kategori
        ];

        $simpan = DB::table('coa')->insert($data);
        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
        }
    }
    public function delete($kode_akun)
    {
        $kode_akun = Crypt::decrypt($kode_akun);
        $hapus = DB::table('coa')->where('kode_akun', $kode_akun)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
        }
    }

    public function create()
    {
        $coa = Coa::orderBy('kode_akun')->get();
        $kategori = DB::table('kategori_cost_ratio')->orderBy('kode_kategori')->get();
        return view('coa.create', compact('coa', 'kategori'));
    }

    public function update($kode_akun, Request $request)
    {
        $kode_akun = Crypt::decrypt($kode_akun);
        $nama_akun = $request->nama_akun;
        $sub_akun = $request->sub_akun;
        $kode_kategori = $request->kode_kategori;
        $cek_subakun = DB::table('coa')->where('kode_akun', $sub_akun)->first();
        $level = $cek_subakun->level + 1;
        $update = DB::table('coa')->where('kode_akun', $kode_akun)->update([
            'nama_akun' => $nama_akun,
            'sub_akun' => $sub_akun,
            'level' => $level,
            'kode_kategori' => $kode_kategori
        ]);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate, Hubungi Tim IT']);
        }
    }
    public function getcoacabang(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        if ($kode_cabang != "") {

            $coa = DB::table('set_coa_cabang')
                ->select('set_coa_cabang.kode_akun', 'nama_akun')
                ->join('coa', 'set_coa_cabang.kode_akun', '=', 'coa.kode_akun')
                ->where('set_coa_cabang.kode_cabang', $kode_cabang)->groupBy('set_coa_cabang.kode_akun', 'nama_akun')->get();
        } else {
            if (Auth::user()->kode_cabang == "PCF") {
                $coa = DB::table('set_coa_cabang')
                    ->select('set_coa_cabang.kode_akun', 'nama_akun')
                    ->join('coa', 'set_coa_cabang.kode_akun', '=', 'coa.kode_akun')
                    ->groupBy('set_coa_cabang.kode_akun', 'nama_akun')->get();
            } else {
                $coa = DB::table('set_coa_cabang')
                    ->select('set_coa_cabang.kode_akun', 'nama_akun')
                    ->join('coa', 'set_coa_cabang.kode_akun', '=', 'coa.kode_akun')
                    ->where('set_coa_cabang.kode_cabang', $kode_cabang)->groupBy('set_coa_cabang.kode_akun', 'nama_akun')->get();
            }
        }

        echo "<option value=''>Semua Akun</option>";
        foreach ($coa as $d) {
            echo "<option value='$d->kode_akun'>$d->kode_akun" . " " . "$d->nama_akun</option>";
        }
    }
}
