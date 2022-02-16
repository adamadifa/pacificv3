<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Harga;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use PDOException;

class PenjualanController extends Controller
{

    public function index(Request $request)
    {
        $pelanggan = '"' . $request->nama_pelanggan . '"';
        $query = Penjualan::query();
        $query->select('penjualan.*', 'nama_pelanggan', 'nama_karyawan', 'karyawan.kode_cabang');
        $query->orderBy('tgltransaksi', 'desc');
        $query->orderBy('no_fak_penj', 'asc');
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        if (empty($request->no_fak_penj) && empty($request->nama_pelanggan) && empty($request->dari) && empty($request->sampai)) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }
        if (!empty($request->nama_pelanggan)) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }

        if (!empty($request->no_fak_penj)) {
            $query->where('no_fak_penj', $request->no_fak_penj);
        }

        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgltransaksi', [$request->dari, $request->sampai]);
        }



        $penjualan = $query->paginate(15);

        $penjualan->appends($request->all());
        return view('penjualan.index', compact('penjualan'));
    }


    //Create

    public function create()
    {
        return view('penjualan.create');
    }

    public function edit($no_fak_penj)
    {
        $no_fak_penj = Crypt::decrypt($no_fak_penj);
        DB::beginTransaction();
        try {
            DB::table('detailpenjualan_edit')->where('no_fak_penj', $no_fak_penj)->delete();
            $detail = DB::table('detailpenjualan')->where('no_fak_penj', $no_fak_penj)->get();
            foreach ($detail as $d) {
                DB::table('detailpenjualan_edit')->insert([
                    'no_fak_penj' => $d->no_fak_penj,
                    'kode_barang' => $d->kode_barang,
                    'harga_dus' => $d->harga_dus,
                    'harga_pack' => $d->harga_pack,
                    'harga_pcs' => $d->harga_pcs,
                    'jumlah' => $d->jumlah,
                    'subtotal' => $d->subtotal,
                    'promo' => $d->promo,
                    'id_admin' => $d->id_admin
                ]);
            }
            $faktur = DB::table('penjualan')
                ->select(
                    'penjualan.*',
                    'nama_pelanggan',
                    'alamat_pelanggan',
                    'alamat_toko',
                    'pelanggan.no_hp',
                    'latitude',
                    'longitude',
                    'limitpel',
                    'karyawan.nama_karyawan',
                    'karyawan.kategori_salesman',
                    'karyawan.kode_cabang'
                )
                ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
                ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
                ->where('penjualan.no_fak_penj', $no_fak_penj)
                ->first();

            $cektitipan = DB::table('historibayar')
                ->where('tglbayar', $faktur->tgltransaksi)
                ->where('no_fak_penj', $no_fak_penj)
                ->where('jenisbayar', 'titipan')
                ->first();
            $cekvouchertunai = DB::table('historibayar')
                ->where('tglbayar', $faktur->tgltransaksi)
                ->where('no_fak_penj', $no_fak_penj)
                ->where('jenisbayar', 'tunai')
                ->where('status_bayar', 'voucher')
                ->first();
            DB::commit();
            return view('penjualan.edit', compact('faktur', 'cektitipan', 'cekvouchertunai'));
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            return Redirect::back();
        }
    }


    public function storebarangtemp(Request $request)
    {
        $barang = Harga::where('kode_barang', $request->kode_barang)->first();
        $id_user = Auth::user()->id;
        $cek = DB::table('detailpenjualan_temp')->where('kode_barang', $request->kode_barang)->where('id_admin', $id_user)->whereNull('promo')->count();
        if (empty($cek)) {
            $simpan = DB::table('detailpenjualan_temp')
                ->insert([
                    'kode_barang' => $request->kode_barang,
                    'jumlah' => 0,
                    'harga_dus' => $barang->harga_dus,
                    'harga_pack' => $barang->harga_pack,
                    'harga_pcs' => $barang->harga_pcs,
                    'subtotal' => 0,
                    'id_admin' => $id_user
                ]);
            if ($simpan) {
                echo 0;
            } else {
                echo 2;
            }
        } else {
            echo 1;
        }
    }


    public function storebarang(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $barang = Harga::where('kode_barang', $request->kode_barang)->first();
        $id_user = Auth::user()->id;
        $cek = DB::table('detailpenjualan_edit')->where('kode_barang', $request->kode_barang)->where('no_fak_penj', $no_fak_penj)->whereNull('promo')->count();
        if (empty($cek)) {
            $simpan = DB::table('detailpenjualan_edit')
                ->insert([
                    'no_fak_Penj' => $no_fak_penj,
                    'kode_barang' => $request->kode_barang,
                    'jumlah' => 0,
                    'harga_dus' => $barang->harga_dus,
                    'harga_pack' => $barang->harga_pack,
                    'harga_pcs' => $barang->harga_pcs,
                    'subtotal' => 0,
                    'id_admin' => $id_user
                ]);
            if ($simpan) {
                echo 0;
            } else {
                echo 2;
            }
        } else {
            echo 1;
        }
    }

    public function showbarangtemp()
    {
        $id_user = Auth::user()->id;
        $barang = DB::table('detailpenjualan_temp')
            ->select('detailpenjualan_temp.*', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs', 'cekjmlbarang')
            ->join('barang', 'detailpenjualan_temp.kode_barang', '=', 'barang.kode_barang')
            ->leftJoin(
                DB::raw("(
                    SELECT kode_barang,COUNT(kode_barang) as cekjmlbarang FROM detailpenjualan_temp GROUP BY kode_barang
                ) dbtemp"),
                function ($join) {
                    $join->on('detailpenjualan_temp.kode_barang', '=', 'dbtemp.kode_barang');
                }
            )
            ->where('id_admin', $id_user)
            ->get();
        return view('penjualan.showbarangtemp', compact('barang'));
    }

    public function showbarang(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $faktur = DB::table('penjualan')
            ->select(
                'penjualan.*'
            )
            ->where('penjualan.no_fak_penj', $no_fak_penj)
            ->first();
        $barang = DB::table('detailpenjualan_edit')
            ->select('detailpenjualan_edit.*', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs', 'cekjmlbarang')
            ->join('barang', 'detailpenjualan_edit.kode_barang', '=', 'barang.kode_barang')
            ->leftJoin(
                DB::raw("(
                    SELECT kode_barang,COUNT(kode_barang) as cekjmlbarang FROM detailpenjualan_edit GROUP BY kode_barang
                ) db"),
                function ($join) {
                    $join->on('detailpenjualan_edit.kode_barang', '=', 'db.kode_barang');
                }
            )
            ->where('no_fak_penj', $no_fak_penj)
            ->get();
        return view('penjualan.showbarang', compact('barang', 'faktur'));
    }




    public function deletebarangtemp(Request $request)
    {
        $id_user = Auth::user()->id;
        $hapus = DB::table('detailpenjualan_temp')
            ->where('kode_barang', $request->kode_barang)
            ->where('id_admin', $id_user)
            ->where('promo', $request->promo)
            ->delete();
        if ($hapus) {
            echo 1;
        } else {
            echo 2;
        }
    }

    public function updatedetailtemp(Request $request)
    {
        $id_user = Auth::user()->id;
        $barang = DB::table('barang')->where('kode_barang', $request->kode_barang)->first();
        $detailtemp = DB::table('detailpenjualan_temp')->where('kode_barang', $request->kode_barang)->where('id_admin', $id_user)->first();
        $jmldus = $request->jmldus * $barang->isipcsdus;
        $jmlpack = $request->jmlpack * $barang->isipcs;
        $jmlpcs = $request->jmlpcs;
        // echo $request->harga_dus;
        // die;
        if (isset($request->promo)) {
            if ($request->promo == 1) {
                $promo = 1;
                if (isset($request->check)) {
                    if ($request->check == "true") {
                        $wherepromo = NULL;
                    } else {
                        $wherepromo = 1;
                    }
                } else {
                    $wherepromo = 1;
                }
            } else {
                $promo = NULL;
                if (isset($request->check)) {
                    if ($request->check == "true") {
                        $wherepromo = NULL;
                        echo "test1";
                    } else {
                        $wherepromo = 1;
                        echo "test2";
                    }
                } else {
                    $wherepromo = NULL;
                    echo "test";
                }
            }
        } else {
            if ($detailtemp->promo == 1) {
                $promo = 1;
                if (isset($request->check)) {
                    if ($request->check == "true") {
                        $wherepromo = NULL;
                    } else {
                        $wherepromo = 1;
                    }
                } else {
                    $wherepromo = 1;
                }
            } else {
                $promo = NULL;
                if (isset($request->check)) {
                    if ($request->check = "true") {
                        $wherepromo = NULL;
                    } else {
                        $wherepromo = 1;
                    }
                } else {
                    $wherepromo = NULL;
                }
            }
        }

        //$cekpromo = DB::table('detailpenjualan_temp')->where('kode_barang', $request->kode_barang)->where('id_admin', $id_user)->where('promo', $promo)->count();
        $harga_dus = str_replace(".", "", $request->harga_dus);
        $harga_pack = str_replace(".", "", $request->harga_pack);
        $harga_pcs = str_replace(".", "", $request->harga_pcs);
        $totalqty = $jmldus + $jmlpack + $jmlpcs;
        $total = $request->total;

        DB::table('detailpenjualan_temp')
            ->where('kode_barang', $request->kode_barang)
            ->where('id_admin', $id_user)
            ->where('promo', $wherepromo)
            ->update([
                'jumlah' => $totalqty,
                'harga_dus' => $harga_dus,
                'harga_pack' => $harga_pack,
                'harga_pcs' => $harga_pcs,
                'subtotal' => $total,
                'promo' => $promo
            ]);
    }


    public function updatedetail(Request $request)
    {

        $no_fak_penj = $request->no_fak_penj;
        $barang = DB::table('barang')->where('kode_barang', $request->kode_barang)->first();
        $detail = DB::table('detailpenjualan_edit')->where('kode_barang', $request->kode_barang)->where('no_fak_penj', $no_fak_penj)->first();
        $jmldus = $request->jmldus * $barang->isipcsdus;
        $jmlpack = $request->jmlpack * $barang->isipcs;
        $jmlpcs = $request->jmlpcs;
        // echo $request->harga_dus;
        // die;
        if (isset($request->promo)) {
            if ($request->promo == 1) {
                $promo = 1;
                if (isset($request->check)) {
                    if ($request->check == "true") {
                        $wherepromo = NULL;
                    } else {
                        $wherepromo = 1;
                    }
                } else {
                    $wherepromo = 1;
                }
            } else {
                $promo = NULL;
                if (isset($request->check)) {
                    if ($request->check == "true") {
                        $wherepromo = NULL;
                        echo "test1";
                    } else {
                        $wherepromo = 1;
                        echo "test2";
                    }
                } else {
                    $wherepromo = NULL;
                    echo "test";
                }
            }
        } else {
            if ($detail->promo == 1) {
                $promo = 1;
                if (isset($request->check)) {
                    if ($request->check == "true") {
                        $wherepromo = NULL;
                    } else {
                        $wherepromo = 1;
                    }
                } else {
                    $wherepromo = 1;
                }
            } else {
                $promo = NULL;
                if (isset($request->check)) {
                    if ($request->check = "true") {
                        $wherepromo = NULL;
                    } else {
                        $wherepromo = 1;
                    }
                } else {
                    $wherepromo = NULL;
                }
            }
        }

        //$cekpromo = DB::table('detailpenjualan_temp')->where('kode_barang', $request->kode_barang)->where('id_admin', $id_user)->where('promo', $promo)->count();
        $harga_dus = str_replace(".", "", $request->harga_dus);
        $harga_pack = str_replace(".", "", $request->harga_pack);
        $harga_pcs = str_replace(".", "", $request->harga_pcs);
        $totalqty = $jmldus + $jmlpack + $jmlpcs;
        $total = $request->total;

        DB::table('detailpenjualan_edit')
            ->where('kode_barang', $request->kode_barang)
            ->where('no_fak_penj', $no_fak_penj)
            ->where('promo', $wherepromo)
            ->update([
                'jumlah' => $totalqty,
                'harga_dus' => $harga_dus,
                'harga_pack' => $harga_pack,
                'harga_pcs' => $harga_pcs,
                'subtotal' => $total,
                'promo' => $promo
            ]);
    }

    public function loadtotalpenjualantemp()
    {
        $detail = DB::table('detailpenjualan_temp')
            ->select(DB::raw('SUM(subtotal) AS total'))
            ->where('id_admin', Auth::user()->id)
            ->first();
        echo rupiah($detail->total);
    }

    public function loadtotalpenjualan(Request $request)
    {
        $detail = DB::table('detailpenjualan_edit')
            ->select(DB::raw('SUM(subtotal) AS total'))
            ->where('no_fak_penj', $request->no_fak_penj)
            ->first();
        echo rupiah($detail->total);
    }

    public function hitungdiskon(Request $request)
    {
        $jenistransaksi = $request->jenistransaksi;
        $id_user = Auth::user()->id;
        $detail = DB::table('detailpenjualan_temp')
            ->select('detailpenjualan_temp.kode_barang', 'promo', 'isipcsdus', 'kategori', 'jumlah')
            ->join('barang', 'detailpenjualan_temp.kode_barang', '=', 'barang.kode_barang')
            ->where('id_admin', $id_user)
            ->whereNull('promo')
            ->get();
        $jmldusswan = 0;
        $jmldusaida = 0;
        $jmldusstick = 0;
        $jmldussp = 0;
        $jmldussb = 0;
        foreach ($detail as $d) {
            $jmldus      = floor($d->jumlah / $d->isipcsdus);
            if ($d->kategori == "SWAN") {
                $jmldusswan   = $jmldusswan + $jmldus;
            }

            if ($d->kategori == "AIDA") {
                $jmldusaida   = $jmldusaida + $jmldus;
            }

            if ($d->kategori == "STICK") {
                $jmldusstick   = $jmldusstick + $jmldus;
            }

            if ($d->kategori == "STICK") {
                $jmldusstick   = $jmldusstick + $jmldus;
            }

            if ($d->kategori == "SP") {
                $jmldussp   = $jmldussp + $jmldus;
            }

            if ($d->kategori == "SAMBAL") {
                $jmldussb   = $jmldussb + $jmldus;
            }
        }

        $diskon = DB::table('diskon')->get();
        $diskonswan = 0;
        $diskonaida = 0;
        $diskonstick = 0;
        $diskonsp = 0;
        $diskonsb = 0;

        $diskonswantunai = 0;
        $diskonaidatunai = 0;
        $diskonsticktunai = 0;
        $diskonsptunai = 0;
        $diskonsbtunai = 0;
        foreach ($diskon as $p) {
            if ($p->kategori == "SWAN" and $jmldusswan >= $p->dari and $jmldusswan <= $p->sampai) {
                $diskonswan = $p->diskon;
                $diskonswantunai = $p->diskon_tunai;
            }

            if ($p->kategori == "AIDA" and $jmldusaida >= $p->dari and $jmldusaida <= $p->sampai) {
                $diskonaida = $p->diskon;
                $diskonaidatunai = $p->diskon_tunai;
            }

            if ($p->kategori == "STICK" and $jmldusstick >= $p->dari and $jmldusstick <= $p->sampai) {
                $diskonstick = $p->diskon;
                $diskonsticktunai = $p->diskon_tunai;
            }

            if ($p->kategori == "SP" and $jmldussp >= $p->dari and $jmldussp <= $p->sampai) {
                $diskonsp = $p->diskon;
                $diskonsptunai = $p->diskon_tunai;
            }

            if ($p->kategori == "SC" and $jmldussb >= $p->dari and $jmldussb <= $p->sampai) {
                $diskonsb = $p->diskon;
                $diskonsbtunai = $p->diskon_tunai;
            }
        }

        if ($jenistransaksi == "tunai") {
            $totaldiskonswan = ($jmldusswan * $diskonswan) + ($jmldusswan * $diskonswantunai);
            $totaldiskonaida = ($jmldusaida * $diskonaida) + ($jmldusaida * $diskonaidatunai);
            $totaldiskonstick = ($jmldusstick * $diskonstick) + ($jmldusstick * $diskonsticktunai);
            $totaldiskonsp = ($jmldussp * $diskonsp) + ($jmldussp * $diskonsptunai);
            $totaldiskonsb = ($jmldussb * $diskonsb) + ($jmldussb * $diskonsbtunai);
        } else {
            $totaldiskonswan = $jmldusswan * $diskonswan;
            $totaldiskonaida = $jmldusaida * $diskonaida;
            $totaldiskonstick = $jmldusstick * $diskonstick;
            $totaldiskonsp = $jmldussp * $diskonsp;
            $totaldiskonsb = $jmldussb * $diskonsb;
        }

        echo rupiah($totaldiskonswan), "|" . rupiah($totaldiskonaida) . "|" . rupiah($totaldiskonstick) . "|" . rupiah($totaldiskonsp) . "|" . rupiah($totaldiskonsb);
    }


    public function hitungdiskonpenjualan(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $jenistransaksi = $request->jenistransaksi;
        $detail = DB::table('detailpenjualan_edit')
            ->select('detailpenjualan_edit.kode_barang', 'promo', 'isipcsdus', 'kategori', 'jumlah')
            ->join('barang', 'detailpenjualan_edit.kode_barang', '=', 'barang.kode_barang')
            ->where('no_fak_penj', $no_fak_penj)
            ->whereNull('promo')
            ->get();
        $jmldusswan = 0;
        $jmldusaida = 0;
        $jmldusstick = 0;
        $jmldussp = 0;
        $jmldussb = 0;
        foreach ($detail as $d) {
            $jmldus      = floor($d->jumlah / $d->isipcsdus);
            if ($d->kategori == "SWAN") {
                $jmldusswan   = $jmldusswan + $jmldus;
            }

            if ($d->kategori == "AIDA") {
                $jmldusaida   = $jmldusaida + $jmldus;
            }

            if ($d->kategori == "STICK") {
                $jmldusstick   = $jmldusstick + $jmldus;
            }

            if ($d->kategori == "STICK") {
                $jmldusstick   = $jmldusstick + $jmldus;
            }

            if ($d->kategori == "SP") {
                $jmldussp   = $jmldussp + $jmldus;
            }

            if ($d->kategori == "SAMBAL") {
                $jmldussb   = $jmldussb + $jmldus;
            }
        }

        $diskon = DB::table('diskon')->get();
        $diskonswan = 0;
        $diskonaida = 0;
        $diskonstick = 0;
        $diskonsp = 0;
        $diskonsb = 0;

        $diskonswantunai = 0;
        $diskonaidatunai = 0;
        $diskonsticktunai = 0;
        $diskonsptunai = 0;
        $diskonsbtunai = 0;
        foreach ($diskon as $p) {
            if ($p->kategori == "SWAN" and $jmldusswan >= $p->dari and $jmldusswan <= $p->sampai) {
                $diskonswan = $p->diskon;
                $diskonswantunai = $p->diskon_tunai;
            }

            if ($p->kategori == "AIDA" and $jmldusaida >= $p->dari and $jmldusaida <= $p->sampai) {
                $diskonaida = $p->diskon;
                $diskonaidatunai = $p->diskon_tunai;
            }

            if ($p->kategori == "STICK" and $jmldusstick >= $p->dari and $jmldusstick <= $p->sampai) {
                $diskonstick = $p->diskon;
                $diskonsticktunai = $p->diskon_tunai;
            }

            if ($p->kategori == "SP" and $jmldussp >= $p->dari and $jmldussp <= $p->sampai) {
                $diskonsp = $p->diskon;
                $diskonsptunai = $p->diskon_tunai;
            }

            if ($p->kategori == "SC" and $jmldussb >= $p->dari and $jmldussb <= $p->sampai) {
                $diskonsb = $p->diskon;
                $diskonsbtunai = $p->diskon_tunai;
            }
        }

        if ($jenistransaksi == "tunai") {
            $totaldiskonswan = ($jmldusswan * $diskonswan) + ($jmldusswan * $diskonswantunai);
            $totaldiskonaida = ($jmldusaida * $diskonaida) + ($jmldusaida * $diskonaidatunai);
            $totaldiskonstick = ($jmldusstick * $diskonstick) + ($jmldusstick * $diskonsticktunai);
            $totaldiskonsp = ($jmldussp * $diskonsp) + ($jmldussp * $diskonsptunai);
            $totaldiskonsb = ($jmldussb * $diskonsb) + ($jmldussb * $diskonsbtunai);
        } else {
            $totaldiskonswan = $jmldusswan * $diskonswan;
            $totaldiskonaida = $jmldusaida * $diskonaida;
            $totaldiskonstick = $jmldusstick * $diskonstick;
            $totaldiskonsp = $jmldussp * $diskonsp;
            $totaldiskonsb = $jmldussb * $diskonsb;
        }

        echo rupiah($totaldiskonswan), "|" . rupiah($totaldiskonaida) . "|" . rupiah($totaldiskonstick) . "|" . rupiah($totaldiskonsp) . "|" . rupiah($totaldiskonsb);
    }

    public function cekpenjtemp()
    {
        $id_user = Auth::user()->id;
        $barang = DB::table('detailpenjualan_temp')
            ->where('id_admin', $id_user)
            ->count();
        echo $barang;
    }


    public function cekpenj(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $barang = DB::table('detailpenjualan')
            ->where('no_fak_penj', $no_fak_penj)
            ->count();
        echo $barang;
    }

    public function cekpiutangpelanggan(Request $request)
    {
        if (isset($request->no_fak_penj) && $request->no_fak_penj != "") {
            $piutang = DB::table('penjualan')
                ->select('penjualan.kode_pelanggan', DB::raw('SUM(IFNULL( retur.total, 0 )) AS totalretur,
		              SUM(IFNULL(penjualan.total,0) - IFNULL(retur.total,0) - IFNULL(jmlbayar,0)) AS sisapiutang'))
                ->leftJoin(
                    DB::raw("(
                    SELECT retur.no_fak_penj AS no_fak_penj, SUM( total ) AS total FROM retur GROUP BY retur.no_fak_penj
                ) retur"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                    }
                )
                ->leftJoin(
                    DB::raw("(
                    SELECT no_fak_penj, IFNULL(SUM(bayar),0) as jmlbayar
					FROM historibayar
                    GROUP BY no_fak_penj
                ) historibayar"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'historibayar.no_fak_penj');
                    }
                )
                ->where('penjualan.kode_pelanggan', $request->kode_pelanggan)
                ->where('penjualan.no_fak_penj', '!=', $request->no_fak_penj)
                ->groupBy('penjualan.kode_pelanggan')
                ->first();
        } else {
            $piutang = DB::table('penjualan')
                ->select('penjualan.kode_pelanggan', DB::raw('SUM(IFNULL( retur.total, 0 )) AS totalretur,
                          SUM(IFNULL(penjualan.total,0) - IFNULL(retur.total,0) - IFNULL(jmlbayar,0)) AS sisapiutang'))
                ->leftJoin(
                    DB::raw("(
                        SELECT retur.no_fak_penj AS no_fak_penj, SUM( total ) AS total FROM retur GROUP BY retur.no_fak_penj
                    ) retur"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                    }
                )
                ->leftJoin(
                    DB::raw("(
                        SELECT no_fak_penj, IFNULL(SUM(bayar),0) as jmlbayar
                        FROM historibayar
                        GROUP BY no_fak_penj
                    ) historibayar"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'historibayar.no_fak_penj');
                    }
                )
                ->where('penjualan.kode_pelanggan', $request->kode_pelanggan)
                ->groupBy('penjualan.kode_pelanggan')
                ->first();
        }

        echo $piutang->sisapiutang;
    }

    public function store(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $tgltransaksi = $request->tgltransaksi;
        $id_karyawan = $request->id_karyawan;
        $kode_pelanggan = $request->kode_pelanggan;
        $limitpel = $request->limitpel;
        $sisapiutang = $request->sisapiutang;
        $jenistransaksi = $request->jenistransaksi;
        $jenisbayar = $request->jenisbayar;
        $subtotal = $request->subtotal;
        $jatuhtempo = $request->jatuhtempo;
        $bruto = $request->bruto;
        $nama_pelanggan = $request->nama_pelanggan;
        $id_admin = Auth::user()->id;
        //Potongan
        $potaida        = str_replace(".", "", $request->potaida);
        if (empty($potaida)) {
            $potaida = 0;
        } else {
            $potaida = $potaida;
        }
        $potswan        = str_replace(".", "", $request->potswan);
        if (empty($potswan)) {
            $potswan = 0;
        } else {
            $potswan = $potswan;
        }
        $potstick       = str_replace(".", "", $request->potstick);
        if (empty($potstick)) {
            $potstick = 0;
        } else {
            $potstick = $potstick;
        }
        $potsp       = str_replace(".", "", $request->potsp);
        if (empty($potsp)) {
            $potsp = 0;
        } else {
            $potsp = $potsp;
        }
        $potsb       = str_replace(".", "", $request->potsb);
        if (empty($potsb)) {
            $potsambal = 0;
        } else {
            $potsambal = $potsb;
        }

        // Voucher
        $voucher       = str_replace(".", "", $request->voucher);
        if (empty($voucher)) {
            $voucher = 0;
        } else {
            $voucher = $voucher;
        }

        // Potongan Istimewa
        $potisaida        = str_replace(".", "", $request->potisaida);
        $potisswan        = str_replace(".", "", $request->potisswan);
        $potisstick       = str_replace(".", "", $request->potisstick);
        if (empty($potisaida)) {
            $potisaida = 0;
        } else {
            $potisaida = $potisaida;
        }
        if (empty($potisswan)) {
            $potisswan = 0;
        } else {
            $potisswan = $potisswan;
        }
        if (empty($potisstick)) {
            $potisstick = 0;
        } else {
            $potisstick = $potisstick;
        }

        //Penyesuaian
        $penyaida        = str_replace(".", "", $request->penyaida);
        $penyswan        = str_replace(".", "", $request->penyswan);
        $penystick       = str_replace(".", "", $request->penystick);
        if (empty($penyaida)) {
            $penyaida = 0;
        } else {
            $penyaida = $penyaida;
        }
        if (empty($penyswan)) {
            $penyswan = 0;
        } else {
            $penyswan = $penyswan;
        }
        if (empty($penystick)) {
            $penystick = 0;
        } else {
            $penystick = $penystick;
        }

        $potongan = $potaida + $potswan + $potstick + $potsp + $potsambal;
        $potistimewa = $potisaida + $potisswan + $potisstick;
        $penyesuaian = $penyaida + $penyswan + $penystick;
        $titipan = str_replace(".", "", $request->titipan);
        $kode_cabang = $request->kode_cabang;
        $tahunini  = date('y');

        //Get No Bukti
        $bayar = DB::table("historibayar")
            ->whereRaw('LEFT(nobukti,6) = "' . $kode_cabang . $tahunini . '-"')
            ->orderBy("nobukti", "desc")
            ->first();
        $lastnobukti = $bayar->nobukti;
        $nobukti  = buatkode($lastnobukti, $kode_cabang . $tahunini . "-", 6);



        $totalpiutang  = $sisapiutang + $subtotal;
        if ($jenistransaksi == "tunai") {
            $total = $subtotal + $voucher;
            $status_lunas = "1";
        } else {
            $status_lunas = "2";
            $total = $subtotal;
        }
        if (empty($jatuhtempo)) {
            $jatuhtempo = date("Y-m-d", strtotime("+14 day", strtotime($tgltransaksi)));
        } else {
            $jatuhtempo = date("Y-m-d", strtotime("+$jatuhtempo day", strtotime($tgltransaksi)));
        }

        if (empty($limitpel) and $jenistransaksi == 'kredit' and ($subtotal - $titipan) > 2000000 or !empty($limitpel) and $totalpiutang >= $limitpel and $jenistransaksi == 'kredit') {
            $status = 1; // Pending
        } else {
            $status = "";
        }

        if ($kode_cabang == 'TSM') {
            $akun = "1-1468";
        } else if ($kode_cabang == 'BDG') {
            $akun = "1-1402";
        } else if ($kode_cabang == 'BGR') {
            $akun = "1-1403";
        } else if ($kode_cabang == 'PWT') {
            $akun = "1-1404";
        } else if ($kode_cabang == 'TGL') {
            $akun = "1-1405";
        } else if ($kode_cabang == "SKB") {
            $akun = "1-1407";
        } else if ($kode_cabang == "GRT") {
            $akun = "1-1468";
        } else if ($kode_cabang == "SMR") {
            $akun = "1-1488";
        } else if ($kode_cabang == "SBY") {
            $akun = "1-1486";
        } else if ($kode_cabang == "PST") {
            $akun = "1-1489";
        } else if ($kode_cabang == "KLT") {
            $akun = "1-1490";
        }

        $tanggal    = explode("-", $tgltransaksi);
        $tahun      = substr($tanggal[0], 2, 2);
        $bulan      = $tanggal[1];

        DB::beginTransaction();
        try {
            DB::table('penjualan')->insert([
                'no_fak_penj' => $no_fak_penj,
                'tgltransaksi' => $tgltransaksi,
                'kode_pelanggan' => $kode_pelanggan,
                'id_karyawan' => $id_karyawan,
                'subtotal' => $bruto,
                'potaida' => $potaida,
                'potswan' => $potswan,
                'potstick' => $potstick,
                'potsp' => $potsp,
                'potsambal' => $potsambal,
                'potongan' => $potongan,
                'potisaida' => $potisaida,
                'potisswan' => $potisswan,
                'potisstick' => $potisstick,
                'potistimewa' => $potistimewa,
                'penyaida' => $penyaida,
                'penyswan' => $penyswan,
                'penystick' => $penystick,
                'penyharga' => $penyesuaian,
                'total' => $total,
                'jenistransaksi' => $jenistransaksi,
                'jenisbayar' => $jenisbayar,
                'jatuhtempo' => $jatuhtempo,
                'id_admin' => $id_admin,
                'status' => $status,
                'status_lunas' => $status_lunas
            ]);

            $tmp = DB::table('detailpenjualan_temp')->where('id_admin', $id_admin)->get();
            foreach ($tmp as $d) {
                DB::table('detailpenjualan')->insert([
                    'no_fak_penj' => $no_fak_penj,
                    'kode_barang' => $d->kode_barang,
                    'harga_dus' => $d->harga_dus,
                    'harga_pack' => $d->harga_pack,
                    'harga_pcs' => $d->harga_pcs,
                    'jumlah' => $d->jumlah,
                    'subtotal' => $d->subtotal,
                    'promo' => $d->promo,
                    'id_admin' => $id_admin
                ]);
            }

            DB::table('detailpenjualan_temp')->where('id_admin', $id_admin)->delete();
            if ($jenistransaksi == "tunai") {
                $bukubesar = DB::table("buku_besar")
                    ->whereRaw('LEFT(no_bukti,6) = "GJ' . $bulan . $tahun . '"')
                    ->orderBy("no_bukti", "desc")
                    ->first();
                $lastno_bukti = $bukubesar->no_bukti;
                $no_bukti_bukubesar  = buatkode($lastno_bukti, 'GJ' . $bulan . $tahun, 4);


                DB::table('historibayar')
                    ->insert([
                        'nobukti' => $nobukti,
                        'no_fak_penj' => $no_fak_penj,
                        'tglbayar' => $tgltransaksi,
                        'jenistransaksi' => $jenistransaksi,
                        'jenisbayar' => $jenisbayar,
                        'bayar' => $subtotal,
                        'id_admin' => $id_admin,
                        'id_karyawan' => $id_karyawan
                    ]);

                DB::table('buku_besar')
                    ->insert([
                        'no_bukti' => $no_bukti_bukubesar,
                        'tanggal' => $tgltransaksi,
                        'sumber' => 'Kas Besar',
                        'keterangan' => "Pembayaran Piutang Pelanggan " . $nama_pelanggan,
                        'kode_akun' => $akun,
                        'debet' => $subtotal,
                        'kredit' => 0,
                        'nobukti_transaksi' => $nobukti,
                        'no_ref' => $nobukti
                    ]);
                if (!empty($voucher)) {
                    $nobukti = buatkode($nobukti, $kode_cabang . $tahunini . "-", 6);
                    DB::table('historibayar')
                        ->insert([
                            'nobukti' => $nobukti,
                            'no_fak_penj' => $no_fak_penj,
                            'tglbayar' => $tgltransaksi,
                            'jenistransaksi' => $jenistransaksi,
                            'jenisbayar' => $jenisbayar,
                            'bayar' => $voucher,
                            'id_admin' => $id_admin,
                            'ket_voucher' => 2,
                            'status_bayar' => 'voucher',
                            'id_karyawan' => $id_karyawan
                        ]);
                }
            } else {
                if (!empty($titipan)) {
                    $bukubesar = DB::table("buku_besar")
                        ->whereRaw('LEFT(no_bukti,6) = "GJ' . $bulan . $tahun . '"')
                        ->orderBy("no_bukti", "desc")
                        ->first();
                    if ($bukubesar == null) {
                        $lastno_bukti = "GJ" . $bulan . $tahun . "0000";
                    } else {
                        $lastno_bukti = $bukubesar->no_bukti;
                    }

                    $no_bukti_bukubesar  = buatkode($lastno_bukti, 'GJ' . $bulan . $tahun, 4);
                    DB::table('historibayar')
                        ->insert([
                            'nobukti' => $nobukti,
                            'no_fak_penj' => $no_fak_penj,
                            'tglbayar' => $tgltransaksi,
                            'jenistransaksi' => $jenistransaksi,
                            'jenisbayar' => $jenisbayar,
                            'bayar' => $titipan,
                            'id_admin' => $id_admin,
                            'id_karyawan' => $id_karyawan
                        ]);

                    DB::table('buku_besar')
                        ->insert([
                            'no_bukti' => $no_bukti_bukubesar,
                            'tanggal' => $tgltransaksi,
                            'sumber' => 'Kas Besar',
                            'keterangan' => "Pembayaran Piutang Pelanggan " . $nama_pelanggan,
                            'kode_akun' => $akun,
                            'debet' => $titipan,
                            'kredit' => 0,
                            'nobukti_transaksi' => $nobukti,
                            'no_ref' => $nobukti
                        ]);
                }
            }
            DB::commit();
            return redirect('/penjualan/create')->with(['success' => 'Data Penjualan Berhasil di Simpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('/penjualan/create')->with(['warning' => 'Data Penjualan Gagal di Simpan']);
        }
    }

    public function delete($no_fak_penj)
    {
        $no_fak_penj = Crypt::decrypt($no_fak_penj);
        $hb = DB::table('historibayar')->where('no_fak_penj', $no_fak_penj)->get();
        $giro = DB::table('giro')
            ->select('ledger_bank.no_bukti')
            ->leftJoin('ledger_bank', 'giro.no_giro', '=', 'ledger_bank.no_ref')
            ->where('giro.no_fak_penj', $no_fak_penj)
            ->get();
        DB::beginTransaction();
        try {
            DB::table('penjualan')
                ->where('no_fak_penj', $no_fak_penj)
                ->delete();


            $no_ref[] = "";
            foreach ($hb as $d) {
                $no_ref[] = $d->nobukti;
            }

            $no_bukti[] = "";
            foreach ($giro as $d) {
                $no_bukti[] = $d->no_bukti;
            }


            DB::table('buku_besar')
                ->whereIn('no_ref', $no_ref)
                ->delete();

            DB::table('buku_besar')
                ->whereIn('no_ref', $no_bukti)
                ->delete();

            // DB::table('buku_besar')
            //     ->leftJoin('historibayar', 'buku_besar.no_ref', '=', 'historibayar.nobukti')
            //     ->where('no_fak_penj', $no_fak_penj)
            //     ->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }
    public function cetakfaktur($no_fak_penj)
    {
        $no_fak_penj = Crypt::decrypt($no_fak_penj);
        $faktur = DB::table('penjualan')
            ->select(
                'penjualan.*',
                'nama_pelanggan',
                'alamat_pelanggan',
                'alamat_toko',
                'nama_cabang',
                'alamat_cabang',
                DB::raw('IFNULL(totalpf,0) - IFNULL(totalgb,0) as totalretur'),
                DB::raw('IFNULL(total,0) - IFNULL(totalpf,0) - IFNULL(totalgb,0) as total')
            )
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin(
                DB::raw("(
                    SELECT
                    no_fak_penj,
                    SUM(subtotal_gb) AS totalgb,
                    SUM(subtotal_pf) AS totalpf
                FROM
                    retur
                GROUP BY
                    no_fak_penj
                ) retur"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                }
            )
            ->where('penjualan.no_fak_penj', $no_fak_penj)
            ->first();
        $detail = DB::table('detailpenjualan')
            ->select('detailpenjualan.*', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs')
            ->join('barang', 'detailpenjualan.kode_barang', '=', 'barang.kode_barang')
            ->where('no_fak_penj', $no_fak_penj)
            ->get();
        return view('penjualan.laporan.cetakfaktur', compact('faktur', 'detail'));
    }

    public function cetaksuratjalan($no_fak_penj, $type)
    {
        $no_fak_penj = Crypt::decrypt($no_fak_penj);
        $faktur = DB::table('penjualan')
            ->select(
                'penjualan.*',
                'nama_pelanggan',
                'alamat_pelanggan',
                'alamat_toko',
                'nama_cabang',
                'alamat_cabang',
                DB::raw('IFNULL(totalpf,0) - IFNULL(totalgb,0) as totalretur'),
                DB::raw('IFNULL(total,0) - IFNULL(totalpf,0) - IFNULL(totalgb,0) as total')
            )
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin(
                DB::raw("(
                    SELECT
                    no_fak_penj,
                    SUM(subtotal_gb) AS totalgb,
                    SUM(subtotal_pf) AS totalpf
                FROM
                    retur
                GROUP BY
                    no_fak_penj
                ) retur"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                }
            )
            ->where('penjualan.no_fak_penj', $no_fak_penj)
            ->first();
        $detail = DB::table('detailpenjualan')
            ->select('detailpenjualan.*', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs')
            ->join('barang', 'detailpenjualan.kode_barang', '=', 'barang.kode_barang')
            ->where('no_fak_penj', $no_fak_penj)
            ->get();
        if ($type == 1) {
            return view('penjualan.laporan.cetaksuratjalan', compact('faktur', 'detail'));
        } else if ($type == 2) {
            return view('penjualan.laporan.cetaksuratjalan2', compact('faktur', 'detail'));
        }
    }

    public function update(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $no_fak_penj_new = $request->no_fak_penj_new;
        $tgltransaksi = $request->tgltransaksi;
        $id_karyawan = $request->id_karyawan;
        $kode_pelanggan = $request->kode_pelanggan;
        $nama_pelanggan = $request->nama_pelanggan;
        $limitpel = $request->limitpel;
        $sisapiutang = $request->sisapiutang;
        $jenistransaksi = $request->jenistransaksi;
        $jenisbayar = $request->jenisbayar;
        $subtotal = $request->subtotal;
        $jatuhtempo = $request->jatuhtempo;
        $bruto = $request->bruto;
        $id_admin = Auth::user()->id;
        $potaida        = str_replace(".", "", $request->potaida);
        if (empty($potaida)) {
            $potaida = 0;
        } else {
            $potaida = $potaida;
        }
        $potswan        = str_replace(".", "", $request->potswan);
        if (empty($potswan)) {
            $potswan = 0;
        } else {
            $potswan = $potswan;
        }
        $potstick       = str_replace(".", "", $request->potstick);
        if (empty($potstick)) {
            $potstick = 0;
        } else {
            $potstick = $potstick;
        }
        $potsp       = str_replace(".", "", $request->potsp);
        if (empty($potsp)) {
            $potsp = 0;
        } else {
            $potsp = $potsp;
        }
        $potsb       = str_replace(".", "", $request->potsb);
        if (empty($potsb)) {
            $potsambal = 0;
        } else {
            $potsambal = $potsb;
        }

        // Voucher
        $voucher       = str_replace(".", "", $request->voucher);
        if (empty($voucher)) {
            $voucher = 0;
        } else {
            $voucher = $voucher;
        }

        // Potongan Istimewa
        $potisaida        = str_replace(".", "", $request->potisaida);
        $potisswan        = str_replace(".", "", $request->potisswan);
        $potisstick       = str_replace(".", "", $request->potisstick);
        if (empty($potisaida)) {
            $potisaida = 0;
        } else {
            $potisaida = $potisaida;
        }
        if (empty($potisswan)) {
            $potisswan = 0;
        } else {
            $potisswan = $potisswan;
        }
        if (empty($potisstick)) {
            $potisstick = 0;
        } else {
            $potisstick = $potisstick;
        }

        //Penyesuaian
        $penyaida        = str_replace(".", "", $request->penyaida);
        $penyswan        = str_replace(".", "", $request->penyswan);
        $penystick       = str_replace(".", "", $request->penystick);
        if (empty($penyaida)) {
            $penyaida = 0;
        } else {
            $penyaida = $penyaida;
        }
        if (empty($penyswan)) {
            $penyswan = 0;
        } else {
            $penyswan = $penyswan;
        }
        if (empty($penystick)) {
            $penystick = 0;
        } else {
            $penystick = $penystick;
        }

        $potongan = $potaida + $potswan + $potstick + $potsp + $potsambal;
        $potistimewa = $potisaida + $potisswan + $potisstick;
        $penyesuaian = $penyaida + $penyswan + $penystick;
        $titipan = str_replace(".", "", $request->titipan);
        $kode_cabang = $request->kode_cabang;
        $tahunini  = date('y');

        //Get No Bukti
        $bayar = DB::table("historibayar")
            ->whereRaw('LEFT(nobukti,6) = "' . $kode_cabang . $tahunini . '-"')
            ->orderBy("nobukti", "desc")
            ->first();
        $lastnobukti = $bayar->nobukti;
        $nobukti  = buatkode($lastnobukti, $kode_cabang . $tahunini . "-", 6);



        $totalpiutang  = $sisapiutang + $subtotal;
        if ($jenistransaksi == "tunai") {
            $total = $subtotal + $voucher;
            $status_lunas = "1";
        } else {
            $status_lunas = "2";
            $total = $subtotal;
        }
        if (empty($jatuhtempo)) {
            $jatuhtempo = date("Y-m-d", strtotime("+14 day", strtotime($tgltransaksi)));
        } else {
            $jatuhtempo = date("Y-m-d", strtotime("+$jatuhtempo day", strtotime($tgltransaksi)));
        }

        if (empty($limitpel) and $jenistransaksi == 'kredit' and ($subtotal - $titipan) > 2000000 or !empty($limitpel) and $totalpiutang >= $limitpel and $jenistransaksi == 'kredit') {
            $status = 1; // Pending
        } else {
            $status = "";
        }

        if ($kode_cabang == 'TSM') {
            $akun = "1-1468";
        } else if ($kode_cabang == 'BDG') {
            $akun = "1-1402";
        } else if ($kode_cabang == 'BGR') {
            $akun = "1-1403";
        } else if ($kode_cabang == 'PWT') {
            $akun = "1-1404";
        } else if ($kode_cabang == 'TGL') {
            $akun = "1-1405";
        } else if ($kode_cabang == "SKB") {
            $akun = "1-1407";
        } else if ($kode_cabang == "GRT") {
            $akun = "1-1468";
        } else if ($kode_cabang == "SMR") {
            $akun = "1-1488";
        } else if ($kode_cabang == "SBY") {
            $akun = "1-1486";
        } else if ($kode_cabang == "PST") {
            $akun = "1-1489";
        } else if ($kode_cabang == "KLT") {
            $akun = "1-1490";
        }

        $tanggal    = explode("-", $tgltransaksi);
        $tahun      = substr($tanggal[0], 2, 2);
        $bulan      = $tanggal[1];
        DB::beginTransaction();
        try {
            DB::table('penjualan')
                ->where('no_fak_penj', $no_fak_penj)
                ->update([
                    'no_fak_penj' => $no_fak_penj_new,
                    'tgltransaksi' => $tgltransaksi,
                    'kode_pelanggan' => $kode_pelanggan,
                    'id_karyawan' => $id_karyawan,
                    'subtotal' => $bruto,
                    'potaida' => $potaida,
                    'potswan' => $potswan,
                    'potstick' => $potstick,
                    'potsp' => $potsp,
                    'potsambal' => $potsambal,
                    'potongan' => $potongan,
                    'potisaida' => $potisaida,
                    'potisswan' => $potisswan,
                    'potisstick' => $potisstick,
                    'potistimewa' => $potistimewa,
                    'penyaida' => $penyaida,
                    'penyswan' => $penyswan,
                    'penystick' => $penystick,
                    'penyharga' => $penyesuaian,
                    'total' => $total,
                    'jenistransaksi' => $jenistransaksi,
                    'jenisbayar' => $jenisbayar,
                    'jatuhtempo' => $jatuhtempo,
                    'id_admin' => $id_admin,
                    'status' => $status,
                    'status_lunas' => $status_lunas
                ]);
            DB::table('detailpenjualan')->where('no_fak_penj', $no_fak_penj)->delete();
            $edit = DB::table('detailpenjualan_edit')->where('no_fak_penj', $no_fak_penj)->get();
            foreach ($edit as $d) {
                DB::table('detailpenjualan')->insert([
                    'no_fak_penj' => $no_fak_penj_new,
                    'kode_barang' => $d->kode_barang,
                    'harga_dus' => $d->harga_dus,
                    'harga_pack' => $d->harga_pack,
                    'harga_pcs' => $d->harga_pcs,
                    'jumlah' => $d->jumlah,
                    'subtotal' => $d->subtotal,
                    'promo' => $d->promo,
                    'id_admin' => $id_admin
                ]);
            }
            DB::table('detailpenjualan_edit')->where('no_fak_penj', $no_fak_penj)->delete();
            if ($jenistransaksi == "tunai") {
                $hb = DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->get();
                $no_ref[] = "";
                foreach ($hb as $d) {
                    $no_ref[] = $d->nobukti;
                }

                DB::table('buku_besar')
                    ->whereIn('no_ref', $no_ref)
                    ->delete();


                $bukubesar = DB::table("buku_besar")
                    ->whereRaw('LEFT(no_bukti,6) = "GJ' . $bulan . $tahun . '"')
                    ->orderBy("no_bukti", "desc")
                    ->first();
                if ($bukubesar == null) {
                    $lastno_bukti = 'GJ' . $bulan . $tahun . '0000';
                } else {
                    $lastno_bukti = $bukubesar->no_bukti;
                }
                $no_bukti_bukubesar  = buatkode($lastno_bukti, 'GJ' . $bulan . $tahun, 4);


                DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->delete();
                DB::table('historibayar')
                    ->insert([
                        'nobukti' => $nobukti,
                        'no_fak_penj' => $no_fak_penj_new,
                        'tglbayar' => $tgltransaksi,
                        'jenistransaksi' => $jenistransaksi,
                        'jenisbayar' => $jenisbayar,
                        'bayar' => $subtotal,
                        'id_admin' => $id_admin,
                        'id_karyawan' => $id_karyawan
                    ]);

                DB::table('buku_besar')
                    ->insert([
                        'no_bukti' => $no_bukti_bukubesar,
                        'tanggal' => $tgltransaksi,
                        'sumber' => 'Kas Besar',
                        'keterangan' => "Pembayaran Piutang Pelanggan " . $nama_pelanggan,
                        'kode_akun' => $akun,
                        'debet' => $subtotal,
                        'kredit' => 0,
                        'nobukti_transaksi' => $nobukti,
                        'no_ref' => $nobukti
                    ]);
                if (!empty($voucher)) {
                    $nobukti = buatkode($nobukti, $kode_cabang . $tahunini . "-", 6);
                    DB::table('historibayar')
                        ->insert([
                            'nobukti' => $nobukti,
                            'no_fak_penj' => $no_fak_penj_new,
                            'tglbayar' => $tgltransaksi,
                            'jenistransaksi' => $jenistransaksi,
                            'jenisbayar' => $jenisbayar,
                            'bayar' => $voucher,
                            'id_admin' => $id_admin,
                            'ket_voucher' => 2,
                            'status_bayar' => 'voucher',
                            'id_karyawan' => $id_karyawan
                        ]);
                }
            } else {
                $hbtunai = DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->where('jenisbayar', 'tunai')->get();
                $hbtitipan = DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->where('jenisbayar', 'titipan')->where('tglbayar', $tgltransaksi)->first();

                if ($hbtunai != null) {
                    $no_ref_tunai[] = "";
                    foreach ($hbtunai as $d) {
                        $no_ref_tunai[] = $d->nobukti;
                    }

                    DB::table('buku_besar')
                        ->whereIn('no_ref', $no_ref_tunai)
                        ->delete();
                }

                if ($hbtitipan != null) {
                    DB::table('buku_besar')
                        ->where('no_ref', $hbtitipan->nobukti)->delete();
                }





                DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->where('jenisbayar', 'tunai')->delete();
                DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->where('jenisbayar', 'titipan')->where('tglbayar', $tgltransaksi)->delete();
                if (!empty($titipan)) {
                    $bukubesar = DB::table("buku_besar")
                        ->whereRaw('LEFT(no_bukti,6) = "GJ' . $bulan . $tahun . '"')
                        ->orderBy("no_bukti", "desc")
                        ->first();
                    if ($bukubesar == null) {
                        $lastno_bukti = "GJ" . $bulan . $tahun . "0000";
                    } else {
                        $lastno_bukti = $bukubesar->no_bukti;
                    }

                    $no_bukti_bukubesar  = buatkode($lastno_bukti, 'GJ' . $bulan . $tahun, 4);

                    DB::table('historibayar')
                        ->insert([
                            'nobukti' => $nobukti,
                            'no_fak_penj' => $no_fak_penj_new,
                            'tglbayar' => $tgltransaksi,
                            'jenistransaksi' => $jenistransaksi,
                            'jenisbayar' => $jenisbayar,
                            'bayar' => $titipan,
                            'id_admin' => $id_admin,
                            'id_karyawan' => $id_karyawan
                        ]);

                    DB::table('buku_besar')
                        ->insert([
                            'no_bukti' => $no_bukti_bukubesar,
                            'tanggal' => $tgltransaksi,
                            'sumber' => 'Kas Besar',
                            'keterangan' => "Pembayaran Piutang Pelanggan " . $nama_pelanggan,
                            'kode_akun' => $akun,
                            'debet' => $titipan,
                            'kredit' => 0,
                            'nobukti_transaksi' => $nobukti,
                            'no_ref' => $nobukti
                        ]);
                }
            }
            DB::commit();
            return redirect('/penjualan?no_fak_penj=' . $no_fak_penj_new)->with(['success' => 'Data Penjualan Berhasil di Update']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('/penjualan?no_fak_penj=' . $no_fak_penj_new)->with(['warning' => 'Data Penjualan Gagal di Update']);
        }
    }

    public function show($no_fak_penj)
    {
        $no_fak_penj = Crypt::decrypt($no_fak_penj);
        $data = DB::table('penjualan')
            ->select(
                'penjualan.*',
                'nama_pelanggan',
                'alamat_pelanggan',
                'alamat_toko',
                'pelanggan.no_hp',
                'latitude',
                'longitude',
                'limitpel',
                'foto',
                'nik',
                'no_kk',
                'tgl_lahir',
                'pasar',
                'hari',
                'cara_pembayaran',
                'status_outlet',
                'type_outlet',
                'lama_usaha',
                'jaminan',
                'lama_langganan',
                'omset_toko',
                'karyawan.nama_karyawan',
                'karyawan.kategori_salesman',
                'karyawan.kode_cabang',
                'nama_cabang',
                DB::raw('IFNULL(totalpf,0) - IFNULL(totalgb,0) as totalretur'),
                'jmlbayar'
            )
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin(
                DB::raw("(
                    SELECT
                    no_fak_penj,
                    SUM(subtotal_gb) AS totalgb,
                    SUM(subtotal_pf) AS totalpf
                FROM
                    retur
                WHERE no_fak_penj = '$no_fak_penj'
                GROUP BY
                    no_fak_penj
                ) retur"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT no_fak_penj, IFNULL(SUM(bayar),0) as jmlbayar
                FROM historibayar
                WHERE no_fak_penj = '$no_fak_penj'
                GROUP BY no_fak_penj
            ) historibayar"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'historibayar.no_fak_penj');
                }
            )
            ->where('penjualan.no_fak_penj', $no_fak_penj)
            ->first();

        $detailpenjualan = DB::table('detailpenjualan')
            ->join('barang', 'detailpenjualan.kode_barang', '=', 'barang.kode_barang')
            ->where('no_fak_penj', $no_fak_penj)
            ->get();

        $historibayar = DB::table('historibayar')
            ->join('karyawan', 'historibayar.id_karyawan', '=', 'karyawan.id_karyawan')
            ->where('no_fak_penj', $no_fak_penj)
            ->orderBy('tglbayar', 'asc')
            ->get();

        $retur = DB::table('detailretur')
            ->select('detailretur.*', 'tglretur', 'jenis_retur', 'kode_produk', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs')
            ->join('retur', 'detailretur.no_retur_penj', '=', 'retur.no_retur_penj')
            ->join('barang', 'detailretur.kode_barang', '=', 'barang.kode_barang')
            ->where('retur.no_fak_penj', $no_fak_penj)
            ->orderBy('retur.no_retur_penj')
            ->get();
        $salesman = DB::table('karyawan')->where('kode_cabang', $data->kode_cabang)->where('status_aktif_sales', 1)->get();
        $girotolak = DB::table('giro')
            ->select('giro.id_giro', 'no_giro')
            ->leftJoin(
                DB::raw("(
                SELECT id_giro,girotocash FROM historibayar WHERE no_fak_penj ='$no_fak_penj'
            ) hb"),
                function ($join) {
                    $join->on('giro.id_giro', '=', 'hb.id_giro');
                }
            )
            ->where('giro.status', 2)
            ->where('giro.no_fak_penj', $no_fak_penj)
            ->get();

        $giro = DB::table('giro')
            ->select('giro.*', 'nama_karyawan', 'tglbayar')
            ->leftJoin('karyawan', 'giro.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin(
                DB::raw("(
                SELECT id_giro,tglbayar
                FROM historibayar
                WHERE no_fak_penj = '$no_fak_penj'
            ) historibayar"),
                function ($join) {
                    $join->on('giro.id_giro', '=', 'historibayar.id_giro');
                }
            )
            ->where('giro.no_fak_penj', $no_fak_penj)
            ->get();

        $transfer = DB::table('transfer')
            ->select('transfer.*', 'nama_karyawan', 'tglbayar')
            ->leftJoin('karyawan', 'transfer.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin(
                DB::raw("(
                SELECT id_transfer,tglbayar
                FROM historibayar
                WHERE no_fak_penj = '$no_fak_penj'
            ) historibayar"),
                function ($join) {
                    $join->on('transfer.id_transfer', '=', 'historibayar.id_transfer');
                }
            )
            ->where('transfer.no_fak_penj', $no_fak_penj)
            ->get();
        return view('penjualan.show', compact('data', 'detailpenjualan', 'retur', 'historibayar', 'salesman', 'girotolak', 'giro', 'transfer'));
    }
    public function rekapcashin(Request $request)
    {
        $dari = $request->tahun . "-" . $request->bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $rekap = DB::table('cabang')
            ->select('cabang.kode_cabang', 'nama_cabang', DB::raw('ifnull(total,0) - ifnull(totalretur,0) as netto'), 'totalbayar')
            ->leftJoin(
                DB::raw("(
                    SELECT karyawan.kode_cabang, SUM(retur.total )AS totalretur
                    FROM retur
                    INNER JOIN penjualan ON retur.no_fak_penj = penjualan.no_fak_penj
                    INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                    WHERE tglretur BETWEEN '$dari'  AND '$sampai'
                    GROUP BY karyawan.kode_cabang
                ) retur"),
                function ($join) {
                    $join->on('cabang.kode_cabang', '=', 'retur.kode_cabang');
                }
            )
            ->leftJoin(
                DB::raw("(
                    SELECT karyawan.kode_cabang, SUM(bayar )AS totalbayar
                    FROM historibayar
                    INNER JOIN penjualan ON historibayar.no_fak_penj = penjualan.no_fak_penj
                    INNER JOIN karyawan ON historibayar.id_karyawan = karyawan.id_karyawan
                    WHERE tglbayar BETWEEN '$dari'  AND '$sampai' AND status_bayar IS NULL
                    GROUP BY karyawan.kode_cabang
                ) historibayar"),
                function ($join) {
                    $join->on('cabang.kode_cabang', '=', 'historibayar.kode_cabang');
                }
            )
            ->leftJoin(
                DB::raw("(
                    SELECT karyawan.kode_cabang,SUM(total) as total
                    FROM penjualan
                    INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                    WHERE tgltransaksi BETWEEN '$dari'  AND '$sampai'
                    GROUP BY karyawan.kode_cabang
                ) penjualan"),
                function ($join) {
                    $join->on('cabang.kode_cabang', '=', 'penjualan.kode_cabang');
                }
            )
            ->get();
        return view('penjualan.dashboard.rekapcashin', compact('rekap'));
    }

    public function aupdashboardall(Request $request)
    {
        $tanggal_aup = $request->tanggal_aup;
        $query = Penjualan::query();
        if ($request->exclude == "yes") {
            $query->where('cabangbarunew', '!=', 'PST');
        }
        $query->select(
            'cabangbarunew as kode_cabang',
            DB::raw("
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 15,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as duaminggu,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 31 AND datediff( '$tanggal_aup', tgltransaksi ) > 15,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as satubulan,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 46 AND datediff( '$tanggal_aup', tgltransaksi ) > 31,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as satubulan15,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 60 AND datediff( '$tanggal_aup', tgltransaksi ) > 46,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as duabulan,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 90 AND datediff( '$tanggal_aup', tgltransaksi ) > 60,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as tigabulan,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 180 AND datediff( '$tanggal_aup', tgltransaksi ) > 90,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as enambulan,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) > 180,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as lebihenambulan
            ")
        );
        $query->leftJoin(
            DB::raw("(
                SELECT
                    pj.no_fak_penj,
                    IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,
                    karyawan.nama_karyawan AS nama_sales,
                    IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                FROM
                    penjualan pj
                INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                LEFT JOIN (
                SELECT
                    MAX( id_move ) AS id_move,
                    no_fak_penj,
                    move_faktur.id_karyawan AS salesbaru,
                    karyawan.kode_cabang AS cabangbaru
                FROM
                    move_faktur
                    INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                WHERE
                    tgl_move <= '$tanggal_aup'
                GROUP BY
                    no_fak_penj,
                    move_faktur.id_karyawan,
                    karyawan.kode_cabang
                ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj)
            ) pjmove"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'pjmove.no_fak_penj');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT no_fak_penj, sum( historibayar.bayar ) AS jmlbayar
		        FROM historibayar WHERE tglbayar <= '$tanggal_aup' GROUP BY no_fak_penj
            ) hblalu"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'hblalu.no_fak_penj');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT retur.no_fak_penj AS no_fak_penj, SUM( total ) AS total
		        FROM retur WHERE tglretur <= '$tanggal_aup' GROUP BY retur.no_fak_penj
            ) retur"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
            }
        );
        $query->where('tgltransaksi', '<=', $tanggal_aup);
        $query->whereRaw('(ifnull( penjualan.total, 0 ) - (ifnull( retur.total, 0 ))) != IFNULL( jmlbayar, 0 )');
        $query->groupBy('cabangbarunew');
        $aup = $query->get();
        return view('penjualan.dashboard.aupall', compact('aup'));
    }

    public function aupdashboardcabang(Request $request)
    {
        $tanggal_aup = $request->tanggal_aup;
        $query = Penjualan::query();
        $query->select(
            'salesbarunew as id_sales',
            'nama_sales',
            DB::raw("
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 15,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as duaminggu,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 31 AND datediff( '$tanggal_aup', tgltransaksi ) > 15,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as satubulan,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 46 AND datediff( '$tanggal_aup', tgltransaksi ) > 31,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as satubulan15,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 60 AND datediff( '$tanggal_aup', tgltransaksi ) > 46,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as duabulan,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 90 AND datediff( '$tanggal_aup', tgltransaksi ) > 60,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as tigabulan,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 180 AND datediff( '$tanggal_aup', tgltransaksi ) > 90,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as enambulan,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) > 180,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as lebihenambulan
            ")
        );
        $query->leftJoin(
            DB::raw("(
                SELECT
                    pj.no_fak_penj,
                    IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,
                    karyawan.nama_karyawan AS nama_sales,
                    IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                FROM
                    penjualan pj
                INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                LEFT JOIN (
                SELECT
                    MAX( id_move ) AS id_move,
                    no_fak_penj,
                    move_faktur.id_karyawan AS salesbaru,
                    karyawan.kode_cabang AS cabangbaru
                FROM
                    move_faktur
                    INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                WHERE
                    tgl_move <= '$tanggal_aup'
                GROUP BY
                    no_fak_penj,
                    move_faktur.id_karyawan,
                    karyawan.kode_cabang
                ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj)
            ) pjmove"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'pjmove.no_fak_penj');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT no_fak_penj, sum( historibayar.bayar ) AS jmlbayar
		        FROM historibayar WHERE tglbayar <= '$tanggal_aup' GROUP BY no_fak_penj
            ) hblalu"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'hblalu.no_fak_penj');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT retur.no_fak_penj AS no_fak_penj, SUM( total ) AS total
		        FROM retur WHERE tglretur <= '$tanggal_aup' GROUP BY retur.no_fak_penj
            ) retur"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
            }
        );
        $query->where('tgltransaksi', '<=', $tanggal_aup);
        $query->where('cabangbarunew', $request->cabang);
        $query->whereRaw('(ifnull( penjualan.total, 0 ) - (ifnull( retur.total, 0 ))) != IFNULL( jmlbayar, 0 )');
        $query->groupBy('salesbarunew');
        $aup = $query->get();
        return view('penjualan.dashboard.aupcabang', compact('aup'));
    }

    function dpppdashboard(Request $request)
    {
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $cabang = $request->cabang;
        $tahunini = $tahun;
        $tahunlalu = $tahun - 1;

        $tgllalu1 = $tahunlalu . "-" . $bulan . "-01";
        $tgllalu2 = date('Y-m-t', strtotime($tgllalu1));

        $tglini1 = $tahunini . "-" . $bulan . "-01";
        $tglini2 = date('Y-m-t', strtotime($tglini1));

        $tglawaltahunlalu = $tahunlalu . "-01-01";
        $tglawaltahunini = $tahunini . "-01-01";
        if (!empty($cabang)) {
            $cbg = "AND karyawan.kode_cabang = '$cabang'";
        } else {
            $cbg = "";
        }
        $query = Barang::query();
        $query->select(
            'master_barang.kode_produk',
            'nama_barang',
            'isipcsdus',
            'realisasi_bulanini_tahunlalu',
            'jmltarget',
            'realisasi_bulanini_tahunini',
            'realisasi_sampaibulanini_tahunlalu',
            'jmltarget_sampaibulanini',
            'realisasi_sampaibulanini_tahunini'
        );
        $query->leftJoin(
            DB::raw("(
                SELECT kt.kode_produk,SUM(jumlah_target) as jmltarget
                FROM komisi_target_qty_detail kt
                INNER JOIN komisi_target ON kt.kode_target = komisi_target.kode_target
                INNER JOIN karyawan ON kt.id_karyawan = karyawan.id_karyawan
                WHERE bulan ='$bulan' AND tahun ='$tahunini'" . $cbg . "
                GROUP BY kt.kode_produk
            ) target"),
            function ($join) {
                $join->on('target.kode_produk', '=', 'master_barang.kode_produk');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT kt.kode_produk,SUM(jumlah_target) as jmltarget_sampaibulanini
                FROM komisi_target_qty_detail kt
                INNER JOIN komisi_target ON kt.kode_target = komisi_target.kode_target
                INNER JOIN karyawan ON kt.id_karyawan = karyawan.id_karyawan
                WHERE bulan BETWEEN '1' AND '$bulan' AND tahun ='$tahunini'" . $cbg . "
                GROUP BY kt.kode_produk
            ) target2"),
            function ($join) {
                $join->on('target2.kode_produk', '=', 'master_barang.kode_produk');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT b.kode_produk,SUM(jumlah) as realisasi_bulanini_tahunlalu
                FROM detailpenjualan dp
                INNER JOIN barang b ON dp.kode_barang = b.kode_barang
                INNER JOIN penjualan p ON dp.no_fak_penj = p.no_fak_penj
                INNER JOIN karyawan ON p.id_karyawan = karyawan.id_karyawan
                WHERE tgltransaksi BETWEEN '$tgllalu1' AND '$tgllalu2'" . $cbg . "
                GROUP BY b.kode_produk
            ) dpen"),
            function ($join) {
                $join->on('dpen.kode_produk', '=', 'master_barang.kode_produk');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT b.kode_produk,SUM(jumlah) as realisasi_bulanini_tahunini
                FROM detailpenjualan dp
                INNER JOIN barang b ON dp.kode_barang = b.kode_barang
                INNER JOIN penjualan p ON dp.no_fak_penj = p.no_fak_penj
                INNER JOIN karyawan ON p.id_karyawan = karyawan.id_karyawan
                WHERE tgltransaksi BETWEEN '$tglini1' AND '$tglini2'" . $cbg . "
			GROUP BY b.kode_produk
            ) dpen2"),
            function ($join) {
                $join->on('dpen2.kode_produk', '=', 'master_barang.kode_produk');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT b.kode_produk,SUM(jumlah) as realisasi_sampaibulanini_tahunlalu
                FROM detailpenjualan dp
                INNER JOIN barang b ON dp.kode_barang = b.kode_barang
                INNER JOIN penjualan p ON dp.no_fak_penj = p.no_fak_penj
                INNER JOIN karyawan ON p.id_karyawan = karyawan.id_karyawan
                WHERE tgltransaksi BETWEEN '$tglawaltahunlalu' AND '$tgllalu2'" . $cbg . "
                GROUP BY b.kode_produk
            ) dpen3"),
            function ($join) {
                $join->on('dpen3.kode_produk', '=', 'master_barang.kode_produk');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT b.kode_produk,SUM(jumlah) as realisasi_sampaibulanini_tahunini
                FROM detailpenjualan dp
                INNER JOIN barang b ON dp.kode_barang = b.kode_barang
                INNER JOIN penjualan p ON dp.no_fak_penj = p.no_fak_penj
                INNER JOIN karyawan ON p.id_karyawan = karyawan.id_karyawan
                WHERE tgltransaksi BETWEEN '$tglawaltahunini' AND '$tglini2'" . $cbg . "
                GROUP BY b.kode_produk
            ) dpen4"),
            function ($join) {
                $join->on('dpen4.kode_produk', '=', 'master_barang.kode_produk');
            }
        );

        $dppp = $query->get();

        return view('penjualan.dashboard.dppp', compact('dppp', 'tahunlalu', 'tahunini'));
    }

    public function laporanpenjualan()
    {
        $cabang = DB::table('cabang')->get();
        return view('penjualan.laporan.frm.lap_penjualan', compact('cabang'));
    }

    public function cetaklaporanpenjualan(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $cabang = DB::table('cabang')->where('kode_cabang', $request->kode_cabang)->first();
        $salesman = DB::table('karyawan')->where('id_karyawan', $request->id_karyawan)->first();
        $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $request->kode_pelanggan)->first();
        $jenistransaksi = $request->jenistransaksi;
        $jenislaporan = $request->jenislaporan;
        if (!empty($request->kode_cabang)) {
            if ($jenislaporan == "standar") {
                $query = Penjualan::query();
                $query->selectRaw('penjualan.no_fak_penj AS no_fak_penj,
                penjualan.tgltransaksi AS tgltransaksi,
                penjualan.kode_pelanggan AS kode_pelanggan,
                pelanggan.nama_pelanggan AS nama_pelanggan,
                pelanggan.alamat_pelanggan AS alamat_pelanggan,
                pelanggan.no_hp AS no_hp,
                pelanggan.pasar AS pasar,
                pelanggan.hari AS hari,
                pelanggan.kode_cabang AS kode_cabang,
                penjualan.subtotal AS subtotal,
                penjualan.potongan AS potongan,
                penjualan.potaida as potaida,
                penjualan.potswan as potswan,
                penjualan.potstick as potstick,
                penjualan.potsp as potsp,
                penjualan.potsambal as potsambal,
                penjualan.potistimewa AS potistimewa,
                penjualan.penyharga AS penyharga,
                date_created,
                date_updated,
                ifnull( penjualan.total, 0 ) AS total,
                ifnull( retur.totalgb, 0 ) AS totalgb,
                ifnull( retur.totalpf, 0 ) AS totalpf,
                (ifnull( retur.totalpf, 0 ) - ifnull( retur.totalgb, 0 ) ) AS totalretur,
                (ifnull( penjualan.total, 0 ) - ( ifnull( retur.totalpf, 0 ) - ifnull( retur.totalgb, 0))) AS totalpiutang,
                penjualan.jenistransaksi AS jenistransaksi,
                penjualan.jenisbayar AS jenisbayar,
                penjualan.id_karyawan AS id_karyawan,
                karyawan.nama_karyawan AS nama_karyawan,
                penjualan.jatuhtempo AS jatuhtempo,
                penjualan.status');
                $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
                $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
                $query->leftJoin(
                    DB::raw("(
                SELECT
                    retur.no_fak_penj AS no_fak_penj,
                    sum(retur.subtotal_gb) AS totalgb,
                    sum(retur.subtotal_pf) AS totalpf
                FROM
                    retur
                WHERE
                    tglretur BETWEEN '$dari' AND '$sampai'
                GROUP BY
                retur.no_fak_penj
                ) retur"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                    }
                );

                $query->whereBetween('tgltransaksi', [$dari, $sampai]);
                if ($request->cabang != "") {
                    $query->where('karyawan.kode_cabang', $request->kode_cabang);
                }

                if ($request->id_karyawan != "") {
                    $query->where('penjualan.id_karyawan', $request->id_karyawan);
                }

                if ($request->kode_pelanggan != "") {
                    $query->where('penjualan.kode_pelanggan', $request->kode_pelanggan);
                }

                if ($request->jenistransaksi != "") {
                    $query->where('penjualan.jenistransaksi', $request->jenistransaksi);
                }



                if ($request->status != "") {
                    if ($request->status == "pending") {
                        $query->where('penjualan.status', 1);
                    } else if ($request->status == "disetujui") {
                        $query->where('penjualan.status', '!=', 1);
                    }
                }

                $query->groupByRaw('
                penjualan.no_fak_penj,
                tgltransaksi,
                penjualan.kode_pelanggan,
                pelanggan.nama_pelanggan,
                pelanggan.alamat_pelanggan,
                pelanggan.no_hp,
                pelanggan.pasar,
                pelanggan.hari,
                pelanggan.kode_cabang,
                penjualan.subtotal,
                penjualan.potongan,
                penjualan.potongan,
                penjualan.potaida,
                penjualan.potswan,
                penjualan.potstick,
                penjualan.potsp,
                penjualan.potsambal,
                penjualan.potistimewa,
                penjualan.penyharga,
                penjualan.jenistransaksi,
                penjualan.jenisbayar,
                penjualan.id_karyawan,
                karyawan.nama_karyawan,
                penjualan.jatuhtempo,
                penjualan.total,
                penjualan.status,
                retur.totalgb,
                retur.totalpf,
                date_created,
                date_updated');
                $query->orderBy('tgltransaksi', 'asc');
                $query->orderBy('no_fak_penj', 'asc');
                $penjualan = $query->get();
                if (isset($_POST['export'])) {
                    // Fungsi header dengan mengirimkan raw data excel
                    header("Content-type: application/vnd-ms-excel");
                    // Mendefinisikan nama file ekspor "hasil-export.xls"
                    header("Content-Disposition: attachment; filename=Laporan Penjualan Periode $dari-$sampai.xls");
                }
                return view('penjualan.laporan.cetak_penjualan', compact('penjualan', 'cabang', 'dari', 'sampai', 'salesman', 'pelanggan'));
            } else if ($jenislaporan == "rekapperpelanggan") {
                $query = Penjualan::query();
                $query->selectRaw('penjualan.kode_pelanggan,nama_pelanggan,pasar,hari,penjualan.id_karyawan,nama_karyawan,SUM(subtotal) as totalpenjualan,
                sum(potongan) as totalpotongan,sum(potistimewa) as totalpotonganistimewa,sum(penyharga) as totalpenyharga, sum(total) as totalpenjualannetto,
                SUM(totretur) as totalretur');
                $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
                $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
                $query->leftJoin(
                    DB::raw("(
                        SELECT no_fak_penj,SUM(total) as totretur FROM retur WHERE tglretur
                        BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                    ) retur"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                    }
                );
                $query->whereBetween('tgltransaksi', [$dari, $sampai]);
                if ($request->cabang != "") {
                    $query->where('karyawan.kode_cabang', $request->kode_cabang);
                }

                if ($request->id_karyawan != "") {
                    $query->where('penjualan.id_karyawan', $request->id_karyawan);
                }

                if ($request->kode_pelanggan != "") {
                    $query->where('penjualan.kode_pelanggan', $request->kode_pelanggan);
                }

                if ($request->jenistransaksi != "") {
                    $query->where('penjualan.jenistransaksi', $request->jenistransaksi);
                }



                if ($request->status != "") {
                    if ($request->status == "pending") {
                        $query->where('penjualan.status', 1);
                    } else if ($request->status == "disetujui") {
                        $query->where('penjualan.status', '!=', 1);
                    }
                }
                $query->groupByRaw('
                penjualan.kode_pelanggan,
                nama_pelanggan,
                pasar,
                Hari,
                penjualan.id_karyawan,
                nama_karyawan');
                $penjualan = $query->get();
                if (isset($_POST['export'])) {
                    $time = date("H:i:s");
                    // Fungsi header dengan mengirimkan raw data excel
                    header("Content-type: application/vnd-ms-excel");
                    // Mendefinisikan nama file ekspor "hasil-export.xls"
                    header("Content-Disposition: attachment; filename=Laporan Penjualan Rekap Per Pelanggan Periode $dari-$sampai-$time.xls");
                }
                return view('penjualan.laporan.cetak_penjualan_rekapperpelanggan', compact('penjualan', 'cabang', 'dari', 'sampai', 'salesman', 'pelanggan'));
            } else if ($jenislaporan == "formatsatubaris") {
                $query = Penjualan::query();
                $query->selectRaw('penjualan.no_fak_penj,
                tgltransaksi,
                penjualan.kode_pelanggan,pelanggan.nama_pelanggan,
                penjualan.id_karyawan,karyawan.nama_karyawan,
                pelanggan.pasar,pelanggan.hari,
                AB,AR,`AS`,BB,CG,CGG,DEP,DK,DS,SP,BBP,SPP,CG5,SC,SP8,
                penjualan.subtotal as totalbruto,
                (ifnull( r.totalpf, 0 ) - ifnull( r.totalgb, 0 ) ) AS totalretur,
                penjualan.penyharga AS penyharga,
                penjualan.potaida as potaida,
                penjualan.potswan as potswan,
                penjualan.potstick as potstick,
                penjualan.potsp as potsp,
                penjualan.potongan as potongan,
                penjualan.potistimewa,
                penjualan.subtotal,
                 (ifnull( penjualan.total, 0 ) - ( ifnull( r.totalpf, 0 ) - ifnull( r.totalgb, 0))) as totalnetto,
                totalbayar,
                penjualan.jenistransaksi,
                penjualan.status_lunas,
                lastpayment');
                $query->leftJoin(
                    DB::raw("(
                    SELECT dp.no_fak_penj,
                    SUM(IF(kode_produk = 'AB',jumlah,0)) as AB,
                    SUM(IF(kode_produk = 'AR',jumlah,0)) as AR,
                    SUM(IF(kode_produk = 'AS',jumlah,0)) as `AS`,
                    SUM(IF(kode_produk = 'BB',jumlah,0)) as BB,
                    SUM(IF(kode_produk = 'CG',jumlah,0)) as CG,
                    SUM(IF(kode_produk = 'CGG',jumlah,0)) as CGG,
                    SUM(IF(kode_produk = 'DEP',jumlah,0)) as DEP,
                    SUM(IF(kode_produk = 'DK',jumlah,0)) as DK,
                    SUM(IF(kode_produk = 'DS',jumlah,0)) as DS,
                    SUM(IF(kode_produk = 'SP',jumlah,0)) as SP,
                    SUM(IF(kode_produk = 'BBP',jumlah,0)) as BBP,
                    SUM(IF(kode_produk = 'SPP',jumlah,0)) as SPP,
                    SUM(IF(kode_produk = 'CG5',jumlah,0)) as CG5,
                    SUM(IF(kode_produk = 'SC',jumlah,0)) as SC,
                    SUM(IF(kode_produk = 'SP8',jumlah,0)) as SP8
                    FROM detailpenjualan dp
                    INNER JOIN barang b ON dp.kode_barang = b.kode_barang
                    GROUP BY dp.no_fak_penj
                    ) dp"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'dp.no_fak_penj');
                    }
                );

                $query->leftJoin(
                    DB::raw("(
                    SELECT hb.no_fak_penj,
                    MAX(tglbayar) as lastpayment,
                    SUM(bayar) as totalbayar
                    FROM historibayar hb
                    GROUP BY hb.no_fak_penj
                    ) hb"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'hb.no_fak_penj');
                    }
                );

                $query->leftJoin(
                    DB::raw("(
                        SELECT
                        retur.no_fak_penj AS no_fak_penj,
                        sum(retur.subtotal_gb) AS totalgb,
                        sum(retur.subtotal_pf) AS totalpf
                        FROM
                            retur
                        WHERE
                            tglretur
                        GROUP BY
                            retur.no_fak_penj
                    ) r"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'r.no_fak_penj');
                    }
                );

                $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
                $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
                $query->whereBetween('tgltransaksi', [$dari, $sampai]);
                if ($request->cabang != "") {
                    $query->where('karyawan.kode_cabang', $request->kode_cabang);
                }
                if ($request->id_karyawan != "") {
                    $query->where('penjualan.id_karyawan', $request->id_karyawan);
                }

                if ($request->kode_pelanggan != "") {
                    $query->where('penjualan.kode_pelanggan', $request->kode_pelanggan);
                }

                if ($request->jenistransaksi != "") {
                    $query->where('penjualan.jenistransaksi', $request->jenistransaksi);
                }

                if ($request->status != "") {
                    if ($request->status == "pending") {
                        $query->where('penjualan.status', 1);
                    } else if ($request->status == "disetujui") {
                        $query->where('penjualan.status', '!=', 1);
                    }
                }
                $query->orderBy('tgltransaksi', 'asc');
                $query->orderBy('penjualan.no_fak_penj', 'asc');
                $penjualan = $query->get();

                $barang = Barang::all();

                if (isset($_POST['export'])) {
                    $time = date("H:i:s");
                    // Fungsi header dengan mengirimkan raw data excel
                    header("Content-type: application/vnd-ms-excel");
                    // Mendefinisikan nama file ekspor "hasil-export.xls"
                    header("Content-Disposition: attachment; filename=Laporan Penjualan Format Satu Baris $dari-$sampai-$time.xls");
                }
                return view('penjualan.laporan.cetak_penjualan_formatsatubaris', compact('penjualan', 'cabang', 'dari', 'sampai', 'salesman', 'pelanggan', 'barang'));
            } else if ($jenislaporan == "komisi") {
                $query = Penjualan::query();
                $query->selectRaw('penjualan.no_fak_penj,
                tgltransaksi,
                penjualan.kode_pelanggan,pelanggan.nama_pelanggan,
                penjualan.id_karyawan,karyawan.nama_karyawan,
                pelanggan.pasar,pelanggan.hari,
                AB,AR,`AS`,BB,CG,CGG,DEP,DK,DS,SP,BBP,SPP,CG5,SC,SP8,
                penjualan.subtotal as totalbruto,
                (ifnull( r.totalpf, 0 ) - ifnull( r.totalgb, 0 ) ) AS totalretur,
                penjualan.penyharga AS penyharga,
                penjualan.potaida as potaida,
                penjualan.potswan as potswan,
                penjualan.potstick as potstick,
                penjualan.potsp as potsp,
                penjualan.potongan as potongan,
                penjualan.potistimewa,
                penjualan.subtotal,
                 (ifnull( penjualan.total, 0 ) - ( ifnull( r.totalpf, 0 ) - ifnull( r.totalgb, 0))) as totalnetto,
                totalbayar,
                penjualan.jenistransaksi,
                penjualan.status_lunas,
                lastpayment');
                $query->leftJoin(
                    DB::raw("(
                    SELECT dp.no_fak_penj,
                    SUM(IF(kode_produk = 'AB',jumlah,0)) as AB,
                    SUM(IF(kode_produk = 'AR',jumlah,0)) as AR,
                    SUM(IF(kode_produk = 'AS',jumlah,0)) as `AS`,
                    SUM(IF(kode_produk = 'BB',jumlah,0)) as BB,
                    SUM(IF(kode_produk = 'CG',jumlah,0)) as CG,
                    SUM(IF(kode_produk = 'CGG',jumlah,0)) as CGG,
                    SUM(IF(kode_produk = 'DEP',jumlah,0)) as DEP,
                    SUM(IF(kode_produk = 'DK',jumlah,0)) as DK,
                    SUM(IF(kode_produk = 'DS',jumlah,0)) as DS,
                    SUM(IF(kode_produk = 'SP',jumlah,0)) as SP,
                    SUM(IF(kode_produk = 'BBP',jumlah,0)) as BBP,
                    SUM(IF(kode_produk = 'SPP',jumlah,0)) as SPP,
                    SUM(IF(kode_produk = 'CG5',jumlah,0)) as CG5,
                    SUM(IF(kode_produk = 'SC',jumlah,0)) as SC,
                    SUM(IF(kode_produk = 'SP8',jumlah,0)) as SP8
                    FROM detailpenjualan dp
                    INNER JOIN barang b ON dp.kode_barang = b.kode_barang
                    GROUP BY dp.no_fak_penj
                    ) dp"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'dp.no_fak_penj');
                    }
                );

                $query->leftJoin(
                    DB::raw("(
                    SELECT hb.no_fak_penj,
                    MAX(tglbayar) as lastpayment,
                    SUM(bayar) as totalbayar
                    FROM historibayar hb
                    GROUP BY hb.no_fak_penj
                    ) hb"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'hb.no_fak_penj');
                    }
                );

                $query->leftJoin(
                    DB::raw("(
                        SELECT
                        retur.no_fak_penj AS no_fak_penj,
                        sum(retur.subtotal_gb) AS totalgb,
                        sum(retur.subtotal_pf) AS totalpf
                        FROM
                            retur
                        WHERE
                            tglretur
                        GROUP BY
                            retur.no_fak_penj
                    ) r"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'r.no_fak_penj');
                    }
                );

                $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
                $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');

                if ($request->cabang != "") {
                    $query->where('karyawan.kode_cabang', $request->kode_cabang);
                }
                if ($request->id_karyawan != "") {
                    $query->where('penjualan.id_karyawan', $request->id_karyawan);
                }

                if ($request->kode_pelanggan != "") {
                    $query->where('penjualan.kode_pelanggan', $request->kode_pelanggan);
                }

                if ($request->jenistransaksi != "") {
                    $query->where('penjualan.jenistransaksi', $request->jenistransaksi);
                }

                if ($request->status != "") {
                    if ($request->status == "pending") {
                        $query->where('penjualan.status', 1);
                    } else if ($request->status == "disetujui") {
                        $query->where('penjualan.status', '!=', 1);
                    }
                }

                $query->whereBetween('lastpayment', [$dari, $sampai]);
                $query->where('penjualan.status_lunas', 1);
                $query->orderBy('tgltransaksi', 'asc');
                $query->orderBy('penjualan.no_fak_penj', 'asc');
                $penjualan = $query->get();

                $barang = Barang::all();

                if (isset($_POST['export'])) {
                    $time = date("H:i:s");
                    // Fungsi header dengan mengirimkan raw data excel
                    header("Content-type: application/vnd-ms-excel");
                    // Mendefinisikan nama file ekspor "hasil-export.xls"
                    header("Content-Disposition: attachment; filename=Laporan Penjualan Format Komisi Periode $dari-$sampai-$time.xls");
                }
                return view('penjualan.laporan.cetak_penjualan_formatkomisi', compact('penjualan', 'cabang', 'dari', 'sampai', 'salesman', 'pelanggan', 'barang'));
            }
        } else {
            $query = Penjualan::query();
            $query->selectRaw('karyawan.kode_cabang AS kode_cabang,nama_cabang,
            (ifnull( SUM(penjualan.subtotal), 0 )
            ) AS totalbruto, totalretur,ifnull( SUM(penjualan.penyharga), 0 )  as totalpenyharga,
            ifnull( SUM(penjualan.potongan), 0 )  as totalpotongan,
            ifnull( SUM(penjualan.potistimewa), 0 )  as totalpotistimewa');
            $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
            $query->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');
            $query->leftJoin(
                DB::raw("(
                SELECT karyawan.kode_cabang, SUM(retur.total )AS totalretur FROM retur
                INNER JOIN penjualan ON retur.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                WHERE tglretur BETWEEN '$dari' AND '$sampai' GROUP BY karyawan.kode_cabang
                ) retur"),
                function ($join) {
                    $join->on('karyawan.kode_cabang', '=', 'retur.kode_cabang');
                }
            );

            $query->whereBetween('tgltransaksi', [$dari, $sampai]);
            if ($request->jenistransaksi != "") {
                $query->where('penjualan.jenistransaksi', $request->jenistransaksi);
            }
            if ($request->status != "") {
                if ($request->status == "pending") {
                    $query->where('penjualan.status', 1);
                } else if ($request->status == "disetujui") {
                    $query->where('penjualan.status', '!=', 1);
                }
            }
            $query->groupByRaw(' karyawan.kode_cabang,nama_cabang,totalretur');
            $penjualan = $query->get();
            if (isset($_POST['export'])) {
                $time = date("H:i:s");
                // Fungsi header dengan mengirimkan raw data excel
                header("Content-type: application/vnd-ms-excel");
                // Mendefinisikan nama file ekspor "hasil-export.xls"
                header("Content-Disposition: attachment; filename=Rekap Penjualan All Cabang $dari-$sampai-$time.xls");
            }
            return view('penjualan.laporan.cetak_penjualan_rekapallcabang', compact('penjualan', 'dari', 'sampai'));
        }
    }
}
