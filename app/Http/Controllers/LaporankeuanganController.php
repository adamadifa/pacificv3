<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Cabang;
use App\Models\Kaskecil;
use App\Models\Ledger;
use App\Models\Salesman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class LaporankeuanganController extends Controller
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
    public function kaskecil()
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }

        return view('kaskecil.laporan.frm.lap_kaskecil', compact('cabang'));
    }

    public function cetak_kaskecil(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $dari_kode_akun = $request->dari_kode_akun;
        $sampai_kode_akun = $request->sampai_kode_akun;
        $query = Kaskecil::query();
        $query->selectRaw('id,nobukti,tgl_kaskecil,kaskecil_detail.keterangan,kaskecil_detail.jumlah,kaskecil_detail.kode_akun,status_dk,nama_akun,no_ref,date_created,date_updated');
        $query->join('coa', 'kaskecil_detail.kode_akun', '=', 'coa.kode_akun');
        $query->whereBetween('tgl_kaskecil', [$request->dari, $request->sampai]);
        if (!empty($dari_kode_akun) && !empty($sampai_kode_akun)) {
            $query->whereBetween('kaskecil_detail.kode_akun', [$dari_kode_akun, $sampai_kode_akun]);
        }
        $query->where('kaskecil_detail.kode_cabang', $kode_cabang);
        $query->orderBy('tgl_kaskecil');
        $query->orderBy('nobukti');
        $query->orderBy('order');
        $kaskecil = $query->get();

        $queryrekap = Kaskecil::query();
        $queryrekap->selectRaw("kaskecil_detail.kode_akun,nama_akun,SUM(IF(status_dk='D',jumlah,0)) as totalpengeluaran,SUM(IF(status_dk='K',jumlah,0)) as totalpemasukan");
        $queryrekap->join('coa', 'kaskecil_detail.kode_akun', '=', 'coa.kode_akun');
        $queryrekap->whereBetween('tgl_kaskecil', [$request->dari, $request->sampai]);
        if (!empty($dari_kode_akun) && !empty($sampai_kode_akun)) {
            $queryrekap->whereBetween('kaskecil_detail.kode_akun', [$dari_kode_akun, $sampai_kode_akun]);
        }
        $queryrekap->where('kaskecil_detail.kode_cabang', $kode_cabang);
        $queryrekap->orderBy('kode_akun');
        $queryrekap->groupByRaw('kaskecil_detail.kode_akun,nama_akun');
        $rekap = $queryrekap->get();


        $qsaldoawal = Kaskecil::query();
        $qsaldoawal->selectRaw("SUM(IF( `status_dk` = 'K', jumlah, 0)) -SUM(IF( `status_dk` = 'D', jumlah, 0)) as saldo_awal");
        $qsaldoawal->where('tgl_kaskecil', '<', $request->dari);
        $qsaldoawal->where('kode_cabang', $kode_cabang);
        $saldoawal = $qsaldoawal->first();

        $cabang = Cabang::where('kode_cabang', $kode_cabang)->first();
        if (isset($_POST['export'])) {
            echo "EXPORT";
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Kas Kecil Periode $dari-$sampai.xls");
        }
        return view('kaskecil.laporan.cetak_kaskecil', compact('kaskecil', 'cabang', 'saldoawal', 'dari', 'sampai', 'dari_kode_akun', 'sampai_kode_akun', 'rekap'));
    }


    public function ledger()
    {
        $role = ['admin', 'direktur', 'general manager', 'manager accounting', 'staff keuangan'];
        $level = Auth::user()->level;
        if (in_array($level, $role)) {
            $bank = DB::table('master_bank')->orderBy('kode_bank')->get();
        } else {
            if ($this->cabang == "PCF") {
                $listbank = DB::table('cabang')->where('kode_cabang', '!=', 'PST')->get();
                $list[] = "";
                foreach ($listbank as $d) {
                    $list[] = $d->kode_cabang;
                }
                $bank = DB::table('master_bank')->whereIn('kode_cabang', $list)->orderBy('kode_bank')->get();
            } else {
                $bank = DB::table('master_bank')->where('kode_cabang', $this->cabang)->orderBy('kode_bank')->get();
            }
        }
        if ($this->cabang == "PCF") {
            $coa = DB::table('coa')->orderBy('kode_akun')->get();
        } else {
            $coa = DB::table('set_coa_cabang')
                ->select('set_coa_cabang.kode_akun', 'nama_akun')
                ->join('coa', 'set_coa_cabang.kode_akun', '=', 'coa.kode_akun')
                ->where('set_coa_cabang.kode_cabang', $this->cabang)->groupBy('set_coa_cabang.kode_akun', 'nama_akun')->get();
        }
        return view('ledger.laporan.frm.lap_ledger', compact('bank', 'coa'));
    }

    public function cetak_ledger(Request $request)
    {
        $kode_bank = $request->kode_bank;
        $jenislaporan = $request->jenislaporan;
        $dari_kode_akun = $request->dari_kode_akun;
        $sampai_kode_akun = $request->sampai_kode_akun;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $bank = Bank::where('kode_bank', $kode_bank)->first();
        if ($bank == null) {
            $namabank = "";
        } else {
            $namabank = $bank->nama_bank;
        }
        if ($jenislaporan == "detail") {
            $query = Ledger::query();
            $query->select('ledger_bank.*', 'nama_akun');
            $query->join('coa', 'ledger_bank.kode_akun', '=', 'coa.kode_akun');
            $query->join('master_bank', 'ledger_bank.bank', '=', 'master_bank.kode_bank');
            $query->orderBy('tgl_ledger');
            $query->orderBy('date_created');
            $query->whereBetween('tgl_ledger', [$request->dari, $request->sampai]);
            $query->where('ledger_bank.bank', $kode_bank);
            if (!empty($dari_kode_akun) && !empty($sampai_kode_akun)) {
                $query->whereBetween('ledger_bank.kode_akun', [$dari_kode_akun, $sampai_kode_akun]);
            }
            $ledger = $query->get();

            if (!empty($request->dari)) {
                $tanggal = explode("-", $request->dari);
                $bulan = $tanggal[1];
                $tahun = $tanggal[0];
            } else {
                $bulan = "";
                $tahun = "";
            }

            $lastsaldoawal = DB::table('saldoawal_ledger')
                ->where('bulan', '<=', $bulan)
                ->where('tahun', '<=', $tahun)
                ->where('kode_bank', $kode_bank)
                ->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->first();
            if ($lastsaldoawal != null) {
                $sa = $lastsaldoawal->jumlah;
                $tgl_mulai = $lastsaldoawal->tahun . "-" . $lastsaldoawal->bulan . "-01";
            } else {
                $sa = 0;
                $tgl_mulai = "";
            }

            if (!empty($request->dari)) {
                $mutasi = DB::table('ledger_bank')
                    ->selectRaw("SUM(IF(status_dk='K',jumlah,0)) - SUM(IF(status_dk='D',jumlah,0)) as sisamutasi")
                    ->where('bank', $kode_bank)
                    ->where('tgl_ledger', '>=', $tgl_mulai)
                    ->where('tgl_ledger', '<', $request->dari)
                    ->first();

                $saldoawal = $sa + $mutasi->sisamutasi;
            } else {
                $saldoawal = 0;
            }
            if (isset($_POST['export'])) {
                echo "EXPORT";
                // Fungsi header dengan mengirimkan raw data excel
                header("Content-type: application/vnd-ms-excel");
                // Mendefinisikan nama file ekspor "hasil-export.xls"
                header("Content-Disposition: attachment; filename=Ledger $bank->nama_bank Periode $dari-$sampai.xls");
            }
            return view('ledger.laporan.cetak_ledger', compact('ledger', 'saldoawal', 'bank', 'dari', 'sampai'));
        } else {
            $query = Ledger::query();
            $query->selectRaw(
                "ledger_bank.kode_akun,nama_akun,
                SUM(IF(status_dk='D',jumlah,0)) as totaldebet,
                SUM(IF(status_dk='K',jumlah,0)) as totalkredit"
            );
            $query->join('coa', 'ledger_bank.kode_akun', '=', 'coa.kode_akun');
            $query->orderBy('ledger_bank.kode_akun');
            $query->whereBetween('tgl_ledger', [$request->dari, $request->sampai]);
            if (!empty($request->kode_bank)) {
                $query->where('ledger_bank.bank', $kode_bank);
            }
            $query->groupBy('ledger_bank.kode_akun', 'nama_akun');
            $ledger = $query->get();
            if (isset($_POST['export'])) {
                // Fungsi header dengan mengirimkan raw data excel
                header("Content-type: application/vnd-ms-excel");
                // Mendefinisikan nama file ekspor "hasil-export.xls"
                header("Content-Disposition: attachment; filename=Rekap Ledger $namabank Periode $dari-$sampai.xls");
            }
            return view('ledger.laporan.cetak_rekapledger', compact('bank', 'ledger', 'dari', 'sampai'));
        }
    }

    public function penjualan()
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        return view('penjualan.laporan.frm.lap_penjualan_keuangan', compact('cabang'));
    }

    public function cetak_penjualan(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $dari = $request->dari;
        $sampai = $request->sampai;

        $setoran_penjualan = DB::table('setoran_penjualan')
            ->select('setoran_penjualan.id_karyawan', 'nama_karyawan', 'tgl_lhp', 'lhp_tunai', 'lhp_tagihan')
            ->join('karyawan', 'setoran_penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->whereBetween('tgl_lhp', [$dari, $sampai])
            ->where('setoran_penjualan.kode_cabang', $kode_cabang)
            ->orderBy('id_karyawan', 'asc')
            ->orderBy('tgl_lhp', 'asc')->get();
        $cabang = Cabang::where('kode_cabang', $kode_cabang)->first();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Penjualan Periode $dari-$sampai.xls");
        }
        return view('penjualan.laporan.cetak_penjualan_keuangan', compact('dari', 'sampai', 'kode_cabang', 'setoran_penjualan', 'cabang'));
    }

    public function uanglogam()
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('uanglogam.laporan.frm.lap_uanglogam', compact('cabang', 'bulan'));
    }

    public function cetak_uanglogam(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));

        if ($bulan == 12) {
            $bln = 1;
            $thn = $tahun + 1;
        } else {
            $bln = $bulan + 1;
            $thn = $tahun;
        }
        $ceknextBulan = DB::table('setoran_pusat')->where('omset_bulan', $bulan)->where('omset_tahun', $tahun)
            ->whereRaw('MONTH(tgl_diterimapusat) = ' . $bln)
            ->whereRaw('YEAR(tgl_diterimapusat) = ' . $thn)
            ->where('kode_cabang', $kode_cabang)
            ->orderBy('tgl_diterimapusat', 'desc')
            ->first();
        if ($ceknextBulan ==  null) {
            $end = $sampai;
        } else {
            $end = $ceknextBulan->tgl_diterimapusat;
        }

        $saldokasbesar = DB::table('saldoawal_kasbesar')
            ->select('uang_logam')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('kode_cabang', $kode_cabang)->first();
        $saldologam = $saldokasbesar != null ?  $saldokasbesar->uang_logam : 0;
        $cabang = Cabang::where('kode_cabang', $kode_cabang)->first();
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Mutasi Uang Logam $dari-$sampai.xls");
        }
        return view('uanglogam.laporan.cetak_uanglogam', compact('bulan', 'tahun', 'cabang', 'saldologam', 'end', 'namabulan', 'dari', 'kode_cabang', 'sampai'));
    }

    public function rekapbg()
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('giro.laporan.frm.lap_rekapbg', compact('cabang', 'bulan'));
    }

    public function cetak_rekapbg(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));

        if ($bulan == 12) {
            $bln = 1;
            $thn = $tahun + 1;
        } else {
            $bln = $bulan + 1;
            $thn = $tahun;
        }

        if ($bulan == 1) {
            $batasbulan = 11;
            $tahunlast = $tahun - 1;
        } else if ($bulan == 2) {
            $batasbulan = 12;
            $tahunlast = $tahun - 1;
        } else {
            $batasbulan = $bulan - 2;
            $tahunlast = $tahun;
        }

        $ceknextBulan = DB::table('setoran_pusat')->where('omset_bulan', $bulan)->where('omset_tahun', $tahun)
            ->whereRaw('MONTH(tgl_diterimapusat) = ' . $bln)
            ->whereRaw('YEAR(tgl_diterimapusat) = ' . $thn)
            ->where('kode_cabang', $kode_cabang)
            ->orderBy('tgl_diterimapusat', 'desc')
            ->first();
        if ($ceknextBulan ==  null) {
            $sampaibayar = $sampai;
        } else {
            $sampaibayar = $ceknextBulan->tgl_diterimapusat;
        }

        $tglbatas = $tahunlast . "-" . $batasbulan . "-01";
        $cabang = Cabang::where('kode_cabang', $kode_cabang)->first();
        $rekapbg = DB::table('giro')
            ->selectRaw("tgl_giro,penjualan.id_karyawan,nama_karyawan,giro.no_fak_penj,nama_pelanggan,namabank,no_giro,tglcair as jatuhtempo,jumlah,tglbayar as tgl_pencairan")
            ->join('penjualan', 'giro.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->leftJoin(
                DB::raw("(SELECT id_giro,tglbayar FROM historibayar WHERE tglbayar BETWEEN '$dari' AND '$sampaibayar' GROUP BY id_giro,tglbayar) hb"),
                function ($join) {
                    $join->on('giro.id_giro', '=', 'hb.id_giro');
                }
            )
            ->whereBetween('tgl_giro', [$dari, $sampai])
            ->where('karyawan.kode_cabang', $kode_cabang)
            ->orWhere('omset_bulan', $bulan)
            ->where('omset_tahun', $tahun)
            ->where('karyawan.kode_cabang', $kode_cabang)
            ->orWhereBetween('tgl_giro', [$tglbatas, $sampai])
            ->where('omset_bulan', 0)
            ->where('karyawan.kode_cabang', $kode_cabang)
            ->orWhereBetween('tgl_giro', [$tglbatas, $sampai])
            ->where('omset_bulan', '>', $bulan)
            ->where('omset_tahun', $tahun)
            ->where('karyawan.kode_cabang', $kode_cabang)
            ->orderBy('tgl_giro')
            ->orderBy('no_giro')
            ->get();
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekap BG $dari-$sampai.xls");
        }
        return view('giro.laporan.cetak_rekapbg', compact('dari', 'sampai', 'cabang', 'rekapbg', 'bulan', 'tahun', 'namabulan'));
    }

    public function saldokasbesar()
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('kasbesar.laporan.frm.lap_saldokasbesar', compact('cabang', 'bulan'));
    }

    public function cetak_saldokasbesar(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $dari = $tahun . "-" . $bulan . "-01";
        $tgl_akhirsetoran = date("Y-m-t", strtotime($dari));
        if ($bulan == 12) {
            $bln = 1;
            $thn = $tahun + 1;
        } else {
            $bln = $bulan + 1;
            $thn = $tahun;
        }

        $ceknextBulan = DB::table('setoran_pusat')->where('omset_bulan', $bulan)->where('omset_tahun', $tahun)
            ->whereRaw('MONTH(tgl_diterimapusat) = ' . $bln)
            ->whereRaw('YEAR(tgl_diterimapusat) = ' . $thn)
            ->where('kode_cabang', $kode_cabang)
            ->orderBy('tgl_diterimapusat', 'desc')
            ->first();
        if ($ceknextBulan ==  null) {
            $sampai = $tgl_akhirsetoran;
        } else {
            $sampai = $ceknextBulan->tgl_diterimapusat;
        }

        $saldokasbesar = DB::table('saldoawal_kasbesar')
            ->select('uang_logam', 'uang_kertas', 'giro', 'transfer')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('kode_cabang', $kode_cabang)->first();
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $cabang = Cabang::where('kode_cabang', $kode_cabang)->first();
        if (isset($_POST['export'])) {
            echo "EXPORT";
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Saldo Kas Besar Cabang $cabang->nama_cabang Periode Bulan $namabulan[$bulan] $tahun.xls");
        }
        return view('kasbesar.laporan.cetak_saldokasbesar', compact('dari', 'sampai', 'saldokasbesar', 'tgl_akhirsetoran', 'cabang', 'namabulan', 'bulan', 'tahun', 'kode_cabang'));
    }

    public function lpu()
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('kasbesar.laporan.frm.lap_lpu', compact('cabang', 'bulan'));
    }

    public function cetak_lpu(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $showallsales = $request->showallsales;
        $format = $request->format;

        if ($bulan == 12) {
            $bln = 1;
            $thn = $tahun + 1;
        } else {
            $bln = $bulan + 1;
            $thn = $tahun;
        }

        if ($bulan == 1) {
            $blnbefore = 12;
            $thnbefore = $tahun - 1;
        } else {
            $blnbefore = $bulan - 1;
            $thnbefore = $tahun;
        }

        $ceknextBulan = DB::table('setoran_pusat')->where('omset_bulan', $bulan)->where('omset_tahun', $tahun)
            ->whereRaw('MONTH(tgl_diterimapusat) = ' . $bln)
            ->whereRaw('YEAR(tgl_diterimapusat) = ' . $thn)
            ->where('kode_cabang', $kode_cabang)
            ->orderBy('tgl_diterimapusat', 'desc')
            ->first();

        $cekbeforeBulan = DB::table('setoran_pusat')->where('omset_bulan', $bulan)->where('omset_tahun', $tahun)
            ->whereRaw('MONTH(tgl_diterimapusat) = ' . $blnbefore)
            ->whereRaw('YEAR(tgl_diterimapusat) = ' . $thnbefore)
            ->where('kode_cabang', $kode_cabang)
            ->orderBy('tgl_diterimapusat', 'desc')
            ->first();

        if ($ceknextBulan ==  null) {
            $sampai = $sampai;
        } else {
            $sampai = $ceknextBulan->tgl_diterimapusat;
        }

        if ($cekbeforeBulan ==  null) {
            $fromlast = $dari;
        } else {
            $fromlast = $cekbeforeBulan->tgl_diterimapusat;
        }

        $qsalesman = Salesman::query();
        if (empty($showallsales)) {
            $qsalesman->where('status_aktif_sales', 1);
        }
        $qsalesman->where('nama_karyawan', '!=', '-');
        $qsalesman->where('kode_cabang', $kode_cabang);
        $qsalesman->orderBy('nama_karyawan');
        $salesman = $qsalesman->get();
        $jmlsales = $qsalesman->count();

        $qbank = Bank::where('kode_cabang', 'PST');
        $bank = $qbank->get();
        $jmlbank = $qbank->count();
        $cabang = Cabang::where('kode_cabang', $kode_cabang)->first();
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if (isset($_POST['export'])) {
            echo "EXPORT";
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=LPU Cabang $cabang->nama_cabang Periode Bulan $namabulan[$bulan] $tahun.xls");
        }
        if ($format == 2) {
            return view('kasbesar.laporan.cetak_lpu_2', compact('salesman', 'bank', 'jmlbank', 'jmlsales', 'cabang', 'bulan', 'tahun', 'namabulan', 'dari', 'sampai', 'kode_cabang'));
        } else {
            return view('kasbesar.laporan.cetak_lpu', compact('salesman', 'bank', 'jmlbank', 'jmlsales', 'cabang', 'bulan', 'tahun', 'namabulan', 'dari', 'sampai', 'kode_cabang'));
        }
    }
}