<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Realisasi Program</title>
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

        tr:nth-child(even) {
            background-color: #d6d6d6c2;
        }
    </style>
</head>
@php

    if ($bulan == '1') {
        $bulan = 'JANUARI';
    } elseif ($bulan == '2') {
        $bulan = 'FEBRUARI';
    } elseif ($bulan == '3') {
        $bulan = 'MARET';
    } elseif ($bulan == '4') {
        $bulan = 'APRIL';
    } elseif ($bulan == '5') {
        $bulan = 'MEI';
    } elseif ($bulan == '6') {
        $bulan = 'JUNI';
    } elseif ($bulan == '7') {
        $bulan = 'JULI';
    } elseif ($bulan == '8') {
        $bulan = 'AGUSTUS';
    } elseif ($bulan == '9') {
        $bulan = 'SEPTEMBER';
    } elseif ($bulan == '10') {
        $bulan = 'OKTOBER';
    } elseif ($bulan == '11') {
        $bulan = 'NOVEMBER';
    } else {
        $bulan = 'DESEMBER';
    }
@endphp

<body>
    <b style="font-size:20px;">
        LAPORAN REALISASI PROGRAM<br>
        PERIODE BULAN {{ $bulan }} TAHUN {{ $tahun }}<br>
        {{ $cabang != '' ? 'CABANG ' . $cabang : 'SEMUA CABANG' }}
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:100%" border="1">
        <thead>
            <tr>
                <th bgcolor="#024a75" style="color:white; font-size:14;">NO</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">IM</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">AJUAN</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">KODE PELANGGAN</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">NAMA PELANGGAN</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">KETERANGAN</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">NOMINAL</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">BENTUK HADIAH</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">CABANG</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($realisasi as $v)
                @php
                    $total += $v->nominal;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $v->im }}</td>
                    <td>{{ $v->ajuan }}</td>
                    <td>{{ $v->kode_pelanggan }}</td>
                    <td>{{ $v->nama_pelanggan }}</td>
                    <td>{{ $v->keterangan }}</td>
                    <td align="right">{{ rupiah($v->nominal) }}</td>
                    <td>{{ $v->bentuk_hadiah }}</td>
                    <td>{{ $v->kode_cabang }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr bgcolor="#31869b">
                <th colspan="5" style="color:white; font-size:14;">TOTAL</th>
                <th style="color:white; font-size:14;text-align:right">{{ rupiah($total) }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
