<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KonfigurasijadwalController extends Controller
{
    public function store(Request $request)
    {
        $tahun = substr(date('Y'), 2, 2);
        $konfigurasijadwal = DB::table('konfigurasi_jadwalkerjad')->whereRaw('MID(kode_setjadwal,2,2)="' . $tahun . '"')
            ->orderBy('kode_setjadwal', 'desc')->first();
    }
}
