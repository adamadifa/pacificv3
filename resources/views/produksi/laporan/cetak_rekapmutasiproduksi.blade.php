<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Akun {{ date("d-m-y") }}</title>
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
        REKAP MUTASI PRODUKSI<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:80%" border="1">
        <thead>
            <tr>

                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">No</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">Barang/Produk</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">Saldo Awal</th>
                <th colspan="2" bgcolor="#28a745" style="color:white; font-size:12;">IN</th>
                <th colspan="2" bgcolor="#c7473a" style="color:white; font-size:12;">OUT</th>
                <th rowspan="2" rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">SALDO AKHIR</th>
            </tr>
            <tr>
                <th bgcolor="#28a745" style="color:white; font-size:12; width:200px">BARANG HASIL PRODUKSI</th>
                <th bgcolor="#28a745" style="color:white; font-size:12;">LAINNYA</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">GUDANG</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">LAINNYA</th>
            </tr>

        </thead>
        <tbody>
            <?php

                $no=1;
                $totalsaldoawal  = 0;
                $totalbpbj		 = 0;
                $totalfsthp 	 = 0;
                $totalmutasiin   = 0;
                $totalmutasiout  = 0;
                $totalsaldoakhir = 0;
                foreach ($mutasi as $m){

                $saldoakhir 		= $m->saldoawal + ($m->jmlbpbj + $m->mutasi_in) - ($m->jmlfsthp + $m->mutasi_out);
                $totalsaldoawal		= $totalsaldoawal + $m->saldoawal;
                $totalbpbj 			= $totalbpbj + $m->jmlbpbj;
                $totalfsthp 		= $totalfsthp + $m->jmlfsthp;
                $totalmutasiin 		= $totalmutasiin + $m->mutasi_in;
                $totalmutasiout 	= $totalmutasiout + $m->mutasi_out;
                $totalsaldoakhir 	= $totalsaldoakhir + $saldoakhir;

            ?>
            <tr style="font-weight: bold; font-size:11px">
                <td><?php echo $no; ?></td>
                <td><?php echo $m->nama_barang; ?></td>
                <td align="right"><?php if($m->saldoawal !=0){echo rupiah($m->saldoawal); } ?></td>
                <td align="right"><?php if($m->jmlbpbj !=0){echo rupiah($m->jmlbpbj); } ?></td>
                <td align="right"><?php if($m->mutasi_in !=0){echo rupiah($m->mutasi_in); } ?></td>
                <td align="right"><?php if($m->jmlfsthp !=0){echo rupiah($m->jmlfsthp); } ?></td>
                <td align="right"><?php if($m->mutasi_out !=0){echo rupiah($m->mutasi_out); } ?></td>
                <td align="right"><?php echo rupiah($saldoakhir);  ?></td>
            </tr>
            <?php $no++; } ?>
        </tbody>
        <tfoot bgcolor="#024a75" style="color:white; font-size:12;">
            <tr>
                <th colspan="2">TOTAL</th>
                <th style="text-align: right"><?php echo rupiah($totalsaldoawal); ?></th>
                <th style="text-align: right"><?php echo rupiah($totalbpbj); ?></th>
                <th style="text-align: right"><?php echo rupiah($totalmutasiin); ?></th>
                <th style="text-align: right"><?php echo rupiah($totalfsthp); ?></th>
                <th style="text-align: right"><?php echo rupiah($totalmutasiout); ?></th>
                <th style="text-align: right"><?php echo rupiah($totalsaldoakhir); ?></th>
            </tr>
        </tfoot>
    </table>

</body>
</html>
