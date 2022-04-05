<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap BG {{ $cabang->nama_cabang }} {{ date("d-m-y") }}</title>
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
        REKAP BG
        <br>
        @if ($cabang->kode_cabang=="PST")
        PACIFIC PUSAT
        @else
        PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
        @endif
        <br>
        PERIODE BULAN {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }}
    </b>
    <br>
    <table class="datatable3" border="1">
        <thead style=" background-color:#31869b; color:white; font-size:12;">
            <tr style=" background-color:#31869b; color:white; font-size:12;">
                <th>TGL PENERIMAAN</th>
                <th>SALES</th>
                <th>NO FAKTUR</th>
                <th>NAMA PELANGGAN</th>
                <th>NAMA BANK</th>
                <th>NO CHEQUE</th>
                <th>TGL JATUH TEMPO</th>
                <th>JUMLAH PENERIMAAN</th>
                <th>TGL PENCAIRAN</th>
                <th>SALDO GIRO BELUM CAIR</th>
            </tr>
        </thead>
        <tbody>
            @php
            $totalgiro = 0;
            $totalgirobc = 0;
            $totalgiroall = 0;
            $totalgiroallbc = 0;
            @endphp
            @foreach ($rekapbg as $key => $r)
            @php
            $nogiro = @$rekapbg[$key + 1]->no_giro;
            if (empty($r->tgl_pencairan)) {
            //Giro Belum Cair
            $girobc = $r->jumlah;
            $tglcair = "";
            } else {
            $girobc = 0;
            $tglcair = $r->tgl_pencairan;
            }

            $totalgiro = $totalgiro + $r->jumlah;
            $totalgirobc = $totalgirobc + $girobc;
            $totalgiroall = $totalgiroall + $r->jumlah;
            $totalgiroallbc = $totalgiroallbc + $girobc;
            @endphp
            <tr style="font-size:12px">
                <td>{{ DateToIndo2($r->tgl_giro)}}</td>
                <td>{{ $r->nama_karyawan}}</td>
                <td>{{ $r->no_fak_penj}}</td>
                <td>{{ $r->nama_pelanggan}}</td>
                <td>{{ $r->namabank}}</td>
                <td>{{ $r->no_giro}}</td>
                <td>{{ DateToIndo2($r->jatuhtempo)}}</td>
                <td align="right" style="font-weight:bold"><?php if (!empty($r->jumlah)) {echo rupiah($r->jumlah);} ?></td>
                <td><?php if (!empty($tglcair)) {echo DateToIndo2($tglcair);} ?></td>
                <td align="right" style="font-weight:bold; background-color:#54bbd8"><?php if (!empty($girobc)) {echo rupiah($girobc);} ?></td>
            </tr>
            @php
            if ($nogiro != $r->no_giro) {
            echo '
            <tr bgcolor="#199291" style="color:white; font-weight:bold">
                <td colspan="7">TOTAL</td>
                <td align="right">' . rupiah($totalgiro) . '</td>
                <td></td>
                <td align="right">' . rupiah($totalgirobc) . '</td>
            </tr>';
            $totalgiro = 0;
            $totalgirobc = 0;
            }
            @endphp
            @endforeach
            <tr style="font-size:12px" bgcolor="yellow">
                <th colspan="7">TOTAL</th>
                <th style="text-align:right"><?php if (!empty($totalgiroall)) {echo rupiah($totalgiroall);} ?></th>
                <th></th>
                <th style="text-align:right"><?php if (!empty($totalgiroallbc)) {echo rupiah($totalgiroallbc);} ?></th>
            </tr>
        </tbody>
    </table>
</body>
</html>
