<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Rekap Retur</title>
    <style>
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
    <table class="datatable3" style="width:150%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td rowspan="3">No</td>
                <td rowspan="3">Nama Sales</td>
                <td colspan="26">Produk</td>
                <td rowspan="3" bgcolor="#f5ae15">Total Retur</td>
                <td rowspan="3" bgcolor="#f5ae15">Penyeseuaian</td>
                <td rowspan="3" bgcolor="#f5ae15">Total Retur Netto</td>
            </tr>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td colspan="2">AIDA BESAR 500 GR</td>
                <td colspan="2">AIDA KECIL SACHET</td>
                <td colspan="2">AIDA BESAR 250 GR</td>
                <td colspan="2">SAOS BAWANG BALL</td>
                <td colspan="2">CABE GILING KG</td>
                <td colspan="2">CABE GILING MURAH</td>
                <td colspan="2">SAOS BAWANG DUS</td>
                <td colspan="2">SAUS EXTRA PEDAS</td>
                <td colspan="2">KECAP DUS</td>
                <td colspan="2">SAUS STICK</td>
                <td colspan="2">SAUS STICK PREMIUM</td>
                <td colspan="2">SAUS PREMIUM</td>
                <td colspan="2">SAUS PREMIUM PRORMO</td>
            </tr>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td>Qty</td>
                <td>Rp</td>
                <td>Qty</td>
                <td>Rp</td>
                <td>Qty</td>
                <td>Rp</td>
                <td>Qty</td>
                <td>Rp</td>
                <td>Qty</td>
                <td>Rp</td>
                <td>Qty</td>
                <td>Rp</td>
                <td>Qty</td>
                <td>Rp</td>
                <td>Qty</td>
                <td>Rp</td>
                <td>Qty</td>
                <td>Rp</td>
                <td>Qty</td>
                <td>Rp</td>
                <td>Qty</td>
                <td>Rp</td>
                <td>Qty</td>
                <td>Rp</td>
                <td>Qty</td>
                <td>Rp</td>
            </tr>
        </thead>
        <tbody>
            <?php
          $no           = 1;
          $qtytotalAB       = 0;
          $qtytotalAR       = 0;
          $qtytotalASE       = 0;
          $qtytotalBB       = 0;
          $qtytotalCG       = 0;
          $qtytotalCGG       = 0;
          $qtytotalDB       = 0;
          $qtytotalDEP       = 0;
          $qtytotalDK       = 0;
          $qtytotalDS       = 0;
          $qtytotalSP       = 0;
          $qtytotalSPP       = 0;
          $qtytotalSP8       = 0;

          $grandtytotalAB     = 0;
          $grandtytotalAR     = 0;
          $grandtytotalASE     = 0;
          $grandtytotalBB     = 0;
          $grandtytotalCG     = 0;
          $grandtytotalCGG     = 0;
          $grandtytotalDB     = 0;
          $grandtytotalDEP     = 0;
          $grandtytotalDK     = 0;
          $grandtytotalDS     = 0;
          $grandtytotalSP     = 0;
          $grandtytotalSPP     = 0;
          $grandtytotalSP8     = 0;



          $totalAB         = 0;
          $totalAR         = 0;
          $totalASE         = 0;
          $totalBB         = 0;
          $totalCG         = 0;
          $totalCGG         = 0;
          $totalDB         = 0;
          $totalDEP         = 0;
          $totalDK         = 0;
          $totalDS         = 0;
          $totalSP         = 0;
          $totalSPP        = 0;
          $totalSP8        = 0;

          $grandtotalAB       = 0;
          $grandtotalAR       = 0;
          $grandtotalASE       = 0;
          $grandtotalBB       = 0;
          $grandtotalCG       = 0;
          $grandtotalCGG       = 0;
          $grandtotalDB       = 0;
          $grandtotalDEP       = 0;
          $grandtotalDK       = 0;
          $grandtotalDS       = 0;
          $grandtotalSP       = 0;
          $grandtotalSPP       = 0;
          $grandtotalSP8       = 0;


          $totalretur       = 0;
          $totalpenyesuaian     = 0;
          $totalreturnetto     = 0;
          $grandtotalreturnetto   = 0;
          $grandtotalretur    = 0;
          $grandtotalpenyesuaian  = 0;
          $grandtotalallreturnetto = 0;

          foreach ($rekap as $key => $p) {

            $rek  = @$rekap[$key + 1]->kode_cabang;
            $qtytotalAB       = $qtytotalAB + $p->JML_AB;
            $qtytotalAR       = $qtytotalAR + $p->JML_AR;
            $qtytotalASE       = $qtytotalASE + $p->JML_ASE;
            $qtytotalBB       = $qtytotalBB + $p->JML_BB;
            $qtytotalCG       = $qtytotalCG + $p->JML_CG;
            $qtytotalCGG       = $qtytotalCGG + $p->JML_CGG;
            $qtytotalDB       = $qtytotalDB + $p->JML_DB;
            $qtytotalDEP       = $qtytotalDEP + $p->JML_DEP;
            $qtytotalDK       = $qtytotalDK + $p->JML_DK;
            $qtytotalDS       = $qtytotalDS + $p->JML_DS;
            $qtytotalSP       = $qtytotalSP + $p->JML_SP;
            $qtytotalSPP       = $qtytotalSPP + $p->JML_SPP;
            $qtytotalSP8       = $qtytotalSP8 + $p->JML_SP8;

            $grandtytotalAB     = $grandtytotalAB + $p->JML_AB;
            $grandtytotalAR     = $grandtytotalAR + $p->JML_AR;
            $grandtytotalASE     = $grandtytotalASE + $p->JML_ASE;
            $grandtytotalBB     = $grandtytotalBB + $p->JML_BB;
            $grandtytotalCG     = $grandtytotalCG + $p->JML_CG;
            $grandtytotalCGG     = $grandtytotalCGG + $p->JML_CGG;
            $grandtytotalDB     = $grandtytotalDB + $p->JML_DB;
            $grandtytotalDEP     = $grandtytotalDEP + $p->JML_DEP;
            $grandtytotalDK     = $grandtytotalDK + $p->JML_DK;
            $grandtytotalDS     = $grandtytotalDS + $p->JML_DS;
            $grandtytotalSP     = $grandtytotalSP + $p->JML_SP;
            $grandtytotalSPP     = $grandtytotalSPP + $p->JML_SPP;
            $grandtytotalSP8     = $grandtytotalSP8 + $p->JML_SP8;


            $totalAB         = $totalAB + $p->AB;
            $totalAR         = $totalAR + $p->AR;
            $totalASE         = $totalASE + $p->ASE;
            $totalBB         = $totalBB + $p->BB;
            $totalCG         = $totalCG + $p->CG;
            $totalCGG        = $totalCGG + $p->CGG;
            $totalDB         = $totalDB + $p->DB;
            $totalDEP         = $totalDEP + $p->DEP;
            $totalDK         = $totalDK + $p->DK;
            $totalDS         = $totalDS + $p->DS;
            $totalSP         = $totalSP + $p->SP;
            $totalSPP         = $totalSPP + $p->SPP;
            $totalSP8         = $totalSP8 + $p->SP8;

            $grandtotalAB       = $grandtotalAB + $p->AB;
            $grandtotalAR       = $grandtotalAR + $p->AR;
            $grandtotalASE       = $grandtotalASE + $p->ASE;
            $grandtotalBB       = $grandtotalBB + $p->BB;
            $grandtotalCG       = $grandtotalCG + $p->CG;
            $grandtotalCGG      = $grandtotalCGG + $p->CGG;
            $grandtotalDB       = $grandtotalDB + $p->DB;
            $grandtotalDEP       = $grandtotalDEP + $p->DEP;
            $grandtotalDK       = $grandtotalDK + $p->DK;
            $grandtotalDS       = $grandtotalDS + $p->DS;
            $grandtotalSP       = $grandtotalSP + $p->SP;
            $grandtotalSPP       = $grandtotalSPP + $p->SPP;
            $grandtotalSP8       = $grandtotalSP8 + $p->SP8;

            $totalretur        = $totalretur + $p->totalretur;
            $grandtotalretur     = $grandtotalretur + $p->totalretur;
            $totalpenyesuaian     = $totalpenyesuaian + $p->total_gb;
            $grandtotalpenyesuaian   = $grandtotalpenyesuaian + $p->total_gb;
            $totalreturnetto     = $p->totalretur - $p->total_gb;
            $grandtotalreturnetto   = $grandtotalreturnetto + $totalreturnetto;
            $grandtotalallreturnetto = $grandtotalallreturnetto + $totalreturnetto;
          ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td><?php echo $p->nama_karyawan; ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->JML_AB)) {echo desimal($p->JML_AB);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->AB)) {echo rupiah($p->AB);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->JML_AR)) {echo desimal($p->JML_AR);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->AR)) {echo rupiah($p->AR);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->JML_ASE)) {echo desimal($p->JML_ASE); } ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->ASE)) {echo rupiah($p->ASE);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->JML_BB)) {echo desimal($p->JML_BB);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->BB)) {echo rupiah($p->BB);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->JML_CG)) {echo desimal($p->JML_CG);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->CG)) { echo rupiah($p->CG);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->JML_CGG)) {echo desimal($p->JML_CGG);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->CGG)) {echo rupiah($p->CGG);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->JML_DB)) {echo desimal($p->JML_DB);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->DB)) {echo rupiah($p->DB);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->JML_DEP)) {
                                                                echo desimal($p->JML_DEP);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->DEP)) {
                                                                echo rupiah($p->DEP);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->JML_DK)) {
                                                                echo desimal($p->JML_DK);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->DK)) {
                                                                echo rupiah($p->DK);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->JML_DS)) {
                                                                echo desimal($p->JML_DS);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->DS)) {
                                                                echo rupiah($p->DS);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->JML_SP8)) {
                                                                echo desimal($p->JML_SP8);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->SP8)) {
                                                                echo rupiah($p->SP8);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->JML_SP)) {
                                                                echo desimal($p->JML_SP);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->SP)) {
                                                                echo rupiah($p->SP);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->JML_SPP)) {
                                                                echo desimal($p->JML_SPP);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->SPP)) {
                                                                echo rupiah($p->SPP);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->totalretur)) {
                                                                echo rupiah($p->totalretur);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->total_gb)) {
                                                                echo rupiah($p->total_gb);} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($totalreturnetto)) {
                                                                echo rupiah($totalreturnetto);} ?></td>
            </tr>
            <?php

            if ($rek != $p->kode_cabang) {
            echo '
            <tr bgcolor="#024a75" style="color:white; font-weight:bold">
            <td colspan="2" >TOTAL ' . $p->kode_cabang . '</td>
            <td align="right" >' . desimal($qtytotalAB) . '</td>
            <td align="right" >' . rupiah($totalAB) . '</td>
            <td align="right" >' . desimal($qtytotalAR) . '</td>
            <td align="right" >' . rupiah($totalAR) . '</td>
            <td align="right" >' . desimal($qtytotalASE) . '</td>
            <td align="right" >' . rupiah($totalASE) . '</td>
            <td align="right" >' . desimal($qtytotalBB) . '</td>
            <td align="right" >' . rupiah($totalBB) . '</td>
            <td align="right" >' . desimal($qtytotalCG) . '</td>
            <td align="right" >' . rupiah($totalCG) . '</td>
            <td align="right" >' . desimal($qtytotalCGG) . '</td>
            <td align="right" >' . rupiah($totalCGG) . '</td>
            <td align="right" >' . desimal($qtytotalDB) . '</td>
            <td align="right" >' . rupiah($totalDB) . '</td>
            <td align="right" >' . desimal($qtytotalDEP) . '</td>
            <td align="right" >' . rupiah($totalDEP) . '</td>
            <td align="right" >' . desimal($qtytotalDK) . '</td>
            <td align="right" >' . rupiah($totalDK) . '</td>
            <td align="right" >' . desimal($qtytotalDS) . '</td>
            <td align="right" >' . rupiah($totalDS) . '</td>
            <td align="right" >' . desimal($qtytotalSP8) . '</td>
            <td align="right" >' . rupiah($totalSP8) . '</td>
            <td align="right" >' . desimal($qtytotalSP) . '</td>
            <td align="right" >' . rupiah($totalSP) . '</td>
            <td align="right" >' . desimal($qtytotalSPP) . '</td>
            <td align="right" >' . rupiah($totalSPP) . '</td>
            <td align="right" >' . rupiah($totalretur) . '</td>
            <td align="right" >' . rupiah($totalpenyesuaian) . '</td>
            <td align="right" >' . rupiah($grandtotalreturnetto) . '</td>
         </tr>';
              $qtytotalAB       = 0;
              $qtytotalAR       = 0;
              $qtytotalASE       = 0;
              $qtytotalBB       = 0;
              $qtytotalCG       = 0;
              $qtytotalCGG       = 0;
              $qtytotalDB       = 0;
              $qtytotalDEP       = 0;
              $qtytotalDK       = 0;
              $qtytotalDS       = 0;
              $qtytotalDS       = 0;
              $qtytotalSP       = 0;
              $qtytotalSPP       = 0;
              $qtytotalSP8       = 0;


              $totalAB         = 0;
              $totalAR         = 0;
              $totalASE         = 0;
              $totalBB         = 0;
              $totalCG         = 0;
              $totalCGG         = 0;
              $totalDB         = 0;
              $totalDEP         = 0;
              $totalDK         = 0;
              $totalDS         = 0;
              $totalSP         = 0;
              $totalSPP         = 0;
              $totalSP8         = 0;

              $totalretur       = 0;
              $totalpenyesuaian     = 0;
              $grandtotalreturnetto   = 0;
            }
            $rek  = $p->kode_cabang;
            $no++;
          }
          ?>
        </tbody>
        <tfoot>
            <tr bgcolor="#ec8585" style="font-weight:bold">
                <td colspan="2">TOTAL RETUR</td>
                <td align="right"><?php echo desimal($grandtytotalAB); ?></td>
                <td align="right"><?php echo rupiah($grandtotalAB); ?></td>
                <td align="right"><?php echo desimal($grandtytotalAR); ?></td>
                <td align="right"><?php echo rupiah($grandtotalAR); ?></td>
                <td align="right"><?php echo desimal($grandtytotalASE); ?></td>
                <td align="right"><?php echo rupiah($grandtotalASE); ?></td>
                <td align="right"><?php echo desimal($grandtytotalBB); ?></td>
                <td align="right"><?php echo rupiah($grandtotalBB); ?></td>
                <td align="right"><?php echo desimal($grandtytotalCG); ?></td>
                <td align="right"><?php echo rupiah($grandtotalCG); ?></td>
                <td align="right"><?php echo desimal($grandtytotalCGG); ?></td>
                <td align="right"><?php echo rupiah($grandtotalCGG); ?></td>
                <td align="right"><?php echo desimal($grandtytotalDB); ?></td>
                <td align="right"><?php echo rupiah($grandtotalDB); ?></td>
                <td align="right"><?php echo desimal($grandtytotalDEP); ?></td>
                <td align="right"><?php echo rupiah($grandtotalDEP); ?></td>
                <td align="right"><?php echo desimal($grandtytotalDK); ?></td>
                <td align="right"><?php echo rupiah($grandtotalDK); ?></td>
                <td align="right"><?php echo desimal($grandtytotalDS); ?></td>
                <td align="right"><?php echo rupiah($grandtotalDS); ?></td>
                <td align="right"><?php echo desimal($grandtytotalSP8); ?></td>
                <td align="right"><?php echo rupiah($grandtotalSP8); ?></td>
                <td align="right"><?php echo desimal($grandtytotalSP); ?></td>
                <td align="right"><?php echo rupiah($grandtotalSP); ?></td>
                <td align="right"><?php echo desimal($grandtytotalSPP); ?></td>
                <td align="right"><?php echo rupiah($grandtotalSPP); ?></td>
                <td align="right"><?php echo rupiah($grandtotalretur); ?></td>
                <td align="right"><?php echo rupiah($grandtotalpenyesuaian); ?></td>
                <td align="right"><?php echo rupiah($grandtotalallreturnetto); ?></td>
            </tr>
        </tfoot>
    </table>
    <br>
    <?php
    $totalqtyretur  = $grandtytotalAB + $grandtytotalAR  + $grandtytotalASE  + $grandtytotalBB  + $grandtytotalCG + $grandtytotalCGG  + $grandtytotalDB + $grandtytotalDEP + $grandtytotalDK + $grandtytotalDS + $grandtytotalSP + $grandtytotalSPP + $grandtytotalSP8;
    $average     = $grandtotalpenyesuaian / $totalqtyretur;
    $avgAB       = $grandtytotalAB * $average;
    $avgAR       = $grandtytotalAR * $average;
    $avgASE     = $grandtytotalASE * $average;
    $avgBB       = $grandtytotalBB * $average;
    $avgCG       = $grandtytotalCG * $average;
    $avgCGG     = $grandtytotalCGG * $average;
    $avgDB       = $grandtytotalDB * $average;
    $avgDEP     = $grandtytotalDEP * $average;
    $avgDK       = $grandtytotalDK * $average;
    $avgDS       = $grandtytotalDS * $average;
    $avgSP       = $grandtytotalSP * $average;
    $avgSPP      = $grandtytotalSPP * $average;
    $avgSP8      = $grandtytotalSP8 * $average;
    ?>
    <table class="datatable3" style="width:120%">
        <thead>
            <tr bgcolor="#ec8585">
                <th style="text-align:right; font-weight:bold" colspan="2">PENYESUAIAN RETUR</th>
                <th>AIDA BESAR 500 GR</th>
                <th>AIDA KECIL SACHET</th>
                <th>AIDA BESAR 250 GR</th>
                <th>SAOS BAWANG BALL</th>
                <th>CABE GILING KG</th>
                <th>CABE GILING MURAH</th>
                <th>SAOS BAWANG DUS</th>
                <th>SAUS EXTRA PEDAS</th>
                <th>KECAP DUS</th>
                <th>SAUS STICK</th>
                <th>SAUS STICK PREMIUM</th>
                <th>SAUS PREMIUM</th>
                <th>SAUS PREMIUM PROMO</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>TOTAL QTY RETUR</td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($totalqtyretur)) {
                                                          echo desimal($totalqtyretur);
                                                        } ?></td>
                <td style="text-align:right; font-weight:bold" rowspan="3"><?php if (!empty($avgAB)) {
                                                                      echo  rupiah($avgAB);
                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold" rowspan="3"><?php if (!empty($avgAR)) {
                                                                      echo  rupiah($avgAR);
                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold" rowspan="3"><?php if (!empty($avgASE)) {
                                                                      echo rupiah($avgASE);
                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold" rowspan="3"><?php if (!empty($avgBB)) {
                                                                      echo  rupiah($avgBB);
                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold" rowspan="3"><?php if (!empty($avgCG)) {
                                                                      echo  rupiah($avgCG);
                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold" rowspan="3"><?php if (!empty($avgCGG)) {
                                                                      echo rupiah($avgCGG);
                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold" rowspan="3"><?php if (!empty($avgDB)) {
                                                                      echo  rupiah($avgDB);
                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold" rowspan="3"><?php if (!empty($avgDEP)) {
                                                                      echo rupiah($avgDEP);
                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold" rowspan="3"><?php if (!empty($avgDK)) {
                                                                      echo  rupiah($avgDK);
                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold" rowspan="3"><?php if (!empty($avgDS)) {
                                                                      echo  rupiah($avgDS);
                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold" rowspan="3"><?php if (!empty($avgSP8)) {
                                                                      echo  rupiah($avgSP8);
                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold" rowspan="3"><?php if (!empty($avgSP)) {
                                                                      echo  rupiah($avgSP);
                                                                    } ?></td>
                <td style="text-align:right; font-weight:bold" rowspan="3"><?php if (!empty($avgSPP)) {
                                                                      echo  rupiah($avgSPP);
                                                                    } ?></td>

            </tr>
            <tr>
                <td>PENYESUAIAN HARGA RETUR</td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($grandtotalpenyesuaian)) {
                                                          echo desimal($grandtotalpenyesuaian);
                                                        } ?></td>
            </tr>
            <tr>
                <td>AVERAGE</td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($average)) {
                                                          echo rupiah($average);
                                                        } ?></td>

            </tr>
        </tbody>

    </table>
</body>
</html>
