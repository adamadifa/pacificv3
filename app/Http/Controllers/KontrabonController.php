<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Cabang;
use App\Models\Kontrabon;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class KontrabonController extends Controller
{
    public function index(Request $request)
    {
        $query = Kontrabon::query();
        $query->selectRaw("kontrabon.no_kontrabon,no_dokumen,tgl_kontrabon,kategori,nama_supplier,totalbayar,tglbayar,jenisbayar,via,status");
        $query->join('supplier', 'kontrabon.kode_supplier', '=', 'supplier.kode_supplier');
        $query->leftJoin('historibayar_pembelian', 'kontrabon.no_kontrabon', '=', 'historibayar_pembelian.no_kontrabon');
        $query->leftJoin(
            DB::raw('(
                SELECT no_kontrabon,SUM(jmlbayar) as totalbayar
                FROM detail_kontrabon
                GROUP BY no_kontrabon
            ) detailkontrabon'),
            function ($join) {
                $join->on('kontrabon.no_kontrabon', '=', 'detailkontrabon.no_kontrabon');
            }
        );

        if (!empty($request->no_kontrabon)) {
            $query->where('kontrabon.no_kontrabon', $request->no_kontrabon);
        }

        if (!empty($request->no_dokumen)) {
            $query->where('kontrabon.no_dokumen', $request->no_dokumen);
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_kontrabon', [$request->dari, $request->sampai]);
        } else {
            $query->where('tgl_kontrabon', '>=', startreport());
        }

        lockreport($request->dari);
        if (!empty($request->kode_supplier)) {
            $query->where('kontrabon.kode_supplier', $request->kode_supplier);
        }

        if (!empty($request->status)) {
            if ($request->status == 1) {
                $query->whereNull('tglbayar');
            } else {
                $query->whereNotNull('tglbayar');
            }
        }

        if (!empty($request->kategori)) {
            $query->where('kategori', $request->kategori);
        }
        $query->orderBy('tgl_kontrabon', 'desc');
        $kontrabon = $query->paginate(15);
        $kontrabon->appends($request->all());
        $supplier = Supplier::orderBy('nama_supplier')->get();
        $level = Auth::user()->level;
        $hakakses = config('global.pinjamanpage');

        return view('kontrabon.index', compact('supplier', 'kontrabon'));
    }

    public function show(Request $request)
    {
        $no_kontrabon = Crypt::decrypt($request->no_kontrabon);
        $kontrabon = DB::table('kontrabon')
            ->select('kontrabon.*', 'nama_supplier')
            ->join('supplier', 'kontrabon.kode_supplier', '=', 'supplier.kode_supplier')
            ->where('no_kontrabon', $no_kontrabon)->first();
        $detailkontrabon = DB::table('detail_kontrabon')
            ->select('detail_kontrabon.*', 'tgl_pembelian')
            ->join('pembelian', 'detail_kontrabon.nobukti_pembelian', '=', 'pembelian.nobukti_pembelian')
            ->where('no_kontrabon', $no_kontrabon)->get();
        return view('kontrabon.show', compact('kontrabon', 'detailkontrabon'));
    }

    public function create()
    {
        return view('kontrabon.create');
    }

    public function storetemp(Request $request)
    {
        $id_admin = Auth::user()->id;
        $nobukti_pembelian = $request->nobukti_pembelian;
        $kode_supplier = $request->kode_supplier;
        $jml_bayar = str_replace(".", "", $request->jmlbayar);
        $jmlbayar = str_replace(",", ".", $jml_bayar);
        $keterangan = $request->keterangan;
        $cek = DB::table('detailkontrabon_temp')->where('nobukti_pembelian', $nobukti_pembelian)->where('id_admin', $id_admin)->count();
        if (!empty($cek)) {
            echo 1;
        } else {
            $data = [
                'nobukti_pembelian' => $nobukti_pembelian,
                'kode_supplier' => $kode_supplier,
                'jmlbayar' => $jmlbayar,
                'keterangan' => $keterangan,
                'id_admin' => $id_admin
            ];

            $simpan = DB::table('detailkontrabon_temp')->insert($data);
            if ($simpan) {
                echo 0;
            } else {
                echo 2;
            }
        }
    }

    public function showtemp(Request $request)
    {
        $kode_supplier = $request->kode_supplier;
        $id_admin = Auth::user()->id;
        $detail = DB::table('detailkontrabon_temp')->where('id_admin', $id_admin)->where('kode_supplier', $kode_supplier)->get();
        return view('kontrabon.showtemp', compact('detail'));
    }

    public function deletetemp(Request $request)
    {
        $nobukti_pembelian = $request->nobukti_pembelian;
        $hapus = DB::table('detailkontrabon_temp')->where('nobukti_pembelian', $nobukti_pembelian)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function store(Request $request)
    {
        $no_kontrabon = $request->no_kontrabon;
        $tgl_kontragbon = $request->tgl_kontrabon;
        $kategori = $request->kategori;
        $kode_supplier = $request->kode_supplier;
        $no_dokumen = $request->no_dokumen;
        $jenisbayar = $request->jenisbayar;
        $id_admin = Auth::user()->id;

        $detailtemp = DB::table('detailkontrabon_temp')
            ->where('kode_supplier', $kode_supplier)
            ->where('id_admin', $id_admin)->get();
        DB::beginTransaction();
        try {
            $data = [
                'no_kontrabon' => $no_kontrabon,
                'tgl_kontrabon' => $tgl_kontragbon,
                'kategori' => $kategori,
                'kode_supplier' => $kode_supplier,
                'no_dokumen' => $no_dokumen,
                'jenisbayar' => $jenisbayar,
                'id_admin' => Auth::user()->id
            ];

            DB::table('kontrabon')->insert($data);
            foreach ($detailtemp as $d) {
                $datadetail = [
                    'no_kontrabon' => $no_kontrabon,
                    'nobukti_pembelian' => $d->nobukti_pembelian,
                    'jmlbayar' => $d->jmlbayar,
                    'keterangan' => $d->keterangan
                ];
                DB::table('detail_kontrabon')->insert($datadetail);
            }
            DB::table('detailkontrabon_temp')->where('kode_supplier', $kode_supplier)->where('id_admin', $id_admin)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Kontrabon Berhasil di Simpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Kontrabon Gagal di Simpan']);
        }
    }

    public function edit($no_kontrabon)
    {
        $no_kontrabon = Crypt::decrypt($no_kontrabon);
        $kontrabon = DB::table('kontrabon')
            ->select('kontrabon.*', 'nama_supplier')
            ->join('supplier', 'kontrabon.kode_supplier', '=', 'supplier.kode_supplier')
            ->where('no_kontrabon', $no_kontrabon)->first();
        $detail = DB::table('detail_kontrabon')->where('no_kontrabon', $no_kontrabon)->get();
        return view('kontrabon.edit', compact('kontrabon', 'detail'));
    }

    public function showdetail(Request $request)
    {
        $no_kontrabon = $request->no_kontrabon;
        $detail = DB::table('detail_kontrabon')->where('no_kontrabon', $no_kontrabon)->get();
        return view('kontrabon.showdetail', compact('detail'));
    }

    public function storedetail(Request $request)
    {
        $no_kontrabon = $request->no_kontrabon;
        $nobukti_pembelian = $request->nobukti_pembelian;
        $jml_bayar = str_replace(".", "", $request->jmlbayar);
        $jmlbayar = str_replace(",", ".", $jml_bayar);
        $keterangan = $request->keterangan;
        $cek = DB::table('detail_kontrabon')->where('nobukti_pembelian', $nobukti_pembelian)->where('no_kontrabon', $no_kontrabon)->count();
        if (!empty($cek)) {
            echo 1;
        } else {
            $data = [
                'no_kontrabon' => $no_kontrabon,
                'nobukti_pembelian' => $nobukti_pembelian,
                'jmlbayar' => $jmlbayar,
                'keterangan' => $keterangan,
            ];

            $simpan = DB::table('detail_kontrabon')->insert($data);
            if ($simpan) {
                echo 0;
            } else {
                echo 2;
            }
        }
    }

    public function deletedetail(Request $request)
    {
        $no_kontrabon = $request->no_kontrabon;
        $nobukti_pembelian = $request->nobukti_pembelian;
        $hapus = DB::table('detail_kontrabon')->where('no_kontrabon', $no_kontrabon)->where('nobukti_pembelian', $nobukti_pembelian)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function updatedetail(Request $request)
    {
        $no_kontrabon = $request->no_kontrabon;
        $nobukti_pembelian = $request->nobukti_pembelian;
        $jml_bayar = str_replace(".", "", $request->jmlbayar);
        $jmlbayar = str_replace(",", ".", $jml_bayar);

        $data = [
            'jmlbayar' => $jmlbayar
        ];
        $update = DB::table('detail_kontrabon')->where('no_kontrabon', $no_kontrabon)->where('nobukti_pembelian', $nobukti_pembelian)->update($data);
        if ($update) {
            echo 0;
        } else {
            echo 2;
        }
    }

    public function update($no_kontrabon, Request $request)
    {
        $no_kontrabon = Crypt::decrypt($no_kontrabon);
        $tgl_kontrabon = $request->tgl_kontrabon;
        $kategori = $request->kategori;
        $jenisbayar = $request->jenisbayar;
        $no_dokumen = $request->no_dokumen;

        $data = [
            'tgl_kontrabon' => $tgl_kontrabon,
            'kategori' => $kategori,
            'jenisbayar' => $jenisbayar,
            'no_dokumen' => $no_dokumen
        ];

        $update = DB::table('kontrabon')->where('no_kontrabon', $no_kontrabon)->update($data);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan !']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT !']);
        }
    }

    public function delete($no_kontrabon)
    {
        $no_kontrabon = Crypt::decrypt($no_kontrabon);
        $hapus = DB::table('kontrabon')->where('no_kontrabon', $no_kontrabon)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => "Data Gagal Dihapus, Hubungi Tim IT"]);
        }
    }

    public function proseskontrabon(Request $request)
    {
        $no_kontrabon = Crypt::decrypt($request->no_kontrabon);
        $kontrabon = DB::table('kontrabon')
            ->select('kontrabon.*', 'nama_supplier')
            ->join('supplier', 'kontrabon.kode_supplier', '=', 'supplier.kode_supplier')
            ->where('no_kontrabon', $no_kontrabon)->first();
        $detailkontrabon = DB::table('detail_kontrabon')
            ->select('detail_kontrabon.*', 'tgl_pembelian')
            ->join('pembelian', 'detail_kontrabon.nobukti_pembelian', '=', 'pembelian.nobukti_pembelian')
            ->where('no_kontrabon', $no_kontrabon)->get();
        $bank = Bank::orderBy('kode_bank')->get();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('kontrabon.proseskontrabon', compact('kontrabon', 'detailkontrabon', 'bank', 'cabang'));
    }

    public function editkontrabon(Request $request)
    {
        $no_kontrabon = Crypt::decrypt($request->no_kontrabon);
        $kontrabon = DB::table('kontrabon')
            ->select('kontrabon.*', 'nama_supplier')
            ->join('supplier', 'kontrabon.kode_supplier', '=', 'supplier.kode_supplier')
            ->where('no_kontrabon', $no_kontrabon)->first();
        $detailkontrabon = DB::table('detail_kontrabon')
            ->select('detail_kontrabon.*', 'tgl_pembelian')
            ->join('pembelian', 'detail_kontrabon.nobukti_pembelian', '=', 'pembelian.nobukti_pembelian')
            ->where('no_kontrabon', $no_kontrabon)->get();
        $bank = Bank::orderBy('kode_bank')->get();
        $bayar = DB::table('historibayar_pembelian')->where('no_kontrabon', $no_kontrabon)->first();
        $ledger = DB::table('ledger_bank')->where('no_ref', $no_kontrabon)->first();
        $kaskecil = DB::table('kaskecil_detail')->where('no_ref', $no_kontrabon)->first();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('kontrabon.editkontrabon', compact('kontrabon', 'detailkontrabon', 'bank', 'cabang', 'ledger', 'bayar', 'kaskecil'));
    }

    public function storeproseskontrabon(Request $request)
    {
        $no_kontrabon = $request->no_kontrabon;
        $supplier = DB::table('supplier')->where('kode_supplier', $request->kode_supplier)->first();
        $tglbayar = $request->tglbayar;
        $bayar = $request->jmlbayar;
        $kode_bank = $request->kode_bank;
        $kode_akun = $request->kode_akun;
        $cekcabang = $request->cekcabang;
        $kode_cabang = $request->kode_cabang;
        $keterangan = $request->keterangan;
        $no_bkk = $request->no_bkk;
        $pelanggan = $supplier->nama_supplier;;
        $id_user = Auth::user()->id;
        $tanggal = explode("-", $tglbayar);
        $bulan = $tanggal[1];
        $tahun = substr($tanggal[0], 2, 2);
        $kategori_transaksi = $kode_bank == "KAS" ? $request->kategori_transaksi : NULL;
        $bank = DB::table('master_bank')->where('kode_bank', $kode_bank)->first();
        $kode_akun_bank = $bank->kode_akun;
        $akun = [
            'BDG' => '1-1102',
            'BGR' => '1-1103',
            'PST' => '1-1111',
            'TSM' => '1-1112',
            'SKB' => '1-1113',
            'PWT' => '1-1114',
            'TGL' => '1-1115',
            'SBY' => '1-1116',
            'SMR' => '1-1117',
            'KLT' => '1-1118',
            'GRT' => '1-1119'
        ];

        $cbg = "PST";
        $ledger = DB::table('ledger_bank')->select('no_bukti')->whereRaw('LEFT(no_bukti,7)="LR' . $cbg . $tahun . '"')->orderBy('no_bukti', 'desc')->first();
        if ($ledger != null) {
            $lastno_bukti = $ledger->no_bukti;
        } else {
            $lastno_bukti = "";
        }

        $ceklastnobukti = substr($lastno_bukti, 7, 5);

        if ($ceklastnobukti >= 9999) {
            $no_bukti = buatkode($lastno_bukti, 'LR' . $cbg . $tahun, 5);
        } else {
            $no_bukti = buatkode($lastno_bukti, 'LR' . $cbg . $tahun, 4);
        }




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
        $nobukti_bukubesar_trans_kk = buatkode($nobukti_bukubesar_bank, 'GJ' . $bulan . $tahun, 6);
        $nobukti_bukubesar_kk = buatkode($nobukti_bukubesar_trans_kk, 'GJ' . $bulan . $tahun, 6);


        $nobukti_pembelian = "";
        $detailkontrabon = DB::table('detail_kontrabon')->where('no_kontrabon', $no_kontrabon);
        $cekkontrabon = $detailkontrabon->count();
        $datadetailkontrabon = $detailkontrabon->get();
        foreach ($datadetailkontrabon as $d) {
            if ($cekkontrabon > 1) {
                $nobukti_pembelian .= $d->nobukti_pembelian . ",";
            } else {
                $nobukti_pembelian .= $d->nobukti_pembelian;
            }

            if ($kode_bank == "KAS KECIL") {
                // $detailpembelian = DB::table('detail_pembelian')
                //     ->selectRaw("detail_pembelian.kode_barang,nama_barang,((qty*harga)+penyesuaian) as totalharga")
                //     ->join('master_barang_pembelian', 'detail_pembelian.kode_barang', 'master_barang_pembelian.kode_barang')
                //     ->where('nobukti_pembelian', $d->nobukt_pembelian)
                //     ->get();

                // foreach ($detailpembelian as $p) {
                //     $barang[] = "PEMB " . $p->nama_barang;
                //     $datakk[] = [
                //         'nobukti' => $no_bkk,
                //         'tgl_kaskecil' => $tglbayar,
                //         'keterangan' => "PEMB " . $p->nama_barang,
                //         'jumlah' => $p->totalharga,
                //         'status_dk' => "D",
                //         'kode_akun' => "2-1300"
                //     ];
                // }
            }

            $data = array(
                'no_kontrabon' => $no_kontrabon,
                'bayar'        => $bayar,
                'tglbayar'     => $tglbayar,
                'via'          => $kode_bank,
                'kode_cabang'  => $kode_cabang,
                'id_admin'     => $id_user
            );

            $dataledger = array(
                'no_bukti'              => $no_bukti,
                'no_ref'                => $no_kontrabon,
                'pelanggan'             => $pelanggan,
                'bank'                  => $kode_bank,
                'tgl_ledger'            => $tglbayar,
                'keterangan'            => $keterangan . " " . $nobukti_pembelian,
                'kode_akun'             => $kode_akun,
                'jumlah'                => $bayar,
                'status_dk'             => 'D',
                'status_validasi'       => 1,
                'kategori'              => 'PMB',
                'peruntukan'            => $kategori_transaksi,
                'nobukti_bukubesar'     => $nobukti_bukubesar,
                'nobukti_bukubesar_2'   => $nobukti_bukubesar_bank
            );


            $databukubesar = array(
                'no_bukti' => $nobukti_bukubesar,
                'tanggal' => $tglbayar,
                'sumber' => 'ledger',
                'keterangan' => $keterangan . " " . $nobukti_pembelian,
                'kode_akun' => $kode_akun,
                'debet' => $bayar,
                'kredit' => 0,
                'nobukti_transaksi' => $no_bukti
            );


            $databukubesarbank = array(
                'no_bukti' => $nobukti_bukubesar_bank,
                'tanggal' => $tglbayar,
                'sumber' => 'ledger',
                'keterangan' => $keterangan . " " . $nobukti_pembelian,
                'kode_akun' => $kode_akun_bank,
                'debet' => 0,
                'kredit' => $bayar,
                'nobukti_transaksi' => $no_bukti
            );


            DB::beginTransaction();
            try {
                //Simpan Histori Pembayaran

                if ($kode_bank == "KAS KECIL") {
                    $databukubesar_kk = array(
                        'no_bukti' => $nobukti_bukubesar_kk,
                        'tanggal' => $tglbayar,
                        'sumber' => 'Kas Kecil',
                        'keterangan' => $keterangan . " " . $nobukti_pembelian,
                        'kode_akun' => $akun[$kode_cabang],
                        'debet' => 0,
                        'kredit' => $bayar,
                        'nobukti_transaksi' => $no_bkk,
                    );


                    $databukubesartrans = array(
                        'no_bukti' => $nobukti_bukubesar_trans_kk,
                        'tanggal' => $tglbayar,
                        'sumber' => 'Kas Kecil',
                        'keterangan' =>  $keterangan . " " . $nobukti_pembelian,
                        'kode_akun' => $kode_akun,
                        'debet' => $bayar,
                        'kredit' => 0,
                        'nobukti_transaksi' => $no_bkk,
                    );
                    $datakaskecil = [
                        'nobukti' => $no_bkk,
                        'no_ref' => $no_kontrabon,
                        'tgl_kaskecil' => $tglbayar,
                        'keterangan' => $keterangan . " " . $nobukti_pembelian,
                        'jumlah' => $bayar,
                        'status_dk' => "D",
                        'kode_cabang' => $kode_cabang,
                        'kode_akun' => $kode_akun,
                        'nobukti_bukubesar' => $nobukti_bukubesar_kk,
                        'nobukti_bukubesar_2' => $nobukti_bukubesar_trans_kk
                    ];

                    DB::table('kaskecil_detail')->insert($datakaskecil);
                    DB::table('buku_besar')->insert($databukubesar_kk);
                    DB::table('buku_besar')->insert($databukubesartrans);
                } else {
                    DB::table('ledger_bank')->insert($dataledger);
                    DB::table('buku_besar')->insert($databukubesar);
                    DB::table('buku_besar')->insert($databukubesarbank);
                }

                DB::table('historibayar_pembelian')->insert($data);


                DB::commit();
                return Redirect::back()->with(['success' => 'Data Kontrabon Berhasil di Simpan']);
            } catch (\Exception $e) {
                dd($e);
                DB::rollback();
                return Redirect::back()->with(['warning' => 'Data Kontrabon Gagal di Simpan']);
            }
        }
    }


    public function updatekontrabon(Request $request)
    {
        $no_kontrabon = $request->no_kontrabon;
        $supplier = DB::table('supplier')->where('kode_supplier', $request->kode_supplier)->first();
        $tglbayar = $request->tglbayar;
        $bayar = $request->jmlbayar;
        $kode_bank = $request->kode_bank;
        $kode_akun = $request->kode_akun;
        $cekcabang = $request->cekcabang;
        $kode_cabang = $request->kode_cabang;
        $keterangan = $request->keterangan;
        $no_bkk = $request->no_bkk;
        $pelanggan = $supplier->nama_supplier;;
        $id_user = Auth::user()->id;
        $tanggal = explode("-", $tglbayar);
        $bulan = $tanggal[1];
        $tahun = substr($tanggal[0], 2, 2);

        $bank = DB::table('master_bank')->where('kode_bank', $kode_bank)->first();
        $kode_akun_bank = $bank->kode_akun;
        $akun = [
            'BDG' => '1-1102',
            'BGR' => '1-1103',
            'PST' => '1-1111',
            'TSM' => '1-1112',
            'SKB' => '1-1113',
            'PWT' => '1-1114',
            'TGL' => '1-1115',
            'SBY' => '1-1116',
            'SMR' => '1-1117',
            'KLT' => '1-1118',
            'GRT' => '1-1119'
        ];

        $cbg = "PST";
        $ledger = DB::table('ledger_bank')->select('no_bukti')->whereRaw('LEFT(no_bukti,7)="LR' . $cbg . $tahun . '"')->orderBy('no_bukti', 'desc')->first();
        if ($ledger != null) {
            $lastno_bukti = $ledger->no_bukti;
        } else {
            $lastno_bukti = "";
        }
        $no_bukti = buatkode($lastno_bukti, 'LR' . $cbg . $tahun, 4);

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
        $nobukti_bukubesar_trans_kk = buatkode($nobukti_bukubesar_bank, 'GJ' . $bulan . $tahun, 6);
        $nobukti_bukubesar_kk = buatkode($nobukti_bukubesar_trans_kk, 'GJ' . $bulan . $tahun, 6);


        $nobukti_pembelian = "";
        $detailkontrabon = DB::table('detail_kontrabon')->where('no_kontrabon', $no_kontrabon);
        $cekkontrabon = $detailkontrabon->count();
        $datadetailkontrabon = $detailkontrabon->get();
        foreach ($datadetailkontrabon as $d) {
            if ($cekkontrabon > 1) {
                $nobukti_pembelian .= $d->nobukti_pembelian . ",";
            } else {
                $nobukti_pembelian .= $d->nobukti_pembelian;
            }

            if ($kode_bank == "KAS KECIL") {
                // $detailpembelian = DB::table('detail_pembelian')
                //     ->selectRaw("detail_pembelian.kode_barang,nama_barang,((qty*harga)+penyesuaian) as totalharga")
                //     ->join('master_barang_pembelian', 'detail_pembelian.kode_barang', 'master_barang_pembelian.kode_barang')
                //     ->where('nobukti_pembelian', $d->nobukt_pembelian)
                //     ->get();

                // foreach ($detailpembelian as $p) {
                //     $barang[] = "PEMB " . $p->nama_barang;
                //     $datakk[] = [
                //         'nobukti' => $no_bkk,
                //         'tgl_kaskecil' => $tglbayar,
                //         'keterangan' => "PEMB " . $p->nama_barang,
                //         'jumlah' => $p->totalharga,
                //         'status_dk' => "D",
                //         'kode_akun' => "2-1300"
                //     ];
                // }
            }

            $data = array(
                'no_kontrabon' => $no_kontrabon,
                'bayar'        => $bayar,
                'tglbayar'     => $tglbayar,
                'via'          => $kode_bank,
                'kode_cabang'  => $kode_cabang,
                'id_admin'     => $id_user
            );

            $dataledger = array(
                'no_bukti'              => $no_bukti,
                'no_ref'                => $no_kontrabon,
                'pelanggan'             => $pelanggan,
                'bank'                  => $kode_bank,
                'tgl_ledger'            => $tglbayar,
                'keterangan'            => $keterangan . " " . $nobukti_pembelian,
                'kode_akun'             => $kode_akun,
                'jumlah'                => $bayar,
                'status_dk'             => 'D',
                'status_validasi'       => 1,
                'kategori'              => 'PMB',
                'nobukti_bukubesar'     => $nobukti_bukubesar,
                'nobukti_bukubesar_2'   => $nobukti_bukubesar_bank
            );


            $databukubesar = array(
                'no_bukti' => $nobukti_bukubesar,
                'tanggal' => $tglbayar,
                'sumber' => 'ledger',
                'keterangan' => $keterangan . " " . $nobukti_pembelian,
                'kode_akun' => $kode_akun,
                'debet' => $bayar,
                'kredit' => 0,
                'nobukti_transaksi' => $no_bukti
            );


            $databukubesarbank = array(
                'no_bukti' => $nobukti_bukubesar_bank,
                'tanggal' => $tglbayar,
                'sumber' => 'ledger',
                'keterangan' => $keterangan . " " . $nobukti_pembelian,
                'kode_akun' => $kode_akun_bank,
                'debet' => 0,
                'kredit' => $bayar,
                'nobukti_transaksi' => $no_bukti
            );


            DB::beginTransaction();
            try {
                //Simpan Histori Pembayaran
                $ledger = DB::table('ledger_bank')->where('no_ref', $no_kontrabon)->first();
                $kaskecil = DB::table('kaskecil_detail')->where('no_ref', $no_kontrabon)->first();
                if ($ledger != null) {
                    $nobukti_bukubesar_1 = $ledger->nobukti_bukubesar;
                    $nobukti_bukubesar_2 = $ledger->nobukti_bukubesar_2;
                } else {
                    $nobukti_bukubesar_1 =  "";
                    $nobukti_bukubesar_2 = "";
                }

                if ($kaskecil != null) {
                    $nobukti_bukubesar_kk_1 = $kaskecil->nobukti_bukubesar;
                    $nobukti_bukubesar_kk_2 = $kaskecil->nobukti_bukubesar_2;
                } else {
                    $nobukti_bukubesar_kk_1 = "";
                    $nobukti_bukubesar_kk_2 = "";
                }

                $nobukti_bukubesar = [
                    $nobukti_bukubesar_1, $nobukti_bukubesar_2, $nobukti_bukubesar_kk_1, $nobukti_bukubesar_kk_2
                ];

                DB::table('historibayar_pembelian')->where('no_kontrabon', $no_kontrabon)->delete();

                DB::table('buku_besar')->whereIn('no_bukti', $nobukti_bukubesar)->delete();

                DB::table('ledger_bank')->where('no_ref', $no_kontrabon)->delete();
                DB::table('kaskecil_detail')->where('no_ref', $no_kontrabon)->delete();

                if ($kode_bank == "KAS KECIL") {
                    $databukubesar_kk = array(
                        'no_bukti' => $nobukti_bukubesar_kk,
                        'tanggal' => $tglbayar,
                        'sumber' => 'Kas Kecil',
                        'keterangan' => $keterangan . " " . $nobukti_pembelian,
                        'kode_akun' => $akun[$kode_cabang],
                        'debet' => 0,
                        'kredit' => $bayar,
                        'nobukti_transaksi' => $no_bkk,
                    );


                    $databukubesartrans = array(
                        'no_bukti' => $nobukti_bukubesar_trans_kk,
                        'tanggal' => $tglbayar,
                        'sumber' => 'Kas Kecil',
                        'keterangan' =>  $keterangan . " " . $nobukti_pembelian,
                        'kode_akun' => $kode_akun,
                        'debet' => $bayar,
                        'kredit' => 0,
                        'nobukti_transaksi' => $no_bkk,
                    );
                    $datakaskecil = [
                        'nobukti' => $no_bkk,
                        'no_ref' => $no_kontrabon,
                        'tgl_kaskecil' => $tglbayar,
                        'keterangan' => $keterangan . " " . $nobukti_pembelian,
                        'jumlah' => $bayar,
                        'status_dk' => "D",
                        'kode_cabang' => $kode_cabang,
                        'kode_akun' => $kode_akun,
                        'nobukti_bukubesar' => $nobukti_bukubesar_kk,
                        'nobukti_bukubesar_2' => $nobukti_bukubesar_trans_kk
                    ];

                    DB::table('kaskecil_detail')->insert($datakaskecil);
                    DB::table('buku_besar')->insert($databukubesar_kk);
                    DB::table('buku_besar')->insert($databukubesartrans);
                } else {
                    DB::table('ledger_bank')->insert($dataledger);
                    DB::table('buku_besar')->insert($databukubesar);
                    DB::table('buku_besar')->insert($databukubesarbank);
                }

                DB::table('historibayar_pembelian')->insert($data);


                DB::commit();
                return Redirect::back()->with(['success' => 'Data Kontrabon Berhasil di Simpan']);
            } catch (\Exception $e) {
                dd($e);
                DB::rollback();
                return Redirect::back()->with(['warning' => 'Data Kontrabon Gagal di Simpan']);
            }
        }
    }


    public function batalkankontrabon($no_kontrabon)
    {
        $no_kontrabon = Crypt::decrypt($no_kontrabon);
        $ledger = DB::table('ledger_bank')->where('no_ref', $no_kontrabon)->first();
        $kaskecil = DB::table('kaskecil_detail')->where('no_ref', $no_kontrabon)->first();
        if ($ledger != null) {
            $nobukti_bukubesar_1 = $ledger->nobukti_bukubesar;
            $nobukti_bukubesar_2 = $ledger->nobukti_bukubesar_2;
        } else {
            $nobukti_bukubesar_1 =  "";
            $nobukti_bukubesar_2 = "";
        }

        if ($kaskecil != null) {
            $nobukti_bukubesar_kk_1 = $kaskecil->nobukti_bukubesar;
            $nobukti_bukubesar_kk_2 = $kaskecil->nobukti_bukubesar_2;
        } else {
            $nobukti_bukubesar_kk_1 = "";
            $nobukti_bukubesar_kk_2 = "";
        }

        $nobukti_bukubesar = [
            $nobukti_bukubesar_1, $nobukti_bukubesar_2, $nobukti_bukubesar_kk_1, $nobukti_bukubesar_kk_2
        ];

        DB::beginTransaction();
        try {
            DB::table('historibayar_pembelian')->where('no_kontrabon', $no_kontrabon)->delete();

            DB::table('buku_besar')->whereIn('no_bukti', $nobukti_bukubesar)->delete();

            DB::table('ledger_bank')->where('no_ref', $no_kontrabon)->delete();
            DB::table('kaskecil_detail')->where('no_ref', $no_kontrabon)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Kontrabon Berhasil di Batalkan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Kontrabon Gagal di Dbatalkan, Hubungi Tim IT']);
        }
    }

    public function approvekontrabon($no_kontrabon)
    {
        $no_kontrabon = Crypt::decrypt($no_kontrabon);
        $data = [
            'status' => 1
        ];
        $simpan = DB::table('kontrabon')->where('no_kontrabon', $no_kontrabon)->update($data);
        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Berhasil di Approve']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal di Approve']);
        }
    }

    public function cancelkontrabon($no_kontrabon)
    {
        $no_kontrabon = Crypt::decrypt($no_kontrabon);
        $data = [
            'status' => NULL
        ];
        $simpan = DB::table('kontrabon')->where('no_kontrabon', $no_kontrabon)->update($data);
        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Berhasil di Approve']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal di Approve']);
        }
    }

    public function getNokontrabon(Request $request)
    {
        $tgl_kontrabon = $request->tgl_kontrabon;
        $kategori = $request->kategori;
        $tanggal = explode("-", $tgl_kontrabon);
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $kontrabon = DB::table('kontrabon')
            ->whereBetween('tgl_kontrabon', [$dari, $sampai])
            ->where('kategori', $kategori)
            ->orderBy('no_kontrabon', 'desc')
            ->first();
        $lastnokontrabon = $kontrabon != null ? $kontrabon->no_kontrabon : '';
        $no_kontrabon = buatkode($lastnokontrabon, $kategori, 3) . "/" . $bulan . "/" . $tahun;
        echo $no_kontrabon;
    }


    public function cetak(Request $request)
    {
        $no_kontrabon = Crypt::decrypt($request->no_kontrabon);
        $kontrabon = DB::table('kontrabon')
            ->select('kontrabon.*', 'nama_supplier')
            ->join('supplier', 'kontrabon.kode_supplier', '=', 'supplier.kode_supplier')
            ->where('no_kontrabon', $no_kontrabon)->first();
        $detailkontrabon = DB::table('detail_kontrabon')
            ->select('detail_kontrabon.*', 'tgl_pembelian', 'nama_barang', 'qty', 'harga', 'penyesuaian')
            ->join('pembelian', 'detail_kontrabon.nobukti_pembelian', '=', 'pembelian.nobukti_pembelian')
            ->join('detail_pembelian', 'detail_kontrabon.nobukti_pembelian', '=', 'detail_pembelian.nobukti_pembelian')
            ->join('master_barang_pembelian', 'detail_pembelian.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('no_kontrabon', $no_kontrabon)->get();
        return view('kontrabon.cetak', compact('kontrabon', 'detailkontrabon'));
    }
}
