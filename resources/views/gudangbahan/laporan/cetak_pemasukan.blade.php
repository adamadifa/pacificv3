<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Pemasukan Barang Gudang Bahan {{ date("d-m-y") }}</title>
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

        tr:nth-child(even) {
            background-color: #d6d6d6c2;
        }

    </style>

    </style>
</head>
<body>
    <b style="font-size:14px;">
        REKAP BARANG MASUK<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        @if ($barang != null)
        {{ $barang->nama_barang }}
        @endif
    </b>
    <br>
    <table class="datatable3" style="width:100%" border="1" style="font-size: 14">
        <thead>
            <tr>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;">TANGGAL</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;">BUKTI</th>
                <th colspan="7" bgcolor="#28a745" style="color:white; font-size:14;">BARANG MASUK</th>
            </tr>
            <tr bgcolor="red">
                <th style="color:white; font-size:14;" bgcolor="red">NAMA BARANG</th>
                <th style="color:white; font-size:14;" bgcolor="red">SATUAN</th>
                <th style="color:white; font-size:14;" bgcolor="red">KETERANGAN</th>
                <th style="color:white; font-size:14;" bgcolor="red">QTY UNIT</th>
                <th style="color:white; font-size:14;" bgcolor="red">QTY BERAT</th>
                <th style="color:white; font-size:14;" bgcolor="red">QTY LEBIH</th>
            </tr>
        </thead>
        <tbody>
            <?php
    $qty_berat  = 0;
    $qty_unit   = 0;
    $qty_lebih  = 0;
    foreach ($pemasukan as $key => $d) {
      $qty_unit      = $qty_unit + $d->qty_unit;
      $qty_berat     = $qty_berat + $d->qty_berat;
      $qty_lebih     = $qty_lebih + $d->qty_lebih;
    ?>
            <tr style="font-size: 14;">
                <td><?php echo DateToIndo2($d->tgl_pemasukan); ?></td>
                <td><?php echo $d->nobukti_pemasukan; ?></td>
                <td><?php echo $d->nama_barang; ?></td>
                <td><?php echo $d->satuan; ?></td>
                <td><?php echo $d->keterangan; ?></td>
                <td align="right"><?php echo desimal($d->qty_unit); ?></td>
                <td align="right"><?php echo desimal($d->qty_berat, 2); ?></td>
                <td align="right"><?php echo desimal($d->qty_lebih, 2); ?></td>
            </tr>
            <?php
    }
    ?>
        </tbody>
        <tfoot bgcolor="#024a75" style="color:white; font-size:14;">
            <tr>
                <th style="color:white; font-size:14;" colspan="5">TOTAL</th>
                <th style="color:white; font-size:14;"><?php echo desimal($qty_unit); ?></th>
                <th style="color:white; font-size:14;"><?php echo desimal($qty_berat, 2); ?></th>
                <th style="color:white; font-size:14;"><?php echo desimal($qty_lebih, 2); ?></th>
            </tr>
        </tfoot>
    </table>


</body>
</html>
