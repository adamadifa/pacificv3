<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Analisa Umur Piutang (AUP) {{ date('d-m-y') }}</title>
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
        @if ($cbg->kode_cabang == 'PST')
            PACIFIC PUSAT
        @else
            PACIFIC CABANG {{ strtoupper($cbg->nama_cabang) }}
        @endif
        <br>
        LAPORAN KOMISI DRIVER HELPER<br>
        {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
    </b>

    <table class="datatable3">
        <tr>
            <th>NO</th>
            <th>NAMA KARYAWAN</th>
            <th>KATEGORI</th>
            <th>QTY</th>
            <th>RATIO</th>
            <th>REWARD</th>

        </tr>
        <?php
            $grandtotalrewarddriver = 0;
            $no = 1;
            foreach ($driver as $d) {
            $jmlqty = $d->jml_driver;
            if (empty($d->ratioaktif)) {
                if (empty($d->ratioterakhir)) {
                $ratio = $d->ratiodefault;
                } else {
                $ratio = $d->ratioterakhir;
                }
            } else {
                $ratio = $d->ratioaktif;
            }
            $rewarddriver = $jmlqty * $ratio;
            $grandtotalrewarddriver += $rewarddriver;
            ?>
        <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $d->nama_driver_helper; ?></td>
            <td><?php echo $d->kategori; ?></td>
            <td align="center">{{ desimal($d->jml_driver) }}</td>
            <td align="center"><?php echo $ratio; ?></td>
            <td align="right"><?php echo rupiah($rewarddriver); ?></td>
        </tr>
        <?php
                $no++;
            }
            $grandtotalrewardhelper = 0;
            $nextno = $no + 1;
                foreach ($helper as $d) {
                    $jmlqty = $d->jml_helper;
                    // if (empty($d->ratioaktif)) {
                    //   if (empty($d->ratioterakhir)) {
                    //     $ratio = $d->ratiodefault;
                    //   } else {
                    //     $ratio = $d->ratioterakhir;
                    //   }
                    // } else {
                    //   $ratio = $d->ratioaktif;
                    // }

                    if (empty($d->ratiohelperaktif) || $d->ratiohelperaktif == 0.00) {
                        if (empty($d->ratiohelperterakhir) || $d->ratiohelperterakhir == 0.00) {
                        if (empty($d->ratiohelperdefault) || $d->ratiohelperdefault == 0.00) {
                            $ratio = $d->ratiodefault;
                        } else {
                            $ratio = $d->ratiohelperdefault;
                        }
                        } else {
                        $ratio = $d->ratiohelperterakhir;
                        }
                    } else {
                        $ratio = $d->ratiohelperaktif;
                    }
                    $rewardhelper = $jmlqty * $ratio;
                    $grandtotalrewardhelper += $rewardhelper;
        ?>
        <tr>
            <td><?php echo $nextno; ?></td>
            <td><?php echo $d->nama_driver_helper; ?></td>
            <td>HELPER</td>
            <td align="center">
                {{ desimal($d->jml_helper) }}
            </td>
            <td align="center"><?php echo $ratio; ?></td>
            <td align="right">{{ rupiah($rewardhelper) }}</td>

        </tr>
        <?php
            $nextno++;
        }
        $grandtotalrewardgudang=0;
        $nextno = $no + 1;
                foreach ($gudang as $d) {
                    $jmlqty = $tunaikredit->total;
                    if (empty($d->ratioaktif)) {
                        if (empty($d->ratioterakhir)) {
                        $ratio = $d->ratiodefault;
                        } else {
                        $ratio = $d->ratioterakhir;
                        }
                    } else {
                        $ratio = $d->ratioaktif;
                    }
                    $rewardgudang = $jmlqty * $ratio;
                    $grandtotalrewardgudang += $rewardgudang;
        ?>
        <tr>
            <td><?php echo $nextno; ?></td>
            <td><?php echo $d->nama_driver_helper; ?></td>
            <td>GUDANG</td>
            <td align="center">{{ desimal($jmlqty) }}</td>
            <td align="center"><?php echo $ratio; ?></td>
            <td align="right">{{ rupiah($rewardgudang) }}</td>
        </tr>
        <?php
            $nextno++;
        }
        $grandtotalreward = $grandtotalrewarddriver + $grandtotalrewardhelper + $grandtotalrewardgudang;
    ?>
        <tr>
            <td colspan="5" style="font-size:24px; font-weight:bold" align="center">TOTAL</td>
            <td style="font-size:24px; font-weight:bold" align="right"><?php echo rupiah($grandtotalreward); ?>
            </td>
        </tr>
    </table>
