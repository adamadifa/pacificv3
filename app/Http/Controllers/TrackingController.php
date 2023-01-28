<?php

namespace App\Http\Controllers;

use App\Models\Checkin;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    public function index()
    {
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        return view('tracking.index', compact('cabang'));
    }

    public function mappelanggan()
    {
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        return view('tracking.mappelanggan', compact('cabang'));
    }

    function getlocationcheckin(Request $request)
    {
        $hariini = $request->tanggal;
        $kode_cabang = $request->kode_cabang;
        $id_salesman = $request->id_salesman;

        $query = Checkin::query();
        $query->select('checkin.*', 'nama_pelanggan', 'pelanggan.foto', 'alamat_pelanggan', 'marker');
        $query->join('pelanggan', 'checkin.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('users', 'checkin.id_karyawan', '=', 'users.id');
        $query->leftjoin('karyawan', 'users.id_salesman', '=', 'karyawan.id_karyawan');
        $query->where('tgl_checkin', $hariini);
        if (!empty($kode_cabang)) {
            $query->where('pelanggan.kode_cabang', $kode_cabang);
        }

        if (!empty($id_salesman)) {
            $query->where('karyawan.id_karyawan', $id_salesman);
        }
        $checkin = $query->get();
        $jsondata = json_encode($checkin);
        return $jsondata;
    }


    function getmappelanggan(Request $request)
    {
        $hariini = $request->tanggal;
        $kode_cabang = $request->kode_cabang;


        $query = Pelanggan::query();
        $query->select('kode_pelanggan', 'nama_pelanggan', 'foto', 'alamat_pelanggan', 'latitude', 'longitude');
        $query->whereNotNull('latitude');
        $query->whereNotNull('longitude');
        // $query->where('kode_cabang', 'BDG');
        if (!empty($kode_cabang)) {
            $query->where('pelanggan.kode_cabang', $kode_cabang);
        }
        $pelanggan = $query->get();
        $jsondata = json_encode($pelanggan);
        return $jsondata;
    }
}
