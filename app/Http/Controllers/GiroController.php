<?php

namespace App\Http\Controllers;

use App\Models\Giro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class GiroController extends Controller
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
        $pelanggan = '"' . $request->nama_pelanggan . '"';
        $query = Giro::query();
        $query->select('giro.no_giro', 'tgl_giro', 'nama_pelanggan', 'karyawan.kode_cabang', 'namabank', DB::raw('SUM(giro.jumlah) as jumlah'), 'tglcair', 'giro.status', 'ket', 'tglbayar', 'ledger_bank.no_bukti', 'nama_bank');
        $query->leftJoin('historibayar', 'giro.id_giro', '=', 'historibayar.id_giro');
        $query->leftJoin('ledger_bank', 'giro.no_giro', '=', 'ledger_bank.no_ref');
        $query->leftJoin('master_bank', 'ledger_bank.bank', '=', 'master_bank.kode_bank');
        $query->join('penjualan', 'giro.no_fak_penj', '=', 'penjualan.no_fak_penj');
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'giro.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->orderBy('tglcair', 'desc');
        $query->groupBy('giro.no_giro', 'tgl_giro', 'nama_pelanggan', 'karyawan.kode_cabang', 'namabank', 'nama_bank', 'tglcair', 'giro.status', 'ket', 'ledger_bank.no_bukti', 'tglbayar');
        if (empty($request->no_giro) && empty($request->nama_pelanggan) && empty($request->dari) && empty($request->sampai) && $request->status === null) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }
        if (!empty($request->nama_pelanggan)) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }

        if (!empty($request->no_giro)) {
            $query->where('giro.no_giro', $request->no_giro);
        }

        if ($request->status !== null) {
            $query->where('giro.status', $request->status);
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tglcair', [$request->dari, $request->sampai]);
        } else {
            $query->where('tglcair', '>=', startreport());
        }
        lockreport($request->dari);
        if ($this->cabang != "PCF") {
            $cbg = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
            $cabang[] = "";
            foreach ($cbg as $c) {
                $cabang[] = $c->kode_cabang;
            }
            $query->whereIn('karyawan.kode_cabang', $cabang);
        }

        if (Auth::user()->level == "salesman") {
            $query->where('penjualan.id_karyawan', Auth::user()->id_salesman);
        }
        $giro = $query->paginate(15);
        $giro->appends($request->all());
        return view('giro.index', compact('giro'));
    }


    public function detailfaktur(Request $request)
    {
        $detailfaktur = DB::table('giro')
            ->select('giro.no_fak_penj', 'jumlah', 'tgl_giro', 'giro.date_created as tgl_input', 'historibayar.date_created as tgl_aksi')
            ->leftJoin('historibayar', 'giro.id_giro', '=', 'historibayar.id_giro')
            ->where('no_giro', $request->no_giro)
            ->get();
        return view('giro.detailfaktur', compact('detailfaktur'));
    }

    public function prosesgiro(Request $request)
    {
        $giro = DB::table('giro')
            ->select('giro.no_giro', 'tgl_giro', 'penjualan.kode_pelanggan', 'nama_pelanggan', 'karyawan.kode_cabang', 'namabank', DB::raw('SUM(giro.jumlah) as jumlah'), 'tglcair', 'giro.status', 'ket', 'tglbayar', 'ledger_bank.no_bukti', 'penjualan.jenistransaksi')
            ->leftJoin('historibayar', 'giro.id_giro', '=', 'historibayar.id_giro')
            ->leftJoin('ledger_bank', 'giro.no_giro', '=', 'ledger_bank.no_ref')
            ->join('penjualan', 'giro.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan')
            ->groupBy('giro.no_giro', 'tgl_giro', 'penjualan.kode_pelanggan', 'nama_pelanggan', 'karyawan.kode_cabang', 'namabank', 'tglcair', 'giro.status', 'ket', 'ledger_bank.no_bukti', 'tglbayar', 'penjualan.jenistransaksi')
            ->where('no_giro', $request->no_giro)
            ->first();
        $bank = DB::table('master_bank')->where('kode_cabang', 'PST')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('giro.prosesgiro', compact('giro', 'bank', 'bulan'));
    }

    public function update(Request $request)
    {
        $no_giro = $request->no_giro;
        $status = $request->statusaksi;
        $tgl_giro = $request->tgl_giro;
        $pelanggan = $request->pelanggan;
        $omsetbulan = $request->bulan;
        $omsettahun = $request->tahun;
        $jatuhtempo = $request->jatuhtempo;
        $jt = explode("-", $jatuhtempo);
        $bulanjt = $jt[1];
        $tahunjt = $jt[0];
        $tgl_ditolak = $request->tgl_ditolak;
        $jumlah  = $request->jumlah;
        $id_admin = Auth::user()->id;
        $bank = $request->bank;


        if ($status == 1) {
            $tglcair = $request->tgl_diterima;
        } else if ($status == 2) {
            $tglcair = $tgl_ditolak;
        } else {
            $tglcair = "";
        }

        $tahunini = date('y');
        $cabang  = $request->kode_cabang;
        $jenistransaksi = $request->jenistransaksi;

        $datagiro = DB::table('giro')->where('no_giro', $no_giro)->get();


        // //Setoran Pusat
        // $lastsetoranpusat = DB::table('setoran_pusat')
        // ->select('kode_setoranpusat')
        // ->whereRaw('LEFT(kode_setoranpusat,4) = "SB' .$tahunini. '"')
        // ->orderBy('kode_setoranpusat','desc')
        // ->first();
        // if($lastsetoranpusat == null){
        //     $lastkode_setoranpusat = 'SB'.$tahunini.'00000';
        // }else{
        //     $lastkode_setoranpusat = $lastsetoranpusat->kode_setoranpusat;
        // }
        // $kode_setoranpusat = buatkode($lastkode_setoranpusat, 'SB' . $tahunini, 5);




        $listfaktur = "";
        $id_giro = [];
        foreach ($datagiro as $d) {
            $listfaktur = $listfaktur .= $d->no_fak_penj . ",";
            $id_giro[] = $d->id_giro;
        }

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

        DB::beginTransaction();
        try {
            if ($status == 1) {
                $b = DB::table('master_bank')->where('kode_bank', $bank)->first();
                $kode_akun_bank = $b->kode_akun;
                //Ledger
                $ledger = DB::table('ledger_bank')->where('no_ref', $no_giro)->first();
                $tanggal = explode("-", $tglcair);
                $tahun = substr($tanggal[0], 2, 2);
                $bln = $tanggal[1];


                if ($cabang == "PST") {
                    $lastledger = DB::table('ledger_bank')
                        ->select('no_bukti')
                        ->whereRaw('LEFT(no_bukti,7) ="LR' . $cabang . $tahun . '"')
                        ->whereRaw('LENGTH(no_bukti)=12')
                        ->orderBy('no_bukti', 'desc')
                        ->first();
                    if ($lastledger == null) {
                        $last_no_bukti = 'LR' . $cabang . $tahun . '0000';
                    } else {
                        $last_no_bukti = $lastledger->no_bukti;
                    }
                    $no_bukti = buatkode($last_no_bukti, 'LR' . $cabang . $tahun, 5);
                } else {
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
                }

                //Buku Besar
                $lastbukubesar = DB::table('buku_besar')
                    ->select('no_bukti')
                    ->whereRaw('LEFT(no_bukti,6)="GJ' . $bln . $tahun . '"')
                    ->orderBy('no_bukti', 'desc')
                    ->first();
                if ($lastbukubesar == null) {
                    $last_no_bukti_bb = '';
                } else {
                    $last_no_bukti_bb =  $lastbukubesar->no_bukti;
                }
                $no_bukti_bb   = buatkode($last_no_bukti_bb, 'GJ' . $bln . $tahun, 6);
                $nobukti_bukubesar_bank   = buatkode($no_bukti_bb, 'GJ' . $bln . $tahun, 6);

                //Update Setoran Pusat
                DB::table('setoran_pusat')
                    ->where('no_ref', $no_giro)
                    ->update([
                        'tgl_diterimapusat' => $tglcair,
                        'bank' => $bank,
                        'status'  => '1',
                        'omset_bulan' => $omsetbulan,
                        'omset_tahun' => $omsettahun
                    ]);


                //dd($id_giro);

                //Hapus Buku Besar

                if ($ledger != null) {
                    DB::table('buku_besar')->where('no_bukti', $ledger->nobukti_bukubesar)->delete();
                    DB::table('buku_besar')->where('no_bukti', $ledger->nobukti_bukubesar_2)->delete();
                }

                //dd($id_giro);
                //Hapus Ledger
                DB::table('ledger_bank')
                    ->where('no_ref', $no_giro)
                    ->delete();
                //Hapus Kas Besar
                $hb = DB::table('historibayar')->whereIn('id_giro', $id_giro)->get();
                $nobukti_hb = [];
                foreach ($hb as $d) {
                    $nobukti_hb[] = $d->nobukti;
                }

                if ($hb != null) {
                    DB::table('buku_besar')->whereIn('nobukti_transaksi', $nobukti_hb)->delete();
                }
                DB::table('historibayar')->whereIn('id_giro', $id_giro)->delete();
                //Insert Ledger
                DB::table('ledger_bank')
                    ->insert([
                        'no_bukti'        => $no_bukti,
                        'no_ref'          => $no_giro,
                        'bank'            => $bank,
                        'tgl_ledger'      => $tglcair,
                        'tgl_penerimaan'  => $tgl_giro,
                        'pelanggan'       => $pelanggan,
                        'keterangan'      => "INV " . $listfaktur,
                        'kode_akun'       => $akun,
                        'jumlah'          => $jumlah,
                        'status_dk'       => 'K',
                        'status_validasi' => 1,
                        'kategori'        => 'PNJ',
                        'nobukti_bukubesar' => $no_bukti_bb,
                        'nobukti_bukubesar_2' => $nobukti_bukubesar_bank
                    ]);

                //Insert Buku Besar
                DB::table('buku_besar')
                    ->insert([
                        'no_bukti' => $no_bukti_bb,
                        'tanggal' => $tglcair,
                        'sumber' => 'ledger',
                        'keterangan' => "INV " . $listfaktur,
                        'kode_akun' => $akun,
                        'debet' => 0,
                        'kredit' => $jumlah,
                        'nobukti_transaksi' => $no_bukti,
                        'no_ref' => $no_bukti
                    ]);

                $databukubesarbank = array(
                    'no_bukti' => $nobukti_bukubesar_bank,
                    'tanggal' => $tglcair,
                    'sumber' => 'ledger',
                    'keterangan' => "INV " . $listfaktur,
                    'kode_akun' => $kode_akun_bank,
                    'debet' => $jumlah,
                    'kredit' => 0,
                    'no_ref' => $no_bukti,
                    'nobukti_transaksi' => $no_bukti
                );

                DB::table('buku_besar')->insert($databukubesarbank);
            } else if ($status == 2) {
                $ledger = DB::table('ledger_bank')->where('no_ref', $no_giro)->first();
                if ($ledger != null) {
                    $nobukti_ledger = $ledger->no_bukti;
                    //Hapus Buku Besar
                    DB::table('buku_besar')->where('no_ref', $nobukti_ledger)->delete();
                }
                //Hapus Ledger
                DB::table('ledger_bank')->where('no_ref', $no_giro)->delete();
                //Update Setoran Pusat
                DB::table('setoran_pusat')
                    ->where('no_ref', $no_giro)
                    ->update([
                        'tgl_diterimapusat'  => $tglcair,
                        'bank'  => $bank,
                        'status' => '2',
                        'omset_bulan' => $bulanjt,
                        'omset_tahun' => $tahunjt
                    ]);
            } else {
                $ledger = DB::table('ledger_bank')->where('no_ref', $no_giro)->first();
                if ($ledger != null) {
                    $nobukti_ledger = $ledger->no_bukti;
                } else {
                    $nobukti_ledger = "";
                }
                //Hapus  Ledger
                DB::table('ledger_bank')->where('no_ref', $no_giro)->delete();
                //Hapus Buku Besar
                DB::table('buku_besar')->where('no_ref', $nobukti_ledger)->delete();
                //Update Setoran Pusat
                DB::table('setoran_pusat')
                    ->where('no_ref', $no_giro)
                    ->update([
                        'tgl_diterimapusat'  => NULL,
                        'bank'              => $bank,
                        'status'            => '0',
                        'omset_bulan'       => 0,
                        'omset_tahun'       => ''
                    ]);
            }

            foreach ($datagiro as $d) {
                if ($status == 1) {
                    $tanggal    = explode("-", $tglcair);
                    $tahun      = substr($tanggal[0], 2, 2);
                    $bulan      = $tanggal[1];
                    DB::table('giro')
                        ->where('id_giro', $d->id_giro)
                        ->update([
                            'status' => $status,
                            'bank_penerima' => $bank,
                            'jumlah' => $d->jumlah,
                            'omset_bulan' => $omsetbulan,
                            'omset_tahun' => $omsettahun
                        ]);
                    // $cekbayar = DB::table('historibayar')->where('id_giro', $d->id_giro)->count();
                    // if (empty($cekbayar)) {
                    $tahunini = date("y");
                    $historibayar = DB::table("historibayar")
                        ->whereRaw('LEFT(nobukti,6) = "' . $cabang . $tahunini . '-"')
                        ->orderBy("nobukti", "desc")
                        ->first();
                    if ($historibayar == null) {
                        $lastnobukti = $cabang . $tahunini . '-000000';
                    } else {
                        $lastnobukti = $historibayar->nobukti;
                    }
                    $nobukti  = buatkode($lastnobukti, $cabang . $tahunini . "-", 6);
                    DB::table('historibayar')
                        ->insert([
                            'nobukti' => $nobukti,
                            'no_fak_penj' => $d->no_fak_penj,
                            'tglbayar' => $tglcair,
                            'jenistransaksi' => $jenistransaksi,
                            'jenisbayar' => 'giro',
                            'bayar' => $d->jumlah,
                            'id_giro' => $d->id_giro,
                            'id_karyawan' => $d->id_karyawan,
                            'id_admin' => $id_admin
                        ]);


                    $bukubesar = DB::table("buku_besar")
                        ->whereRaw('LEFT(no_bukti,6) = "GJ' . $bulan . $tahun . '"')
                        ->orderBy("no_bukti", "desc")
                        ->first();
                    $lastno_bukti = $bukubesar != null ? $bukubesar->no_bukti : '';
                    $no_bukti_bukubesar  = buatkode($lastno_bukti, 'GJ' . $bulan . $tahun, 6);

                    DB::table('buku_besar')
                        ->insert([
                            'no_bukti' => $no_bukti_bukubesar,
                            'tanggal' => $tglcair,
                            'sumber' => 'Kas Besar',
                            'keterangan' => "Pembayaran Piutang Pelanggan " . $pelanggan,
                            'kode_akun' => $akun,
                            'debet' => $d->jumlah,
                            'kredit' => 0,
                            'nobukti_transaksi' => $nobukti,
                            'no_ref' => $nobukti
                        ]);
                    // } else {
                    //     DB::table('historibayar')
                    //         ->where('id_giro', $d->id_giro)
                    //         ->update([
                    //             'tglbayar' => $tglcair,
                    //             'bayar' => $d->jumlah
                    //         ]);
                    // }
                } else if ($status == 2) {
                    DB::table('giro')
                        ->where('id_giro', $d->id_giro)
                        ->update([
                            'tgl_ditolak'     => $tgl_ditolak,
                            'bank_penerima'   => $bank,
                            'status'          => $status,
                            'omset_bulan'     => $bulanjt,
                            'omset_tahun'     => $tahunjt
                        ]);
                    $hb = DB::table('historibayar')->where('id_giro', $d->id_giro)->first();
                    if ($hb != null) {
                        DB::table('buku_besar')
                            ->where('no_ref', $hb->nobukti)
                            ->delete();
                    }


                    DB::table('historibayar')->where('id_giro', $d->id_giro)->delete();
                } else {
                    DB::table('giro')
                        ->where('id_giro', $d->id_giro)
                        ->update([
                            'bank_penerima' => '',
                            'status'        => $status,
                            'omset_bulan'   => 0,
                            'omset_tahun'   => ''
                        ]);
                    $hb = DB::table('historibayar')->where('id_giro', $d->id_giro)->first();
                    if ($hb != null) {
                        DB::table('buku_besar')
                            ->where('no_ref', $hb->nobukti)
                            ->delete();
                    }
                    DB::table('historibayar')->where('id_giro', $d->id_giro)->delete();
                }
            }
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Giro Berhasil di Update']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Giro Gagal di Update,  Silahkan Hubungi Tim IT']);
        }
    }
}
