<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Checkin;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class TrackingController extends Controller
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
    public function index()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        return view('tracking.index', compact('cabang'));
    }

    public function mappelanggan()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
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

    function getlocationcheckinsalesman(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $id_salesman = $request->id_salesman;

        $query = Checkin::query();
        $query->select('checkin.*', 'nama_pelanggan', 'pelanggan.foto', 'alamat_pelanggan', 'marker');
        $query->join('pelanggan', 'checkin.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('users', 'checkin.id_karyawan', '=', 'users.id');
        $query->leftjoin('karyawan', 'users.id_salesman', '=', 'karyawan.id_karyawan');
        $query->whereBetween('tgl_checkin', [$dari, $sampai]);
        $query->where('karyawan.id_karyawan', $id_salesman);
        $checkin = $query->get();
        $jsondata = json_encode($checkin);
        return $jsondata;
    }


    function getmappelanggan(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $id_salesman = $request->id_salesman;

        $query = Pelanggan::query();
        $query->select('kode_pelanggan', 'nama_pelanggan', 'foto', 'alamat_pelanggan', 'latitude', 'longitude', 'colormarker');
        $query->join('cabang', 'pelanggan.kode_cabang', '=', 'cabang.kode_cabang');
        $query->whereNotNull('latitude');
        $query->whereNotNull('longitude');
        $query->where('latitude', '!=', 0);
        $query->where('longitude', '!=', 0);
        $query->where('status_pelanggan', 1);
        // $query->where('kode_cabang', 'BDG');
        if (!empty($kode_cabang)) {
            $query->where('pelanggan.kode_cabang', $kode_cabang);
        }

        if (!empty($id_salesman)) {
            $query->where('pelanggan.id_sales', $id_salesman);
        }
        $pelanggan = $query->get();
        $jsondata = json_encode($pelanggan);
        return $jsondata;
    }
}
