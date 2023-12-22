<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Ajuan Limit Kredit {{ date('d-m-y') }}</title>
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
        REKAP BUFFER, MAX & SELL OUT ALL CABANG <br>
        BULAN {{ strtoupper($namabulan) }} {{ $tahun }}
    </b>
    <table class="datatable3" style="width:120%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th style="width:1%" rowspan="3">No</th>
                <th rowspan="3">Cabang</th>
                <th colspan="{{ $jml_produk * 3 }}">Produk</th>
            </tr>
            <tr>
                @foreach ($produk as $d)
                    <th colspan="3">{{ $d->kode_produk }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach ($produk as $d)
                    <th>BUFFER</th>
                    <th>MAX</th>
                    <th>SELLING OUT</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($rekap as $d)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $d->nama_cabang }}</td>
                    @foreach ($produk as $p)
                        @php
                            $field = 'data_' . $p->kode_produk;
                            $data = explode('|', $d->$field);
                        @endphp
                        <td style="text-align: right">{{ !empty($data[0]) ? desimal($data[0]) : '' }}</td>
                        <td style="text-align: right">{{ !empty($data[1]) ? desimal($data[1]) : '' }}</td>
                        <td style="text-align: right">{{ !empty($data[2]) ? desimal($data[2]) : '' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
