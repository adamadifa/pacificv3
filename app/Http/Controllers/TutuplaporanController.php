<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TutuplaporanController extends Controller
{
    public function cektutuplaporan(Request $request)
    {
        $tanggal = explode("-", $request->tanggal);
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];
        $cek = DB::table('tutup_laporan')
            ->where('jenis_laporan', $request->jenislaporan)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('status', 1)
            ->count();
        echo $cek;
    }
}