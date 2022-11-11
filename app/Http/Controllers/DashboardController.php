<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Kendaraan;
use App\Models\Limitkredit;
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
            || Auth::user()->level == "rsm"
            || Auth::user()->level == "general manager"
            || Auth::user()->level == "direktur"
        ) {
            return $this->dashboardadmin();
        } else if (Auth::user()->level == "admin penjualan") {
            return $this->dashboardadminpenjualan();
        } else if (Auth::user()->level == "kepala penjualan") {
            return $this->dashboardkepalapenjualan();
        } else if (Auth::user()->level == "kepala admin" || Auth::user()->level == "admin pusat") {
            return $this->dashboardkepalaadmin();
        } else if (Auth::user()->level == "manager accounting" || Auth::user()->level == "audit" || Auth::user()->level == "spv accounting" || Auth::user()->level == "admin pajak 2") {
            return $this->dashboardaccounting();
        } else if (Auth::user()->level == "staff keuangan") {
            return $this->dashboardstaffkeuangan();
        } else if (Auth::user()->level == "admin kas kecil") {
            return $this->dashboardadminkaskecil();
        } else if (Auth::user()->level == "kasir") {
            return $this->dashboardkasir();
        } else if (Auth::user()->level == "manager pembelian" || Auth::user()->level == "admin pembelian") {
            return $this->dashboardpembelian();
        } else if (Auth::user()->level == "kepala gudang" || Auth::user()->level == "admin gudang pusat" || Auth::user()->level == "emf" || Auth::user()->level == "admin produksi") {
            return $this->dashboardgudang();
        } else if (Auth::user()->level == "admin gudang cabang" || Auth::user()->level == "admin gudang cabang dan marketing" || Auth::user()->level == "admin persediaan dan kas kecil") {
            return $this->dashboardgudangcabang();
        } else {
            return $this->dashboardadminkaskecil();
        }
    }
    public function dashboardadmin()
    {

        $wilayah_barat = array('BDG', 'TSM', 'GRT', 'PWK', 'BGR', 'SKB');
        $wilayah_timur = array('TGL', 'PWT', 'SBY', 'KLT', 'SMR');
        $cbg = new Cabang();
        $cabang = $cbg->getCabang(Auth::user()->kode_cabang);
        $kode_cabang = Auth::user()->kode_cabang;
        $id_user = Auth::user()->id;
        $level = Auth::user()->level;
        $no_pengajuan[] = "";
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
                ->whereNotNull('mm')
                ->whereNull('dirut')
                ->where('status', 0)
                ->count();
        } else if ($level == "rsm") {
            $query = Limitkredit::query();
            $query->join('pelanggan', 'pengajuan_limitkredit_v3.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
            $query->whereIn('no_pengajuan', $no_pengajuan);
            $query->whereNotNull('kacab');
            $query->whereNull('rsm');
            $query->where('status', 0);
            if ($id_user == 82) {
                $query->whereIn('pelanggan.kode_cabang', $wilayah_barat);
            } else if ($id_user == 97) {
                $query->whereIn('pelanggan.kode_cabang', $wilayah_timur);
            }
            $jmlpengajuan = $query->count();

            //dd($jmlpengajuan);
        } else if ($level == "manager marketing") {
            $jmlpengajuan = DB::table('pengajuan_limitkredit_v3')
                ->join('pelanggan', 'pengajuan_limitkredit_v3.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
                ->whereIn('no_pengajuan', $no_pengajuan)
                ->whereNotNull('rsm')
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

    public function dashboardgudang()
    {

        return view('dashboard.gudang');
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
        $no_pengajuan[] = "";
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
        return view('dashboard.kepalaadmin', compact('rekappenjualan', 'bulan', 'cabang', 'dari', 'sampai'));
    }


    function dashboardgudangcabang()
    {
        $dari = date("Y") . "-" . date("m") . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $cabang = $this->cabang;
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        return view('dashboard.gudangcabang', compact('bulan', 'cabang', 'dari', 'sampai'));
    }

    function dashboardkepalapenjualan()
    {
        $kode_cabang = Auth::user()->kode_cabang;
        $pengajuanterakhir = DB::table('pengajuan_limitkredit_v3')
            ->select(DB::raw('MAX(no_pengajuan) as no_pengajuan'))
            ->groupBy('kode_pelanggan')
            ->get();

        $no_pengajuan[] = "";
        foreach ($pengajuanterakhir as $d) {
            $no_pengajuan[] = $d->no_pengajuan;
        }


        $qpengajuan = Limitkredit::query();
        $qpengajuan->join('pelanggan', 'pengajuan_limitkredit_v3.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $qpengajuan->whereIn('no_pengajuan', $no_pengajuan);
        $qpengajuan->where('pelanggan.kode_cabang', $kode_cabang);
        $qpengajuan->whereNull('kacab');
        $qpengajuan->where('status', 0);
        if (Auth::user()->id == 7) {
            $qpengajuan->orwhere('pelanggan.kode_cabang', 'GRT');
            $qpengajuan->whereIn('no_pengajuan', $no_pengajuan);
            $qpengajuan->whereNull('kacab');
            $qpengajuan->where('status', 0);
        }
        $jmlpengajuan = $qpengajuan->count();

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
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        return view('dashboard.pembelian', compact('cabang'));
    }

    public function dashboardga()
    {
        $bulanini = date("m");
        $tahunini = date("Y");
        $bulandepan = date("m") + 1 > 12 ? (date("m") + 1) - 12 : date("m") + 1;
        $tahun2 = $bulandepan > 12 ? $tahunini + 1 : $tahunini;
        $duabulan = date("m") + 2 > 12 ? (date("m") + 2) - 12 : date("m") + 2;
        $tahun3 = $duabulan > 12 ? $tahunini + 1 : $tahunini;

        $qkir_sudahlewat = DB::table('kendaraan')->whereRaw('MONTH(jatuhtempo_kir)<' . $bulanini)
            ->whereRaw('YEAR(jatuhtempo_kir)<=' . $tahunini);

        $qkir_bulanini = DB::table('kendaraan')->whereRaw('MONTH(jatuhtempo_kir)=' . $bulanini)
            ->whereRaw('YEAR(jatuhtempo_kir)=' . $tahunini);
        $qkir_bulandepan = DB::table('kendaraan')->whereRaw('MONTH(jatuhtempo_kir)=' . $bulandepan)
            ->whereRaw('YEAR(jatuhtempo_kir)=' . $tahun2);
        $qkir_duabulan = DB::table('kendaraan')->whereRaw('MONTH(jatuhtempo_kir)=' . $duabulan)
            ->whereRaw('YEAR(jatuhtempo_kir)=' . $tahun3);
        $kir_sudahlewat = $qkir_sudahlewat->get();
        $kir_bulanini = $qkir_bulanini->get();
        $kir_bulandepan = $qkir_bulandepan->get();
        $kir_duabulan = $qkir_duabulan->get();
        $jml_kir_sudahlewat = $qkir_sudahlewat->count();
        $jml_kir_bulanini = $qkir_bulanini->count();
        $jml_kir_bulandepan = $qkir_bulandepan->count();
        $jml_kir_duabulan = $qkir_duabulan->count();


        $qpajak_satutahun_sudahlewat = DB::table('kendaraan')->whereRaw('MONTH(jatuhtempo_pajak_satutahun)<' . $bulanini)
            ->whereRaw('YEAR(jatuhtempo_pajak_satutahun)<=' . $tahunini);
        $qpajak_satutahun_bulanini = DB::table('kendaraan')->whereRaw('MONTH(jatuhtempo_pajak_satutahun)=' . $bulanini)
            ->whereRaw('YEAR(jatuhtempo_pajak_satutahun)=' . $tahunini);
        $qpajak_satutahun_bulandepan = DB::table('kendaraan')->whereRaw('MONTH(jatuhtempo_pajak_satutahun)=' . $bulandepan)
            ->whereRaw('YEAR(jatuhtempo_pajak_satutahun)=' . $tahun2);
        $qpajak_satutahun_duabulan = DB::table('kendaraan')->whereRaw('MONTH(jatuhtempo_pajak_satutahun)=' . $duabulan)
            ->whereRaw('YEAR(jatuhtempo_pajak_satutahun)=' . $tahun3);
        $pajak_satutahun_sudahlewat = $qpajak_satutahun_sudahlewat->get();
        $pajak_satutahun_bulanini = $qpajak_satutahun_bulanini->get();
        $pajak_satutahun_bulandepan = $qpajak_satutahun_bulandepan->get();
        $pajak_satutahun_duabulan = $qpajak_satutahun_duabulan->get();
        $jml_pajak_satutahun_sudahlewat = $qpajak_satutahun_sudahlewat->count();
        $jml_pajak_satutahun_bulanini = $qpajak_satutahun_bulanini->count();
        $jml_pajak_satutahun_bulandepan = $qpajak_satutahun_bulandepan->count();
        $jml_pajak_satutahun_duabulan = $qpajak_satutahun_duabulan->count();

        $qpajak_limatahun_sudahlewat = DB::table('kendaraan')->whereRaw('MONTH(jatuhtempo_pajak_limatahun)<' . $bulanini)
            ->whereRaw('YEAR(jatuhtempo_pajak_limatahun)<=' . $tahunini);
        $qpajak_limatahun_bulanini = DB::table('kendaraan')->whereRaw('MONTH(jatuhtempo_pajak_limatahun)=' . $bulanini)
            ->whereRaw('YEAR(jatuhtempo_pajak_limatahun)=' . $tahunini);
        $qpajak_limatahun_bulandepan = DB::table('kendaraan')->whereRaw('MONTH(jatuhtempo_pajak_limatahun)=' . $bulandepan)
            ->whereRaw('YEAR(jatuhtempo_pajak_limatahun)=' . $tahun2);
        $qpajak_limatahun_duabulan = DB::table('kendaraan')->whereRaw('MONTH(jatuhtempo_pajak_limatahun)=' . $duabulan)
            ->whereRaw('YEAR(jatuhtempo_pajak_limatahun)=' . $tahun3);
        $pajak_limatahun_sudahlewat = $qpajak_limatahun_sudahlewat->get();
        $pajak_limatahun_bulanini = $qpajak_limatahun_bulanini->get();
        $pajak_limatahun_bulandepan = $qpajak_limatahun_bulandepan->get();
        $pajak_limatahun_duabulan = $qpajak_limatahun_duabulan->get();
        $jml_pajak_limatahun_sudahlewat = $qpajak_limatahun_sudahlewat->count();
        $jml_pajak_limatahun_bulanini = $qpajak_limatahun_bulanini->count();
        $jml_pajak_limatahun_bulandepan = $qpajak_limatahun_bulandepan->count();
        $jml_pajak_limatahun_duabulan = $qpajak_limatahun_duabulan->count();

        $jmlkendaraan = Kendaraan::count();
        $rekapkendaraancabang = DB::table('kendaraan')
            ->selectRaw('kendaraan.kode_cabang,nama_cabang,COUNT(no_polisi) as jmlkendaraan')
            ->join('cabang', 'kendaraan.kode_cabang', '=', 'cabang.kode_cabang')
            ->groupByRaw('kendaraan.kode_cabang,nama_cabang')
            ->get();
        return view('dashboard.ga', compact('kir_bulanini', 'jml_kir_bulanini', 'kir_bulandepan', 'jml_kir_bulandepan', 'kir_duabulan', 'jml_kir_duabulan', 'pajak_satutahun_bulanini', 'jml_pajak_satutahun_bulanini', 'pajak_satutahun_bulandepan', 'jml_pajak_satutahun_bulandepan', 'pajak_satutahun_duabulan', 'jml_pajak_satutahun_duabulan', 'pajak_limatahun_bulanini', 'jml_pajak_limatahun_bulanini', 'pajak_limatahun_bulandepan', 'jml_pajak_limatahun_bulandepan', 'pajak_limatahun_duabulan', 'jml_pajak_limatahun_duabulan', 'jml_kir_sudahlewat', 'kir_sudahlewat', 'pajak_satutahun_sudahlewat', 'jml_pajak_satutahun_sudahlewat', 'pajak_limatahun_sudahlewat', 'jml_pajak_limatahun_sudahlewat', 'jmlkendaraan', 'rekapkendaraancabang'));
    }
}
