<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Giro;
use App\Models\Pembayaran;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class PembayaranController extends Controller
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
    public function store(Request $request)
    {
        $id_admin = Auth::user()->id;
        $no_fak_penj = $request->no_fak_penj;
        $tglbayar = $request->tglbayar;
        $bayar = str_replace(".", "", $request->bayar);
        $jenisbayar = $request->jenisbayar;
        $girotocash = $request->girotocash;
        $voucher = $request->voucher;
        $ket_voucher = $request->ket_voucher;
        $id_karyawan = $request->id_karyawan;
        $jenistransaksi = $request->jenistransaksi;
        $kode_cabang = $request->kode_cabang;
        if ($girotocash == 1) {
            $id_giro = $request->id_giro;
        } else {
            $id_giro = NULL;
        }

        if (isset($request->voucher)) {
            $status_bayar = $voucher;
            $ket_voucher = $ket_voucher;
        } else {
            $status_bayar = NULL;
            $ket_voucher = NULL;
        }

        $tanggal    = explode("-", $tglbayar);
        $tahun      = substr($tanggal[0], 2, 2);
        $bulan      = $tanggal[1];
        $pelanggan = DB::table('penjualan')
            ->select('nama_pelanggan')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->where('no_fak_penj', $no_fak_penj)
            ->first();
        $tahunini = date("y");
        $historibayar = DB::table("historibayar")
            ->whereRaw('LEFT(nobukti,6) = "' . $kode_cabang . $tahunini . '-"')
            ->orderBy("nobukti", "desc")
            ->first();

        $lastnobukti = $historibayar != null ? $historibayar->nobukti : '';

        $nobukti  = buatkode($lastnobukti, $kode_cabang . $tahunini . "-", 6);


        $bukubesar = DB::table("buku_besar")
            ->whereRaw('LEFT(no_bukti,6) = "GJ' . $bulan . $tahun . '"')
            ->orderBy("no_bukti", "desc")
            ->first();
        if ($bukubesar == null) {
            $lastno_bukti = '';
        } else {
            $lastno_bukti = $bukubesar->no_bukti;
        }
        $no_bukti_bukubesar  = buatkode($lastno_bukti, 'GJ' . $bulan . $tahun, 6);


        $akun = getAkunpiutangcabang(Auth::user()->kode_cabang);
        DB::beginTransaction();
        try {
            DB::table('historibayar')
                ->insert([
                    'nobukti' => $nobukti,
                    'tglbayar' => $tglbayar,
                    'no_fak_penj' => $no_fak_penj,
                    'jenistransaksi' => $jenistransaksi,
                    'jenisbayar' => $jenisbayar,
                    'bayar' => $bayar,
                    'girotocash' => $girotocash,
                    'status_bayar' => $status_bayar,
                    'ket_voucher' => $ket_voucher,
                    'id_karyawan' => $id_karyawan,
                    'id_giro' => $id_giro,
                    'id_admin' => $id_admin
                ]);
            if ($status_bayar != "voucher") {
                DB::table('buku_besar')
                    ->insert([
                        'no_bukti' => $no_bukti_bukubesar,
                        'tanggal' => $tglbayar,
                        'sumber' => 'Kas Besar',
                        'keterangan' => "Pembayaran Piutang Pelanggan " . $pelanggan->nama_pelanggan,
                        'kode_akun' => $akun,
                        'debet' => $bayar,
                        'kredit' => 0,
                        'nobukti_transaksi' => $nobukti,
                        'no_ref' => $nobukti
                    ]);
            }

            DB::commit();
            return Redirect::back()->with(['success' => 'Data Pembayaran Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            //return Redirect::back()->with(['warning' => 'Data Pembayaran Gagal Disimpan']);
        }
    }

    public function edit(Request $request)
    {
        $kode_cabang = Auth::user()->kode_cabang;
        if ($kode_cabang == "PCF") {
            $salesman = DB::table('karyawan')->orderBy('kode_cabang')->get();
        } else {
            $salesman = DB::table('karyawan')->where('kode_cabang', $kode_cabang)->get();
        }
        $girotolak = DB::table('giro')
            ->select('giro.id_giro', 'no_giro')
            ->leftJoin(
                DB::raw("(
                SELECT id_giro,girotocash FROM historibayar WHERE no_fak_penj ='$request->no_fak_penj'
            ) hb"),
                function ($join) {
                    $join->on('giro.id_giro', '=', 'hb.id_giro');
                }
            )
            ->where('giro.status', 2)
            ->where('giro.no_fak_penj', $request->no_fak_penj)
            ->get();
        $hb = DB::table('historibayar')->where('nobukti', $request->nobukti)->first();
        $sisabayar = $request->sisabayar;
        return view('pembayaran.edit', compact('hb', 'salesman', 'girotolak', 'sisabayar'));
    }


    public function update($nobukti, Request $request)
    {
        $id_admin = Auth::user()->id;
        $tglbayar = $request->tglbayar_edit;
        $bayar = str_replace(".", "", $request->bayar_edit);
        //$jenisbayar = $request->jenisbayar;
        $girotocash = $request->girotocash;
        $voucher = $request->voucher;
        $ket_voucher = $request->ket_voucher;
        $id_karyawan = $request->id_karyawan;
        //$jenistransaksi = $request->jenistransaksi;
        $kode_cabang = $request->kode_cabang;
        if ($girotocash == 1) {
            $id_giro = $request->id_giro;
        } else {
            $id_giro = NULL;
        }

        if (isset($request->voucher)) {
            $status_bayar = $voucher;
            $ket_voucher = $ket_voucher;
        } else {
            $status_bayar = NULL;
            $ket_voucher = NULL;
        }

        $akun = getAkunpiutangcabang(Auth::user()->kode_cabang);

        DB::beginTransaction();
        try {
            DB::table('historibayar')
                ->where('nobukti', $nobukti)
                ->update([
                    'tglbayar' => $tglbayar,
                    // 'jenistransaksi' => $jenistransaksi,
                    // 'jenisbayar' => $jenisbayar,
                    'bayar' => $bayar,
                    'girotocash' => $girotocash,
                    'status_bayar' => $status_bayar,
                    'ket_voucher' => $ket_voucher,
                    'id_karyawan' => $id_karyawan,
                    'id_giro' => $id_giro,
                    'id_admin' => $id_admin
                ]);
            if ($status_bayar != "voucher") {
                DB::table('buku_besar')
                    ->where('no_ref', $nobukti)
                    ->update([
                        'tanggal' => $tglbayar,
                        'debet' => $bayar,
                    ]);
            } else {
                DB::table('buku_besar')
                    ->where('no_ref', $nobukti)
                    ->delete();
            }

            DB::commit();
            return Redirect::back()->with(['success' => 'Data Pembayaran Berhasil Di Update']);
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Pembayaran Gagal Di Update']);
        }
    }

    public function delete($nobukti)
    {
        $nobukti = Crypt::decrypt($nobukti);
        DB::beginTransaction();
        try {
            DB::table('historibayar')
                ->where('nobukti', $nobukti)
                ->delete();
            DB::table('buku_besar')
                ->where('no_ref', $nobukti)
                ->delete();

            DB::commit();
            return Redirect::back()->with(['success' => 'Data Pembayaran Berhasil Di Hapus']);
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Pembayaran Gagal Di Hapus']);
        }
    }

    public function storegiro(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $tgl_giro = $request->tgl_giro;
        $no_giro = $request->no_giro;
        $namabank = $request->namabank_giro;
        $materai = "-";
        $tglcair = $request->tglcair;
        $jumlah = str_replace(".", "", $request->jumlah_giro);
        $id_karyawan = $request->id_karyawan;
        $simpan = DB::table('giro')
            ->insert([
                'no_fak_penj' => $no_fak_penj,
                'tgl_giro' => $tgl_giro,
                'no_giro' => $no_giro,
                'namabank' => $namabank,
                'materai' => $materai,
                'tglcair' => $tglcair,
                'jumlah' => $jumlah,
                'id_karyawan' => $id_karyawan,
                'status' => 0
            ]);

        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Giro Berhasil Di Simpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Giro Gagal Di Hapus']);
        }
    }




    public function deletegiro($id_giro)
    {


        $id_giro = Crypt::decrypt($id_giro);
        $hapus = DB::table('giro')
            ->where('id_giro', $id_giro)
            ->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Giro Berhasil Di Hapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Giro Gagal Di Hapus']);
        }
    }

    public function deleteallnogiro($no_giro)
    {
        $no_giro = Crypt::decrypt($no_giro);
        $hapus = DB::table('giro')
            ->where('no_giro', $no_giro)
            ->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Giro Berhasil Di Hapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Giro Gagal Di Hapus']);
        }
    }

    public function editgiro(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $salesman = DB::table('karyawan')->where('kode_cabang', $kode_cabang)->get();
        $giro = DB::table('giro')->where('id_giro', $request->id_giro)->first();
        $sisabayar = $request->sisabayar;
        return view('pembayaran.editgiro', compact('giro', 'salesman', 'sisabayar'));
    }

    public function updategiro($id_giro, Request $request)
    {
        $tgl_giro = $request->tgl_giro_edit;
        $no_giro = $request->no_giro_edit;
        $namabank = $request->namabank_giro_edit;
        $tglcair = $request->tglcair_edit;
        $jumlah = str_replace(".", "", $request->jumlah_giro_edit);
        $id_karyawan = $request->id_karyawan;
        $simpan = DB::table('giro')
            ->where('id_giro', $id_giro)
            ->update([
                'tgl_giro' => $tgl_giro,
                'no_giro' => $no_giro,
                'namabank' => $namabank,
                'tglcair' => $tglcair,
                'jumlah' => $jumlah,
                'id_karyawan' => $id_karyawan,
            ]);

        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Giro Berhasil Di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Giro Gagal Di Update']);
        }
    }


    public function storetransfer(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $tgl_transfer = $request->tgl_transfer;
        $namabank = $request->namabank_transfer;
        $tglcair = $request->tglcair_transfer;
        $jumlah = str_replace(".", "", $request->jumlah_transfer);
        $id_karyawan = $request->id_karyawan;
        $kode_pelanggan = $request->kode_pelanggan;
        $ket = $request->ket;
        $tgl          = explode("-", $tgl_transfer);
        $tanggal      = $tgl[2];
        $bulan        = $tgl[1];
        $tahun        = substr($tgl[0], 2, 2);
        $kode_transfer =  $kode_pelanggan . $tanggal . $bulan . $tahun . $ket;
        $simpan = DB::table('transfer')
            ->insert([
                'no_fak_penj' => $no_fak_penj,
                'tgl_transfer' => $tgl_transfer,
                'namabank' => $namabank,
                'tglcair' => $tglcair,
                'jumlah' => $jumlah,
                'id_karyawan' => $id_karyawan,
                'kode_transfer' => $kode_transfer,
                'ket' => $ket,
                'status' => 0
            ]);

        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Transfer Berhasil Di Simpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Transfer Gagal Di Hapus']);
        }
    }

    public function deletetransfer($id_transfer)
    {
        $id_transfer = Crypt::decrypt($id_transfer);
        $hapus = DB::table('transfer')
            ->where('id_transfer', $id_transfer)
            ->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Transfer Berhasil Di Hapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Transfer Gagal Di Hapus']);
        }
    }

    public function edittransfer(Request $request)
    {
        $kode_pelanggan = $request->kode_pelanggan;
        $kode_cabang = $request->kode_cabang;
        $salesman = DB::table('karyawan')->where('kode_cabang', $kode_cabang)->get();
        $transfer = DB::table('transfer')->where('id_transfer', $request->id_transfer)->first();
        $sisabayar = $request->sisabayar;
        return view('pembayaran.edittransfer', compact('transfer', 'salesman', 'sisabayar', 'kode_pelanggan'));
    }

    public function updatetransfer($id_transfer, Request $request)
    {
        $tgl_transfer = $request->tgl_transfer_edit;
        $namabank = $request->namabank_transfer_edit;
        $tglcair = $request->tglcair_transfer_edit;
        $jumlah = str_replace(".", "", $request->jumlah_transfer_edit);
        $id_karyawan = $request->id_karyawan;
        $kode_pelanggan = $request->kode_pelanggan_edit;
        $ket = $request->ket_edit;
        $tgl          = explode("-", $tgl_transfer);
        $tanggal      = $tgl[2];
        $bulan        = $tgl[1];
        $tahun        = substr($tgl[0], 2, 2);
        $kode_transfer =  $kode_pelanggan . $tanggal . $bulan . $tahun . $ket;
        $simpan = DB::table('transfer')
            ->where('id_transfer', $id_transfer)
            ->update([
                'tgl_transfer' => $tgl_transfer,
                'namabank' => $namabank,
                'tglcair' => $tglcair,
                'jumlah' => $jumlah,
                'id_karyawan' => $id_karyawan,
                'kode_transfer' => $kode_transfer,
                'ket' => $ket
            ]);

        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Transfer Berhasil Di Di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Transfer Gagal Di Update']);
        }
    }

    public function laporankasbesarpenjualan()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        return view('pembayaran.laporan.frm.lap_kasbesar', compact('cabang'));
    }

    public function cetaklaporankasbesarpenjualan(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        lockreport($dari);
        $cabang = DB::table('cabang')->where('kode_cabang', $request->kode_cabang)->first();
        $salesman = DB::table('karyawan')->where('id_karyawan', $request->id_karyawan)->first();
        $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $request->kode_pelanggan)->first();
        $jenislaporan = $request->jenislaporan;


        if (empty($request->kode_cabang) && $jenislaporan == "rekap") {
            //echo 1;
            $query = Pembayaran::query();
            $query->selectRaw('karyawan.kode_cabang,nama_cabang,SUM(IF(status_bayar="voucher",bayar,0)) as voucher,
            SUM(IF(status_bayar IS NULL,bayar,0)) as cashin');
            $query->join('penjualan', 'historibayar.no_fak_penj', '=', 'penjualan.no_fak_penj');
            $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
            $query->join('karyawan', 'historibayar.id_karyawan', '=', 'karyawan.id_karyawan');
            $query->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');
            $query->whereBetween('tglbayar', [$dari, $sampai]);
            if (!empty($request->jenisbayar)) {
                $query->where('historibayar.jenisbayar', $request->jenisbayar);
            }
            $query->groupByRaw('karyawan.kode_cabang,nama_cabang');
            $kasbesar = $query->get();
            if (isset($_POST['export'])) {
                $time = date("H:i:s");
                // Fungsi header dengan mengirimkan raw data excel
                header("Content-type: application/vnd-ms-excel");
                // Mendefinisikan nama file ekspor "hasil-export.xls"
                header("Content-Disposition: attachment; filename=Rekap Kas Besar All Cabang $dari-$sampai-$time.xls");
            }
            return view('pembayaran.laporan.cetak_kasbesar_rekapallcabang', compact('kasbesar', 'cabang', 'dari', 'sampai', 'salesman', 'pelanggan'));
        } else {
            if ($jenislaporan == "rekap") {
                //echo 2;
                $query = Pembayaran::query();
                $query->selectRaw('historibayar.id_karyawan,nama_karyawan,SUM(IF(status_bayar="voucher",bayar,0)) as voucher,
                SUM(IF(status_bayar IS NULL,bayar,0)) as cashin');
                $query->join('penjualan', 'historibayar.no_fak_penj', '=', 'penjualan.no_fak_penj');
                $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
                $query->join('karyawan', 'historibayar.id_karyawan', '=', 'karyawan.id_karyawan');
                $query->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');
                $query->whereBetween('tglbayar', [$dari, $sampai]);
                $query->where('karyawan.kode_cabang', $request->kode_cabang);
                if (!empty($request->jenisbayar)) {
                    $query->where('historibayar.jenisbayar', $request->jenisbayar);
                }
                $query->groupByRaw('historibayar.id_karyawan,nama_karyawan');
                $kasbesar = $query->get();
                if (isset($_POST['export'])) {
                    $time = date("H:i:s");
                    // Fungsi header dengan mengirimkan raw data excel
                    header("Content-type: application/vnd-ms-excel");
                    // Mendefinisikan nama file ekspor "hasil-export.xls"
                    header("Content-Disposition: attachment; filename=Laporan Kas Besar Salesman Periode $dari-$sampai-$time.xls");
                }
                return view('pembayaran.laporan.cetak_kasbesar_rekapallsalesman', compact('kasbesar', 'cabang', 'dari', 'sampai', 'salesman', 'pelanggan'));
            } else if ($jenislaporan == "lhp") {
                $query = Pembayaran::query();
                $query->select('historibayar.no_fak_penj', 'tglbayar', 'penjualan.kode_pelanggan', 'nama_pelanggan', 'penjualan.jenistransaksi', 'bayar', 'girotocash', 'status_bayar', 'ket_voucher');

                $query->join('penjualan', 'historibayar.no_fak_penj', '=', 'penjualan.no_fak_penj');
                $query->join('karyawan', 'historibayar.id_karyawan', '=', 'karyawan.id_karyawan');
                $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');

                $query->whereBetween('tglbayar', [$dari, $sampai]);
                if (!empty($request->id_karyawan)) {
                    $query->where('historibayar.id_karyawan', $request->id_karyawan);
                }

                if (!empty($request->kode_cabang)) {
                    $query->where('karyawan.kode_cabang', $request->kode_cabang);
                }
                $query->whereNull('historibayar.id_giro');
                $query->whereNull('historibayar.id_transfer');
                $query->whereNull('historibayar.girotocash');

                $query->orwhereBetween('tglbayar', [$dari, $sampai]);
                if (!empty($request->id_karyawan)) {
                    $query->where('historibayar.id_karyawan', $request->id_karyawan);
                }

                if (!empty($request->kode_cabang)) {
                    $query->where('karyawan.kode_cabang', $request->kode_cabang);
                }

                $query->whereNull('historibayar.id_giro');
                $query->whereNull('historibayar.id_transfer');
                $query->where('historibayar.girotocash', 1);


                $query->orwhereBetween('tglbayar', [$dari, $sampai]);
                if (!empty($request->id_karyawan)) {
                    $query->where('historibayar.id_karyawan', $request->id_karyawan);
                }

                if (!empty($request->kode_cabang)) {
                    $query->where('karyawan.kode_cabang', $request->kode_cabang);
                }
                $query->whereNotNull('historibayar.id_giro');
                $query->whereNull('historibayar.id_transfer');
                $query->where('historibayar.girotocash', 1);

                $query->orderBy('tglbayar');
                $query->orderBy('historibayar.no_fak_penj');
                $kasbesar = $query->get();


                //Voucher

                $queryvoucher = Pembayaran::query();
                $queryvoucher->select('historibayar.no_fak_penj', 'tglbayar', 'penjualan.kode_pelanggan', 'nama_pelanggan', 'penjualan.jenistransaksi', 'bayar', 'girotocash', 'status_bayar', 'ket_voucher');

                $queryvoucher->join('penjualan', 'historibayar.no_fak_penj', '=', 'penjualan.no_fak_penj');
                $queryvoucher->join('karyawan', 'historibayar.id_karyawan', '=', 'karyawan.id_karyawan');
                $queryvoucher->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');

                $queryvoucher->whereBetween('tglbayar', [$dari, $sampai]);
                if (!empty($request->id_karyawan)) {
                    $queryvoucher->where('historibayar.id_karyawan', $request->id_karyawan);
                }

                if (!empty($request->kode_cabang)) {
                    $queryvoucher->where('karyawan.kode_cabang', $request->kode_cabang);
                }
                $queryvoucher->whereNull('historibayar.id_giro');
                $queryvoucher->whereNull('historibayar.id_transfer');
                $queryvoucher->whereNull('historibayar.girotocash');
                $queryvoucher->where('status_bayar', 'voucher');

                $queryvoucher->orwhereBetween('tglbayar', [$dari, $sampai]);
                if (!empty($request->id_karyawan)) {
                    $queryvoucher->where('historibayar.id_karyawan', $request->id_karyawan);
                }

                if (!empty($request->kode_cabang)) {
                    $queryvoucher->where('karyawan.kode_cabang', $request->kode_cabang);
                }
                $queryvoucher->whereNull('historibayar.id_giro');
                $queryvoucher->whereNull('historibayar.id_transfer');
                $queryvoucher->where('historibayar.girotocash', 1);
                $queryvoucher->where('status_bayar', 'voucher');

                $queryvoucher->whereBetween('tglbayar', [$dari, $sampai]);
                if (!empty($request->id_karyawan)) {
                    $queryvoucher->where('historibayar.id_karyawan', $request->id_karyawan);
                }

                if (!empty($request->kode_cabang)) {
                    $queryvoucher->where('karyawan.kode_cabang', $request->kode_cabang);
                }
                $queryvoucher->whereNotNull('historibayar.id_giro');
                $queryvoucher->whereNull('historibayar.id_transfer');
                $queryvoucher->where('historibayar.girotocash', 1);

                $queryvoucher->orderBy('tglbayar');
                $queryvoucher->orderBy('historibayar.no_fak_penj');
                $voucher = $queryvoucher->get();

                $querygiro = Giro::query();
                $querygiro->selectRaw("giro.no_fak_penj,penjualan.kode_pelanggan,nama_pelanggan,tgl_giro,no_giro,namabank,jumlah,tglcair,giro.status");
                $querygiro->join('penjualan', 'giro.no_fak_penj', '=', 'penjualan.no_fak_penj');
                $querygiro->join('karyawan', 'giro.id_karyawan', '=', 'karyawan.id_karyawan');
                $querygiro->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
                $querygiro->whereBetween('tgl_giro', [$dari, $sampai]);
                if (!empty($request->id_karyawan)) {
                    $querygiro->where('giro.id_karyawan', $request->id_karyawan);
                }

                if (!empty($request->kode_cabang)) {
                    $querygiro->where('karyawan.kode_cabang', $request->kode_cabang);
                }
                $listgiro = $querygiro->get();

                $querytransfer = Transfer::query();
                $querytransfer->selectRaw("transfer.no_fak_penj,penjualan.kode_pelanggan,nama_pelanggan,tgl_transfer,namabank,jumlah,tglcair,transfer.status,girotocash,kode_transfer");
                $querytransfer->join('penjualan', 'transfer.no_fak_penj', '=', 'penjualan.no_fak_penj');
                $querytransfer->join('karyawan', 'transfer.id_karyawan', '=', 'karyawan.id_karyawan');
                $querytransfer->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
                $querytransfer->leftJoin('historibayar', 'transfer.id_transfer', '=', 'historibayar.id_transfer');
                $querytransfer->whereBetween('tgl_transfer', [$dari, $sampai]);
                if (!empty($request->id_karyawan)) {
                    $querytransfer->where('transfer.id_karyawan', $request->id_karyawan);
                }

                if (!empty($request->kode_cabang)) {
                    $querytransfer->where('karyawan.kode_cabang', $request->kode_cabang);
                }
                $listtransfer = $querytransfer->get();


                return view('pembayaran.laporan.cetak_kasbesarlhp', compact('kasbesar', 'cabang', 'dari', 'sampai', 'salesman', 'pelanggan', 'listgiro', 'listtransfer', 'voucher'));
            } else {
                //echo 3;
                $query = Pembayaran::query();
                $query->selectRaw('historibayar.no_fak_penj,
                datediff(tglbayar,tgltransaksi) as ljt,
                karyawan.nama_karyawan,
                pasar,
                k.nama_karyawan as penagih,
                tgltransaksi,
                tglbayar,
                bayar,
                bayar as bayarterakhir,
                girotocash,status_bayar,historibayar.date_created,historibayar.date_updated,penjualan.status,penjualan.jenistransaksi,
                historibayar.jenisbayar,
                no_giro,
                materai,
                giro.namabank as bankgiro,
                giro.jumlah as jumlahgiro,
                transfer.namabank as banktransfer,
                transfer.jumlah as jumlahtransfer,
                historibayar.id_karyawan,
                penjualan.kode_pelanggan,
                nama_pelanggan,
                users.name,
                ket_voucher,
                (
                    SELECT IFNULL(penjualan.total, 0) - (ifnull(r.totalpf, 0) - ifnull(r.totalgb, 0)) AS totalpiutang
                    FROM penjualan
                    LEFT JOIN (
                        SELECT retur.no_fak_penj AS no_fak_penj,
                        sum(retur.subtotal_gb) AS totalgb,
                        sum(retur.subtotal_pf) AS totalpf
                        FROM
                            retur
                        GROUP BY
                            retur.no_fak_penj
                    ) r ON (penjualan.no_fak_penj = r.no_fak_penj)
                    WHERE penjualan.no_fak_penj = historibayar.no_fak_penj
                ) as totalpenjualan,
                (SELECT IFNULL(SUM(bayar),0)
                FROM historibayar h
                WHERE h.no_fak_penj = historibayar.no_fak_penj
                AND h.tglbayar <= historibayar.tglbayar AND h.tglbayar >= penjualan.tgltransaksi) as totalbayar');
                $query->leftJoin('giro', 'historibayar.id_giro', '=', 'giro.id_giro');
                $query->leftJoin('transfer', 'historibayar.id_transfer', '=', 'transfer.id_transfer');
                $query->leftJoin('users', 'historibayar.id_admin', '=', 'users.id');
                $query->join('penjualan', 'historibayar.no_fak_penj', '=', 'penjualan.no_fak_penj');
                // $query->join('v_movefaktur', 'historibayar.no_fak_penj', '=', 'v_movefaktur.no_fak_penj');
                $query->leftJoin(
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
                    ) v_movefaktur"),
                    function ($join) {
                        $join->on('historibayar.no_fak_penj', '=', 'v_movefaktur.no_fak_penj');
                    }
                );
                $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
                $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
                $query->join('karyawan as k', 'historibayar.id_karyawan', '=', 'k.id_karyawan');
                $query->orderBy('tglbayar', 'asc');
                $query->orderBy('historibayar.no_fak_penj', 'asc');
                $query->whereBetween('tglbayar', [$dari, $sampai]);
                if (!empty($request->kode_cabang)) {
                    $query->where('cabangbarunew', $request->kode_cabang);
                }
                if (!empty($request->id_karyawan)) {
                    $query->where('historibayar.id_karyawan', $request->id_karyawan);
                }
                if (!empty($request->kode_pelanggan)) {
                    $query->where('penjualan.kode_pelanggan', $request->kode_pelanggan);
                }

                if (!empty($request->jenisbayar)) {
                    $query->where('historibayar.jenisbayar', $request->jenisbayar);
                }
                $kasbesar = $query->get();

                $voucher = $query->where('status_bayar', 'voucher')->get();
                if (isset($_POST['export'])) {
                    $time = date("H:i:s");
                    // Fungsi header dengan mengirimkan raw data excel
                    header("Content-type: application/vnd-ms-excel");
                    // Mendefinisikan nama file ekspor "hasil-export.xls"
                    header("Content-Disposition: attachment; filename=Laporan Kas Besar Periode $dari-$sampai-$time.xls");
                }
                return view('pembayaran.laporan.cetak_kasbesar', compact('kasbesar', 'cabang', 'dari', 'sampai', 'salesman', 'pelanggan', 'voucher'));
            }
        }
    }
}
