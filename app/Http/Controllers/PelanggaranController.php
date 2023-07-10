<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PelanggaranController extends Controller
{
    public function index(Request $request)
    {
        $kantor = DB::table('cabang')->orderBy('kode_cabang')->get();
        $departemen = DB::table('hrd_departemen')->get();
        $query = Pelanggaran::query();
        $query->select('hrd_sp.*', 'nama_karyawan', 'nama_dept', 'nama_jabatan', 'id_kantor', 'id_perusahaan');
        $query->join('master_karyawan', 'hrd_sp.nik', '=', 'master_karyawan.nik');
        $query->leftjoin('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->leftJoin('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        if (!empty($request->nama_karyawan_search)) {
            $query->where('nama_karyawan', 'like', '%' . $request->nama_karyawan_search . '%');
        }

        if (!empty($request->id_perusahaan_search)) {
            $query->where('master_karyawan.id_perusahaan', $request->kode_dept_search);
        }


        if (!empty($request->id_kantor_search)) {
            $query->where('master_karyawan.id_kantor', $request->id_kantor_search);
        }

        if (!empty($request->kode_dept_search)) {
            $query->where('master_karyawan.kode_dept', $request->kode_dept);
        }
        $query->orderBy('no_sp', 'desc');
        $pelanggaran = $query->paginate(15);
        $pelanggaran->appends($request->all());
        return view('pelanggaran.index', compact('kantor', 'departemen', 'pelanggaran'));
    }


    public function create()
    {
        $karyawan = DB::table('master_karyawan')->orderBy('nama_karyawan')->get();
        return view('pelanggaran.create', compact('karyawan'));
    }

    public function store(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $nik = $request->nik;
        $ket = $request->jenis_pelanggaran;
        $pelanggaran = $request->pelanggaran;
        $tahun = date("Y", strtotime($dari));
        $thn = substr($tahun, 2, 2);
        $sp = DB::table("hrd_sp")
            ->whereRaw('YEAR(dari)="' . $tahun . '"')
            ->orderBy("no_sp", "desc")
            ->first();
        $last_nosp = $sp != null ? $sp->no_sp : '';
        $no_sp  = buatkode($last_nosp, "SP" . $thn, 3);

        $data = [
            'no_sp' => $no_sp,
            'dari' => $dari,
            'sampai' => $sampai,
            'nik' => $nik,
            'ket' => $ket,
            'pelanggaran' => $pelanggaran
        ];
        try {
            DB::table('hrd_sp')->insert($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }


    public function edit(Request $request)
    {
        $no_sp = $request->no_sp;
        $pelanggaran = DB::table('hrd_sp')->where('no_sp', $no_sp)->first();
        $karyawan = DB::table('master_karyawan')->orderBy('nama_karyawan')->get();
        return view('pelanggaran.edit', compact('karyawan', 'pelanggaran'));
    }


    public function update($no_sp, Request $request)
    {
        $no_sp = Crypt::decrypt($no_sp);
        $dari = $request->dari;
        $sampai = $request->sampai;
        $nik = $request->nik;
        $ket = $request->jenis_pelanggaran;
        $pelanggaran = $request->pelanggaran;
        $data = [
            'dari' => $dari,
            'sampai' => $sampai,
            'nik' => $nik,
            'ket' => $ket,
            'pelanggaran' => $pelanggaran
        ];
        try {
            DB::table('hrd_sp')->where('no_sp', $no_sp)->update($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }


    public function delete($no_sp)
    {
        $no_sp = Crypt::decrypt($no_sp);
        try {
            DB::table('hrd_sp')->where('no_sp', $no_sp)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }
}
