<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Kontrabon {{ date("d-m-y") }}</title>
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
        REKAP KONTRABON<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:70%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12; text-align:center">
                <td>NO</td>
                <td>NO KONTRABON</td>
                <td>NAMA SUPPLIER</td>
                <td>KET</td>
                <td>JUMLAH</td>
                <td>NO REKENING</td>
        </thead>
        <tbody style="font-size:12px">
            <?php $no = 1;
          $total = 0;
          foreach ($pf as $k) {
            $total = $total + $k->jumlah; ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td><?php echo $k->no_dokumen; ?></td>
                <td><?php echo $k->nama_supplier; ?></td>
                <td>
                    <?php
                if ($k->ppn == 1) {
                  echo "FP";
                }
                ?>
                </td>
                <td align="right"><?php echo desimal($k->jumlah); ?></td>
                <td align="center"><?php echo $k->norekening; ?></td>
            </tr>
            <?php $no++;
          } ?>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td colspan="4">TOTAL</td>
                <td align="right"><?php echo desimal($total); ?></th>
                <td></td>
            </tr>
        </tbody>
    </table>
    <br>
    <br>
    <table class="datatable3" style="width:70%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12; text-align:center">
                <td>NO</td>
                <td>NO KONTRABON</td>
                <td>NAMA SUPPLIER</td>
                <td>KET</td>
                <td>JUMLAH</td>
                <td>NO REKENING</td>
        </thead>
        <tbody style="font-size:12px">
            <?php $no = 1;
            $total = 0;
            foreach ($kb as $k) {
            $total = $total + $k->jumlah; ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td><?php echo $k->no_dokumen; ?></td>
                <td><?php echo $k->nama_supplier; ?></td>
                <td>
                    <?php
                if ($k->ppn == 1) {
                echo "FP";
                }
                ?>
                </td>
                <td align="right"><?php echo desimal($k->jumlah); ?></td>
                <td align="center"><?php echo $k->norekening; ?></td>
            </tr>
            <?php $no++;
            } ?>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td colspan="4">TOTAL</td>
                <td align="right"><?php echo desimal($total); ?></th>
                <td></td>
            </tr>
        </tbody>
    </table>

</body>
</html>
