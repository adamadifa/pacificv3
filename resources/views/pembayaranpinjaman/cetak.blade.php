<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Pembayaran Pinjaman {{ date("d-m-y") }}</title>
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
        LAPORAN PEMBAYARAN PINJAMAN<br>
        PERIODE PEMBAYARAN GAJI BULAN {{ $bln }} {{ $thn }}
    </b>
    <br>
    <table class="datatable3">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>No</th>
                <th>No. Bukti</th>
                <th>No. Pinjaman</th>
                <th>Nik</th>
                <th>Nama Karyawan</th>
                <th>Jabatan</th>
                <th>Departemen</th>
                <th>Saldo Awal</th>
                <th>Potong Gaji</th>
                <th>Pelunasan</th>
                <th>Sisa Tagihan</th>
                <th>Keterangan</th>
                <th>Jumlah Angsuran</th>
                <th>Cicilan Ke</th>
                <th>Kategori</th>
            </tr>
        </thead>
        <tbody>
            @php
            $total = 0;
            $totalsaldoawal = 0;
            $totalpelunasannow = 0;
            $totalsisatagihan = 0;
            @endphp
            @foreach ($historibayar as $d)
            @php
            $total+= $d->jumlah;
            $saldoawal = $d->jumlah_pinjaman-$d->totalpembayaran-$d->totalpelunasanlast;
            $sisatagihan = $saldoawal- $d->jumlah - $d->totalpelunasannow;
            $totalsaldoawal += $saldoawal;
            $totalpelunasannow += $d->totalpelunasannow;
            $totalsisatagihan += $sisatagihan;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->no_bukti }}</td>
                <td>{{ $d->no_pinjaman }}</td>
                <td>{{ "'".$d->nik }}</td>
                <td>{{ $d->nama_karyawan }}</td>
                <td>{{ $d->nama_jabatan }}</td>
                <td>{{ $d->nama_dept }}</td>
                <td style="text-align: right">{{ rupiah($saldoawal) }}</td>
                <td style="text-align: right">{{ rupiah($d->jumlah) }}</td>
                <td style="text-align: right">{{ !empty($d->totalpelunasannow) ? rupiah($d->totalpelunasannow) : '' }}</td>
                <td style="text-align: right">{{ rupiah($saldoawal- $d->jumlah - $d->totalpelunasannow) }}</td>
                <td>
                    @if ($sisatagihan == 0)
                    <span style="color:green">Lunas</span>
                    @else
                    <span style="color:red">Belum Lunas</span>
                    @endif
                </td>
                <td>{{ $d->angsuran }} Bulan</td>
                <td>{{ $d->cicilan_ke }}</td>
                <td>
                    @if (in_array($d->id_jabatan,$show_for_hrd))
                    <span class="info">Management</span>
                    @endif

                </td>
            </tr>
            @endforeach
            <tr>
                <th colspan="7">TOTAL</th>
                <th style="text-align: right">{{ rupiah($totalsaldoawal) }}</th>
                <th style="text-align: right">{{ rupiah($total) }}</th>
                <th style="text-align: right">{{ rupiah($totalpelunasannow) }}</th>
                <th style="text-align: right">{{ rupiah($totalsisatagihan) }}</th>
            </tr>
        </tbody>

    </table>
</body>
