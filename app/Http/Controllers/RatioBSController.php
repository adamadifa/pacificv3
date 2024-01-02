<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class RatioBSController extends Controller
{
    protected $cabang;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->cabang = Auth::user()->kode_cabang;
            return $next($request);
        });
        View::share('cabang', $this->cabang);
    }


    public function laporanRatioBS()
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->get();
        }
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('ratiobs.laporan.frm_laporanRatioBS', compact('cabang', 'bulan'));
    }


    public function cetakRatioBS(Request $request)
    {
        $cabang = $request->kode_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        if (isset($_POST['export'])) {
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=Laporan Ratio BS.xls");
        }
        return view('ratiobs.laporan.cetak_ratioBS', compact('bulan', 'tahun', 'cabang'));
    }
}
