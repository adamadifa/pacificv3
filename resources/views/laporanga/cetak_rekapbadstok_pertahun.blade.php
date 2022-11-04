<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Tunai Kredit {{ date("d-m-y") }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&display=swap');

        body {
            font-family: 'Poppins'
        }

        .datatable3 {
            border: 2px solid #D6DDE6;
            border-collapse: collapse;
            font-size: 14px;
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
        REKAP BAD STOK
        <br>
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
        TAHUN {{ $tahun }}
    </b>
    <br>
    <br>

    <table class="datatable3">
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Produk</th>
                <th colspan="12">Bulan {{ $tahun }}</th>
                <th rowspan="2">Total</th>
            </tr>
            <tr>
                <th>Jan</th>
                <th>Feb</th>
                <th>Mar</th>
                <th>Apr</th>
                <th>Mei</th>
                <th>Jun</th>
                <th>Jul</th>
                <th>Agu</th>
                <th>Sep</th>
                <th>Okt</th>
                <th>Nov</th>
                <th>Des</th>
            </tr>
        </thead>
        <tbody>
            @php
            $no = 1;
            @endphp
            @foreach ($badstok as $d)
            <tr>
                <td>{{ $no }}</td>
                <td>{{ $d->nama_barang }}</td>
                <td style="text-align: right">{{ !empty($d->jan) ? $d->jan : '' }}</td>
                <td style="text-align: right">{{ !empty($d->feb) ? $d->feb : '' }}</td>
                <td style="text-align: right">{{ !empty($d->mar) ? $d->mar : '' }}</td>
                <td style="text-align: right">{{ !empty($d->apr) ? $d->apr : '' }}</td>
                <td style="text-align: right">{{ !empty($d->mei) ? $d->mei : '' }}</td>
                <td style="text-align: right">{{ !empty($d->jun) ? $d->jun : '' }}</td>
                <td style="text-align: right">{{ !empty($d->jul) ? $d->jul : '' }}</td>
                <td style="text-align: right">{{ !empty($d->agu) ? $d->agu : '' }}</td>
                <td style="text-align: right">{{ !empty($d->sep) ? $d->sep : '' }}</td>
                <td style="text-align: right">{{ !empty($d->okt) ? $d->okt : '' }}</td>
                <td style="text-align: right">{{ !empty($d->nov) ? $d->nov : '' }}</td>
                <td style="text-align: right">{{ !empty($d->des) ? $d->des : '' }}</td>
                <td style="text-align: right">{{ rupiah($d->total) }}</td>
            </tr>
            @php
            $no++;
            @endphp
            @endforeach
        </tbody>
    </table>
</body>
</html>
