<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class RekeningkaryawanController extends Controller
{
    public function index(Request $request)
    {
        $hakakses = config('global.karyawanpage');
        $level = Auth::user()->level;
        $cabang = Auth::user()->kode_cabang;
        $nama_karyawan = $request->nama_karyawan_search;
        $status_aktif = $request->status_aktif_karyawan;

        //dd($status_aktif);
        $query = Karyawan::query();
        $query->select('nik', 'nama_karyawan', 'tgl_masuk', 'master_karyawan.kode_dept', 'nama_dept', 'jenis_kelamin', 'no_rekening', 'nama_rekening', 'nama_jabatan', 'id_perusahaan', 'id_kantor', 'klasifikasi', 'status_karyawan', 'pin', 'status_aktif', 'lock_location');
        $query->leftjoin('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->leftjoin('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');

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

        if ($level == "kepala admin") {
            $query->where('master_karyawan.id_kantor', $cabang);
            $query->where('master_karyawan.id_perusahaan', "MP");
            $query->where('nama_jabatan', '!=', 'KEPALA ADMIN');
        }

        if ($level == "kepala penjualan") {
            if (Auth::user()->id == "27") {
                $query->whereIn('master_karyawan.id_kantor', [$cabang, 'PWK']);
            } else {
                $query->where('master_karyawan.id_kantor', $cabang);
            }
            $query->where('nama_jabatan', '!=', 'KEPALA PENJUALAN');
            $query->where('master_karyawan.id_perusahaan', "PCF");
        }

        if ($level == "manager pembelian") {
            $query->where('master_karyawan.kode_dept', 'PMB');
        }

        if ($level == "kepala gudang") {
            $query->where('master_karyawan.kode_dept', 'GDG');
            $query->whereNotIN('nama_jabatan', ['MANAGER', 'ASST. MANAGER']);
        }

        if ($level == "spv produksi") {
            $query->where('master_karyawan.kode_dept', 'PRD');
            $query->whereNotIN('nama_jabatan', ['MANAGER', 'SUPERVISOR']);
        }

        if ($level == "spv maintenance") {
            $query->where('master_karyawan.kode_dept', 'MTC');
        }

        if ($level == "manager produksi") {
            $query->whereIn('master_karyawan.kode_dept', ['PRD', 'MTC']);
            $query->where('nama_jabatan', '!=', 'MANAGER');
        }

        if ($level == "manager ga") {
            $query->where('master_karyawan.kode_dept', 'GAF');
        }

        if ($level == "emf") {
            $query->whereIn('master_karyawan.kode_dept', ['PMB', 'PRD', 'GAF', 'GDG', 'HRD', 'PDQ']);
        }

        if ($level == "admin pdqc") {
            $listkaryawan = [
                '08.12.100',
                '11.10.090',
                '13.02.198',
                '91.01.016',
                '03.04.045',
                '08.05.042',
                '12.09.182',
                '05.01.055',
                '13.03.202'
            ];

            $query->whereIn('nik', $listkaryawan);
        }

        if ($level == "spv pdqc") {
            $listkaryawan = [
                '13.03.200',
                '14.08.220',
                '13.07.021',
                '15.05.174',
                '10.08.128',
                '13.09.206',
                '13.09.209',
                '19.09.303',
                '21.06.304',
                '16.01.069',
                '18.03.305'
            ];

            $query->whereIn('nik', $listkaryawan);
        }




        if ($level == "manager marketing") {
            $query->where('master_karyawan.kode_dept', 'MKT');
            $query->where('nama_jabatan', 'REGIONAL SALES MANAGER');
        }

        if ($level == "manager audit") {
            $query->where('master_karyawan.kode_dept', 'ADT');
        }

        if ($level == "rsm") {
            $list_wilayah = Auth::user()->wilayah != null ? unserialize(Auth::user()->wilayah) : NULL;
            $wilayah = $list_wilayah != null ? "'" . implode("', '", $list_wilayah) . "'" : '';
            $query->whereIn('master_karyawan.id_kantor', $list_wilayah);
            $query->where('master_karyawan.kode_dept', 'MKT');
            $query->where('nama_jabatan', 'KEPALA PENJUALAN');
            $query->where('id_perusahaan', 'PCF');
        }

        $query->orderBy('nama_karyawan');
        $karyawan = $query->paginate(15);
        $karyawan->appends($request->all());
        $kantor = DB::table('cabang')->orderBy('kode_cabang')->get();
        $departemen = DB::table('hrd_departemen')->get();
        $group = DB::table('hrd_group')->orderBy('nama_group')->get();
        if (in_array($level, $hakakses)) {
            return view('rekeningkaryawan.index', compact('karyawan', 'departemen', 'kantor', 'group'));
        } else {
            echo "Anda Tidak Punya Hak Akses";
        }
    }


    public function edit($nik)
    {
        $nik = Crypt::decrypt($nik);
        $karyawan = DB::table('master_karyawan')->where('nik', $nik)->first();
        return view('rekeningkaryawan.edit', compact('karyawan'));
    }

    public function update(Request $request, $nik)
    {
        $nik = Crypt::decrypt($nik);
        try {
            DB::table('master_karyawan')->where('nik', $nik)->update([
                'no_rekening' => $request->no_rekening,
                'nama_rekening' => $request->nama_rekening
            ]);

            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }
}
