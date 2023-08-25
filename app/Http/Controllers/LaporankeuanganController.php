<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Cabang;
use App\Models\Karyawan;
use App\Models\Kasbon;
use App\Models\Kaskecil;
use App\Models\Ledger;
use App\Models\Pinjaman;
use App\Models\Piutangkaryawan;
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
        $cbg = new Cabang();
        $cabang = $cbg->getCabanggudang($this->cabang);
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
        if ($kode_cabang != "") {
            $query->where('kaskecil_detail.kode_cabang', $kode_cabang);
        }
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
        if (!empty($kode_cabang)) {
            $queryrekap->where('kaskecil_detail.kode_cabang', $kode_cabang);
        }
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

        if ($request->jenislaporan == "detail") {
            return view('kaskecil.laporan.cetak_kaskecil', compact('kaskecil', 'cabang', 'saldoawal', 'dari', 'sampai', 'dari_kode_akun', 'sampai_kode_akun', 'rekap'));
        } else {
            return view('kaskecil.laporan.cetak_rekapkaskecil', compact('kaskecil', 'cabang', 'saldoawal', 'dari', 'sampai', 'dari_kode_akun', 'sampai_kode_akun', 'rekap'));
        }
    }


    public function ledger()
    {
        $role = ['admin', 'direktur', 'general manager', 'manager accounting', 'staff keuangan', 'spv accounting', 'staff keuangan 3'];
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
                if (Auth::user()->id == 115) {
                    $list = ['BNI CV', 'BNI TGL', 'BNI SKB', 'BNI SBY', 'BNI PWT', 'BNI PWK', 'BNI KLT', 'BNI BTN', 'BNI BGR', 'BNI SMR', 'BNI BDG', 'BNI BKI', 'BNI GRT'];
                    $bank = DB::table('master_bank')->whereIn('kode_bank', $list)->orderBy('kode_bank')->get();
                } else {
                    $bank = DB::table('master_bank')->where('kode_cabang', $this->cabang)->orderBy('kode_bank')->get();
                }
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
        $management = config('global.show_for_hrd');
        $bank = Bank::where('kode_bank', $kode_bank)->first();
        if ($bank == null) {
            $namabank = "";
        } else {
            $namabank = $bank->nama_bank;
        }
        if ($jenislaporan == "detail") {
            $query = Ledger::query();
            $query->select('ledger_bank.*', 'nama_akun', 'nama_bank', 'id_jabatan');
            $query->join('coa', 'ledger_bank.kode_akun', '=', 'coa.kode_akun');
            $query->join('master_bank', 'ledger_bank.bank', '=', 'master_bank.kode_bank');
            $query->leftJoin('pinjaman', 'ledger_bank.no_ref', '=', 'pinjaman.no_pinjaman');
            $query->leftJoin('master_karyawan', 'pinjaman.nik', '=', 'master_karyawan.nik');
            $query->orderBy('tgl_ledger');
            $query->orderBy('date_created');
            $query->whereBetween('tgl_ledger', [$request->dari, $request->sampai]);
            if ($kode_bank != "") {
                $query->where('ledger_bank.bank', $kode_bank);
            }
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
                header("Content-Disposition: attachment; filename=Ledger  Periode $dari-$sampai.xls");
            }
            return view('ledger.laporan.cetak_ledger', compact('ledger', 'saldoawal', 'bank', 'dari', 'sampai', 'management'));
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
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
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
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
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
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
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
            ->selectRaw("tgl_giro,salesbarunew,nama_karyawan,giro.no_fak_penj,nama_pelanggan,namabank,no_giro,tglcair as jatuhtempo,jumlah,tglbayar as tgl_pencairan")
            ->join('penjualan', 'giro.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->leftJoin(
                DB::raw("(
                    SELECT pj.no_fak_penj,
                    IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
                    IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
                    FROM penjualan pj
                    INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                    LEFT JOIN (
                        SELECT
                        id_move,no_fak_penj,
                        move_faktur.id_karyawan as salesbaru,
                        karyawan.kode_cabang  as cabangbaru
                        FROM move_faktur
                        INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                        WHERE id_move IN (SELECT max(id_move) FROM move_faktur WHERE tgl_move <= '$dari' GROUP BY no_fak_penj)
                    ) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
                ) pjmove"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'pjmove.no_fak_penj');
                }
            )
            ->join('karyawan', 'pjmove.salesbarunew', '=', 'karyawan.id_karyawan')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->leftJoin(
                DB::raw("(SELECT id_giro,tglbayar FROM historibayar WHERE tglbayar BETWEEN '$dari' AND '$sampaibayar' GROUP BY id_giro,tglbayar) hb"),
                function ($join) {
                    $join->on('giro.id_giro', '=', 'hb.id_giro');
                }
            )
            ->whereBetween('tgl_giro', [$dari, $sampai])
            ->where('cabangbarunew', $kode_cabang)
            ->orWhere('omset_bulan', $bulan)
            ->where('omset_tahun', $tahun)
            ->where('cabangbarunew', $kode_cabang)
            ->orWhereBetween('tgl_giro', [$tglbatas, $sampai])
            ->where('omset_bulan', 0)
            ->where('cabangbarunew', $kode_cabang)
            ->orWhereBetween('tgl_giro', [$tglbatas, $sampai])
            ->where('omset_bulan', '>', $bulan)
            ->where('omset_tahun', $tahun)
            ->where('cabangbarunew', $kode_cabang)
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
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('kasbesar.laporan.frm.lap_saldokasbesar', compact('cabang', 'bulan'));
    }

    public function cetak_saldokasbesar(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $dari  = $tahun . "-" . $bulan . "-01";
        $darilast  = $tahun . "-" . $bulan - 1 . "-01";
        $sampailast = date("Y-m-t", strtotime($darilast));
        $daripenerimaan = $dari;
        $tgl_akhirsetoran = date("Y-m-t", strtotime($dari));
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
        if ($ceknextBulan ==  null) {
            $sampai = $tgl_akhirsetoran;
        } else {
            $sampai = $ceknextBulan->tgl_diterimapusat;
        }


        $cekbeforeBulan = DB::table('setoran_pusat')->where('omset_bulan', $bulan)->where('omset_tahun', $tahun)
            ->whereRaw('MONTH(tgl_setoranpusat) = ' . $blnbefore)
            ->whereRaw('YEAR(tgl_setoranpusat) = ' . $thnbefore)
            ->where('kode_cabang', $kode_cabang)
            ->orderBy('tgl_setoranpusat', 'asc')
            ->first();
        if ($cekbeforeBulan ==  null) {
            $dari = $dari;
        } else {
            $dari = $cekbeforeBulan->tgl_setoranpusat;
        }

        // if ($daripenerimaan > $dari) {

        // } else {
        //     $daripenerimaan = $sampailast;
        // }
        $daripenerimaan = $sampailast;
        // dd($cekbeforeBulan);
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
        return view('kasbesar.laporan.cetak_saldokasbesar', compact('dari', 'sampai', 'saldokasbesar', 'tgl_akhirsetoran', 'cabang', 'namabulan', 'bulan', 'tahun', 'kode_cabang', 'daripenerimaan'));
    }

    public function lpu()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
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


    public function pinjaman()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        $departemen = DB::table('hrd_departemen')->get();
        return view('penjualan.laporan.frm.lap_pinjaman', compact('cabang', 'departemen'));
    }


    public function piutangkaryawan()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        $departemen = DB::table('hrd_departemen')->get();
        return view('penjualan.laporan.frm.lap_piutangkaryawan', compact('cabang', 'departemen'));
    }


    public function cetak_piutangkaryawan(Request $request)
    {
        $id_kantor = $request->id_kantor;
        $kode_dept = $request->kode_dept;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $query = Piutangkaryawan::query();
        $query->select('pinjaman_nonpjp.*', 'nama_karyawan', 'nama_jabatan', 'nama_dept', 'totalpembayaran', 'id_jabatan');
        $query->join('master_karyawan', 'pinjaman_nonpjp.nik', '=', 'master_karyawan.nik');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->leftJoin(
            DB::raw("(
            SELECT no_pinjaman_nonpjp,SUM(jumlah) as totalpembayaran FROM pinjaman_nonpjp_historibayar GROUP BY no_pinjaman_nonpjp
        ) hb"),
            function ($join) {
                $join->on('pinjaman_nonpjp.no_pinjaman_nonpjp', '=', 'hb.no_pinjaman_nonpjp');
            }
        );
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_pinjaman', [$request->dari, $request->sampai]);
        }

        if (!empty($request->id_kantor)) {
            $query->where('master_karyawan.id_kantor', $request->id_kantor);
        }

        if (!empty($request->kode_dept)) {
            $query->where('master_karyawan.kode_dept', $request->kode_dept);
        }

        $pinjaman = $query->get();

        $departemen = DB::table('hrd_departemen')->where('kode_dept', $kode_dept)->first();
        $kantor = DB::table('cabang')->where('kode_cabang', $id_kantor)->first();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Pinjaman $dari-$sampai.xls");
        }

        //dd($pinjaman);
        return view('piutangkaryawan.laporan.cetak', compact('pinjaman', 'departemen', 'kantor', 'dari', 'sampai'));
    }
    public function cetak_pinjaman(Request $request)
    {
        $id_kantor = $request->id_kantor;
        $kode_dept = $request->kode_dept;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $level = Auth::user()->level;
        $cabang = Auth::user()->kode_cabang;
        $show_for_hrd = config('global.show_for_hrd');
        $show_for_hrd_2 = config('global.show_for_hrd_2');
        $level_show_all = config('global.show_all');

        $query = Pinjaman::query();
        $query->select('pinjaman.*', 'nama_karyawan', 'nama_jabatan', 'nama_dept', 'totalpembayaran', 'id_jabatan');
        $query->join('master_karyawan', 'pinjaman.nik', '=', 'master_karyawan.nik');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->leftJoin(
            DB::raw("(
            SELECT no_pinjaman,SUM(jumlah) as totalpembayaran FROM pinjaman_historibayar GROUP BY no_pinjaman
        ) hb"),
            function ($join) {
                $join->on('pinjaman.no_pinjaman', '=', 'hb.no_pinjaman');
            }
        );
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_pinjaman', [$request->dari, $request->sampai]);
        }

        if (!empty($request->id_kantor)) {
            $query->where('master_karyawan.id_kantor', $request->id_kantor);
        }

        if (!empty($request->kode_dept)) {
            $query->where('master_karyawan.kode_dept', $request->kode_dept);
        }

        if ($level == "kepala admin") {
            $query->where('master_karyawan.id_kantor', $cabang);
            $query->where('master_karyawan.id_perusahaan', "MP");
            $query->where('nama_jabatan', '!=', 'KEPALA ADMIN');
        }

        if ($level == "kepala penjualan") {
            if (Auth::user()->id == "27") {
                $query->whereIn('master_karyawan.id_kantor', [$cabang, 'PWK']);
            } else {
                $query->where('master_karyawan.id_kantor', $cabang);
            }
            $query->where('nama_jabatan', '!=', 'KEPALA PENJUALAN');
            $query->where('master_karyawan.id_perusahaan', "PCF");
        }

        if ($level == "manager pembelian") {
            $query->where('master_karyawan.kode_dept', 'PMB');
        }

        if ($level == "kepala gudang") {
            $query->where('master_karyawan.kode_dept', 'GDG');
            $query->whereNotIN('nama_jabatan', ['MANAGER', 'ASST. MANAGER']);
        }

        if ($level == "spv produksi") {
            $query->where('master_karyawan.kode_dept', 'PRD');
            $query->whereNotIN('nama_jabatan', ['MANAGER', 'SUPERVISOR']);
        }

        if ($level == "manager produksi") {
            $query->whereIn('master_karyawan.kode_dept', ['PRD', 'MTC']);
            $query->where('nama_jabatan', '!=', 'MANAGER');
        }

        if ($level == "manager ga") {
            $query->where('master_karyawan.kode_dept', 'GAF');
        }

        if ($level == "emf") {
            $query->whereIn('master_karyawan.kode_dept', ['PMB', 'PRD', 'GAF', 'GDG', 'HRD', 'PDQ']);
        }


        if ($level == "manager marketing") {
            $query->where('master_karyawan.kode_dept', 'MKT');
            $query->where('nama_jabatan', 'REGIONAL SALES MANAGER');
        }

        if ($level == "rsm") {
            $list_wilayah = Auth::user()->wilayah != null ? unserialize(Auth::user()->wilayah) : NULL;
            $wilayah = $list_wilayah != null ? "'" . implode("', '", $list_wilayah) . "'" : '';
            $query->whereIn('master_karyawan.id_kantor', $list_wilayah);
            $query->where('master_karyawan.kode_dept', 'MKT');
            $query->where('nama_jabatan', 'KEPALA PENJUALAN');
            $query->where('id_perusahaan', 'PCF');
        }

        if (!in_array($level, $level_show_all)) {
            $query->whereNotIn('id_jabatan', $show_for_hrd);
        }


        if ($level == "admin pdqc") {
            $listkaryawan = [
                '08.12.100',
                '11.10.090',
                '13.02.198',
                '91.01.016',
                '03.04.045',
                '08.05.042',
                '12.09.182',
                '05.01.055',
                '13.03.202'
            ];

            $query->whereIn('pinjaman.nik', $listkaryawan);
        }


        if ($level == "spv pdqc") {
            $listkaryawan = [
                '13.03.200',
                '14.08.220',
                '13.07.021',
                '15.05.174',
                '10.08.128',
                '13.09.206',
                '13.09.209',
                '19.09.303',
                '21.06.304',
                '16.01.069',
                '18.03.305'
            ];

            $query->whereIn('pinjaman.nik', $listkaryawan);
        }

        if ($level == "manager audit") {
            $query->where('master_karyawan.kode_dept', 'ADT');
        }
        $pinjaman = $query->get();

        $departemen = DB::table('hrd_departemen')->where('kode_dept', $kode_dept)->first();
        $kantor = DB::table('cabang')->where('kode_cabang', $id_kantor)->first();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Pinjaman $dari-$sampai.xls");
        }
        return view('pinjaman.laporan.cetak', compact('pinjaman', 'departemen', 'kantor', 'dari', 'sampai', 'show_for_hrd'));
    }


    public function cetak_kartupinjaman(Request $request)
    {

        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $id_kantor = $request->id_kantor;
        $kode_dept = $request->kode_dept;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        if ($bulan == 12) {
            $bulanpotongan = 1;
            $tahunpotongan = $tahun + 1;
        } else {
            $bulanpotongan = $bulan + 1;
            $tahunpotongan = $tahun;
        }

        $tglgajidari = $tahun . "-" . $bulan . "-01";
        $tglgajisampai = date("Y-m-t", strtotime($tglgajidari));
        $tglpotongan = $tahunpotongan . "-" . $bulanpotongan . "-01";
        $level = Auth::user()->level;
        $cabang = Auth::user()->kode_cabang;
        $show_for_hrd = config('global.show_for_hrd');
        $show_for_hrd_2 = config('global.show_for_hrd_2');
        $level_show_all = config('global.show_all');

        $query = Pinjaman::query();
        $query->selectRaw("pinjaman.nik, nama_karyawan,
        SUM(IF(tgl_pinjaman < '$tglgajidari',jumlah_pinjaman,0)) as jumlah_pinjamanlast,
        SUM(totalpembayaranlast) as total_pembayaranlast,
        SUM(totalpelunasanlast) as total_pelunasanlast,
        SUM(IF(tgl_pinjaman BETWEEN '$tglgajidari' AND '$tglgajisampai',jumlah_pinjaman,0)) as jumlah_pinjamannow,
        SUM(totalpembayarannow) as total_pembayarannow,
        SUM(totalpelunasannow) as total_pelunasannow
        ");
        $query->join('master_karyawan', 'pinjaman.nik', '=', 'master_karyawan.nik');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->leftJoin(
            DB::raw("(
            SELECT no_pinjaman,SUM(jumlah) as totalpembayaranlast FROM pinjaman_historibayar
            WHERE tgl_bayar < '$tglpotongan' AND kode_potongan IS NOT NULL
            GROUP BY no_pinjaman
        ) hb"),
            function ($join) {
                $join->on('pinjaman.no_pinjaman', '=', 'hb.no_pinjaman');
            }
        );

        $query->leftJoin(
            DB::raw("(
            SELECT no_pinjaman,SUM(jumlah) as totalpelunasanlast FROM pinjaman_historibayar
            WHERE tgl_bayar < '$tglgajidari' AND kode_potongan IS NULL
            GROUP BY no_pinjaman
        ) hbpllast"),
            function ($join) {
                $join->on('pinjaman.no_pinjaman', '=', 'hbpllast.no_pinjaman');
            }
        );

        $query->leftJoin(
            DB::raw("(
            SELECT no_pinjaman,SUM(jumlah) as totalpelunasannow FROM pinjaman_historibayar
            WHERE tgl_bayar BETWEEN '$tglgajidari' AND '$tglgajisampai' AND kode_potongan IS NULL
            GROUP BY no_pinjaman
        ) hbplnow"),
            function ($join) {
                $join->on('pinjaman.no_pinjaman', '=', 'hbplnow.no_pinjaman');
            }
        );
        $query->leftJoin(
            DB::raw("(
            SELECT no_pinjaman,SUM(jumlah) as totalpembayarannow FROM pinjaman_historibayar
            WHERE tgl_bayar = '$tglpotongan' AND kode_potongan IS NOT NULL
            GROUP BY no_pinjaman
        ) hbnow"),
            function ($join) {
                $join->on('pinjaman.no_pinjaman', '=', 'hbnow.no_pinjaman');
            }
        );
        $query->where('tgl_pinjaman', '<=', $tglgajisampai);

        if (!empty($request->id_kantor)) {
            $query->where('master_karyawan.id_kantor', $request->id_kantor);
        }

        if (!empty($request->kode_dept)) {
            $query->where('master_karyawan.kode_dept', $request->kode_dept);
        }

        if ($level == "kepala admin") {
            $query->where('master_karyawan.id_kantor', $cabang);
            $query->where('master_karyawan.id_perusahaan', "MP");
            $query->where('nama_jabatan', '!=', 'KEPALA ADMIN');
        }

        if ($level == "kepala penjualan") {
            if (Auth::user()->id == "27") {
                $query->whereIn('master_karyawan.id_kantor', [$cabang, 'PWK']);
            } else {
                $query->where('master_karyawan.id_kantor', $cabang);
            }
            $query->where('nama_jabatan', '!=', 'KEPALA PENJUALAN');
            $query->where('master_karyawan.id_perusahaan', "PCF");
        }

        if ($level == "manager pembelian") {
            $query->where('master_karyawan.kode_dept', 'PMB');
        }

        if ($level == "kepala gudang") {
            $query->where('master_karyawan.kode_dept', 'GDG');
            $query->whereNotIN('nama_jabatan', ['MANAGER', 'ASST. MANAGER']);
        }

        if ($level == "spv produksi") {
            $query->where('master_karyawan.kode_dept', 'PRD');
            $query->whereNotIN('nama_jabatan', ['MANAGER', 'SUPERVISOR']);
        }

        if ($level == "manager produksi") {
            $query->whereIn('master_karyawan.kode_dept', ['PRD', 'MTC']);
            $query->where('nama_jabatan', '!=', 'MANAGER');
        }

        if ($level == "manager ga") {
            $query->where('master_karyawan.kode_dept', 'GAF');
        }

        if ($level == "emf") {
            $query->whereIn('master_karyawan.kode_dept', ['PMB', 'PRD', 'GAF', 'GDG', 'HRD', 'PDQ']);
        }


        if ($level == "manager marketing") {
            $query->where('master_karyawan.kode_dept', 'MKT');
            $query->where('nama_jabatan', 'REGIONAL SALES MANAGER');
        }

        if ($level == "rsm") {
            $list_wilayah = Auth::user()->wilayah != null ? unserialize(Auth::user()->wilayah) : NULL;
            $wilayah = $list_wilayah != null ? "'" . implode("', '", $list_wilayah) . "'" : '';
            $query->whereIn('master_karyawan.id_kantor', $list_wilayah);
            $query->where('master_karyawan.kode_dept', 'MKT');
            $query->where('nama_jabatan', 'KEPALA PENJUALAN');
            $query->where('id_perusahaan', 'PCF');
        }

        if (!in_array($level, $level_show_all)) {
            $query->whereNotIn('id_jabatan', $show_for_hrd);
        }


        if ($level == "admin pdqc") {
            $listkaryawan = [
                '08.12.100',
                '11.10.090',
                '13.02.198',
                '91.01.016',
                '03.04.045',
                '08.05.042',
                '12.09.182',
                '05.01.055',
                '13.03.202'
            ];

            $query->whereIn('pinjaman.nik', $listkaryawan);
        }


        if ($level == "spv pdqc") {
            $listkaryawan = [
                '13.03.200',
                '14.08.220',
                '13.07.021',
                '15.05.174',
                '10.08.128',
                '13.09.206',
                '13.09.209',
                '19.09.303',
                '21.06.304',
                '16.01.069',
                '18.03.305'
            ];

            $query->whereIn('pinjaman.nik', $listkaryawan);
        }

        if ($level == "manager audit") {
            $query->where('master_karyawan.kode_dept', 'ADT');
        }

        $query->groupByRaw('pinjaman.nik,nama_karyawan');
        $query->orderBy('nama_karyawan');
        $pinjaman = $query->get();

        $departemen = DB::table('hrd_departemen')->where('kode_dept', $kode_dept)->first();
        $kantor = DB::table('cabang')->where('kode_cabang', $id_kantor)->first();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Kartu Pinjaman.xls");
        }
        return view('pinjaman.laporan.cetak_kartupinjaman', compact('pinjaman', 'departemen', 'kantor', 'show_for_hrd', 'namabulan', 'bulan', 'tahun'));
    }


    public function cetak_kartupiutangkaryawan(Request $request)
    {

        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $id_kantor = $request->id_kantor;
        $kode_dept = $request->kode_dept;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        if ($bulan == 12) {
            $bulanpotongan = 1;
            $tahunpotongan = $tahun + 1;
        } else {
            $bulanpotongan = $bulan + 1;
            $tahunpotongan = $tahun;
        }

        $tglgajidari = $tahun . "-" . $bulan . "-01";
        $tglgajisampai = date("Y-m-t", strtotime($tglgajidari));
        $tglpotongan = $tahunpotongan . "-" . $bulanpotongan . "-01";
        $level = Auth::user()->level;
        $cabang = Auth::user()->kode_cabang;


        $query = Piutangkaryawan::query();
        $query->selectRaw("pinjaman_nonpjp.nik, nama_karyawan,
        SUM(IF(tgl_pinjaman < '$tglgajidari',jumlah_pinjaman,0)) as jumlah_pinjamanlast,
        SUM(totalpembayaranlast) as total_pembayaranlast,
        SUM(totalpelunasanlast) as total_pelunasanlast,
        SUM(IF(tgl_pinjaman BETWEEN '$tglgajidari' AND '$tglgajisampai',jumlah_pinjaman,0)) as jumlah_pinjamannow,
        SUM(totalpembayarannow) as total_pembayarannow,
        SUM(totalpembayaranpotongkomisi) as total_pembayaranpotongkomisi,
        SUM(totalpembayarantitipan) as total_pembayarantitipan,
        SUM(totalpelunasannow) as total_pelunasannow
        ");
        $query->join('master_karyawan', 'pinjaman_nonpjp.nik', '=', 'master_karyawan.nik');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->leftJoin(
            DB::raw("(
            SELECT no_pinjaman_nonpjp,SUM(jumlah) as totalpembayaranlast FROM pinjaman_nonpjp_historibayar
            WHERE tgl_bayar < '$tglpotongan' AND kode_potongan IS NOT NULL
            GROUP BY no_pinjaman_nonpjp
        ) hb"),
            function ($join) {
                $join->on('pinjaman_nonpjp.no_pinjaman_nonpjp', '=', 'hb.no_pinjaman_nonpjp');
            }
        );

        $query->leftJoin(
            DB::raw("(
            SELECT no_pinjaman_nonpjp,SUM(jumlah) as totalpelunasanlast FROM pinjaman_nonpjp_historibayar
            WHERE tgl_bayar < '$tglgajidari' AND kode_potongan IS NULL
            GROUP BY no_pinjaman_nonpjp
        ) hbpllast"),
            function ($join) {
                $join->on('pinjaman_nonpjp.no_pinjaman_nonpjp', '=', 'hbpllast.no_pinjaman_nonpjp');
            }
        );

        $query->leftJoin(
            DB::raw("(
            SELECT no_pinjaman_nonpjp,SUM(jumlah) as totalpelunasannow FROM pinjaman_nonpjp_historibayar
            WHERE tgl_bayar BETWEEN '$tglgajidari' AND '$tglgajisampai' AND kode_potongan IS NULL
            GROUP BY no_pinjaman_nonpjp
        ) hbplnow"),
            function ($join) {
                $join->on('pinjaman_nonpjp.no_pinjaman_nonpjp', '=', 'hbplnow.no_pinjaman_nonpjp');
            }
        );
        $query->leftJoin(
            DB::raw("(
            SELECT no_pinjaman_nonpjp,
            SUM(IF(jenis_bayar=1,jumlah,0)) as totalpembayarannow,
            SUM(IF(jenis_bayar=2,jumlah,0)) as totalpembayaranpotongkomisi,
            SUM(IF(jenis_bayar=3,jumlah,0)) as totalpembayarantitipan
            FROM pinjaman_nonpjp_historibayar
            WHERE tgl_bayar = '$tglpotongan'
            GROUP BY no_pinjaman_nonpjp
        ) hbnow"),
            function ($join) {
                $join->on('pinjaman_nonpjp.no_pinjaman_nonpjp', '=', 'hbnow.no_pinjaman_nonpjp');
            }
        );
        $query->where('tgl_pinjaman', '<=', $tglgajisampai);

        if (!empty($request->id_kantor)) {
            $query->where('master_karyawan.id_kantor', $request->id_kantor);
        }

        if (!empty($request->kode_dept)) {
            $query->where('master_karyawan.kode_dept', $request->kode_dept);
        }

        $query->groupByRaw('pinjaman_nonpjp.nik,nama_karyawan');
        // $query->orderBy('pinjaman_nonpjp.no_pinjaman_nonpjp');
        $pinjaman = $query->get();

        $departemen = DB::table('hrd_departemen')->where('kode_dept', $kode_dept)->first();
        $kantor = DB::table('cabang')->where('kode_cabang', $id_kantor)->first();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Kartu Pinjaman.xls");
        }
        return view('piutangkaryawan.laporan.cetak_kartupiutangkaryawan', compact('pinjaman', 'departemen', 'kantor', 'namabulan', 'bulan', 'tahun'));
    }

    public function cetak_kartukasbon(Request $request)
    {

        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $id_kantor = $request->id_kantor;
        $kode_dept = $request->kode_dept;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        if ($bulan == 12) {
            $bulanpotongan = 1;
            $tahunpotongan = $tahun + 1;
        } else {
            $bulanpotongan = $bulan + 1;
            $tahunpotongan = $tahun;
        }

        $tglgajidari = $tahun . "-" . $bulan . "-01";
        $tglgajisampai = date("Y-m-t", strtotime($tglgajidari));
        $tglpotongan = $tahunpotongan . "-" . $bulanpotongan . "-01";
        $level = Auth::user()->level;
        $cabang = Auth::user()->kode_cabang;
        $show_for_hrd = config('global.show_for_hrd');
        $show_for_hrd_2 = config('global.show_for_hrd_2');
        $level_show_all = config('global.show_all');

        $query = Kasbon::query();
        $query->selectRaw("kasbon.nik, nama_karyawan,
        SUM(IF(tgl_kasbon < '$tglgajidari',jumlah_kasbon,0)) as jumlah_kasbonlast,
        SUM(totalpembayaranlast) as total_pembayaranlast,
        SUM(totalpelunasanlast) as total_pelunasanlast,
        SUM(IF(tgl_kasbon BETWEEN '$tglgajidari' AND '$tglgajisampai',jumlah_kasbon,0)) as jumlah_kasbonnow,
        SUM(totalpembayarannow) as total_pembayarannow,
        SUM(totalpelunasannow) as total_pelunasannow
        ");
        $query->join('master_karyawan', 'kasbon.nik', '=', 'master_karyawan.nik');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->leftJoin(
            DB::raw("(
            SELECT no_kasbon,SUM(jumlah) as totalpembayaranlast FROM kasbon_historibayar
            WHERE tgl_bayar < '$tglpotongan' AND kode_potongan IS NOT NULL
            GROUP BY no_kasbon
        ) hb"),
            function ($join) {
                $join->on('kasbon.no_kasbon', '=', 'hb.no_kasbon');
            }
        );

        $query->leftJoin(
            DB::raw("(
            SELECT no_kasbon,SUM(jumlah) as totalpelunasanlast FROM kasbon_historibayar
            WHERE tgl_bayar < '$tglgajidari' AND kode_potongan IS NULL
            GROUP BY no_kasbon
        ) hbpllast"),
            function ($join) {
                $join->on('kasbon.no_kasbon', '=', 'hbpllast.no_kasbon');
            }
        );

        $query->leftJoin(
            DB::raw("(
            SELECT no_kasbon,SUM(jumlah) as totalpelunasannow FROM kasbon_historibayar
            WHERE tgl_bayar BETWEEN '$tglgajidari' AND '$tglgajisampai' AND kode_potongan IS NULL
            GROUP BY no_kasbon
        ) hbplnow"),
            function ($join) {
                $join->on('kasbon.no_kasbon', '=', 'hbplnow.no_kasbon');
            }
        );
        $query->leftJoin(
            DB::raw("(
            SELECT no_kasbon,SUM(jumlah) as totalpembayarannow FROM kasbon_historibayar
            WHERE tgl_bayar = '$tglpotongan' AND kode_potongan IS NOT NULL
            GROUP BY no_kasbon
        ) hbnow"),
            function ($join) {
                $join->on('kasbon.no_kasbon', '=', 'hbnow.no_kasbon');
            }
        );
        $query->where('tgl_kasbon', '<=', $tglgajisampai);

        if (!empty($request->id_kantor)) {
            $query->where('master_karyawan.id_kantor', $request->id_kantor);
        }

        if (!empty($request->kode_dept)) {
            $query->where('master_karyawan.kode_dept', $request->kode_dept);
        }

        if ($level == "kepala admin") {
            $query->where('master_karyawan.id_kantor', $cabang);
            $query->where('master_karyawan.id_perusahaan', "MP");
            $query->where('nama_jabatan', '!=', 'KEPALA ADMIN');
        }

        if ($level == "kepala penjualan") {
            if (Auth::user()->id == "27") {
                $query->whereIn('master_karyawan.id_kantor', [$cabang, 'PWK']);
            } else {
                $query->where('master_karyawan.id_kantor', $cabang);
            }
            $query->where('nama_jabatan', '!=', 'KEPALA PENJUALAN');
            $query->where('master_karyawan.id_perusahaan', "PCF");
        }

        if ($level == "manager pembelian") {
            $query->where('master_karyawan.kode_dept', 'PMB');
        }

        if ($level == "kepala gudang") {
            $query->where('master_karyawan.kode_dept', 'GDG');
            $query->whereNotIN('nama_jabatan', ['MANAGER', 'ASST. MANAGER']);
        }

        if ($level == "spv produksi") {
            $query->where('master_karyawan.kode_dept', 'PRD');
            $query->whereNotIN('nama_jabatan', ['MANAGER', 'SUPERVISOR']);
        }

        if ($level == "manager produksi") {
            $query->whereIn('master_karyawan.kode_dept', ['PRD', 'MTC']);
            $query->where('nama_jabatan', '!=', 'MANAGER');
        }

        if ($level == "manager ga") {
            $query->where('master_karyawan.kode_dept', 'GAF');
        }

        if ($level == "emf") {
            $query->whereIn('master_karyawan.kode_dept', ['PMB', 'PRD', 'GAF', 'GDG', 'HRD', 'PDQ']);
        }


        if ($level == "manager marketing") {
            $query->where('master_karyawan.kode_dept', 'MKT');
            $query->where('nama_jabatan', 'REGIONAL SALES MANAGER');
        }

        if ($level == "rsm") {
            $list_wilayah = Auth::user()->wilayah != null ? unserialize(Auth::user()->wilayah) : NULL;
            $wilayah = $list_wilayah != null ? "'" . implode("', '", $list_wilayah) . "'" : '';
            $query->whereIn('master_karyawan.id_kantor', $list_wilayah);
            $query->where('master_karyawan.kode_dept', 'MKT');
            $query->where('nama_jabatan', 'KEPALA PENJUALAN');
            $query->where('id_perusahaan', 'PCF');
        }

        if (!in_array($level, $level_show_all)) {
            $query->whereNotIn('master_karyawan.id_jabatan', $show_for_hrd);
        }


        if ($level == "admin pdqc") {
            $listkaryawan = [
                '08.12.100',
                '11.10.090',
                '13.02.198',
                '91.01.016',
                '03.04.045',
                '08.05.042',
                '12.09.182',
                '05.01.055',
                '13.03.202'
            ];

            $query->whereIn('pinjaman.nik', $listkaryawan);
        }


        if ($level == "spv pdqc") {
            $listkaryawan = [
                '13.03.200',
                '14.08.220',
                '13.07.021',
                '15.05.174',
                '10.08.128',
                '13.09.206',
                '13.09.209',
                '19.09.303',
                '21.06.304',
                '16.01.069',
                '18.03.305'
            ];

            $query->whereIn('kasbon.nik', $listkaryawan);
        }

        if ($level == "manager audit") {
            $query->where('master_karyawan.kode_dept', 'ADT');
        }

        $query->groupByRaw('kasbon.nik,nama_karyawan');
        $query->orderBy('nama_karyawan');
        $kasbon = $query->get();

        $departemen = DB::table('hrd_departemen')->where('kode_dept', $kode_dept)->first();
        $kantor = DB::table('cabang')->where('kode_cabang', $id_kantor)->first();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Kartu Kasbon.xls");
        }
        return view('kasbon.laporan.cetak_kartukasbon', compact('kasbon', 'departemen', 'kantor', 'show_for_hrd', 'namabulan', 'bulan', 'tahun'));
    }




    public function kasbon()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        $departemen = DB::table('hrd_departemen')->get();
        return view('kasbon.laporan.frm.lap_kasbon', compact('cabang', 'departemen'));
    }


    public function kartupinjaman()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        $departemen = DB::table('hrd_departemen')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('pinjaman.laporan.frm.lap_kartupinjaman', compact('cabang', 'departemen', 'bulan'));
    }

    public function kartupiutangkaryawan()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        $departemen = DB::table('hrd_departemen')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('piutangkaryawan.laporan.frm.lap_kartupiutangkaryawan', compact('cabang', 'departemen', 'bulan'));
    }


    public function kartupiutangall()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        $departemen = DB::table('hrd_departemen')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('piutangkaryawan.laporan.frm.lap_kartupiutangall', compact('cabang', 'departemen', 'bulan'));
    }



    public function kartukasbon()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        $departemen = DB::table('hrd_departemen')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('kasbon.laporan.frm.lap_kartukasbon', compact('cabang', 'departemen', 'bulan'));
    }


    public function cetak_kasbon(Request $request)
    {
        $id_kantor = $request->id_kantor;
        $kode_dept = $request->kode_dept;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $level = Auth::user()->level;
        $cabang = Auth::user()->kode_cabang;
        $show_for_hrd = config('global.show_for_hrd');
        $show_for_hrd_2 = config('global.show_for_hrd_2');
        $level_show_all = config('global.show_all');

        $query = Kasbon::query();
        $query->select('kasbon.*', 'nama_karyawan', 'nama_jabatan', 'nama_dept', 'totalpembayaran');
        $query->join('master_karyawan', 'kasbon.nik', '=', 'master_karyawan.nik');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->leftJoin(
            DB::raw("(
            SELECT no_kasbon,SUM(jumlah) as totalpembayaran FROM kasbon_historibayar GROUP BY no_kasbon
        ) hb"),
            function ($join) {
                $join->on('kasbon.no_kasbon', '=', 'hb.no_kasbon');
            }
        );
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_kasbon', [$request->dari, $request->sampai]);
        }

        if (!empty($request->id_kantor)) {
            $query->where('master_karyawan.id_kantor', $request->id_kantor);
        }

        if (!empty($request->kode_dept)) {
            $query->where('master_karyawan.kode_dept', $request->kode_dept);
        }

        if ($level == "kepala admin") {
            $query->where('master_karyawan.id_kantor', $cabang);
            $query->where('master_karyawan.id_perusahaan', "MP");
            $query->where('nama_jabatan', '!=', 'KEPALA ADMIN');
        }

        if ($level == "kepala penjualan") {
            if (Auth::user()->id == "27") {
                $query->whereIn('master_karyawan.id_kantor', [$cabang, 'PWK']);
            } else {
                $query->where('master_karyawan.id_kantor', $cabang);
            }
            $query->where('nama_jabatan', '!=', 'KEPALA PENJUALAN');
            $query->where('master_karyawan.id_perusahaan', "PCF");
        }

        if ($level == "manager pembelian") {
            $query->where('master_karyawan.kode_dept', 'PMB');
        }

        if ($level == "kepala gudang") {
            $query->where('master_karyawan.kode_dept', 'GDG');
            $query->whereNotIN('nama_jabatan', ['MANAGER', 'ASST. MANAGER']);
        }

        if ($level == "spv produksi") {
            $query->where('master_karyawan.kode_dept', 'PRD');
            $query->whereNotIN('nama_jabatan', ['MANAGER', 'SUPERVISOR']);
        }

        if ($level == "manager produksi") {
            $query->whereIn('master_karyawan.kode_dept', ['PRD', 'MTC']);
            $query->where('nama_jabatan', '!=', 'MANAGER');
        }

        if ($level == "manager ga") {
            $query->where('master_karyawan.kode_dept', 'GAF');
        }

        if ($level == "emf") {
            $query->whereIn('master_karyawan.kode_dept', ['PMB', 'PRD', 'GAF', 'GDG', 'HRD', 'PDQ']);
        }


        if ($level == "manager marketing") {
            $query->where('master_karyawan.kode_dept', 'MKT');
            $query->where('nama_jabatan', 'REGIONAL SALES MANAGER');
        }

        if ($level == "rsm") {
            $list_wilayah = Auth::user()->wilayah != null ? unserialize(Auth::user()->wilayah) : NULL;
            $wilayah = $list_wilayah != null ? "'" . implode("', '", $list_wilayah) . "'" : '';
            $query->whereIn('master_karyawan.id_kantor', $list_wilayah);
            $query->where('master_karyawan.kode_dept', 'MKT');
            $query->where('nama_jabatan', 'KEPALA PENJUALAN');
            $query->where('id_perusahaan', 'PCF');
        }

        if (!in_array($level, $level_show_all)) {
            $query->whereNotIn('master_karyawan.id_jabatan', $show_for_hrd);
        }


        if ($level == "admin pdqc") {
            $listkaryawan = [
                '08.12.100',
                '11.10.090',
                '13.02.198',
                '91.01.016',
                '03.04.045',
                '08.05.042',
                '12.09.182',
                '05.01.055',
                '13.03.202'
            ];

            $query->whereIn('kasbon.nik', $listkaryawan);
        }


        if ($level == "spv pdqc") {
            $listkaryawan = [
                '13.03.200',
                '14.08.220',
                '13.07.021',
                '15.05.174',
                '10.08.128',
                '13.09.206',
                '13.09.209',
                '19.09.303',
                '21.06.304',
                '16.01.069',
                '18.03.305'
            ];

            $query->whereIn('kasbon.nik', $listkaryawan);
        }

        if ($level == "manager audit") {
            $query->where('master_karyawan.kode_dept', 'ADT');
        }

        $kasbon = $query->get();

        $departemen = DB::table('hrd_departemen')->where('kode_dept', $kode_dept)->first();
        $kantor = DB::table('cabang')->where('kode_cabang', $id_kantor)->first();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Kasbon $dari-$sampai.xls");
        }
        return view('kasbon.laporan.cetak', compact('kasbon', 'departemen', 'kantor', 'dari', 'sampai'));
    }



    public function cetak_kartupiutangkaryawanall(Request $request)
    {

        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $id_kantor = $request->id_kantor;
        $kode_dept = $request->kode_dept;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        if ($bulan == 12) {
            $bulanpotongan = 1;
            $tahunpotongan = $tahun + 1;
        } else {
            $bulanpotongan = $bulan + 1;
            $tahunpotongan = $tahun;
        }

        $tglgajidari = $tahun . "-" . $bulan . "-01";
        $tglgajisampai = date("Y-m-t", strtotime($tglgajidari));
        $tglpotongan = $tahunpotongan . "-" . $bulanpotongan . "-01";


        $query = Karyawan::query();
        $query->selectRaw('
            master_karyawan.nik,nama_karyawan,
            pinjaman_jumlah_pinjamanlast,
            pinjaman_total_pembayaranlast,
            pinjaman_total_pelunasanlast,
            pinjaman_jumlah_pinjamannow,
            pinjaman_total_pembayarannow,
            pinjaman_total_pelunasannow,

            kasbon_jumlah_kasbonlast,
            kasbon_total_pembayaranlast,
            kasbon_total_pelunasanlast,
            kasbon_jumlah_kasbonnow,
            kasbon_total_pembayarannow,
            kasbon_total_pelunasannow

            piutang_jumlah_pinjamanlast,
            piutang_total_pembayaranlast,
            piutang_total_pelunasanlast,
            piutang_jumlah_pinjamannow,
            piutang_total_pembayarannow,
            piutang_total_pembayaranpotongkomisi,
            piutang_total_pembayarantitipan,
            piutang_total_pelunasannow
        ');

        $query->leftJoin(
            DB::raw("(
            SELECT pinjaman.nik,
            SUM(IF(tgl_pinjaman < '2023-08-01',jumlah_pinjaman,0)) as pinjaman_jumlah_pinjamanlast,
            SUM(totalpembayaranlast) as pinjaman_total_pembayaranlast,
            SUM(totalpelunasanlast) as pinjaman_total_pelunasanlast,
            SUM(IF(tgl_pinjaman BETWEEN '2023-08-01' AND '2023-08-31',jumlah_pinjaman,0)) as pinjaman_jumlah_pinjamannow,
            SUM(totalpembayarannow) as pinjaman_total_pembayarannow,
            SUM(totalpelunasannow) as pinjaman_total_pelunasannow
            FROM pinjaman
            LEFT JOIN (
                SELECT no_pinjaman,SUM(jumlah) as totalpembayaranlast FROM pinjaman_historibayar
                WHERE tgl_bayar < '2023-09-01' AND kode_potongan IS NOT NULL
                GROUP BY no_pinjaman
            ) hb ON (pinjaman.no_pinjaman = hb.no_pinjaman)

            LEFT JOIN (
                SELECT no_pinjaman,SUM(jumlah) as totalpelunasanlast FROM pinjaman_historibayar
                WHERE tgl_bayar < '2023-09-01' AND kode_potongan IS NULL
                GROUP BY no_pinjaman
            ) hbplast ON (pinjaman.no_pinjaman = hbplast.no_pinjaman)

            LEFT JOIN (
                SELECT no_pinjaman,SUM(jumlah) as totalpelunasannow FROM pinjaman_historibayar
                WHERE tgl_bayar BETWEEN '2023-08-01' AND '2023-08-31' AND kode_potongan IS NULL
                GROUP BY no_pinjaman
            ) hbplnow ON (pinjaman.no_pinjaman = hbplnow.no_pinjaman)

            LEFT JOIN (
                SELECT no_pinjaman,SUM(jumlah) as totalpembayarannow FROM pinjaman_historibayar
                WHERE tgl_bayar = '2023-09-01' AND kode_potongan IS NOT NULL
                GROUP BY no_pinjaman
            ) hbnow ON (pinjaman.no_pinjaman = hbnow.no_pinjaman)
            WHERE tgl_pinjaman <= '2023-08-31'
            GROUP BY pinjaman.nik
        ) pinjaman"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'pinjaman.nik');
            }
        );


        $query->leftJoin(
            DB::raw("(
            SELECT kasbon.nik,
            SUM(IF(tgl_kasbon < '2023-08-01',jumlah_kasbon,0)) as kasbon_jumlah_kasbonlast,
            SUM(totalpembayaranlast) as kasbon_total_pembayaranlast,
            SUM(totalpelunasanlast) as kasbon_total_pelunasanlast,
            SUM(IF(tgl_kasbon BETWEEN '2023-08-01' AND '2023-08-31',jumlah_kasbon,0)) as kasbon_jumlah_kasbonnow,
            SUM(totalpembayarannow) as kasbon_total_pembayarannow,
            SUM(totalpelunasannow) as kasbon_total_pelunasannow
            FROM kasbon
            LEFT JOIN (
                SELECT no_kasbon,SUM(jumlah) as totalpembayaranlast FROM kasbon_historibayar
                WHERE tgl_bayar < '2023-09-01' AND kode_potongan IS NOT NULL
                GROUP BY no_kasbon
            ) hb ON (kasbon.no_kasbon = hb.no_kasbon)

            LEFT JOIN (
                SELECT no_kasbon,SUM(jumlah) as totalpelunasanlast FROM kasbon_historibayar
                WHERE tgl_bayar < '2023-08-01' AND kode_potongan IS NULL
                GROUP BY no_kasbon
            ) hbpllast ON (kasbon.no_kasbon = hbpllast.no_kasbon)

            LEFT JOIN (
                SELECT no_kasbon,SUM(jumlah) as totalpelunasannow FROM kasbon_historibayar
                WHERE tgl_bayar BETWEEN '2023-08-01' AND '2023-08-31' AND kode_potongan IS NULL
                GROUP BY no_kasbon
            ) hbplnow ON (kasbon.no_kasbon = hbplnow.no_kasbon)

            LEFT JOIN (
                SELECT no_kasbon,SUM(jumlah) as totalpembayarannow FROM kasbon_historibayar
                WHERE tgl_bayar = '2023-09-01' AND kode_potongan IS NOT NULL
                GROUP BY no_kasbon
            ) hbnow ON (kasbon.no_kasbon = hbnow.no_kasbon)

            WHERE tgl_kasbon <= '2023-08-31'
            GROUP BY kasbon.nik
        ) kasbon"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'kasbon.nik');
            }
        );


        $query->leftJoin(
            DB::raw("(
            SELECT pinjaman_nonpjp.nik,
            SUM(IF(tgl_pinjaman < '2023-08-01',jumlah_pinjaman,0)) as piutang_jumlah_pinjamanlast,
            SUM(totalpembayaranlast) as piutang_total_pembayaranlast,
            SUM(totalpelunasanlast) as piutang_total_pelunasanlast,
            SUM(IF(tgl_pinjaman BETWEEN '2023-08-01' AND '2023-08-31',jumlah_pinjaman,0)) as piutang_jumlah_pinjamannow,
            SUM(totalpembayarannow) as piutang_total_pembayarannow,
            SUM(totalpembayaranpotongkomisi) as piutang_total_pembayaranpotongkomisi,
            SUM(totalpembayarantitipan) as piutang_total_pembayarantitipan,
            SUM(totalpelunasannow) as piutang_total_pelunasannow
            FROM pinjaman_nonpjp
            LEFT JOIN (
                SELECT no_pinjaman_nonpjp,SUM(jumlah) as totalpembayaranlast FROM pinjaman_nonpjp_historibayar
                WHERE tgl_bayar < '2023-09-01' AND kode_potongan IS NOT NULL
                GROUP BY no_pinjaman_nonpjp
            ) hb ON (pinjaman_nonpjp.no_pinjaman_nonpjp = hb.no_pinjaman_nonpjp)


            LEFT JOIN (
                SELECT no_pinjaman_nonpjp,SUM(jumlah) as totalpelunasanlast FROM pinjaman_nonpjp_historibayar
                WHERE tgl_bayar < '2023-08-01' AND kode_potongan IS NULL
                GROUP BY no_pinjaman_nonpjp
            ) hbpllast ON (pinjaman_nonpjp.no_pinjaman_nonpjp = hbpllast.no_pinjaman_nonpjp)


            LEFT JOIN (
                SELECT no_pinjaman_nonpjp,SUM(jumlah) as totalpelunasannow FROM pinjaman_nonpjp_historibayar
                WHERE tgl_bayar BETWEEN '2023-08-01' AND '2023-08-31' AND kode_potongan IS NULL
                GROUP BY no_pinjaman_nonpjp
            ) hbplnow ON (pinjaman_nonpjp.no_pinjaman_nonpjp = hbplnow.no_pinjaman_nonpjp)


            LEFT JOIN (
                SELECT no_pinjaman_nonpjp,
                SUM(IF(jenis_bayar=1,jumlah,0)) as totalpembayarannow,
                SUM(IF(jenis_bayar=2,jumlah,0)) as totalpembayaranpotongkomisi,
                SUM(IF(jenis_bayar=3,jumlah,0)) as totalpembayarantitipan
                FROM pinjaman_nonpjp_historibayar
                WHERE tgl_bayar = '2023-09-01'
                GROUP BY no_pinjaman_nonpjp
            ) hbnow ON (pinjaman_nonpjp.no_pinjaman_nonpjp = hbnow.no_pinjaman_nonpjp)

            WHERE tgl_pinjaman <= '2023-08-31'
            GROUP BY pinjaman_nonpjp.nik
        ) piutang"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'piutang.nik');
            }
        );
        $query->orderBy('nama_karyawan');
        $piutangkaryawan = $query->get();



        $departemen = DB::table('hrd_departemen')->where('kode_dept', $kode_dept)->first();
        $kantor = DB::table('cabang')->where('kode_cabang', $id_kantor)->first();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekap Piutang Karyawan $bulan-$tahun.xls");
        }
        return view('piutangkaryawan.laporan.cetak_rekapall', compact('piutangkaryawan', 'departemen', 'kantor', 'namabulan', 'bulan', 'tahun'));
    }
}
