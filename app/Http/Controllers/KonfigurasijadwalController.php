<?php

namespace App\Http\Controllers;

use App\Models\Konfigurasijadwalkerjadetail;
use Illuminate\Http\Request;
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
            ->orderBy('nama_karyawan')
            ->get();

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
        $jadwal = DB::table('konfigurasi_jadwalkerja_detail')
            ->join('master_karyawan', 'konfigurasi_jadwalkerja_detail.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->join('hrd_group', 'master_karyawan.grup', '=', 'hrd_group.id')
            ->where('konfigurasi_jadwalkerja_detail.kode_setjadwal', $kode_setjadwal)
            ->where('konfigurasi_jadwalkerja_detail.kode_jadwal', $kode_jadwal)
            ->orderBy('nama_karyawan')
            ->get();
        return view('konfigurasijadwal.showjadwal', compact('jadwal'));
    }

    public function delete($kode_jadwal)
    {
        $kode_jadwal = Crypt::decrypt($kode_jadwal);
        try {
            DB::table('konfigurasi_jadwalkerja')->where('kode_setjadwal', $kode_jadwal)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }
}