<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Karyawan;
use App\Models\Kontrak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class KaryawanController extends Controller
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
        $query->select('nik', 'nama_karyawan', 'tgl_masuk', 'master_karyawan.kode_dept', 'nama_dept', 'jenis_kelamin', 'nama_jabatan', 'id_perusahaan', 'id_kantor', 'klasifikasi', 'status_karyawan', 'pin', 'status_aktif', 'lock_location');
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

        if ($level == "rom") {
            $query->where('nama_jabatan', 'KEPALA ADMIN');
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
            $query->whereNotIN('nama_jabatan', ['MANAGER', 'ASST. MANAGER']);
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
                '13.03.202',
                '17.08.287'
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
            return view('karyawan.index', compact('karyawan', 'departemen', 'kantor', 'group'));
        } else {
            echo "Anda Tidak Punya Hak Akses";
        }
    }

    public function create()
    {
        $departemen = DB::table('hrd_departemen')->get();
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        $jabatan = DB::table('hrd_jabatan')->orderBy('nama_jabatan')->get();
        $group = DB::table('hrd_group')->orderBy('nama_group')->get();
        $status_perkawinan = DB::table('hrd_status_perkawinan')->orderBy('kode_perkawinan')->get();
        return view('karyawan.create', compact('cabang', 'departemen', 'jabatan', 'group', 'status_perkawinan'));
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
            'status_karyawan' => $status_karyawan,
            'status_aktif' => 1
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
        $departemen = DB::table('hrd_departemen')->get();
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        $jabatan = DB::table('hrd_jabatan')->orderBy('nama_jabatan')->get();
        $group = DB::table('hrd_group')->orderBy('nama_group')->get();
        $karyawan = DB::table('master_karyawan')->where('nik', $nik)->first();
        $status_perkawinan = DB::table('hrd_status_perkawinan')->orderBy('kode_perkawinan')->get();
        return view('karyawan.edit', compact('cabang', 'departemen', 'jabatan', 'group', 'karyawan', 'status_perkawinan'));
    }

    public function update($nik, Request $request)
    {
        $nik = Crypt::decrypt($nik);
        $nik_baru = $request->nik_baru;
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
        $status_aktif = $request->status_aktif;
        $pin = $request->pin;
        $tgl_nonaktif = $request->tgl_nonaktif;
        $tgl_off_gaji = $request->tgl_off_gaji;
        if (!empty($pin)) {
            $cek = DB::table('master_karyawan')->where('pin', $pin)->where('nik', '!=', $nik)->count();
        } else {
            $cek = 0;
        }
        if ($cek > 0) {
            return Redirect::back()->with(['warning' => 'Pin Sudah Terdaftar']);
        }
        $data = [
            'nik' => $nik_baru,
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
            'status_karyawan' => $status_karyawan,
            'status_aktif' => $status_aktif,
            'pin' => $pin,
            'tgl_nonaktif' => $tgl_nonaktif,
            'tgl_off_gaji' => $tgl_off_gaji
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
            ->leftjoin('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->leftjoin('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
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

    public function getkaryawankontrak(Request $request)
    {
        $nik = $request->nik;
        $karyawan = DB::table('master_karyawan')->where('nik', $nik)->first();
        echo $karyawan->id_jabatan . "|" . $karyawan->kode_dept . "|" . $karyawan->id_kantor . "|" . $karyawan->id_perusahaan;
    }


    public function habiskontrak()
    {
        $level = Auth::user()->level;
        $cabang = Auth::user()->kode_cabang;
        $hariini = date("Y-m-d");
        $bulanini = date("m");
        $tahunini = date("Y");
        $bulandepan = date("m") + 1 > 12 ? (date("m") + 1) - 12 : date("m") + 1;
        $tahun2 = date("m") + 1  > 12 ? $tahunini + 1 : $tahunini;
        $duabulan = date("m") + 2 > 12 ? (date("m") + 2) - 12 : date("m") + 2;
        $tahun3 = date("m") + 2 > 12 ? $tahunini + 1 : $tahunini;
        $qkontrak_lewat = Kontrak::query();
        $qkontrak_lewat->selectRaw('hrd_kontrak.nik, nama_karyawan, IFNULL(jb.nama_jabatan,jb2.nama_jabatan) as nama_jabatan, IFNULL(hrd_kontrak.kode_dept,master_karyawan.kode_dept) as kode_dept, sampai, IFNULL(hrd_kontrak.id_perusahaan,master_karyawan.id_perusahaan) as id_perusahaan, IFNULL(hrd_kontrak.id_kantor,master_karyawan.id_kantor) as id_kantor');
        $qkontrak_lewat->join('master_karyawan', 'hrd_kontrak.nik', '=', 'master_karyawan.nik');
        $qkontrak_lewat->leftjoin('hrd_jabatan as jb', 'hrd_kontrak.id_jabatan', '=', 'jb.id');
        $qkontrak_lewat->leftjoin('hrd_jabatan as jb2', 'master_karyawan.id_jabatan', '=', 'jb2.id');
        $qkontrak_lewat->where('sampai', '<', $hariini);
        $qkontrak_lewat->where('status_kontrak', 1);
        $qkontrak_lewat->where('status_karyawan', 'K');
        if ($level == "kepala admin") {
            $qkontrak_lewat->where('master_karyawan.id_kantor', $cabang);
            $qkontrak_lewat->where('master_karyawan.id_perusahaan', "MP");
        }

        if ($level == "kepala penjualan") {
            $qkontrak_lewat->where('master_karyawan.id_kantor', $cabang);
            $qkontrak_lewat->where('master_karyawan.id_perusahaan', "PCF");
        }

        if ($level == "manager pembelian") {
            $qkontrak_lewat->where('master_karyawan.kode_dept', 'PMB');
        }

        if ($level == "kepala gudang") {
            $qkontrak_lewat->where('master_karyawan.kode_dept', 'GDG');
        }

        if ($level == "spv maintenance") {
            $qkontrak_lewat->where('master_karyawan.kode_dept', 'MTC');
        }



        if ($level == "manager ga") {
            $qkontrak_lewat->where('master_karyawan.kode_dept', 'GAF');
        }

        if ($level == "emf") {
            $qkontrak_lewat->whereIn('master_karyawan.kode_dept', ['PMB', 'PRD', 'GAF', 'GDG', 'PDQ']);
        }


        if ($level == "manager marketing") {
            $qkontrak_lewat->where('master_karyawan.kode_dept', 'MKT');
        }

        if ($level == "rsm") {
            $list_wilayah = Auth::user()->wilayah != null ? unserialize(Auth::user()->wilayah) : NULL;
            $wilayah = $list_wilayah != null ? "'" . implode("', '", $list_wilayah) . "'" : '';
            $qkontrak_lewat->whereIn('master_karyawan.id_kantor', $list_wilayah);
        }
        $qkontrak_lewat->orderBy('sampai');

        $qkontrak_bulanini = Kontrak::query();
        $qkontrak_bulanini->selectRaw('hrd_kontrak.nik, nama_karyawan, IFNULL(jb.nama_jabatan,jb2.nama_jabatan) as nama_jabatan, IFNULL(hrd_kontrak.kode_dept,master_karyawan.kode_dept) as kode_dept, sampai, IFNULL(hrd_kontrak.id_perusahaan,master_karyawan.id_perusahaan) as id_perusahaan, IFNULL(hrd_kontrak.id_kantor,master_karyawan.id_kantor) as id_kantor');
        $qkontrak_bulanini->join('master_karyawan', 'hrd_kontrak.nik', '=', 'master_karyawan.nik');
        $qkontrak_bulanini->leftjoin('hrd_jabatan as jb', 'hrd_kontrak.id_jabatan', '=', 'jb.id');
        $qkontrak_bulanini->leftjoin('hrd_jabatan as jb2', 'master_karyawan.id_jabatan', '=', 'jb2.id');
        $qkontrak_bulanini->whereRaw('MONTH(sampai)=' . $bulanini);
        $qkontrak_bulanini->whereRaw('YEAR(sampai)=' . $tahunini);
        $qkontrak_bulanini->where('status_kontrak', 1);
        $qkontrak_bulanini->where('status_karyawan', 'K');
        if ($level == "kepala admin") {
            $qkontrak_bulanini->where('master_karyawan.id_kantor', $cabang);
            $qkontrak_bulanini->where('master_karyawan.id_perusahaan', "MP");
        }

        if ($level == "kepala penjualan") {
            $qkontrak_bulanini->where('master_karyawan.id_kantor', $cabang);
            $qkontrak_bulanini->where('master_karyawan.id_perusahaan', "PCF");
        }

        if ($level == "manager pembelian") {
            $qkontrak_bulanini->where('master_karyawan.kode_dept', 'PMB');
        }

        if ($level == "kepala gudang") {
            $qkontrak_bulanini->where('master_karyawan.kode_dept', 'GDG');
        }

        if ($level == "manager produksi" || $level == "spv produksi") {
            $qkontrak_bulanini->where('master_karyawan.kode_dept', 'PRD');
        }


        if ($level == "spv maintenance") {
            $qkontrak_bulanini->where('master_karyawan.kode_dept', 'MTC');
        }

        if ($level == "manager ga") {
            $qkontrak_bulanini->where('master_karyawan.kode_dept', 'GAF');
        }

        if ($level == "emf") {
            $qkontrak_bulanini->whereIn('master_karyawan.kode_dept', ['PMB', 'PRD', 'GAF', 'GDG', 'PDQ']);
        }


        if ($level == "manager marketing") {
            $qkontrak_bulanini->where('master_karyawan.kode_dept', 'MKT');
        }

        if ($level == "rsm") {
            $list_wilayah = Auth::user()->wilayah != null ? unserialize(Auth::user()->wilayah) : NULL;
            $wilayah = $list_wilayah != null ? "'" . implode("', '", $list_wilayah) . "'" : '';
            $qkontrak_bulanini->whereIn('master_karyawan.id_kantor', $list_wilayah);
        }
        $qkontrak_bulanini->orderBy('sampai');


        $qkontrak_bulandepan = Kontrak::query();
        $qkontrak_bulandepan->selectRaw('hrd_kontrak.nik, nama_karyawan, IFNULL(jb.nama_jabatan,jb2.nama_jabatan) as nama_jabatan, IFNULL(hrd_kontrak.kode_dept,master_karyawan.kode_dept) as kode_dept, sampai, IFNULL(hrd_kontrak.id_perusahaan,master_karyawan.id_perusahaan) as id_perusahaan, IFNULL(hrd_kontrak.id_kantor,master_karyawan.id_kantor) as id_kantor');
        $qkontrak_bulandepan->join('master_karyawan', 'hrd_kontrak.nik', '=', 'master_karyawan.nik');
        $qkontrak_bulandepan->leftjoin('hrd_jabatan as jb', 'hrd_kontrak.id_jabatan', '=', 'jb.id');
        $qkontrak_bulandepan->leftjoin('hrd_jabatan as jb2', 'master_karyawan.id_jabatan', '=', 'jb2.id');
        $qkontrak_bulandepan->whereRaw('MONTH(sampai)=' . $bulandepan);
        $qkontrak_bulandepan->whereRaw('YEAR(sampai)=' . $tahun2);
        $qkontrak_bulandepan->where('status_kontrak', 1);
        $qkontrak_bulandepan->where('status_karyawan', 'K');
        if ($level == "kepala admin") {
            $qkontrak_bulandepan->where('master_karyawan.id_kantor', $cabang);
            $qkontrak_bulandepan->where('master_karyawan.id_perusahaan', "MP");
        }

        if ($level == "kepala penjualan") {
            $qkontrak_bulandepan->where('master_karyawan.id_kantor', $cabang);
            $qkontrak_bulandepan->where('master_karyawan.id_perusahaan', "PCF");
        }

        if ($level == "manager pembelian") {
            $qkontrak_bulandepan->where('master_karyawan.kode_dept', 'PMB');
        }

        if ($level == "kepala gudang") {
            $qkontrak_bulandepan->where('master_karyawan.kode_dept', 'GDG');
        }

        if ($level == "manager produksi" || $level == "spv produksi") {
            $qkontrak_bulandepan->where('master_karyawan.kode_dept', 'PRD');
        }

        if ($level == "spv maintenance") {
            $qkontrak_bulandepan->where('master_karyawan.kode_dept', 'MTC');
        }

        if ($level == "manager ga") {
            $qkontrak_bulandepan->where('master_karyawan.kode_dept', 'GAF');
        }

        if ($level == "emf") {
            $qkontrak_bulandepan->whereIn('master_karyawan.kode_dept', ['PMB', 'PRD', 'GAF', 'GDG', 'PDQ']);
        }


        if ($level == "manager marketing") {
            $qkontrak_bulandepan->where('master_karyawan.kode_dept', 'MKT');
        }

        if ($level == "rsm") {
            $list_wilayah = Auth::user()->wilayah != null ? unserialize(Auth::user()->wilayah) : NULL;
            $wilayah = $list_wilayah != null ? "'" . implode("', '", $list_wilayah) . "'" : '';
            $qkontrak_bulanini->whereIn('master_karyawan.id_kantor', $list_wilayah);
        }
        $qkontrak_bulandepan->orderBy('sampai');


        $qkontrak_duabulan = Kontrak::query();
        $qkontrak_duabulan->selectRaw('hrd_kontrak.nik, nama_karyawan, IFNULL(jb.nama_jabatan,jb2.nama_jabatan) as nama_jabatan, IFNULL(hrd_kontrak.kode_dept,master_karyawan.kode_dept) as kode_dept, sampai, IFNULL(hrd_kontrak.id_perusahaan,master_karyawan.id_perusahaan) as id_perusahaan, IFNULL(hrd_kontrak.id_kantor,master_karyawan.id_kantor) as id_kantor');
        $qkontrak_duabulan->join('master_karyawan', 'hrd_kontrak.nik', '=', 'master_karyawan.nik');
        $qkontrak_duabulan->leftjoin('hrd_jabatan as jb', 'hrd_kontrak.id_jabatan', '=', 'jb.id');
        $qkontrak_duabulan->leftjoin('hrd_jabatan as jb2', 'master_karyawan.id_jabatan', '=', 'jb2.id');
        $qkontrak_duabulan->whereRaw('MONTH(sampai)=' . $duabulan);
        $qkontrak_duabulan->whereRaw('YEAR(sampai)=' . $tahun3);
        $qkontrak_duabulan->where('status_kontrak', 1);
        $qkontrak_duabulan->where('status_karyawan', 'K');
        if ($level == "kepala admin") {
            $qkontrak_duabulan->where('master_karyawan.id_kantor', $cabang);
            $qkontrak_duabulan->where('master_karyawan.id_perusahaan', "MP");
        }

        if ($level == "kepala penjualan") {
            $qkontrak_duabulan->where('master_karyawan.id_kantor', $cabang);
            $qkontrak_duabulan->where('master_karyawan.id_perusahaan', "PCF");
        }

        if ($level == "manager pembelian") {
            $qkontrak_duabulan->where('master_karyawan.kode_dept', 'PMB');
        }

        if ($level == "kepala gudang") {
            $qkontrak_duabulan->where('master_karyawan.kode_dept', 'GDG');
        }

        if ($level == "manager produksi" || $level == "spv produksi") {
            $qkontrak_duabulan->where('master_karyawan.kode_dept', 'PRD');
        }

        if ($level == "spv maintenance") {
            $qkontrak_duabulan->where('master_karyawan.kode_dept', 'MTC');
        }

        if ($level == "manager ga") {
            $qkontrak_duabulan->where('master_karyawan.kode_dept', 'GAF');
        }

        if ($level == "emf") {
            $qkontrak_duabulan->whereIn('master_karyawan.kode_dept', ['PMB', 'PRD', 'GAF', 'GDG', 'PDQ']);
        }


        if ($level == "manager marketing") {
            $qkontrak_duabulan->where('master_karyawan.kode_dept', 'MKT');
        }
        $qkontrak_duabulan->orderBy('sampai');


        $kontrak_lewat = $qkontrak_lewat->get();
        $jml_kontrak_lewat = $qkontrak_lewat->count();

        $kontrak_bulanini = $qkontrak_bulanini->get();
        $jml_kontrak_bulanini = $qkontrak_bulanini->count();

        $kontrak_bulandepan = $qkontrak_bulandepan->get();
        $jml_kontrak_bulandepan = $qkontrak_bulandepan->count();

        $kontrak_duabulan = $qkontrak_duabulan->get();
        $jml_kontrak_duabulan = $qkontrak_duabulan->count();

        return view('karyawan.habiskontrak', compact('kontrak_lewat', 'jml_kontrak_lewat', 'kontrak_bulanini', 'jml_kontrak_bulanini', 'hariini', 'kontrak_bulandepan', 'jml_kontrak_bulandepan', 'kontrak_duabulan', 'jml_kontrak_duabulan'));
    }


    public function locklocation($nik)
    {
        $nik = Crypt::decrypt($nik);
        try {
            DB::table('master_karyawan')->where('nik', $nik)->update([
                'lock_location' => 0
            ]);
            return Redirect::back()->with(['success' => 'Data Lokasi Di Kunci']);
        } catch (\Exception $e) {

            dd($e);
            return Redirect::back()->with(['warning' => 'Data Lokai Gagal DIkunci']);
        }
    }


    public function unlocklocation($nik)
    {

        $nik = Crypt::decrypt($nik);
        try {
            DB::table('master_karyawan')->where('nik', $nik)->update([
                'lock_location' => 1
            ]);
            return Redirect::back()->with(['success' => 'Data Lokasi Di Buka']);
        } catch (\Throwable $th) {
            return Redirect::back()->with(['warning' => 'Data Lokasi Gagal Dibuka']);
        }
    }


    public function laporanKaryawan()
    {
        $cabang = Auth::user()->kode_cabang;
        if ($cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $cabang)->get();
        }
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('karyawan.laporan.frm_laporanKaryawan', compact('cabang', 'bulan'));
    }

    public function cetakKaryawan(Request $request)
    {
        $cabang = $request->kode_cabang;
        if ($cabang == '') {
            $karyawan = DB::table('master_karyawan')
                ->leftJoin('hrd_group', 'master_karyawan.grup', 'hrd_group.id')
                ->leftJoin('hrd_jabatan', 'master_karyawan.id_jabatan', 'hrd_jabatan.id')
                ->get();
        } else {
            $karyawan = DB::table('master_karyawan')
                ->leftJoin('hrd_group', 'master_karyawan.grup', 'hrd_group.id')
                ->leftJoin('hrd_jabatan', 'master_karyawan.id_jabatan', 'hrd_jabatan.id')
                ->where('master_karyawan.id_kantor', $request->kode_cabang)
                ->get();
        }
        if (isset($_POST['export'])) {
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=Laporan karyawan Program.xls");
        }
        return view('karyawan.laporan.cetak_karyawan', compact('karyawan', 'cabang'));
    }

    public function uploadktp($nik)
    {
        $nik = Crypt::decrypt($nik);
        $karyawan = DB::table('master_karyawan')->where('nik', $nik)
            ->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->first();
        return view('karyawan.uploadktp', compact('karyawan'));
    }


    public function storeuploadktp(Request $request, $nik)
    {
        $nik = Crypt::decrypt($nik);
        $nik_name = str_replace(".", "", $nik);
        $karyawan = DB::table('master_karyawan')->where('nik', $nik)->first();
        try {
            if ($request->hasfile('ktp')) {
                $ktp_name =  $nik_name . "." . $request->file('ktp')->getClientOriginalExtension();
                $destination_path = "/public/ktp";
                $ktp = $ktp_name;
            }
            $update = DB::table('master_karyawan')->where('nik', $nik)->update([
                'ktp' => $ktp
            ]);

            if ($update) {
                Storage::delete($destination_path . "/" . $karyawan->ktp);
                $request->file('ktp')->storeAs($destination_path, $ktp);
            }

            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }
}
