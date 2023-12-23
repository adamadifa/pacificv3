<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Penjualan {{ date('d-m-y') }}</title>
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
        OMAN CABANG {{ $kode_cabang }}<br>
        TAHUN {{ $tahun }}
    </b>
    <br>

    <table class="datatable3">
        <thead class="thead-dark" style="background-color:#024a75; color:white">
            <tr>
                <th rowspan="2" width="10px" style="vertical-align: middle;">No</th>
                <th rowspan="2" style="vertical-align: middle;">Kode Produk</th>
                <th rowspan="2" style="vertical-align: middle;">Nama Produk</th>
                <th colspan="{{ $sampaibulan }}">Bulan</th>
                <th rowspan="2">TOTAL</th>
            </tr>
            <tr>
                @for ($i = 1; $i <= $sampaibulan; $i++)
                    <th>{{ $nama_bulan[$i - 1] }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach ($rekap as $d)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $d->kode_produk }}</td>
                    <td>{{ $d->nama_barang }}</td>
                    @php
                        $total = 0;
                    @endphp
                    @for ($i = 1; $i <= $sampaibulan; $i++)
                        @php
                            $field_bulan = 'bulan_' . $i;
                            $total += $d->$field_bulan;
                        @endphp
                        <td style="text-align:right">{{ !empty($d->$field_bulan) ? rupiah($d->$field_bulan) : '' }}</td>
                    @endfor
                    <td style="text-align: right">{{ !empty($total) ? rupiah($total) : '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
