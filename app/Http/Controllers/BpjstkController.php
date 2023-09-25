<?php

namespace App\Http\Controllers;

use App\Models\Bpjstk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class BpjstkController extends Controller
{
    public function index(Request $request)
    {

        $query = Bpjstk::query();
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
}
