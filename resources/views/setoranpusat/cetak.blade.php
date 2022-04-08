<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Setoran Ke Bank {{ date("d-m-y") }}</title>
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
        RINCIAN SETORAN KAS BESAR PADA BANK/ PUSAT
        <br>
        @if ($cabang->kode_cabang=="PST")
        PACIFIC PUSAT
        @else
        PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
        @endif
        <br>
        @if($bank != null)
        BANK {{ $bank->nama_bank }}
        @endif
        <br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
    </b>

    <table class="datatable3" border="1">
        <thead style=" background-color:#31869b; font-size:12;">
            <tr style=" background-color:#31869b; color:white; font-size:12;">
                <th style="padding:10px !important!;">TANGGAL</th>
                <th>KETERANGAN</th>
                <th>BANK</th>
                <th>KERTAS</th>
                <th>LOGAM</th>
                <th>TRANSFER</th>
                <th>GIRO</th>
                <th>JUMLAH SETOR</th>
                <th>TGL DITERIMA PUSAT</th>
            </tr>
        </thead>
        <tbody>
            @php
            $totalsetoran = 0;
            $totalkertas = 0;
            $totallogam = 0;
            $totaltransfer = 0;
            $totalgiro = 0;
            $totalallsetoran = 0;
            @endphp
            @foreach ($setoranpusat as $d)
            @php
            $totalsetoran = $d->uang_kertas + $d->uang_logam + $d->transfer + $d->giro;
            $totalkertas += $d->uang_kertas;
            $totallogam += $d->uang_logam;
            $totaltransfer += $d->transfer;
            $totalgiro += $d->giro;
            $totalallsetoran += $totalsetoran;
            @endphp
            <tr>
                <td>{{ date("d-m-Y",strtotime($d->tgl_setoranpusat)) }}</td>
                <td>{{ ucwords(strtolower($d->keterangan)) }}</td>
                <td>{{ $d->nama_bank }}</td>
                <td style="text-align:right">{{ !empty($d->uang_kertas) ? rupiah($d->uang_kertas) : '' }}</td>
                <td style="text-align:right">{{ !empty($d->uang_logam) ? rupiah($d->uang_logam) : '' }}</td>
                <td style="text-align:right">{{ !empty($d->transfer) ? rupiah($d->transfer) : '' }}</td>
                <td style="text-align:right">{{ !empty($d->giro) ? rupiah($d->giro) : '' }}</td>
                <td style="text-align:right">{{ !empty($totalsetoran) ? rupiah($totalsetoran) : '' }}</td>
                <td style="text-align: center">
                    @if ($d->status==1)
                    <span class="badge bg-success"><i class="fa fa-check"></i> Diterima {{ date("d-m-Y",strtotime($d->tgl_diterimapusat)) }}</span>
                    @elseif($d->status==2)
                    <span class="badge bg-danger"><i class="fa fa-close"></i> Ditolak</span>
                    @else
                    <span class="badge bg-warning"><i class="fa fa-history"></i> Belum Diterima</span>
                    @endif
                </td>

            </tr>
            @endforeach
            <tr style=" background-color:#31869b; color:white; font-size:12px; font-weight:bold">
                <td colspan="3">TOTAL</td>
                <td style="text-align:right">{{ !empty($totalkertas) ? rupiah($totalkertas) : '' }}</td>
                <td style="text-align:right">{{ !empty($totallogam) ? rupiah($totallogam) : '' }}</td>
                <td style="text-align:right">{{ !empty($totaltransfer) ? rupiah($totaltransfer) : '' }}</td>
                <td style="text-align:right">{{ !empty($totalgiro) ? rupiah($totalgiro) : '' }}</td>
                <td style="text-align:right">{{ !empty($totalallsetoran) ? rupiah($totalallsetoran) : '' }}</td>
                <td></td>

            </tr>
        </tbody>
    </table>
    <br>
    <table class="datatable3" border="1">
        <thead>
            <tr style=" background-color:orange; color:black; font-size:12;">
                <th colspan="5" style="text-align:left">SUMMARY</th>
            </tr>
            <tr style=" background-color:#31869b; color:white; font-size:12;">
                <th>BANK</th>
                <th>UANG KERTAS</th>
                <th>UANG LOGAM</th>
                <th>TRANSFER</th>
                <th>GIRO</th>
                </th>
        </thead>
        <tbody>
            @foreach ($rekap as $d)
            <tr>
                <td>{{ $d->nama_bank }}</td>
                <td align="right">{{ !empty($d->uang_kertas) ? rupiah($d->uang_kertas) :''}}</td>
                <td align="right">{{ !empty($d->uang_logam) ? rupiah($d->uang_logam) :''}}</td>
                <td align="right">{{ !empty($d->transfer) ? rupiah($d->transfer) :''}}</td>
                <td align="right">{{ !empty($d->giro) ? rupiah($d->giro) :''}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
