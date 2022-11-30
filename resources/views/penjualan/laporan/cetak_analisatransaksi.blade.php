<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Data Pengambilan Pelanggan</title>
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
        LAPORAN DATA ANALISA TRANSAKSI PELANGGAN<br>
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
                <th rowspan="3">No.</th>
                <th rowspan="3">Nama Pelanggan</th>
                <th colspan="5">Minggu Ke 1</th>
                <th colspan="5">Minggu Ke 2</th>
                <th colspan="5">Minggu Ke 3</th>
                <th colspan="5">Minggu Ke 4</th>
                <th rowspan="3">Total Penjualan</th>
                <th rowspan="3">Total Pembayaran</th>
                <th rowspan="3">Rata Rata Pembelian Produk</th>
            </tr>
            <tr>
                <th colspan="2">JENIS TRANSAKSI</th>
                <th colspan="3">JENIS PEMBAYARAN</th>
                <th colspan="2">JENIS TRANSAKSI</th>
                <th colspan="3">JENIS PEMBAYARAN</th>
                <th colspan="2">JENIS TRANSAKSI</th>
                <th colspan="3">JENIS PEMBAYARAN</th>
                <th colspan="2">JENIS TRANSAKSI</th>
                <th colspan="3">JENIS PEMBAYARAN</th>
            </tr>
            <tr>
                <th>TUNAI</th>
                <th>KREDIT</th>
                <th>CASH</th>
                <th>TRANSFER</th>
                <th>GIRO</th>
                <th>TUNAI</th>
                <th>KREDIT</th>
                <th>CASH</th>
                <th>TRANSFER</th>
                <th>GIRO</th>
                <th>TUNAI</th>
                <th>KREDIT</th>
                <th>CASH</th>
                <th>TRANSFER</th>
                <th>GIRO</th>
                <th>TUNAI</th>
                <th>KREDIT</th>
                <th>CASH</th>
                <th>TRANSFER</th>
                <th>GIRO</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($analisatransaksi as $d)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->nama_pelanggan }}</td>
                <td align="right">{{ !empty($d->tunai_1) ? rupiah($d->tunai_1) : '' }}</td>
                <td align="right">{{ !empty($d->kredit_1) ? rupiah($d->kredit_1) : '' }}</td>
                <td align="right">{{ !empty($d->cash_1) ? rupiah($d->cash_1) : '' }}</td>
                <td align="right">{{ !empty($d->transfer_1) ? rupiah($d->transfer_1) : '' }}</td>
                <td align="right">{{ !empty($d->giro_1) ? rupiah($d->giro_1) : '' }}</td>

                <td align="right">{{ !empty($d->tunai_2) ? rupiah($d->tunai_2) : '' }}</td>
                <td align="right">{{ !empty($d->kredit_2) ? rupiah($d->kredit_2) : '' }}</td>
                <td align="right">{{ !empty($d->cash_2) ? rupiah($d->cash_2) : '' }}</td>
                <td align="right">{{ !empty($d->transfer_2) ? rupiah($d->transfer_2) : '' }}</td>
                <td align="right">{{ !empty($d->giro_2) ? rupiah($d->giro_2) : '' }}</td>

                <td align="right">{{ !empty($d->tunai_3) ? rupiah($d->tunai_3) : '' }}</td>
                <td align="right">{{ !empty($d->kredit_3) ? rupiah($d->kredit_3) : '' }}</td>
                <td align="right">{{ !empty($d->cash_3) ? rupiah($d->cash_3) : '' }}</td>
                <td align="right">{{ !empty($d->transfer_3) ? rupiah($d->transfer_3) : '' }}</td>
                <td align="right">{{ !empty($d->giro_3) ? rupiah($d->giro_3) : '' }}</td>

                <td align="right">{{ !empty($d->tunai_4) ? rupiah($d->tunai_4) : '' }}</td>
                <td align="right">{{ !empty($d->kredit_4) ? rupiah($d->kredit_4) : '' }}</td>
                <td align="right">{{ !empty($d->cash_4) ? rupiah($d->cash_4) : '' }}</td>
                <td align="right">{{ !empty($d->transfer_4) ? rupiah($d->transfer_4) : '' }}</td>
                <td align="right">{{ !empty($d->giro_4) ? rupiah($d->giro_4) : '' }}</td>
                <td align="right">{{ !empty($d->total) ? rupiah($d->total) : '' }}</td>
                <td align="right">{{ !empty($d->totalbayar) ? rupiah($d->totalbayar) : '' }}</td>
                <td align="right">{{ !empty($d->qty /4) ? rupiah($d->qty/4) : '' }}</td>
            </tr>
            @endforeach
        </tbody>

    </table>
</body>
</html>
