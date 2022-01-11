<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboardadmin()
    {
        $cabang = DB::table('cabang')->get();
        $kode_cabang = Auth::user()->kode_cabang;
        $level = Auth::user()->level;
        $pengajuanterakhir = DB::table('pengajuan_limitkredit_v3')
            ->select(DB::raw('MAX(no_pengajuan) as no_pengajuan'))
            ->groupBy('kode_pelanggan')
            ->get();
        foreach ($pengajuanterakhir as $d) {
            $no_pengajuan[] = $d->no_pengajuan;
        }

        if ($level == "admin" || $level == "direktur") {
            $jmlpengajuan = DB::table('pengajuan_limitkredit_v3')
                ->join('pelanggan', 'pengajuan_limitkredit_v3.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
                ->whereIn('no_pengajuan', $no_pengajuan)
                ->whereNotNull('gm')
                ->whereNull('dirut')
                ->where('status', 0)
                ->count();
        } else if ($level == "kacab") {
            $jmlpengajuan = DB::table('pengajuan_limitkredit_v3')
                ->join('pelanggan', 'pengajuan_limitkredit_v3.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
                ->whereIn('no_pengajuan', $no_pengajuan)
                ->where('pelanggan.kode_cabang', $kode_cabang)
                ->whereNull('kacab')
                ->where('status', 0)
                ->count();
        } else if ($level == "manager marketing") {
            $jmlpengajuan = DB::table('pengajuan_limitkredit_v3')
                ->join('pelanggan', 'pengajuan_limitkredit_v3.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
                ->whereIn('no_pengajuan', $no_pengajuan)
                ->whereNotNull('kacab')
                ->whereNull('mm')
                ->where('status', 0)
                ->count();
        } else if ($level == "general manager") {
            $jmlpengajuan = DB::table('pengajuan_limitkredit_v3')
                ->join('pelanggan', 'pengajuan_limitkredit_v3.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
                ->whereIn('no_pengajuan', $no_pengajuan)
                ->whereNotNull('mm')
                ->whereNull('gm')
                ->where('status', 0)
                ->count();
        }
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('dashboard.administrator', compact('jmlpengajuan', 'bulan', 'cabang'));
    }
}
