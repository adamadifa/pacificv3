<?php

namespace App\Http\Controllers;

use App\Models\Bpjskesehatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class BpjskesehatanController extends Controller
{
    public function index(Request $request)
    {
        $status_aktif = $request->status_aktif_karyawan;
        $nama_karyawan = $request->nama_karyawan_search;

        $query = Bpjskesehatan::query();
        if ($status_aktif == 1 || $status_aktif === "0") {
            $query->where('status_aktif', $status_aktif);
        }

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


        $query->select('bpjs_kesehatan.*', 'nama_karyawan', 'nama_jabatan', 'nama_dept', 'id_kantor');
        $query->join('master_karyawan', 'bpjs_kesehatan.nik', '=', 'master_karyawan.nik');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');

        $bpjskes = $query->paginate(15);
        $bpjskes->appends($request->all());

        $kantor = DB::table('cabang')->orderBy('kode_cabang')->get();
        $departemen = DB::table('hrd_departemen')->get();
        $group = DB::table('hrd_group')->orderBy('nama_group')->get();
        return view('bpjskesehatan.index', compact('bpjskes', 'kantor', 'departemen', 'group'));
    }


    public function create()
    {
        $karyawan = DB::table('master_karyawan')
            ->orderBy('nama_karyawan')->get();
        return view('bpjskesehatan.create', compact('karyawan'));
    }

    public function store(Request $request)
    {
        $nik = $request->nik;
        $iuran = isset($request->iuran) ? str_replace(".", "", $request->iuran) : 0;
        $tgl_berlaku = $request->tgl_berlaku;
        $tgl = explode("-", $tgl_berlaku);
        $tahun = substr($tgl[0], 2, 2);
        $bpjskes = DB::table("bpjs_kesehatan")
            ->whereRaw('YEAR(tgl_berlaku)="' . $tgl[0] . '"')
            ->orderBy("kode_bpjs_kes", "desc")
            ->first();

        $last_kodebpjskes = $bpjskes != null ? $bpjskes->kode_bpjs_kes : '';
        $kode_bpjs_kes  = buatkode($last_kodebpjskes, "BK" . $tahun, 3);

        $data  = [
            'kode_bpjs_kes' => $kode_bpjs_kes,
            'nik' => $nik,
            'iuran' => $iuran,
            'tgl_berlaku' => $tgl_berlaku
        ];
        try {
            DB::table('bpjs_kesehatan')->insert($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Simpan']);
        }
    }

    public function delete($kode_bpjs_kes, Request $request)
    {
        $kode_bpjs_kes = Crypt::decrypt($kode_bpjs_kes);
        try {
            DB::table('bpjs_kesehatan')->where('kode_bpjs_kes', $kode_bpjs_kes)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }


    public function edit($kode_bpjs_kes)
    {
        $karyawan = DB::table('master_karyawan')->orderBy('nama_karyawan')->get();
        $kode_bpjs_kes = Crypt::decrypt($kode_bpjs_kes);
        $bpjskes = DB::table('bpjs_kesehatan')->where('kode_bpjs_kes', $kode_bpjs_kes)->first();
        return view('bpjskesehatan.edit', compact('karyawan', 'bpjskes'));
    }

    public function update($kode_bpjs_kes, Request $request)
    {
        $kode_bpjs_kes = Crypt::decrypt($kode_bpjs_kes);
        $iuran = isset($request->iuran) ? str_replace(".", "", $request->iuran) : 0;
        $data = [
            'tgl_berlaku' => $request->tgl_berlaku,
            'iuran' => $iuran
        ];

        try {
            DB::table('bpjs_kesehatan')->where('kode_bpjs_kes', $kode_bpjs_kes)->update($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }
}
