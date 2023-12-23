<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Monitoring Retur {{ date('d-m-y') }}</title>
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
        MONITORING RETUR <br>
        PERIODE
    </b>
    <table class="datatable3" style="width:90%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th style="width:1%" rowspan="2">No</th>
                <th rowspan="2">Tanggal</th>
                <th rowspan="2">No. Retur</th>
                <th rowspan="2">No. Faktur</th>
                <th rowspan="2">Kode Pelanggan</th>
                <th rowspan="2">Nama Pelanggan</th>
                <th rowspan="2">Pasar</th>
                <th rowspan="2">Hari Routing</th>
                <th rowspan="2">Nama Barang</th>
                <th colspan="3" style="background-color: #f8a221">Qty Retur</th>
                <th colspan="3" style="background-color: #14cc00ec">Penggantian Retur</th>
                <th colspan="3" style="background-color: #cc003aec">Sisa Retur</th>
                <th rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <th style="background-color: #f8a221">Dus</th>
                <th style="background-color: #f8a221">Pack</th>
                <th style="background-color: #f8a221">Pcs</th>

                <th style="background-color: #14cc00ec">Dus</th>
                <th style="background-color: #14cc00ec">Pack</th>
                <th style="background-color: #14cc00ec">Pcs</th>

                <th style="background-color: #cc003aec">Dus</th>
                <th style="background-color: #cc003aec">Pack</th>
                <th style="background-color: #cc003aec">Pcs</th>


            </tr>
        </thead>
        <tbody>
            @foreach ($retur as $key => $d)
                @php
                    $no_retur_penj = @$retur[$key + 1]->no_retur_penj;
                @endphp
                @php
                    $jmldus = floor($d->jumlah / $d->isipcsdus);
                    $sisadus = $d->jumlah % $d->isipcsdus;

                    if ($d->isipack == 0) {
                        $jmlpack = 0;
                        $sisapack = $sisadus;
                    } else {
                        $jmlpack = floor($sisadus / $d->isipcs);
                        $sisapack = $sisadus % $d->isipcs;
                    }

                    $jmlpcs = $sisapack;

                    //Pelunasan
                    $jmldus_pelunasan = floor($d->jumlahpelunasan / $d->isipcsdus);
                    $sisadus_pelunasan = $d->jumlahpelunasan % $d->isipcsdus;

                    if ($d->isipack == 0) {
                        $jmlpack_pelunasan = 0;
                        $sisapack_pelunasan = $sisadus_pelunasan;
                    } else {
                        $jmlpack_pelunasan = floor($sisadus_pelunasan / $d->isipcs);
                        $sisapack_pelunasan = $sisadus_pelunasan % $d->isipcs;
                    }

                    $jmlpcs_pelunasan = $sisapack_pelunasan;

                    //Sisa
                    $jmlsisa = $d->jumlah - $d->jumlahpelunasan;
                    $jmldus_sisa = floor($jmlsisa / $d->isipcsdus);
                    $sisadus_sisa = $jmlsisa % $d->isipcsdus;

                    if ($d->isipack == 0) {
                        $jmlpack_sisa = 0;
                        $sisapack_sisa = $sisadus_sisa;
                    } else {
                        $jmlpack_sisa = floor($sisadus_sisa / $d->isipcs);
                        $sisapack_sisa = $sisadus_sisa % $d->isipcs;
                    }

                    $jmlpcs_sisa = $sisapack_sisa;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ date('d-m-Y', strtotime($d->tglretur)) }}</td>
                    <td>{{ $d->no_retur_penj }}</td>
                    <td>{{ $d->no_fak_penj }}</td>
                    <td>{{ $d->kode_pelanggan }}</td>
                    <td>{{ $d->nama_pelanggan }}</td>
                    <td>{{ $d->pasar }}</td>
                    <td>{{ $d->hari }}</td>
                    <td>{{ $d->nama_barang }}</td>
                    <td align="center">{{ !empty($jmldus) ? $jmldus : '' }}</td>
                    <td align="center">{{ !empty($jmlpack) ? $jmlpack : '' }}</td>
                    <td align="center">{{ !empty($jmlpcs) ? $jmlpcs : '' }}</td>

                    <td align="center">{{ !empty($jmldus_pelunasan) ? $jmldus_pelunasan : '' }}</td>
                    <td align="center">{{ !empty($jmlpack_pelunasan) ? $jmlpack_pelunasan : '' }}</td>
                    <td align="center">{{ !empty($jmlpcs_pelunasan) ? $jmlpcs_pelunasan : '' }}</td>

                    <td align="center">{{ !empty($jmldus_sisa) ? $jmldus_sisa : '' }}</td>
                    <td align="center">{{ !empty($jmlpack_sisa) ? $jmlpack_sisa : '' }}</td>
                    <td align="center">{{ !empty($jmlpcs_sisa) ? $jmlpcs_sisa : '' }}</td>
                    <td>
                        @if ($jmlsisa == 0)
                            <span style="color:green">Lunas</span>
                        @else
                            <span style="color:red">Belum Lunas</span>
                        @endif
                    </td>
                </tr>
                @if ($no_retur_penj != $d->no_retur_penj)
                    <tr>
                        <th colspan="19" style="background-color:#024a75; color:white">
                        </th>
                    </tr>
                @endif
            @endforeach
        </tbody>
</body>

</html>
