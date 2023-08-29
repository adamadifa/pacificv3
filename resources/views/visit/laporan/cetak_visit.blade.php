<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Visit Pelanggan </title>
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
        LAPORAN VISIT PELANGGAN<br>
        PERIODE BULAN {{ $bulan }} TAHUN {{ $tahun }}<br>
        {{ $cabang != '' ? 'CABANG ' . $cabang : 'SEMUA CABANG' }}
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:200%" border="1">
        <thead>
            <tr>
                <th bgcolor="#024a75" style="color:white; font-size:14;">NO</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">TGL VISIT</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">PELANGGAN</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">SALESMAN</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">ALAMAT/PASAR</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">TGL FAKTUR</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">NO FAKTUR</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">NILAI FAKTUR</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">JENIS TRANSAKSI</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">HASIL KONFIRMASI</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">NOTE</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">SARAN/KELUHAN PRODUK</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">ACTION KA ADMIN</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">CABANG</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($visit as $v)
                @php
                    $total += $v->nominal;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $v->tgl_visit }}</td>
                    <td>{{ $v->nama_pelanggan }}</td>
                    <td>{{ $v->nama_karyawan }}</td>
                    <td>{{ $v->pasar }}</td>
                    <td>{{ $v->tgltransaksi }}</td>
                    <td>{{ $v->no_fak_penj }}</td>
                    <td align="right">{{ rupiah($v->nominal) }}</td>
                    <td>{{ $v->jenistransaksi }}</td>
                    <td>{{ $v->hasil_konfirmasi }}</td>
                    <td>{{ $v->catatan }}</td>
                    <td>{{ $v->saran }}</td>
                    <td>{{ $v->action }}</td>
                    <td>{{ $v->kode_cabang }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr bgcolor="#31869b">
                <th colspan="7" style="color:white; font-size:14;">TOTAL</th>
                <th style="color:white; font-size:14;" align="right">{{ rupiah($total) }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
