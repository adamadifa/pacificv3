<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Pembayaran Pembelian {{ date("d-m-y") }}</title>
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
        REKAP PEMBELIAN SUPPLIER<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:70%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12; text-align:center">
                <td>NO</td>
                <td>KODE SUPPLIER</td>
                <td>NAMA SUPPLIER</td>
                <td>DEBET</td>
                <td>KREDIT</td>
            </tr>
        </thead>
        <tbody>
            <?php
                $total  = 0;
                $no 		= 1;
                foreach ($pmb as $key => $d) {
                    $total = $total + $d->jumlah;
            ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td><?php echo $d->kode_supplier; ?></td>
                <td><?php echo $d->nama_supplier; ?></td>
                <td align="right"><?php echo desimal($d->jumlah); ?></td>
                <td align="right"><?php echo desimal($d->jumlah); ?></td>
            </tr>
            <?php
                $no++;
            }
            ?>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td colspan="3"><b>TOTAL</b></td>
                <td align="right"><b><?php echo desimal($total); ?></b></td>
                <td align="right"><b><?php echo desimal($total); ?></b></td>
            </tr>
        </tbody>

    </table>

</body>
</html>
