<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class GajiController extends Controller
{
    public function index(Request $request)
    {
        $hakakses = config('global.gajipage');
        $level = Auth::user()->level;
        $show_for_hrd = config('global.show_for_hrd');
        $level_show_all = config('global.show_all');
        $query = Gaji::query();
        $query->select('hrd_mastergaji.*', 'nama_karyawan', 'nama_jabatan');
        $query->join('master_karyawan', 'hrd_mastergaji.nik', '=', 'master_karyawan.nik');
        $query->leftjoin('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->orderBy('kode_gaji', 'desc');
        if (!empty($request->nama_karyawan_search)) {
            $query->where('nama_karyawan', 'like', '%' . $request->nama_karyawan_search . '%');
        }

        if (!in_array($level, $level_show_all)) {
            $query->whereNotIn('id_jabatan', $show_for_hrd);
        }


        $gaji = $query->paginate(15);

        if (in_array($level, $hakakses)) {
            return view('gaji.index', compact('gaji'));
        } else {
            echo "Anda Tidak Memiliki Hak Akses";
        }
    }

    public function create()

    {
        $level = Auth::user()->level;
        $show_for_hrd = config('global.show_for_hrd');
        $level_show_all = config('global.show_all');


        if (in_array($level, $level_show_all)) {
            $karyawan = DB::table('master_karyawan')->orderBy('nama_karyawan')->get();
        } else {
            $karyawan = DB::table('master_karyawan')
                ->whereNotIn('id_jabatan', $show_for_hrd)
                ->orderBy('nama_karyawan')->get();
        }

        return view('gaji.create', compact('karyawan'));
    }

    public function store(Request $request)
    {
        $nik = $request->nik;
        $gaji_pokok = isset($request->gaji_pokok) ? str_replace(".", "", $request->gaji_pokok) : 0;
        $t_jabatan = isset($request->t_jabatan) ? str_replace(".", "", $request->t_jabatan) : 0;
        $t_masakerja = isset($request->t_masakerja) ? str_replace(".", "", $request->t_masakerja) : 0;
        $t_tanggungjawab = isset($request->t_tanggungjawab) ? str_replace(".", "", $request->t_tanggungjawab) : 0;
        $t_makan = isset($request->t_makan) ? str_replace(".", "", $request->t_makan) : 0;
        $t_istri = isset($request->t_istri) ? str_replace(".", "", $request->t_istri) : 0;
        $t_skill = isset($request->t_skill) ?  str_replace(".", "", $request->t_skill) : 0;
        $tgl_berlaku = $request->tgl_berlaku;

        $tgl = explode("-", $tgl_berlaku);
        $tahun = substr($tgl[0], 2, 2);
        $gaji = DB::table("hrd_mastergaji")
            ->whereRaw('YEAR(tgl_berlaku)="' . $tgl[0] . '"')
            ->orderBy("kode_gaji", "desc")
            ->first();

        $last_kodegaji = $gaji != null ? $gaji->kode_gaji : '';
        $kode_gaji  = buatkode($last_kodegaji, "GJ" . $tahun, 3);

        $data  = [
            'kode_gaji' => $kode_gaji,
            'nik' => $nik,
            'gaji_pokok' => $gaji_pokok,
            't_jabatan' => $t_jabatan,
            't_masakerja' => $t_masakerja,
            't_tanggungjawab' => $t_tanggungjawab,
            't_makan' => $t_makan,
            't_istri' => $t_istri,
            't_skill' => $t_skill,
            'tgl_berlaku' => $tgl_berlaku
        ];
        try {
            DB::table('hrd_mastergaji')->insert($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Simpan']);
        }
    }

    public function edit($kode_gaji)
    {
        $karyawan = DB::table('master_karyawan')->orderBy('nama_karyawan')->get();
        $kode_gaji = Crypt::decrypt($kode_gaji);
        $gaji = DB::table('hrd_mastergaji')->where('kode_gaji', $kode_gaji)->first();
        return view('gaji.edit', compact('karyawan', 'gaji'));
    }


    public function update($kode_gaji, Request $request)
    {
        $kode_gaji = Crypt::decrypt($kode_gaji);
        $nik = $request->nik;
        $gaji_pokok = isset($request->gaji_pokok) ? str_replace(".", "", $request->gaji_pokok) : 0;
        $t_jabatan = isset($request->t_jabatan) ? str_replace(".", "", $request->t_jabatan) : 0;
        $t_masakerja = isset($request->t_masakerja) ? str_replace(".", "", $request->t_masakerja) : 0;
        $t_tanggungjawab = isset($request->t_tanggungjawab) ? str_replace(".", "", $request->t_tanggungjawab) : 0;
        $t_makan = isset($request->t_makan) ? str_replace(".", "", $request->t_makan) : 0;
        $t_istri = isset($request->t_istri) ? str_replace(".", "", $request->t_istri) : 0;
        $t_skill = isset($request->t_skill) ?  str_replace(".", "", $request->t_skill) : 0;
        $t_skill = isset($request->t_skill) ?  str_replace(".", "", $request->t_skill) : 0;
        $tgl_berlaku = $request->tgl_berlaku;



        $data  = [
            'nik' => $nik,
            'gaji_pokok' => $gaji_pokok,
            't_jabatan' => $t_jabatan,
            't_masakerja' => $t_masakerja,
            't_tanggungjawab' => $t_tanggungjawab,
            't_makan' => $t_makan,
            't_istri' => $t_istri,
            't_skill' => $t_skill,
            'tgl_berlaku' => $tgl_berlaku
        ];
        try {
            DB::table('hrd_mastergaji')->where('kode_gaji', $kode_gaji)->update($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Simpan']);
        }
    }

    public function delete($kode_gaji)
    {
        $kode_gaji = Crypt::decrypt($kode_gaji);
        try {
            DB::table('hrd_mastergaji')->where('kode_gaji', $kode_gaji)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }


    public function slipgaji()
    {
        return view('gaji.slipgaji');
    }
}
