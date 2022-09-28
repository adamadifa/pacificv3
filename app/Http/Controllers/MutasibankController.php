<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Ledger;
use App\Models\Setcoacabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class MutasibankController extends Controller
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
        $query = Ledger::query();
        $query->select('ledger_bank.*', 'nama_akun');
        $query->join('coa', 'ledger_bank.kode_akun', '=', 'coa.kode_akun');
        $query->join('master_bank', 'ledger_bank.bank', '=', 'master_bank.kode_bank');
        $query->orderBy('tgl_ledger');
        $query->orderBy('pelanggan');
        $query->orderBy('status_dk', 'desc');
        $query->orderBy('date_created', 'asc');
        $query->whereBetween('tgl_ledger', [$request->dari, $request->sampai]);
        $query->where('ledger_bank.bank', $request->bank);
        $mutasibank = $query->get();
        if ($this->cabang == "PCF") {
            $cabang = Cabang::orderBy('kode_cabang')->get();
        } else {
            $cabang = Cabang::where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }

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
            ->where('kode_bank', $request->bank)
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
                ->where('bank', $request->bank)
                ->where('tgl_ledger', '>=', $tgl_mulai)
                ->where('tgl_ledger', '<', $request->dari)
                ->first();

            $saldoawal = $sa + $mutasi->sisamutasi;
        } else {
            $saldoawal = 0;
        }





        return view('mutasibank.index', compact('cabang', 'mutasibank', 'saldoawal'));
    }

    public function create($kode_bank, $kode_cabang)
    {
        $qcoa = Setcoacabang::query();
        $qcoa->select('set_coa_cabang.kode_akun', 'nama_akun');
        $qcoa->join('coa', 'set_coa_cabang.kode_akun', '=', 'coa.kode_akun');
        $qcoa->where('kategori', 'Mutasi Bank');
        $qcoa->groupBy('kode_akun', 'nama_akun');
        $qcoa->where('kode_cabang', $kode_cabang);
        $qcoa->orderBy('kode_akun');
        $coa = $qcoa->get();
        return view('mutasibank.create', compact('kode_bank', 'coa', 'kode_cabang'));
    }

    public function store(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $kode_bank = Crypt::decrypt($request->kode_bank);
        $tgl_ledger = $request->tgl_ledger;
        $keterangan = $request->keterangan;
        $jumlah = str_replace(".", "", $request->jumlah);
        $jumlah     = str_replace(",", ".", $jumlah);
        $kode_akun = $request->kode_akun;
        $debetkredit = $request->debetkredit;
        $tanggal    = explode("-", $tgl_ledger);
        $tahun      = substr($tanggal[0], 2, 2);
        $bulan      = $tanggal[1];

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



        // $akunkaskecil = [
        //     '1-1102',
        //     '1-1103',
        //     '1-1111',
        //     '1-1112',
        //     '1-1113',
        //     '1-1114',
        //     '1-1115',
        //     '1-1116',
        //     '1-1117',
        //     '1-1118'
        // ];


        $bank = DB::table('master_bank')->where('kode_bank', $kode_bank)->first();
        $kode_akun_bank = $bank->kode_akun;

        if ($debetkredit == "D") {
            $debet = $jumlah;
            $kredit = 0;
        } else {
            $debet = 0;
            $kredit = $jumlah;
        }

        $nobukti_bukubesar_bank = buatkode($nobukti_bukubesar, 'GJ' . $bulan . $tahun, 6);


        $databukubesar = array(
            'no_bukti' => $nobukti_bukubesar,
            'tanggal' => $tgl_ledger,
            'sumber' => 'ledger',
            'keterangan' => $keterangan,
            'kode_akun' => $kode_akun,
            'debet' => $debet,
            'kredit' => $kredit,
            'nobukti_transaksi' => $no_bukti
        );

        $databukubesarbank = array(
            'no_bukti' => $nobukti_bukubesar_bank,
            'tanggal' => $tgl_ledger,
            'sumber' => 'ledger',
            'keterangan' => $keterangan,
            'kode_akun' => $kode_akun_bank,
            'debet' => $kredit,
            'kredit' => $debet,
            'nobukti_transaksi' => $no_bukti
        );



        $cekakun = substr($kode_akun, 0, 3);

        DB::beginTransaction();
        try {
            if ($debetkredit == 'D' and $cekakun == '6-1'  or $debetkredit == 'D' and $cekakun == '6-2') {
                $kode = "CR" . $bulan . $tahun;
                $cr = DB::table('costratio_biaya')
                    ->select('kode_cr')
                    ->whereRaw('LEFT(kode_cr,6) ="' . $kode . '"')
                    ->orderBy('kode_cr', 'desc')
                    ->first();
                if ($cr != null) {
                    $last_kode_cr = $cr->kode_cr;
                } else {
                    $last_kode_cr = "";
                }
                $kode_cr = buatkode($last_kode_cr, "CR" . $bulan . $tahun, 4);

                $datacr = [
                    'kode_cr' => $kode_cr,
                    'tgl_transaksi' => $tgl_ledger,
                    'kode_akun'    => $kode_akun,
                    'keterangan'   => $keterangan,
                    'kode_cabang'  => $kode_cabang,
                    'id_sumber_costratio' => 2,
                    'jumlah' => $jumlah
                ];

                DB::table('costratio_biaya')->insert($datacr);
            } else {
                $kode_cr = null;
            }

            $dataledger = array(
                'no_bukti'        => $no_bukti,
                'bank'            => $kode_bank,
                'tgl_ledger'      => $tgl_ledger,
                'keterangan'      => $keterangan,
                'kode_akun'       => $kode_akun,
                'jumlah'          => $jumlah,
                'status_dk'       => $debetkredit,
                'nobukti_bukubesar' => $nobukti_bukubesar,
                'nobukti_bukubesar_2' => $nobukti_bukubesar_bank,
                'kode_cr' => $kode_cr,
                'status_validasi' => 1
            );


            DB::table('ledger_bank')->insert($dataledger);
            DB::table('buku_besar')->insert($databukubesar);
            DB::table('buku_besar')->insert($databukubesarbank);

            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan Hubungi Tim IT']);;
        }
    }

    public function delete($no_bukti)
    {
        $no_bukti = Crypt::decrypt($no_bukti);
        $mutasibank = DB::table('ledger_bank')->where('no_bukti', $no_bukti)->first();
        $tgl_ledger = $mutasibank->tgl_ledger;
        $kode_cr = $mutasibank->kode_cr;
        $tanggal = explode("-", $tgl_ledger);
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];
        $cektutuplaporan = DB::table('tutup_laporan')->where('jenis_laporan', 'ledger')->where('status', '1')->where('bulan', $bulan)->where('tahun', $tahun)->count();
        if ($cektutuplaporan > 0) {
            return Redirect::back()->with(['warning' => 'Laporan Sudah Ditutup']);
        } else {
            DB::beginTransaction();
            try {
                DB::table('ledger_bank')->where('no_bukti', $no_bukti)->delete();
                DB::table('costratio_biaya')->where('kode_cr', $kode_cr)->delete();
                DB::table('buku_besar')->where('no_bukti', $mutasibank->nobukti_bukubesar)->delete();
                DB::table('buku_besar')->where('no_bukti', $mutasibank->nobukti_bukubesar_2)->delete();
                DB::commit();
                return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
            } catch (\Exception $e) {
                dd($e);
                DB::rollback();
                return Redirect::back()->with(['warning' => 'Data Gagal Dihapus Hubungi Tim IT']);
            }
        }
    }

    public function edit(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $mutasibank = DB::table('ledger_bank')
            ->select('ledger_bank.*', 'master_bank.kode_cabang')
            ->join('master_bank', 'ledger_bank.bank', '=', 'master_bank.kode_bank')
            ->where('no_bukti', $no_bukti)->first();
        $qcoa = Setcoacabang::query();
        $qcoa->select('set_coa_cabang.kode_akun', 'nama_akun');
        $qcoa->join('coa', 'set_coa_cabang.kode_akun', '=', 'coa.kode_akun');
        $qcoa->where('kategori', 'Mutasi Bank');
        $qcoa->groupBy('kode_akun', 'nama_akun');
        $qcoa->where('kode_cabang', $mutasibank->kode_cabang);
        $qcoa->orderBy('kode_akun');
        $coa = $qcoa->get();
        return view('mutasibank.edit', compact('mutasibank', 'coa'));
    }

    public function update($no_bukti, Request $request)
    {
        $no_bukti = Crypt::decrypt($no_bukti);
        $mutasibank = DB::table('ledger_bank')
            ->join('master_bank', 'ledger_bank.bank', '=', 'master_bank.kode_bank')
            ->where('no_bukti', $no_bukti)->first();
        $tgl_ledger = $request->tgl_ledger;
        $keterangan = $request->keterangan;
        $jumlah = str_replace(".", "", $request->jumlah);
        $jumlah     = str_replace(",", ".", $jumlah);
        $kode_akun = $request->kode_akun;
        $debetkredit = $request->debetkredit;
        $tanggal = explode("-", $tgl_ledger);
        $tahun = substr($tanggal[0], 2, 2);
        $bulan = $tanggal[1];
        $cekakun = substr($kode_akun, 0, 3);
        $kode_cr = $mutasibank->kode_cr;
        $kode_cabang = $mutasibank->kode_cabang;

        // $bukubesar = DB::table('buku_besar')->whereRaw('LEFT(no_bukti,6)="GJ' . $bulan . $tahun . '"')
        //     ->orderBy('no_bukti', 'desc')
        //     ->first();
        // if ($bukubesar != null) {
        //     $last_no_bukti_bukubesar = $bukubesar->no_bukti;
        // } else {
        //     $last_no_bukti_bukubesar = "";
        // }

        // $nobukti_bukubesar = buatkode($last_no_bukti_bukubesar, 'GJ' . $bulan . $tahun, 4);



        // $akunkaskecil = [
        //     '1-1102',
        //     '1-1103',
        //     '1-1111',
        //     '1-1112',
        //     '1-1113',
        //     '1-1114',
        //     '1-1115',
        //     '1-1116',
        //     '1-1117',
        //     '1-1118'
        // ];

        if ($debetkredit == "D") {
            $debet = $jumlah;
            $kredit = 0;
        } else {
            $debet = 0;
            $kredit = $jumlah;
        }


        DB::beginTransaction();
        try {

            $databukubesar = array(
                'tanggal' => $tgl_ledger,
                'keterangan' => $keterangan,
                'kode_akun' => $kode_akun,
                'debet' => $debet,
                'kredit' => $kredit,
            );

            $databukubesarbank = array(
                'tanggal' => $tgl_ledger,
                'keterangan' => $keterangan,
                'debet' => $kredit,
                'kredit' => $debet,
            );

            $dataledger = array(
                'tgl_ledger'      => $tgl_ledger,
                'keterangan'      => $keterangan,
                'kode_akun'       => $kode_akun,
                'jumlah'          => $jumlah,
                'status_dk'       => $debetkredit,
                'status_validasi' => 1
            );

            if ($debetkredit == 'D' and $cekakun == '6-1'  or $debetkredit == 'D' and $cekakun == '6-2') {
                if (empty($kode_cr)) {
                    $kode = "CR" . $bulan . $tahun;
                    $cr = DB::table('costratio_biaya')
                        ->select('kode_cr')
                        ->whereRaw('LEFT(kode_cr,6) ="' . $kode . '"')
                        ->orderBy('kode_cr', 'desc')
                        ->first();
                    if ($cr != null) {
                        $last_kode_cr = $cr->kode_cr;
                    } else {
                        $last_kode_cr = "";
                    }
                    $kode_cr = buatkode($last_kode_cr, "CR" . $bulan . $tahun, 4);
                    $datacr = [
                        'kode_cr' => $kode_cr,
                        'tgl_transaksi' => $tgl_ledger,
                        'kode_akun'    => $kode_akun,
                        'keterangan'   => $keterangan,
                        'kode_cabang'  => $kode_cabang,
                        'id_sumber_costratio' => 2,
                        'jumlah' => $jumlah
                    ];
                    DB::table('costratio_biaya')->insert($datacr);

                    $datamb = [
                        'kode_cr' => $kode_cr
                    ];
                    DB::table('ledger_bank')->where('no_bukti', $no_bukti)->update($datamb);
                } else {
                    $datacr = [
                        'tgl_transaksi' => $tgl_ledger,
                        'keterangan' => $keterangan,
                        'kode_akun' => $kode_akun,
                        'kode_cabang' => $kode_cabang,
                        'jumlah' => $jumlah
                    ];
                    DB::table('costratio_biaya')->where('kode_cr', $kode_cr)->update($datacr);
                }
            } else {


                if (!empty($kode_cr)) {
                    echo $kode_cr;
                    DB::table('costratio_biaya')->where('kode_crd', $kode_cr)->delete();
                    die;
                    $datamb = [
                        'kode_cr' => null
                    ];
                    DB::table('ledger_bank')->where('no_bukti', $no_bukti)->update($datamb);
                }
            }
            DB::table('buku_besar')->where('no_bukti', $mutasibank->nobukti_bukubesar)->update($databukubesar);
            DB::table('buku_besar')->where('no_bukti', $mutasibank->nobukti_bukubesar_2)->update($databukubesarbank);
            DB::table('ledger_bank')->where('no_bukti', $no_bukti)->update($dataledger);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan Hubungi Tim IT']);;
        }
    }
}
