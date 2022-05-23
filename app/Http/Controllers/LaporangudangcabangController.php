<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Cabang;
use App\Models\Detailmutasicabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class LaporangudangcabangController extends Controller
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
    public function persediaan()
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        $barang = Barang::orderBy('nama_barang')->get();
        return view('gudangcabang.laporan.frm.lap_persediaan', compact('cabang', 'barang'));
    }

    public function cetak_persediaan(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $kode_produk = $request->kode_produk;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $tanggal = explode("-", $dari);
        $bulan      = $tanggal[1];
        $tahun      = $tanggal[0];
        $mulai = $tahun . "-" . $bulan . "-01";
        $cabang = Cabang::where('kode_cabang', $kode_cabang)->first();
        $produk = Barang::where('kode_produk', $kode_produk)->first();
        $query = Detailmutasicabang::query();
        $query->selectRaw('detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang,
        tgl_mutasi_gudang_cabang,
        mutasi_gudang_cabang.no_dpb,
        nama_karyawan,tujuan,
        no_suratjalan,tgl_kirim,
        isipcsdus,
        isipack,
        isipcs,
        satuan,
        inout_good,
        promo,
        mutasi_gudang_cabang.jenis_mutasi,
        no_dok,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="SURAT JALAN",detail_mutasi_gudang_cabang.jumlah,0)) as penerimaanpusat,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="TRANSIT IN",detail_mutasi_gudang_cabang.jumlah,0)) as transit_in,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="RETUR",detail_mutasi_gudang_cabang.jumlah,0)) as retur,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="HUTANG KIRIM",detail_mutasi_gudang_cabang.jumlah,0)) as hutangkirim,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="PL TTR",detail_mutasi_gudang_cabang.jumlah,0)) as plttr,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="PENYESUAIAN BAD",detail_mutasi_gudang_cabang.jumlah,0)) as penyesuaian_bad,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="REPACK",detail_mutasi_gudang_cabang.jumlah,0)) as repack,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="PENYESUAIAN",detail_mutasi_gudang_cabang.jumlah,0)) as penyesuaian,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="PENJUALAN",detail_mutasi_gudang_cabang.jumlah,0)) as penjualan,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="PROMOSI",detail_mutasi_gudang_cabang.jumlah,0)) as promosi,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="REJECT PASAR",detail_mutasi_gudang_cabang.jumlah,0)) as reject_pasar,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="REJECT MOBIL",detail_mutasi_gudang_cabang.jumlah,0)) as reject_mobil,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="REJECT GUDANG",detail_mutasi_gudang_cabang.jumlah,0)) as reject_gudang,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="TRANSIT OUT",detail_mutasi_gudang_cabang.jumlah,0)) as transit_out,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="GANTI BARANG",detail_mutasi_gudang_cabang.jumlah,0)) as ganti_barang,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="PL HUTANG KIRIM",detail_mutasi_gudang_cabang.jumlah,0)) as plhutangkirim,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="TTR",detail_mutasi_gudang_cabang.jumlah,0)) as ttr,
        date_created,date_updated');
        $query->join('mutasi_gudang_cabang', 'detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang', '=', 'mutasi_gudang_cabang.no_mutasi_gudang_cabang');
        $query->join('master_barang', 'detail_mutasi_gudang_cabang.kode_produk', '=', 'master_barang.kode_produk');
        $query->leftJoin('mutasi_gudang_jadi', 'mutasi_gudang_cabang.no_mutasi_gudang_cabang', '=', 'mutasi_gudang_jadi.no_mutasi_gudang');
        $query->leftJoin('dpb', 'mutasi_gudang_cabang.no_dpb', '=', 'dpb.no_dpb');
        $query->leftJoin('karyawan', 'dpb.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->where('mutasi_gudang_cabang.jenis_mutasi', '!=', 'KIRIM PUSAT');
        $query->whereBetween('tgl_mutasi_gudang_cabang', [$dari, $sampai]);
        $query->where('detail_mutasi_gudang_cabang.kode_produk', $kode_produk);
        $query->where('mutasi_gudang_cabang.kode_cabang', $kode_cabang);
        $query->whereNotNull('inout_good');
        if ($dari < "2022-03-01") {
            $query->orWhere('mutasi_gudang_cabang.jenis_mutasi', 'PENYESUAIAN BAD');
            $query->whereBetween('tgl_mutasi_gudang_cabang', [$dari, $sampai]);
            $query->where('detail_mutasi_gudang_cabang.kode_produk', $kode_produk);
            $query->where('mutasi_gudang_cabang.kode_cabang', $kode_cabang);
            $query->whereNotNull('inout_good');
        } else {
            $query->where('mutasi_gudang_cabang.jenis_mutasi', '!=', 'PENYESUAIAN BAD');
        }
        $query->orderBy('tgl_mutasi_gudang_cabang');
        $query->orderBy('order');
        $query->orderBy('no_dpb');
        $query->groupByRaw('
        detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang,
        tgl_mutasi_gudang_cabang,
        mutasi_gudang_cabang.no_dpb,
        nama_karyawan,tujuan,
        no_suratjalan,tgl_kirim,
        isipcsdus,
        isipack,
        isipcs,
        satuan,
        inout_good,
        promo,
        mutasi_gudang_cabang.jenis_mutasi,
        no_dok,date_created,date_updated');
        $mutasi = $query->get();

        $ceksaldo = DB::table('saldoawal_bj_detail')
            ->selectRaw("saldoawal_bj_detail.kode_produk,jumlah,isipcsdus,isipack,isipcs")
            ->join('saldoawal_bj', 'saldoawal_bj_detail.kode_saldoawal', '=', 'saldoawal_bj.kode_saldoawal')
            ->join('master_barang', 'saldoawal_bj_detail.kode_produk', '=', 'master_barang.kode_produk')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('kode_cabang', $kode_cabang)
            ->where('saldoawal_bj.status', 'GS')
            ->where('saldoawal_bj_detail.kode_produk', $kode_produk)
            ->first();
        $mtsa = DB::table('detail_mutasi_gudang_cabang')
            ->selectRaw("SUM(IF( `inout_good` = 'IN', jumlah, 0)) AS jml_in,
            SUM(IF( `inout_good` = 'OUT', jumlah, 0)) AS jml_out,
            SUM(IF( `inout_good` = 'IN', jumlah, 0)) -SUM(IF( `inout_good` = 'OUT', jumlah, 0)) as jumlah,
            isipcsdus")
            ->join('mutasi_gudang_cabang', 'detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang', '=', 'mutasi_gudang_cabang.no_mutasi_gudang_cabang')
            ->join('master_barang', 'detail_mutasi_gudang_cabang.kode_produk', '=', 'master_barang.kode_produk')
            ->where('tgl_mutasi_gudang_cabang', '>=', $mulai)
            ->where('tgl_mutasi_gudang_cabang', '<', $dari)
            ->where('detail_mutasi_gudang_cabang.kode_produk', $kode_produk)
            ->where('kode_cabang', $kode_cabang)
            ->where('jenis_mutasi', '!=', 'KIRIM PUSAT')
            ->groupBy('isipcsdus')
            ->first();

        if (!empty($mtsa->jumlah)) {
            $jmlmtsa    = $mtsa->jumlah / $mtsa->isipcsdus;
            $realjmlmtsa = $mtsa->jumlah;
        } else {
            $jmlmtsa    = 0;
            $realjmlmtsa = 0;
        }

        if (!empty($ceksaldo->jumlah)) {
            $saldoawal    = ($ceksaldo->jumlah / $ceksaldo->isipcsdus) + $jmlmtsa;
            $realsaldoawal = $ceksaldo->jumlah + $realjmlmtsa;
        } else {
            $saldoawal    = 0  + $jmlmtsa;
            $realsaldoawal = 0  + $realjmlmtsa;
        }
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Persediaan $dari-$sampai.xls");
        }
        return view('gudangcabang.laporan.cetak_persediaan', compact('dari', 'sampai', 'produk', 'cabang', 'mutasi', 'saldoawal', 'realsaldoawal'));
    }

    public function badstok()
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        $barang = Barang::orderBy('nama_barang')->get();
        return view('gudangcabang.laporan.frm.lap_badstok', compact('cabang', 'barang'));
    }

    public function cetak_badstok(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $kode_produk = $request->kode_produk;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $tanggal = explode("-", $dari);
        $bulan      = $tanggal[1];
        $tahun      = $tanggal[0];
        $mulai = $tahun . "-" . $bulan . "-01";
        $cabang = Cabang::where('kode_cabang', $kode_cabang)->first();
        $produk = Barang::where('kode_produk', $kode_produk)->first();
        $query = Detailmutasicabang::query();
        $query->selectRaw('detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang,
        tgl_mutasi_gudang_cabang,
        mutasi_gudang_cabang.no_dpb,
        isipcsdus,
        isipack,
        isipcs,
        satuan,
        inout_good,
        inout_bad,
        mutasi_gudang_cabang.keterangan,
        mutasi_gudang_cabang.jenis_mutasi,
        no_suratjalan,tgl_kirim,
        no_dok,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="REJECT PASAR",detail_mutasi_gudang_cabang.jumlah,0)) as reject_pasar,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="REJECT GUDANG",detail_mutasi_gudang_cabang.jumlah,0)) as reject_gudang,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="REJECT MOBIL",detail_mutasi_gudang_cabang.jumlah,0)) as reject_mobil,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="PENYESUAIAN BAD",detail_mutasi_gudang_cabang.jumlah,0)) as penyesuaian_bad,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="KIRIM PUSAT",detail_mutasi_gudang_cabang.jumlah,0)) as kirim_pusat,
        SUM(IF(mutasi_gudang_cabang.jenis_mutasi="REPACK",detail_mutasi_gudang_cabang.jumlah,0)) as repack,
        date_created,date_updated');
        $query->join('mutasi_gudang_cabang', 'detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang', '=', 'mutasi_gudang_cabang.no_mutasi_gudang_cabang');
        $query->join('master_barang', 'detail_mutasi_gudang_cabang.kode_produk', '=', 'master_barang.kode_produk');
        $query->leftJoin('mutasi_gudang_jadi', 'mutasi_gudang_cabang.no_mutasi_gudang_cabang', '=', 'mutasi_gudang_jadi.no_mutasi_gudang');
        $query->leftJoin('dpb', 'mutasi_gudang_cabang.no_dpb', '=', 'dpb.no_dpb');
        $query->whereBetween('tgl_mutasi_gudang_cabang', [$dari, $sampai]);
        $query->where('detail_mutasi_gudang_cabang.kode_produk', $kode_produk);
        $query->where('mutasi_gudang_cabang.kode_cabang', $kode_cabang);
        $query->where('kondisi', 'BAD');
        $query->orderBy('tgl_mutasi_gudang_cabang');
        $query->orderBy('order');
        $query->groupByRaw('
        detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang,
        tgl_mutasi_gudang_cabang,
        mutasi_gudang_cabang.no_dpb,
        no_suratjalan,tgl_kirim,
        isipcsdus,
        isipack,
        isipcs,
        satuan,
        inout_good,
        inout_bad,
        mutasi_gudang_cabang.keterangan,
        promo,
        mutasi_gudang_cabang.jenis_mutasi,
        no_dok,date_created,date_updated');
        $mutasi = $query->get();
        $ceksaldo = DB::table('saldoawal_bj_detail')
            ->selectRaw("saldoawal_bj_detail.kode_produk,jumlah,isipcsdus,isipack,isipcs")
            ->join('saldoawal_bj', 'saldoawal_bj_detail.kode_saldoawal', '=', 'saldoawal_bj.kode_saldoawal')
            ->join('master_barang', 'saldoawal_bj_detail.kode_produk', '=', 'master_barang.kode_produk')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('kode_cabang', $kode_cabang)
            ->where('status', 'BS')
            ->where('saldoawal_bj_detail.kode_produk', $kode_produk)
            ->first();

        $mtsa = DB::table('detail_mutasi_gudang_cabang')
            ->selectRaw("SUM(IF( `inout_good` = 'IN', jumlah, 0)) AS jml_in,
            SUM(IF( `inout_good` = 'OUT', jumlah, 0)) AS jml_out,
            SUM(IF( `inout_good` = 'IN', jumlah, 0)) -SUM(IF( `inout_good` = 'OUT', jumlah, 0)) as jumlah,
            isipcsdus")
            ->join('mutasi_gudang_cabang', 'detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang', '=', 'mutasi_gudang_cabang.no_mutasi_gudang_cabang')
            ->join('master_barang', 'detail_mutasi_gudang_cabang.kode_produk', '=', 'master_barang.kode_produk')
            ->where('tgl_mutasi_gudang_cabang', '>=', $mulai)
            ->where('tgl_mutasi_gudang_cabang', '<', $dari)
            ->where('detail_mutasi_gudang_cabang.kode_produk', $kode_produk)
            ->where('kode_cabang', $kode_cabang)
            ->where('kondisi', 'BAD')
            ->groupBy('isipcsdus')
            ->first();

        if (!empty($mtsa->jumlah)) {
            $jmlmtsa    = $mtsa->jumlah / $mtsa->isipcsdus;
            $realjmlmtsa = $mtsa->jumlah;
        } else {
            $jmlmtsa    = 0;
            $realjmlmtsa = 0;
        }

        if (!empty($ceksaldo->jumlah)) {
            $saldoawal    = ($ceksaldo->jumlah / $ceksaldo->isipcsdus) + $jmlmtsa;
            $realsaldoawal = $ceksaldo->jumlah + $realjmlmtsa;
        } else {
            $saldoawal    = 0  + $jmlmtsa;
            $realsaldoawal = 0  + $realjmlmtsa;
        }

        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Bad Stok $dari-$sampai.xls");
        }
        return view('gudangcabang.laporan.cetak_badstok', compact('dari', 'sampai', 'produk', 'cabang', 'mutasi', 'saldoawal', 'realsaldoawal'));
    }

    public function rekapbj()
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('gudangcabang.laporan.frm.lap_rekapbj', compact('cabang', 'bulan'));
    }

    public function cetak_rekapbj(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $tanggal = explode("-", $dari);
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];
        $mulai = $tahun . "-" . $bulan . "-01";
        $query = Barang::query();
        $query->selectRaw("master_barang.*,saldo_awal_gs,saldo_awal_bs,pusat,transit_in,retur,lainlain_in,penyesuaian_in,penyesuaianbad_in,repack,
        penjualan,promosi,reject_pasar,reject_mobil,reject_gudang,
        transit_out,lainlain_out,penyesuaian_out,penyesuaianbad_out,kirim_pusat,sisamutasi,sisamutasibad");
        $query->leftJoin(
            DB::raw("(
                SELECT kode_produk,kode_cabang,jumlah as saldo_awal_gs
                FROM saldoawal_bj_detail
                INNER JOIN saldoawal_bj ON saldoawal_bj_detail.kode_saldoawal = saldoawal_bj.kode_saldoawal
                WHERE status ='GS' AND bulan ='$bulan' AND tahun='$tahun' AND kode_cabang='$kode_cabang'
            ) saldo_gs"),
            function ($join) {
                $join->on('master_barang.kode_produk', '=', 'saldo_gs.kode_produk');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT kode_produk,kode_cabang,jumlah as saldo_awal_bs
                FROM saldoawal_bj_detail
                INNER JOIN saldoawal_bj ON saldoawal_bj_detail.kode_saldoawal = saldoawal_bj.kode_saldoawal
                WHERE status ='BS' AND bulan ='$bulan' AND tahun='$tahun' AND kode_cabang='$kode_cabang'
            ) saldo_bs"),
            function ($join) {
                $join->on('master_barang.kode_produk', '=', 'saldo_bs.kode_produk');
            }
        );


        $query->leftJoin(
            DB::raw("(
                SELECT kode_produk,
                SUM(IF(jenis_mutasi = 'SURAT JALAN',jumlah,0)) as pusat,
                SUM(IF(jenis_mutasi = 'TRANSIT IN',jumlah,0)) as transit_in,
                SUM(IF(jenis_mutasi = 'RETUR',jumlah,0)) as retur,
                SUM(IF(jenis_mutasi = 'HUTANG KIRIM' AND inout_good='IN' OR jenis_mutasi='PL TTR' AND inout_good='IN',jumlah,0)) as lainlain_in,
                SUM(IF(jenis_mutasi = 'PENYESUAIAN' AND inout_good='IN',jumlah,0)) as penyesuaian_in,
                SUM(IF(jenis_mutasi = 'PENYESUAIAN BAD' AND inout_good='IN',jumlah,0)) as penyesuaianbad_in,
                SUM(IF(jenis_mutasi = 'REPACK',jumlah,0)) as repack,

                SUM(IF(jenis_mutasi = 'PENJUALAN',jumlah,0)) as penjualan,
                SUM(IF(jenis_mutasi = 'PROMOSI',jumlah,0)) as promosi,
                SUM(IF(jenis_mutasi = 'REJECT PASAR',jumlah,0)) as reject_pasar,
                SUM(IF(jenis_mutasi = 'REJECT MOBIL',jumlah,0)) as reject_mobil,
                SUM(IF(jenis_mutasi = 'REJECT GUDANG',jumlah,0)) as reject_gudang,
                SUM(IF(jenis_mutasi = 'TRANSIT OUT',jumlah,0)) as transit_out,
                SUM(IF(jenis_mutasi = 'PL HUTANG KIRIM' AND inout_good='OUT'
                OR jenis_mutasi='TTR' AND inout_good='OUT'
                OR jenis_mutasi='GANTI BARANG' AND inout_good='OUT',jumlah,0)) as lainlain_out,
                SUM(IF(jenis_mutasi = 'PENYESUAIAN' AND inout_good='OUT',jumlah,0)) as penyesuaian_out,
                SUM(IF(jenis_mutasi = 'PENYESUAIAN BAD' AND inout_good='OUT',jumlah,0)) as penyesuaianbad_out,
                SUM(IF(jenis_mutasi = 'KIRIM PUSAT',jumlah,0)) as kirim_pusat
                FROM detail_mutasi_gudang_cabang
                INNER JOIN mutasi_gudang_cabang ON detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang = mutasi_gudang_cabang.no_mutasi_gudang_cabang
                WHERE tgl_mutasi_gudang_cabang BETWEEN '$dari' AND '$sampai' AND kode_cabang='$kode_cabang'
                GROUP BY kode_produk
            ) dmc"),
            function ($join) {
                $join->on('master_barang.kode_produk', '=', 'dmc.kode_produk');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT kode_produk,
                SUM( IF(kode_cabang ='$kode_cabang' AND jenis_mutasi !='KIRIM PUSAT' AND inout_good='IN',jumlah,0)) - SUM(IF(kode_cabang='$kode_cabang' AND jenis_mutasi !='KIRIM PUSAT' AND inout_good='OUT',jumlah,0)) as sisamutasi,
                SUM(IF(kode_cabang='$kode_cabang' AND kondisi ='BAD' AND inout_bad='IN',jumlah,0)) - SUM(IF(kode_cabang='$kode_cabang' AND kondisi ='BAD' AND inout_bad='OUT',jumlah,0)) as sisamutasibad
                FROM detail_mutasi_gudang_cabang
                INNER JOIN mutasi_gudang_cabang ON detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang = mutasi_gudang_cabang.no_mutasi_gudang_cabang
                WHERE tgl_mutasi_gudang_cabang >= '$mulai' AND tgl_mutasi_gudang_cabang < '$dari' AND kode_cabang='$kode_cabang'
                GROUP BY kode_produk
            ) mutasi"),
            function ($join) {
                $join->on('master_barang.kode_produk', '=', 'mutasi.kode_produk');
            }
        );

        $rekap = $query->get();
        $cabang = Cabang::where('kode_cabang', $kode_cabang)->first();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekap BJ $dari-$sampai.xls");
        }
        return view('gudangcabang.laporan.cetak_rekapbj', compact('dari', 'sampai', 'rekap', 'cabang'));
    }

    public function mutasidpb()
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        $barang = Barang::orderBy('nama_barang')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('gudangcabang.laporan.frm.lap_mutasidpb', compact('cabang', 'bulan', 'barang'));
    }

    public function cetak_mutasidpb(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $kode_produk = $request->kode_produk;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $cabang = Cabang::where('kode_cabang', $kode_cabang)->first();
        $produk = Barang::where('kode_produk', $kode_produk)->first();
        $ceksaldo = DB::table('saldoawal_bj_detail')
            ->selectRaw("saldoawal_bj_detail.kode_produk,jumlah,isipcsdus,isipack,isipcs")
            ->join('saldoawal_bj', 'saldoawal_bj_detail.kode_saldoawal', '=', 'saldoawal_bj.kode_saldoawal')
            ->join('master_barang', 'saldoawal_bj_detail.kode_produk', '=', 'master_barang.kode_produk')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('kode_cabang', $kode_cabang)
            ->where('saldoawal_bj.status', 'GS')
            ->where('saldoawal_bj_detail.kode_produk', $kode_produk)
            ->first();

        $suratjalan = DB::table('detail_mutasi_gudang_cabang')
            ->selectRaw('detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang,tgl_mutasi_gudang_cabang,mutasi_gudang_cabang.no_dpb,nama_karyawan,tujuan,no_suratjalan,tgl_kirim,detail_mutasi_gudang_cabang.jumlah,isipcsdus,inout_good,promo,mutasi_gudang_cabang.jenis_mutasi,
            date_format(mutasi_gudang_cabang.date_created, "%d %M %Y %H:%i:%s") as date_created, date_format(mutasi_gudang_cabang.date_updated, "%d %M %Y %H:%i:%s") as date_updated')
            ->join('mutasi_gudang_cabang', 'detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang', '=', 'mutasi_gudang_cabang.no_mutasi_gudang_cabang')
            ->join('master_barang', 'detail_mutasi_gudang_cabang.kode_produk', '=', 'master_barang.kode_produk')
            ->leftJoin('mutasi_gudang_jadi', 'mutasi_gudang_cabang.no_mutasi_gudang_cabang', '=', 'mutasi_gudang_jadi.no_mutasi_gudang')
            ->leftJoin('dpb', 'mutasi_gudang_cabang.no_dpb', '=', 'dpb.no_dpb')
            ->leftJoin('karyawan', 'dpb.id_karyawan', '=', 'karyawan.id_karyawan')
            ->whereBetween('tgl_mutasi_gudang_cabang', [$dari, $sampai])
            ->where('detail_mutasi_gudang_cabang.kode_produk', $kode_produk)
            ->where('mutasi_gudang_cabang.kode_cabang', $kode_cabang)
            ->where('mutasi_gudang_cabang.jenis_mutasi', 'SURAT JALAN')
            ->orWhereBetween('tgl_mutasi_gudang_cabang', [$dari, $sampai])
            ->where('detail_mutasi_gudang_cabang.kode_produk', $kode_produk)
            ->where('mutasi_gudang_cabang.kode_cabang', $kode_cabang)
            ->where('mutasi_gudang_cabang.jenis_mutasi', 'TRANSIT OUT')
            ->orWhereBetween('tgl_mutasi_gudang_cabang', [$dari, $sampai])
            ->where('detail_mutasi_gudang_cabang.kode_produk', $kode_produk)
            ->where('mutasi_gudang_cabang.kode_cabang', $kode_cabang)
            ->where('mutasi_gudang_cabang.jenis_mutasi', 'TRANSIT IN')
            ->get();

        $reject = DB::table('detail_mutasi_gudang_cabang')
            ->selectRaw('detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang,tgl_mutasi_gudang_cabang,mutasi_gudang_cabang.no_dpb,nama_karyawan,tujuan,no_suratjalan,tgl_kirim,detail_mutasi_gudang_cabang.jumlah,isipcsdus,inout_good,promo,mutasi_gudang_cabang.jenis_mutasi,
            date_format(mutasi_gudang_cabang.date_created, "%d %M %Y %H:%i:%s") as date_created, date_format(mutasi_gudang_cabang.date_updated, "%d %M %Y %H:%i:%s") as date_updated')
            ->join('mutasi_gudang_cabang', 'detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang', '=', 'mutasi_gudang_cabang.no_mutasi_gudang_cabang')
            ->join('master_barang', 'detail_mutasi_gudang_cabang.kode_produk', '=', 'master_barang.kode_produk')
            ->leftJoin('mutasi_gudang_jadi', 'mutasi_gudang_cabang.no_mutasi_gudang_cabang', '=', 'mutasi_gudang_jadi.no_mutasi_gudang')
            ->leftJoin('dpb', 'mutasi_gudang_cabang.no_dpb', '=', 'dpb.no_dpb')
            ->leftJoin('karyawan', 'dpb.id_karyawan', '=', 'karyawan.id_karyawan')
            ->whereBetween('tgl_mutasi_gudang_cabang', [$dari, $sampai])
            ->where('detail_mutasi_gudang_cabang.kode_produk', $kode_produk)
            ->where('mutasi_gudang_cabang.kode_cabang', $kode_cabang)
            ->where('mutasi_gudang_cabang.jenis_mutasi', 'like', '%REJECT%')
            ->get();

        $repack = DB::table('detail_mutasi_gudang_cabang')
            ->selectRaw('detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang,tgl_mutasi_gudang_cabang,mutasi_gudang_cabang.no_dpb,nama_karyawan,tujuan,no_suratjalan,tgl_kirim,detail_mutasi_gudang_cabang.jumlah,isipcsdus,inout_good,promo,mutasi_gudang_cabang.jenis_mutasi,
            date_format(mutasi_gudang_cabang.date_created, "%d %M %Y %H:%i:%s") as date_created, date_format(mutasi_gudang_cabang.date_updated, "%d %M %Y %H:%i:%s") as date_updated')
            ->join('mutasi_gudang_cabang', 'detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang', '=', 'mutasi_gudang_cabang.no_mutasi_gudang_cabang')
            ->join('master_barang', 'detail_mutasi_gudang_cabang.kode_produk', '=', 'master_barang.kode_produk')
            ->leftJoin('mutasi_gudang_jadi', 'mutasi_gudang_cabang.no_mutasi_gudang_cabang', '=', 'mutasi_gudang_jadi.no_mutasi_gudang')
            ->leftJoin('dpb', 'mutasi_gudang_cabang.no_dpb', '=', 'dpb.no_dpb')
            ->leftJoin('karyawan', 'dpb.id_karyawan', '=', 'karyawan.id_karyawan')
            ->whereBetween('tgl_mutasi_gudang_cabang', [$dari, $sampai])
            ->where('detail_mutasi_gudang_cabang.kode_produk', $kode_produk)
            ->where('mutasi_gudang_cabang.kode_cabang', $kode_cabang)
            ->where('mutasi_gudang_cabang.jenis_mutasi', 'REPACK')
            ->get();

        $penyesuaian = DB::table('detail_mutasi_gudang_cabang')
            ->selectRaw('detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang,tgl_mutasi_gudang_cabang,mutasi_gudang_cabang.no_dpb,nama_karyawan,tujuan,no_suratjalan,tgl_kirim,detail_mutasi_gudang_cabang.jumlah,isipcsdus,inout_good,promo,mutasi_gudang_cabang.jenis_mutasi,
            date_format(mutasi_gudang_cabang.date_created, "%d %M %Y %H:%i:%s") as date_created, date_format(mutasi_gudang_cabang.date_updated, "%d %M %Y %H:%i:%s") as date_updated')
            ->join('mutasi_gudang_cabang', 'detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang', '=', 'mutasi_gudang_cabang.no_mutasi_gudang_cabang')
            ->join('master_barang', 'detail_mutasi_gudang_cabang.kode_produk', '=', 'master_barang.kode_produk')
            ->leftJoin('mutasi_gudang_jadi', 'mutasi_gudang_cabang.no_mutasi_gudang_cabang', '=', 'mutasi_gudang_jadi.no_mutasi_gudang')
            ->leftJoin('dpb', 'mutasi_gudang_cabang.no_dpb', '=', 'dpb.no_dpb')
            ->leftJoin('karyawan', 'dpb.id_karyawan', '=', 'karyawan.id_karyawan')
            ->whereBetween('tgl_mutasi_gudang_cabang', [$dari, $sampai])
            ->where('detail_mutasi_gudang_cabang.kode_produk', $kode_produk)
            ->where('mutasi_gudang_cabang.kode_cabang', $kode_cabang)
            ->where('mutasi_gudang_cabang.jenis_mutasi', 'PENYESUAIAN')
            ->orWhere('mutasi_gudang_cabang.jenis_mutasi', 'PENYESUAIAN')
            ->whereBetween('tgl_mutasi_gudang_cabang', [$dari, $sampai])
            ->where('detail_mutasi_gudang_cabang.kode_produk', $kode_produk)
            ->where('mutasi_gudang_cabang.kode_cabang', $kode_cabang)
            ->get();

        $dpbpengambilan = DB::table('detail_dpb')
            ->selectRaw("detail_dpb.no_dpb,dpb.kode_cabang,dpb.id_karyawan,nama_karyawan,tujuan,no_kendaraan,tgl_pengambilan,jml_pengambilan,tgl_pengembalian,jml_pengembalian")
            ->join('dpb', 'detail_dpb.no_dpb', '=', 'dpb.no_dpb')
            ->join('karyawan', 'dpb.id_karyawan', '=', 'karyawan.id_karyawan')
            ->whereBetween('tgl_pengambilan', [$dari, $sampai])
            ->where('dpb.kode_cabang', $kode_cabang)
            ->where('kode_produk', $kode_produk)
            ->get();

        $mtsa = DB::table('detail_mutasi_gudang_cabang')
            ->selectRaw("SUM(IF( `inout_good` = 'IN', jumlah, 0)) AS jml_in,
            SUM(IF( `inout_good` = 'OUT', jumlah, 0)) AS jml_out,
            SUM(IF( `inout_good` = 'IN', jumlah, 0)) -SUM(IF( `inout_good` = 'OUT', jumlah, 0)) as jumlah,
            isipcsdus")
            ->join('mutasi_gudang_cabang', 'detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang', '=', 'mutasi_gudang_cabang.no_mutasi_gudang_cabang')
            ->join('master_barang', 'detail_mutasi_gudang_cabang.kode_produk', '=', 'master_barang.kode_produk')
            ->where('tgl_mutasi_gudang_cabang', '>=', $dari)
            ->where('tgl_mutasi_gudang_cabang', '<', $dari)
            ->where('detail_mutasi_gudang_cabang.kode_produk', $kode_produk)
            ->where('kode_cabang', $kode_cabang)
            ->where('jenis_mutasi', '!=', 'KIRIM PUSAT')
            ->groupBy('isipcsdus')
            ->first();

        if ($mtsa != null) {
            $jmlmtsa    = $mtsa->jumlah / $mtsa->isipcsdus;
        } else {
            $jmlmtsa    = 0;
        }
        if ($ceksaldo != null) {
            $saldoawal    = ($ceksaldo->jumlah / $ceksaldo->isipcsdus) + $jmlmtsa;
        } else {
            $saldoawal    = 0  + $jmlmtsa;
        }
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Mutasi DPB $dari-$sampai.xls");
        }
        return view('gudangcabang.laporan.cetak_mutasidpb', compact('dari', 'sampai', 'suratjalan', 'repack', 'reject', 'penyesuaian', 'dpbpengambilan', 'saldoawal', 'cabang', 'produk'));
    }

    public function rekonsiliasibj()
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('gudangcabang.laporan.frm.lap_rekonsiliasibj', compact('cabang', 'bulan'));
    }

    public function cetak_rekonsiliasibj(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $jeniskonsolidasi = $request->jeniskonsolidasi;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $cabang = Cabang::where('kode_cabang', $kode_cabang)->first();
        if ($kode_cabang == 'TSM') {
            $wherenotsalesgarut = "AND penjualan.id_karyawan NOT IN ('STSM05','STSM09','STSM11')";
        } else {
            $wherenotsalesgarut = "";
        }

        if ($kode_cabang == 'GRT') {
            $wheresalesgarut = "AND penjualan.id_karyawan IN ('STSM05','STSM09','STSM11')";
        } else {
            $wheresalesgarut = "";
        }

        $kode_cabang_2 = $kode_cabang;
        if ($kode_cabang == 'GRT') {
            $kode_cabang = 'TSM';
        }
        if ($jeniskonsolidasi == 1) {
            $jk = "PENJUALAN";
            $rekap = DB::table('master_barang')
                ->selectRaw("master_barang.kode_produk,nama_barang,isipcsdus,satuan,isipack,isipcs,
                totalpenjualan,totalpersediaan, IFNULL(totalpenjualan,0) - IFNULL(totalpersediaan,0) as selisih")
                ->leftJoin(
                    DB::raw("(
                        SELECT kode_produk, SUM(jumlah) as totalpenjualan
                        FROM detailpenjualan
                        INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                        INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                        INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                        WHERE tgltransaksi BETWEEN '$dari' AND '$sampai' AND karyawan.kode_cabang ='$kode_cabang' AND promo !=1 " . $wherenotsalesgarut . $wheresalesgarut . "
                        OR tgltransaksi BETWEEN '$dari' AND '$sampai' AND karyawan.kode_cabang ='$kode_cabang' AND promo IS NULL " . $wherenotsalesgarut . $wheresalesgarut . "
                        GROUP BY kode_produk
                    ) detailpenjualan"),
                    function ($join) {
                        $join->on('master_barang.kode_produk', '=', 'detailpenjualan.kode_produk');
                    }
                )
                ->leftJoin(
                    DB::raw("(
                        SELECT kode_produk,SUM(jumlah) as totalpersediaan
                        FROM detail_mutasi_gudang_cabang
                        INNER JOIN mutasi_gudang_cabang
                        ON detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang = mutasi_gudang_cabang.no_mutasi_gudang_cabang
                        WHERE jenis_mutasi = 'PENJUALAN'
                        AND tgl_mutasi_gudang_cabang BETWEEN '$dari' AND '$sampai'
                        AND kode_cabang ='$kode_cabang_2'
                        GROUP BY kode_produk
                    ) persediaan"),
                    function ($join) {
                        $join->on('master_barang.kode_produk', '=', 'persediaan.kode_produk');
                    }
                )
                ->get();
        } else {
            $jk = "RETUR";
            $rekap = DB::table('master_barang')
                ->selectRaw("master_barang.kode_produk,nama_barang,isipcsdus,satuan,isipack,isipcs,
                totalpenjualan,totalpersediaan, IFNULL(totalpenjualan,0) - IFNULL(totalpersediaan,0) as selisih")
                ->leftJoin(
                    DB::raw("(
                        SELECT kode_produk, SUM(jumlah) as totalpenjualan
                        FROM detailretur
                        INNER JOIN barang ON detailretur.kode_barang = barang.kode_barang
                        INNER JOIN retur ON detailretur.no_retur_penj = retur.no_retur_penj
                        INNER JOIN penjualan ON retur.no_fak_penj = penjualan.no_fak_penj
                        INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                        WHERE tglretur BETWEEN '$dari' AND '$sampai' AND karyawan.kode_cabang ='$kode_cabang' " . $wherenotsalesgarut . $wheresalesgarut . "
                        GROUP BY kode_produk
                    ) detailpenjualan"),
                    function ($join) {
                        $join->on('master_barang.kode_produk', '=', 'detailpenjualan.kode_produk');
                    }
                )
                ->leftJoin(
                    DB::raw("(
                        SELECT kode_produk,SUM(jumlah) as totalpersediaan
                        FROM detail_mutasi_gudang_cabang
                        INNER JOIN mutasi_gudang_cabang
                        ON detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang = mutasi_gudang_cabang.no_mutasi_gudang_cabang
                        WHERE jenis_mutasi = 'RETUR'
                        AND tgl_mutasi_gudang_cabang BETWEEN '$dari' AND '$sampai'
                        AND kode_cabang ='$kode_cabang_2'
                        GROUP BY kode_produk
                    ) persediaan"),
                    function ($join) {
                        $join->on('master_barang.kode_produk', '=', 'persediaan.kode_produk');
                    }
                )
                ->get();
        }
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekonsiliasi BJ $dari-$sampai.xls");
        }
        return view('gudangcabang.laporan.cetak_rekonsiliasibj', compact('dari', 'sampai', 'cabang', 'rekap', 'jk'));
    }
}
