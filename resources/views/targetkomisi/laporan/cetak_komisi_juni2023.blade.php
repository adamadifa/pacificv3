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

    </style>

</head>

<body>

    <b style="font-size:14px;">
        @if ($cbg->kode_cabang=="PST")
        PACIFIC PUSAT
        @else
        PACIFIC CABANG {{ strtoupper($cbg->nama_cabang) }}
        @endif
        <br>
        LAPORAN KOMISI<br>
        {{ $nmbulan }} {{ $tahun }}
    </b>
    <br>
    @php
    $poinBBDP = 40;
    $poinDS = 10;
    $poinSP = 15;
    $poinAR = 12.5;
    $poinASABCG5 = 10;
    $poinSC = 12.5;
    $kebijakan = 100;

    @endphp
    <table class="datatable3" style="width:250%">
        <thead>
            <tr>
                <th rowspan="3">NO</th>
                <th rowspan="3">ID KARYAWAN</th>
                <th rowspan="3">NAMA KARYAWAN</th>
                <th rowspan="3">KATEGORI</th>
                <th colspan="3" style="background-color: #35ce35;">TARGET & REALISASI BB & DEP</th>
                <th colspan="3" style="background-color: #ffcb00;">TARGET & REALISASI SP8 </th>
                <th colspan="3" style="background-color: #058cbe;">TARGET & REALISASI SP & SP500 </th>
                <th colspan="3" style="background-color: #ce3ae4;">TARGET & REALISASI AR </th>
                <th colspan="3" style="background-color: #ff9b0d;">TARGET & REALISASI AS,AB</th>
                <th colspan="3" style="background-color: #0daeff;">TARGET & REALISASI SC</th>
                <th rowspan="2" colspan="2" style="background-color: #ff570d;">TOTAL POIN</th>
                <th rowspan="3">PELANGGAN AKTIF<br><small>{{ date('d-m-y',strtotime($startdate)) }} s/d {{ date('d-m-y',strtotime($enddate)) }}</small></th>
                <th rowspan="3">JUMLAH <br> TRANSAKSI</th>
                <th colspan="3" rowspan="2">PENETRASI SKU</th>
                <th colspan="2" rowspan="2">PRODUKTIVITAS KENDARAAN</th>
                <th colspan="2" rowspan="2">OUTLET AKTIF</th>
                <th colspan="2" rowspan="2">EFFECTIVE CALL</th>
                <th rowspan="2" colspan="3" style="background-color: #9e9895;">CASH IN</th>
                <th rowspan="2" colspan="3" style="background-color: #e43a90;">LJT</th>
                <th rowspan="3" style="background-color: #ff570d;">TOTAL REWARD</th>
                <th rowspan="3" style="background-color: #ffffff;">POTONGAN</th>
                <th rowspan="3" style="background-color: #ffffff;">TOTAL KOMISI</th>
                <th rowspan="3" style="background-color: #ffffff;">KOMISI AKHIR</th>
                <th rowspan="3" style="background-color: #ffffff;">KETERANGAN</th>

            </tr>
            <tr style="text-align: center;">
                <th colspan="3" style="background-color: #35ce35;"><?php echo $poinBBDP; ?></th>
                <th colspan="3" style="background-color: #ffcb00;"><?php echo $poinDS; ?></th>
                <th colspan="3" style="background-color: #058cbe;"><?php echo $poinSP; ?></th>
                <th colspan="3" style="background-color: #ce3ae4;"><?php echo $poinAR; ?></th>
                <th colspan="3" style="background-color: #ff9b0d;"> <?php echo $poinASABCG5; ?></th>
                <th colspan="3" style="background-color: #0daeff;"><?php echo $poinSC; ?></th>

            </tr>
            <tr>
                <th style="background-color: #35ce35;">TARGET</th>
                <th style="background-color: #35ce35;">REALISASI</th>
                <th style="background-color: #35ce35;">POIN</th>
                <th style="background-color: #ffcb00;">TARGET</th>
                <th style="background-color: #ffcb00;">REALISASI</th>
                <th style="background-color: #ffcb00;">POIN</th>
                <th style="background-color: #058cbe;">TARGET</th>
                <th style="background-color: #058cbe;">REALISASI</th>
                <th style="background-color: #058cbe;">POIN</th>
                <th style="background-color: #ce3ae4;">TARGET</th>
                <th style="background-color: #ce3ae4;">REALISASI</th>
                <th style="background-color: #ce3ae4;">POIN</th>
                <th style="background-color: #ff9b0d;">TARGET</th>
                <th style="background-color: #ff9b0d;">REALISASI</th>
                <th style="background-color: #ff9b0d;">POIN</th>
                <th style="background-color: #0daeff;">TARGET</th>
                <th style="background-color: #0daeff;">REALISASI</th>
                <th style="background-color: #0daeff;">POIN</th>
                <th style="background-color: #ff570d;">TOTAL POIN</th>
                <th style="background-color: #ff570d;">REWARD</th>
                <th> > 3 SKU</th>
                <th>PERSENTASE</th>
                <th>REWARD</th>
                <th>PERSENTASE</th>
                <th>REWARD</th>
                <th>PERSENTASE</th>
                <th>REWARD</th>
                <th>PERSENTASE</th>
                <th>REWARD</th>
                <th style="background-color: #9e9895;">REALISASI</th>
                <th style="background-color: #9e9895;">RATIO</th>
                <th style="background-color: #9e9895;">REWARD</th>
                <th style="background-color: #e43a90;">REALISASI</th>
                <th style="background-color: #e43a90;">RATIO</th>
                <th style="background-color: #e43a90;">REWARD</th>
            </tr>
        </thead>
        <tbody style="font-size:12px !important">
            <?php
            foreach ($produk as $b) {
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

                if ($b->kode_produk == "SP500") {
                $isipcsdusSP500 = $b->isipcsdus;
                }
            }
            $totaltargetBBDP = 0;
            $totalrealisasiBBDP = 0;
            $totalhasilpoinBBDP = 0;

            $totaltargetDS = 0;
            $totalrealisasiDS = 0;
            $totalhasilpoinDS = 0;

            $totaltargetSP = 0;
            $totalrealisasiSP = 0;
            $totalhasilpoinSP = 0;

            $totaltargetAR = 0;
            $totalrealisasiAR = 0;
            $totalhasilpoinAR = 0;

            $totaltargetABASCG5 = 0;
            $totalrealisasiABASCG5 = 0;
            $totalhasilpoinABASCG5 = 0;

            $totaltargetSC = 0;
            $totalrealisasiSC = 0;
            $totalhasilpoinSC = 0;

            $totalallpoin =0;
            $rewardallpoin = 0;
            $rewardcashinkp = 0;
            $ratioljtkp  = 0;
            $rewardljtkp  =0;
            $totalrewardkp  = 0;

            $totalcashin = 0;
            $totalsisapiutang = 0;

            $grandtotalrewardsales = 0;
            $grandtotalrewarddriver = 0;
            $grandtotalrewardhelper = 0;
            $grandtotalrewardgudang = 0;
            $no = 1;
            $komisi_aktif = 0;
            $totalrewardpoin = 0;
            $totalrewardcashin=0;
            $totalrewardljt = 0;

            foreach ($komisi as $d) {
                if($d->status_komisi==1){
                    $komisi_aktif += 1;
                }
                //BB & DEP
                $BB = $d->BB / $isipcsdusBB;
                $returBB = $d->retur_BB / $isipcsdusBB;
                $DEP = $d->DEP / $isipcsdusDEP;
                $returDEP = $d->retur_DEP / $isipcsdusDEP;
                $realisasi_BB_DEP = $BB + $DEP;
                //$realisasi_BB_DEP = ($BB - $returBB) + ($returDEP - $DEP);
                if (empty($d->target_BB_DP)) {
                    $ratioBBDP = 0;
                } else {
                    $ratioBBDP = $realisasi_BB_DEP / $d->target_BB_DP;
                }
                if ($ratioBBDP > 1) {
                    $hasilpoinBBDP =  $poinBBDP;
                } else {
                    $hasilpoinBBDP = $ratioBBDP * $poinBBDP;
                }

                //SP8
                $SP8 = $d->SP8 / $isipcsdusSP8;
                $returSP8 = $d->retur_SP8 / $isipcsdusSP8;
                $realisasi_DS = $SP8;
                //$realisasi_DS = $SP8 - $returSP8;
                if (empty($d->target_DS)) {
                    $ratioDS = 0;
                } else {
                    $ratioDS = $realisasi_DS / $d->target_DS;
                }

                if ($ratioDS > 1) {
                    $hasilpoinDS =  $poinDS;
                } else {
                    $hasilpoinDS = $ratioDS * $poinDS;
                }

                //SP
                $SP = $d->SP / $isipcsdusSP;
                $SP500 = $d->SP500 / $isipcsdusSP500;
                $returSP = $d->retur_SP / $isipcsdusSP;
                $returSP500 = $d->retur_SP500 / $isipcsdusSP500;
                //$realisasi_SP = $SP-$returSP;
                $realisasi_SP = $SP + $SP500;
                if (empty($d->target_SP)) {
                    $ratioSP = 0;
                } else {
                    $ratioSP = $realisasi_SP / $d->target_SP;
                }

                if ($ratioSP > 1) {
                    $hasilpoinSP =  $poinSP;
                } else {
                    $hasilpoinSP = $ratioSP * $poinSP;
                }

                //AR
                $AR = $d->AR / $isipcsdusAR;
                $returAR = $d->retur_AR / $isipcsdusAR;
                $realisasi_AR = $AR;
                //$realisasi_AR = $AR - $returAR;
                if (empty($d->target_AR)) {
                    $ratioAR = 0;
                } else {
                    $ratioAR = $realisasi_AR / $d->target_AR;
                }

                if ($ratioAR > 1) {
                    $hasilpoinAR =  $poinAR;
                } else {
                    $hasilpoinAR = $ratioAR * $poinAR;
                }

                //AS & AB
                $AB = $d->AB / $isipcsdusAB;
                $returAB = $d->retur_AB / $isipcsdusAB;
                $AS = $d->AS / $isipcsdusAS;
                $returAS = $d->retur_AS / $isipcsdusAS;
                $CG5 = $d->CG5 / $isipcsdusCG5;
                $returCG5 = $d->retur_CG5 / $isipcsdusCG5;

                $realisasi_AB_AS_CG5 =$AB + $AS + $CG5;
                //$realisasi_AB_AS_CG5 = ($AB - $returAB) + ($AS-$returAS) + ($CG5-$returCG5);
                if (empty($d->target_AB_AS_CG5)) {
                    $ratioAB_AS_CG5 = 0;
                } else {
                    $ratioAB_AS_CG5 = $realisasi_AB_AS_CG5 / $d->target_AB_AS_CG5;
                }
                //$ratioAB_AS_CG5 = $d->realisasi_AB_AS_CG5 / $d->target_AB_AS_CG5;
                if ($ratioAB_AS_CG5 > 1) {
                    $hasilpoinAB_AS_CG5 =  $poinASABCG5;
                } else {
                    $hasilpoinAB_AS_CG5 = $ratioAB_AS_CG5 * $poinASABCG5;
                }

                //SC
                $SC = $d->SC / $isipcsdusSC;
                $returSC = $d->retur_SC / $isipcsdusSC;
                $realisasi_SC = $SC;
                //$realisasi_SC = $SC-$returSC;
                if (empty($d->target_SC)) {
                    $ratioSC = 0;
                } else {
                    $ratioSC = $realisasi_SC / $d->target_SC;
                }

                if ($ratioSC > 1) {
                    $hasilpoinSC =  $poinSC;
                } else {
                    $hasilpoinSC = $ratioSC * $poinSC;
                }


                $totalpoin = $hasilpoinBBDP + $hasilpoinDS + $hasilpoinAR + $hasilpoinSP + $hasilpoinAB_AS_CG5 + $hasilpoinSC;
                // if($cbg->kode_cabang == "BGR"){
                //     if ($d->kategori_salesman == "RETAIL") {
                //         $ratiocashin = 0.30;
                //     } else {
                //         $ratiocashin = 0.10;
                //     }
                // }else{
                //     // if ($d->kategori_salesman == "CANVASER" || $d->kategori_salesman == "RETAIL") {
                //     //     $ratiocashin = 0.30;
                //     // } else {
                //     //     $ratiocashin = 0.10;
                //     // }


                // }

                // if($d->realisasi_cashin < 250000000){
                //     $ratiocashin = 0.30;
                // }else{
                //     $ratiocashin = 0.10;
                // }

                $ratiocashin = 0.10;

                if($d->status_komisi == 1){
                $rewardcashin = $d->realisasi_cashin * ($ratiocashin / 100);
                }else{
                    $rewardcashin = 0;
                }

                //Ratio LJT

                $ratioljt = !empty($d->realisasi_cashin)  ? ($d->sisapiutang / $d->realisasi_cashin * 100) * ($kebijakan / 100) : 0;
                if ($ratioljt > 0) {
                    $ratioljt = $ratioljt;
                } else {
                    $ratioljt = 0;
                }

                if($d->status_komisi == 1){
                    if ($ratioljt >= 0 and $ratioljt <= 0.20) {
                        $rewardljt = 500000;
                    } else  if ($ratioljt > 0.20 and $ratioljt <= 0.40) {
                        $rewardljt = 375000;
                    } else {
                        $rewardljt = 0;
                    }
                }else{
                    $rewardljt = 0;
                }

                if($d->status_komisi == 1){

                    if (round($totalpoin,2) <= 70) {
                        $rewardpoin = 0;
                    } else if (round($totalpoin,2) > 70 and round($totalpoin,2) <= 75) {
                        $rewardpoin = 500000;
                    } else if (round($totalpoin,2) > 75 and round($totalpoin,2) <= 80) {
                        $rewardpoin = 1000000;
                    } else if (round($totalpoin,2) > 80 and round($totalpoin,2) <= 85) {
                        $rewardpoin = 1500000;
                    } else if (round($totalpoin,2) > 85 and round($totalpoin,2) <= 90) {
                        $rewardpoin = 2000000;
                    } else if (round($totalpoin,2) > 90 and round($totalpoin,2) <= 95) {
                        $rewardpoin = 2500000;
                    } else if (round($totalpoin,2) > 95 and round($totalpoin,2) <= 100) {
                        $rewardpoin = 3000000;
                    } else {
                        $rewardpoin = 0;
                    }
                }else{
                    $rewardpoin = 0;
                }

                $totalrewardpoin += $rewardpoin;
                $totalrewardcashin += $rewardcashin;
                $totalrewardljt += $rewardljt;
                $totalreward = $rewardcashin + $rewardljt + $rewardpoin;
                $grandtotalrewardsales += $totalreward;
                $totaltargetBBDP += $d->target_BB_DP;
                $totalrealisasiBBDP += $realisasi_BB_DEP;

                $totaltargetDS += $d->target_DS;
                $totalrealisasiDS += $realisasi_DS;

                $totaltargetSP += $d->target_SP;
                $totalrealisasiSP += $realisasi_SP;

                $totaltargetAR += $d->target_AR;
                $totalrealisasiAR += $realisasi_AR;

                $totaltargetABASCG5 += $d->target_AB_AS_CG5;
                $totalrealisasiABASCG5 += $realisasi_AB_AS_CG5;

                $totaltargetSC += $d->target_SC;
                $totalrealisasiSC += $realisasi_SC;

                $totalcashin += $d->realisasi_cashin;
                $totalsisapiutang += $d->sisapiutang;


                $persentasesku = !empty($d->jmlpelanggan) ? ROUND($d->jmltigasku / $d->jmlpelanggan * 100) : 0;

                if($d->status_komisi == 1){

                    if ($persentasesku <= 65) {
                        $rewardsku = 0;
                    }else if ($persentasesku > 65 and $persentasesku  <= 70) {
                        $rewardsku = 100000;
                    } else if ($persentasesku >= 70 and $persentasesku  <= 75) {
                        $rewardsku = 200000;
                    } else if ( $persentasesku > 75 and $persentasesku<= 80) {
                        $rewardsku = 300000;
                    } else if ($persentasesku > 80 and $persentasesku <= 85) {
                        $rewardsku = 400000;
                    } else if ($persentasesku > 85 and $persentasesku <= 90) {
                        $rewardsku = 500000;
                    } else if ($persentasesku > 90 and $persentasesku <= 95) {
                        $rewardsku = 600000;
                    } else if ($persentasesku > 95 and $persentasesku <= 100) {
                        $rewardsku = 700000;
                    } else {
                        $rewardsku = 0;
                    }
                }else{
                    $rewardsku = 0;
                }

                //Outlet Aktif

                $transvsregister = $d->jmlpelanggan != null ? $d->jmltrans / $d->jmlpelanggan* 100 : 0;

                if($d->status_komisi == 1){
                    if ($transvsregister <= 80) {
                        $reward_oa = 0;
                    } else if ($transvsregister > 80 and $transvsregister <= 85) {
                        $reward_oa = 200000;
                    } else if ($transvsregister > 85 and $transvsregister <= 90) {
                        $reward_oa = 400000;
                    } else if ($transvsregister > 90 and $transvsregister <= 95) {
                        $reward_oa = 600000;
                    } else if ($transvsregister > 95 and $transvsregister <= 100) {
                        $reward_oa = 800000;
                    } else {
                        $reward_oa = 0;
                    }
                }else{
                    $reward_oa = 0;
                }


                //Outlet Aktif

                $ec = $d->jmltrans / 23;

                if($d->status_komisi == 1){
                    if ($ec > 10 and $transvsregister <= 15) {
                       if($d->kategori_salesman == "TO"){
                            $reward_ec = 100000;
                       }else{
                            $reward_ec = 0;
                       }
                    } else if ($ec > 15 and $ec <= 20) {
                        if($d->kategori_salesman == "TO"){
                            $reward_ec = 200000;
                       }else{
                            $reward_ec = 100000;
                       }
                    } else if ($ec > 20 and $ec <= 25) {
                        if($d->kategori_salesman == "TO"){
                            $reward_ec = 300000;
                       }else{
                            $reward_ec = 200000;
                       }
                    }else if ($ec > 25) {
                        if($d->kategori_salesman == "TO"){
                            $reward_ec = 400000;
                       }else{
                            $reward_ec = 300000;
                       }
                    } else {
                        $reward_ec = 0;
                    }
                }else{
                    $reward_ec = 0;
                }
            ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td><?php echo $d->id_karyawan; ?></td>
                <td><?php echo $d->nama_karyawan; ?></td>
                <td><?php echo $d->kategori_salesman; ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($d->target_BB_DP); ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($realisasi_BB_DEP); ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($hasilpoinBBDP); ?></td>
                <td align="right" style="background-color: #ffcb00;"><?php echo desimal($d->target_DS); ?></td>
                <td align="right" style="background-color: #ffcb00;"><?php echo desimal($realisasi_DS); ?></td>
                <td align="right" style="background-color: #ffcb00;"><?php echo desimal($hasilpoinDS); ?></td>
                <td align="right" style="background-color: #058cbe;"><?php echo desimal($d->target_SP); ?></td>
                <td align="right" style="background-color: #058cbe;"><?php echo desimal($realisasi_SP); ?></td>
                <td align="right" style="background-color: #058cbe;"><?php echo desimal($hasilpoinSP); ?></td>
                <td align="right" style="background-color: #ce3ae4;"><?php echo desimal($d->target_AR); ?></td>
                <td align="right" style="background-color: #ce3ae4;"><?php echo desimal($realisasi_AR); ?></td>
                <td align="right" style="background-color: #ce3ae4;"><?php echo desimal($hasilpoinAR); ?></td>
                <td align="right" style="background-color: #ff9b0d;"><?php echo desimal($d->target_AB_AS_CG5); ?></td>
                <td align="right" style="background-color: #ff9b0d;"><?php echo desimal($realisasi_AB_AS_CG5); ?></td>
                <td align="right" style="background-color: #ff9b0d;"><?php echo desimal($hasilpoinAB_AS_CG5); ?></td>
                <td align="right" style="background-color: #0daeff;"><?php echo desimal($d->target_SC); ?></td>
                <td align="right" style="background-color: #0daeff;"><?php echo desimal($realisasi_SC); ?></td>
                <td align="right" style="background-color: #0daeff;"><?php echo desimal($hasilpoinSC); ?></td>
                <td align="right" style="background-color: #ff570d;"><?php echo round($totalpoin,2); ?></td>
                <td align="right" style="background-color: #ff570d;"><?php echo desimal($rewardpoin); ?></td>
                <td align="center">{{ !empty($d->jmlpelanggan) ?  rupiah($d->jmlpelanggan) : '' }}</td>
                <td align="center">{{ !empty($d->jmltrans) ?  rupiah($d->jmltrans) : '' }}</td>
                <td align="center">{{ !empty($d->jmltigasku) ?  rupiah($d->jmltigasku) : '' }}</td>
                <td align="center">{{ !empty($persentasesku) ?  $persentasesku.'%': '' }}</td>
                <td align="right"><?php echo desimal($rewardsku); ?></td>
                <td></td>
                <td></td>
                <td align="center">
                    {{ desimal($transvsregister) }}%
                </td>
                <td align="right"><?php echo desimal($reward_oa); ?></td>
                <td align="center">
                    {{ desimal($ec) }}%
                </td>
                //Cashin
                <td align="right"><?php echo desimal($reward_ec); ?></td>
                <td align="right" style="background-color: #9e9895;"><?php echo desimal($d->realisasi_cashin); ?></td>
                <td align="center" style="background-color: #9e9895;"><?php echo $ratiocashin; ?>%</td>
                <td align="right" style="background-color: #9e9895;"><?php echo desimal($rewardcashin); ?></td>
                //LJT
                <td align="right" style="background-color: #e43a90;"><?php if ($d->sisapiutang > 0) {echo desimal($d->sisapiutang); } else {echo 0;} ?></td>
                <td align="center" style="background-color: #e43a90;"><?php echo round($ratioljt, 2); ?></td>
                <td align="right" style="background-color: #e43a90;"><?php echo desimal($rewardljt); ?></td>
            </tr>

            <?php $no++;} ?>
        </tbody>
    </table>

</body>
</html>
