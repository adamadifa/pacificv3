<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Cabang;
use App\Models\Ledger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $query = Ledger::query();
        $query->select('ledger_bank.*', 'nama_akun');
        $query->join('coa', 'ledger_bank.kode_akun', '=', 'coa.kode_akun');
        $query->join('master_bank', 'ledger_bank.bank', '=', 'master_bank.kode_bank');
        $query->orderBy('tgl_ledger');
        $query->orderBy('date_created');
        $query->whereBetween('tgl_ledger', [$request->dari, $request->sampai]);
        $query->where('ledger_bank.bank', $request->ledger);
        $ledger = $query->get();
        $bank = Bank::orderBy('kode_bank')->get();

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
            ->where('kode_bank', $request->ledger)
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
                ->where('bank', $request->ledger)
                ->where('tgl_ledger', '>=', $tgl_mulai)
                ->where('tgl_ledger', '<', $request->dari)
                ->first();

            $saldoawal = $sa + $mutasi->sisamutasi;
        } else {
            $saldoawal = 0;
        }
        return view('ledger.index', compact('bank', 'ledger', 'saldoawal'));
    }


    public function create($kode_ledger)
    {
        $coa = DB::table('coa')->orderBy('kode_akun')->get();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('ledger.create', compact('kode_ledger', 'coa', 'cabang'));
    }

    public function storetemp(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $kode_ledger = $request->kode_ledger;
        $status_dk = $request->status_dk;
        $tgl_ledger = $request->tgl_ledger;
        $pelanggan = $request->pelanggan;
        $keterangan = $request->keterangan;
        $jumlah  = str_replace(".", "", $request->jumlah);
        $kode_akun = $request->kode_akun;
        $peruntukan = $request->peruntukan;
        $id_user = Auth::user()->id;
        $data = array(
            'tgl_ledger'   => $tgl_ledger,
            'pelanggan'    => $pelanggan,
            'keterangan'   => $keterangan,
            'jumlah'       => $jumlah,
            'kode_akun'    => $kode_akun,
            'status_dk'    => $status_dk,
            'peruntukan'   => $peruntukan,
            'ket_peruntukan'  => $kode_cabang,
            'kode_bank' => $kode_ledger,
            'id_user' => $id_user
        );

        $simpan = DB::table('ledger_temp')->insert($data);
        if ($simpan) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function getledgertemp(Request $request)
    {
        $id_user = Auth::user()->id;
        $kode_bank = $request->kode_ledger;
        $ledgertemp = DB::table('ledger_temp')
            ->select('ledger_temp.*', 'nama_akun')
            ->where('kode_bank', $kode_bank)
            ->where('id_user', $id_user)->join('coa', 'ledger_temp.kode_akun', '=', 'coa.kode_akun')->get();

        //dd($ledgertemp);
        return view('ledger.getledgertemp', compact('ledgertemp'));
    }

    public function cekledgertemp(Request $request)
    {
        $id_user = Auth::user()->id;
        $kode_bank = $request->kode_ledger;
        $cek = DB::table('ledger_temp')
            ->where('kode_bank', $kode_bank)
            ->where('id_user', $id_user)->count();
        echo $cek;
    }

    public function deletetemp(Request $request)
    {
        $hapus = DB::table('ledger_temp')->where('id', $request->id)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function store(Request $request)
    {
        $kode_ledger = $request->kode_ledger;
        $id_user = Auth::user()->id;
        $bank = DB::table('master_bank')->where('kode_bank', $kode_ledger)->first();
        $kode_akun_bank = $bank->kode_akun;
        DB::beginTransaction();
        try {
            $ledgertemp = DB::table('ledger_temp')->where('id_user', $id_user)->where('kode_bank', $kode_ledger)->get();
            foreach ($ledgertemp as $d) {
                $kode_cabang = "PST";
                $tanggal = explode("-", $d->tgl_ledger);
                $bulan = $tanggal[1];
                $tahun = substr($tanggal[0], 2, 2);

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

                $nobukti_bukubesar = buatkode($last_no_bukti_bukubesar, 'GJ' . $bulan . $tahun, 4);

                if ($d->status_dk == "D") {
                    $debetbukubesar = 0;
                    $kreditbukubesar = $d->jumlah;
                } else {
                    $debetbukubesar = $d->jumlah;
                    $kreditbukubesar = 0;
                }
                $databukubesar = array(
                    'no_bukti' => $nobukti_bukubesar,
                    'tanggal' => $d->tgl_ledger,
                    'sumber' => 'ledger',
                    'keterangan' => $d->keterangan,
                    'kode_akun' => $kode_akun_bank,
                    'debet' => $debetbukubesar,
                    'kredit' => $kreditbukubesar,
                    'nobukti_transaksi' => $no_bukti
                );

                DB::table('buku_besar')->insert($databukubesar);
                if ($d->status_dk == "D" && $d->peruntukan == "PC") {
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

                    $dataledger = array(
                        'no_bukti'        => $no_bukti,
                        'bank'            => $d->kode_bank,
                        'tgl_ledger'      => $d->tgl_ledger,
                        'pelanggan'       => $d->pelanggan,
                        'keterangan'      => $d->keterangan,
                        'kode_akun'       => $d->kode_akun,
                        'jumlah'          => $d->jumlah,
                        'status_dk'       => $d->status_dk,
                        'status_validasi' => 1,
                        'peruntukan'      => $d->peruntukan,
                        'ket_peruntukan'  => $d->ket_peruntukan,
                        'kode_cr'         => $kode_cr,
                        'nobukti_bukubesar' => $nobukti_bukubesar
                    );

                    $datacr = [
                        'kode_cr' => $kode_cr,
                        'tgl_transaksi' => $d->tgl_ledger,
                        'kode_akun'    => $d->kode_akun,
                        'keterangan'   => $d->keterangan,
                        'kode_cabang'  => $d->ket_peruntukan,
                        'id_sumber_costratio' => 2,
                        'jumlah' => $d->jumlah
                    ];

                    DB::table('ledger_bank')->insert($dataledger);
                    DB::table('costratio_biaya')->insert($datacr);
                } else {

                    //dd($ledgertemp);
                    $dataledger = array(
                        'no_bukti'        => $no_bukti,
                        'bank'            => $d->kode_bank,
                        'tgl_ledger'      => $d->tgl_ledger,
                        'pelanggan'       => $d->pelanggan,
                        'keterangan'      => $d->keterangan,
                        'kode_akun'       => $d->kode_akun,
                        'jumlah'          => $d->jumlah,
                        'status_dk'       => $d->status_dk,
                        'status_validasi' => 1,
                        'peruntukan'      => $d->peruntukan,
                        'ket_peruntukan'  => $d->ket_peruntukan,
                        'nobukti_bukubesar' => $nobukti_bukubesar
                    );

                    DB::table('ledger_bank')->insert($dataledger);
                }
            }

            DB::table('ledger_temp')->where('id_user', $id_user)->where('kode_bank', $kode_ledger)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan Hubungi Tim IT']);;
        }
    }

    public function delete($no_bukti)
    {
        $no_bukti = Crypt::decrypt($no_bukti);
        $ledger = DB::table('ledger_bank')->where('no_bukti', $no_bukti)->first();
        $kode_cr = $ledger->kode_cr;
        $nobukti_bukubesar = $ledger->nobukti_bukubesar;
        DB::beginTransaction();
        try {
            DB::table('buku_besar')->where('no_bukti', $nobukti_bukubesar)->delete();
            DB::table('costratio_biaya')->where('kode_cr', $kode_cr)->delete();
            DB::table('ledger_bank')->where('no_bukti', $no_bukti)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan Hubungi Tim IT']);;
        }
    }


    public function edit($no_bukti)
    {
        $coa = DB::table('coa')->orderBy('kode_akun')->get();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $ledger = DB::table('ledger_bank')->where('no_bukti', $no_bukti)->first();
        return view('ledger.edit', compact('ledger', 'coa', 'cabang'));
    }

    public function update($no_bukti, Request $request)
    {
        $no_bukti = Crypt::decrypt($no_bukti);
        $ledger = DB::table('ledger_bank')->where('no_bukti', $no_bukti)->first();
        $tgl_ledger = $request->tgl_ledger;
        $pelanggan = $request->pelanggan;
        $keterangan = $request->keterangan;
        $jumlah = str_replace(".", "", $request->jumlah);
        $kode_akun = $request->kode_akun;
        $status_dk = $request->status_dk;
        $peruntukan = $request->peruntukan;
        $ket_peruntukan = $request->kode_cabang;
        $tanggal = explode("-", $tgl_ledger);
        $bulan = $tanggal[1];
        $tahun = substr($tanggal[0], 2, 2);


        if ($status_dk == "D") {
            $debetbukubesar = 0;
            $kreditbukubesar = $jumlah;
        } else {
            $debetbukubesar = $jumlah;
            $kreditbukubesar = 0;
        }
        $databukubesar = [
            'tanggal' => $tgl_ledger,
            'keterangan' => $keterangan,
            'debet' => $debetbukubesar,
            'kredit' => $kreditbukubesar
        ];

        DB::beginTransaction();
        try {

            DB::table('buku_besar')->where('no_bukti', $ledger->nobukti_bukubesar)->update($databukubesar);
            if (empty($ledger->kode_cr)) {
                if ($status_dk == "D" && $peruntukan == "PC") {
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
                        'kode_cabang'  => $ket_peruntukan,
                        'id_sumber_costratio' => 2,
                        'jumlah' => $jumlah
                    ];

                    $dataledger = [
                        'tgl_ledger' => $tgl_ledger,
                        'pelanggan' => $pelanggan,
                        'keterangan' => $keterangan,
                        'jumlah' => $jumlah,
                        'kode_akun' => $kode_akun,
                        'status_dk' => $status_dk,
                        'peruntukan' => $peruntukan,
                        'ket_peruntukan' => $ket_peruntukan,
                        'kode_cr' => $kode_cr
                    ];
                    DB::table('ledger_bank')->where('no_bukti', $no_bukti)->update($dataledger);
                    DB::table('costratio_biaya')->insert($datacr);
                }
            } else {
                if ($status_dk == "D" && $peruntukan == "PC") {
                    $datacr = [
                        'tgl_transaksi' => $tgl_ledger,
                        'kode_akun'    => $kode_akun,
                        'keterangan'   => $keterangan,
                        'kode_cabang'  => $ket_peruntukan,
                        'jumlah' => $jumlah
                    ];
                    $dataledger = [
                        'tgl_ledger' => $tgl_ledger,
                        'pelanggan' => $pelanggan,
                        'keterangan' => $keterangan,
                        'jumlah' => $jumlah,
                        'kode_akun' => $kode_akun,
                        'status_dk' => $status_dk,
                        'peruntukan' => $peruntukan,
                        'ket_peruntukan' => $ket_peruntukan,
                    ];
                    DB::table('ledger_bank')->where('no_bukti', $no_bukti)->update($dataledger);
                    DB::table('costratio_biaya')->where('kode_cr', $ledger->kode_cr)->update($datacr);
                } else {
                    $dataledger = [
                        'tgl_ledger' => $tgl_ledger,
                        'pelanggan' => $pelanggan,
                        'keterangan' => $keterangan,
                        'jumlah' => $jumlah,
                        'kode_akun' => $kode_akun,
                        'status_dk' => $status_dk,
                        'peruntukan' => $peruntukan,
                        'ket_peruntukan' => NULL,
                        'kode_cr' => NULL
                    ];
                    DB::table('ledger_bank')->where('no_bukti', $no_bukti)->update($dataledger);
                    DB::table('costratio_biaya')->where('kode_cr', $ledger->kode_cr)->delete();
                }
            }
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan Hubungi Tim IT']);;
        }
    }
}
