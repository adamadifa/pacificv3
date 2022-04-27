<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Pengeluaran Barang Produksi {{ date("d-m-y") }}</title>
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
        REKAP BARANG KELUAR<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
    </b>
    <br>

    <table class="datatable3" style="width:100%" border="1">
        <thead>
            <tr>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;">NO</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;">TANGGAL</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;">BUKTI</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;">JENIS PENGELUARAN</th>
                <th colspan="7" bgcolor="#28a745" style="color:white; font-size:14;">BARANG KELUAR</th>
            </tr>
            <tr bgcolor="#024a75">
                <th style="color:white; font-size:14;">NAMA BARANG</th>
                <th style="color:white; font-size:14;">SATUAN</th>
                <th style="color:white; font-size:14;">KETERANGAN</th>
                <th style="color:white; font-size:14;">QTY</th>
            </tr>
        </thead>
        <tbody>
            <?php
          $qty    = 0;
          $no     = 1;
          foreach ($pengeluaran as $key => $d) {
            $qty = $qty + $d->qty;
          ?>
            <tr style="font-size: 14">
                <td><?php echo $no++; ?></td>
                <td><?php echo DateToIndo2($d->tgl_pengeluaran); ?></td>
                <td><?php echo $d->nobukti_pengeluaran; ?></td>
                <td><?php echo $d->kode_dept; ?></td>
                <td><?php echo $d->nama_barang; ?></td>
                <td><?php echo $d->satuan; ?></td>
                <td><?php echo $d->keterangan; ?></td>
                <td align="center"><?php echo number_format($d->qty, 2); ?></td>
            </tr>
            <?php
          }
          ?>
        </tbody>
        <tfoot bgcolor="#024a75" style="color:white; font-size:14;">
            <tr>
                <th style="color:white; font-size:14;" colspan="7">TOTAL</th>
                <th style="color:white; font-size:14;"><?php echo number_format($qty, 2); ?></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
