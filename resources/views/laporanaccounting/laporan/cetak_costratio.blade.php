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
        COST RATIO<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }} <br>
    </b>
    <br>
    <table class="datatable3" style="width:90%" border="1">
        <thead>
            <tr>
                <th style="background-color:rgb(0, 52, 93); color:white">No</th>
                <th style="background-color:rgb(0, 52, 93); color:white">Kode Akun</th>
                <th style="background-color:rgb(0, 52, 93); color:white">Nama Akun</th>
                <th style="background-color: rgb(0, 77, 0); color:white">TASIKMALAYA</th>
                <th style="background-color: rgb(0, 77, 0); color:white">BANDUNG</th>
                <th style="background-color: rgb(0, 77, 0); color:white">SUKABUMI</th>
                <th style="background-color: rgb(0, 77, 0); color:white">TEGAL</th>
                <th style="background-color: rgb(0, 77, 0); color:white">BOGOR</th>
                <th style="background-color: rgb(0, 77, 0); color:white">PURWOKERTO</th>
                <th style="background-color: rgb(0, 77, 0); color:white">PCF PST</th>
                <th style="background-color: rgb(0, 77, 0); color:white">GARUT</th>
                <th style="background-color: rgb(0, 77, 0); color:white">SURABAYA</th>
                <th style="background-color: rgb(0, 77, 0); color:white">SEMARANG</th>
                <th style="background-color: rgb(0, 77, 0); color:white">YOGYAKARTA</th>
                <th style="background-color: rgb(0, 77, 0); color:white">PURWAKARTA</th>
                <th style="background-color:rgb(0, 52, 93); color:white">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php
            $totaltsm =0;
            $totalbdg = 0;
            $totalskb = 0;
            $totaltgl = 0;
            $totalbgr = 0;
            $totalpwt = 0;
            $totalpst =0;
            $totalgrt = 0;
            $totalsby = 0;
            $totalsmr = 0;
            $totalklt = 0;
            $totalpwk = 0;
            $grandtotal = 0;
            @endphp
            @foreach ($biaya as $d)
            @php
            $totaltsm += $d->tsm;
            $totalbdg += $d->bdg;
            $totalskb += $d->skb;
            $totaltgl += $d->tgl;
            $totalbgr += $d->bgr;
            $totalpwt += $d->pwt;
            $totalpst += $d->pst;
            $totalgrt += $d->grt;
            $totalsby += $d->sby;
            $totalsmr += $d->smr;
            $totalklt += $d->klt;
            $totalpwk += $d->pwk;
            $grandtotal += $d->total;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td style="text-align: center">'{{$d->kode_akun }}</td>
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
                <td style="text-align:right">{{ !empty($d->tsm) ?  rupiah($d->tsm) : '' }}</td>
                <td style="text-align:right">{{ !empty($d->bdg) ?  rupiah($d->bdg) : '' }}</td>
                <td style="text-align:right">{{ !empty($d->skb) ?  rupiah($d->skb) : '' }}</td>
                <td style="text-align:right">{{ !empty($d->tgl) ?  rupiah($d->tgl) : '' }}</td>
                <td style="text-align:right">{{ !empty($d->bgr) ?  rupiah($d->bgr) : '' }}</td>
                <td style="text-align:right">{{ !empty($d->pwt) ?  rupiah($d->pwt) : '' }}</td>
                <td style="text-align:right">{{ !empty($d->pst) ?  rupiah($d->pst) : '' }}</td>
                <td style="text-align:right">{{ !empty($d->grt) ?  rupiah($d->grt) : '' }}</td>
                <td style="text-align:right">{{ !empty($d->sby) ?  rupiah($d->sby) : '' }}</td>
                <td style="text-align:right">{{ !empty($d->smr) ?  rupiah($d->smr) : '' }}</td>
                <td style="text-align:right">{{ !empty($d->klt) ?  rupiah($d->klt) : '' }}</td>
                <td style="text-align:right">{{ !empty($d->pwk) ?  rupiah($d->pwk) : '' }}</td>
                <td style="text-align:right">{{ !empty($d->total) ?  rupiah($d->total) : '' }}</td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td style="text-align: center"></td>
                <td>
                    Potongan Penjualan
                </td>
                <td style="text-align:right">{{ !empty($potongan->tsm) ?  rupiah($potongan->tsm) : '' }}</td>
                <td style="text-align:right">{{ !empty($potongan->bdg) ?  rupiah($potongan->bdg) : '' }}</td>
                <td style="text-align:right">{{ !empty($potongan->skb) ?  rupiah($potongan->skb) : '' }}</td>
                <td style="text-align:right">{{ !empty($potongan->tgl) ?  rupiah($potongan->tgl) : '' }}</td>
                <td style="text-align:right">{{ !empty($potongan->bgr) ?  rupiah($potongan->bgr) : '' }}</td>
                <td style="text-align:right">{{ !empty($potongan->pwt) ?  rupiah($potongan->pwt) : '' }}</td>
                <td style="text-align:right">{{ !empty($potongan->pst) ?  rupiah($potongan->pst) : '' }}</td>
                <td style="text-align:right">{{ !empty($potongan->grt) ?  rupiah($potongan->grt) : '' }}</td>
                <td style="text-align:right">{{ !empty($potongan->sby) ?  rupiah($potongan->sby) : '' }}</td>
                <td style="text-align:right">{{ !empty($potongan->smr) ?  rupiah($potongan->smr) : '' }}</td>
                <td style="text-align:right">{{ !empty($potongan->klt) ?  rupiah($potongan->klt) : '' }}</td>
                <td style="text-align:right">{{ !empty($potongan->pwk) ?  rupiah($potongan->pwk) : '' }}</td>
                <td style="text-align:right">{{ !empty($potongan->total) ?  rupiah($potongan->total) : '' }}</td>
            </tr>
            @php
            $totaltsm += $potongan->tsm;
            $totalbdg += $potongan->bdg;
            $totalskb += $potongan->skb;
            $totaltgl += $potongan->tgl;
            $totalbgr += $potongan->bgr;
            $totalpwt += $potongan->pwt;
            $totalpst += $potongan->pst;
            $totalgrt += $potongan->grt;
            $totalsby += $potongan->sby;
            $totalsmr += $potongan->smr;
            $totalklt += $potongan->klt;
            $totalpwk += $potongan->pwk;
            $grandtotal += $potongan->total;
            @endphp
        </tbody>
        <tfoot>
            <tr>
                <th style="background-color:rgb(0, 52, 93); color:white" colspan="3">TOTAL</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($totaltsm ) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($totalbdg ) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($totalskb ) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($totaltgl ) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($totalbgr ) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($totalpwt ) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($totalpst ) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($totalgrt ) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($totalsby ) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($totalsmr ) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($totalklt ) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($totalpwk ) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($grandtotal) }}</th>
            </tr>
            @php
            $swan_tsm = $penjualan->netswanTSM - $retur->returswanTSM;
            $swan_bdg = $penjualan->netswanBDG - $retur->returswanBDG;
            $swan_skb = $penjualan->netswanSKB - $retur->returswanSKB;
            $swan_tgl = $penjualan->netswanTGL - $retur->returswanTGL;
            $swan_bgr = $penjualan->netswanBGR - $retur->returswanBGR;
            $swan_pwt = $penjualan->netswanPWT - $retur->returswanPWT;
            $swan_pst = $penjualan->netswanPST - $retur->returswanPST;
            $swan_grt = $penjualan->netswanGRT - $retur->returswanGRT;
            $swan_sby = $penjualan->netswanSBY - $retur->returswanSBY;
            $swan_smr = $penjualan->netswanSMR - $retur->returswanSMR;
            $swan_klt = $penjualan->netswanKLT - $retur->returswanKLT;
            $swan_pwk = $penjualan->netswanPWK - $retur->returswanPWK;

            $aida_tsm = $penjualan->netaidaTSM - $retur->returaidaTSM;
            $aida_bdg = $penjualan->netaidaBDG - $retur->returaidaBDG;
            $aida_skb = $penjualan->netaidaSKB - $retur->returaidaSKB;
            $aida_tgl = $penjualan->netaidaTGL - $retur->returaidaTGL;
            $aida_bgr = $penjualan->netaidaBGR - $retur->returaidaBGR;
            $aida_pwt = $penjualan->netaidaPWT - $retur->returaidaPWT;
            $aida_pst = $penjualan->netaidaPST - $retur->returaidaPST;
            $aida_grt = $penjualan->netaidaGRT - $retur->returaidaGRT;
            $aida_sby = $penjualan->netaidaSBY - $retur->returaidaSBY;
            $aida_smr = $penjualan->netaidaSMR - $retur->returaidaSMR;
            $aida_klt = $penjualan->netaidaKLT - $retur->returaidaKLT;
            $aida_pwk = $penjualan->netaidaPWK - $retur->returaidaPWK;

            $penjualan_tsm = $swan_tsm + $aida_tsm;
            $penjualan_bdg = $swan_bdg + $aida_bdg;
            $penjualan_skb = $swan_skb + $aida_skb;
            $penjualan_tgl = $swan_tgl + $aida_tgl;
            $penjualan_bgr = $swan_bgr + $aida_bgr;
            $penjualan_pwt = $swan_pwt + $aida_pwt;
            $penjualan_pst = $swan_pst + $aida_pst;
            $penjualan_grt = $swan_grt + $aida_grt;
            $penjualan_sby = $swan_sby + $aida_sby;
            $penjualan_smr = $swan_smr + $aida_smr;
            $penjualan_klt = $swan_klt + $aida_klt;
            $penjualan_pwk = $swan_pwk + $aida_pwk;


            $totalswan = $penjualan->totalswan - $retur->totalreturswan;
            $totalaida = $penjualan->totalaida - $retur->totalreturaida;
            $totalpenjualan = $totalswan + $totalaida;

            $cr_swan_biaya_tsm = $swan_tsm != 0 ? ROUND((($totaltsm)/$swan_tsm)*100) : 0;
            $cr_swan_biaya_bdg = $swan_bdg != 0 ? ROUND((($totalbdg)/$swan_bdg)*100) : 0;
            $cr_swan_biaya_skb = $swan_skb != 0 ? ROUND((($totalskb)/$swan_skb)*100) : 0;
            $cr_swan_biaya_tgl = $swan_tgl != 0 ? ROUND((($totaltgl)/$swan_tgl)*100) : 0;
            $cr_swan_biaya_bgr = $swan_bgr != 0 ? ROUND((($totalbgr)/$swan_bgr)*100) : 0;
            $cr_swan_biaya_pwt = $swan_pwt != 0 ? ROUND((($totalpwt)/$swan_pwt)*100) : 0;
            $cr_swan_biaya_pst = $swan_pst != 0 ? ROUND((($totalpst)/$swan_pst)*100) : 0;
            $cr_swan_biaya_grt = $swan_grt != 0 ? ROUND((($totalgrt)/$swan_grt)*100) : 0;
            $cr_swan_biaya_sby = $swan_sby != 0 ? ROUND((($totalsby)/$swan_sby)*100) : 0;
            $cr_swan_biaya_smr = $swan_smr != 0 ? ROUND((($totalsmr)/$swan_smr)*100) : 0;
            $cr_swan_biaya_klt = $swan_klt != 0 ? ROUND((($totalklt)/$swan_klt)*100) : 0;
            $cr_swan_biaya_pwk = $swan_pwk != 0 ? ROUND((($totalpwk)/$swan_pwk)*100) : 0;
            $cr_swan_biaya_total = $totalswan != 0 ? ROUND((($grandtotal)/$totalswan)*100) : 0;

            $cr_aida_biaya_tsm = $aida_tsm != 0 ? ROUND((($totaltsm)/$aida_tsm)*100) : 0;
            $cr_aida_biaya_bdg = $aida_bdg != 0 ? ROUND((($totalbdg)/$aida_bdg)*100) : 0;
            $cr_aida_biaya_skb = $aida_skb != 0 ? ROUND((($totalskb)/$aida_skb)*100) : 0;
            $cr_aida_biaya_tgl = $aida_tgl != 0 ? ROUND((($totaltgl)/$aida_tgl)*100) : 0;
            $cr_aida_biaya_bgr = $aida_bgr != 0 ? ROUND((($totalbgr)/$aida_bgr)*100) : 0;
            $cr_aida_biaya_pwt = $aida_pwt != 0 ? ROUND((($totalpwt)/$aida_pwt)*100) : 0;
            $cr_aida_biaya_pst = $aida_pst != 0 ? ROUND((($totalpst)/$aida_pst)*100) : 0;
            $cr_aida_biaya_grt = $aida_grt != 0 ? ROUND((($totalgrt)/$aida_grt)*100) : 0;
            $cr_aida_biaya_sby = $aida_sby != 0 ? ROUND((($totalsby)/$aida_sby)*100) : 0;
            $cr_aida_biaya_smr = $aida_smr != 0 ? ROUND((($totalsmr)/$aida_smr)*100) : 0;
            $cr_aida_biaya_klt = $aida_klt != 0 ? ROUND((($totalklt)/$aida_klt)*100) : 0;
            $cr_aida_biaya_pwk = $aida_pwk != 0 ? ROUND((($totalpwk)/$aida_pwk)*100) : 0;
            $cr_aida_biaya_total = $totalaida != 0 ? ROUND(($grandtotal/$totalaida)*100) : 0;

            $cr_penjualan_biaya_tsm = $penjualan_tsm != 0 ? ROUND(($totaltsm/$penjualan_tsm)*100) : 0;
            $cr_penjualan_biaya_bdg = $penjualan_bdg != 0 ? ROUND(($totalbdg/$penjualan_bdg)*100) : 0;
            $cr_penjualan_biaya_skb = $penjualan_skb != 0 ? ROUND(($totalskb/$penjualan_skb)*100) : 0;
            $cr_penjualan_biaya_tgl = $penjualan_tgl != 0 ? ROUND(($totaltgl/$penjualan_tgl)*100) : 0;
            $cr_penjualan_biaya_bgr = $penjualan_bgr != 0 ? ROUND(($totalbgr/$penjualan_bgr)*100) : 0;
            $cr_penjualan_biaya_pwt = $penjualan_pwt != 0 ? ROUND(($totalpwt/$penjualan_pwt)*100) : 0;
            $cr_penjualan_biaya_pst = $penjualan_pst != 0 ? ROUND(($totalpst/$penjualan_pst)*100) : 0;
            $cr_penjualan_biaya_grt = $penjualan_grt != 0 ? ROUND(($totalgrt/$penjualan_grt)*100) : 0;
            $cr_penjualan_biaya_sby = $penjualan_sby != 0 ? ROUND(($totalsby/$penjualan_sby)*100) : 0;
            $cr_penjualan_biaya_smr = $penjualan_smr != 0 ? ROUND(($totalsmr/$penjualan_smr)*100) : 0;
            $cr_penjualan_biaya_klt = $penjualan_klt != 0 ? ROUND(($totalklt/$penjualan_klt)*100) : 0;
            $cr_penjualan_biaya_pwk = $penjualan_pwk != 0 ? ROUND(($totalpwk/$penjualan_pwk)*100) : 0;
            $cr_penjualan_biaya_total = $totalpenjualan != 0 ? ROUND(($grandtotal/$totalpenjualan)*100) : 0;

            @endphp
            <tr>
                <th style="background-color:rgb(0, 52, 93); color:white" colspan="2" rowspan="4">PENJUALAN</th>
                <th style="background-color:rgb(0, 52, 93); color:white">SWAN</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($swan_tsm) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($swan_bdg) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($swan_skb) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($swan_tgl) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($swan_bgr) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($swan_pwt) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($swan_pst) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($swan_grt) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($swan_sby) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($swan_smr) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($swan_klt) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($swan_pwk) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($totalswan) }}</th>
            </tr>
            <tr>
                <th style="background-color:rgb(0, 52, 93); color:white">COST RATIO(%)</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_swan_biaya_tsm }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_swan_biaya_bdg }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_swan_biaya_skb }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_swan_biaya_tgl }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_swan_biaya_bgr }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_swan_biaya_pwt }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_swan_biaya_pst }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_swan_biaya_grt }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_swan_biaya_sby }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_swan_biaya_smr }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_swan_biaya_klt }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_swan_biaya_pwk }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_swan_biaya_total }}%</th>
            </tr>
            <tr>
                <th style="background-color:rgb(93, 0, 0); color:white">AIDA</th>
                <th style="background-color:rgb(93, 0, 0); color:white; text-align:right">{{ rupiah($aida_tsm) }}</th>
                <th style="background-color:rgb(93, 0, 0); color:white; text-align:right">{{ rupiah($aida_bdg) }}</th>
                <th style="background-color:rgb(93, 0, 0); color:white; text-align:right">{{ rupiah($aida_skb) }}</th>
                <th style="background-color:rgb(93, 0, 0); color:white; text-align:right">{{ rupiah($aida_tgl) }}</th>
                <th style="background-color:rgb(93, 0, 0); color:white; text-align:right">{{ rupiah($aida_bgr) }}</th>
                <th style="background-color:rgb(93, 0, 0); color:white; text-align:right">{{ rupiah($aida_pwt) }}</th>
                <th style="background-color:rgb(93, 0, 0); color:white; text-align:right">{{ rupiah($aida_pst) }}</th>
                <th style="background-color:rgb(93, 0, 0); color:white; text-align:right">{{ rupiah($aida_grt) }}</th>
                <th style="background-color:rgb(93, 0, 0); color:white; text-align:right">{{ rupiah($aida_sby) }}</th>
                <th style="background-color:rgb(93, 0, 0); color:white; text-align:right">{{ rupiah($aida_smr) }}</th>
                <th style="background-color:rgb(93, 0, 0); color:white; text-align:right">{{ rupiah($aida_klt) }}</th>
                <th style="background-color:rgb(93, 0, 0); color:white; text-align:right">{{ rupiah($aida_pwk) }}</th>
                <th style="background-color:rgb(93, 0, 0); color:white; text-align:right">{{ rupiah($totalaida) }}</th>
            </tr>
            <tr>
                <th style="background-color:rgb(93, 0, 0); color:white">COST RATIO</th>
                <th style="background-color:rgb(93, 0, 0); color:white;">{{ $cr_aida_biaya_tsm }}%</th>
                <th style="background-color:rgb(93, 0, 0); color:white;">{{ $cr_aida_biaya_bdg }}%</th>
                <th style="background-color:rgb(93, 0, 0); color:white;">{{ $cr_aida_biaya_skb }}%</th>
                <th style="background-color:rgb(93, 0, 0); color:white;">{{ $cr_aida_biaya_tgl }}%</th>
                <th style="background-color:rgb(93, 0, 0); color:white;">{{ $cr_aida_biaya_bgr }}%</th>
                <th style="background-color:rgb(93, 0, 0); color:white;">{{ $cr_aida_biaya_pwt }}%</th>
                <th style="background-color:rgb(93, 0, 0); color:white;">{{ $cr_aida_biaya_pst }}%</th>
                <th style="background-color:rgb(93, 0, 0); color:white;">{{ $cr_aida_biaya_grt }}%</th>
                <th style="background-color:rgb(93, 0, 0); color:white;">{{ $cr_aida_biaya_sby }}%</th>
                <th style="background-color:rgb(93, 0, 0); color:white;">{{ $cr_aida_biaya_smr }}%</th>
                <th style="background-color:rgb(93, 0, 0); color:white;">{{ $cr_aida_biaya_klt }}%</th>
                <th style="background-color:rgb(93, 0, 0); color:white;">{{ $cr_aida_biaya_pwk }}%</th>
                <th style="background-color:rgb(93, 0, 0); color:white;">{{ $cr_aida_biaya_total }}%</th>
            </tr>
            <tr>
                <th style="background-color:rgb(0, 52, 93); color:white" colspan="3">TOTAL PENJUALAN</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($penjualan_tsm) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($penjualan_bdg) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($penjualan_skb) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($penjualan_tgl) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($penjualan_bgr) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($penjualan_pwt) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($penjualan_pst) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($penjualan_grt) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($penjualan_sby) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($penjualan_smr) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($penjualan_klt) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($penjualan_pwk) }}</th>
                <th style="background-color:rgb(0, 52, 93); color:white; text-align:right">{{ rupiah($totalpenjualan) }}</th>
            </tr>
            <tr>
                <th style="background-color:rgb(0, 52, 93); color:white" colspan="3">COST RATIO(%)</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_penjualan_biaya_tsm }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_penjualan_biaya_bdg }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_penjualan_biaya_skb }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_penjualan_biaya_tgl }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_penjualan_biaya_bgr }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_penjualan_biaya_pwt }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_penjualan_biaya_pst }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_penjualan_biaya_grt }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_penjualan_biaya_sby }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_penjualan_biaya_smr }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_penjualan_biaya_klt }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_penjualan_biaya_pwk }}%</th>
                <th style="background-color:rgb(0, 52, 93); color:white">{{ $cr_penjualan_biaya_total }}%</th>
            </tr>
            <tr>
                <th style="background-color:rgb(210, 59, 4); color:white" colspan="3">PIUTANG > 1 BULAN</th>
                <th style="background-color:rgb(210, 59, 4); color:white; text-align:right">{{ rupiah($piutang->TSM) }}</th>
                <th style="background-color:rgb(210, 59, 4); color:white; text-align:right">{{ rupiah($piutang->BDG) }}</th>
                <th style="background-color:rgb(210, 59, 4); color:white; text-align:right">{{ rupiah($piutang->SKB) }}</th>
                <th style="background-color:rgb(210, 59, 4); color:white; text-align:right">{{ rupiah($piutang->TGL) }}</th>
                <th style="background-color:rgb(210, 59, 4); color:white; text-align:right">{{ rupiah($piutang->BGR) }}</th>
                <th style="background-color:rgb(210, 59, 4); color:white; text-align:right">{{ rupiah($piutang->PWT) }}</th>
                <th style="background-color:rgb(210, 59, 4); color:white; text-align:right">{{ rupiah($piutang->PST) }}</th>
                <th style="background-color:rgb(210, 59, 4); color:white; text-align:right">{{ rupiah($piutang->GRT) }}</th>
                <th style="background-color:rgb(210, 59, 4); color:white; text-align:right">{{ rupiah($piutang->SBY) }}</th>
                <th style="background-color:rgb(210, 59, 4); color:white; text-align:right">{{ rupiah($piutang->SMR) }}</th>
                <th style="background-color:rgb(210, 59, 4); color:white; text-align:right">{{ rupiah($piutang->KLT) }}</th>
                <th style="background-color:rgb(210, 59, 4); color:white; text-align:right">{{ rupiah($piutang->PWK) }}</th>
                <th style="background-color:rgb(210, 59, 4); color:white; text-align:right">{{ rupiah($piutang->totalpiutang) }}</th>

            </tr>
            @php
            $piutang_tsm = $piutang->TSM;
            $piutang_bdg = $piutang->BDG;
            $piutang_skb = $piutang->SKB;
            $piutang_tgl = $piutang->TGL;
            $piutang_bgr = $piutang->BGR;
            $piutang_pwt = $piutang->PWT;
            $piutang_pst = $piutang->PST;
            $piutang_grt = $piutang->GRT;
            $piutang_sby = $piutang->SBY;
            $piutang_smr = $piutang->SMR;
            $piutang_klt = $piutang->KLT;
            $piutang_pwk = $piutang->PWK;
            $totalpiutang = $piutang->totalpiutang;

            $cr_swan_piutang_tsm = $swan_tsm != 0 ? ROUND(($piutang_tsm/$swan_tsm)*100) : 0;
            $cr_swan_piutang_bdg = $swan_bdg != 0 ? ROUND(($piutang_bdg/$swan_bdg)*100) : 0;
            $cr_swan_piutang_skb = $swan_skb != 0 ? ROUND(($piutang_skb/$swan_skb)*100) : 0;
            $cr_swan_piutang_tgl = $swan_tgl != 0 ? ROUND(($piutang_tgl/$swan_tgl)*100) : 0;
            $cr_swan_piutang_bgr = $swan_bgr != 0 ? ROUND(($piutang_bgr/$swan_bgr)*100) : 0;
            $cr_swan_piutang_pwt = $swan_pwt != 0 ? ROUND(($piutang_pwt/$swan_pwt)*100) : 0;
            $cr_swan_piutang_pst = $swan_pst != 0 ? ROUND(($piutang_pst/$swan_pst)*100) : 0;
            $cr_swan_piutang_grt = $swan_grt != 0 ? ROUND(($piutang_grt/$swan_grt)*100) : 0;
            $cr_swan_piutang_sby = $swan_sby != 0 ? ROUND(($piutang_sby/$swan_sby)*100) : 0;
            $cr_swan_piutang_smr = $swan_smr != 0 ? ROUND(($piutang_smr/$swan_smr)*100) : 0;
            $cr_swan_piutang_klt = $swan_klt != 0 ? ROUND(($piutang_klt/$swan_klt)*100) : 0;
            $cr_swan_piutang_pwk = $swan_pwk != 0 ? ROUND(($piutang_pwk/$swan_pwk)*100) : 0;
            $cr_swan_piutang_total = $totalswan != 0 ? ROUND(($totalpiutang/$totalswan)*100) : 0;

            $cr_aida_piutang_tsm = $aida_tsm != 0 ? ROUND(($piutang_tsm/$aida_tsm)*100) : 0;
            $cr_aida_piutang_bdg = $aida_bdg != 0 ? ROUND(($piutang_bdg/$aida_bdg)*100) : 0;
            $cr_aida_piutang_skb = $aida_skb != 0 ? ROUND(($piutang_skb/$aida_skb)*100) : 0;
            $cr_aida_piutang_tgl = $aida_tgl != 0 ? ROUND(($piutang_tgl/$aida_tgl)*100) : 0;
            $cr_aida_piutang_bgr = $aida_bgr != 0 ? ROUND(($piutang_bgr/$aida_bgr)*100) : 0;
            $cr_aida_piutang_pwt = $aida_pwt != 0 ? ROUND(($piutang_pwt/$aida_pwt)*100) : 0;
            $cr_aida_piutang_pst = $aida_pst != 0 ? ROUND(($piutang_pst/$aida_pst)*100) : 0;
            $cr_aida_piutang_grt = $aida_grt != 0 ? ROUND(($piutang_grt/$aida_grt)*100) : 0;
            $cr_aida_piutang_sby = $aida_sby != 0 ? ROUND(($piutang_sby/$aida_sby)*100) : 0;
            $cr_aida_piutang_smr = $aida_smr != 0 ? ROUND(($piutang_smr/$aida_smr)*100) : 0;
            $cr_aida_piutang_klt = $aida_klt != 0 ? ROUND(($piutang_klt/$aida_klt)*100) : 0;
            $cr_aida_piutang_pwk = $aida_pwk != 0 ? ROUND(($piutang_pwk/$aida_pwk)*100) : 0;
            $cr_aida_piutang_total = $totalaida != 0 ? ROUND(($totalpiutang/$totalaida)*100) : 0;

            $cr_penjualan_piutang_tsm = $penjualan_tsm != 0 ? ROUND(($piutang_tsm/$penjualan_tsm)*100) : 0;
            $cr_penjualan_piutang_bdg = $penjualan_bdg != 0 ? ROUND(($piutang_bdg/$penjualan_bdg)*100) : 0;
            $cr_penjualan_piutang_skb = $penjualan_skb != 0 ? ROUND(($piutang_skb/$penjualan_skb)*100) : 0;
            $cr_penjualan_piutang_tgl = $penjualan_tgl != 0 ? ROUND(($piutang_tgl/$penjualan_tgl)*100) : 0;
            $cr_penjualan_piutang_bgr = $penjualan_bgr != 0 ? ROUND(($piutang_bgr/$penjualan_bgr)*100) : 0;
            $cr_penjualan_piutang_pwt = $penjualan_pwt != 0 ? ROUND(($piutang_pwt/$penjualan_pwt)*100) : 0;
            $cr_penjualan_piutang_pst = $penjualan_pst != 0 ? ROUND(($piutang_pst/$penjualan_pst)*100) : 0;
            $cr_penjualan_piutang_grt = $penjualan_grt != 0 ? ROUND(($piutang_grt/$penjualan_grt)*100) : 0;
            $cr_penjualan_piutang_sby = $penjualan_sby != 0 ? ROUND(($piutang_sby/$penjualan_sby)*100) : 0;
            $cr_penjualan_piutang_smr = $penjualan_smr != 0 ? ROUND(($piutang_smr/$penjualan_smr)*100) : 0;
            $cr_penjualan_piutang_klt = $penjualan_klt != 0 ? ROUND(($piutang_klt/$penjualan_klt)*100) : 0;
            $cr_penjualan_piutang_pwk = $penjualan_pwk != 0 ? ROUND(($piutang_pwk/$penjualan_pwk)*100) : 0;
            $cr_penjualan_piutang_total = $totalpenjualan != 0 ? ROUND(($totalpiutang/$totalpenjualan)*100) : 0;
            @endphp
            <tr>
                <th style="background-color:rgb(210, 59, 4); color:white" colspan="3">COST RATIO SWAN</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_swan_piutang_tsm }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_swan_piutang_bdg }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_swan_piutang_skb }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_swan_piutang_tgl }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_swan_piutang_bgr }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_swan_piutang_pwt }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_swan_piutang_pst }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_swan_piutang_grt }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_swan_piutang_sby }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_swan_piutang_smr }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_swan_piutang_klt }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_swan_piutang_pwk }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_swan_piutang_total }}%</th>
            </tr>
            <tr>
                <th style="background-color:rgb(210, 59, 4); color:white" colspan="3">COST RATIO AIDA</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_aida_piutang_tsm }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_aida_piutang_bdg }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_aida_piutang_skb }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_aida_piutang_tgl }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_aida_piutang_bgr }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_aida_piutang_pwt }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_aida_piutang_pst }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_aida_piutang_grt }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_aida_piutang_sby }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_aida_piutang_smr }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_aida_piutang_klt }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_aida_piutang_pwk }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_aida_piutang_total }}%</th>
            </tr>
            <tr>
                <th style="background-color:rgb(210, 59, 4); color:white" colspan="3">COST RATIO SWAN + AIDA</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_penjualan_piutang_tsm }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_penjualan_piutang_bdg }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_penjualan_piutang_skb }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_penjualan_piutang_tgl }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_penjualan_piutang_bgr }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_penjualan_piutang_pwt }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_penjualan_piutang_pst }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_penjualan_piutang_grt }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_penjualan_piutang_sby }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_penjualan_piutang_smr }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_penjualan_piutang_klt }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_penjualan_piutang_pwk }}%</th>
                <th style="background-color:rgb(210, 59, 4); color:white">{{ $cr_penjualan_piutang_total }}%</th>
            </tr>
            @php
            $biaya_piutang_tsm = $totaltsm + $piutang_tsm;
            $biaya_piutang_bdg = $totalbdg + $piutang_bdg;
            $biaya_piutang_skb = $totalskb + $piutang_skb;
            $biaya_piutang_tgl = $totaltgl + $piutang_tgl;
            $biaya_piutang_bgr = $totalbgr + $piutang_bgr;
            $biaya_piutang_pwt = $totalpwt + $piutang_pwt;
            $biaya_piutang_pst = $totalpst + $piutang_pst;
            $biaya_piutang_grt = $totalgrt + $piutang_grt;
            $biaya_piutang_sby = $totalsby + $piutang_sby;
            $biaya_piutang_smr = $totalsmr + $piutang_smr;
            $biaya_piutang_klt = $totalklt + $piutang_klt;
            $biaya_piutang_pwk = $totalpwk + $piutang_pwk;
            $total_biaya_piutang = $grandtotal + $totalpiutang;

            $cr_swan_biayapiutang_tsm = $swan_tsm != 0 ? ROUND(($biaya_piutang_tsm/$swan_tsm)*100) : 0;
            $cr_swan_biayapiutang_bdg = $swan_bdg != 0 ? ROUND(($biaya_piutang_bdg/$swan_bdg)*100) : 0;
            $cr_swan_biayapiutang_skb = $swan_skb != 0 ? ROUND(($biaya_piutang_skb/$swan_skb)*100) : 0;
            $cr_swan_biayapiutang_tgl = $swan_tgl != 0 ? ROUND(($biaya_piutang_tgl/$swan_tgl)*100) : 0;
            $cr_swan_biayapiutang_bgr = $swan_bgr != 0 ? ROUND(($biaya_piutang_bgr/$swan_bgr)*100) : 0;
            $cr_swan_biayapiutang_pwt = $swan_pwt != 0 ? ROUND(($biaya_piutang_pwt/$swan_pwt)*100) : 0;
            $cr_swan_biayapiutang_pst = $swan_pst != 0 ? ROUND(($biaya_piutang_pst/$swan_pst)*100) : 0;
            $cr_swan_biayapiutang_grt = $swan_grt != 0 ? ROUND(($biaya_piutang_grt/$swan_grt)*100) : 0;
            $cr_swan_biayapiutang_sby = $swan_sby != 0 ? ROUND(($biaya_piutang_sby/$swan_sby)*100) : 0;
            $cr_swan_biayapiutang_smr = $swan_smr != 0 ? ROUND(($biaya_piutang_smr/$swan_smr)*100) : 0;
            $cr_swan_biayapiutang_klt = $swan_klt != 0 ? ROUND(($biaya_piutang_klt/$swan_klt)*100) : 0;
            $cr_swan_biayapiutang_pwk = $swan_pwk != 0 ? ROUND(($biaya_piutang_pwk/$swan_pwk)*100) : 0;
            $cr_swan_biayapiutang_total = $totalswan != 0 ? ROUND(($total_biaya_piutang/$totalswan)*100) : 0;

            $cr_aida_biayapiutang_tsm = $aida_tsm != 0 ? ROUND(($biaya_piutang_tsm/$aida_tsm)*100) : 0;
            $cr_aida_biayapiutang_bdg = $aida_bdg != 0 ? ROUND(($biaya_piutang_bdg/$aida_bdg)*100) : 0;
            $cr_aida_biayapiutang_skb = $aida_skb != 0 ? ROUND(($biaya_piutang_skb/$aida_skb)*100) : 0;
            $cr_aida_biayapiutang_tgl = $aida_tgl != 0 ? ROUND(($biaya_piutang_tgl/$aida_tgl)*100) : 0;
            $cr_aida_biayapiutang_bgr = $aida_bgr != 0 ? ROUND(($biaya_piutang_bgr/$aida_bgr)*100) : 0;
            $cr_aida_biayapiutang_pwt = $aida_pwt != 0 ? ROUND(($biaya_piutang_pwt/$aida_pwt)*100) : 0;
            $cr_aida_biayapiutang_pst = $aida_pst != 0 ? ROUND(($biaya_piutang_pst/$aida_pst)*100) : 0;
            $cr_aida_biayapiutang_grt = $aida_grt != 0 ? ROUND(($biaya_piutang_grt/$aida_grt)*100) : 0;
            $cr_aida_biayapiutang_sby = $aida_sby != 0 ? ROUND(($biaya_piutang_sby/$aida_sby)*100) : 0;
            $cr_aida_biayapiutang_smr = $aida_smr != 0 ? ROUND(($biaya_piutang_smr/$aida_smr)*100) : 0;
            $cr_aida_biayapiutang_klt = $aida_klt != 0 ? ROUND(($biaya_piutang_klt/$aida_klt)*100) : 0;
            $cr_aida_biayapiutang_pwk = $aida_pwk != 0 ? ROUND(($biaya_piutang_pwk/$aida_pwk)*100) : 0;
            $cr_aida_biayapiutang_total = $totalaida != 0 ? ROUND(($total_biaya_piutang/$totalaida)*100) : 0;

            $cr_penjualan_biayapiutang_tsm = $penjualan_tsm != 0 ? ROUND(($biaya_piutang_tsm/$penjualan_tsm)*100) : 0;
            $cr_penjualan_biayapiutang_bdg = $penjualan_bdg != 0 ? ROUND(($biaya_piutang_bdg/$penjualan_bdg)*100) : 0;
            $cr_penjualan_biayapiutang_skb = $penjualan_skb != 0 ? ROUND(($biaya_piutang_skb/$penjualan_skb)*100) : 0;
            $cr_penjualan_biayapiutang_tgl = $penjualan_tgl != 0 ? ROUND(($biaya_piutang_tgl/$penjualan_tgl)*100) : 0;
            $cr_penjualan_biayapiutang_bgr = $penjualan_bgr != 0 ? ROUND(($biaya_piutang_bgr/$penjualan_bgr)*100) : 0;
            $cr_penjualan_biayapiutang_pwt = $penjualan_pwt != 0 ? ROUND(($biaya_piutang_pwt/$penjualan_pwt)*100) : 0;
            $cr_penjualan_biayapiutang_pst = $penjualan_pst != 0 ? ROUND(($biaya_piutang_pst/$penjualan_pst)*100) : 0;
            $cr_penjualan_biayapiutang_grt = $penjualan_grt != 0 ? ROUND(($biaya_piutang_grt/$penjualan_grt)*100) : 0;
            $cr_penjualan_biayapiutang_sby = $penjualan_sby != 0 ? ROUND(($biaya_piutang_sby/$penjualan_sby)*100) : 0;
            $cr_penjualan_biayapiutang_smr = $penjualan_smr != 0 ? ROUND(($biaya_piutang_smr/$penjualan_smr)*100) : 0;
            $cr_penjualan_biayapiutang_klt = $penjualan_klt != 0 ? ROUND(($biaya_piutang_klt/$penjualan_klt)*100) : 0;
            $cr_penjualan_biayapiutang_pwk = $penjualan_pwk != 0 ? ROUND(($biaya_piutang_pwk/$penjualan_pwk)*100) : 0;
            $cr_penjualan_biayapiutang_total = $totalpenjualan != 0 ? ROUND(($total_biaya_piutang/$totalpenjualan)*100) : 0;

            @endphp
            <tr>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white" colspan="3">BIAYA + PIUTANG</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white; text-align:right">{{ rupiah($biaya_piutang_tsm) }}</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white; text-align:right">{{ rupiah($biaya_piutang_bdg) }}</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white; text-align:right">{{ rupiah($biaya_piutang_skb) }}</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white; text-align:right">{{ rupiah($biaya_piutang_tgl) }}</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white; text-align:right">{{ rupiah($biaya_piutang_bgr) }}</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white; text-align:right">{{ rupiah($biaya_piutang_pwt) }}</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white; text-align:right">{{ rupiah($biaya_piutang_pst) }}</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white; text-align:right">{{ rupiah($biaya_piutang_grt) }}</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white; text-align:right">{{ rupiah($biaya_piutang_sby) }}</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white; text-align:right">{{ rupiah($biaya_piutang_smr) }}</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white; text-align:right">{{ rupiah($biaya_piutang_klt) }}</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white; text-align:right">{{ rupiah($biaya_piutang_pwk) }}</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white; text-align:right">{{ rupiah($total_biaya_piutang) }}</th>
            </tr>
            <tr>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white" colspan="3">COST RATIO SWAN</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_swan_biayapiutang_tsm }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_swan_biayapiutang_bdg }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_swan_biayapiutang_skb }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_swan_biayapiutang_tgl }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_swan_biayapiutang_bgr }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_swan_biayapiutang_pwt }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_swan_biayapiutang_pst }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_swan_biayapiutang_grt }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_swan_biayapiutang_sby }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_swan_biayapiutang_smr }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_swan_biayapiutang_klt }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_swan_biayapiutang_pwk }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_swan_biayapiutang_total }}%</th>
            </tr>
            <tr>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white" colspan="3">COST RATIO AIDA</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_aida_biayapiutang_tsm }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_aida_biayapiutang_bdg }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_aida_biayapiutang_skb }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_aida_biayapiutang_tgl }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_aida_biayapiutang_bgr }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_aida_biayapiutang_pwt }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_aida_biayapiutang_pst }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_aida_biayapiutang_grt }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_aida_biayapiutang_sby }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_aida_biayapiutang_smr }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_aida_biayapiutang_klt }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_aida_biayapiutang_pwk }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_aida_biayapiutang_total }}%</th>
            </tr>
            <tr>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white" colspan="3">COST RATIO SWAN + AIDA</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_penjualan_biayapiutang_tsm }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_penjualan_biayapiutang_bdg }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_penjualan_biayapiutang_skb }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_penjualan_biayapiutang_tgl }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_penjualan_biayapiutang_bgr }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_penjualan_biayapiutang_pwt }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_penjualan_biayapiutang_pst }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_penjualan_biayapiutang_grt }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_penjualan_biayapiutang_sby }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_penjualan_biayapiutang_smr }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_penjualan_biayapiutang_klt }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_penjualan_biayapiutang_pwk }}%</th>
                <th style="background-color:rgba(145, 2, 59, 0.961); color:white">{{ $cr_penjualan_biayapiutang_total }}%</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
