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
        @if ($cabang != null)
            @if ($cabang->kode_cabang == 'PST')
                PACIFIC PUSAT
            @else
                PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
            @endif
        @else
            PACIFC ALL CABANG
        @endif
        <br>
        LAPORAN REKAP OMSET PELANGGAN<br>
        @if ($karyawan != null)
            SALESMAN : {{ $karyawan->nama_karyawan }}<br>
        @endif
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>

    </b>
    <table class="datatable3" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th rowspan="2">NO</th>
                <th rowspan="2">KODE PELANGGAN</th>
                <th rowspan="2">NAMA PELANGGAN</th>
                <th rowspan="2">PASAR</th>
                <th rowspan="2">TOTAL OMSET</th>

                <th rowspan="2">SALESMAN</th>
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

                <td><?php echo $r->pasar; ?></td>
                <td align="right"><?php echo rupiah($r->netpenjualan); ?></td>

                <td>{{ $r->nama_karyawan }}</td>
            </tr>
            <?php $no++;} ?>
            <tr>
                <th colspan="4" align="right">TOTAL</th>
                <th style="text-align:right"><?php echo rupiah($totalomset); ?></th>


            </tr>
        </tbody>
    </table>
</body>

</html>
