<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Tunai Transfer</title>
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
        <br>
        LAPORAN TUNAI TRANSFER<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        @if ($salesman != null)
        SALESMAN {{ strtoupper($salesman->nama_karyawan) }}
        @else
        SEMUA SALESMAN
        @endif
        <br />
    </b>
    <table class="datatable3" style="width:80%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>No.</th>
                <th>No. Faktur</th>
                <th>Tanggal</th>
                <th>Kode Pelanggan</th>
                <th>Nama Pelanggan</th>
                <th>Salesman</th>
                <th>Total</th>
                <th>Retur</th>
                <th>Netto</th>
                <th>Pembayaran</th>
                <th>Sisa Bayar</th>
            </tr>
        </thead>
        <tbody>
            @php
            $total = 0;
            $totalretur = 0;
            $totalbayar = 0;
            $totalnetto = 0;
            $totalsisabayar = 0;
            @endphp
            @foreach ($tunaitransfer as $d)
            @php
            $total += $d->total;
            $totalretur += $d->totalretur;
            $totalbayar += $d->totalbayar;
            $totalnetto = $d->total - $d->totalretur;
            $totalsisabayar = $d->total - $d->totalretur - $d->totalbayar;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->no_fak_penj }}</td>
                <td>{{ DateToIndo2($d->tgltransaksi) }}</td>
                <td>{{ $d->kode_pelanggan }}</td>
                <td>{{ $d->nama_pelanggan }}</td>
                <td>{{ $d->nama_karyawan }}</td>
                <td align="right">{{ rupiah($d->total) }}</td>
                <td align="right">{{ rupiah($d->totalretur) }}</td>
                <td align="right">{{ rupiah($d->total - $d->totalretur) }}</td>
                <td align="right">{{ rupiah($d->totalbayar) }}</td>
                <td align="right">{{ rupiah($d->total - $d->totalretur - $d->totalbayar) }}</td>
            </tr>
            @endforeach
            <tr>
                <th colspan="6"></th>
                <th style="text-align: right">{{ rupiah($total) }}</th>
                <th style="text-align: right">{{ rupiah($totalretur) }}</th>
                <th style="text-align: right">{{ rupiah($totalnetto) }}</th>
                <th style="text-align: right">{{ rupiah($totalbayar) }}</th>
                <th style="text-align: right">{{ rupiah($totalsisabayar) }}</th>
            </tr>
        </tbody>
    </table>
</body>
</html>
