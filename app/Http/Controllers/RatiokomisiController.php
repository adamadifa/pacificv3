<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;

class RatiokomisiController extends Controller
{
    public function index(){
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September","Oktober", "November", "Desember");
        return view('ratiokomisi.index',compact('cabang','bulan'));
    }
}
