<?php

namespace App\Http\Controllers;

use App\Models\Konfigurasijadwalkerjadetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Redis;

class KonfigurasijadwalController extends Controller
{

    public function index()
    {
        $konfigurasijadwal = DB::table('konfigurasi_jadwalkerja')->orderBy('dari', 'desc')->get();
        return view('konfigurasijadwal.index', compact('konfigurasijadwal'));
    }
    public function store(Request $request)
    {
        $tahun = substr(date('Y'), 2, 2);
        $konfigurasijadwal = DB::table('konfigurasi_jadwalkerja')->whereRaw('MID(kode_setjadwal,3,2)="' . $tahun . '"')
            ->orderBy('kode_setjadwal', 'desc')->first();
        $lastkode_setjadwal = $konfigurasijadwal != null ? $konfigurasijadwal->kode_setjadwal : '';
        $kode_setjadwal = buatkode($lastkode_setjadwal, "SJ" . $tahun, 4);
        $dari = $request->dari;
        $sampai = $request->sampai;
        $cek = DB::table('konfigurasi_jadwalkerja')
            ->whereRaw('"' . $dari . '" >= dari')
            ->whereRaw('"' . $dari . '" <= sampai')
            ->count();

        if (!empty($cek)) {
            return Redirect::back()->with(['warning' => 'Jadwal Sudah Ada']);
        }

        try {
            $data = [
                'kode_setjadwal' => $kode_setjadwal,
                'dari' => $dari,
                'sampai' => $sampai
            ];
            DB::table('konfigurasi_jadwalkerja')->insert($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function edit($kode_setjadwal)
    {
        $konfigurasijadwal = DB::table('konfigurasi_jadwalkerja')->where('kode_setjadwal', $kode_setjadwal)->first();
        return view('konfigurasijadwal.edit', compact('konfigurasijadwal'));
    }

    public function update($kode_setjadwal, Request $request)
    {
        $kode_setjadwal = Crypt::decrypt($kode_setjadwal);
        $dari = $request->dari;
        $sampai = $request->sampai;
        $cek = DB::table('konfigurasi_jadwalkerja')
            ->whereRaw('"' . $dari . '" >= dari')
            ->whereRaw('"' . $dari . '" <= sampai')
            ->where('kode_setjadwal', '!=', $kode_setjadwal)
            ->count();

        if (!empty($cek)) {
            return Redirect::back()->with(['warning' => 'Jadwal Sudah Ada']);
        }
        try {
            $data = [
                'dari' => $dari,
                'sampai' => $sampai
            ];
            DB::table('konfigurasi_jadwalkerja')->where('kode_setjadwal', $kode_setjadwal)->update($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function aturjadwal($kode_setjadwal)
    {
        $kode_setjadwal = Crypt::decrypt($kode_setjadwal);
        $konfigurasijadwal = DB::table('konfigurasi_jadwalkerja')->where('kode_setjadwal', $kode_setjadwal)->first();
        return view('konfigurasijadwal.aturjadwal', compact('konfigurasijadwal'));
    }

    public function aturshift(Request $request)
    {
        $shift = $request->shift;
        $kode_setjadwal = $request->kode_setjadwal;
        return view('konfigurasijadwal.aturshift', compact('shift', 'kode_setjadwal'));
    }

    public function showgroup($id_group, Request $request)
    {

        $kode_setjadwal = $request->kode_setjadwal;
        $shift = $request->shift;
        $group_produksi = array(26, 27, 28, 29, 30, 31);
        if (Auth::user()->level == "general affair") {
            $id_group = 7;
        } else if (Auth::user()->level == "admin maintenance" || Auth::user()->level == "spv maintenance") {
            $id_group = 18;
        } else if (Auth::user()->level == "spv pdqc") {
            $id_group = 23;
        } else if (Auth::user()->level == "spv gudang pusat") {
            $id_group = 11;
        }
        if ($id_group == 23) {
            $karyawan = DB::table('master_karyawan')
                ->select('master_karyawan.nik', 'nama_karyawan', 'konfigurasijadwalkerja.kode_jadwal', 'nama_jadwal', 'grup')
                ->leftJoin(
                    DB::raw("(
                SELECT nik,konfigurasi_jadwalkerja_detail.kode_jadwal,nama_jadwal
                FROM konfigurasi_jadwalkerja_detail
                INNER JOIN jadwal_kerja ON konfigurasi_jadwalkerja_detail.kode_jadwal = jadwal_kerja.kode_jadwal
                WHERE kode_setjadwal = '$kode_setjadwal'
            ) konfigurasijadwalkerja"),
                    function ($join) {
                        $join->on('master_karyawan.nik', '=', 'konfigurasijadwalkerja.nik');
                    }
                )
                ->where('kode_dept', 'PDQ')
                ->where('master_karyawan.status_aktif', 1)
                ->orderBy('nama_karyawan')
                ->get();
        } elseif ($id_group == 18) {
            $karyawan = DB::table('master_karyawan')
                ->select('master_karyawan.nik', 'nama_karyawan', 'konfigurasijadwalkerja.kode_jadwal', 'nama_jadwal', 'grup')
                ->leftJoin(
                    DB::raw("(
                    SELECT nik,konfigurasi_jadwalkerja_detail.kode_jadwal,nama_jadwal
                    FROM konfigurasi_jadwalkerja_detail
                    INNER JOIN jadwal_kerja ON konfigurasi_jadwalkerja_detail.kode_jadwal = jadwal_kerja.kode_jadwal
                    WHERE kode_setjadwal = '$kode_setjadwal'
                ) konfigurasijadwalkerja"),
                    function ($join) {
                        $join->on('master_karyawan.nik', '=', 'konfigurasijadwalkerja.nik');
                    }
                )
                ->where('kode_dept', 'MTC')
                ->where('master_karyawan.status_aktif', 1)
                ->orderBy('nama_karyawan')
                ->get();
        } elseif ($id_group == "nongroup") {
            $karyawan = DB::table('master_karyawan')
                ->select('master_karyawan.nik', 'nama_karyawan', 'konfigurasijadwalkerja.kode_jadwal', 'nama_jadwal', 'grup')
                ->leftJoin(
                    DB::raw("(
                    SELECT nik,konfigurasi_jadwalkerja_detail.kode_jadwal,nama_jadwal
                    FROM konfigurasi_jadwalkerja_detail
                    INNER JOIN jadwal_kerja ON konfigurasi_jadwalkerja_detail.kode_jadwal = jadwal_kerja.kode_jadwal
                    WHERE kode_setjadwal = '$kode_setjadwal'
                ) konfigurasijadwalkerja"),
                    function ($join) {
                        $join->on('master_karyawan.nik', '=', 'konfigurasijadwalkerja.nik');
                    }
                )
                ->where('kode_dept', 'PRD')
                ->where('master_karyawan.status_aktif', 1)
                ->whereNotIn('grup', $group_produksi)
                ->orderBy('nama_karyawan')
                ->get();
        } else {
            $karyawan = DB::table('master_karyawan')
                ->select('master_karyawan.nik', 'nama_karyawan', 'konfigurasijadwalkerja.kode_jadwal', 'nama_jadwal', 'grup')
                ->leftJoin(
                    DB::raw("(
                SELECT nik,konfigurasi_jadwalkerja_detail.kode_jadwal,nama_jadwal
                FROM konfigurasi_jadwalkerja_detail
                INNER JOIN jadwal_kerja ON konfigurasi_jadwalkerja_detail.kode_jadwal = jadwal_kerja.kode_jadwal
                WHERE kode_setjadwal = '$kode_setjadwal'
            ) konfigurasijadwalkerja"),
                    function ($join) {
                        $join->on('master_karyawan.nik', '=', 'konfigurasijadwalkerja.nik');
                    }
                )
                ->where('grup', $id_group)
                ->where('master_karyawan.status_aktif', 1)
                ->orderBy('nama_karyawan')
                ->get();
        }


        if (!isset($request->kode_jadwal)) {
            if ($shift == 1) {
                $kode_jadwal = "JD002";
            } elseif ($shift == 2) {
                $kode_jadwal = "JD003";
            } else if ($shift == 3) {
                $kode_jadwal = "JD004";
            }
        } else {
            $kode_jadwal = $request->kode_jadwal;
        }

        return view('konfigurasijadwal.showgroup', compact('karyawan', 'kode_setjadwal', 'kode_jadwal', 'id_group'));
    }

    public function storekaryawanshift(Request $request)
    {
        $kode_setjadwal = $request->kode_setjadwal;
        $kode_jadwal = $request->kode_jadwal;
        $nik = $request->nik;
        try {
            $data = [
                'kode_setjadwal' => $kode_setjadwal,
                'kode_jadwal' => $kode_jadwal,
                'nik' => $nik
            ];
            DB::table('konfigurasi_jadwalkerja_detail')->insert($data);
            return 0;
        } catch (\Exception $e) {
            return 1;
        }
    }


    public function updatekaryawanshift(Request $request)
    {
        $kode_setjadwal = $request->kode_setjadwal;
        $kode_jadwal = $request->kode_jadwal;
        $nik = $request->nik;
        try {
            DB::table('konfigurasi_jadwalkerja_detail')
                ->where('kode_setjadwal', $kode_setjadwal)
                ->where('nik', $nik)
                ->update([
                    'kode_jadwal' => $kode_jadwal
                ]);
            return 0;
        } catch (\Exception $e) {
            return 1;
        }
    }


    public function hapuskaryawanshift(Request $request)
    {
        $kode_setjadwal = $request->kode_setjadwal;
        $nik = $request->nik;
        try {
            DB::table('konfigurasi_jadwalkerja_detail')->where('kode_setjadwal', $kode_setjadwal)->where('nik', $nik)->delete();
            return 0;
        } catch (\Exception $e) {
            return 1;
        }
    }


    public function storeallkaryawanshift(Request $request)
    {
        $id_group = $request->id_group;
        $kode_jadwal = $request->kode_jadwal;
        $kode_setjadwal = $request->kode_setjadwal;
        DB::beginTransaction();
        try {
            $nik = [];
            $karyawan = DB::table('master_karyawan')->where('grup', $id_group)->get();
            foreach ($karyawan as $d) {
                $nik[] = $d->nik;
                $data[] = [
                    'kode_setjadwal' => $kode_setjadwal,
                    'nik' => $d->nik,
                    'kode_jadwal' => $kode_jadwal,

                ];
            }


            DB::table('konfigurasi_jadwalkerja_detail')->where('kode_setjadwal', $kode_setjadwal)->whereIn('nik', $nik)->delete();
            //dd($data);
            $chunks = array_chunk($data, 5);
            foreach ($chunks as $chunk) {
                Konfigurasijadwalkerjadetail::insert($chunk);
            }

            DB::commit();
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            return 1;
            dd($e);
        }
    }


    public function cancelallkaryawanshift(Request $request)
    {
        $id_group = $request->id_group;
        $kode_setjadwal = $request->kode_setjadwal;
        DB::beginTransaction();
        try {
            $nik = [];
            $karyawan = DB::table('master_karyawan')->where('grup', $id_group)->get();
            foreach ($karyawan as $d) {
                $nik[] = $d->nik;
            }
            DB::table('konfigurasi_jadwalkerja_detail')->where('kode_setjadwal', $kode_setjadwal)->whereIn('nik', $nik)->delete();
            //dd($data);
            DB::commit();
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            return 1;
            dd($e);
        }
    }

    public function showjadwal(Request $request)
    {
        $kode_jadwal = $request->kode_jadwal;
        $kode_setjadwal = $request->kode_setjadwal;
        $kode_dept = Auth::user()->kode_dept_presensi;
        $level = Auth::user()->level;
        if ($level == "manager hrd" || $level == "admin") {
            $jadwal = DB::table('konfigurasi_jadwalkerja_detail')
                ->join('master_karyawan', 'konfigurasi_jadwalkerja_detail.nik', '=', 'master_karyawan.nik')
                ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
                ->join('hrd_group', 'master_karyawan.grup', '=', 'hrd_group.id')
                ->where('konfigurasi_jadwalkerja_detail.kode_setjadwal', $kode_setjadwal)
                ->where('konfigurasi_jadwalkerja_detail.kode_jadwal', $kode_jadwal)
                ->orderByRaw('master_karyawan.grup,nama_karyawan')
                ->get();
        } else {
            $jadwal = DB::table('konfigurasi_jadwalkerja_detail')
                ->join('master_karyawan', 'konfigurasi_jadwalkerja_detail.nik', '=', 'master_karyawan.nik')
                ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
                ->join('hrd_group', 'master_karyawan.grup', '=', 'hrd_group.id')
                ->where('konfigurasi_jadwalkerja_detail.kode_setjadwal', $kode_setjadwal)
                ->where('konfigurasi_jadwalkerja_detail.kode_jadwal', $kode_jadwal)
                ->where('master_karyawan.kode_dept', $kode_dept)
                ->orderByRaw('master_karyawan.grup,nama_karyawan')
                ->get();
        }

        return view('konfigurasijadwal.showjadwal', compact('jadwal'));
    }

    public function delete($kode_jadwal)
    {
        $kode_jadwal = Crypt::decrypt($kode_jadwal);
        try {
            DB::table('konfigurasi_jadwalkerja')->where('kode_setjadwal', $kode_jadwal)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    public function gantishift(Request $request)
    {
        $kode_setjadwal = $request->kode_setjadwal;
        $setjadwal = DB::table('konfigurasi_jadwalkerja')->where('kode_setjadwal', $kode_setjadwal)->first();
        $dari = explode("-", $setjadwal->dari);
        $sampai = explode("-", $setjadwal->sampai);
        $hari_dari = $dari[2];
        $bulan_dari = $dari[1] - 1;
        $tahun_dari = $dari[0];

        $hari_sampai = $sampai[2];
        $bulan_sampai = $sampai[1] - 1;
        $tahun_sampai = $sampai[0];

        $grup = array(26, 27, 28, 29, 30, 31);
        if (Auth::user()->level == "manager hrd" || Auth::user()->level == "admin") {
            $karyawan = DB::table('master_karyawan')
                ->orderBy('nama_karyawan')
                ->get();
        } else {
            $karyawan = DB::table('master_karyawan')
                ->orderBy('nama_karyawan')
                ->where('kode_dept', Auth::user()->kode_dept_presensi)
                ->get();
        }

        return view('konfigurasijadwal.gantishift', compact('karyawan', 'hari_dari', 'bulan_dari', 'tahun_dari', 'hari_sampai', 'bulan_sampai', 'tahun_sampai', 'kode_setjadwal'));
    }

    public function storegantishift(Request $request)
    {
        $kode_setjadwal = $request->kode_setjadwal;
        $nik = $request->nik;
        $tanggal = $request->tanggal;
        $kode_jadwal = $request->kode_jadwal;

        $tahun = substr(date('Y', strtotime($tanggal)), 2, 2);
        $gantishift = DB::table('konfigurasi_gantishift')->whereRaw('MID(kode_gs,3,2)="' . $tahun . '"')
            ->orderBy('kode_gs', 'desc')->first();
        $last_kodeshift = $gantishift != null ? $gantishift->kode_gs : '';
        $kode_gs = buatkode($last_kodeshift, "GS" . $tahun, 3);
        $cek = DB::table('konfigurasi_gantishift')->where('nik', $nik)->where('tanggal', $tanggal)->count();
        if ($cek > 0) {
            return 2;
        }
        try {
            $data = [
                'kode_gs' => $kode_gs,
                'nik' => $nik,
                'tanggal' => $tanggal,
                'kode_jadwal' => $kode_jadwal,
                'kode_setjadwal' => $kode_setjadwal
            ];

            DB::table('konfigurasi_gantishift')->insert($data);
            return 0;
        } catch (\Exception $e) {
            return 1;
        }
    }

    public function showgantishift($kode_setjadwal)
    {

        $level = Auth::user()->level;
        if ($level == "manager hrd" || $level == "admin") {
            $gantishift = DB::table('konfigurasi_gantishift')
                ->join('master_karyawan', 'konfigurasi_gantishift.nik', '=', 'master_karyawan.nik')
                ->join('hrd_group', 'master_karyawan.grup', '=', 'hrd_group.id')
                ->join('jadwal_kerja', 'konfigurasi_gantishift.kode_jadwal', '=', 'jadwal_kerja.kode_jadwal')
                ->where('kode_setjadwal', $kode_setjadwal)->get();
        } else {
            $gantishift = DB::table('konfigurasi_gantishift')
                ->join('master_karyawan', 'konfigurasi_gantishift.nik', '=', 'master_karyawan.nik')
                ->join('hrd_group', 'master_karyawan.grup', '=', 'hrd_group.id')
                ->join('jadwal_kerja', 'konfigurasi_gantishift.kode_jadwal', '=', 'jadwal_kerja.kode_jadwal')
                ->where('kode_setjadwal', $kode_setjadwal)
                ->where('master_karyawan.kode_dept', Auth::user()->kode_dept_presensi)
                ->get();
        }

        return view('konfigurasijadwal.showgantishift', compact('gantishift'));
    }

    public function deletegantishift(Request $request)
    {
        $kode_gs = $request->kode_gs;
        try {
            DB::table('konfigurasi_gantishift')->where('kode_gs', $kode_gs)->delete();
            return 0;
        } catch (\Exception $e) {
            return 1;
        }
    }


    public function setjadwal($nik)
    {
        $karyawan = DB::table('master_karyawan')->where('nik', $nik)->first();
        return view('konfigurasijadwal.setjadwal', compact('karyawan'));
    }
}
