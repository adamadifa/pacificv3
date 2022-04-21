<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Jurnal Koreksi {{ date("d-m-y") }}</title>
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
        LAPORAN JURNAL KOREKSI<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
    </b>
    <br>

    <table class="datatable3" style="width:100%" border="1">
        <thead>
            <tr>
                <th width="20px" bgcolor="#024a75" style="color:white; font-size:12;">No</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">TGL</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">No Bukti</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">Supplier</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">Nama Barang</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">Keterangan</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">Kode Akun</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">Nama Akun</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">Qty</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">Harga</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">Total</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">Debet</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">Kredit</th>
            </tr>
        </thead>
        <tbody>
            <?php
          $no = 1;
          $total       = 0;
          $totalkredit = 0;
          $totaldebet  = 0;
          foreach ($jurnalkoreksi as $j) {
            $total       = $total + $j->harga * $j->qty;
            if ($j->status_dk == 'D') {
              $totalkredit =  $totalkredit + $j->harga * $j->qty;
            }
            if ($j->status_dk == 'K') {
              $totaldebet =  $totaldebet + $j->harga * $j->qty;
            }
          ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td><?php echo $j->tgl_jurnalkoreksi; ?></td>
                <td><?php echo $j->nobukti_pembelian; ?></td>
                <td><?php echo $j->nama_supplier; ?></td>
                <td><?php echo $j->nama_barang; ?></td>
                <td><?php echo $j->keterangan; ?></td>
                <td><?php echo $j->kode_akun; ?></td>
                <td><?php echo $j->nama_akun; ?></td>
                <td align="right"><?php echo  number_format($j->qty, '2', ',', '.'); ?></td>
                <td align="right"><?php echo  number_format($j->harga, '2', ',', '.'); ?></td>
                <td align="right"><?php echo  number_format($j->harga * $j->qty, '2', ',', '.'); ?></td>
                <td align="right">
                    <?php
                if ($j->status_dk == 'D') {
                  echo  number_format($j->harga * $j->qty, '2', ',', '.');
                }
                ?>
                </td>
                <td align="right">
                    <?php
                if ($j->status_dk == 'K') {
                  echo  number_format($j->harga * $j->qty, '2', ',', '.');
                }
                ?>
                </td>
            </tr>

            <?php
            $no++;
          }
          ?>
            <tr>
                <th bgcolor="#024a75" style="color:white; font-size:12;" colspan="10" align="center">Total</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;" align="right"><?php echo  number_format($total, '2', ',', '.'); ?></th>
                <th bgcolor="#024a75" style="color:white; font-size:12;" align="right"><?php echo  number_format($totaldebet, '2', ',', '.'); ?></th>
                <th bgcolor="#024a75" style="color:white; font-size:12;" align="right"><?php echo  number_format($totalkredit, '2', ',', '.'); ?></th>
            </tr>
        </tbody>
    </table>
</body>
</html>
