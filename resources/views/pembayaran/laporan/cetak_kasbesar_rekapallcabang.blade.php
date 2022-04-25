<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Rekap Kas Besar All Cabang</title>
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
        PACIFC ALL CABANG
        <br>
        LAPORAN REKAP KAS BESAR PENJUALAN<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:40%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>CABANG</th>
                <th>Cash IN</th>
                <th>Voucher</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
			$totalvoucher = 0;
			$totalcashin =  0;
			foreach ($kasbesar as $r) {
				$totalcashin = $totalcashin + $r->cashin;
				$totalvoucher = $totalvoucher + $r->voucher ?>
            <tr style="font-size:12">
                <td style="font-weight:bold"><?php echo strtoUpper($r->nama_cabang); ?></td>
                <td style="text-align:right; font-weight:bold"><?php echo rupiah($r->cashin); ?></td>
                <td style="text-align:right; font-weight:bold"><?php echo rupiah($r->voucher); ?></td>
                <td style="text-align:right; font-weight:bold"><?php echo rupiah($r->voucher + $r->cashin); ?></td>
            </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td style="font-weight:bold">TOTAL</td>
                <td style="text-align:right; font-weight:bold"><?php echo rupiah($totalcashin); ?></td>
                <td style="text-align:right; font-weight:bold"><?php echo rupiah($totalvoucher); ?></td>
                <td style="text-align:right; font-weight:bold"><?php echo rupiah($totalvoucher + $totalcashin); ?></td>

            </tr>
        </tfoot>
    </table>
</body>
</html>
