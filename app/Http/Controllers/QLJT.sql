SELECT salesbarunew,IFNULL(SUM(sa.jumlah),0) + IFNULL(SUM(IF(tgltransaksi BETWEEN '$dari' AND '$sampai',penjualan.total,0)),0)  -  SUM(IFNULL(totalretur,0)) - SUM(IFNULL(totalbayar,0)) as sisapiutang
FROM penjualan
LEFT JOIN (
    SELECT pj.no_fak_penj,
    IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
    IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
    FROM penjualan pj
    INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
    LEFT JOIN (
        SELECT
        id_move,no_fak_penj,
        move_faktur.id_karyawan as salesbaru,
        karyawan.kode_cabang  as cabangbaru
        FROM move_faktur
        INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
        WHERE id_move IN (SELECT max(id_move) FROM move_faktur WHERE tgl_move <= '$sampai' GROUP BY no_fak_penj)
    ) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
) pjmove ON (penjualan.no_fak_penj = pjmove.no_fak_penj)

LEFT JOIN (
    SELECT retur.no_fak_penj AS no_fak_penj,
		SUM(IFNULL(subtotal_pf,0) - IFNULL(subtotal_gb,0)) as totalretur
    FROM
    retur
    WHERE tglretur BETWEEN '$dari' AND '$sampai'
    GROUP BY
    retur.no_fak_penj
) r ON (penjualan.no_fak_penj = r.no_fak_penj)

LEFT JOIN (
    SELECT no_fak_penj,sum( historibayar.bayar ) AS totalbayar
    FROM historibayar
    WHERE tglbayar BETWEEN '$dari' AND '$sampai'
    GROUP BY no_fak_penj
    ) hb ON (penjualan.no_fak_penj = hb.no_fak_penj)


LEFT JOIN (
	SELECT no_fak_penj,jumlah
	FROM saldoawal_piutang_faktur
	WHERE bulan = '1' AND tahun='2023'
) sa ON (penjualan.no_fak_penj = sa.no_fak_penj)

WHERE sa.jumlah IS NOT NULL AND jenistransaksi="kredit" AND datediff('$sampai', penjualan.tgltransaksi) > 15
OR  penjualan.tgltransaksi BETWEEN '$dari' AND '$sampai' AND jenistransaksi = "kredit" AND datediff('$sampai', penjualan.tgltransaksi) > 15
GROUP BY salesbarunew
