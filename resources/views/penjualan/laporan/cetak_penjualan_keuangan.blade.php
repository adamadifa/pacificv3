<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Penjualan {{ date("d-m-y") }}</title>
    <style>
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
        @if ($cabang->kode_cabang=="PST")
        PACIFIC PUSAT
        @else
        PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
        @endif
        <br>
        LAPORAN KAS KECIL<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
    </b>
    <br>
    <table class="datatable3">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>TGL LHP</th>
                <th>TUNAI</th>
                <th>TAGIHAN</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php
            $totaltunaipersales = 0;
            $totaltagihanpersales = 0;
            $totaltunaitagihanpersales = 0;
            $id_sales = "";
            @endphp
            @foreach ($setoran_penjualan as $key => $d)
            @php
            $totaltunaipersales = $totaltunaipersales += $d->lhp_tunai;
            $totaltagihanpersales = $totaltagihanpersales += $d->lhp_tagihan;
            $totaltunaitagihan = $d->lhp_tunai + $d->lhp_tagihan;
            $totaltunaitagihanpersales += $totaltunaitagihan;
            $id_karyawan = @$setoran_penjualan[$key + 1]->id_karyawan;
            @endphp
            @if ($id_sales != $d->id_karyawan)
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th colspan="4">{{ strtoupper($d->id_karyawan).' - '. strtoupper($d->nama_karyawan) }}</th>
            </tr>
            @endif
            <tr>
                <td>{{ date("d-m-Y",strtotime($d->tgl_lhp)) }}</td>
                <td style="text-align: right">{{ !empty($d->lhp_tunai) ? rupiah($d->lhp_tunai) : '' }}</td>
                <td style="text-align: right">{{ !empty($d->lhp_tagihan) ? rupiah($d->lhp_tagihan) : '' }}</td>
                <td style="text-align: right">{{ !empty($totaltunaitagihan) ? rupiah($totaltunaitagihan) : '' }}</td>
            </tr>
            @if ($d->id_karyawan != $id_karyawan)
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>TOTAL</td>
                <th style="text-align:right">{{ rupiah($totaltunaipersales) }}</th>
                <th style="text-align:right">{{ rupiah($totaltagihanpersales) }}</th>
                <th style="text-align:right">{{ rupiah($totaltunaitagihanpersales) }}</th>
            </tr>
            @endif
            @php
            $id_sales = $d->id_karyawan;
            @endphp
            @endforeach
        </tbody>
    </table>
</body>
</html>
