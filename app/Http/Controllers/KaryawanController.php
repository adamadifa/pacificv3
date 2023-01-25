<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index()
    {
        $query = Karyawan::query();
        $query->select('nik', 'nama_karyawan', 'tgl_masuk', 'nama_dept');
        $query->join('departemen', 'master_karyawan.kode_dept', '=', 'departemen.kode_dept');
        $query->orderBy('nama_karyawan');
        $karyawan = $query->paginate(15);
        return view('karyawan.index', compact('karyawan'));
    }
}
