<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Routing Salesman {{ date("d-m-y") }}</title>
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
        LAPORAN ROUTING SALESMAN<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        @if ($salesman != null)
        SALESMAN {{ strtoupper($salesman->nama_karyawan) }}
        @else
        SEMUA SALESMAN
        @endif
        <br />
    </b>
    <br>
    <table class="datatable3">
        <thead>
            <tr bgcolor="#295ea9" style="color:white;">
                <th>No.</th>
                <th>ID Salesman</th>
                <th>Nama Salesman</th>
                <th>Total Kunjungan</th>
                <th>Sesuai Jadwal</th>
                <th>Tidak Sesuai Jadwal</th>
            </tr>
        </thead>
        <tbody>
            @php
            $grandtotalkunjungan = 0;
            $grandtotalkunjungansesuai = 0;
            $grandtotalkunjungantidaksesuai = 0;
            @endphp
            @foreach ($rekap as $d)
            @php
            $grandtotalkunjungan += $d->totalkunjungan;
            $grandtotalkunjungansesuai += $d->totalsesuaijadwal;
            $totaltidaksesuai = $d->totalkunjungan - $d->totalsesuaijadwal;
            $grandtotalkunjungantidaksesuai += $totaltidaksesuai;

            $persentasekunjungansesuai = !empty($grandtotalkunjungan) ? $grandtotalkunjungansesuai / $grandtotalkunjungan : 0;
            $persentasekunjungantidaksesuai = !empty($grandtotalkunjungan) ? $grandtotalkunjungantidaksesuai / $grandtotalkunjungan : 0;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->id_karyawan }}</td>
                <td>{{ $d->nama_karyawan }}</td>
                <td style="text-align: center">{{ $d->totalkunjungan }}</td>
                <td style="text-align: center">{{ $d->totalsesuaijadwal }} ({{ !empty($d->totalkunjungan) ? ROUND($d->totalsesuaijadwal/$d->totalkunjungan * 100) : 0 }} % )</td>
                <td style="text-align: center">
                    {{ $totaltidaksesuai }} ({{ !empty($d->totalkunjungan) ? ROUND($totaltidaksesuai/$d->totalkunjungan * 100) : 0 }} % )
                </td>
            </tr>
            @endforeach
            <tr>
                <th colspan="3">TOTAL</th>
                <th style="text-align: center">{{ $grandtotalkunjungan }}</th>
                <th style="text-align: center">{{ $grandtotalkunjungansesuai }} ({{ $persentasekunjungansesuai }}%)</th>
                <th style="text-align: center">{{ $grandtotalkunjungantidaksesuai }} ({{ $persentasekunjungantidaksesuai }}%)</th>
            </tr>
        </tbody>
    </table>

</body>
</html>
