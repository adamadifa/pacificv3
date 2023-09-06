<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class JaminanController extends Controller
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
        return view('jaminan.index', compact('bln', 'cabang', 'kode_cabang'));
    }

    public function show(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $cabang = $request->kode_cabang;

        if ($cabang == '') {
            if (Auth::user()->kode_cabang != "PCF") {
                $jaminan = DB::table('jaminan')
                    ->leftjoin('pelanggan', 'jaminan.kode_pelanggan', 'pelanggan.kode_pelanggan')
                    ->where('bulan',$bulan)
                    ->where('tahun',$tahun)
                    ->where('jaminan.kode_cabang', Auth::user()->kode_cabang)
                    ->orderBy('jaminan.kode_cabang', 'ASC')
                    ->orderBy('jaminan.bulan', 'ASC')
                    ->orderBy('jaminan.tahun', 'ASC')
                    ->get();
            } else {
                $jaminan = DB::table('jaminan')
                    ->leftjoin('pelanggan', 'jaminan.kode_pelanggan', 'pelanggan.kode_pelanggan')
                    ->where('bulan',$bulan)
                    ->where('tahun',$tahun)
                    ->orderBy('jaminan.kode_cabang', 'ASC')
                    ->orderBy('jaminan.bulan', 'ASC')
                    ->orderBy('jaminan.tahun', 'ASC')
                    ->get();
            }
        } else {
            $jaminan = DB::table('jaminan')
                ->leftjoin('pelanggan', 'jaminan.kode_pelanggan', 'pelanggan.kode_pelanggan')
                ->where('bulan',$bulan)
                ->where('tahun',$tahun)
                ->where('jaminan.kode_cabang', $request->kode_cabang)
                ->orderBy('jaminan.kode_cabang', 'ASC')
                ->orderBy('jaminan.bulan', 'ASC')
                ->orderBy('jaminan.tahun', 'ASC')
                ->get();
        }
        return view('jaminan.show', compact('jaminan'));
    }

    public function create()
    {

        $bln = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('jaminan.create',compact('bln'));
    }

    public function store(Request $request)
    {

        DB::table('jaminan')
            ->insert([
                'kode_cabang' => Auth::user()->kode_cabang,
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'keterangan' => $request->keterangan,
                'jenis_jaminan' => $request->jenis_jaminan,
                'pengikat_jaminan' => $request->pengikat_jaminan,
                'total_piutang' => str_replace(".","",$request->total_piutang),
                'nilai_jaminan' => str_replace(".","",$request->nilai_jaminan),
                'kode_pelanggan' => $request->kode_pelanggan,
                'created_at' => Date('Y-m-d H:i:s'),
            ]);
        return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
    }

    public function delete(Request $request)
    {
        $hapus = DB::table('jaminan')->where('id', $request->id)->delete();
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
        $jaminan = DB::table('jaminan')
            ->leftjoin('pelanggan', 'jaminan.kode_pelanggan', 'pelanggan.kode_pelanggan')
            ->where('jaminan.id', $request->id)->first();
        $bln = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('jaminan.edit', compact('jaminan', 'cabang','bln'));
    }

    public function update(Request $request)
    {
        $update = DB::table('jaminan')
            ->where('id', $request->id)
            ->update([
                'kode_cabang' => Auth::user()->kode_cabang,
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'keterangan' => $request->keterangan,
                'jenis_jaminan' => $request->jenis_jaminan,
                'pengikat_jaminan' => $request->pengikat_jaminan,
                'total_piutang' => str_replace(".","",$request->total_piutang),
                'nilai_jaminan' => str_replace(".","",$request->nilai_jaminan),
                'kode_pelanggan' => $request->kode_pelanggan,
                'updated_at' => Date('Y-m-d H:i:s'),
            ]);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagak Disimpan']);
        }
    }

    public function laporanJaminan()
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->get();
        }
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('jaminan.laporan.frm_laporanJaminan', compact('cabang', 'bulan'));
    }

    public function cetakJaminan(Request $request)
    {
        $cabang = $request->kode_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        if ($cabang == '') {
            $jaminan = DB::table('jaminan')
                ->join('pelanggan', 'jaminan.kode_pelanggan', 'pelanggan.kode_pelanggan')
                ->where('bulan',$bulan)
                ->where('tahun',$tahun)
                ->get();
        } else {
            $jaminan = DB::table('jaminan')
                ->join('pelanggan', 'jaminan.kode_pelanggan', 'pelanggan.kode_pelanggan')
                ->where('bulan',$bulan)
                ->where('tahun',$tahun)
                ->where('jaminan.kode_cabang', $request->kode_cabang)
                ->get();
        }
        if (isset($_POST['export'])) {
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=Laporan jaminan Program.xls");
        }
        return view('jaminan.laporan.cetak_jaminan', compact('jaminan', 'bulan', 'tahun', 'cabang'));
    }
}
