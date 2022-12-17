<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Rekonsilias BJ {{ date("d-m-y") }}</title>
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
        PACIFIC CABANG {{ $cabang->nama_cabang }}<br>
        KONSOLIDASI BJ {{ $jk }}<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:50%;  margin-bottom: 30px" border="1">
        <thead bgcolor="#024a75" style="color:white;">
            <tr>
                <th rowspan="2" bgcolor="#024a75" style="font-size:14px !important">NO</th>
                <th rowspan="2" bgcolor="#024a75" style="font-size:14px !important">PRODUK</th>
                <th colspan="3" bgcolor="#024a75" style="font-size:14px !important">TUNAI KREDIT</th>
                <th colspan="3" bgcolor="#0cb30c" style="font-size:14px !important">PERSEDIAAN</th>
                <th colspan="3" bgcolor="#b30c26" style="font-size:14px !important">SELISIH</th>
            </tr>
            <tr>
                <th>Dus</th>
                <th>Pack</th>
                <th>Pcs</th>
                <th style="background-color: #0cb30c;">Dus</th>
                <th style="background-color: #0cb30c;">Pack</th>
                <th style="background-color: #0cb30c;">Pcs</th>
                <th style="background-color: #b30c26;">Dus</th>
                <th style="background-color: #b30c26;">Pack</th>
                <th style="background-color: #b30c26;">Pcs</th>
            </tr>
        </thead>
        <tbody style="font-size: 12px;">
            <?php
            $no = 1;
            foreach ($rekap as $t) {
                if ($t->totalpenjualan != 0) {
                    $jmldust    = floor($t->totalpenjualan / $t->isipcsdus);
                    $sisadus   = $t->totalpenjualan % $t->isipcsdus;
                    if ($t->isipack == 0) {
                        $jmlpackt    = 0;
                        $sisapack   = $sisadus;
                    } else {

                        $jmlpackt    = floor($sisadus / $t->isipcs);
                        $sisapack   = $sisadus % $t->isipcs;
                    }
                    $jmlpcst = $sisapack;
                    if ($t->satuan == 'PCS') {

                        $jmldust = 0;
                        $jmlpackt = 0;
                        $jmlpcst = $t->totalpenjualan;
                    }
                } else {

                    $jmldust 	= 0;
                    $jmlpackt	= 0;
                    $jmlpcst 	= 0;
                    $subtotalt  = 0;
                }


                if ($t->totalpersediaan != 0) {
                    $jmldusk    = floor($t->totalpersediaan  / $t->isipcsdus);
                    $sisadus   = $t->totalpersediaan % $t->isipcsdus;
                    if ($t->isipack == 0) {
                        $jmlpackk   = 0;
                        $sisapack   = $sisadus;
                    } else {

                        $jmlpackk   = floor($sisadus / $t->isipcs);
                        $sisapack   = $sisadus % $t->isipcs;
                    }
                    $jmlpcsk = $sisapack;


                    if ($t->satuan == 'PCS') {

                        $jmldusk = 0;
                        $jmlpackk = 0;
                        $jmlpcsk = $t->totalpersediaan;
                    }
                } else {

                    $jmldusk 	= 0;
                    $jmlpackk	= 0;
                    $jmlpcsk 	= 0;
                    $subtotalk  = 0;
                }
                $selisih = $t->selisih < 0 ? $t->selisih * -1 : $t->selisih;
                if ($selisih != 0) {
                    $cekdus = $tselisih / $t->isipcsdus;
                    if ($cekdus < 0) {
                        $jmldusall = ceil($selisih / $t->isipcsdus);
                    } else {
                        $jmldusall    = floor($selisih / $t->isipcsdus);
                    }
                    $sisadus   	  = $selisih % $t->isipcsdus;
                    if ($t->isipack == 0) {
                        $jmlpackall    = 0;
                        $sisapack      = $sisadus;
                    } else {

                        $jmlpackall    = floor($sisadus / $t->isipcs);
                        $sisapack   	= $sisadus % $t->isipcs;
                    }
                    $jmlpcsall 	= $sisapack;


                    if ($t->satuan == 'PCS') {
                        $jmldusall = 0;
                        $jmlpackall = 0;
                        $jmlpcsall = $selisih;
                    }
                } else {

                    $jmldusall 	= 0;
                    $jmlpackall	= 0;
                    $jmlpcsall 	= 0;
                    $subtotalall  = 0;
                }


            ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td><?php echo $t->nama_barang; ?></td>
                <td style="text-align: center;"><?php echo (!empty($jmldust) ? number_format($jmldust, '0', '', '.') : ""); ?></td>
                <td style="text-align: center;"><?php echo (!empty($jmlpackt) ? number_format($jmlpackt, '0', '', '.') : ""); ?></td>
                <td style="text-align: center;"><?php echo (!empty($jmlpcst) ? number_format($jmlpcst, '0', '', '.') : ""); ?></td>
                <td style="text-align: center;"><?php echo (!empty($jmldusk) ? number_format($jmldusk, '0', '', '.') : ""); ?></td>
                <td style="text-align: center;"><?php echo (!empty($jmlpackk) ? number_format($jmlpackk, '0', '', '.') : ""); ?></td>
                <td style="text-align: center;"><?php echo (!empty($jmlpcsk) ? number_format($jmlpcsk, '0', '', '.') : ""); ?></td>
                <td style="text-align: center;"><?php echo (!empty($jmldusall) ? number_format($jmldusall, '0', '', '.') : ""); ?></td>
                <td style="text-align: center;"><?php echo (!empty($jmlpackall) ? number_format($jmlpackall, '0', '', '.') : ""); ?></td>
                <td style="text-align: center;"><?php echo (!empty($jmlpcsall) ? number_format($jmlpcsall, '0', '', '.') : ""); ?></td>
            </tr>
            <?php $no++;
            } ?>
        </tbody>

    </table>
</body>
</html>
