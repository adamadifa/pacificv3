<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    public function index()
    {
        return view('tracking.index');
    }

    function getlocationcheckin()
    {
        $checkin = DB::table('checkin')
            ->select('checkin.*', 'nama_pelanggan', 'foto', 'alamat_pelanggan')
            ->join('pelanggan', 'checkin.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->get();
        $jsondata = json_encode($checkin);
        return $jsondata;
    }
}
