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
        BULAN {{ strtoupper($namabulan[$bulan]) }} TAHUN {{ $tahun }}
    </b>
    <br>
    <table class="datatable3">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th rowspan="3">NO</th>
                <th rowspan="3">NIK</th>
                <th rowspan="3">NAMA KARYAWAN</th>
                <th rowspan="3">SALDO AWAL</th>
                <th colspan="2">PENAMBAHAN</th>
                <th colspan="4">PEMBAYARAN</th>
                <th rowspan="3">SALDO AKHIR</th>
            </tr>
            <tr>
                <th rowspan="2">PINJAMAN</th>
                <th rowspan="2">LAIN LAIN</th>
                <th colspan="3">CICILAN</th>
                <th rowspan="2">LAIN LAIN</th>
            </tr>
            <tr>
                <th>GAJI</th>
                <th>POT. KOMISI</th>
                <th>TITIPAN</th>
            </tr>
        </thead>

        <tbody>

            @php
            $totalsaldoawal = 0;
            $totalpenambahan = 0;
            $totalpembayaran = 0;
            $totalsaldoakhir = 0;
            $totalpmbnow = 0;
            $totalpotongkomisi = 0;
            $totaltitipan = 0;
            $totalplnow = 0;
            $no = 1;
            @endphp

            @foreach ($pinjaman as $d)
            @php
            $jumlah_pinjamanlast = $d->jumlah_pinjamanlast;
            $jumlah_pelunasanlast = $d->total_pelunasanlast;
            $jumlah_pembayaranlast = $d->total_pembayaranlast;

            $jumlah_pinjamannow = $d->jumlah_pinjamannow;
            $jumlah_pembayarannow = $d->total_pembayarannow + $d->total_pembayaranpotongkomisi + $d->total_pembayarantitipan ;
            $jumlah_pembayaranpotongkomisi = $d->total_pembayaranpotongkomisi;
            $jumlah_pembayarantitipan = $d->total_pembayarantitipan;
            $jumlah_pelunasannow = $d->total_pelunasannow;

            $saldoawal = $jumlah_pinjamanlast - $jumlah_pembayaranlast - $jumlah_pelunasanlast ;


            $totalpembayarannow = $jumlah_pembayarannow + $jumlah_pelunasannow ;
            $totalpmbnow += $jumlah_pembayarannow;
            $totalplnow += $jumlah_pelunasannow;

            $totalpotongkomisi += $jumlah_pembayaranpotongkomisi;
            $totaltitipan += $jumlah_pembayarantitipan;

            $saldoakhir = $saldoawal + $jumlah_pinjamannow - $totalpembayarannow ;

            $totalsaldoawal += $saldoawal;
            $totalsaldoakhir += $saldoakhir;
            $totalpembayaran += $totalpembayarannow;
            $totalpenambahan += $jumlah_pinjamannow;

            @endphp
            @if (!empty($saldoawal) || !empty($jumlah_pinjamannow))
            <tr>
                <td>{{ $no }}</td>
                <td>{{ "'".$d->nik }}</td>
                <td>{{ $d->nama_karyawan }}</td>
                <td style="text-align: right">{{ !empty($saldoawal) ?  rupiah($saldoawal) : '' }}</td>
                <td style="text-align: right">{{ !empty($jumlah_pinjamannow) ?  rupiah($jumlah_pinjamannow) : '' }}</td>
                <td></td>
                <td style="text-align: right">{{ !empty($d->total_pembayarannow) ?  rupiah($d->total_pembayarannow) : '' }}</td>
                <td style="text-align: right">{{ !empty($jumlah_pembayaranpotongkomisi) ?  rupiah($jumlah_pembayaranpotongkomisi) : '' }}</td>
                <td style="text-align: right">{{ !empty($jumlah_pembayarantitipan) ?  rupiah($jumlah_pembayarantitipan) : '' }}</td>
                <td></td>
                <td style="text-align: right">{{ !empty($saldoakhir) ?  rupiah($saldoakhir) : '' }}</td>
            </tr>
            @php
            $no++;
            @endphp
            @endif

            @endforeach
            <tr bgcolor=" #024a75" style=" color:white; font-size:12;">
                <th colspan="3">TOTAL</th>
                <th style="text-align: right">{{ rupiah($totalsaldoawal) }}</th>
                <th style="text-align: right">{{ rupiah($totalpenambahan) }}</th>
                <th></th>
                <th style="text-align: right">{{ rupiah($totalpmbnow) }}</th>
                <th style="text-align: right">{{ rupiah($totalpotongkomisi) }}</th>
                <th style="text-align: right">{{ rupiah($totaltitipan) }}</th>
                <th></th>
                <th style="text-align: right">{{ rupiah($totalsaldoakhir) }}</th>
            </tr>
        </tbody>
    </table>
</body>
