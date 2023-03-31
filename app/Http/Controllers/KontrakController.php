<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class KontrakController extends Controller
{
    public function createformpenilaian(Request $request)
    {
        $kode_penilaian = $request->kode_penilaian;
        $penilaian = DB::table('hrd_penilaian')
            ->select('kode_penilaian', 'hrd_penilaian.nik', 'nama_karyawan', 'hrd_penilaian.id_jabatan', 'nama_jabatan')
            ->join('master_karyawan', 'hrd_penilaian.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->where('kode_penilaian', $kode_penilaian)
            ->first();
        $jabatan = DB::table('hrd_jabatan')->orderBy('nama_jabatan')->get();
        $gaji = DB::table('hrd_mastergaji')->where('nik', $penilaian->nik)->orderBy('tgl_berlaku', 'desc')->first();
        return view('kontrak.createformpenilaian', compact('penilaian', 'jabatan', 'gaji'));
    }

    public function storefrompenilaian(Request $request)
    {
        $kode_penilaian = $request->kode_penilaian;
        $nik = $request->nik;
        $dari = $request->kontrak_dari;
        $sampai = $request->kontrak_sampai;
        $id_jabatan = $request->id_jabatan;
        $gaji_pokok = str_replace(".", "", $request->gaji_pokok);
        $t_jabatan = str_replace(".", "", $request->t_jabatan);
        $t_masakerja = str_replace(".", "", $request->t_masakerja);
        $t_tanggungjawab = str_replace(".", "", $request->t_tanggungjawab);
        $t_makan = str_replace(".", "", $request->t_makan);
        $t_istri = str_replace(".", "", $request->t_istri);
        $t_skill = str_replace(".", "", $request->t_skill);

        $tanggal = $request->kontrak_dari;
        $tgl = explode("-", $tanggal);
        $bulan = $tgl[1];
        $tahun = substr($tgl[0], 2, 2);
        $format = $bulan . $tahun;
        $kontrak = DB::table("hrd_kontrak")
            ->whereRaw('MONTH(dari)="' . $bulan . '"')
            ->whereRaw('YEAR(dari)="' . $tgl[0] . '"')
            ->orderBy("no_kontrak", "desc")
            ->first();
        $last_nokontrak = $kontrak != null ? $kontrak->no_kontrak : '';
        $no_kontrak  = buatkode($last_nokontrak, "K" . $format, 3);


        $gaji = DB::table("hrd_mastergaji")
            ->whereRaw('YEAR(tgl_berlaku)="' . $tgl[0] . '"')
            ->orderBy("kode_gaji", "desc")
            ->first();

        $last_kodegaji = $gaji != null ? $gaji->kode_gaji : '';
        $kode_gaji  = buatkode($last_kodegaji, "GJ" . $tahun, 3);
        DB::beginTransaction();
        try {
            DB::table('hrd_kontrak')->insert([
                'no_kontrak' => $no_kontrak,
                'nik' => $nik,
                'dari' => $dari,
                'sampai' => $sampai,
                'id_jabatan' => $id_jabatan,
                'kode_penilaian' => $kode_penilaian
            ]);

            DB::table('hrd_mastergaji')->insert([
                'kode_gaji' => $kode_gaji,
                'nik' => $nik,
                'gaji_pokok' => $gaji_pokok,
                't_jabatan' => $t_jabatan,
                't_masakerja' => $t_masakerja,
                't_tanggungjawab' => $t_tanggungjawab,
                't_makan' => $t_makan,
                't_istri' => $t_istri,
                't_skill' => $t_skill,
                'tgl_berlaku' => $dari,
                'no_kontrak' => $no_kontrak
            ]);

            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }


    public function cetak($no_kontrak)
    {
        $no_kontrak = Crypt::decrypt($no_kontrak);
        $kontrak = DB::table('hrd_kontrak')
            ->select('hrd_kontrak.*', 'nama_karyawan', 'tempat_lahir', 'tgl_lahir', 'alamat', 'no_ktp', 'hrd_mastergaji.*')
            ->join('master_karyawan', 'hrd_kontrak.nik', '=', 'master_karyawan.nik')
            ->leftJoin('hrd_mastergaji', 'hrd_kontrak.no_kontrak', '=', 'hrd_mastergaji.no_kontrak')
            ->where('hrd_kontrak.no_kontrak', $no_kontrak)
            ->first();
        $approve = DB::table('hrd_approvekb')
            ->where('tgl_berlaku', '<=', $kontrak->dari)
            ->orderBy('tgl_berlaku', 'desc')->first();
        return view('kontrak.cetak', compact('kontrak', 'approve'));
    }
}
