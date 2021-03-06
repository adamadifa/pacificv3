<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Coa;
use App\Models\Costratio;
use App\Models\Detailretur;
use App\Models\Jurnalumum;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanaccountingController extends Controller
{
    public function rekapbj()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('laporanaccounting.laporan.frm.lap_rekapbj', compact('bulan'));
    }

    public function cetak_rekapbj(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $tgl1 = $tahun . "-" . $bulan . "-01";
        $tgl2 = date('Y-m-t', strtotime($tgl1));
        $rekaphpp = DB::select("SELECT mb.kode_produk,nama_barang,mb.isipcsdus,harga_hpp,
		sa_tsm,
		mutasi_tsm,
		sa_bdg,
		mutasi_bdg,
		sa_bgr,
		mutasi_bgr,
		sa_skb,
		mutasi_skb,
		sa_tgl,
		mutasi_tgl,
		sa_pwt,
		mutasi_pwt,
		sa_sby,
		mutasi_sby,
		sa_smr,
		mutasi_smr,
		sa_klt,
		mutasi_klt,
		sa_grt,
		mutasi_grt,
		sa_pst,
		mutasi_pst,
		harga_tsm,
		harga_bdg,
		harga_skb,
		harga_tgl,
		harga_bgr,
		harga_pwt,
		harga_pst,
		harga_sby,
		harga_smr,
		harga_klt,
		harga_grt,
		saldoawal_gd,
		jmlfsthp_gd,
		jmllainlain_in_gd,
		jmlrepack_gd,
		jmlreject_gd,
		jmlsuratjalan_gd,
		jmllainlain_out_gd,
		harga_kirim_cabang

		FROM master_barang mb

		LEFT JOIN (
			SELECT sa_bj_detail.kode_produk,
			SUM(IF(sa_bj.kode_cabang ='TSM',jumlah,0)) as sa_tsm,
			SUM(IF(sa_bj.kode_cabang ='BDG',jumlah,0)) as sa_bdg,
			SUM(IF(sa_bj.kode_cabang ='SKB',jumlah,0)) as sa_skb,
			SUM(IF(sa_bj.kode_cabang ='BGR',jumlah,0)) as sa_bgr,
			SUM(IF(sa_bj.kode_cabang ='TGL',jumlah,0)) as sa_tgl,
			SUM(IF(sa_bj.kode_cabang ='PWT',jumlah,0)) as sa_pwt,
			SUM(IF(sa_bj.kode_cabang ='SBY',jumlah,0)) as sa_sby,
			SUM(IF(sa_bj.kode_cabang ='SMR',jumlah,0)) as sa_smr,
			SUM(IF(sa_bj.kode_cabang ='KLT',jumlah,0)) as sa_klt,
			SUM(IF(sa_bj.kode_cabang ='GRT',jumlah,0)) as sa_grt,
			SUM(IF(sa_bj.kode_cabang ='PST',jumlah,0)) as sa_pst
			FROM saldoawal_bj_detail sa_bj_detail
			INNER JOIN saldoawal_bj sa_bj ON sa_bj_detail.kode_saldoawal = sa_bj.kode_saldoawal
			WHERE bulan = '$bulan' AND tahun='$tahun' AND status='GS'
			GROUP BY sa_bj_detail.kode_produk
		) sa ON (mb.kode_produk = sa.kode_produk)

		LEFT JOIN (
			SELECT dm.kode_produk,
			(SUM(IF(mgc.kode_cabang='TSM' AND inout_good = 'IN',jumlah,0)) - SUM(IF(mgc.kode_cabang='TSM' AND inout_good = 'OUT',jumlah,0))) as mutasi_tsm,
			(SUM(IF(mgc.kode_cabang='BDG' AND inout_good = 'IN',jumlah,0)) - SUM(IF(mgc.kode_cabang='BDG' AND inout_good = 'OUT',jumlah,0))) as mutasi_bdg,
			(SUM(IF(mgc.kode_cabang='BGR' AND inout_good = 'IN',jumlah,0)) - SUM(IF(mgc.kode_cabang='BGR' AND inout_good = 'OUT',jumlah,0))) as mutasi_bgr,
			(SUM(IF(mgc.kode_cabang='SKB' AND inout_good = 'IN',jumlah,0)) - SUM(IF(mgc.kode_cabang='SKB' AND inout_good = 'OUT',jumlah,0))) as mutasi_skb,
			(SUM(IF(mgc.kode_cabang='TGL' AND inout_good = 'IN',jumlah,0)) - SUM(IF(mgc.kode_cabang='TGL' AND inout_good = 'OUT',jumlah,0))) as mutasi_tgl,
			(SUM(IF(mgc.kode_cabang='PWT' AND inout_good = 'IN',jumlah,0)) - SUM(IF(mgc.kode_cabang='PWT' AND inout_good = 'OUT',jumlah,0))) as mutasi_pwt,
			(SUM(IF(mgc.kode_cabang='SBY' AND inout_good = 'IN',jumlah,0)) - SUM(IF(mgc.kode_cabang='SBY' AND inout_good = 'OUT',jumlah,0))) as mutasi_sby,
			(SUM(IF(mgc.kode_cabang='SMR' AND inout_good = 'IN',jumlah,0)) - SUM(IF(mgc.kode_cabang='SMR' AND inout_good = 'OUT',jumlah,0))) as mutasi_smr,
			(SUM(IF(mgc.kode_cabang='KLT' AND inout_good = 'IN',jumlah,0)) - SUM(IF(mgc.kode_cabang='KLT' AND inout_good = 'OUT',jumlah,0))) as mutasi_klt,
			(SUM(IF(mgc.kode_cabang='GRT' AND inout_good = 'IN',jumlah,0)) - SUM(IF(mgc.kode_cabang='GRT' AND inout_good = 'OUT',jumlah,0))) as mutasi_grt,
			(SUM(IF(mgc.kode_cabang='PST' AND inout_good = 'IN',jumlah,0)) - SUM(IF(mgc.kode_cabang='PST' AND inout_good = 'OUT',jumlah,0))) as mutasi_pst
			FROM detail_mutasi_gudang_cabang dm
			INNER JOIN mutasi_gudang_cabang mgc ON dm.no_mutasi_gudang_cabang = mgc.no_mutasi_gudang_cabang
			WHERE tgl_mutasi_gudang_cabang BETWEEN '$tgl1' AND '$tgl2'
			GROUP BY dm.kode_produk
			) mutasi ON (mb.kode_produk = mutasi.kode_produk)



		LEFT JOIN(
			SELECT kode_produk,harga_hpp
			FROM harga_hpp
			WHERE bulan='$bulan' AND tahun='$tahun'
		) hpp ON (mb.kode_produk = hpp.kode_produk)

		LEFT JOIN (
			SELECT
   			m.kode_produk,
			isipcsdus,
			((IFNULL(saldoawal,0) * IFNULL(harga_awal_produksi,0)) + (IFNULL(jmlbpbj,0) * IFNULL(harga_hpp,0))) / (IFNULL(saldoawal,0)  + IFNULL(jmlbpbj,0)) as harga_gudang,
			((IFNULL(saldoawal_gd,0) * IFNULL(harga_awal_gudang,0))
			+ ((IFNULL(jmlfsthp_gd,0) * IFNULL((SELECT harga_gudang),0)))
			+ ((IFNULL(jmlrepack_gd,0) * IFNULL((SELECT harga_gudang),0)))
			+ ((IFNULL(jmllainlain_in_gd,0) * IFNULL((SELECT harga_gudang),0)))
			) / (IFNULL(saldoawal_gd,0) + IFNULL(jmlfsthp_gd,0) + IFNULL(jmlrepack_gd,0) + + IFNULL(jmllainlain_in_gd,0) ) as harga_kirim_cabang,

			ROUND((((ROUND(IFNULL(sa_tsm,0) / IFNULL(isipcsdus,0),2)) * IFNULL(harga_awal_tsm,0))
			+ ((ROUND(IFNULL(pusat_tsm,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_tsm))
			+ ((ROUND(IFNULL(transit_in_tsm,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_tsm))
			+ ((ROUND(IFNULL(retur_tsm,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_tsm))
			+ ((ROUND(IFNULL(lainlain_tsm,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_tsm))
			+ ((ROUND(IFNULL(repack_tsm,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_tsm))) /
			(ROUND(IFNULL(sa_tsm,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(pusat_tsm,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(transit_in_tsm,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(retur_tsm,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(lainlain_tsm,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(repack_tsm,0) / IFNULL(isipcsdus,0),2)
			),9) as harga_tsm,


			ROUND((((ROUND(IFNULL(sa_bdg,0) / IFNULL(isipcsdus,0),2)) * IFNULL(harga_awal_bdg,0))
			+ ((ROUND(IFNULL(pusat_bdg,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_bdg))
			+ ((ROUND(IFNULL(transit_in_bdg,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_bdg))
			+ ((ROUND(IFNULL(retur_bdg,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_bdg))
			+ ((ROUND(IFNULL(lainlain_bdg,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_bdg))
			+ ((ROUND(IFNULL(repack_bdg,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_bdg))) /
			(ROUND(IFNULL(sa_bdg,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(pusat_bdg,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(transit_in_bdg,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(retur_bdg,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(lainlain_bdg,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(repack_bdg,0) / IFNULL(isipcsdus,0),2)
			),9) as harga_bdg,

			ROUND((((ROUND(IFNULL(sa_skb,0) / IFNULL(isipcsdus,0),2)) * IFNULL(harga_awal_skb,0))
			+ ((ROUND(IFNULL(pusat_skb,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_skb))
			+ ((ROUND(IFNULL(transit_in_skb,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_skb))
			+ ((ROUND(IFNULL(retur_skb,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_skb))
			+ ((ROUND(IFNULL(lainlain_skb,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_skb))
			+ ((ROUND(IFNULL(repack_skb,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_skb))) /
			(ROUND(IFNULL(sa_skb,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(pusat_skb,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(transit_in_skb,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(retur_skb,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(lainlain_skb,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(repack_skb,0) / IFNULL(isipcsdus,0),2)
			),9) as harga_skb,

			ROUND((((ROUND(IFNULL(sa_tgl,0) / IFNULL(isipcsdus,0),2)) * IFNULL(harga_awal_tgl,0))
			+ ((ROUND(IFNULL(pusat_tgl,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_tgl))
			+ ((ROUND(IFNULL(transit_in_tgl,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_tgl))
			+ ((ROUND(IFNULL(retur_tgl,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_tgl))
			+ ((ROUND(IFNULL(lainlain_tgl,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_tgl))
			+ ((ROUND(IFNULL(repack_tgl,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_tgl))) /
			(ROUND(IFNULL(sa_tgl,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(pusat_tgl,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(transit_in_tgl,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(retur_tgl,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(lainlain_tgl,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(repack_tgl,0) / IFNULL(isipcsdus,0),2)
			),9) as harga_tgl,

			ROUND((((ROUND(IFNULL(sa_bgr,0) / IFNULL(isipcsdus,0),2)) * IFNULL(harga_awal_bgr,0))
			+ ((ROUND(IFNULL(pusat_bgr,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_bgr))
			+ ((ROUND(IFNULL(transit_in_bgr,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_bgr))
			+ ((ROUND(IFNULL(retur_bgr,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_bgr))
			+ ((ROUND(IFNULL(lainlain_bgr,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_bgr))
			+ ((ROUND(IFNULL(repack_bgr,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_bgr))) /
			(ROUND(IFNULL(sa_bgr,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(pusat_bgr,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(transit_in_bgr,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(retur_bgr,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(lainlain_bgr,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(repack_bgr,0) / IFNULL(isipcsdus,0),2)
			),9) as harga_bgr,

			ROUND((((ROUND(IFNULL(sa_pwt,0) / IFNULL(isipcsdus,0),2)) * IFNULL(harga_awal_pwt,0))
			+ ((ROUND(IFNULL(pusat_pwt,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_pwt))
			+ ((ROUND(IFNULL(transit_in_pwt,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_pwt))
			+ ((ROUND(IFNULL(retur_pwt,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_pwt))
			+ ((ROUND(IFNULL(lainlain_pwt,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_pwt))
			+ ((ROUND(IFNULL(repack_pwt,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_pwt))) /
			(ROUND(IFNULL(sa_pwt,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(pusat_pwt,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(transit_in_pwt,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(retur_pwt,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(lainlain_pwt,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(repack_pwt,0) / IFNULL(isipcsdus,0),2)
			),9) as harga_pwt,

			ROUND((((ROUND(IFNULL(sa_pst,0) / IFNULL(isipcsdus,0),2)) * IFNULL(harga_awal_pst,0))
			+ ((ROUND(IFNULL(pusat_pst,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_pst))
			+ ((ROUND(IFNULL(transit_in_pst,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_pst))
			+ ((ROUND(IFNULL(retur_pst,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_pst))
			+ ((ROUND(IFNULL(lainlain_pst,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_pst))
			+ ((ROUND(IFNULL(repack_pst,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_pst))) /
			(ROUND(IFNULL(sa_pst,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(pusat_pst,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(transit_in_pst,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(retur_pst,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(lainlain_pst,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(repack_pst,0) / IFNULL(isipcsdus,0),2)
			),9) as harga_pst,

			ROUND((((ROUND(IFNULL(sa_sby,0) / IFNULL(isipcsdus,0),2)) * IFNULL(harga_awal_sby,0))
			+ ((ROUND(IFNULL(pusat_sby,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_sby))
			+ ((ROUND(IFNULL(transit_in_sby,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_sby))
			+ ((ROUND(IFNULL(retur_sby,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_sby))
			+ ((ROUND(IFNULL(lainlain_sby,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_sby))
			+ ((ROUND(IFNULL(repack_sby,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_sby))) /
			(ROUND(IFNULL(sa_sby,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(pusat_sby,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(transit_in_sby,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(retur_sby,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(lainlain_sby,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(repack_sby,0) / IFNULL(isipcsdus,0),2)
			),9) as harga_sby,

			ROUND((((ROUND(IFNULL(sa_smr,0) / IFNULL(isipcsdus,0),2)) * IFNULL(harga_awal_smr,0))
			+ ((ROUND(IFNULL(pusat_smr,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_smr))
			+ ((ROUND(IFNULL(transit_in_smr,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_smr))
			+ ((ROUND(IFNULL(retur_smr,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_smr))
			+ ((ROUND(IFNULL(lainlain_smr,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_smr))
			+ ((ROUND(IFNULL(repack_smr,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_smr))) /
			(ROUND(IFNULL(sa_smr,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(pusat_smr,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(transit_in_smr,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(retur_smr,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(lainlain_smr,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(repack_smr,0) / IFNULL(isipcsdus,0),2)
			),9) as harga_smr,

			ROUND((((ROUND(IFNULL(sa_klt,0) / IFNULL(isipcsdus,0),2)) * IFNULL(harga_awal_klt,0))
			+ ((ROUND(IFNULL(pusat_klt,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_klt))
			+ ((ROUND(IFNULL(transit_in_klt,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_klt))
			+ ((ROUND(IFNULL(retur_klt,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_klt))
			+ ((ROUND(IFNULL(lainlain_klt,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_klt))
			+ ((ROUND(IFNULL(repack_klt,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_klt))) /
			(ROUND(IFNULL(sa_klt,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(pusat_klt,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(transit_in_klt,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(retur_klt,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(lainlain_klt,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(repack_klt,0) / IFNULL(isipcsdus,0),2)
			),9) as harga_klt,

			ROUND((((ROUND(IFNULL(sa_grt,0) / IFNULL(isipcsdus,0),2)) * IFNULL(harga_awal_grt,0))
			+ ((ROUND(IFNULL(pusat_grt,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_grt))
			+ ((ROUND(IFNULL(transit_in_grt,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_grt))
			+ ((ROUND(IFNULL(retur_grt,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_grt))
			+ ((ROUND(IFNULL(lainlain_grt,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_grt))
			+ ((ROUND(IFNULL(repack_grt,0) / IFNULL(isipcsdus,0),2)) * IFNULL((SELECT harga_kirim_cabang),harga_awal_grt))) /
			(ROUND(IFNULL(sa_grt,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(pusat_grt,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(transit_in_grt,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(retur_grt,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(lainlain_grt,0) / IFNULL(isipcsdus,0),2)
			+ ROUND(IFNULL(repack_grt,0) / IFNULL(isipcsdus,0),2)
			),9) as harga_grt,

			saldoawal_gd,
			jmlfsthp_gd,
			jmllainlain_in_gd,
			jmlrepack_gd,
			jmlreject_gd,
			jmlsuratjalan_gd,
			jmllainlain_out_gd
		FROM
			master_barang m
		LEFT JOIN (
			SELECT
				kode_produk,
				IFNULL(SUM( IF ( `inout` = 'IN', jumlah, 0 ) ) - SUM( IF ( `inout` = 'OUT', jumlah, 0 ) ),0 )  as saldoawal
			FROM
				detail_mutasi_produksi d
			INNER JOIN mutasi_produksi ON d.no_mutasi_produksi = mutasi_produksi.no_mutasi_produksi
			WHERE tgl_mutasi_produksi < '$tgl1'
			GROUP BY kode_produk
		) sa ON (sa.kode_produk = m.kode_produk)

		LEFT JOIN (
			SELECT kode_produk,
				SUM(IF( jenis_mutasi = 'BPBJ', jumlah, 0 )) as jmlbpbj,
				SUM(IF( jenis_mutasi = 'FSTHP', jumlah, 0 )) as jmlfsthp,
				SUM(IF( jenis_mutasi = 'LAIN-LAIN' AND `inout` = 'IN', jumlah, 0 )) as mutasi_in,
				SUM(IF( jenis_mutasi = 'LAIN-LAIN' AND `inout` = 'OUT', jumlah, 0 )) as mutasi_out
    		FROM
				detail_mutasi_produksi d
    		INNER JOIN
				mutasi_produksi ON d.no_mutasi_produksi = mutasi_produksi.no_mutasi_produksi
    		WHERE
				tgl_mutasi_produksi BETWEEN '$tgl1' AND '$tgl2'
			GROUP BY kode_produk
		) dm ON (dm.kode_produk = m.kode_produk)

		LEFT JOIN (
			SELECT kode_produk,harga_hpp
			FROM harga_hpp
			WHERE bulan='$bulan' AND tahun='$tahun'
		) hpp ON (hpp.kode_produk = m.kode_produk)

		LEFT JOIN (
			SELECT kode_produk,
			SUM(IF(lokasi='PRD',harga_awal,0)) as harga_awal_produksi,
			SUM(IF(lokasi='GDG',harga_awal,0)) as harga_awal_gudang,
			SUM(IF(lokasi='TSM',harga_awal,0)) as harga_awal_tsm,
			SUM(IF(lokasi='BDG',harga_awal,0)) as harga_awal_bdg,
			SUM(IF(lokasi='SKB',harga_awal,0)) as harga_awal_skb,
			SUM(IF(lokasi='TGL',harga_awal,0)) as harga_awal_tgl,
			SUM(IF(lokasi='BGR',harga_awal,0)) as harga_awal_bgr,
			SUM(IF(lokasi='PWT',harga_awal,0)) as harga_awal_pwt,
			SUM(IF(lokasi='PST',harga_awal,0)) as harga_awal_pst,
			SUM(IF(lokasi='SBY',harga_awal,0)) as harga_awal_sby,
			SUM(IF(lokasi='SMR',harga_awal,0)) as harga_awal_smr,
			SUM(IF(lokasi='KLT',harga_awal,0)) as harga_awal_klt,
			SUM(IF(lokasi='GRT',harga_awal,0)) as harga_awal_grt
			FROM harga_awal
			WHERE bulan='$bulan' AND tahun='$tahun'
			GROUP BY kode_produk
		) ha ON (ha.kode_produk = m.kode_produk)

		LEFT JOIN (
			SELECT
				kode_produk,
				IFNULL(SUM( IF ( `inout` = 'IN', jumlah, 0 ) ) - SUM( IF ( `inout` = 'OUT', jumlah, 0 ) ),0) as saldoawal_gd
			FROM
				detail_mutasi_gudang d
			INNER JOIN
				mutasi_gudang_jadi ON d.no_mutasi_gudang = mutasi_gudang_jadi.no_mutasi_gudang
			WHERE
				tgl_mutasi_gudang < '$tgl1'
			GROUP BY
			kode_produk
		) sagd ON (sagd.kode_produk = m.kode_produk)

		LEFT JOIN (
				SELECT
					kode_produk,
					SUM(IF(jenis_mutasi = 'FSTHP' ,jumlah,0)) as jmlfsthp_gd,
					SUM(IF(jenis_mutasi = 'REPACK',jumlah,0)) as jmlrepack_gd,
					SUM(IF(jenis_mutasi = 'REJECT',jumlah,0)) as jmlreject_gd,
					SUM(IF(jenis_mutasi = 'LAINLAIN' AND  `inout` ='IN',jumlah,0)) as jmllainlain_in_gd,
					SUM(IF(jenis_mutasi = 'LAINLAIN' AND  `inout` ='OUT',jumlah,0)) as jmllainlain_out_gd,
					SUM(IF(jenis_mutasi = 'SURAT JALAN',jumlah,0)) as jmlsuratjalan_gd
				FROM
					detail_mutasi_gudang d
				INNER JOIN mutasi_gudang_jadi ON d.no_mutasi_gudang = mutasi_gudang_jadi.no_mutasi_gudang
				WHERE
					tgl_mutasi_gudang BETWEEN '$tgl1' AND '$tgl2' GROUP BY kode_produk
			) mutasi ON (m.kode_produk = mutasi.kode_produk)

		LEFT JOIN(
			SELECT kode_produk,
			SUM(IF(kode_cabang='TSM',jumlah,0)) as sa_tsm,
			SUM(IF(kode_cabang='BDG',jumlah,0)) as sa_bdg,
			SUM(IF(kode_cabang='SKB',jumlah,0)) as sa_skb,
			SUM(IF(kode_cabang='TGL',jumlah,0)) as sa_tgl,
			SUM(IF(kode_cabang='BGR',jumlah,0)) as sa_bgr,
			SUM(IF(kode_cabang='PWT',jumlah,0)) as sa_pwt,
			SUM(IF(kode_cabang='PST',jumlah,0)) as sa_pst,
			SUM(IF(kode_cabang='SBY',jumlah,0)) as sa_sby,
			SUM(IF(kode_cabang='SMR',jumlah,0)) as sa_smr,
			SUM(IF(kode_cabang='KLT',jumlah,0)) as sa_klt,
			SUM(IF(kode_cabang='GRT',jumlah,0)) as sa_grt
			FROM saldoawal_bj_detail s_detail
			INNER JOIN saldoawal_bj s ON s_detail.kode_saldoawal = s.kode_saldoawal
			WHERE bulan ='$bulan' AND tahun ='$tahun' AND status='GS'
			GROUP BY kode_produk
		) sacab ON (sacab.kode_produk = m.kode_produk)

		LEFT JOIN (
			SELECT kode_produk,
				SUM(IF(jenis_mutasi = 'SURAT JALAN' AND mc.kode_cabang='TSM' ,jumlah,0)) as pusat_tsm,
				SUM(IF(jenis_mutasi = 'TRANSIT IN' AND mc.kode_cabang='TSM' ,jumlah,0)) as transit_in_tsm,
				SUM(IF(jenis_mutasi = 'RETUR' AND mc.kode_cabang='TSM' ,jumlah,0)) as retur_tsm,
				SUM(IF(jenis_mutasi = 'PENYESUAIAN' AND mc.kode_cabang='TSM' AND inout_good ='IN'
				OR jenis_mutasi = 'HUTANG KIRIM' AND mc.kode_cabang='TSM' AND inout_good ='IN'
				OR jenis_mutasi = 'PL TTR' AND mc.kode_cabang='TSM' AND inout_good ='IN',jumlah,0)) as lainlain_tsm,
				SUM(IF(jenis_mutasi = 'REPACK' AND mc.kode_cabang='TSM' ,jumlah,0)) as repack_tsm,

				SUM(IF(jenis_mutasi = 'SURAT JALAN' AND mc.kode_cabang='BDG' ,jumlah,0)) as pusat_bdg,
				SUM(IF(jenis_mutasi = 'TRANSIT IN' AND mc.kode_cabang='BDG' ,jumlah,0)) as transit_in_bdg,
				SUM(IF(jenis_mutasi = 'RETUR' AND mc.kode_cabang='BDG' ,jumlah,0)) as retur_bdg,
				SUM(IF(jenis_mutasi = 'PENYESUAIAN' AND mc.kode_cabang='BDG' AND inout_good ='IN'
				OR jenis_mutasi = 'HUTANG KIRIM' AND mc.kode_cabang='BDG' AND inout_good ='IN'
				OR jenis_mutasi = 'PL TTR' AND mc.kode_cabang='BDG' AND inout_good ='IN',jumlah,0)) as lainlain_bdg,
				SUM(IF(jenis_mutasi = 'REPACK' AND mc.kode_cabang='BDG' ,jumlah,0)) as repack_bdg,

				SUM(IF(jenis_mutasi = 'SURAT JALAN' AND mc.kode_cabang='SKB' ,jumlah,0)) as pusat_skb,
				SUM(IF(jenis_mutasi = 'TRANSIT IN' AND mc.kode_cabang='SKB' ,jumlah,0)) as transit_in_skb,
				SUM(IF(jenis_mutasi = 'RETUR' AND mc.kode_cabang='SKB' ,jumlah,0)) as retur_skb,
				SUM(IF(jenis_mutasi = 'PENYESUAIAN' AND mc.kode_cabang='SKB' AND inout_good ='IN'
				OR jenis_mutasi = 'HUTANG KIRIM' AND mc.kode_cabang='SKB' AND inout_good ='IN'
				OR jenis_mutasi = 'PL TTR' AND mc.kode_cabang='SKB' AND inout_good ='IN',jumlah,0)) as lainlain_skb,
				SUM(IF(jenis_mutasi = 'REPACK' AND mc.kode_cabang='SKB' ,jumlah,0)) as repack_skb,

				SUM(IF(jenis_mutasi = 'SURAT JALAN' AND mc.kode_cabang='TGL' ,jumlah,0)) as pusat_tgl,
				SUM(IF(jenis_mutasi = 'TRANSIT IN' AND mc.kode_cabang='TGL' ,jumlah,0)) as transit_in_tgl,
				SUM(IF(jenis_mutasi = 'RETUR' AND mc.kode_cabang='TGL' ,jumlah,0)) as retur_tgl,
				SUM(IF(jenis_mutasi = 'PENYESUAIAN' AND mc.kode_cabang='TGL' AND inout_good ='IN'
				OR jenis_mutasi = 'HUTANG KIRIM' AND mc.kode_cabang='TGL' AND inout_good ='IN'
				OR jenis_mutasi = 'PL TTR' AND mc.kode_cabang='TGL' AND inout_good ='IN',jumlah,0)) as lainlain_tgl,
				SUM(IF(jenis_mutasi = 'REPACK' AND mc.kode_cabang='TGL' ,jumlah,0)) as repack_tgl,

				SUM(IF(jenis_mutasi = 'SURAT JALAN' AND mc.kode_cabang='BGR' ,jumlah,0)) as pusat_bgr,
				SUM(IF(jenis_mutasi = 'TRANSIT IN' AND mc.kode_cabang='BGR' ,jumlah,0)) as transit_in_bgr,
				SUM(IF(jenis_mutasi = 'RETUR' AND mc.kode_cabang='BGR' ,jumlah,0)) as retur_bgr,
				SUM(IF(jenis_mutasi = 'PENYESUAIAN' AND mc.kode_cabang='BGR' AND inout_good ='IN'
				OR jenis_mutasi = 'HUTANG KIRIM' AND mc.kode_cabang='BGR' AND inout_good ='IN'
				OR jenis_mutasi = 'PL TTR' AND mc.kode_cabang='BGR' AND inout_good ='IN',jumlah,0)) as lainlain_bgr,
				SUM(IF(jenis_mutasi = 'REPACK' AND mc.kode_cabang='BGR' ,jumlah,0)) as repack_bgr,

				SUM(IF(jenis_mutasi = 'SURAT JALAN' AND mc.kode_cabang='PWT' ,jumlah,0)) as pusat_pwt,
				SUM(IF(jenis_mutasi = 'TRANSIT IN' AND mc.kode_cabang='PWT' ,jumlah,0)) as transit_in_pwt,
				SUM(IF(jenis_mutasi = 'RETUR' AND mc.kode_cabang='PWT' ,jumlah,0)) as retur_pwt,
				SUM(IF(jenis_mutasi = 'PENYESUAIAN' AND mc.kode_cabang='PWT' AND inout_good ='IN'
				OR jenis_mutasi = 'HUTANG KIRIM' AND mc.kode_cabang='PWT' AND inout_good ='IN'
				OR jenis_mutasi = 'PL TTR' AND mc.kode_cabang='PWT' AND inout_good ='IN',jumlah,0)) as lainlain_pwt,
				SUM(IF(jenis_mutasi = 'REPACK' AND mc.kode_cabang='PWT' ,jumlah,0)) as repack_pwt,

				SUM(IF(jenis_mutasi = 'SURAT JALAN' AND mc.kode_cabang='PST' ,jumlah,0)) as pusat_pst,
				SUM(IF(jenis_mutasi = 'TRANSIT IN' AND mc.kode_cabang='PST' ,jumlah,0)) as transit_in_pst,
				SUM(IF(jenis_mutasi = 'RETUR' AND mc.kode_cabang='PST' ,jumlah,0)) as retur_pst,
				SUM(IF(jenis_mutasi = 'PENYESUAIAN' AND mc.kode_cabang='PST' AND inout_good ='IN'
				OR jenis_mutasi = 'HUTANG KIRIM' AND mc.kode_cabang='PST' AND inout_good ='IN'
				OR jenis_mutasi = 'PL TTR' AND mc.kode_cabang='PST' AND inout_good ='IN',jumlah,0)) as lainlain_pst,
				SUM(IF(jenis_mutasi = 'REPACK' AND mc.kode_cabang='PST' ,jumlah,0)) as repack_pst,

				SUM(IF(jenis_mutasi = 'SURAT JALAN' AND mc.kode_cabang='SBY' ,jumlah,0)) as pusat_sby,
				SUM(IF(jenis_mutasi = 'TRANSIT IN' AND mc.kode_cabang='SBY' ,jumlah,0)) as transit_in_sby,
				SUM(IF(jenis_mutasi = 'RETUR' AND mc.kode_cabang='SBY' ,jumlah,0)) as retur_sby,
				SUM(IF(jenis_mutasi = 'PENYESUAIAN' AND mc.kode_cabang='SBY' AND inout_good ='IN'
				OR jenis_mutasi = 'HUTANG KIRIM' AND mc.kode_cabang='SBY' AND inout_good ='IN'
				OR jenis_mutasi = 'PL TTR' AND mc.kode_cabang='SBY' AND inout_good ='IN',jumlah,0)) as lainlain_sby,
				SUM(IF(jenis_mutasi = 'REPACK' AND mc.kode_cabang='SBY' ,jumlah,0)) as repack_sby,

				SUM(IF(jenis_mutasi = 'SURAT JALAN' AND mc.kode_cabang='SMR' ,jumlah,0)) as pusat_smr,
				SUM(IF(jenis_mutasi = 'TRANSIT IN' AND mc.kode_cabang='SMR' ,jumlah,0)) as transit_in_smr,
				SUM(IF(jenis_mutasi = 'RETUR' AND mc.kode_cabang='SMR' ,jumlah,0)) as retur_smr,
				SUM(IF(jenis_mutasi = 'PENYESUAIAN' AND mc.kode_cabang='SMR' AND inout_good ='IN'
				OR jenis_mutasi = 'HUTANG KIRIM' AND mc.kode_cabang='SMR' AND inout_good ='IN'
				OR jenis_mutasi = 'PL TTR' AND mc.kode_cabang='SMR' AND inout_good ='IN',jumlah,0)) as lainlain_smr,
				SUM(IF(jenis_mutasi = 'REPACK' AND mc.kode_cabang='SMR' ,jumlah,0)) as repack_smr,

				SUM(IF(jenis_mutasi = 'SURAT JALAN' AND mc.kode_cabang='KLT' ,jumlah,0)) as pusat_klt,
				SUM(IF(jenis_mutasi = 'TRANSIT IN' AND mc.kode_cabang='KLT' ,jumlah,0)) as transit_in_klt,
				SUM(IF(jenis_mutasi = 'RETUR' AND mc.kode_cabang='KLT' ,jumlah,0)) as retur_klt,
				SUM(IF(jenis_mutasi = 'PENYESUAIAN' AND mc.kode_cabang='KLT' AND inout_good ='IN'
				OR jenis_mutasi = 'HUTANG KIRIM' AND mc.kode_cabang='KLT' AND inout_good ='IN'
				OR jenis_mutasi = 'PL TTR' AND mc.kode_cabang='KLT' AND inout_good ='IN',jumlah,0)) as lainlain_klt,
				SUM(IF(jenis_mutasi = 'REPACK' AND mc.kode_cabang='KLT' ,jumlah,0)) as repack_klt,

				SUM(IF(jenis_mutasi = 'SURAT JALAN' AND mc.kode_cabang='GRT' ,jumlah,0)) as pusat_grt,
				SUM(IF(jenis_mutasi = 'TRANSIT IN' AND mc.kode_cabang='GRT' ,jumlah,0)) as transit_in_grt,
				SUM(IF(jenis_mutasi = 'RETUR' AND mc.kode_cabang='GRT' ,jumlah,0)) as retur_grt,
				SUM(IF(jenis_mutasi = 'PENYESUAIAN' AND mc.kode_cabang='GRT' AND inout_good ='IN'
				OR jenis_mutasi = 'HUTANG KIRIM' AND mc.kode_cabang='GRT' AND inout_good ='IN'
				OR jenis_mutasi = 'PL TTR' AND mc.kode_cabang='GRT' AND inout_good ='IN',jumlah,0)) as lainlain_grt,
				SUM(IF(jenis_mutasi = 'REPACK' AND mc.kode_cabang='GRT' ,jumlah,0)) as repack_grt

			FROM detail_mutasi_gudang_cabang dmc
			INNER JOIN mutasi_gudang_cabang mc ON dmc.no_mutasi_gudang_cabang = mc.no_mutasi_gudang_cabang
			WHERE tgl_mutasi_gudang_cabang BETWEEN '$tgl1' AND '$tgl2'
			GROUP BY kode_produk
		) mcab ON (mcab.kode_produk = m.kode_produk)
		ORDER BY urutan ASC
		) harga ON (harga.kode_produk = mb.kode_produk) ORDER BY urutan ASC");
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekap BJ.xls");
        }
        return view('laporanaccounting.laporan.cetak_rekapbj', compact('rekaphpp', 'tgl1', 'tgl2'));
    }

    public function rekappersediaan()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('laporanaccounting.laporan.frm.lap_rekappersediaan', compact('bulan'));
    }

    public function cetak_rekappersediaan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $dari =  $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $jenislaporan = $request->jenislaporan;
        if ($jenislaporan == "detail") {
            $rekap = DB::select("SELECT
            IFNULL(dmc.kode_produk,saldo_gs.kode_produk) as kode_produk,IFNULL(dmc.nama_barang,saldo_gs.nama_barang) as nama_barang,
            IFNULL(dmc.kode_cabang,saldo_gs.kode_cabang) as kode_cabang,IFNULL(isipcsdus,isipcsdus_sa) as isipcsdus,saldo_awal,pusat,transit_in,retur,lainlain_in,
            penyesuaian_in,penyesuaianbad_in,repack,
            penjualan,promosi,reject_pasar,reject_mobil,reject_gudang,transit_out,lainlain_out,penyesuaian_out,penyesuaianbad_out,kirim_pusat
            FROM (
            SELECT
            detail_mutasi_gudang_cabang.kode_produk,mutasi_gudang_cabang.kode_cabang,nama_barang,isipcsdus,
            SUM(IF(jenis_mutasi = 'SURAT JALAN',jumlah,0)) as pusat,
            SUM(IF(jenis_mutasi = 'TRANSIT IN',jumlah,0)) as transit_in,
            SUM(IF(jenis_mutasi = 'RETUR',jumlah,0)) as retur,
            SUM(IF(jenis_mutasi = 'HUTANG KIRIM' AND inout_good='IN' OR jenis_mutasi='PL TTR' AND inout_good='IN',jumlah,0)) as lainlain_in,
            SUM(IF(jenis_mutasi = 'PENYESUAIAN' AND inout_good='IN',jumlah,0)) as penyesuaian_in,
            SUM(IF(jenis_mutasi = 'PENYESUAIAN BAD' AND inout_good='IN',jumlah,0)) as penyesuaianbad_in,
            SUM(IF(jenis_mutasi = 'REPACK',jumlah,0)) as repack,

            SUM(IF(jenis_mutasi = 'PENJUALAN',jumlah,0)) as penjualan,
            SUM(IF(jenis_mutasi = 'PROMOSI',jumlah,0)) as promosi,
            SUM(IF(jenis_mutasi = 'REJECT PASAR',jumlah,0)) as reject_pasar,
            SUM(IF(jenis_mutasi = 'REJECT MOBIL',jumlah,0)) as reject_mobil,
            SUM(IF(jenis_mutasi = 'REJECT GUDANG',jumlah,0)) as reject_gudang,
            SUM(IF(jenis_mutasi = 'TRANSIT OUT',jumlah,0)) as transit_out,
            SUM(IF(jenis_mutasi = 'PL HUTANG KIRIM' AND inout_good='OUT'
            OR jenis_mutasi='TTR' AND inout_good='OUT'
            OR jenis_mutasi='GANTI BARANG' AND inout_good='OUT',jumlah,0)) as lainlain_out,
            SUM(IF(jenis_mutasi = 'PENYESUAIAN' AND inout_good='OUT',jumlah,0)) as penyesuaian_out,
            SUM(IF(jenis_mutasi = 'PENYESUAIAN BAD' AND inout_good='OUT',jumlah,0)) as penyesuaianbad_out,
            SUM(IF(jenis_mutasi = 'KIRIM PUSAT',jumlah,0)) as kirim_pusat
            FROM detail_mutasi_gudang_cabang
            INNER JOIN mutasi_gudang_cabang ON detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang = mutasi_gudang_cabang.no_mutasi_gudang_cabang
            INNER JOIN master_barang ON detail_mutasi_gudang_cabang.kode_produk = master_barang.kode_produk
            WHERE tgl_mutasi_gudang_cabang BETWEEN '$dari' AND '$sampai'
            GROUP BY mutasi_gudang_cabang.kode_cabang,detail_mutasi_gudang_cabang.kode_produk,nama_barang,isipcsdus
            ) AS dmc
            LEFT JOIN (
                SELECT saldoawal_bj_detail.kode_produk,nama_barang,isipcsdus as isipcsdus_sa,kode_cabang,jumlah as saldo_awal
                FROM saldoawal_bj_detail
                INNER JOIN saldoawal_bj ON saldoawal_bj_detail.kode_saldoawal = saldoawal_bj.kode_saldoawal
                INNER JOIN master_barang ON saldoawal_bj_detail.kode_produk = master_barang.kode_produk
                WHERE saldoawal_bj.status ='GS' AND bulan ='$bulan' AND tahun='$tahun'
            ) saldo_gs ON (dmc.kode_produk = saldo_gs.kode_produk AND dmc.kode_cabang = saldo_gs.kode_cabang)

            UNION
            SELECT
            IFNULL(dmc.kode_produk,saldo_gs.kode_produk) as kode_produk,IFNULL(dmc.nama_barang,saldo_gs.nama_barang) as nama_barang,
            IFNULL(dmc.kode_cabang,saldo_gs.kode_cabang) as kode_cabang,IFNULL(isipcsdus,isipcsdus_sa) as isipcsdus,saldo_awal,pusat,transit_in,retur,lainlain_in,
            penyesuaian_in,penyesuaianbad_in,repack,
            penjualan,promosi,reject_pasar,reject_mobil,reject_gudang,transit_out,lainlain_out,penyesuaian_out,penyesuaianbad_out,kirim_pusat
            FROM (
            SELECT
            detail_mutasi_gudang_cabang.kode_produk,mutasi_gudang_cabang.kode_cabang,nama_barang,isipcsdus,
            SUM(IF(jenis_mutasi = 'SURAT JALAN',jumlah,0)) as pusat,
            SUM(IF(jenis_mutasi = 'TRANSIT IN',jumlah,0)) as transit_in,
            SUM(IF(jenis_mutasi = 'RETUR',jumlah,0)) as retur,
            SUM(IF(jenis_mutasi = 'HUTANG KIRIM' AND inout_good='IN' OR jenis_mutasi='PL TTR' AND inout_good='IN',jumlah,0)) as lainlain_in,
            SUM(IF(jenis_mutasi = 'PENYESUAIAN' AND inout_good='IN',jumlah,0)) as penyesuaian_in,
            SUM(IF(jenis_mutasi = 'PENYESUAIAN BAD' AND inout_good='IN',jumlah,0)) as penyesuaianbad_in,
            SUM(IF(jenis_mutasi = 'REPACK',jumlah,0)) as repack,

            SUM(IF(jenis_mutasi = 'PENJUALAN',jumlah,0)) as penjualan,
            SUM(IF(jenis_mutasi = 'PROMOSI',jumlah,0)) as promosi,
            SUM(IF(jenis_mutasi = 'REJECT PASAR',jumlah,0)) as reject_pasar,
            SUM(IF(jenis_mutasi = 'REJECT MOBIL',jumlah,0)) as reject_mobil,
            SUM(IF(jenis_mutasi = 'REJECT GUDANG',jumlah,0)) as reject_gudang,
            SUM(IF(jenis_mutasi = 'TRANSIT OUT',jumlah,0)) as transit_out,
            SUM(IF(jenis_mutasi = 'PL HUTANG KIRIM' AND inout_good='OUT'
            OR jenis_mutasi='TTR' AND inout_good='OUT'
            OR jenis_mutasi='GANTI BARANG' AND inout_good='OUT',jumlah,0)) as lainlain_out,
            SUM(IF(jenis_mutasi = 'PENYESUAIAN' AND inout_good='OUT',jumlah,0)) as penyesuaian_out,
            SUM(IF(jenis_mutasi = 'PENYESUAIAN BAD' AND inout_good='OUT',jumlah,0)) as penyesuaianbad_out,
            SUM(IF(jenis_mutasi = 'KIRIM PUSAT',jumlah,0)) as kirim_pusat
            FROM detail_mutasi_gudang_cabang
            INNER JOIN mutasi_gudang_cabang ON detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang = mutasi_gudang_cabang.no_mutasi_gudang_cabang
            INNER JOIN master_barang ON detail_mutasi_gudang_cabang.kode_produk = master_barang.kode_produk
            WHERE tgl_mutasi_gudang_cabang BETWEEN '$dari' AND '$sampai'
            GROUP BY mutasi_gudang_cabang.kode_cabang,detail_mutasi_gudang_cabang.kode_produk,nama_barang,isipcsdus
            ) AS dmc
            RIGHT JOIN (
                SELECT saldoawal_bj_detail.kode_produk,nama_barang,isipcsdus as isipcsdus_sa,kode_cabang,jumlah as saldo_awal
                FROM saldoawal_bj_detail
                INNER JOIN saldoawal_bj ON saldoawal_bj_detail.kode_saldoawal = saldoawal_bj.kode_saldoawal
                INNER JOIN master_barang ON saldoawal_bj_detail.kode_produk = master_barang.kode_produk
                WHERE saldoawal_bj.status ='GS' AND bulan ='$bulan' AND tahun='$tahun'
            ) saldo_gs ON (dmc.kode_produk = saldo_gs.kode_produk AND dmc.kode_cabang = saldo_gs.kode_cabang)
            ORDER BY kode_cabang,kode_produk");
        } else {
        }
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Persediaan Cabang $dari-$sampai.xls");
        }
        return view('laporanaccounting.laporan.cetak_rekappersediaan', compact('dari', 'sampai', 'rekap'));
    }

    public function bukubesar()
    {
        $akun = Coa::orderBy('kode_akun')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('laporanaccounting.laporan.frm.lap_bukubesar', compact('bulan', 'akun'));
    }

    public function cetak_bukubesar(Request $request)
    {
        // $bulan = $request->bulan;
        // $tahun = $request->tahun;

        $dari = $request->dari;
        $sampai = $request->sampai;

        $dari_akun = $request->dari_akun;
        $sampai_akun = $request->sampai_akun;
        if (!empty($request->dari)) {
            $tanggal = explode("-", $request->dari);
            $bulan = $tanggal[1] + 0;
            $tahun = $tanggal[0];
        } else {
            $bulan = "";
            $tahun = "";
        }


        $bukubesar = DB::table('coa')
            ->select('coa.kode_akun', 'nama_akun', 'tanggal', 'debet', 'kredit', 'sumber', 'keterangan', 'nobukti_transaksi', 'jenis_akun')
            ->leftJoin(
                DB::raw("(
                    SELECT kode_akun,tanggal,debet,kredit,sumber,keterangan,nobukti_transaksi
                    FROM buku_besar
                    WHERE tanggal BETWEEN '$dari' AND '$sampai'
                ) bb"),
                function ($join) {
                    $join->on('coa.kode_akun', '=', 'bb.kode_akun');
                }
            )
            ->whereBetween('coa.kode_akun', [$dari_akun, $sampai_akun])
            ->orderBy('coa.kode_akun')
            ->orderBy('bb.tanggal')
            ->orderBy('bb.debet', 'desc')
            ->get();
        $dariakun = DB::table('coa')->where('kode_akun', $dari_akun)->first();
        $sampaiakun = DB::table('coa')->where('kode_akun', $sampai_akun)->first();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Buku Besar $dari-$sampai.xls");
        }
        return view('laporanaccounting.laporan.cetak_bukubesar', compact('dari', 'sampai', 'dariakun', 'sampaiakun', 'bukubesar', 'bulan', 'tahun'));
    }

    public function jurnalumum()
    {
        if (Auth::user()->level == "general affair") {
            $departemen = DB::table('departemen')
                ->where('kode_dept', 'GAF')
                ->where('status_pengajuan', 1)->get();
        } else {
            $departemen = DB::table('departemen')->where('status_pengajuan', 1)->get();
        }
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('laporanaccounting.laporan.frm.lap_jurnalumum', compact('bulan', 'departemen'));
    }

    public function cetak_jurnalumum(Request $request)
    {
        // $bulan = $request->bulan;
        // $tahun = $request->tahun;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $kode_dept = $request->kode_dept;

        $query = Jurnalumum::query();
        $query->join('coa', 'jurnal_umum.kode_akun', '=', 'coa.kode_akun');
        $query->whereBetween('tanggal', [$dari, $sampai]);
        if (!empty($kode_dept)) {
            $query->where('kode_dept', $kode_dept);
        }
        $query->orderBy('tanggal');
        $query->orderBy('keterangan');
        $jurnalumum = $query->get();
        $departemen = DB::table('departemen')->where('kode_dept', $kode_dept)->first();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Jurnal Umum.xls");
        }
        return view('laporanaccounting.laporan.cetak_jurnalumum', compact('dari', 'sampai', 'departemen', 'jurnalumum'));
    }

    public function costratio()
    {
        if (Auth::user()->kode_cabang != "PCF") {
            $cabang = Cabang::orderBy('kode_cabang')->where('kode_cabang', Auth::user()->kode_cabang)->get();
        } else {
            $cabang = Cabang::orderBy('kode_cabang')->get();
        }
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('laporanaccounting.laporan.frm.lap_costratio', compact('bulan', 'cabang'));
    }

    public function cetak_costratio(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kode_cabang = $request->kode_cabang;
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $query = Costratio::query();
        if (!empty($request->kode_cabang)) {
            $query->selectRaw("costratio_biaya.kode_akun,nama_akun,SUM(jumlah) as total");
            $query->where('costratio_biaya.kode_cabang', $request->kode_cabang);
        } else {
            $query->selectRaw("costratio_biaya.kode_akun,nama_akun,
            SUM(IF(kode_cabang='BDG',jumlah,0)) as bdg,
            SUM(IF(kode_cabang='BGR',jumlah,0)) as bgr,
            SUM(IF(kode_cabang='GRT',jumlah,0)) as grt,
            SUM(IF(kode_cabang='KLT',jumlah,0)) as klt,
            SUM(IF(kode_cabang='PST',jumlah,0)) as pst,
            SUM(IF(kode_cabang='PWT',jumlah,0)) as pwt,
            SUM(IF(kode_cabang='SBY',jumlah,0)) as sby,
            SUM(IF(kode_cabang='SKB',jumlah,0)) as skb,
            SUM(IF(kode_cabang='SMR',jumlah,0)) as smr,
            SUM(IF(kode_cabang='TGL',jumlah,0)) as tgl,
            SUM(IF(kode_cabang='TSM',jumlah,0)) as tsm,
            SUM(jumlah) as total");
        }

        $query->join('coa', 'costratio_biaya.kode_akun', '=', 'coa.kode_akun');
        $query->whereBetween('tgl_transaksi', [$dari, $sampai]);
        $query->orderBy('costratio_biaya.kode_akun');
        $query->groupByRaw('costratio_biaya.kode_akun,nama_akun');
        $query->get();
        $biaya = $query->get();

        $qpenjualan = Penjualan::query();
        if (!empty($kode_cabang)) {
            $qpenjualan->selectRaw("
            SUM(brutoswan-IFNULL(potswan,0)-IFNULL(potisswan,0) - IFNULL(potisstick,0) - IFNULL(potstick,0) - IFNULL(penyswan,0) - IFNULL(penystick,0) - IFNULL(potsp,0)- IFNULL(potsambal,0)) as totalswan,
            SUM(brutoaida-potaida - potisaida-penyaida) as totalaida
            ");
            $qpenjualan->where('karyawan.kode_cabang', $kode_cabang);
        } else {
            $qpenjualan->selectRaw("
            SUM(IF(karyawan.kode_cabang ='TSM',brutoswan-IFNULL(potswan,0)-IFNULL(potisswan,0) - IFNULL(potisstick,0) - IFNULL(potstick,0) - IFNULL(penyswan,0) - IFNULL(penystick,0) - IFNULL(potsp,0)- IFNULL(potsambal,0) ,0)) as netswanTSM,
            SUM(IF(karyawan.kode_cabang ='TSM',brutoaida-potaida - potisaida-penyaida,0)) as netaidaTSM,
            SUM(IF(karyawan.kode_cabang ='BDG',brutoswan-IFNULL(potswan,0)-IFNULL(potisswan,0) - IFNULL(potisstick,0) - IFNULL(potstick,0) - IFNULL(penyswan,0) - IFNULL(penystick,0) - IFNULL(potsp,0)- IFNULL(potsambal,0),0)) as netswanBDG,
            SUM(IF(karyawan.kode_cabang ='BDG',brutoaida-potaida - potisaida-penyaida,0)) as netaidaBDG,
            SUM(IF(karyawan.kode_cabang ='SKB',brutoswan-IFNULL(potswan,0)-IFNULL(potisswan,0) - IFNULL(potisstick,0) - IFNULL(potstick,0) - IFNULL(penyswan,0) - IFNULL(penystick,0) - IFNULL(potsp,0)- IFNULL(potsambal,0),0)) as netswanSKB,
            SUM(IF(karyawan.kode_cabang ='SKB',brutoaida-potaida - potisaida-penyaida,0)) as netaidaSKB,
            SUM(IF(karyawan.kode_cabang ='TGL',brutoswan-IFNULL(potswan,0)-IFNULL(potisswan,0) - IFNULL(potisstick,0) - IFNULL(potstick,0) - IFNULL(penyswan,0) - IFNULL(penystick,0) - IFNULL(potsp,0)- IFNULL(potsambal,0),0)) as netswanTGL,
            SUM(IF(karyawan.kode_cabang ='TGL',brutoaida-potaida - potisaida-penyaida,0)) as netaidaTGL,
            SUM(IF(karyawan.kode_cabang ='BGR',brutoswan-IFNULL(potswan,0)-IFNULL(potisswan,0) - IFNULL(potisstick,0) - IFNULL(potstick,0) - IFNULL(penyswan,0) - IFNULL(penystick,0) - IFNULL(potsp,0)- IFNULL(potsambal,0),0)) as netswanBGR,
            SUM(IF(karyawan.kode_cabang ='BGR',brutoaida-potaida - potisaida-penyaida,0)) as netaidaBGR,
            SUM(IF(karyawan.kode_cabang ='PWT',brutoswan-IFNULL(potswan,0)-IFNULL(potisswan,0) - IFNULL(potisstick,0) - IFNULL(potstick,0) - IFNULL(penyswan,0) - IFNULL(penystick,0) - IFNULL(potsp,0)- IFNULL(potsambal,0),0)) as netswanPWT,
            SUM(IF(karyawan.kode_cabang ='PWT',brutoaida-potaida - potisaida-penyaida,0)) as netaidaPWT,
            SUM(IF(karyawan.kode_cabang ='PST',brutoswan-IFNULL(potswan,0)-IFNULL(potisswan,0) - IFNULL(potisstick,0) - IFNULL(potstick,0) - IFNULL(penyswan,0) - IFNULL(penystick,0) - IFNULL(potsp,0)- IFNULL(potsambal,0),0)) as netswanPST,
            SUM(IF(karyawan.kode_cabang ='PST',brutoaida-potaida - potisaida-penyaida,0)) as netaidaPST,
            SUM(IF(karyawan.kode_cabang ='GRT',brutoswan-IFNULL(potswan,0)-IFNULL(potisswan,0) - IFNULL(potisstick,0) - IFNULL(potstick,0) - IFNULL(penyswan,0) - IFNULL(penystick,0) - IFNULL(potsp,0)- IFNULL(potsambal,0),0)) as netswanGRT,
            SUM(IF(karyawan.kode_cabang ='GRT',brutoaida-potaida - potisaida-penyaida,0)) as netaidaGRT,
            SUM(IF(karyawan.kode_cabang ='SBY',brutoswan-IFNULL(potswan,0)-IFNULL(potisswan,0) - IFNULL(potisstick,0) - IFNULL(potstick,0) - IFNULL(penyswan,0) - IFNULL(penystick,0) - IFNULL(potsp,0)- IFNULL(potsambal,0),0)) as netswanSBY,
            SUM(IF(karyawan.kode_cabang ='SBY',brutoaida-potaida - potisaida-penyaida,0)) as netaidaSBY,
            SUM(IF(karyawan.kode_cabang ='SMR',brutoswan-IFNULL(potswan,0)-IFNULL(potisswan,0) - IFNULL(potisstick,0) - IFNULL(potstick,0) - IFNULL(penyswan,0) - IFNULL(penystick,0) - IFNULL(potsp,0)- IFNULL(potsambal,0),0)) as netswanSMR,
            SUM(IF(karyawan.kode_cabang ='SMR',brutoaida-potaida - potisaida-penyaida,0)) as netaidaSMR,
            SUM(IF(karyawan.kode_cabang ='KLT',brutoswan-IFNULL(potswan,0)-IFNULL(potisswan,0) - IFNULL(potisstick,0) - IFNULL(potstick,0) - IFNULL(penyswan,0) - IFNULL(penystick,0) - IFNULL(potsp,0)- IFNULL(potsambal,0),0)) as netswanKLT,
            SUM(IF(karyawan.kode_cabang ='KLT',brutoaida-potaida - potisaida-penyaida,0)) as netaidaKLT,
            SUM(brutoswan-IFNULL(potswan,0)-IFNULL(potisswan,0) - IFNULL(potisstick,0) - IFNULL(potstick,0) - IFNULL(penyswan,0) - IFNULL(penystick,0) - IFNULL(potsp,0)- IFNULL(potsambal,0)) as totalswan,
            SUM(brutoaida-potaida - potisaida-penyaida) as totalaida
            ");
        }

        $qpenjualan->leftJoin(
            DB::raw("(
                SELECT no_fak_penj,
                SUM(IF(master_barang.jenis_produk = 'SWAN',detailpenjualan.subtotal,0)) as brutoswan,
                SUM(IF(master_barang.jenis_produk = 'AIDA',detailpenjualan.subtotal,0)) as brutoaida
                FROM detailpenjualan
                INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                INNER JOIN master_barang ON barang.kode_produk = master_barang.kode_produk
                GROUP BY no_fak_penj
            ) dp"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'dp.no_fak_penj');
            }
        );
        $qpenjualan->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        $qpenjualan->whereBetween('tgltransaksi', [$dari, $sampai]);
        $penjualan = $qpenjualan->first();

        $qretur = Detailretur::query();
        if (!empty($kode_cabang)) {
            $qretur->selectRaw("
            SUM(IF(master_barang.jenis_produk = 'SWAN',detailretur.subtotal,0)) as totalreturswan,
            SUM(IF(master_barang.jenis_produk = 'AIDA',detailretur.subtotal,0)) as totalreturaida
            ");
            $qretur->where('karyawan.kode_cabang', $kode_cabang);
        } else {
            $qretur->selectRaw("
            SUM(IF(master_barang.jenis_produk = 'SWAN' AND karyawan.kode_cabang ='TSM',detailretur.subtotal,0)) as returswanTSM,
            SUM(IF(master_barang.jenis_produk = 'AIDA' AND karyawan.kode_cabang ='TSM',detailretur.subtotal,0)) as returaidaTSM,
            SUM(IF(master_barang.jenis_produk = 'SWAN' AND karyawan.kode_cabang ='BDG',detailretur.subtotal,0)) as returswanBDG,
            SUM(IF(master_barang.jenis_produk = 'AIDA' AND karyawan.kode_cabang ='BDG',detailretur.subtotal,0)) as returaidaBDG,
            SUM(IF(master_barang.jenis_produk = 'SWAN' AND karyawan.kode_cabang ='SKB',detailretur.subtotal,0)) as returswanSKB,
            SUM(IF(master_barang.jenis_produk = 'AIDA' AND karyawan.kode_cabang ='SKB',detailretur.subtotal,0)) as returaidaSKB,
            SUM(IF(master_barang.jenis_produk = 'SWAN' AND karyawan.kode_cabang ='TGL',detailretur.subtotal,0)) as returswanTGL,
            SUM(IF(master_barang.jenis_produk = 'AIDA' AND karyawan.kode_cabang ='TGL',detailretur.subtotal,0)) as returaidaTGL,
            SUM(IF(master_barang.jenis_produk = 'SWAN' AND karyawan.kode_cabang ='BGR',detailretur.subtotal,0)) as returswanBGR,
            SUM(IF(master_barang.jenis_produk = 'AIDA' AND karyawan.kode_cabang ='BGR',detailretur.subtotal,0)) as returaidaBGR,
            SUM(IF(master_barang.jenis_produk = 'SWAN' AND karyawan.kode_cabang ='PST',detailretur.subtotal,0)) as returswanPST,
            SUM(IF(master_barang.jenis_produk = 'AIDA' AND karyawan.kode_cabang ='PST',detailretur.subtotal,0)) as returaidaPST,
            SUM(IF(master_barang.jenis_produk = 'SWAN' AND karyawan.kode_cabang ='GRT',detailretur.subtotal,0)) as returswanGRT,
            SUM(IF(master_barang.jenis_produk = 'AIDA' AND karyawan.kode_cabang ='GRT',detailretur.subtotal,0)) as returaidaGRT,
            SUM(IF(master_barang.jenis_produk = 'SWAN' AND karyawan.kode_cabang ='SBY',detailretur.subtotal,0)) as returswanSBY,
            SUM(IF(master_barang.jenis_produk = 'AIDA' AND karyawan.kode_cabang ='SBY',detailretur.subtotal,0)) as returaidaSBY,
            SUM(IF(master_barang.jenis_produk = 'SWAN' AND karyawan.kode_cabang ='SMR',detailretur.subtotal,0)) as returswanSMR,
            SUM(IF(master_barang.jenis_produk = 'AIDA' AND karyawan.kode_cabang ='SMR',detailretur.subtotal,0)) as returaidaSMR,
            SUM(IF(master_barang.jenis_produk = 'SWAN' AND karyawan.kode_cabang ='PWT',detailretur.subtotal,0)) as returswanPWT,
            SUM(IF(master_barang.jenis_produk = 'AIDA' AND karyawan.kode_cabang ='PWT',detailretur.subtotal,0)) as returaidaPWT,
            SUM(IF(master_barang.jenis_produk = 'SWAN' AND karyawan.kode_cabang ='KLT',detailretur.subtotal,0)) as returswanKLT,
            SUM(IF(master_barang.jenis_produk = 'AIDA' AND karyawan.kode_cabang ='KLT',detailretur.subtotal,0)) as returaidaKLT,
            SUM(IF(master_barang.jenis_produk = 'SWAN',detailretur.subtotal,0)) as totalreturswan,
            SUM(IF(master_barang.jenis_produk = 'AIDA',detailretur.subtotal,0)) as totalreturaida
            ");
        }

        $qretur->join('barang', 'detailretur.kode_barang', '=', 'barang.kode_barang');
        $qretur->join('master_barang', 'barang.kode_produk', '=', 'master_barang.kode_produk');
        $qretur->join('retur', 'detailretur.no_retur_penj', '=', 'retur.no_retur_penj');
        $qretur->join('penjualan', 'retur.no_fak_penj', '=', 'penjualan.no_fak_penj');
        $qretur->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        $qretur->whereBetween('tglretur', [$dari, $sampai]);
        $qretur->where('jenis_retur', 'pf');
        $retur = $qretur->first();


        $qpiutang = Penjualan::query();
        if (!empty($kode_cabang)) {
            $qpiutang->selectRaw("
            SUM(ifnull(penjualan.total, 0) - ifnull(retur.total, 0) - ifnull(hblalu.jmlbayar, 0)) as totalpiutang
            ");
            $qpiutang->where('cabangbarunew', $kode_cabang);
        } else {
            $qpiutang->selectRaw("
            SUM(CASE WHEN cabangbarunew = 'TSM' THEN ifnull(penjualan.total, 0) - ifnull(retur.total, 0) - ifnull(hblalu.jmlbayar, 0) END) AS TSM,
            SUM(CASE WHEN cabangbarunew = 'BDG' THEN ifnull(penjualan.total, 0) - ifnull(retur.total, 0) - ifnull(hblalu.jmlbayar, 0) END) AS BDG,
            SUM(CASE WHEN cabangbarunew = 'SKB' THEN ifnull(penjualan.total, 0) - ifnull(retur.total, 0) - ifnull(hblalu.jmlbayar, 0) END) AS SKB,
            SUM(CASE WHEN cabangbarunew = 'TGL' THEN ifnull(penjualan.total, 0) - ifnull(retur.total, 0) - ifnull(hblalu.jmlbayar, 0) END) AS TGL,
            SUM(CASE WHEN cabangbarunew = 'BGR' THEN ifnull(penjualan.total, 0) - ifnull(retur.total, 0) - ifnull(hblalu.jmlbayar, 0) END) AS BGR,
            SUM(CASE WHEN cabangbarunew = 'PWT' THEN ifnull(penjualan.total, 0) - ifnull(retur.total, 0) - ifnull(hblalu.jmlbayar, 0) END) AS PWT,
            SUM(CASE WHEN cabangbarunew = 'PST' THEN ifnull(penjualan.total, 0) - ifnull(retur.total, 0) - ifnull(hblalu.jmlbayar, 0) END) AS PST,
            SUM(CASE WHEN cabangbarunew = 'GRT' THEN ifnull(penjualan.total, 0) - ifnull(retur.total, 0) - ifnull(hblalu.jmlbayar, 0) END) AS GRT,
            SUM(CASE WHEN cabangbarunew = 'SBY' THEN ifnull(penjualan.total, 0) - ifnull(retur.total, 0) - ifnull(hblalu.jmlbayar, 0) END) AS SBY,
            SUM(CASE WHEN cabangbarunew = 'SMR' THEN ifnull(penjualan.total, 0) - ifnull(retur.total, 0) - ifnull(hblalu.jmlbayar, 0) END) AS SMR,
            SUM(CASE WHEN cabangbarunew = 'KLT' THEN ifnull(penjualan.total, 0) - ifnull(retur.total, 0) - ifnull(hblalu.jmlbayar, 0) END) AS KLT,
            SUM(ifnull(penjualan.total, 0) - ifnull(retur.total, 0) - ifnull(hblalu.jmlbayar, 0)) as totalpiutang
        ");
        }

        $qpiutang->leftJoin(
            DB::raw("(
                SELECT pj.no_fak_penj,
                IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
                IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
                FROM penjualan pj
                INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                LEFT JOIN (
                    SELECT MAX(id_move) as id_move,no_fak_penj,move_faktur.id_karyawan as salesbaru,karyawan.kode_cabang as cabangbaru
                    FROM move_faktur
                    INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                    WHERE tgl_move <= '$sampai'
                    GROUP BY no_fak_penj,move_faktur.id_karyawan,karyawan.kode_cabang
                ) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
            ) pjmove"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'pjmove.no_fak_penj');
            }
        );
        $qpiutang->leftJoin(
            DB::raw("(
                SELECT
                    historibayar.no_fak_penj AS no_fak_penj,
                    sum(historibayar.bayar) AS jmlbayar
                FROM
                    historibayar
                WHERE
                    historibayar.tglbayar <= '$sampai'
                GROUP BY
                    historibayar.no_fak_penj
            ) hblalu"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'hblalu.no_fak_penj');
            }
        );
        $qpiutang->leftJoin(
            DB::raw("(
                SELECT
                    retur.no_fak_penj AS no_fak_penj,
                    sum(retur.total) AS total
                FROM
                    retur
                WHERE
                    retur.tglretur <= '$sampai'
                GROUP BY
                    retur.no_fak_penj
            ) retur"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
            }
        );

        $qpiutang->where('penjualan.jenisbayar', '!=', 'tunai');
        $qpiutang->where('tgltransaksi', '<=', $sampai);
        $qpiutang->whereRaw('ifnull(penjualan.total, 0) - ifnull(retur.total, 0) <> ifnull(hblalu.jmlbayar, 0)');
        $qpiutang->whereRaw('to_days("' . $sampai . '") - to_days(penjualan.tgltransaksi) > 31');
        $qpiutang->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        $piutang = $qpiutang->first();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Cost Ratio.xls");
        }
        if (!empty($request->kode_cabang)) {
            $cabang = Cabang::where('kode_cabang', $request->kode_cabang)->first();
            return view('laporanaccounting.laporan.cetak_costratio_cabang', compact('dari', 'sampai', 'biaya', 'penjualan', 'retur', 'piutang', 'cabang'));
        } else {
            return view('laporanaccounting.laporan.cetak_costratio', compact('dari', 'sampai', 'biaya', 'penjualan', 'retur', 'piutang'));
        }
    }
}
