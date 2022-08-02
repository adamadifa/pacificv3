<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kartu Gudang Bahan {{ date("d-m-y") }}</title>
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
        REKAPITULASI PERSEDIAAN
        @if ($barang != null)
        {{ strtoupper($barang->nama_barang) }}
        @endif
        <br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:100%" border="1">
        <thead>
            <tr>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;">TANGGAL</th>
                <!-- <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;">BTB</th> -->
                <th rowspan="1" colspan="2" bgcolor="#024a75" style="color:white; font-size:14;">UNIT</th>
                <th rowspan="1" bgcolor="#024a75" style="color:white; font-size:14;">SALDO</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;">KETERANGAN</th>
                <th rowspan="1" colspan="3" bgcolor="#024a75" style="color:white; font-size:14;">MASUK</th>
                <th rowspan="1" colspan="6" bgcolor="#024a75" style="color:white; font-size:14;">KELUAR</th>
                <th rowspan="1" bgcolor="#024a75" style="color:white; font-size:14;">SALDO AKHIR</th>
            </tr>
            <tr>
                <th bgcolor="#024a75" style="color:white; font-size:14;">IN</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">OUT</th>
                <th bgcolor="red" style="color:white; font-size:14;">
                    <?php
                    if ($barang->satuan != 'KG') {
                        if (!empty($saldoawal->qtyunitsa)) {
                            echo desimal($saldoawal->qtyunitsa);
                        }
                    } else {
                        if (!empty($saldoawal->qtyunitsa)) {
                            echo desimal($saldoawal->qtyunitsa);
                        }
                    }
                    ?>
                </th>
                <th bgcolor="green" style="color:white; font-size:14;">PEMBELIAN</th>
                <th bgcolor="green" style="color:white; font-size:14;">LAINNYA</th>
                <th bgcolor="green" style="color:white; font-size:14;">RETUR PENGGANTI</th>
                <th bgcolor="green" style="color:white; font-size:14;">PRODUKSI</th>
                <th bgcolor="green" style="color:white; font-size:14;">SEASONING</th>
                <th bgcolor="green" style="color:white; font-size:14;">PDQC</th>
                <th bgcolor="green" style="color:white; font-size:14;">SUSUT</th>
                <th bgcolor="green" style="color:white; font-size:14;">CABANG</th>
                <th bgcolor="green" style="color:white; font-size:14;">LAINNYA</th>
                <th bgcolor="red" style="color:white; font-size:14;">
                    <?php
                    if ($barang->satuan != 'KG') {
                        if (!empty($saldoawal->qtyunitsa)) {
                            echo desimal($saldoawal->qtyunitsa);
                        }
                    } else {
                        if (!empty($saldoawal->qtyberatsa)) {
                            echo desimal($saldoawal->qtyberatsa);
                        }
                    }
                    ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($barang->satuan == 'KG') {
                $saldoakhir  = $saldoawal->qtyberatsa;
                $saldoakhirunit  = $saldoawal->qtyunitsa;
            } else {
                $saldoakhir  = $saldoawal->qtyunitsa;
                $saldoakhirunit  = $saldoawal->qtyunitsa;
            }
            $totqtypemb         = 0;
            $totqtylainnya      = 0;
            $totqtylain         = 0;
            $totqtyretur        = 0;
            $totqtypro          = 0;
            $totqtyseas         = 0;
            $totqtypdqc         = 0;
            $totqtycabang       = 0;
            $totqtysus          = 0;
            $totqtyunitmasuk    = 0;
            $totqtyunitkeluar   = 0;
            while (strtotime($dari) <= strtotime($sampai)) {
                $masuk = DB::table('detail_pemasukan_gb')
                ->selectRaw("SUM(qty_unit) as qty_unit, SUM(qty_berat) as qty_berat,
                SUM( IF( departemen = 'Pembelian' , qty_berat ,0 )) AS qtypemb1,
                SUM( IF( departemen = 'Lainnya' , qty_berat ,0 )) AS qtylainnya1,
                SUM( IF( departemen = 'Retur Pengganti' , qty_berat ,0 )) AS qtyretur1,

                SUM( IF( departemen = 'Pembelian' , qty_unit ,0 )) AS qtypemb2,
                SUM( IF( departemen = 'Lainnya' , qty_unit ,0 )) AS qtylainnya2,
                SUM( IF( departemen = 'Retur Pengganti' , qty_unit ,0 )) AS qtyretur2")
                ->join('pemasukan_gb','detail_pemasukan_gb.nobukti_pemasukan','=','pemasukan_gb.nobukti_pemasukan')
                ->where('tgl_pemasukan',$dari)
                ->where('kode_barang',$kode_barang)
                ->groupBy('tgl_pemasukan')
                ->first();

                $keluar = DB::table('detail_pengeluaran_gb')
                ->selectRaw("SUM(qty_unit) as qty_unit, SUM(qty_berat) as qty_berat,
                SUM( IF( pengeluaran_gb.kode_dept = 'Produksi' , qty_berat ,0 )) AS qtyprod1,
                SUM( IF( pengeluaran_gb.kode_dept = 'Seasoning' , qty_berat ,0 )) AS qtyseas1,
                SUM( IF( pengeluaran_gb.kode_dept = 'PDQC' , qty_berat ,0 )) AS qtypdqc1,
                SUM( IF( pengeluaran_gb.kode_dept = 'Susut' , qty_berat ,0 )) AS qtysus1,
                SUM( IF( pengeluaran_gb.kode_dept = 'Lainnya' , qty_berat ,0 )) AS qtylain1,
                SUM( IF( pengeluaran_gb.kode_dept = 'Cabang' , qty_berat ,0 )) AS qtycabang1,

                SUM( IF( pengeluaran_gb.kode_dept = 'Produksi' , qty_unit ,0 )) AS qtyprod2,
                SUM( IF( pengeluaran_gb.kode_dept = 'Seasoning' , qty_unit ,0 )) AS qtyseas2,
                SUM( IF( pengeluaran_gb.kode_dept = 'PDQC' , qty_unit ,0 )) AS qtypdqc2,
                SUM( IF( pengeluaran_gb.kode_dept = 'Susut' , qty_unit ,0 )) AS qtysus2,
                SUM( IF( pengeluaran_gb.kode_dept = 'Lainnya' , qty_unit ,0 )) AS qtylain2,
                SUM( IF( pengeluaran_gb.kode_dept = 'Cabang' , qty_unit ,0 )) AS qtycabang2")
                ->join('pengeluaran_gb','detail_pengeluaran_gb.nobukti_pengeluaran','=','pengeluaran_gb.nobukti_pengeluaran')
                ->where('tgl_pengeluaran',$dari)
                ->where('kode_barang',$kode_barang)
                ->groupBy('tgl_pengeluaran')
                ->first();

                if ($barang->satuan == 'KG') {
                if($masuk != null){
                    $qtymasuk = $masuk->qty_berat;
                    $qtypemb = $masuk->qtypemb1;
                    $qtylainnya = $masuk->qtylainnya1;
                    $qtyretur = $masuk->qtyretur1;

                    $totqtypemb += $masuk->qtypemb1;
                    $totqtylainnya += $masuk->qtylainnya1;
                    $totqtyretur += $masuk->qtyretur1;
                    $totqtyunitmasuk += $masuk->qty_berat;

                }else{
                    $qtymasuk = 0;
                    $qtypemb =0;
                    $qtylainnya = 0;
                    $qtyretur = 0;

                    $totqtypemb +=0;
                    $totqtylainnya += 0;
                    $totqtyretur += 0;
                    $totqtyunitmasuk += 0;


                }

                if($keluar != null){
                    $qtykeluar = $keluar->qty_berat;
                    $qtyprod = $keluar->qtyprod1;
                    $qtyseas = $keluar->qtyseas1;
                    $qtypdqc = $keluar->qtypdqc1;
                    $qtylain = $keluar->qtylain1;
                    $qtysus = $keluar->qtysus1;
                    $qtycabang = $keluar->qtycabang1;

                    $totqtypro += $keluar->qtyprod1;
                    $totqtyseas += $keluar->qtyseas1;
                    $totqtypdqc += $keluar->qtypdqc1;
                    $totqtylain += $keluar->qtylain1;
                    $totqtysus += $keluar->qtysus1;
                    $totqtycabang += $keluar->qtycabang1;
                    $totqtyunitkeluar += $keluar->qty_berat;

                }else{
                    $qtykeluar = 0;
                    $qtyprod = 0;
                    $qtyseas = 0;
                    $qtypdqc = 0;
                    $qtylain = 0;
                    $qtysus = 0;
                    $qtycabang = 0;

                    $totqtypro += 0;
                    $totqtyseas += 0;
                    $totqtypdqc += 0;
                    $totqtylain += 0;
                    $totqtysus += 0;
                    $totqtycabang += 0;
                    $totqtyunitkeluar += 0;

                }






                // $qtymasukberat = $masuk->qtypemb1 + $masuk->qtylainnya1 + $masuk->qtyretur1;
                // $qtykeluarberat = $keluar->qtyprod1 + $keluar->qtyseas1 + $keluar->qtypdqc1 + $keluar->qtylain1 + $keluar->qtysus1 + $keluar->qtycabang1;
                // $hasilqtyberat = $qtymasukberat - $qtykeluarberat;
                // $saldoakhir2 = $saldoakhirberat + $hasilqtyberat;
                // $saldoakhir1 = $saldoawal + $masuk->qty_berat - $keluar->qty_berat;
                } else {
                if($masuk != null){
                    $qtymasuk = $masuk->qty_unit;
                    $qtypemb = $masuk->qtypemb2;
                    $qtylainnya = $masuk->qtylainnya2;
                    $qtyretur = $masuk->qtyretur2;

                    $totqtypemb += $masuk->qtypemb2;
                    $totqtylainnya += $masuk->qtylainnya2;
                    $totqtyretur += $masuk->qtyretur2;
                    $totqtyunitmasuk += $masuk->qty_unit;
                }else{
                    $qtymasuk = 0;
                    $qtypemb = 0;
                    $qtylainnya = 0;
                    $qtyretur = 0;

                    $totqtypemb += 0;
                    $totqtylainnya += 0;
                    $totqtyretur += 0;
                    $totqtyunitmasuk += 0;
                }

                if($keluar != null){
                    $qtykeluar = $keluar->qty_unit;
                    $qtyprod = $keluar->qtyprod2;
                    $qtyseas = $keluar->qtyseas2;
                    $qtypdqc = $keluar->qtypdqc2;
                    $qtylain = $keluar->qtylain2;
                    $qtysus = $keluar->qtysus2;
                    $qtycabang = $keluar->qtycabang2;

                    $totqtypro += $keluar->qtyprod2;
                    $totqtyseas += $keluar->qtyseas2;
                    $totqtypdqc += $keluar->qtypdqc2;
                    $totqtylain += $keluar->qtylain2;
                    $totqtysus += $keluar->qtysus2;
                    $totqtycabang += $keluar->qtycabang2;
                    $totqtyunitkeluar += $keluar->qty_unit;

                }else{
                    $qtykeluar = 0;
                    $qtyprod = 0;
                    $qtyseas = 0;
                    $qtypdqc = 0;
                    $qtylain = 0;
                    $qtysus = 0;
                    $qtycabang = 0;

                    $totqtypro += 0;
                    $totqtyseas += 0;
                    $totqtypdqc += 0;
                    $totqtylain += 0;
                    $totqtysus += 0;
                    $totqtycabang += 0;
                    $totqtyunitkeluar += 0;
                }

                }

                $saldoakhir = $saldoakhir + $qtymasuk - $qtykeluar;
                $qtyunitmasuk = $masuk != null ? $masuk->qty_unit : 0;
                $qtyunitkeluar = $keluar != null ? $keluar->qty_unit : 0;
                $saldoakhirunit = $saldoakhirunit + $qtyunitmasuk - $qtyunitkeluar;


            ?>
            <tr style="color:black; font-size:14;">
                <td><?php echo $dari; ?></td>
                <td align="right">
                    <?php
                    if (isset($masuk->qty_unit) and $masuk->qty_unit != "0") {
                        echo desimal($masuk->qty_unit);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($keluar->qty_unit) and $keluar->qty_unit != "0") {
                        echo desimal($keluar->qty_unit);
                    }
                    ?>
                </td>
                <td align="right"><?php echo desimal($saldoakhirunit); ?></td>
                <td align="right"></td>
                <td align="right">
                    <?php
                    if (isset($qtypemb) and $qtypemb != "0") {
                        echo desimal($qtypemb);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($qtylainnya) and $qtylainnya != "0") {
                        echo desimal($qtylainnya);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($qtyretur) and $qtyretur != "0") {
                        echo desimal($qtyretur);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($qtyprod) and $qtyprod != "0") {
                        echo desimal($qtyprod);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($qtyseas) and $qtyseas != "0") {
                        echo desimal($qtyseas);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($qtypdqc) and $qtypdqc != "0") {
                        echo desimal($qtypdqc);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($qtysus) and $qtysus != "0") {
                        echo desimal($qtysus);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($qtycabang) and $qtycabang != '0') {
                        echo desimal($qtycabang);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($qtylain) and $qtylain != "0") {
                        echo desimal($qtylain);
                    }
                    ?>
                </td>
                <td align="right"><?php echo desimal($saldoakhir); ?></td>
            </tr>
            <?php
                $dari = date("Y-m-d", strtotime("+1 day", strtotime($dari))); //looping tambah 1 date
            }
            ?>
        </tbody>
        <tfoot>
            <tr bgcolor="#31869b">
                <th colspan="5" style="color:white; font-size:14;">TOTAL</th>
                <th style="color:white; font-size:14;"><?php echo desimal($totqtypemb); ?></th>
                <th style="color:white; font-size:14;"><?php echo desimal($totqtylainnya); ?></th>
                <th style="color:white; font-size:14;"><?php echo desimal($totqtyretur); ?></th>
                <th style="color:white; font-size:14;"><?php echo desimal($totqtypro); ?></th>
                <th style="color:white; font-size:14;"><?php echo desimal($totqtyseas); ?></th>
                <th style="color:white; font-size:14;"><?php echo desimal($totqtypdqc); ?></th>
                <th style="color:white; font-size:14;"><?php echo desimal($totqtysus); ?></th>
                <th style="color:white; font-size:14;"><?php echo desimal($totqtycabang); ?></th>
                <th style="color:white; font-size:14;"><?php echo desimal($totqtylain); ?></th>
                <th style="color:white; font-size:14;"></th>
            </tr>
        </tfoot>

    </table>

</body>
</html>
