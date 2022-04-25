<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Rekap Omset Pelanggan</title>
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
        @if ($cabang!=null)
        @if ($cabang->kode_cabang=="PST")
        PACIFIC PUSAT
        @else
        PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
        @endif
        @else
        PACIFC ALL CABANG
        @endif
        <br>
        LAPORAN REKAP OMSET PELANGGAN<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>

    </b>
    <table class="datatable3" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th rowspan="2">NO</th>
                <th rowspan="2">KODE PELANGGAN</th>
                <th rowspan="2">NAMA PELANGGAN</th>
                <th rowspan="2">NAMA KARYAWAN</th>
                <th rowspan="2">PASAR</th>
                <th rowspan="2">TOTAL OMSET</th>
                <th rowspan="2">RATA RATA OMSET</th>
                <th colspan="2">RATA RATA OMSET KATEGORI PRODUK</th>

            </tr>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>AIDA</th>
                <th>SWAN</th>
            </tr>
        </thead>
        <tbody style="font-size:14px">
            <?php
            $totalomset =  0;
            $totalratarataomset = 0;
            $totalratarataswan = 0;
            $totalratarataaida =0;
            $no =1; foreach($rekapomsetpelanggan as $r){
                $totalomset += $r->netpenjualan;
                $totalratarataomset += ($r->netpenjualan / $periode);
                $totalratarataswan += ($r->netswan / $periode);
                $totalratarataaida += ($r->netaida / $periode);
            ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td><?php echo $r->kode_pelanggan; ?></td>
                <td><?php echo $r->nama_pelanggan; ?></td>
                <td><?php echo $r->nama_karyawan; ?></td>
                <td><?php echo $r->pasar; ?></td>
                <td align="right"><?php echo rupiah($r->netpenjualan); ?></td>
                <td align="right"><?php echo rupiah($r->netpenjualan / $periode); ?></td>
                <td align="right"><?php echo rupiah($r->netaida / $periode); ?></td>
                <td align="right"><?php echo rupiah($r->netswan / $periode); ?></td>
            </tr>
            <?php $no++;} ?>
            <tr>
                <th colspan="5" align="right">TOTAL</th>
                <th style="text-align:right"><?php echo rupiah($totalomset); ?></th>
                <th style="text-align:right"><?php echo rupiah($totalratarataomset); ?></th>
                <th style="text-align:right"><?php echo rupiah($totalratarataaida); ?></th>
                <th style="text-align:right"><?php echo rupiah($totalratarataswan); ?></th>

            </tr>
        </tbody>
    </table>
</body>
</html>
