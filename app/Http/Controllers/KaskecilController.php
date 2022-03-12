<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Kaskecil;
use App\Models\Setcoacabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KaskecilController extends Controller
{
    public function index(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query = Kaskecil::query();
            $query->selectRaw('id,nobukti,tgl_kaskecil,kaskecil_detail.keterangan,kaskecil_detail.jumlah,kaskecil_detail.kode_akun,status_dk,nama_akun,kaskecil_detail.kode_klaim,klaim.keterangan as ket_klaim,no_ref,
            costratio_biaya.kode_cr, kaskecil_detail.kode_cr as kk_cr,peruntukan');
            $query->join('coa', 'kaskecil_detail.kode_akun', '=', 'coa.kode_akun');
            $query->leftJoin('klaim', 'kaskecil_detail.kode_klaim', '=', 'klaim.kode_klaim');
            $query->leftJoin('costratio_biaya', 'kaskecil_detail.kode_cr', '=', 'costratio_biaya.kode_cr');
            $query->whereBetween('tgl_kaskecil', [$request->dari, $request->sampai]);
            $query->where('kaskecil_detail.kode_cabang', $kode_cabang);
            $query->orderBy('tgl_kaskecil');
            $query->orderBy('order');
            $query->orderBy('nobukti');
            $kaskecil = $query->get();


            $qsaldoawal = Kaskecil::query();
            $qsaldoawal->selectRaw("SUM(IF( `status_dk` = 'K', jumlah, 0)) -SUM(IF( `status_dk` = 'D', jumlah, 0)) as saldo_awal");
            $qsaldoawal->where('tgl_kaskecil', '<', $request->dari);
            $qsaldoawal->where('kode_cabang', $kode_cabang);
            $saldoawal = $qsaldoawal->first();
        } else {
            $kaskecil = null;
            $saldoawal = null;
        }


        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('kaskecil.index', compact('kaskecil', 'cabang', 'saldoawal'));
    }

    public function create()
    {
        $qcoa = Setcoacabang::query();
        $qcoa->select('set_coa_cabang.kode_akun', 'nama_akun');
        $qcoa->join('coa', 'set_coa_cabang.kode_akun', '=', 'coa.kode_akun');
        $qcoa->where('kategori', 'Kas Kecil');
        $qcoa->groupBy('kode_akun', 'nama_akun');
        if (Auth::user()->kode_cabang != "PCF") {
            $qcoa->where('kode_cabang', Auth::user()->kode_cabang);
        }
        $qcoa->orderBy('kode_akun');
        $coa = $qcoa->get();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('kaskecil.create', compact('coa', 'cabang'));
    }

    public function getkaskeciltemp(Request $request)
    {
        $kaskeciltemp = DB::table('kaskecil_detail_temp')->where('nobukti', $request->nobukti)->where('kode_cabang', $request->kode_cabang)
            ->join('coa', 'kaskecil_detail_temp.kode_akun', '=', 'coa.kode_akun')
            ->get();

        return view('kaskecil.getkaskeciltemp', compact('kaskeciltemp'));
    }

    public function storetemp(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $status_dk = $request->inout;
        $tgl_kaskecil = $request->tgl_kaskecil;
        $nobukti = $request->nobukti;
        $keterangan = $request->keterangan;
        $jumlah  = str_replace(".", "", $request->jumlah);
        $kode_akun = $request->kode_akun;
        $peruntukan = $request->peruntukan;
        $data = array(
            'tgl_kaskecil' => $tgl_kaskecil,
            'nobukti'      => $nobukti,
            'keterangan'   => $keterangan,
            'jumlah'       => $jumlah,
            'kode_akun'    => $kode_akun,
            'kode_cabang'  => $kode_cabang,
            'status_dk'    => $status_dk,
            'peruntukan'   => $peruntukan
        );

        $simpan = DB::table('kaskecil_detail_temp')->insert($data);
        if ($simpan) {
            echo 0;
        } else {
            echo 1;
        }
    }
}
