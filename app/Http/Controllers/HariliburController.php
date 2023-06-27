<?php

namespace App\Http\Controllers;

use App\Models\Harilibur;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Expr\FuncCall;

class HariliburController extends Controller
{
    public function index(Request $request)
    {

        $query = Harilibur::query();
        if (!empty($request->bulan)) {
            $query->whereRaw('MONTH(tanggal_libur)="' . $request->bulan . '"');
        }

        if (!empty($request->tahun)) {
            $query->whereRaw('YEAR(tanggal_libur)="' . $request->tahun . '"');
        }

        if (!empty($request->kategori_search)) {
            $query->where('kategori', $request->kategori_search);
        }
        $harilibur = $query->paginate(15);
        $harilibur->appends($request->all());

        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        return view('harilibur.index', compact('harilibur', 'bulan', 'cabang'));
    }

    public function store(Request $request)
    {

        $tanggal = $request->tanggal;
        $keterangan = $request->keterangan;
        $id_kantor = $request->id_kantor;
        $kategori = $request->kategori;
        $tahun = substr(date('Y', strtotime($tanggal)), 2, 2);
        $harilibur = DB::table('harilibur')->whereRaw('MID(kode_libur,3,2)="' . $tahun . '"')
            ->orderBy('kode_libur', 'desc')->first();
        $last_kodelibur = $harilibur != null ? $harilibur->kode_libur : '';
        $kode_libur = buatkode($last_kodelibur, "LB" . $tahun, 3);

        if ($kategori == "1") {
            $beforeday = date('Y-m-d', strtotime('-1 day', strtotime($tanggal)));
        } else {
            $beforeday = null;
        }
        $data = [
            'kode_libur' => $kode_libur,
            'tanggal_libur' => $tanggal,
            'id_kantor' => $id_kantor,
            'kategori' => $kategori,
            'keterangan' => $keterangan,
            'tanggal_limajam' => $beforeday,
            'tanggal_minggu' => $request->tanggal_minggu
        ];
        try {
            $cek = DB::table('harilibur')->where('tanggal_libur', $tanggal)->count();
            if ($cek > 0) {
                return Redirect::back()->with(['warning' => 'Tanggal Libur Sudah Diinputkan Sebelumnya']);
            }
            DB::table('harilibur')->insert($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function delete($kode_libur)
    {
        $kode_libur = Crypt::decrypt($kode_libur);
        try {
            DB::table('harilibur')->where('kode_libur', $kode_libur)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    public function edit($kode_libur)
    {
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        $harilibur = DB::table('harilibur')->where('kode_libur', $kode_libur)->first();
        return view('harilibur.edit', compact('harilibur', 'cabang'));
    }

    public function update($kode_libur, Request $request)
    {
        $kode_libur = Crypt::decrypt($kode_libur);
        $tanggal = $request->tanggal;
        $keterangan = $request->keterangan;
        $id_kantor = $request->id_kantor;
        $kategori = $request->kategori;
        if ($kategori == 1) {
            $beforeday = date('Y-m-d', strtotime('-1 day', strtotime($tanggal)));
        } else {
            $beforeday = null;
        }

        if ($kategori == 2) {
            $tanggal_minggu = $request->tanggal_menu;
        } else {
            $tanggal_minggu = null;
        }
        $data = [
            'tanggal_libur' => $tanggal,
            'keterangan' => $keterangan,
            'id_kantor' => $id_kantor,
            'kategori' => $kategori,
            'tanggal_limajam' => $beforeday,
            'tanggal_minggu' => $tanggal_minggu
        ];
        try {
            $cek = DB::table('harilibur')->where('tanggal_libur', $tanggal)->where('kode_libur', '!=', $kode_libur)->count();
            if ($cek > 0) {
                return Redirect::back()->with(['warning' => 'Tanggal Libur Sudah Diinputkan Sebelumnya']);
            }
            DB::table('harilibur')
                ->where('kode_libur', $kode_libur)
                ->update($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }

    public function tambahkaryawan($kode_libur)
    {
        $kode_libur = Crypt::decrypt($kode_libur);
        $harilibur = DB::table('harilibur')->where('kode_libur', $kode_libur)->first();
        return view('harilibur.tambahkaryawan', compact('harilibur'));
    }

    public function getkaryawan($kode_libur, $id_kantor)
    {
        $departemen = DB::table('hrd_departemen')->get();
        $group = DB::table('hrd_group')->orderBy('nama_group')->get();
        return view('harilibur.getkaryawan', compact('kode_libur', 'id_kantor', 'departemen', 'group'));
    }

    public function getlistkaryawan(Request $request)
    {
        $kode_libur = $request->kode_libur;
        $id_kantor = $request->id_kantor;
        $kode_dept = $request->kode_dept;
        $id_perusahaan = $request->id_perusahaan;
        $grup = $request->grup;
        $nama_karyawan = $request->nama_karyawan;
        $query = Karyawan::query();
        $query->select('master_karyawan.nik', 'nama_karyawan', 'kode_dept', 'nama_jabatan', 'nama_group', 'kode_libur');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->join('hrd_group', 'master_karyawan.grup', '=', 'hrd_group.id');
        $query->leftJoin(
            DB::raw("(
            SELECT nik,kode_libur
            FROM harilibur_karyawan
            WHERE kode_libur = '$kode_libur'
        ) hariliburkaryawan"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'hariliburkaryawan.nik');
            }
        );

        $query->where('id_kantor', $id_kantor);
        if (!empty($kode_dept)) {
            $query->where('kode_dept', $kode_dept);
        }

        if (!empty($id_perusahaan)) {
            $query->where('id_perusahaan', $id_perusahaan);
        }

        if (!empty($grup)) {
            $query->where('grup', $grup);
        }

        if (!empty($nama_karyawan)) {
            $query->where('nama_karyawan', 'like', '%' . $nama_karyawan . '%');
        }
        $query->orderBy('nama_karyawan');
        $karyawan = $query->get();
        return view('harilibur.getlistkaryawan', compact('kode_libur', 'karyawan', 'id_kantor'));
    }

    public function storekaryawanlibur(Request $request)
    {
        $kode_libur = $request->kode_libur;
        $nik = $request->nik;
        try {
            DB::table('harilibur_karyawan')->insert([
                'kode_libur' => $kode_libur,
                'nik' => $nik
            ]);
            return 0;
        } catch (\Exception $e) {
            return 1;
        }
    }


    public function hapuskaryawanlibur(Request $request)
    {
        $kode_libur = $request->kode_libur;
        $nik = $request->nik;
        try {
            DB::table('harilibur_karyawan')->where('nik', $nik)
                ->where('kode_libur', $kode_libur)
                ->delete();
            return 0;
        } catch (\Exception $e) {
            return 1;
        }
    }


    public function getliburkaryawan($kode_libur)
    {
        $liburkaryawan = DB::table('harilibur_karyawan')
            ->select(
                'kode_libur',
                'harilibur_karyawan.nik',
                'nama_karyawan',
                'kode_dept',
                'nama_jabatan',
                'nama_group',
                'grup'
            )
            ->join('master_karyawan', 'harilibur_karyawan.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->join('hrd_group', 'master_karyawan.grup', '=', 'hrd_group.id')
            ->where('kode_libur', $kode_libur)
            ->orderByRaw('grup,nama_karyawan')
            ->get();

        return view('harilibur.getliburkaryawan', compact('liburkaryawan'));
    }


    public function hapusliburkaryawan(Request $request)
    {
        try {
            DB::table('harilibur_karyawan')->where('kode_libur', $request->kode_libur)->where('nik', $request->nik)->delete();
            return 0;
        } catch (\Exception $e) {
            return 1;
        }
    }
}
