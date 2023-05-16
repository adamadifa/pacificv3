<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Persentase SFA</title>
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
        LAPORAN DATA PERSENTASE PENGINPUTAN SFA<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        @if ($salesman != null)
        SALESMAN {{ strtoupper($salesman->nama_karyawan) }}
        @else
        SEMUA SALESMAN
        @endif
        <br />
    </b>
    <table class="datatable3" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>No.</th>
                <th>ID Salesman</th>
                <th>Nama Salesman</th>
                <th>Cabang</th>
                <th>Total Transaksi</th>
                <th>Input BY SFA</th>
                <th>Persentase</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($persentasesfa as $key => $d)
            @php
            $kode_cabang = @$persentasesfa[$key + 1]->kode_cabang;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->id_karyawan }}</td>
                <td>{{ $d->nama_karyawan }}</td>
                <td>{{ $d->kode_cabang }}</td>
                <td style="text-align:center">{{ rupiah($d->totaltransaksi) }}</td>
                <td style="text-align: center">{{ rupiah($d->totaltransaksisfa) }}</td>
                <td style="text-align: center">{{ rupiah($d->persentase)  }}%</td>
            </tr>
            @if ($kode_cabang != $d->kode_cabang)
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th colspan="7"></th>
            </tr>
            @endif
            @endforeach
        </tbody>


    </table>
</body>
</html>
