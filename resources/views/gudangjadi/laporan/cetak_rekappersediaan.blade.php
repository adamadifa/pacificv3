<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Persediaan Gudang Jadi {{ date('d-m-y') }}</title>
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
        REKAPITULASI PERSEDIAAN BARANG<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:80%" border="1">
        <thead>
            <tr>

                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">No</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">Barang/Produk</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">Saldo Awal</th>
                <th colspan="3" bgcolor="#28a745" style="color:white; font-size:12;">PENERIMAAN</th>
                <th colspan="3" bgcolor="#c7473a" style="color:white; font-size:12;">PENGELUARAN</th>
                <th rowspan="2" rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">SALDO AKHIR</th>
            </tr>
            <tr>
                <th bgcolor="#28a745" style="color:white; font-size:12;">PRODUKSI</th>
                <th bgcolor="#28a745" style="color:white; font-size:12;">REPACK</th>
                <th bgcolor="#28a745" style="color:white; font-size:12;">LAIN LAIN</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">KIRIM KE CABANG</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">REJECT</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">LAIN LAIN</th>
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
            foreach ($mutasi as $m) {

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
                <td align="right"><?php if ($m->jmlfsthp != 0) {
                    echo rupiah($m->jmlfsthp);
                } ?></td>
                <td align="right"><?php if ($m->jmlrepack != 0) {
                    echo rupiah($m->jmlrepack);
                } ?></td>
                <td align="right"><?php if ($m->jmllainlain_in != 0) {
                    echo rupiah($m->jmllainlain_in);
                } ?></td>
                <td align="right"><?php if ($m->jmlsuratjalan != 0) {
                    echo rupiah($m->jmlsuratjalan);
                } ?></td>
                <td align="right"><?php if ($m->jmlreject != 0) {
                    echo rupiah($m->jmlreject);
                } ?></td>
                <td align="right"><?php if ($m->jmllainlain_out != 0) {
                    echo rupiah($m->jmllainlain_out);
                } ?></td>
                <td align="right"><?php echo rupiah($saldoakhir); ?></td>
            </tr>
            <?php $no++;
            } ?>
        </tbody>
        <tfoot bgcolor="#024a75" style="color:white; font-size:12;">
            <tr>
                <th colspan="2">TOTAL</th>
                <th style="text-align: right"><?php echo rupiah($totalsaldoawal); ?></th>
                <th style="text-align: right"><?php echo rupiah($totalfsthp); ?></th>
                <th style="text-align: right"><?php echo rupiah($totlarepack); ?></th>
                <th style="text-align: right"><?php echo rupiah($totallainlain_in); ?></th>
                <th style="text-align: right"><?php echo rupiah($totalsuratjalan); ?></th>
                <th style="text-align: right"><?php echo rupiah($totalreject); ?></th>
                <th style="text-align: right"><?php echo rupiah($totallainlain_out); ?></th>
                <th style="text-align: right"><?php echo rupiah($totalsaldoakhir); ?></th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
