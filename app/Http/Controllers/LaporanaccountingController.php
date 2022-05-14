<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use Illuminate\Http\Request;
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
                WHERE status ='GS' AND bulan ='$bulan' AND tahun='$tahun'
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
                WHERE status ='GS' AND bulan ='$bulan' AND tahun='$tahun'
            ) saldo_gs ON (dmc.kode_produk = saldo_gs.kode_produk AND dmc.kode_cabang = saldo_gs.kode_cabang)
            ORDER BY kode_cabang,kode_produk");
        } else {
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
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));

        $dari_akun = $request->dari_akun;
        $sampai_akun = $request->sampai_akun;

        $bukubesar = DB::table('buku_besar')
            ->select('buku_besar.*', 'nama_akun', 'saldo_awal')
            ->join('coa', 'buku_besar.kode_akun', '=', 'coa.kode_akun')
            ->leftJoin(
                DB::raw("(
                    SELECT kode_akun,jumlah as saldo_awal
                    FROM detailsaldoawal_bb
                    INNER JOIN saldoawal_bb ON detailsaldoawal_bb.kode_saldoawal_bb = saldoawal_bb.kode_saldoawal_bb
                    WHERE bulan = '$bulan' AND tahun='$tahun'
                ) sa"),
                function ($join) {
                    $join->on('buku_besar.kode_akun', '=', 'sa.kode_akun');
                }
            )
            ->whereBetween('tanggal', [$dari, $sampai])
            ->whereBetween('buku_besar.kode_akun', [$dari_akun, $sampai_akun])
            ->orderBy('buku_besar.kode_akun')
            ->get();
        $dariakun = DB::table('coa')->where('kode_akun', $dari_akun)->first();
        $sampaiakun = DB::table('coa')->where('kode_akun', $sampai_akun)->first();
        return view('laporanaccounting.laporan.cetak_bukubesar', compact('dari', 'sampai', 'dariakun', 'sampaiakun', 'bukubesar', 'bulan', 'tahun'));
    }

    public function jurnalumum()
    {
        $departemen = DB::table('departemen')->where('status_pengajuan', 1)->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('laporanaccounting.laporan.frm.lap_jurnalumum', compact('bulan', 'departemen'));
    }

    public function cetak_jurnalumum(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $kode_dept = $request->kode_dept;

        $jurnalumum = DB::table('jurnal_umum')
            ->join('coa', 'jurnal_umum.kode_akun', '=', 'coa.kode_akun')
            ->whereBetween('tanggal', [$dari, $sampai])
            ->where('kode_dept', $kode_dept)
            ->orderBy('tanggal')
            ->get();
        $departemen = DB::table('departemen')->where('kode_dept', $kode_dept)->first();
        return view('laporanaccounting.laporan.cetak_jurnalumum', compact('dari', 'sampai', 'departemen', 'jurnalumum'));
    }
}