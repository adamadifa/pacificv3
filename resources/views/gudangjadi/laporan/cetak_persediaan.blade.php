<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Persediaan Gudang Jadi {{ date("d-m-y") }}</title>
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
    <table class="datatable3" style="width:100%" border="1">
        <thead>
            <tr>

                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">Tanggal</th>
                <th colspan="5" bgcolor="#024a75" style="color:white; font-size:12;">BUKTI</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">KETERANGAN</th>
                <th colspan="3" bgcolor="#28a745" style="color:white; font-size:12;">PENERIMAAN</th>
                <th colspan="3" bgcolor="#c7473a" style="color:white; font-size:12;">PENGELUARAN</th>
                <th rowspan="2" rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">SALDO AKHIR</th>
            </tr>
            <tr>
                <th bgcolor="#024a75" style="color:white; font-size:12;">FSTHP</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">SURAT JALAN</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">REPACK</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">REJECT</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">LAINLAIN</th>
                <th bgcolor="#28a745" style="color:white; font-size:12;">PRODUKSI</th>
                <th bgcolor="#28a745" style="color:white; font-size:12;">REPACK</th>
                <th bgcolor="#28a745" style="color:white; font-size:12;">LAIN LAIN</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">KIRIM KE CABANG</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">REJECT</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">LAIN LAIN</th>
            </tr>
            </tr>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th colspan="6"></th>
                <th>SALDO AWAL</th>
                <th colspan="6"></th>
                <th style="text-align: right"><?php echo rupiah($saldoawal->saldo_awal); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalfsthp 	 = 0;
            $totalsuratjalan = 0;
            $totalrepack 	 = 0;
            $totalreject     = 0;
            $totallainlain_in  = 0;
            $totallainlain_out  = 0;
            $saldoakhir      = $saldoawal->saldo_awal;
            foreach ($mutasi as $m) {
                if ($m->jenis_mutasi == "FSTHP") {
                    $no_fsthp = $m->no_mutasi_gudang;
                    $jmlfsthp = $m->jumlah;
                    $ket 	  = "PRODUKSI";
                } else {
                    $no_fsthp = "";
                    $jmlfsthp = 0;
                }

                if ($m->jenis_mutasi == "SURAT JALAN") {
                    $no_suratjalan = $m->no_mutasi_gudang;
                    $jmlsuratjalan = $m->jumlah;
                    $ket 		   = $m->kode_cabang;
                } else {
                    $no_suratjalan = "";
                    $jmlsuratjalan = 0;
                }

                if ($m->jenis_mutasi == "REPACK") {
                    $no_repack 		= $m->no_mutasi_gudang;
                    $jmlrepack  	= $m->jumlah;
                    $ket 		    = "REPACK";
                } else {
                    $no_repack = "";
                    $jmlrepack = 0;
                }

                if ($m->jenis_mutasi == "REJECT") {
                    $no_reject 		= $m->no_mutasi_gudang;
                    $jmlreject  	= $m->jumlah;
                    $ket 		    = "REJECT";
                } else {
                    $no_reject = "";
                    $jmlreject = 0;
                }

                if ($m->jenis_mutasi == "LAINLAIN" and $m->inout == 'IN') {
                    $no_mutasilainlain 		= $m->no_mutasi_gudang;
                    $jmllainlain_in  	    = $m->jumlah;
                    $ket 		    		= $m->keterangan;
                } else {
                    $no_mutasilainlain = "";
                    $jmllainlain_in = 0;
                }

                if ($m->jenis_mutasi == "LAINLAIN" and $m->inout == 'OUT') {
                    $no_mutasilainlain 		= $m->no_mutasi_gudang;
                    $jmllainlain_out  		= $m->jumlah;
                    $ket 		    	    = $m->keterangan;
                } else {
                    $no_mutasilainlain = "";
                    $jmllainlain_out = 0;
                }

                if ($m->inout == 'IN') {
                    $jumlah  = $m->jumlah;
                } else {
                    $jumlah = -$m->jumlah;
                }

                $saldoakhir 		= $saldoakhir + $jumlah;
                $totalfsthp 		= $totalfsthp + $jmlfsthp;
                $totalsuratjalan 	= $totalsuratjalan + $jmlsuratjalan;
                $totalrepack 		= $totalrepack + $jmlrepack;
                $totalreject 		= $totalreject + $jmlreject;
                $totallainlain_in = $totallainlain_in + $jmllainlain_in;
                $totallainlain_out = $totallainlain_out + $jmllainlain_out;
            ?>
            <tr style="font-weight: bold; font-size:11px">
                <td><?php echo DateToIndo2($m->tgl_mutasi_gudang); ?></td>
                <td><?php echo $no_fsthp; ?></td>
                <td><?php echo $no_suratjalan; ?></td>
                <td><?php echo $no_repack; ?></td>
                <td><?php echo $no_reject; ?></td>
                <td><?php echo $no_mutasilainlain; ?></td>
                <td><?php echo $ket; ?></td>
                <td align="right">{{ !empty($jmlfsthp) ? rupiah($jmlfsthp) : '' }}</td>
                <td align="right">{{ !empty($jmlrepack) ? rupiah($jmlrepack) : '' }}</td>
                <td align="right">{{ !empty($jmllainlain_in) ? rupiah($jmllainlain_in) : '' }}</td>
                <td align="right">{{ !empty($jmlsuratjalan) ? rupiah($jmlsuratjalan) : '' }}</td>
                <td align="right">{{ !empty($jmlreject) ? rupiah($jmlreject) : '' }}</td>
                <td align="right">{{ !empty($jmllainlain_out) ? rupiah($jmllainlain_out) : '' }}</td>
                <td align="right"><?php echo rupiah($saldoakhir); ?></td>
            </tr>
            <?php
            }
            ?>

        </tbody>
        <tfoot bgcolor="#024a75" style="color:white; font-size:12;">
            <tr>
                <th colspan="7">TOTAL</th>
                <th style="text-align: right"><?php echo rupiah($totalfsthp); ?></th>
                <th style="text-align: right"><?php echo rupiah($totalrepack); ?></th>
                <th style="text-align: right"><?php echo rupiah($totallainlain_in); ?></th>
                <th style="text-align: right"><?php echo rupiah($totalsuratjalan); ?></th>
                <th style="text-align: right"><?php echo rupiah($totalreject); ?></th>
                <th style="text-align: right"><?php echo rupiah($totallainlain_out); ?></th>
                <th style="text-align: right"><?php echo rupiah($saldoakhir); ?></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
