<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Cabang;
use App\Models\Setoranpusat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class SetoranpusatController extends Controller
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
        $query = Setoranpusat::query();
        $query->select('setoran_pusat.*', 'nama_bank');
        $query->join('master_bank', 'setoran_pusat.bank', '=', 'master_bank.kode_bank');
        $query->whereBetween('tgl_setoranpusat', [$request->dari, $request->sampai]);
        if (!empty($request->kode_bank)) {
            $query->where('bank', $request->kode_bank);
        }

        if (!empty($request->kode_cabang)) {
            $query->where('setoran_pusat.kode_cabang', $request->kode_cabang);
        } else {
            if ($this->cabang != 'PCF') {
                $query->where('setoran_pusat.kode_cabang', $this->cabang);
            }
        }
        $query->orderBy('tgl_setoranpusat');
        $query->orderBy('kode_setoranpusat');
        $setoranpusat = $query->get();
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        $bank = Bank::where('show_on_cabang', 1)->get();
        $kode_cabang = $this->cabang;
        lockreport($request->dari);
        return view('setoranpusat.index', compact('cabang', 'bank', 'setoranpusat', 'kode_cabang'));
    }

    public function create()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        $bank = Bank::where('show_on_cabang', 1)->get();
        return view('setoranpusat.create', compact('bank', 'cabang'));
    }

    public function store(Request $request)
    {
        $tgl_setoranpusat = $request->tgl_setoranpusat;
        $kode_cabang = $request->kode_cabang;
        $kode_bank = $request->kode_bank;
        $uang_kertas = !empty($request->uang_kertas) ? str_replace(".", "", $request->uang_kertas) : 0;
        $uang_logam = !empty($request->uang_logam) ? str_replace(".", "", $request->uang_logam) : 0;
        $keterangan = $request->keterangan;
        $tanggal = explode("-", $tgl_setoranpusat);
        $hari = $tanggal[2];
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];
        $tahunini = date("y");
        $setoranpusat = DB::table('setoran_pusat')
            ->select('kode_setoranpusat')
            ->whereRaw('LEFT(kode_setoranpusat,4)="SB' . $tahunini . '"')
            ->orderBy('kode_setoranpusat', 'desc')
            ->first();
        $last_kode_setoranpusat = $setoranpusat != null ? $setoranpusat->kode_setoranpusat : '';
        $kode_setoranpusat   = buatkode($last_kode_setoranpusat, 'SB' . $tahunini, 5);
        $data = [
            'kode_setoranpusat' => $kode_setoranpusat,
            'tgl_setoranpusat'  => $tgl_setoranpusat,
            'kode_cabang' => $kode_cabang,
            'bank' => $kode_bank,
            'uang_kertas' => $uang_kertas,
            'uang_logam' => $uang_logam,
            'keterangan' => $keterangan,
            'status' => '0'
        ];
        if ($bulan == 12) {
            $bulan = 1;
            $tahun = $tahun + 1;
        } else {
            $bulan = $bulan + 1;
            $tahun = $tahun;
        }
        $ceksaldo = DB::table('saldoawal_kasbesar')->where('bulan', $bulan)->where('tahun', $tahun)->where('kode_cabang', $kode_cabang)->count();
        if (empty($ceksaldo)) {
            $simpan = DB::table('setoran_pusat')->insert($data);
            if ($simpan) {
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } else {
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan Hubungi Tim IT']);
            }
        } else {
            return Redirect::back()->with(['warning' => 'Periode Laporan Sudah Ditutup']);
        }
    }

    public function delete($kode_setoranpusat)
    {
        $kode_setoranpusat = Crypt::decrypt($kode_setoranpusat);
        $hapus = DB::table('setoran_pusat')->where('kode_setoranpusat', $kode_setoranpusat)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus Hubungi Tim IT']);
        }
    }

    public function edit($kode_setoranpusat)
    {
        $setoranpusat = DB::table('setoran_pusat')->where('kode_setoranpusat', $kode_setoranpusat)->first();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $bank = Bank::where('show_on_cabang', 1)->get();
        return view('setoranpusat.edit', compact('bank', 'cabang', 'setoranpusat'));
    }

    public function update($kode_setoranpusat, Request $request)
    {
        $kode_setoranpusat = Crypt::decrypt($kode_setoranpusat);
        $uang_kertas = !empty($request->uang_kertas) ? str_replace(".", "", $request->uang_kertas) : 0;
        $uang_logam = !empty($request->uang_logam) ? str_replace(".", "", $request->uang_logam) : 0;
        $setoranpusat = DB::table('setoran_pusat')->where('kode_setoranpusat', $kode_setoranpusat)->first();
        $status = $setoranpusat->status;
        if ($status == 0) {
            $data = [
                'tgl_setoranpusat' => $request->tgl_setoranpusat,
                'bank' => $request->kode_bank,
                'uang_kertas' => $uang_kertas,
                'uang_logam' => $uang_logam,
                'keterangan' => $request->keterangan
            ];
        } else {
            $data = [
                'tgl_setoranpusat' => $request->tgl_setoranpusat,
                'keterangan' => $request->keterangan
            ];
        }

        $update = DB::table('setoran_pusat')->where('kode_setoranpusat', $kode_setoranpusat)->update($data);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate Hubungi Tim IT']);
        }
    }

    public function cetak(Request $request)
    {
        $query = Setoranpusat::query();
        $query->select('setoran_pusat.*', 'nama_bank');
        $query->join('master_bank', 'setoran_pusat.bank', '=', 'master_bank.kode_bank');
        $query->whereBetween('tgl_setoranpusat', [$request->dari, $request->sampai]);
        if (!empty($request->kode_bank)) {
            $query->where('bank', $request->kode_bank);
        }

        if (!empty($request->kode_cabang)) {
            $query->where('setoran_pusat.kode_cabang', $request->kode_cabang);
        }
        $query->orderBy('tgl_setoranpusat');
        $query->orderBy('kode_setoranpusat');
        $setoranpusat = $query->get();

        $qrekap = Setoranpusat::query();
        $qrekap->selectRaw("nama_bank,sum(uang_kertas) as uang_kertas, sum(uang_logam) as uang_logam, sum(giro) as giro,SUM(transfer) as transfer");
        $qrekap->join('master_bank', 'setoran_pusat.bank', '=', 'master_bank.kode_bank');
        $qrekap->whereBetween('tgl_setoranpusat', [$request->dari, $request->sampai]);
        if (!empty($request->kode_bank)) {
            $qrekap->where('bank', $request->kode_bank);
        }

        if (!empty($request->kode_cabang)) {
            $qrekap->where('setoran_pusat.kode_cabang', $request->kode_cabang);
        }
        $qrekap->groupBy('nama_bank');
        $rekap = $qrekap->get();



        $cabang = Cabang::where('kode_cabang', $request->kode_cabang)->first();
        $bank = Bank::where('kode_bank', $request->kode_bank)->first();
        $dari = $request->dari;
        $sampai = $request->sampai;
        if ($request->excel == "true") {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Setoran Penjualan $dari-$sampai-$time.xls");
        }
        return view('setoranpusat.cetak', compact('cabang', 'bank', 'setoranpusat', 'dari', 'sampai', 'rekap'));
    }

    public function createterimasetoran($kode_setoranpusat)
    {
        $setoranpusat = DB::table('setoran_pusat')
            ->join('master_bank', 'setoran_pusat.bank', '=', 'master_bank.kode_bank')
            ->where('kode_setoranpusat', $kode_setoranpusat)->first();
        $bank = Bank::where('show_on_cabang', 1)->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('setoranpusat.createterimasetoran', compact('setoranpusat', 'bank', 'bulan'));
    }

    public function terimasetoran($kode_setoranpusat, Request $request)
    {
        $kode_setoranpusat = Crypt::decrypt($kode_setoranpusat);
        $tgl_diterimapusat = $request->tgl_diterimapusat;
        $bank = $request->bank;
        $omset_bulan = $request->bulan;
        $omset_tahun = $request->tahun;
        $setoranpusat = DB::table('setoran_pusat')->where('kode_setoranpusat', $kode_setoranpusat)->first();


        $b = DB::table('master_bank')->where('kode_bank', $bank)->first();
        $kode_akun_bank = $b->kode_akun;

        $tgl_diterima = explode("-", $tgl_diterimapusat);
        $tahun = substr($tgl_diterima[0], 2, 2);
        $bulan = $tgl_diterima[1];

        $cabang = $setoranpusat->kode_cabang;
        $tgl_setoranpusat = $setoranpusat->tgl_setoranpusat;
        $jmlbayar = $setoranpusat->uang_kertas + $setoranpusat->uang_logam;

        $lastledger = DB::table('ledger_bank')
            ->select('no_bukti')
            ->whereRaw('LEFT(no_bukti,7) ="LR' . $cabang . $tahun . '"')
            ->orderBy('no_bukti', 'desc')
            ->first();
        if ($lastledger == null) {
            $last_no_bukti = 'LR' . $cabang . $tahun . '0000';
        } else {
            $last_no_bukti = $lastledger->no_bukti;
        }

        $no_bukti = buatkode($last_no_bukti, 'LR' . $cabang . $tahun, 4);

        //Buku Besar
        $lastbukubesar = DB::table('buku_besar')
            ->select('no_bukti')
            ->whereRaw('LEFT(no_bukti,6)="GJ' . $bulan . $tahun . '"')
            ->orderBy('no_bukti', 'desc')
            ->first();
        if ($lastbukubesar == null) {
            $last_no_bukti_bb = '';
        } else {
            $last_no_bukti_bb =  $lastbukubesar->no_bukti;
        }
        $no_bukti_bb   = buatkode($last_no_bukti_bb, 'GJ' . $bulan . $tahun, 6);
        $nobukti_bukubesar_bank = buatkode($no_bukti_bb, 'GJ' . $bulan . $tahun, 6);
        // if ($cabang == 'TSM') {
        //     $akun = "1-1468";
        // } else if ($cabang == 'BDG') {
        //     $akun = "1-1402";
        // } else if ($cabang == 'BGR') {
        //     $akun = "1-1403";
        // } else if ($cabang == 'PWT') {
        //     $akun = "1-1404";
        // } else if ($cabang == 'TGL') {
        //     $akun = "1-1405";
        // } else if ($cabang == "SKB") {
        //     $akun = "1-1407";
        // } else if ($cabang == "GRT") {
        //     $akun = "1-1487";
        // } else if ($cabang == "SMR") {
        //     $akun = "1-1488";
        // } else if ($cabang == "SBY") {
        //     $akun = "1-1486";
        // } else if ($cabang == "PST") {
        //     $akun = "1-1489";
        // } else if ($cabang == "KLT") {
        //     $akun = "1-1490";
        // } else if ($cabang == "PWK") {
        //     $akun = "1-1492";
        // } else if ($cabang == "BTN") {
        //     $akun = "1-1493";
        // }

        $akun = getAkunpiutangcabang($cabang);

        $dataledger = array(
            'no_bukti'              => $no_bukti,
            'no_ref'                => $kode_setoranpusat,
            'bank'                  => $bank,
            'tgl_ledger'            => $tgl_diterimapusat,
            'keterangan'            => "SETORAN CAB " . $cabang,
            'kode_akun'             => $akun,
            'jumlah'                => $jmlbayar,
            'status_dk'             => 'K',
            'status_validasi'       => 1,
            'kategori'              => 'PNJ',
            'nobukti_bukubesar'     => $no_bukti_bb,
            'nobukti_bukubesar_2'   => $nobukti_bukubesar_bank
        );

        $data = array(
            'status' => 1,
            'tgl_diterimapusat' => $tgl_diterimapusat,
            'bank' => $bank,
            'omset_bulan' => $omset_bulan,
            'omset_tahun' => $omset_tahun
        );

        $databukubesar = array(
            'no_bukti' => $no_bukti_bb,
            'tanggal' => $tgl_diterimapusat,
            'sumber' => 'ledger',
            'keterangan' => "SETORAN CAB " . $cabang,
            'kode_akun' => $akun,
            'debet' => 0,
            'kredit' => $jmlbayar,
            'nobukti_transaksi' => $no_bukti
        );

        $databukubesarbank = array(
            'no_bukti' => $nobukti_bukubesar_bank,
            'tanggal' => $tgl_diterimapusat,
            'sumber' => 'ledger',
            'keterangan' => "SETORAN CAB " . $cabang,
            'kode_akun' => $kode_akun_bank,
            'debet' => $jmlbayar,
            'kredit' => 0,
            'nobukti_transaksi' => $no_bukti
        );
        DB::beginTransaction();
        try {
            DB::table('setoran_pusat')->where('kode_setoranpusat', $kode_setoranpusat)->update($data);
            DB::table('ledger_bank')->insert($dataledger);
            DB::table('buku_besar')->insert($databukubesar);
            DB::table('buku_besar')->insert($databukubesarbank);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Setoran Gagal di Update,  Silahkan Hubungi Tim IT']);
        }
    }

    function batalkansetoran($kode_setoranpusat)
    {
        $kode_setoranpusat = Crypt::decrypt($kode_setoranpusat);
        $data = array(
            'status' => 0,
            'tgl_diterimapusat' => NULL
        );
        $ledger = DB::table('ledger_bank')->where('no_ref', $kode_setoranpusat)->first();
        $no_bukti_bb = $ledger != null ? $ledger->nobukti_bukubesar : '';
        $nobukti_bukubesar_bank = $ledger != null ?  $ledger->nobukti_bukubesar_2 : '';

        DB::beginTransaction();
        try {
            DB::table('setoran_pusat')->where('kode_setoranpusat', $kode_setoranpusat)->update($data);
            DB::table('ledger_bank')->where('no_ref', $kode_setoranpusat)->delete();
            DB::table('buku_besar')->where('no_bukti', $no_bukti_bb)->delete();
            DB::table('buku_besar')->where('no_bukti', $nobukti_bukubesar_bank)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dibatalkan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Giro Gagal di Batalkan,  Silahkan Hubungi Tim IT']);
        }
    }
}
