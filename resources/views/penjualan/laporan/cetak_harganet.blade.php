<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Data Pengambilan Pelanggan</title>
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
        LAPORAN HARGA NET<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
    </b>
    @php
    foreach ($produk as $p) {
    if ($p->kode_produk == "AB") {
    $isipcs_ab = $p->isipcsdus;
    }
    if ($p->kode_produk == "AR") {
    $isipcs_ar = $p->isipcsdus;
    }
    if ($p->kode_produk == "AS") {
    $isipcs_as = $p->isipcsdus;
    }

    if ($p->kode_produk == "BB") {
    $isipcs_bb = $p->isipcsdus;
    }

    if ($p->kode_produk == "BBP") {
    $isipcs_bbp = $p->isipcsdus;
    }

    if ($p->kode_produk == "CG") {
    $isipcs_cg = $p->isipcsdus;
    }

    if ($p->kode_produk == "CGG") {
    $isipcs_cgg = $p->isipcsdus;
    }

    if ($p->kode_produk == "CG5") {
    $isipcs_cg5 = $p->isipcsdus;
    }

    if ($p->kode_produk == "DEP") {
    $isipcs_dep = $p->isipcsdus;
    }

    if ($p->kode_produk == "DS") {
    $isipcs_ds = $p->isipcsdus;
    }

    if ($p->kode_produk == "SP") {
    $isipcs_sp = $p->isipcsdus;
    }

    if ($p->kode_produk == "SPP") {
    $isipcs_spp = $p->isipcsdus;
    }

    if ($p->kode_produk == "SC") {
    $isipcs_sc = $p->isipcsdus;
    }

    if ($p->kode_produk == "SP8") {
    $isipcs_sp8 = $p->isipcsdus;
    }

    if ($p->kode_produk == "SP500") {
    $isipcs_sp500 = $p->isipcsdus;
    }
    }
    $penyharga = $harganet->penyharga;

    $totalqty = ($harganet->qty_AB / $isipcs_ab) + ($harganet->qty_AR / $isipcs_ar) + ($harganet->qty_AS / $isipcs_as) + ($harganet->qty_BB / $isipcs_bb) + ($harganet->qty_BBP / $isipcs_bbp)
    + ($harganet->qty_CG / $isipcs_cg) + ($harganet->qty_CGG / $isipcs_cgg) + ($harganet->qty_CG5 / $isipcs_cg5) + ($harganet->qty_DEP / $isipcs_dep) + ($harganet->qty_DS / $isipcs_ds) + ($harganet->qty_SP / $isipcs_sp)
    + ($harganet->qty_SPP / $isipcs_sp) + ($harganet->qty_SC / $isipcs_sp) + ($harganet->qty_SP8 / $isipcs_sp) + ($harganet->qty_SP500 / $isipcs_sp500);


    $ratiopeny = ($totalqty != 0) ? $penyharga / $totalqty : 0;
    //dd($harganet->qty_CG)
    @endphp
    <table class="datatable3">
        <tr>
            <th rowspan="2">KETERANGAN</th>
            <th colspan="14">NAMA PRODUK</th>

        </tr>
        <tr>
            <th>AB</th>
            <th>AR</th>
            <th>AS</th>
            <th>BB</th>
            <th>BBP</th>
            <th>CG</th>
            <th>CGG</th>
            <th>CG5</th>
            <th>DEP</th>
            <th>DS</th>
            <th>SP</th>

            <th>SC</th>
            <th>SP8</th>
            <th>SP500</th>
        </tr>



        <tr style="font-size:14px;">
            <?php
                $bruto_AB_tunai = $harganet->bruto_AB_tunai + (($harganet->bruto_AB_tunai/$harganet->totalbrutotunai)*$harganet->totalppntunai);
                $bruto_AB_kredit = $harganet->bruto_AB_kredit + (($harganet->bruto_AB_kredit/$harganet->totalbrutokredit)*$harganet->totalppnkredit);
                $bruto_AB = $bruto_AB_tunai + $bruto_AB_kredit;


                $bruto_AR_tunai = $harganet->bruto_AR_tunai + (($harganet->bruto_AR_tunai/$harganet->totalbrutotunai)*$harganet->totalppntunai);
                $bruto_AR_kredit = $harganet->bruto_AR_kredit + (($harganet->bruto_AR_kredit/$harganet->totalbrutokredit)*$harganet->totalppnkredit);
                $bruto_AR = $bruto_AR_tunai + $bruto_AR_kredit;


                $bruto_AS_tunai = $harganet->bruto_AS_tunai + (($harganet->bruto_AS_tunai/$harganet->totalbrutotunai)*$harganet->totalppntunai);
                $bruto_AS_kredit = $harganet->bruto_AS_kredit + (($harganet->bruto_AS_kredit/$harganet->totalbrutokredit)*$harganet->totalppnkredit);
                $bruto_AS = $bruto_AS_tunai + $bruto_AS_kredit;


                $bruto_BB_tunai = $harganet->bruto_BB_tunai + (($harganet->bruto_BB_tunai/$harganet->totalbrutotunai)*$harganet->totalppntunai);
                $bruto_BB_kredit = $harganet->bruto_BB_kredit + (($harganet->bruto_BB_kredit/$harganet->totalbrutokredit)*$harganet->totalppnkredit);
                $bruto_BB = $bruto_BB_tunai + $bruto_BB_kredit;


                $bruto_BBP_tunai = $harganet->bruto_BBP_tunai + (($harganet->bruto_BBP_tunai/$harganet->totalbrutotunai)*$harganet->totalppntunai);
                $bruto_BBP_kredit = $harganet->bruto_BBP_kredit + (($harganet->bruto_BBP_kredit/$harganet->totalbrutokredit)*$harganet->totalppnkredit);
                $bruto_BBP = $bruto_BBP_tunai + $bruto_BBP_kredit;

                $bruto_CG_tunai = $harganet->bruto_CG_tunai + (($harganet->bruto_CG_tunai/$harganet->totalbrutotunai)*$harganet->totalppntunai);
                $bruto_CG_kredit = $harganet->bruto_CG_kredit + (($harganet->bruto_CG_kredit/$harganet->totalbrutokredit)*$harganet->totalppnkredit);
                $bruto_CG = $bruto_CG_tunai + $bruto_CG_kredit;



                $bruto_CGG = $harganet->bruto_CGG + (($harganet->bruto_CGG/$harganet->totalbruto)*$harganet->totalppn);
                $bruto_CGG = $harganet->bruto_CGG + (($harganet->bruto_CGG/$harganet->totalbruto)*$harganet->totalppn);
                $bruto_CGG = $harganet->bruto_CGG + (($harganet->bruto_CGG/$harganet->totalbruto)*$harganet->totalppn);


                $bruto_CG5_tunai = $harganet->bruto_CG5_tunai + (($harganet->bruto_CG5_tunai/$harganet->totalbrutotunai)*$harganet->totalppntunai);
                $bruto_CG5_kredit = $harganet->bruto_CG5_kredit + (($harganet->bruto_CG5_kredit/$harganet->totalbrutokredit)*$harganet->totalppnkredit);
                $bruto_CG5 = $bruto_CG5_tunai + $bruto_CG5_kredit;

                $bruto_DEP_tunai = $harganet->bruto_DEP_tunai + (($harganet->bruto_DEP_tunai/$harganet->totalbrutotunai)*$harganet->totalppntunai);
                $bruto_DEP_kredit = $harganet->bruto_DEP_kredit + (($harganet->bruto_DEP_kredit/$harganet->totalbrutokredit)*$harganet->totalppnkredit);
                $bruto_DEP = $bruto_DEP_tunai + $bruto_DEP_kredit;

                $bruto_DS_tunai = $harganet->bruto_DS_tunai + (($harganet->bruto_DS_tunai/$harganet->totalbrutotunai)*$harganet->totalppntunai);
                $bruto_DS_kredit = $harganet->bruto_DS_kredit + (($harganet->bruto_DS_kredit/$harganet->totalbrutokredit)*$harganet->totalppnkredit);
                $bruto_DS = $bruto_DS_tunai + $bruto_DS_kredit;

                $bruto_SP_tunai = $harganet->bruto_SP_tunai + (($harganet->bruto_SP_tunai/$harganet->totalbrutotunai)*$harganet->totalppntunai);
                $bruto_SP_kredit = $harganet->bruto_SP_kredit + (($harganet->bruto_SP_kredit/$harganet->totalbrutokredit)*$harganet->totalppnkredit);
                $bruto_SP = $bruto_SP_tunai + $bruto_SP_kredit;


                $bruto_SC_tunai = $harganet->bruto_SC_tunai + (($harganet->bruto_SC_tunai/$harganet->totalbrutotunai)*$harganet->totalppntunai);
                $bruto_SC_kredit = $harganet->bruto_SC_kredit + (($harganet->bruto_SC_kredit/$harganet->totalbrutokredit)*$harganet->totalppnkredit);
                $bruto_SC = $bruto_SC_tunai + $bruto_SC_kredit;

                $bruto_SP8_tunai = $harganet->bruto_SP8_tunai + (($harganet->bruto_SP8_tunai/$harganet->totalbrutotunai)*$harganet->totalppntunai);
                $bruto_SP8_kredit = $harganet->bruto_SP8_kredit + (($harganet->bruto_SP8_kredit/$harganet->totalbrutokredit)*$harganet->totalppnkredit);
                $bruto_SP8 = $bruto_SP8_tunai + $bruto_SP8_kredit;


                $bruto_SP500_tunai = $harganet->bruto_SP500_tunai + (($harganet->bruto_SP500_tunai/$harganet->totalbrutotunai)*$harganet->totalppntunai);
                $bruto_SP500_kredit = $harganet->bruto_SP500_kredit + (($harganet->bruto_SP500_kredit/$harganet->totalbrutokredit)*$harganet->totalppnkredit);
                $bruto_SP500 = $bruto_SP500_tunai + $bruto_SP500_kredit;
            ?>
            <td>PENJUALAN BRUTO</td>
            <td align="right"><?php echo number_format($bruto_AB, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($bruto_AR, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($bruto_AS, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($bruto_BB, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($bruto_BBP, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($bruto_CG, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($bruto_CGG, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($bruto_CG5, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($bruto_DEP, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($bruto_DS, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($bruto_SP, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($bruto_SC, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($bruto_SP8, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($bruto_SP500, '0', '', '.'); ?></td>

        </tr>

        <tr style="font-size:14px;">
            <td>DISKON QTY / PRODUK</td>
            <td align="right"><?php echo number_format($harganet->diskon_AB, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->diskon_AR, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->diskon_AS, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->diskon_BB, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->diskon_BBP, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->diskon_CG, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->diskon_CGG, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->diskon_CG5, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->diskon_DEP, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->diskon_DS, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->diskon_SP, '0', '', '.'); ?></td>

            <td align="right"><?php echo number_format($harganet->diskon_SC, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->diskon_SP8, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->diskon_SP500, '0', '', '.'); ?></td>

        </tr>
        <tr style="font-size:14px;">
            <?php
            $peny_AB = ($ratiopeny != 0 ) ? ($harganet->qty_AB / $isipcs_ab) / $ratiopeny : 0;
            $peny_AR = ($ratiopeny != 0 ) ? ($harganet->qty_AR / $isipcs_ar) / $ratiopeny : 0;
            $peny_AS = ($ratiopeny != 0 ) ? ($harganet->qty_AS / $isipcs_as) / $ratiopeny : 0;
            $peny_BB = ($ratiopeny != 0 ) ? ($harganet->qty_BB / $isipcs_bb) / $ratiopeny : 0;
            $peny_BBP = ($ratiopeny != 0 ) ? ($harganet->qty_BBP / $isipcs_bbp) / $ratiopeny : 0;
            $peny_CG = ($ratiopeny != 0 ) ? ($harganet->qty_CG / $isipcs_cg) / $ratiopeny : 0;
            $peny_CGG = ($ratiopeny != 0 ) ? ($harganet->qty_CGG / $isipcs_cgg) / $ratiopeny : 0;
            $peny_CG5 = ($ratiopeny != 0 ) ? ($harganet->qty_CG5 / $isipcs_cg5) / $ratiopeny : 0;
            $peny_DEP = ($ratiopeny != 0 ) ? ($harganet->qty_DEP / $isipcs_dep) / $ratiopeny : 0;
            $peny_DS = ($ratiopeny != 0 ) ? ($harganet->qty_DS / $isipcs_ds) / $ratiopeny : 0;
            $peny_SP = ($ratiopeny != 0 ) ? ($harganet->qty_SP / $isipcs_sp) / $ratiopeny : 0;
            $peny_SC = ($ratiopeny != 0 ) ? ($harganet->qty_SC / $isipcs_sc) / $ratiopeny : 0;
            $peny_SP8 = ($ratiopeny != 0 ) ? ($harganet->qty_SP8 / $isipcs_sp8) / $ratiopeny : 0;
            $peny_SP500 = ($ratiopeny != 0 ) ? ($harganet->qty_SP500 / $isipcs_sp500) / $ratiopeny : 0;
            ?>
            <td>PENYESUAIAN HARGA</td>
            <td align="right"><?php echo number_format($peny_AB, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($peny_AR, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($peny_AS, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($peny_BB, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($peny_BBP, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($peny_CG, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($peny_CGG, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($peny_CG5, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($peny_DEP, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($peny_DS, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($peny_SP, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($peny_SC, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($peny_SP8, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($peny_SP500, '2', ',', '.'); ?></td>

        </tr>
        <tr style="font-size:14px;">
            <td>PENJUALAN QTY DUS</td>

            <td align="right"><?php echo number_format($harganet->qty_AB / $isipcs_ab, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->qty_AR / $isipcs_ar, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->qty_AS / $isipcs_as, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->qty_BB / $isipcs_bb, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->qty_BBP / $isipcs_bbp, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->qty_CG / $isipcs_cg, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->qty_CGG / $isipcs_cgg, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->qty_CG5 / $isipcs_cg5, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->qty_DEP / $isipcs_dep, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->qty_DS / $isipcs_ds, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->qty_SP / $isipcs_sp, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->qty_SC / $isipcs_sc, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->qty_SP8 / $isipcs_sp8, '2', ',', '.'); ?></td>
            <td align="right"><?php echo number_format($harganet->qty_SP500 / $isipcs_sp500, '2', ',', '.'); ?></td>
        </tr>
        <tr style="font-size:14px; background-color: black; color:white">
            <td>HARGA NET TANPA RETUR</td>

            <td align="right">
                <?php if($harganet->qty_AB!=0){
                    echo number_format(($bruto_AB - (($harganet->qty_AB / $isipcs_ab) / $ratiopeny) - $harganet->diskon_AB) / ($harganet->qty_AB / $isipcs_ab), '0', '', '.'); }?>
            </td>
            <td align="right"><?php if($harganet->qty_AR!=0){echo number_format(($bruto_AR - (($harganet->qty_AR / $isipcs_ar) / $ratiopeny) - $harganet->diskon_AR) / ($harganet->qty_AR / $isipcs_ar), '0', '', '.'); }?></td>
            <td align="right"><?php if($harganet->qty_AS!=0){echo number_format(($bruto_AS - (($harganet->qty_AS / $isipcs_as) / $ratiopeny) - $harganet->diskon_AS) / ($harganet->qty_AS / $isipcs_as), '0', '', '.'); }?></td>
            <td align="right">
                <?php if($harganet->qty_BB!=0){echo number_format(($bruto_BB - (($harganet->qty_BB / $isipcs_bb) / $ratiopeny) - $harganet->diskon_BB) / ($harganet->qty_BB / $isipcs_bb), '0', '', '.'); }?>
            </td>

            <td align="right"><?php if($harganet->qty_BBP!=0){echo number_format(($bruto_BBP - (($harganet->qty_BBP / $isipcs_bbp) / $ratiopeny) - $harganet->diskon_BBP) / ($harganet->qty_BBP / $isipcs_bbp), '0', '', '.'); }?></td>
            <td align="right"><?php if($harganet->qty_CG!=0){echo number_format(($bruto_CG - (($harganet->qty_CG / $isipcs_cg) / $ratiopeny) - $harganet->diskon_CG) / ($harganet->qty_CG / $isipcs_cg), '0', '', '.'); }?></td>
            <td align="right"><?php if($harganet->qty_CGG!=0){echo number_format(($bruto_CGG - (($harganet->qty_CGG / $isipcs_cgg) / $ratiopeny) - $harganet->diskon_CGG) / ($harganet->qty_CGG / $isipcs_cgg), '0', '', '.'); }?></td>
            <td align="right"><?php if($harganet->qty_CG5!=0){echo number_format(($bruto_CG5 - (($harganet->qty_CG5 / $isipcs_cg5) / $ratiopeny) - $harganet->diskon_CG5) / ($harganet->qty_CG5 / $isipcs_cg5), '0', '', '.'); }?></td>
            <td align="right"><?php if($harganet->qty_DEP!=0){echo number_format(($bruto_DEP - (($harganet->qty_DEP / $isipcs_dep) / $ratiopeny) - $harganet->diskon_DEP) / ($harganet->qty_DEP / $isipcs_dep), '0', '', '.'); }?></td>
            <td align="right"><?php if($harganet->qty_DS!=0){echo number_format(($bruto_DS - (($harganet->qty_DS / $isipcs_ds) / $ratiopeny) - $harganet->diskon_DS) / ($harganet->qty_DS / $isipcs_ds), '0', '', '.'); }?></td>
            <td align="right"><?php if($harganet->qty_SP!=0){echo number_format(($bruto_SP - (($harganet->qty_SP / $isipcs_ab) / $ratiopeny) - $harganet->diskon_SP) / ($harganet->qty_SP / $isipcs_sp), '0', '', '.'); }?></td>
            <td align="right"><?php if($harganet->qty_SC!=0){echo number_format(($bruto_SC - (($harganet->qty_SC / $isipcs_ab) / $ratiopeny) - $harganet->diskon_SC) / ($harganet->qty_SC / $isipcs_sc), '0', '', '.'); }?></td>
            <td align="right"><?php if($harganet->qty_SP8!=0){echo number_format(($bruto_SP8 - (($harganet->qty_SP8 / $isipcs_ab) / $ratiopeny) - $harganet->diskon_SP8) / ($harganet->qty_SP8 / $isipcs_sp8), '0', '', '.'); }?></td>

            <td align="right"><?php if($harganet->qty_SP500!=0){echo number_format(($bruto_SP500 - (($harganet->qty_SP500 / $isipcs_ab) / $ratiopeny) - $harganet->diskon_SP500) / ($harganet->qty_SP500 / $isipcs_sp500), '0', '', '.'); }?></td>


        </tr>
        <tr style="font-size:14px;">
            <td>RETUR</td>
            <td align="right"><?php echo number_format($retur->retur_AB, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->retur_AR, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->retur_AS, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->retur_BB, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->retur_BBP, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->retur_CG, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->retur_CGG, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->retur_CG5, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->retur_DEP, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->retur_DS, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->retur_SP, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->retur_SC, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->retur_SP8, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->retur_SP500, '0', '', '.'); ?></td>
        </tr>
        <tr style="font-size:14px;">
            <td>PENY RETUR</td>
            <td align="right"><?php echo number_format($retur->returpeny_AB, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->returpeny_AR, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->returpeny_AS, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->returpeny_BB, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->returpeny_BBP, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->returpeny_CG, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->returpeny_CGG, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->returpeny_CG5, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->returpeny_DEP, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->returpeny_DS, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->returpeny_SP, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->returpeny_SC, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->returpeny_SP8, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($retur->returpeny_SP500, '0', '', '.'); ?></td>
        </tr>

        <tr style="font-size:14px;">
            <?php
            $netretur_AB = $retur->retur_AB - $retur->returpeny_AB;
            $netretur_AR = $retur->retur_AR - $retur->returpeny_AR;
            $netretur_AS = $retur->retur_AS - $retur->returpeny_AS;
            $netretur_BB = $retur->retur_BB - $retur->returpeny_BB;
            $netretur_BBP = $retur->retur_BBP - $retur->returpeny_BBP;
            $netretur_CG = $retur->retur_CG - $retur->returpeny_CG;
            $netretur_CGG = $retur->retur_CGG - $retur->returpeny_CGG;
            $netretur_CG5 = $retur->retur_CG5 - $retur->returpeny_CG5;
            $netretur_DEP = $retur->retur_DEP - $retur->returpeny_DEP;
            $netretur_DS = $retur->retur_DS - $retur->returpeny_DS;
            $netretur_SP = $retur->retur_SP - $retur->returpeny_SP;
            $netretur_SC = $retur->retur_SC - $retur->returpeny_SC;
            $netretur_SP8 = $retur->retur_SP8 - $retur->returpeny_SP8;
            $netretur_SP500 = $retur->retur_SP500 - $retur->returpeny_SP500;
            ?>
            <td>RETUR NET</td>
            <td align="right"><?php echo number_format($netretur_AB, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netretur_AR, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netretur_AS, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netretur_BB, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netretur_BBP, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netretur_CG, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netretur_CGG, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netretur_CG5, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netretur_DEP, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netretur_DS, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netretur_SP, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netretur_SC, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netretur_SP8, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netretur_SP500, '0', '', '.'); ?></td>
        </tr>

        <tr style="font-size:14px; background-color:black; color:white">
            <?php
            $netjual_AB = (($harganet->qty_AB != 0) ? ($bruto_AB - $harganet->diskon_AB - $peny_AB - $netretur_AB) / ($harganet->qty_AB / $isipcs_ab) : 0);
            $netjual_AR = (($harganet->qty_AR != 0) ? ($bruto_AR - $harganet->diskon_AR - $peny_AR - $netretur_AR) / ($harganet->qty_AR / $isipcs_ar) : 0);
            $netjual_AS = (($harganet->qty_AS != 0) ? ($bruto_AS - $harganet->diskon_AS - $peny_AS - $netretur_AS) / ($harganet->qty_AS / $isipcs_as) : 0);
            $netjual_BB = (($harganet->qty_BB != 0) ? ($bruto_BB - $harganet->diskon_BB - $peny_AB - $netretur_BB) / ($harganet->qty_BB / $isipcs_bb) : 0);
            $netjual_BBP = (($harganet->qty_BBP != 0) ? ($bruto_BBP - $harganet->diskon_BBP - $peny_BBP - $netretur_BBP) / ($harganet->qty_BBP / $isipcs_bbp) : 0);
            $netjual_CG = (($harganet->qty_CG != 0) ? ($bruto_CG - $harganet->diskon_CG - $peny_CG - $netretur_CG) / ($harganet->qty_CG / $isipcs_cg) : 0);
            $netjual_CGG = (($harganet->qty_CGG != 0) ? ($bruto_CGG - $harganet->diskon_CGG - $peny_CGG - $netretur_CGG) / ($harganet->qty_CGG / $isipcs_cgg) : 0);
            $netjual_CG5 = (($harganet->qty_CG5 != 0) ? ($bruto_CG5 - $harganet->diskon_CG5 - $peny_CG5 - $netretur_CG5) / ($harganet->qty_CG5 / $isipcs_cg5) : 0);
            $netjual_DEP = (($harganet->qty_DEP != 0) ? ($bruto_DEP - $harganet->diskon_DEP - $peny_DEP - $netretur_DEP) / ($harganet->qty_DEP / $isipcs_dep) : 0);
            $netjual_DS = (($harganet->qty_DS != 0) ? ($bruto_DS - $harganet->diskon_DS - $peny_DS - $netretur_DS) / ($harganet->qty_DS / $isipcs_ds) : 0);
            $netjual_SP = (($harganet->qty_SP != 0) ? ($bruto_SP - $harganet->diskon_SP - $peny_SP - $netretur_SP) / ($harganet->qty_SP / $isipcs_sp) : 0);
            $netjual_SC = (($harganet->qty_SC != 0) ? ($bruto_SC - $harganet->diskon_SC - $peny_SC - $netretur_SC) / ($harganet->qty_SC / $isipcs_sc) : 0);
            $netjual_SP8 = (($harganet->qty_SP8 != 0) ? ($bruto_SP8 - $harganet->diskon_SP8 - $peny_SP8 - $netretur_SP8) / ($harganet->qty_SP8 / $isipcs_sp8) : 0);

            $netjual_SP500 = (($harganet->qty_SP500 != 0) ? ($bruto_SP500 - $harganet->diskon_SP500 - $peny_SP500 - $netretur_SP500) / ($harganet->qty_SP500 / $isipcs_sp500) : 0);
            ?>
            <td>HARGA JUAL NET</td>
            <td align="right"><?php echo number_format($netjual_AB, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netjual_AR, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netjual_AS, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netjual_BB, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netjual_BBP, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netjual_CG, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netjual_CGG, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netjual_CG5, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netjual_DEP, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netjual_DS, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netjual_SP, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netjual_SC, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netjual_SP8, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($netjual_SP500, '0', '', '.'); ?></td>
        </tr>

        <tr style="font-size:14px; background-color:black; color:white">
            <?php
            $exclude_AB = (($harganet->qty_AB!=0) ? ($bruto_AB - $peny_AB - $retur->retur_AB) / ($harganet->qty_AB / $isipcs_ab) : 0);
            $exclude_AR = (($harganet->qty_AR!=0) ? ($bruto_AR - $peny_AR - $retur->retur_AR) / ($harganet->qty_AR / $isipcs_ar) : 0);
            $exclude_AS = (($harganet->qty_AS!=0) ? ($bruto_AS - $peny_AS - $retur->retur_AS) / ($harganet->qty_AS / $isipcs_as) : 0);
            $exclude_BB = (($harganet->qty_BB!=0) ? ($bruto_BB - $peny_BB - $retur->retur_BB) / ($harganet->qty_BB / $isipcs_bb) : 0);
            $exclude_BBP = (($harganet->qty_BBP!=0) ? ($bruto_BBP - $peny_BBP - $retur->retur_BBP) / ($harganet->qty_BBP / $isipcs_bbp) : 0);
            $exclude_CG = (($harganet->qty_CG!=0) ? ($bruto_CG - $peny_CG - $retur->retur_CG) / ($harganet->qty_CG / $isipcs_cg) : 0);
            $exclude_CGG = (($harganet->qty_CGG!=0) ? ($bruto_CGG - $peny_CGG - $retur->retur_CGG) / ($harganet->qty_CGG / $isipcs_cgg) : 0);
            $exclude_CG5 = (($harganet->qty_CG5=0) ? ($bruto_CG5 - $peny_CG5 - $retur->retur_CG5) / ($harganet->qty_CG5 / $isipcs_cg5) : 0);
            $exclude_DEP = (($harganet->qty_DEP!=0) ? ($bruto_DEP - $peny_DEP - $retur->retur_DEP) / ($harganet->qty_DEP / $isipcs_dep) : 0);
            $exclude_DS = (($harganet->qty_DS!=0) ? ($bruto_DS - $peny_DS - $retur->retur_DS) / ($harganet->qty_DS / $isipcs_ds) : 0);
            $exclude_SP = (($harganet->qty_SP!=0) ? ($bruto_SP - $peny_SP - $retur->retur_SP) / ($harganet->qty_SP / $isipcs_sp) : 0);
            $exclude_SC = (($harganet->qty_SC!=0) ? ($bruto_SC - $peny_SC - $retur->retur_SC) / ($harganet->qty_SC / $isipcs_sc) : 0);
            $exclude_SP8 =(($harganet->qty_SP8!=0) ? ($bruto_SP8 - $peny_SP8 - $retur->retur_SP8) / ($harganet->qty_SP8 / $isipcs_sp8) : 0);

            $exclude_SP500 =(($harganet->qty_SP500!=0) ? ($bruto_SP500 - $peny_SP500 - $retur->retur_SP500) / ($harganet->qty_SP500 / $isipcs_sp500) : 0);
            ?>
            <td>HARGA NET (EXLUDE DISKON)</td>
            <td align="right"><?php echo number_format($exclude_AB, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_AR, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_AS, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_BB, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_BBP, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_CG, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_CGG, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_CG5, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_DEP, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_DS, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_SP, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_SC, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_SP8, '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_SP500, '0', '', '.'); ?></td>
        </tr>
        <tr style="font-size:14px; background-color:black; color:white">

            <td>HARGA NET(INCLUDE DISKON)</td>
            <td align="right"><?php echo number_format( $exclude_AB - (($harganet->qty_AB!=0) ? ($harganet->diskon_AB / ($harganet->qty_AB / $isipcs_ab)) : 0)  , '0', '', '.' ); ?></td>
            <td align="right"><?php echo number_format($exclude_AR -  (($harganet->qty_AR!=0) ? ($harganet->diskon_AR / ($harganet->qty_AR / $isipcs_ar))  : 0), '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_AS -  (($harganet->qty_AS!=0) ? ($harganet->diskon_AS / ($harganet->qty_AS / $isipcs_as)) : 0), '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_BB -  (($harganet->qty_BB!=0) ? ($harganet->diskon_BB / ($harganet->qty_BB / $isipcs_bb)) : 0), '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_BBP -  (($harganet->qty_BBP!=0) ? ($harganet->diskon_BBP / ($harganet->qty_BBP / $isipcs_bbp)) : 0), '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_CG -  (($harganet->qty_CG!=0) ? ($harganet->diskon_CG / ($harganet->qty_CG / $isipcs_cg)) : 0), '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_CGG -  (($harganet->qty_CGG!=0) ? ($harganet->diskon_CGG / ($harganet->qty_CGG / $isipcs_cgg)) : 0), '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_CG5 -  (($harganet->qty_CG5!=0) ? ($harganet->diskon_CG5 / ($harganet->qty_CG5 / $isipcs_cg5)) : 0), '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_DEP -  (($harganet->qty_DEP!=0) ? ($harganet->diskon_DEP / ($harganet->qty_DEP / $isipcs_dep)) : 0), '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_DS -  (($harganet->qty_DS!=0) ? ($harganet->diskon_DS / ($harganet->qty_DS / $isipcs_ds)) : 0), '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_SP -  (($harganet->qty_SP!=0) ? ($harganet->diskon_SP / ($harganet->qty_SP / $isipcs_sp)) : 0), '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_SC -  (($harganet->qty_SC!=0) ? ($harganet->diskon_SC / ($harganet->qty_SC / $isipcs_sc)) : 0), '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_SP8 -  (($harganet->qty_SP8!=0) ? ($harganet->diskon_SP8 / ($harganet->qty_SP8 / $isipcs_sp8)) : 0), '0', '', '.'); ?></td>
            <td align="right"><?php echo number_format($exclude_SP500 -  (($harganet->qty_SP500!=0) ? ($harganet->diskon_SP500 / ($harganet->qty_SP500 / $isipcs_sp500)) : 0), '0', '', '.'); ?></td>
        </tr>
    </table>
</body>
</html>
