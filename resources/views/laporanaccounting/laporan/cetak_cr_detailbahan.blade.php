<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cost Ratio {{ date("d-m-y") }}</title>
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

        .table-scroll {
            position: relative;
            max-width: 100%;
            margin: auto;
            overflow: hidden;

        }

        .table-wrap {
            width: 100%;
            overflow: auto;
        }

        .table-scroll table {
            width: 100%;
            margin: auto;
            border-collapse: separate;
            border-spacing: 0;
        }


        .clone {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
        }

        .clone th,
        .clone td {
            visibility: hidden
        }

        .clone td,
        .clone th {
            border-color: transparent
        }

        .clone tbody th {
            visibility: visible;
            color: red;
        }

        .clone .fixed-side {
            border: 1px solid #000;
            background: #eee;
            visibility: visible;
        }

    </style>
</head>
<body>
    <b style="font-size:14px;">
        DETAIL PENGGUNAKAN BAHAN KEMASAN {{ strtoupper($cabang->nama_cabang) }}<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }} <br>
    </b>
    <br>
    <table class="datatable3" style="width:40%" border="1">
        <thead>
            <tr>
                <th style="background-color:rgb(0, 52, 93); color:white">No</th>
                <th style="background-color:rgb(0, 52, 93); color:white">Kode Barang</th>
                <th style="background-color:rgb(0, 52, 93); color:white">Nama Barang</th>
                <th style="background-color:rgb(0, 52, 93); color:white">Qty</th>
                <th style="background-color:rgb(0, 52, 93); color:white">Harga</th>
                <th style="background-color:rgb(0, 52, 93); color:white">Total</th>
            </tr>
        </thead>
        <tbody>
            @php
            $total = 0;
            @endphp
            @foreach ($bahan as $d)
            @php
            $total += $d->total;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->kode_barang }}</td>
                <td>{{ $d->nama_barang }}</td>
                <td style="text-align:center">{{ $d->qty }}</td>
                <td align="right">{{ rupiah($d->harga) }}</td>
                <td align="right">{{ rupiah($d->total) }}</td>
            </tr>
            @endforeach
            <tr>
                <th colspan="5">Total</th>
                <th style="text-align: right">{{ rupiah($total) }}</th>
            </tr>
        </tbody>
    </table>

</body>
</html>
