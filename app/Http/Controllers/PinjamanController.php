<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class PinjamanController extends Controller
{
    public function create($nik)
    {
        $nik = Crypt::decrypt($nik);
        $query = Karyawan::query();
        $query->select('nik', 'nama_karyawan', 'tgl_masuk', 'nama_dept', 'jenis_kelamin', 'nama_jabatan', 'id_perusahaan', 'id_kantor', 'klasifikasi', 'status_karyawan', 'nama_cabang');
        $query->join('departemen', 'master_karyawan.kode_dept', '=', 'departemen.kode_dept');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->leftjoin('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang');
        $query->where('nik', $nik);
        $karyawan = $query->first();

        $gaji = DB::table('hrd_mastergaji')
            ->selectRaw('gaji_pokok+t_jabatan+t_masakerja+t_tanggungjawab+t_makan+t_istri+t_skill as gajitunjangan,gaji_pokok')
            ->where('nik', $nik)->orderBy('tgl_berlaku', 'desc')->first();

        $jmk = DB::table('hrd_bayarjmk')
            ->selectRaw('SUM(jumlah) as jml_jmk')
            ->where('nik', $nik)
            ->groupBy('nik')
            ->first();

        $kontrak = DB::table('hrd_kontrak')->where('nik', $nik)->orderBy('dari', 'desc')->first();
        return view('pinjaman.create', compact('karyawan', 'gaji', 'jmk', 'kontrak'));
    }
}
