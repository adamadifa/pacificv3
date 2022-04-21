<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Pembayaran Pembelian {{ date("d-m-y") }}</title>
    <style>
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
        REKAP BAHAN KEMASAN<br>
        NAMA BARANG : <span style="color:red">{{ strtoupper($barang->nama_barang) }}</span>
        <br>
        @if ($supplier != null)
        SUPPLIER : {{ $supplier->nama_supplier }}
        @else
        ALL SUPPLIER
        @endif
        <br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:70%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12; text-align:center">
                <td>NO</td>
                <td>NO BUKTI</td>
                <td>TANGGAL</td>
                <td>KODE SUPPLIER</td>
                <td>NAMA SUPPLIER</td>
                <td>NAMA BARANG</td>
                <td>QTY</td>
                <td>HARGA</td>
                <td>SUBTOTAL</td>
                <td>PENYESUAIAN</td>
                <td>TOTAL</td>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $totalqty = 0;
            $totalsubtotal = 0;
            $grandtotal = 0;
            $totalpenyesuaian = 0;

            $totalqtysub = 0;
            $totalsubtotalsub = 0;
            $grandtotalsub = 0;
            $totalpenyesuaiansub = 0;
          foreach ($pmb as $key => $d) {
            $totalqty = $totalqty + $d->qty;
            $totalsubtotal = $totalsubtotal + ($d->qty * $d->harga);
            $grandtotal = $grandtotal + ($d->qty * $d->harga) + $d->penyesuaian;
            $totalpenyesuaian = $totalpenyesuaian + $d->penyesuaian;

            $totalqtysub = $totalqtysub + $d->qty;
            $totalsubtotalsub = $totalsubtotalsub + ($d->qty * $d->harga);
            $grandtotalsub = $grandtotalsub + ($d->qty * $d->harga) + $d->penyesuaian;
            $totalpenyesuaiansub = $totalpenyesuaiansub + $d->penyesuaian;
            $supplier  = @$pmb[$key + 1]->kode_supplier;
        ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td><?php echo $d->nobukti_pembelian; ?></td>
                <td><?php echo $d->tgl_pembelian; ?></td>
                <td><?php echo $d->kode_supplier; ?></td>
                <td><?php echo $d->nama_supplier; ?></td>
                <td><?php echo $d->nama_barang; ?></td>
                <td align="right"><?php echo desimal($d->qty); ?></td>
                <td align="right"><?php echo desimal($d->harga); ?></td>
                <td align="right"><?php echo desimal($d->harga * $d->qty); ?></td>
                <td align="right"><?php echo desimal($d->penyesuaian); ?></td>
                <td align="right"><?php echo desimal(($d->harga * $d->qty) + $d->penyesuaian); ?></td>
            </tr>
            <?php
            if ($supplier != $d->kode_supplier) {
            echo '
                <tr bgcolor="#199291" style="color:white; font-weight:bold">
                    <td colspan="6">TOTAL</td>
                    <td align="right">' . desimal($totalqtysub) . '</td>
                    <td></td>
                    <td align="right">' . desimal($totalsubtotalsub) . '</td>
                    <td align="right">' . desimal($totalpenyesuaiansub) . '</td>
                    <td align="right">' . desimal($grandtotalsub) . '</td>
                </tr>';
                $totalqtysub    = 0;
                $totalsubtotalsub    = 0;
                $totalpenyesuaiansub  = 0;
                $grandtotalsub = 0;
            }

            ?>
            <?php $no++;
            }
            ?>
        </tbody>
        <tfooter bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12; text-align:center">
                <td colspan="6">TOTAL</td>
                <td align="right"><?php echo desimal($totalqty); ?></td>
                <td></td>
                <td align="right"><?php echo desimal($totalsubtotal); ?></td>
                <td align="right"><?php echo desimal($totalpenyesuaian); ?></td>
                <td align="right"><?php echo desimal($grandtotal); ?></td>
            </tr>
        </tfooter>
    </table>
</body>
</html>
