<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Persediaan Barang Gudang Bahan {{ date("d-m-y") }}</title>
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

        tr:nth-child(even) {
            background-color: #d6d6d6c2;
        }

    </style>

    </style>
</head>
<body>
    <b style="font-size:14px;">
        REKAP PERSEDIAAN GUDANG BAHAN<br>
        BULAN {{$namabulan[$bulan]}} TAHUN {{$tahun}}
        <br>
        @if ($kategori != null)
        {{ $kategori->kategori }}
        @endif
    </b>
    <br>
    <table class="datatable3" style="width:115%;zoom:80%" border="1">
        <thead>
            <tr bgcolor="#024a75">
                <th rowspan="3" bgcolor="#024a75" style="color:white; font-size:14;">NO</th>
                <th rowspan="3" bgcolor="#024a75" style="color:white; font-size:14;">KODE</th>
                <th rowspan="3" bgcolor="#024a75" style="color:white; font-size:14;">NAMA BARANG</th>
                <th rowspan="3" bgcolor="#024a75" style="color:white; font-size:14;">SATUAN</th>
            </tr>
            <tr bgcolor="#28a745">
                <th colspan="2" bgcolor="#28a745" style="color:white; font-size:14;">SALDO AWAL</th>
                <th colspan="3" bgcolor="#28a745" style="color:white; font-size:14;">PEMASUKAN</th>
                <th colspan="6" bgcolor="#28a745" style="color:white; font-size:14;">PENGELUARAN</th>
                <th colspan="2" bgcolor="#28a745" style="color:white; font-size:14;">SALDO AKHIR</th>
                <th colspan="2" bgcolor="#28a745" style="color:white; font-size:14;">OPNAME</th>
                <th colspan="2" bgcolor="#28a745" style="color:white; font-size:14;">SELISIH</th>
            </tr>
            <tr bgcolor="red">
                <th style="color:white; font-size:14;">UNIT</th>
                <th style="color:white; font-size:14;">BERAT</th>
                <th style="color:white; font-size:14;">PEMBELIAN</th>
                <th style="color:white; font-size:14;">LAINNYA</th>
                <th style="color:white; font-size:14;">RETUR PENGGANTI</th>
                <th style="color:white; font-size:14;">PRODUKSI</th>
                <th style="color:white; font-size:14;">SEASONING</th>
                <th style="color:white; font-size:14;">PDQC</th>
                <th style="color:white; font-size:14;">SUSUT</th>
                <th style="color:white; font-size:14;">CABANG</th>
                <th style="color:white; font-size:14;">LAINNYA</th>
                <th style="color:white; font-size:14;">UNIT</th>
                <th style="color:white; font-size:14;">BERAT</th>
                <th style="color:white; font-size:14;">UNIT</th>
                <th style="color:white; font-size:14;">BERAT</th>
                <th style="color:white; font-size:14;">UNIT</th>
                <th style="color:white; font-size:14;">BERAT</th>
            </tr>
        </thead>
        <tbody>
            <?php
    $no               = 1;
    $totalproduksi    = 0;
    $totalcabangpeng  = 0;
    $totalseasoning   = 0;
    $totalpdqc        = 0;
    $totalsusut       = 0;
    $totalpembelian   = 0;
    $totallainnya     = 0;
    $totalqtyunitsa   = 0;
    $totalqtyberatsa  = 0;
    $saldoakhirunit2  = 0;
    $totallainnyapeng = 0;
    $saldoakhirberat2 = 0;
    $totalretur       = 0;
    $opnameberat      = 0;
    $opnameunit       = 0;
    foreach ($persediaan as $key => $d) {
      $saldoakhirberat     = $d->qtyberatsa + $d->qtypemb2 + $d->qtylainnya2 + $d->qtypengganti2 - $d->qtyprod4 - $d->qtyseas4 - $d->qtypdqc4 - $d->qtylain4 - $d->qtysus4 - $d->qtycabang4;
      $saldoakhirunit      = $d->qtyunitsa + $d->qtypemb1 + $d->qtylainnya1 + $d->qtypengganti1 - $d->qtyprod3 - $d->qtyseas3 - $d->qtypdqc3 - $d->qtylain3 - $d->qtysus3 - $d->qtycabang3;

      if($d->kode_barang  == 'BB-15'){
        $satuan = "KG";
      }else{
        $satuan = $d->satuan;
      }


      if ($satuan != 'KG') {
        $totalpembelian     += $d->qtypemb1;
      } else {
        $totalpembelian     += $d->qtypemb2;
      }

      if ($satuan != 'KG') {
        $totallainnya       += $d->qtylainnya1;
      } else {
        $totallainnya       += $d->qtylainnya2;
      }

      if ($satuan != 'KG') {
        $totalproduksi      += $d->qtyprod3;
      } else {
        $totalproduksi      += $d->qtyprod4;
      }

      if ($satuan != 'KG') {
        $totalseasoning     += $d->qtyseas3;
      } else {
        $totalseasoning     += $d->qtyseas4;
      }

      if ($satuan != 'KG') {
        $totalpdqc          += $d->qtypdqc3;
      } else {
        $totalpdqc          += $d->qtypdqc4;
      }

      if ($satuan != 'KG') {
        $totallainnyapeng   += $d->qtylain3;
      } else {
        $totallainnyapeng   += $d->qtylain4;
      }

      if ($satuan != 'KG') {
        $totalcabangpeng   += $d->qtycabang3;
      } else {
        $totalcabangpeng   += $d->qtycabang4;
      }

      if ($satuan != 'KG') {
        $totalsusut         += $d->qtysus3;
        $totalretur       = $totalretur + $d->qtypengganti1;
      } else {
        $totalsusut         += $d->qtysus4;
        $totalretur       = $totalretur + $d->qtypengganti2;
      }

      $totalqtyunitsa   = $totalqtyunitsa + $d->qtyunitsa;
      $totalqtyberatsa  = $totalqtyberatsa + $d->qtyberatsa;

      $saldoakhirunit2  = $saldoakhirunit2 + $saldoakhirunit;
      $saldoakhirberat2 = $saldoakhirberat2 + $saldoakhirberat;

      $opnameberat      += $d->qtyberatop;
      $opnameunit       += $d->qtyunitop;


    ?>
            <tr style="font-size: 14;">
                <td><?php echo $no++; ?></td>
                <td><?php echo $d->kode_barang; ?></td>
                <td><?php echo $d->nama_barang; ?></td>
                <td><?php echo $d->satuan; ?></td>
                <td align="center">
                    <?php if ($d->qtyunitsa != 0) {
            echo desimal($d->qtyunitsa);
          } else {
            echo "";
          }
          ?>
                </td>
                <td align="center">
                    <?php if ($d->qtyberatsa != 0) {
            echo desimal($d->qtyberatsa, 2);
          } else {
            echo "";
          }
          ?>
                </td>

                <?php if ($satuan != 'KG') { ?>
                <td align="center">
                    <?php if (!empty($d->qtypemb1)) {
              echo desimal($d->qtypemb1, 2);
            }
            ?>
                </td>
                <?php } else { ?>
                <td align="center">
                    <?php if (!empty($d->qtypemb2)) {
              echo desimal($d->qtypemb2, 2);
            }
            ?>
                </td>
                <?php } ?>

                <?php if ($satuan != 'KG') { ?>
                <td align="center">
                    <?php if (!empty($d->qtylainnya1)) {
              echo desimal($d->qtylainnya1, 2);
            }
            ?>
                </td>
                <?php } else { ?>
                <td align="center">
                    <?php if (!empty($d->qtylainnya2)) {
              echo desimal($d->qtylainnya2, 2);
            }
            ?>
                </td>
                <?php } ?>

                <?php if ($satuan != 'KG') { ?>
                <td align="center">
                    <?php if (!empty($d->qtypengganti1)) {
              echo desimal($d->qtypengganti1, 2);
            }
            ?>
                </td>
                <?php } else { ?>
                <td align="center">
                    <?php if (!empty($d->qtypengganti2)) {
              echo desimal($d->qtypengganti2, 2);
            }
            ?>
                </td>
                <?php } ?>


                <?php if ($satuan != 'KG') { ?>
                <td align="center">
                    <?php if (!empty($d->qtyprod3)) {
              echo desimal($d->qtyprod3, 2);
            }
            ?>
                </td>
                <?php } else { ?>
                <td align="center">
                    <?php if (!empty($d->qtyprod4)) {
              echo desimal($d->qtyprod4, 2);
            }
            ?>
                </td>
                <?php } ?>

                <?php if ($satuan != 'KG') { ?>
                <td align="center">
                    <?php if (!empty($d->qtyseas3)) {
              echo desimal($d->qtyseas3, 2);
            }
            ?>
                </td>
                <?php } else { ?>
                <td align="center">
                    <?php if (!empty($d->qtyseas4)) {
              echo desimal($d->qtyseas4, 2);
            }
            ?>
                </td>
                <?php } ?>

                <?php if ($satuan != 'KG') { ?>
                <td align="center">
                    <?php if (!empty($d->qtypdqc3)) {
              echo desimal($d->qtypdqc3, 2);
            }
            ?>
                </td>
                <?php } else { ?>
                <td align="center">
                    <?php if (!empty($d->qtypdqc4)) {
              echo desimal($d->qtypdqc4, 2);
            }
            ?>
                </td>
                <?php } ?>

                <?php if ($satuan != 'KG') { ?>
                <td align="center">
                    <?php if (!empty($d->qtysus3)) {
              echo desimal($d->qtysus3, 2);
            }
            ?>
                </td>
                <?php } else { ?>
                <td align="center">
                    <?php if (!empty($d->qtysus4)) {
              echo desimal($d->qtysus4, 2);
            }
            ?>
                </td>
                <?php } ?>

                <?php if ($satuan != 'KG') { ?>

                <td align="center">
                    <?php if (!empty($d->qtycabang3)) {
              echo desimal($d->qtycabang3, 2);
            }
            ?>
                </td>
                <?php } else { ?>
                <td align="center">
                    <?php if (!empty($d->qtycabang4)) {
              echo desimal($d->qtycabang4, 2);
            }
            ?>
                </td>
                <?php } ?>

                <?php if ($satuan != 'KG') { ?>

                <td align="center">
                    <?php if (!empty($d->qtylain3)) {
              echo desimal($d->qtylain3, 2);
            }
            ?>
                </td>
                <?php } else { ?>
                <td align="center">
                    <?php if (!empty($d->qtylain4)) {
              echo desimal($d->qtylain4, 2);
            }
            ?>
                </td>
                <?php } ?>

                <td align="center">
                    <?php if (!empty($saldoakhirunit)) {
            echo desimal($saldoakhirunit);
          }
          ?>
                </td>

                <td align="center">
                    <?php if (!empty($saldoakhirberat)) {
            echo desimal($saldoakhirberat, 2);
          }
          ?>
                </td>

                <td align="center">
                    <?php if (!empty($d->qtyunitop)) {
            echo desimal($d->qtyunitop);
          }
          ?>
                </td>

                <td align="center">
                    <?php if (!empty($d->qtyberatop)) {
            echo desimal($d->qtyberatop, 2);
          }
          ?>
                </td>

                <td align="center">
                    <?php if (!empty($saldoakhirunit-$d->qtyunitop)) {
            echo desimal($saldoakhirunit-$d->qtyunitop);
          }
          ?>
                </td>

                <td align="center">
                    <?php if (!empty($saldoakhirberat-$d->qtyberatop)) {
            echo desimal($saldoakhirberat-$d->qtyberatop, 2);
          }
          ?>
                </td>

            </tr>
            <?php
    }
    ?>
        </tbody>
        <tfoot>
            <tr bgcolor="#024a75">
                <th colspan="4" style="color:white; font-size:14;">TOTAL</th>

                <th align="center" style="color:white; font-size:14;">
                    <?php if (!empty($totalqtyunitsa)) {
          echo desimal($totalqtyunitsa, 2);
        } else {
          echo "0";
        }
        ?>
                </th>


                <th align="center" style="color:white; font-size:14;">
                    <?php if (!empty($totalqtyberatsa)) {
          echo desimal($totalqtyberatsa, 2);
        } else {
          echo "0";
        }
        ?>
                </th>

                <th align="center" style="color:white; font-size:14;">
                    <?php if (!empty($totalpembelian)) {
          echo desimal($totalpembelian, 2);
        } else {
          echo "0";
        }
        ?>
                </th>

                <th align="center" style="color:white; font-size:14;">
                    <?php if (!empty($totallainnya)) {
          echo desimal($totallainnya, 2);
        } else {
          echo "0";
        }
        ?>
                </th>

                <th align="center" style="color:white; font-size:14;">
                    <?php if (!empty($totalretur)) {
          echo desimal($totalretur, 2);
        } else {
          echo "0";
        }
        ?>
                </th>

                <th align="center" style="color:white; font-size:14;">
                    <?php if (!empty($totalproduksi)) {
          echo desimal($totalproduksi, 2);
        } else {
          echo "0";
        }
        ?>
                </th>

                <th align="center" style="color:white; font-size:14;">
                    <?php if (!empty($totalseasoning)) {
          echo desimal($totalseasoning, 2);
        } else {
          echo "0";
        }
        ?>
                </th>

                <th align="center" style="color:white; font-size:14;">
                    <?php if (!empty($totalpdqc)) {
          echo desimal($totalpdqc, 2);
        } else {
          echo "0";
        }
        ?>
                </th>


                <th align="center" style="color:white; font-size:14;">
                    <?php if (!empty($totalsusut)) {
          echo desimal($totalsusut, 2);
        } else {
          echo "0";
        }
        ?>
                </th>

                <th align="center" style="color:white; font-size:14;">
                    <?php if (!empty($totalcabangpeng)) {
          echo desimal($totalcabangpeng, 2);
        } else {
          echo "0";
        }
        ?>
                </th>

                <th align="center" style="color:white; font-size:14;">
                    <?php if (!empty($totallainnyapeng)) {
          echo desimal($totallainnyapeng, 2);
        } else {
          echo "0";
        }
        ?>
                </th>

                <th align="center" style="color:white; font-size:14;">
                    <?php if (!empty($saldoakhirunit2)) {
          echo desimal($saldoakhirunit2, 2);
        } else {
          echo "0";
        }
        ?>
                </th>

                <th align="center" style="color:white; font-size:14;">
                    <?php if (!empty($saldoakhirberat2)) {
          echo desimal($saldoakhirberat2, 2);
        } else {
          echo "0";
        }
        ?>
                </th>

                <th align="center" style="color:white; font-size:14;">
                    <?php if (!empty($opnameunit)) {
          echo desimal($opnameunit, 2);
        } else {
          echo "0";
        }
        ?>
                </th>

                <th align="center" style="color:white; font-size:14;">
                    <?php if (!empty($opnameberat)) {
          echo desimal($opnameberat, 2);
        } else {
          echo "0";
        }
        ?>
                </th>

            </tr>
        </tfoot>
    </table>



</body>
</html>
