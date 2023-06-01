<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak kasbon {{ date("d-m-y") }}</title>
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

        LAPORAN kasbon<br>
        BULAN {{ strtoupper($namabulan[$bulan]) }} TAHUN {{ $tahun }}
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
                <th colspan="2">PEMBAYARAN</th>
                <th rowspan="2">SALDO AKHIR</th>
            </tr>
            <tr>
                <th>kasbon</th>
                <th>LAIN LAIN</th>
                <th>CICILAN</th>
                <th>LAIN LAIN</th>
            </tr>
        </thead>

        <tbody>

            @php
            $totalsaldoawal = 0;
            $totalpenambahan = 0;
            $totalpembayaran = 0;
            $totalsaldoakhir = 0;
            @endphp

            @foreach ($kasbon as $d)
            @php
            $jumlah_kasbonlast = $d->jumlah_kasbonlast;
            $jumlah_pelunasanlast = $d->total_pelunasanlast;
            $jumlah_pembayaranlast = $d->total_pembayaranlast;

            $jumlah_kasbonnow = $d->jumlah_kasbonnow;
            $jumlah_pembayarannow = $d->total_pembayarannow;
            $jumlah_pelunasannow = $d->total_pelunasannow;

            $saldoawal = $jumlah_kasbonlast - $jumlah_pembayaranlast - $jumlah_pelunasanlast ;


            $totalpembayarannow = $jumlah_pembayarannow + $jumlah_pelunasannow;
            $saldoakhir = $saldoawal + $jumlah_kasbonnow - $totalpembayarannow ;

            $totalsaldoawal += $saldoawal;
            $totalsaldoakhir += $saldoakhir;
            $totalpembayaran += $totalpembayarannow;
            $totalpenambahan += $jumlah_kasbonnow;

            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->nik }}</td>
                <td>{{ $d->nama_karyawan }}</td>
                <td style="text-align: right">{{ !empty($saldoawal) ?  rupiah($saldoawal) : '' }}</td>
                <td style="text-align: right">{{ !empty($jumlah_kasbonnow) ?  rupiah($jumlah_kasbonnow) : '' }}</td>
                <td></td>
                <td style="text-align: right">{{ !empty($totalpembayarannow) ?  rupiah($totalpembayarannow) : '' }}</td>
                <td></td>
                <td style="text-align: right">{{ !empty($saldoakhir) ?  rupiah($saldoakhir) : '' }}</td>
            </tr>
            @endforeach
            <tr bgcolor=" #024a75" style=" color:white; font-size:12;">
                <th colspan="3">TOTAL</th>
                <th style="text-align: right">{{ rupiah($totalsaldoawal) }}</th>
                <th style="text-align: right">{{ rupiah($totalpenambahan) }}</th>
                <th></th>
                <th style="text-align: right">{{ rupiah($totalpembayaran) }}</th>
                <th></th>
                <th style="text-align: right">{{ rupiah($totalsaldoakhir) }}</th>
            </tr>
        </tbody>
    </table>
</body>
