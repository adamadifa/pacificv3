<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankController extends Controller
{
    public function getbankcabang(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $kode_bank = $request->bank;
        $bank = DB::table('master_bank')->where('kode_cabang', $kode_cabang)->get();
        echo "<option value=''>Pilih Bank</option>";
        foreach ($bank as $d) {
            if ($kode_bank == $d->kode_bank) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            echo "<option $selected value='$d->kode_bank'>$d->nama_bank</option>";
        }
    }
}
