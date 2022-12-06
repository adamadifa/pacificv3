<?php

namespace App\Http\Controllers;

use App\Models\Checkin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    public function index()
    {
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        return view('tracking.index', compact('cabang'));
    }

    function getlocationcheckin(Request $request)
    {
        $hariini = $request->tanggal;
        $kode_cabang = $request->kode_cabang;


        $query = Checkin::query();
        $query->select('checkin.*', 'nama_pelanggan', 'foto', 'alamat_pelanggan');
        $query->join('pelanggan', 'checkin.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->where('tgl_checkin', $hariini);
        if (!empty($kode_cabang)) {
            $query->where('pelanggan.kode_cabang', $kode_cabang);
        }
        $checkin = $query->get();
        $jsondata = json_encode($checkin);
        return $jsondata;
    }
}
