<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Rekap Penjualan</title>
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
        REKAP PENJUALAN<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        @if ($salesman != null)
        SALESMAN {{ strtoupper($salesman->nama_karyawan) }}
        @else
        SEMUA SALESMAN
        @endif
        <br />
    </b>
    <table class="datatable3" style="width:200%">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td rowspan="2" class="fixed-side" scope="col" style=" background-color:#024a75;color:white;">No</td>
                <td rowspan="2" class="fixed-side" scope="col" style=" background-color:#024a75;color:white;">Nama Sales</td>
                <td colspan="15" align="center">PRODUK</td>
                <td rowspan="2" bgcolor="#f5ae15">Penjualan Bruto</td>
                <td rowspan="2" bgcolor="#f5ae15">Retur</td>
                <td rowspan="2" bgcolor="#f5ae15">Potongan</td>
                <td rowspan="2" bgcolor="#f5ae15">Potongan Istimewa</td>
                <td rowspan="2" bgcolor="#f5ae15">Penyesuaian Harga</td>
                <td rowspan="2" bgcolor="#1bbb32">Netto</td>
                <td rowspan="2" bgcolor="#1bbb32">Penerimaan rupiah</td>
                <td colspan="6" bgcolor="#1bbb32">Voucher</td>
                <td rowspan="2" bgcolor="#1bbb32">Saldo Awal Piutang</td>
                <td rowspan="2" bgcolor="#1bbb32">Saldo Akhir Piutang</td>
            </tr>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td>AIDA BESAR 500 GR</td>
                <td>AIDA KECIL SACHET</td>
                <td>AIDA BESAR 250 GR</td>
                <td>SAOS BAWANG BALL</td>
                <td>SAOS BAWANG BALL PROMO</td>
                <td>CABE GILING KG</td>
                <td>CABE GILING MURAH</td>
                <td>CABE GILING 5</td>
                <td>SAOS BAWANG DUS</td>
                <td>SAUS EXTRA PEDAS</td>
                <td>KECAP DUS</td>
                <td>SAUS STICK</td>
                <td>SAUS PREMIUM</td>
                <td>SAMBAL CABE 200</td>
                <td>SAUS STICK PREMIUM</td>
                <td style="background-color:#cc2727">PP</td>
                <td style="background-color:#cc2727">DP</td>
                <td style="background-color:#cc2727">PPS</td>
                <td style="background-color:#cc2727">PPHK</td>
                <td style="background-color:#cc2727">SP</td>
                <td style="background-color:#cc2727">L</td>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $totalAB 					= 0;
            $totalAR 					= 0;
            $totalASE 				= 0;
            $totalBB 					= 0;
            $totalBBP 					= 0;
            $totalCG 					= 0;
            $totalCGG 				= 0;
            $totalCG5 				= 0;
            $totalDB 					= 0;
            $totalDEP 				= 0;
            $totalDK 					= 0;
            $totalDS 					= 0;
            $totalSP 					= 0;
            $totalSP8 					= 0;
            $totalSC 					= 0;
            $subtotalbruto 		= 0;
            $totalpotongan 		= 0;
            $totalpotistimewa	= 0;
            $totalpenyharga 	= 0;
            $totalnetto 			= 0;
            $totalbayar				= 0;
            $totalpenghapusanpiutang = 0;
            $totaldiskonprogram = 0;
            $totalpps = 0;
            $totalpphk = 0;
            $totalvsp = 0;
            $totallainnya = 0;
            $totalsapiutang 	= 0;
            $totalslpiutang 	= 0;
            $totalretur = 0;
            $grandtotalAB 					= 0;
            $grandtotalAR 					= 0;
            $grandtotalASE 					= 0;
            $grandtotalBB 					= 0;
            $grandtotalBBP 					= 0;
            $grandtotalCG 					= 0;
            $grandtotalCGG 					= 0;
            $grandtotalCG5 					= 0;
            $grandtotalDB 					= 0;
            $grandtotalDEP 					= 0;
            $grandtotalDK 					= 0;
            $grandtotalDS 					= 0;
            $grandtotalSP 					= 0;
            $grandtotalSP8 					= 0;
            $grandtotalSC 					= 0;
            $grandsubtotalbruto 		= 0;
            $grandtotalpotongan 		= 0;
            $grandtotalpotistimewa	= 0;
            $grandtotalpenyharga 		= 0;
            $grandtotalnetto 				= 0;
            $grndtotalsapiutang 		= 0;
            $grandtotalslpiutang 		= 0;
            $grandtotalretur 		= 0;
            $grandtotalsapiutang  =0;
            $grandtotalbayar = 0;
            $grandtotalpenghapusanpiutang  = 0;
            $grandtotaldiskonprogram  = 0;
            $grandtotalpps  = 0;
            $grandtotalpphk  = 0;
            $grandtotalvsp = 0;
            $grandtotallainnya  = 0;
            foreach ($rekap as $key => $p) {

                $rek  = @$rekap[$key + 1]->kode_cabang;

                $totalAB 								= $totalAB + $p->AB;
                $totalAR 								= $totalAR + $p->AR;
                $totalASE 							= $totalASE + $p->ASE;
                $totalBB 								= $totalBB + $p->BB;
                $totalBBP 								= $totalBBP + $p->BBP;
                $totalCG 								= $totalCG + $p->CG;
                $totalCGG								= $totalCGG + $p->CGG;
                $totalCG5								= $totalCG5 + $p->CG5;
                $totalDB 								= $totalDB + $p->DB;
                $totalDEP 							= $totalDEP + $p->DEP;
                $totalDK 								= $totalDK + $p->DK;
                $totalDS 								= $totalDS + $p->DS;
                $totalSP 								= $totalSP + $p->SP;
                $totalSC 								= $totalSC + $p->SC;
                $totalSP8 								= $totalSP8 + $p->SP8;
                $subtotalbruto					= $subtotalbruto + $p->totalbruto;
                $totalpotongan  				= $totalpotongan + $p->totalpotongan;
                $totalretur 						= $totalretur + $p->totalretur;
                $totalpotistimewa 			= $totalpotistimewa + $p->totalpotistimewa;
                $totalpenyharga 				= $totalpenyharga + $p->totalpenyharga;
                $netto 									= $p->totalbruto - $p->totalpotongan - $p->totalretur - $p->totalpotistimewa - $p->totalpenyharga;
                $totalnetto  						= $totalnetto + $p->totalbruto - $p->totalpotongan - $p->totalretur - $p->totalpotistimewa - $p->totalpenyharga;
                $totalbayar 						= $totalbayar + $p->totalbayar;
                $totalpenghapusanpiutang = $totalpenghapusanpiutang += $p->penghapusanpiutang;
                $totaldiskonprogram = $totaldiskonprogram += $p->diskonprogram;
                $totalpps = $totalpps += $p->pps;
                $totalpphk = $totalpphk += $p->pphk;
                $totalvsp = $totalvsp += $p->vsp;
                $totallainnya = $totallainnya += $p->lainnya;
                $totalsapiutang 				= $totalsapiutang + $p->saldoawalpiutang;
                $totalslpiutang 				= $totalslpiutang + $p->saldoakhirpiutang;
                $grandtotalAB 					= $grandtotalAB + $p->AB;
                $grandtotalAR 					= $grandtotalAR + $p->AR;
                $grandtotalASE 					= $grandtotalASE + $p->ASE;
                $grandtotalBB 					= $grandtotalBB + $p->BB;
                $grandtotalBBP 					= $grandtotalBBP + $p->BBP;
                $grandtotalCG 					= $grandtotalCG + $p->CG;
                $grandtotalCGG					= $grandtotalCGG + $p->CGG;
                $grandtotalCG5					= $grandtotalCG5 + $p->CG5;
                $grandtotalDB 					= $grandtotalDB + $p->DB;
                $grandtotalDEP 					= $grandtotalDEP + $p->DEP;
                $grandtotalDK 					= $grandtotalDK + $p->DK;
                $grandtotalDS 					= $grandtotalDS + $p->DS;
                $grandtotalSP 					= $grandtotalSP + $p->SP;
                $grandtotalSC 					= $grandtotalSC + $p->SC;
                $grandtotalSP8 					= $grandtotalSP8 + $p->SP8;
                $grandsubtotalbruto			= $grandsubtotalbruto + $p->totalbruto;
                $grandtotalpotongan  		= $grandtotalpotongan + $p->totalpotongan;
                $grandtotalretur 				= $grandtotalretur + $p->totalretur;
                $grandtotalpotistimewa 	= $grandtotalpotistimewa + $p->totalpotistimewa;
                $grandtotalpenyharga 		= $grandtotalpenyharga + $p->totalpenyharga;
                $grandtotalnetto  			= $grandtotalnetto + $p->totalbruto - $p->totalpotongan - $p->totalretur - $p->totalpotistimewa - $p->totalpenyharga;
                $grandtotalsapiutang 		= $grandtotalsapiutang + $p->saldoawalpiutang;
                $grandtotalslpiutang 		= $grandtotalslpiutang + $p->saldoakhirpiutang;
                $grandtotalbayar 				= $grandtotalbayar + $p->totalbayar;
                $gradtotalpenghapusanpiutang = $grandtotalpenghapusanpiutang += $p->penghapusanpiutang;
                $grandtotaldiskonprogram = $grandtotaldiskonprogram += $p->diskonprogram;
                $grandtotalpps = $grandtotalpps += $p->pps;
                $grandtotalpphk = $grandtotalpphk += $p->pphk;
                $grandtotalvsp = $grandtotalvsp += $p->vsp;
                $grandtotallainnya = $grandtotallainnya += $p->lainnya;


            ?>
            <tr>
                <td class="fixed-side" scope="col"><?php echo $no; ?></td>
                <td class="fixed-side" scope="col"><?php echo $p->nama_karyawan; ?></td>
                <td style="text-align:right; font-weight:bold">
                    <?php if (!empty($p->AB)) {echo rupiah($p->AB);} ?>
                </td>
                <td style="text-align:right; font-weight:bold">
                    <?php if (!empty($p->AR)) { echo rupiah($p->AR);} ?>
                </td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->ASE)) {
                                                                                                                        echo rupiah($p->ASE);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->BB)) {
                                                                                                                        echo rupiah($p->BB);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->BBP)) {
                                                                                                                        echo rupiah($p->BBP);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->CG)) {
                                                                                                                        echo rupiah($p->CG);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->CGG)) {
                                                                                                                        echo rupiah($p->CGG);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->CG5)) {
                                                                                            echo rupiah($p->CG5);
                                                                                        } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->DB)) {
                                                                                                                        echo rupiah($p->DB);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->DEP)) {
                                                                                                                        echo rupiah($p->DEP);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->DK)) {
                                                                                                                        echo rupiah($p->DK);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->DS)) {
                                                                                                                        echo rupiah($p->DS);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->SP)) {
                                                                                                                        echo rupiah($p->SP);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->SC)) {
                                                                                                                        echo rupiah($p->SC);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->SP8)) {
                                                                                                                        echo rupiah($p->SP8);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->totalbruto)) {
                                                                                                                        echo rupiah($p->totalbruto);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->totalretur)) {
                                                                                                                        echo rupiah($p->totalretur);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->totalpotongan)) {
                                                                                                                        echo rupiah($p->totalpotongan);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->totalpotistimewa)) {
                                                                                                                        echo rupiah($p->totalpotistimewa);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->totalpenyharga)) {
                                                                                                                        echo rupiah($p->totalpenyharga);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($netto)) {
                                                                                                                        echo rupiah($netto);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->totalbayar)) {
                                                                                                                        echo rupiah($p->totalbayar);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->penghapusanpiutang)) {
                                                                                                                        echo rupiah($p->penghapusanpiutang);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->diskonprogram)) {
                                                                                                                        echo rupiah($p->diskonprogram);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->pps)) {
                                                                                                                        echo rupiah($p->pps);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->pphk)) {
                                                                                                                        echo rupiah($p->pphk);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->vsp)) {
                                                                                                                        echo rupiah($p->vsp);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->lainnya)) {
                                                                                                                        echo rupiah($p->lainnya);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->saldoawalpiutang)) {
                                                                                                                        echo rupiah($p->saldoawalpiutang);
                                                                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->saldoakhirpiutang)) {
                                                                                                                        echo rupiah($p->saldoakhirpiutang);
                                                                                                                    } ?></td>

            </tr>


            <?php
                if ($rek != $p->kode_cabang) {
                    echo '
        <tr bgcolor="#024a75" style="color:white; font-weight:bold">
        <td colspan="2" class="fixed-side" scope="col" style=" background-color:#024a75;color:white;">TOTAL ' . $p->kode_cabang . '</td>
            <td align="right" >' . rupiah($totalAB) . '</td>
            <td align="right" >' . rupiah($totalAR) . '</td>
            <td align="right" >' . rupiah($totalASE) . '</td>
            <td align="right" >' . rupiah($totalBB) . '</td>
            <td align="right" >' . rupiah($totalBBP) . '</td>
            <td align="right" >' . rupiah($totalCG) . '</td>
            <td align="right" >' . rupiah($totalCGG) . '</td>
            <td align="right" >' . rupiah($totalCG5) . '</td>
            <td align="right" >' . rupiah($totalDB) . '</td>
            <td align="right" >' . rupiah($totalDEP) . '</td>
            <td align="right" >' . rupiah($totalDK) . '</td>
            <td align="right" >' . rupiah($totalDS) . '</td>
            <td align="right" >' . rupiah($totalSP) . '</td>
            <td align="right" >' . rupiah($totalSC) . '</td>
            <td align="right" >' . rupiah($totalSP8) . '</td>
            <td align="right" >' . rupiah($subtotalbruto) . '</td>
            <td align="right" >' . rupiah($totalretur) . '</td>
            <td align="right" >' . rupiah($totalpotongan) . '</td>
            <td align="right" >' . rupiah($totalpotistimewa) . '</td>
            <td align="right" >' . rupiah($totalpenyharga) . '</td>
            <td align="right" >' . rupiah($totalnetto) . '</td>
            <td align="right" >' . rupiah($totalbayar) . '</td>
            <td align="right" >' . rupiah($totalpenghapusanpiutang) . '</td>
            <td align="right" >' . rupiah($totaldiskonprogram) . '</td>
            <td align="right" >' . rupiah($totalpps) . '</td>
            <td align="right" >' . rupiah($totalpphk) . '</td>
            <td align="right" >' . rupiah($totalvsp) . '</td>
            <td align="right" >' . rupiah($totallainnya) . '</td>
            <td align="right" >' . rupiah($totalsapiutang) . '</td>
            <td align="right" >' . rupiah($totalslpiutang) . '</td>
        </tr>';
                    $totalAB 						= 0;
                    $totalAR 						= 0;
                    $totalASE 					= 0;
                    $totalBB 						= 0;
                    $totalBBP 						= 0;
                    $totalCG 						= 0;
                    $totalCGG 					= 0;
                    $totalCG5 					= 0;
                    $totalDB 						= 0;
                    $totalDEP 					= 0;
                    $totalDK 						= 0;
                    $totalDS 						= 0;
                    $totalSP 						= 0;
                    $totalSC 						= 0;
                    $totalSP8 						= 0;
                    $subtotalbruto 			= 0;
                    $totalretur 				= 0;
                    $totalpotongan 			= 0;
                    $totalpotistimewa 	= 0;
                    $totalpenyharga 		= 0;
                    $totalnetto 				= 0;
                    $totalbayar 				= 0;
                    $totalpenghapusanpiutang = 0;
                    $totaldiskonprogram = 0;
                    $totalpps = 0;
                    $totalpphk = 0;
                    $totalvsp = 0;
                    $totallainnya = 0;
                    $totalsapiutang			= 0;
                    $totalslpiutang 		= 0;
                }
                $rek  = $p->kode_cabang;
                $no++;
            } ?>
        </tbody>
        <tfoot>
            <?php
            echo '
        <tr bgcolor="#024a75" style="color:white; font-weight:bold">
            <td colspan="2" class="fixed-side" scope="col" style=" background-color:#024a75;color:white;">TOTAL </td>
            <td align="right" >' . rupiah($grandtotalAB) . '</td>
            <td align="right" >' . rupiah($grandtotalAR) . '</td>
            <td align="right" >' . rupiah($grandtotalASE) . '</td>
            <td align="right" >' . rupiah($grandtotalBB) . '</td>
            <td align="right" >' . rupiah($grandtotalBBP) . '</td>
            <td align="right" >' . rupiah($grandtotalCG) . '</td>
            <td align="right" >' . rupiah($grandtotalCGG) . '</td>
            <td align="right" >' . rupiah($grandtotalCG5) . '</td>
            <td align="right" >' . rupiah($grandtotalDB) . '</td>
            <td align="right" >' . rupiah($grandtotalDEP) . '</td>
            <td align="right" >' . rupiah($grandtotalDK) . '</td>
            <td align="right" >' . rupiah($grandtotalDS) . '</td>
            <td align="right" >' . rupiah($grandtotalSP) . '</td>
            <td align="right" >' . rupiah($grandtotalSC) . '</td>
            <td align="right" >' . rupiah($grandtotalSP8) . '</td>
            <td align="right" >' . rupiah($grandsubtotalbruto) . '</td>
            <td align="right" >' . rupiah($grandtotalretur) . '</td>
            <td align="right" >' . rupiah($grandtotalpotongan) . '</td>
            <td align="right" >' . rupiah($grandtotalpotistimewa) . '</td>
            <td align="right" >' . rupiah($grandtotalpenyharga) . '</td>
            <td align="right" >' . rupiah($grandtotalnetto) . '</td>
            <td align="right" >' . rupiah($grandtotalbayar) . '</td>
            <td align="right" >' . rupiah($grandtotalpenghapusanpiutang) . '</td>
            <td align="right" >' . rupiah($grandtotaldiskonprogram) . '</td>
            <td align="right" >' . rupiah($grandtotalpps) . '</td>
            <td align="right" >' . rupiah($grandtotalpphk) . '</td>
            <td align="right" >' . rupiah($grandtotalvsp) . '</td>
            <td align="right" >' . rupiah($grandtotallainnya) . '</td>
            <td align="right" >' . rupiah($grandtotalsapiutang) . '</td>
            <td align="right" >' . rupiah($grandtotalslpiutang) . '</td>
        </tr>';
            ?>
        </tfoot>
    </table>
    <br>
    <table class="datatable3" style="border:0px;">

        <?php
        $totalcabnetto 		= 0;
        $totalallcabnetto 	= 0;
        foreach ($rekap as $key => $r) {
            $cab  = @$rekap[$key + 1]->kode_cabang;
            $totalcabnetto 		= $totalcabnetto + $r->totalbruto - $r->totalpotongan - $r->totalretur - $r->totalpotistimewa - $r->totalpenyharga;
            $totalallcabnetto 	= $totalallcabnetto + $r->totalbruto - $r->totalpotongan - $r->totalretur - $r->totalpotistimewa - $r->totalpenyharga;;
            if ($cab != $r->kode_cabang) {
                echo '
                    <tr bgcolor="#024a75" style="color:white; font-weight:bold; border:0px">
                        <td colspan="2" style="width:200px;border:0px">' . $r->kode_cabang . '</td>
                        <td align="right" style="border:0px">' . rupiah($totalcabnetto) . '</td>
                    </tr>
                    ';
                $totalcabnetto = 0;
            }
            $cab  = $r->kode_cabang;
        }
        ?>
        <tr bgcolor="#024a75" style="color:white; font-weight:bold">
            <td style="border:0px"></td>
            <td style="border:0px">TOTAL</td>
            <td align="right" style="border:0px; border-top:1px">
                <u><?php echo rupiah($totalallcabnetto); ?></u>
            </td>
        </tr>
    </table>
    <br>
    <table class="datatable3">
        <thead>
            <th bgcolor="#719ef4" colspan="4">I. JURNAL PENJUALAN PUSAT DAN CABANG </th>
        </thead>
        <tbody>
            <tr>
                <td colspan="2" style="border:0px">PIUTANG DAGANG</td>
                <td style="border:0px" align="right"><b><?php echo rupiah($grandtotalnetto); ?></b></td>
                <td style="border:0px"></td>
            </tr>
            <tr>
                <td colspan="2" style="border:0px">POTONGAN HARGA</td>
                <td style="border:0px" align="right"><b><?php echo rupiah($grandtotalpotistimewa + $grandtotalpotongan); ?></b></td>
                <td style="border:0px"></td>
            </tr>
            <tr>
                <td colspan="2" style="border:0px">RETUR PENJUALAN</td>
                <td style="border:0px" align="right"><b><?php echo rupiah($grandtotalretur); ?></b></td>
                <td style="border:0px"></td>
            </tr>
            <tr>
                <td colspan="2" style="border:0px">PENYESUAIAN HARGA</td>
                <td style="border:0px" align="right"><b><?php echo rupiah($grandtotalpenyharga); ?></b></td>
                <td style="border:0px"></td>
            </tr>
            <tr>
                <td style="border:0px"></td>
                <td colspan="2" style="border:0px">PENJUALAN SAOS BAWANG BALL</td>
                <td style="border:0px" align="right"><b><?php echo rupiah($grandtotalBB); ?></b></td>
            </tr>
            <tr>
                <td style="border:0px"></td>
                <td colspan="2" style="border:0px">PENJUALAN SAOS BAWANG BALL PROMO</td>
                <td style="border:0px" align="right"><b><?php echo rupiah($grandtotalBBP); ?></b></td>
            </tr>
            <tr>
                <td style="border:0px"></td>
                <td style="border:0px" colspan="2" style="border:0px">PENJUALAN SAOS BAWANG DUS</td>
                <td style="border:0px" align="right"><b>-</b></td>
            </tr>
            <tr>
                <td style="border:0px"></td>
                <td style="border:0px" colspan="2" style="border:0px">PENJUALAN SAOS BAWANG EXTRA PEDAS</td>
                <td style="border:0px" align="right"><b><?php echo rupiah($grandtotalDEP); ?></b></td>

            </tr>
            <tr>
                <td style="border:0px"></td>
                <td colspan="2" style="border:0px">PENJUALAN SAOS BAWANG STICK</td>
                <td style="border:0px" align="right"><b><?php echo rupiah($grandtotalDS); ?></b></td>

            </tr>
            <tr>
                <td style="border:0px"></td>
                <td colspan="2" style="border:0px">PENJUALAN SAOS BAWANG SACHET</td>
                <td style="border:0px" align="right"><b>-</b></td>
            </tr>
            <tr>
                <td style="border:0px"></td>
                <td colspan="2" style="border:0px">PENJUALAN SAOS TOMAT DUS</td>
                <td style="border:0px" align="right"><b>-</b></td>
            </tr>

            <tr>
                <td style="border:0px"></td>
                <td colspan="2" style="border:0px">PENJUALAN AIDA RENTENG</td>
                <td style="border:0px" align="right"><b><?php echo rupiah($grandtotalAR); ?></b></td>
            </tr>
            <tr>
                <td style="border:0px"></td>
                <td colspan="2" style="border:0px">PENJUALAN AIDA BESAR 500 GR</td>
                <td style="border:0px" align="right"><b><?php echo rupiah($grandtotalAB); ?></b></td>
            </tr>
            <tr>
                <td style="border:0px"></td>
                <td colspan="2" style="border:0px">PENJUALAN AIDA 250 GR</td>
                <td style="border:0px" align="right"><b><?php echo rupiah($grandtotalASE); ?></b></td>
            </tr>
            <tr>
                <td style="border:0px"></td>
                <td colspan="2" style="border:0px">PENJUALAN CABE GILING MURAH</td>
                <td style="border:0px" align="right"><b><?php echo rupiah($grandtotalCG + $grandtotalCGG + $grandtotalCG5); ?></b></td>
            </tr>
            <tr>
                <td style="border:0px"></td>
                <td colspan="2" style="border:0px">PENJUALAN KECAP DUS</td>
                <td style="border:0px" align="right"><b><?php echo rupiah($grandtotalDK); ?></b></td>
            </tr>
            <tr>
                <td style="border:0px"></td>
                <td colspan="2" style="border:0px">PENJUALAN KECAP BOTOL</td>
                <td style="border:0px" align="right"><b>-</b></td>
            </tr>
            <tr>
                <td style="border:0px"></td>
                <td colspan="2" style="border:0px">PENJUALAN SAUS PREMIUM</td>
                <td style="border:0px" align="right"><b><?php echo rupiah($grandtotalSP); ?></b></td>

            </tr>
            <tr>
                <td style="border:0px"></td>
                <td colspan="2" style="border:0px">PENJUALAN SAMBAL CABE 200</td>
                <td style="border:0px" align="right"><b><?php echo rupiah($grandtotalSC); ?></b></td>

            </tr>
            <tr>
                <td style="border:0px"></td>
                <td colspan="2" style="border:0px">SAUS STICK PREMIUM</td>
                <td style="border:0px" align="right"><b><?php echo rupiah($grandtotalSP8); ?></b></td>

            </tr>
            <tr>
                <td style="border:0px"></td>
                <td colspan="2" style="border:0px"></td>
                <td style="border:0px" align="right"><b><?php echo rupiah($grandsubtotalbruto); ?></b></td>
            </tr>
        </tbody>
    </table>
    <br>
    <table class="datatable3">
        <thead>
            <th bgcolor="#719ef4" colspan="4">II. JURNAL PENERIMAAN rupiah DI CABANG</th>
        </thead>
        <tbody>
            <tr>
                <td colspan="4" style="border:0px"><b>BANDUNG</b></td>
            </tr>
            <tr>
                <td colspan="2" style="border:0px">PIUTANG BANDUNG</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
