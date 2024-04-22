<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Karyawan;
use App\Models\Lembur;
use App\Models\Lemburkaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class LemburController extends Controller
{
    public function index(Request $request)
    {
        $level = Auth::user()->level;
        $query = Lembur::query();
        // if (!empty($request->bulan)) {
        //     $query->whereRaw('MONTH(tanggal_dari)="' . $request->bulan . '"');
        // }

        // if (!empty($request->tahun)) {
        //     $query->whereRaw('YEAR(tanggal_sampai)="' . $request->tahun . '"');
        // }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->wherebetween('tanggal', [$request->dari, $request->sampai]);
        }
        if (!empty($request->kategori_search)) {
            $query->where('kategori', $request->kategori_search);
        }

        if (!empty($request->kode_cabang_search)) {
            $query->where('id_kantor', $request->kode_cabang_search);
        }

        if (!empty($request->kode_dept_search)) {
            $query->where('kode_dept', $request->kode_dept_search);
        }

        if (Auth::user()->kode_cabang != "PCF" && Auth::user()->kode_cabang != "PST") {
            $query->where('id_kantor', Auth::user()->kode_cabang);
        } else {
            $level_search = array("manager hrd", "admin", "spv presensi", "direktur");
            if (in_array(Auth::user()->level, $level_search)) {
                if (!empty($request->id_kantor_search)) {
                    $query->where('id_kantor', $request->id_kantor_search);
                }
            } else if ($level == "manager ga") {
                $query->where('kode_dept', 'GAF');
            } else if ($level == "spv maintenance") {
                $query->where('kode_dept', 'MTC');
            } else if ($level == "manager produksi") {
                $query->where('kode_dept', 'PRD');
            } else if ($level == "emf") {
                $query->where('head',1);
                $query->whereIn('kode_dept', ['GAF', 'PRD', 'MTC', 'PDQ']);
                $query->orderBy('gm');
            } else {
                $query->where('id_kantor', 'PST');
                $query->where('kode_dept', Auth::user()->kode_dept_presensi);
            }
        }

        $query->orderBy('tanggal_dari', 'desc');
        $lembur = $query->paginate(15);
        $lembur->appends($request->all());

        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();

        $cbg = new Cabang();
        $cb = $cbg->getCabang(Auth::user()->kode_cabang);


        $departemen = DB::table('hrd_departemen')->orderBy('kode_dept')->get();
        return view('lembur.index', compact('cabang', 'departemen', 'lembur', 'bulan', 'cb'));
    }

    public function store(Request $request)
    {
        $tanggal = $request->tanggal;
        $tanggal_dari = $request->tanggal_dari;
        $tanggal_sampai = $request->tanggal_sampai;
        $jam_dari = $request->jam_dari;
        $jam_sampai = $request->jam_sampai;
        $kategori = $request->kategori;
        $dari = $tanggal_dari . " " . $jam_dari;
        $sampai = $tanggal_sampai . " " . $jam_sampai;
        $id_kantor = $request->id_kantor;
        $kode_dept = $request->kode_dept;
        $keterangan = $request->keterangan;
        $istirahat = $request->istirahat;
        $tahun = substr(date('Y', strtotime($tanggal_dari)), 2, 2);
        $lembur = DB::table('lembur')->whereRaw('MID(kode_lembur,3,2)="' . $tahun . '"')
            ->orderBy('kode_lembur', 'desc')->first();
        $last_kodelembur = $lembur != null ? $lembur->kode_lembur : '';
        $kode_lembur = buatkode($last_kodelembur, "LM" . $tahun, 3);


        $data = [
            'kode_lembur' => $kode_lembur,
            'tanggal' => $tanggal,
            'tanggal_dari' => $dari,
            'tanggal_sampai' => $sampai,
            'id_kantor' => $id_kantor,
            'kode_dept' => $kode_dept,
            'keterangan' => $keterangan,
            'kategori' => $kategori,
            'istirahat' => $istirahat
        ];
        try {

            // if (Auth::user()->kode_cabang != "PCF" && Auth::user()->kode_cabang != "PST") {
            //     $cek = DB::table('lembur')->whereRaw('dari="' . $dari . '"')
            //         ->where('id_kantor', $id_kantor)
            //         ->count();
            // } else {
            //     $cek = DB::table('lembur')->whereRaw('dari="' . $dari . '"')
            //         ->where('kode_dept', $kode_dept)
            //         ->where('id_kantor', $id_kantor)
            //         ->count();
            // }

            // if ($cek > 0) {
            //     return Redirect::back()->with(['warning' => 'Tanggal Lembur Sudah Diinputkan Sebelumnya']);
            // }
            DB::table('lembur')->insert($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }


    public function delete($kode_lembur)
    {
        $kode_lembur = Crypt::decrypt($kode_lembur);
        try {
            DB::table('lembur')->where('kode_lembur', $kode_lembur)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }


    public function tambahkaryawan($kode_lembur)
    {
        $kode_lembur = Crypt::decrypt($kode_lembur);
        $lembur = DB::table('lembur')->where('kode_lembur', $kode_lembur)->first();
        return view('lembur.tambahkaryawan', compact('lembur'));
    }


    public function getkaryawan($kode_lembur, $id_kantor, $kode_dept)
    {
        $level_access = array("manager hrd", "admin", "spv presensi");
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

        return view('lembur.getkaryawan', compact('kode_lembur', 'id_kantor', 'kode_dept', 'departemen', 'group'));
    }


    public function getlistkaryawan(Request $request)
    {
        $kode_lembur = $request->kode_lembur;
        $id_kantor = $request->id_kantor;
        $kode_dept = $request->kode_dept;
        $id_perusahaan = $request->id_perusahaan;
        $grup = $request->grup;
        $nama_karyawan = $request->nama_karyawan;
        $query = Karyawan::query();
        $query->select('master_karyawan.nik', 'nama_karyawan', 'kode_dept', 'nama_jabatan', 'nama_group', 'kode_lembur');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->join('hrd_group', 'master_karyawan.grup', '=', 'hrd_group.id');
        $query->leftJoin(
            DB::raw("(
            SELECT nik,kode_lembur
            FROM lembur_karyawan
            WHERE kode_lembur = '$kode_lembur'
        ) lemburkaryawan"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'lemburkaryawan.nik');
            }
        );

        $query->where('id_kantor', $id_kantor);
        $query->where('status_aktif', 1);
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
        return view('lembur.getlistkaryawan', compact('kode_lembur', 'karyawan', 'id_kantor'));
    }

    public function storekaryawanlembur(Request $request)
    {
        $kode_lembur = $request->kode_lembur;
        $nik = $request->nik;
        try {
            DB::table('lembur_karyawan')->insert([
                'kode_lembur' => $kode_lembur,
                'nik' => $nik
            ]);
            return 0;
        } catch (\Exception $e) {
            return 1;
        }
    }


    public function hapuskaryawanlembur(Request $request)
    {
        $kode_lembur = $request->kode_lembur;
        $nik = $request->nik;
        try {
            DB::table('lembur_karyawan')->where('nik', $nik)
                ->where('kode_lembur', $kode_lembur)
                ->delete();
            return 0;
        } catch (\Exception $e) {
            return 1;
        }
    }

    public function storeallkaryawan(Request $request)
    {
        $kode_dept = $request->kode_dept;
        $kode_lembur = $request->kode_lembur;
        $id_kantor = $request->id_kantor;
        $grup = $request->grup;
        DB::beginTransaction();
        try {
            $nik = [];
            $qkaryawan = Karyawan::query();
            $qkaryawan->select('nik');
            $qkaryawan->where('id_kantor', $id_kantor);
            if (!empty($kode_dept)) {
                $qkaryawan->where('kode_dept', $kode_dept);
            }

            if (!empty($grup)) {
                $qkaryawan->where('grup', $grup);
            }
            $karyawan = $qkaryawan->get();
            foreach ($karyawan as $d) {
                $nik[] = $d->nik;
                $data[] = [
                    'kode_lembur' => $kode_lembur,
                    'nik' => $d->nik
                ];
            }


            DB::table('lembur_karyawan')->where('kode_lembur', $kode_lembur)->whereIn('nik', $nik)->delete();
            //dd($data);
            $chunks = array_chunk($data, 5);
            foreach ($chunks as $chunk) {
                Lemburkaryawan::insert($chunk);
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
        $kode_lembur = $request->kode_lembur;
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
                    'kode_lembur' => $kode_lembur,
                    'nik' => $d->nik
                ];
            }


            DB::table('lembur_karyawan')->where('kode_lembur', $kode_lembur)->whereIn('nik', $nik)->delete();


            DB::commit();
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            // return 1;
            dd($e);
        }
    }


    public function getlemburkaryawan($kode_lembur)
    {
        $lemburkaryawan = DB::table('lembur_karyawan')
            ->select(
                'lembur_karyawan.kode_lembur',
                'lembur_karyawan.nik',
                'nama_karyawan',
                'master_karyawan.kode_dept',
                'nama_jabatan',
                'nama_group',
                'grup',
                'hrd'
            )
            ->join('lembur', 'lembur_karyawan.kode_lembur', '=', 'lembur.kode_lembur')
            ->join('master_karyawan', 'lembur_karyawan.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->join('hrd_group', 'master_karyawan.grup', '=', 'hrd_group.id')
            ->where('lembur_karyawan.kode_lembur', $kode_lembur)
            ->orderByRaw('grup,nama_karyawan')
            ->get();

        return view('lembur.getlemburkaryawan', compact('lemburkaryawan'));
    }

    public function hapuslemburkaryawan(Request $request)
    {
        try {
            DB::table('lembur_karyawan')->where('kode_lembur', $request->kode_lembur)->where('nik', $request->nik)->delete();
            return 0;
        } catch (\Exception $e) {
            return 1;
        }
    }


    public function approve(Request $request)
    {
        $level_head = [
            'spv maintenance',
            'manager produksi',
            'manager ga',
        ];

        $level_hrd = ["manager hrd", "spv presensi"];
        $level = Auth::user()->level;
        $kode_lembur = Crypt::decrypt($request->kode_lembur);
        if (isset($request->approve)) {
            try {

                if (in_array($level, $level_head)) {
                    DB::table('lembur')->where('kode_lembur', $kode_lembur)->update(['head' => 1]);
                } else if ($level == "emf") {
                    DB::table('lembur')->where('kode_lembur', $kode_lembur)->update(['gm' => 1]);
                } else if (in_array($level, $level_hrd)) {
                    DB::table('lembur')->where('kode_lembur', $kode_lembur)->update(['hrd' => 1]);
                } else if ($level == "direktur") {
                    DB::table('lembur')->where('kode_lembur', $kode_lembur)->update(['dirut' => 1]);
                }
                return Redirect::back()->with(['success' => 'Pengajuan Lembur Disetujui']);
            } catch (\Exception $e) {
                return Redirect::back()->with(['warning' => 'Data Gagal di Update']);
                //throw $th;
            }
        } else {
            try {
                DB::table('lembur')->where('kode_lembur', $kode_lembur)->update(['hrd' => 2]);
                return Redirect::back()->with(['success' => 'Pengajuan Lembur Ditolak']);
            } catch (\Exception $e) {
                return Redirect::back()->with(['warning' => 'Data Gagal di Update']);
                //throw $th;
            }
        }
    }

    public function batalkan($kode_lembur)
    {
        $level_head = [
            'spv maintenance',
            'manager produksi',
            'manager ga',
        ];

        $level_hrd = ["manager hrd", "spv presensi"];
        $level = Auth::user()->level;
        $kode_lembur = Crypt::decrypt($kode_lembur);
        try {
            if (in_array($level, $level_head)) {
                DB::table('lembur')->where('kode_lembur', $kode_lembur)->update(['head' => null]);
            } else if ($level == "emf") {
                DB::table('lembur')->where('kode_lembur', $kode_lembur)->update(['gm' => null]);
            } else if (in_array($level, $level_hrd)) {
                DB::table('lembur')->where('kode_lembur', $kode_lembur)->update(['hrd' => null]);
            } else if ($level == "direktur") {
                DB::table('lembur')->where('kode_lembur', $kode_lembur)->update(['dirut' => null]);
            }

            return Redirect::back()->with(['success' => 'Pengajuan Lembur Dibatalkan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal di Update']);
            //throw $th;
        }
    }

    public function create_kethrd($kode_lembur)
    {
        $lembur = DB::table('lembur')->where('kode_lembur', $kode_lembur)->first();
        return view('lembur.create_kethrd', compact('lembur'));
    }


    public function update_kethrd($kode_lembur, Request $request)
    {
        try {
            DB::table('lembur')->where('kode_lembur', $kode_lembur)->update([
                'keterangan_hrd' => $request->keterangan_hrd
            ]);

            return Redirect::back()->with(['success' => 'Komentar Berhasil Ditambahkan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Komentar Gagal Ditambahkan']);
        }
    }
}
