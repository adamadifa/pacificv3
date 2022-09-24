<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Analisa Umur Piutang (AUP) {{ date("d-m-y") }}</title>
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
        @if ($cabang != null)
        @if ($cabang->kode_cabang=="PST")
        PACIFIC PUSAT
        @else
        PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
        @endif
        @else
        PACIFC ALL CABANG
        @endif
        <br>
        LAPORAN INSENTIF KEPALA ADMIN<br>
        {{ $namabulan[$bulan] }} {{ $tahun }}
    </b>
    <br>
    <?php
    $tgl1 = "2021-08-31";
    $bln = $bulan;
    $tanggal = $tahun . "-" . $bln . "-" . "31";
    if ($bln == 9 and $tahun == "2021") {
        $persentaseljt = 55;
    } else if ($bln == 10 and $tahun == "2021") {
        $persentaseljt = 60;
    } else if ($bln == 11 and $tahun == "2021") {
        $persentaseljt = 65;
    } else if ($bln >= 12 and $tahun == "2021") {
        $persentaseljt = 70;
    } else if ($bln == 1 and $tahun == "2022") {
        $persentaseljt = 75;
    } else if ($bln >= 2 and $tahun == "2022") {
        $persentaseljt = 80;
    } else if ($bln <= 8 and $tahun == "2021") {
        $persentaseljt = 50;
    }
    ?>
    <table class="datatable3">
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">CABANG</th>
                <th colspan="3">Kategori I</th>
                <th colspan="3">Kategori II</th>
                <th colspan="2">Kategori III</th>
                <th rowspan="2">Total</th>
            </tr>
            <tr>
                <th>Cash IN</th>
                <th>Ratio</th>
                <th>Reward</th>
                <th>LJT > 14</th>
                <th>Ratio</th>
                <th>Reward</th>
                <th>Waktu LPC</th>
                <th>Reward</th>
            </tr>
        </thead>
        <tbody style="font-size:14px">
            <?php
            $no = 1;
            foreach ($insentif as $d) {
                $piutang = $d->sisapiutang + $d->cashin_jt;
            ?>
            <tr>
                <td><?= $no; ?></td>
                <td><?= $d->nama_cabang; ?></td>
                <td align="right"><?= number_format($d->cashin, '0', '', '.'); ?></td>
                <td>0.005%</td>
                <td align="right">
                    <?php
                        $rewardkategori1 = $d->cashin * (0.005 / 100);
                        echo  number_format($rewardkategori1, '0', '', '.');
                        ?>
                </td>
                <td align="right"><?= number_format($piutang, '0', '', '.'); ?></td>
                <td>
                    <?php
                        $ratiokategori2 = $d->cashin != 0 ? (($piutang / $d->cashin) * 100) * ($persentaseljt / 100) : 0;
                        echo number_format($ratiokategori2, '2', ',', '.') . "%";

                        ?>
                </td>
                <td align="right">
                    <?php
                        if ($bln >= 12 and $tahun >= 2021) {
                            if ($ratiokategori2 >= 0 and $ratiokategori2 <= 2) {
                                $rewardkategori2 = 350000;
                            } else  if ($ratiokategori2 > 2 and $ratiokategori2 <= 4) {
                                $rewardkategori2 = 315000;
                            } else  if ($ratiokategori2 > 4 and $ratiokategori2 <= 6) {
                                $rewardkategori2 = 280000;
                            } else  if ($ratiokategori2 > 6 and $ratiokategori2 <= 8) {
                                $rewardkategori2 = 245000;
                            } else  if ($ratiokategori2 > 8 and $ratiokategori2 <= 10) {
                                $rewardkategori2 = 210000;
                            } else  if ($ratiokategori2 > 10 and $ratiokategori2 <= 12) {
                                $rewardkategori2 = 175000;
                            } else  if ($ratiokategori2 > 12 and $ratiokategori2 <= 14) {
                                $rewardkategori2 = 140000;
                            } else  if ($ratiokategori2 > 14 and $ratiokategori2 <= 16) {
                                $rewardkategori2 = 105000;
                            } else  if ($ratiokategori2 > 16 and $ratiokategori2 <= 18) {
                                $rewardkategori2 = 70000;
                            } else  if ($ratiokategori2 > 18 and $ratiokategori2 <= 20) {
                                $rewardkategori2 = 35000;
                            } else {
                                $rewardkategori2 = 0;
                            }
                        } else {
                            if ($ratiokategori2 >= 0 and $ratiokategori2 <= 2) {
                                $rewardkategori2 = 350000;
                            } else  if ($ratiokategori2 > 2 and $ratiokategori2 <= 4) {
                                $rewardkategori2 = 315000;
                            } else  if ($ratiokategori2 > 4 and $ratiokategori2 <= 6) {
                                $rewardkategori2 = 280000;
                            } else  if ($ratiokategori2 > 6 and $ratiokategori2 <= 8) {
                                $rewardkategori2 = 245000;
                            } else  if ($ratiokategori2 > 8 and $ratiokategori2 <= 10) {
                                $rewardkategori2 = 210000;
                            } else  if ($ratiokategori2 > 10 and $ratiokategori2 <= 12) {
                                $rewardkategori2 = 175000;
                            } else  if ($ratiokategori2 > 12 and $ratiokategori2 <= 14) {
                                $rewardkategori2 = 140000;
                            } else  if ($ratiokategori2 > 14 and $ratiokategori2 <= 16) {
                                $rewardkategori2 = 105000;
                            } else  if ($ratiokategori2 > 16 and $ratiokategori2 <= 18) {
                                $rewardkategori2 = 70000;
                            } else  if ($ratiokategori2 > 18 and $ratiokategori2 <= 20) {
                                $rewardkategori2 = 35000;
                            } else {
                                $rewardkategori2 = 0;
                            }
                        }
                        echo  number_format($rewardkategori2, '0', '', '.');
                        ?>
                </td>
                <td align="center"><?php echo $d->lamalpc . "(" . date("H:i", strtotime($d->jam_lpc)) . ")"; ?> </td>
                <td align="right">
                    <?php
                        $jam_lpc =  new DateTime($d->jam_lpc);
                        $jam_set =  new DateTime('12:00');
                        if ($d->lamalpc === "0" || $d->lamalpc == 1) {
                            if ($jam_lpc <= $jam_set) {
                                $rewardkategori3 = 350000;
                            } else {
                                $rewardkategori3 = 0;
                            }
                        } else {
                            $rewardkategori3 = 0;
                        }
                        echo  number_format($rewardkategori3, '0', '', '.');
                        ?>
                </td>
                <td>
                    <?php
                        $total = $rewardkategori1 + $rewardkategori2 + $rewardkategori3;
                        echo  number_format($total, '0', '', '.');
                        ?>
                </td>
            </tr>
            <?php $no++;
            } ?>
        </tbody>
    </table>

    <br>
    <table>
        <tr>
            <td>
                <table class="datatable3">
                    <thead>
                        <tr>
                            <th colspan="3">Ratio LJT > 14 Hari(%) Reward</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:14px">
                        <tr>
                            <td>20%</td>
                            <td>></td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>19%</td>
                            <td>20%</td>
                            <td align="right">35.000</td>
                        </tr>
                        <tr>
                            <td>17%</td>
                            <td>18%</td>
                            <td align="right">70.000</td>
                        </tr>
                        <tr>
                            <td>15%</td>
                            <td>16%</td>
                            <td align="right">105.000</td>

                        </tr>
                        <tr>
                            <td>13%</td>
                            <td>14%</td>
                            <td align="right">140.000</td>
                        </tr>
                        <tr>
                            <td>11%</td>
                            <td>12%</td>
                            <td align="right">175.000</td>
                        </tr>
                        <tr>
                            <td>9%</td>
                            <td>10%</td>
                            <td align="right">21.000</td>
                        </tr>
                        <tr>
                            <td>7%</td>
                            <td>8%</td>
                            <td align="right">245.000</td>
                        </tr>
                        <tr>
                            <td>5%</td>
                            <td>6%</td>
                            <td align="right">280.000</td>
                        </tr>
                        <tr>
                            <td>3%</td>
                            <td>4%</td>
                            <td align="right">315.000</td>
                        </tr>
                        <tr>
                            <td>2%</td>
                            <td>0%</td>
                            <td align="right">350.000</td>
                        </tr>

                    </tbody>
                </table>
            </td>
            <td valign="top">
                <table class="datatable3">
                    <thead>
                        <tr>
                            <th>LPC(Hari)</th>
                            <th>Reward</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:14px">

                        <tr>
                            <td>1 (s/d 12:00)</td>
                            <td align="right">350.000</td>
                        </tr>

                    </tbody>
                </table>
            </td>
            <td valign="top">
                <table class="datatable3">
                    <tbody style="font-size:14px">
                        <tr>
                            <td>
                                Asumsi pemberlakuan kebijakan LJT
                            </td>
                        </tr>
                        <tr style="background-color: red; color:white">
                            <td>
                                <?php echo $persentaseljt; ?>%
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
