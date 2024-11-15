<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Penjualan Format Komisi</title>

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
        LAPORAN PENJUALAN<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        @if ($salesman != null)
        SALESMAN {{ strtoupper($salesman->nama_karyawan) }}
        @else
        SEMUA SALESMAN
        @endif
        <br />
        @if ($pelanggan != null)
        PELANGGAN {{ strtoupper($pelanggan->nama_pelanggan) }}
        @else
        SEMUA PELANGGAN
        @endif
    </b>
    <table class="datatable3" style="width:200%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th rowspan="3" style="width: 1%;">No</th>
                <th rowspan="3" style="width: 3%;">No Faktur</th>
                <th rowspan="3" style="width: 3%;">Tgl Transaksi</th>
                <th rowspan="3" style="width: 3%;">Kode Pelanggan</th>
                <th rowspan="3" style="width: 5%;">Nama Pelanggan</th>
                <th rowspan="3" style="width: 4%;">Salesman</th>
                <th rowspan="3" style="width: 3%;">Pasar</th>
                <th rowspan="3" style="width: 3%;">Hari</th>
                <th colspan="30" style="background-color: #19c116;">Produk</th>
                <th rowspan="2" style="width: 3%; background-color: #ef6a0b;">Total Bruto</th>
                <th rowspan="2" style="width: 3%; background-color: #ef6a0b;">Total Retur</th>
                <th colspan="5" style="background-color: #a71033;">Potongan</th>
                <th rowspan="2" style="width: 3%; background-color: #f353c1;">Pot. Istimewa</th>
                <th rowspan="2" style="width: 3%; background-color: #024a75;">Total Netto</th>
                <th rowspan="2" style="width: 5%; background-color: #024a75;">Tunai / Kredit</th>
                <th rowspan="2" style="width: 3%; background-color: #024a75;">Total Bayar</th>
                <th rowspan="2" style="width: 3%; background-color: #024a75;">Last Payment</th>
                <th rowspan="2" style="width: 5%; background-color: #024a75;">Lunas / Belum Lunas</th>

            </tr>
            <tr style="background-color: #19c116;">
                <th style="width: 1%;" colspan="2">AB</th>
                <th style="width: 1%;" colspan="2">AR</th>
                <th style="width: 1%;" colspan="2">AS</th>
                <th style="width: 1%;" colspan="2">BB</th>
                <th style="width: 1%;" colspan="2">CG</th>
                <th style="width: 1%;" colspan="2">CGG</th>
                <th style="width: 1%;" colspan="2">DEP</th>
                <th style="width: 1%;" colspan="2">DK</th>
                <th style="width: 1%;" colspan="2">DS</th>
                <th style="width: 1%;" colspan="2">SP</th>
                <th style="width: 1%;" colspan="2">BBP</th>
                <th style="width: 1%;" colspan="2">SPP</th>
                <th style="width: 1%;" colspan="2">CG5</th>
                <th style="width: 1%;" colspan="2">SC</th>
                <th style="width: 1%;" colspan="2">SP8</th>
                <th style="width: 1%; background-color: #a71033;">AIDA</th>
                <th style="width: 1%; background-color: #a71033;">SWAN</th>
                <th style="width: 1%; background-color: #a71033;">STICK</th>
                <th style="width: 1%; background-color: #a71033;">SP</th>
                <th style="width: 1%; background-color: #a71033;">POTONGAN</th>

            </tr>
            <tr>
                <th>Qty</th>
                <th style="background: red">Retur</th>
                <th>Qty</th>
                <th style="background: red">Retur</th>
                <th>Qty</th>
                <th style="background: red">Retur</th>
                <th>Qty</th>
                <th style="background: red">Retur</th>
                <th>Qty</th>
                <th style="background: red">Retur</th>
                <th>Qty</th>
                <th style="background: red">Retur</th>
                <th>Qty</th>
                <th style="background: red">Retur</th>
                <th>Qty</th>
                <th style="background: red">Retur</th>
                <th>Qty</th>
                <th style="background: red">Retur</th>
                <th>Qty</th>
                <th style="background: red">Retur</th>
                <th>Qty</th>
                <th style="background: red">Retur</th>
                <th>Qty</th>
                <th style="background: red">Retur</th>
                <th>Qty</th>
                <th style="background: red">Retur</th>
                <th>Qty</th>
                <th style="background: red">Retur</th>
                <th>Qty</th>
                <th style="background: red">Retur</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($barang as $b)
            @php
            if ($b->kode_produk == "AB") {
            $isipcsdusAB = $b->isipcsdus;
            }

            if ($b->kode_produk == "AR") {
            $isipcsdusAR = $b->isipcsdus;
            }

            if ($b->kode_produk == "AS") {
            $isipcsdusAS = $b->isipcsdus;
            }

            if ($b->kode_produk == "BB") {
            $isipcsdusBB = $b->isipcsdus;
            }

            if ($b->kode_produk == "CG") {
            $isipcsdusCG = $b->isipcsdus;
            }

            if ($b->kode_produk == "CGG") {
            $isipcsdusCGG = $b->isipcsdus;
            }

            if ($b->kode_produk == "DEP") {
            $isipcsdusDEP = $b->isipcsdus;
            }

            if ($b->kode_produk == "DK") {
            $isipcsdusDK = $b->isipcsdus;
            }

            if ($b->kode_produk == "DS") {
            $isipcsdusDS = $b->isipcsdus;
            }

            if ($b->kode_produk == "SP") {
            $isipcsdusSP = $b->isipcsdus;
            }

            if ($b->kode_produk == "BBP") {
            $isipcsdusBBP = $b->isipcsdus;
            }

            if ($b->kode_produk == "SPP") {
            $isipcsdusSPP = $b->isipcsdus;
            }

            if ($b->kode_produk == "CG5") {
            $isipcsdusCG5 = $b->isipcsdus;
            }

            if ($b->kode_produk == "SC") {
            $isipcsdusSC = $b->isipcsdus;
            }
            if ($b->kode_produk == "SP8") {
            $isipcsdusSP8 = $b->isipcsdus;
            }
            @endphp
            @endforeach

            @php
            $no = 1;
            $totalbruto = 0;
            $totalretur = 0;
            $totalpotaida = 0;
            $totalpotswan = 0;
            $totalpotstick = 0;
            $totalpotsp = 0;
            $totalpotongan = 0;
            $totalpotis = 0;
            $totalnetto = 0;

            $totalAB = 0;
            $totalreturAB = 0;
            $totalAR = 0;
            $totalreturAR = 0;
            $totalAS = 0;
            $totalreturAS = 0;
            $totalBB = 0;
            $totalreturBB = 0;
            $totalCG = 0;
            $totalreturCG = 0;
            $totalCGG = 0;
            $totalreturCGG = 0;
            $totalDEP = 0;
            $totalreturDEP = 0;
            $totalDK = 0;
            $totalreturDK = 0;
            $totalDS = 0;
            $totalreturDS = 0;
            $totalSP = 0;
            $totalreturSP = 0;
            $totalBBP = 0;
            $totalreturBBP = 0;
            $totalSPP = 0;
            $totalreturSPP = 0;
            $totalCG5 = 0;
            $totalreturCG5 = 0;
            $totalSC = 0;
            $totalreturSC = 0;
            $totalSP8 = 0;
            $totalreturSP8 = 0;
            @endphp
            @foreach ($penjualan as $d)
            @php
            $totalbruto += $d->totalbruto;
            $totalretur += $d->totalretur;
            $totalpotaida += $d->potaida;
            $totalpotswan += $d->potswan;
            $totalpotstick += $d->potstick;
            $totalpotsp += $d->potsp;
            $totalpotongan += $d->potongan;
            $totalpotis += $d->potistimewa;
            $totalnetto += $d->totalnetto;

            if (!empty($d->AB)) {
            $AB = $d->AB / $isipcsdusAB;
            } else {
            $AB = 0;
            }

            if (!empty($d->retur_AB)) {
            $returAB = $d->retur_AB / $isipcsdusAB;
            } else {
            $returAB = 0;
            }


            if (!empty($d->AR)) {
            $AR = $d->AR / $isipcsdusAR;
            } else {
            $AR = 0;
            }

            if (!empty($d->retur_AR)) {
            $returAR = $d->retur_AR / $isipcsdusAR;
            } else {
            $returAR = 0;
            }



            if (!empty($d->AS)) {
            $AS = $d->AS / $isipcsdusAS;
            } else {
            $AS = 0;
            }

            if (!empty($d->retur_AS)) {
            $returAS = $d->retur_AS / $isipcsdusAS;
            } else {
            $returAS = 0;
            }

            if (!empty($d->BB)) {
            $BB = $d->BB / $isipcsdusBB;
            } else {
            $BB = 0;
            }

            if (!empty($d->retur_BB)) {
            $returBB = $d->retur_BB / $isipcsdusBB;
            } else {
            $returBB = 0;
            }

            if (!empty($d->CG)) {
            $CG = $d->CG / $isipcsdusCG;
            } else {
            $CG = 0;
            }

            if (!empty($d->retur_CG)) {
            $returCG = $d->retur_CG / $isipcsdusCG;
            } else {
            $returCG = 0;
            }

            if (!empty($d->CGG)) {
            $CGG = $d->CGG / $isipcsdusCGG;
            } else {
            $CGG = 0;
            }

            if (!empty($d->retur_CGG)) {
            $returCGG = $d->retur_CGG / $isipcsdusCGG;
            } else {
            $returCGG = 0;
            }

            if (!empty($d->DEP)) {
            $DEP = $d->DEP / $isipcsdusDEP;
            } else {
            $DEP = 0;
            }

            if (!empty($d->retur_DEP)) {
            $returDEP = $d->retur_DEP / $isipcsdusDEP;
            } else {
            $returDEP = 0;
            }

            if (!empty($d->DK)) {
            $DK = $d->DK / $isipcsdusDK;

            } else {
            $DK = 0;

            }

            if (!empty($d->retur_DK)) {
            $returDK = $d->retur_DK / $isipcsdusDK;
            } else {
            $returDK = 0;
            }

            if (!empty($d->DS)) {
            $DS = $d->DS / $isipcsdusDS;

            } else {
            $DS = 0;

            }

            if (!empty($d->retur_DS)) {
            $returDS = $d->retur_DS / $isipcsdusDS;
            } else {
            $returDS = 0;
            }

            if (!empty($d->SP)) {
            $SP = $d->SP / $isipcsdusSP;

            } else {
            $SP = 0;

            }

            if (!empty($d->retur_SP)) {

            $returSP = $d->retur_SP / $isipcsdusSP;
            } else {

            $returSP = 0;
            }

            if (!empty($d->BBP)) {
            $BBP = $d->BBP / $isipcsdusBBP;
            } else {
            $BBP = 0;
            }


            if (!empty($d->retur_BBP)) {
            $returBBP = $d->retur_BBP / $isipcsdusBBP;
            } else {
            $returBBP = 0;
            }

            if (!empty($d->SPP)) {
            $SPP = $d->SPP / $isipcsdusSPP;

            } else {
            $SPP = 0;

            }

            if (!empty($d->retur_SPP)) {

            $returSPP = $d->retur_SPP / $isipcsdusSPP;
            } else {

            $returSPP = 0;
            }

            if (!empty($d->CG5)) {
            $CG5 = $d->CG5 / $isipcsdusCG5;

            } else {
            $CG5 = 0;

            }

            if (!empty($d->retur_CG5)) {

            $returCG5 = $d->retur_CG5 / $isipcsdusCG5;
            } else {

            $returCG5 = 0;
            }

            if (!empty($d->SC)) {
            $SC = $d->SC / $isipcsdusSC;

            } else {
            $SC = 0;

            }

            if (!empty($d->retur_SC)) {

            $returSC = $d->retur_SC / $isipcsdusSC;
            } else {

            $returSC = 0;
            }

            if (!empty($d->SP8)) {
            $SP8 = $d->SP8 / $isipcsdusSP8;

            } else {
            $SP8 = 0;

            }

            if (!empty($d->retur_SP8)) {

            $returSP8 = $d->retur_SP8 / $isipcsdusSP8;
            } else {

            $returSP8 = 0;
            }
            $totalAB += $AB;
            $totalAR += $AR;
            $totalAS += $AS;
            $totalBB += $BB;
            $totalCG += $CG;
            $totalCGG += $CGG;
            $totalDEP += $DEP;
            $totalDK += $DK;
            $totalDS += $DS;
            $totalSP += $SP;
            $totalBBP += $BBP;
            $totalSPP += $SPP;
            $totalCG5 += $CG5;
            $totalSC += $SC;
            $totalSP8 += $SP8;

            $totalreturAB += $returAB;
            $totalreturAR += $returAR;
            $totalreturAS += $returAS;
            $totalreturBB += $returBB;
            $totalreturCG += $returCG;
            $totalreturCGG += $returCGG;
            $totalreturDEP += $returDEP;
            $totalreturDK += $returDK;
            $totalreturDS += $returDS;
            $totalreturSP += $returSP;
            $totalreturBBP += $returBBP;
            $totalreturSPP += $returSPP;
            $totalreturCG5 += $returCG5;
            $totalreturSC += $returSC;
            $totalreturSP8 += $returSP8;


            if ($d->status_lunas == 1) {
            $ket = "LUNAS";
            $colortext = "green";
            $color = "";
            } else {
            $ket = "BELUM LUNAS";
            $colortext = "red";
            $color = "#f70a4b61";
            }
            @endphp
            <tr style="font-size: 12px; background-color: {{ $color}}">
                <td align="center">{{ $loop->iteration}}</td>
                <td align="center">{{ $d->no_fak_penj}}</td>
                <td align="center">{{ date("d-m-y",strtotime($d->tgltransaksi))}}</td>
                <td align="center">{{ $d->kode_pelanggan}}</td>
                <td align="left">{{ ucwords(strtolower($d->nama_pelanggan))}}</td>
                <td align="left">{{ ucwords(strtolower($d->nama_karyawan))}}</td>
                <td align="left">{{ ucwords(strtolower($d->pasar))}}</td>
                <td align="left">{{ ucwords(strtolower($d->hari))}}</td>
                <td align="center">@php if (!empty($AB)) { echo desimal($AB); } @endphp </td>
                <td align="center">@php if (!empty($returAB)) { echo desimal($returAB); } @endphp </td>
                <td align="center">@php if (!empty($AR)) { echo desimal($AR); } @endphp </td>
                <td align="center">@php if (!empty($returAR)) { echo desimal($returAR); } @endphp </td>
                <td align="center">@php if (!empty($AS)) { echo desimal($AS); } @endphp </td>
                <td align="center">@php if (!empty($returAS)) { echo desimal($returAS); } @endphp </td>
                <td align="center">@php if (!empty($BB)) { echo desimal($BB); } @endphp </td>
                <td align="center">@php if (!empty($returBB)) { echo desimal($returBB); } @endphp </td>
                <td align="center">@php if (!empty($CG)) { echo desimal($CG); } @endphp </td>
                <td align="center">@php if (!empty($returCG)) { echo desimal($returCG); } @endphp </td>
                <td align="center">@php if (!empty($CGG)) { echo desimal($CGG); } @endphp </td>
                <td align="center">@php if (!empty($returCGG)) { echo desimal($returCGG); } @endphp </td>
                <td align="center">@php if (!empty($DEP)) { echo desimal($DEP); } @endphp </td>
                <td align="center">@php if (!empty($returDEP)) { echo desimal($returDEP); } @endphp </td>
                <td align="center">@php if (!empty($DK)) { echo desimal($DK); } @endphp </td>
                <td align="center">@php if (!empty($returDK)) { echo desimal($returDK); } @endphp </td>
                <td align="center">@php if (!empty($DS)) { echo desimal($DS); } @endphp </td>
                <td align="center">@php if (!empty($returDS)) { echo desimal($returDS); } @endphp </td>
                <td align="center">@php if (!empty($SP)) { echo desimal($SP); } @endphp </td>
                <td align="center">@php if (!empty($returSP)) { echo desimal($returSP); } @endphp </td>
                <td align="center">@php if (!empty($BBP)) { echo desimal($BBP); } @endphp </td>
                <td align="center">@php if (!empty($returBBP)) { echo desimal($returBBP); } @endphp </td>
                <td align="center">@php if (!empty($SPP)) { echo desimal($SPP); } @endphp </td>
                <td align="center">@php if (!empty($returSPP)) { echo desimal($returSPP); } @endphp </td>
                <td align="center">@php if (!empty($CG5)) { echo desimal($CG5); } @endphp </td>
                <td align="center">@php if (!empty($returCG5)) { echo desimal($returCG5); } @endphp </td>
                <td align="center">@php if (!empty($SC)) { echo desimal($SC); } @endphp </td>
                <td align="center">@php if (!empty($returSC)) { echo desimal($returSC); } @endphp </td>
                <td align="center">@php if (!empty($SP8)) { echo desimal($SP8); } @endphp </td>
                <td align="center">@php if (!empty($returSP8)) { echo desimal($returSP8); } @endphp </td>
                <td align="right"><b>{{ rupiah($d->totalbruto)}}</b></td>
                <td align="right"><b>@php if (!empty($d->totalretur)) { echo rupiah($d->totalretur);}@endphp</b></td>
                <td align="right"><b>@php if (!empty($d->potaida)) { echo rupiah($d->potaida);}@endphp</b></td>
                <td align="right"><b>@php if (!empty($d->potswan)) { echo rupiah($d->potswan);}@endphp</b></td>
                <td align="right"><b>@php if (!empty($d->potstick)) { echo rupiah($d->potstick);}@endphp</b></td>
                <td align="right"><b>@php if (!empty($d->potsp)) { echo rupiah($d->potsp);}@endphp</b></td>
                <td align="right"><b>@php if (!empty($d->potongan)) { echo rupiah($d->potongan);}@endphp</b></td>
                <td align="right"><b>@php if (!empty($d->potistimewa)) { echo rupiah($d->potistimewa);}@endphp</b></td>
                <td align="right"><b>@php if (!empty($d->totalnetto)) { echo rupiah($d->totalnetto);}@endphp</b></td>
                <td align="right"><b>@php if (!empty($d->jenistransaksi)) { echo ucwords($d->jenistransaksi); }@endphp</b></td>
                <td align="right"><b>@php if (!empty($d->totalbayar)) { echo rupiah($d->totalbayar);}@endphp</b></td>
                <td align="center"><b>@php if (!empty($d->lastpayment)) { echo date("d-m-y",strtotime($d->lastpayment));}@endphp</b></td>
                <td align="center" style="color: {{ $colortext}};"><b>{{ $ket}}</b></td>
            </tr>
            <tr>

            </tr>
            @endforeach
            <tr style="background-color: #024a75; color:white">
                <th colspan="8">TOTAL</th>
                <th align="right"><b>{{ desimal($totalAB)}}</b></th>
                <th align="right" style="background: red"><b>{{ desimal($totalreturAB)}}</b></th>
                <th align="right"><b>{{ desimal($totalAR)}}</b></th>
                <th align="right" style="background: red"><b>{{ desimal($totalreturAR)}}</b></th>
                <th align="right"><b>{{ desimal($totalAS)}}</b></th>
                <th align="right" style="background: red"><b>{{ desimal($totalreturAS)}}</b></th>
                <th align="right"><b>{{ desimal($totalBB)}}</b></th>
                <th align="right" style="background: red"><b>{{ desimal($totalreturBB)}}</b></th>
                <th align="right"><b>{{ desimal($totalCG)}}</b></th>
                <th align="right" style="background: red"><b>{{ desimal($totalreturCG)}}</b></th>
                <th align="right"><b>{{ desimal($totalCGG)}}</b></th>
                <th align="right" style="background: red"><b>{{ desimal($totalreturCGG)}}</b></th>
                <th align="right"><b>{{ desimal($totalDEP)}}</b></th>
                <th align="right" style="background: red"><b>{{ desimal($totalreturDEP)}}</b></th>
                <th align="right"><b>{{ desimal($totalDK)}}</b></th>
                <th align="right" style="background: red"><b>{{ desimal($totalreturDK)}}</b></th>
                <th align="right"><b>{{ desimal($totalDS)}}</b></th>
                <th align="right" style="background: red"><b>{{ desimal($totalreturDS)}}</b></th>
                <th align="right"><b>{{ desimal($totalSP)}}</b></th>
                <th align="right" style="background: red"><b>{{ desimal($totalreturSP)}}</b></th>
                <th align="right"><b>{{ desimal($totalBBP)}}</b></th>
                <th align="right" style="background: red"><b>{{ desimal($totalreturBBP)}}</b></th>
                <th align="right"><b>{{ desimal($totalSPP)}}</b></th>
                <th align="right" style="background: red"><b>{{ desimal($totalreturSPP)}}</b></th>
                <th align="right"><b>{{ desimal($totalCG5)}}</b></th>
                <th align="right" style="background: red"><b>{{ desimal($totalreturCG5)}}</b></th>
                <th align="right"><b>{{ desimal($totalSC)}}</b></th>
                <th align="right" style="background: red"><b>{{ desimal($totalreturSC)}}</b></th>
                <th align="right"><b>{{ desimal($totalSP8)}}</b></th>
                <th align="right" style="background: red"><b>{{ desimal($totalreturSP8)}}</b></th>
                <th align="right"><b>{{ desimal($totalbruto)}}</b></th>
                <th align="right"><b>{{ desimal($totalretur)}}</b></th>
                <th align="right"><b>{{ desimal($totalpotaida)}}</b></th>
                <th align="right"><b>{{ desimal($totalpotswan)}}</b></th>
                <th align="right"><b>{{ desimal($totalpotstick)}}</b></th>
                <th align="right"><b>{{ desimal($totalpotsp)}}</b></th>
                <th align="right"><b>{{ desimal($totalpotongan)}}</b></th>
                <th align="right"><b>{{ desimal($totalpotis)}}</b></th>
                <th align="right"><b>{{ desimal($totalnetto)}}</b></th>
                <th colspan="4"></th>
            </tr>
        </tbody>
    </table>
    <br>

    <table class="datatable3" style="width:40%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th colspan="5">SELLING OUT KOMISI</th>
            </tr>
            <tr>
                <th style="background-color: #35ce35;">BB & DP</th>
                <th style="background-color: #ffcb00;">SP8</th>
                <th style="background-color: #058cbe;">SP</th>
                <th style="background-color: #ce3ae4;">AR</th>
                <th style="background-color: #ff9b0d;">AB,AS,CG5</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                @php
                $BBDP = $totalBB + $totalDEP;
                $returBBDP = $totalreturBB + $totalreturDEP;
                $SP8 = $totalSP8;
                $returSP8 = $totalreturSP8;
                $SP = $totalSP;
                $returSP = $totalreturSP;
                $AR = $totalAR;
                $returAR = $totalreturAR;
                $ABAS = $totalAB + $totalAS;
                $returABAS = $totalreturAB + $totalreturAS;
                @endphp


                <th align="right"><b>{{ desimal($BBDP)}} - {{ desimal($returBBDP) }} = {{ desimal($BBDP - $returBBDP) }} </b></th>
                <th align="right"><b>{{ desimal($SP8)}} - {{ desimal($returSP8)  }} = {{ desimal($SP8 - $returSP8) }} </b></th>
                <th align="right"><b>{{ desimal($SP)}} - {{ desimal($returSP) }} = {{ desimal($SP - $returSP) }} </b></th>
                <th align="right"><b>{{ desimal($AR)}} - {{ desimal($returAR) }} = {{ desimal($AR - $returAR) }} </b></th>
                <th align="right"><b>{{ desimal($ABAS)}} - {{desimal($returABAS) }} = {{ desimal($ABAS - $returABAS) }} </b></th>
            </tr>
        </tbody>
    </table>

</body>

</html>
