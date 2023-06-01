<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Pinjaman {{ date("d-m-y") }}</title>
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

        .text-right: {
            text-align: right !important;
        }

    </style>
</head>
<body>
    <b style="font-size:14px;">
        @if ($kantor != null)
        @if ($kantor->kode_cabang=="PST")
        PACIFIC PUSAT
        @else
        PACIFIC CABANG {{ strtoupper($kantor->nama_cabang) }}
        @endif
        <br>
        @endif

        LAPORAN PINJAMAN<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
    </b>
    <br>
    <table class="datatable3">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th rowspan="2">NO</th>
                <th rowspan="2">NIK</th>
                <th rowspan="2">NAMA KARYAWAN</th>
                <th rowspan="2">SALDO AWAL</th>
                <th colspan="2">PENAMBAHAN</th>
                <th colspan="2">PENGURAN</th>
            </tr>
            <tr>
                <th>PINJAMAN</th>
                <th>LAIN LAIN</th>
                <th>CICILAN</th>
                <th>LAIN LAIN</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($pinjaman as $d)
            @php
            $jumlah_lastpinjaman = !empty($d->jumlah_lastpinjaman) ? $d->jumlah_lastpinjaman : 0;
            $jumlah_lastpembayaran = !empty($d->jumlah_lastpembayaran) ? $d->jumlah_lastpembayaran : 0;
            $saldoawal = $jumlah_lastpinjaman - $jumlah_lastpembayaran ;

            $jumlah_pinjamannow = !empty($d->jumlah_pinjamannow) ? $d->jumlah_pinjamannow : 0;
            $jumlah_pembayarannow = !empty($d->jumlah_pembayarannow) ? $d->jumlah_pembayarannow : 0;


            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->nik }}</td>
                <td>{{ $d->nama_karyawan }}</td>
                <td>{{ !empty($saldoawal) ?  rupiah($saldoawal) : '' }}</td>
                <td style="text-align: right">{{ !empty($jumlah_pinjamannow) ?  rupiah($jumlah_pinjamannow) : '' }}</td>
                <td></td>
                <td style="text-align: right">{{ !empty($jumlah_pembayarannow) ?  rupiah($jumlah_pembayarannow) : '' }}</td>
                <td></td>
            </tr>
            @endforeach

        </tbody>
    </table>
</body>
