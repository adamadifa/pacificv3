<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Effective Call {{ date("d-m-y") }}</title>
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
        LAPORAN Effective Call<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>

        <br />
    </b>
    <br>
    <table class="datatable3">
        <thead>
            <tr>
                <th rowspan="2">ID Salesman</th>
                <th rowspan="2">Nama Salesman</th>
                <th colspan="8">Produk</th>
            </tr>
            <tr>
                <th>AB</th>
                <th>AR</th>
                <th>AS</th>
                <th>BB</th>
                <th>DEP</th>
                <th>SP</th>
                <th>SC</th>
                <th>SP8</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ec as $d)
            <tr>
                <td>{{ $d->id_karyawan }}</td>
                <td>{{ $d->nama_karyawan }}</td>
                <td style="text-align: right">{{ rupiah($d->ab)  }}</td>
                <td style="text-align: right">{{ rupiah($d->ar)  }}</td>
                <td style="text-align: right">{{ rupiah($d->as)  }}</td>
                <td style="text-align: right">{{ rupiah($d->bb)  }}</td>
                <td style="text-align: right">{{ rupiah($d->dep)  }}</td>
                <td style="text-align: right">{{ rupiah($d->sp)  }}</td>
                <td style="text-align: right">{{ rupiah($d->sc)  }}</td>
                <td style="text-align: right">{{ rupiah($d->sp8)  }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
@include('layouts.script')
<script>
    $(function() {
        $(".detailsalesman").click(function() {
            $(this).closest("form").submit();
        });
    });

</script>
</html>
