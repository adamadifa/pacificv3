<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Kas Kecil {{ date("d-m-y") }}</title>
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
    <table class="datatable3" style="width:80%">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>No</th>
                <th>No. Kasbon</th>
                <th>Tanggal</th>
                <th>Nik</th>
                <th>Nama Karyawan</th>
                <th>Jabatan</th>
                <th>Departemen</th>
                <th>Jumlah</th>
                <th>Bayar</th>
                <th>Sisa Tagihan</th>
                {{-- <th>Ket</th> --}}
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @php
            $totalkasbon = 0;
            $totalpembayaran = 0;
            $totaltagihan = 0;
            @endphp
            @foreach ($kasbon as $d)
            @php
            $totalkasbon += $d->jumlah;
            $totalpembayaran += $d->totalpembayaran;

            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->no_kasbon }}</td>
                <td>{{ DateToIndo2($d->tgl_kasbon) }}</td>
                <td>{{ $d->nik }}</td>
                <td>{{ $d->nama_karyawan }}</td>
                <td>{{ $d->nama_jabatan }}</td>
                <td>{{ $d->nama_dept }}</td>
                <td style="text-align: right">{{ rupiah($d->jumlah_kasbon)  }}</td>
                <td style="text-align: right">{{ rupiah($d->totalpembayaran) }}</td>
                <td style="text-align: right">
                    @php
                    $sisatagihan = $d->jumlah_kasbon - $d->totalpembayaran;
                    $totaltagihan += $sisatagihan;
                    @endphp
                    {{ rupiah($sisatagihan) }}
                </td>
                <td style="color:{{ $d->jumlah_kasbon - $d->totalpembayaran != 0 ? 'red' : 'green' }}">{!! $d->jumlah_kasbon - $d->totalpembayaran == 0 ? 'Lunas' : 'Belum Lunas' !!}</td>
                {{-- <td style="color:{{ $d->status==0 ? 'orange' : 'green' }}">
                @if ($d->status==0)
                Belum di Proses
                @else
                Sudah di Proses
                @endif
                </td> --}}

            </tr>
            @endforeach
            <tr>
                <th colspan="7">TOTAL</th>
                <th style="text-align: right">{{ rupiah($totalkasbon) }}</th>
                <th style="text-align: right">{{ rupiah($totalpembayaran) }}</th>
                <th style="text-align: right">{{ rupiah($totaltagihan) }}</th>
            </tr>
        </tbody>
    </table>
</body>
