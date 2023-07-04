<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Karyawan extends Model
{
    use HasFactory;
    protected $table = "master_karyawan";
    protected $guarded = [];

    public function getkaryawanpengajuan($kode_dept_presensi)
    {
        $cabang = Auth::user()->kode_cabang;
        $id_user = Auth::user()->id;

        $qkaryawan = Karyawan::query();
        if (!empty($kode_dept_presensi)) {
            if ($id_user != 69) {
                $qkaryawan->where('master_karyawan.kode_dept', $kode_dept_presensi);
                if ($cabang == "PCF") {
                    $qkaryawan->where('master_karyawan.id_kantor', 'PST');
                } else {
                    $qkaryawan->where('master_karyawan.id_kantor', $cabang);
                }

                if (Auth::user()->id == 73) {
                    $qkaryawan->where('grup', 9);
                    $qkaryawan->where('id_kantor', 'PST');
                }
            }

            if (Auth::user()->id == 69) {
                $qkaryawan->where('grup', 11);
                $qkaryawan->where('id_kantor', 'PST');
                $qkaryawan->where('nama_jabatan', '!=', 'MANAGER');
                $qkaryawan->orWhere('grup', 4);
                $qkaryawan->where('id_kantor', 'PST');
                $qkaryawan->where('nama_jabatan', 'MANAGER');
            }
        }

        if (!empty(Auth::user()->pic_presensi)) {
            if ($cabang != "PCF") {
                $qkaryawan->where('master_karyawan.id_kantor', $cabang);
            }
        }
        $qkaryawan->leftJoin('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $qkaryawan->orderBy('nama_karyawan');
        $karyawan = $qkaryawan->get();
        return $karyawan;
    }
}
