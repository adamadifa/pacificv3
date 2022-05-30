<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WhatsappController extends Controller
{
    public function cekdpb()
    {
        $hariini = date("Y-m-d");
        $dpb = DB::table('dpb')->select('kode_cabang')->where('tgl_pengambilan', $hariini)->get();
        $cabang = [];
        foreach ($dpb as $d) {
            $cabang[] = $d->kode_cabang;
        }

        $cekdpb = DB::table('cabang')->select('kode_cabang')->whereNotIn('kode_cabang', $cabang)->get();
        echo json_encode($cekdpb);
    }

    public function cekpenjualan()
    {
        $tanggal = date("Y-m-d");
        $day = date('D', strtotime($tanggal));
        $dayList = array(
            'Sun' => 'Minggu',
            'Mon' => 'Senin',
            'Tue' => 'Selasa',
            'Wed' => 'Rabu',
            'Thu' => 'Kamis',
            'Fri' => 'Jumat',
            'Sat' => 'Sabtu'
        );
        if ($dayList[$day] == "Senin") {

            $tgl_kemarin    = date('Y-m-d', strtotime("-2 day", strtotime(date("Y-m-d"))));
        } else {
            $tgl_kemarin    = date('Y-m-d', strtotime("-1 day", strtotime(date("Y-m-d"))));
        }

        $penjualan = DB::table('penjualan')
            ->select('kode_cabang')
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->where('tgltransaksi', $tgl_kemarin)
            ->groupBy('kode_cabang')->get();

        $cabang = [];
        foreach ($penjualan as $d) {
            $cabang[] = $d->kode_cabang;
        }

        $cekpenjualan = DB::table('cabang')->select('kode_cabang')->whereNotIn('kode_cabang', $cabang)->get();
        echo json_encode($cekpenjualan);
    }
}
