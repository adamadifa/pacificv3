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
    $poinSP = 10;
    $poinDS = 10;
    $poinAR = 15;
    $poinASABCG5 = 15;
    $poinSC = 10;
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
                <th colspan="2" rowspan="2">KETEPATAN ROUTING</th>
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
                <th> 3 SKU</th>
                <th>PERSENTASE</th>
                <th>REWARD</th>
                <th>PERSENTASE</th>
                <th>REWARD</th>
                <th>PERSENTASE</th>
                <th>REWARD</th>
                <th>PERSENTASE</th>
                <th>REWARD</th>
                <th>RATA RATA</th>
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
            $totalpelangganaktif = 0;
            $totaltransaksi = 0;
            $totaltransaksipenjualan = 0;
            $totaltigasku = 0;
            $totalrewardsku =0;
            $totalreward_oa = 0;
            $totalkunjungan = 0;
            $totalsesuaijadwal = 0;
            $totalrewardrouting = 0;
            $totalrewardec = 0;
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
                    }else  if ($ratioljt > 0.40 and $ratioljt <= 0.60) {
                        $rewardljt = 250000;
                    }else  if ($ratioljt > 0.60 and $ratioljt <= 0.80) {
                        $rewardljt = 125000;
                    } else {
                        $rewardljt = 0;
                    }
                }else{
                    $rewardljt = 0;
                }

                if($d->status_komisi == 1){

                    if (round($totalpoin,2) >= 70 and round($totalpoin,2) <= 75) {
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




                $persentasesku = !empty($d->jmlpelanggan) ? $d->jmltigasku / $d->jmlpelanggan * 100 : 0;

                if($d->status_komisi == 1){

                     if ($persentasesku >= 65 and $persentasesku  <= 70) {
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
                     if ($transvsregister >= 80 and $transvsregister <= 85) {
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

                $ec = $d->jmltranspenjualan / 24;

                //echo $transvsregister;
                if($d->status_komisi == 1){
                    if ($ec > 10 and $ec <= 15) {
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


                $persentaserouting = !empty($d->jmlkunjungan) ? $d->jmlsesuaijadwal/$d->jmlkunjungan * 100 : 0;

                if($d->status_komisi == 1){
                    if ($persentaserouting >= 85 and $persentaserouting <= 90) {
                        $reward_routing = 200000;
                    } else if ($persentaserouting > 90 and $persentaserouting <= 95) {
                        $reward_routing = 400000;
                    }  else if ($persentaserouting > 95 and $persentaserouting <= 100) {
                        $reward_routing = 600000;
                    } else {
                        $reward_routing = 0;
                    }
                }else{
                    $reward_routing = 0;
                }


                $totalrewardpoin += $rewardpoin;
                $totalrewardcashin += $rewardcashin;
                $totalrewardljt += $rewardljt;
                $totalreward = $rewardpoin + $rewardsku + $reward_oa + $reward_routing + $reward_ec + $rewardcashin + $rewardljt ;
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

                $totalpelangganaktif += $d->jmlpelanggan;
                $totaltransaksi += $d->jmltrans;
                $totaltransaksipenjualan += $d->jmltranspenjualan;
                $totaltigasku += $d->jmltigasku;
                $totalrewardsku += $rewardsku;
                $totalreward_oa += $reward_oa;
                $totalkunjungan += $d->jmlkunjungan;
                $totalsesuaijadwal += $d->jmlsesuaijadwal;
                $totalrewardrouting += $reward_routing;
                $totalrewardec += $reward_ec;
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
                <td align="right" style="background-color: #ff570d;"><?php echo rupiah($rewardpoin); ?></td>
                <td align="center">{{ !empty($d->jmlpelanggan) ?  rupiah($d->jmlpelanggan) : '' }}</td>
                <td align="center">{{ !empty($d->jmltrans) ?  rupiah($d->jmltrans) : '' }}</td>
                <td align="center">{{ !empty($d->jmltigasku) ?  rupiah($d->jmltigasku) : '' }}</td>
                <td align="center">{{ !empty($persentasesku) ?  desimal($persentasesku).'%': '' }}</td>
                <td align="right"><?php echo rupiah($rewardsku); ?></td>
                <td></td>
                <td></td>
                <td align="center">
                    {{ desimal($transvsregister) }}%
                </td>
                <td align="right"><?php echo rupiah($reward_oa); ?></td>
                <td align="center">
                    {{ desimal($persentaserouting) }}%
                </td>
                <td align="right"><?php echo rupiah($reward_routing); ?></td>
                <td align="center">
                    {{ desimal($ec) }}
                </td>
                <!--Cashin-->
                <td align="right"><?php echo rupiah($reward_ec); ?></td>
                <td align="right" style="background-color: #9e9895;"><?php echo desimal($d->realisasi_cashin); ?></td>
                <td align="center" style="background-color: #9e9895;"><?php echo $ratiocashin; ?>%</td>
                <td align="right" style="background-color: #9e9895;"><?php echo rupiah($rewardcashin); ?></td>
                <!-- LJT -->
                <td align="right" style="background-color: #e43a90;"><?php if ($d->sisapiutang > 0) {echo desimal($d->sisapiutang); } else {echo 0;} ?></td>
                <td align="center" style="background-color: #e43a90;"><?php echo round($ratioljt, 2); ?></td>
                <td align="right" style="background-color: #e43a90;"><?php echo rupiah($rewardljt); ?></td>
                <td align="right" style="background-color: #ff570d;"><?php echo rupiah($totalreward); ?></td>
                <td style="text-align: right">
                    @if (in_array($level,$inputpotongankomisi))
                    <a href="#" class="inputpotongan" id_karyawan="{{ $d->id_karyawan }}" nama_karyawan="{{ $d->nama_karyawan }}" style="color:red">
                        {{ $d->potongankomisi != null ? rupiah($d->potongankomisi) : 'Input Potongan' }}
                    </a>
                    @else
                    &#128274;
                    @endif
                </td>
                <td style="text-align: right">
                    @php
                    $totalkomisi = $totalreward - $d->potongankomisi;
                    @endphp
                    {{ rupiah($totalkomisi) }}
                </td>
                <td style="text-align: right">
                    @if (in_array($level,$inputpotongankomisi))
                    <a href="#" class="inputkomisiakhir" id_karyawan="{{ $d->id_karyawan }}" nama_karyawan="{{ $d->nama_karyawan }}" style="color:red">
                        {{ $d->komisifix != null ? rupiah($d->komisifix) : rupiah($totalkomisi) }}
                    </a>
                    @else
                    &#128274;
                    @endif
                </td>
                <td>
                    @if (!empty($d->potongankomisi))
                    Potongan : {{ $d->ket_potongan }}
                    @endif

                    @if (!empty($d->ket_komisifix))
                    Disesuaikan Karena : {{ $d->ket_komisifix }}
                    @endif
                </td>
            </tr>

            <?php $no++;
            if (empty($totaltargetBBDP)) {
                    $totalratioBBDP = 0;
                } else {
                    $totalratioBBDP = $totalrealisasiBBDP / $totaltargetBBDP;
                }

                if ($totalratioBBDP > 1) {
                    $totalhasilpoinBBDP =  $poinBBDP;
                } else {
                    $totalhasilpoinBBDP = $totalratioBBDP * $poinBBDP;
                }

                if (empty($totaltargetDS)) {
                    $totalratioDS = 0;
                } else {
                    $totalratioDS = $totalrealisasiDS / $totaltargetDS;
                }

                if ($totalratioDS > 1) {
                    $totalhasilpoinDS =  $poinDS;
                } else {
                    $totalhasilpoinDS = $totalratioDS * $poinDS;
                }

                if (empty($totaltargetSP)) {
                    $totalratioSP = 0;
                } else {
                    $totalratioSP = $totalrealisasiSP / $totaltargetSP;
                }

                if ($totalratioSP > 1) {
                    $totalhasilpoinSP =  $poinSP;
                } else {
                    $totalhasilpoinSP = $totalratioSP * $poinSP;
                }

                if (empty($totaltargetAR)) {
                    $totalratioAR = 0;
                } else {
                    $totalratioAR = $totalrealisasiAR / $totaltargetAR;
                }

                if ($totalratioAR > 1) {
                    $totalhasilpoinAR =  $poinAR;
                } else {
                    $totalhasilpoinAR = $totalratioAR * $poinAR;
                }

                if (empty($totaltargetABASCG5)) {
                    $totalratioABASCG5 = 0;
                } else {
                    $totalratioABASCG5 = $totalrealisasiABASCG5 / $totaltargetABASCG5;
                }

                if ($totalratioABASCG5 > 1) {
                    $totalhasilpoinABASCG5 =  $poinASABCG5;
                } else {
                    $totalhasilpoinABASCG5 = $totalratioABASCG5 * $poinASABCG5;
                }

                if (empty($totaltargetSC)) {
                    $totalratioSC = 0;
                } else {
                    $totalratioSC = $totalrealisasiSC / $totaltargetSC;
                }

                if ($totalratioSC > 1) {
                    $totalhasilpoinSC =  $poinSC;
                } else {
                    $totalhasilpoinSC = $totalratioSC * $poinSC;
                }



                $totalallpoin = $totalhasilpoinBBDP + $totalhasilpoinDS + $totalhasilpoinSP + $totalhasilpoinAR + $totalhasilpoinABASCG5 + $totalhasilpoinSC;

                if ($totalallpoin < 70) {
                    $rewardallpoin = 0;
                } else if ($totalallpoin >= 70 and $totalallpoin <= 75) {
                    $rewardallpoin = 1500000;
                } else if ($totalallpoin > 75 and $totalallpoin <= 80) {
                    $rewardallpoin = 3000000;
                } else if ($totalallpoin > 80 and $totalallpoin <= 85) {
                    $rewardallpoin = 4500000;
                } else if ($totalallpoin > 85 and $totalallpoin <= 90) {
                    $rewardallpoin = 6000000;
                } else if ($totalallpoin > 90 and $totalallpoin <= 95) {
                    $rewardallpoin = 7500000;
                } else if ($totalallpoin > 95 and $totalallpoin <= 100) {
                    $rewardallpoin = 9000000;
                } else {
                    $rewardallpoin = 0;
                }

                $rewardcashinkp = $totalcashin * (0.05 / 100);
                $ratioljtkp = !empty($totalcashin) ? ($totalsisapiutang / $totalcashin) * 100 : 0;


                if ($ratioljtkp > 0) {
                    $ratioljtkp = $ratioljtkp;
                } else {
                    $ratioljtkp = 0;
                }

                if ($totalsisapiutang > 0) {
                    $totalsisapiutang = $totalsisapiutang;
                } else {
                    $totalsisapiutang = 0;
                }

                if ($ratioljtkp >= 0 and $ratioljtkp <= 0.5) {
                    $rewardljtkp = 2500000;
                } else  if ($ratioljtkp > 0.5 and $ratioljtkp <= 1) {
                    $rewardljtkp = 2250000;
                } else  if ($ratioljtkp > 1 and $ratioljtkp <= 1.5) {
                    $rewardljtkp = 2000000;
                } else  if ($ratioljtkp > 1.5 and $ratioljtkp <= 2) {
                    $rewardljtkp = 1750000;
                } else  if ($ratioljtkp > 2 and $ratioljtkp <= 2.5) {
                    $rewardljtkp = 1500000;
                } else  if ($ratioljtkp > 2.5 and $ratioljtkp <= 3) {
                    $rewardljtkp = 1250000;
                } else  if ($ratioljtkp > 3 and $ratioljtkp <= 3.5) {
                    $rewardljtkp = 1000000;
                } else  if ($ratioljtkp > 3.5 and $ratioljtkp <= 4) {
                    $rewardljtkp = 750000;
                } else  if ($ratioljtkp > 4 and $ratioljtkp <= 4.5) {
                    $rewardljtkp = 500000;
                } else  if ($ratioljtkp > 4.5 and $ratioljtkp <= 5) {
                    $rewardljtkp = 250000;
                } else {
                    $rewardljtkp = 0;
                }
                $totalrewardkp = $rewardallpoin + $rewardcashinkp + $rewardljtkp;
            }
            if(in_array($cbg->kode_cabang,$supervisorcabang)){
            ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td colspan="3">SUPERVISOR</td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totaltargetBBDP); ?></td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totalrealisasiBBDP); ?></td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totalhasilpoinBBDP); ?></td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totaltargetDS); ?></td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totalrealisasiDS); ?></td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totalhasilpoinDS); ?></td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totaltargetSP); ?></td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totalrealisasiSP); ?></td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totalhasilpoinSP); ?></td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totaltargetAR); ?></td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totalrealisasiAR); ?></td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totalhasilpoinAR); ?></td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totaltargetABASCG5); ?></td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totalrealisasiABASCG5); ?></td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totalhasilpoinABASCG5); ?></td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totaltargetSC); ?></td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totalrealisasiSC); ?></td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totalhasilpoinSC); ?></td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totalallpoin ); ?></td>
                <td align="right" style="background-color: #0ca0c9;">
                    <?php
                        $rewardpoinspv = $totalrewardpoin/$komisi_aktif;
                        echo rupiah($rewardpoinspv );
                    ?>
                </td>
                <td align="center" style="background-color: #0ca0c9;">{{ rupiah($totalpelangganaktif) }}</td>
                <td align="center" style="background-color: #0ca0c9;">{{ rupiah($totaltransaksi) }}</td>
                <td align="center" style="background-color: #0ca0c9;">{{ rupiah($totaltigasku) }}</td>
                <td align="center" style="background-color: #0ca0c9;">
                    @php
                    $totalpersentasesku = !empty($totalpelangganaktif) ? $totaltigasku / $totalpelangganaktif * 100 : 0;
                    @endphp
                    {{ desimal($totalpersentasesku) }} %
                </td>

                <td align="right" style="background-color: #0ca0c9;">
                    <?php
                        $rewardskuspv = $totalrewardsku/$komisi_aktif;
                        echo rupiah($rewardskuspv);
                    ?>
                </td>
                <td style="background-color: #0ca0c9;"></td>
                <td style="background-color: #0ca0c9;"></td>
                <td align="center" style="background-color: #0ca0c9;">
                    @php
                    $totalpersentase_oa = !empty($totalpelangganaktif) ? $totaltransaksi/$totalpelangganaktif * 100 : 0;
                    @endphp
                    {{ desimal($totalpersentase_oa) }} %
                </td>
                <td align="right" style="background-color: #0ca0c9;">
                    <?php
                        $reward_oa_spv = $totalreward_oa/$komisi_aktif;
                        echo rupiah($reward_oa_spv);
                    ?>
                </td>
                <td align="center" style="background-color: #0ca0c9;">
                    @php
                    $totalpersentase_routing = !empty($totalkunjungan) ? $totalsesuaijadwal/$totalkunjungan * 100 : 0;
                    @endphp
                    {{ desimal($totalpersentase_routing) }} %
                </td>
                <td align="right" style="background-color: #0ca0c9;">
                    <?php
                        $reward_routing_spv = $totalrewardrouting/$komisi_aktif;
                        echo rupiah($reward_routing_spv);
                    ?>
                </td>
                <td align="center" style="background-color: #0ca0c9;">
                    @php
                    $totalpersentase_ec = $totaltransaksipenjualan / 24;
                    @endphp
                    {{ desimal($totalpersentase_ec) }}
                </td>
                <td align="right" style="background-color: #0ca0c9;">
                    <?php
                        $reward_ec_spv = $totalrewardec /$komisi_aktif;
                        echo rupiah($reward_ec_spv);
                    ?>
                </td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totalcashin); ?></td>
                <td align="center" style="background-color: #0ca0c9;"></td>
                <td align="right" style="background-color: #0ca0c9;">
                    <?php
                        $rewardcashinspv= $totalrewardcashin/$komisi_aktif;
                        echo rupiah($rewardcashinspv);
                    ?>
                </td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo desimal($totalsisapiutang); ?></td>
                <td align="center" style="background-color: #0ca0c9;"><?php echo ROUND($ratioljtkp, 2); ?></td>
                <td align="right" style="background-color: #0ca0c9;"><?php echo rupiah($rewardljtspv = $totalrewardljt / $komisi_aktif); ?></td>
                <td align="right" style="background-color: #0ca0c9;">
                    <?php
                        $totalrewardspv = $rewardpoinspv + $rewardskuspv + $reward_oa_spv +  $reward_routing_spv + $reward_ec_spv+ $rewardcashinspv + $rewardljtspv;
                    ?>
                    {{ rupiah($totalrewardspv) }}
                </td>
                <td style="text-align: right">
                    @if (in_array($level,$inputpotongankomisi))
                    <a href="#" class="inputpotongan" id_karyawan="SPV{{ $cbg->kode_cabang }}" nama_karyawan="SUPERVISOR" style="color:red">
                        {{ $potonganspv != null && $potonganspv->jumlah != null ? rupiah($potonganspv->jumlah) : 'Input Potongan' }}
                    </a>
                    @else
                    &#128274;
                    @endif
                </td>
                <td style="text-align: right">

                    @php
                    $potonganspv = $potonganspv != null ? $potonganspv->jumlah : 0;
                    $totalkomisispv = $totalrewardspv - $potonganspv;
                    @endphp
                    {{ rupiah($totalkomisispv) }}
                </td>

                <td style="text-align: right">
                    @if (in_array($level,$inputpotongankomisi))
                    <a href="#" class="inputkomisiakhir" id_karyawan="SPV{{ $cbg->kode_cabang }}" nama_karyawan="SUPERVISOR" style="color:red">
                        {{$komisiakhirspv !=null && $komisiakhirspv->jumlah != null ? rupiah($komisiakhirspv->jumlah) : rupiah($totalkomisispv) }}
                    </a>
                    @else
                    &#128274;
                    @endif
                </td>
            </tr>
            <?php }else{
            $totalrewardspv=0;
            }
            ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td colspan="3">KEPALA PENJUALAN</td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totaltargetBBDP); ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totalrealisasiBBDP); ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totalhasilpoinBBDP); ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totaltargetDS); ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totalrealisasiDS); ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totalhasilpoinDS); ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totaltargetSP); ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totalrealisasiSP); ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totalhasilpoinSP); ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totaltargetAR); ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totalrealisasiAR); ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totalhasilpoinAR); ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totaltargetABASCG5); ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totalrealisasiABASCG5); ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totalhasilpoinABASCG5); ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totaltargetSC); ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totalrealisasiSC); ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totalhasilpoinSC); ?></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totalallpoin ); ?></td>
                <td align="right" style="background-color: #35ce35;"></td>
                <td align="center" style="background-color: #35ce35;">{{ rupiah($totalpelangganaktif) }}</td>
                <td align="center" style="background-color: #35ce35;">{{ rupiah($totaltransaksi) }}</td>
                <td align="center" style="background-color: #35ce35;">{{ rupiah($totaltigasku) }}</td>
                <td align="center" style="background-color: #35ce35;">
                    @php
                    $totalpersentasesku = !empty($totalpelangganaktif) ? $totaltigasku / $totalpelangganaktif * 100 : 0;
                    @endphp
                    {{ desimal($totalpersentasesku) }} %
                </td>
                <td align="center" style="background-color: #35ce35;"></td>
                <td align="center" style="background-color: #35ce35;"></td>
                <td align="center" style="background-color: #35ce35;"></td>
                <td align="center" style="background-color: #35ce35;">
                    @php
                    $totalpersentase_oa = !empty($totalpelangganaktif) ? $totaltransaksi/$totalpelangganaktif * 100 : 0;
                    @endphp
                    {{ desimal($totalpersentase_oa) }} %
                </td>
                <td align="center" style="background-color: #35ce35;">
                <td align="center" style="background-color: #35ce35;">
                    @php
                    $totalpersentase_routing = !empty($totalkunjungan) ? $totalsesuaijadwal/$totalkunjungan * 100 : 0;
                    @endphp
                    {{ desimal($totalpersentase_routing) }} %
                </td>
                <td align="center" style="background-color: #35ce35;">
                <td align="center" style="background-color: #35ce35;">
                    @php
                    $totalpersentase_ec = $totaltransaksipenjualan / 24;
                    @endphp
                    {{ desimal($totalpersentase_ec) }}
                </td>
                <td align="center" style="background-color: #35ce35;">
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totalcashin); ?></td>
                <td align="center" style="background-color: #35ce35;"></td>
                <td align="right" style="background-color: #35ce35;"></td>
                <td align="right" style="background-color: #35ce35;"><?php echo desimal($totalsisapiutang); ?></td>
                <td align="center" style="background-color: #35ce35;"><?php echo ROUND($ratioljtkp, 2); ?></td>
                <td align="center" style="background-color: #35ce35;">
                <td align="center" style="background-color: #35ce35;">
            </tr>

        </tbody>
    </table>

</body>
</html>
