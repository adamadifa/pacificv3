<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $nama_karyawan = $request->nama_karyawan_search;
        $query = Karyawan::query();
        $query->select('nik', 'nama_karyawan', 'tgl_masuk', 'nama_dept', 'jenis_kelamin', 'nama_jabatan', 'id_perusahaan', 'id_kantor', 'klasifikasi', 'status_karyawan');
        $query->join('departemen', 'master_karyawan.kode_dept', '=', 'departemen.kode_dept');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
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
        $query->orderBy('nama_karyawan');
        $karyawan = $query->paginate(15);
        $karyawan->appends($request->all());
        $kantor = DB::table('cabang')->orderBy('kode_cabang')->get();
        $departemen = DB::table('departemen')->where('status_pengajuan', 0)->get();
        $group = DB::table('hrd_group')->orderBy('nama_group')->get();
        return view('karyawan.index', compact('karyawan', 'departemen', 'kantor', 'group'));
    }

    public function create()
    {
        $departemen = DB::table('departemen')->where('status_pengajuan', 0)->get();
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        $jabatan = DB::table('hrd_jabatan')->orderBy('nama_jabatan')->get();
        $group = DB::table('hrd_group')->orderBy('nama_group')->get();
        return view('karyawan.create', compact('cabang', 'departemen', 'jabatan', 'group'));
    }

    public function store(Request $request)
    {
        $nik = $request->nik;
        $no_ktp = $request->no_ktp;
        $nama_karyawan = $request->nama_karyawan;
        $tgl_masuk = $request->tgl_masuk;
        $kode_dept = $request->kode_dept;
        $id_jabatan = $request->id_jabatan;
        $id_kantor = $request->id_kantor;
        $id_perusahaan = $request->id_perusahaan;
        $klasifikasi = $request->klasifikasi;
        $tempat_lahir = $request->tempat_lahir;
        $tgl_lahir = $request->tgl_lahir;
        $alamat = $request->alamat;
        $no_hp = $request->no_hp;
        $pendidikan_terakhir = $request->pendidikan_terakhir;
        $grup = $request->grup;
        $jenis_kelamin = $request->jenis_kelamin;
        $status_kawin = $request->status_kawin;
        $status_karyawan = $request->status_karyawan;

        $data = [
            'nik' => $nik,
            'no_ktp' => $no_ktp,
            'nama_karyawan' => $nama_karyawan,
            'tgl_masuk' => $tgl_masuk,
            'kode_dept' => $kode_dept,
            'id_jabatan' => $id_jabatan,
            'id_kantor' => $id_kantor,
            'id_perusahaan' => $id_perusahaan,
            'klasifikasi' => $klasifikasi,
            'tempat_lahir' => $tempat_lahir,
            'tgl_lahir' => $tgl_lahir,
            'alamat' => $alamat,
            'no_hp' => $no_hp,
            'pendidikan_terakhir' => $pendidikan_terakhir,
            'grup' => $grup,
            'jenis_kelamin' => $jenis_kelamin,
            'status_kawin' => $status_kawin,
            'status_karyawan' => $status_karyawan
        ];

        $simpan = DB::table('master_karyawan')->insert($data);
        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }


    public function edit($nik)
    {
        $nik = Crypt::decrypt($nik);
        $departemen = DB::table('departemen')->where('status_pengajuan', 0)->get();
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        $jabatan = DB::table('hrd_jabatan')->orderBy('nama_jabatan')->get();
        $group = DB::table('hrd_group')->orderBy('nama_group')->get();
        $karyawan = DB::table('master_karyawan')->where('nik', $nik)->first();
        return view('karyawan.edit', compact('cabang', 'departemen', 'jabatan', 'group', 'karyawan'));
    }

    public function update($nik, Request $request)
    {
        $nik = Crypt::decrypt($nik);
        $no_ktp = $request->no_ktp;
        $nama_karyawan = $request->nama_karyawan;
        $tgl_masuk = $request->tgl_masuk;
        $kode_dept = $request->kode_dept;
        $id_jabatan = $request->id_jabatan;
        $id_kantor = $request->id_kantor;
        $id_perusahaan = $request->id_perusahaan;
        $klasifikasi = $request->klasifikasi;
        $tempat_lahir = $request->tempat_lahir;
        $tgl_lahir = $request->tgl_lahir;
        $alamat = $request->alamat;
        $no_hp = $request->no_hp;
        $pendidikan_terakhir = $request->pendidikan_terakhir;
        $grup = $request->grup;
        $jenis_kelamin = $request->jenis_kelamin;
        $status_kawin = $request->status_kawin;
        $status_karyawan = $request->status_karyawan;

        $data = [
            'no_ktp' => $no_ktp,
            'nama_karyawan' => $nama_karyawan,
            'tgl_masuk' => $tgl_masuk,
            'kode_dept' => $kode_dept,
            'id_jabatan' => $id_jabatan,
            'id_kantor' => $id_kantor,
            'id_perusahaan' => $id_perusahaan,
            'klasifikasi' => $klasifikasi,
            'tempat_lahir' => $tempat_lahir,
            'tgl_lahir' => $tgl_lahir,
            'alamat' => $alamat,
            'no_hp' => $no_hp,
            'pendidikan_terakhir' => $pendidikan_terakhir,
            'grup' => $grup,
            'jenis_kelamin' => $jenis_kelamin,
            'status_kawin' => $status_kawin,
            'status_karyawan' => $status_karyawan
        ];

        $simpan = DB::table('master_karyawan')->where('nik', $nik)->update($data);
        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
        }
    }

    public function show($nik)
    {
        $nik = Crypt::decrypt($nik);
        $karyawan = DB::table('master_karyawan')
            ->join('departemen', 'master_karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->leftJoin('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang')
            ->leftJoin('hrd_group', 'master_karyawan.grup', '=', 'hrd_group.id')
            ->where('nik', $nik)->first();

        $kontrak = DB::table('hrd_kontrak')
            ->select('hrd_kontrak.*', 'nama_karyawan', 'nama_jabatan')
            ->leftjoin('hrd_jabatan', 'hrd_kontrak.id_jabatan', '=', 'hrd_jabatan.id')
            ->leftjoin('master_karyawan', 'hrd_kontrak.nik', '=', 'master_karyawan.nik')
            ->orderBy('hrd_kontrak.dari', 'asc')
            ->where('hrd_kontrak.nik', $nik)->orderBy('dari')->get();
        return view('karyawan.show', compact('karyawan', 'kontrak'));
    }
}
