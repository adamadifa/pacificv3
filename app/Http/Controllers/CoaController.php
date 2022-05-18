<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoaController extends Controller
{
    public function getcoacabang(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        if ($kode_cabang != "") {

            $coa = DB::table('set_coa_cabang')
                ->select('set_coa_cabang.kode_akun', 'nama_akun')
                ->join('coa', 'set_coa_cabang.kode_akun', '=', 'coa.kode_akun')
                ->where('set_coa_cabang.kode_cabang', $kode_cabang)->groupBy('set_coa_cabang.kode_akun', 'nama_akun')->get();
        } else {
            $coa = DB::table('set_coa_cabang')
                ->select('set_coa_cabang.kode_akun', 'nama_akun')
                ->join('coa', 'set_coa_cabang.kode_akun', '=', 'coa.kode_akun')
                ->groupBy('set_coa_cabang.kode_akun', 'nama_akun')->get();
        }

        echo "<option value=''>Semua Akun</option>";
        foreach ($coa as $d) {
            echo "<option value='$d->kode_akun'>$d->kode_akun" . " " . "$d->nama_akun</option>";
        }
    }
}