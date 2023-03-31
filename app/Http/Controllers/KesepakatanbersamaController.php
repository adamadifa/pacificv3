<?php

namespace App\Http\Controllers;

use App\Models\Kesepakatanbersama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class KesepakatanbersamaController extends Controller
{

    public function index()
    {
        $query = Kesepakatanbersama::query();
        $query->select('no_kb', 'tgl_kb', 'hrd_penilaian.nik', 'nama_karyawan', 'nama_jabatan', 'tahun');
        $query->join('hrd_penilaian', 'hrd_kesepakatanbersama.kode_penilaian', '=', 'hrd_penilaian.kode_penilaian');
        $query->join('hrd_jabatan', 'hrd_penilaian.id_jabatan', '=', 'hrd_jabatan.id');
        $query->join('master_karyawan', 'hrd_penilaian.nik', '=', 'master_karyawan.nik');
        $query->orderBy('hrd_kesepakatanbersama.no_kb', 'desc');
        $kb = $query->get();

        $kantor = DB::table('cabang')->orderBy('kode_cabang')->get();
        $departemen = DB::table('departemen')->where('status_pengajuan', 0)->get();
        return view('kesepakatanbersama.index', compact('kb', 'departemen', 'kantor'));
    }
    public function store(Request $request)
    {
        $nik = $request->nik;
        $kode_penilaian = $request->kode_penilaian;
        $tahunkb = $request->tahun;
        $tanggal = $request->tanggal;
        $tgl = explode("-", $tanggal);
        $bulan = $tgl[1];
        $tahun = substr($tgl[0], 2, 2);
        $format = $bulan . $tahun;
        $kb = DB::table("hrd_kesepakatanbersama")
            ->whereRaw('MONTH(tgl_kb)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_kb)="' . $tgl[0] . '"')
            ->orderBy("no_kb", "desc")
            ->first();
        $lastno_kb = $kb != null ? $kb->no_kb : '';
        $no_kb  = buatkode($lastno_kb, "KB" . $format, 3);

        $gaji = DB::table('hrd_mastergaji')->where('tgl_berlaku', '<=', $tanggal)->where('nik', $nik)->first();

        DB::beginTransaction();
        try {
            $data = [
                'no_kb' => $no_kb,
                'tgl_kb' => $tanggal,
                'nik' => $nik,
                'kode_penilaian' => $kode_penilaian,
                'tahun' => $tahunkb,
                'kode_gaji' => $gaji->kode_gaji
            ];

            DB::table('hrd_kesepakatanbersama')->insert($data);
            DB::commit();
            return Redirect::back();
        } catch (\Exception $e) {
            dd($e);
            return Redirect::back();
        }
    }

    public function cetak($no_kb, Request $request)
    {
        $no_kb = Crypt::decrypt($no_kb);
        $query = Kesepakatanbersama::query();
        $query->select('no_kb', 'tgl_kb', 'hrd_penilaian.nik', 'nama_karyawan', 'nama_jabatan', 'tahun', 'tgl_masuk', 'alamat', 'no_ktp', 'hrd_mastergaji.*');
        $query->join('hrd_penilaian', 'hrd_kesepakatanbersama.kode_penilaian', '=', 'hrd_penilaian.kode_penilaian');
        $query->join('hrd_jabatan', 'hrd_penilaian.id_jabatan', '=', 'hrd_jabatan.id');
        $query->join('master_karyawan', 'hrd_penilaian.nik', '=', 'master_karyawan.nik');
        $query->leftJoin('hrd_mastergaji', 'hrd_kesepakatanbersama.kode_gaji', '=', 'hrd_mastergaji.kode_gaji');
        $query->orderBy('hrd_kesepakatanbersama.no_kb', 'desc');
        $query->where('no_kb', $no_kb);
        $kb = $query->first();

        $approve = DB::table('hrd_approvekb')->where('tgl_berlaku', '<=', $kb->tgl_kb)->orderBy('tgl_berlaku', 'desc')->first();

        $potongan = DB::table('hrd_potongankb')->where('no_kb', $no_kb)->get();
        return view('kesepakatanbersama.cetak', compact('kb', 'approve', 'potongan'));
    }

    public function edit(Request $request)
    {
        $no_kb = $request->no_kb;
        $kb = DB::table('hrd_kesepakatanbersama')->where('no_kb', $no_kb)->first();
        return view('kesepakatanbersama.edit', compact('kb'));
    }

    public function update($no_kb, Request $request)
    {
        $tgl_kb = $request->tanggal;
        $tahun = $request->tahun;

        $data = [
            'tgl_kb' => $tgl_kb,
            'tahun' => $tahun
        ];
        DB::beginTransaction();
        try {
            DB::table('hrd_kesepakatanbersama')->where('no_kb', $no_kb)->update($data);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }

    public function potongan(Request $request)
    {
        $no_kb = $request->no_kb;
        return view('kesepakatanbersama.potongan', compact('no_kb'));
    }

    public function storepotongan(Request $request)
    {
        $no_kb = $request->no_kb;
        $keterangan = $request->keterangan;
        $jumlah = str_replace(".", "", $request->jumlah);

        $cekpotongan = DB::table('hrd_potongankb')->where('no_kb', $no_kb)->orderBy('no_urut', 'desc')->first();
        $no_urut = $cekpotongan != null ? $cekpotongan->no_urut + 1 : 1;
        $data = [
            'no_kb' => $no_kb,
            'keterangan' => $keterangan,
            'jumlah' => $jumlah,
            'no_urut' => $no_urut
        ];
        DB::beginTransaction();
        try {
            DB::table('hrd_potongankb')->insert($data);
            DB::commit();
            echo 0;
        } catch (\Exception $e) {
            DB::rollBack();
            echo 1;
        }
    }

    public function getpotongan(Request $request)
    {
        $no_kb  = $request->no_kb;
        $kb = DB::table('hrd_potongankb')->where('no_kb', $no_kb)->get();
        return view('kesepakatanbersama.getpotongan', compact('kb', 'no_kb'));
    }

    public function deletepotongan(Request $request)
    {
        $no_kb = $request->no_kb;
        $no_urut = $request->no_urut;
        DB::beginTransaction();
        try {
            DB::table('hrd_potongankb')->where('no_kb', $no_kb)->where('no_urut', $no_urut)->delete();
            DB::commit();
            echo 0;
        } catch (\Exception $e) {
            DB::rollBack();
            echo 1;
        }
    }

    public function delete($no_kb)
    {
        $no_kb = Crypt::decrypt($no_kb);
        DB::beginTransaction();
        try {
            DB::table('hrd_kesepakatanbersama')->where('no_kb', $no_kb)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }
}
