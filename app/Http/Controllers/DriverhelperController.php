<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverhelperController extends Controller
{
    public function getdriverhelpercab(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        // if ($kode_cabang == "GRT") {
        //     $kode_cabang = "TSM";
        // }
        $kategori = $request->kategori;
        $id_driver_helper = $request->id_driver_helper;
        $driverhelper = DB::table('driver_helper')
            ->join('cabang', 'driver_helper.kode_cabang', '=', 'cabang.kode_cabang')
            ->where('driver_helper.kode_cabang', $kode_cabang)
            ->orderBy('nama_driver_helper')->get();
        if ($kategori == "DRIVER") {
            echo "<option value=''>Pilih Driver</option>";
        } else {
            echo "<option value=''>Pilih Helper</option>";
        }
        foreach ($driverhelper as $d) {
            if ($d->id_driver_helper == $id_driver_helper) {
                $select = "selected";
            } else {
                $select = "";
            }
            echo "<option $select value='$d->id_driver_helper'>$d->nama_driver_helper</option>";
        }
    }
}
