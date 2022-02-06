<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LpcController extends Controller
{
    public function index(){
        $bln = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('lpc.index',compact('bln'));
    }

    public function show(Request $request){
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $bln = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $lpc = DB::table('lpc')->where('bulan',$bulan)->where('tahun',$tahun)->get();
        return view('lpc.show',compact('lpc','bln'));
    }

    public function create(){
        $bln = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $cabang = DB::table('cabang')->get();
        return view('lpc.create',compact('cabang','bln'));
    }
}
