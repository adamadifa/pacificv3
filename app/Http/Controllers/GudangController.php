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
        $query = Cabang::query();

        $query->select(
            'cabang.kode_cabang',
            'nama_cabang',
            'saldo_ab',
            'saldo_ar',
            'saldo_as',
            'saldo_bb',
            'saldo_dep',
            'saldo_ds',
            'saldo_sp',
            'saldo_spp',
            'saldo_sc',
            'saldo_sp8',
            'saldo_sp8p',
            'saldo_sp500',
            'saldo_br20',
            'mutasi_ab',
            'mutasi_ar',
            'mutasi_as',
            'mutasi_bb',
            'mutasi_dep',
            'mutasi_ds',
            'mutasi_sp',
            'mutasi_spp',
            'mutasi_sc',
            'mutasi_sp8',
            'mutasi_sp8p',
            'mutasi_sp500',
            'mutasi_br20',
            'ab_ambil',
            'ar_ambil',
            'as_ambil',
            'bb_ambil',
            'dep_ambil',
            'ds_ambil',
            'sp_ambil',
            'spp_ambil',
            'sc_ambil',
            'sp8_ambil',
            'sp8p_ambil',
            'sp500_ambil',
            'br20_ambil',
            'ab_kembali',
            'ar_kembali',
            'as_kembali',
            'bb_kembali',
            'dep_kembali',
            'ds_kembali',
            'sp_kembali',
            'spp_kembali',
            'sc_kembali',
            'sp8_kembali',
            'sp8p_kembali',
            'sp500_kembali',
            'br20_kembali',
            'mg_ab',
            'mg_as',
            'mg_ar',
            'mg_bb',
            'mg_dep',
            'mg_ds',
            'mg_sp',
            'mg_spp',
            'mg_sc',
            'mg_sp8',
            'mg_spp',
            'mg_sp500',
            'mg_br20'
        );
        $query->leftJoin(
            DB::raw("(
                SELECT kode_cabang,
                SUM(IF(kode_produk='AB',jumlah,0)) as saldo_ab,
                SUM(IF(kode_produk='AR',jumlah,0)) as saldo_ar,
                SUM(IF(kode_produk='AS',jumlah,0)) as saldo_as,
                SUM(IF(kode_produk='BB',jumlah,0)) as saldo_bb,
                SUM(IF(kode_produk='DEP',jumlah,0)) as saldo_dep,
                SUM(IF(kode_produk='DS',jumlah,0)) as saldo_ds,
                SUM(IF(kode_produk='SP',jumlah,0)) as saldo_sp,
                SUM(IF(kode_produk='SPP',jumlah,0)) as saldo_spp,
                SUM(IF(kode_produk='SC',jumlah,0)) as saldo_sc,
                SUM(IF(kode_produk='SP8',jumlah,0)) as saldo_sp8,
                SUM(IF(kode_produk='SP8-P',jumlah,0)) as saldo_sp8p,
                SUM(IF(kode_produk='SP500',jumlah,0)) as saldo_sp500,
                SUM(IF(kode_produk='BR20',jumlah,0)) as saldo_br20
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
                SELECT kode_cabang,
			    IFNULL(SUM(IF(inout_good ='IN' AND kode_produk ='AB',jumlah,0)),0) - IFNULL(SUM(IF(inout_good ='OUT' AND kode_produk ='AB',jumlah,0)),0)   as mutasi_ab,
			    IFNULL(SUM(IF(inout_good ='IN' AND kode_produk ='AR',jumlah,0)),0) - IFNULL(SUM(IF(inout_good ='OUT' AND kode_produk ='AR',jumlah,0)),0)   as mutasi_ar,
				IFNULL(SUM(IF(inout_good ='IN' AND kode_produk ='AS',jumlah,0)),0) - IFNULL(SUM(IF(inout_good ='OUT' AND kode_produk ='AS',jumlah,0)),0)   as mutasi_as,
				IFNULL(SUM(IF(inout_good ='IN' AND kode_produk ='BB',jumlah,0)),0) - IFNULL(SUM(IF(inout_good ='OUT' AND kode_produk ='BB',jumlah,0)),0)   as mutasi_bb,
                IFNULL(SUM(IF(inout_good ='IN' AND kode_produk ='DEP',jumlah,0)),0) - IFNULL(SUM(IF(inout_good ='OUT' AND kode_produk ='DEP',jumlah,0)),0)   as mutasi_dep,
                IFNULL(SUM(IF(inout_good ='IN' AND kode_produk ='DS',jumlah,0)),0) - IFNULL(SUM(IF(inout_good ='OUT' AND kode_produk ='DS',jumlah,0)),0)   as mutasi_ds,
                IFNULL(SUM(IF(inout_good ='IN' AND kode_produk ='SP',jumlah,0)),0) - IFNULL(SUM(IF(inout_good ='OUT' AND kode_produk ='SP',jumlah,0)),0)   as mutasi_sp,
                IFNULL(SUM(IF(inout_good ='IN' AND kode_produk ='SPP',jumlah,0)),0) - IFNULL(SUM(IF(inout_good ='OUT' AND kode_produk ='SPP',jumlah,0)),0)   as mutasi_spp,
                IFNULL(SUM(IF(inout_good ='IN' AND kode_produk ='SC',jumlah,0)),0) - IFNULL(SUM(IF(inout_good ='OUT' AND kode_produk ='SC',jumlah,0)),0)   as mutasi_sc,
                IFNULL(SUM(IF(inout_good ='IN' AND kode_produk ='SP8',jumlah,0)),0) - IFNULL(SUM(IF(inout_good ='OUT' AND kode_produk ='SP8',jumlah,0)),0)   as mutasi_sp8,
                IFNULL(SUM(IF(inout_good ='IN' AND kode_produk ='SP8-P',jumlah,0)),0) - IFNULL(SUM(IF(inout_good ='OUT' AND kode_produk ='SP8-P',jumlah,0)),0)   as mutasi_sp8p,
                IFNULL(SUM(IF(inout_good ='IN' AND kode_produk ='SP500',jumlah,0)),0) - IFNULL(SUM(IF(inout_good ='OUT' AND kode_produk ='SP500',jumlah,0)),0)   as mutasi_sp500,

                IFNULL(SUM(IF(inout_good ='IN' AND kode_produk ='BR20',jumlah,0)),0) - IFNULL(SUM(IF(inout_good ='OUT' AND kode_produk ='BR20',jumlah,0)),0)   as mutasi_br20
                FROM detail_mutasi_gudang_cabang dmc
                INNER JOIN mutasi_gudang_cabang mc ON dmc.no_mutasi_gudang_cabang = mc.no_mutasi_gudang_cabang
                WHERE

                tgl_mutasi_gudang_cabang >= (SELECT MAX(saldomax.tanggal)
                FROM saldoawal_bj saldomax
                WHERE saldomax.kode_cabang = mc.kode_cabang)
                AND tgl_mutasi_gudang_cabang <= CURDATE() AND jenis_mutasi = 'SURAT JALAN'

                OR  tgl_mutasi_gudang_cabang >= (SELECT MAX(saldomax.tanggal)
                FROM saldoawal_bj saldomax
                WHERE saldomax.kode_cabang = mc.kode_cabang)
                AND tgl_mutasi_gudang_cabang <= CURDATE() AND jenis_mutasi = 'TRANSIT IN'

                OR  tgl_mutasi_gudang_cabang >= (SELECT MAX(saldomax.tanggal)
                FROM saldoawal_bj saldomax
                WHERE saldomax.kode_cabang = mc.kode_cabang)
                AND tgl_mutasi_gudang_cabang <= CURDATE() AND jenis_mutasi = 'TRANSIT OUT'

                OR  tgl_mutasi_gudang_cabang >= (SELECT MAX(saldomax.tanggal)
                FROM saldoawal_bj saldomax
                WHERE saldomax.kode_cabang = mc.kode_cabang)
                AND tgl_mutasi_gudang_cabang <= CURDATE() AND jenis_mutasi = 'REJECT GUDANG'

                OR  tgl_mutasi_gudang_cabang >= (SELECT MAX(saldomax.tanggal)
                FROM saldoawal_bj saldomax
                WHERE saldomax.kode_cabang = mc.kode_cabang)
                AND tgl_mutasi_gudang_cabang <= CURDATE() AND jenis_mutasi = 'REJECT PASAR'

                OR  tgl_mutasi_gudang_cabang >= (SELECT MAX(saldomax.tanggal)
                FROM saldoawal_bj saldomax
                WHERE saldomax.kode_cabang = mc.kode_cabang)
                AND tgl_mutasi_gudang_cabang <= CURDATE() AND jenis_mutasi = 'REPACK'

                OR  tgl_mutasi_gudang_cabang >= (SELECT MAX(saldomax.tanggal)
                FROM saldoawal_bj saldomax
                WHERE saldomax.kode_cabang = mc.kode_cabang)
                AND tgl_mutasi_gudang_cabang <= CURDATE() AND jenis_mutasi = 'PENYESUAIAN'


                GROUP BY kode_cabang
            ) mutasi"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'mutasi.kode_cabang');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT kode_cabang,
                ROUND(SUM(IF(kode_produk ='AB',jml_pengambilan,0)),2) as ab_ambil,
                ROUND(SUM(IF(kode_produk ='AR',jml_pengambilan,0)),2) as ar_ambil,
                ROUND(SUM(IF(kode_produk ='AS',jml_pengambilan,0)),2) as as_ambil,
                ROUND(SUM(IF(kode_produk ='BB',jml_pengambilan,0)),2) as bb_ambil,
                ROUND(SUM(IF(kode_produk ='DEP',jml_pengambilan,0)),2) as dep_ambil,
                ROUND(SUM(IF(kode_produk ='DS',jml_pengambilan,0)),2) as ds_ambil,
                ROUND(SUM(IF(kode_produk ='SP',jml_pengambilan,0)),2) as sp_ambil,
                ROUND(SUM(IF(kode_produk ='SPP',jml_pengambilan,0)),2) as spp_ambil,
                ROUND(SUM(IF(kode_produk ='SC',jml_pengambilan,0)),2) as sc_ambil,
                ROUND(SUM(IF(kode_produk ='SP8',jml_pengambilan,0)),2) as sp8_ambil,
                ROUND(SUM(IF(kode_produk ='SP8-P',jml_pengambilan,0)),2) as sp8p_ambil,
                ROUND(SUM(IF(kode_produk ='SP500',jml_pengambilan,0)),2) as sp500_ambil,
                ROUND(SUM(IF(kode_produk ='BR20',jml_pengambilan,0)),2) as br20_ambil,
                ROUND(SUM(IF(kode_produk ='AB',jml_pengembalian,0)),2) as ab_kembali,
                ROUND(SUM(IF(kode_produk ='AR',jml_pengembalian,0)),2) as ar_kembali,
                ROUND(SUM(IF(kode_produk ='AS',jml_pengembalian,0)),2) as as_kembali,
                ROUND(SUM(IF(kode_produk ='BB',jml_pengembalian,0)),2) as bb_kembali,
                ROUND(SUM(IF(kode_produk ='DEP',jml_pengembalian,0)),2) as dep_kembali,
                ROUND(SUM(IF(kode_produk ='DS',jml_pengembalian,0)),2) as ds_kembali,
                ROUND(SUM(IF(kode_produk ='SP',jml_pengembalian,0)),2) as sp_kembali,
                ROUND(SUM(IF(kode_produk ='SPP',jml_pengembalian,0)),2) as spp_kembali,
                ROUND(SUM(IF(kode_produk ='SC',jml_pengembalian,0)),2) as sc_kembali,
                ROUND(SUM(IF(kode_produk ='SP8',jml_pengembalian,0)),2) as sp8_kembali,
                ROUND(SUM(IF(kode_produk ='SP8-P',jml_pengembalian,0)),2) as sp8p_kembali,
                ROUND(SUM(IF(kode_produk ='SP500',jml_pengembalian,0)),2) as sp500_kembali,
                ROUND(SUM(IF(kode_produk ='BR20',jml_pengembalian,0)),2) as br20_kembali
                FROM detail_dpb
                INNER JOIN dpb ON detail_dpb.no_dpb = dpb.no_dpb
                WHERE tgl_pengambilan  >= (SELECT MAX(saldomax.tanggal)
                FROM saldoawal_bj saldomax
                WHERE saldomax.kode_cabang = dpb.kode_cabang)	 AND tgl_pengambilan <= CURDATE()
				GROUP BY kode_cabang
            ) dpb"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'dpb.kode_cabang');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT kode_cabang,
			SUM(IF(kode_produk='AB',jumlah,0)) as mg_ab,
			SUM(IF(kode_produk='AR',jumlah,0)) as mg_ar,
			SUM(IF(kode_produk='AS',jumlah,0)) as mg_as,
			SUM(IF(kode_produk='BB',jumlah,0)) as mg_bb,
			SUM(IF(kode_produk='DEP',jumlah,0)) as mg_dep,
			SUM(IF(kode_produk='DS',jumlah,0)) as mg_ds,
			SUM(IF(kode_produk='SP',jumlah,0)) as mg_sp,
			SUM(IF(kode_produk='SPP',jumlah,0)) as mg_spp,
			SUM(IF(kode_produk='SC',jumlah,0)) as mg_sc,
			SUM(IF(kode_produk='SP8',jumlah,0)) as mg_sp8,
			SUM(IF(kode_produk='SP8-P',jumlah,0)) as mg_sp8p,
			SUM(IF(kode_produk='SP500',jumlah,0)) as mg_sp500,
			SUM(IF(kode_produk='BR20',jumlah,0)) as mg_br20
			FROM detail_mutasi_gudang dmg
			INNER JOIN mutasi_gudang_jadi mg ON dmg.no_mutasi_gudang = mg.no_mutasi_gudang
			INNER JOIN permintaan_pengiriman pp ON mg.no_permintaan_pengiriman = pp.no_permintaan_pengiriman
			WHERE jenis_mutasi ='SURAT JALAN' AND status_sj='0' AND tgl_mutasi_gudang > '2021-11-01' AND tgl_mutasi_gudang < CURDATE()
			GROUP BY kode_cabang
            ) mgudang"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'mgudang.kode_cabang');
            }
        );
        // if (Auth::user()->id == 82) {
        //     $wilayah_barat = array('BDG', 'TSM', 'GRT', 'PWK', 'BGR', 'SKB');
        //     $query->whereIn('cabang.kode_cabang', $wilayah_barat);
        // } else if (Auth::user()->id == 97) {
        //     $wilayah_timur = array('TGL', 'PWT', 'SBY', 'KLT', 'SMR');
        //     $query->whereIn('cabang.kode_cabang', $wilayah_timur);
        // }




        $wilayah = Auth::user()->wilayah;
        if (!empty($wilayah)) {
            $wilayah_user = unserialize($wilayah);
            $query->whereIn('cabang.kode_cabang', $wilayah_user);
        }

        $query->orderBy('cabang.urutan');
        $rekapdpb = $query->get();
        $barang = Barang::all();
        $sampai = date("Y-m-d");
        $rekapgudang = DB::table('master_barang')
            ->select(
                'master_barang.kode_produk',
                'nama_barang',
                'isipcsdus',
                'isipack',
                'isipcs',
                DB::raw("SUM(IF(`inout`='IN'  AND detail_mutasi_gudang.kode_produk = master_barang.kode_produk
        AND mutasi_gudang_jadi.tgl_mutasi_gudang <= '$sampai',jumlah,0)) -
        SUM(IF(`inout`='OUT' AND detail_mutasi_gudang.kode_produk = master_barang.kode_produk
        AND mutasi_gudang_jadi.tgl_mutasi_gudang <= '$sampai',jumlah,0)) as saldoakhir")
            )
            ->leftJoin('detail_mutasi_gudang', 'detail_mutasi_gudang.kode_produk', '=', 'master_barang.kode_produk')
            ->leftJoin('mutasi_gudang_jadi', 'detail_mutasi_gudang.no_mutasi_gudang', '=', 'mutasi_gudang_jadi.no_mutasi_gudang')
            ->groupBy('master_barang.kode_produk', 'nama_barang', 'isipcsdus', 'isipcs', 'isipack')
            ->get();



        return view('gudang.dashboard.rekapdpb', compact('rekapdpb', 'barang', 'rekapgudang'));
    }
}
