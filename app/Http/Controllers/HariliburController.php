<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Harilibur;
use App\Models\Hariliburkaryawan;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        if (Auth::user()->kode_cabang != "PCF" && Auth::user()->kode_cabang != "PST") {
            $query->where('id_kantor', Auth::user()->kode_cabang);
        } else {
            $level_search = array("manager hrd", "admin");
            if (in_array(Auth::user()->level, $level_search)) {
                if (!empty($request->id_kantor_search)) {
                    $query->where('id_kantor', $request->id_kantor_search);
                }
            } else {
                $query->where('id_kantor', 'PST');
                $query->where('kode_dept', Auth::user()->kode_dept_presensi);
            }
        }

        $query->orderBy('tanggal_libur', 'desc');
        $harilibur = $query->paginate(15);
        $harilibur->appends($request->all());

        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();

        $departemen = DB::table('hrd_departemen')->orderBy('kode_dept')->get();
        return view('harilibur.index', compact('harilibur', 'bulan', 'cabang', 'departemen'));
    }

    public function store(Request $request)
    {

        $tanggal = $request->tanggal;
        $keterangan = $request->keterangan;
        $id_kantor = $request->id_kantor;
        $kode_dept = $request->kode_dept;
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
            'kode_dept' => $kode_dept,
            'kategori' => $kategori,
            'keterangan' => $keterangan,
            'tanggal_limajam' => $beforeday,
            'tanggal_diganti' => $request->tanggal_diganti
        ];
        try {

            if (Auth::user()->kode_cabang != "PCF" && Auth::user()->kode_cabang != "PST") {
                $cek = DB::table('harilibur')->where('tanggal_libur', $tanggal)
                    ->where('id_kantor', $id_kantor)
                    ->where('kategori', $kategori)
                    ->count();
            } else {
                $cek = DB::table('harilibur')->where('tanggal_libur', $tanggal)
                    ->where('kode_dept', $kode_dept)
                    ->where('id_kantor', $id_kantor)
                    ->where('kategori', $kategori)
                    ->count();
            }

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
            $cek = DB::table('harilibur')
                ->where('tanggal_libur', $tanggal)
                ->where('id_kantor', $id_kantor)
                ->where('kategori', $kategori)
                ->where('kode_libur', '!=', $kode_libur)->count();
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

    public function getkaryawan($kode_libur, $id_kantor, $kode_dept)
    {
        $level_access = array("manager hrd", "admin");
        $level = Auth::user()->level;
        if ($id_kantor == "PCF" || $id_kantor == "PST") {
            if (in_array($level, $level_access)) {
                $group = DB::table('hrd_group')->orderBy('nama_group')->get();
            } else {
                $group = DB::table('hrd_group')->where('kode_dept_group', $kode_dept)->orderBy('nama_group')->get();
            }
        } else {
            $group = DB::table('hrd_group')->orderBy('nama_group')->get();
        }
        $departemen = DB::table('hrd_departemen')->get();

        return view('harilibur.getkaryawan', compact('kode_libur', 'id_kantor', 'kode_dept', 'departemen', 'group'));
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

    public function approve(Request $request)
    {

        $kode_libur = Crypt::decrypt($request->kode_libur);
        if (isset($request->approve)) {
            try {
                DB::table('harilibur')->where('kode_libur', $kode_libur)->update(['hrd' => 1]);
                return Redirect::back()->with(['success' => 'Pengajuan Libur Disetujui']);
            } catch (\Exception $e) {
                return Redirect::back()->with(['warning' => 'Data Gagal di Update']);
                //throw $th;
            }
        } else {
            try {
                DB::table('harilibur')->where('kode_libur', $kode_libur)->update(['hrd' => 2]);
                return Redirect::back()->with(['success' => 'Pengajuan Libur Ditolak']);
            } catch (\Exception $e) {
                return Redirect::back()->with(['warning' => 'Data Gagal di Update']);
                //throw $th;
            }
        }
    }

    public function batalkan($kode_libur)
    {
        $kode_libur = Crypt::decrypt($kode_libur);
        try {
            DB::table('harilibur')->where('kode_libur', $kode_libur)->update(['hrd' => null]);
            return Redirect::back()->with(['success' => 'Pengajuan Libur Dibatalkan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal di Update']);
            //throw $th;
        }
    }


    public function storeallkaryawan(Request $request)
    {
        $kode_dept = $request->kode_dept;
        $kode_libur = $request->kode_libur;
        $id_kantor = $request->id_kantor;
        $grup = $request->grup;
        DB::beginTransaction();
        try {
            $nik = [];
            $qkaryawan = Karyawan::query();
            $qkaryawan->select('nik');
            $qkaryawan->where('id_kantor', $id_kantor);
            if (!empty($kode_dept) && $kode_dept != "ALL") {
                $qkaryawan->where('kode_dept', $kode_dept);
            }

            if (!empty($grup)) {
                $qkaryawan->where('grup', $grup);
            }
            $karyawan = $qkaryawan->get();
            foreach ($karyawan as $d) {
                $nik[] = $d->nik;
                $data[] = [
                    'kode_libur' => $kode_libur,
                    'nik' => $d->nik
                ];
            }


            DB::table('harilibur_karyawan')->where('kode_libur', $kode_libur)->whereIn('nik', $nik)->delete();
            //dd($data);
            $chunks = array_chunk($data, 5);
            foreach ($chunks as $chunk) {
                Hariliburkaryawan::insert($chunk);
            }

            DB::commit();
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            // return 1;
            dd($e);
        }
    }

    public function cancelkaryawan(Request $request)
    {
        $kode_dept = $request->kode_dept;
        $kode_libur = $request->kode_libur;
        $id_kantor = $request->id_kantor;
        DB::beginTransaction();
        try {
            $nik = [];
            $qkaryawan = Karyawan::query();
            $qkaryawan->select('nik');
            $qkaryawan->where('id_kantor', $id_kantor);
            if (!empty($kode_dept)) {
                $qkaryawan->where('kode_dept', $kode_dept);
            }
            $karyawan = $qkaryawan->get();
            foreach ($karyawan as $d) {
                $nik[] = $d->nik;
                $data[] = [
                    'kode_libur' => $kode_libur,
                    'nik' => $d->nik
                ];
            }


            DB::table('harilibur_karyawan')->where('kode_libur', $kode_libur)->whereIn('nik', $nik)->delete();


            DB::commit();
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            // return 1;
            dd($e);
        }
    }
}
