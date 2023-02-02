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
        $tanggal = explode("-", $request->tanggal);
        $tgl1 = explode("/", $tanggal[0]);
        $tgl2 = explode("/", $tanggal[1]);
        $dari = str_replace(' ', '', implode("-", array($tgl1[2], $tgl1[1], $tgl1[0])));
        $sampai = str_replace(' ', '', implode("-", array($tgl2[2], $tgl2[1], $tgl2[0])));


        // $checkin = DB::table('checkin')
        //     ->join('users', 'checkin.id_karyawan', '=', 'users.id')
        //     ->leftJoin('karyawan', 'users.id_salesman', '=', 'karyawan.id_karyawan')
        //     ->where('tgl_checkin', $tanggal)
        //     ->where('karyawan.kode_cabang', $kode_cabang)
        //     ->get();

        $karyawan = DB::table('karyawan')->where('kode_cabang', $kode_cabang)
            ->selectRaw('karyawan.id_karyawan,nama_karyawan,totalpenjualan,totalorder,totalkunjungan,totalcust')
            ->leftJoin(
                DB::raw("(
                SELECT id_karyawan, SUM(total) as totalpenjualan,COUNT(no_fak_penj) as totalorder
                FROM penjualan
                WHERE tgltransaksi BETWEEN '$dari' AND '$sampai'
                GROUP BY penjualan.id_karyawan
                ) pj"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'pj.id_karyawan');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT id_salesman, COUNT(kode_checkin) as totalkunjungan, COUNT(DISTINCT(kode_pelanggan)) as totalcust
                FROM checkin
                INNER JOIN users ON checkin.id_karyawan = users.id
                WHERE tgl_checkin BETWEEN '$dari' AND '$sampai'
                GROUP BY id_salesman
                ) check_in"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'check_in.id_salesman');
                }
            )
            ->where('status_aktif_sales', 1)
            ->where('nama_karyawan', '!=', '-')
            ->get();
        return view('sap.getsalesperfomance', compact('karyawan'));
    }
}
