<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Pemasukan Barang Gudang Logistik {{ date("d-m-y") }}</title>
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
        @if ($kategori != null)
        KATEGORI {{ $kategori->kategori }}
        @endif
        <br>
        @if ($barang != null)
        {{ $barang->nama_barang }}
        @endif
    </b>
    <br>
    <table class="datatable3" style="width:100%; size: A4;zoom:80%" border="1">
        <thead>
            <tr>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;">NO</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;">TANGGAL</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;">SUPPLIER</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;">BUKTI</th>
                <th colspan="8" bgcolor="#28a745" style="color:white; font-size:14;">BARANG MASUK</th>
            </tr>
            <tr bgcolor="red">
                <th style="color:white; font-size:14;" bgcolor="red">NAMA BARANG</th>
                <th style="color:white; font-size:14;" bgcolor="red">SATUAN</th>
                <th style="color:white; font-size:14;" bgcolor="red">KETERANGAN</th>
                <th style="color:white; font-size:14;" bgcolor="red">AKUN</th>
                <th style="color:white; font-size:14;" bgcolor="red">HARGA</th>
                <th style="color:white; font-size:14;" bgcolor="red">QTY</th>
                <th style="color:white; font-size:14;" bgcolor="red">PENYESUAIAN</th>
                <th style="color:white; font-size:14;" bgcolor="red">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php
    $no = 1;
    $harga = 0;
    $qty = 0;
    foreach ($pemasukan as $key => $d) {
      $harga   = $harga + $d->qty * $d->harga + $d->penyesuaian;
      $qty     = $qty + $d->qty;
    ?>
            <tr style="font-size: 14">
                <td><?php echo $no++; ?></td>
                <td><?php echo DateToIndo2($d->tgl_pemasukan); ?></td>
                <td><?php echo $d->nama_supplier; ?></td>
                <td><?php echo $d->nobukti_pemasukan; ?></td>
                <td><?php echo $d->nama_barang; ?></td>
                <td><?php echo $d->satuan; ?></td>
                <td><?php echo $d->keterangan; ?></td>
                <td><?php echo $d->kode_akun; ?> <?php echo $d->nama_akun; ?></td>
                <td align="right"><?php echo desimal($d->harga); ?></td>
                <td align="center"><?php echo desimal($d->qty); ?></td>
                <td align="right"><?php echo desimal($d->penyesuaian); ?></td>
                <td align="right"><?php echo desimal($d->qty * $d->harga + $d->penyesuaian); ?></td>
            </tr>
            <?php
    }
    ?>
            <tr bgcolor="#024a75" style="color:white; text-align: center;font-size: 16px">
                <td colspan="9" bgcolor="#024a75">TOTAL</td>
                <td align="center" bgcolor="#024a75"><?php echo desimal($qty); ?></td>
                <td bgcolor="#024a75"></td>
                <td align="right" bgcolor="#024a75"><?php echo desimal($harga); ?></td>
            </tr>
        </tbody>
    </table>

</body>
</html>
