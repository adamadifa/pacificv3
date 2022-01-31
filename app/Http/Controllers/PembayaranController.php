<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PembayaranController extends Controller
{
    public function store(Request $request)
    {
        $id_admin = Auth::user()->id;
        $no_fak_penj = $request->no_fak_penj;
        $tglbayar = $request->tglbayar;
        $bayar = str_replace(".", "", $request->bayar);
        $jenisbayar = $request->jenisbayar;
        $girotocash = $request->girotocash;
        $voucher = $request->voucher;
        $ket_voucher = $request->ket_voucher;
        $id_karyawan = $request->id_karyawan;
        $jenistransaksi = $request->jenistransaksi;
        $kode_cabang = $request->kode_cabang;
        if ($girotocash == 1) {
            $id_giro = $request->id_giro;
        } else {
            $id_giro = NULL;
        }

        if (isset($request->voucher)) {
            $status_bayar = $voucher;
            $ket_voucher = $ket_voucher;
        } else {
            $status_bayar = NULL;
            $ket_voucher = NULL;
        }

        $tahunini = date("y");
        $historibayar = DB::table("historibayar")
            ->whereRaw('LEFT(nobukti,6) = "' . $kode_cabang . $tahunini . '-"')
            ->orderBy("nobukti", "desc")
            ->first();
        $lastnobukti = $historibayar->nobukti;
        $nobukti  = buatkode($lastnobukti, $kode_cabang . $tahunini . "-", 6);



        $simpan = DB::table('historibayar')
            ->insert([
                'nobukti' => $nobukti,
                'tglbayar' => $tglbayar,
                'no_fak_penj' => $no_fak_penj,
                'jenistransaksi' => $jenistransaksi,
                'jenisbayar' => $jenisbayar,
                'bayar' => $bayar,
                'girotocash' => $girotocash,
                'status_bayar' => $status_bayar,
                'ket_voucher' => $ket_voucher,
                'id_karyawan' => $id_karyawan,
                'id_giro' => $id_giro,
                'id_admin' => $id_admin
            ]);

        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Pembayaran Berhasil Disimpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Pembayaran Gagal Disimpan']);
        }
    }

    public function edit(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $salesman = DB::table('karyawan')->where('kode_cabang', $kode_cabang)->get();
        $girotolak = DB::table('giro')
            ->select('giro.id_giro', 'no_giro')
            ->leftJoin(
                DB::raw("(
                SELECT id_giro,girotocash FROM historibayar WHERE no_fak_penj ='$request->no_fak_penj'
            ) hb"),
                function ($join) {
                    $join->on('giro.id_giro', '=', 'hb.id_giro');
                }
            )
            ->where('giro.status', 2)
            ->where('giro.no_fak_penj', $request->no_fak_penj)
            ->get();
        $hb = DB::table('historibayar')->where('nobukti', $request->nobukti)->first();
        $sisabayar = $request->sisabayar;
        return view('pembayaran.edit', compact('hb', 'salesman', 'girotolak', 'sisabayar'));
    }


    public function update($nobukti, Request $request)
    {
        $id_admin = Auth::user()->id;
        $tglbayar = $request->tglbayar_edit;
        $bayar = str_replace(".", "", $request->bayar_edit);
        //$jenisbayar = $request->jenisbayar;
        $girotocash = $request->girotocash;
        $voucher = $request->voucher;
        $ket_voucher = $request->ket_voucher;
        $id_karyawan = $request->id_karyawan;
        //$jenistransaksi = $request->jenistransaksi;
        $kode_cabang = $request->kode_cabang;
        if ($girotocash == 1) {
            $id_giro = $request->id_giro;
        } else {
            $id_giro = NULL;
        }

        if (isset($request->voucher)) {
            $status_bayar = $voucher;
            $ket_voucher = $ket_voucher;
        } else {
            $status_bayar = NULL;
            $ket_voucher = NULL;
        }


        $simpan = DB::table('historibayar')
            ->where('nobukti', $nobukti)
            ->update([
                'tglbayar' => $tglbayar,
                // 'jenistransaksi' => $jenistransaksi,
                // 'jenisbayar' => $jenisbayar,
                'bayar' => $bayar,
                'girotocash' => $girotocash,
                'status_bayar' => $status_bayar,
                'ket_voucher' => $ket_voucher,
                'id_karyawan' => $id_karyawan,
                'id_giro' => $id_giro,
                'id_admin' => $id_admin
            ]);

        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Pembayaran Berhasil Di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Pembayaran Gagal Di Update']);
        }
    }

    public function delete($nobukti)
    {


        $nobukti = Crypt::decrypt($nobukti);
        $hapus = DB::table('historibayar')
            ->where('nobukti', $nobukti)
            ->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Pembayaran Berhasil Di Hapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Pembayaran Gagal Di Hapus']);
        }
    }
}
