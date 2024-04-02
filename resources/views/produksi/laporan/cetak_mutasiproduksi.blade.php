<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Akun {{ date('d-m-y') }}</title>
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
        LAPORAN MUTASI PRODUKSI<br>
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
    <table class="datatable3" style="width:80%" border="1">
        <thead>
            <tr>

                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">Tanggal</th>
                <th colspan="3" bgcolor="#024a75" style="color:white; font-size:12;">BUKTI</th>
                <th colspan="2" bgcolor="#28a745" style="color:white; font-size:12;">IN</th>
                <th colspan="2" bgcolor="#c7473a" style="color:white; font-size:12;">OUT</th>
                <th rowspan="2" rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">SALDO AKHIR</th>
            </tr>
            <tr>
                <th bgcolor="#024a75" style="color:white; font-size:12;">BPBJ</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">FSTHP</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">LAIN LAIN</th>
                <th bgcolor="#28a745" style="color:white; font-size:12; width:200px">BARANG HASIL PRODUKSI</th>
                <th bgcolor="#28a745" style="color:white; font-size:12;">LAINNYA</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">GUDANG</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">LAINNYA</th>
            </tr>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th colspan="4">SALDO AWAL</th>
                <th colspan="4"></th>
                <th style="text-align: right">{{ rupiah($saldoawal->saldo_awal) }}</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $saldoakhir 	= $saldoawal->saldo_awal;
                $totalbpbj 		= 0;
                $totalfsthp 	= 0;
                $totalmutasiin  = 0;
                $totalmutasiout = 0;
                foreach ($mutasi as $m){

                    if($m->jenis_mutasi=="BPBJ"){

                        $no_bpbj = $m->no_mutasi_produksi;
                        $jmlbpbj = $m->jumlah;

                    }else{

                        $no_bpbj = "";
                        $jmlbpbj = 0;
                    }


                    if($m->jenis_mutasi=="FSTHP"){

                        $no_fsthp = $m->no_mutasi_produksi;
                        $jmlfsthp = $m->jumlah;
                    }else{

                        $no_fsthp = "";
                        $jmlfsthp = 0;
                    }

                    if($m->jenis_mutasi=="LAIN-LAIN" AND $m->inout=="IN"){

                        $no_mutasi 	 	 = $m->no_mutasi_produksi;
                        $jmlmutasiin 	 = $m->jumlah;
                    }else if($m->jenis_mutasi=="LAIN-LAIN" AND $m->inout=="OUT"){
                        $no_mutasi 		= $m->no_mutasi_produksi;
                        $jmlmutasiout 	= $m->jumlah;
                    }else{
                        $no_mutasi     = "";
                        $jmlmutasiin   = 0;
                        $jmlmutasiout  = 0;
                    }




                    if($m->inout=='IN'){
                        $jumlah  = $m->jumlah;
                    }else{
                        $jumlah = -$m->jumlah;
                    }

                    $saldoakhir 	= $saldoakhir + $jumlah;
                    $totalbpbj		= $totalbpbj + $jmlbpbj;
                    $totalfsthp 	= $totalfsthp + $jmlfsthp;
                    $totalmutasiin	= $totalmutasiin + $jmlmutasiin;
                    $totalmutasiout = $totalmutasiout + $jmlmutasiout;


            ?>
            <tr style="font-weight: bold; font-size:11px">
                <td><?php echo DateToIndo2($m->tgl_mutasi_produksi); ?></td>
                <td><?php echo $no_bpbj; ?></td>
                <td><?php echo $no_fsthp; ?></td>
                <td><?php echo $no_mutasi; ?></td>
                <td align="right"><?php if (!empty($jmlbpbj)) {
                    echo rupiah($jmlbpbj);
                } ?></td>
                <td align="right"><?php if (!empty($jmlmutasiin)) {
                    echo rupiah($jmlmutasiin);
                } ?></td>
                <td align="right"><?php if (!empty($jmlfsthp)) {
                    echo rupiah($jmlfsthp);
                } ?></td>
                <td align="right"><?php if (!empty($jmlmutasiout)) {
                    echo rupiah($jmlmutasiout);
                } ?></td>
                <td align="right"><?php if (!empty($saldoakhir)) {
                    echo rupiah($saldoakhir);
                } ?></td>

            </tr>
            <?php
                }
            ?>
        </tbody>
        <tfoot bgcolor="#024a75" style="color:white; font-size:12;">
            <tr>
                <th colspan="4">TOTAL</th>
                <th style="text-align: right"><?php echo rupiah($totalbpbj); ?></th>
                <th style="text-align: right"><?php echo rupiah($totalmutasiin); ?></th>
                <th style="text-align: right"><?php echo rupiah($totalfsthp); ?></th>
                <th style="text-align: right"><?php echo rupiah($totalmutasiout); ?></th>
                <th style="text-align: right"><?php echo rupiah($saldoakhir); ?></th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
