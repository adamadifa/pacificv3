<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Analisa Umur Piutang (AUP) {{ date("d-m-y") }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
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

        .myreport {
            display: flex
        }

        .reportitem {
            margin: 10px;
        }

    </style>

</head>
<body>
    @foreach ($potongankomisikp as $d)
    @php
    ${"potongan$d->id_karyawan"} = $d->jumlah;
    @endphp
    @endforeach

    @foreach ($komisiakhirkp as $d)
    @php
    ${"ka$d->id_karyawan"} = $d->jumlah;
    @endphp
    @endforeach
    <b style="font-size:14px;">
        REKAP KOMISI <br>
        {{ $nmbulan }} {{ $tahun }}
    </b>
    <br>
    <br>
    <div class="myreport">
        <div class="reportitem">
            <table class="datatable3">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>ID KARYAWAN</th>
                        <th>NAMA KARYAWAN</th>
                        <th>KOMISI</th>
                        <th>KOMISI AKHIR</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $poinBBDP = 40;
                    $poinDS = 10;
                    $poinSP = 15;
                    $poinAR = 12.5;
                    $poinASABCG5 = 10;
                    $poinSC = 12.5;
                    $kebijakan = 100;

                    $subtotaltargetBBDP = 0;
                    $subtotaltargetDS = 0;
                    $subtotaltargetSP = 0;
                    $subtotaltargetAR = 0;
                    $subtotaltargetASAB = 0;
                    $subtotaltargetSC = 0;


                    $subtotalrealisasiBBDP = 0;
                    $subtotalrealisasiDS = 0;
                    $subtotalrealisasiSP = 0;
                    $subtotalrealisasiAR = 0;
                    $subtotalrealisasiASAB = 0;
                    $subtotalrealisasiSC = 0;

                    $subtotalrealisasicashin = 0;
                    $subtotalsisapiutang = 0;

                    $totalrewardsales = 0;
                    $totalkomisiakhirsales = 0;

                    $kebijakan = 100;
                    @endphp
                    @foreach ($komisi as $key => $d)
                    <?php
            $kode_cabang = @$komisi[$key + 1]->kode_cabang;
            $totalpoin = $d->poinBBDP + $d->poinSP8 + $d->poinSPSP500 + $d->poinAR + $d->poinASAB + $d->poinSC;

            $subtotaltargetBBDP += $d->target_BB_DP;
            $subtotaltargetDS += $d->target_DS;
            $subtotaltargetSP += $d->target_SP;
            $subtotaltargetAR += $d->target_AR;
            $subtotaltargetASAB += $d->target_AB_AS_CG5;
            $subtotaltargetSC += $d->target_SC;


            $subtotalrealisasiBBDP += $d->BBDP;
            $subtotalrealisasiDS += $d->SP8;
            $subtotalrealisasiSP += $d->SPSP500;
            $subtotalrealisasiAR += $d->AR;
            $subtotalrealisasiASAB += $d->ASAB;
            $subtotalrealisasiSC += $d->SC;

            $subtotalrealisasicashin += $d->realisasi_cashin;
            $subtotalsisapiutang += $d->sisapiutang;

            if (round($totalpoin,2) < 75) {
                $rewardpoin = 0;
            } else if (round($totalpoin,2) >= 75 and round($totalpoin,2) <= 75) {
                $rewardpoin = 750000;
            } else if (round($totalpoin,2) > 75 and round($totalpoin,2) <= 80) {
                $rewardpoin = 1500000;
            } else if (round($totalpoin,2) > 80 and round($totalpoin,2) <= 85) {
                $rewardpoin = 2250000;
            } else if (round($totalpoin,2) > 85 and round($totalpoin,2) <= 90) {
                $rewardpoin = 3000000;
            } else if (round($totalpoin,2) > 90 and round($totalpoin,2) <= 95) {
                $rewardpoin = 3750000;
            } else if (round($totalpoin,2) > 95 and round($totalpoin,2) <= 100) {
                $rewardpoin = 4500000;
            } else {
                $rewardpoin = 0;
            }

            if($d->kode_cabang == "BGR"){
                if ($d->kategori_salesman == "RETAIL") {
                    $ratiocashin = 0.30;
                } else {
                    $ratiocashin = 0.10;
                }
            }else{
                if ($d->kategori_salesman == "CANVASER" || $d->kategori_salesman == "RETAIL") {
                    $ratiocashin = 0.30;
                } else {
                    $ratiocashin = 0.10;
                }
            }

            $rewardcashin = $d->realisasi_cashin * ($ratiocashin / 100);


            $ratioljt = !empty($d->realisasi_cashin)  ? ($d->sisapiutang / $d->realisasi_cashin * 100) * ($kebijakan / 100) : 0;
            if ($ratioljt > 0) {
                $ratioljt = $ratioljt;
            } else {
                $ratioljt = 0;
            }

            if ($ratioljt >= 0 and $ratioljt <= 0.50) {
                $rewardljt = 1250000;
            } else  if ($ratioljt > 0.50 and $ratioljt <= 1) {
                $rewardljt = 1125000;
            } else  if ($ratioljt > 1 and $ratioljt <= 1.50) {
                $rewardljt = 1000000;
            } else  if ($ratioljt > 1.50 and $ratioljt <= 2) {
                $rewardljt = 875000;
            } else  if ($ratioljt > 2 and $ratioljt <= 2.50) {
                $rewardljt = 750000;
            } else  if ($ratioljt > 2.50 and $ratioljt <= 3) {
                $rewardljt = 625000;
            } else  if ($ratioljt > 3 and $ratioljt <= 3.50) {
                $rewardljt = 500000;
            } else  if ($ratioljt > 3.50 and $ratioljt <= 4) {
                $rewardljt = 375000;
            } else  if ($ratioljt > 4 and $ratioljt <= 4.50) {
                $rewardljt = 250000;
            } else  if ($ratioljt > 4.50 and $ratioljt <= 5) {
                $rewardljt = 125000;
            } else {
                $rewardljt = 0;
            }


            $totalreward = $rewardpoin + $rewardcashin + $rewardljt;
            $komisiakhirsales = !empty($d->komisifix) ?  $d->komisifix : $totalreward - $d->potongankomisi;
            $totalrewardsales += $totalreward;
            $totalkomisiakhirsales += $komisiakhirsales;
            ?>
                    <tr>

                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $d->id_karyawan }}</td>
                        <td>{{ $d->nama_karyawan }}</td>
                        <td style="text-align: right">{{ !empty($totalreward) ? desimal($totalreward) : 0 }}</td>
                        <td style="text-align: right">{{ !empty($komisiakhirsales) ? desimal($komisiakhirsales) : 0 }}</td>
                    </tr>
                    <?php
                if($kode_cabang != $d->kode_cabang){
                    $ratioBBDP_KP = !empty($subtotaltargetBBDP) ?  $subtotalrealisasiBBDP / $subtotaltargetBBDP : 0;
                    $poinBBDP_KP = $ratioBBDP_KP > 1 ? $poinBBDP  : $ratioBBDP_KP * $poinBBDP;

                    $ratioDS_KP = !empty($subtotaltargetDS) ?  $subtotalrealisasiDS / $subtotaltargetDS : 0;
                    $poinDS_KP = $ratioDS_KP > 1 ? $poinDS  : $ratioDS_KP * $poinDS;

                    $ratioSP_KP = !empty($subtotaltargetSP) ?  $subtotalrealisasiSP / $subtotaltargetSP : 0;
                    $poinSP_KP = $ratioSP_KP > 1 ? $poinSP  : $ratioSP_KP * $poinSP;

                    $ratioAR_KP = !empty($subtotaltargetAR) ?  $subtotalrealisasiAR / $subtotaltargetAR : 0;
                    $poinAR_KP = $ratioAR_KP > 1 ? $poinAR  : $ratioAR_KP * $poinAR;

                    $ratioASAB_KP = !empty($subtotaltargetASAB) ?  $subtotalrealisasiASAB / $subtotaltargetASAB : 0;
                    $poinASAB_KP = $ratioASAB_KP > 1 ? $poinASABCG5  : $ratioASAB_KP * $poinASABCG5;

                    $ratioSC_KP = !empty($subtotaltargetSC) ?  $subtotalrealisasiSC / $subtotaltargetSC : 0;
                    $poinSC_KP = $ratioSC_KP > 1 ? $poinSC  : $ratioSC_KP * $poinSC;

                    $totalpoin_KP = $poinBBDP_KP + $poinDS_KP + $poinSP_KP + $poinAR_KP + $poinASAB_KP + $poinSC_KP;

                    if ($totalpoin_KP < 70) {
                        $rewardpoin_KP = 0;
                    } else if ($totalpoin_KP >= 70 and $totalpoin_KP <= 75) {
                        $rewardpoin_KP = 1500000;
                    } else if ($totalpoin_KP > 75 and $totalpoin_KP <= 80) {
                        $rewardpoin_KP = 3000000;
                    } else if ($totalpoin_KP > 80 and $totalpoin_KP <= 85) {
                        $rewardpoin_KP = 4500000;
                    } else if ($totalpoin_KP > 85 and $totalpoin_KP <= 90) {
                        $rewardpoin_KP = 6000000;
                    } else if ($totalpoin_KP > 90 and $totalpoin_KP <= 95) {
                        $rewardpoin_KP = 7500000;
                    } else if ($totalpoin_KP > 95 and $totalpoin_KP <= 100) {
                        $rewardpoin_KP = 9000000;
                    } else {
                        $rewardpoin_KP = 0;
                    }

                    $rewardcashin_KP = $subtotalrealisasicashin * (0.05 / 100);
                    $ratioljt_KP = !empty($subtotalrealisasicashin)  ? ($subtotalsisapiutang / $subtotalrealisasicashin * 100) * ($kebijakan / 100) : 0;

                    if ($ratioljt_KP >= 0 and $ratioljt_KP <= 0.5) {
                        $rewardljt_KP = 2500000;
                    } else  if ($ratioljt_KP > 0.5 and $ratioljt_KP <= 1) {
                        $rewardljt_KP = 2250000;
                    } else  if ($ratioljt_KP > 1 and $ratioljt_KP <= 1.5) {
                        $rewardljt_KP = 2000000;
                    } else  if ($ratioljt_KP > 1.5 and $ratioljt_KP <= 2) {
                        $rewardljt_KP = 1750000;
                    } else  if ($ratioljt_KP > 2 and $ratioljt_KP <= 2.5) {
                        $rewardljt_KP = 1500000;
                    } else  if ($ratioljt_KP > 2.5 and $ratioljt_KP <= 3) {
                        $rewardljt_KP = 1250000;
                    } else  if ($ratioljt_KP > 3 and $ratioljt_KP <= 3.5) {
                        $rewardljt_KP = 1000000;
                    } else  if ($ratioljt_KP > 3.5 and $ratioljt_KP <= 4) {
                        $rewardljt_KP = 750000;
                    } else  if ($ratioljt_KP > 4 and $ratioljt_KP <= 4.5) {
                        $rewardljt_KP = 500000;
                    } else  if ($ratioljt_KP > 4.5 and $ratioljt_KP <= 5) {
                        $rewardljt_KP = 250000;
                    } else {
                        $rewardljt_KP = 0;
                    }
                    $totalreward_KP = $rewardpoin_KP + $rewardcashin_KP + $rewardljt_KP;

                    $kaKP = isset(${"kaKP$d->kode_cabang"}) ? ${"kaKP$d->kode_cabang"} : 0;
                    $potonganKP = isset(${"potonganKP$d->kode_cabang"}) ? ${"potonganKP$d->kode_cabang"} : 0;
                    $komisiakhir_KP = !empty($kaKP) ? $kaKP : $totalreward_KP - $potonganKP;

                    $totalrewardcabang = $totalrewardsales + $totalreward_KP ;
                    $totalkacabang = $totalkomisiakhirsales + $komisiakhir_KP;
                    ${"total$d->kode_cabang"} = $totalrewardcabang;
                    ${"totalcashin$d->kode_cabang"} = $subtotalrealisasicashin;
                    ${"totalka$d->kode_cabang"} = $totalkacabang;
            ?>
                    <tr style="background-color:rgb(0, 82, 145); color:white">
                        <td colspan="3">KEPALA PENJUALAN {{ $d->kode_cabang }}</td>
                        <td style="text-align: right">{{desimal($totalreward_KP) }}</td>
                        <td style="text-align: right">{{desimal($komisiakhir_KP) }}</td>

                    </tr>
                    <tr style="background-color:rgb(0, 82, 145); color:white">
                        <td colspan="3">TOTAL CABANG {{ $d->kode_cabang }}</td>
                        <td style="text-align: right">{{ desimal($totalrewardcabang) }}</td>
                        <td style="text-align: right">{{ desimal($totalkacabang) }}</td>
                    </tr>
                    <?php

                $subtotaltargetBBDP = 0;
                $subtotaltargetDS = 0;
                $subtotaltargetSP = 0;
                $subtotaltargetAR = 0;
                $subtotaltargetASAB = 0;
                $subtotaltargetSC = 0;

                $subtotalrealisasiBBDP = 0;
                $subtotalrealisasiDS = 0;
                $subtotalrealisasiSP = 0;
                $subtotalrealisasiAR = 0;
                $subtotalrealisasiASAB = 0;
                $subtotalrealisasiSC = 0;

                $subtotalrealisasicashin = 0;
                $subtotalsisapiutang = 0;

                $totalrewardsales = 0;
                $totalkomisiakhirsales = 0;
            } ?>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="reportitem">
            <table class="datatable3">
                <thead>
                    <tr>
                        <th>CABANG</th>
                        <th>CASHIN</th>
                        <th>KOMISI</th>
                        <th>RATIO</th>
                        <th>KOMISI AKHIR</th>
                        <th>RATIO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cabang as $d)
                    @php
                    $ratio = ROUND((${"total$d->kode_cabang"} / ${"totalcashin$d->kode_cabang"}) * 100,2);
                    $ratioakhir = ROUND((${"totalka$d->kode_cabang"} / ${"totalcashin$d->kode_cabang"}) * 100,2);
                    @endphp
                    <tr>
                        <td>{{ $d->nama_cabang }}</td>
                        <td style="text-align:right">{{ desimal(${"totalcashin$d->kode_cabang"}) }}</td>
                        <td style="text-align:right">{{ desimal(${"total$d->kode_cabang"}) }}</td>
                        <td style="text-align: center">{{ $ratio }}%</td>
                        <td style="text-align:right">{{ desimal(${"totalka$d->kode_cabang"}) }}</td>
                        <td style="text-align: center">{{ $ratioakhir }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
