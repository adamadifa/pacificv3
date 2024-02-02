<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Analisa Umur Piutang (AUP) {{ date('d-m-y') }}</title>
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

        a {
            color: white;
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
        @if ($cabang != null)
            @if ($cabang->kode_cabang == 'PST')
                PACIFIC PUSAT
            @else
                PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
            @endif
        @else
            PACIFC ALL CABANG
        @endif
        <br>
        LAPORAN INSENTIF KEPALA ADMIN<br>
        {{ $namabulan[$bulan * 1] }} {{ $tahun }}
    </b>
    <br>

    @php
        function getreward($realisasi)
        {
            if ($realisasi >= 60 && $realisasi <= 65) {
                $reward = 25000;
            } elseif ($realisasi > 65 && $realisasi <= 70) {
                $reward = 50000;
            } elseif ($realisasi > 70 && $realisasi <= 75) {
                $reward = 75000;
            } elseif ($realisasi > 75 && $realisasi <= 80) {
                $reward = 100000;
            } elseif ($realisasi > 80 && $realisasi <= 85) {
                $reward = 125000;
            } elseif ($realisasi > 85 && $realisasi <= 90) {
                $reward = 150000;
            } elseif ($realisasi > 90 && $realisasi <= 95) {
                $reward = 175000;
            } elseif ($realisasi > 95) {
                $reward = 200000;
            } else {
                $reward = 0;
            }

            return $reward;
        }
    @endphp
    <table class="datatable3">
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">CABANG</th>
                <th colspan="4">OA</th>
                <th colspan="2">KENDARAAN</th>
                <th colspan="2">PENJUALAN BERJALAN <br> VS PENJUALAN BULAN LALU</th>
                <th colspan="2">ROUTING</th>
                <th colspan="3">LPC H + 1</th>
                <th colspan="2">CASHIN</th>
                <th colspan="3">LJT</th>
                <th colspan="3">COSTRATIO</th>
                <th colspan="3">RATIO BS</th>
                <th rowspan="2">TOTAL</th>
            </tr>
            <tr>
                <th>JML PELANGGAN</th>
                <th>JML PELANGGAN <br> BERTRANSAKSI</th>
                <th>RATIO</th>
                <th>REWARD</th>
                <th>REALISASI</th>
                <th>REWARD</th>
                <th>REALISASI</th>
                <th>REWARD</th>
                <th>REALISASI</th>
                <th>REWARD</th>
                <th>LAMA</th>
                <th>JAM</th>
                <th>REWARD</th>
                <th>REALISASI</th>
                <th>REWARD</th>
                <th>REALISASI</th>
                <th>RATIO</th>
                <th>REWARD</th>
                <th>REALISASI</th>
                <th>RATIO</th>
                <th>REWARD</th>
                <th>REALISASI</th>
                <th>RATIO</th>
                <th>REWARD</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($insentif as $d)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $d->nama_cabang }}</td>
                    <td align="center">{{ !empty($d->jmlpelanggan) ? rupiah($d->jmlpelanggan) : '' }}</td>
                    <td align="center">{{ !empty($d->jmltrans) ? rupiah($d->jmltrans) : '' }}</td>

                    <td align="center">
                        @php
                            $ratio_oa = ROUND(!empty($d->jmlpelanggan) ? ($d->jmltrans / $d->jmlpelanggan) * 100 : 0);
                        @endphp
                        {{ $ratio_oa }} %
                    </td>
                    <td align="right">
                        @php
                            $reward_oa = getreward($ratio_oa);
                        @endphp
                        {{ rupiah($reward_oa) }}
                    </td>

                    <td align="center">{{ !empty($d->ratio_kendaraan) ? $d->ratio_kendaraan . '%' : '' }}</td>
                    <td align="right">
                        @php
                            $rewardkendaraan = getreward($d->ratio_kendaraan);
                        @endphp
                        {{ rupiah($rewardkendaraan) }}
                    </td>
                    <td align="center">{{ !empty($d->ratio_penjualan) ? $d->ratio_penjualan . '%' : '' }}</td>
                    <td align="right">
                        @php
                            $rewardpenjualan = getreward($d->ratio_penjualan);
                        @endphp
                        {{ rupiah($rewardpenjualan) }}
                    </td>
                    <td align="center">{{ !empty($d->ratio_routing) ? $d->ratio_routing . '%' : '' }}</td>
                    <td align="right">
                        @php
                            //$reward_routing = getreward($d->ratio_routing);
                            if ($d->ratio_routing >= 90 && $d->ratio_routing <= 95) {
                                $reward_routing = 100000;
                            } elseif ($d->ratio_routing > 95) {
                                $reward_routing = 200000;
                            }
                        @endphp
                        {{ rupiah($reward_routing) }}
                    </td>
                    <td align="center">{{ $d->lama_lpc }}</td>
                    <td align="center">{{ $d->jam_lpc }}</td>
                    <td align="right">
                        @if (!empty($d->lama_lpc) && $d->lama_lpc <= 1 && $d->jam_lpc <= '13:00')
                            @php
                                $reward_lpc = 350000;
                            @endphp
                        @else
                            @php
                                $reward_lpc = 0;
                            @endphp
                        @endif
                        {{ rupiah($reward_lpc) }}
                    </td>
                    <td style="text-align: right">
                        {{ !empty($d->realisasi_cashin) ? rupiah($d->realisasi_cashin) : '' }}</td>
                    <td style="text-align: right">
                        @php
                            $reward_cashin = (0.01 / 100) * $d->realisasi_cashin;
                        @endphp
                        {{ rupiah($reward_cashin) }}
                    </td>
                    <td style="text-align: right">
                        {{ !empty($d->sisapiutang) ? rupiah($d->sisapiutang) : '' }}
                    </td>
                    <td align="center">
                        @php
                            $ratio_ljt = ROUND(!empty($d->realisasi_cashin) ? ($d->sisapiutang / $d->realisasi_cashin) * 100 : 0, 2);
                        @endphp
                        {{ $ratio_ljt }}%
                    </td>
                    <td align="right">
                        @php
                            if ($ratio_ljt < 0.5) {
                                $reward_ljt = 200000;
                            } elseif ($ratio_ljt > 0.5 && $ratio_ljt <= 1) {
                                $reward_ljt = 150000;
                            } elseif ($ratio_ljt > 1 && $ratio_ljt <= 1.5) {
                                $reward_ljt = 100000;
                            } elseif ($ratio_ljt > 1.5 && $ratio_ljt <= 2) {
                                $reward_ljt = 50000;
                            } else {
                                $reward_ljt = 0;
                            }
                        @endphp
                        {{ rupiah($reward_ljt) }}
                    </td>
                    <td align="right">
                        {{ rupiah($d->totalbiaya) }}
                    </td>
                    <td align="center">
                        @php
                            if ($d->kode_cabang == 'TSM') {
                                $cost_ratio = ROUND(!empty($d->penjualanbulanberjalan) ? ($d->totalbiaya / $d->penjualanbulanberjalan) * 100 : 0) + 4;
                            } else {
                                $cost_ratio = ROUND(!empty($d->penjualanbulanberjalan) ? ($d->totalbiaya / $d->penjualanbulanberjalan) * 100 : 0);
                            }
                        @endphp
                        {{ $cost_ratio }} %
                    </td>
                    <td align="right">
                        @php
                            if ($cost_ratio <= 6) {
                                $reward_costratio = 200000;
                            } elseif ($cost_ratio > 6 && $cost_ratio <= 7) {
                                $reward_costratio = 175000;
                            } elseif ($cost_ratio > 7 && $cost_ratio <= 8) {
                                $reward_costratio = 150000;
                            } elseif ($cost_ratio > 8 && $cost_ratio <= 9) {
                                $reward_costratio = 125000;
                            } elseif ($cost_ratio > 9 && $cost_ratio <= 10) {
                                $reward_costratio = 100000;
                            } elseif ($cost_ratio > 10 && $cost_ratio <= 11) {
                                $reward_costratio = 75000;
                            } elseif ($cost_ratio > 11 && $cost_ratio <= 12) {
                                $reward_costratio = 50000;
                            } else {
                                $reward_costratio = 25000;
                            }
                        @endphp
                        {{ rupiah($reward_costratio) }}
                    </td>
                    <td align="right">
                        @php
                            $totalharga = 0;
                        @endphp
                        @foreach ($produk->get() as $p)
                            @php
                                $jmlreject = $d->{"reject_pasar_$p->kode_produk"} + $d->{"reject_mobil_$p->kode_produk"} + $d->{"reject_gudang_$p->kode_produk"} - $d->{"repack_$p->kode_produk"};
                                $harga = $d->{"reject_pasar_$p->kode_produk"} > 0 ? $d->{"totalretur_$p->kode_produk"} / $d->{"retur_$p->kode_produk"} : 0;
                                $total = ROUND($jmlreject, 2) * $harga;
                                $totalharga += $total;
                            @endphp
                        @endforeach
                        {{ rupiah($totalharga) }}
                    </td>
                    <td align="center">
                        @php
                            $ratio_bs = ROUND(!empty($d->realisasi_cashin) ? (ROUND($totalharga) / $d->realisasi_cashin) * 100 : 0, 2);
                        @endphp
                        {{ $ratio_bs }}%
                    </td>
                    <td align="right">
                        @if ($ratio_bs <= 0.4)
                            @php
                                $reward_bs = 125000;
                            @endphp
                        @elseif ($ratio_bs > 0.4 && $ratio_bs <= 0.6)
                            @php
                                $reward_bs = 100000;
                            @endphp
                        @elseif ($ratio_bs > 0.6 && $ratio_bs <= 0.8)
                            @php
                                $reward_bs = 75000;
                            @endphp
                        @elseif ($ratio_bs > 0.8 && $ratio_bs <= 1)
                            @php
                                $reward_bs = 50000;
                            @endphp
                        @else
                            @php
                                $reward_bs = 25000;
                            @endphp
                        @endif
                        {{ rupiah($reward_bs) }}
                    </td>
                    <td align="right">
                        @php
                            $totalreward = $reward_oa + $rewardkendaraan + $rewardpenjualan + $reward_routing + $reward_lpc + $reward_cashin + $reward_ljt + $reward_costratio + $reward_bs;
                        @endphp
                        {{ rupiah($totalreward) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
</body>

</html>
