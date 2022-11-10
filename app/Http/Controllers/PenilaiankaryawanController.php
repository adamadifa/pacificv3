<?php

namespace App\Http\Controllers;

use App\Models\Detailpenilaiankaryawan;
use App\Models\Penilaiankaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PenilaiankaryawanController extends Controller
{
    public function index($kategori_jabatan, $kantor)
    {
        $karyawan = DB::table('master_karyawan')->orderBy('nama_karyawan')->get();
        $kategori_penilaian = DB::table('hrd_kategoripenilaian')->get();
        $query = Penilaiankaryawan::query();
        $query->select('hrd_penilaian.kode_penilaian', 'tanggal', 'hrd_penilaian.nik', 'nama_karyawan', 'hrd_penilaian.periode_kontrak', 'hrd_penilaian.kode_dept', 'nama_dept', 'hrd_penilaian.id_jabatan', 'nama_jabatan', 'kp', 'ka', 'm', 'gm', 'hrd', 'dirut', 'status');
        $query->join('master_karyawan', 'hrd_penilaian.nik', '=', 'master_karyawan.nik');
        $query->join('departemen', 'hrd_penilaian.kode_dept', '=', 'departemen.kode_dept');
        $query->join('hrd_jabatan', 'hrd_penilaian.id_jabatan', '=', 'hrd_jabatan.id');
        $query->where('hrd_penilaian.id_kategori_jabatan', $kategori_jabatan);
        $query->where('hrd_penilaian.id_kantor', $kantor);
        $list_dept = Auth::user()->kode_dept != null ?  unserialize(Auth::user()->kode_dept) : NULL;
        if ($list_dept != NULL) {
            $query->whereIn('hrd_penilaian.kode_dept', $list_dept);
        }
        $penilaian = $query->get();
        $kategori_jabatan_user = DB::table('hrd_kategori_jabatan')->where('id', Auth::user()->kategori_jabatan)->first();
        $kat_jab_user =  $kategori_jabatan_user != null ? $kategori_jabatan_user->kategori_jabatan : '';
        $inisial = ["" => "", "manager" => "M", "general manager" => "GM", "manager hrd" => "HRD", "direktur" => "DIRUT"];

        $dept = $list_dept != null ? "'" . implode("', '", $list_dept) . "'" : '';


        $field_kategori = strtolower($inisial[strtolower($kat_jab_user)]);


        if (Auth::user()->level != "admin" and $kat_jab_user != "DIREKTUR" and $kat_jab_user != "MANAGER HRD") {
            $kategori_approval = DB::table('hrd_penilaian_approval')
                ->select('hrd_penilaian_approval.id', 'kategori_jabatan', 'kantor', 'jml')
                ->join('hrd_kategori_jabatan', 'hrd_penilaian_approval.id', '=', 'hrd_kategori_jabatan.id')
                ->leftJoin(
                    DB::raw("(
                    SELECT id_kategori_jabatan,COUNT(kode_penilaian) as jml FROM hrd_penilaian WHERE status IS NULL
                    AND kode_dept IN (" . $dept . ")
                    GROUP BY id_kategori_jabatan
                ) cekpengajuan"),
                    function ($join) {
                        $join->on('hrd_penilaian_approval.id', '=', 'cekpengajuan.id_kategori_jabatan');
                    }
                )
                ->where('kantor', 'PST')
                ->whereRaw($field_kategori . "=1")
                ->orderBy('hrd_penilaian_approval.id', 'desc')
                ->get();
        } else {
            $kategori_approval = DB::table('hrd_penilaian_approval')
                ->select('hrd_penilaian_approval.id', 'kategori_jabatan', 'kantor', 'jml')
                ->join('hrd_kategori_jabatan', 'hrd_penilaian_approval.id', '=', 'hrd_kategori_jabatan.id')
                ->leftJoin(
                    DB::raw("(
                SELECT id_kategori_jabatan,COUNT(kode_penilaian) as jml FROM hrd_penilaian WHERE status IS NULL
                GROUP BY id_kategori_jabatan
            ) cekpengajuan"),
                    function ($join) {
                        $join->on('hrd_penilaian_approval.id', '=', 'cekpengajuan.id_kategori_jabatan');
                    }
                )
                ->where('kantor', 'PST')
                ->orderBy('hrd_penilaian_approval.id', 'desc')
                ->get();
        }

        $approval = DB::table('hrd_penilaian_approval')->where('id', $kategori_jabatan)->where('kantor', $kantor)->first();
        $approve = unserialize($approval->approval);
        $cekindex = array_search(strtolower($kat_jab_user), $approve);

        return view('penilaiankaryawan.index', compact('karyawan', 'kategori_penilaian', 'penilaian', 'kategori_jabatan', 'kantor', 'approve', 'kategori_approval', 'field_kategori', 'kat_jab_user', 'cekindex'));
    }

    public function create(Request $request)
    {
        $tanggal = $request->tanggal;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $nik = $request->nik;
        $karyawan = DB::table('master_karyawan')
            ->selectRaw('nik,nama_karyawan,master_karyawan.kode_dept,nama_dept,master_karyawan.id_jabatan,nama_jabatan,hrd_jabatan.id_kategori_jabatan,id_kantor')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->leftjoin('departemen', 'master_karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->where('nik', $nik)
            ->first();
        $kantor = $karyawan->id_kantor != "PST" ? "PST" : "CBG";
        $id_kategori_jabatan = $karyawan->id_kategori_jabatan;
        $kategori_approval = DB::table('hrd_penilaian_approval')->where('kantor', $kantor)->where('id', $id_kategori_jabatan)->first();
        $kategori = $kategori_approval->doc;
        $kategori_penilaian = DB::table('hrd_penilaiankaryawan_item')->where('id_kategori', $kategori)
            ->select('hrd_penilaiankaryawan_item.id', 'penilaian', 'hrd_penilaiankaryawan_item.id_jenis_penilaian', 'jenis_penilaian', 'hrd_penilaiankaryawan_item.id_jenis_kompetensi')
            ->join('hrd_jenispenilaian', 'hrd_penilaiankaryawan_item.id_jenis_penilaian', '=', 'hrd_jenispenilaian.id')
            ->orderBy('hrd_penilaiankaryawan_item.id_jenis_penilaian')->get();
        if ($kategori == 1) {
            return view('penilaiankaryawan.create', compact('tanggal', 'dari', 'sampai', 'karyawan', 'kategori_penilaian', 'kategori'));
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
            'kategori' => $kategori,
            'sid' => $sid,
            'sakit' => $sakit,
            'izin' => $izin,
            'alfa' => $alfa,
            'masa_kontrak_kerja' => $masa_kontrak_kerja,
            'rekomendasi' => $rekomendasi,
            'evaluasi' => $evaluasi,
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
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
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
            ->selectRaw('hrd_penilaian.nik,nama_karyawan,hrd_penilaian.kode_dept,nama_dept,hrd_penilaian.id_jabatan,nama_jabatan,hrd_penilaian.id_kategori_jabatan,hrd_penilaian.id_kantor')
            ->join('master_karyawan', 'hrd_penilaian.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'hrd_penilaian.id_jabatan', '=', 'hrd_jabatan.id')
            ->leftjoin('departemen', 'hrd_penilaian.kode_dept', '=', 'departemen.kode_dept')
            ->where('hrd_penilaian.nik', $nik)
            ->first();
        $kantor = $karyawan->id_kantor != "PST" ? "PST" : "CBG";
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
        $skor = $request->skor;
        $id_penilaian = $request->id_penilaian;
        $sid = $request->sid;
        $sakit = $request->sakit;
        $izin = $request->izin;
        $alfa = $request->alfa;
        $masa_kontrak_kerja = $request->masa_kontrak_kerja;
        $rekomendasi = $request->rekomendasi;
        $evaluasi = $request->evaluasi;
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
            'evaluasi' => $evaluasi
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
            $chunks = array_chunk($detail_nilai, 5);
            foreach ($chunks as $chunk) {
                Detailpenilaiankaryawan::insert($chunk);
            }
            DB::commit();
            return redirect('/penilaiankaryawan')->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return redirect('/penilaiankaryawan')->with(['warning' => 'Data Gagal Diupdate']);
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
            ->selectRaw('hrd_penilaian.nik,nama_karyawan,hrd_penilaian.kode_dept,nama_dept,hrd_penilaian.id_jabatan,nama_jabatan,hrd_penilaian.id_kategori_jabatan,hrd_penilaian.id_kantor,status,ka,kp,rsm,m,gm,hrd,dirut')
            ->join('master_karyawan', 'hrd_penilaian.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'hrd_penilaian.id_jabatan', '=', 'hrd_jabatan.id')
            ->leftjoin('departemen', 'hrd_penilaian.kode_dept', '=', 'departemen.kode_dept')
            ->where('hrd_penilaian.nik', $nik)
            ->first();

        $kantor = $karyawan->id_kantor == "PST" ? "PST" : "CBG";
        $id_kategori_jabatan = $karyawan->id_kategori_jabatan;
        $kategori_approval = DB::table('hrd_penilaian_approval')->where('kantor', $kantor)->where('id', $id_kategori_jabatan)->first();
        $kategori = $kategori_approval->doc;
        $approve = unserialize($kategori_approval->approval);

        $kategori_penilaian = DB::table('hrd_penilaian_detail')
            ->select('hrd_penilaiankaryawan_item.id', 'penilaian', 'hrd_penilaiankaryawan_item.id_jenis_penilaian', 'jenis_penilaian', 'hrd_penilaiankaryawan_item.id_jenis_kompetensi', 'nilai')
            ->where('kode_penilaian', $kode_penilaian)
            ->join('hrd_penilaiankaryawan_item', 'hrd_penilaian_detail.id_penilaian', '=', 'hrd_penilaiankaryawan_item.id')
            ->join('hrd_jenispenilaian', 'hrd_penilaiankaryawan_item.id_jenis_penilaian', '=', 'hrd_jenispenilaian.id')
            ->orderBy('hrd_penilaiankaryawan_item.id_jenis_penilaian')->get();
        if ($kategori == 1) {
            return view('penilaiankaryawan.cetak', compact('tanggal', 'dari', 'sampai', 'karyawan', 'kategori_penilaian', 'kategori', 'penilaian', 'approve'));
        } else {
            return view('penilaiankaryawan.cetak_operator', compact('tanggal', 'dari', 'sampai', 'karyawan', 'kategori_penilaian', 'kategori', 'penilaian', 'approve'));
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
