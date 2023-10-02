<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CETAK SUMBER DAYA KENDARAAN</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&display=swap');

        body {
            font-family: 'Poppins'
        }

        .datatable3 {
            border: 1px solid #000000;
            border-collapse: collapse;
            font-size: 11px;
        }

        .datatable3 td {
            border: 1px solid #000000;
            padding: 6px;
        }

        .datatable3 th {
            border: 1px solid #000000;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }

        tr:nth-child(even) {
            background-color: #c7c7c7c2;
        }
    </style>
</head>

<body>
    <b style="font-size:14px;">
        @if ($cabang != null)
            @if ($cabang->kode_cabang == 'PST')
                PACIFIC PUSAT
            @else
                PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
            @endif
        @endif
        <br>
        LAPORAN SUMBER DAYA KENDARAAN
    </b>
    <table class="datatable3" style="width: 150%">
        <thead bgcolor="#295ea9" style="color:white; font-size:16;">
            <tr bgcolor="#295ea9" style="color:white; font-size:16;">
                <th rowspan="2" style="text-align: center">No Polisi</th>
                <th rowspan="2" style="text-align: center">Merk/Type</th>
                <th rowspan="2" style="text-align: center">Model</th>
                <th rowspan="2" style="text-align: center">Tahun</th>
                <th rowspan="2" style="text-align: center">No Mesin</th>
                <th rowspan="2" style="text-align: center">No Rangka</th>
                <th colspan="8" style="text-align: center">Surat Kendaraan</th>
                <th rowspan="2" style="text-align: center">Pemakai</th>
            </tr>
            <tr bgcolor="#295ea9" style="color:rgb(0, 0, 0); font-size:16;">
                <th style="text-align: center">No. BPKB/ No. STNK</th>
                <th style="text-align: center">Pajak Tahunan</th>
                <th style="text-align: center">Atas Nama</th>
                <th style="text-align: center">Ijin Bongkar Muat (IBM)</th>
                <th style="text-align: center">No. Uji</th>
                <th style="text-align: center">KIR</th>
                <th style="text-align: center">STNK</th>
                <th style="text-align: center">SIPA</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kendaraan as $k)
                <tr>
                    <td>{{ $k->no_polisi }}</td>
                    <td>{{ $k->merk }} / {{ $k->tipe }}</td>
                    <td>{{ $k->tipe_kendaraan }}</td>
                    <td>{{ $k->tahun_pembuatan }}</td>
                    <td>{{ $k->no_mesin }}</td>
                    <td>{{ $k->no_rangka }}</td>
                    <td>{{ $k->no_stnk }}</td>
                    <td>{{ $k->jatuhtempo_pajak_satutahun != '' ? DateToIndo2($k->jatuhtempo_pajak_satutahun) : '' }}
                    </td>
                    <td>{{ $k->atas_nama }}</td>
                    <td>{{ $k->ibm }}</td>
                    <td>{{ $k->no_uji }}</td>
                    <td>{{ $k->jatuhtempo_kir != '' ? DateToIndo2($k->jatuhtempo_kir) : '' }}</td>
                    <td>{{ $k->jatuhtempo_pajak_limatahun != '' ? DateToIndo2($k->jatuhtempo_pajak_limatahun) : '' }}
                    </td>
                    <td>{{ $k->sipa }}</td>
                    <td>{{ $k->nama_driver_helper }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
