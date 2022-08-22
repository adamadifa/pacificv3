<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laba Rugi {{ date("d-m-y") }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&display=swap');

        body {
            font-family: 'Poppins'

        }

        .datatable3 {
            border: 0px solid #D6DDE6;
            border-collapse: collapse;
            font-size: 14px;
        }

        .datatable3 td {
            border: 0px solid #000000;
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
        LABA RUGI<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }} <br>
    </b>
    <br>
    <table class="datatable3">
        @foreach ($labarugi as $key => $d)
        @php
        $nextkategori1 = @$labarugi[$key + 1]->kategori_1;
        $nextkategori2 = @$labarugi[$key + 1]->kategori_2;
        $nextkategori3 = @$labarugi[$key + 1]->kategori_3;
        $nextlevel = @$labarugi[$key + 1]->level;
        $level = $d->level;
        $kategori_1 = $d->kategori_1;
        $kategori_2 = $d->kategori_2;
        $kategori_3 = $d->kategori_3;
        @endphp
        @if ($d->level==1)
        @php
        $padding = "20px";
        @endphp
        @elseif($d->level== 2)
        @php
        $padding ="40px";
        @endphp
        @elseif($d->level==3)
        @php
        $padding ="60px";
        @endphp
        @else
        @php
        $padding = 0;
        @endphp
        @endif
        <tr>
            <td style="padding-left: {{ $padding }}">
                @if ($d->level != 3)
                <b>{{ $d->kode_akun }} {{ $d->nama_akun }}</b>
                @else
                {{ $d->kode_akun }} {{ $d->nama_akun }}
                @endif
            </td>
            <td style="text-align: right">
                @if ($d->level==3)
                {{ desimal($d->saldoawal) }}
                @endif
            </td>

        </tr>
        @if ($nextkategori1==$kategori_1 && $level == 3 && $nextkategori3 != $kategori_3 OR $nextkategori1!=$kategori_1 && $level == 3 && $nextkategori3 != $kategori_3 )
        <tr>
            <td style="padding-left:60px; font-weight:bold">JUMLAH {{ $d->nama_akun_3 }} </td>
        </tr>
        @endif
        @if ($nextkategori1==$kategori_1 && $level == 3 && $nextkategori2 != $kategori_2 OR $nextkategori1!=$kategori_1 && $level == 3 && $nextkategori2 != $kategori_2 )
        <tr>
            <td style="padding-left:60px; font-weight:bold">TOTAL {{ $d->nama_akun_2 }} </td>
        </tr>
        @endif

        @if ($nextkategori1!= $kategori_1)
        <tr>
            <td style="padding-left:60px; font-weight:bold">TOTAL {{ $d->nama_akun_1 }} </td>
        </tr>
        @endif
        @endforeach

    </table>

</body>

</html>
