<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class PinjamanController extends Controller
{
    public function create($nik)
    {
        $nik = Crypt::decrypt($nik);
        $query = Karyawan::query();
        $query->select('nik', 'nama_karyawan', 'tgl_masuk', 'nama_dept', 'jenis_kelamin', 'nama_jabatan', 'id_perusahaan', 'id_kantor', 'klasifikasi', 'status_karyawan', 'nama_cabang');
        $query->join('departemen', 'master_karyawan.kode_dept', '=', 'departemen.kode_dept');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->leftjoin('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang');
        $query->where('nik', $nik);
        $karyawan = $query->first();

        $gaji = DB::table('hrd_mastergaji')
            ->selectRaw('gaji_pokok+t_jabatan+t_masakerja+t_tanggungjawab+t_makan+t_istri+t_skill as gajitunjangan,gaji_pokok')
            ->where('nik', $nik)->orderBy('tgl_berlaku', 'desc')->first();

        $jmk = DB::table('hrd_bayarjmk')
            ->selectRaw('SUM(jumlah) as jml_jmk')
            ->where('nik', $nik)
            ->groupBy('nik')
            ->first();
        $hariini = date("Y-m-d");
        $sp = DB::table('hrd_sp')->where('nik', $nik)->where('sampai', '>', $hariini)->first();

        $kontrak = DB::table('hrd_kontrak')->where('nik', $nik)->orderBy('dari', 'desc')->first();
        if ($sp != null) {
            return view('pinjaman.notifsp', compact('sp'));
        } else {
            return view('pinjaman.create', compact('karyawan', 'gaji', 'jmk', 'kontrak'));
        }
    }

    public function store(Request $request)
    {
        $tgl_pinjaman = $request->tgl_pinjaman;
        $nik  = $request->nik;
        $status_karyawan = $request->status_karyawan;
        $akhir_kontrak = $request->akhir_kontrak;
        $gapok_tunjangan = str_replace(".", "", $request->gapok_tunjangan);
        $tenor_max = $request->tenor_max;
        $angsuran_max = str_replace(".", "", $request->angsuran_max);
        $jmk = str_replace(".", "", $request->jmk);
        $jmk_sudahbayar = str_replace(".", "", $request->jmk_sudahbayar);
        $plafon_max = str_replace(".", "", $request->plafon_max);
        $jumlah_pinjaman = str_replace(".", "", $request->jml_pinjaman);
        $angsuran = $request->angsuran;
        $jumlah_angsuran = str_replace(".", "", $request->jml_angsuran);
        $mulai_cicilan = $request->mulai_cicilan;

        $tanggal = $request->tgl_pinjaman;
        $tgl = explode("-", $tanggal);
        $tahun = substr($tgl[0], 2, 2);
        $pinjaman = DB::table("pinjaman")
            ->whereRaw('YEAR(tgl_pinjaman)="' . $tgl[0] . '"')
            ->orderBy("no_pinjaman", "desc")
            ->first();
        $last_nopinjaman = $pinjaman != null ? $pinjaman->no_pinjaman : '';
        $no_pinjaman  = buatkode($last_nopinjaman, "PJK" . $tahun, 3);

        $data = [
            'no_pinjaman' => $no_pinjaman,
            'tgl_pinjaman' => $tgl_pinjaman,
            'nik' => $nik,
            'status_karyawan' => $status_karyawan,
            'akhir_kontrak' => $akhir_kontrak,
            'gapok_tunjangan' => $gapok_tunjangan,
            'tenor_max' => $tenor_max,
            'angsuran_max' => $angsuran_max,
            'jmk' => $jmk,
            'jmk_sudahbayar' => $jmk_sudahbayar,
            'plafon_max' => $plafon_max,
            'jumlah_pinjaman' => $jumlah_pinjaman,
            'angsuran' => $angsuran,
            'jumlah_angsuran' => $jumlah_angsuran,
            'mulai_cicilan' => $mulai_cicilan
        ];

        DB::beginTransaction();
        try {
            DB::table('pinjaman')->insert($data);
            DB::commit();
            echo "Berhasil";
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
