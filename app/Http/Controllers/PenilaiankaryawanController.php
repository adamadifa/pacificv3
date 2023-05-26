<?php

namespace App\Http\Controllers;

use App\Models\Detailpenilaiankaryawan;
use App\Models\Masterkaryawan;
use App\Models\Penilaiankaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PenilaiankaryawanController extends Controller
{
    public function index($kategori_jabatan, $perusahaan, Request $request)
    {

        $list_dept = Auth::user()->kode_dept != null ?  unserialize(Auth::user()->kode_dept) : NULL;
        $list_wilayah = Auth::user()->wilayah != null ? unserialize(Auth::user()->wilayah) : NULL;

        $dept = $list_dept != null ? "'" . implode("', '", $list_dept) . "'" : '';
        $wilayah = $list_wilayah != null ? "'" . implode("', '", $list_wilayah) . "'" : '';
        $qkaryawan =  Masterkaryawan::query();
        $qkaryawan->select('nik', 'nama_karyawan', 'nama_jabatan', 'master_karyawan.id_kantor');
        $qkaryawan->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $qkaryawan->join('hrd_kategori_jabatan', 'hrd_jabatan.id_kategori_jabatan', '=', 'hrd_kategori_jabatan.id');
        if (Auth::user()->kode_cabang != "PCF") {
            $qkaryawan->where('id_kantor', Auth::user()->kode_cabang);
        } else {
            if (Auth::user()->level == "rsm") {
                $qkaryawan->whereIn('id_kantor', $list_wilayah);
                $qkaryawan->where('id_jabatan', 11);
            } else if (Auth::user()->kategori_jabatan == 3) {
                $qkaryawan->whereIn('id_kategori_jabatan', [8, 9, 10, 5, 15]);
                $qkaryawan->where('id_kantor', 'PST');
            } else if (Auth::user()->kategori_jabatan == 2) {
                $qkaryawan->whereIn('id_kategori_jabatan', [3, 4]);
            }
        }
        if ($list_dept != NULL) {
            $qkaryawan->whereIn('kode_dept', $list_dept);
        }
        $qkaryawan->where('status_karyawan', 'K');
        $qkaryawan->where('id_kategori_jabatan', '!=', Auth::user()->kategori_jabatan);
        $karyawan = $qkaryawan->get();




        $kategori_jabatan_user = DB::table('hrd_kategori_jabatan')->where('id', Auth::user()->kategori_jabatan)->first();
        $kat_jab_user =  $kategori_jabatan_user != null ? $kategori_jabatan_user->kategori_jabatan : '';
        $inisial = ["" => "", "kepala admin" => "KA", "kepala penjualan" => "KP", "rsm" => "RSM", "manager" => "M", "general manager" => "GM", "manager hrd" => "HRD", "direktur" => "DIRUT"];
        $field_kategori = strtolower($inisial[strtolower($kat_jab_user)]);

        if (Auth::user()->kode_cabang != "PCF") {
            $whereCabang = "AND hrd_penilaian.id_kantor ='" . Auth::user()->kode_cabang . "'";
        } else {
            if (Auth::user()->level == "rsm") {
                $whereCabang = "AND hrd_penilaian.id_kantor IN (" . $wilayah . ") ";
            } else {
                $whereCabang = "";
            }
        }
        // if (Auth::user()->level != "admin" and $kat_jab_user != "DIREKTUR" and $kat_jab_user != "MANAGER HRD") {
        //     $kategori_approval = DB::table('hrd_penilaian_approval')
        //         ->select('hrd_penilaian_approval.id', 'kategori_jabatan', 'kantor', 'jml')
        //         ->join('hrd_kategori_jabatan', 'hrd_penilaian_approval.id', '=', 'hrd_kategori_jabatan.id')
        //         ->leftJoin(
        //             DB::raw("(
        //             SELECT id_kategori_jabatan,COUNT(kode_penilaian) as jml FROM hrd_penilaian
        //             WHERE status IS NULL
        //             AND id_perusahaan = 'MP'
        //             AND kode_dept IN (" . $dept . ") " . $whereCabang . "
        //             GROUP BY id_kategori_jabatan
        //         ) cekpengajuan"),
        //             function ($join) {
        //                 $join->on('hrd_penilaian_approval.id', '=', 'cekpengajuan.id_kategori_jabatan');
        //             }
        //         )
        //         ->where('hrd_penilaian_approval.kantor', 'MP')
        //         ->whereRaw($field_kategori . "=1")
        //         ->orderBy('hrd_penilaian_approval.id', 'desc')
        //         ->get();

        //     $kategori_approval_pcf = DB::table('hrd_penilaian_approval')
        //         ->select('hrd_penilaian_approval.id', 'kategori_jabatan', 'kantor', 'jml')
        //         ->join('hrd_kategori_jabatan', 'hrd_penilaian_approval.id', '=', 'hrd_kategori_jabatan.id')
        //         ->leftJoin(
        //             DB::raw("(
        //             SELECT id_kategori_jabatan,COUNT(kode_penilaian) as jml FROM hrd_penilaian WHERE status IS NULL
        //             AND id_perusahaan = 'PCF'
        //             AND kode_dept IN (" . $dept . ") " . $whereCabang . "
        //             GROUP BY id_kategori_jabatan
        //         ) cekpengajuan"),
        //             function ($join) {
        //                 $join->on('hrd_penilaian_approval.id', '=', 'cekpengajuan.id_kategori_jabatan');
        //             }
        //         )
        //         ->where('hrd_penilaian_approval.kantor', 'PCF')
        //         ->whereRaw($field_kategori . "=1")
        //         ->orderBy('hrd_penilaian_approval.id', 'desc')
        //         ->get();
        // } else {
        //     $kategori_approval = DB::table('hrd_penilaian_approval')
        //         ->select('hrd_penilaian_approval.id', 'kategori_jabatan', 'kantor', 'jml')
        //         ->join('hrd_kategori_jabatan', 'hrd_penilaian_approval.id', '=', 'hrd_kategori_jabatan.id')
        //         ->leftJoin(
        //             DB::raw("(
        //         SELECT id_kategori_jabatan,COUNT(kode_penilaian) as jml FROM hrd_penilaian
        //         WHERE status IS NULL
        //         AND id_perusahaan = 'MP'
        //         GROUP BY id_kategori_jabatan
        //     ) cekpengajuan"),
        //             function ($join) {
        //                 $join->on('hrd_penilaian_approval.id', '=', 'cekpengajuan.id_kategori_jabatan');
        //             }
        //         )
        //         ->where('hrd_penilaian_approval.kantor', 'MP')
        //         ->orderBy('hrd_penilaian_approval.id', 'desc')
        //         ->get();


        //     $kategori_approval_pcf = DB::table('hrd_penilaian_approval')
        //         ->select('hrd_penilaian_approval.id', 'kategori_jabatan', 'kantor', 'jml')
        //         ->join('hrd_kategori_jabatan', 'hrd_penilaian_approval.id', '=', 'hrd_kategori_jabatan.id')
        //         ->leftJoin(
        //             DB::raw("(
        //             SELECT id_kategori_jabatan,COUNT(kode_penilaian) as jml FROM hrd_penilaian
        //             WHERE status IS NULL AND id_perusahaan = 'PCF'
        //             GROUP BY id_kategori_jabatan
        //         ) cekpengajuan"),
        //             function ($join) {
        //                 $join->on('hrd_penilaian_approval.id', '=', 'cekpengajuan.id_kategori_jabatan');
        //             }
        //         )
        //         ->where('hrd_penilaian_approval.kantor', 'PCF')
        //         ->orderBy('hrd_penilaian_approval.id', 'desc')
        //         ->get();
        // }

        $approve_jabatan = Auth::user()->approve_jabatan != null ? unserialize(Auth::user()->approve_jabatan) : [];
        if (Auth::user()->level != "admin" and $kat_jab_user != "DIREKTUR" and $kat_jab_user != "MANAGER HRD") {
            $kategori_approval = DB::table('hrd_kategori_jabatan')
                ->select('hrd_kategori_jabatan.id', 'kategori_jabatan', 'jml', 'hrd_kategori_jabatan.id_perusahaan')
                ->leftJoin(
                    DB::raw("(
                        SELECT id_kategori_jabatan,id_perusahaan,COUNT(kode_penilaian) as jml FROM hrd_penilaian
                        WHERE status IS NULL
                        AND $field_kategori IS NULL
                        AND kode_dept IN (" . $dept . ") " . $whereCabang . "
                        GROUP BY id_kategori_jabatan,id_perusahaan
                    ) cekpengajuan"),
                    function ($join) {
                        $join->on('hrd_kategori_jabatan.id', '=', 'cekpengajuan.id_kategori_jabatan');
                        $join->on('hrd_kategori_jabatan.id_perusahaan', '=', 'cekpengajuan.id_perusahaan');
                    }
                )
                ->orderBy('urutan')
                ->whereIn('id', $approve_jabatan)->get();
        } else {
            $kategori_approval = DB::table('hrd_kategori_jabatan')
                ->select('hrd_kategori_jabatan.id', 'kategori_jabatan', 'jml', 'hrd_kategori_jabatan.id_perusahaan')
                ->leftJoin(
                    DB::raw("(
                        SELECT id_kategori_jabatan,id_perusahaan,COUNT(kode_penilaian) as jml FROM hrd_penilaian
                        WHERE status IS NULL
                        AND $field_kategori IS NULL
                        GROUP BY id_kategori_jabatan,id_perusahaan
                    ) cekpengajuan"),
                    function ($join) {
                        $join->on('hrd_kategori_jabatan.id', '=', 'cekpengajuan.id_kategori_jabatan');
                        $join->on('hrd_kategori_jabatan.id_perusahaan', '=', 'cekpengajuan.id_perusahaan');
                    }
                )
                ->orderBy('urutan')
                ->whereIn('id', $approve_jabatan)->get();
        }

        $approval = DB::table('hrd_penilaian_approval')->where('id', $kategori_jabatan)->where('kantor', $perusahaan)->first();
        $approve = unserialize($approval->approval);
        $cekindex = array_search(strtolower($kat_jab_user), $approve);
        $lastindex = $cekindex != 0 ? $approve[$cekindex - 1] : $approve[$cekindex];
        $lastfield = strtolower($inisial[$lastindex]);

        $query = Penilaiankaryawan::query();
        $query->select('hrd_penilaian.kode_penilaian', 'tanggal', 'hrd_penilaian.nik', 'nama_karyawan', 'hrd_penilaian.id_kantor', 'hrd_penilaian.periode_kontrak', 'hrd_penilaian.kode_dept', 'nama_dept', 'hrd_penilaian.id_jabatan', 'nama_jabatan', 'kp', 'ka', 'rsm', 'm', 'gm', 'hrd', 'dirut', 'status', 'pemutihan', 'no_kb', 'hrd_kontrak.no_kontrak');
        $query->join('master_karyawan', 'hrd_penilaian.nik', '=', 'master_karyawan.nik');
        $query->join('hrd_departemen', 'hrd_penilaian.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->join('hrd_jabatan', 'hrd_penilaian.id_jabatan', '=', 'hrd_jabatan.id');
        $query->leftjoin('hrd_kesepakatanbersama', 'hrd_penilaian.kode_penilaian', '=', 'hrd_kesepakatanbersama.kode_penilaian');
        $query->leftjoin('hrd_kontrak', 'hrd_penilaian.kode_penilaian', '=', 'hrd_kontrak.kode_penilaian');
        $query->where('hrd_penilaian.id_kategori_jabatan', $kategori_jabatan);
        $query->where('hrd_penilaian.id_perusahaan', $perusahaan);
        if ($request->filter == 1) {
            $query->whereNotNull($lastfield);
            $query->whereNull($field_kategori);
        } else  if ($request->filter == 2) {
            $query->whereNotNull($field_kategori);
        } else  if ($request->filter == 3) {
            $query->whereNull($lastfield);
        }
        if ($list_dept != NULL) {
            $query->whereIn('hrd_penilaian.kode_dept', $list_dept);
        }

        if (Auth::user()->kode_cabang != 'PCF') {
            $query->where('hrd_penilaian.id_kantor', Auth::user()->kode_cabang);
        } else {
            if (Auth::user()->level == "rsm") {
                $query->whereIn('hrd_penilaian.id_kantor', $list_wilayah);
            }
        }

        if (!empty($request->nama_karyawan)) {
            $query->where('nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }
        $penilaian = $query->paginate(15);
        $penilaian->appends($request->all());


        return view('penilaiankaryawan.index', compact('karyawan', 'penilaian', 'kategori_jabatan', 'perusahaan', 'approve', 'kategori_approval', 'field_kategori', 'kat_jab_user', 'cekindex', 'inisial'));
    }

    public function create(Request $request)
    {
        $tanggal = $request->tanggal;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $no_kontrak = $request->no_kontrak;
        $nik = $request->nik;
        $karyawan = DB::table('master_karyawan')
            ->selectRaw('nik,nama_karyawan,master_karyawan.kode_dept,nama_dept,master_karyawan.id_jabatan,nama_jabatan,hrd_jabatan.id_kategori_jabatan,id_kantor,id_perusahaan')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->leftjoin('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->where('nik', $nik)
            ->first();
        $kantor = $karyawan->id_perusahaan;
        $id_kategori_jabatan = $karyawan->id_kategori_jabatan;
        $kategori_approval = DB::table('hrd_penilaian_approval')->where('kantor', $kantor)->where('id', $id_kategori_jabatan)->first();
        $kategori = $kategori_approval->doc;
        $kategori_penilaian = DB::table('hrd_penilaiankaryawan_item')->where('id_kategori', $kategori)
            ->select('hrd_penilaiankaryawan_item.id', 'penilaian', 'hrd_penilaiankaryawan_item.id_jenis_penilaian', 'jenis_penilaian', 'hrd_penilaiankaryawan_item.id_jenis_kompetensi')
            ->join('hrd_jenispenilaian', 'hrd_penilaiankaryawan_item.id_jenis_penilaian', '=', 'hrd_jenispenilaian.id')
            ->orderBy('hrd_penilaiankaryawan_item.id_jenis_penilaian')->get();
        if ($kategori == 1) {
            return view('penilaiankaryawan.create', compact('tanggal', 'dari', 'sampai', 'karyawan', 'kategori_penilaian', 'kategori', 'no_kontrak'));
        } else {
            return view('penilaiankaryawan.create_operator', compact('tanggal', 'dari', 'sampai', 'karyawan', 'kategori_penilaian', 'kategori'));
        }
    }

    public function store(Request $request)
    {
        $tanggal = $request->tanggal;
        $periode_kontrak = $request->periode_kontrak;
        //dd($periode_kontrak);
        $nik = $request->nik;
        $kode_dept = $request->kode_dept;
        //dd($kode_dept);
        $id_jabatan = $request->id_jabatan;
        $kategori = $request->kategori;
        $id_kategori_jabatan = $request->id_kategori_jabatan;
        $id_kantor = $request->id_kantor;
        $id_perusahaan = $request->id_perusahaan;
        $skor = $request->skor;
        $id_penilaian = $request->id_penilaian;
        $sid = $request->sid;
        $sakit = $request->sakit;
        $izin = $request->izin;
        $alfa = $request->alfa;
        $masa_kontrak_kerja = $request->masa_kontrak_kerja;
        $rekomendasi = $request->rekomendasi;
        $evaluasi = $request->evaluasi;
        $tgl = explode("-", $tanggal);
        $bulan = $tgl[1];
        $tahun = substr($tgl[0], 2);
        $no_kontrak = $request->no_kontrak;
        $penilaian = DB::table("hrd_penilaian")
            ->whereRaw('MONTH(tanggal)=' . $bulan)
            ->whereRaw('YEAR(tanggal)=' . $tgl[0])
            ->orderBy("kode_penilaian", "desc")
            ->first();

        $lastkode = $penilaian != null ? $penilaian->kode_penilaian : '';

        $kode_penilaian  = buatkode($lastkode, "PK" . $bulan . $tahun, 2);

        $data = [
            'kode_penilaian' => $kode_penilaian,
            'tanggal' => $tanggal,
            'nik' => $nik,
            'periode_kontrak' => $periode_kontrak,
            'kode_dept' => $kode_dept,
            'id_jabatan' => $id_jabatan,
            'id_kategori_jabatan' => $id_kategori_jabatan,
            'id_kantor' => $id_kantor,
            'id_perusahaan' => $id_perusahaan,
            'kategori' => $kategori,
            'sid' => $sid,
            'sakit' => $sakit,
            'izin' => $izin,
            'alfa' => $alfa,
            'masa_kontrak_kerja' => $masa_kontrak_kerja,
            'rekomendasi' => $rekomendasi,
            'evaluasi' => $evaluasi,
            'no_kontrak' => $no_kontrak
        ];



        for ($i = 0; $i < count($id_penilaian); $i++) {
            $id = $id_penilaian[$i];
            $nilai = $skor[$i];

            $detail_nilai[] = [
                'kode_penilaian' => $kode_penilaian,
                'id_penilaian' => $id,
                'nilai' => $nilai
            ];
        }


        DB::beginTransaction();
        try {
            DB::table('hrd_penilaian')->insert($data);
            $chunks = array_chunk($detail_nilai, 5);
            foreach ($chunks as $chunk) {
                Detailpenilaiankaryawan::insert($chunk);
            }
            DB::commit();
            return redirect('/penilaiankaryawan/' . $id_kategori_jabatan . "/" . $id_perusahaan . "/list")->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return redirect('/penilaiankaryawan/' . $id_kategori_jabatan . "/" . $id_perusahaan . "/list")->with(['warning' => 'Data Gagal Disimpan']);
        }
    }


    public function edit($kode_penilaian)
    {

        $kode_penilaian = Crypt::decrypt($kode_penilaian);
        $penilaian = DB::table('hrd_penilaian')->where('kode_penilaian', $kode_penilaian)->first();
        $tanggal = $penilaian->tanggal;
        $periode_kontrak = explode("/", $penilaian->periode_kontrak);
        $dari = $periode_kontrak[0];
        $sampai = $periode_kontrak[1];
        $nik = $penilaian->nik;
        $karyawan = DB::table('hrd_penilaian')
            ->selectRaw('hrd_penilaian.nik,nama_karyawan,hrd_penilaian.kode_dept,nama_dept,hrd_penilaian.id_jabatan,nama_jabatan,hrd_penilaian.id_kategori_jabatan,hrd_penilaian.id_kantor,hrd_penilaian.id_perusahaan,pemutihan')
            ->join('master_karyawan', 'hrd_penilaian.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'hrd_penilaian.id_jabatan', '=', 'hrd_jabatan.id')
            ->leftjoin('hrd_departemen', 'hrd_penilaian.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->where('hrd_penilaian.nik', $nik)
            ->first();
        $kantor = $karyawan->id_perusahaan;
        $id_kategori_jabatan = $karyawan->id_kategori_jabatan;
        $kategori_approval = DB::table('hrd_penilaian_approval')->where('kantor', $kantor)->where('id', $id_kategori_jabatan)->first();
        $kategori = $kategori_approval->doc;
        $kategori_penilaian = DB::table('hrd_penilaian_detail')
            ->select('hrd_penilaiankaryawan_item.id', 'penilaian', 'hrd_penilaiankaryawan_item.id_jenis_penilaian', 'jenis_penilaian', 'hrd_penilaiankaryawan_item.id_jenis_kompetensi', 'nilai')
            ->where('kode_penilaian', $kode_penilaian)
            ->join('hrd_penilaiankaryawan_item', 'hrd_penilaian_detail.id_penilaian', '=', 'hrd_penilaiankaryawan_item.id')
            ->join('hrd_jenispenilaian', 'hrd_penilaiankaryawan_item.id_jenis_penilaian', '=', 'hrd_jenispenilaian.id')
            ->orderBy('hrd_penilaiankaryawan_item.id_jenis_penilaian')->get();
        if ($kategori == 1) {
            return view('penilaiankaryawan.edit', compact('tanggal', 'dari', 'sampai', 'karyawan', 'kategori_penilaian', 'kategori', 'penilaian'));
        } else {
            return view('penilaiankaryawan.edit_operator', compact('tanggal', 'dari', 'sampai', 'karyawan', 'kategori_penilaian', 'kategori', 'penilaian'));
        }
    }

    public function delete($kode_penilaian)
    {
        $kode_penilaian = Crypt::decrypt($kode_penilaian);
        $hapus = DB::table('hrd_penilaian')->where('kode_penilaian', $kode_penilaian)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function update($kode_penilaian, Request $request)
    {
        $kode_penilaian = Crypt::decrypt($kode_penilaian);
        $tanggal = $request->tanggal;
        $periode_kontrak = $request->periode_kontrak;
        //dd($periode_kontrak);
        $nik = $request->nik;
        $kode_dept = $request->kode_dept;
        //dd($kode_dept);
        $id_jabatan = $request->id_jabatan;
        $kategori = $request->kategori;
        $id_kategori_jabatan = $request->id_kategori_jabatan;
        $id_kantor = $request->id_kantor;
        $id_perusahaan = $request->id_perusahaan;
        $skor = $request->skor;
        $id_penilaian = $request->id_penilaian;
        $sid = $request->sid;
        $sakit = $request->sakit;
        $izin = $request->izin;
        $alfa = $request->alfa;
        $masa_kontrak_kerja = $request->masa_kontrak_kerja;
        $rekomendasi = $request->rekomendasi;
        $evaluasi = $request->evaluasi;
        $pemutihan = $request->pemutihan != null ? $request->pemutihan : 0;

        $data = [
            'tanggal' => $tanggal,
            'nik' => $nik,
            'periode_kontrak' => $periode_kontrak,
            'kode_dept' => $kode_dept,
            'id_jabatan' => $id_jabatan,
            'id_kategori_jabatan' => $id_kategori_jabatan,
            'id_kantor' => $id_kantor,
            'kategori' => $kategori,
            'sid' => $sid,
            'sakit' => $sakit,
            'izin' => $izin,
            'alfa' => $alfa,
            'masa_kontrak_kerja' => $masa_kontrak_kerja,
            'rekomendasi' => $rekomendasi,
            'evaluasi' => $evaluasi,
            'pemutihan' => $pemutihan
        ];



        for ($i = 0; $i < count($id_penilaian); $i++) {
            $id = $id_penilaian[$i];
            $nilai = $skor[$i];

            $detail_nilai[] = [
                'kode_penilaian' => $kode_penilaian,
                'id_penilaian' => $id,
                'nilai' => $nilai
            ];
        }


        DB::beginTransaction();
        try {
            DB::table('hrd_penilaian_detail')->where('kode_penilaian', $kode_penilaian)->delete();
            DB::table('hrd_penilaian')->where('kode_penilaian', $kode_penilaian)->update($data);
            $cek = DB::table('hrd_historieditpemutihan')->where('id_user', Auth::user()->id)->where('kode_penilaian', $kode_penilaian)->count();
            if ($cek > 0) {
                DB::table('hrd_historieditpemutihan')->where('id_user', Auth::user()->id)->where('kode_penilaian', $kode_penilaian)
                    ->update([
                        'pemutihan' => $pemutihan
                    ]);
            } else {
                DB::table('hrd_historieditpemutihan')->insert([
                    'kode_penilaian' => $kode_penilaian,
                    'id_user' => Auth::user()->id,
                    'level' => Auth::user()->level,
                    'pemutihan' => $pemutihan
                ]);
            }
            $chunks = array_chunk($detail_nilai, 5);
            foreach ($chunks as $chunk) {
                Detailpenilaiankaryawan::insert($chunk);
            }
            DB::commit();
            return redirect('/penilaiankaryawan/' . $id_kategori_jabatan . "/" . $id_perusahaan . "/list")->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return redirect('/penilaiankaryawan/' . $id_kategori_jabatan . "/" . $id_perusahaan . "/list")->with(['warning' => 'Data Gagal Diupdate']);
        }
    }


    public function cetak($kode_penilaian)
    {

        $kode_penilaian = Crypt::decrypt($kode_penilaian);
        $penilaian = DB::table('hrd_penilaian')->where('kode_penilaian', $kode_penilaian)->first();
        $tanggal = $penilaian->tanggal;
        $periode_kontrak = explode("/", $penilaian->periode_kontrak);
        $dari = $periode_kontrak[0];
        $sampai = $periode_kontrak[1];
        $nik = $penilaian->nik;
        $karyawan = DB::table('hrd_penilaian')
            ->selectRaw('hrd_penilaian.nik,nama_karyawan,hrd_penilaian.kode_dept,nama_dept,hrd_penilaian.id_jabatan,nama_jabatan,hrd_penilaian.id_kategori_jabatan,hrd_penilaian.id_kantor,hrd_penilaian.id_perusahaan,status,ka,kp,rsm,m,gm,hrd,dirut')
            ->join('master_karyawan', 'hrd_penilaian.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'hrd_penilaian.id_jabatan', '=', 'hrd_jabatan.id')
            ->leftjoin('hrd_departemen', 'hrd_penilaian.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->where('hrd_penilaian.kode_penilaian', $kode_penilaian)
            ->first();

        $kantor = $karyawan->id_perusahaan;
        $id_kategori_jabatan = $karyawan->id_kategori_jabatan;
        $kategori_approval = DB::table('hrd_penilaian_approval')->where('kantor', $kantor)->where('id', $id_kategori_jabatan)->first();
        $kategori = $kategori_approval->doc;
        $approve = unserialize($kategori_approval->approval);
        $inisial = ["" => "", "kepala admin" => "KA", "kepala penjualan" => "KP", "rsm" => "RSM", "manager" => "M", "general manager" => "GM", "manager hrd" => "HRD", "direktur" => "DIRUT"];
        $kategori_penilaian = DB::table('hrd_penilaian_detail')
            ->select('hrd_penilaiankaryawan_item.id', 'penilaian', 'hrd_penilaiankaryawan_item.id_jenis_penilaian', 'jenis_penilaian', 'hrd_penilaiankaryawan_item.id_jenis_kompetensi', 'nilai')
            ->where('kode_penilaian', $kode_penilaian)
            ->join('hrd_penilaiankaryawan_item', 'hrd_penilaian_detail.id_penilaian', '=', 'hrd_penilaiankaryawan_item.id')
            ->join('hrd_jenispenilaian', 'hrd_penilaiankaryawan_item.id_jenis_penilaian', '=', 'hrd_jenispenilaian.id')
            ->orderBy('hrd_penilaiankaryawan_item.id_jenis_penilaian')->get();

        $histori_kontrak = DB::table('hrd_kontrak')->where('nik', $penilaian->nik)
            ->orderBy('dari')
            ->get();
        if ($kategori == 1) {
            return view('penilaiankaryawan.cetak', compact('tanggal', 'dari', 'sampai', 'karyawan', 'kategori_penilaian', 'kategori', 'penilaian', 'approve', 'inisial', 'histori_kontrak'));
        } else {
            return view('penilaiankaryawan.cetak_operator', compact('tanggal', 'dari', 'sampai', 'karyawan', 'kategori_penilaian', 'kategori', 'penilaian', 'approve', 'inisial', 'histori_kontrak'));
        }
    }

    public function batalkan($kode_penilaian, $kategori_jabatan)
    {
        $kode_penilaian = Crypt::decrypt($kode_penilaian);
        $kategori_jabatan = Crypt::decrypt($kategori_jabatan);
        $update = DB::table('hrd_penilaian')->where('kode_penilaian', $kode_penilaian)
            ->update([
                $kategori_jabatan => NULL,
                'status' => NULL
            ]);

        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }

    public function approve($kode_penilaian, $kategori_jabatan)
    {
        $kode_penilaian = Crypt::decrypt($kode_penilaian);
        $kategori_jabatan = Crypt::decrypt($kategori_jabatan);
        $executor = Auth::user()->kategori_jabatan;
        if ($executor == 1) {
            $update = DB::table('hrd_penilaian')->where('kode_penilaian', $kode_penilaian)
                ->update([
                    $kategori_jabatan => Auth::user()->id,
                    'status' => 1
                ]);
        } else {
            $update = DB::table('hrd_penilaian')->where('kode_penilaian', $kode_penilaian)
                ->update([
                    $kategori_jabatan => Auth::user()->id,
                    'status' => NULL
                ]);
        }


        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }

    public function decline($kode_penilaian, $kategori_jabatan)
    {
        $kode_penilaian = Crypt::decrypt($kode_penilaian);
        $kategori_jabatan = Crypt::decrypt($kategori_jabatan);
        $update = DB::table('hrd_penilaian')->where('kode_penilaian', $kode_penilaian)
            ->update([
                $kategori_jabatan => Auth::user()->id,
                'status' => 2
            ]);

        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }
}
