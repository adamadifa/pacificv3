<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Pembelian {{ date("d-m-y") }}</title>
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
        LAPORAN PEMBELIAN<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        @if ($departemen != null)
        DEPARTEMEN : {{ strtoupper($departemen->nama_dept) }}
        @else
        ALL DEPARTEMEN
        @endif
        <br>
        @if ($supplier != null)
        SUPPLIER : {{ $supplier->nama_supplier }}
        @else
        ALL SUPPLIER
        @endif
        <br>
        @if ($ppn!="-")
        @if ($ppn==1)
        PPN
        @else
        NON PPN
        @endif
        @endif
    </b>
    <br>
    <table class="datatable3" style="width:100%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12; text-align:center">
                <td>NO</td>
                <td>TGL</td>
                <td>NO BUKTI</td>
                <td>SUPPLIER</td>
                <td>NAMA BARANG</td>
                <td>KETERANGAN</td>
                <td>JENIS TRANSAKSI</td>
                <td>PCF/MP</td>
                <td>AKUN</td>
                <td>JURNAL</td>
                <td>PPN</td>
                <td>QTY</td>
                <td>HARGA</td>
                <td>SUBTOTAL</td>
                <td>PENYESUAIAN</td>
                <td>TOTAL</td>
                <td>DEBET</td>
                <td>KREDIT</td>
                <td>TANGGAL INPUT</td>
                <td>TANGGAL UPDATE</td>
            </tr>
        </thead>
        <tbody>
            <?php
            // $subtotal 			= 0;
            $totaldk = 0;
            $totalppn = 0;
            $no = 1;
            $grandtotall = 0;
            $totaldebet = 0;
            $totalkredit = 0;
            $subtotal = 0;
            foreach ($pmb as $key => $d) {
                $nobukti  	    = @$pmb[$key + 1]->nobukti_pembelian;
                $totalharga     =  ($d->qty * $d->harga) + $d->penyesuaian;
                $subtotalharga  = $d->qty * $d->harga;
                if ($d->kode_dept != "GDB") {
                    $akun       = "2-1300";
                    $namaakun   = "Hutang Lainnya";
                } else {
                    $akun       = "2-1200";
                    $namaakun   = "Hutang Dagang";
                }
                if ($d->ppn == '1') {
                    $cekppn  =  "&#10004;";
                    $bgcolor = "#ececc8";
                    $dpp     = (100 / 110) * $totalharga;
                    $ppn     = 10 / 100 * $dpp;
                } else {
                    $bgcolor = "";
                    $cekppn  = "";
                    $dpp     = "";
                    $ppn     = "";
                }
                if ($d->status == 'PNJ') {
                    $totharga 	= -$totalharga;
                    $debet 		= 0;
                    $kredit   	= $totalharga;
                    $namabarang = $d->ket_penjualan;
                } else {
                    $totharga 	= $totalharga;
                    $debet 		= $totalharga;
                    $kredit   	= 0;
                    $namabarang = $d->nama_barang;
                }
                $grandtotall 	= $grandtotall + $totalharga;
                $grandtotal 	= $totharga;
                $totaldebet 	= $totaldebet + $debet;
                $totalkredit    = $totalkredit + $kredit;
                $totaldk 	    = $totaldk + $grandtotal;
                // $totalppn    = $totalppn + $ppn;

            ?>
            <tr style="background-color:<?php echo $bgcolor; ?>; font-size:10px">
                <td><?php echo $no; ?></td>
                <td><?php echo date("d-m-Y",strtotime($d->tgl_pembelian))?></td>
                <td><?php echo $d->nobukti_pembelian; ?></td>
                <td><?php echo $d->nama_supplier; ?></td>
                <td><?php echo $namabarang; ?></td>
                <td><?php echo $d->keterangan; ?></td>
                <td><?php echo strtoupper($d->jenistransaksi); ?></td>
                <td>
                    <?php
                    if (substr($d->kode_akun, 0, 1) == "6" and !empty($d->kode_cabang) or substr($d->kode_akun, 0, 1) == "5" and !empty($d->kode_cabang)) {
                        echo  $d->kode_cabang;
                    } else {
                        echo  "";
                    } ?>
                </td>
                <td align="center" class="str"><?php echo $d->kode_akun; ?></td>
                <td><?php echo $d->nama_akun; ?></td>
                <td align="center"><?php echo $cekppn; ?></td>
                <td align="center"><?php echo desimal($d->qty); ?></td>
                <td align="right"><?php echo desimal($d->harga); ?></td>
                <td align="right"><?php echo desimal($subtotalharga); ?></td>
                <td align="right"><?php echo desimal($d->penyesuaian); ?></td>
                <td align="right"><?php echo desimal($totalharga); ?></td>
                <td align="right"><?php echo desimal($debet); ?></td>
                <td align="right"><?php echo desimal($kredit); ?></td>
                <?php if ($d->tgl_pembelian < "2020-12-02") { ?>
                <td><?php echo $d->date_created; ?></td>
                <td><?php echo $d->date_updated; ?></td>
                <?php } else { ?>
                <td><?php echo $d->detaildate_created; ?></td>
                <td><?php echo $d->detaildate_updated; ?></td>
                <?php } ?>
            </tr>
            <?php
                $subtotal = $subtotal + $grandtotal;
                if ($nobukti != $d->nobukti_pembelian) {
                    echo '
                        <tr bgcolor="#a7efe4" style="color:black; font-weight:bold">
                            <td></td>
                            <td></td>
                            <td>' . $d->nobukti_pembelian . '</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>' . strtoupper($d->jenistransaksi) . '</td>
                            <td></td>
                            <td align=center>' . $akun . '</td>
                            <td>' . $namaakun . '</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td align=right>' . desimal($subtotal) . '</td>
                            <td></td>
                            <td></td>
                        </tr>';
                            $subtotal = 0;
                        }

                        ?>
            <?php
                $no++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="14" align="center"><b>TOTAL</b></td>
            <td align="right"><b></b></td>
            <td align="right"><b><?php echo desimal($grandtotall); ?></b></td>
            <td align="right"><b><?php echo desimal($totaldebet); ?></b></td>
            <td align="right"><b><?php echo desimal($totalkredit + $totaldk); ?></b></td>
            <td></td>
            <td></td>
        </tr>
    </table>
</body>
</html>
