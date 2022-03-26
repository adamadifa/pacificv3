<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Saldoawalkasbesar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class SaldoawalkasbesarController extends Controller
{
    public function index(Request $request)
    {
        $query = Saldoawalkasbesar::query();
        $query->selectRaw("kode_saldoawalkb,tanggal,bulan,tahun,uang_logam,uang_kertas,giro,transfer,kode_cabang");
        $query->where('tahun', $request->tahun);
        if (!empty($request->bulan)) {
            $query->where('bulan', $request->bulan);
        }
        $query->orderBy('kode_cabang');
        $query->orderBy('bulan');
        $saldoawalkasbesar = $query->get();

        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('saldoawalkasbesar.index', compact('saldoawalkasbesar', 'bulan'));
    }

    public function create()
    {
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('saldoawalkasbesar.create', compact('cabang', 'bulan'));
    }

    public function getsaldo(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        if ($bulan == 1) {
            $bln = 12;
            $thn = $tahun - 1;
        } else {
            $bln = $bulan - 1;
            $thn = $tahun;

            $nextbln = $bulan + 1;
            $nexthn = $tahun;
        }

        if ($bln == 12) {
            $nextbln = 1;
            $nexthn = $tahun + 1;
        } else {
            $nextbln = $bulan + 1;
            $nexthn = $tahun;
        }


        $dari = $thn . "-" . $bln . "-" . "01";
        $ceksaldo = DB::table('saldoawal_kasbesar')->where('bulan', $bln)->where('tahun', $thn)->where('kode_cabang', $kode_cabang)->count();
        $cekall = DB::table('saldoawal_kasbesar')->where('kode_cabang', $kode_cabang)->count();
        $ceksaldoSkrg = DB::table('saldoawal_kasbesar')->where('bulan', $bulan)->where('tahun', $tahun)->where('kode_cabang', $kode_cabang)->count();
        $ceknextbulan = DB::table('setoran_pusat')->where('omset_bulan', $bulan)->where('omset_tahun', $tahun)
            ->whereRaw('MONTH(tgl_diterimapusat)="' . $nextbln . '"')
            ->whereRaw('YEAR(tgl_diterimapusat)="' . $nexthn . '"')
            ->where('kode_cabang', $kode_cabang)
            ->first();

        if ($ceknextbulan == null) {
            $sampai = date("Y-m-t", strtotime($dari));
        } else {
            $sampai = $ceknextbulan->tgl_diterimapusat;
        }
        if (empty($ceksaldo) && !empty($cekall) || !empty($ceknow)) {
            echo 1;
        } else {
            $saldoterakhir = DB::table('saldoawal_kasbesar')->where('bulan', $bln)->where('tahun', $thn)->where('kode_cabang', $kode_cabang)->first();
            $setoranpenjualan = DB::table('setoran_penjualan')
                ->selectRaw("SUM(setoran_kertas) as uangkertas,
                SUM(setoran_logam) as uanglogam,SUM(setoran_bg) as giro,SUM(setoran_transfer) as transfer,SUM(girotocash) as girotocash,SUM(girototransfer) as girototransfer")
                ->where('kode_cabang', $kode_cabang)
                ->whereRaw('MONTH(tgl_lhp)="' . $bln . '"')
                ->whereRaw('YEAR(tgl_lhp)="' . $thn . '"')
                ->first();

            $setoranpusat = DB::table('setoran_pusat')
                ->selectRaw("SUM(uang_kertas) as uangkertas,
                SUM(uang_logam) as uanglogam,SUM(giro) as giro,SUM(transfer) as transfer")
                ->where('kode_cabang', $kode_cabang)
                ->whereBetween('tgl_setoranpusat', [$dari, $sampai])
                ->where('omset_bulan', $bln)
                ->where('omset_tahun', $thn)
                ->where('status', 1)
                ->first();
            $kurangsetor = DB::table('kuranglebihsetor')
                ->selectRaw("SUM(uang_kertas) as uangkertas,SUM(uang_logam) as uanglogam")
                ->where('kode_cabang', $kode_cabang)
                ->whereRaw('MONTH(tgl_kl)="' . $bln . '"')
                ->whereRaw('YEAR(tgl_kl)="' . $thn . '"')
                ->where('pembayaran', 1)
                ->first();

            $lebihsetor = DB::table('kuranglebihsetor')
                ->selectRaw("SUM(uang_kertas) as uangkertas,SUM(uang_logam) as uanglogam")
                ->where('kode_cabang', $kode_cabang)
                ->whereRaw('MONTH(tgl_kl)="' . $bln . '"')
                ->whereRaw('YEAR(tgl_kl)="' . $thn . '"')
                ->where('pembayaran', 2)
                ->first();

            $gantilogam = DB::table('logamtokertas')
                ->selectRaw("SUM(jumlah_logamtokertas) as gantikertas")
                ->where('kode_cabang', $kode_cabang)
                ->whereRaw('MONTH(tgl_logamtokertas)="' . $bln . '"')
                ->whereRaw('YEAR(tgl_logamtokertas)="' . $thn . '"')
                ->first();

            //Saldo Sebelumnya
            $saldokertas = $saldoterakhir->uang_kertas;
            $saldologam  = $saldoterakhir->uang_logam;
            $saldogiro   = $saldoterakhir->giro;
            $saldotransfer  = $saldoterakhir->transfer;
            //Setoran Penjualan
            $setoranpenjkertas     = $setoranpenjualan->uangkertas;
            $setoranpenjlogam      = $setoranpenjualan->uanglogam;
            $setoranpenjgiro       = $setoranpenjualan->giro;
            $setoranpenjtransfer   = $setoranpenjualan->transfer;
            $girotocash            = $setoranpenjualan->girotocash;
            $girototransfer        = $setoranpenjualan->girototransfer;
            //Kurang Lebih Setor
            $kkertas = $kurangsetor->uangkertas;
            $klogam  = $kurangsetor->uanglogam;

            $lkertas = $lebihsetor->uangkertas;
            $llogam  = $lebihsetor->uanglogam;

            $gantikertas = $gantilogam->gantikertas;
            $setoranpuskertas = $setoranpusat->uangkertas;
            $setoranpuslogam = $setoranpusat->uanglogam;
            $setoranpusgiro = $setoranpusat->giro;
            $setoranpustransfer = $setoranpusat->transfer;

            $kertas   = $saldokertas + $setoranpenjkertas + $kkertas - $lkertas + $gantikertas + $girotocash - $setoranpuskertas;
            $logam    = $saldologam + $setoranpenjlogam + $klogam - $llogam - $gantikertas - $setoranpuslogam;
            $giro     = $saldogiro + $setoranpenjgiro - $setoranpusgiro - $girotocash - $girototransfer;
            $transfer = $saldotransfer + $setoranpenjtransfer - $setoranpustransfer + $girototransfer;
            echo number_format($kertas, '0', '', '.') . "|" . number_format($logam, '0', '', '.') . "|" . number_format($giro, '0', '', '.') . "|" . number_format($transfer, '0', '', '.');
        }
    }

    public function store(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $thn = substr($tahun, 2, 2);
        $uang_kertas = !empty($request->uang_kertas) ? str_replace(".", "", $request->uang_kertas) : 0;
        $uang_logam = !empty($request->uang_logam) ? str_replace(".", "", $request->uang_logam) : 0;
        $giro = !empty($request->giro) ? str_replace(".", "", $request->giro) : 0;
        $transfer = !empty($request->transfer) ? str_replace(".", "", $request->transfer) : 0;
        $kode_saldoawalkb = "SA" . $kode_cabang . $bulan . $thn;
        $id_admin = Auth::user()->id;
        $data = array(
            'kode_saldoawalkb' => $kode_saldoawalkb,
            'tanggal' => $tahun . "-" . $bulan . "-01",
            'bulan' => $bulan,
            'tahun' => $tahun,
            'uang_kertas' => $uang_kertas,
            'uang_logam' => $uang_logam,
            'giro'  => $giro,
            'transfer' => $transfer,
            'kode_cabang' => $kode_cabang,
            'id_admin' => $id_admin
        );

        $cek = DB::table('saldoawal_kasbesar')->where('kode_saldoawalkb', $kode_saldoawalkb)->count();
        if (empty($cek)) {
            $simpan = DB::table('saldoawal_kasbesar')->insert($data);
            if ($simpan) {
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } else {
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
            }
        } else {
            return Redirect::back()->with(['warning' => 'Data Sudah Ada']);
        }
    }

    public function delete($kode_saldoawalkb)
    {
        $kode_saldoawalkb = Crypt::decrypt($kode_saldoawalkb);
        $hapus = DB::table('saldoawal_kasbesar')->where('kode_saldoawalkb', $kode_saldoawalkb)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
        }
    }
}
