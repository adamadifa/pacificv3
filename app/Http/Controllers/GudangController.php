<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GudangController extends Controller
{
    public function rekappersediaandashboard()
    {
        $select_saldo = "";
        $select_mutasi = "";
        $select_dpb = "";
        $select_mutasi_gudang = "";
        $select_saldo_gudang = "";
        $select_buffer = "";
        $select_maxstok = "";
        $select_penjualan = "";


        $field_saldo = "";
        $field_mutasi = "";
        $field_dpb = "";
        $field_mutasi_gudang = "";
        $field_buffer = "";
        $field_maxstok = "";
        $field_penjualan = "";

        $dari = date('Y-m') . "-01";
        $sampai = date('Y-m-t', strtotime($dari));


        $barang = DB::table('master_barang')
            ->where('status', 1)
            ->orderBy('kode_produk')
            ->get();
        foreach ($barang as $d) {

            $field_saldo .= "saldo_" . strtolower($d->kode_produk) . ",";
            $field_mutasi .= "mutasi_" . strtolower($d->kode_produk) . ",";
            $field_dpb .= "ambil_" . strtolower($d->kode_produk) . "," . "kembali_" . strtolower($d->kode_produk) . ",";
            $field_mutasi_gudang .= "mg_" . strtolower($d->kode_produk) . ",";
            $field_buffer .= "buffer_" . $d->kode_produk . ",";
            $field_maxstok .= "maxstok_" . $d->kode_produk . ",";
            $field_penjualan .= "sellout_" . $d->kode_produk . ",";

            $select_saldo .= "SUM(IF(kode_produk='$d->kode_produk',jumlah,0)) as saldo_" . strtolower($d->kode_produk) . ",";
            $select_mutasi .= "IFNULL(SUM(IF(inout_good ='IN' AND kode_produk ='$d->kode_produk',jumlah,0)),0) - IFNULL(SUM(IF(inout_good ='OUT' AND kode_produk ='$d->kode_produk',jumlah,0)),0) as mutasi_" . strtolower($d->kode_produk) . ",";
            $select_dpb .= "ROUND(SUM(IF(kode_produk ='$d->kode_produk',jml_pengambilan,0)),2) as ambil_" . strtolower($d->kode_produk) . ",
            ROUND(SUM(IF(kode_produk ='$d->kode_produk',jml_pengembalian,0)),2) as kembali_" . strtolower($d->kode_produk) . ",";
            $select_mutasi_gudang .= "SUM(IF(kode_produk='$d->kode_produk',jumlah,0)) as mg_" . strtolower($d->kode_produk) . ",";
            $select_saldo_gudang .= "SUM(IF(`inout`='IN'  AND detail_mutasi_gudang.kode_produk = '$d->kode_produk',jumlah,0)) -
            SUM(IF(`inout`='OUT' AND detail_mutasi_gudang.kode_produk = '$d->kode_produk',jumlah,0)) as saldo_" . $d->kode_produk . ",";
            $select_buffer .= "SUM(IF(kode_produk='$d->kode_produk',jumlah,0)) as buffer_" . $d->kode_produk . ",";
            $select_maxstok .= "SUM(IF(kode_produk='$d->kode_produk',jumlah,0)) as maxstok_" . $d->kode_produk . ",";
            $select_penjualan .= "SUM(IF(kode_produk='$d->kode_produk',ROUND((jumlah/isipcsdus),3),0)) as `sellout_" . $d->kode_produk . "`,";
        }
        $query = Cabang::query();
        $query->selectRaw(
            "
            $field_saldo
            $field_mutasi
            $field_dpb
            $field_mutasi_gudang
            $field_buffer
            $field_maxstok
            $field_penjualan
            cabang.kode_cabang,
            nama_cabang"
        );
        $query->leftJoin(
            DB::raw("(

            SELECT
            $select_saldo
            kode_cabang
            FROM saldoawal_bj_detail detailsaldo
            INNER JOIN saldoawal_bj saldo ON detailsaldo.kode_saldoawal = saldo.kode_saldoawal
            WHERE status='GS' AND tanggal =
            (SELECT MAX(saldomax.tanggal)
            FROM saldoawal_bj saldomax
            WHERE saldomax.kode_cabang = saldo.kode_cabang)
            GROUP BY kode_cabang
            ) sb2"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'sb2.kode_cabang');
            }
        );
        $query->leftJoin(
            DB::raw("(

            SELECT
            $select_mutasi
            kode_cabang
            FROM detail_mutasi_gudang_cabang dmc
            INNER JOIN mutasi_gudang_cabang mc ON dmc.no_mutasi_gudang_cabang = mc.no_mutasi_gudang_cabang
            WHERE tgl_mutasi_gudang_cabang >= (SELECT MAX(saldomax.tanggal)
            FROM saldoawal_bj saldomax
            WHERE saldomax.kode_cabang = mc.kode_cabang)
            AND tgl_mutasi_gudang_cabang <= CURDATE()
            AND jenis_mutasi='SURAT JALAN'
            OR tgl_mutasi_gudang_cabang >= (SELECT MAX(saldomax.tanggal) FROM saldoawal_bj saldomax
            WHERE saldomax.kode_cabang = mc.kode_cabang)
            AND tgl_mutasi_gudang_cabang <= CURDATE()
            AND jenis_mutasi='TRANSIT IN'
            OR tgl_mutasi_gudang_cabang>= (SELECT MAX(saldomax.tanggal) FROM saldoawal_bj saldomax
            WHERE saldomax.kode_cabang = mc.kode_cabang)
            AND tgl_mutasi_gudang_cabang <= CURDATE()
            AND jenis_mutasi='TRANSIT OUT'
            OR tgl_mutasi_gudang_cabang>= (SELECT MAX(saldomax.tanggal) FROM saldoawal_bj saldomax
            WHERE saldomax.kode_cabang = mc.kode_cabang)
            AND tgl_mutasi_gudang_cabang <= CURDATE()
            AND jenis_mutasi='REJECT GUDANG'
            OR tgl_mutasi_gudang_cabang>= (SELECT MAX(saldomax.tanggal) FROM saldoawal_bj saldomax
            WHERE saldomax.kode_cabang = mc.kode_cabang)
            AND tgl_mutasi_gudang_cabang <= CURDATE()
            AND jenis_mutasi='REJECT PASAR'
            OR tgl_mutasi_gudang_cabang >= (SELECT MAX(saldomax.tanggal) FROM saldoawal_bj saldomax
            WHERE saldomax.kode_cabang = mc.kode_cabang)
            AND tgl_mutasi_gudang_cabang <= CURDATE()
            AND jenis_mutasi='REPACK'
            OR tgl_mutasi_gudang_cabang>= (SELECT MAX(saldomax.tanggal) FROM saldoawal_bj saldomax
            WHERE saldomax.kode_cabang = mc.kode_cabang)
            AND tgl_mutasi_gudang_cabang <= CURDATE()
            AND jenis_mutasi='PENYESUAIAN'
            GROUP BY kode_cabang
            ) mutasi"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'mutasi.kode_cabang');
            }
        );

        $query->leftJoin(
            DB::raw("(
            SELECT
            $select_dpb
            kode_cabang
            FROM detail_dpb
            INNER JOIN dpb ON detail_dpb.no_dpb = dpb.no_dpb
            WHERE tgl_pengambilan >= (SELECT MAX(saldomax.tanggal)
            FROM saldoawal_bj saldomax
            WHERE saldomax.kode_cabang = dpb.kode_cabang) AND tgl_pengambilan <= CURDATE()
            GROUP BY kode_cabang
            ) dpb"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'dpb.kode_cabang');
            }
        );


        $query->leftJoin(
            DB::raw("(
            SELECT
            $select_mutasi_gudang
            kode_cabang
            FROM detail_mutasi_gudang dmg
            INNER JOIN mutasi_gudang_jadi mg ON dmg.no_mutasi_gudang = mg.no_mutasi_gudang
            INNER JOIN permintaan_pengiriman pp ON mg.no_permintaan_pengiriman =
            pp.no_permintaan_pengiriman
            WHERE jenis_mutasi ='SURAT JALAN' AND status_sj='0' AND tgl_mutasi_gudang > '2021-11-01'
            AND tgl_mutasi_gudang < CURDATE() GROUP BY kode_cabang ) mgudang"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'mgudang.kode_cabang');
            }
        );


        $query->leftJoin(
            DB::raw("(
            SELECT
            $select_buffer
            kode_cabang
            FROM detail_bufferstok
            INNER JOIN buffer_stok  ON detail_bufferstok.kode_bufferstok = buffer_stok.kode_bufferstok
            GROUP BY kode_cabang ) bufferstok"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'bufferstok.kode_cabang');
            }
        );

        $query->leftJoin(
            DB::raw("(
            SELECT
            $select_maxstok
            kode_cabang
            FROM limit_stok_detail
            INNER JOIN limit_stok  ON limit_stok_detail.kode_limit_stok = limit_stok.kode_limit_stok
            GROUP BY kode_cabang ) limitstok"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'limitstok.kode_cabang');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT
                    $select_penjualan
                    karyawan.kode_cabang
                FROM
                    detailpenjualan
                    INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                    INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                    INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                    WHERE tgltransaksi BETWEEN '$dari' AND '$sampai'
                GROUP BY kode_cabang
                ) penjualan"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'penjualan.kode_cabang');
            }
        );
        $wilayah = Auth::user()->wilayah;
        if (!empty($wilayah)) {
            $wilayah_user = unserialize($wilayah);
            $query->whereIn('cabang.kode_cabang', $wilayah_user);
        }

        $query->orderBy('cabang.urutan');
        $rekapdpb = $query->get();




        // $barang = Barang::all();
        $sampai = date("Y-m-d");
        $rekapgudang = DB::table('detail_mutasi_gudang')
            ->selectRaw("$select_saldo_gudang
             'Gudang' as lokasi")
            ->leftJoin('mutasi_gudang_jadi', 'detail_mutasi_gudang.no_mutasi_gudang', '=', 'mutasi_gudang_jadi.no_mutasi_gudang')
            ->where('mutasi_gudang_jadi.tgl_mutasi_gudang', '<=', $sampai)
            ->first();



        return view('gudang.dashboard.rekapdpb5', compact('rekapdpb', 'barang', 'rekapgudang'));
    }
}
