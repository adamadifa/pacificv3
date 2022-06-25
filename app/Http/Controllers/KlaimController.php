<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Kaskecil;
use App\Models\Klaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class KlaimController extends Controller
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
        $query = Klaim::query();
        $query->selectRaw('klaim.kode_klaim,tgl_klaim,klaim.keterangan,kode_cabang,status,no_bukti,tgl_ledger,status_validasi,jumlah');
        $query->whereBetween('tgl_klaim', [$request->dari, $request->sampai]);
        if (!empty($request->kode_cabang)) {
            $query->where('kode_cabang', $request->kode_cabang);
        }

        if ($this->cabang != "PCF") {
            $query->where('kode_cabang', $request->kode_cabang);
        }
        $query->leftJoin('ledger_bank', 'klaim.kode_klaim', '=', 'ledger_bank.kode_klaim');
        $klaim = $query->get();
        $cbg = DB::table('cabang')->where('kode_cabang', $this->cabang)->first();
        if ($this->cabang !== "PCF") {
            if (Auth::user()->level == "kepala admin") {
                $cabang = Cabang::where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
            } else {
                $cabang = Cabang::where('kode_cabang', $this->cabang)->get();
            }
        } else {
            $cabang = Cabang::orderBy('kode_cabang')->get();
        }
        return view('klaim.index', compact('klaim', 'cabang'));
    }

    public function cetak($kode_klaim, $excel)
    {
        $kode_klaim = Crypt::decrypt($kode_klaim);
        $klaim = DB::table('klaim')->where('kode_klaim', $kode_klaim)->first();
        $tgl_klaim = $klaim->tgl_klaim;
        $kode_cabang = $klaim->kode_cabang;
        $cekklaim = DB::table('klaim')->where('kode_cabang', $kode_cabang)->where('tgl_klaim', '<', $tgl_klaim)->count();
        if (empty($cekklaim)) {
            $sa = DB::table('kaskecil_detail')->where('keterangan', 'SALDO AWAL')->where('kode_cabang', $kode_cabang)->first();
            $saldoawal = $sa->jumlah;
        } else {
            $sa = DB::table('klaim')->where('kode_klaim', '<', $kode_klaim)->where('kode_cabang', $kode_cabang)->orderBy('kode_klaim', 'desc')->first();
            $saldoawal = $sa->saldo_akhir;
        }
        $detail = DB::table('kaskecil_detail')->where('kode_klaim', $kode_klaim)->get();

        //dd($klaim);
        if ($excel == 'true') {
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=$kode_klaim.xls");
        }
        return view('klaim.cetak', compact('saldoawal', 'klaim', 'detail'));
    }

    public function show($kode_klaim)
    {
        $kode_klaim = Crypt::decrypt($kode_klaim);
        $klaim = DB::table('klaim')->where('kode_klaim', $kode_klaim)->first();
        $tgl_klaim = $klaim->tgl_klaim;
        $kode_cabang = $klaim->kode_cabang;
        $cekklaim = DB::table('klaim')->where('kode_cabang', $kode_cabang)->where('tgl_klaim', '<', $tgl_klaim)->count();
        if (empty($cekklaim)) {
            $sa = DB::table('kaskecil_detail')->where('keterangan', 'SALDO AWAL')->where('kode_cabang', $kode_cabang)->first();
            $saldoawal = $sa->jumlah;
        } else {
            $sa = DB::table('klaim')->where('kode_klaim', '<', $kode_klaim)->where('kode_cabang', $kode_cabang)->orderBy('kode_klaim', 'desc')->first();
            $saldoawal = $sa->saldo_akhir;
        }
        $detail = DB::table('kaskecil_detail')->where('kode_klaim', $kode_klaim)->get();
        return view('klaim.show', compact('saldoawal', 'klaim', 'detail'));
    }

    public function delete($kode_klaim)
    {
        $kode_klaim = Crypt::decrypt($kode_klaim);
        DB::beginTransaction();
        try {
            DB::table('klaim')->where('kode_klaim', $kode_klaim)->delete();
            DB::table('kaskecil_detail')->where('kode_klaim', $kode_klaim)->update([
                'kode_klaim' => null
            ]);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);;
        }
    }

    public function create(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query = Kaskecil::query();
            $query->selectRaw('id,nobukti,tgl_kaskecil,kaskecil_detail.keterangan,kaskecil_detail.jumlah,kaskecil_detail.kode_akun,status_dk,nama_akun,kaskecil_detail.kode_klaim,klaim.keterangan as ket_klaim,no_ref,
            costratio_biaya.kode_cr, kaskecil_detail.kode_cr as kk_cr,peruntukan');
            $query->join('coa', 'kaskecil_detail.kode_akun', '=', 'coa.kode_akun');
            $query->leftJoin('klaim', 'kaskecil_detail.kode_klaim', '=', 'klaim.kode_klaim');
            $query->leftJoin('costratio_biaya', 'kaskecil_detail.kode_cr', '=', 'costratio_biaya.kode_cr');
            $query->whereBetween('tgl_kaskecil', [$request->dari, $request->sampai]);
            $query->where('kaskecil_detail.kode_cabang', $kode_cabang);
            $query->orderBy('tgl_kaskecil');
            $query->orderBy('order');
            $query->orderBy('nobukti');
            $kaskecil = $query->get();


            $qsaldoawal = Kaskecil::query();
            $qsaldoawal->selectRaw("SUM(IF( `status_dk` = 'K', jumlah, 0)) -SUM(IF( `status_dk` = 'D', jumlah, 0)) as saldo_awal");
            $qsaldoawal->where('tgl_kaskecil', '<', $request->dari);
            $qsaldoawal->where('kode_cabang', $kode_cabang);
            $saldoawal = $qsaldoawal->first();
        } else {
            $kaskecil = null;
            $saldoawal = null;
        }


        $cbg = DB::table('cabang')->where('kode_cabang', $this->cabang)->first();
        if ($this->cabang !== "PCF") {
            if (Auth::user()->level == "kepala admin") {
                $cabang = Cabang::where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
            } else {
                $cabang = Cabang::where('kode_cabang', $this->cabang)->get();
            }
        } else {
            $cabang = Cabang::orderBy('kode_cabang')->get();
        }
        return view('klaim.create', compact('kaskecil', 'cabang', 'saldoawal'));
    }

    public function store(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $tgl_klaim = $request->tgl_klaim;
        $tanggal = explode("-", $tgl_klaim);
        $tahun = substr($tanggal[0], 2, 2);
        $keterangan = $request->keterangan;
        $klaim = DB::table('klaim')->select('kode_klaim')->whereRaw('LEFT(kode_klaim,7) ="KL' . $kode_cabang . $tahun . '"')->orderBy('kode_klaim', 'desc')->first();
        if ($klaim != null) {
            $lastkode_klaim = $klaim->kode_klaim;
        } else {
            $lastkode_klaim = "";
        }
        $kode_klaim = buatkode($lastkode_klaim, 'KL' . $kode_cabang . $tahun, 4);
        $cekklaim = DB::table('klaim')->where('status', 0)->where('kode_cabang', $kode_cabang)->count();
        if (empty($cekklaim)) {
            DB::beginTransaction();
            try {
                $data = array(
                    'kode_klaim'    => $kode_klaim,
                    'tgl_klaim'     => $tgl_klaim,
                    'keterangan'    => $keterangan,
                    'kode_cabang'   => $kode_cabang
                );
                DB::table('klaim')->insert($data);
                foreach ($_POST['id'] as $id) {
                    $data = ['kode_klaim' => $kode_klaim];
                    //echo $id;
                    DB::table('kaskecil_detail')->where('id', $id)->update($data);
                }

                DB::commit();
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } catch (\Exception $e) {
                dd($e);
                DB::rollback();
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan Hubungi Tim IT']);;
            }
        } else {
            return Redirect::back()->with(['warning' => 'Data Sebelumnya Belum Di Proses']);
        }
    }

    public function prosesklaim($kode_klaim)
    {
        $kode_klaim = Crypt::decrypt($kode_klaim);
        $klaim = DB::table('klaim')->where('kode_klaim', $kode_klaim)->first();
        $tgl_klaim = $klaim->tgl_klaim;
        $kode_cabang = $klaim->kode_cabang;
        $cekklaim = DB::table('klaim')->where('kode_cabang', $kode_cabang)->where('tgl_klaim', '<', $tgl_klaim)->count();
        if (empty($cekklaim)) {
            $sa = DB::table('kaskecil_detail')->where('keterangan', 'SALDO AWAL')->where('kode_cabang', $kode_cabang)->first();
            $saldoawal = $sa->jumlah;
        } else {
            $sa = DB::table('klaim')->where('kode_klaim', '<', $kode_klaim)->where('kode_cabang', $kode_cabang)->orderBy('kode_klaim', 'desc')->first();
            $saldoawal = $sa->saldo_akhir;
        }
        $detail = DB::table('kaskecil_detail')
            ->join('coa', 'kaskecil_detail.kode_akun', '=', 'coa.kode_akun')
            ->where('kode_klaim', $kode_klaim)->get();
        $bank = DB::table('master_bank')->orderBy('kode_bank')->get();
        return view('klaim.prosesklaim', compact('saldoawal', 'klaim', 'detail', 'bank'));
    }

    public function storeprosesklaim(Request $request)
    {
        $kode_klaim = $request->kode_klaim;
        $klaim = DB::table('klaim')->where('kode_klaim', $kode_klaim)->first();
        $kode_cabang = $klaim->kode_cabang;
        $saldo_akhir = $request->saldo_akhir;
        $tanggal = $request->tanggal;
        $keterangan = $request->keterangan;
        $jumlah = str_replace(".", "", $request->jumlah);
        $bank = $request->bank;
        $tgl = explode("-", $tanggal);
        $bulan = $tgl[1];
        $tahun = substr($tgl[0], 2, 2);
        if ($kode_cabang != 'GRT') {
            $akun = "1-1104";
        } else {
            $akun = "1-1119";
        }
        $databank = DB::table('master_bank')->where('kode_bank', $bank)->first();
        $akunbank = $databank->kode_akun;

        $ledger = DB::table('ledger_bank')->select('no_bukti')->whereRaw('LEFT(no_bukti,7)="LR' . $kode_cabang . $tahun . '"')->orderBy('no_bukti', 'desc')->first();
        if ($ledger != null) {
            $lastno_bukti = $ledger->no_bukti;
        } else {
            $lastno_bukti = "";
        }
        $no_bukti = buatkode($lastno_bukti, 'LR' . $kode_cabang . $tahun, 4);

        $bukubesar = DB::table('buku_besar')->whereRaw('LEFT(no_bukti,6)="GJ' . $bulan . $tahun . '"')
            ->orderBy('no_bukti', 'desc')
            ->first();
        if ($bukubesar != null) {
            $last_no_bukti_bukubesar = $bukubesar->no_bukti;
        } else {
            $last_no_bukti_bukubesar = "";
        }

        $nobukti_bukubesar = buatkode($last_no_bukti_bukubesar, 'GJ' . $bulan . $tahun, 6);

        $databukubesar = array(
            'no_bukti' => $nobukti_bukubesar,
            'tanggal' => $tanggal,
            'sumber' => 'Ledger',
            'keterangan' => $keterangan,
            'kode_akun' => $akunbank,
            'debet' => 0,
            'kredit' => $jumlah,
            'nobukti_transaksi' => $no_bukti,
        );
        $data = [
            'no_bukti'        => $no_bukti,
            'tgl_ledger'      => $tanggal,
            'bank'            => $bank,
            'pelanggan'       => 'BNI CAB ' . $kode_cabang,
            'keterangan'      => $keterangan,
            'kode_akun'       => $akun,
            'jumlah'          => $jumlah,
            'status_dk'       => 'D',
            'kode_klaim'      => $kode_klaim,
            'status_validasi' => '0',
            'nobukti_bukubesar' => $nobukti_bukubesar
        ];

        $dataklaim = [
            'status' => 1,
            'saldo_akhir' => $saldo_akhir
        ];
        DB::beginTransaction();
        try {
            DB::table('ledger_bank')->insert($data);
            DB::table('klaim')->where('kode_klaim', $kode_klaim)->update($dataklaim);
            DB::table('buku_besar')->insert($databukubesar);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan Hubungi Tim IT']);;
        }
    }

    public function batalkanproses($kode_klaim)
    {
        $kode_klaim = Crypt::decrypt($kode_klaim);
        $ledger_bank = DB::table('ledger_bank')->where('kode_klaim', $kode_klaim)->first();
        $nobukti_bukubesar = $ledger_bank->nobukti_bukubesar;
        $dataklaim = [
            'status' => 0,
            'saldo_akhir' => 0
        ];
        DB::beginTransaction();
        try {
            DB::table('ledger_bank')->where('kode_klaim', $kode_klaim)->delete();
            DB::table('klaim')->where('kode_klaim', $kode_klaim)->update($dataklaim);
            DB::table('buku_besar')->where("no_bukti", $nobukti_bukubesar)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            // /dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus Hubungi Tim IT']);;
        }
    }

    public function validasikaskecil($kode_klaim)
    {
        $kode_klaim = Crypt::decrypt($kode_klaim);
        $klaim = DB::table('klaim')->where('kode_klaim', $kode_klaim)->first();
        $kode_cabang = $klaim->kode_cabang;
        $kasbank_perantara = ['PST', 'TSM'];
        $ledger = DB::table('ledger_bank')->where('kode_klaim', $kode_klaim)->first();
        $no_bukti = $ledger->no_bukti;
        $tgl_ledger = $ledger->tgl_ledger;
        $keterangan = "Penerimaan Kas Kecil";
        $akun = [
            'BDG' => '1-1102',
            'BGR' => '1-1103',
            'PST' => '1-1104',
            'TSM' => '1-1104',
            'SKB' => '1-1113',
            'PWT' => '1-1114',
            'TGL' => '1-1115',
            'SBY' => '1-1116',
            'SMR' => '1-1117',
            'KLT' => '1-1118',
            'GRT' => '1-1119'
        ];

        $akunpsttsm = [
            'PST' => '1-1111',
            'TSM' => '1-1112'
        ];
        $kode_akun = $akun[$kode_cabang];
        $jumlah = $ledger->jumlah;
        $tgl = explode("-", $tgl_ledger);
        $tahun = $tgl[0];
        $thn = substr($tahun, 2, 2);
        $bulan = $tgl[1];
        $cektutuplaporan = DB::table('tutup_laporan')->where('bulan', $bulan)->where('tahun', $tahun)->where('status', 1)->where('jenis_laporan', 'Kas Kecil')->count();
        if ($cektutuplaporan > 0) {
            return Redirect::back()->with(['warning' => 'Laporan Sudah Ditutup !']);
        } else {


            DB::beginTransaction();
            try {
                if (in_array($kode_cabang, $kasbank_perantara)) {

                    $psttsm = $akunpsttsm[$kode_cabang];
                    $bukubesar = DB::table('buku_besar')->whereRaw('LEFT(no_bukti,6)="GJ' . $bulan . $thn . '"')
                        ->orderBy('no_bukti', 'desc')
                        ->first();
                    if ($bukubesar != null) {
                        $last_no_bukti_bukubesar = $bukubesar->no_bukti;
                    } else {
                        $last_no_bukti_bukubesar = "";
                    }

                    $nobukti_bukubesar = buatkode($last_no_bukti_bukubesar, 'GJ' . $bulan . $thn, 6);
                    $databukubesar = array(
                        'no_bukti' => $nobukti_bukubesar,
                        'tanggal' => $tgl_ledger,
                        'sumber' => 'Kas Kecil',
                        'keterangan' => $keterangan,
                        'kode_akun' => $psttsm,
                        'debet' => $jumlah,
                        'kredit' => 0,
                        'nobukti_transaksi' => $no_bukti
                    );



                    DB::table('buku_besar')->insert($databukubesar);

                    $data = array(
                        'nobukti'         => $no_bukti,
                        'tgl_kaskecil'    => $tgl_ledger,
                        'keterangan'      => $keterangan,
                        'jumlah'          => $jumlah,
                        'status_dk'       => 'K',
                        'kode_akun'       => $kode_akun,
                        'kode_cabang'     => $kode_cabang,
                        'order'           => 1,
                        'nobukti_bukubesar' => $nobukti_bukubesar
                    );
                } else {
                    $data = array(
                        'nobukti'         => $no_bukti,
                        'tgl_kaskecil'    => $tgl_ledger,
                        'keterangan'      => $keterangan,
                        'jumlah'          => $jumlah,
                        'status_dk'       => 'K',
                        'kode_akun'       => $kode_akun,
                        'kode_cabang'     => $kode_cabang,
                        'order'           => 1
                    );
                }
                DB::table('kaskecil_detail')->insert($data);
                DB::table('ledger_bank')->where('no_bukti', $no_bukti)->update(['status_validasi' => 1]);

                DB::commit();
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } catch (\Exception $e) {
                dd($e);
                DB::rollback();
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan Hubungi Tim IT']);;
            }
        }
    }

    public function batalkanvalidasi($no_bukti)
    {
        $no_bukti = Crypt::decrypt($no_bukti);
        DB::beginTransaction();
        try {
            DB::table('kaskecil_detail')->where('nobukti', $no_bukti)->delete();
            DB::table('ledger_bank')->where('no_bukti', $no_bukti)->update(['status_validasi' => 0]);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil DiBatalkan']);
        } catch (\Exception $e) {
            // /dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Dibatalkan Hubungi Tim IT']);;
        }
    }
}
