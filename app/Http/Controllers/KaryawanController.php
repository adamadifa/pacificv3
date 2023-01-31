<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $nama_karyawan = $request->nama_karyawan;
        $query = Karyawan::query();
        $query->select('nik', 'nama_karyawan', 'tgl_masuk', 'nama_dept', 'jenis_kelamin', 'nama_jabatan', 'id_perusahaan', 'id_kantor', 'klasifikasi');
        $query->join('departemen', 'master_karyawan.kode_dept', '=', 'departemen.kode_dept');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        if (!empty($nama_karyawan)) {
            $query->where('nama_karyawan', 'like', '%' . $nama_karyawan . '%');
        }
        $query->orderBy('nama_karyawan');
        $karyawan = $query->paginate(15);
        return view('karyawan.index', compact('karyawan'));
    }

    public function create()
    {
        $departemen = DB::table('departemen')->where('status_pengajuan', 0)->get();
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        $jabatan = DB::table('hrd_jabatan')->orderBy('nama_jabatan')->get();
        $group = DB::table('hrd_group')->orderBy('nama_group')->get();
        return view('karyawan.create', compact('cabang', 'departemen', 'jabatan', 'group'));
    }
}
