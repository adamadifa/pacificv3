<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresensiController extends Controller
{
    public function monitoring(Request $request)
    {
        $level = Auth::user()->level;
        $cabang = Auth::user()->kode_cabang;
        $nama_karyawan = $request->nama_karyawan_search;
        $tanggal = !empty($request->tanggal) ? $request->tanggal : date('Y-m-d');
        // $tanggal = date("Y-m-d");
        $query = Karyawan::query();
        $query->select('master_karyawan.nik', 'nama_karyawan', 'tgl_masuk', 'master_karyawan.kode_dept', 'nama_dept', 'jenis_kelamin', 'nama_jabatan', 'id_perusahaan', 'id_kantor', 'klasifikasi', 'status_karyawan', 'nama_jadwal', 'jam_kerja.jam_masuk', 'jam_kerja.jam_pulang', 'jam_in', 'jam_out', 'presensi.status as status_presensi', 'presensi.kode_izin', 'kode_izin_terlambat', 'tgl_presensi', 'pengajuan_izin.status as status_izin', 'pengajuan_izin.jenis_izin', 'pengajuan_izin.jam_keluar', 'pengajuan_izin.jam_masuk as jam_masuk_kk', 'total_jam', 'kode_izin_pulang', 'jam_istirahat');
        $query->leftjoin('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->leftjoin('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');

        $query->leftJoin(
            DB::raw("(
            SELECT
                *
            FROM
                presensi
            WHERE tgl_presensi = '$tanggal'
            ) presensi"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'presensi.nik');
            }
        );
        $query->leftjoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin');
        $query->leftJoin(
            DB::raw("(
            SELECT
                jadwal_kerja_detail.kode_jadwal,nama_jadwal,kode_jam_kerja
            FROM
                jadwal_kerja_detail
            INNER JOIN jadwal_kerja ON jadwal_kerja_detail.kode_jadwal = jadwal_kerja.kode_jadwal
            GROUP BY
            jadwal_kerja_detail.kode_jadwal,nama_jadwal,kode_jam_kerja
            ) jadwal"),
            function ($join) {
                $join->on('presensi.kode_jadwal', '=', 'jadwal.kode_jadwal');
                $join->on('presensi.kode_jam_kerja', '=', 'jadwal.kode_jam_kerja');
            }
        );
        $query->leftjoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja');
        if (!empty($nama_karyawan)) {
            $query->where('nama_karyawan', 'like', '%' . $nama_karyawan . '%');
        }

        if (!empty($request->kode_dept_search)) {
            $query->where('master_karyawan.kode_dept', $request->kode_dept_search);
        }

        if (!empty($request->id_perusahaan_search)) {
            $query->where('master_karyawan.id_perusahaan', $request->id_perusahaan_search);
        }

        if (!empty($request->id_kantor_search)) {
            $query->where('master_karyawan.id_kantor', $request->id_kantor_search);
        }

        if (!empty($request->grup_search)) {
            $query->where('master_karyawan.grup', $request->grup_search);
        }

        if ($level == "kepala admin") {
            $query->where('id_kantor', $cabang);
            $query->where('id_perusahaan', "MP");
        }

        if ($level == "kepala penjualan") {
            $query->where('id_kantor', $cabang);
            $query->where('id_perusahaan', "PCF");
        }

        if ($level == "manager pembelian") {
            $query->where('master_karyawan.kode_dept', 'PMB');
        }

        if ($level == "kepala gudang") {
            $query->where('master_karyawan.kode_dept', 'GDG');
        }

        if ($level == "manager produksi") {
            $query->where('master_karyawan.kode_dept', 'PRD');
        }

        if ($level == "manager ga") {
            $query->where('master_karyawan.kode_dept', 'GAF');
        }

        if ($level == "emf") {
            $query->whereIn('master_karyawan.kode_dept', ['PMB', 'PRD', 'GAF', 'GDG', 'PDQ']);
        }


        if ($level == "manager marketing") {
            $query->where('master_karyawan.kode_dept', 'MKT');
        }

        if ($level == "rsm") {
            $list_wilayah = Auth::user()->wilayah != null ? unserialize(Auth::user()->wilayah) : NULL;
            $wilayah = $list_wilayah != null ? "'" . implode("', '", $list_wilayah) . "'" : '';
            $query->whereIn('master_karyawan.id_kantor', $list_wilayah);
        }

        $query->orderBy('nama_karyawan');
        $karyawan = $query->paginate(15);
        $karyawan->appends($request->all());
        $kantor = DB::table('cabang')->orderBy('kode_cabang')->get();
        $departemen = DB::table('hrd_departemen')->get();
        $group = DB::table('hrd_group')->orderBy('nama_group')->get();
        return view('presensi.monitoring', compact('karyawan', 'departemen', 'kantor', 'group'));
    }
}
