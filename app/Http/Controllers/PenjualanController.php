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
        $cek = DB::table('detailpenjualan_temp')->where('kode_barang', $request->kode_barang)->where('id_admin', $id_user)->count();
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
            ->select('detailpenjualan_temp.*', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs')
            ->join('barang', 'detailpenjualan_temp.kode_barang', '=', 'barang.kode_barang')
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
            } else {
                $promo = NULL;
            }
        } else {
            if ($detailtemp->promo == 1) {
                $promo = 1;
            } else {
                $promo = NULL;
            }
        }
        $harga_dus = str_replace(".", "", $request->harga_dus);
        $harga_pack = str_replace(".", "", $request->harga_pack);
        $harga_pcs = str_replace(".", "", $request->harga_pcs);
        $totalqty = $jmldus + $jmlpack + $jmlpcs;
        $total = $request->total;

        DB::table('detailpenjualan_temp')
            ->where('kode_barang', $request->kode_barang)
            ->where('id_admin', $id_user)
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
