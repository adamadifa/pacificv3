<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenilaiankaryawanController extends Controller
{
    public function index()
    {
        $karyawan = DB::table('master_karyawan')->get();
        $kategori_penilaian = DB::table('hrd_kategoripenilaian')->get();
        return view('penilaiankaryawan.index', compact('karyawan', 'kategori_penilaian'));
    }

    public function create(Request $request)
    {
        $tanggal = $request->tanggal;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $nik = $request->nik;
        $kategori = $request->kategori_penilaian;
        $karyawan = DB::table('master_karyawan')->where('nik', $nik)->first();
        $kategori_penilaian = DB::table('hrd_penilaiankaryawan_item')->where('id_kategori', $kategori)
            ->join('hrd_jenispenilaian', 'hrd_penilaiankaryawan_item.id_jenis_penilaian', '=', 'hrd_jenispenilaian.id')
            ->orderBy('hrd_penilaiankaryawan_item.id_jenis_penilaian')->get();
        if ($kategori == 1) {

            return view('penilaiankaryawan.create', compact('tanggal', 'dari', 'sampai', 'karyawan', 'kategori_penilaian'));
        } else {
            return view('penilaiankaryawan.create_operator', compact('tanggal', 'dari', 'sampai', 'karyawan', 'kategori_penilaian'));
        }
    }
}
