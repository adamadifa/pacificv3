<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Pembayaran Pembelian {{ date('d-m-y') }}</title>
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
        KARTU HUTANG<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        @if ($supplier != null)
            SUPPLIER : {{ $supplier->nama_supplier }}
        @else
            ALL SUPPLIER
        @endif
        <br>
        @if ($coa != null)
            JENIS HUTANG : {{ $coa->kode_akun }} {{ $coa->nama_akun }}
        @endif
        <br>
    </b>
    <br>

    <table class="datatable3" style="width:100%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12; text-align:center">
                <td>NO</td>
                <td>TGL</td>
                <td>NO BUKTI</td>
                <td>KATEGORI</td>
                <td>SUPPLIER</td>
                <td>AKUN</td>
                <!-- <td>TOTAL HUTANG</td>
                <td>BAYAR BULAN LALU</td>
                <td>PENY BULAN LALU</td> -->
                <td>SALDO AWAL</td>
                <td>PEMBELIAN</td>
                <td>PENYESUAIAN</td>
                <td>PEMBAYARAN</td>
                <td>SALDO AKHIR</td>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalsaldoawal = 0;
            $totalsaldoakhir = 0;
            $totalpembelian = 0;
            $totalpenyesuaian = 0;
            $totalpembayaran = 0;
            $no = 1;
            foreach ($pmb as $d) {
                if ($d->tgl_pembelian < $dari) {
                    $saldoawal = $d->sisapiutang;
                } else {
                    $saldoawal = 0;
                }
                $saldoakhir = $d->totalhutang - $d->jmlbayarbulanlalu - $d->jmlbayarbulanini;
                $totalsaldoawal += $saldoawal;
                $totalsaldoakhir += $saldoakhir;
                $totalpembelian += $d->pmbbulanini;
                $totalpenyesuaian += $d->penyesuaianbulanini;
                $totalpembayaran += $d->jmlbayarbulanini;
            ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td><?php echo DateToIndo2($d->tgl_pembelian); ?></td>
                <td><?php echo $d->nobukti_pembelian; ?></td>
                <td><?php echo $d->kategori_transaksi; ?></td>
                <td><?php echo $d->nama_supplier; ?></td>
                <td><?php echo $d->nama_akun; ?></td>
                <!-- <td><?php echo $d->totalhutang; ?></td>
                    <td><?php echo $d->jmlbayarbulanlalu; ?></td>

                    <td><?php echo $d->penyesuaianbulanlalu; ?></td> -->
                <td align="right"><?php if (!empty($saldoawal)) {
                    echo desimal($saldoawal);
                } ?></td>
                <td align="right"><?php if (!empty($d->pmbbulanini)) {
                    echo desimal($d->pmbbulanini);
                } ?></td>
                <td align="right"><?php if (!empty($d->penyesuaianbulanini)) {
                    echo desimal($d->penyesuaianbulanini);
                } ?></td>
                <td align="right"><?php if (!empty($d->jmlbayarbulanini)) {
                    echo desimal($d->jmlbayarbulanini);
                } ?></td>
                <td align="right"><?php if (!empty($saldoakhir)) {
                    echo desimal($saldoakhir);
                } ?></td>
            </tr>
            <?php $no++;
            } ?>

            <tr bgcolor="#024a75" style="color:white; font-size:12; font-weight:bold">
                <td colspan="6"><b>TOTAL</b></td>
                <td align="right"><?php if (!empty($totalsaldoawal)) {
                    echo desimal($totalsaldoawal);
                } ?></td>
                <td align="right"><?php if (!empty($totalpembelian)) {
                    echo desimal($totalpembelian);
                } ?></td>
                <td align="right"><?php if (!empty($totalpenyesuaian)) {
                    echo desimal($totalpenyesuaian);
                } ?></td>
                <td align="right"><?php if (!empty($totalpembayaran)) {
                    echo desimal($totalpembayaran);
                } ?></td>
                <td align="right"><?php if (!empty($totalsaldoakhir)) {
                    echo desimal($totalsaldoakhir);
                } ?></td>
            </tr>
        </tbody>

    </table>
</body>

</html>
