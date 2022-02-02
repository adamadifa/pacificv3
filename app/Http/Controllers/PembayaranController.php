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

    public function storegiro(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $tgl_giro = $request->tgl_giro;
        $no_giro = $request->no_giro;
        $namabank = $request->namabank_giro;
        $materai = "-";
        $tglcair = $request->tglcair;
        $jumlah = str_replace(".", "", $request->jumlah_giro);
        $id_karyawan = $request->id_karyawan;
        $simpan = DB::table('giro')
            ->insert([
                'no_fak_penj' => $no_fak_penj,
                'tgl_giro' => $tgl_giro,
                'no_giro' => $no_giro,
                'namabank' => $namabank,
                'materai' => $materai,
                'tglcair' => $tglcair,
                'jumlah' => $jumlah,
                'id_karyawan' => $id_karyawan,
                'status' => 0
            ]);

        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Giro Berhasil Di Simpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Giro Gagal Di Hapus']);
        }
    }




    public function deletegiro($id_giro)
    {


        $id_giro = Crypt::decrypt($id_giro);
        $hapus = DB::table('giro')
            ->where('id_giro', $id_giro)
            ->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Giro Berhasil Di Hapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Giro Gagal Di Hapus']);
        }
    }

    public function editgiro(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $salesman = DB::table('karyawan')->where('kode_cabang', $kode_cabang)->get();
        $giro = DB::table('giro')->where('id_giro', $request->id_giro)->first();
        $sisabayar = $request->sisabayar;
        return view('pembayaran.editgiro', compact('giro', 'salesman', 'sisabayar'));
    }

    public function updategiro($id_giro, Request $request)
    {
        $tgl_giro = $request->tgl_giro_edit;
        $no_giro = $request->no_giro_edit;
        $namabank = $request->namabank_giro_edit;
        $tglcair = $request->tglcair_edit;
        $jumlah = str_replace(".", "", $request->jumlah_giro_edit);
        $id_karyawan = $request->id_karyawan;
        $simpan = DB::table('giro')
            ->where('id_giro', $id_giro)
            ->update([
                'tgl_giro' => $tgl_giro,
                'no_giro' => $no_giro,
                'namabank' => $namabank,
                'tglcair' => $tglcair,
                'jumlah' => $jumlah,
                'id_karyawan' => $id_karyawan,
            ]);

        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Giro Berhasil Di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Giro Gagal Di Update']);
        }
    }


    public function storetransfer(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $tgl_transfer = $request->tgl_transfer;
        $namabank = $request->namabank_transfer;
        $tglcair = $request->tglcair_transfer;
        $jumlah = str_replace(".", "", $request->jumlah_transfer);
        $id_karyawan = $request->id_karyawan;
        $kode_pelanggan = $request->kode_pelanggan;
        $ket = $request->ket;
        $tgl          = explode("-", $tgl_transfer);
        $tanggal      = $tgl[2];
        $bulan        = $tgl[1];
        $tahun        = substr($tgl[0], 2, 2);
        $kode_transfer =  $kode_pelanggan . $tanggal . $bulan . $tahun . $ket;
        $simpan = DB::table('transfer')
            ->insert([
                'no_fak_penj' => $no_fak_penj,
                'tgl_transfer' => $tgl_transfer,
                'namabank' => $namabank,
                'tglcair' => $tglcair,
                'jumlah' => $jumlah,
                'id_karyawan' => $id_karyawan,
                'kode_transfer' => $kode_transfer,
                'status' => 0
            ]);

        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Transfer Berhasil Di Simpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Transfer Gagal Di Hapus']);
        }
    }

    public function deletetransfer($id_transfer)
    {
        $id_transfer = Crypt::decrypt($id_transfer);
        $hapus = DB::table('transfer')
            ->where('id_transfer', $id_transfer)
            ->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Transfer Berhasil Di Hapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Transfer Gagal Di Hapus']);
        }
    }

    public function edittransfer(Request $request)
    {
        $kode_pelanggan = $request->kode_pelanggan;
        $kode_cabang = $request->kode_cabang;
        $salesman = DB::table('karyawan')->where('kode_cabang', $kode_cabang)->get();
        $transfer = DB::table('transfer')->where('id_transfer', $request->id_transfer)->first();
        $sisabayar = $request->sisabayar;
        return view('pembayaran.edittransfer', compact('transfer', 'salesman', 'sisabayar', 'kode_pelanggan'));
    }

    public function updatetransfer($id_transfer, Request $request)
    {
        $tgl_transfer = $request->tgl_transfer_edit;
        $namabank = $request->namabank_transfer_edit;
        $tglcair = $request->tglcair_transfer_edit;
        $jumlah = str_replace(".", "", $request->jumlah_transfer_edit);
        $id_karyawan = $request->id_karyawan;
        $kode_pelanggan = $request->kode_pelanggan_edit;
        $ket = $request->ket_edit;
        $tgl          = explode("-", $tgl_transfer);
        $tanggal      = $tgl[2];
        $bulan        = $tgl[1];
        $tahun        = substr($tgl[0], 2, 2);
        $kode_transfer =  $kode_pelanggan . $tanggal . $bulan . $tahun . $ket;
        $simpan = DB::table('transfer')
            ->where('id_transfer', $id_transfer)
            ->update([
                'tgl_transfer' => $tgl_transfer,
                'namabank' => $namabank,
                'tglcair' => $tglcair,
                'jumlah' => $jumlah,
                'id_karyawan' => $id_karyawan,
                'kode_transfer' => $kode_transfer,
                'ket' => $ket
            ]);

        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Transfer Berhasil Di Di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Transfer Gagal Di Update']);
        }
    }
}
