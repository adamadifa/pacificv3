<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Pembelian;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembelian::query();
        $query->selectRaw("pembelian.nobukti_pembelian,nobukti_pemasukan,tgl_pemasukan,
        pembelian.tgl_pembelian,
        tgl_jatuhtempo,
        ppn,
        no_fak_pajak,
        pembelian.kode_supplier,
        nama_supplier,
        pembelian.kode_dept,
        jenistransaksi,
        ref_tunai,
        harga,
        kontrabon,
        penyesuaian,
        jmlbayar");
        $query->join('supplier', 'pembelian.kode_supplier', '=', 'supplier.kode_supplier');
        $query->leftJoin('pemasukan', 'pembelian.nobukti_pembelian', '=', 'pemasukan.nobukti_pemasukan');
        $query->leftJoin(
            DB::raw('(
                SELECT nobukti_pembelian, SUM( IF ( STATUS = "PMB", ( ( qty * harga ) + penyesuaian ), 0 ) ) - SUM( IF ( STATUS = "PNJ", ( qty * harga ), 0 ) ) as harga
                FROM detail_pembelian
                GROUP BY nobukti_pembelian
            ) detailpembelian'),
            function ($join) {
                $join->on('pembelian.nobukti_pembelian', '=', 'detailpembelian.nobukti_pembelian');
            }
        );
        $query->leftJoin(
            DB::raw('(
                SELECT nobukti_pembelian, COUNT( nobukti_pembelian ) as kontrabon
                FROM detail_kontrabon
                GROUP BY nobukti_pembelian
            ) kontrabon'),
            function ($join) {
                $join->on('pembelian.nobukti_pembelian', '=', 'kontrabon.nobukti_pembelian');
            }
        );

        $query->leftJoin(
            DB::raw('(
                SELECT
                nobukti_pembelian,
                (SUM(IF( status_dk = "K" AND kode_akun = "2-1200" OR status_dk = "K" AND kode_akun = "2-1300", (qty * harga), 0))
                - SUM(IF( status_dk = "D" AND kode_akun = "2-1200" OR status_dk = "D" AND kode_akun = "2-1300", (qty * harga), 0))
                ) as penyesuaian
                FROM
                jurnal_koreksi
                GROUP BY nobukti_pembelian
            ) jurnalkoreksi'),
            function ($join) {
                $join->on('pembelian.nobukti_pembelian', '=', 'jurnalkoreksi.nobukti_pembelian');
            }
        );

        $query->leftJoin(
            DB::raw('(
                SELECT
                nobukti_pembelian,
                SUM(jmlbayar) as jmlbayar
                FROM
                historibayar_pembelian hb
                INNER JOIN detail_kontrabon ON hb.no_kontrabon = detail_kontrabon.no_kontrabon
                GROUP BY
                nobukti_pembelian
            ) historibayar'),
            function ($join) {
                $join->on('pembelian.nobukti_pembelian', '=', 'historibayar.nobukti_pembelian');
            }
        );

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('pembelian.tgl_pembelian', [$request->dari, $request->sampai]);
        }
        if (!empty($request->nobukti_pembelian)) {
            $query->where('pembelian.nobukti_pembelian', $request->nobukti_pembelian);
        }

        $levelgudang = ['kepala gudang', 'admin gudang logistik'];
        if (in_array(Auth::user()->level, $levelgudang)) {
            $query->where('pembelian.kode_dept', 'GDL');
        } else {
            if (!empty($request->kode_dept)) {
                $query->where('pembelian.kode_dept', $request->kode_dept);
            }
        }

        if (!empty($request->kode_supplier)) {
            $query->where('pembelian.kode_supplier', $request->kode_supplier);
        }

        if ($request->ppn != "-") {
            $query->where('pembelian.ppn', $request->ppn);
        }

        if (!empty($request->jenistransaksi)) {
            $query->where('pembelian.jenistransaksi', $request->jenistransaksi);
        }
        $query->orderBy('tgl_pembelian', 'desc');
        $query->orderBy('nobukti_pembelian', 'desc');
        $pembelian = $query->paginate(15);
        $pembelian->appends($request->all());
        $departemen = DB::table('departemen')->where('status_pengajuan', 1)->get();
        $supplier = Supplier::orderBy('nama_supplier')->get();
        return view('pembelian.index', compact('departemen', 'supplier', 'pembelian', 'levelgudang'));
    }

    public function create()
    {
        $coa = DB::table('set_coa_cabang')
            ->select('set_coa_cabang.kode_akun', 'nama_akun')
            ->join('coa', 'set_coa_cabang.kode_akun', '=', 'coa.kode_akun')
            ->where('kategori', 'pembelian')->get();
        $departemen = DB::table('departemen')->where('status_pengajuan', 1)->get();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('pembelian.create', compact('departemen', 'coa', 'cabang'));
    }



    public function storetemp(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $kode_dept = $request->kode_dept;
        $qty = $request->qty;
        $qty = !empty($qty) ? str_replace(".", "", $qty) : 0;
        $qty = str_replace(",", ".", $qty);
        $harga = !empty($request->harga) ? str_replace(".", "", $request->harga) : 0;
        $harga = str_replace(",", ".", $harga);
        $peny_harga = !empty($request->peny_harga) ? str_replace(".", "", $request->peny_harga) : 0;
        $peny_harga = str_replace(",", ".", $peny_harga);
        $kode_akun = $request->kode_akun;
        $kode_cabang = $request->kode_cabang;
        $keterangan = $request->keterangan;
        $id_admin = Auth::user()->id;
        $cek = DB::table('detailpembelian_temp')->where('kode_barang', $kode_barang)->where('id_admin', $id_admin)->count();
        if ($cek > 0) {
            echo 1;
        } else {
            $data = [
                'kode_barang' => $kode_barang,
                'kode_dept' => $kode_dept,
                'qty' => $qty,
                'harga' => $harga,
                'penyesuaian' => $peny_harga,
                'kode_akun' => $kode_akun,
                'kode_cabang' => $kode_cabang,
                'keterangan' => $keterangan,
                'id_admin' => $id_admin
            ];
            $simpan =  DB::table('detailpembelian_temp')->insert($data);
            if ($simpan) {
                echo 0;
            } else {
                echo 2;
            }
        }
    }

    public function storedetailpembelian(Request $request)
    {
        $nobukti_pembelian = $request->nobukti_pembelian;
        $pembelian = DB::table('pembelian')->where('nobukti_pembelian', $nobukti_pembelian)->first();
        $tgl_pembelian = $pembelian->tgl_pembelian;
        $tanggal   = explode("-", $tgl_pembelian);
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];
        $thn = substr($tahun, 2, 2);
        $kode_barang = $request->kode_barang;
        $kode_dept = $request->kode_dept;
        $qty = !empty($request->qty) ? str_replace(".", "", $request->qty) : 0;
        $qty = str_replace(",", ".", $qty);
        $harga = !empty($request->harga) ? str_replace(".", "", $request->harga) : 0;
        $harga = str_replace(",", ".", $harga);
        $penyesuaian = !empty($request->peny_harga) ? str_replace(".", "", $request->peny_harga) : 0;
        $penyesuaian = str_replace(",", ".", $penyesuaian);
        $kode_akun = $request->kode_akun;
        $kode_cabang = $request->kode_cabang;
        $keterangan = $request->keterangan;
        $id_admin = Auth::user()->id;
        $detailpembelian = DB::table('detail_pembelian')->where('nobukti_pembelian', $nobukti_pembelian)->orderBy('no_urut', 'desc')->first();
        $no_urut = $detailpembelian != null ? $detailpembelian->no_urut + 1 : 1;
        $barang = DB::table('master_barang_pembelian')->where('kode_barang', $kode_barang)->first();
        $nama_barang = $barang->nama_barang;
        $cek = DB::table('detail_pembelian')->where('kode_barang', $kode_barang)->where('nobukti_pembelian', $nobukti_pembelian)->count();
        $kode_dept = $pembelian->kode_dept;
        $kontrabon = DB::table('detail_kontrabon')->where('nobukti_pembelian', $nobukti_pembelian)
            ->join('kontrabon', 'detail_kontrabon.no_kontrabon', '=', 'kontrabon.no_kontrabon')
            ->whereNull('status')
            ->orderBy('tgl_kontrabon', 'desc')
            ->first();

        if ($cek > 0) {
            echo 1;
        } else {
            DB::beginTransaction();
            try {
                if (substr($kode_akun, 0, 3) == "6-1" && !empty($kode_cabang) or substr($kode_akun, 0, 3) == "6-2" && !empty($kode_cabang)) {

                    $kode = "CR" . $bulan . $thn;
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
                    $kode_cr = buatkode($last_kode_cr, "CR" . $bulan . $thn, 4);

                    $datacr = [
                        'kode_cr' => $kode_cr,
                        'tgl_transaksi' => $tgl_pembelian,
                        'kode_akun' => $kode_akun,
                        'keterangan'   => "Pembelian " . $nama_barang . "(" . $qty . ")",
                        'kode_cabang'  => $kode_cabang,
                        'id_sumber_costratio' => 4,
                        'jumlah' => ($qty * $harga) + $penyesuaian
                    ];
                    DB::table('costratio_biaya')->insert($datacr);
                } else {
                    $kode_cr = NULL;
                }

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
                    'tanggal' => $tgl_pembelian,
                    'sumber' => 'pembelian',
                    'keterangan' => "Pembelian " . $nama_barang,
                    'kode_akun' => $kode_akun,
                    'debet' => ($qty * $harga) + $penyesuaian,
                    'kredit' => 0,
                    'nobukti_transaksi' => $nobukti_pembelian
                );
                $data = [
                    'nobukti_pembelian' => $nobukti_pembelian,
                    'kode_barang' => $kode_barang,
                    'qty' => $qty,
                    'harga' => $harga,
                    'penyesuaian' => $penyesuaian,
                    'kode_akun' => $kode_akun,
                    'kode_cabang' => $kode_cabang,
                    'keterangan' => $keterangan,
                    'no_urut' => $no_urut,
                    'kode_cr' => $kode_cr,
                    'nobukti_bukubesar' => $nobukti_bukubesar,
                    'status' => 'PMB'

                ];
                DB::table('buku_besar')->insert($databukubesar);
                DB::table('detail_pembelian')->insert($data);

                if ($kode_dept != "GDB") {
                    $kode_akun = "2-1300";
                } else {
                    $kode_akun = "2-1200";
                }

                $jmlbayar = DB::table('detail_pembelian')
                    ->selectRaw("SUM(IF(status='PMB',(qty*harga+penyesuaian),0)) - SUM(IF(status='PNJ',(qty*harga+penyesuaian),0))  as jmlbayar")
                    ->where('nobukti_pembelian', $nobukti_pembelian)
                    ->first();
                $databukubesar = array(
                    'kode_akun' => $kode_akun,
                    'debet' => 0,
                    'kredit' => $jmlbayar->jmlbayar,
                );
                $datakontrabon = array(
                    'jmlbayar' => $jmlbayar->jmlbayar
                );
                if ($kontrabon != null) {
                    DB::table('detail_kontrabon')->where('nobukti_pembelian', $nobukti_pembelian)->where('no_kontrabon', $kontrabon->no_kontrabon)->update($datakontrabon);
                }
                DB::table('buku_besar')->where('no_bukti', $pembelian->nobukti_bukubesar)->update($databukubesar);
                echo 0;
                DB::commit();
            } catch (\Exception $e) {
                dd($e);
                DB::rollback();
                echo 2;
            }
        }
    }

    public function showtemp(Request $request)
    {
        $kode_dept = $request->kode_dept;
        $id_admin = Auth::user()->id;
        $detailtemp = DB::table('detailpembelian_temp')
            ->select('detailpembelian_temp.*', 'nama_barang')
            ->join('master_barang_pembelian', 'detailpembelian_temp.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('detailpembelian_temp.kode_dept', $kode_dept)->where('id_admin', $id_admin)
            ->get();
        return view('pembelian.showtemp', compact('detailtemp'));
    }

    public function deletetemp(Request $request)
    {
        $hapus = DB::table('detailpembelian_temp')->where('id', $request->id)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }


    public function store(Request $request)
    {
        $id_admin = Auth::user()->id;
        $nobukti_pembelian = $request->nobukti_pembelian;
        $tgl_pembelian = $request->tgl_pembelian;
        $kode_supplier = $request->kode_supplier;
        $kode_dept = $request->kode_dept;
        $jenistransaksi = $request->jenistransaksi;
        $tgl_jatuhtempo = $jenistransaksi == 'kredit' ? $request->tgl_jatuhtempo : $tgl_pembelian;
        $ppn = empty($request->ppn) ? 0 : $request->ppn;

        $tanggal   = explode("-", $tgl_pembelian);
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];
        $thn = substr($tahun, 2, 2);
        $tgl = $tanggal[2] . $tanggal[1] . $tanggal[0];
        $rand = rand(10, 100);
        $nokontrabon = $jenistransaksi == "tunai" ?  "T" . $tgl . $rand : '';



        if ($kode_dept != "GDB") {
            $kode_akun = "2-1300";
        } else {
            $kode_akun = "2-1200";
        }
        $detailpembeliantemp = DB::table('detailpembelian_temp')
            ->selectRaw("SUM((qty*harga)+penyesuaian) as jmlbayar")
            ->where('id_admin', $id_admin)
            ->where('kode_dept', $kode_dept)
            ->first();

        $detailtemp = DB::table('detailpembelian_temp')
            ->select('detailpembelian_temp.*', 'nama_barang')
            ->join('master_barang_pembelian', 'detailpembelian_temp.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('detailpembelian_temp.kode_dept', $kode_dept)->where('id_admin', $id_admin)->orderBy('id')->get();

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
            'tanggal' => $tgl_pembelian,
            'sumber' => 'pembelian',
            'keterangan' => "Pembelian " . $nobukti_pembelian,
            'kode_akun' => $kode_akun,
            'debet' => 0,
            'kredit' => $detailpembeliantemp->jmlbayar,
            'nobukti_transaksi' => $nobukti_pembelian
        );

        DB::beginTransaction();
        try {
            $data = array(
                'nobukti_pembelian'  => $nobukti_pembelian,
                'tgl_pembelian'      => $tgl_pembelian,
                'kode_supplier'      => $kode_supplier,
                'kode_dept'          => $kode_dept,
                'kode_akun'          => $kode_akun,
                'ppn'                => $ppn,
                'tgl_jatuhtempo'     => $tgl_jatuhtempo,
                'jenistransaksi'     => $jenistransaksi,
                'ref_tunai'          => $nokontrabon,
                'nobukti_bukubesar'  => $nobukti_bukubesar,
                'id_admin'           => $id_admin
            );
            DB::table('pembelian')->insert($data);
            DB::table('buku_besar')->insert($databukubesar);
            if ($jenistransaksi == "tunai") {
                $datakontrabon = array(
                    'no_kontrabon'       => $nokontrabon,
                    'tgl_kontrabon'      => $tgl_pembelian,
                    'kode_supplier'      => $kode_supplier,
                    'kategori'           => 'TN',
                    'id_admin'           => $id_admin,
                    'jenisbayar'         => 'tunai'
                );

                $datadetailkontrabon = array(
                    'no_kontrabon'        => $nokontrabon,
                    'nobukti_pembelian'   => $nobukti_pembelian,
                    'jmlbayar'            => $detailpembeliantemp->jmlbayar,
                    'keterangan'          => 'tunai'
                );
                DB::table('kontrabon')->insert($datakontrabon);
                DB::table('detail_kontrabon')->insert($datadetailkontrabon);
            }

            $no = 1;
            $no_bb = "";
            foreach ($detailtemp as $d) {

                if (substr($d->kode_akun, 0, 3) == "6-1" && !empty($d->kode_cabang) or substr($d->kode_akun, 0, 3) == "6-2" && !empty($d->kode_cabang)) {
                    $kode = "CR" . $bulan . $thn;
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
                    $kode_cr = buatkode($last_kode_cr, "CR" . $bulan . $thn, 4);

                    $datacr = [
                        'kode_cr' => $kode_cr,
                        'tgl_transaksi' => $tgl_pembelian,
                        'kode_akun' => $d->kode_akun,
                        'keterangan'   => "Pembelian " . $d->nama_barang . "(" . $d->qty . ")",
                        'kode_cabang'  => $d->kode_cabang,
                        'id_sumber_costratio' => 4,
                        'jumlah' => ($d->qty * $d->harga) + $d->penyesuaian
                    ];

                    DB::table('costratio_biaya')->insert($datacr);
                } else {
                    $kode_cr = NULL;
                }



                if (empty($no_bb)) {
                    $nobukti_bukubesar_detail = buatkode($nobukti_bukubesar, 'GJ' . $bulan . $thn, 6);
                } else {
                    $nobukti_bukubesar_detail = buatkode($no_bb, 'GJ' . $bulan . $thn, 6);
                }
                $databukubesar = array(
                    'no_bukti' => $nobukti_bukubesar_detail,
                    'tanggal' => $tgl_pembelian,
                    'sumber' => 'pembelian',
                    'keterangan' => "Pembelian " . $d->nama_barang,
                    'kode_akun' => $d->kode_akun,
                    'debet' => ($d->qty * $d->harga) + $d->penyesuaian,
                    'kredit' => 0,
                    'nobukti_transaksi' => $nobukti_pembelian
                );


                $datadetail = array(
                    'nobukti_pembelian' => $nobukti_pembelian,
                    'kode_barang'       => $d->kode_barang,
                    'keterangan'        => $d->keterangan,
                    'qty'               => $d->qty,
                    'harga'             => $d->harga,
                    'penyesuaian'       => $d->penyesuaian,
                    'status'            => 'PMB',
                    'kode_akun'         => $d->kode_akun,
                    'no_urut'           => $no,
                    'kode_cabang'       => $d->kode_cabang,
                    'kode_cr'           => $kode_cr,
                    'nobukti_bukubesar' => $nobukti_bukubesar_detail
                );

                DB::table('detail_pembelian')->insert($datadetail);
                DB::table('buku_besar')->insert($databukubesar);
                $no_bb = $nobukti_bukubesar_detail;
                $no++;
            }

            DB::table('detailpembelian_temp')->where('kode_dept', $kode_dept)->where('id_admin', $id_admin)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Penjualan Berhasil di Simpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Penjualan Gagal di Simpan']);
        }
    }

    public function show(Request $request)
    {
        $nobukti_pembelian = $request->nobukti_pembelian;
        $pembelian = DB::table('pembelian')
            ->select('pembelian.*', 'nama_supplier', 'nama_dept')
            ->join('supplier', 'pembelian.kode_supplier', '=', 'supplier.kode_supplier')
            ->join('departemen', 'pembelian.kode_dept', '=', 'departemen.kode_dept')
            ->where('nobukti_pembelian', $nobukti_pembelian)
            ->first();
        $detailpembelian = DB::table('detail_pembelian')
            ->select('detail_pembelian.*', 'nama_barang')
            ->join('master_barang_pembelian', 'detail_pembelian.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('nobukti_pembelian', $nobukti_pembelian)
            ->where('detail_pembelian.status', 'PMB')
            ->get();
        $detailpenjualan = DB::table('detail_pembelian')
            ->where('status', 'PNJ')
            ->where('nobukti_pembelian', $nobukti_pembelian)
            ->get();

        $kontrabon = DB::table('detail_kontrabon')
            ->selectRaw("detail_kontrabon.no_kontrabon,jmlbayar
            ,tgl_kontrabon,kategori,tglbayar")
            ->join('kontrabon', 'detail_kontrabon.no_kontrabon', '=', 'kontrabon.no_kontrabon')
            ->leftjoin('historibayar_pembelian', 'historibayar_pembelian.no_kontrabon', '=', 'kontrabon.no_kontrabon')
            ->where('nobukti_pembelian', $nobukti_pembelian)
            ->orderBy('tgl_kontrabon', 'desc')
            ->get();

        return view('pembelian.show', compact('pembelian', 'detailpembelian', 'detailpenjualan', 'kontrabon'));
    }


    public function prosespembelian(Request $request)
    {
        $nobukti_pembelian = $request->nobukti_pembelian;
        $pembelian = DB::table('pembelian')
            ->select('pembelian.*', 'nama_supplier', 'nama_dept')
            ->join('supplier', 'pembelian.kode_supplier', '=', 'supplier.kode_supplier')
            ->join('departemen', 'pembelian.kode_dept', '=', 'departemen.kode_dept')
            ->where('nobukti_pembelian', $nobukti_pembelian)
            ->first();
        $detailpembelian = DB::table('detail_pembelian')
            ->select('detail_pembelian.*', 'nama_barang')
            ->join('master_barang_pembelian', 'detail_pembelian.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('nobukti_pembelian', $nobukti_pembelian)
            ->where('detail_pembelian.status', 'PMB')
            ->get();


        return view('pembelian.prosespembelian', compact('pembelian', 'detailpembelian'));
    }


    public function edit($nobukti_pembelian)
    {
        $nobukti_pembelian = Crypt::decrypt($nobukti_pembelian);
        $cekpembayaran = DB::table('detail_kontrabon')
            ->leftJoin('historibayar_pembelian', 'detail_kontrabon.no_kontrabon', '=', 'historibayar_pembelian.no_kontrabon')
            ->where('nobukti_pembelian', $nobukti_pembelian)
            ->whereNotNull('historibayar_pembelian.no_kontrabon')->count();
        $pembelian = DB::table('pembelian')
            ->select('pembelian.*', 'nama_supplier', 'nama_dept', 'jmlbayar')
            ->join('supplier', 'pembelian.kode_supplier', '=', 'supplier.kode_supplier')
            ->join('departemen', 'pembelian.kode_dept', '=', 'departemen.kode_dept')
            ->leftJoin(
                DB::raw('(
                    SELECT
                    nobukti_pembelian,
                    SUM(jmlbayar) as jmlbayar
                    FROM
                    historibayar_pembelian hb
                    INNER JOIN detail_kontrabon ON hb.no_kontrabon = detail_kontrabon.no_kontrabon
                    GROUP BY
                    nobukti_pembelian
                ) historibayar'),
                function ($join) {
                    $join->on('pembelian.nobukti_pembelian', '=', 'historibayar.nobukti_pembelian');
                }
            )
            ->where('pembelian.nobukti_pembelian', $nobukti_pembelian)
            ->first();
        $detailpembelian = DB::table('detail_pembelian')
            ->select('detail_pembelian.*', 'nama_barang')
            ->join('master_barang_pembelian', 'detail_pembelian.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('nobukti_pembelian', $nobukti_pembelian)
            ->where('detail_pembelian.status', 'PMB')
            ->get();
        $detailpenjualan = DB::table('detail_pembelian')
            ->where('status', 'PNJ')
            ->where('nobukti_pembelian', $nobukti_pembelian)
            ->get();
        $coa = DB::table('set_coa_cabang')
            ->select('set_coa_cabang.kode_akun', 'nama_akun')
            ->join('coa', 'set_coa_cabang.kode_akun', '=', 'coa.kode_akun')
            ->where('kategori', 'pembelian')->get();
        $departemen = DB::table('departemen')->where('status_pengajuan', 1)->get();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('pembelian.edit', compact('departemen', 'coa', 'cabang', 'pembelian', 'detailpembelian', 'detailpenjualan', 'cekpembayaran'));
    }

    public function showdetailpembelian(Request $request)
    {
        $nobukti_pembelian = $request->nobukti_pembelian;
        $cekpembayaran = $request->cekpembayaran;
        $detail = DB::table('detail_pembelian')
            ->select('detail_pembelian.*', 'nama_barang')
            ->join('master_barang_pembelian', 'detail_pembelian.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('nobukti_pembelian', $nobukti_pembelian)
            ->where('detail_pembelian.status', 'PMB')
            ->get();
        return view('pembelian.showdetailpembelian', compact('detail', 'cekpembayaran'));
    }

    public function showdetailpembeliankontrabon(Request $request)
    {
        $nobukti_pembelian = $request->nobukti_pembelian;
        $detail = DB::table('detail_pembelian')
            ->select('detail_pembelian.*', 'nama_barang')
            ->join('master_barang_pembelian', 'detail_pembelian.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('nobukti_pembelian', $nobukti_pembelian)
            ->where('detail_pembelian.status', 'PMB')
            ->get();
        $detailpenjualan = DB::table('detail_pembelian')
            ->where('status', 'PNJ')
            ->where('nobukti_pembelian', $nobukti_pembelian)
            ->get();
        return view('kontrabon.showdetailpembeliankontrabon', compact('detail', 'detailpenjualan'));
    }

    public function deletedetail(Request $request)
    {
        $nobukti_pembelian = $request->nobukti_pembelian;
        $kode_barang = $request->kode_barang;
        $no_urut = $request->no_urut;
        $pembelian = DB::table('pembelian')->where('nobukti_pembelian', $nobukti_pembelian)->first();
        $detailpembelian = DB::table('detail_pembelian')->where('nobukti_pembelian', $nobukti_pembelian)->where('kode_barang', $kode_barang)->where('no_urut', $no_urut)->first();
        $kode_cr = $detailpembelian->kode_cr;
        $nobukti_bukubesar = $detailpembelian->nobukti_bukubesar;
        $jenistransaksi = $pembelian->jenistransaksi;
        $no_kontrabbon = $pembelian->ref_tunai;
        $kode_dept = $pembelian->kode_dept;
        $kontrabon = DB::table('detail_kontrabon')->where('nobukti_pembelian', $nobukti_pembelian)
            ->join('kontrabon', 'detail_kontrabon.no_kontrabon', '=', 'kontrabon.no_kontrabon')
            ->whereNull('status')
            ->orderBy('tgl_kontrabon', 'desc')
            ->first();
        DB::beginTransaction();
        try {
            DB::table('detail_pembelian')->where('nobukti_pembelian', $nobukti_pembelian)->where('no_urut', $no_urut)->where('kode_barang', $kode_barang)->delete();
            DB::table('costratio_biaya')->where('kode_cr', $kode_cr)->delete();
            DB::table('buku_besar')->where('no_bukti', $nobukti_bukubesar)->delete();
            // if ($jenistransaksi == "tunai") {
            //     $bayar = DB::table('detail_pembelian')
            //         ->selectRaw("(SUM( IF ( STATUS = 'PMB', ((qty*harga)+penyesuaian), 0 ) ) - SUM( IF ( STATUS = 'PNJ',(qty*harga), 0 ) )) as totalpembelian")
            //         ->where('nobukti_pembelian', $nobukti_pembelian)
            //         ->first();
            //     $data = [
            //         'jmlbayar' => !empty($bayar->totalpembelian) ?  $bayar->totalpembelian : 0,
            //     ];

            //     DB::table('detail_kontrabon')->where('no_kontrabon', $no_kontrabbon)->update($data);
            // } else {
            // }
            if ($kode_dept != "GDB") {
                $kode_akun = "2-1300";
            } else {
                $kode_akun = "2-1200";
            }

            $jmlbayar = DB::table('detail_pembelian')
                ->selectRaw("SUM(IF(status='PMB',(qty*harga+penyesuaian),0)) - SUM(IF(status='PNJ',(qty*harga+penyesuaian),0))  as jmlbayar")
                ->where('nobukti_pembelian', $nobukti_pembelian)
                ->first();
            $databukubesar = array(
                'kode_akun' => $kode_akun,
                'debet' => 0,
                'kredit' => $jmlbayar->jmlbayar,
            );
            $datakontrabon = array(
                'jmlbayar' => $jmlbayar->jmlbayar
            );
            if ($kontrabon != null) {
                DB::table('detail_kontrabon')->where('nobukti_pembelian', $nobukti_pembelian)->where('no_kontrabon', $kontrabon->no_kontrabon)->update($datakontrabon);
            }
            DB::table('buku_besar')->where('no_bukti', $pembelian->nobukti_bukubesar)->update($databukubesar);
            echo 0;
            DB::commit();
        } catch (\Exception $e) {
            dd($e);
            echo 1;
            DB::rollback();
        }
    }

    public function editbarang(Request $request)
    {
        $nobukti_pembelian = $request->nobukti_pembelian;
        $kode_barang = $request->kode_barang;
        $no_urut = $request->no_urut;
        $cekpembayaran = DB::table('detail_kontrabon')
            ->leftJoin('historibayar_pembelian', 'detail_kontrabon.no_kontrabon', '=', 'historibayar_pembelian.no_kontrabon')
            ->where('nobukti_pembelian', $nobukti_pembelian)
            ->whereNotNull('historibayar_pembelian.no_kontrabon')->count();
        $coa = DB::table('set_coa_cabang')
            ->select('set_coa_cabang.kode_akun', 'nama_akun')
            ->join('coa', 'set_coa_cabang.kode_akun', '=', 'coa.kode_akun')
            ->where('kategori', 'pembelian')->get();
        $detailpembelian = DB::table('detail_pembelian')
            ->join('master_barang_pembelian', 'detail_pembelian.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('nobukti_pembelian', $nobukti_pembelian)->where('detail_pembelian.kode_barang', $kode_barang)->where('no_urut', $no_urut)->first();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('pembelian.editbarang', compact('detailpembelian', 'coa', 'cabang', 'cekpembayaran'));
    }

    public function updatebarang(Request $request)
    {
        $nobukti_pembelian = $request->nobukti_pembelian;
        $pembelian = DB::table('pembelian')->where('nobukti_pembelian', $nobukti_pembelian)->first();
        $tgl_pembelian = $pembelian->tgl_pembelian;
        $tanggal   = explode("-", $tgl_pembelian);
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];
        $thn = substr($tahun, 2, 2);
        $kode_barang = $request->kode_barang;
        $keterangan = $request->keterangan;
        $qty = $request->qty;
        $qty = !empty($qty) ? str_replace(".", "", $qty) : 0;
        $qty = str_replace(",", ".", $qty);
        $harga = !empty($request->harga) ? str_replace(".", "", $request->harga) : 0;
        $harga = str_replace(",", ".", $harga);
        $penyesuaian = !empty($request->penyesuaian) ? str_replace(".", "", $request->penyesuaian) : 0;
        $penyesuaian = str_replace(",", ".", $penyesuaian);
        $kode_akun = $request->kode_akun;
        $kode_cabang = !empty($request->kode_cabang) ? $request->kode_cabang : NULL;
        $no_urut = $request->no_urut;
        $kode_dept = $pembelian->kode_dept;
        $konversi_gram = $request->konversi_gram;
        $detailpembelian = DB::table('detail_pembelian')
            ->join('master_barang_pembelian', 'detail_pembelian.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('nobukti_pembelian', $nobukti_pembelian)->where('detail_pembelian.kode_barang', $kode_barang)->where('no_urut', $no_urut)->first();
        $kontrabon = DB::table('detail_kontrabon')->where('nobukti_pembelian', $nobukti_pembelian)
            ->join('kontrabon', 'detail_kontrabon.no_kontrabon', '=', 'kontrabon.no_kontrabon')
            ->whereNull('status')
            ->orderBy('tgl_kontrabon', 'desc')
            ->first();
        DB::beginTransaction();
        try {

            if (substr($kode_akun, 0, 3) == "6-1" && !empty($kode_cabang) or substr($kode_akun, 0, 3) == "6-2" && !empty($kode_cabang)) {

                $kode = "CR" . $bulan . $thn;
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
                $kode_cr = buatkode($last_kode_cr, "CR" . $bulan . $thn, 4);

                $datacr = [
                    'kode_cr' => $kode_cr,
                    'tgl_transaksi' => $tgl_pembelian,
                    'kode_akun' => $kode_akun,
                    'keterangan'   => "Pembelian " . $detailpembelian->nama_barang . "(" . $qty . ")",
                    'kode_cabang'  => $kode_cabang,
                    'id_sumber_costratio' => 4,
                    'jumlah' => ($qty * $harga) + $penyesuaian
                ];
                DB::table('costratio_biaya')->where('kode_cr', $detailpembelian->kode_cr)->delete();
                DB::table('costratio_biaya')->insert($datacr);
            } else {
                DB::table('costratio_biaya')->where('kode_cr', $detailpembelian->kode_cr)->delete();
                $kode_cr = NULL;
            }

            $databukubesar_detail = array(
                'keterangan' => "Pembelian " . $detailpembelian->nama_barang,
                'kode_akun' => $kode_akun,
                'debet' => ($qty * $harga) + $penyesuaian,
                'kredit' => 0,
            );



            $data = [
                'keterangan' => $keterangan,
                'qty' => $qty,
                'harga' => $harga,
                'penyesuaian' => $penyesuaian,
                'kode_akun' => $kode_akun,
                'kode_cabang' => $kode_cabang,
                'konversi_gram' => $konversi_gram,
                'kode_cr' => $kode_cr
            ];
            DB::table('buku_besar')->where('no_bukti', $detailpembelian->nobukti_bukubesar)->update($databukubesar_detail);
            if ($kode_dept != "GDB") {
                $kode_akun = "2-1300";
            } else {
                $kode_akun = "2-1200";
            }
            DB::table('detail_pembelian')->where('nobukti_pembelian', $nobukti_pembelian)->where('kode_barang', $kode_barang)->where('no_urut', $no_urut)->update($data);
            $jmlbayar = DB::table('detail_pembelian')
                ->selectRaw("SUM(IF(status='PMB',(qty*harga+penyesuaian),0)) - SUM(IF(status='PNJ',(qty*harga+penyesuaian),0))  as jmlbayar")
                ->where('nobukti_pembelian', $nobukti_pembelian)
                ->first();
            $databukubesar = array(
                'kode_akun' => $kode_akun,
                'debet' => 0,
                'kredit' => $jmlbayar->jmlbayar,
            );
            $datakontrabon = array(
                'jmlbayar' => $jmlbayar->jmlbayar
            );
            if ($kontrabon != null) {
                DB::table('detail_kontrabon')->where('nobukti_pembelian', $nobukti_pembelian)->where('no_kontrabon', $kontrabon->no_kontrabon)->update($datakontrabon);
            }
            DB::table('buku_besar')->where('no_bukti', $pembelian->nobukti_bukubesar)->update($databukubesar);
            echo 0;
            DB::commit();
        } catch (\Exception $e) {
            dd($e);
            echo 1;
            DB::rollback();
        }
    }

    public function inputpotongan(Request $request)
    {
        $nobukti_pembelian = Crypt::decrypt($request->nobukti_pembelian);
        $coa = DB::table('set_coa_cabang')
            ->select('set_coa_cabang.kode_akun', 'nama_akun')
            ->join('coa', 'set_coa_cabang.kode_akun', '=', 'coa.kode_akun')
            ->where('kategori', 'pembelian')->get();
        return view('pembelian.inputpotongan', compact('nobukti_pembelian', 'coa'));
    }

    public function storepotongan(Request $request)
    {
        $nobukti_pembelian = $request->nobukti_pembelian;

        $keterangan = $request->keterangan;
        $qty = !empty($request->qty) ? $request->qty : 0;
        $qty = str_replace(",", ".", $qty);
        $harga = !empty($request->harga) ? str_replace(".", "", $request->harga) : 0;
        $harga = str_replace(",", ".", $harga);
        $kode_akun = $request->kode_akun;
        $pembelian = DB::table('pembelian')->where('nobukti_pembelian', $nobukti_pembelian)->first();
        $tgl_pembelian = $pembelian->tgl_pembelian;
        $tanggal   = explode("-", $tgl_pembelian);
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];
        $thn = substr($tahun, 2, 2);
        $kode_dept = $pembelian->kode_dept;
        $kontrabon = DB::table('detail_kontrabon')->where('nobukti_pembelian', $nobukti_pembelian)
            ->join('kontrabon', 'detail_kontrabon.no_kontrabon', '=', 'kontrabon.no_kontrabon')
            ->whereNull('status')
            ->orderBy('tgl_kontrabon', 'desc')
            ->first();
        $detailpotongan = DB::table('detail_pembelian')->where('status', 'PNJ')->where('nobukti_pembelian', $nobukti_pembelian)->first();
        if ($detailpotongan != null) {
            $no_urut = $detailpotongan->no_urut + 1;
        } else {
            $no_urut = 1;
        }
        $bukubesar = DB::table('buku_besar')->whereRaw('LEFT(no_bukti,6)="GJ' . $bulan . $thn . '"')
            ->orderBy('no_bukti', 'desc')
            ->first();
        if ($bukubesar != null) {
            $last_no_bukti_bukubesar = $bukubesar->no_bukti;
        } else {
            $last_no_bukti_bukubesar = "";
        }

        $nobukti_bukubesar = buatkode($last_no_bukti_bukubesar, 'GJ' . $bulan . $thn, 6);


        DB::beginTransaction();
        try {
            $data = [
                'nobukti_pembelian' => $nobukti_pembelian,
                'kode_barang' => 'PNJKR',
                'ket_penjualan' => $keterangan,
                'qty' => $qty,
                'penyesuaian' => 0.00,
                'harga' => $harga,
                'kode_akun' => $kode_akun,
                'status' => 'PNJ',
                'no_urut' => $no_urut,
                'nobukti_bukubesar' => $nobukti_bukubesar
            ];
            $databukubesar = array(
                'no_bukti' => $nobukti_bukubesar,
                'tanggal' => $tgl_pembelian,
                'sumber' => 'pembelian',
                'keterangan' => $keterangan,
                'kode_akun' => $kode_akun,
                'debet' => 0,
                'kredit' => ($qty * $harga),
                'nobukti_transaksi' => $nobukti_pembelian
            );
            DB::table('buku_besar')->insert($databukubesar);
            DB::table('detail_pembelian')->insert($data);
            if ($kode_dept != "GDB") {
                $kode_akun = "2-1300";
            } else {
                $kode_akun = "2-1200";
            }

            $jmlbayar = DB::table('detail_pembelian')
                ->selectRaw("SUM(IF(status='PMB',(qty*harga+penyesuaian),0)) - SUM(IF(status='PNJ',(qty*harga+penyesuaian),0))  as jmlbayar")
                ->where('nobukti_pembelian', $nobukti_pembelian)
                ->first();
            $databukubesar = array(
                'kode_akun' => $kode_akun,
                'debet' => 0,
                'kredit' => $jmlbayar->jmlbayar,
            );
            $datakontrabon = array(
                'jmlbayar' => $jmlbayar->jmlbayar
            );
            if ($kontrabon != null) {
                DB::table('detail_kontrabon')->where('nobukti_pembelian', $nobukti_pembelian)->where('no_kontrabon', $kontrabon->no_kontrabon)->update($datakontrabon);
            }
            DB::table('buku_besar')->where('no_bukti', $pembelian->nobukti_bukubesar)->update($databukubesar);
            echo 0;
            DB::commit();
        } catch (\Exception $e) {
            dd($e);
            echo 1;
            DB::rollback();
        }
    }


    public function showdetailpotongan(Request $request)
    {
        $nobukti_pembelian = $request->nobukti_pembelian;

        $detail = DB::table('detail_pembelian')
            ->select('detail_pembelian.*')
            ->where('nobukti_pembelian', $nobukti_pembelian)
            ->where('detail_pembelian.status', 'PNJ')
            ->get();
        return view('pembelian.showdetailpotongan', compact('detail'));
    }

    public function update($nobukti_pembelian, Request $request)
    {
        $id_admin = Auth::user()->id;
        $no_bukti = Crypt::decrypt($nobukti_pembelian);

        $nobukti_pembelian = $request->nobukti_pembelian;
        $pembelian = DB::table('pembelian')->where('nobukti_pembelian', $no_bukti)->first();
        $tgl_pembelian = $request->tgl_pembelian;
        $kode_supplier = $request->kode_supplier;
        $kode_dept = $request->kode_dept;
        $jenistransaksi = $request->jenistransaksi;
        $tgl_jatuhtempo = $jenistransaksi == 'kredit' ? $request->tgl_jatuhtempo : $tgl_pembelian;
        $ppn = empty($request->ppn) ? 0 : $request->ppn;
        $tanggal   = explode("-", $tgl_pembelian);
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];
        $thn = substr($tahun, 2, 2);
        $tgl = $tanggal[2] . $tanggal[1] . $tanggal[0];
        $rand = rand(10, 100);
        $nokontrabon = $jenistransaksi == "tunai" ?  "T" . $tgl . $rand : '';
        $ref_tunai = $pembelian->ref_tunai;
        $jmlbayar = DB::table('detail_pembelian')
            ->selectRaw("SUM(IF(status='PMB',(qty*harga+penyesuaian),0)) - SUM(IF(status='PNJ',(qty*harga+penyesuaian),0))  as jmlbayar")
            ->where('nobukti_pembelian', $no_bukti)
            ->first();
        if ($kode_dept != "GDB") {
            $kode_akun = "2-1300";
        } else {
            $kode_akun = "2-1200";
        }
        DB::beginTransaction();
        try {
            if ($jenistransaksi == "tunai" && $pembelian->jenistransaksi == "kredit") {
                $cekdetailkontrabon = DB::table('detail_kontrabon')->where('nobukti_pembelian', $no_bukti)->get();
                foreach ($cekdetailkontrabon as $d) {
                    DB::table('detail_kontrabon')->where('nobukti_pembelian', $no_bukti)->where('no_kontrabon', $d->no_kontrabon)->delete();
                    $cekkontrabon = DB::table('detail_kontrabon')->where('no_kontrabon', $d->no_kontrabon)->count();
                    if (empty($cekkontrabon)) {
                        DB::table('kontrabon')->where('no_kontrabon', $d->no_kontrabon)->delete();
                    }
                }
                $datakontrabon = array(
                    'no_kontrabon'       => $nokontrabon,
                    'tgl_kontrabon'      => $tgl_pembelian,
                    'kode_supplier'      => $kode_supplier,
                    'kategori'           => 'TN',
                    'id_admin'           => $id_admin,
                    'jenisbayar'         => 'tunai'
                );

                $datadetailkontrabon = array(
                    'no_kontrabon'        => $nokontrabon,
                    'nobukti_pembelian'   => $nobukti_pembelian,
                    'jmlbayar'            => $jmlbayar->jmlbayar,
                    'keterangan'          => 'tunai'
                );
                DB::table('kontrabon')->insert($datakontrabon);
                DB::table('detail_kontrabon')->insert($datadetailkontrabon);
            } else  if ($jenistransaksi == "kredit" && $pembelian->jenistransaksi == "tunai") {


                DB::table('kontrabon')->where('no_kontrabon', $ref_tunai)->delete();
                DB::table('detail_kontrabon')->where('no_kontrabon', $ref_tunai)->delete();
            } else {
                $kontrabon = DB::table('detail_kontrabon')
                    ->join('kontrabon', 'detail_kontrabon.no_kontrabon', '=', 'kontrabon.no_kontrabon')
                    ->where('nobukti_pembelian', $pembelian->nobukti_pembelian)
                    ->whereNull('status')
                    ->orderBy('tgl_kontrabon', 'desc')
                    ->first();
                if ($jenistransaksi == "tunai") {
                    $datakb = [
                        'kode_supplier' => $kode_supplier,
                        'tgl_kontrabon' => $tgl_pembelian,
                    ];
                } else {
                    $datakb = [
                        'kode_supplier' => $kode_supplier
                    ];
                }
                if ($kontrabon != null) {
                    DB::table('kontrabon')->where('no_kontrabon', $kontrabon->no_kontrabon)->update($datakb);
                }
            }


            $datacr = [
                'tgl_transaksi' => $tgl_pembelian
            ];

            DB::table('costratio_biaya')
                ->leftJoin('detail_pembelian', 'costratio_biaya.kode_cr', '=', 'detail_pembelian.kode_cr')
                ->where('nobukti_pembelian', $no_bukti)
                ->update($datacr);


            //Update Pembelian
            $data = [
                'nobukti_pembelian' => $nobukti_pembelian,
                'tgl_pembelian' => $tgl_pembelian,
                'kode_supplier' => $kode_supplier,
                'kode_dept' => $kode_dept,
                'jenistransaksi' => $jenistransaksi,
                'tgl_jatuhtempo' => $tgl_jatuhtempo,
                'ppn' => $ppn,
                'ref_tunai' => $nokontrabon
            ];
            DB::table('pembelian')->where('nobukti_pembelian', $no_bukti)->update($data);

            //Buku Besar
            $databukubesar = [
                'keterangan' => 'Pembelian ' . $nobukti_pembelian,
                'nobukti_transaksi' => $nobukti_pembelian,
                'tanggal' => $tgl_pembelian,
                'kode_akun' => $kode_akun
            ];
            $databukubesardetail = [
                'nobukti_transaksi' => $nobukti_pembelian,
                'tanggal' => $tgl_pembelian
            ];



            DB::table('buku_besar')->where('no_bukti', $pembelian->nobukti_bukubesar)->update($databukubesar);
            DB::table('buku_besar')->where('nobukti_transaksi', $no_bukti)->where('no_bukti', '!=', $pembelian->nobukti_bukubesar)->update($databukubesardetail);


            //Costratio
            DB::commit();
            return redirect('/pembelian/' . Crypt::encrypt($nobukti_pembelian) . '/edit')->with(['success' => 'Data Penjualan Berhasil di Update']);
        } catch (\Exception $e) {
            dd($e);
            return redirect('/pembelian/' . Crypt::encrypt($nobukti_pembelian) . '/edit')->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
            DB::rollback();
        }
    }

    public function delete($nobukti_pembelian)
    {
        $nobukti_pembelian = Crypt::decrypt($nobukti_pembelian);
        DB::beginTransaction();
        try {
            $cekdetailkontrabon = DB::table('detail_kontrabon')->where('nobukti_pembelian', $nobukti_pembelian)->get();
            DB::table('costratio_biaya')
                ->leftJoin('detail_pembelian', 'costratio_biaya.kode_cr', '=', 'detail_pembelian.kode_cr')
                ->where('nobukti_pembelian', $nobukti_pembelian)
                ->delete();
            DB::table('pembelian')->where('nobukti_pembelian', $nobukti_pembelian)->delete();
            DB::table('buku_besar')->where('nobukti_transaksi', $nobukti_pembelian)->delete();

            foreach ($cekdetailkontrabon as $d) {
                $no_kontrabon = $d->no_kontrabon;
                DB::table('detail_kontrabon')->where('nobukti_pembelian', $nobukti_pembelian)->where('no_kontrabon', $d->no_kontrabon)->delete();
                $cekkontrabon = DB::table('detail_kontrabon')->where('no_kontrabon', $no_kontrabon)->count();
                if (empty($cekkontrabon)) {
                    DB::table('kontrabon')->where('no_kontrabon', $no_kontrabon)->delete();
                }
            }

            //die;
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Penjualan Berhasil di Update']);
        } catch (\Exception $e) {
            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
            DB::rollback();
        }
    }

    public function getpembeliankontrabon($kode_supplier)
    {
        $pembelian = DB::table('pembelian')
            ->selectRaw("pembelian.nobukti_pembelian,
            tgl_pembelian,
            pembelian.kode_supplier,
            nama_supplier,
            totalpembelian,
            jmlbayar,
            pembelian.kode_dept,
            nama_dept,
            pembelian.jenistransaksi")
            ->join('supplier', 'pembelian.kode_supplier', '=', 'supplier.kode_supplier')
            ->join('departemen', 'pembelian.kode_dept', '=', 'departemen.kode_dept')
            ->leftJoin(
                DB::raw('(
                    SELECT nobukti_pembelian, SUM( IF ( STATUS = "PMB", ( ( qty * harga ) + penyesuaian ), 0 ) ) - SUM( IF ( STATUS = "PNJ", ( qty * harga ), 0 ) ) as totalpembelian
                    FROM detail_pembelian
                    GROUP BY nobukti_pembelian
                ) detailpembelian'),
                function ($join) {
                    $join->on('pembelian.nobukti_pembelian', '=', 'detailpembelian.nobukti_pembelian');
                }
            )
            ->leftJoin(
                DB::raw('(
                    SELECT nobukti_pembelian, SUM(jmlbayar) as jmlbayar
                    FROM
                    historibayar_pembelian hb
                    INNER JOIN detail_kontrabon ON hb.no_kontrabon = detail_kontrabon.no_kontrabon
                    GROUP BY nobukti_pembelian
                ) historibayar'),
                function ($join) {
                    $join->on('pembelian.nobukti_pembelian', '=', 'historibayar.nobukti_pembelian');
                }
            )

            ->where('pembelian.kode_supplier', $kode_supplier)
            ->where('pembelian.jenistransaksi', '!=', 'tunai')
            ->whereRaw('IFNULL( jmlbayar, 0 ) != ( totalpembelian )')
            ->orderBy('tgl_pembelian')
            ->get();

        return view('pembelian.getpembeliankontrabon', compact('pembelian'));
    }

    public function jatuhtempo(Request $request)
    {
        $query = Pembelian::query();
        $query->selectRaw("pembelian.nobukti_pembelian,
        tgl_pembelian,
        tgl_jatuhtempo,
        ppn,
        no_fak_pajak,
        pembelian.kode_supplier,
        nama_supplier,
        pembelian.kode_dept,
        jenistransaksi,
        ref_tunai,
        harga,
        kontrabon,
        penyesuaian,
        jmlbayar");
        $query->join('supplier', 'pembelian.kode_supplier', '=', 'supplier.kode_supplier');
        $query->leftJoin(
            DB::raw('(
                SELECT nobukti_pembelian, SUM( IF ( STATUS = "PMB", ( ( qty * harga ) + penyesuaian ), 0 ) ) - SUM( IF ( STATUS = "PNJ", ( qty * harga ), 0 ) ) as harga
                FROM detail_pembelian
                GROUP BY nobukti_pembelian
            ) detailpembelian'),
            function ($join) {
                $join->on('pembelian.nobukti_pembelian', '=', 'detailpembelian.nobukti_pembelian');
            }
        );
        $query->leftJoin(
            DB::raw('(
                SELECT nobukti_pembelian, COUNT( nobukti_pembelian ) as kontrabon
                FROM detail_kontrabon
                GROUP BY nobukti_pembelian
            ) kontrabon'),
            function ($join) {
                $join->on('pembelian.nobukti_pembelian', '=', 'kontrabon.nobukti_pembelian');
            }
        );

        $query->leftJoin(
            DB::raw('(
                SELECT
                nobukti_pembelian,
                (SUM(IF( status_dk = "K" AND kode_akun = "2-1200" OR status_dk = "K" AND kode_akun = "2-1300", (qty * harga), 0))
                - SUM(IF( status_dk = "D" AND kode_akun = "2-1200" OR status_dk = "D" AND kode_akun = "2-1300", (qty * harga), 0))
                ) as penyesuaian
                FROM
                jurnal_koreksi
                GROUP BY nobukti_pembelian
            ) jurnalkoreksi'),
            function ($join) {
                $join->on('pembelian.nobukti_pembelian', '=', 'jurnalkoreksi.nobukti_pembelian');
            }
        );

        $query->leftJoin(
            DB::raw('(
                SELECT
                nobukti_pembelian,
                SUM(jmlbayar) as jmlbayar
                FROM
                historibayar_pembelian hb
                INNER JOIN detail_kontrabon ON hb.no_kontrabon = detail_kontrabon.no_kontrabon
                GROUP BY
                nobukti_pembelian
            ) historibayar'),
            function ($join) {
                $join->on('pembelian.nobukti_pembelian', '=', 'historibayar.nobukti_pembelian');
            }
        );


        $query->whereBetween('tgl_jatuhtempo', [$request->dari, $request->sampai]);
        if (!empty($request->nobukti_pembelian)) {
            $query->where('pembelian.nobukti_pembelian', $request->nobukti_pembelian);
        }

        if (!empty($request->kode_dept)) {
            $query->where('pembelian.kode_dept', $request->kode_dept);
        }

        if (!empty($request->kode_supplier)) {
            $query->where('pembelian.kode_supplier', $request->kode_supplier);
        }
        $query->orderBy('tgl_jatuhtempo', 'asc');
        $query->orderBy('nobukti_pembelian', 'desc');
        $pembelian = $query->paginate(15);
        $pembelian->appends($request->all());
        $departemen = DB::table('departemen')->where('status_pengajuan', 1)->get();
        $supplier = Supplier::orderBy('nama_supplier')->get();

        return view('pembelian.jatuhtempo', compact('departemen', 'supplier', 'pembelian'));
    }


    public function getpembelianjurnalkoreksi($kode_supplier)
    {
        $pembelian = DB::table('pembelian')
            ->selectRaw("pembelian.nobukti_pembelian,
            tgl_pembelian,
            pembelian.kode_supplier,
            nama_supplier,
            totalpembelian,
            jmlbayar,
            pembelian.kode_dept,
            nama_dept,
            pembelian.jenistransaksi")
            ->join('supplier', 'pembelian.kode_supplier', '=', 'supplier.kode_supplier')
            ->join('departemen', 'pembelian.kode_dept', '=', 'departemen.kode_dept')
            ->leftJoin(
                DB::raw('(
                    SELECT nobukti_pembelian, SUM( IF ( STATUS = "PMB", ( ( qty * harga ) + penyesuaian ), 0 ) ) - SUM( IF ( STATUS = "PNJ", ( qty * harga ), 0 ) ) as totalpembelian
                    FROM detail_pembelian
                    GROUP BY nobukti_pembelian
                ) detailpembelian'),
                function ($join) {
                    $join->on('pembelian.nobukti_pembelian', '=', 'detailpembelian.nobukti_pembelian');
                }
            )
            ->leftJoin(
                DB::raw('(
                    SELECT nobukti_pembelian, SUM(jmlbayar) as jmlbayar
                    FROM
                    historibayar_pembelian hb
                    INNER JOIN detail_kontrabon ON hb.no_kontrabon = detail_kontrabon.no_kontrabon
                    GROUP BY nobukti_pembelian
                ) historibayar'),
                function ($join) {
                    $join->on('pembelian.nobukti_pembelian', '=', 'historibayar.nobukti_pembelian');
                }
            )

            ->where('pembelian.kode_supplier', $kode_supplier)
            ->where('pembelian.jenistransaksi', '!=', 'tunai')
            ->whereRaw('IFNULL( jmlbayar, 0 ) != ( totalpembelian )')
            ->orderBy('tgl_pembelian')
            ->get();

        echo "<option value=''>Pilih No. Bukti Pembelian</option>";
        foreach ($pembelian as $d) {
            $nobukti_pembelian = Crypt::encrypt($d->nobukti_pembelian);
            echo "<option value='$nobukti_pembelian'>$d->nobukti_pembelian</option>";
        }
    }

    public function getbarangjurnalkoreksi($nobukti_pembelian)
    {
        $nobukti_pembelian = Crypt::decrypt($nobukti_pembelian);
        $detailpembelian = DB::table('detail_pembelian')
            ->join('master_barang_pembelian', 'detail_pembelian.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('nobukti_pembelian', $nobukti_pembelian)
            ->get();
        echo "<option value=''>Pilih Barang</option>";
        foreach ($detailpembelian as $d) {
            echo "<option value='$d->kode_barang'>$d->nama_barang</option>";
        }
    }

    public function storeprosespembelian($nobukti_pembelian, Request $request)
    {
        $nobukti_pembelian = Crypt::decrypt($nobukti_pembelian);
        $pembelian = DB::table('pembelian')->where('nobukti_pembelian', $nobukti_pembelian)->first();
        $detailpembelian = DB::table('detail_pembelian')->where('nobukti_pembelian', $nobukti_pembelian)->get();
        $tgl_pembelian = $pembelian->tgl_pembelian;
        $tgl_pemasukan = $request->tgl_pemasukan;
        DB::beginTransaction();
        try {
            $data = [
                'nobukti_pemasukan' => $nobukti_pembelian,
                'tgl_pembelian' => $tgl_pembelian,
                'tgl_pemasukan' => $tgl_pemasukan,
                'gdb' => 0
            ];
            DB::table('pemasukan')->insert($data);
            foreach ($detailpembelian as $d) {
                $datadetail = [
                    'nobukti_pemasukan' => $nobukti_pembelian,
                    'kode_barang' => $d->kode_barang,
                    'keterangan' => $d->keterangan,
                    'qty' => $d->qty,
                    'harga' => $d->harga,
                    'penyesuaian' => $d->penyesuaian,
                    'kode_akun' => $d->kode_akun
                ];

                DB::table('detail_pemasukan')->insert($datadetail);
            }
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Di Proses']);
        } catch (\Exception $e) {
            //dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Diproses, Hubungi Tim IT']);
            DB::rollback();
        }
    }

    public function updatecostratio()
    {
        $dari = "2022-05-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $pembelian = DB::table('detail_pembeliand')
            ->select('tgl_pembelian', 'detail_pembelian.*', 'nama_barang')
            ->join('pembelian', 'detail_pembelian.nobukti_pembelian', '=', 'pembelian.nobukti_pembelian')
            ->join('master_barang_pembelian', 'detail_pembelian.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->whereBetween('tgl_pembelian', [$dari, $sampai])
            ->whereRaw('LEFT(detail_pembelian.kode_akun,3)="6-1"')
            ->where('detail_pembelian.kode_cabang', '!=', '')
            ->orWhereBetween('tgl_pembelian', [$dari, $sampai])
            ->whereRaw('LEFT(detail_pembelian.kode_akun,3)="6-2"')
            ->where('detail_pembelian.kode_cabang', '!=', '')
            ->get();
        dd($pembelian);

        $kode = "CR0522";
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
        $ceksimpan = 0;
        $cekupdate = 0;
        foreach ($pembelian as $d) {
            $kode_cr = buatkode($kode_cr, $kode, 4);
            $data = [
                'kode_cr' => $kode_cr,
                'tgl_transaksi' => $d->tgl_pembelian,
                'kode_akun' => $d->kode_akun,
                'keterangan'   => "Pembelian " . $d->nama_barang . "(" . $d->qty . ")",
                'kode_cabang'  => $d->kode_cabang,
                'id_sumber_costratio' => 4,
                'jumlah' => ($d->qty * $d->harga) + $d->penyesuaian
            ];
            $simpan = DB::table('costratio_biaya')->insert($data);
            $update = DB::table('detail_pembelian')->where('nobukti_pembelian', $d->nobukti_pembelian)->where('kode_barang', $d->kode_barang)->update(['kode_cr' => $kode_cr]);
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
    }
}
