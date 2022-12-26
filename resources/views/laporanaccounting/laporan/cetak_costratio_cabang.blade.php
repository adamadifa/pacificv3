<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cost Ratio {{ date("d-m-y") }}</title>
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

        .table-scroll {
            position: relative;
            max-width: 100%;
            margin: auto;
            overflow: hidden;

        }

        .table-wrap {
            width: 100%;
            overflow: auto;
        }

        .table-scroll table {
            width: 100%;
            margin: auto;
            border-collapse: separate;
            border-spacing: 0;
        }


        .clone {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
        }

        .clone th,
        .clone td {
            visibility: hidden
        }

        .clone td,
        .clone th {
            border-color: transparent
        }

        .clone tbody th {
            visibility: visible;
            color: red;
        }

        .clone .fixed-side {
            border: 1px solid #000;
            background: #eee;
            visibility: visible;
        }

    </style>
</head>
<body>
    <b style="font-size:14px;">
        COST RATIO CABANG {{ strtoupper($cabang->nama_cabang) }}<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }} <br>
    </b>
    <br>
    <table class="datatable3" style="width:65%" border="1">
        <thead>
            <tr>
                <th style="background-color:rgb(0, 52, 93); color:white">No</th>
                <th style="background-color:rgb(0, 52, 93); color:white">Kode Akun</th>
                <th style="background-color:rgb(0, 52, 93); color:white">Nama Akun</th>
                <th style="background-color:rgb(0, 52, 93); color:white">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php
            $grandtotal = 0;
            @endphp
            @foreach ($biaya as $d)
            @php
            $grandtotal += $d->total;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>'{{ $d->kode_akun }}</td>
                <td>
                    @php
                    if($d->kode_akun == 1){
                    $nama_akun = 'Sewa Gedung';
                    }elseif($d->kode_akun==2){
                    $nama_akun = 'Ratio BS';
                    }else{
                    $nama_akun = $d->nama_akun;
                    }

                    echo $nama_akun;
                    @endphp
                </td>
                <td align="right">{{ rupiah($d->total) }}</td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td>Potongan Penjualan</td>
                <td style="text-align: right">{{ rupiah($potongan->total) }}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>Logistik</td>
                <td style="text-align: right">{{ rupiah($logistik->total) }}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>Bahan Kemasan</td>
                <td style="text-align: right">{{ rupiah($bahan->total) }}</td>
            </tr>
        </tbody>
        <tfoot>
            @php
            $totalswan = $penjualan->totalswan - $retur->totalreturswan;
            $totalaida = $penjualan->totalaida - $retur->totalreturaida;
            $totalpenjualan = $totalswan + $totalaida;
            $grandtotal = $grandtotal + $potongan->total + $logistik->total + $bahan->total;

            $cr_swan_biaya_total = $totalswan != 0 ? ROUND(($grandtotal/$totalswan)*100) : 0;
            $cr_aida_biaya_total = $totalaida != 0 ? ROUND(($grandtotal/$totalaida)*100) : 0;
            $cr_penjualan_biaya_total = $totalpenjualan != 0 ? ROUND(($grandtotal/$totalpenjualan)*100) : 0;


            @endphp
            <tr>
                <th style="background-color:rgb(0, 52, 93); color:white" colspan="3">TOTAL</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($grandtotal) }}</th>
            </tr>
            <tr>
                <th style="background-color:rgb(0, 52, 93); color:white" colspan="2" rowspan="4">PENJUALAN</th>
                <th style="background-color:rgb(0, 52, 93); color:white">SWAN</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($totalswan) }}</th>
            </tr>
            <tr>
                <th style="background-color:rgb(0, 52, 93); color:white">COST RATIO</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_swan_biaya_total }}%</th>
            </tr>
            <tr>
                <th style="background-color:rgb(93, 0, 0); color:white">AIDA</th>
                <th style="background-color:rgb(93, 0, 0); color:white; text-align:right">{{ rupiah($totalaida) }}</th>
            </tr>
            <tr>
                <th style="background-color:rgb(93, 0, 0); color:white">COST RATIO</th>
                <th style="background-color:rgb(93, 0, 0); color:white;">{{ $cr_aida_biaya_total }}%</th>
            </tr>
            <tr>
                <th style="background-color:rgb(210, 59, 4); color:white" colspan="3">PIUTANG > 1 BULAN</th>
                <th style="background-color:rgb(210, 59, 4); color:white; text-align:right">{{ rupiah($piutang->totalpiutang) }}</th>
            </tr>
            @php
            $totalpiutang = $piutang->totalpiutang;
            $cr_swan_piutang_total = $totalswan != 0 ? ROUND(($totalpiutang/$totalswan)*100) : 0;
            $cr_aida_piutang_total = $totalaida != 0 ? ROUND(($totalpiutang/$totalaida)*100) : 0;
            $cr_penjualan_piutang_total = $totalpenjualan != 0 ? ROUND(($totalpiutang/$totalpenjualan)*100) : 0;
            @endphp
            <tr>
                <th style="background-color:rgb(210, 59, 4); color:white" colspan="3">COST RATIO SWAN</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_swan_piutang_total }}%</th>
            </tr>
            <tr>
                <th style="background-color:rgb(210, 59, 4); color:white" colspan="3">COST RATIO AIDA</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_aida_piutang_total }}%</th>
            </tr>
            <tr>
                <th style="background-color:rgb(210, 59, 4); color:white" colspan="3">COST RATIO SWAN + AIDA</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_penjualan_piutang_total }}%</th>
            </tr>
            @php
            $total_biaya_piutang = $grandtotal + $totalpiutang;
            $cr_swan_biayapiutang_total = $totalswan != 0 ? ROUND(($total_biaya_piutang/$totalswan)*100) : 0;
            $cr_aida_biayapiutang_total = $totalaida != 0 ? ROUND(($total_biaya_piutang/$totalaida)*100) : 0;
            $cr_penjualan_biayapiutang_total = $totalpenjualan != 0 ? ROUND(($total_biaya_piutang/$totalpenjualan)*100) : 0;
            @endphp
            <tr>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white" colspan="3">BIAYA + PIUTANG</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white; text-align:right">{{ rupiah($total_biaya_piutang) }}</th>
            </tr>
            <tr>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white" colspan="3">COST RATIO SWAN</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_swan_biayapiutang_total }}%</th>
            </tr>
            <tr>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white" colspan="3">COST RATIO AIDA</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_aida_biayapiutang_total }}%</th>
            </tr>
            <tr>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white" colspan="3">COST RATIO SWAN + AIDA</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_penjualan_biayapiutang_total }}%</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
