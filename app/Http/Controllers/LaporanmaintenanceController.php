<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanmaintenanceController extends Controller
{
    public function rekapbahanbakar()
    {
        $kode_barang = ['GA-002', 'GA-007', 'GA-588'];
        $barang = DB::table('master_barang_pembelian')
            ->whereIn('kode_barang', $kode_barang)
            ->orderBy('kode_barang')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('maintenance.laporan.frm.lap_rekapbahabakar', compact('bulan', 'barang'));
    }

    public function cetak_rekapbahanbakar(Request $request)
    {
        $level = Auth::user()->level;
        $kode_barang = $request->kode_barang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;


        if (strlen($bulan) == 1) {
            $bulan = "0" . $bulan;
        } else {
            $bulan = $bulan;
        }

        $dari = $tahun . "-" . $bulan . "-01";

        $nextbulan = DB::table('pengeluaran_bb')
            ->whereRaw('MONTH(tgl_pengeluaran)=' . $bulan)
            ->whereRaw('YEAR(tgl_pengeluaran)=' . $tahun)
            ->orderBy('tgl_pengeluaran', 'desc')
            ->first();
        $saldoawal = DB::table('saldoawal_bahan_bakar')
            ->select('qty', 'harga')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('kode_barang', $kode_barang)
            ->first();
        $barang = DB::table('master_barang_pembelian')->where('kode_barang', $kode_barang)->first();
        $tglnextbulan = $nextbulan != null ? $nextbulan->tgl_pengeluaran : '';
        if (!empty($tglnextbulan)) {
            $sampai = $tglnextbulan;
        } else {
            $sampai = date("Y-m-t", strtotime($dari));
        }

        $tglakhirpenerimaan = date("Y-m-t", strtotime($dari));
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekap Bahan Bakar.xls");
        }
        if ($level == 'manager accounting' ||  $level == 'spv accounting' ||  $level == 'admin' || $level == "admin pusat") {

            return view('maintenance.laporan.cetak_rekapbahanbakar_harga', compact('kode_barang', 'saldoawal', 'barang', 'dari', 'sampai'));
        } else {
            return view('maintenance.laporan.cetak_rekapbahanbakar', compact('kode_barang', 'saldoawal', 'barang', 'dari', 'sampai'));
        }
    }
}
