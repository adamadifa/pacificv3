<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class VisitController extends Controller
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
        return view('visit.index', compact('bln','cabang','kode_cabang'));
    }

    public function show(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $cabang = $request->kode_cabang;

        if($cabang == ''){
            $visit = DB::table('visit')
            ->join('penjualan','penjualan.no_fak_penj','visit.no_fak_penj')
            ->join('pelanggan','penjualan.kode_pelanggan','pelanggan.kode_pelanggan')
            ->join('karyawan','karyawan.id_karyawan','pelanggan.id_sales')
            ->whereRaw('MONTH(tgl_visit)=' . $bulan)
            ->whereRaw('YEAR(tgl_visit)=' . $tahun)
            ->get();
        }else{
            $visit = DB::table('visit')
            ->join('penjualan','penjualan.no_fak_penj','visit.no_fak_penj')
            ->join('pelanggan','penjualan.kode_pelanggan','pelanggan.kode_pelanggan')
            ->join('karyawan','karyawan.id_karyawan','pelanggan.id_sales')
            ->whereRaw('MONTH(tgl_visit)=' . $bulan)
            ->whereRaw('YEAR(tgl_visit)=' . $tahun)
            ->whereRaw('visit.kode_cabang', $cabang)
            ->get();
        }
        return view('visit.show', compact('visit'));
    }

    public function create()
    {

        return view('visit.create');
    }

    public function store(Request $request)
    {

        DB::table('visit')
        ->insert([
            'kode_cabang' => $request->kode_cabang,
            'tgl_visit' => $request->tgl_visit,
            'hasil_konfirmasi' => $request->hasil_konfirmasi,
            'no_fak_penj' => $request->no_fak_penj,
            'catatan' => $request->catatan,
            'action' => $request->action,
            'nominal' => $request->nominal,
            'saran' => $request->saran,
        ]);
        return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
    }

    public function delete(Request $request)
    {
        $hapus = DB::table('visit')->where('id', $request->id)->delete();
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
        $visit = DB::table('visit')
        ->where('visit.id', $request->id)->first();
        return view('visit.edit', compact('visit','cabang'));
    }

    public function update(Request $request)
    {
        $update = DB::table('visit')
            ->where('id', $request->id)
            ->update([
                'kode_cabang' => $request->kode_cabang,
                'tgl_visit' => $request->tgl_visit,
                'hasil_konfirmasi' => $request->hasil_konfirmasi,
                'no_fak_penj' => $request->no_fak_penj,
                'catatan' => $request->catatan,
                'action' => $request->action,
                'nominal' => $request->nominal,
                'saran' => $request->saran,
            ]);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagak Disimpan']);
        }

    }

    public function laporanVisit()
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->get();
        }
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('visit.laporan.frm_laporanVisit', compact('cabang','bulan'));
    }

    public function cetakVisit(Request $request)
    {
        $cabang = $request->kode_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        if($cabang == ''){
            $visit = DB::table('visit')
            ->select('tgl_visit','visit.kode_cabang','visit.no_fak_penj','action','saran','hasil_konfirmasi','nama_pelanggan','nama_karyawan','pasar','tgltransaksi','jenistransaksi','nominal','catatan')
            ->join('penjualan','penjualan.no_fak_penj','visit.no_fak_penj')
            ->join('pelanggan','penjualan.kode_pelanggan','pelanggan.kode_pelanggan')
            ->join('karyawan','karyawan.id_karyawan','pelanggan.id_sales')
            ->whereRaw('MONTH(tgl_visit)=' . $bulan)
            ->whereRaw('YEAR(tgl_visit)=' . $tahun)
            ->get();
        }else{
            $visit = DB::table('visit')
            ->select('tgl_visit','visit.kode_cabang','visit.no_fak_penj','action','saran','hasil_konfirmasi','nama_pelanggan','nama_karyawan','pasar','tgltransaksi','jenistransaksi','nominal','catatan')
            ->join('penjualan','penjualan.no_fak_penj','visit.no_fak_penj')
            ->join('pelanggan','penjualan.kode_pelanggan','pelanggan.kode_pelanggan')
            ->join('karyawan','karyawan.id_karyawan','pelanggan.id_sales')
            ->whereRaw('MONTH(tgl_visit)=' . $bulan)
            ->whereRaw('YEAR(tgl_visit)=' . $tahun)
            ->whereRaw('visit.kode_cabang', $cabang)
            ->get();
        }
        if (isset($_POST['export'])) {
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=Laporan Visit Pelanggan.xls");
        }
        return view('visit.laporan.cetak_visit', compact('visit','bulan','tahun','cabang'));
    }
}
