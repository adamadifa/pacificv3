<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Visit Pelanggan {{ date('d-m-y') }}</title>
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
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <table class="datatable3" style="width:100%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th style="width:1%">No</th>
                <th>Tanggal Visit</th>
                <th>Pelanggan</th>
                <th>Salesman</th>
                <th>Pasar</th>
                <th>Tgl Faktur</th>
                <th>No. Faktur</th>
                <th>Nilai Faktur</th>
                <th>Jenis Transaksi</th>
                <th>Hasil Konfirmasi</th>
                <th>Note</th>
                <th>Action OM</th>
                <th>Cabang</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($visitpelanggan as $d)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ date('d-m-Y', strtotime($d->tanggal_visit)) }}</td>
                    <td>{{ $d->nama_pelanggan }}</td>
                    <td>{{ $d->pasar }}</td>
                    <td>{{ $d->nama_karyawan }}</td>
                    <td>{{ date('d-m-Y', strtotime($d->tgltransaksi)) }}</td>
                    <td>{{ $d->no_fak_penj }}</td>
                    <td style="text-align: right">{{ rupiah($d->total) }}</td>
                    <td>{{ $d->jenistransaksi }}</td>
                    <td>{{ $d->hasil_konfirmasi }}</td>
                    <td>{{ $d->note }}</td>
                    <td>{{ $d->act_om }}</td>
                    <td>{{ $d->kode_cabang }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
