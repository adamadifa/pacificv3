<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Kas Kecil {{ date("d-m-y") }}</title>
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
    <b>
        REKAP KAS KECIL<br>
        PERIODE <?php echo DateToIndo2($dari) . " s/d " . DateToIndo2($sampai); ?><br>
    </b>
    <table class="datatable3">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>KODE AKUN</th>
                <th>AKUN</th>
                <th>PENERIMAAN</th>
                <th>PENGELUARAN</th>
            </tr>
        </thead>
        <tbody>
            @php
            $totalkredit = 0;
            $totaldebet = 0;
            @endphp
            @foreach ($rekap as $d)
            @php
            $totalkredit += $d->totalpemasukan;
            $totaldebet += $d->totalpengeluaran;
            @endphp
            <tr>
                <td>{{ "'".$d->kode_akun }}</td>
                <td>{{ $d->nama_akun }}</td>
                <td style="text-align:right">{{ !empty($d->totalpemasukan) ? rupiah($d->totalpemasukan) : '' }}</td>
                <td style="text-align:right">{{ !empty($d->totalpengeluaran) ? rupiah($d->totalpengeluaran) : '' }}</td>
            </tr>
            @endforeach
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th colspan="2">TOTAL</th>
                <th style="text-align:right">{{ !empty($totalkredit) ? rupiah($totalkredit) : '' }}</th>
                <th style="text-align:right">{{ !empty($totaldebet) ? rupiah($totaldebet) : '' }}</th>
            </tr>
        </tbody>
    </table>
</body>
</html>
