<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cost Ratio {{ date('d-m-y') }}</title>
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
        COST RATIO<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }} <br>
    </b>
    <br>
</body>
<table class="datatable3" @if (empty($kode_cabang)) style="width:100%" @endif border="1">
    <thead>
        <tr>
            <th style="background-color:rgb(0, 52, 93); color:white">No</th>
            <th style="background-color:rgb(0, 52, 93); color:white">Kode Akun</th>
            <th style="background-color:rgb(0, 52, 93); color:white">Nama Akun</th>
            @foreach ($cbg as $c)
                <th style="background-color: rgb(0, 77, 0); color:white">{{ strtoupper($c->nama_cabang) }}</th>
            @endforeach
            @if (empty($kode_cabang))
                <th style="background-color:rgb(0, 52, 93); color:white">Jumlah</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($cbg as $e)
            @php
                $fieldtotal_cabang = strtolower($e->kode_cabang);
                ${"total$fieldtotal_cabang"} = 0;
            @endphp
        @endforeach
        @php
            $grandtotal = 0;
        @endphp
        @foreach ($biaya as $d)
            @foreach ($cbg as $g)
                @php
                    $fieldtotal_cabang = strtolower($g->kode_cabang);
                    ${"total$fieldtotal_cabang"} += $d->$fieldtotal_cabang;
                @endphp
            @endforeach
            @php
                $grandtotal += $d->total;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td style="text-align: center">'{{ $d->kode_akun }}</td>
                <td>
                    @php
                        if ($d->kode_akun == 1) {
                            $nama_akun = 'Sewa Gedung';
                        } elseif ($d->kode_akun == 2) {
                            $nama_akun = 'Ratio BS';
                        } else {
                            $nama_akun = $d->nama_akun;
                        }

                        echo $nama_akun;
                    @endphp
                </td>
                @foreach ($cbg as $b)
                    @php
                        $field_cabang = strtolower($b->kode_cabang);
                    @endphp
                    <td style="text-align:right">{{ !empty($d->$field_cabang) ? rupiah($d->$field_cabang) : '' }}</td>
                @endforeach
                @if (empty($kode_cabang))
                    <td style="text-align:right">{{ !empty($d->total) ? rupiah($d->total) : '' }}</td>
                @endif
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td style="text-align: center"></td>
            <td>
                Logistik
            </td>
            @foreach ($cbg as $c)
                @php
                    $field_cabang = strtolower($c->kode_cabang);
                @endphp
                <td style="text-align:right">
                    {{ !empty($logistik->$field_cabang) ? rupiah($logistik->$field_cabang) : '' }}
                </td>
            @endforeach
            @if (empty($kode_cabang))
                <td style="text-align:right">
                    {{ !empty($logistik->total) ? rupiah($logistik->total) : '' }}
                </td>
            @endif
        </tr>
        <tr>
            <td></td>
            <td style="text-align: center"></td>
            <td>
                Penggunaan Bahan Kemasan
            </td>
            @foreach ($cbg as $c)
                @php
                    $field_cabang = strtolower($c->kode_cabang);
                @endphp
                <td style="text-align:right">
                    {{ !empty($bahan->$field_cabang) ? rupiah($bahan->$field_cabang) : '' }}
                </td>
            @endforeach
            @if (empty($kode_cabang))
                <td style="text-align:right">
                    {{ !empty($bahan->total) ? rupiah($bahan->total) : '' }}
                </td>
            @endif
        </tr>
        @foreach ($cbg as $f)
            @php
                $kode_cbg = strtolower($f->kode_cabang);
                ${"total$kode_cbg"} += $logistik->$kode_cbg + $bahan->$kode_cbg;
            @endphp
        @endforeach
        @php
            $grandtotal += $logistik->total + $bahan->total;
        @endphp
    </tbody>
    <tfoot>
        <tr>
            <th style="background-color:rgb(0, 52, 93); color:white" colspan="3">TOTAL</th>
            @foreach ($cbg as $f)
                @php
                    $kode_cbg = strtolower($f->kode_cabang);
                @endphp
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">
                    {{ rupiah(${"total$kode_cbg"}) }}
                </th>
            @endforeach
            @if (empty($kode_cabang))
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">
                    {{ rupiah($grandtotal) }}
                </th>
            @endif

        </tr>
        <!-- Penjualan SWAN -->
        @foreach ($cbg as $c)
            @php
                $kode_cbg = strtolower($c->kode_cabang);

                //SWAN
                $swan_cbg = 'netswan' . $c->kode_cabang;
                $returswan_cbg = 'returswan' . $c->kode_cabang;
                ${"swan_$kode_cbg"} = $penjualan->$swan_cbg - $retur->$returswan_cbg;
                ${"cr_swan_biaya_$kode_cbg"} = ${"swan_$kode_cbg"} != 0 ? ROUND((${"total$kode_cbg"} / ${"swan_$kode_cbg"}) * 100) : 0;

                //AIDA
                $aida_cbg = 'netaida' . $c->kode_cabang;
                $returaida_cbg = 'returaida' . $c->kode_cabang;
                ${"aida_$kode_cbg"} = $penjualan->$aida_cbg - $retur->$returaida_cbg;
                ${"cr_aida_biaya_$kode_cbg"} = ${"aida_$kode_cbg"} != 0 ? ROUND((${"total$kode_cbg"} / ${"aida_$kode_cbg"}) * 100) : 0;

                //PPN
                $ppn_cbg = 'ppn_' . $kode_cbg;
                ${"ppn_$kode_cbg"} = $ppn->$ppn_cbg;

                //Penjualan
                ${"penjualan_$kode_cbg"} = ${"swan_$kode_cbg"} + ${"aida_$kode_cbg"} + ${"ppn_$kode_cbg"};
                ${"cr_penjualan_biaya_$kode_cbg"} = ${"penjualan_$kode_cbg"} != 0 ? ROUND((${"total$kode_cbg"} / ${"penjualan_$kode_cbg"}) * 100) : 0;

                //Piutang
                $piutang_cbg = 'piutang_' . strtoupper($kode_cbg);
                ${'piutang_' . $kode_cbg} = $piutang->$piutang_cbg;
                ${"cr_swan_piutang_$kode_cbg"} = ${"swan_$kode_cbg"} != 0 ? ROUND((${'piutang_' . $kode_cbg} / ${"swan_$kode_cbg"}) * 100) : 0;
                ${"cr_aida_piutang_$kode_cbg"} = ${"aida_$kode_cbg"} != 0 ? ROUND((${'piutang_' . $kode_cbg} / ${"aida_$kode_cbg"}) * 100) : 0;
                ${"cr_penjualan_piutang_$kode_cbg"} = ${"penjualan_$kode_cbg"} != 0 ? ROUND((${'piutang_' . $kode_cbg} / ${"penjualan_$kode_cbg"}) * 100) : 0;

                //Biaya + Piutang
                ${'biaya_piutang_' . $kode_cbg} = ${"total$kode_cbg"} + ${'piutang_' . $kode_cbg};
                ${"cr_swan_biayapiutang_$kode_cbg"} = ${"swan_$kode_cbg"} != 0 ? ROUND((${'biaya_piutang_' . $kode_cbg} / ${"swan_$kode_cbg"}) * 100) : 0;
                ${"cr_aida_biayapiutang_$kode_cbg"} = ${"aida_$kode_cbg"} != 0 ? ROUND((${'biaya_piutang_' . $kode_cbg} / ${"aida_$kode_cbg"}) * 100) : 0;
                ${"cr_penjualan_biayapiutang_$kode_cbg"} = ${"penjualan_$kode_cbg"} != 0 ? ROUND((${'biaya_piutang_' . $kode_cbg} / ${"penjualan_$kode_cbg"}) * 100) : 0;
            @endphp
        @endforeach
        @php
            //Swan
            $totalswan = $penjualan->totalswan - $retur->totalreturswan;
            $cr_swan_biaya_total = $totalswan != 0 ? ROUND(($grandtotal / $totalswan) * 100) : 0;

            //Aida
            $totalaida = $penjualan->totalaida - $retur->totalreturaida;
            $cr_aida_biaya_total = $totalaida != 0 ? ROUND(($grandtotal / $totalaida) * 100) : 0;

            $totalppn = $ppn->total;
            $totalpenjualan = $totalswan + $totalaida + $totalppn;
            $cr_penjualan_biaya_total = $totalpenjualan != 0 ? ROUND(($grandtotal / $totalpenjualan) * 100) : 0;

            $totalpiutang = $piutang->totalpiutang;
            $cr_swan_piutang_total = $totalswan != 0 ? ROUND(($totalpiutang / $totalswan) * 100) : 0;
            $cr_aida_piutang_total = $totalaida != 0 ? ROUND(($totalpiutang / $totalaida) * 100) : 0;
            $cr_penjualan_piutang_total = $totalpenjualan != 0 ? ROUND(($totalpiutang / $totalpenjualan) * 100) : 0;

            $total_biaya_piutang = $grandtotal + $totalpiutang;
            $cr_swan_biayapiutang_total = $totalswan != 0 ? ROUND(($total_biaya_piutang / $totalswan) * 100) : 0;
            $cr_aida_biayapiutang_total = $totalaida != 0 ? ROUND(($total_biaya_piutang / $totalaida) * 100) : 0;
            $cr_penjualan_biayapiutang_total = $totalpenjualan != 0 ? ROUND(($total_biaya_piutang / $totalpenjualan) * 100) : 0;
        @endphp
        <tr>
            <th style="background-color:rgb(0, 52, 93); color:white" colspan="2" rowspan="4">PENJUALAN</th>
            <th style="background-color:rgb(0, 52, 93); color:white">SWAN</th>
            @foreach ($cbg as $f)
                @php
                    $kode_cbg = strtolower($f->kode_cabang);
                @endphp
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">
                    {{ rupiah(${"swan_$kode_cbg"}) }}
                </th>
            @endforeach
            @if (empty($kode_cabang))
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">
                    {{ rupiah($totalswan) }}
                </th>
            @endif
        </tr>
        <tr>
            <th style="background-color:rgb(0, 52, 93); color:white">COST RATIO(%)</th>
            @foreach ($cbg as $f)
                @php
                    $kode_cbg = strtolower($f->kode_cabang);
                @endphp
                <th style="background-color:rgb(0, 52, 93); color:white">
                    {{ rupiah(${"cr_swan_biaya_$kode_cbg"}) }}%
                </th>
            @endforeach
            @if (empty($kode_cabang))
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_swan_biaya_total }}%</th>
            @endif

        </tr>
        <tr>
            <th style="background-color:rgb(93, 0, 0); color:white">AIDA</th>
            @foreach ($cbg as $f)
                @php
                    $kode_cbg = strtolower($f->kode_cabang);
                @endphp
                <th style="background-color:rgb(93, 0, 0); color:white; text-align:right">
                    {{ rupiah(${"aida_$kode_cbg"}) }}
                </th>
            @endforeach
            @if (empty($kode_cabang))
                <th style="background-color:rgb(93, 0, 0); color:white; text-align:right">
                    {{ rupiah($totalaida) }}
                </th>
            @endif
        </tr>
        <tr>
            <th style="background-color:rgb(93, 0, 0); color:white;">COST RATIO(%)</th>
            @foreach ($cbg as $f)
                @php
                    $kode_cbg = strtolower($f->kode_cabang);
                @endphp
                <th style="background-color:rgb(93, 0, 0); color:white;">
                    {{ rupiah(${"cr_aida_biaya_$kode_cbg"}) }}%
                </th>
            @endforeach
            @if (empty($kode_cabang))
                <th style="background-color:rgb(93, 0, 0); color:white;">{{ $cr_swan_biaya_total }}%</th>
            @endif
        </tr>
        <tr>
            <th style="background-color:rgb(0, 52, 93); color:white" colspan="3">TOTAL PPN</th>
            @foreach ($cbg as $f)
                @php
                    $kode_cbg = strtolower($f->kode_cabang);
                @endphp
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">
                    {{ rupiah(${"ppn_$kode_cbg"}) }}
                </th>
            @endforeach
            @if (empty($kode_cabang))
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">
                    {{ rupiah($totalppn) }}
                </th>
            @endif
        </tr>
        <tr>
            <th style="background-color:rgb(0, 52, 93); color:white" colspan="3">TOTAL PENJUALAN + PPN</th>
            @foreach ($cbg as $f)
                @php
                    $kode_cbg = strtolower($f->kode_cabang);
                @endphp
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">
                    {{ rupiah(${"penjualan_$kode_cbg"}) }}
                </th>
            @endforeach
            @if (empty($kode_cabang))
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">
                    {{ rupiah($totalpenjualan) }}
                </th>
            @endif
        </tr>
        <tr>
            <th style="background-color:rgb(0, 52, 93); color:white" colspan="3">COST RATIO(%)</th>
            @foreach ($cbg as $f)
                @php
                    $kode_cbg = strtolower($f->kode_cabang);
                @endphp
                <th style="background-color:rgb(0, 52, 93); color:white;">
                    {{ rupiah(${"cr_penjualan_biaya_$kode_cbg"}) }}%
                </th>
            @endforeach
            @if (empty($kode_cabang))
                <th style="background-color:rgb(0, 52, 93); color:white;">
                    {{ rupiah($cr_penjualan_biaya_total) }}%
                </th>
            @endif
        </tr>
        <tr>
            <th style="background-color:rgb(210, 59, 4); color:white" colspan="3">PIUTANG > 1 BULAN</th>
            @foreach ($cbg as $f)
                @php
                    $kode_cbg = strtolower($f->kode_cabang);
                @endphp
                <th style="background-color:rgb(210, 59, 4); color:white; text-align:right">
                    {{ rupiah(${"piutang_$kode_cbg"}) }}
                </th>
            @endforeach
            @if (empty($kode_cabang))
                <th style="background-color:rgb(210, 59, 4); color:white; text-align:right">
                    {{ rupiah($totalpiutang) }}
                </th>
            @endif
        </tr>
        <tr>
            <th style="background-color:rgb(210, 59, 4); color:white" colspan="3">COST RATIO SWAN</th>
            @foreach ($cbg as $f)
                @php
                    $kode_cbg = strtolower($f->kode_cabang);
                @endphp
                <th style="background-color:rgb(210, 59, 4); color:white">
                    {{ ${"cr_swan_piutang_$kode_cbg"} }}%
                </th>
            @endforeach
            @if (empty($kode_cabang))
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_swan_piutang_total }}%</th>
            @endif
        </tr>
        <tr>
            <th style="background-color:rgb(210, 59, 4); color:white" colspan="3">COST RATIO AIDA</th>
            @foreach ($cbg as $f)
                @php
                    $kode_cbg = strtolower($f->kode_cabang);
                @endphp
                <th style="background-color:rgb(210, 59, 4); color:white"> {{ ${"cr_aida_piutang_$kode_cbg"} }}%</th>
            @endforeach
            @if (empty($kode_cabang))
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_aida_piutang_total }}%</th>
            @endif
        </tr>
        <tr>
            <th style="background-color:rgb(210, 59, 4); color:white" colspan="3">COST RATIO SWAN + AIDA</th>
            @foreach ($cbg as $f)
                @php
                    $kode_cbg = strtolower($f->kode_cabang);
                @endphp
                <th style="background-color:rgb(210, 59, 4); color:white"> {{ ${"cr_penjualan_piutang_$kode_cbg"} }}%
                </th>
            @endforeach
            @if (empty($kode_cabang))
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_penjualan_piutang_total }}%</th>
            @endif
        </tr>
        <tr>
            <th style="background-color:rgba(145, 2, 59, 0.961); color:white" colspan="3">BIAYA + PIUTANG</th>
            @foreach ($cbg as $f)
                @php
                    $kode_cbg = strtolower($f->kode_cabang);
                @endphp
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white; text-align:right">
                    {{ rupiah(${"biaya_piutang_$kode_cbg"}) }}
                </th>
            @endforeach
            @if (empty($kode_cabang))
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white; text-align:right">
                    {{ rupiah($total_biaya_piutang) }}
                </th>
            @endif

        </tr>
        <tr>
            <th style="background-color:rgba(145, 2, 59, 0.961); color:white" colspan="3">COST RATIO SWAN</th>
            @foreach ($cbg as $f)
                @php
                    $kode_cbg = strtolower($f->kode_cabang);
                @endphp
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">
                    {{ rupiah(${"cr_swan_biayapiutang_$kode_cbg"}) }}%
                </th>
            @endforeach
            @if (empty($kode_cabang))
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_swan_biayapiutang_total }}%
                </th>
            @endif
        </tr>
        <tr>
            <th style="background-color:rgba(145, 2, 59, 0.961); color:white" colspan="3">COST RATIO AIDA</th>
            @foreach ($cbg as $f)
                @php
                    $kode_cbg = strtolower($f->kode_cabang);
                @endphp
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">
                    {{ rupiah(${"cr_aida_biayapiutang_$kode_cbg"}) }}%
                </th>
            @endforeach
            @if (empty($kode_cabang))
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_aida_biayapiutang_total }}%
                </th>
            @endif
        </tr>
        <tr>
            <th style="background-color:rgba(145, 2, 59, 0.961); color:white" colspan="3">COST RATIO SWAN + AIDA</th>
            @foreach ($cbg as $f)
                @php
                    $kode_cbg = strtolower($f->kode_cabang);
                @endphp
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">
                    {{ rupiah(${"cr_penjualan_biayapiutang_$kode_cbg"}) }}%
                </th>
            @endforeach
            @if (empty($kode_cabang))
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">
                    {{ $cr_penjualan_biayapiutang_total }}%
                </th>
            @endif
        </tr>
    </tfoot>
</table>

</html>
