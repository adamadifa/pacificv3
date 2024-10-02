<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Persediaan Gudang Cabang {{ date('d-m-y') }}</title>
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
</head>

<body>
    <b style="font-size:14px;">
        MAKMUR PERMATA
        REKAPITULASI PERSEDIAAN {{ $barang->nama_barang }}<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:100%" border="1">
        <thead>
            <tr>
                <th rowspan="3" bgcolor="#024a75" style="color:white; font-size:14;">TANGGAL</th>
                <!-- <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:14;">BTB</th> -->
                <th rowspan="2" colspan="3" bgcolor="green" style="color:white; font-size:14;">SALDO AWAL</th>
                <th colspan="6" bgcolor="#024a75" style="color:white; font-size:14;">MASUK</th>
                <th colspan="6" bgcolor="#024a75" style="color:white; font-size:14;">KELUAR</th>
                <th rowspan="2" colspan="3" bgcolor="green" style="color:white; font-size:14;">SALDO AKHIR</th>
            </tr>
            <tr>
                <th bgcolor="green" style="color:white; font-size:14;" colspan="3">PEMBELIAN</th>
                <th bgcolor="green" style="color:white; font-size:14;" colspan="3">PENERIMAAN LAINNYA</th>
                <th bgcolor="green" style="color:white; font-size:14;" colspan="3">PEMAKAIAN</th>
                <th bgcolor="green" style="color:white; font-size:14;" colspan="3">PEMAKAIAN LAINNYA</th>
            </tr>
            <tr>
                <th bgcolor="#024a75" style="color:white; font-size:14;">QTY</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">HARGA</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">JUMLAH</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">QTY</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">HARGA</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">JUMLAH</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">QTY</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">HARGA</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">JUMLAH</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">QTY</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">HARGA</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">JUMLAH</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">QTY</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">HARGA</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">JUMLAH</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">QTY</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">HARGA</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @php
                $saldoawalqty = $saldoawal != null ? $saldoawal->qty : 0;
                $saldoawalharga = $saldoawal != null ? $saldoawal->harga : 0;
                $totalqtysaldoawal = 0;
                $totaljmlhsaldoawal = 0;
                $totalqtypemakaian = 0;
                $totalqtykeluarlainnya = 0;
                $totaljmlpembelian = 0;
                $totalqtypembelian = 0;
                $totaljmllainnya = 0;
                $totalqtylainnya = 0;
                $totaljmlhpemakaian = 0;
                $totaljmlhkeluarlainnya = 0;
                $totalqtysaldoakhir = 0;
                $totaljmlhsaldoakhir = 0;
            @endphp
            <?php
            while (strtotime($dari) <= strtotime($sampai)) {
                $masuk = DB::table('detail_pemasukan_bb')
                ->selectRaw('
                SUM( IF( pemasukan_bb.status = 1 , qty ,0 )) AS qtypemb,
                SUM( IF( pemasukan_bb.status = 2 , qty ,0 )) AS qtylainya,
                SUM( IF( pemasukan_bb.status = 2 , pemasukan_bb.harga ,0 )) AS hargalainnya,
                SUM(penyesuaian) as penyesuaian,
	            SUM(db.harga) AS harga')
                ->join('pemasukan_bb','detail_pemasukan_bb.nobukti_pemasukan','=','pemasukan_bb.nobukti_pemasukan')
                ->leftJoin(
                DB::raw("(
                    SELECT pembelian.nobukti_pembelian,kode_barang,harga FROM detail_pembelian
                    INNER JOIN pembelian ON pembelian.nobukti_pembelian=detail_pembelian.nobukti_pembelian
                    WHERE  tgl_pembelian = '$dari' AND kode_barang = '$kode_barang'
                    GROUP BY pembelian.nobukti_pembelian,kode_barang,harga
                ) db"),
                    function ($join) {
                        $join->on('pemasukan_bb.nobukti_pemasukan', '=', 'db.nobukti_pembelian');
                    }
                )
                ->where('tgl_pemasukan',$dari)
                ->where('detail_pemasukan_bb.kode_barang',$kode_barang)
                ->groupByRaw('tgl_pemasukan')
                ->first();

                $keluar = DB::table('detail_pengeluaran_bb')
                ->selectRaw("tgl_pengeluaran,
                SUM( IF( pengeluaran_bb.status = 1 , qty ,0 )) AS qtypemakaian,
                SUM( IF( pengeluaran_bb.status = 2 , qty ,0 )) AS qtykeluarlainnya")
                ->join('pengeluaran_bb','detail_pengeluaran_bb.nobukti_pengeluaran','=','pengeluaran_bb.nobukti_pengeluaran')
                ->where('tgl_pengeluaran',$dari)
                ->where('detail_pengeluaran_bb.kode_barang',$kode_barang)
                ->groupByRaw('tgl_pengeluaran,qty')
                ->first();

                $qtypembelian = $masuk != null ? $masuk->qtypemb : 0;
                $qtylainnya   = $masuk != null ? $masuk->qtylainya : 0;
                $hargamasuk   = $masuk != null ? $masuk->harga : 0;
                $penyesuaian  = $masuk != null ? $masuk->penyesuaian : 0;
                $hargalainnya = $masuk != null ? $masuk->hargalainnya : 0;
                $jmlpembelian = $hargamasuk * $qtypembelian + $penyesuaian;
                $jmllainnya   = $hargalainnya * $qtylainnya;

                if(substr($dari,8,2) == '01'){
                    $qtysaldoawal   = $saldoawalqty;
                    $hargasaldoawal = $qtysaldoawal != 0 ? $saldoawalharga / $qtysaldoawal : 0;
                    $jmlhsaldoawal  = $qtysaldoawal * $hargasaldoawal;
                    $qtypemakaian   =  $keluar != null ? $keluar->qtypemakaian : 0;
                    $qtykeluarlainnya   =  $keluar != null ? $keluar->qtykeluarlainnya : 0;
                    $hargakeluar    = ($jmlhsaldoawal+$jmlpembelian+$jmllainnya)/($qtysaldoawal+$qtypembelian+$qtylainnya+0.000000001);
                    $jmlhpemakaian  = $hargakeluar * $qtypemakaian;
                    $jmlhkeluarlainnya  = $hargakeluar * $qtykeluarlainnya;
                }else{
                    $qtysaldoawal   = $qtysaldoakhir;
                    $hargasaldoawal = $hargakeluar;
                    $jmlhsaldoawal  = $qtysaldoawal * $hargasaldoawal;
                    $qtypemakaian   =  $keluar != null ? $keluar->qtypemakaian : 0;
                    $qtykeluarlainnya   =  $keluar != null ? $keluar->qtykeluarlainnya : 0;
                    $hargakeluar    = ($jmlhsaldoawal+$jmlpembelian+$jmllainnya)/($qtysaldoawal+$qtypembelian+$qtylainnya+0.000000001);
                    $jmlhpemakaian  = $hargakeluar * $qtypemakaian;
                    $jmlhkeluarlainnya  = $hargakeluar * $qtykeluarlainnya;
                }

                $qtysaldoakhir    = $qtysaldoawal+$qtypembelian+$qtylainnya-$qtypemakaian-$qtykeluarlainnya;
                $jmlhsaldoakhir   = $qtysaldoakhir*$hargakeluar;

                $totalqtysaldoawal    += $qtysaldoawal;
                $totaljmlhsaldoawal   += $jmlhsaldoawal;
                $totalqtypemakaian    += $qtypemakaian;
                $totalqtykeluarlainnya    += $qtykeluarlainnya;
                $totaljmlhpemakaian   += $jmlhpemakaian;
                $totaljmlhkeluarlainnya   += $jmlhkeluarlainnya;
                $totalqtypembelian    += $qtypembelian;
                $totaljmlpembelian    += $jmlpembelian;
                $totalqtylainnya      += $qtylainnya;
                $totaljmllainnya      += $jmllainnya;
                $totalqtysaldoakhir   += $qtysaldoakhir;
                $totaljmlhsaldoakhir  += $jmlhsaldoakhir;
            ?>
            <tr style="color:black; font-size:14;">
                <td><?php echo $dari; ?></td>
                <td align="right"><?php echo desimal($qtysaldoawal); ?></td>
                <td align="right"><?php echo desimal($hargasaldoawal); ?></td>
                <td align="right"><?php echo desimal($jmlhsaldoawal); ?></td>
                <td align="right">
                    <?php
                    if (isset($qtypembelian) and $qtypembelian != '0') {
                        echo desimal($qtypembelian);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($hargamasuk) and $hargamasuk != '0') {
                        echo desimal($hargamasuk);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($qtypembelian) and $qtypembelian != '0') {
                        echo desimal($jmlpembelian);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($qtylainnya) and $qtylainnya != '0') {
                        echo desimal($qtylainnya);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($qtylainnya) and $qtylainnya != '0') {
                        echo desimal($hargalainnya);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($qtylainnya) and $qtylainnya != '0') {
                        echo desimal($jmllainnya);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($qtypemakaian) and $qtypemakaian != '0') {
                        echo desimal($qtypemakaian);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($qtypemakaian) and $qtypemakaian != '0') {
                        echo desimal($hargakeluar);
                    }
                    ?>
                    <?php
                    echo desimal(($jmlhsaldoawal + $jmlpembelian + $jmllainnya) / ($qtysaldoawal + $qtypembelian + $qtylainnya + 0.000000001));
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($qtypemakaian) and $qtypemakaian != '0') {
                        echo desimal($jmlhpemakaian);
                    }
                    ?>
                </td>

                <td align="right">
                    <?php
                    if (isset($qtykeluarlainnya) and $qtykeluarlainnya != '0') {
                        echo desimal($qtykeluarlainnya);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($qtykeluarlainnya) and $qtykeluarlainnya != '0') {
                        echo desimal($hargakeluar);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($qtykeluarlainnya) and $qtykeluarlainnya != '0') {
                        echo desimal($jmlhkeluarlainnya);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($qtysaldoakhir) and $qtysaldoakhir != '0') {
                        echo desimal($qtysaldoakhir);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($qtysaldoakhir) and $qtysaldoakhir != '0') {
                        echo desimal($hargakeluar);
                    }
                    ?>
                </td>
                <td align="right">
                    <?php
                    if (isset($jmlhsaldoakhir) and $jmlhsaldoakhir != '0') {
                        echo desimal($jmlhsaldoakhir);
                    }
                    ?>
                </td>
            </tr>
            <?php
                $dari = date("Y-m-d", strtotime("+1 day", strtotime($dari))); //looping tambah 1 date
                }
            ?>
        </tbody>
        <tfoot>
            <tr bgcolor="#31869b">
                <th colspan="" style="color:white; font-size:14;">TOTAL</th>
                <th style="color:white; font-size:14;"></th>
                <th style="color:white; font-size:14;"></th>
                <th style="color:white; font-size:14;"></th>
                <th style="color:white; font-size:14;"><?php echo desimal($totalqtypembelian); ?></th>
                <th style="color:white; font-size:14;"></th>
                <th style="color:white; font-size:14;"><?php echo desimal($totaljmlpembelian); ?></th>
                <th style="color:white; font-size:14;"><?php echo desimal($totalqtylainnya); ?></th>
                <th style="color:white; font-size:14;"></th>
                <th style="color:white; font-size:14;"><?php echo desimal($totaljmllainnya); ?></th>
                <th style="color:white; font-size:14;"><?php echo desimal($totalqtypemakaian); ?></th>
                <th style="color:white; font-size:14;"></th>
                <th style="color:white; font-size:14;"><?php echo desimal($totaljmlhpemakaian); ?></th>
                <th style="color:white; font-size:14;"><?php echo desimal($totalqtykeluarlainnya); ?></th>
                <th style="color:white; font-size:14;"></th>
                <th style="color:white; font-size:14;"><?php echo desimal($totaljmlhkeluarlainnya); ?></th>
                <th style="color:white; font-size:14;"></th>
                <th style="color:white; font-size:14;"></th>
                <th style="color:white; font-size:14;"></th>

            </tr>
        </tfoot>
    </table>
</body>

</html>
