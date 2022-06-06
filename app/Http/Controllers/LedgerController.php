<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Cabang;
use App\Models\Ledger;
use App\Models\Saldoawalledger;
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

                $nobukti_bukubesar = buatkode($last_no_bukti_bukubesar, 'GJ' . $bulan . $tahun, 6);
                $nobukti_bukubesar_bank = buatkode($nobukti_bukubesar, 'GJ' . $bulan . $tahun, 6);

                if ($d->status_dk == "D") {
                    $debet = $d->jumlah;
                    $kredit = 0;
                } else {
                    $debet = 0;
                    $kredit = $d->jumlah;
                }
                $databukubesar = array(
                    'no_bukti' => $nobukti_bukubesar,
                    'tanggal' => $d->tgl_ledger,
                    'sumber' => 'ledger',
                    'keterangan' => $d->keterangan,
                    'kode_akun' => $d->kode_akun,
                    'debet' => $debet,
                    'kredit' => $kredit,
                    'nobukti_transaksi' => $no_bukti
                );


                $databukubesarbank = array(
                    'no_bukti' => $nobukti_bukubesar_bank,
                    'tanggal' => $d->tgl_ledger,
                    'sumber' => 'ledger',
                    'keterangan' => $d->keterangan,
                    'kode_akun' => $kode_akun_bank,
                    'debet' => $kredit,
                    'kredit' => $debet,
                    'nobukti_transaksi' => $no_bukti
                );

                DB::table('buku_besar')->insert($databukubesar);
                DB::table('buku_besar')->insert($databukubesarbank);
                $cekakun = substr($d->kode_akun, 0, 3);
                if ($d->status_dk == 'D' and $cekakun == '6-1' and $d->peruntukan == 'PC' or $d->status_dk == 'D' and $cekakun == '6-2' and $d->peruntukan == 'PC') {
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
                        'nobukti_bukubesar' => $nobukti_bukubesar,
                        'nobukti_bukubesar_2' => $nobukti_bukubesar_bank
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
                        'nobukti_bukubesar' => $nobukti_bukubesar,
                        'nobukti_bukubesar_2' => $nobukti_bukubesar_bank
                    );

                    DB::table('ledger_bank')->insert($dataledger);
                }
            }

            DB::table('ledger_temp')->where('id_user', $id_user)->where('kode_bank', $kode_ledger)->delete();
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
        $ledger = DB::table('ledger_bank')->where('no_bukti', $no_bukti)->first();
        $kode_cr = $ledger->kode_cr;

        DB::beginTransaction();
        try {
            DB::table('buku_besar')->where('no_bukti', $ledger->nobukti_bukubesar)->delete();
            DB::table('buku_besar')->where('no_bukti', $ledger->nobukti_bukubesar_2)->delete();
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
            $debet = $jumlah;
            $kredit = 0;
        } else {
            $debet = 0;
            $kredit = $jumlah;
        }
        $databukubesar = [
            'tanggal' => $tgl_ledger,
            'keterangan' => $keterangan,
            'kode_akun' => $kode_akun,
            'debet' => $debet,
            'kredit' => $kredit
        ];

        $databukubesarbank = [
            'tanggal' => $tgl_ledger,
            'keterangan' => $keterangan,
            'debet' => $kredit,
            'kredit' => $debet
        ];

        DB::beginTransaction();
        try {

            DB::table('buku_besar')->where('no_bukti', $ledger->nobukti_bukubesar)->update($databukubesar);
            DB::table('buku_besar')->where('no_bukti', $ledger->nobukti_bukubesar_2)->update($databukubesarbank);
            if (empty($ledger->kode_cr)) {
                $cekakun = substr($kode_akun, 0, 3);
                if ($status_dk == 'D' and $cekakun == '6-1' and $peruntukan == 'PC' or $status_dk == 'D' and $cekakun == '6-2' and $peruntukan == 'PC') {
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
                } else {
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
                }

                //echo 1;
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

                //echo 2;
            }
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            //return Redirect::back()->with(['warning' => 'Data Gagal Disimpan Hubungi Tim IT']);;
        }
    }

    public function saldoawal(Request $request)
    {
        $query = Saldoawalledger::query();
        if (empty($request->bank) && empty($request->bulan) && empty($request->tahun)) {
            $bulanini = date("m");
            $tahunini = date("Y");
            $query->where('bulan', $bulanini);
            $query->where('tahun', $tahunini);
        } else {
            if (!empty($request->bank)) {
                $query->where('saldoawal_ledger.kode_bank', $request->bank);
            }

            if (!empty($request->bulan)) {
                $query->where('bulan', $request->bulan);
            }

            if (!empty($request->tahun)) {
                $query->where('tahun', $request->tahun);
            }
        }
        $query->join('master_bank', 'saldoawal_ledger.kode_bank', '=', 'master_bank.kode_bank');
        $query->orderBy('bulan');
        $saldoawal = $query->get();

        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $bank = Bank::orderBy('kode_bank')->get();
        return view('ledger.saldoawal', compact('bank', 'bulan', 'saldoawal'));
    }

    public function saldoawal_create()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $bank = Bank::orderBy('kode_bank')->get();
        return view('ledger.saldoawal_create', compact('bulan', 'bank'));
    }

    public function getsaldo(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $bank = $request->bank;

        if ($bulan == 1) {
            $bulanlalu = 12;
            $tahunlalu = $tahun - 1;
        } else {
            $bulanlalu = $bulan - 1;
            $tahunlalu = $tahun;
        }
        $ceksaldosebelumnya = DB::table('saldoawal_ledger')->where('bulan', $bulanlalu)->where('tahun', $tahunlalu)->where('kode_bank', $bank)->count();
        $ceksaldobank = DB::table('saldoawal_ledger')->where('kode_bank', $bank)->count();
        $ceksaldo = DB::table('saldoawal_ledger')->where('bulan', $bulan)->where('tahun', $tahun)->where('kode_bank', $bank)->count();
        if (empty($ceksaldosebelumnya) && !empty($ceksaldobank) || !empty($ceksaldo)) {
            echo 1;
        } else {
            $saldo = DB::table('saldoawal_ledger')->where('kode_bank', $bank)->where('bulan', $bulanlalu)->where('tahun', $tahunlalu)->first();
            $mutasi = DB::table('ledger_bank')
                ->selectRaw("SUM(IF(status_dk='K',jumlah,0)) as kredit,
            SUM(IF(status_dk='D',jumlah,0)) as debet")
                ->whereRaw('MONTH(tgl_ledger)=' . $bulanlalu)
                ->whereRaw('YEAR(tgl_ledger)=' . $tahunlalu)
                ->where('bank', $bank)
                ->first();

            $saldoawal = $saldo->jumlah + $mutasi->kredit - $mutasi->debet;
            echo rupiah($saldoawal);
        }
    }

    public function saldoawal_store(Request $request)
    {
        $kode_saldoawalledger = $request->kode_saldoawalledger;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $tanggal = $tahun . "-" . $bulan . "-01";
        $bank = $request->bank;
        $jumlah = str_replace(".", "", $request->jumlah);
        $data = [
            'kode_saldoawalledger' => $kode_saldoawalledger,
            'tanggal' => $tanggal,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'kode_bank' => $bank,
            'jumlah' => $jumlah,
            'id_admin' => Auth::user()->id
        ];

        $simpan = DB::table('saldoawal_ledger')->insert($data);
        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan Hubungi Tim IT']);
        }
    }

    public function saldoawal_delete($kode_saldoawalledger)
    {
        $kode_saldoawalledger = Crypt::decrypt($kode_saldoawalledger);
        $hapus = DB::table('saldoawal_ledger')->where('kode_saldoawalledger', $kode_saldoawalledger)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus Hubungi Tim IT']);
        }
    }

    public function updatecostratio()
    {
        $dari = "2022-02-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $ledger = DB::table('ledger_bank')
            ->whereBetween('tgl_ledger', [$dari, $sampai])
            ->whereRaw('LEFT(kode_akun,3)="6-1"')
            ->where('peruntukan', 'PC')
            ->orWhereBetween('tgl_ledger', [$dari, $sampai])
            ->whereRaw('LEFT(kode_akun,3)="6-2"')
            ->where('peruntukan', 'PC')
            ->get();

        $kode = "CR0222";
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
        $kode_cr = $last_kode_cr != null ? $cr->kode_cr : "";
        //dd($ledger);
        $ceksimpan = 0;
        $cekupdate = 0;
        DB::beginTransaction();
        try {
            foreach ($ledger as $d) {
                $kode_cr = buatkode($kode_cr, $kode, 4);
                $data = [
                    'kode_cr' => $kode_cr,
                    'tgl_transaksi' => $d->tgl_ledger,
                    'kode_akun' => $d->kode_akun,
                    'keterangan' => $d->keterangan,
                    'kode_cabang' => $d->ket_peruntukan,
                    'id_sumber_costratio' => 2,
                    'jumlah' => $d->jumlah
                ];
                $simpan = DB::table('costratio_biaya')->insert($data);
                $update = DB::table('ledger_bank')->where('no_bukti', $d->no_bukti)->update(['kode_cr' => $kode_cr]);
                if ($simpan) {
                    $ceksimpan++;
                }

                if ($update) {
                    $cekupdate++;
                }
                $kode_cr = $kode_cr;
            }

            echo $ceksimpan . "<br>";
            echo $cekupdate;
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
        }
    }
}
