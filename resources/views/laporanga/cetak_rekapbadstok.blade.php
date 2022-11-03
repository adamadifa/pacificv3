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
        @if ($cabang!=null)
        @if ($cabang->kode_cabang=="PST")
        PACIFIC PUSAT
        @else
        PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
        @endif
        @else
        PACIFC ALL CABANG
        @endif

    </b>
    <br>

    <table class="datatable3">
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Produk</th>
                <th colspan="31">Bulan {{ $bulan }}</th>
            </tr>
            <tr>
                @for ($i = 1; $i <= 31; $i++) <th>{{ $i }}</th> @endfor
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
                @for ($i = 1; $i <=31; $i++) @php $tgl="tgl_" .$i; @endphp <td>{{ !empty($d->$tgl) ? $d->$tgl : ''   }}</td>
                    @endfor
            </tr>
            @php
            $no++;
            @endphp
            @endforeach
        </tbody>
    </table>
</body>
</html>
