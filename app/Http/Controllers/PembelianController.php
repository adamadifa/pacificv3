<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Pembelian;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PembelianController extends Controller
{
    public function index(Request $request)
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


        $query->whereBetween('tgl_pembelian', [$request->dari, $request->sampai]);
        if (!empty($request->nobukti_pembelian)) {
            $query->where('pembelian.nobukti_pembelian', $request->nobukti_pembelian);
        }

        if (!empty($request->kode_dept)) {
            $query->where('pembelian.kode_dept', $request->kode_dept);
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
        return view('pembelian.index', compact('departemen', 'supplier', 'pembelian'));
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
        $harga = !empty($request->harga) ? str_replace(".", "", $request->harga) : 0;
        $peny_harga = !empty($request->peny_harga) ? str_replace(".", "", $request->peny_harga) : 0;
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
                'id_admin'           => $id_admin
            );
            DB::table('pembelian')->insert($data);
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
            foreach ($detailtemp as $d) {

                if (substr($d->kode_akun, 0, 3) == "6-1" && !empty($d->kode_cabang) or substr($d->kode_akun, 0, 3) == "6-2" && !empty($d->kode_cabang)) {
                    $bulan = $tanggal[1];
                    $tahun = $tanggal[2];
                    $thn = substr($tahun, 2, 2);
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
                    'kode_cr'           => $kode_cr
                );

                DB::table('detail_pembelian')->insert($datadetail);
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
}
