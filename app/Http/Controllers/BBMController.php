<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
class BBMController extends Controller
{
    protected $cabang;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->cabang = Auth::user()->kode_cabang;
            return $next($request);
        });
        View::share('cabang', $this->cabang);
    }

    public function index(Request $request)
    {
        $kode_cabang = $request->kode_cabang;

        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->get();
        }
        $bln = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('bbm.index', compact('bln', 'cabang', 'kode_cabang'));
    }

    public function show(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $cabang = $request->kode_cabang;

        if ($cabang == '') {
            if (Auth::user()->kode_cabang != "PCF") {
                $bbm = DB::table('bbm')
                    ->leftjoin('driver_helper', 'bbm.id_driver', 'driver_helper.id_driver_helper')
                    ->where('bbm.kode_cabang', Auth::user()->kode_cabang)
                    ->orderBy('bbm.kode_cabang', 'ASC')
                    ->get();
            } else {
                $bbm = DB::table('bbm')
                    ->leftjoin('driver_helper', 'bbm.id_driver', 'driver_helper.id_driver_helper')
                    ->orderBy('bbm.kode_cabang', 'ASC')
                    ->get();
            }
        } else {
            $bbm = DB::table('bbm')
                ->leftjoin('driver_helper', 'bbm.id_driver', 'driver_helper.id_driver_helper')
                ->where('bbm.kode_cabang', $request->kode_cabang)
                ->orderBy('bbm.kode_cabang', 'ASC')
                ->get();
        }
        return view('bbm.show', compact('bbm'));
    }

    public function create()
    {

        $bln = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('bbm.create',compact('bln'));
    }

    public function store(Request $request)
    {

        DB::table('bbm')
            ->insert([
                'kode_cabang' => Auth::user()->kode_cabang,
                'tanggal' => $request->tanggal,
                'id_driver' => $request->id_driver,
                'tujuan' => $request->tujuan,
                'saldo_awal' => str_replace(".","",$request->saldo_awal),
                'saldo_akhir' => str_replace(".","",$request->saldo_akhir),
                'jumlah_liter' => str_replace(".","",$request->jumlah_liter),
                'keterangan' => $request->keterangan,
                'no_polisi' => $request->no_polisi,
            ]);
        return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
    }

    public function delete(Request $request)
    {
        $hapus = DB::table('bbm')->where('id', $request->id)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 2;
        }
    }

    public function edit(Request $request)
    {
        $bbm = DB::table('bbm')
            ->where('bbm.id', $request->id)->first();
        return view('bbm.edit', compact('bbm'));
    }

    public function update(Request $request)
    {
        $update = DB::table('bbm')
            ->where('id', $request->id)
            ->update([
                'tanggal' => $request->tanggal,
                'id_driver' => $request->id_driver,
                'tujuan' => $request->tujuan,
                'saldo_awal' => str_replace(".","",$request->saldo_awal),
                'saldo_akhir' => str_replace(".","",$request->saldo_akhir),
                'jumlah_liter' => str_replace(".","",$request->jumlah_liter),
                'keterangan' => $request->keterangan,
                'no_polisi' => $request->no_polisi,
            ]);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagak Disimpan']);
        }
    }
    public function laporanBBM()
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->get();
        }
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('bbm.laporan.frm_laporanBBM', compact('cabang', 'bulan'));
    }

    public function cetakbbm(Request $request)
    {
        $cabang = $request->kode_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $no_polisi = $request->no_polisi;
        $tanggal = Carbon::createFromDate($tahun, $bulan, 1);
        $tanggal->subMonth();
        $bln = $tanggal->month;
        $thn = $tanggal->year;

        $saldoawal = DB::table('bbm')
        ->selectRaw("SUM(saldo_awal) AS saldo_awal")
        ->whereRaw("MONTH(tanggal) = '$bln'")
        ->whereRaw("YEAR(tanggal) = '$thn'")
        ->where('bbm.kode_cabang', $cabang)
        ->where('bbm.no_polisi', $no_polisi)
        ->groupBy('saldo_awal')
        ->orderBy('bbm.tanggal','DESC')
        ->first();
        $bbm = DB::table('bbm')
        ->leftJoin('kendaraan', 'bbm.no_polisi', 'kendaraan.no_polisi')
        ->leftJoin('driver_helper', 'bbm.id_driver', 'driver_helper.id_driver_helper')
        ->whereRaw("MONTH(tanggal) = '$bulan'")
        ->whereRaw("YEAR(tanggal) = '$tahun'")
        ->where('bbm.no_polisi', $no_polisi)
        ->where('bbm.kode_cabang', $cabang)
        ->get();
        if (isset($_POST['export'])) {
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=Laporan BBM.xls");
        }
        return view('bbm.laporan.cetak_bbm', compact('bbm', 'bulan', 'tahun', 'cabang','saldoawal'));
    }
}
