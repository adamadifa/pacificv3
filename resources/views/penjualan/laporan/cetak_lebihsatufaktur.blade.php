<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Analisa Umur Piutang (AUP) {{ date('d-m-y') }}</title>
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

        a {
            color: white;
        }
    </style>
</head>

<body>
    <b style="font-size:14px;">
        @if ($cabang != null)
            @if ($cabang->kode_cabang == 'PST')
                PACIFIC PUSAT
            @else
                PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
            @endif
        @else
            PACIFC ALL CABANG
        @endif
        <br>
        LAPORAN LEBIH SATU FAKTUR<br>
        PER TANGGAL {{ DateToIndo2($tanggal) }}
        <br>
        @if ($salesman != null)
            SALESMAN {{ strtoupper($salesman->nama_karyawan) }}
        @else
            SEMUA SALESMAN
        @endif
        <br />
    </b>
    <br>
    <table class="datatable3" style="width:50%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td>Tanggal Penjualan</td>
                <td>No Faktur</td>
                <td>Kode Pelanggan</td>
                <td>Nama Pelanggan</td>
                <td>Pasar / Daerah</td>
                <td>Saldo Piutang</td>
                <td style="width:5%">Jumlah</td>
                <td style="width:5%">Keterangan</td>
            </tr>
        </thead>
        <tbody>
            <?php
                $no=1;
                $totalsisabayar = 0;
                $totalfaktur 	= 0;
                $grandtotal 	= 0;
                $kode_pelanggan = "";
                foreach($lebihsatufaktur as $key => $lb){
                    $pel  = @$lebihsatufaktur[$key+1]->kode_pelanggan;
                    if($kode_pelanggan != $lb->kode_pelanggan){
                        $totalfaktur 		= 0;
                        $totalsisabayar 	= 0;

                    }
                    //echo $pel."<br>";
                    $totalsisabayar = $totalsisabayar + $lb->sisabayar;
                    $totalfaktur 	= $totalfaktur + $lb->jmlfaktur;
                    //$grandtotal 	= $grandtotal + $lb->sisabayar;
            ?>

            <?php
            
            ?>

            <tr>
                <td><?php echo $lb->tgltransaksi; ?></td>
                <td><?php echo $lb->no_fak_penj; ?></td>
                <td><?php echo $lb->kode_pelanggan; ?></td>
                <td><?php echo $lb->nama_pelanggan; ?></td>
                <td><?php echo $lb->pasar; ?></td>
                <td align="right"><?php echo rupiah($lb->sisabayar); ?></td>
                <td></td>
                <td>{{ $lb->keterangan }}</td>
            </tr>

            <?php

                    if($pel != $lb->kode_pelanggan ){
                        if($totalfaktur>1){

                            $grandtotal = $grandtotal + $totalsisabayar;
                        }
                        echo '
                            <tr bgcolor="#199291" style="color:white; font-weight:bold">

                                <td colspan="5" >Jumlah Faktur</td>
                                <td align=right>'.rupiah($totalsisabayar).'</td>
                                <td>'.$totalfaktur.'</td>
                            </tr>';
                        $totalfaktur    = 0;
                        $totalsisabayar = 0;


                    }

                    $kode_pelanggan = $lb->kode_pelanggan;

                }
            ?>



        </tbody>
        <tfoot>
            <tr bgcolor="#199291" style="color:white; font-weight:bold">
                <td colspan="5">GRAND TOTAL LEBIH SATU FAKTUR</td>
                <td align="right"><?php echo rupiah($grandtotal); ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
