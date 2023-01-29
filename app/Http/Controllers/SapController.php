<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class SapController extends Controller
{
    protected $cabang;
    public function __construct()
    {
        // Fetch the Site Settings object
        $this->middleware(function ($request, $next) {
            $this->cabang = Auth::user()->kode_cabang;
            return $next($request);
        });


        View::share('cabang', $this->cabang);
    }
    public function salesperfomance()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        return view('sap.salesperfomance', compact('cabang'));
    }

    public function getsalesperfomance(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $tanggal = $request->tanggal;

        // $checkin = DB::table('checkin')
        //     ->join('users', 'checkin.id_karyawan', '=', 'users.id')
        //     ->leftJoin('karyawan', 'users.id_salesman', '=', 'karyawan.id_karyawan')
        //     ->where('tgl_checkin', $tanggal)
        //     ->where('karyawan.kode_cabang', $kode_cabang)
        //     ->get();

        $karyawan = DB::table('karyawan')->where('kode_cabang', $kode_cabang)->get();
        return view('sap.getsalesperfomance', compact('karyawan'));
    }
}
