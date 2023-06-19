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
                <th rowspan="2">No.</th>
                <th rowspan="2">ID Salesman</th>
                <th rowspan="2">Nama Salesman</th>
                <th rowspan="2">Cabang</th>
                <th rowspan="2">Jml Pelanggan Aktif</th>
                <th colspan="4">Lokasi</th>
                <th rowspan="2">No.HP</th>
                <th rowspan="2">Persentase</th>
            </tr>
            <tr>
                <th>Lokasi Teriisi</th>
                <th>Persentase</th>
                <th>Sudah di Update SFA</th>
                <th>Persentase</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($persentaselokasi as $key => $d)
            @php
            $kode_cabang = @$persentaselokasi[$key + 1]->kode_cabang;
            $persentaselokasiterisi = $d->lokasi / $d->jmlpelangganaktif * 100;
            $persentasesfa = $d->updatebysfa / $d->jmlpelangganaktif * 100;
            $persentasenohp = $d->nohpcomplete / $d->jmlpelangganaktif * 100;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->id_sales }}</td>
                <td>{{ $d->nama_karyawan }}</td>
                <td>{{ $d->kode_cabang }}</td>
                <td align="center">{{ rupiah($d->jmlpelangganaktif) }}</td>
                <td align="center">{{ rupiah($d->lokasi) }}</td>
                <td align="center">{{ desimal($persentaselokasiterisi) }} %</td>
                <td align="center">{{ rupiah($d->updatebysfa) }}</td>
                <td align="center">{{ desimal($persentasesfa) }} %</td>
                <td align="center">{{ $d->nohpcomplete }}</td>
                <td align="center">{{ desimal($persentasenohp) }} %</td>

            </tr>
            @if ($kode_cabang != $d->kode_cabang)
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th colspan="11"></th>
            </tr>
            @endif
            @endforeach
        </tbody>


    </table>
</body>
</html>
