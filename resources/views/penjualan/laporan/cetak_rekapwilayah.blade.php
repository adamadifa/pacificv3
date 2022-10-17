<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Rekap Wilayah {{ date("d-m-y") }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&display=swap');

        body {
            font-family: 'Poppins'
        }

        .datatable3 {
            border: 2px solid #D6DDE6;
            border-collapse: collapse;
            font-size: 14px;
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
        REKAP OMSET PER WILAYAH<br>
        TAHUN {{ $tahun }}
        <br />
    </b>
    <br>
    <table class="datatable3">
        <thead bgcolor="#295ea9" style="color:white;">
            <tr bgcolor="#295ea9" style="color:white;">
                <th rowspan="2">Rute / Wilayah</th>
                <th colspan="12">Bulan</th>
                <th rowspan="2">TOTAL</th>
            </tr>
            <tr>
                <th>Jan</th>
                <th>Feb</th>
                <th>Mar</th>
                <th>Apr</th>
                <th>Mei</th>
                <th>Jun</th>
                <th>Jul</th>
                <th>Agu</th>
                <th>Sep</th>
                <th>Ok</th>
                <th>Nov</th>
                <th>Des</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rekapwilayah as $d)

            <tr>
                <td>{{ $d->pasar }}</td>
                <td style="text-align: right">{{ !empty($d->jan) ? rupiah($d->jan) : '' }}</td>
                <td style="text-align: right">{{ !empty($d->feb) ? rupiah($d->feb) : '' }}</td>
                <td style="text-align: right">{{ !empty($d->mar) ? rupiah($d->mar) : '' }}</td>
                <td style="text-align: right">{{ !empty($d->apr) ? rupiah($d->apr) : '' }}</td>
                <td style="text-align: right">{{ !empty($d->mei) ? rupiah($d->mei) : '' }}</td>
                <td style="text-align: right">{{ !empty($d->jun) ? rupiah($d->jun) : '' }}</td>
                <td style="text-align: right">{{ !empty($d->jul) ? rupiah($d->jul) : '' }}</td>
                <td style="text-align: right">{{ !empty($d->agu) ? rupiah($d->agu) : '' }}</td>
                <td style="text-align: right">{{ !empty($d->sep) ? rupiah($d->sep) : '' }}</td>
                <td style="text-align: right">{{ !empty($d->okt) ? rupiah($d->okt) : '' }}</td>
                <td style="text-align: right">{{ !empty($d->nov) ? rupiah($d->nov) : '' }}</td>
                <td style="text-align: right">{{ !empty($d->des) ? rupiah($d->des) : '' }}</td>
                <td style="text-align: right">{{ !empty($d->total) ? rupiah($d->total) : '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
