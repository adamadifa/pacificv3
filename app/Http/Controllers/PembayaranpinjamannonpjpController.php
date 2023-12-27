<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PembayaranpinjamannonpjpController extends Controller
{
    public function create(Request $request)
    {
        $no_pinjaman_nonpjp = $request->no_pinjaman_nonpjp;
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('pembayaranpiutangkaryawan.create', compact('no_pinjaman_nonpjp', 'bulan'));
    }

    public function store(Request $request)
    {
        $no_pinjaman_nonpjp = $request->no_pinjaman_nonpjp;
        $jenis_bayar = $request->jenis_bayar;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        if ($bulan == 12) {
            $bulanpotongan = 1;
            $tahunpotongan = $tahun + 1;
        } else {
            $bulanpotongan = $bulan + 1;
            $tahunpotongan = $tahun;
        }

        $kode_potongan = $jenis_bayar == 1 ? "GJ" . $bulan . $tahun : "";
        $start = $tahun . "-" . $bulan . "-01";
        $tgl_bayar = $tahunpotongan . "-" . $bulanpotongan . "-01";
        $jumlah = str_replace(".", "", $request->jumlah);
        $id_user = Auth::user()->id;
        $historibayar = DB::table("pinjaman_nonpjp_historibayar")
            ->whereRaw('YEAR(tgl_bayar)="' . $tahun . '"')
            ->orderBy("no_bukti", "desc")
            ->first();
        $tahun = substr($tahun, 2, 2);
        $last_nobukti = $historibayar != null ? $historibayar->no_bukti : '';
        $no_bukti  = buatkode($last_nobukti, "PK" . substr($tahunpotongan, 2, 2), 4);

        $cek = DB::table('pinjaman_nonpjp_historibayar')
            ->where('no_pinjaman_nonpjp', $no_pinjaman_nonpjp)
            ->where('kode_potongan', $kode_potongan)->count();

        $cekpotongkomisi = DB::table('pinjaman_nonpjp_historibayar')
            ->where('no_pinjaman_nonpjp', $no_pinjaman_nonpjp)
            ->where('tgl_bayar', $tgl_bayar)
            ->where('jenis_bayar', 2)
            ->count();

        $cektitipan = DB::table('pinjaman_nonpjp_historibayar')
            ->where('no_pinjaman_nonpjp', $no_pinjaman_nonpjp)
            ->where('tgl_bayar', $tgl_bayar)
            ->where('jenis_bayar', 3)->count();


        if ($cek > 0 && $jenis_bayar == 1) {
            echo 2;
        } else if ($jenis_bayar == 2 && $cekpotongkomisi > 0) {
            echo 3;
        } else if ($jenis_bayar == 3 && $cektitipan > 0) {
            echo 4;
        } else {
            try {
                $data = [
                    'no_bukti' => $no_bukti,
                    'tgl_bayar' => $tgl_bayar,
                    'no_pinjaman_nonpjp' => $no_pinjaman_nonpjp,
                    'jumlah' => $jumlah,
                    'jenis_bayar' => $jenis_bayar,
                    'kode_potongan' => $kode_potongan,
                    'id_user' => $id_user
                ];

                DB::table('pinjaman_nonpjp_historibayar')->insert($data);
                echo 0;
            } catch (\Exception $e) {
                //echo 1;
                dd($e);
            }
        }
    }

    public function delete(Request $request)
    {
        $no_bukti = $request->no_bukti;
        try {
            DB::table('pinjaman_nonpjp_historibayar')->where('no_bukti', $no_bukti)->delete();
            echo 0;
        } catch (\Exception $e) {
            echo 1;
        }
    }
}
