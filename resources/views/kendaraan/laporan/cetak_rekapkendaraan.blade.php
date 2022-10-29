<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Rekap Kendaraan</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&display=swap');

        body {
            font-family: 'Poppins'
        }

        .datatable3 {
            border: 2px solid #D6DDE6;
            border-collapse: collapse;
            font-size: 11px;
        }

        .datatable3 td {
            border: 1px solid #000000;
            padding: 6px;
        }

        .datatable3 th {
            border: 2px solid #828282;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }

    </style>
</head>
<body>
    <b style="font-size:14px;">
        @if ($cabang!=null)
        @if ($cabang->kode_cabang=="PST")
        PACIFIC PUSAT
        @else
        PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
        @endif
        @endif
        <br>
        REKAP PENJUALAN BERDASARKAN KENDARAAN<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        @if ($kendaraan != null)
        NO POLISI {{ strtoupper($kendaraan->no_polisi) }} / MODEL {{ strtoupper($kendaraan->merk) }} {{ $kendaraan->tipe_kendaraan }} {{ $kendaraan->tipe }}
        @endif
        <br />
    </b>
    <table class="datatable3">
        <thead bgcolor="#295ea9" style="color:white; font-size:16;">
            <tr bgcolor="#295ea9" style="color:white; font-size:16;">
                <td rowspan="2" align="center">KODE PRODUK</td>
                <td rowspan="2" align="center">PENGAMBILAN</td>
                <td colspan="5" align="center" style="background-color: #166511ab;">BARANG KELUAR</td>
                <td rowspan="2" align="center" style="background-color: #166511ab;">TOTAL</td>
                <td rowspan="2" align="center" style="background-color: #ef2121cf;">SISA</td>
            </tr>
            <tr style="background-color: #166511ab;">
                <td align="center">PENJUALAN</td>
                <td align="center">GANTI BARANG</td>
                <td align="center">PROMOSI</td>
                <td align="center">TTR</td>
                <td align="center">PL HUTANG KIRIM</td>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalpengambilan = 0;
            $totalpenjualan = 0;
            $totalgantibarang = 0;
            $totalpromosi  = 0;
            $totalttr = 0;
            $totalplhk = 0;
            $totalsisa = 0;
            $grandtotalbarangkeluar = 0;
            foreach ($rekapkendaraan as $d) {
                $totalbarangkeluar = ($d->jmlpenjualan + $d->jmlgantibarang + $d->jmlpromosi + $d->jmlttr + $d->jmlplhk) / $d->isipcsdus;
                $sisa = $d->jml_pengambilan - $totalbarangkeluar;
                $totalpenjualan += ($d->jmlpenjualan / $d->isipcsdus);
                $totalgantibarang += ($d->jmlgantibarang / $d->isipcsdus);
                $totalpromosi += ($d->jmlpromosi / $d->isipcsdus);
                $totalttr += ($d->jmlttr / $d->isipcsdus);
                $totalplhk += ($d->jmlttr / $d->isipcsdus);
                $totalpengambilan += $d->jml_pengambilan;
                $grandtotalbarangkeluar += $totalbarangkeluar;
                $totalsisa += $sisa;

            ?>
            <tr style="font-size: 14px;">
                <td><?php echo $d->kode_produk; ?></td>
                <td style="text-align:right"><?php echo desimal($d->jml_pengambilan); ?></td>
                <td style="text-align:right"><?php if (!empty($d->jmlpenjualan)) { echo desimal($d->jmlpenjualan / $d->isipcsdus);} ?></td>
                <td style="text-align:right"><?php if ($d->jmlgantibarang > 0) {echo desimal($d->jmlgantibarang / $d->isipcsdus);} ?></td>
                <td style="text-align:right"><?php if ($d->jmlpromosi > 0) {echo desimal($d->jmlpromosi / $d->isipcsdus);} ?></td>
                <td style="text-align:right"><?php if ($d->jmlttr > 0) {echo desimal($d->jmlttr / $d->isipcsdus);} ?></td>
                <td style="text-align:right"><?php if ($d->jmlplhk > 0) {echo desimal($d->jmlplhk / $d->isipcsdus);} ?></td>
                <td style="text-align:right"><?php if ($totalbarangkeluar > 0) {echo desimal($totalbarangkeluar);} ?></td>
                <td style="text-align:right"><?php if ($sisa > 0) {echo desimal($sisa); } ?></td>

            </tr>
            <?php } ?>
            <tr bgcolor="#295ea9" style="color:white; font-size:16;">
                <td>TOTAL</td>
                <td align="right"><?php echo desimal($totalpengambilan); ?></td>
                <td align="right"><?php echo desimal($totalpenjualan); ?></td>
                <td align="right"><?php echo desimal($totalgantibarang); ?></td>
                <td align="right"><?php echo desimal($totalpromosi); ?></td>
                <td align="right"><?php echo desimal($totalttr); ?></td>
                <td align="right"><?php echo desimal($totalplhk); ?></td>
                <td align="right"><?php echo desimal($grandtotalbarangkeluar); ?></td>
                <td align="right"><?php echo desimal($totalsisa); ?></td>
            </tr>
        </tbody>
    </table>
    <br>
    <br>
    <table>
        <tr>
            <td>
                <table class="datatable3">
                    <thead>
                        <tr bgcolor="#295ea9" style="color:white; font-size:16;">
                            <td colspan="2">HISTORI KEBERANGKATAN</td>
                        </tr>
                        <tr bgcolor="#295ea9" style="color:white; font-size:16;">
                            <td>TANGGAL</td>
                            <td>JML</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $jmlhari = 0;
                $totalpengambilan = 0;
                foreach ($historikendaraan as $h) {
                  $totalpengambilan += $h->jmlpengambilan;
                ?>
                        <tr style="font-size:14px">
                            <td><?php echo DateToIndo2($h->tgl_pengambilan); ?></td>
                            <td><?php echo $h->jmlpengambilan . " x Pengambilan" ?></td>
                        </tr>
                        <?php $jmlhari++;
                } ?>
                        <tr bgcolor="#295ea9" style="color:white; font-size:16;">
                            <td><?= $jmlhari ?></td>
                            <td><?= $totalpengambilan ?></td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td valign="top">
                <table class="datatable3">
                    <thead>
                        <tr bgcolor="#0b6ea9" style="color:white; font-size:16;">
                            <td colspan="2">RATA RATA BARANG KELUAR</td>
                        </tr>
                        <tr style="font-size:14">
                            <td style="background-color: #0b6ea9; color:white">TOTAL BARANG KELUAR</td>
                            <td align="right"><?php echo desimal($grandtotalbarangkeluar); ?></td>
                        </tr>
                        <tr style="font-size:14">
                            <td style="background-color: #0b6ea9; color:white">JUMLAH KEBERANGKATAN</td>
                            <td align="right"><?php echo $totalpengambilan; ?> x </td>
                        </tr>
                        <tr style="font-size:14">
                            <td style="background-color: #0b6ea9; color:white">RATA RATA</td>
                            <td align="right"><?php if(!empty($totalpengambilan)){ echo desimal($grandtotalbarangkeluar / $totalpengambilan);} ?></td>
                        </tr>
                    </thead>
                </table>
            </td>
            <td valign="top">
                <table class="datatable3">
                    <thead>
                        <tr bgcolor="#a94211" style="color:white; font-size:16;">
                            <td colspan="2">RATA RATA PENJUALAN</td>
                        </tr>
                        <tr style="font-size:14">
                            <td style="background-color: #a94211; color:white">TOTAL PENJUALAN</td>
                            <td align="right"><?php echo desimal($totalpenjualan); ?></td>
                        </tr>
                        <tr style="font-size:14">
                            <td style="background-color: #a94211; color:white">JUMLAH KEBERANGKATAN</td>
                            <td align="right"><?php echo $totalpengambilan; ?> x</td>
                        </tr>
                        <tr style="font-size:14">
                            <td style="background-color: #a94211; color:white">RATA RATA</td>
                            <td align="right"><?php if(!empty($totalpengambilan)) { echo desimal($totalpenjualan / $totalpengambilan);} ?></td>
                        </tr>
                    </thead>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
