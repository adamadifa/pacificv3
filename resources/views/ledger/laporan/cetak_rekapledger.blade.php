<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Rekap Ledger {{ $bank != null ? $bank->nama_bank : 'All Ledger' }} {{ date('d-m-y') }}</title>
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
        REKAP {{ $bank != null ? 'LEDGER ' . $bank->nama_bank : 'ALL LEDGER' }}
        <br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
    </b>
    <br>
    <table class="datatable3">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>Kode Akun</th>
                <th>Nama Akun</th>
                <th>Debet</th>
                <th>Kredit</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totaldebet = 0;
                $totalkredit = 0;
            @endphp
            @foreach ($ledger as $d)
                @php
                    $totaldebet += $d->totaldebet;
                    $totalkredit += $d->totalkredit;
                @endphp
                <tr>
                    <td>{{ "'" . $d->kode_akun }}</td>
                    <td>{{ $d->nama_akun }}</td>
                    <td style="text-align: right">{{ !empty($d->totaldebet) ? rupiah($d->totaldebet) : '' }}</td>
                    <td style="text-align: right">{{ !empty($d->totalkredit) ? rupiah($d->totalkredit) : '' }}</td>
                </tr>
            @endforeach
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th colspan="2">TOTAL</th>
                <th style="text-align: right">{{ rupiah($totaldebet) }}</th>
                <th style="text-align: right">{{ rupiah($totalkredit) }}</th>
            </tr>
        </tbody>
    </table>
</body>

</html>
