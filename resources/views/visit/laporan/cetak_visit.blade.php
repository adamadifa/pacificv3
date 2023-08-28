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

<body>
    <b style="font-size:20px;">
        LAPORAN VISIT PELANGGAN<br>
        PERIODE BULAN {{ $bulan }} TAHUN {{ $tahun }}<br>
        {{ $cabang != '' ? 'CABANG ' . $cabang : '' }}
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
            @foreach ($visit as $v)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $v->tgl_visit }}</td>
                    <td>{{ $v->nama_pelanggan }}</td>
                    <td>{{ $v->nama_karyawan }}</td>
                    <td>{{ $v->pasar }}</td>
                    <td>{{ $v->tgltransaksi }}</td>
                    <td>{{ $v->no_fak_penj }}</td>
                    <td style="text-align: right">{{ number_format($v->nominal) }}</td>
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
                <th colspan="" style="color:white; font-size:14;">TOTAL</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
