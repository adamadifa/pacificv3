<?php

namespace App\Http\Controllers;

use App\Models\Bpjstk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class BpjstkController extends Controller
{
    public function index(Request $request)
    {

        $query = Bpjstk::query();
        if (!empty($request->nama_karyawan_search)) {
            $query->where('nama_karyawan', 'like', '%' . $request->nama_karyawan_search . '%');
        }

        $query->select('bpjs_tenagakerja.*', 'nama_karyawan', 'nama_jabatan', 'nama_dept', 'id_kantor');
        $query->join('master_karyawan', 'bpjs_tenagakerja.nik', '=', 'master_karyawan.nik');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');

        $bpjstk = $query->paginate(15);
        $bpjstk->appends($request->all());
        return view('bpjstk.index', compact('bpjstk'));
    }


    public function create()
    {
        $karyawan = DB::table('master_karyawan')
            ->orderBy('nama_karyawan')->get();
        return view('bpjstk.create', compact('karyawan'));
    }

    public function store(Request $request)
    {
        $nik = $request->nik;
        $iuran = isset($request->iuran) ? str_replace(".", "", $request->iuran) : 0;
        $tgl_berlaku = $request->tgl_berlaku;
        $tgl = explode("-", $tgl_berlaku);
        $tahun = substr($tgl[0], 2, 2);
        $bpjstk = DB::table("bpjs_tenagakerja")
            ->whereRaw('YEAR(tgl_berlaku)="' . $tgl[0] . '"')
            ->orderBy("kode_bpjs_tk", "desc")
            ->first();

        $last_kodebpjstk = $bpjstk != null ? $bpjstk->kode_bpjs_tk : '';
        $kode_bpjst_tk  = buatkode($last_kodebpjstk, "BT" . $tahun, 3);

        $data  = [
            'kode_bpjs_tk' => $kode_bpjst_tk,
            'nik' => $nik,
            'iuran' => $iuran,
            'tgl_berlaku' => $tgl_berlaku
        ];
        try {
            DB::table('bpjs_tenagakerja')->insert($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Simpan']);
        }
    }

    public function delete($kode_bpjs_tk, Request $request)
    {
        $kode_bpjs_tk = Crypt::decrypt($kode_bpjs_tk);
        try {
            DB::table('bpjs_tenagakerja')->where('kode_bpjs_tk', $kode_bpjs_tk)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }


    public function edit($kode_bpjs_tk)
    {
        $karyawan = DB::table('master_karyawan')->orderBy('nama_karyawan')->get();
        $kode_bpjs_tk = Crypt::decrypt($kode_bpjs_tk);
        $bpjstk = DB::table('bpjs_tenagakerja')->where('kode_bpjs_tk', $kode_bpjs_tk)->first();
        return view('bpjstk.edit', compact('karyawan', 'bpjstk'));
    }

    public function update($kode_bpjs_tk, Request $request)
    {
        $kode_bpjs_tk = Crypt::decrypt($kode_bpjs_tk);
        $iuran = isset($request->iuran) ? str_replace(".", "", $request->iuran) : 0;
        $data = [
            'tgl_berlaku' => $request->tgl_berlaku,
            'iuran' => $iuran
        ];

        try {
            DB::table('bpjs_tenagakerja')->where('kode_bpjs_tk', $kode_bpjs_tk)->update($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }
}
