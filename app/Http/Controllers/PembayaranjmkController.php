<?php

namespace App\Http\Controllers;

use App\Models\Pembayaranjmk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranjmkController extends Controller
{
    public function index(Request $request)
    {
        $kantor = DB::table('cabang')->orderBy('kode_cabang')->get();
        $departemen = DB::table('hrd_departemen')->get();
        $query = Pembayaranjmk::query();
        $query->select('hrd_bayarjmk.*', 'nama_karyawan', 'nama_jabatan', 'nama_dept', 'id_kantor');
        $query->join('master_karyawan', 'hrd_bayarjmk.nik', '=', 'master_karyawan.nik');
        $query->leftjoin('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->leftJoin('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->orderBy('no_bukti', 'desc');
        $jmk = $query->get();
        return view('pembayaranjmk.index', compact('kantor', 'departemen', 'jmk'));
    }

    public function create()
    {
        $karyawan = DB::table('master_karyawan')->get();
        return view('pembayaranjmk.create', compact('karyawan'));
    }
}
