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
        return view('sap.getsalesperfomance', compact('karyawan', 'dari', 'sampai'));
    }

    public function salesperfomancedetail(Request $request)
    {

        $salesman = DB::table('karyawan')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->where('id_karyawan', $request->id_karyawan)->first();
        return view('sap.salesperfomance_detail', compact('salesman'));
    }

    public function getpenjualansalesman(Request $request)
    {
        $id_karyawan = $request->id_karyawan;
        $dari = $request->dari;
        $sampai = $request->sampai;

        $penjualan = DB::table('penjualan')
            ->selectRaw('SUM(total) as totalpenjualan,
            SUM(potongan) as totalpotongan,
            SUM(potistimewa) as totalpotis,
            SUM(penyharga) as totalpeny,
            SUM(ppn) as totalppn,
            SUM(IF(jenistransaksi="tunai",subtotal,0)) as totaltunai,
            SUM(IF(jenistransaksi="kredit",subtotal,0)) as totalkredit,
            SUM(IF(jenistransaksi="kredit",subtotal,0)) as totalkredit,
            SUM(IF(penjualan.jenistransaksi="tunai",1,0)) as ordertunai,
            SUM(IF(penjualan.jenistransaksi="kredit",1,0)) as orderkredit')
            ->where('penjualan.id_karyawan', $id_karyawan)
            ->whereBetween('tgltransaksi', [$dari, $sampai])
            ->first();

        $detailpenjualan = DB::table('master_barang')
            ->selectRaw('master_barang.kode_produk,nama_barang,qty,subtotal,isipcsdus,nama_barang')
            ->leftJoin(
                DB::raw("(
                    SELECT kode_produk,
                    SUM(jumlah) as qty, SUM(detailpenjualan.subtotal) as subtotal
                    FROM detailpenjualan
                    INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                    INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                    WHERE tgltransaksi BETWEEN '$dari' AND '$sampai' AND penjualan.id_karyawan = 'SBDG01'
                    GROUP BY kode_produk
                    ) dp"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'dp.kode_produk');
                }
            )
            ->where('status', 1)
            ->orderBy('master_barang.kode_produk')
            ->get();
        return view('sap.getpenjualansalesman', compact('penjualan', 'detailpenjualan'));
    }
}
