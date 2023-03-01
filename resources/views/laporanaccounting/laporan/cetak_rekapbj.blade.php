<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Rekap Persediaan Barang Jadi {{ date("d-m-y") }}</title>
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
        REKAPITULASI PERSEDIAAN BARANG JADI<br>
        PERIODE {{ DateToIndo2($tgl1) }} s/d {{ DateToIndo2($tgl2) }}
        <br>
    </b>
    <br>
    <div id="table-scroll" class="table-scroll">
        <div class="table-wrap">
            <table class="datatable3" style="width:200%" margin-bottom: 30px" border="1">
                <thead bgcolor="#024a75" style="color:white; font-size:18px">
                    <tr>
                        <th rowspan="3" class="fixed-side" scope="col" style="background-color:#024a75 ;">NO</th>
                        <th rowspan="3" class="fixed-side" scope="col" style="background-color:#024a75 ;">PRODUK</th>
                        <th colspan="45" bgcolor="#024a75">CABANG</th>
                    </tr>
                    <tr style="background-color: #03b058;">
                        <th colspan="3">TASIKMALAYA</th>
                        <th colspan="3">BANDUNG</th>
                        <th colspan="3">SUKABUMI</th>
                        <th colspan="3">BOGOR</th>
                        <th colspan="3">TEGAL</th>
                        <th colspan="3">PURWOKETO</th>
                        <th colspan="3">PUSAT</th>
                        <th colspan="3">SURABAYA</th>
                        <th colspan="3">SEMARANG</th>
                        <th colspan="3">KLATEN</th>
                        <th colspan="3">GARUT</th>
                        <th colspan="3">PWK</th>
                        <th colspan="3">BTN</th>
                        <th colspan="3">GD PUSAT</th>
                        <th colspan="3">JUMLAH</th>
                    </tr>
                    <tr style="background-color: #03b058;">
                        <th>QTY</th>
                        <th>HARGA</th>
                        <th>JUMLAH</th>
                        <th>QTY</th>
                        <th>HARGA</th>
                        <th>JUMLAH</th>
                        <th>QTY</th>
                        <th>HARGA</th>
                        <th>JUMLAH</th>
                        <th>QTY</th>
                        <th>HARGA</th>
                        <th>JUMLAH</th>
                        <th>QTY</th>
                        <th>HARGA</th>
                        <th>JUMLAH</th>
                        <th>QTY</th>
                        <th>HARGA</th>
                        <th>JUMLAH</th>
                        <th>QTY</th>
                        <th>HARGA</th>
                        <th>JUMLAH</th>
                        <th>QTY</th>
                        <th>HARGA</th>
                        <th>JUMLAH</th>
                        <th>QTY</th>
                        <th>HARGA</th>
                        <th>JUMLAH</th>
                        <th>QTY</th>
                        <th>HARGA</th>
                        <th>JUMLAH</th>
                        <th>QTY</th>
                        <th>HARGA</th>
                        <th>JUMLAH</th>
                        <th>QTY</th>
                        <th>HARGA</th>
                        <th>JUMLAH</th>
                        <th>QTY</th>
                        <th>HARGA</th>
                        <th>JUMLAH</th>
                        <th>QTY</th>
                        <th>HARGA</th>
                        <th>JUMLAH</th>
                        <th>QTY</th>
                        <th>HARGA</th>
                        <th>JUMLAH</th>
                    </tr>
                </thead>
                <tbody style="font-size:14px;">
                    <?php $no = 1;
              $totaltsm = 0;
              $totalbdg = 0;
              $totalskb = 0;
              $totalbgr = 0;
              $totaltgl = 0;
              $totalpwt = 0;
              $totalsby = 0;
              $totalsmr = 0;
              $totalklt = 0;
              $totalgrt = 0;
              $totalpwk = 0;
              $totalbtn = 0;
              $totalpst = 0;
              $totalgdpst = 0;
              $grandtotaljml = 0;
              foreach ($rekaphpp as $d) {
                $qtytsm = ($d->sa_tsm + $d->mutasi_tsm) / $d->isipcsdus;
                $qtybdg = ($d->sa_bdg + $d->mutasi_bdg) / $d->isipcsdus;
                $qtybgr = ($d->sa_bgr + $d->mutasi_bgr) / $d->isipcsdus;
                $qtyskb = ($d->sa_skb + $d->mutasi_skb) / $d->isipcsdus;
                $qtytgl = ($d->sa_tgl + $d->mutasi_tgl) / $d->isipcsdus;
                $qtypwt = ($d->sa_pwt + $d->mutasi_pwt) / $d->isipcsdus;
                $qtysby = ($d->sa_sby + $d->mutasi_sby) / $d->isipcsdus;
                $qtysmr = ($d->sa_smr + $d->mutasi_smr) / $d->isipcsdus;
                $qtyklt = ($d->sa_klt + $d->mutasi_klt) / $d->isipcsdus;
                $qtypst = ($d->sa_pst + $d->mutasi_pst) / $d->isipcsdus;
                $qtygrt = ($d->sa_grt + $d->mutasi_grt) / $d->isipcsdus;
                $qtypwk = ($d->sa_pwk + $d->mutasi_pwk) / $d->isipcsdus;
                $qtybtn = ($d->sa_btn + $d->mutasi_btn) / $d->isipcsdus;
                $harga = ROUND($d->harga_hpp);

                $jmltsm = ROUND(ROUND($qtytsm, 2) * ROUND($d->harga_tsm));
                $jmlbdg = ROUND(ROUND($qtybdg, 2) * ROUND($d->harga_bdg));
                $jmlskb = ROUND(ROUND($qtyskb, 2) * ROUND($d->harga_skb));
                $jmlbgr = ROUND(ROUND($qtybgr, 2) * ROUND($d->harga_bgr));
                $jmltgl = ROUND(ROUND($qtytgl, 2) * ROUND($d->harga_tgl));
                $jmlpwt = ROUND(ROUND($qtypwt, 2) * ROUND($d->harga_pwt));
                $jmlsby = ROUND(ROUND($qtysby, 2) * ROUND($d->harga_sby));
                $jmlsmr = ROUND(ROUND($qtysmr, 2) * ROUND($d->harga_smr));
                $jmlklt = ROUND(ROUND($qtyklt, 2) * ROUND($d->harga_klt));
                $jmlpst = ROUND(ROUND($qtypst, 2) * ROUND($d->harga_pst));
                $jmlgrt = ROUND(ROUND($qtygrt, 2) * ROUND($d->harga_grt));
                $jmlpwk = ROUND(ROUND($qtypwk, 2) * ROUND($d->harga_pwk));
                $jmlbtn = ROUND(ROUND($qtybtn, 2) * ROUND($d->harga_btn));

                $sa_gdpusat = $d->saldoawal_gd + ($d->jmlfsthp_gd + $d->jmlrepack_gd + $d->jmllainlain_in_gd) - ($d->jmlsuratjalan_gd + $d->jmlreject_gd + $d->jmllainlain_out_gd);
                $jmlgdpst = $sa_gdpusat * $d->harga_kirim_cabang;


                $totalqty = ROUND($qtytsm) + ROUND($qtybdg, 2) + ROUND($qtyskb, 2) + ROUND($qtybgr, 2) + ROUND($qtytgl, 2) + ROUND($qtypwt, 2) + ROUND($qtysby, 2) + ROUND($qtysmr, 2) + ROUND($qtyklt, 2) + ROUND($qtypst, 2)
                  + ROUND($qtygrt, 2) + ROUND($qtypwk, 2)  + ROUND($qtybtn, 2) + $sa_gdpusat;

                $totaljml = $jmltsm + $jmlbdg + $jmlskb + $jmlbgr + $jmltgl + $jmlpwt + $jmlsby + $jmlsmr + $jmlklt + $jmlpst + $jmlgdpst + $jmlgrt + $jmlpwk + $jmlbtn;
                if ($totalqty != 0) {
                  $hargatotal = $totaljml / $totalqty;
                } else {
                  $hargatotal = 0;
                }


                $totaltsm += $jmltsm;
                $totalbdg += $jmlbdg;
                $totalskb += $jmlskb;
                $totalbgr += $jmlbgr;
                $totaltgl += $jmltgl;
                $totalpwt += $jmlpwt;
                $totalsby += $jmlsby;
                $totalsmr += $jmlsmr;
                $totalklt += $jmlklt;
                $totalpst += $jmlpst;
                $totalgrt += $jmlgrt;
                $totalpwk += $jmlpwk;
                $totalbtn += $jmlbtn;
                $totalgdpst += $jmlgdpst;
                $grandtotaljml += $totaljml;
              ?>
                    <tr>
                        <td class="fixed-side" scope="col"><?php echo $no;; ?></td>
                        <td class="fixed-side" scope="col"><?php echo $d->nama_barang; ?></td>
                        <td align="right"><?php echo number_format($qtytsm, '2', ',', '.'); ?></td>
                        <td align="right">
                            <?php if (!empty($d->harga_tsm)) {
                      echo number_format($d->harga_tsm, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right">
                            <?php if (!empty($jmltsm)) {
                      echo number_format($jmltsm, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right"><?php echo number_format($qtybdg, '2', ',', '.'); ?></td>
                        <td align="right">
                            <?php if (!empty($d->harga_bdg)) {
                      echo number_format($d->harga_bdg, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right">
                            <?php if (!empty($jmlbdg)) {
                      echo number_format($jmlbdg, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right"><?php echo number_format($qtyskb, '2', ',', '.'); ?></td>
                        <td align="right">
                            <?php if (!empty($d->harga_skb)) {
                      echo number_format($d->harga_skb, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right">
                            <?php if (!empty($jmlskb)) {
                      echo number_format($jmlskb, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right"><?php echo number_format($qtybgr, '2', ',', '.'); ?></td>
                        <td align="right">
                            <?php if (!empty($d->harga_bgr)) {
                      echo number_format($d->harga_bgr, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right">
                            <?php if (!empty($jmlbgr)) {
                      echo number_format($jmlbgr, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right"><?php echo number_format($qtytgl, '2', ',', '.'); ?></td>
                        <td align="right">
                            <?php if (!empty($d->harga_tgl)) {
                      echo number_format($d->harga_tgl, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right">
                            <?php if (!empty($jmltgl)) {
                      echo number_format($jmltgl, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right"><?php echo number_format($qtypwt, '2', ',', '.'); ?></td>
                        <td align="right">
                            <?php if (!empty($d->harga_pwt)) {
                      echo number_format($d->harga_pwt, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right">
                            <?php if (!empty($jmlpwt)) {
                      echo number_format($jmlpwt, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right"><?php echo number_format($qtypst, '2', ',', '.'); ?></td>
                        <td align="right">
                            <?php if (!empty($d->harga_pst)) {
                      echo number_format($d->harga_pst, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right">
                            <?php if (!empty($jmlpst)) {
                      echo number_format($jmlpst, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right"><?php echo number_format($qtysby, '2', ',', '.'); ?></td>
                        <td align="right">
                            <?php if (!empty($d->harga_sby)) {
                      echo number_format($d->harga_sby, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right">
                            <?php if (!empty($jmlsby)) {
                      echo number_format($jmlsby, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right"><?php echo number_format($qtysmr, '2', ',', '.'); ?></td>
                        <td align="right">
                            <?php if (!empty($d->harga_smr)) {
                      echo number_format($d->harga_smr, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right">
                            <?php if (!empty($jmlsmr)) {
                      echo number_format($jmlsmr, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right"><?php echo number_format($qtyklt, '2', ',', '.'); ?></td>
                        <td align="right">
                            <?php if (!empty($d->harga_klt)) {
                      echo number_format($d->harga_klt, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right">
                            <?php if (!empty($jmlklt)) {
                      echo number_format($jmlklt, '0', ',', '.');
                    } ?>
                        </td>

                        <td align="right"><?php echo number_format($qtygrt, '2', ',', '.'); ?></td>
                        <td align="right">
                            <?php if (!empty($d->harga_grt)) {
                      echo number_format($d->harga_grt, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right">
                            <?php if (!empty($jmlgrt)) {
                      echo number_format($jmlgrt, '0', ',', '.');
                    } ?>
                        </td>
                        </td>

                        <td align="right"><?php echo number_format($qtypwk, '2', ',', '.'); ?></td>
                        <td align="right"><?php if (!empty($d->harga_pwk)) {echo number_format($d->harga_pwk, '0', ',', '.');} ?>
                        </td>
                        <td align="right"><?php if (!empty($jmlpwk)) {echo number_format($jmlpwk, '0', ',', '.');} ?></td>


                        <td align="right"><?php echo number_format($qtybtn, '2', ',', '.'); ?></td>
                        <td align="right"><?php if (!empty($d->harga_btn)) {echo number_format($d->harga_btn, '0', ',', '.');} ?>
                        </td>
                        <td align="right"><?php if (!empty($jmlbtn)) {echo number_format($jmlbtn, '0', ',', '.');} ?></td>

                        <td align="right"><?php echo number_format($sa_gdpusat, '2', ',', '.'); ?></td>
                        <td align="right">
                            <?php if (!empty($d->harga_kirim_cabang)) {
                      echo number_format($d->harga_kirim_cabang, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right">
                            <?php if (!empty($jmlgdpst)) {
                      echo number_format($jmlgdpst, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right"><?php echo number_format($totalqty, '2', ',', '.'); ?></td>
                        <td align="right">
                            <?php if (!empty($hargatotal)) {
                      echo number_format($hargatotal, '0', ',', '.');
                    } ?>
                        </td>
                        <td align="right">
                            <?php if (!empty($totaljml)) {
                      echo number_format($totaljml, '0', ',', '.');
                    } ?>
                        </td>
                    </tr>
                    <?php $no++;
              } ?>
                    <tr bgcolor="#024a75" style="color:white; font-size:14px">
                        <td colspan="2" style="background-color:#024a75 ;" class="fixed-side">TOTAL</td>
                        <td></td>
                        <td></td>
                        <td align="right">
                            <?php if (!empty($totaltsm)) {
                    echo number_format($totaltsm, '0', ',', '.');
                  } ?>
                        </td>
                        <td></td>
                        <td></td>
                        <td align="right">
                            <?php if (!empty($totalbdg)) {
                    echo number_format($totalbdg, '0', ',', '.');
                  } ?>
                        </td>
                        <td></td>
                        <td></td>
                        <td align="right">
                            <?php if (!empty($totalskb)) {
                    echo number_format($totalskb, '0', ',', '.');
                  } ?>
                        </td>
                        <td></td>
                        <td></td>
                        <td align="right">
                            <?php if (!empty($totalbgr)) {
                    echo number_format($totalbgr, '0', ',', '.');
                  } ?>
                        </td>
                        <td></td>
                        <td></td>
                        <td align="right">
                            <?php if (!empty($totaltgl)) {
                    echo number_format($totaltgl, '0', ',', '.');
                  } ?>
                        </td>
                        <td></td>
                        <td></td>
                        <td align="right">
                            <?php if (!empty($totalpwt)) {
                    echo number_format($totalpwt, '0', ',', '.');
                  } ?>
                        </td>
                        <td></td>
                        <td></td>
                        <td align="right">
                            <?php if (!empty($totalpst)) {
                    echo number_format($totalpst, '0', ',', '.');
                  } ?>
                        </td>
                        <td></td>
                        <td></td>
                        <td align="right">
                            <?php if (!empty($totalsby)) {
                    echo number_format($totalsby, '0', ',', '.');
                  } ?>
                        </td>
                        <td></td>
                        <td></td>
                        <td align="right">
                            <?php if (!empty($totalsmr)) {
                    echo number_format($totalsmr, '0', ',', '.');
                  } ?>
                        </td>
                        <td></td>
                        <td></td>
                        <td align="right">
                            <?php if (!empty($totalklt)) {
                    echo number_format($totalklt, '0', ',', '.');
                  } ?>
                        </td>
                        <td></td>
                        <td></td>
                        <td align="right">
                            <?php if (!empty($totalgrt)) {
                    echo number_format($totalgrt, '0', ',', '.');
                  } ?>
                        </td>
                        <td></td>
                        <td></td>
                        <td align="right"><?php if (!empty($totalpwk)) {echo number_format($totalpwk, '0', ',', '.');} ?>
                        </td>

                        <td></td>
                        <td></td>
                        <td align="right"><?php if (!empty($totalbtn)) {echo number_format($totalbtn, '0', ',', '.');} ?>
                        </td>


                        <td></td>
                        <td></td>
                        <td align="right">
                            <?php if (!empty($totalgdpst)) {
                    echo number_format($totalgdpst, '0', ',', '.');
                  } ?>
                        </td>
                        <td></td>
                        <td></td>
                        <td align="right">
                            <?php if (!empty($grandtotaljml)) {
                    echo number_format($grandtotaljml, '0', ',', '.');
                  } ?>
                        </td>

                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script>
        // requires jquery library
        jQuery(document).ready(function() {
            jQuery(".datatable3").clone(true).appendTo('#table-scroll').addClass('clone');
        });

    </script>
</body>
</html>
