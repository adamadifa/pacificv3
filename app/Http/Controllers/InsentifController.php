<?php

namespace App\Http\Controllers;

use App\Models\Insentif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class InsentifController extends Controller
{
    public function index(Request $request)
    {

        $level = Auth::user()->level;
        $show_for_hrd = config('global.show_for_hrd');
        $level_show_all = config('global.show_all');
        $query = Insentif::query();
        $query->select('hrd_masterinsentif.*', 'nama_karyawan', 'nama_jabatan');
        $query->join('master_karyawan', 'hrd_masterinsentif.nik', '=', 'master_karyawan.nik');
        $query->leftjoin('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->orderBy('kode_insentif', 'desc');
        if (!empty($request->nama_karyawan_search)) {
            $query->where('nama_karyawan', 'like', '%' . $request->nama_karyawan_search . '%');
        }

        if (!in_array($level, $level_show_all)) {
            $query->whereNotIn('id_jabatan', $show_for_hrd);
        }
        $insentif = $query->paginate(15);
        return view('insentif.index', compact('insentif'));
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

        return view('insentif.create', compact('karyawan'));
    }


    public function store(Request $request)
    {
        $nik = $request->nik;
        $iu_masakerja = isset($request->iu_masakerja) ? str_replace(".", "", $request->iu_masakerja) : 0;
        $iu_lembur = isset($request->iu_lembur) ? str_replace(".", "", $request->iu_lembur) : 0;
        $iu_penempatan = isset($request->iu_penempatan) ? str_replace(".", "", $request->iu_penempatan) : 0;
        $iu_kpi = isset($request->iu_kpi) ? str_replace(".", "", $request->iu_kpi) : 0;
        $im_ruanglingkup = isset($request->im_ruanglingkup) ? str_replace(".", "", $request->im_ruanglingkup) : 0;
        $im_penempatan = isset($request->im_penempatan) ? str_replace(".", "", $request->im_penempatan) : 0;
        $im_kinerja = isset($request->im_kinerja) ? str_replace(".", "", $request->im_kinerja) : 0;

        $tgl_berlaku = $request->tgl_berlaku;

        $tgl = explode("-", $tgl_berlaku);
        $tahun = substr($tgl[0], 2, 2);
        $insentif = DB::table("hrd_masterinsentif")
            ->whereRaw('YEAR(tgl_berlaku)="' . $tgl[0] . '"')
            ->orderBy("kode_insentif", "desc")
            ->first();

        $last_kodeinsentif = $insentif != null ? $insentif->kode_insentif : '';
        $kode_insentif  = buatkode($last_kodeinsentif, "IS" . $tahun, 3);

        $data  = [
            'kode_insentif' => $kode_insentif,
            'nik' => $nik,
            'iu_masakerja' => $iu_masakerja,
            'iu_lembur' => $iu_lembur,
            'iu_penempatan' => $iu_penempatan,
            'iu_kpi' => $iu_kpi,
            'im_ruanglingkup' => $im_ruanglingkup,
            'im_penempatan' => $im_penempatan,
            'im_kinerja' => $im_kinerja,
            'tgl_berlaku' => $tgl_berlaku
        ];
        try {
            DB::table('hrd_masterinsentif')->insert($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Simpan']);
        }
    }


    public function edit($kode_insentif)
    {
        $karyawan = DB::table('master_karyawan')->orderBy('nama_karyawan')->get();
        $kode_insentif = Crypt::decrypt($kode_insentif);
        $insentif = DB::table('hrd_masterinsentif')->where('kode_insentif', $kode_insentif)->first();
        return view('insentif.edit', compact('karyawan', 'insentif'));
    }


    public function update($kode_insentif, Request $request)
    {
        $kode_insentif = Crypt::decrypt($kode_insentif);
        $nik = $request->nik;
        $iu_masakerja = isset($request->iu_masakerja) ? str_replace(".", "", $request->iu_masakerja) : 0;
        $iu_lembur = isset($request->iu_lembur) ? str_replace(".", "", $request->iu_lembur) : 0;
        $iu_penempatan = isset($request->iu_penempatan) ? str_replace(".", "", $request->iu_penempatan) : 0;
        $iu_kpi = isset($request->iu_kpi) ? str_replace(".", "", $request->iu_kpi) : 0;
        $im_ruanglingkup = isset($request->im_ruanglingkup) ? str_replace(".", "", $request->im_ruanglingkup) : 0;
        $im_penempatan = isset($request->im_penempatan) ? str_replace(".", "", $request->im_penempatan) : 0;
        $im_kinerja = isset($request->im_kinerja) ? str_replace(".", "", $request->im_kinerja) : 0;

        $tgl_berlaku = $request->tgl_berlaku;

        $tgl = explode("-", $tgl_berlaku);


        $data  = [

            'nik' => $nik,
            'iu_masakerja' => $iu_masakerja,
            'iu_lembur' => $iu_lembur,
            'iu_penempatan' => $iu_penempatan,
            'iu_kpi' => $iu_kpi,
            'im_ruanglingkup' => $im_ruanglingkup,
            'im_penempatan' => $im_penempatan,
            'im_kinerja' => $im_kinerja,
            'tgl_berlaku' => $tgl_berlaku
        ];
        try {
            DB::table('hrd_masterinsentif')->where('kode_insentif', $kode_insentif)->update($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Diupdate']);
        }
    }

    public function delete($kode_insentif)
    {
        $kode_insentif = Crypt::decrypt($kode_insentif);
        try {
            DB::table('hrd_masterinsentif')->where('kode_insentif', $kode_insentif)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }
}
