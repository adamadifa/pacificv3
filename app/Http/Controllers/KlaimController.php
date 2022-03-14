<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Kaskecil;
use App\Models\Klaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class KlaimController extends Controller
{
    public function index(Request $request)
    {
        $query = Klaim::query();
        $query->whereBetween('tgl_klaim', [$request->dari, $request->sampai]);
        if (!empty($request->kode_cabang)) {
            $query->where('kode_cabang', $request->kode_cabang);
        }
        $klaim = $query->get();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('klaim.index', compact('klaim', 'cabang'));
    }

    public function cetak($kode_klaim, $excel)
    {
        $kode_klaim = Crypt::decrypt($kode_klaim);
        $klaim = DB::table('klaim')->where('kode_klaim', $kode_klaim)->first();
        $tgl_klaim = $klaim->tgl_klaim;
        $kode_cabang = $klaim->kode_cabang;
        $cekklaim = DB::table('klaim')->where('kode_cabang', $kode_cabang)->where('tgl_klaim', '<', $tgl_klaim)->count();
        if (empty($cekklaim)) {
            $sa = DB::table('kaskecil_detail')->where('keterangan', 'SALDO AWAL')->where('kode_cabang', $kode_cabang)->first();
            $saldoawal = $sa->jumlah;
        } else {
            $sa = DB::table('klaim')->where('kode_klaim', '<', $kode_klaim)->where('kode_cabang', $kode_cabang)->orderBy('kode_klaim', 'desc')->first();
            $saldoawal = $sa->saldo_akhir;
        }
        $detail = DB::table('kaskecil_detail')->where('kode_klaim', $kode_klaim)->get();

        if ($excel == 'true') {
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=$klaim->keterangan.xls");
        }
        return view('klaim.cetak', compact('saldoawal', 'klaim', 'detail'));
    }

    public function show($kode_klaim)
    {
        $kode_klaim = Crypt::decrypt($kode_klaim);
        $klaim = DB::table('klaim')->where('kode_klaim', $kode_klaim)->first();
        $tgl_klaim = $klaim->tgl_klaim;
        $kode_cabang = $klaim->kode_cabang;
        $cekklaim = DB::table('klaim')->where('kode_cabang', $kode_cabang)->where('tgl_klaim', '<', $tgl_klaim)->count();
        if (empty($cekklaim)) {
            $sa = DB::table('kaskecil_detail')->where('keterangan', 'SALDO AWAL')->where('kode_cabang', $kode_cabang)->first();
            $saldoawal = $sa->jumlah;
        } else {
            $sa = DB::table('klaim')->where('kode_klaim', '<', $kode_klaim)->where('kode_cabang', $kode_cabang)->orderBy('kode_klaim', 'desc')->first();
            $saldoawal = $sa->saldo_akhir;
        }
        $detail = DB::table('kaskecil_detail')->where('kode_klaim', $kode_klaim)->get();
        return view('klaim.show', compact('saldoawal', 'klaim', 'detail'));
    }

    public function delete($kode_klaim)
    {
        $kode_klaim = Crypt::decrypt($kode_klaim);
        DB::beginTransaction();
        try {
            DB::table('klaim')->where('kode_klaim', $kode_klaim)->delete();
            DB::table('kaskecil_detail')->where('kode_klaim', $kode_klaim)->update([
                'kode_klaim' => null
            ]);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);;
        }
    }

    public function create(Request $request)
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
        return view('klaim.create', compact('kaskecil', 'cabang', 'saldoawal'));
    }
}
