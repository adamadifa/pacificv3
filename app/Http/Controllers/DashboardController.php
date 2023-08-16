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
        } else if (Auth::user()->level == "manager accounting" || Auth::user()->level == "audit" || Auth::user()->level == "spv accounting" || Auth::user()->level == "admin pajak 2" || Auth::user()->level == "manager audit") {
            return $this->dashboardaccounting();
        } else if (Auth::user()->level == "staff keuangan") {
            return $this->dashboardstaffkeuangan();
        } else if (Auth::user()->level == "admin kas kecil") {
            return $this->dashboardadminkaskecil();
        } else if (Auth::user()->level == "kasir") {
            return $this->dashboardkasir();
        } else if (Auth::user()->level == "manager pembelian" || Auth::user()->level == "admin pembelian") {
            return $this->dashboardpembelian();
        } else if (Auth::user()->level == "kepala gudang" || Auth::user()->level == "admin gudang pusat" || Auth::user()->level == "emf" || Auth::user()->level == "admin produksi" || Auth::user()->level == "manager produksi" || Auth::user()->level == "spv produksi" || Auth::user()->level == "spv gudang pusat") {
            return $this->dashboardgudang();
        } else if (Auth::user()->level == "admin gudang cabang" || Auth::user()->level == "admin gudang cabang dan marketing" || Auth::user()->level == "admin persediaan dan kas kecil" || Auth::user()->level == "admin persediaan dan kasir") {
            return $this->dashboardgudangcabang();
        } else if (Auth::user()->level == "salesman") {
            return $this->dashboardsalesman();
        } else if (Auth::user()->level == "manager hrd") {
            return $this->dashboardhrd();
        } else {
            return $this->dashboardadminkaskecil();
        }
    }
    public function dashboardadmin()
    {

        $wilayah_barat = array('BDG', 'TSM', 'GRT', 'PWK', 'BGR', 'SKB', 'BTN');
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
            //dd($id_user);
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
        // if (Auth::user()->id == 7) {
        //     $qpengajuan->orwhere('pelanggan.kode_cabang', 'GRT');
        //     $qpengajuan->whereIn('no_pengajuan', $no_pengajuan);
        //     $qpengajuan->whereNull('kacab');
        //     $qpengajuan->where('status', 0);
        // }
        $qpengajuan->where('jumlah', '>', 2000000);

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

    public function dashboardhrd()
    {
        $karyawan = DB::table('master_karyawan')
            ->selectRaw('COUNT(nik) as jmlkaryawan,
            SUM(IF(status_karyawan="T",1,0)) as jmlkaryawantetap,
            SUM(IF(status_karyawan="K",1,0)) as jmlkaryawankontrak,
            SUM(IF(status_karyawan="O",1,0)) as jmlkaryawanos,
            SUM(IF(jenis_kelamin="1",1,0)) as jml_lakilaki,
            SUM(IF(jenis_kelamin="2",1,0)) as jml_perempuan,
            SUM(IF(id_perusahaan="MP",1,0)) as jml_mp,
            SUM(IF(id_perusahaan="PCF",1,0)) as jml_pcf')
            ->where('status_aktif', 1)
            ->first();

        $rekapdepartemen = DB::table('master_karyawan')
            ->selectRaw('master_karyawan.kode_dept,nama_dept,COUNT(nik) as jmlkaryawan')
            ->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->groupByRaw('master_karyawan.kode_dept,nama_dept')
            ->where('status_aktif', 1)
            ->get();

        $rekapkantor = DB::table('master_karyawan')
            ->selectRaw('master_karyawan.id_kantor,nama_cabang,COUNT(nik) as jmlkaryawan')
            ->join('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang')
            ->groupByRaw('master_karyawan.id_kantor,nama_cabang')
            ->where('status_aktif', 1)
            ->get();

        $hariini = date("Y-m-d");
        $bulanini = date("m");
        $tahunini = date("Y");
        $bulandepan = date("m") + 1 > 12 ? (date("m") + 1) - 12 : date("m") + 1;
        $tahun2 = $bulandepan > 12 ? $tahunini + 1 : $tahunini;
        $duabulan = date("m") + 2 > 12 ? (date("m") + 2) - 12 : date("m") + 2;
        $tahun3 = $duabulan > 12 ? $tahunini + 1 : $tahunini;
        $qkontrak_lewat = DB::table('hrd_kontrak')
            ->selectRaw('hrd_kontrak.nik, nama_karyawan, IFNULL(jb.nama_jabatan,jb2.nama_jabatan) as nama_jabatan, IFNULL(hrd_kontrak.kode_dept,master_karyawan.kode_dept) as kode_dept, sampai, IFNULL(hrd_kontrak.id_perusahaan,master_karyawan.id_perusahaan) as id_perusahaan, IFNULL(hrd_kontrak.id_kantor,master_karyawan.id_kantor) as id_kantor')
            ->join('master_karyawan', 'hrd_kontrak.nik', '=', 'master_karyawan.nik')
            ->leftjoin('hrd_jabatan as jb', 'hrd_kontrak.id_jabatan', '=', 'jb.id')
            ->leftjoin('hrd_jabatan as jb2', 'master_karyawan.id_jabatan', '=', 'jb2.id')
            ->where('sampai', '<', $hariini)
            ->where('status_kontrak', 1)
            ->where('status_karyawan', 'K')
            ->where('status_aktif', 1)
            ->orderBy('sampai');

        $qkontrak_bulanini = DB::table('hrd_kontrak')
            ->selectRaw('hrd_kontrak.nik, nama_karyawan, IFNULL(jb.nama_jabatan,jb2.nama_jabatan) as nama_jabatan, IFNULL(hrd_kontrak.kode_dept,master_karyawan.kode_dept) as kode_dept, sampai, IFNULL(hrd_kontrak.id_perusahaan,master_karyawan.id_perusahaan) as id_perusahaan, IFNULL(hrd_kontrak.id_kantor,master_karyawan.id_kantor) as id_kantor')
            ->join('master_karyawan', 'hrd_kontrak.nik', '=', 'master_karyawan.nik')
            ->leftjoin('hrd_jabatan as jb', 'hrd_kontrak.id_jabatan', '=', 'jb.id')
            ->leftjoin('hrd_jabatan as jb2', 'master_karyawan.id_jabatan', '=', 'jb2.id')
            ->whereRaw('MONTH(sampai)=' . $bulanini)
            ->whereRaw('YEAR(sampai)=' . $tahunini)
            ->where('status_kontrak', 1)
            ->where('status_karyawan', 'K')
            ->where('status_aktif', 1)
            ->orderBy('sampai');


        $qkontrak_bulandepan = DB::table('hrd_kontrak')
            ->selectRaw('hrd_kontrak.nik, nama_karyawan, IFNULL(jb.nama_jabatan,jb2.nama_jabatan) as nama_jabatan, IFNULL(hrd_kontrak.kode_dept,master_karyawan.kode_dept) as kode_dept, sampai, IFNULL(hrd_kontrak.id_perusahaan,master_karyawan.id_perusahaan) as id_perusahaan, IFNULL(hrd_kontrak.id_kantor,master_karyawan.id_kantor) as id_kantor')
            ->join('master_karyawan', 'hrd_kontrak.nik', '=', 'master_karyawan.nik')
            ->leftjoin('hrd_jabatan as jb', 'hrd_kontrak.id_jabatan', '=', 'jb.id')
            ->leftjoin('hrd_jabatan as jb2', 'master_karyawan.id_jabatan', '=', 'jb2.id')
            ->whereRaw('MONTH(sampai)=' . $bulandepan)
            ->whereRaw('YEAR(sampai)=' . $tahun2)
            ->where('status_kontrak', 1)
            ->where('status_karyawan', 'K')
            ->where('status_aktif', 1)
            ->orderBy('sampai');


        $qkontrak_duabulan = DB::table('hrd_kontrak')
            ->selectRaw('hrd_kontrak.nik, nama_karyawan, IFNULL(jb.nama_jabatan,jb2.nama_jabatan) as nama_jabatan, IFNULL(hrd_kontrak.kode_dept,master_karyawan.kode_dept) as kode_dept, sampai, IFNULL(hrd_kontrak.id_perusahaan,master_karyawan.id_perusahaan) as id_perusahaan, IFNULL(hrd_kontrak.id_kantor,master_karyawan.id_kantor) as id_kantor')
            ->join('master_karyawan', 'hrd_kontrak.nik', '=', 'master_karyawan.nik')
            ->leftjoin('hrd_jabatan as jb', 'hrd_kontrak.id_jabatan', '=', 'jb.id')
            ->leftjoin('hrd_jabatan as jb2', 'master_karyawan.id_jabatan', '=', 'jb2.id')
            ->whereRaw('MONTH(sampai)=' . $duabulan)
            ->whereRaw('YEAR(sampai)=' . $tahun3)
            ->where('status_kontrak', 1)
            ->where('status_karyawan', 'K')
            ->where('status_aktif', 1)
            ->orderBy('sampai');


        $kontrak_lewat = $qkontrak_lewat->get();
        $jml_kontrak_lewat = $qkontrak_lewat->count();

        $kontrak_bulanini = $qkontrak_bulanini->get();
        $jml_kontrak_bulanini = $qkontrak_bulanini->count();

        $kontrak_bulandepan = $qkontrak_bulandepan->get();
        $jml_kontrak_bulandepan = $qkontrak_bulandepan->count();

        $kontrak_duabulan = $qkontrak_duabulan->get();
        $jml_kontrak_duabulan = $qkontrak_duabulan->count();


        return view('dashboard.hrd', compact('karyawan', 'kontrak_lewat', 'jml_kontrak_lewat', 'kontrak_bulanini', 'jml_kontrak_bulanini', 'hariini', 'kontrak_bulandepan', 'jml_kontrak_bulandepan', 'kontrak_duabulan', 'jml_kontrak_duabulan', 'rekapdepartemen', 'rekapkantor'));
    }

    public function dashboardsalesman()
    {
        $hariini = date("Y-m-d");
        $tahunini = date("Y");
        $tahunlalu = $tahunini - 1;
        $kode_cabang = Auth::user()->kode_cabang;
        $akhirtanggal = $hariini;
        $id_karyawan = Auth::user()->id_salesman;
        $jmlpelanggan = DB::table('pelanggan')->where('id_sales', $id_karyawan)->where('status_pelanggan', 1)->count();
        $jmlpelangganhariini = DB::table('pelanggan')->where('id_sales', $id_karyawan)->where('status_pelanggan', 1)
            ->where('time_stamps', $hariini)->count();
        $penjualanhariini = DB::table('penjualan')
            ->selectRaw('SUM(total) as totalpenjualan')
            ->where('id_karyawan', $id_karyawan)
            ->where('tgltransaksi', $hariini)->first();



        $bayarhariini = DB::table('historibayar')
            ->selectRaw('SUM(bayar) as totalbayar')
            ->where('id_karyawan', $id_karyawan)
            ->where('tglbayar', $hariini)->first();
        $jmltransaksi = DB::table('penjualan')->where('id_karyawan', $id_karyawan)->where('tgltransaksi', $hariini)->count();

        // $piutang = DB::table('penjualan')
        //     ->selectRaw("salesbarunew,SUM((IFNULL(penjualan.total,0))-(IFNULL(retur.total,0))-IFNULL(jmlbayar,0))  as saldopiutang")
        //     ->leftJoin(
        //         DB::raw("(
        //             SELECT no_fak_penj,sum( historibayar.bayar ) AS jmlbayar
        //             FROM historibayar
        //             WHERE tglbayar <= '$akhirtanggal'
        //             GROUP BY no_fak_penj
        //         ) hblalu"),
        //         function ($join) {
        //             $join->on('penjualan.no_fak_penj', '=', 'hblalu.no_fak_penj');
        //         }
        //     )

        //     ->leftJoin(
        //         DB::raw("(
        //             SELECT retur.no_fak_penj AS no_fak_penj,
        //             SUM(total) AS total
        //             FROM
        //                 retur
        //             WHERE tglretur <= '$akhirtanggal'
        //             GROUP BY
        //                 retur.no_fak_penj
        //         ) retur"),
        //         function ($join) {
        //             $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
        //         }
        //     )

        //     ->leftJoin(
        //         DB::raw("(
        //             SELECT pj.no_fak_penj,
        //             IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
        //             IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
        //             FROM penjualan pj
        //             INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
        //             LEFT JOIN (
        //             SELECT MAX(id_move) as id_move,no_fak_penj,move_faktur.id_karyawan as salesbaru,karyawan.kode_cabang as cabangbaru
        //             FROM move_faktur
        //             INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
        //             WHERE tgl_move <= '$akhirtanggal'
        //             GROUP BY no_fak_penj,move_faktur.id_karyawan,karyawan.kode_cabang
        //             ) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
        //         ) pjmove"),
        //         function ($join) {
        //             $join->on('penjualan.no_fak_penj', '=', 'pjmove.no_fak_penj');
        //         }
        //     )

        //     ->where('cabangbarunew', $kode_cabang)
        //     ->where('penjualan.jenistransaksi', '!=', 'tunai')
        //     ->where('tgltransaksi', '<=', $akhirtanggal)
        //     ->whereRaw('(ifnull(penjualan.total,0) - (ifnull(retur.total,0))) != IFNULL(jmlbayar,0)')
        //     ->where('salesbarunew', $id_karyawan)
        //     ->groupBy('salesbarunew')
        //     ->first();

        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");


        $penjtahunini = DB::table('bulan')
            ->selectRaw('id,nama_bulan,IFNULL(totalpenjualan,0) as totalpenjualan, IFNULL(totalpenjualanlast,0) as totalpenjualanlast')
            ->leftJoin(
                DB::raw("(
                SELECT MONTH(tgltransaksi) as bulan, SUM(total) as totalpenjualan
                FROM penjualan
                WHERE YEAR(tgltransaksi) = '$tahunini' AND id_karyawan = '$id_karyawan'
                GROUP BY MONTH(tgltransaksi)
            ) penjualan"),
                function ($join) {
                    $join->on('penjualan.bulan', '=', 'bulan.id');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT MONTH(tgltransaksi) as bulan, SUM(total) as totalpenjualanlast
                FROM penjualan
                WHERE YEAR(tgltransaksi) = '$tahunlalu' AND id_karyawan = '$id_karyawan'
                GROUP BY MONTH(tgltransaksi)
            ) penjualanlast"),
                function ($join) {
                    $join->on('penjualanlast.bulan', '=', 'bulan.id');
                }
            )
            ->get();

        $bln = [];
        $totalpenjnow = [];
        $totalpenjlast = [];
        foreach ($penjtahunini as $d) {
            $bln[] = substr($d->nama_bulan, 0, 3);
            $totalpenjnow[] = $d->totalpenjualan;
            $totalpenjlast[] = $d->totalpenjualanlast;
        }




        return view('dashboard.salesman', compact('jmlpelanggan', 'jmlpelangganhariini', 'penjualanhariini', 'bayarhariini', 'jmltransaksi', 'bulan', 'bln', 'totalpenjnow', 'totalpenjlast'));
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

        $qkir_sudahlewat = DB::table('kendaraan')
            ->whereRaw('MONTH(jatuhtempo_kir)<' . $bulanini)
            ->whereRaw('YEAR(jatuhtempo_kir)<=' . $tahunini)
            ->where('status', 1);

        $qkir_bulanini = DB::table('kendaraan')
            ->whereRaw('MONTH(jatuhtempo_kir)=' . $bulanini)
            ->whereRaw('YEAR(jatuhtempo_kir)=' . $tahunini)
            ->where('status', 1);
        $qkir_bulandepan = DB::table('kendaraan')
            ->whereRaw('MONTH(jatuhtempo_kir)=' . $bulandepan)
            ->whereRaw('YEAR(jatuhtempo_kir)=' . $tahun2)
            ->where('status', 1);
        $qkir_duabulan = DB::table('kendaraan')
            ->whereRaw('MONTH(jatuhtempo_kir)=' . $duabulan)
            ->whereRaw('YEAR(jatuhtempo_kir)=' . $tahun3)
            ->where('status', 1);


        $kir_sudahlewat = $qkir_sudahlewat->get();
        $kir_bulanini = $qkir_bulanini->get();
        $kir_bulandepan = $qkir_bulandepan->get();
        $kir_duabulan = $qkir_duabulan->get();
        $jml_kir_sudahlewat = $qkir_sudahlewat->count();
        $jml_kir_bulanini = $qkir_bulanini->count();
        $jml_kir_bulandepan = $qkir_bulandepan->count();
        $jml_kir_duabulan = $qkir_duabulan->count();


        $qpajak_satutahun_sudahlewat = DB::table('kendaraan')
            ->whereRaw('MONTH(jatuhtempo_pajak_satutahun)<' . $bulanini)
            ->whereRaw('YEAR(jatuhtempo_pajak_satutahun)<=' . $tahunini)
            ->where('status', 1);
        $qpajak_satutahun_bulanini = DB::table('kendaraan')
            ->whereRaw('MONTH(jatuhtempo_pajak_satutahun)=' . $bulanini)
            ->whereRaw('YEAR(jatuhtempo_pajak_satutahun)=' . $tahunini)
            ->where('status', 1);
        $qpajak_satutahun_bulandepan = DB::table('kendaraan')
            ->whereRaw('MONTH(jatuhtempo_pajak_satutahun)=' . $bulandepan)
            ->whereRaw('YEAR(jatuhtempo_pajak_satutahun)=' . $tahun2)
            ->where('status', 1);
        $qpajak_satutahun_duabulan = DB::table('kendaraan')
            ->whereRaw('MONTH(jatuhtempo_pajak_satutahun)=' . $duabulan)
            ->whereRaw('YEAR(jatuhtempo_pajak_satutahun)=' . $tahun3)
            ->where('status', 1);
        $pajak_satutahun_sudahlewat = $qpajak_satutahun_sudahlewat->get();
        $pajak_satutahun_bulanini = $qpajak_satutahun_bulanini->get();
        $pajak_satutahun_bulandepan = $qpajak_satutahun_bulandepan->get();
        $pajak_satutahun_duabulan = $qpajak_satutahun_duabulan->get();
        $jml_pajak_satutahun_sudahlewat = $qpajak_satutahun_sudahlewat->count();
        $jml_pajak_satutahun_bulanini = $qpajak_satutahun_bulanini->count();
        $jml_pajak_satutahun_bulandepan = $qpajak_satutahun_bulandepan->count();
        $jml_pajak_satutahun_duabulan = $qpajak_satutahun_duabulan->count();

        $qpajak_limatahun_sudahlewat = DB::table('kendaraan')
            ->whereRaw('MONTH(jatuhtempo_pajak_limatahun)<' . $bulanini)
            ->whereRaw('YEAR(jatuhtempo_pajak_limatahun)<=' . $tahunini)
            ->where('status', 1);
        $qpajak_limatahun_bulanini = DB::table('kendaraan')
            ->whereRaw('MONTH(jatuhtempo_pajak_limatahun)=' . $bulanini)
            ->whereRaw('YEAR(jatuhtempo_pajak_limatahun)=' . $tahunini)
            ->where('status', 1);
        $qpajak_limatahun_bulandepan = DB::table('kendaraan')
            ->whereRaw('MONTH(jatuhtempo_pajak_limatahun)=' . $bulandepan)
            ->whereRaw('YEAR(jatuhtempo_pajak_limatahun)=' . $tahun2)
            ->where('status', 1);
        $qpajak_limatahun_duabulan = DB::table('kendaraan')
            ->whereRaw('MONTH(jatuhtempo_pajak_limatahun)=' . $duabulan)
            ->whereRaw('YEAR(jatuhtempo_pajak_limatahun)=' . $tahun3)
            ->where('status', 1);
        $pajak_limatahun_sudahlewat = $qpajak_limatahun_sudahlewat->get();
        $pajak_limatahun_bulanini = $qpajak_limatahun_bulanini->get();
        $pajak_limatahun_bulandepan = $qpajak_limatahun_bulandepan->get();
        $pajak_limatahun_duabulan = $qpajak_limatahun_duabulan->get();
        $jml_pajak_limatahun_sudahlewat = $qpajak_limatahun_sudahlewat->count();
        $jml_pajak_limatahun_bulanini = $qpajak_limatahun_bulanini->count();
        $jml_pajak_limatahun_bulandepan = $qpajak_limatahun_bulandepan->count();
        $jml_pajak_limatahun_duabulan = $qpajak_limatahun_duabulan->count();

        $jmlkendaraan = Kendaraan::where('status', 1)->count();
        $rekapkendaraancabang = DB::table('kendaraan')
            ->selectRaw('kendaraan.kode_cabang,nama_cabang,COUNT(no_polisi) as jmlkendaraan')
            ->join('cabang', 'kendaraan.kode_cabang', '=', 'cabang.kode_cabang')
            ->where('status', 1)
            ->groupByRaw('kendaraan.kode_cabang,nama_cabang')
            ->get();
        return view('dashboard.ga', compact('kir_bulanini', 'jml_kir_bulanini', 'kir_bulandepan', 'jml_kir_bulandepan', 'kir_duabulan', 'jml_kir_duabulan', 'pajak_satutahun_bulanini', 'jml_pajak_satutahun_bulanini', 'pajak_satutahun_bulandepan', 'jml_pajak_satutahun_bulandepan', 'pajak_satutahun_duabulan', 'jml_pajak_satutahun_duabulan', 'pajak_limatahun_bulanini', 'jml_pajak_limatahun_bulanini', 'pajak_limatahun_bulandepan', 'jml_pajak_limatahun_bulandepan', 'pajak_limatahun_duabulan', 'jml_pajak_limatahun_duabulan', 'jml_kir_sudahlewat', 'kir_sudahlewat', 'pajak_satutahun_sudahlewat', 'jml_pajak_satutahun_sudahlewat', 'pajak_limatahun_sudahlewat', 'jml_pajak_limatahun_sudahlewat', 'jmlkendaraan', 'rekapkendaraancabang'));
    }

    public function getkunjungan(Request $request)
    {
        $id_karyawan = Auth::user()->id;
        $tanggal = $request->tanggalkunjungan;
        $kunjungan = DB::table('checkin')
            ->selectRaw('checkin.kode_pelanggan,nama_pelanggan,alamat_pelanggan,checkin_time,checkout_time,jarak,foto')
            ->join('pelanggan', 'checkin.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')

            ->where('checkin.id_karyawan', $id_karyawan)
            ->where('tgl_checkin', $tanggal)
            ->orderBy('checkin_time', 'asc')
            ->get();

        return view('dashboard.getkunjungan', compact('kunjungan'));
    }


    public function getdpb(Request $request)
    {
        $id_karyawan = Auth::user()->id_salesman;

        $tanggal = $request->tgl_dpb;
        $dpb = DB::table('detail_dpb')
            ->selectRaw('detail_dpb.kode_produk,nama_barang,isipcsdus,SUM(jml_pengambilan) as jml_pengambilan,qtyjual')
            ->join('master_barang', 'detail_dpb.kode_produk', '=', 'master_barang.kode_produk')
            ->join('dpb', 'detail_dpb.no_dpb', '=', 'dpb.no_dpb')
            ->leftJoin(
                DB::raw("(
                SELECT barang.kode_produk,SUM(jumlah) as qtyjual
                FROM detailpenjualan
                INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                WHERE tgltransaksi = '$tanggal' AND penjualan.id_karyawan = '$id_karyawan'
                GROUP BY barang.kode_produk,isipcsdus
                ) dp"),
                function ($join) {
                    $join->on('detail_dpb.kode_produk', '=', 'dp.kode_produk');
                }
            )
            ->where('dpb.id_karyawan', $id_karyawan)
            ->where('tgl_pengambilan', $tanggal)
            ->groupByRaw('detail_dpb.kode_produk,nama_barang,isipcsdus,qtyjual')
            ->get();

        return view('dashboard.getdpb', compact('dpb'));
    }

    public function homesap()
    {
        $hariini = date("Y-m-d");
        $bulanini = date('m');
        $tahunini = date('Y');
        $id_user = Auth::user()->id;
        $level = Auth::user()->level;
        $kode_cabang = Auth::user()->kode_cabang;
        $no_pengajuan[] = "";
        $wilayah_barat = array('BDG', 'TSM', 'GRT', 'PWK', 'BGR', 'SKB', 'BTN');
        $wilayah_timur = array('TGL', 'PWT', 'SBY', 'KLT', 'SMR');
        $pengajuanterakhir = DB::table('pengajuan_limitkredit_v3')
            ->select(DB::raw('MAX(no_pengajuan) as no_pengajuan'))
            ->groupBy('kode_pelanggan')
            ->get();
        foreach ($pengajuanterakhir as $d) {
            $no_pengajuan[] = $d->no_pengajuan;
        }

        if ($kode_cabang != "PCF") {
            $penjualan = DB::table('penjualan')
                ->selectRaw('SUM(total) as totalpenjualan')
                ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
                ->whereRaw('MONTH(tgltransaksi)="' . $bulanini . '"')
                ->whereRaw('YEAR(tgltransaksi)="' . $tahunini . '"')
                ->where('karyawan.kode_cabang', $kode_cabang)
                ->first();
        } else {
            if ($id_user == 82) {
                $penjualan = DB::table('penjualan')
                    ->selectRaw('SUM(total) as totalpenjualan')
                    ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
                    ->whereRaw('MONTH(tgltransaksi)="' . $bulanini . '"')
                    ->whereRaw('YEAR(tgltransaksi)="' . $tahunini . '"')
                    ->whereIn('karyawan.kode_cabang', $wilayah_barat)
                    ->first();
            } else if ($id_user == 97) {
                $penjualan = DB::table('penjualan')
                    ->selectRaw('SUM(total) as totalpenjualan')
                    ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
                    ->whereRaw('MONTH(tgltransaksi)="' . $bulanini . '"')
                    ->whereRaw('YEAR(tgltransaksi)="' . $tahunini . '"')
                    ->whereIn('karyawan.kode_cabang', $wilayah_timur)
                    ->first();
            } else {
                $penjualan = DB::table('penjualan')
                    ->selectRaw('SUM(total) as totalpenjualan')
                    ->whereRaw('MONTH(tgltransaksi)="' . $bulanini . '"')
                    ->whereRaw('YEAR(tgltransaksi)="' . $tahunini . '"')
                    ->first();
            }
        }

        if ($kode_cabang != "PCF") {
            $penjualancabang = DB::table('penjualan')
                ->selectRaw('nama_cabang,SUM(total) as totalpenjualan,COUNT(no_fak_penj) as jmlorder')
                ->whereRaw('MONTH(tgltransaksi)="' . $bulanini . '"')
                ->whereRaw('YEAR(tgltransaksi)="' . $tahunini . '"')
                ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
                ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                ->where('karyawan.kode_cabang', $kode_cabang)
                ->orderBy('nama_cabang')
                ->groupByRaw('karyawan.kode_cabang,nama_cabang')
                ->get();
        } else {

            if ($id_user == 82) {
                $penjualancabang = DB::table('penjualan')
                    ->selectRaw('nama_cabang,SUM(total) as totalpenjualan,COUNT(no_fak_penj) as jmlorder')
                    ->whereRaw('MONTH(tgltransaksi)="' . $bulanini . '"')
                    ->whereRaw('YEAR(tgltransaksi)="' . $tahunini . '"')
                    ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
                    ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                    ->whereIn('karyawan.kode_cabang', $wilayah_barat)
                    ->orderBy('nama_cabang')
                    ->groupByRaw('karyawan.kode_cabang,nama_cabang')
                    ->get();
            } else if ($id_user == 97) {
                $penjualancabang = DB::table('penjualan')
                    ->selectRaw('nama_cabang,SUM(total) as totalpenjualan,COUNT(no_fak_penj) as jmlorder')
                    ->whereRaw('MONTH(tgltransaksi)="' . $bulanini . '"')
                    ->whereRaw('YEAR(tgltransaksi)="' . $tahunini . '"')
                    ->whereIn('karyawan.kode_cabang', $wilayah_timur)
                    ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
                    ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                    ->orderBy('nama_cabang')
                    ->groupByRaw('karyawan.kode_cabang,nama_cabang')
                    ->get();
            } else {
                $penjualancabang = DB::table('penjualan')
                    ->selectRaw('nama_cabang,SUM(total) as totalpenjualan,COUNT(no_fak_penj) as jmlorder')
                    ->whereRaw('MONTH(tgltransaksi)="' . $bulanini . '"')
                    ->whereRaw('YEAR(tgltransaksi)="' . $tahunini . '"')
                    ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
                    ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                    ->orderBy('nama_cabang')
                    ->groupByRaw('karyawan.kode_cabang,nama_cabang')
                    ->get();
            }
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
            //dd($id_user);
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
        } else if ($level == "kepala penjualan") {
            $qpengajuan = Limitkredit::query();
            $qpengajuan->join('pelanggan', 'pengajuan_limitkredit_v3.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
            $qpengajuan->whereIn('no_pengajuan', $no_pengajuan);
            $qpengajuan->where('pelanggan.kode_cabang', $kode_cabang);
            $qpengajuan->whereNull('kacab');
            $qpengajuan->where('status', 0);
            // if (Auth::user()->id == 7) {
            //     $qpengajuan->orwhere('pelanggan.kode_cabang', 'GRT');
            //     $qpengajuan->whereIn('no_pengajuan', $no_pengajuan);
            //     $qpengajuan->whereNull('kacab');
            //     $qpengajuan->where('status', 0);
            // }
            $qpengajuan->where('jumlah', '>', 2000000);

            $jmlpengajuan = $qpengajuan->count();
        } else if ($level == "admin" || $level == "manager accounting") {
            $jmlpengajuan = DB::table('pengajuan_limitkredit_v3')
                ->join('pelanggan', 'pengajuan_limitkredit_v3.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
                ->whereIn('no_pengajuan', $no_pengajuan)
                ->where('status', 0)
                ->count();
        }


        return view('sap.home', compact('penjualan', 'penjualancabang', 'jmlpengajuan'));
    }

    public function dashboardsfa(Request $request)
    {

        $tanggal = $request->tanggal;
        $id_karyawan = $request->id_karyawan;
        $rekap = DB::select("SELECT * FROM (
            SELECT tgl_checkin,tgltransaksi,
            karyawan.kode_cabang,
            karyawan.id_karyawan,
            nama_karyawan,
            checkin.kode_pelanggan,
            nama_pelanggan,
            alamat_pelanggan,
            checkin_time,checkout_time,
            qty_AB,
            qty_AR,
            qty_AS,
            qty_BB,
            qty_DEP,
            qty_SP8,
            qty_SP,
            qty_SP500,
            qty_SC,
            jml_tunai,
            jml_kredit,
            bayar_tunai,
            bayar_titipan,
            bayar_voucher,
            bayar_transfer,
            bayar_giro
            FROM  checkin
            LEFT JOIN users ON checkin.id_karyawan = users.id
            LEFT JOIN karyawan ON  users.id_salesman = karyawan.id_karyawan
            INNER JOIN pelanggan ON checkin.kode_pelanggan = pelanggan.kode_pelanggan
            LEFT JOIN (
                SELECT penjualan.kode_pelanggan,
                tgltransaksi,
                ROUND(SUM(IF(kode_produk='AR',jumlah/isipcsdus,0)),2) as qty_AR,
                ROUND(SUM(IF(kode_produk='AS',jumlah/isipcsdus,0)),2) as qty_AS,
                ROUND(SUM(IF(kode_produk='AB',jumlah/isipcsdus,0)),2) as qty_AB,
                ROUND(SUM(IF(kode_produk='BB',jumlah/isipcsdus,0)),2) as qty_BB,
                ROUND(SUM(IF(kode_produk='DEP',jumlah/isipcsdus,0)),2) as qty_DEP,
                ROUND(SUM(IF(kode_produk='SP8',jumlah/isipcsdus,0)),2) as qty_SP8,
                ROUND(SUM(IF(kode_produk='SP',jumlah/isipcsdus,0)),2) as qty_SP,
                ROUND(SUM(IF(kode_produk='SP500',jumlah/isipcsdus,0)),2) as qty_SP500,
                ROUND(SUM(IF(kode_produk='SC',jumlah/isipcsdus,0)),2) as qty_SC,
                SUM(IF(jenistransaksi='tunai',total,0)) as jml_tunai,
                SUM(IF(jenistransaksi='kredit',total,0)) as jml_kredit
                FROM detailpenjualan
                INNER JOIN penjualan ON  detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                WHERE
                date(date_created) = '$tanggal'
                GROUP BY kode_pelanggan,tgltransaksi
            ) penjualan ON (checkin.kode_pelanggan = penjualan.kode_pelanggan)

            LEFT JOIN (
                SELECT
                kode_pelanggan,
                SUM(IF(historibayar.jenisbayar = 'tunai',bayar,0)) as bayar_tunai,
                SUM(IF(historibayar.jenisbayar = 'titipan',bayar,0)) as bayar_titipan,
                SUM(IF(historibayar.status_bayar='voucher',bayar,0)) as bayar_voucher
                FROM historibayar
                INNER JOIN penjualan ON historibayar.no_fak_penj = penjualan.no_fak_penj
                WHERE tglbayar = '$tanggal'
                GROUP BY kode_pelanggan
            ) historibayar ON (checkin.kode_pelanggan = historibayar.kode_pelanggan)


            LEFT JOIN (
                SELECT kode_pelanggan,SUM(jumlah) as bayar_transfer
                FROM transfer
                INNER JOIN penjualan ON transfer.no_fak_penj = penjualan.no_fak_penj
                WHERE tgl_transfer = '$tanggal'
                GROUP BY kode_pelanggan
            ) transfer ON (checkin.kode_pelanggan = transfer.kode_pelanggan)


            LEFT JOIN (
                SELECT kode_pelanggan,SUM(jumlah) as bayar_giro
                FROM giro
                INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                WHERE tgl_giro = '$tanggal'
                GROUP BY kode_pelanggan
            ) giro ON (checkin.kode_pelanggan = giro.kode_pelanggan)
            WHERE
            tgl_checkin =  '$tanggal'
            AND karyawan.id_karyawan = '$id_karyawan'

            UNION


            SELECT tgltransaksi as tgl_checkin,tgltransaksi,karyawan.
            kode_cabang,
            penjualan.id_karyawan,
            nama_karyawan,
            penjualan.kode_pelanggan,
            nama_pelanggan,
            alamat_pelanggan,
            'NA' as 'checkin_time' , 'NA' as 'checkout_time',
            SUM(IFNULL(qty_AR,0)) as qty_AR,
            SUM(IFNULL(qty_AS,0)) as qty_AS,
            SUM(IFNULL(qty_AB,0)) as qty_AB,
            SUM(IFNULL(qty_BB,0)) as qty_BB,
            SUM(IFNULL(qty_DEP,0)) as qty_DEP,
            SUM(IFNULL(qty_SP8,0)) as qty_SP8,
            SUM(IFNULL(qty_SP,0)) as qty_SP,
            SUM(IFNULL(qty_SP500,0)) as qty_SP500,
            SUM(IFNULL(qty_SC,0)) as qty_SC,
            SUM(IF(penjualan.jenistransaksi='tunai',total,0)) as jml_tunai,
            SUM(IF(penjualan.jenistransaksi='kredit',total,0)) as jml_kredit,
            SUM(IFNULL(bayar_tunai,0)) as bayar_tunai,
            SUM(IFNULL(bayar_titipan,0)) as bayar_titipan,
            SUM(IFNULL(bayar_voucher,0)) as bayar_voucher,
            SUM(IFNULL(bayar_transfer,0)) as bayar_transfer,
            SUM(IFNULL(bayar_giro,0)) as bayar_giro
            FROM penjualan
            INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
            INNER JOIN pelanggan ON penjualan.kode_pelanggan = pelanggan.kode_pelanggan
            LEFT JOIN (
                SELECT no_fak_penj,
                ROUND(SUM(IF(kode_produk='AR',jumlah/isipcsdus,0)),2) as qty_AR,
                ROUND(SUM(IF(kode_produk='AS',jumlah/isipcsdus,0)),2) as qty_AS,
                ROUND(SUM(IF(kode_produk='AB',jumlah/isipcsdus,0)),2) as qty_AB,
                ROUND(SUM(IF(kode_produk='BB',jumlah/isipcsdus,0)),2) as qty_BB,
                ROUND(SUM(IF(kode_produk='DEP',jumlah/isipcsdus,0)),2) as qty_DEP,
                ROUND(SUM(IF(kode_produk='SP8',jumlah/isipcsdus,0)),2) as qty_SP8,
                ROUND(SUM(IF(kode_produk='SP',jumlah/isipcsdus,0)),2) as qty_SP,
                ROUND(SUM(IF(kode_produk='SP500',jumlah/isipcsdus,0)),2) as qty_SP500,
                ROUND(SUM(IF(kode_produk='SC',jumlah/isipcsdus,0)),2) as qty_SC
                FROM detailpenjualan
                INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                GROUP BY no_fak_penj
            ) detailpenjualan ON (penjualan.no_fak_penj = detailpenjualan.no_fak_penj)


            LEFT JOIN (
                SELECT
                historibayar.no_fak_penj,
                SUM(IF(historibayar.jenisbayar = 'tunai',bayar,0)) as bayar_tunai,
                SUM(IF(historibayar.jenisbayar = 'titipan',bayar,0)) as bayar_titipan,
                SUM(IF(historibayar.status_bayar='voucher',bayar,0)) as bayar_voucher
                FROM historibayar
                INNER JOIN penjualan ON historibayar.no_fak_penj = penjualan.no_fak_penj
                WHERE tglbayar = '$tanggal'
                GROUP BY no_fak_penj
            ) historibayar ON (historibayar.no_fak_penj = penjualan.no_fak_penj)

            LEFT JOIN (
                SELECT transfer.no_fak_penj,SUM(jumlah) as bayar_transfer
                FROM transfer
                INNER JOIN penjualan ON transfer.no_fak_penj = penjualan.no_fak_penj
                WHERE tgl_transfer = '$tanggal'
                GROUP BY no_fak_penj
            ) transfer ON (penjualan.no_fak_penj = transfer.no_fak_penj)


            LEFT JOIN (
                SELECT giro.no_fak_penj,SUM(jumlah) as bayar_giro
                FROM giro
                INNER JOIN penjualan ON giro.no_fak_penj = penjualan.no_fak_penj
                WHERE tgl_giro = '$tanggal'
                GROUP BY no_fak_penj
            ) giro ON (penjualan.no_fak_penj = giro.no_fak_penj)

            WHERE date(date_created) = '$tanggal'  AND penjualan.id_karyawan = '$id_karyawan'
            AND  penjualan.kode_pelanggan NOT IN (SELECT kode_pelanggan FROM checkin WHERE tgl_checkin = '$tanggal')

            GROUP BY tgl_checkin,tgltransaksi,karyawan.
            kode_cabang,
            penjualan.id_karyawan,
            nama_karyawan,
            penjualan.kode_pelanggan,
            nama_pelanggan,
            alamat_pelanggan

            ) sfa ORDER BY checkin_time");

        $kode_cabang = Auth::user()->kode_cabang;
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($kode_cabang);

        return view('dashboard.sfa', compact('rekap', 'cabang'));
    }


    public function dashboardsfakp(Request $request)
    {

        $tanggal = $request->tanggal;
        $kode_cabang = $request->kode_cabang;


        $cabang = DB::table('cabang')->get();



        if (!empty($kode_cabang)) {
            $lok_cabang = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
        } else {
            $lok_cabang = DB::table('cabang')->where('kode_cabang', 'PST')->first();
        }
        $lokasi = explode(",", $lok_cabang->lokasi_cabang);


        $smactivity = DB::table('activity_sm')
            ->select('activity_sm.*')
            ->leftJoin('users', 'activity_sm.id_user', '=', 'users.id')
            ->where('users.kode_cabang', $kode_cabang)
            ->whereRaw('DATE(tanggal)="' . $tanggal . '"')
            ->orderBy('tanggal')
            ->get();
        return view('dashboard.sfakp', compact('cabang', 'smactivity', 'lokasi'));
    }


    public function showsmactivity($kode_act_sm)
    {
        $smactivity = DB::table('activity_sm')
            ->select('activity_sm.*')
            ->where('kode_act_sm', $kode_act_sm)
            ->first();



        return view('dashboard.showsmactivity', compact('smactivity'));
    }
}
