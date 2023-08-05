<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LhpController extends Controller
{
    public function index()
    {
        $kode_cabang = Auth::user()->kode_cabang;
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($kode_cabang);
        return view('lhp.index', compact('cabang'));
    }


    public function create(Request $request)
    {
        $kode_cabang = Auth::user()->kode_cabang;
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($kode_cabang);


        $tanggal = $request->tanggal;
        $kode_cabang = $request->kode_cabang;
        $id_karyawan = $request->id_karyawan;

        $query = Penjualan::query();
        $query->select('penjualan.*', 'nama_pelanggan', 'nama_karyawan', 'karyawan.kode_cabang');
        $query->orderBy('tgltransaksi', 'desc');
        $query->orderBy('no_fak_penj', 'asc');
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');

        $query->leftJoin(
            DB::raw("(
                    SELECT
                        pj.no_fak_penj,
                        IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,
                        karyawan.nama_karyawan AS nama_sales,
                        IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                    FROM
                        penjualan pj
                    INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                    LEFT JOIN (
                    SELECT
                        MAX( id_move ) AS id_move,
                        no_fak_penj,
                        move_faktur.id_karyawan AS salesbaru,
                        karyawan.kode_cabang AS cabangbaru
                    FROM
                        move_faktur
                        INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                    GROUP BY
                        no_fak_penj,
                        move_faktur.id_karyawan,
                        karyawan.kode_cabang
                    ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj)
                ) pjmove"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'pjmove.no_fak_penj');
            }
        );
        $query->join('karyawan', 'pjmove.salesbarunew', '=', 'karyawan.id_karyawan');
        $query->where('tgltransaksi', $tanggal);
        $query->where('karyawan.kode_cabang', $kode_cabang);
        $query->where('penjualan.id_karyawan', $id_karyawan);
        $penjualan = $query->get();


        return view('lhp.create', compact('cabang', 'penjualan'));
    }
}
