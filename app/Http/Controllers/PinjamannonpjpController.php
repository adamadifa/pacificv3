<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PinjamannonpjpController extends Controller
{
    public function create($nik)
    {
        $nik = Crypt::decrypt($nik);
        $query = Karyawan::query();
        $query->select('nik', 'nama_karyawan', 'tgl_masuk', 'nama_dept', 'jenis_kelamin', 'nama_jabatan', 'id_perusahaan', 'id_kantor', 'klasifikasi', 'status_karyawan', 'nama_cabang');
        $query->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->leftjoin('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang');
        $query->where('nik', $nik);
        $karyawan = $query->first();

        return view('pinjamannonpjp.create', compact('karyawan'));
        //return view('pinjaman.create2', compact('karyawan', 'gaji', 'jmk', 'kontrak'));
    }


    public function store(Request $request)
    {
        $tgl_pinjaman = $request->tgl_pinjaman;
        $jumlah_pinjaman = str_replace(".", "", $request->jml_pinjaman);
        $tanggal = $request->tgl_pinjaman;
        $nik = $request->nik;
        $tgl = explode("-", $tanggal);
        $tahun = substr($tgl[0], 2, 2);
        $pinjaman = DB::table("pinjaman_nonpjp")
            ->whereRaw('YEAR(tgl_pinjaman)="' . $tgl[0] . '"')
            ->orderBy("no_pinjaman_nonpjp", "desc")
            ->first();

        $last_nopinjaman = $pinjaman != null ? $pinjaman->no_pinjaman_nonpjp : '';
        $no_pinjaman  = buatkode($last_nopinjaman, "NPJ" . $tahun, 3);

        try {
            DB::table('pinjaman_nonpjp')->insert([
                'no_pinjaman_nonpjp' => $no_pinjaman,
                'nik' => $nik,
                'tgl_pinjaman' => $tgl_pinjaman,
                'jumlah_pinjaman' => $jumlah_pinjaman,
                'id_user' => Auth::user()->id
            ]);

            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }
}
