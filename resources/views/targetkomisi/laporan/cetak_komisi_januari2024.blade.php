<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Perhitungan Komisi Per Oktober 2023 {{ date('d-m-y') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
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
        PACIFIC CABANG {{ strtoupper($cbg->nama_cabang) }}
        <br>
        LAPORAN KOMISI<br>
        {{ $nmbulan }} {{ $tahun }}
    </b>
    <br>
    <div class="freeze-table">
        <table class="datatable3">
            <thead>
                <tr>
                    <th rowspan="3">NO</th>
                    <th rowspan="3">ID KARYAWAN</th>
                    <th rowspan="3">NAMA KARYAWAN</th>
                    <th rowspan="3">KATEGORI</th>
                    @foreach ($kategori_komisi as $d)
                        <th colspan="3" style="background-color: #35ce35;">TARGET {{ $d->nama_kategori }}</th>
                    @endforeach
                    <th rowspan="2" colspan="2" style="background-color: #ff570d;">TOTAL POIN</th>
                    <th rowspan="2" colspan="2" style="background-color: #ff570d;">KENDARAAN <br>Qty * Rp. 25</th>
                    <th rowspan="2" colspan="2" style="background-color: rgb(18, 161, 213)">OA</th>
                    <th rowspan="2" colspan="2" style="background-color: rgb(213, 18, 73)">PENJUALAN VS AVG</th>
                    <th rowspan="2" colspan="2" style="background-color: rgb(18, 57, 213)">ROUTING</th>
                    <th rowspan="2" colspan="2" style="background-color: rgb(255, 197, 24)">CASHIN</th>
                    <th rowspan="2" colspan="3" style="background-color: rgb(129, 18, 213)">LJT</th>
                    <th rowspan="3">TOTAL REWARD</th>
                </tr>
                <tr>
                    @foreach ($kategori_komisi as $d)
                        <th colspan="3" style="background-color: #35ce35;"> {{ $d->poin }}</th>
                    @endforeach

                </tr>
                <tr>
                    @foreach ($kategori_komisi as $d)
                        <th style="">TARGET</th>
                        <th style="">REALISASI</th>
                        <th style="">POIN</th>
                    @endforeach
                    <th>REALISASI</th>
                    <th>REWARD</th>

                    <th>REALISASI</th>
                    <th>REWARD</th>

                    <th>REALISASI</th>
                    <th>REWARD</th>

                    <th>REALISASI</th>
                    <th>REWARD</th>

                    <th>REALISASI</th>
                    <th>REWARD</th>

                    <th>REALISASI</th>
                    <th>REWARD</th>

                    <th>REALISASI</th>
                    <th>RATIO</th>
                    <th>REWARD</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $kebijakan = 100;
                    $jmlsales = 0;
                    $total_all_reward_qty = 0;
                    $total_all_realisasi_qty_kendaraan = 0;
                    $total_all_reward_qty_kendaraan = 0;
                    $total_all_reward_pelanggantrans = 0;
                    $total_all_realisasipenjvsavg = 0;
                    $total_all_reward_penjvsavg = 0;
                    $total_all_realisasi_cashin = 0;
                    $total_all_reward_cashin = 0;
                    $total_all_reward_routing = 0;
                    $total_all_realisasi_ljt = 0;
                    $total_all_reward_ljt = 0;
                @endphp
                @foreach ($kategori_komisi as $k)
                    @php
                        ${"total_target_$k->kode_kategori"} = 0;
                        ${"total_realisasi_qty_$k->kode_kategori"} = 0;
                    @endphp
                @endforeach

                @foreach ($komisi as $d)
                    @php
                        $realisasi_qty_kendaraan = 0;
                        $jmlsales += 1;
                    @endphp
                    @foreach ($produk as $p)
                        @php
                            $realisasi_qty_kendaraan += FLOOR($d->{"qty_$p->kode_produk"});
                        @endphp
                    @endforeach
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $d->id_karyawan }}</td>
                        <td>{{ strtoupper($d->nama_karyawan) }}</td>
                        <td><?php echo $d->kategori_salesman; ?></td>
                        @php
                            $totalpoin = 0;
                        @endphp
                        @foreach ($kategori_komisi as $k)
                            @php
                                ${"total_target_$k->kode_kategori"} += $d->{"target_$k->kode_kategori"};
                                ${"total_realisasi_qty_$k->kode_kategori"} += $d->{"realisasi_qty_$k->kode_kategori"};
                                if (empty($d->{"target_$k->kode_kategori"})) {
                                    ${"ratio_$k->kode_kategori"} = 0;
                                } else {
                                    ${"ratio_$k->kode_kategori"} = $d->{"realisasi_qty_$k->kode_kategori"} / $d->{"target_$k->kode_kategori"};
                                }

                                if (${"ratio_$k->kode_kategori"} > 1) {
                                    ${"hasilpoin_$k->kode_kategori"} = $k->poin;
                                } else {
                                    ${"hasilpoin_$k->kode_kategori"} = ${"ratio_$k->kode_kategori"} * $k->poin;
                                }

                                $totalpoin += ${"hasilpoin_$k->kode_kategori"};
                            @endphp


                            <td style="text-align:center">
                                {{ desimal($d->{"target_$k->kode_kategori"}) }}</td>
                            <td style="text-align:center">
                                {{ desimal($d->{"realisasi_qty_$k->kode_kategori"}) }}
                            </td>
                            <td style="text-align:center">
                                {{ desimal(${"hasilpoin_$k->kode_kategori"}) }}
                            </td>
                        @endforeach

                        <td style="text-align: center">{{ desimal($totalpoin) }}</td>
                        <td style="text-align: right">
                            {{-- Reward Poin --}}
                            @if ($d->status_komisi == 1)
                                @if ($totalpoin > 70 && $totalpoin <= 75)
                                    @php
                                        $reward_qty = 600000;
                                    @endphp
                                @elseif ($totalpoin > 75 && $totalpoin <= 80)
                                    @php
                                        $reward_qty = 1200000;
                                    @endphp
                                @elseif ($totalpoin > 80 && $totalpoin <= 85)
                                    @php
                                        $reward_qty = 1800000;
                                    @endphp
                                @elseif ($totalpoin > 85 && $totalpoin <= 90)
                                    @php
                                        $reward_qty = 2400000;
                                    @endphp
                                @elseif ($totalpoin > 90 && $totalpoin <= 95)
                                    @php
                                        $reward_qty = 3000000;
                                    @endphp
                                @elseif ($totalpoin > 95)
                                    @php
                                        $reward_qty = 3600000;
                                    @endphp
                                @else
                                    @php
                                        $reward_qty = 0;
                                    @endphp
                                @endif
                            @else
                                @php
                                    $reward_qty = 0;
                                @endphp
                            @endif
                            {{ rupiah($reward_qty) }}
                        </td>
                        <td style="text-align: center">{{ rupiah($realisasi_qty_kendaraan) }}</td>
                        <td style="text-align: right">
                            @php
                                if ($d->status_komisi == 1) {
                                    $reward_kendaraan = $realisasi_qty_kendaraan * 25;
                                } else {
                                    $reward_kendaraan = 0;
                                }
                            @endphp
                            {{ rupiah($reward_kendaraan) }}
                        </td>
                        <td style="text-align: center">{{ $d->realisasi_jmlpelanggantrans }}</td>
                        <td style="text-align: right">
                            @php
                                if ($d->status_komisi == 1) {
                                    $reward_pelanggantrans = $d->realisasi_jmlpelanggantrans * 2000;
                                } else {
                                    $reward_pelanggantrans = 0;
                                }
                            @endphp
                            {{ rupiah($reward_pelanggantrans) }}
                        </td>
                        <td style="text-align: center">{{ desimal($d->realisasipenjvsavg) }}</td>
                        <td style="text-align: right">
                            @php
                                if ($d->status_komisi == 1) {
                                    $reward_penjvsavg = $d->realisasipenjvsavg * 2000;
                                } else {
                                    $reward_penjvsavg = 0;
                                }
                            @endphp
                            {{ rupiah($reward_penjvsavg) }}
                        </td>
                        <td style="text-align: center">
                            @php
                                $persentaserouting = !empty($d->jmlkunjungan) ? ($d->jmlsesuaijadwal / $d->jmlkunjungan) * 100 : 0;
                            @endphp
                            {{ desimal($persentaserouting, 2) }}
                        </td>
                        <td style="text-align: right">
                            @php
                                if ($d->status_komisi == 1) {
                                    if ($persentaserouting >= 90 && $persentaserouting <= 95) {
                                        $reward_routing = 200000;
                                    } elseif ($persentaserouting > 95) {
                                        $reward_routing = 400000;
                                    } else {
                                        $reward_routing = 0;
                                    }
                                } else {
                                    $reward_routing = 0;
                                }
                            @endphp
                            {{ rupiah($reward_routing) }}
                        </td>
                        <td style="text-align: right">{{ rupiah($d->realisasi_cashin) }}</td>
                        <td style="text-align: right">
                            <!-- Reward Cashin-->
                            @php
                                $ratiocashin = 0.05;
                                if ($d->status_komisi == 1) {
                                    $reward_cashin = $d->realisasi_cashin * ($ratiocashin / 100);
                                } else {
                                    $reward_cashin = 0;
                                }

                            @endphp
                            {{ rupiah($reward_cashin) }}
                        </td>
                        <td style="text-align: right">{{ rupiah($d->sisapiutang) }}</td>
                        <td style="text-align: center">
                            <!-- Ratio LJT-->
                            @php
                                $ratioljt = !empty($d->realisasi_cashin) ? ($d->sisapiutang / $d->realisasi_cashin) * 100 * ($kebijakan / 100) : 0;
                                if ($ratioljt > 0) {
                                    $ratioljt = $ratioljt;
                                } else {
                                    $ratioljt = 0;
                                }
                            @endphp

                            {{ ROUND($ratioljt) }} %
                        </td>
                        <td style="text-align: right">
                            @php
                                if ($d->status_komisi == 1) {
                                    if ($ratioljt >= 0 and $ratioljt <= 0.5) {
                                        $rewardljt = 300000;
                                    } elseif ($ratioljt > 0.5 and $ratioljt <= 1) {
                                        $rewardljt = 225000;
                                    } elseif ($ratioljt > 1 and $ratioljt <= 1.5) {
                                        $rewardljt = 150000;
                                    } elseif ($ratioljt > 1.5 and $ratioljt <= 2) {
                                        $rewardljt = 75000;
                                    } else {
                                        $rewardljt = 0;
                                    }
                                } else {
                                    $rewardljt = 0;
                                }
                            @endphp
                            {{ rupiah($rewardljt) }}
                        </td>
                        <td style="text-align: right">
                            @php
                                $totalreward = $reward_qty + $reward_kendaraan + $reward_pelanggantrans + $reward_penjvsavg + $reward_routing + $reward_cashin + $rewardljt;
                            @endphp
                            {{ rupiah($totalreward) }}
                        </td>
                    </tr>
                    @php
                        $total_all_reward_qty += $reward_qty;
                        $total_all_realisasi_qty_kendaraan += $realisasi_qty_kendaraan;
                        $total_all_reward_qty_kendaraan += $reward_kendaraan;
                        $total_all_reward_pelanggantrans += $reward_pelanggantrans;
                        $total_all_realisasipenjvsavg += $d->realisasipenjvsavg;
                        $total_all_reward_penjvsavg += $reward_penjvsavg;
                        $total_all_reward_cashin += $reward_cashin;
                        $total_all_reward_routing += $reward_routing;
                        $total_all_realisasi_cashin += $d->realisasi_cashin;
                        $total_all_realisasi_ljt += $d->sisapiutang;
                        $total_all_reward_ljt += $rewardljt;
                    @endphp
                @endforeach
                @php
                    $reward_qty_spv = $total_all_reward_qty / $jmlsales;
                    $rewawrd_kendaraan_spv = $total_all_reward_qty_kendaraan / $jmlsales;
                    $reward_oa_spv = $total_all_reward_pelanggantrans / $jmlsales;
                    $reward_penjvsavg_spv = $total_all_reward_penjvsavg / $jmlsales;
                    $reward_cashin_spv = $total_all_reward_cashin / $jmlsales;
                    $reward_routing_spv = $total_all_reward_routing / $jmlsales;
                    $reward_ljt_spv = $total_all_reward_ljt / $jmlsales;
                    $total_reward_spv = $reward_qty_spv + $rewawrd_kendaraan_spv + $reward_oa_spv + $reward_penjvsavg_spv + $reward_cashin_spv + $reward_routing_spv + $reward_ljt_spv;

                    $reward_qty_kp = $reward_qty_spv * 2;
                    $rewawrd_kendaraan_kp = $rewawrd_kendaraan_spv * 2;
                    $reward_oa_kp = $reward_oa_spv * 2;
                    $reward_penjvsavg_kp = $reward_penjvsavg_spv * 2;
                    $reward_cashin_kp = $reward_cashin_spv * 2;
                    $reward_routing_kp = $reward_routing_spv * 2;
                    $reward_ljt_kp = $reward_ljt_spv * 2;
                    $total_reward_kp = $reward_qty_kp + $rewawrd_kendaraan_kp + $reward_oa_kp + $reward_penjvsavg_kp + $reward_cashin_kp + $reward_routing_kp + $reward_ljt_kp;
                @endphp
                @if ($cbg->kode_cabang == 'BDG')
                    <tr>
                        <th colspan="4">SUPERVISOR</th>
                        @php
                            $total_all_hasilpoin = 0;
                        @endphp
                        @foreach ($kategori_komisi as $k)
                            @php
                                ${"total_ratio_$k->kode_kategori"} = !empty(${"total_target_$k->kode_kategori"}) ? ${"total_realisasi_qty_$k->kode_kategori"} / ${"total_target_$k->kode_kategori"} : 0;
                                if (${"total_ratio_$k->kode_kategori"} > 1) {
                                    ${"total_hasilpoin_$k->kode_kategori"} = $k->poin;
                                } else {
                                    ${"total_hasilpoin_$k->kode_kategori"} = !empty(${"total_target_$k->kode_kategori"}) ? (${"total_realisasi_qty_$k->kode_kategori"} / ${"total_target_$k->kode_kategori"}) * $k->poin : 0;
                                }

                                $total_all_hasilpoin += ${"total_hasilpoin_$k->kode_kategori"};
                            @endphp
                            <th>{{ desimal(${"total_target_$k->kode_kategori"}) }}</th>
                            <th>{{ desimal(${"total_realisasi_qty_$k->kode_kategori"}) }}</th>
                            <th>{{ desimal(${"total_hasilpoin_$k->kode_kategori"}) }}</th>
                        @endforeach

                        <th>{{ desimal($total_all_hasilpoin) }}</th>
                        <th style="text-align: right">{{ rupiah($reward_qty_spv) }} </th>
                        <th>{{ desimal($total_all_realisasi_qty_kendaraan) }}</th>
                        <th style="text-align: right">{{ rupiah($rewawrd_kendaraan_spv) }} </th>
                        <th></th>
                        <th style="text-align: right">{{ rupiah($reward_oa_spv) }} </th>
                        <th>{{ desimal($total_all_realisasipenjvsavg) }}</th>
                        <th style="text-align: right">{{ rupiah($reward_penjvsavg_spv) }} </th>
                        <th></th>
                        <th style="text-align: right">{{ rupiah($reward_routing_spv) }} </th>
                        <th style="text-align: right">{{ rupiah($total_all_realisasi_cashin) }}</th>
                        <th style="text-align: right">{{ rupiah($reward_cashin_spv) }}</th>
                        <th style="text-align: right">{{ rupiah($total_all_realisasi_ljt) }}</th>
                        <th></th>
                        <th style="text-align: right">{{ rupiah($reward_ljt_spv) }}</th>
                        <th style="text-align: right">{{ rupiah($total_reward_spv) }}</th>
                    </tr>
                @endif

                <tr>
                    <th colspan="4">SMM</th>
                    @php
                        $total_all_hasilpoin = 0;
                    @endphp
                    @foreach ($kategori_komisi as $k)
                        @php
                            ${"total_ratio_$k->kode_kategori"} = !empty(${"total_target_$k->kode_kategori"}) ? ${"total_realisasi_qty_$k->kode_kategori"} / ${"total_target_$k->kode_kategori"} : 0;
                            if (${"total_ratio_$k->kode_kategori"} > 1) {
                                ${"total_hasilpoin_$k->kode_kategori"} = $k->poin;
                            } else {
                                ${"total_hasilpoin_$k->kode_kategori"} = !empty(${"total_target_$k->kode_kategori"}) ? (${"total_realisasi_qty_$k->kode_kategori"} / ${"total_target_$k->kode_kategori"}) * $k->poin : 0;
                            }

                            $total_all_hasilpoin += ${"total_hasilpoin_$k->kode_kategori"};
                        @endphp
                        <th>{{ desimal(${"total_target_$k->kode_kategori"}) }}</th>
                        <th>{{ desimal(${"total_realisasi_qty_$k->kode_kategori"}) }}</th>
                        <th>{{ desimal(${"total_hasilpoin_$k->kode_kategori"}) }}</th>
                    @endforeach
                    <th>{{ desimal($total_all_hasilpoin) }}</th>
                    <th style="text-align: right">{{ rupiah($reward_qty_kp) }} </th>
                    <th>{{ desimal($total_all_realisasi_qty_kendaraan) }}</th>
                    <th style="text-align: right">{{ rupiah($rewawrd_kendaraan_kp) }} </th>
                    <th></th>
                    <th style="text-align: right">{{ rupiah($reward_oa_kp) }} </th>
                    <th>{{ desimal($total_all_realisasipenjvsavg) }}</th>
                    <th style="text-align: right">{{ rupiah($reward_penjvsavg_kp) }} </th>
                    <th></th>
                    <th style="text-align: right">{{ rupiah($reward_routing_kp) }} </th>
                    <th style="text-align: right">{{ rupiah($total_all_realisasi_cashin) }}</th>
                    <th style="text-align: right">{{ rupiah($reward_cashin_kp) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_realisasi_ljt) }}</th>
                    <th></th>
                    <th style="text-align: right">{{ rupiah($reward_ljt_kp) }}</th>
                    <th style="text-align: right">{{ rupiah($total_reward_kp) }}</th>
                </tr>
            </tbody>
        </table>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="{{ asset('dist/js/freeze/js/freeze-table.js') }}"></script>
    <script>
        $(function() {
            $('.freeze-table').freezeTable({
                'scrollable': true,
                'columnNum': 4
            });
        });
    </script>
</body>

</html>
