<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Rekap Persediaan Barang Jadi {{ date("d-m-y") }}</title>
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
        REKAPITULASI PERSEDIAAN BARANG ALL CABANG<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:100%;  margin-bottom: 30px" border="1">
        <thead>
            <tr>

                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">No</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">Barang/Produk</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">Saldo Awal</th>
                <th colspan="6" bgcolor="#28a745" style="color:white; font-size:12;">PENERIMAAN</th>
                <th colspan="8" bgcolor="#c7473a" style="color:white; font-size:12;">PENGELUARAN</th>
                <th rowspan="2" rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">SALDO AKHIR</th>
            </tr>
            <tr>
                <th bgcolor="#28a745" colspan="2" style="color:white; font-size:12;">PRODUKSI</th>
                <th bgcolor="#28a745" colspan="2" style="color:white; font-size:12;">REPACK</th>
                <th bgcolor="#28a745" colspan="2" style="color:white; font-size:12;">LAIN LAIN</th>
                <th bgcolor="#c7473a" colspan="3" style="color:white; font-size:12;">KIRIM KE CABANG</th>
                <th bgcolor="#c7473a" colspan="3" style="color:white; font-size:12;">REJECT</th>
                <th bgcolor="#c7473a" colspan="2" style="color:white; font-size:12;">LAIN LAIN</th>
            </tr>

        </thead>
        <tbody>
            <?php

            $no = 1;
            $totalsaldoawal  = 0;
            $totalsuratjalan = 0;
            $totalfsthp 	 = 0;
            $totlarepack     = 0;
            $totallainlain_in     = 0;
            $totalreject  	 = 0;
            $totallainlain_out  	 = 0;
            $totalsaldoakhir = 0;
            foreach ($mutasigudangpusat as $m) {

                $saldoakhir 		= $m->saldoawal + ($m->jmlfsthp + $m->jmlrepack + $m->jmllainlain_in) - ($m->jmlsuratjalan + $m->jmlreject + $m->jmllainlain_out);
                $totalsaldoawal		= $totalsaldoawal + $m->saldoawal;
                $totalsuratjalan 	= $totalsuratjalan + $m->jmlsuratjalan;
                $totalfsthp 		= $totalfsthp + $m->jmlfsthp;
                $totlarepack 		= $totlarepack + $m->jmlrepack;
                $totallainlain_in   = $totallainlain_in + $m->jmllainlain_in;
                $totallainlain_out 	= $totallainlain_out + $m->jmllainlain_out;
                $totalreject 		= $totalreject + $m->jmlreject;
                $totalsaldoakhir 	= $totalsaldoakhir + $saldoakhir;

            ?>
            <tr style="font-weight: bold; font-size:11px">
                <td><?php echo $no; ?></td>
                <td><?php echo $m->nama_barang; ?></td>
                <td align="right"><?php if ($m->saldoawal != 0) {
                                                            echo rupiah($m->saldoawal);
                                                        } ?></td>
                <td align="right" colspan="2"><?php if ($m->jmlfsthp != 0) {
                                                            echo rupiah($m->jmlfsthp);
                                                        } ?></td>
                <td align="right" colspan="2"><?php if ($m->jmlrepack != 0) {
                                                            echo rupiah($m->jmlrepack);
                                                        } ?></td>
                <td align="right" colspan="2"><?php if ($m->jmllainlain_in != 0) {
                                                            echo rupiah($m->jmllainlain_in);
                                                        } ?></td>
                <td align="right" colspan="3"><?php if ($m->jmlsuratjalan != 0) {
                                                            echo rupiah($m->jmlsuratjalan);
                                                        } ?></td>
                <td align="right" colspan="3"><?php if ($m->jmlreject != 0) {
                                                            echo rupiah($m->jmlreject);
                                                        } ?></td>
                <td align="right" colspan="2"><?php if ($m->jmllainlain_out != 0) {
                                                            echo rupiah($m->jmllainlain_out);
                                                        } ?></td>
                <td align="right"><?php echo rupiah($saldoakhir);  ?></td>
            </tr>
            <?php $no++;
            } ?>
        </tbody>
        <tr bgcolor="#31869b" style="color:white; font-weight:bold">
            <th colspan="2">TOTAL GUDANG PUSAT</th>
            <th style="text-align: right"><?php echo rupiah($totalsaldoawal); ?></th>
            <th style="text-align: right" colspan="2"><?php echo rupiah($totalfsthp); ?></th>
            <th style="text-align: right" colspan="2"><?php echo rupiah($totlarepack); ?></th>
            <th style="text-align: right" colspan="2"><?php echo rupiah($totallainlain_in); ?></th>
            <th style="text-align: right" colspan="3"><?php echo rupiah($totalsuratjalan); ?></th>
            <th style="text-align: right" colspan="3"><?php echo rupiah($totalreject); ?></th>
            <th style="text-align: right" colspan="2"><?php echo rupiah($totallainlain_out); ?></th>
            <th style="text-align: right"><?php echo rupiah($totalsaldoakhir); ?></th>
        </tr>
        <thead bgcolor="#024a75" style="color:white;">
            <tr>
                <th rowspan="2" bgcolor="#024a75" style="font-size:10px !important">KODE PRODUK</th>
                <th rowspan="2" bgcolor="#024a75" style="font-size:10px !important">PRODUK</th>
                <th rowspan="2" bgcolor="#024a75" style="font-size:10px !important">SALDO AWAL</th>
                <th colspan="6" bgcolor="#22a538" style="font-size:10px !important">PENERIMAAN</th>
                <th colspan="8" bgcolor="#c7473a" style="font-size:10px !important">PENGELUARAN</th>
                <th rowspan="2" bgcolor="#024a75" style="font-size:10px !important">SALDO AKHIR</th>
            </tr>
            <tr>
                <th bgcolor="#22a538" style="font-size:10px !important">PUSAT</th>
                <th bgcolor="#2e73c6" style="font-size:10px !important">TRANSIT IN</th>
                <th bgcolor="#22a538" style="font-size:10px !important">RETUR</th>
                <th bgcolor="#22a538" style="font-size:10px !important">LAIN LAIN</th>
                <th bgcolor="#22a538" style="font-size:10px !important">REPACK</th>
                <th bgcolor="#22a538" style="font-size:10px !important">PENYESUAIAN</th>
                <th bgcolor="#c7473a" style="font-size:10px !important">PENJUALAN</th>
                <th bgcolor="#c7473a" style="font-size:10px !important">PROMOSI</th>
                <th bgcolor="#c7473a" style="font-size:10px !important">REJECT PASAR</th>
                <th bgcolor="#c7473a" style="font-size:10px !important">REJECT MOBIL</th>
                <th bgcolor="#c7473a" style="font-size:10px !important">REJECT GUDANG</th>
                <th bgcolor="#2e73c6" style="font-size:10px !important">TRANSIT OUT</th>
                <th bgcolor="#c7473a" style="font-size:10px !important">LAIN LAIN</th>
                <th bgcolor="#c7473a" style="font-size:10px !important">PENYESUAIAN</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($rekap as $key => $d) {
                $kode_cabang = @$rekap[$key + 1]->kode_cabang;
                $saldoawal = $d->saldo_awal / $d->isipcsdus;
                $pusat = $d->pusat / $d->isipcsdus;
                $transit_in = $d->transit_in / $d->isipcsdus;
                $retur = $d->retur / $d->isipcsdus;
                $lainlain_in = $d->lainlain_in / $d->isipcsdus;
                $repack = $d->repack / $d->isipcsdus;
                $penyesuaian_in = $d->penyesuaian_in / $d->isipcsdus;
                $penjualan = $d->penjualan / $d->isipcsdus;
                $promosi = $d->promosi / $d->isipcsdus;
                $reject_pasar = $d->reject_pasar / $d->isipcsdus;
                $reject_mobil = $d->reject_mobil / $d->isipcsdus;
                $reject_gudang = $d->reject_gudang / $d->isipcsdus;
                $transit_out = $d->transit_out / $d->isipcsdus;
                $lainlain_out = $d->lainlain_out / $d->isipcsdus;
                $penyesuaian_out = $d->penyesuaian_out / $d->isipcsdus;

                $sisamutasi = ($saldoawal + $pusat + $transit_in + $retur + $lainlain_in + $repack + $penyesuaian_in) - ($penjualan + $promosi + $reject_pasar + $reject_mobil + $reject_gudang + $transit_out + $lainlain_out + $penyesuaian_out);
            ?>
            <tr>
                <td><?php echo $d->kode_produk ?></td>
                <td><?php echo $d->nama_barang ?></td>
                <td align="right"><?php echo desimal($saldoawal) ?></td>
                <td align="right"><?php echo desimal($pusat) ?></td>
                <td align="right">
                    <?php
                        if (!empty($transit_in)) {
                            echo desimal($transit_in);
                        }
                        ?>
                </td>
                <td align="right">
                    <?php
                        if (!empty($retur)) {
                            echo desimal($retur);
                        }
                        ?>
                </td>
                <td align="right">
                    <?php
                        if (!empty($lainlain_in)) {
                            echo desimal($lainlain_in);
                        }
                        ?>
                </td>
                <td align="right">
                    <?php
                        if (!empty($repack)) {
                            echo desimal($repack);
                        }
                        ?>
                </td>
                <td align="right">
                    <?php
                        if (!empty($penyesuaian_in)) {
                            echo desimal($penyesuaian_in);
                        }
                        ?>
                </td>
                <td align="right">
                    <?php
                        if (!empty($penjualan)) {
                            echo desimal($penjualan);
                        }
                        ?>
                </td>
                <td align="right">
                    <?php
                        if (!empty($promosi)) {
                            echo desimal($promosi);
                        }
                        ?>
                </td>
                <td align="right">
                    <?php
                        if (!empty($reject_pasar)) {
                            echo desimal($reject_pasar);
                        }
                        ?>
                </td>
                <td align="right">
                    <?php
                        if (!empty($reject_mobil)) {
                            echo desimal($reject_mobil);
                        }
                        ?>
                </td>
                <td align="right">
                    <?php
                        if (!empty($reject_gudang)) {
                            echo desimal($reject_gudang);
                        }
                        ?>
                </td>
                <td align="right">
                    <?php
                        if (!empty($transit_out)) {
                            echo desimal($transit_out);
                        }
                        ?>
                </td>
                <td align="right">
                    <?php
                        if (!empty($lainlain_out)) {
                            echo desimal($lainlain_out);
                        }
                        ?>
                </td>
                <td align="right">
                    <?php
                        if (!empty($penyesuaian_out)) {
                            echo desimal($penyesuaian_out);
                        }
                        ?>
                </td>
                <td align="right">
                    <?php
                        if (!empty($sisamutasi)) {
                            echo desimal($sisamutasi);
                        }
                        ?>
                </td>
            </tr>
            <?php
                if ($kode_cabang != $d->kode_cabang) {
                    echo '
                <tr bgcolor="#31869b" style="color:white; font-weight:bold">
                    <td>' . $d->kode_cabang . '</td>
                    <td colspan="17" ></td>
                </tr>';
                }
                $kode_cabang = $d->kode_cabang;
            }
            ?>
        </tbody>
    </table>
</body>
</html>
