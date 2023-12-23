<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan BBM</title>
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
        LAPORAN BBM<br>
        PERIODE BULAN {{ $bulan }} TAHUN {{ $tahun }}<br>
        {{ $cabang != '' ? 'CABANG ' . $cabang : 'SEMUA CABANG' }}
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:100%" border="1">
        <thead>
            <tr>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;">NO</th>
                <th rowspan="2"bgcolor="#024a75" style="color:white; font-size:14;">TANGGAL</th>
                <th rowspan="2"bgcolor="#024a75" style="color:white; font-size:14;">NAMA DRIVER</th>
                <th rowspan="2"bgcolor="#024a75" style="color:white; font-size:14;">TUJUAN</th>
                <th colspan="2" bgcolor="#024a75" style="color:white; font-size:14;">POSISI KILO METER</th>
                <th rowspan="2"bgcolor="#024a75" style="color:white; font-size:14;">JUMLAH METER</th>
                <th rowspan="2"bgcolor="#024a75" style="color:white; font-size:14;">JARAK TEMPUH (KM)</th>
                <th rowspan="2"bgcolor="#024a75" style="color:white; font-size:14;">RATA RATA / LITER</th>
                <th rowspan="2"bgcolor="#024a75" style="color:white; font-size:14;">KTERANGAN</th>
            </tr>
            <tr>
                <th bgcolor="#024a75" style="color:white; font-size:14;">AWAL</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">AKHIR</th>
            </tr>
        </thead>
        <tbody>
            @php
                $jumlah_liter = 0;
                $jarak_tempuh = 0;
            @endphp
            @foreach ($bbm as $v)
                @php
                    if ($v->jumlah_liter != 0) {
                        $jumlah_liter = $v->jumlah_liter;
                    } else {
                        $jumlah_liter = 0;
                    }
                    $jumlah_liter += $jumlah_liter;
                    $jarak_tempuh += $v->saldo_akhir - $v->saldo_awal;

                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $v->tanggal }}</td>
                    <td>{{ $v->nama_driver_helper }}</td>
                    <td>{{ $v->tujuan }}</td>
                    <td align="right">{{ number_format($v->saldo_awal, 2) }}</td>
                    <td align="right">{{ number_format($v->saldo_akhir, 2) }}</td>
                    <td align="right">{{ number_format($jumlah_liter, 2) }}</td>
                    <td align="right">{{ number_format($v->saldo_akhir - $v->saldo_awal, 2) }}</td>
                    @if ($jumlah_liter != 0)
                        <td align="right">{{ number_format($v->saldo_akhir - $jumlah_liter, 2) }}
                        </td>
                    @else
                        <td align="right">0</td>
                    @endif
                    <td>{{ $v->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr bgcolor="#31869b">
                <th colspan="6" style="color:white; font-size:14;">TOTAL</th>
                <th style="color:white; font-size:14;text-align:right">{{ rupiah($jumlah_liter, 2) }}</th>
                <th style="color:white; font-size:14;text-align:right">{{ rupiah($jarak_tempuh, 2) }}</th>
                @if ($jumlah_liter != 0)
                    <th style="color:white; font-size:14;text-align:right">
                        {{ rupiah($jarak_tempuh / $jumlah_liter, 2) }}
                        </td>
                    @else
                    <th style="color:white; font-size:14;text-align:right">
                        0
                        </td>
                @endif
                </th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
