<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Tunai Kredit {{ date("d-m-y") }}</title>
    <style>
        body {
            font-family: 'Poppins'
        }

        .datatable3 {
            border: 2px solid #D6DDE6;
            border-collapse: collapse;
            font-size: 14px;
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
    <table class="datatable3" style="font-size:14px">
        <tr>
            <td>Kode Klaim</td>
            <td>:</td>
            <td><b>{{ $klaim->kode_klaim }}</b></td>
        </tr>
        <tr>
            <td>Tanggal Klaim</td>
            <td>:</td>
            <td><b>{{ date("d-m-Y",strtotime($klaim->tgl_klaim)) }}</b></td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td>:</td>
            <td><b>{{ ucwords(strtolower($klaim->keterangan)) }}</b></td>
        </tr>

    </table>
    <table class="datatable3" style="font-size:14px">
        <thead bgcolor="#024a75" style="color:white; font-size:14px;">
            <tr>
                <th>TGL</th>
                <th>No Bukti</th>
                <th>Keterangan</th>
                <th>Penerimaan</th>
                <th>Pengeluaran</th>
                <th>Saldo</th>
            </tr>
            <tr>
                <th>SALDO AWAL</th>
                <th colspan="4"></th>
                <th style="text-align:right">
                    <?php
                    if (!empty($saldoawal)) {
                        echo number_format($saldoawal, '0', '', '.');
                    }
                    ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
                $saldo = $saldoawal;
                $totalpenerimaan  = 0;
                $totalpengeluaran = 0;
                $totalpenerimaan2 = 0;
                foreach ($detail as $d) {
                    if ($d->status_dk == 'K') {
                        $penerimaan   = $d->jumlah;
                        $s = $penerimaan;
                        $pengeluaran  = 0;
                    } else {
                        $penerimaan = 0;
                        $pengeluaran = $d->jumlah;
                        $s = -$pengeluaran;
                    }
                    $saldo = $saldo + $s;
                    $totalpenerimaan = $totalpenerimaan + $penerimaan;
                    $totalpengeluaran = $totalpengeluaran + $pengeluaran;
                    if ($d->keterangan != 'Penerimaan Kas Kecil') {
                        $totalpenerimaan2 = $totalpenerimaan2 + $penerimaan;
                    }
            ?>
            <tr>
                <td><?php echo $d->tgl_kaskecil; ?></td>
                <td><?php echo $d->nobukti; ?></td>
                <td><?php echo $d->keterangan; ?></td>
                <td align="right" style="color:green"><?php if (!empty($penerimaan)) {echo number_format($penerimaan, '0', '', '.');} ?></td>
                <td align="right" style="color:red"><?php if (!empty($pengeluaran)) {echo number_format($pengeluaran, '0', '', '.');} ?></td>
                <td align="right"><?php if (!empty($saldo)) {echo number_format($saldo, '0', '', '.');} ?></td>
            </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">TOTAL</th>
                <td align="right" style="color:green"><b><?php if (!empty($totalpenerimaan)) {echo number_format($totalpenerimaan, '0', '', '.');} ?></b></td>
                <td align="right" style="color:red"><b><?php if (!empty($totalpengeluaran)) { echo number_format($totalpengeluaran, '0', '', '.');} ?></b></td>
                <td align="right"><b><?php if (!empty($saldo)) {echo number_format($saldo, '0', '', '.');} ?></b></td>
            </tr>
            <tr>
                <td class="bg-blue text-white">Penggantian Kas Kecil</td>
                <?php
                if ($klaim->kode_cabang == 'PST') {
                    $penggantian = $totalpengeluaran - $totalpenerimaan2;
                } else {
                    $penggantian = $totalpengeluaran - $totalpenerimaan2;
                }
                ?>
                <td colspan="2" align="right"><b><?php if (!empty($penggantian)) {echo number_format($penggantian, '0', '', '.');} ?></b></td>
                <td class="bg-blue text-white">Saldo Awal</td>
                <td colspan="2" align="right"><b><?php if (!empty($saldoawal['jumlah'])) {echo number_format($saldoawal['jumlah'], '0', '', '.');} ?></b></td>
            </tr>
            <tr>
                <td class="bg-blue text-white">Terbilang</td>
                <td colspan="2" align="right"><b><?php echo ucwords(terbilang($penggantian)); ?></b></td>
                <td class="bg-blue text-white">Penerimaan Pusat</td>
                <td colspan="2" align="right"><b><?php if (!empty($totalpenerimaan)) {echo number_format($totalpenerimaan, '0', '', '.');} ?></b></td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2"></td>
                <td class="bg-blue text-white">Total</td>
                <td colspan="2" align="right"><b><?php if (!empty($saldoawal + $totalpenerimaan - $totalpenerimaan2)) {echo number_format($saldoawal + $totalpenerimaan - $totalpenerimaan2, '0', '', '.');} ?></b></td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2"></td>
                <td class="bg-blue text-white">Pengeluaran Kas Kecil</td>
                <td colspan="2" align="right"><b><?php if (!empty($totalpengeluaran)) {echo number_format($totalpengeluaran, '0', '', '.');} ?></b></td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2"></td>
                <td class="bg-blue text-white">Saldo Akhir</td>
                <td colspan="2" align="right"><b><?php if (!empty($saldo)) {echo rupiah($saldo);} ?></b></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
