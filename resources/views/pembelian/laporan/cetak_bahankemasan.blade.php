<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Bahan Kemasan {{ date('d-m-y') }}</title>
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
        LAPORAN BAHAN & KEMASAN<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        @if ($jenis_barang != '')
            JENIS BARANG : {{ $jenis_barang }}
        @endif
        @php
            $jenis = $jenis_barang;
        @endphp
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:70%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12; text-align:center">
                <td>NO</td>
                <td>KODE</td>
                <td>NAMA BAHAN</td>
                <td>JENIS</td>
                <td>SATUAN</td>
                <td>QTY</td>
                <td>HARGA</td>
                <td>QTY(gram)</td>
                <td>HARGA(gram)</td>
                <td>Jurnal Koreksi</td>
                <td>TOTAL</td>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalbahanbaku  = 0;
            $totalbahanpembantu = 0;
            $totalkemasan = 0;
            $no 		= 1;
            foreach ($pmb as $key => $d) {
                // $totalharga = ($d->harga * $d->qty) + $d->penyesuaian;
                // $subtotal = $d->harga * $d->qty;
                // if($d->ppn=='1')
                // {
                //   $cekppn  =  "&#10004;";
                //   $bgcolor = "#ececc8";
                //   $dpp     = (100/110) * $totalharga;
                //   $ppn     = 10/100*$dpp;
                // }else{
                //   $bgcolor = "";
                //   $cekppn  = "";
                //   $dpp     = "";
                //   $ppn     = "";
                // }
                //
                // $grandtotal 	= $totalharga;
                // $total 				= $total + $grandtotal;

                $totalharga = $d->totalharga - $d->jml_jk;
                if ($d->jenis_barang == 'BAHAN BAKU') {
                    //echo "TEST";
                    $totalbahanbaku = $totalbahanbaku + $totalharga;
                    $totalbahanpembantu = $totalbahanpembantu +  0;
                    $totalkemasan = $totalkemasan + 0;
                } else if ($d->jenis_barang == 'Bahan Tambahan') {
                    $totalbahanbaku = $totalbahanbaku + 0;
                    $totalbahanpembantu = $totalbahanpembantu + $totalharga;
                    $totalkemasan = $totalkemasan + 0;
                } else if ($d->jenis_barang == 'KEMASAN') {
                    $totalbahanbaku = $totalbahanbaku + 0;
                    $totalbahanpembantu = $totalbahanpembantu + 0;
                    $totalkemasan = $totalkemasan + $totalharga;
                }

                //echo strlen($d->jenis_barang);


            ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td><?php echo $d->kode_barang; ?></td>
                <td><?php echo $d->nama_barang; ?></td>
                <td><?php echo $d->jenis_barang; ?></td>
                <td><?php echo $d->satuan; ?></td>
                <td align="center"><?php echo rupiah($d->totalqty); ?></td>
                <td align="right"><?php echo rupiah($d->totalharga / $d->totalqty); ?></td>
                <?php if ($d->satuan == "KG") { ?>
                <td align="center"><?php echo rupiah($d->totalqty * 1000); ?></td>
                <td align="center"><?php echo rupiah($d->totalharga / ($d->totalqty * 1000)); ?></td>
                <td align="right">{{ rupiah($d->jml_jk) }}</td>
                <td align="right"><?php echo rupiah($totalharga); ?></td>
                <?php } else { ?>
                <td align="center"><?php echo rupiah($d->totalqty); ?></td>
                <td align="center"><?php echo rupiah($d->totalharga / $d->totalqty); ?></td>
                <td align="right">{{ rupiah($d->jml_jk) }}</td>
                <td align="right"><?php echo rupiah($totalharga); ?></td>
                <?php } ?>
            </tr>
            <?php
                $no++;
            }
            ?>
            <?php if ($jenis == 'BAHAN') { ?>
            <tr bgcolor="#024a75" style="color:white">
                <td colspan="9"><b>Total Bahan Baku</b></td>
                <td></td>
                <td align="right"><b><?php echo rupiah($totalbahanbaku); ?></b></td>
            </tr>
            <tr bgcolor="#024a75" style="color:white">
                <td colspan="9"><b>Total Bahan Pembantu</b></td>
                <td></td>
                <td align="right"><b><?php echo rupiah($totalbahanpembantu); ?></b></td>
            </tr>
            <?php } else if ($jenis == 'KEMASAN') { ?>
            <tr bgcolor="#024a75" style="color:white">
                <td colspan="9"><b>Total Bahan Kemasan</b></td>
                <td></td>
                <td align="right"><b><?php echo rupiah($totalkemasan); ?></b></td>
            </tr>
            <?php } else { ?>
            <tr bgcolor="#024a75" style="color:white">
                <td colspan="9"><b>Total Bahan Baku</b></td>
                <td></td>
                <td align="right"><b><?php echo rupiah($totalbahanbaku); ?></b></td>
            </tr>
            <tr bgcolor="#024a75" style="color:white">
                <td colspan="9"><b>Total Bahan Pembantu</b></td>
                <td></td>
                <td align="right"><b><?php echo rupiah($totalbahanpembantu); ?></b></td>
            </tr>
            <tr bgcolor="#024a75" style="color:white">
                <td colspan="9"><b>Total Bahan Kemasan</b></td>
                <td></td>
                <td align="right"><b><?php echo rupiah($totalkemasan); ?></b></td>
            </tr>
            <tr bgcolor="#024a75" style="color:white">
                <td colspan="9"><b>Total</b></td>
                <td></td>
                <td align="right"><b><?php echo rupiah($totalkemasan + $totalbahanbaku + $totalbahanpembantu); ?></b></td>
            </tr>
            <?php } ?>
        </tbody>

    </table>
</body>

</html>
