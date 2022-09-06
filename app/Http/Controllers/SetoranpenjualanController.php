<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Setoranpenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class SetoranpenjualanController extends Controller
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
    public function index(Request $request)
    {
        $query = Setoranpenjualan::query();
        $query->selectRaw(" kode_setoran,tgl_lhp,setoran_penjualan.id_karyawan,setoran_penjualan.kode_cabang,nama_karyawan,lhp_tunai,
        ifnull(cektunai,0) AS cektunai,lhp_tagihan,ifnull(cekkredit,0) AS cekkredit,ifnull(ceksetorangiro,0) AS ceksetorangiro,
        ifnull(ceksetorantransfer,0) AS ceksetorantransfer,ifnull(cekgirotocash,0) AS cekgirotocash,
        setoran_kertas,setoran_logam,setoran_bg,setoran_transfer,keterangan,girotocash,girototransfer,
        ifnull(kurangsetorlogam,0) AS kurangsetorlogam,ifnull(kurangsetorkertas,0) AS kurangsetorkertas,
        ifnull(lebihsetorlogam,0) AS lebihsetorlogam,ifnull(lebihsetorkertas,0) AS lebihsetorkertas");
        $query->join('karyawan', 'setoran_penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->leftJoin(
            DB::raw("(
                SELECT id_karyawan,tglbayar,SUM(IF(jenistransaksi='tunai' AND id_giro  IS NULL AND id_transfer IS NULL AND status_bayar IS NULL,bayar,0)) AS cektunai
                ,SUM(IF(jenistransaksi='kredit' AND id_giro  IS NULL AND id_transfer IS NULL AND status_bayar IS NULL,bayar,0)) AS cekkredit
                ,SUM(IF(girotocash IS NOT NULL AND status_bayar IS NULL,bayar,0)) AS cekgirotocash
                FROM historibayar
                WHERE  tglbayar >= '$request->dari'
                AND tglbayar <= '$request->sampai'
                GROUP BY historibayar.id_karyawan,tglbayar
            ) ceklhp"),
            function ($join) {
                $join->on('setoran_penjualan.id_karyawan', '=', 'ceklhp.id_karyawan');
                $join->on('setoran_penjualan.tgl_lhp', '=', 'ceklhp.tglbayar');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT giro.id_karyawan,tgl_giro,SUM(jumlah) as ceksetorangiro
                FROM giro
                WHERE tgl_giro >= '$request->dari'
                AND tgl_giro <= '$request->sampai'
                GROUP BY giro.id_karyawan,tgl_giro
            ) cekgiro"),
            function ($join) {
                $join->on('setoran_penjualan.id_karyawan', '=', 'cekgiro.id_karyawan');
                $join->on('setoran_penjualan.tgl_lhp', '=', 'cekgiro.tgl_giro');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT transfer.id_karyawan,tgl_transfer,SUM(jumlah) as ceksetorantransfer
                FROM transfer
                LEFT JOIN historibayar ON transfer.id_transfer = historibayar.id_transfer
                WHERE tgl_transfer >= '$request->dari'
                AND tgl_transfer <= '$request->sampai' AND girotocash ='' OR tgl_transfer >= '$request->dari'
                AND tgl_transfer <= '$request->sampai' AND girotocash IS NULL
                GROUP BY transfer.id_karyawan,tgl_transfer
            ) cektransfer"),
            function ($join) {
                $join->on('setoran_penjualan.id_karyawan', '=', 'cektransfer.id_karyawan');
                $join->on('setoran_penjualan.tgl_lhp', '=', 'cektransfer.tgl_transfer');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT kuranglebihsetor.id_karyawan,tgl_kl,
                SUM(IF(pembayaran='1',uang_logam,0)) AS kurangsetorlogam,
                SUM(IF(pembayaran='1',uang_kertas,0)) AS kurangsetorkertas,
                SUM(IF(pembayaran='2',uang_logam,0)) AS lebihsetorlogam,
                SUM(IF(pembayaran='2',uang_kertas,0)) AS lebihsetorkertas
                FROM kuranglebihsetor
                WHERE kode_cabang ='$request->kode_cabang' AND tgl_kl >= '$request->dari'
                AND tgl_kl <= '$request->sampai'
                GROUP BY kuranglebihsetor.id_karyawan,tgl_kl
            ) cek_kl"),
            function ($join) {
                $join->on('setoran_penjualan.id_karyawan', '=', 'cek_kl.id_karyawan');
                $join->on('setoran_penjualan.tgl_lhp', '=', 'cek_kl.tgl_kl');
            }
        );

        $query->where('setoran_penjualan.kode_cabang', $request->kode_cabang);
        $query->whereBetween('tgl_lhp', [$request->dari, $request->sampai]);
        if (!empty($request->id_karyawan)) {
            $query->where('setoran_penjualan.id_karyawan', $request->id_karyawan);
        }
        $query->orderBy('tgl_lhp');
        $query->orderBy('nama_karyawan');
        $setoranpenjualan = $query->get();
        //dd($setoranpenjualan);
        if ($this->cabang != "PCF") {
            if ($this->cabang == "GRT") {
                $cabang = DB::table('cabang')->where('kode_cabang', 'TSM')->get();
            } else {
                $cbg = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
                $cabang[] = "";
                foreach ($cbg as $c) {
                    $cabang[] = $c->kode_cabang;
                }
                //dd($cabang);
                $cabang = DB::table('cabang')->whereIn('kode_cabang', $cabang)->get();
            }
        } else {
            $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        }
        $kode_cabang = $this->cabang;
        return view('setoranpenjualan.index', compact('cabang', 'setoranpenjualan', 'kode_cabang'));
    }

    public function detailsetoran(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $id_karyawan = $request->id_karyawan;
        $tgl_lhp = $request->tgl_lhp;

        $salesman = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->first();
        $cabang = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();

        $kasbesar = DB::table('historibayar')
            ->select('historibayar.no_fak_penj', 'tglbayar', 'penjualan.kode_pelanggan', 'nama_pelanggan', 'penjualan.jenistransaksi', 'bayar', 'girotocash')

            ->join('penjualan', 'historibayar.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')

            ->where('tglbayar', $tgl_lhp)
            ->where('historibayar.id_karyawan', $id_karyawan)
            ->whereNull('historibayar.id_giro')
            ->whereNull('historibayar.id_transfer')
            ->whereNull('historibayar.girotocash')

            ->orWhere('tglbayar', $tgl_lhp)
            ->where('historibayar.id_karyawan', $id_karyawan)
            ->whereNull('historibayar.id_giro')
            ->whereNull('historibayar.id_transfer')
            ->where('historibayar.girotocash', 1)

            ->orWhere('tglbayar', $tgl_lhp)
            ->where('historibayar.id_karyawan', $id_karyawan)
            ->whereNotNull('historibayar.id_giro')
            ->whereNull('historibayar.id_transfer')
            ->where('historibayar.girotocash', 1)

            ->orderBy('tglbayar')
            ->orderBy('historibayar.no_fak_penj')
            ->get();

        $listgiro = DB::table('giro')
            ->selectRaw("giro.no_fak_penj,penjualan.kode_pelanggan,nama_pelanggan,tgl_giro,no_giro,namabank,jumlah,tglcair,giro.status")
            ->join('penjualan', 'giro.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->where('tgl_giro', $tgl_lhp)
            ->where('giro.id_karyawan', $id_karyawan)
            ->get();

        $listtransfer = DB::table('transfer')
            ->selectRaw("transfer.no_fak_penj,penjualan.kode_pelanggan,nama_pelanggan,tgl_transfer,namabank,jumlah,tglcair,transfer.status,girotocash,kode_transfer")
            ->join('penjualan', 'transfer.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->leftJoin('historibayar', 'transfer.id_transfer', '=', 'historibayar.id_transfer')
            ->where('tgl_transfer', $tgl_lhp)
            ->where('transfer.id_karyawan', $id_karyawan)
            ->get();
        return view('setoranpenjualan.detailsetoran', compact('cabang', 'salesman', 'tgl_lhp', 'kasbesar', 'listgiro', 'listtransfer'));
    }

    public function synclhp($kode_setoran)
    {
        $kode_setoran = Crypt::decrypt($kode_setoran);
        $setoranpenjualan = DB::table('setoran_penjualan')->where('kode_setoran', $kode_setoran)->first();
        $tgl_lhp = $setoranpenjualan->tgl_lhp;
        $tgl   = explode("-", $tgl_lhp);
        $bulan = $tgl[1];
        $tahun = $tgl[0];
        if ($bulan == 12) {
            $bulan = 1;
            $tahun = $tahun + 1;
        } else {
            $bulan = $bulan + 1;
            $tahun = $tahun;
        }

        $kode_cabang = $setoranpenjualan->kode_cabang;
        $id_karyawan = $setoranpenjualan->id_karyawan;
        $ceksaldo = DB::table('saldoawal_kasbesar')->where('bulan', $bulan)->where('tahun', $tahun)->where('kode_cabang', $kode_cabang)->count();

        if (empty($ceksaldo)) {
            $tunaitagihan = DB::table('historibayar')
                ->selectRaw("historibayar.id_karyawan,SUM(IF(historibayar.jenistransaksi='kredit',bayar,0)) as setoran_tagihan,
                SUM(IF(historibayar.jenistransaksi='tunai',bayar,0)) as setoran_tunai")
                ->where('tglbayar', $tgl_lhp)
                ->whereNull('id_giro')
                ->whereNull('girotocash')
                ->whereNull('id_transfer')
                ->whereNull('status_bayar')
                ->where('historibayar.id_karyawan', $id_karyawan)
                ->groupBy('historibayar.id_karyawan')
                ->first();

            $girotocash = DB::table('historibayar')
                ->selectRaw("historibayar.id_karyawan,SUM(bayar) as setoran_girotocash")
                ->where('historibayar.id_karyawan', $id_karyawan)
                ->where('tglbayar', $tgl_lhp)
                ->whereNotNull('girotocash')
                ->whereNull('id_transfer')
                ->groupBy('historibayar.id_karyawan')
                ->first();

            $girototransfer = DB::table('historibayar')
                ->selectRaw("historibayar.id_karyawan,SUM(bayar) as setoran_girototransfer")
                ->where('historibayar.id_karyawan', $id_karyawan)
                ->where('tglbayar', $tgl_lhp)
                ->whereNotNull('girotocash')
                ->whereNotNull('id_transfer')
                ->groupBy('historibayar.id_karyawan')
                ->first();

            $giro = DB::table('giro')
                ->selectRaw("giro.id_karyawan,SUM(jumlah) as setoran_giro")
                ->where('giro.id_karyawan', $id_karyawan)
                ->where('tgl_giro', $tgl_lhp)
                ->groupBy('giro.id_karyawan')
                ->first();

            $transfer = DB::table('transfer')
                ->selectRaw("transfer.id_karyawan,SUM(jumlah) as setoran_transfer")
                ->leftJoin('historibayar', 'transfer.id_transfer', '=', 'historibayar.id_transfer')
                ->where('transfer.id_karyawan', $id_karyawan)
                ->where('tgl_transfer', $tgl_lhp)
                ->whereNull('girotocash')
                ->groupBy('transfer.id_karyawan')
                ->first();


            $setoran_tunai = $tunaitagihan != null ? $tunaitagihan->setoran_tunai : 0;
            $setoran_giro = $giro != null ? $giro->setoran_giro : 0;
            $setoran_transfer = $transfer != null ? $transfer->setoran_transfer : 0;
            $setoran_tagihan = $tunaitagihan->setoran_tagihan + $setoran_giro + $setoran_transfer != null ? $tunaitagihan->setoran_tagihan + $setoran_giro + $setoran_transfer : 0;
            $gantigirokecash = $girotocash != null  ?  $girotocash->setoran_girotocash : 0;
            $gantigiroketransfer = $girototransfer != null ? $girototransfer->setoran_girototransfer : 0;

            $data = [
                'lhp_tunai' => $setoran_tunai,
                'lhp_tagihan' => $setoran_tagihan,
                'girotocash' => $gantigirokecash,
                'girototransfer' => $gantigiroketransfer,
                'setoran_bg' => $setoran_giro,
                'setoran_transfer' => $setoran_transfer
            ];

            $update = DB::table('setoran_penjualan')->where('kode_setoran', $kode_setoran)->update($data);
            if ($update) {
                return Redirect::back()->with(['success' => 'Data Berhasil Disyncronisasi']);
            } else {
                return Redirect::back()->with(['warning' => 'Data Gagal Disyncronisasi Hubungi Tim IT']);
            }
        } else {
            return Redirect::back()->with(['warning' => 'Tidak Dapat Ubah Data karena Saldo Bulan Berikutnya Sudah di Set']);
        }
    }

    public function delete($kode_setoran)
    {
        $kode_setoran = Crypt::decrypt($kode_setoran);
        $hapus = DB::table('setoran_penjualan')->where('kode_setoran', $kode_setoran)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus Hubungi Tim IT']);
        }
    }

    public function create()
    {
        if ($this->cabang != "PCF") {
            if ($this->cabang == "GRT") {
                $cabang = DB::table('cabang')->where('kode_cabang', 'TSM')->get();
            } else {
                $cbg = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
                $cabang[] = "";
                foreach ($cbg as $c) {
                    $cabang[] = $c->kode_cabang;
                }
                //dd($cabang);
                $cabang = DB::table('cabang')->whereIn('kode_cabang', $cabang)->get();
            }
        } else {
            $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        }
        return view('setoranpenjualan.create', compact('cabang'));
    }

    public function getsetoranpenjualan(Request $request)
    {
        $tgl_lhp = $request->tgl_lhp;
        $id_karyawan = $request->id_karyawan;

        $tunaitagihan = DB::table('historibayar')
            ->selectRaw("historibayar.id_karyawan,SUM(IF(historibayar.jenistransaksi='kredit',bayar,0)) as setoran_tagihan,
                SUM(IF(historibayar.jenistransaksi='tunai',bayar,0)) as setoran_tunai")
            ->where('tglbayar', $tgl_lhp)
            ->whereNull('id_giro')
            ->whereNull('girotocash')
            ->whereNull('id_transfer')
            ->whereNull('status_bayar')
            ->where('historibayar.id_karyawan', $id_karyawan)
            ->groupBy('historibayar.id_karyawan')
            ->first();
        $girotocash = DB::table('historibayar')
            ->selectRaw("historibayar.id_karyawan,SUM(bayar) as setoran_girotocash")
            ->where('historibayar.id_karyawan', $id_karyawan)
            ->where('tglbayar', $tgl_lhp)
            ->whereNotNull('girotocash')
            ->whereNull('id_transfer')
            ->groupBy('historibayar.id_karyawan')
            ->first();

        $girototransfer = DB::table('historibayar')
            ->selectRaw("historibayar.id_karyawan,SUM(bayar) as setoran_girototransfer")
            ->where('historibayar.id_karyawan', $id_karyawan)
            ->where('tglbayar', $tgl_lhp)
            ->whereNotNull('girotocash')
            ->whereNotNull('id_transfer')
            ->groupBy('historibayar.id_karyawan')
            ->first();

        $giro = DB::table('giro')
            ->selectRaw("giro.id_karyawan,SUM(jumlah) as setoran_giro")
            ->where('giro.id_karyawan', $id_karyawan)
            ->where('tgl_giro', $tgl_lhp)
            ->whereNotNull('tgl_giro')
            ->groupBy('giro.id_karyawan')
            ->first();

        $transfer = DB::table('transfer')
            ->selectRaw("transfer.id_karyawan,SUM(jumlah) as setoran_transfer")
            ->leftJoin('historibayar', 'transfer.id_transfer', '=', 'historibayar.id_transfer')
            ->where('transfer.id_karyawan', $id_karyawan)
            ->where('tgl_transfer', $tgl_lhp)
            ->whereNotNull('tgl_transfer')
            ->whereNull('girotocash')
            ->groupBy('transfer.id_karyawan')
            ->first();

        $setoran_tunai = $tunaitagihan != null ? $tunaitagihan->setoran_tunai : 0;
        $setoran_giro = $giro != null ? $giro->setoran_giro : 0;
        $setoran_transfer = $transfer != null ? $transfer->setoran_transfer : 0;
        $setoran_giro_transfer = $setoran_giro + $setoran_transfer;
        $setoran_tagihan = $tunaitagihan != null ? $tunaitagihan->setoran_tagihan : 0;
        $total_setoran_tagihan = $setoran_tagihan + $setoran_giro + $setoran_transfer;
        $gantigirokecash = $girotocash != null  ?  $girotocash->setoran_girotocash : 0;
        $gantigiroketransfer = $girototransfer != null ? $girototransfer->setoran_girototransfer : 0;


        echo rupiah($setoran_tunai) . "|" . rupiah($total_setoran_tagihan) . "|" . rupiah($setoran_giro) . "|" . rupiah($gantigirokecash) . "|" . rupiah($setoran_transfer) . "|" . rupiah($setoran_giro_transfer) . "|" . rupiah($gantigiroketransfer) . "|" . rupiah($setoran_tunai + $total_setoran_tagihan);
    }

    public function ceksetoran(Request $request)
    {
        $ceksetoran = DB::table('setoran_penjualan')->where('id_karyawan', $request->id_karyawan)->where('tgl_lhp', $request->tgl_lhp)->count();
        echo $ceksetoran;
    }

    public function store(Request $request)
    {
        $tgl_lhp = $request->tgl_lhp;
        $kode_cabang = $request->kode_cabang;
        $id_karyawan = $request->id_karyawan;
        $lhp_tunai = !empty($request->lhp_tunai) ? str_replace(".", "", $request->lhp_tunai) : 0;
        $lhp_tagihan = !empty($request->lhp_tagihan)  ? str_replace(".", "", $request->lhp_tagihan) : 0;

        $setoran_kertas = !empty($request->setoran_kertas) ? str_replace(".", "", $request->setoran_kertas) : 0;
        $setoran_logam = !empty($request->setoran_logam) ? str_replace(".", "", $request->setoran_logam) : 0;
        $setoran_bg = !empty($request->setoran_bg) ? str_replace(".", "", $request->setoran_bg) : 0;
        $setoran_transfer = !empty($request->setoran_transfer) ? str_replace(".", "", $request->setoran_transfer) : 0;

        $girotocash = !empty($request->girotocash) ? str_replace(".", "", $request->girotocash) : 0;
        $girototransfer = !empty($request->girototransfer) ? str_replace(".", "", $request->girototransfer) : 0;
        $keterangan = $request->keterangan;

        $tanggal = explode("-", $tgl_lhp);
        $hari  = $tanggal[2];
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];
        $thn = substr($tahun, 2, 2);

        $tahunini = date("y");
        $setoranpenjualan = DB::table('setoran_penjualan')
            ->select('kode_setoran')
            ->whereRaw('LEFT(kode_setoran,4)="SP' . $tahunini . '"')
            ->orderBy('kode_setoran', 'desc')
            ->first();
        $lastkode_setoran = $setoranpenjualan->kode_setoran;
        $kode_setoran = buatkode($lastkode_setoran, 'SP' . $tahunini, 5);
        $data = array(
            'kode_setoran'    => $kode_setoran,
            'tgl_lhp'         => $tgl_lhp,
            'kode_cabang'     => $kode_cabang,
            'id_karyawan'     => $id_karyawan,
            'lhp_tunai'       => $lhp_tunai,
            'lhp_tagihan'     => $lhp_tagihan,
            'setoran_kertas'  => $setoran_kertas,
            'setoran_logam'   => $setoran_logam,
            'setoran_bg'      => $setoran_bg,
            'setoran_transfer' => $setoran_transfer,
            'girotocash'      => $girotocash,
            'girototransfer'  => $girototransfer,
            'keterangan'      => $keterangan
        );

        if ($bulan == 12) {
            $bulan = 1;
            $tahun = $tahun + 1;
        } else {
            $bulan = $bulan + 1;
            $tahun = $tahun;
        }

        $ceksaldo = DB::table('saldoawal_kasbesar')->where('bulan', $bulan)->where('tahun', $tahun)->where('kode_cabang', $kode_cabang)->count();
        if (empty($ceksaldo)) {
            $simpan = DB::table('setoran_penjualan')->insert($data);
            if ($simpan) {
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan !']);
            } else {
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan Hubungi Tim IT !']);
            }
        } else {
            return Redirect::back()->with(['warning' => 'Periode Sudah Ditutup']);
        }
    }

    public function edit($kode_setoran)
    {
        $kode_setoran = Crypt::decrypt($kode_setoran);
        $setoranpenjualan = DB::table('setoran_penjualan')->where('kode_setoran', $kode_setoran)->first();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('setoranpenjualan.edit', compact('cabang', 'setoranpenjualan'));
    }

    public function update($kode_setoran, Request $request)
    {

        $kode_setoran = Crypt::decrypt($kode_setoran);
        $lhp_tunai = !empty($request->lhp_tunai) ? str_replace(".", "", $request->lhp_tunai) : 0;
        $lhp_tagihan = !empty($request->lhp_tagihan)  ? str_replace(".", "", $request->lhp_tagihan) : 0;

        $setoran_kertas = !empty($request->setoran_kertas) ? str_replace(".", "", $request->setoran_kertas) : 0;
        $setoran_logam = !empty($request->setoran_logam) ? str_replace(".", "", $request->setoran_logam) : 0;
        $setoran_bg = !empty($request->setoran_bg) ? str_replace(".", "", $request->setoran_bg) : 0;
        $setoran_transfer = !empty($request->setoran_transfer) ? str_replace(".", "", $request->setoran_transfer) : 0;

        $girotocash = !empty($request->girotocash) ? str_replace(".", "", $request->girotocash) : 0;
        $girototransfer = !empty($request->girototransfer) ? str_replace(".", "", $request->girototransfer) : 0;
        $keterangan = $request->keterangan;
        $data = array(
            'lhp_tunai'       => $lhp_tunai,
            'lhp_tagihan'     => $lhp_tagihan,
            'setoran_kertas'  => $setoran_kertas,
            'setoran_logam'   => $setoran_logam,
            'setoran_bg'      => $setoran_bg,
            'setoran_transfer' => $setoran_transfer,
            'girotocash'      => $girotocash,
            'girototransfer'  => $girototransfer,
            'keterangan'      => $keterangan
        );
        $simpan = DB::table('setoran_penjualan')->where('kode_setoran', $kode_setoran)->update($data);
        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di update !']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Update Hubungi Tim IT !']);
        }
    }

    public function cetak(Request $request)
    {
        $query = Setoranpenjualan::query();
        $query->selectRaw(" kode_setoran,tgl_lhp,setoran_penjualan.id_karyawan,setoran_penjualan.kode_cabang,
        nama_karyawan,lhp_tunai,
        ifnull(cektunai,0) AS cektunai,
        lhp_tagihan,ifnull(cekkredit,0) AS cekkredit,
        ifnull(ceksetorangiro,0) AS ceksetorangiro,
        ifnull(ceksetorantransfer,0) AS ceksetorantransfer,
        ifnull(cekgirotocash,0) AS cekgirotocash,
        setoran_kertas,setoran_logam,setoran_bg,setoran_transfer,
        keterangan,girotocash,girototransfer,
        ifnull(kurangsetorlogam,0) AS kurangsetorlogam,ifnull(kurangsetorkertas,0) AS kurangsetorkertas,
        ifnull(lebihsetorlogam,0) AS lebihsetorlogam,ifnull(lebihsetorkertas,0) AS lebihsetorkertas");
        $query->join('karyawan', 'setoran_penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->leftJoin(
            DB::raw("(
                SELECT id_karyawan,tglbayar,SUM(IF(jenistransaksi='tunai' AND id_giro  IS NULL AND id_transfer IS NULL AND status_bayar IS NULL,bayar,0)) AS cektunai
                ,SUM(IF(jenistransaksi='kredit' AND id_giro  IS NULL AND id_transfer IS NULL AND status_bayar IS NULL,bayar,0)) AS cekkredit
                ,SUM(IF(girotocash IS NOT NULL AND status_bayar IS NULL,bayar,0)) AS cekgirotocash
                FROM historibayar
                WHERE  tglbayar >= '$request->dari'
                AND tglbayar <= '$request->sampai'
                GROUP BY historibayar.id_karyawan,tglbayar
            ) ceklhp"),
            function ($join) {
                $join->on('setoran_penjualan.id_karyawan', '=', 'ceklhp.id_karyawan');
                $join->on('setoran_penjualan.tgl_lhp', '=', 'ceklhp.tglbayar');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT giro.id_karyawan,tgl_giro,SUM(jumlah) as ceksetorangiro
                FROM giro
                WHERE tgl_giro >= '$request->dari'
                AND tgl_giro <= '$request->sampai'
                GROUP BY giro.id_karyawan,tgl_giro
            ) cekgiro"),
            function ($join) {
                $join->on('setoran_penjualan.id_karyawan', '=', 'cekgiro.id_karyawan');
                $join->on('setoran_penjualan.tgl_lhp', '=', 'cekgiro.tgl_giro');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT transfer.id_karyawan,tgl_transfer,SUM(jumlah) as ceksetorantransfer
                FROM transfer
                LEFT JOIN historibayar ON transfer.id_transfer = historibayar.id_transfer
                WHERE tgl_transfer >= '$request->dari'
                AND tgl_transfer <= '$request->sampai' AND girotocash ='' OR tgl_transfer >= '$request->dari'
                AND tgl_transfer <= '$request->sampai' AND girotocash IS NULL
                GROUP BY transfer.id_karyawan,tgl_transfer
            ) cektransfer"),
            function ($join) {
                $join->on('setoran_penjualan.id_karyawan', '=', 'cektransfer.id_karyawan');
                $join->on('setoran_penjualan.tgl_lhp', '=', 'cektransfer.tgl_transfer');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT kuranglebihsetor.id_karyawan,tgl_kl,
                SUM(IF(pembayaran='1',uang_logam,0)) AS kurangsetorlogam,
                SUM(IF(pembayaran='1',uang_kertas,0)) AS kurangsetorkertas,
                SUM(IF(pembayaran='2',uang_logam,0)) AS lebihsetorlogam,
                SUM(IF(pembayaran='2',uang_kertas,0)) AS lebihsetorkertas
                FROM kuranglebihsetor
                WHERE kode_cabang ='$request->kode_cabang' AND tgl_kl >= '$request->dari'
                AND tgl_kl <= '$request->sampai'
                GROUP BY kuranglebihsetor.id_karyawan,tgl_kl
            ) cek_kl"),
            function ($join) {
                $join->on('setoran_penjualan.id_karyawan', '=', 'cek_kl.id_karyawan');
                $join->on('setoran_penjualan.tgl_lhp', '=', 'cek_kl.tgl_kl');
            }
        );

        $query->where('setoran_penjualan.kode_cabang', $request->kode_cabang);
        $query->whereBetween('tgl_lhp', [$request->dari, $request->sampai]);
        if (!empty($request->id_karyawan)) {
            $query->where('setoran_penjualan.id_karyawan', $request->id_karyawan);
        }
        $query->orderBy('tgl_lhp');
        $query->orderBy('nama_karyawan');
        $setoranpenjualan = $query->get();
        //dd($setoranpenjualan);
        $cabang = Cabang::where('kode_cabang', $request->kode_cabang)->first();
        $dari = $request->dari;
        $sampai = $request->sampai;
        if ($request->excel == "true") {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Setoran Penjualan $dari-$sampai-$time.xls");
        }
        return view('setoranpenjualan.cetak', compact('cabang', 'setoranpenjualan', 'dari', 'sampai'));
    }
}
