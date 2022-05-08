<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Pengeluaran {{ date("d-m-y") }}</title>
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
        REKAPITULASI PENGELUARAN GUDANG JADI<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:80%" border="1">
        <thead>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>NO</th>
                <th>JENIS PRODUK</th>
                <th style="text-align: center">MINGGU 1 <br> TGL 01 - 07</th>
                <th style="text-align: center">MINGGU 2 <br> TGL 08 - 14</th>
                <th style="text-align: center">MINGGU 3 <br> TGL 15 - 21</th>
                <th style="text-align: center">MINGGU 4 <br> TGL 22 - 31</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $no=1;
                foreach($mutasi as $r){

                    $total = $r->minggu1 + $r->minggu2 + $r->minggu3 + $r->minggu4;
            ?>
            <tr style=" background-color: #91d7d7; font-weight: bold; font-size:11px">
                <td><?php echo $no; ?></td>
                <td><?php echo $r->nama_barang; ?></td>
                <td align="right"><?php if($r->minggu1 !=0){echo rupiah($r->minggu1); } ?></td>
                <td align="right"><?php if($r->minggu2 !=0){echo rupiah($r->minggu2); } ?></td>
                <td align="right"><?php if($r->minggu3 !=0){echo rupiah($r->minggu3); } ?></td>
                <td align="right"><?php if($r->minggu4 !=0){echo rupiah($r->minggu4); } ?></td>
                <td align="right"><?php echo rupiah($total);  ?></td>
            </tr>
            <?php
                $no++;
                }
            ?>
        </tbody>
    </table>
</body>
</html>
