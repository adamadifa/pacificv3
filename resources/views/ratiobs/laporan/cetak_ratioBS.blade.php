<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Ratio BS</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&display=swap');

        body {
            font-family: 'Poppins'
        }

        .datatable3 {
            border: 1px solid #000000;
            border-collapse: collapse;
            font-size: 11px;
        }

        .datatable3 td {
            border: 1px solid #000000;
            padding: 6px;
        }

        .datatable3 th {
            border: 1px solid #000000;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }

        tr:nth-child(even) {
            background-color: #d6d6d6c2;
        }
    </style>
</head>
@php
    if ($bulan == '1') {
        $bulans = 'JANUARI';
    } elseif ($bulan == '2') {
        $bulans = 'FEBRUARI';
    } elseif ($bulan == '3') {
        $bulans = 'MARET';
    } elseif ($bulan == '4') {
        $bulans = 'APRIL';
    } elseif ($bulan == '5') {
        $bulans = 'MEI';
    } elseif ($bulan == '6') {
        $bulans = 'JUNI';
    } elseif ($bulan == '7') {
        $bulans = 'JULI';
    } elseif ($bulan == '8') {
        $bulans = 'AGUSTUS';
    } elseif ($bulan == '9') {
        $bulans = 'SEPTEMBER';
    } elseif ($bulan == '10') {
        $bulans = 'OKTOBER';
    } elseif ($bulan == '11') {
        $bulans = 'NOVEMBER';
    } else {
        $bulans = 'DESEMBER';
    }
@endphp

<body>
    <b style="font-size:20px;">
        LAPORAN RATIO BS<br>
        PERIODE BULAN {{ $bulans }} TAHUN {{ $tahun }}<br>
        {{ $cabang != '' ? 'CABANG ' . $cabang : 'SEMUA CABANG' }}
        <br>
    </b>
    <br>
    @php
        $produk = DB::table('master_barang')
            ->where('status', 1)
            ->get();
        $jmlh = DB::table('master_barang')
            ->where('status', 1)
            ->count();
    @endphp
    <table class="datatable3" style="width:200.00%" border="1">
        <thead>
            <tr>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;">NO</th>
                <th rowspan="2"bgcolor="#024a75" style="color:white; font-size:14;">NAMA SALESMAN</th>
                <th rowspan="2"bgcolor="#024a75" style="color:white; font-size:14;">CAB.</th>
                @foreach ($produk as $p)
                    <th colspan="4" bgcolor="#024a75" style="color:white; font-size:14;">{{ $p->kode_produk }}</th>
                @endforeach
            </tr>
            <tr>
                @for ($i = 0; $i < $jmlh; $i++)
                    <th bgcolor="#024a75" style="color:white; font-size:14;">BS</th>
                    <th bgcolor="#024a75" style="color:white; font-size:14;">PNJ</th>
                    <th bgcolor="#024a75" style="color:white; font-size:14;">RATIO</th>
                    <th bgcolor="#024a75" style="color:white; font-size:14;"></th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @php
                $data = DB::table('karyawan')
                    ->select('nama_karyawan', 'karyawan.kode_cabang', 'qtyPenj_AB', 'qtyBS_AB', 'qtyPenj_AS', 'qtyBS_AS', 'qtyPenj_AR', 'qtyBS_AR', 'qtyPenj_BB', 'qtyBS_BB', 'qtyPenj_DEP', 'qtyBS_DEP', 'qtyPenj_SC', 'qtyBS_SC', 'qtyPenj_SP8', 'qtyPenj_SC', 'qtyBS_SC', 'qtyBS_SP8', 'qtyPenj_SP', 'qtyBS_SP', 'qtyPenj_SP500', 'qtyBS_SP500', 'qtyPenj_BR20', 'qtyBS_BR20')
                    ->leftJoin(
                        DB::raw("(SELECT id_karyawan,
                        SUM(IF(kode_produk = 'AB', ROUND(jumlah/isipcsdus, 2), 0)) AS qtyPenj_AB,
                        SUM(IF(kode_produk = 'AS', ROUND(jumlah/isipcsdus, 2), 0)) AS qtyPenj_AS,
                        SUM(IF(kode_produk = 'AR', ROUND(jumlah/isipcsdus, 2), 0)) AS qtyPenj_AR,
                        SUM(IF(kode_produk = 'BB', ROUND(jumlah/isipcsdus, 2), 0)) AS qtyPenj_BB,
                        SUM(IF(kode_produk = 'DEP', ROUND(jumlah/isipcsdus, 2), 0)) AS qtyPenj_DEP,
                        SUM(IF(kode_produk = 'SC', ROUND(jumlah/isipcsdus, 2), 0)) AS qtyPenj_SC,
                        SUM(IF(kode_produk = 'SP8', ROUND(jumlah/isipcsdus, 2), 0)) AS qtyPenj_SP8,
                        SUM(IF(kode_produk = 'SP', ROUND(jumlah/isipcsdus, 2), 0)) AS qtyPenj_SP,
                        SUM(IF(kode_produk = 'SP500', ROUND(jumlah/isipcsdus, 2), 0)) AS qtyPenj_SP500,
                        SUM(IF(kode_produk = 'BR20', ROUND(jumlah/isipcsdus, 2), 0)) AS qtyPenj_BR20
                        FROM detailpenjualan
                        INNER JOIN penjualan ON penjualan.no_fak_penj = detailpenjualan.no_fak_penj
                        INNER JOIN barang ON barang.kode_barang = detailpenjualan.kode_barang
                        WHERE MONTH(tgltransaksi) = '$bulan' AND  YEAR(tgltransaksi) = '$tahun' AND promo != '1' OR MONTH(tgltransaksi) = '$bulan' AND  YEAR(tgltransaksi) = '$tahun' AND promo IS NULL
                        GROUP BY id_karyawan) AS pnj"),
                        'pnj.id_karyawan',
                        '=',
                        'karyawan.id_karyawan',
                    )
                    ->leftJoin(
                        DB::raw("(SELECT id_karyawan,
                        SUM(IF(kode_produk = 'AB', ROUND(jumlah/isipcsdus, 2), 0)) AS qtyBS_AB,
                        SUM(IF(kode_produk = 'AS', ROUND(jumlah/isipcsdus, 2), 0)) AS qtyBS_AS,
                        SUM(IF(kode_produk = 'AR', ROUND(jumlah/isipcsdus, 2), 0)) AS qtyBS_AR,
                        SUM(IF(kode_produk = 'BB', ROUND(jumlah/isipcsdus, 2), 0)) AS qtyBS_BB,
                        SUM(IF(kode_produk = 'DEP', ROUND(jumlah/isipcsdus, 2), 0)) AS qtyBS_DEP,
                        SUM(IF(kode_produk = 'SC', ROUND(jumlah/isipcsdus, 2), 0)) AS qtyBS_SC,
                        SUM(IF(kode_produk = 'SP8', ROUND(jumlah/isipcsdus, 2), 0)) AS qtyBS_SP8,
                        SUM(IF(kode_produk = 'SP', ROUND(jumlah/isipcsdus, 2), 0)) AS qtyBS_SP,
                        SUM(IF(kode_produk = 'SP500', ROUND(jumlah/isipcsdus, 2), 0)) AS qtyBS_SP500,
                        SUM(IF(kode_produk = 'BR20', ROUND(jumlah/isipcsdus, 2), 0)) AS qtyBS_BR20
                            FROM detailretur
                            INNER JOIN penjualan ON penjualan.no_fak_penj = detailretur.no_fak_penj
                            INNER JOIN barang ON barang.kode_barang = detailretur.kode_barang
                            WHERE MONTH(tgltransaksi) = '$bulan' AND  YEAR(tgltransaksi) = '$tahun'
                            GROUP BY id_karyawan) AS retur"),
                        'retur.id_karyawan',
                        '=',
                        'karyawan.id_karyawan',
                    )
                    ->when($cabang, function ($query) use ($cabang) {
                        return $query->where('kode_cabang', $cabang);
                    })
                    ->where('karyawan.nama_karyawan', '!=', '-')
                    ->where('karyawan.status_aktif_sales', '1')
                    ->get();
                $no = 1;
                $grandTotalBS_AB = 0;
                $grandTotalBS_AS = 0;
                $grandTotalBS_AR = 0;
                $grandTotalBS_BB = 0;
                $grandTotalBS_BR20 = 0;
                $grandTotalBS_DEP = 0;
                $grandTotalBS_SC = 0;
                $grandTotalBS_SP = 0;
                $grandTotalBS_SP500 = 0;
                $grandTotalBS_SP8 = 0;

                $grandTotalPenj_AB = 0;
                $grandTotalPenj_AS = 0;
                $grandTotalPenj_AR = 0;
                $grandTotalPenj_BB = 0;
                $grandTotalPenj_BR20 = 0;
                $grandTotalPenj_DEP = 0;
                $grandTotalPenj_SC = 0;
                $grandTotalPenj_SP = 0;
                $grandTotalPenj_SP500 = 0;
                $grandTotalPenj_SP8 = 0;

                $totalBS_AB = 0;
                $totalBS_AS = 0;
                $totalBS_AR = 0;
                $totalBS_BB = 0;
                $totalBS_BR20 = 0;
                $totalBS_DEP = 0;
                $totalBS_SC = 0;
                $totalBS_SP = 0;
                $totalBS_SP500 = 0;
                $totalBS_SP8 = 0;

                $totalPenj_AB = 0;
                $totalPenj_AS = 0;
                $totalPenj_AR = 0;
                $totalPenj_BB = 0;
                $totalPenj_BR20 = 0;
                $totalPenj_DEP = 0;
                $totalPenj_SC = 0;
                $totalPenj_SP = 0;
                $totalPenj_SP500 = 0;
                $totalPenj_SP8 = 0;
            @endphp
            @foreach ($data as $key => $d)
                @php
                    $totalBS_AB += $d->qtyBS_AB;
                    $totalBS_AS += $d->qtyBS_AS;
                    $totalBS_AR += $d->qtyBS_AR;
                    $totalBS_BB += $d->qtyBS_BB;
                    $totalBS_BR20 += $d->qtyBS_BR20;
                    $totalBS_DEP += $d->qtyBS_DEP;
                    $totalBS_SC += $d->qtyBS_SC;
                    $totalBS_SP += $d->qtyBS_SP;
                    $totalBS_SP500 += $d->qtyBS_SP500;
                    $totalBS_SP8 += $d->qtyBS_SP8;

                    $totalPenj_AB += $d->qtyPenj_AB;
                    $totalPenj_AS += $d->qtyPenj_AS;
                    $totalPenj_AR += $d->qtyPenj_AR;
                    $totalPenj_BB += $d->qtyPenj_BB;
                    $totalPenj_BR20 += $d->qtyPenj_BR20;
                    $totalPenj_DEP += $d->qtyPenj_DEP;
                    $totalPenj_SC += $d->qtyPenj_SC;
                    $totalPenj_SP += $d->qtyPenj_SP;
                    $totalPenj_SP500 += $d->qtyPenj_SP500;
                    $totalPenj_SP8 += $d->qtyPenj_SP8;

                    $grandTotalBS_AB += $d->qtyBS_AB;
                    $grandTotalBS_AS += $d->qtyBS_AS;
                    $grandTotalBS_AR += $d->qtyBS_AR;
                    $grandTotalBS_BB += $d->qtyBS_BB;
                    $grandTotalBS_BR20 += $d->qtyBS_BR20;
                    $grandTotalBS_DEP += $d->qtyBS_DEP;
                    $grandTotalBS_SC += $d->qtyBS_SC;
                    $grandTotalBS_SP += $d->qtyBS_SP;
                    $grandTotalBS_SP500 += $d->qtyBS_SP500;
                    $grandTotalBS_SP8 += $d->qtyBS_SP8;

                    $grandTotalPenj_AB += $d->qtyPenj_AB;
                    $grandTotalPenj_AS += $d->qtyPenj_AS;
                    $grandTotalPenj_AR += $d->qtyPenj_AR;
                    $grandTotalPenj_BB += $d->qtyPenj_BB;
                    $grandTotalPenj_BR20 += $d->qtyPenj_BR20;
                    $grandTotalPenj_DEP += $d->qtyPenj_DEP;
                    $grandTotalPenj_SC += $d->qtyPenj_SC;
                    $grandTotalPenj_SP += $d->qtyPenj_SP;
                    $grandTotalPenj_SP500 += $d->qtyPenj_SP500;
                    $grandTotalPenj_SP8 += $d->qtyPenj_SP8;

                    $kode_cabang = @$data[$key + 1]->kode_cabang;
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $d->nama_karyawan }}</td>
                    <td>{{ $d->kode_cabang }}</td>

                    <td>{{ number_format($d->qtyBS_AB, 2) }}</td>
                    <td>{{ number_format($d->qtyPenj_AB, 2) }}</td>
                    @if ($d->qtyPenj_AB != 0)
                        <td>{{ number_format(($d->qtyBS_AB / $d->qtyPenj_AB) * 100, 2) }}%</td>
                    @else
                        <td>0.00%</td>
                    @endif
                    <td></td>

                    <td>{{ number_format($d->qtyBS_AR, 2) }}</td>
                    <td>{{ number_format($d->qtyPenj_AR, 2) }}</td>
                    @if ($d->qtyPenj_AR != 0)
                        <td>{{ number_format(($d->qtyBS_AR / $d->qtyPenj_AR) * 100, 2) }}%</td>
                    @else
                        <td>0.00%</td>
                    @endif
                    <td></td>

                    <td>{{ number_format($d->qtyBS_AS, 2) }}</td>
                    <td>{{ number_format($d->qtyPenj_AS, 2) }}</td>
                    @if ($d->qtyPenj_AS != 0)
                        <td>{{ number_format(($d->qtyBS_AS / $d->qtyPenj_AS) * 100, 2) }}%</td>
                    @else
                        <td>0.00%</td>
                    @endif
                    <td></td>

                    <td>{{ number_format($d->qtyBS_BB, 2) }}</td>
                    <td>{{ number_format($d->qtyPenj_BB, 2) }}</td>

                    @if ($d->qtyPenj_BB != 0)
                        <td>{{ number_format(($d->qtyBS_BB / $d->qtyPenj_BB) * 100, 2) }}%</td>
                    @else
                        <td>0.00%</td>
                    @endif
                    <td></td>

                    <td>{{ number_format($d->qtyBS_BR20, 2) }}</td>
                    <td>{{ number_format($d->qtyPenj_BR20, 2) }}</td>

                    @if ($d->qtyPenj_BR20 != 0)
                        <td>{{ number_format(($d->qtyBS_BR20 / $d->qtyPenj_BR20) * 100, 2) }}%</td>
                    @else
                        <td>0.00%</td>
                    @endif
                    <td></td>

                    <td>{{ number_format($d->qtyBS_DEP, 2) }}</td>
                    <td>{{ number_format($d->qtyPenj_DEP, 2) }}</td>
                    @if ($d->qtyPenj_DEP != 0)
                        <td>{{ number_format(($d->qtyBS_DEP / $d->qtyPenj_DEP) * 100, 2) }}%</td>
                    @else
                        <td>0.00%</td>
                    @endif
                    <td></td>

                    <td>{{ number_format($d->qtyBS_SC, 2) }}</td>
                    <td>{{ number_format($d->qtyPenj_SC, 2) }}</td>
                    @if ($d->qtyPenj_SC != 0)
                        <td>{{ number_format(($d->qtyBS_SC / $d->qtyPenj_SC) * 100, 2) }}%</td>
                    @else
                        <td>0.00%</td>
                    @endif
                    <td></td>

                    <td>{{ number_format($d->qtyBS_SP, 2) }}</td>
                    <td>{{ number_format($d->qtyPenj_SP, 2) }}</td>
                    @if ($d->qtyPenj_SP != 0)
                        <td>{{ number_format(($d->qtyBS_SP / $d->qtyPenj_SP) * 100, 2) }}%</td>
                    @else
                        <td>0.00%</td>
                    @endif
                    <td></td>

                    <td>{{ number_format($d->qtyBS_SP500, 2) }}</td>
                    <td>{{ number_format($d->qtyPenj_SP500, 2) }}</td>
                    @if ($d->qtyPenj_SP500 != 0)
                        <td>{{ number_format(($d->qtyBS_SP500 / $d->qtyPenj_SP500) * 100, 2) }}%</td>
                    @else
                        <td>0.00%</td>
                    @endif
                    <td></td>

                    <td>{{ number_format($d->qtyBS_SP8, 2) }}</td>
                    <td>{{ number_format($d->qtyPenj_SP8, 2) }}</td>
                    @if ($d->qtyPenj_SP8 != 0)
                        <td>{{ number_format(($d->qtyBS_SP8 / $d->qtyPenj_SP8) * 100, 2) }}%</td>
                    @else
                        <td>0.00%</td>
                    @endif
                    <td></td>
                </tr>
                @if ($kode_cabang != $d->kode_cabang)
                    <tr>
                        <th colspan="3">TOTAL</th>
                        <th>{{ number_format($totalBS_AB, 2) }}</th>
                        <th>{{ number_format($totalPenj_AB, 2) }}</th>
                        @if ($totalPenj_AB != 0)
                            <th>{{ number_format(($totalBS_AB / $totalPenj_AB) * 100, 2) }}%</th>
                        @else
                            <th>0.00%</th>
                        @endif
                        <th style="width: 1%"></th>

                        <th>{{ number_format($totalBS_AR, 2) }}</th>
                        <th>{{ number_format($totalPenj_AR, 2) }}</th>
                        @if ($totalPenj_AR != 0)
                            <th>{{ number_format(($totalBS_AR / $totalPenj_AR) * 100, 2) }}%</th>
                        @else
                            <th>0.00%</th>
                        @endif
                        <th></th>

                        <th>{{ number_format($totalBS_AS, 2) }}</th>
                        <th>{{ number_format($totalPenj_AS, 2) }}</th>
                        @if ($totalPenj_AS != 0)
                            <th>{{ number_format(($totalBS_AS / $totalPenj_AS) * 100, 2) }}%</th>
                        @else
                            <th>0.00%</th>
                        @endif
                        <th></th>

                        <th>{{ number_format($totalBS_BB, 2) }}</th>
                        <th>{{ number_format($totalPenj_BB, 2) }}</th>
                        @if ($totalPenj_BB != 0)
                            <th>{{ number_format(($totalBS_BB / $totalPenj_BB) * 100, 2) }}%</th>
                        @else
                            <th>0.00%</th>
                        @endif
                        <th></th>

                        <th>{{ number_format($totalBS_BR20, 2) }}</th>
                        <th>{{ number_format($totalPenj_BR20, 2) }}</th>
                        @if ($totalPenj_BR20 != 0)
                            <th>{{ number_format(($totalBS_BR20 / $totalPenj_BR20) * 100, 2) }}%</th>
                        @else
                            <th>0.00%</th>
                        @endif
                        <th></th>

                        <th>{{ number_format($totalBS_DEP, 2) }}</th>
                        <th>{{ number_format($totalPenj_DEP, 2) }}</th>
                        @if ($totalPenj_DEP != 0)
                            <th>{{ number_format(($totalBS_DEP / $totalPenj_DEP) * 100, 2) }}%</th>
                        @else
                            <th>0.00%</th>
                        @endif
                        <th></th>

                        <th>{{ number_format($totalBS_SC, 2) }}</th>
                        <th>{{ number_format($totalPenj_SC, 2) }}</th>
                        @if ($totalPenj_SC != 0)
                            <th>{{ number_format(($totalBS_SC / $totalPenj_SC) * 100, 2) }}%</th>
                        @else
                            <th>0.00%</th>
                        @endif
                        <th></th>

                        <th>{{ number_format($totalBS_SP, 2) }}</th>
                        <th>{{ number_format($totalPenj_SP, 2) }}</th>
                        @if ($totalPenj_SP != 0)
                            <th>{{ number_format(($totalBS_SP / $totalPenj_SP) * 100, 2) }}%</th>
                        @else
                            <th>0.00%</th>
                        @endif
                        <th></th>

                        <th>{{ number_format($totalBS_SP500, 2) }}</th>
                        <th>{{ number_format($totalPenj_SP500, 2) }}</th>
                        @if ($totalPenj_SP500 != 0)
                            <th>{{ number_format(($totalBS_SP500 / $totalPenj_SP500) * 100, 2) }}%</th>
                        @else
                            <th>0.00%</th>
                        @endif
                        <th></th>

                        <th>{{ number_format($totalBS_SP8, 2) }}</th>
                        <th>{{ number_format($totalPenj_SP8, 2) }}</th>
                        @if ($totalPenj_SP8 != 0)
                            <th>{{ number_format(($totalBS_SP8 / $totalPenj_SP8) * 100, 2) }}%</th>
                        @else
                            <th>0.00%</th>
                        @endif
                        <th></th>
                    </tr>
                    @php
                        $totalBS_AB = 0;
                        $totalBS_AS = 0;
                        $totalBS_AR = 0;
                        $totalBS_BB = 0;
                        $totalBS_BR20 = 0;
                        $totalBS_DEP = 0;
                        $totalBS_SC = 0;
                        $totalBS_SP = 0;
                        $totalBS_SP500 = 0;
                        $totalBS_SP8 = 0;

                        $totalPenj_AB = 0;
                        $totalPenj_AS = 0;
                        $totalPenj_AR = 0;
                        $totalPenj_BB = 0;
                        $totalPenj_BR20 = 0;
                        $totalPenj_DEP = 0;
                        $totalPenj_SC = 0;
                        $totalPenj_SP = 0;
                        $totalPenj_SP500 = 0;
                        $totalPenj_SP8 = 0;
                    @endphp
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">GRAND TOTAL</th>
                <th>{{ number_format($grandTotalBS_AB, 2) }}</th>
                <th>{{ number_format($grandTotalPenj_AB, 2) }}</th>
                @if ($grandTotalPenj_AB != 0)
                    <th>{{ number_format(($grandTotalBS_AB / $grandTotalPenj_AB) * 100, 2) }}%</th>
                @else
                    <th>0.00%</th>
                @endif
                <th style="width: 1%"></th>

                <th>{{ number_format($grandTotalBS_AR, 2) }}</th>
                <th>{{ number_format($grandTotalPenj_AR, 2) }}</th>
                @if ($grandTotalPenj_AR != 0)
                    <th>{{ number_format(($grandTotalBS_AR / $grandTotalPenj_AR) * 100, 2) }}%</th>
                @else
                    <th>0.00%</th>
                @endif
                <th></th>

                <th>{{ number_format($grandTotalBS_AS, 2) }}</th>
                <th>{{ number_format($grandTotalPenj_AS, 2) }}</th>
                @if ($grandTotalPenj_AS != 0)
                    <th>{{ number_format(($grandTotalBS_AS / $grandTotalPenj_AS) * 100, 2) }}%</th>
                @else
                    <th>0.00%</th>
                @endif
                <th></th>

                <th>{{ number_format($grandTotalBS_BB, 2) }}</th>
                <th>{{ number_format($grandTotalPenj_BB, 2) }}</th>
                @if ($grandTotalPenj_BB != 0)
                    <th>{{ number_format(($grandTotalBS_BB / $grandTotalPenj_BB) * 100, 2) }}%</th>
                @else
                    <th>0.00%</th>
                @endif
                <th></th>

                <th>{{ number_format($grandTotalBS_BR20, 2) }}</th>
                <th>{{ number_format($grandTotalPenj_BR20, 2) }}</th>
                @if ($grandTotalPenj_BR20 != 0)
                    <th>{{ number_format(($grandTotalBS_BR20 / $grandTotalPenj_BR20) * 100, 2) }}%</th>
                @else
                    <th>0.00%</th>
                @endif
                <th></th>

                <th>{{ number_format($grandTotalBS_DEP, 2) }}</th>
                <th>{{ number_format($grandTotalPenj_DEP, 2) }}</th>
                @if ($grandTotalPenj_DEP != 0)
                    <th>{{ number_format(($grandTotalBS_DEP / $grandTotalPenj_DEP) * 100, 2) }}%</th>
                @else
                    <th>0.00%</th>
                @endif
                <th></th>

                <th>{{ number_format($grandTotalBS_SC, 2) }}</th>
                <th>{{ number_format($grandTotalPenj_SC, 2) }}</th>
                @if ($grandTotalPenj_SC != 0)
                    <th>{{ number_format(($grandTotalBS_SC / $grandTotalPenj_SC) * 100, 2) }}%</th>
                @else
                    <th>0.00%</th>
                @endif
                <th></th>

                <th>{{ number_format($grandTotalBS_SP, 2) }}</th>
                <th>{{ number_format($grandTotalPenj_SP, 2) }}</th>
                @if ($grandTotalPenj_SP != 0)
                    <th>{{ number_format(($grandTotalBS_SP / $grandTotalPenj_SP) * 100, 2) }}%</th>
                @else
                    <th>0.00%</th>
                @endif
                <th></th>

                <th>{{ number_format($grandTotalBS_SP500, 2) }}</th>
                <th>{{ number_format($grandTotalPenj_SP500, 2) }}</th>
                @if ($grandTotalPenj_SP500 != 0)
                    <th>{{ number_format(($grandTotalBS_SP500 / $grandTotalPenj_SP500) * 100, 2) }}%</th>
                @else
                    <th>0.00%</th>
                @endif
                <th></th>

                <th>{{ number_format($grandTotalBS_SP8, 2) }}</th>
                <th>{{ number_format($grandTotalPenj_SP8, 2) }}</th>
                @if ($grandTotalPenj_SP8 != 0)
                    <th>{{ number_format(($grandTotalBS_SP8 / $grandTotalPenj_SP8) * 100, 2) }}%</th>
                @else
                    <th>0.00%</th>
                @endif
                <th></th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
