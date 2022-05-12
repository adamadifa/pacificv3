<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mutasi DPB {{ date("d-m-y") }}</title>
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
        MUTASI DPB<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        <table>
            <tr>
                <td>KODE PRODUK</td>
                <td>{{ $produk->kode_produk }}</td>
            </tr>
            <tr>
                <td>NAMA PRODUK</td>
                <td>{{ $produk->nama_barang }}</td>
            </tr>
        </table>
        <br>
    </b>
    <br>
    <table style="width:120%">
        <tr>
            <td valign="top">
                <table class="datatable3" border="1" style="width:100%">
                    <thead>
                        <tr>
                            <th bgcolor="#024a75" style="color:white; font-size:12;" colspan="4">PENERIMAAN PUSAT</th>
                        </tr>
                        <tr>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">TANGGAL</th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">BUKTI</th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">JUMLAH</th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                    $total = 0;
                    foreach($suratjalan as $m){
                    $jumlah = $m->jumlah / $m->isipcsdus;
                    if($m->inout_good=='IN'){
                        $color_sa = "#28a745";
                        $operator = "";
                    }else{
                        $color_sa= "#c7473a";
                        $operator = "-";
                    }
                    $jml = $operator.$jumlah;
                    $total = $total + $jml;
                ?>
                        <tr>
                            <td><?php echo date("d-m-Y",strtotime($m->tgl_mutasi_gudang_cabang)); ?></td>
                            <td><?php echo $m->no_mutasi_gudang_cabang; ?></td>
                            <td style="text-align:right; background-color:<?php echo $color_sa; ?>"><?php echo desimal($jumlah); ?></td>
                            <td>
                                <?php
                                if($m->jenis_mutasi !="SURAT JALAN"){
                                    echo $m->jenis_mutasi." ".$m->no_suratjalan;
                                }
                                ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th bgcolor="#024a75" style="color:white; font-size:12;" colspan="2">TOTAL</th>
                            <th style="text-align:right; background-color:#28a745"><?php echo desimal($total); ?></th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;" colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </td>
            <td valign="top">
                <table class="datatable3" border="1" style="width:100%">
                    <thead>
                        <tr>
                            <th bgcolor="#024a75" style="color:white; font-size:12;" colspan="8">MUTASI DPB</th>
                        </tr>
                        <tr>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">NO DPB</th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">SALESMAN</th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">TUJUAN</th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">NO KENDARAAN</th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">TANGGAL PENGAMBILAN</th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">JUMLAH PENGAMBILAN</th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">TANGGAL PENGEMBALIAN</th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">JUMLAH PENGEMBALIAN</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $jmlpengambilan = 0;
                        $jmlpengembalian = 0;
                        foreach($dpbpengambilan as $m){
                        $jmlpengambilan = $jmlpengambilan + $m->jml_pengambilan;
                        $jmlpengembalian = $jmlpengembalian + $m->jml_pengembalian;
                    ?>
                        <tr>
                            <td><?php echo $m->no_dpb; ?></td>
                            <td><?php echo $m->nama_karyawan; ?></td>
                            <td><?php echo $m->tujuan; ?></td>
                            <td><?php echo $m->no_kendaraan; ?></td>
                            <td><?php if(!empty($m->tgl_pengambilan)) {echo date("d-m-Y",strtotime($m->tgl_pengambilan)); } ?></td>
                            <td style="text-align:right; background-color:#c7473a"><?php echo desimal($m->jml_pengambilan); ?></td>
                            <td><?php if(!empty($m->tgl_pengembalian)) {echo date("d-m-Y",strtotime($m->tgl_pengembalian)); } ?></td>
                            <td style="text-align:right; background-color:#28a745"><?php echo desimal($m->jml_pengembalian); ?></td>

                            <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th bgcolor="#024a75" style="color:white; font-size:12;" colspan="5">TOTAL</th>
                            <th style="text-align:right; background-color:#c7473a"><?php echo desimal($jmlpengambilan); ?></th>
                            <th></th>
                            <th style="text-align:right; background-color:#28a745"><?php echo desimal($jmlpengembalian); ?></th>

                        </tr>
                    </tfoot>
                </table>
            </td>
            <td valign="top">
                <table class="datatable3" border="1" style="width:100%">
                    <thead>
                        <tr>
                            <th bgcolor="#024a75" style="color:white; font-size:12;" colspan="4">REJECT</th>
                        </tr>
                        <tr>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">TANGGAL</th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">BUKTI</th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">JUMLAH</th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totalreject = 0;
                        foreach($reject as $m){
                        $jumlah = $m->jumlah / $m->isipcsdus;
                        $totalreject = $totalreject + $jumlah;
                    ?>
                        <tr>
                            <td><?php echo date("d-m-Y",strtotime($m->tgl_mutasi_gudang_cabang)); ?></td>
                            <td><?php echo $m->no_mutasi_gudang_cabang; ?></td>
                            <td style="text-align:right; background-color:#c7473a"><?php echo desimal($jumlah); ?></td>
                            <td><?php echo $m->jenis_mutasi;?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th bgcolor="#024a75" style="color:white; font-size:12;" colspan="2">TOTAL</th>
                            <th style="text-align:right; background-color:#c7473a"><?php echo desimal($totalreject); ?></th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;" colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </td>
            <td valign="top">
                <table class="datatable3" border="1" style="width:100%">
                    <thead>
                        <tr>
                            <th bgcolor="#024a75" style="color:white; font-size:12;" colspan="4">REPACK</th>
                        </tr>
                        <tr>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">TANGGAL</th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">BUKTI</th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">JUMLAH</th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                      $totalrepack = 0;
                      foreach($repack as $m){
                        $jumlah = $m->jumlah / $m->isipcsdus;


                        $totalrepack = $totalrepack + $jumlah;
                    ?>
                        <tr>
                            <td><?php echo date("d-m-Y",strtotime($m->tgl_mutasi_gudang_cabang)); ?></td>
                            <td><?php echo $m->no_mutasi_gudang_cabang; ?></td>
                            <td style="text-align:right; background-color:#28a745"><?php echo desimal($jumlah); ?></td>
                            <td>
                                <?php
                            echo $m->jenis_mutasi;
                          ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th bgcolor="#024a75" style="color:white; font-size:12;" colspan="2">TOTAL</th>
                            <th style="text-align:right; background-color:#28a745"><?php echo desimal($totalrepack); ?></th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;" colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </td>
            <td valign="top">
                <table class="datatable3" border="1" style="width:100%">
                    <thead>
                        <tr>
                            <th bgcolor="#024a75" style="color:white; font-size:12;" colspan="4">PENYESUAIN</th>
                        </tr>
                        <tr>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">TANGGAL</th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">BUKTI</th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">JUMLAH</th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;">KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                      $totalpenyesuaian = 0;
                      foreach($penyesuaian as $m){
                        $jumlah = $m->jumlah / $m->isipcsdus;
                        if($m->inout_good=='IN'){
                                    $color_sa = "#28a745";
                          $operator = "";
                                }else{
                                    $color_sa= "#c7473a";
                          $operator = "-";
                                }

                        $jml = $operator.$jumlah;

                        $totalpenyesuaian = $totalpenyesuaian + $jml;
                    ?>
                        <tr>
                            <td><?php echo DateToIndo2($m->tgl_mutasi_gudang_cabang); ?></td>
                            <td><?php echo $m->no_mutasi_gudang_cabang; ?></td>
                            <td style="text-align:right; background-color:<?php echo $color_sa; ?>"><?php echo desimal($jumlah); ?></td>
                            <td>
                                <?php

                            echo $m->jenis_mutasi;

                          ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th bgcolor="#024a75" style="color:white; font-size:12;" colspan="2">TOTAL</th>
                            <th style="text-align:right; background-color:#28a745"><?php echo desimal($totalpenyesuaian); ?></th>
                            <th bgcolor="#024a75" style="color:white; font-size:12;" colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </td>
            <td valign="top">
                <table class="datatable3" border="1" style="width:100%">
                    <thead>
                        <tr>
                            <th bgcolor="#024a75" style="color:white; font-size:12;" colspan="2">REKAP</th>
                        </tr>
                        <tr>
                            <th>SALDO AWAL</th>
                            <th style="text-align:right; background-color:#28a745"><?php echo desimal($saldoawal); ?></th>
                        </tr>
                        <tr>
                            <th>PENERIMAAN PUSAT</th>
                            <th style="text-align:right; background-color:#28a745"><?php echo desimal($total); ?></th>
                        </tr>
                        <tr>
                            <th>PENGAMBILAN DPB</th>
                            <th style="text-align:right; background-color:#c7473a"><?php echo desimal($jmlpengambilan); ?></th>
                        </tr>
                        <tr>

                        <tr>
                            <th>PENGEMBALIAN</th>
                            <th style="text-align:right; background-color:#28a745"><?php echo desimal($jmlpengembalian); ?></th>
                        </tr>
                        <tr>
                            <th>REJECT</th>
                            <th style="text-align:right; background-color:#c7473a"><?php echo desimal($totalreject); ?></th>
                        </tr>
                        <tr>
                            <th>REPACK</th>
                            <th style="text-align:right; background-color:#28a745"><?php echo desimal($totalrepack); ?></th>
                        </tr>
                        <tr>
                            <th>PENYESUAIAN</th>
                            <th style="text-align:right; background-color:#28a745"><?php echo desimal($totalpenyesuaian); ?></th>
                        </tr>
                        <tr>
                            <th>SISA STOK</th>
                            <th style="text-align:right; background-color:#28a745"><?php echo desimal($saldoawal + $total - $jmlpengambilan + $jmlpengembalian -$totalreject+$totalrepack+$totalpenyesuaian); ?></th>
                        </tr>
                    </thead>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
