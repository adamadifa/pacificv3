<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Pembelian {{ date("d-m-y") }}</title>
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
        REKAP PEMBELIAN<br>
        @if (!empty($jenis_barang))
        {{ strtoupper($jenis_barang) }}
        <br>
        @endif
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:70%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12; text-align:center">
                <td>NO</td>
                <td>NO BUKTI</td>
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
            $total  = 0;
            $no = 1;
            foreach ($pmb as $key => $d) {
                $totalharga = ($d->harga * $d->qty) + $d->penyesuaian;
                $subtotal = $d->harga * $d->qty;
                if ($d->ppn == '1') {
                    $cekppn  =  "&#10004;";
                    $bgcolor = "#ececc8";
                    $dpp     = (100 / 110) * $totalharga;
                    $ppn     = 10 / 100 * $dpp;
                } else {
                    $bgcolor = "";
                    $cekppn  = "";
                    $dpp     = "";
                    $ppn     = "";
                }

                $grandtotal 	= $totalharga;
                $total 			= $total + $grandtotal;
            ?>
            <tr style="background-color:<?php echo $bgcolor; ?>">
                <td><?php echo $no; ?></td>
                <td><?php echo $d->nobukti_pembelian; ?></td>
                <td><?php echo $d->kode_supplier; ?></td>
                <td><?php echo $d->nama_supplier; ?></td>
                <td><?php echo $d->nama_barang; ?></td>
                <td align="right"><?php echo desimal($d->qty); ?></td>
                <td align="right"><?php echo desimal($d->harga); ?></td>
                <td align="right"><?php echo desimal($subtotal); ?></td>
                <td align="right"><?php echo desimal($d->penyesuaian); ?></td>
                <td align="right"><?php echo desimal($totalharga); ?></td>
            </tr>
            <?php
                $no++;
            }
            ?>
            <tr bgcolor="#024a75" style="color:white">
                <td colspan="9"><b>TOTAL</b></td>
                <td align="right"><b><?php echo desimal($total); ?></b></td>
            </tr>
        </tbody>

    </table>
</body>
</html>
