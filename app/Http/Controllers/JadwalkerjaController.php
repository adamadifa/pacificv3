<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class JadwalkerjaController extends Controller
{
    public function index()
    {
        $qnonshift = Karyawan::query();
        $qnonshift->select('nik', 'nama_karyawan', 'id_kantor');
        $qnonshift->where('kode_jadwal', 'JD001');
        $qnonshift->orderBy('nama_karyawan');
        $nonshift = $qnonshift->get();
        $jmlnonshift = $qnonshift->count();

        $qshift1 = Karyawan::query();
        $qshift1->select('nik', 'nama_karyawan', 'id_kantor');
        $qshift1->where('kode_jadwal', 'JD002');
        $qshift1->orderBy('nama_karyawan');
        $shift1 = $qshift1->get();
        $jmlshift1 = $qshift1->count();

        $qshift2 = Karyawan::query();
        $qshift2->select('nik', 'nama_karyawan', 'id_kantor');
        $qshift2->where('kode_jadwal', 'JD003');
        $qshift2->orderBy('nama_karyawan');
        $shift2 = $qshift2->get();
        $jmlshift2 = $qshift2->count();

        $qshift3 = Karyawan::query();
        $qshift3->select('nik', 'nama_karyawan', 'id_kantor');
        $qshift3->where('kode_jadwal', 'JD004');
        $qshift3->orderBy('nama_karyawan');
        $shift3 = $qshift3->get();
        $jmlshift3 = $qshift3->count();

        return view('jadwalkerja.index', compact('nonshift', 'shift1', 'shift2', 'shift3', 'jmlnonshift', 'jmlshift1', 'jmlshift2', 'jmlshift3'));
    }

    public function pindahjadwal(Request $request)
    {
        $nik = $request->nik;
        $jadwal = DB::table('jadwal_kerja')->orderBy('kode_jadwal')->get();
        $karyawan = DB::table('master_karyawan')
            ->leftJoin('jadwal_kerja', 'master_karyawan.kode_jadwal', '=', 'jadwal_kerja.kode_jadwal')
            ->where('nik', $nik)->first();
        return view('jadwalkerja.pindahjadwal', compact('karyawan', 'jadwal'));
    }

    public function updatejadwalkerja(Request $request)
    {
        $nik = $request->nik;
        $kode_jadwal = $request->kode_jadwal;
        try {
            DB::table('master_karyawan')->where('nik', $nik)->update([
                'kode_jadwal' => $kode_jadwal
            ]);
            return Redirect::back()->with(['success' => 'Jadwal Berhasil Dipindahkan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Dipindahkan']);
        }
    }
}
