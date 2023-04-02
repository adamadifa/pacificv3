<?php

namespace App\Http\Controllers;

use App\Models\Kontrak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class KontrakController extends Controller
{

    public function index(Request $request)
    {
        $query = Kontrak::query();
        $query->select('hrd_kontrak.*', 'nama_karyawan', 'nama_jabatan');;
        $query->join('hrd_jabatan', 'hrd_kontrak.id_jabatan', '=', 'hrd_jabatan.id');
        $query->join('master_karyawan', 'hrd_kontrak.nik', '=', 'master_karyawan.nik');
        $query->orderBy('hrd_kontrak.no_kontrak', 'desc');
        if (!empty($request->nama_karyawan_search)) {
            $query->where('nama_karyawan', 'like', '%' . $request->nama_karyawan_search . '%');
        }

        if (!empty($request->id_perusahaan_search)) {
            $query->where('hrd_kontrak.id_perusahaan', $request->kode_dept_search);
        }


        if (!empty($request->id_kantor_search)) {
            $query->where('hrd_kontrak.id_kantor', $request->id_kantor_search);
        }
        $datakontrak = $query->paginate(20);
        $datakontrak->appends($request->all());
        $kantor = DB::table('cabang')->orderBy('kode_cabang')->get();

        return view('kontrak.index', compact('datakontrak', 'kantor'));
    }


    public function create(Request $request)
    {
        $jabatan = DB::table('hrd_jabatan')->orderBy('nama_jabatan')->get();
        $kantor = DB::table('cabang')->orderBy('kode_cabang')->get();
        $karyawan = DB::table('master_karyawan')->orderBy('nama_karyawan')->get();
        return view('kontrak.create', compact('kantor', 'jabatan', 'karyawan'));
    }

    public function edit(Request $request)
    {
        $no_kontrak = $request->no_kontrak;
        $jabatan = DB::table('hrd_jabatan')->orderBy('nama_jabatan')->get();
        $kantor = DB::table('cabang')->orderBy('kode_cabang')->get();
        $karyawan = DB::table('master_karyawan')->orderBy('nama_karyawan')->get();
        $kontrak = DB::table('hrd_kontrak')->where('no_kontrak', $no_kontrak)->first();
        $gaji = DB::table('hrd_mastergaji')->where('no_kontrak', $no_kontrak)->first();
        return view('kontrak.edit', compact('kantor', 'jabatan', 'karyawan', 'kontrak', 'gaji'));
    }

    public function createformpenilaian(Request $request)
    {
        $kode_penilaian = $request->kode_penilaian;
        $penilaian = DB::table('hrd_penilaian')
            ->select('kode_penilaian', 'hrd_penilaian.nik', 'nama_karyawan', 'hrd_penilaian.id_jabatan', 'nama_jabatan', 'hrd_penilaian.id_perusahaan', 'hrd_penilaian.masa_kontrak_kerja')
            ->join('master_karyawan', 'hrd_penilaian.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->where('kode_penilaian', $kode_penilaian)
            ->first();
        $jabatan = DB::table('hrd_jabatan')->orderBy('nama_jabatan')->get();
        $gaji = DB::table('hrd_mastergaji')->where('nik', $penilaian->nik)->orderBy('tgl_berlaku', 'desc')->first();
        return view('kontrak.createformpenilaian', compact('penilaian', 'jabatan', 'gaji'));
    }

    public function store(Request $request)
    {

        $nik = $request->nik;
        $dari = $request->kontrak_dari;
        $sampai = $request->kontrak_sampai;
        $id_jabatan = $request->id_jabatan;
        $id_perusahaan = $request->id_perusahaan;
        $id_kantor = $request->id_kantor;
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
                'id_perusahaan' => $id_perusahaan,
                'id_kantor' => $id_kantor
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



    public function update($no_kontrak, Request $request)
    {

        $no_kontrak = Crypt::decrypt($no_kontrak);
        $nik = $request->nik;
        $dari = $request->kontrak_dari;
        $sampai = $request->kontrak_sampai;
        $id_jabatan = $request->id_jabatan;
        $id_perusahaan = $request->id_perusahaan;
        $id_kantor = $request->id_kantor;
        $gaji_pokok = str_replace(".", "", $request->gaji_pokok);
        $t_jabatan = str_replace(".", "", $request->t_jabatan);
        $t_masakerja = str_replace(".", "", $request->t_masakerja);
        $t_tanggungjawab = str_replace(".", "", $request->t_tanggungjawab);
        $t_makan = str_replace(".", "", $request->t_makan);
        $t_istri = str_replace(".", "", $request->t_istri);
        $t_skill = str_replace(".", "", $request->t_skill);

        $tanggal = $request->kontrak_dari;



        DB::beginTransaction();
        try {
            DB::table('hrd_kontrak')
                ->where('no_kontrak', $no_kontrak)
                ->update([

                    'dari' => $dari,
                    'sampai' => $sampai,
                    'id_jabatan' => $id_jabatan,
                    'id_perusahaan' => $id_perusahaan,
                    'id_kantor' => $id_kantor
                ]);

            DB::table('hrd_mastergaji')
                ->where('no_kontrak', $no_kontrak)
                ->update([
                    'gaji_pokok' => $gaji_pokok,
                    't_jabatan' => $t_jabatan,
                    't_masakerja' => $t_masakerja,
                    't_tanggungjawab' => $t_tanggungjawab,
                    't_makan' => $t_makan,
                    't_istri' => $t_istri,
                    't_skill' => $t_skill,
                    'tgl_berlaku' => $dari,
                ]);

            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
        }
    }

    public function storefrompenilaian(Request $request)
    {
        $kode_penilaian = $request->kode_penilaian;
        $nik = $request->nik;
        $dari = $request->kontrak_dari;
        $sampai = $request->kontrak_sampai;
        $id_jabatan = $request->id_jabatan;
        $id_perusahaan = $request->id_perusahaan;

        $gaji_pokok = isset($request->gaji_pokok) ?  str_replace(".", "", $request->gaji_pokok) : 0;
        $t_jabatan = isset($request->t_jabatan) ?  str_replace(".", "", $request->t_jabatan) : 0;
        $t_masakerja = isset($request->gaji_pokok) ?  str_replace(".", "", $request->t_masakerja) : 0;
        $t_tanggungjawab = isset($request->t_masakerja) ?  str_replace(".", "", $request->t_tanggungjawab) : 0;
        $t_makan = isset($request->t_makan) ?  str_replace(".", "", $request->t_makan) : 0;
        $t_istri = isset($request->t_istri) ?  str_replace(".", "", $request->t_istri) : 0;
        $t_skill = isset($request->t_skill) ?  str_replace(".", "", $request->t_skill) : 0;

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
                'id_perusahaan' => $id_perusahaan,
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
            ->select('hrd_kontrak.*', 'nama_karyawan', 'tempat_lahir', 'tgl_lahir', 'alamat', 'no_ktp', 'hrd_mastergaji.*', 'hrd_penilaian.masa_kontrak_kerja', 'nama_cabang', 'nama_jabatan')
            ->join('master_karyawan', 'hrd_kontrak.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'hrd_kontrak.id_jabatan', '=', 'hrd_jabatan.id')
            ->leftJoin('hrd_mastergaji', 'hrd_kontrak.no_kontrak', '=', 'hrd_mastergaji.no_kontrak')
            ->leftJoin('hrd_penilaian', 'hrd_kontrak.kode_penilaian', '=', 'hrd_penilaian.kode_penilaian')
            ->leftJoin('cabang', 'hrd_kontrak.id_kantor', '=', 'cabang.kode_cabang')
            ->where('hrd_kontrak.no_kontrak', $no_kontrak)
            ->first();
        $approve = DB::table('hrd_approvekb')
            ->where('tgl_berlaku', '<=', $kontrak->dari)
            ->orderBy('tgl_berlaku', 'desc')->first();

        if ($kontrak->masa_kontrak_kerja != "Karyawan Tetap") {
            return view('kontrak.cetak', compact('kontrak', 'approve'));
        } else {
            return view('kontrak.cetak_pkwtt', compact('kontrak', 'approve'));
        }
    }


    public function createfromkb(Request $request)
    {
        $kode_penilaian = $request->kode_penilaian;
        $penilaian = DB::table('hrd_penilaian')
            ->select('kode_penilaian', 'hrd_penilaian.nik', 'nama_karyawan', 'hrd_penilaian.id_jabatan', 'nama_jabatan', 'hrd_penilaian.id_perusahaan', 'hrd_penilaian.masa_kontrak_kerja')
            ->join('master_karyawan', 'hrd_penilaian.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->where('kode_penilaian', $kode_penilaian)
            ->first();
        $jabatan = DB::table('hrd_jabatan')->orderBy('nama_jabatan')->get();
        $gaji = DB::table('hrd_mastergaji')->where('nik', $penilaian->nik)->orderBy('tgl_berlaku', 'desc')->first();
        return view('kontrak.createformpenilaian', compact('penilaian', 'jabatan', 'gaji'));
    }

    public function delete($no_kontrak)
    {
        $no_kontrak = Crypt::decrypt($no_kontrak);
        DB::beginTransaction();
        try {
            DB::table('hrd_kontrak')->where('no_kontrak', $no_kontrak)->delete();
            DB::table('hrd_mastergaji')->where('no_kontrak', $no_kontrak)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }
}
