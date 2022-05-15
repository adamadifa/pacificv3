<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class DashboardController extends Controller
{
    protected $cabang;
    public function __construct()
    {
        // Fetch the Site Settings object
        $this->middleware(function ($request, $next) {
            $this->cabang = Auth::user()->kode_cabang;
            return $next($request);
        });


        View::share('cabang', $this->cabang);
    }

    public function home()
    {
        if (
            Auth::user()->level == "admin"
            || Auth::user()->level == "manager marketing"
            || Auth::user()->level == "general manager"
            || Auth::user()->level == "direktur"
        ) {
            return $this->dashboardadmin();
        } else if (Auth::user()->level == "admin penjualan") {
            return $this->dashboardadminpenjualan();
        } else if (Auth::user()->level == "kepala penjualan") {
            return $this->dashboardkepalapenjualan();
        } else if (Auth::user()->level == "kepala admin") {
            return $this->dashboardkepalaadmin();
        } else if (Auth::user()->level == "manager accounting") {
            return $this->dashboardaccounting();
        } else if (Auth::user()->level == "staff keuangan") {
            return $this->dashboardstaffkeuangan();
        } else if (Auth::user()->level == "admin kas kecil") {
            return $this->dashboardadminkaskecil();
        } else if (Auth::user()->level == "kasir") {
            return $this->dashboardkasir();
        } else if (Auth::user()->level == "manager pembelian" || Auth::user()->level == "admin pembelian") {
            return $this->dashboardpembelian();
        }
    }
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

        if ($level == "direktur") {
            $jmlpengajuan = DB::table('pengajuan_limitkredit_v3')
                ->join('pelanggan', 'pengajuan_limitkredit_v3.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
                ->whereIn('no_pengajuan', $no_pengajuan)
                ->whereNotNull('gm')
                ->whereNull('dirut')
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
        } else if ($level == "admin") {
            $jmlpengajuan = DB::table('pengajuan_limitkredit_v3')
                ->join('pelanggan', 'pengajuan_limitkredit_v3.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
                ->whereIn('no_pengajuan', $no_pengajuan)
                ->where('status', 0)
                ->count();
        }
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('dashboard.administrator', compact('jmlpengajuan', 'bulan', 'cabang'));
    }

    public function dashboardaccounting()
    {
        $cabang = DB::table('cabang')->get();
        $hariini = date("Y-m-d");
        $tgl = explode("-", $hariini);
        $tahun = $tgl[0];
        $bulan = $tgl[1];

        if ($bulan == 1) {
            $tahun = $tahun - 1;
            $bln = 12;
        } else {
            $tahun = $tahun;
            $bln = $bulan - 1;
        }
        $tgllast = $tahun . "-" . $bln . "-01";
        $lastupdate = DB::table('cabang')
            ->selectRaw("cabang.kode_cabang,nama_cabang,penjualan,kasbesar,kaskecil,persediaan")
            ->leftJoin(
                DB::raw("(
                SELECT karyawan.kode_cabang,max(tgltransaksi) as penjualan
                FROM penjualan
                INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                WHERE tgltransaksi BETWEEN '$tgllast' AND '$hariini'
                GROUP BY karyawan.kode_cabang
                ) pj"),
                function ($join) {
                    $join->on('cabang.kode_cabang', '=', 'pj.kode_cabang');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT karyawan.kode_cabang,max(tglbayar) as kasbesar
                FROM historibayar
                INNER JOIN karyawan ON historibayar.id_karyawan = karyawan.id_karyawan
                WHERE tglbayar BETWEEN '$tgllast' AND '$hariini'
                GROUP BY karyawan.kode_cabang
                ) hb"),
                function ($join) {
                    $join->on('cabang.kode_cabang', '=', 'hb.kode_cabang');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT kode_cabang,max(tgl_kaskecil) as kaskecil
                FROM kaskecil_detail
                WHERE tgl_kaskecil BETWEEN '$tgllast' AND '$hariini'
                GROUP BY kode_cabang
                ) kk"),
                function ($join) {
                    $join->on('cabang.kode_cabang', '=', 'kk.kode_cabang');
                }
            )

            ->leftJoin(
                DB::raw("(
                SELECT kode_cabang,max(tgl_mutasi_gudang_cabang) as persediaan
                FROM mutasi_gudang_cabang
                WHERE tgl_mutasi_gudang_cabang BETWEEN '$tgllast' AND '$hariini'
                GROUP BY kode_cabang
                ) gudang"),
                function ($join) {
                    $join->on('cabang.kode_cabang', '=', 'gudang.kode_cabang');
                }
            )
            ->get();
        $pengajuanterakhir = DB::table('pengajuan_limitkredit_v3')
            ->select(DB::raw('MAX(no_pengajuan) as no_pengajuan'))
            ->groupBy('kode_pelanggan')
            ->get();
        foreach ($pengajuanterakhir as $d) {
            $no_pengajuan[] = $d->no_pengajuan;
        }
        $jmlpengajuan = DB::table('pengajuan_limitkredit_v3')
            ->join('pelanggan', 'pengajuan_limitkredit_v3.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->whereIn('no_pengajuan', $no_pengajuan)
            ->where('status', 0)
            ->count();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('dashboard.accounting', compact('bulan', 'cabang', 'lastupdate', 'jmlpengajuan'));
    }

    function dashboardadminpenjualan()
    {
        $cabang = $this->cabang;
        $dari = date("Y") . "-" . date("m") . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $rekappenjualan = DB::table('penjualan')
            ->selectRaw("karyawan.kode_cabang AS kode_cabang,
        ( ifnull( SUM( penjualan.subtotal ), 0 ) ) AS totalbruto,
        ifnull(SUM(IF(penjualan.`status`=1,penjualan.subtotal,0)),0) as totalbrutopending,
        ifnull(totalretur,0) as totalretur,
        ifnull(totalreturpending,0) as totalreturpending,

        ifnull( SUM( penjualan.penyharga ), 0 ) AS totalpenyharga,
        ifnull(SUM(IF(penjualan.`status`=1,penjualan.penyharga,0)),0) as totalpenyhargapending,

        ifnull( SUM( penjualan.potongan ), 0 ) AS totalpotongan,
        ifnull(SUM(IF(penjualan.`status`=1,penjualan.potongan,0)),0) as totalpotonganpending,

        ifnull( SUM( penjualan.potistimewa ), 0 ) AS totalpotistimewa,
        ifnull(SUM(IF(penjualan.`status`=1,penjualan.potistimewa,0)),0) as totalpotistimewapending")
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin(
                DB::raw("(
                SELECT karyawan.kode_cabang, SUM(retur.total )AS totalretur ,
                SUM(IF(penjualan.`status`=1,retur.total,0)) as totalreturpending
                FROM retur
                INNER JOIN penjualan ON retur.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                WHERE tglretur BETWEEN '$dari' AND '$sampai' AND karyawan.kode_cabang ='$cabang' GROUP BY karyawan.kode_cabang
            ) retur"),
                function ($join) {
                    $join->on('karyawan.kode_cabang', '=', 'retur.kode_cabang');
                }
            )
            ->whereBetween('tgltransaksi', [$dari, $sampai])
            ->where('karyawan.kode_cabang', $this->cabang)
            ->groupByRaw('karyawan.kode_cabang,totalretur,totalreturpending')
            ->first();
        return view('dashboard.adminpenjualan', compact('rekappenjualan'));
    }

    function dashboardkepalaadmin()
    {
        $dari = date("Y") . "-" . date("m") . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $cabang = $this->cabang;
        $rekappenjualan = DB::table('penjualan')
            ->selectRaw("karyawan.kode_cabang AS kode_cabang,
        ( ifnull( SUM( penjualan.subtotal ), 0 ) ) AS totalbruto,
        ifnull(SUM(IF(penjualan.`status`=1,penjualan.subtotal,0)),0) as totalbrutopending,
        ifnull(totalretur,0) as totalretur,
        ifnull(totalreturpending,0) as totalreturpending,

        ifnull( SUM( penjualan.penyharga ), 0 ) AS totalpenyharga,
        ifnull(SUM(IF(penjualan.`status`=1,penjualan.penyharga,0)),0) as totalpenyhargapending,

        ifnull( SUM( penjualan.potongan ), 0 ) AS totalpotongan,
        ifnull(SUM(IF(penjualan.`status`=1,penjualan.potongan,0)),0) as totalpotonganpending,

        ifnull( SUM( penjualan.potistimewa ), 0 ) AS totalpotistimewa,
        ifnull(SUM(IF(penjualan.`status`=1,penjualan.potistimewa,0)),0) as totalpotistimewapending")
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin(
                DB::raw("(
                SELECT karyawan.kode_cabang, SUM(retur.total )AS totalretur ,
                SUM(IF(penjualan.`status`=1,retur.total,0)) as totalreturpending
                FROM retur
                INNER JOIN penjualan ON retur.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                WHERE tglretur BETWEEN '$dari' AND '$sampai' AND karyawan.kode_cabang ='$cabang' GROUP BY karyawan.kode_cabang
            ) retur"),
                function ($join) {
                    $join->on('karyawan.kode_cabang', '=', 'retur.kode_cabang');
                }
            )
            ->whereBetween('tgltransaksi', [$dari, $sampai])
            ->where('karyawan.kode_cabang', $this->cabang)
            ->groupByRaw('karyawan.kode_cabang,totalretur,totalreturpending')
            ->first();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        return view('dashboard.kepalaadmin', compact('rekappenjualan', 'bulan', 'cabang'));
    }

    function dashboardkepalapenjualan()
    {
        $kode_cabang = Auth::user()->kode_cabang;
        $pengajuanterakhir = DB::table('pengajuan_limitkredit_v3')
            ->select(DB::raw('MAX(no_pengajuan) as no_pengajuan'))
            ->groupBy('kode_pelanggan')
            ->get();

        foreach ($pengajuanterakhir as $d) {
            $no_pengajuan[] = $d->no_pengajuan;
        }
        $jmlpengajuan = DB::table('pengajuan_limitkredit_v3')
            ->join('pelanggan', 'pengajuan_limitkredit_v3.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->whereIn('no_pengajuan', $no_pengajuan)
            ->where('pelanggan.kode_cabang', $kode_cabang)
            ->whereNull('kacab')
            ->where('status', 0)
            ->count();

        $dari = date("Y") . "-" . date("m") . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $cabang = $this->cabang;
        $rekappenjualan = DB::table('penjualan')
            ->selectRaw("karyawan.kode_cabang AS kode_cabang,
        ( ifnull( SUM( penjualan.subtotal ), 0 ) ) AS totalbruto,
        ifnull(SUM(IF(penjualan.`status`=1,penjualan.subtotal,0)),0) as totalbrutopending,
        ifnull(totalretur,0) as totalretur,
        ifnull(totalreturpending,0) as totalreturpending,

        ifnull( SUM( penjualan.penyharga ), 0 ) AS totalpenyharga,
        ifnull(SUM(IF(penjualan.`status`=1,penjualan.penyharga,0)),0) as totalpenyhargapending,

        ifnull( SUM( penjualan.potongan ), 0 ) AS totalpotongan,
        ifnull(SUM(IF(penjualan.`status`=1,penjualan.potongan,0)),0) as totalpotonganpending,

        ifnull( SUM( penjualan.potistimewa ), 0 ) AS totalpotistimewa,
        ifnull(SUM(IF(penjualan.`status`=1,penjualan.potistimewa,0)),0) as totalpotistimewapending")
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin(
                DB::raw("(
                SELECT karyawan.kode_cabang, SUM(retur.total )AS totalretur ,
                SUM(IF(penjualan.`status`=1,retur.total,0)) as totalreturpending
                FROM retur
                INNER JOIN penjualan ON retur.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                WHERE tglretur BETWEEN '$dari' AND '$sampai' AND karyawan.kode_cabang ='$cabang' GROUP BY karyawan.kode_cabang
            ) retur"),
                function ($join) {
                    $join->on('karyawan.kode_cabang', '=', 'retur.kode_cabang');
                }
            )
            ->whereBetween('tgltransaksi', [$dari, $sampai])
            ->where('karyawan.kode_cabang', $this->cabang)
            ->groupByRaw('karyawan.kode_cabang,totalretur,totalreturpending')
            ->first();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        return view('dashboard.kepalapenjualan', compact('jmlpengajuan', 'bulan', 'cabang', 'rekappenjualan'));
    }

    public function dashboardstaffkeuangan()
    {
        return view('dashboard.staffkeuangan');
    }

    public function dashboardadminkaskecil()
    {
        return view('dashboard.adminkaskecil');
    }

    public function dashboardkasir()
    {
        return view('dashboard.kasir');
    }

    public function dashboardpembelian()
    {
        return view('dashboard.pembelian');
    }
}