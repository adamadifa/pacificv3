<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Pengeluaran Barang Gudang Logistik {{ date("d-m-y") }}</title>
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
        REKAP BARANG KELUAR<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        @if ($departemen != null)
        DEPARTEMEN {{ $departemen->nama_dept }}
        @endif
        <br>
        @if ($cabang != null)
        CABANG {{ $cabang->nama_cabang }}
        @endif
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
    <table class="datatable3" style="width:100%; zoom:80%" border="1">
        <thead>
            <tr>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;">NO</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;">TANGGAL</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;">BUKTI</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;">DEPARTEMEN</th>
                <th colspan="7" bgcolor="#28a745" style="color:white; font-size:14;">BARANG KELUAR</th>
            </tr>
            <tr bgcolor="red">
                <th style="color:white; font-size:14;" bgcolor="red">KODE BARANG</th>
                <th style="color:white; font-size:14;" bgcolor="red">NAMA BARANG</th>
                <th style="color:white; font-size:14;" bgcolor="red">SATUAN</th>
                <th style="color:white; font-size:14;" bgcolor="red">KETERANGAN</th>
                <th style="color:white; font-size:14;" bgcolor="red">CABANG</th>
                <th style="color:white; font-size:14;" bgcolor="red">QTY</th>
            </tr>
        </thead>
        <tbody>
            <?php
          $total = 0;
          $qty = 0;
          $no    = 1;
          foreach ($pengeluaran as $d) {
            $qty    = $qty + $d->qty;
          ?>
            <tr style="font-size: 14px">
                <td><?php echo $no++; ?></td>
                <td><?php echo DateToIndo2($d->tgl_pengeluaran); ?></td>
                <td><?php echo $d->nobukti_pengeluaran; ?></td>
                <td><?php echo $d->nama_dept; ?></td>
                <td><?php echo $d->kode_barang; ?></td>
                <td><?php echo $d->nama_barang; ?></td>
                <td><?php echo $d->satuan; ?></td>
                <td><?php echo $d->keterangan; ?></td>
                <td><?php echo $d->nama_cabang; ?></td>
                <td align="center"><?php echo desimal($d->qty); ?></td>
            </tr>
            <?php
          }
          ?>
            <tr bgcolor="#024a75" style="color:white; text-align: center;font-size: 16px">
                <td colspan="9">TOTAL</td>
                <td align="center"><?php echo desimal($qty); ?></td>
            </tr>
        </tbody>
    </table>

</body>
</html>
