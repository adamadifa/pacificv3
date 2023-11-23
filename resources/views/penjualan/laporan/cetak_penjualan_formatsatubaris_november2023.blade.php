<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Penjualan Format Satu Baris</title>
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
                <th rowspan="2" style="width: 1%;">No</th>
                <th rowspan="2" style="width: 3%;">No Faktur</th>
                <th rowspan="2" style="width: 3%;">Tgl Transaksi</th>
                <th rowspan="2" style="width: 3%;">Kode Pelanggan</th>
                <th rowspan="2" style="width: 5%;">Nama Pelanggan</th>
                <th rowspan="2" style="width: 4%;">Salesman</th>
                <th rowspan="2" style="width: 3%;">Pasar</th>
                <th rowspan="2" style="width: 3%;">Hari</th>
                <th colspan="11" style="background-color: #19c116;">Produk</th>
                <th rowspan="2" style="width: 3%; background-color: #ef6a0b;">Total Bruto</th>
                <th rowspan="2" style="width: 3%; background-color: #ef6a0b;">Total Retur</th>
                <th colspan="5" style="background-color: #a71033;">Potongan</th>
                <th rowspan="2" style="width: 3%; background-color: #f353c1;">Pot. Istimewa</th>
                <th rowspan="2" style="width: 3%; background-color: #3daad9;">PPN 11%</th>
                <th rowspan="2" style="width: 3%; background-color: #024a75;">Total Netto</th>
                <th rowspan="2" style="width: 5%; background-color: #024a75;">Tunai / Kredit</th>
                <th rowspan="2" style="width: 3%; background-color: #024a75;">Total Bayar</th>
                <th rowspan="2" style="width: 3%; background-color: #024a75;">Last Payment</th>
                <th rowspan="2" style="width: 5%; background-color: #024a75;">Lunas / Belum Lunas</th>

            </tr>
            <tr style="background-color: #19c116;">
                <th style="width: 1%;">AB</th>
                <th style="width: 1%;">AR</th>
                <th style="width: 1%;">AS</th>
                <th style="width: 1%;">BB</th>
                <th style="width: 1%;">DEP</th>

                <th style="width: 1%;">SP</th>

                <th style="width: 1%;">SC</th>
                <th style="width: 1%;">SP8</th>
                <th style="width: 1%;">SP8-P</th>
                <th style="width: 1%;">SP500</th>
                <th style="width: 1%;">BR20</th>
                <th style="width: 1%; background-color: #a71033;">AIDA</th>
                <th style="width: 1%; background-color: #a71033;">SWAN</th>
                <th style="width: 1%; background-color: #a71033;">STICK</th>
                <th style="width: 1%; background-color: #a71033;">SP</th>
                <th style="width: 1%; background-color: #a71033;">POTONGAN</th>
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

            if ($b->kode_produk == "SP8-P") {
            $isipcsdusSP8P = $b->isipcsdus;
            }

            if ($b->kode_produk == "SP500") {
            $isipcsdusSP500 = $b->isipcsdus;
            }

            if ($b->kode_produk == "BR20") {
            $isipcsdusBR20 = $b->isipcsdus;
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
            $totalppn = 0;
            $totalnetto = 0;

            $totalAB = 0;
            $totalAR = 0;
            $totalAS = 0;
            $totalBB = 0;
            $totalCG = 0;
            $totalCGG = 0;
            $totalDEP = 0;
            $totalDK = 0;
            $totalDS = 0;
            $totalSP = 0;
            $totalBBP = 0;
            $totalSPP = 0;
            $totalCG5 = 0;
            $totalSC = 0;
            $totalSP8 = 0;
            $totalSP8P = 0;
            $totalSP500 = 0;
            $totalBR20 = 0;
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
            $totalppn += $d->ppn;
            $totalnetto += $d->totalnetto;

            if (!empty($d->AB)) {
            $AB = $d->AB / $isipcsdusAB;
            } else {
            $AB = 0;
            }

            if (!empty($d->AR)) {
            $AR = $d->AR / $isipcsdusAR;
            } else {
            $AR = 0;
            }


            if (!empty($d->AS)) {
            $AS = $d->AS / $isipcsdusAS;
            } else {
            $AS = 0;
            }

            if (!empty($d->BB)) {
            $BB = $d->BB / $isipcsdusBB;
            } else {
            $BB = 0;
            }

            if (!empty($d->CG)) {
            $CG = $d->CG / $isipcsdusCG;
            } else {
            $CG = 0;
            }

            if (!empty($d->CGG)) {
            $CGG = $d->CGG / $isipcsdusCGG;
            } else {
            $CGG = 0;
            }

            if (!empty($d->DEP)) {
            $DEP = $d->DEP / $isipcsdusDEP;
            } else {
            $DEP = 0;
            }

            if (!empty($d->DK)) {
            $DK = $d->DK / $isipcsdusDK;
            } else {
            $DK = 0;
            }

            if (!empty($d->DS)) {
            $DS = $d->DS / $isipcsdusDS;
            } else {
            $DS = 0;
            }

            if (!empty($d->SP)) {
            $SP = $d->SP / $isipcsdusSP;
            } else {
            $SP = 0;
            }

            if (!empty($d->BBP)) {
            $BBP = $d->BBP / $isipcsdusBBP;
            } else {
            $BBP = 0;
            }

            if (!empty($d->SPP)) {
            $SPP = $d->SPP / $isipcsdusSPP;
            } else {
            $SPP = 0;
            }

            if (!empty($d->CG5)) {
            $CG5 = $d->CG5 / $isipcsdusCG5;
            } else {
            $CG5 = 0;
            }

            if (!empty($d->SC)) {
            $SC = $d->SC / $isipcsdusSC;
            } else {
            $SC = 0;
            }

            if (!empty($d->SP8)) {
            $SP8 = $d->SP8 / $isipcsdusSP8;
            } else {
            $SP8 = 0;
            }

            if (!empty($d->SP8P)) {
            $SP8P = $d->SP8P / $isipcsdusSP8P;
            } else {
            $SP8P = 0;
            }

            if (!empty($d->SP500)) {
            $SP500 = $d->SP500 / $isipcsdusSP500;
            } else {
            $SP500 = 0;
            }

            if (!empty($d->BR20)) {
            $BR20 = $d->BR20 / $isipcsdusBR20;
            } else {
            $BR20 = 0;
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
            $totalSP8P += $SP8P;
            $totalSP500 += $SP500;
            $totalBR20 += $BR20;


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
                <td align="center">@php if (!empty($AR)) { echo desimal($AR); } @endphp </td>
                <td align="center">@php if (!empty($AS)) { echo desimal($AS); } @endphp </td>
                <td align="center">@php if (!empty($BB)) { echo desimal($BB); } @endphp </td>
                <td align="center">@php if (!empty($DEP)) { echo desimal($DEP); } @endphp </td>

                <td align="center">@php if (!empty($SP)) { echo desimal($SP); } @endphp </td>

                <td align="center">@php if (!empty($SC)) { echo desimal($SC); } @endphp </td>
                <td align="center">@php if (!empty($SP8)) { echo desimal($SP8); } @endphp </td>
                <td align="center">@php if (!empty($SP8P)) { echo desimal($SP8P); } @endphp </td>
                <td align="center">@php if (!empty($SP500)) { echo desimal($SP500); } @endphp </td>
                <td align="center">@php if (!empty($BR20)) { echo desimal($BR20); } @endphp </td>
                <td align="right"><b>{{ rupiah($d->totalbruto)}}</b></td>
                <td align="right"><b>@php if (!empty($d->totalretur)) { echo rupiah($d->totalretur);}@endphp</b></td>
                <td align="right"><b>@php if (!empty($d->potaida)) { echo rupiah($d->potaida);}@endphp</b></td>
                <td align="right"><b>@php if (!empty($d->potswan)) { echo rupiah($d->potswan);}@endphp</b></td>
                <td align="right"><b>@php if (!empty($d->potstick)) { echo rupiah($d->potstick);}@endphp</b></td>
                <td align="right"><b>@php if (!empty($d->potsp)) { echo rupiah($d->potsp);}@endphp</b></td>
                <td align="right"><b>@php if (!empty($d->potongan)) { echo rupiah($d->potongan);}@endphp</b></td>
                <td align="right"><b>@php if (!empty($d->potistimewa)) { echo rupiah($d->potistimewa);}@endphp</b></td>
                <td align="right"><b>@php if (!empty($d->ppn)) { echo rupiah($d->ppn);}@endphp</b></td>
                <td align="right"><b>@php if (!empty($d->totalnetto)) { echo rupiah($d->totalnetto);}@endphp</b></td>
                <td align="right"><b>@php if (!empty($d->jenistransaksi)) { echo ucwords($d->jenistransaksi); }@endphp</b></td>
                <td align="right"><b>@php if (!empty($d->totalbayar)) { echo rupiah($d->totalbayar);}@endphp</b></td>
                <td align="center"><b>@php if (!empty($d->lastpayment)) { echo date("d-m-y",strtotime($d->lastpayment));}@endphp</b></td>
                <td align="center" style="color: {{ $colortext}};"><b>{{ $ket}} </b></td>
            </tr>
            @endforeach
            <tr style="background-color: #024a75; color:white">
                <th colspan="8">TOTAL</th>
                <th align="right"><b>{{ desimal($totalAB)}}</b></th>
                <th align="right"><b>{{ desimal($totalAR)}}</b></th>
                <th align="right"><b>{{ desimal($totalAS)}}</b></th>
                <th align="right"><b>{{ desimal($totalBB)}}</b></th>
                <th align="right"><b>{{ desimal($totalDEP)}}</b></th>

                <th align="right"><b>{{ desimal($totalSP)}}</b></th>

                <th align="right"><b>{{ desimal($totalSC)}}</b></th>
                <th align="right"><b>{{ desimal($totalSP8)}}</b></th>
                <th align="right"><b>{{ desimal($totalSP8P)}}</b></th>
                <th align="right"><b>{{ desimal($totalSP500)}}</b></th>
                <th align="right"><b>{{ desimal($totalBR20)}}</b></th>

                <th align="right"><b>{{ desimal($totalbruto)}}</b></th>
                <th align="right"><b>{{ desimal($totalretur)}}</b></th>
                <th align="right"><b>{{ desimal($totalpotaida)}}</b></th>
                <th align="right"><b>{{ desimal($totalpotswan)}}</b></th>
                <th align="right"><b>{{ desimal($totalpotstick)}}</b></th>
                <th align="right"><b>{{ desimal($totalpotsp)}}</b></th>
                <th align="right"><b>{{ desimal($totalpotongan)}}</b></th>
                <th align="right"><b>{{ desimal($totalpotis)}}</b></th>
                <th align="right"><b>{{ desimal($totalppn)}}</b></th>
                <th align="right"><b>{{ desimal($totalnetto)}}</b></th>

            </tr>
        </tbody>
    </table>
</body>
</html>
