<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Pemasukan Barang Gudang Logistik {{ date('d-m-y') }}</title>
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
        REKAP PERSEDIAAN BARANG GUDANG LOGISTIK<br>
        PERIODE BULAN {{ $namabulan[$bulan] }} {{ $tahun }}
        <br>
        @if ($kat != null)
            KATEGORI {{ $kat->kategori }}
        @endif
        <br>
    </b>
    <br>
    <table class="datatable3" id="table-1" <?php if ($kategori == 'Z001') {
        echo "style='width: 100%'";
    } else {
        echo "style='width: 100%'";
    } ?> border="1">
        <thead>
            <tr bgcolor="#024a75">
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;width:15px">NO</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;width:100px">KODE</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;width:300px">NAMA BARANG</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;width:100px">SATUAN</th>
                <th <?php if ($kategori == 'Z001') {
                    echo "colspan='3'";
                } else {
                    echo "rowspan='2'";
                } ?> style="color:white; font-size:14;width:180px">SALDO AWAL</th>
                <th <?php if ($kategori == 'Z001') {
                    echo "colspan='3'";
                } else {
                    echo "rowspan='2'";
                } ?> style="color:white; font-size:14;width:180px">MASUK</th>
                <th <?php if ($kategori == 'Z001') {
                    echo "colspan='3'";
                } else {
                    echo "rowspan='2'";
                } ?> style="color:white; font-size:14;width:180px">KELUAR</th>
                <th <?php if ($kategori == 'Z001') {
                    echo "colspan='3'";
                } else {
                    echo "rowspan='2'";
                } ?> style="color:white; font-size:14;width:180px">STOK AKHIR KARTU GUDANG</th>
                <th rowspan="2" style="color:white; font-size:14;width:70px">OPNAME</th>
                <th rowspan="2" style="color:white; font-size:14;width:70px">SELISIH</th>
            </tr>
            <tr bgcolor="#28a745">
                <?php if ($kategori == "Z001") { ?>
                <th bgcolor="#28a745" style="color:white; font-size:14;width:70px">STOK</th>
                <th bgcolor="#28a745" style="color:white; font-size:14;width:90px">HARGA</th>
                <th bgcolor="#28a745" style="color:white; font-size:14;width:120px">JUMLAH</th>
                <th bgcolor="#28a745" style="color:white; font-size:14;width:70px">QTY</th>
                <th bgcolor="#28a745" style="color:white; font-size:14;width:90px">HARGA</th>
                <th bgcolor="#28a745" style="color:white; font-size:14;width:120px">JUMLAH</th>
                <th bgcolor="#28a745" style="color:white; font-size:14;width:70px">QTY</th>
                <th bgcolor="#28a745" style="color:white; font-size:14;width:90px">HARGA</th>
                <th bgcolor="#28a745" style="color:white; font-size:14;width:120px">JUMLAH</th>
                <th bgcolor="#28a745" style="color:white; font-size:14;width:70px">QTY</th>
                <th bgcolor="#28a745" style="color:white; font-size:14;width:90px">HARGA</th>
                <th bgcolor="#28a745" style="color:white; font-size:14;width:120px">JUMLAH</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $no                     = 1;
            $totqtystokakhir        = 0;
            $tothargasaldo          = 0;
            $totalsaldoawal         = 0;
            $totqtysaldoawal        = 0;
            $totqtymasuk            = 0;
            $totalpemasukan         = 0;
            $totstokakhir           = 0;
            $totalopname            = 0;
            $totalselisih           = 0;
            $totqtykeluar           = 0;
            $totalpengeluaran       = 0;
            foreach ($persediaan as $key => $d) {
                $kode_kategori    = $d->kode_kategori;
                $qtyrata          = $d->qtysaldoawal + $d->qtypemasukan;
                if (!empty($qtyrata)) {
                $qtyrata        = $d->qtysaldoawal + $d->qtypemasukan;
                } else {
                $qtyrata        = 1;
                }
                $stokakhir      = $d->qtysaldoawal + $d->qtypemasukan - $d->qtypengeluaran;

            if ($d->hargasaldoawal == "" and $d->hargasaldoawal == "0") {
                $hargakeluar      = $d->hargapemasukan  + $d->penyesuaian;
            } elseif ($d->hargapemasukan == "" and $d->hargapemasukan == "0") {
                $hargakeluar      = $d->hargasaldoawal;
            } else {
                $hargakeluar      = (($d->totalsa * 1) + ($d->totalpemasukan * 1) + ($d->penyesuaian * 1)) / $qtyrata;
            }

            if ($d->hargapemasukan == "" and $d->hargapemasukan == "0") {
                $hargamasuk = $d->hargapemasukan + $d->penyesuaian;
            } else if ($d->hargapemasukan != "") {
                $hargamasuk = ($d->totalpemasukan * 1) / $d->qtypemasukan + ($d->penyesuaian * 1);
            } else {
                $hargamasuk = 0;
            }

            $jmlhpengeluaran  = $hargakeluar * $d->qtypengeluaran;
            $jmlstokakhir     = $stokakhir * $hargakeluar;
            $selsish          = $stokakhir - $d->qtyopname;

            $totqtystokakhir  += $stokakhir;
            $tothargasaldo    += $d->hargasaldoawal;
            $totalsaldoawal   += $d->totalsa;
            $totqtysaldoawal  += $d->qtysaldoawal;
            $totqtymasuk      += $d->qtypemasukan;
            $totalpemasukan   += $d->totalpemasukan + $d->penyesuaian;

            $totqtykeluar     += $d->qtypengeluaran;
            $totalpengeluaran += $jmlhpengeluaran;

            $totstokakhir     += $jmlstokakhir;
            $totalopname      += $d->qtyopname;
            $totalselisih     += $selsish;


?>
            <tr style="font-size: 12">
                <td width="15px"><?php echo $no++; ?></td>
                <td width="100px"><?php echo $d->kode_barang; ?></td>
                <td><?php echo $d->nama_barang; ?></td>
                <td><?php echo $d->satuan; ?></td>
                <!-- Saldo Awal -->
                <td width="70px" align="center">{{ !empty($d->qtysaldoawal) ? desimal($d->qtysaldoawal) : '' }}
                </td>
                <?php if ($kategori == "Z001") { ?>
                <td width="90px" align="right">{{ !empty($d->hargasaldoawal) ? desimal($d->hargasaldoawal) : '' }}</td>
                <td width="115px" align="right">{{ !empty($d->totalsa) ? desimal($d->totalsa) : '' }}</td>
                <?php } ?>
                <!-- Pemasukan -->
                <td width="70px" align="center">
                    <?php if (!empty($d->qtypemasukan) and $d->qtypemasukan != '0') {
                        echo desimal($d->qtypemasukan);
                    } ?>
                </td>
                <?php if ($kategori == "Z001") { ?>
                <td width="90px" align="right"> <?php if (!empty($hargamasuk) and $hargamasuk != '0') {
                    echo desimal($hargamasuk);
                } ?></td>
                <td width="120px" align="right"><?php if (!empty($d->totalpemasukan + $d->penyesuaian) and $d->totalpemasukan + $d->penyesuaian != '0') {
                    echo desimal($d->totalpemasukan + $d->penyesuaian);
                } ?>
                </td>
                <?php } ?>
                <!-- Pengeluaran -->
                <td width="70px" align="center"> <?php if (!empty($d->qtypengeluaran) and $d->qtypengeluaran != '0') {
                    echo desimal($d->qtypengeluaran);
                } ?>
                </td>
                <?php if ($kategori == "Z001") { ?>
                <td width="90px" align="right"><?php if (!empty($hargakeluar) and $hargakeluar != '0' and !empty($d->qtypengeluaran)) {
                    echo desimal($hargakeluar);
                } ?></td>
                <td width="120px" align="right"><?php if (!empty($jmlhpengeluaran) and $jmlhpengeluaran != '0') {
                    echo desimal($jmlhpengeluaran);
                } ?>
                </td>
                <?php } ?>
                <!-- Stok Akhir -->
                <td width="70px" align="center">{{ !empty($stokakhir) ? desimal($stokakhir) : '' }}</td>
                <?php if ($kategori == "Z001") { ?>
                <td width="90px" align="right">{{ !empty($hargakeluar) ? desimal($hargakeluar) : '' }}</td>
                <td width="120px" align="right">{{ !empty($jmlstokakhir) ? desimal($jmlstokakhir) : '' }}</td>
                <?php } ?>
                <!-- Opname -->
                <td width="70px" align="center"><?php if (!empty($d->qtyopname) and $d->qtyopname != '0') {
                    echo desimal($d->qtyopname);
                } ?></td>
                <td width="70px" align="center"><?php if (!empty($selsish) and $selsish != '0') {
                    echo desimal($selsish);
                } ?></td>
            </tr>
            <?php
                }
            ?>
        </tbody>
        <tfoot bgcolor="#024a75" style="color:white; font-size:14;">
            <tr>
                <th style="color:white; font-size:14;" colspan="4">TOTAL</th>
                <th align="center"><?php echo desimal($totqtysaldoawal); ?>
                </th>
                <?php if ($kategori == "Z001") { ?>
                <th align="center"></th>
                <th align="center"><?php if (!empty($totalsaldoawal) and $totalsaldoawal != '0' and $kode_kategori == 'Z001') {
                    echo desimal($totalsaldoawal);
                } ?>
                </th>
                <?php } ?>
                <th align="center"><?php if (!empty($totqtymasuk) and $totqtymasuk != '0') {
                    echo desimal($totqtymasuk);
                } ?></th>
                <?php if ($kategori == "Z001") { ?>
                <th></th>
                <th align="center"><?php if (!empty($totalpemasukan) and $totalpemasukan != '0' and $kode_kategori == 'Z001') {
                    echo desimal($totalpemasukan);
                } ?></th>
                <?php } ?>
                <th align="center"><?php if (!empty($totqtykeluar) and $totqtykeluar != '0') {
                    echo desimal($totqtykeluar);
                } ?></th>
                <?php if ($kategori == "Z001") { ?>
                <th></th>
                <th align="center"><?php if (!empty($totalpengeluaran) and $totalpengeluaran != '0' and $kode_kategori == 'Z001') {
                    echo desimal($totalpengeluaran);
                } ?>
                </th>
                <?php } ?>
                <th bgcolor="green" align="center"><?php if (!empty($totqtystokakhir) and $totqtystokakhir != '0') {
                    echo desimal($totqtystokakhir);
                } ?>
                </th>
                <?php if ($kategori == "Z001") { ?>
                <th></th>
                <th bgcolor="green" align="center"><?php if (!empty($totstokakhir) and $totstokakhir != '0' and $kode_kategori == 'Z001') {
                    echo desimal($totstokakhir);
                } ?>
                </th>
                <?php } ?>
                <th align="center"><?php if (!empty($totalopname) and $totalopname != '0') {
                    echo desimal($totalopname);
                } ?>
                </th>
                <th align="center"><?php if (!empty($totalselisih) and $totalselisih != '0') {
                    echo desimal($totalselisih);
                } ?>
                </th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
