<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Harga;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{


    //Create

    public function create()
    {
        return view('penjualan.create');
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

    public function loadtotalpenjualantemp()
    {
        $detail = DB::table('detailpenjualan_temp')
            ->select(DB::raw('SUM(subtotal) AS total'))
            ->where('id_admin', Auth::user()->id)
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

    public function cekpenjtemp()
    {
        $id_user = Auth::user()->id;
        $barang = DB::table('detailpenjualan_temp')
            ->where('id_admin', $id_user)
            ->count();
        echo $barang;
    }

    public function cekpiutangpelanggan(Request $request)
    {
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
        $potsambal       = str_replace(".", "", $request->potsambal);
        if (empty($potsambal)) {
            $potsambal = 0;
        } else {
            $potsambal = $potsambal;
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
                if (!empty($voucher)) {
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
                }
            }
            DB::commit();
            return redirect('/penjualan/create')->with(['success' => 'Data Penjualan Berhasil di Simpan']);
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            return redirect('/penjualan/create')->with(['warning' => 'Data Penjualan Gagal di Simpan']);
        }
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
}
