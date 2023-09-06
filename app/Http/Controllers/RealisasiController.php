<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class RealisasiController extends Controller
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
        return view('realisasi.index', compact('bln', 'cabang', 'kode_cabang'));
    }

    public function show(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $cabang = $request->kode_cabang;

        if ($cabang == '') {
            if (Auth::user()->kode_cabang != "PCF") {
                $realisasi = DB::table('realisasi')
                    ->leftjoin('pelanggan', 'realisasi.kode_pelanggan', 'pelanggan.kode_pelanggan')
                    ->where('bulan',$bulan)
                    ->where('tahun',$tahun)
                    ->where('realisasi.kode_cabang', Auth::user()->kode_cabang)
                    ->orderBy('realisasi.kode_cabang', 'ASC')
                    ->orderBy('realisasi.bulan', 'ASC')
                    ->orderBy('realisasi.tahun', 'ASC')
                    ->get();
            } else {
                $realisasi = DB::table('realisasi')
                    ->leftjoin('pelanggan', 'realisasi.kode_pelanggan', 'pelanggan.kode_pelanggan')
                    ->where('bulan',$bulan)
                    ->where('tahun',$tahun)
                    ->orderBy('realisasi.kode_cabang', 'ASC')
                    ->orderBy('realisasi.bulan', 'ASC')
                    ->orderBy('realisasi.tahun', 'ASC')
                    ->get();
            }
        } else {
            $realisasi = DB::table('realisasi')
                ->leftjoin('pelanggan', 'realisasi.kode_pelanggan', 'pelanggan.kode_pelanggan')
                ->where('bulan',$bulan)
                ->where('tahun',$tahun)
                ->where('realisasi.kode_cabang', $request->kode_cabang)
                ->orderBy('realisasi.kode_cabang', 'ASC')
                ->orderBy('realisasi.bulan', 'ASC')
                ->orderBy('realisasi.tahun', 'ASC')
                ->get();
        }
        return view('realisasi.show', compact('realisasi'));
    }

    public function create()
    {

        $bln = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('realisasi.create',compact('bln'));
    }

    public function store(Request $request)
    {

        DB::table('realisasi')
            ->insert([
                'kode_cabang' => Auth::user()->kode_cabang,
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'keterangan' => $request->keterangan,
                'im' => $request->im,
                'ajuan' => $request->ajuan,
                'nominal' => str_replace(".","",$request->nominal),
                'kode_pelanggan' => $request->kode_pelanggan,
                'bentuk_hadiah' => $request->bentuk_hadiah,
            ]);
        return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
    }

    public function delete(Request $request)
    {
        $hapus = DB::table('realisasi')->where('id', $request->id)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 2;
        }
    }

    public function edit(Request $request)
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->get();
        }
        $realisasi = DB::table('realisasi')
            ->leftjoin('pelanggan', 'realisasi.kode_pelanggan', 'pelanggan.kode_pelanggan')
            ->where('realisasi.id', $request->id)->first();
        $bln = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('realisasi.edit', compact('realisasi', 'cabang','bln'));
    }

    public function update(Request $request)
    {
        $update = DB::table('realisasi')
            ->where('id', $request->id)
            ->update([
                'kode_cabang' => Auth::user()->kode_cabang,
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'keterangan' => $request->keterangan,
                'im' => $request->im,
                'ajuan' => $request->ajuan,
                'nominal' => str_replace(".","",$request->nominal),
                'kode_pelanggan' => $request->kode_pelanggan,
                'bentuk_hadiah' => $request->bentuk_hadiah,
            ]);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagak Disimpan']);
        }
    }

    public function laporanRealisasi()
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->get();
        }
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('realisasi.laporan.frm_laporanRealisasi', compact('cabang', 'bulan'));
    }

    public function cetakRealisasi(Request $request)
    {
        $cabang = $request->kode_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        if ($cabang == '') {
            $realisasi = DB::table('realisasi')
                ->join('pelanggan', 'realisasi.kode_pelanggan', 'pelanggan.kode_pelanggan')
                ->where('bulan',$bulan)
                ->where('tahun',$tahun)
                ->get();
        } else {
            $realisasi = DB::table('realisasi')
                ->join('pelanggan', 'realisasi.kode_pelanggan', 'pelanggan.kode_pelanggan')
                ->where('bulan',$bulan)
                ->where('tahun',$tahun)
                ->where('realisasi.kode_cabang', $request->kode_cabang)
                ->get();
        }
        if (isset($_POST['export'])) {
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=Laporan Realisasi Program.xls");
        }
        return view('realisasi.laporan.cetak_realisasi', compact('realisasi', 'bulan', 'tahun', 'cabang'));
    }
}
