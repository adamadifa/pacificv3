<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Monitoring Program {{ date('d-m-y') }}</title>
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
    <b style="font-size:14px;">
        MONITORING PROGRAM <br>
        PERIODE
    </b>
    <table class="datatable3">
        <tr>
            <th>Kode Program</th>
            <td>{{ $program->kode_program }}</th>
        </tr>
        <tr>
            <th>Tanggal</th>
            <td>{{ DateToIndo2($program->tanggal) }}</th>
        </tr>
        <tr>
            <th>Nama Program</th>
            <td>{{ $program->nama_program }}</td>
        </tr>
        <tr>
            <th>Produk</th>
            <td>
                @php
                    $produk = unserialize($program->kode_produk);
                @endphp

                @foreach ($produk as $d)
                    {{ $d }},
                @endforeach

            </td>
        </tr>
        <tr>
            <th>Jml Target</th>
            <td>{{ rupiah($program->jml_target) }}</td>
        </tr>
        <tr>
            <th>Periode</th>
            <td>
                {{ date('d-m-Y', strtotime($program->dari)) }} s/d
                {{ date('d-m-Y', strtotime($program->sampai)) }}
            </td>
        </tr>
        <tr>
            <th>Reward</th>
            <td>{{ $program->nama_reward }}</td>
        </tr>
    </table>
    <br>
    <br>
    <table class="datatable3" style="width:50%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>No.</th>
                <th>Kode Pelanggan</th>
                <th>Nama Pelanggan</th>
                <th>Cabang</th>
                <th>Salesman</th>
                <th>Realisasi</th>
                <th>Sisa</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($peserta as $d)
                @php
                    $sisa = $program->jml_target - $d->jmldus;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $d->kode_pelanggan }}</td>
                    <td>{{ $d->nama_pelanggan }}</td>
                    <td>{{ $d->kode_cabang }}</td>
                    <td>{{ $d->nama_karyawan }}</td>
                    <td style="text-align: center">{{ rupiah($d->jmldus) }}</td>
                    <td style="text-align: center">{{ $sisa > 0 ? rupiah($sisa) : 0 }}</td>
                    <td style="text-align: center">{{ $sisa > 0 ? 'Belum Tercapai' : 'Tercapai' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
