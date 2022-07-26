<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Setoran Ke Bank {{ date("d-m-y") }}</title>
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
        RINCIAN COST RATIO BIAYA
        <br>

        @if ($cabang->kode_cabang=="PST")
        PACIFIC PUSAT
        @else
        PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
        @endif
        <br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
    </b>
    <br>
    <table class="datatable3" border="1">
        <thead style="font-size:12;">
            <tr style="font-size:12;">
                <th class="text-center">No</th>
                <th>Tanggal</th>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
                <th>Keterangan</th>
                <th>Jumlah</th>
                <th>Sumber</th>
                <th>Cabang</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($costratio as $d)
            @if ($d->id_sumber_costratio==1)
            @php
            $color = "bg-info";
            @endphp
            @elseif($d->id_sumber_costratio==2)
            @php
            $color="bg-success";
            @endphp
            @elseif($d->id_sumber_costratio==4)
            @php
            $color="bg-danger";
            @endphp
            @else
            @php
            $color="";
            @endphp
            @endif
            <tr class="{{ $color }}">
                <td>{{ $loop->iteration }}</td>
                <td>{{ date("d-m-Y",strtotime($d->tgl_transaksi)) }}</td>
                <td>{{ $d->kode_akun }}</td>
                <td>{{ $d->nama_akun }}</td>
                <td style="width: 40%">{{ ucwords(strtolower($d->keterangan)) }}</td>
                <td class="text-right">{{ rupiah($d->jumlah) }}</td>
                <td>{{ $d->nama_sumber }}</td>
                <td>{{ $d->kode_cabang }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
