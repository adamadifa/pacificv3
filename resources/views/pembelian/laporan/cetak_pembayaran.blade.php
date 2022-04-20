<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Pembayaran Pembelian {{ date("d-m-y") }}</title>
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
        LAPORAN PEMBAYRAN<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        @if ($supplier != null)
        SUPPLIER : {{ $supplier->nama_supplier }}
        @else
        ALL SUPPLIER
        @endif
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:100%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12; text-align:center">
                <td>NO</td>
                <td>TGL</td>
                <td>NO BUKTI</td>
                <td>SUPPLIER</td>
                <td>NO KONTRABON</td>
                <td>CASH</td>
                <td>BCA AC 0540522278</td>
                <td>BCA CV PACIFIC</td>
                <td>BCA 0548999700 </td>
                <td>BNI MP VALLAS</td>
                <td>BNI AC 177370752</td>
                <td>BNI<br>CV MAKMUR PERMATA</td>
                <td>BCA<br>CV MAKMUR PERMATA</td>
                <td>KAS BESAR PUSAT</td>
                <td>KAS KECIL</td>
                <td>BNI <br>INDO PANGAN</td>
                <td>BNI <br>VALLAS INDO</td>
                <td>LAIN LAIN/BANK CBG</td>
                <td>TOTAL</td>
                <td>TGL INPUT</td>
                <td>TGL UPDATE</td>
            </tr>
        </thead>
        <tbody>
            <?php
            $cash 	 = 0;
            $bca  	 = 0;
            $bca_new = 0;
            $bca_cv  = 0;
            $permata = 0;
            $bni = 0;
            $bni_mp  = 0;
            $bca_mp  = 0;
            $kas     = 0;
            $kaskecil = 0;
            $bni_indo_pangan = 0;
            $bni_indo_vallas = 0;
            $lainlain = 0;

            $totalbayar  = 0;
            $no = 1;
            foreach ($pmb as $key => $d) {
                $cash 				= $cash + $d->cash;
                $bca  				= $bca + $d->bca;
                $bca_new  			= $bca_new + $d->bca_new;
                $bca_cv				= $bca_cv + $d->bca_cv;
                $permata  			= $permata + $d->permata;
                $bni		  		= $bni + $d->bni;
                $bni_mp   			= $bni_mp + $d->bni_mp;
                $bca_mp   			= $bca_mp + $d->bca_mp;
                $kas 				= $kas + $d->kasbesar;
                $kaskecil 			= $kaskecil + $d->kaskecil;
                $bni_indo_pangan	= $bni_indo_pangan + $d->bni_indo_pangan;
                $bni_indo_vallas 	= $bni_indo_vallas + $d->bni_indo_vallas;
                $lainlain 			= $lainlain + $d->lainlain;
                $totalbayar 		= $totalbayar + $d->totalbayar;

            ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td><?php echo $d->tglbayar; ?></td>
                <td><?php echo $d->nobukti_pembelian; ?></td>
                <td><?php echo $d->nama_supplier; ?></td>
                <td><?php echo $d->no_kontrabon; ?></td>
                <td align="right"><?php if (!empty($d->cash)) {
                                            echo desimal($d->cash);
                                        } ?></td>
                <td align="right"><?php if (!empty($d->bca)) {
                                            echo desimal($d->bca);
                                        } ?></td>
                <td align="right"><?php if (!empty($d->bca_cv)) {
                                            echo desimal($d->bca_cv);
                                        } ?></td>
                <td align="right"><?php if (!empty($d->bca_new)) {
                                            echo desimal($d->bca_new);
                                        } ?></td>
                <td align="right"><?php if (!empty($d->permata)) {
                                            echo desimal($d->permata);
                                        } ?></td>
                <td align="right"><?php if (!empty($d->bni)) {
                                            echo desimal($d->bni);
                                        } ?></td>

                <td align="right"><?php if (!empty($d->bni_mp)) {
                                            echo desimal($d->bni_mp);
                                        } ?></td>
                <td align="right"><?php if (!empty($d->bca_mp)) {
                                            echo desimal($d->bca_mp);
                                        } ?></td>
                <td align="right"><?php if (!empty($d->kasbesar)) {
                                            echo desimal($d->kasbesar);
                                        } ?></td>
                <td align="right"><?php if (!empty($d->kaskecil)) {
                                            echo desimal($d->kaskecil);
                                        } ?></td>
                <td align="right"><?php if (!empty($d->bni_indo_pangan)) {
                                            echo desimal($d->bni_indo_pangan);
                                        } ?></td>
                <td align="right"><?php if (!empty($d->bni_indo_vallas)) {
                                            echo desimal($d->bni_indo_vallas);
                                        } ?></td>

                <td align="right"><?php if (!empty($d->lainlain)) {
                                            echo desimal($d->lainlain) . " (" . $d->via . ")";
                                        } ?></td>
                <td align="right"><?php if (!empty($d->totalbayar)) {
                                            echo desimal($d->totalbayar);
                                        } ?></td>
                <td><?php echo $d->log; ?></td>
                <td><?php echo $d->date_updated; ?></td>
            </tr>
            <?php $no++;
            } ?>
            <tr>
                <td colspan="5"><b>TOTAL</b></td>
                <td align="right"><b><?php if (!empty($cash)) {
                                            echo desimal($cash);
                                        } ?></b></td>
                <td align="right"><b><?php if (!empty($bca)) {
                                            echo desimal($bca);
                                        } ?></b></td>
                <td align="right"><b><?php if (!empty($bca_cv)) {
                                            echo desimal($bca_cv);
                                        } ?></b></td>
                <td align="right"><b><?php if (!empty($bca_new)) {
                                            echo desimal($bca_new);
                                        } ?></b></td>
                <td align="right"><b><?php if (!empty($permata)) {
                                            echo desimal($permata);
                                        } ?></b></td>
                <td align="right"><b><?php if (!empty($bni)) {
                                            echo desimal($bni);
                                        } ?></b></td>
                <td align="right"><b><?php if (!empty($bni_mp)) {
                                            echo desimal($bni_mp);
                                        } ?></b></td>
                <td align="right"><b><?php if (!empty($bca_mp)) {
                                            echo desimal($bca_mp);
                                        } ?></b></td>
                <td align="right"><b><?php if (!empty($kas)) {
                                            echo desimal($kas);
                                        } ?></b></td>
                <td align="right"><b><?php if (!empty($kaskecil)) {
                                            echo desimal($kaskecil);
                                        } ?></b></td>
                <td align="right"><b><?php if (!empty($bni_indo_pangan)) {
                                            echo desimal($bni_indo_pangan);
                                        } ?></b></td>
                <td align="right"><b><?php if (!empty($bni_indo_vallas)) {
                                            echo desimal($bni_indo_vallas);
                                        } ?></b></td>

                <td align="right"><b><?php if (!empty($lainlain)) {
                                            echo desimal($lainlain);
                                        } ?></b></td>
                <td align="right"><b><?php if (!empty($totalbayar)) {
                                            echo desimal($totalbayar);
                                        } ?></b></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>

    </table>
</body>
</html>
