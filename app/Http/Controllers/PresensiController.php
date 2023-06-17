<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Facade\Ignition\Tabs\Tab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PresensiController extends Controller
{

    public function hari_ini()
    {
        $hari = date("D");

        switch ($hari) {
            case 'Sun':
                $hari_ini = "Minggu";
                break;

            case 'Mon':
                $hari_ini = "Senin";
                break;

            case 'Tue':
                $hari_ini = "Selasa";
                break;

            case 'Wed':
                $hari_ini = "Rabu";
                break;

            case 'Thu':
                $hari_ini = "Kamis";
                break;

            case 'Fri':
                $hari_ini = "Jumat";
                break;

            case 'Sat':
                $hari_ini = "Sabtu";
                break;

            default:
                $hari_ini = "Tidak di ketahui";
                break;
        }

        return $hari_ini;
    }
    public function monitoring(Request $request)
    {
        $level = Auth::user()->level;
        $cabang = Auth::user()->kode_cabang;
        $nama_karyawan = $request->nama_karyawan_search;
        $tanggal = !empty($request->tanggal) ? $request->tanggal : date('Y-m-d');
        // $tanggal = date("Y-m-d");
        $query = Karyawan::query();
        $query->select('master_karyawan.nik', 'nama_karyawan', 'tgl_masuk', 'master_karyawan.kode_dept', 'nama_dept', 'jenis_kelamin', 'nama_jabatan', 'id_perusahaan', 'id_kantor', 'klasifikasi', 'status_karyawan', 'nama_jadwal', 'jam_kerja.jam_masuk', 'jam_kerja.jam_pulang', 'jam_in', 'jam_out', 'presensi.status as status_presensi', 'presensi.kode_izin', 'kode_izin_terlambat', 'tgl_presensi', 'pengajuan_izin.status as status_izin', 'pengajuan_izin.jenis_izin', 'pengajuan_izin.jam_keluar', 'pengajuan_izin.jam_masuk as jam_masuk_kk', 'total_jam', 'kode_izin_pulang', 'jam_istirahat', 'jam_awal_istirahat', 'sid', 'jadwal_kerja.kode_cabang as jadwalcabang');
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

        $query->leftjoin('jadwal_kerja', 'presensi.kode_jadwal', '=', 'jadwal_kerja.kode_jadwal');
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

    public function updatepresensi(Request $request)
    {
        $nik = $request->nik;
        $tgl = $request->tgl;
        $cek = DB::table('presensi')->where('tgl_presensi', $tgl)->where('nik', $nik)->first();
        $karyawan = DB::table('master_karyawan')
            ->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->where('nik', $nik)->first();
        $jadwal = DB::table('jadwal_kerja')
            ->where('kode_cabang', $karyawan->id_kantor)
            ->orderBy('kode_jadwal')->get();
        return view('presensi.updatepresensi', compact('karyawan', 'tgl', 'jadwal', 'cek'));
    }

    public function storeupdatepresensi(Request $request)
    {
        $nik = $request->nik;
        $tgl_presensi = $request->tgl_presensi;
        $kode_jadwal = $request->kode_jadwal;
        $jam_masuk = $tgl_presensi . " " . $request->jam_masuk;
        $jam_pulang = $tgl_presensi . " " . $request->jam_pulang;
        $nextday = date('Y-m-d', strtotime('+1 day', strtotime($tgl_presensi)));

        $cekperjalanandinas = DB::table('pengajuan_izin')
            ->where('status', 'p')
            ->whereRaw('"' . $tgl_presensi . '" >= dari')
            ->whereRaw('"' . $tgl_presensi . '" <= sampai')
            ->where('nik', $nik)
            ->first();

        if ($cekperjalanandinas != null) {
            $cekjadwaldinas = DB::table('jadwal_kerja')
                ->where('nama_jadwal', 'NON SHIFT')
                ->where('kode_cabang', $cekperjalanandinas->kode_cabang)->first();
            $kode_jadwal = $cekjadwaldinas->kode_jadwal;
        }
        $ceklibur = DB::table('harilibur')->where('tanggal_limajam', $tgl_presensi)->count();
        if ($ceklibur > 0) {
            $hariini = "Sabtu";
        } else {
            $hariini = $this->hari_ini();
        }

        $jadwal = DB::table('jadwal_kerja_detail')
            ->join('jadwal_kerja', 'jadwal_kerja_detail.kode_jadwal', '=', 'jadwal_kerja.kode_jadwal')
            ->where('hari', $hariini)->where('jadwal_kerja_detail.kode_jadwal', $kode_jadwal)
            ->first();
        $jam_kerja = DB::table('jam_kerja')->where('kode_jam_kerja', $jadwal->kode_jam_kerja)->first();
        $lintashari  = $jam_kerja->lintashari;
        if (!empty($lintashari)) {
            $jam_pulang = $nextday . " " . $request->jam_pulang;
        }
        $cekizinterlambat = DB::table('pengajuan_izin')->where('nik', $nik)->where('dari', $tgl_presensi)->where('jenis_izin', 'TL')->where('status_approved', 1)->first();

        $kode_izin = $cekizinterlambat != null  ? $cekizinterlambat->kode_izin : NULL;

        $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->first();

        if ($cek == null) {
            $data = [
                'nik' => $nik,
                'tgl_presensi' => $tgl_presensi,
                'jam_in' => $jam_masuk,
                'jam_out' => $jam_pulang,
                'kode_jadwal' => $kode_jadwal,
                'kode_jam_kerja' => $jadwal->kode_jam_kerja,
                'kode_izin_terlambat' => $kode_izin,
                'status' => $request->status,
            ];

            try {
                DB::table('presensi')->insert($data);
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } catch (\Exception $e) {
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
            }
        } else {

            if ($request->status == "h") {
                $data = [
                    'jam_in' => $jam_masuk,
                    'jam_out' => $jam_pulang,
                    'kode_jadwal' => $kode_jadwal,
                    'kode_jam_kerja' => $jadwal->kode_jam_kerja,
                    'status' => $request->status,
                ];
            } else {
                $data = [
                    'jam_in' => NULL,
                    'jam_out' => NULL,
                    'kode_jadwal' => $kode_jadwal,
                    'kode_jam_kerja' => $jadwal->kode_jam_kerja,
                    'status' => $request->status,
                ];
            }


            try {
                DB::table('presensi')
                    ->where('id', $cek->id)
                    ->update($data);
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } catch (\Exception $e) {
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
            }
        }
    }
}
