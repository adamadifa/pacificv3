<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Data Pengambilan Pelanggan</title>
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
        REKAP PENJUALAN PRODUK<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        @if ($salesman != null)
            SALESMAN {{ strtoupper($salesman->nama_karyawan) }}
        @else
            SEMUA SALESMAN
        @endif
        <br />

    </b>
    <table border="0">
        <thead>
            <tr>
                <th align="left">PENJUALAN</th>
                <th align="right">JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $kategori  				= "";
            $jumlahperkategori		= 0;
            $jumlahall				= 0;
            foreach($rekap as $key => $r){
                $kat = @$rekap[$key+1]->kategori_jenisproduk;
                if($kategori != $r->kategori_jenisproduk){

                    echo "<tr><td colspan='2'><b><i>$r->kategori_jenisproduk</i></b></td></tr>";
                }

                $jumlahperkategori = $jumlahperkategori + $r->jumlah;
                $jumlahall 		   = $jumlahall + $r->jumlah;
            ?>
            <tr>
                <td style="width:300px; text-align:left"><?php echo $r->nama_barang; ?></td>
                <td align="right"><?php echo rupiah($r->jumlah); ?></td>
            </tr>
            <?php
                $kategori  = $r->kategori_jenisproduk;

                if($kat != $r->kategori_jenisproduk){
                ?>
            <tr>
                <td><b><i>JUMLAH</i></b></td>
                <td align="right"><b><i><?php echo rupiah($jumlahperkategori); ?></i></b></td>
            </tr>
            <?php
                    $jumlahperkategori = 0;
                    echo "<tr style='height:20px'><td colspan='2'></tr>";
                }
            }

            ?>
        </tbody>
        <tfoot>
            <tr>
                <td>POTONGAN</td>
                <td align="right"><b><i><?php echo rupiah($penjualan->potongan); ?></i></b></td>
            </tr>
            <tr>
                <td>POTONGAN ISTIMEWA</td>
                <td align="right"><b><i><?php echo rupiah($penjualan->potistimewa); ?></i></b></td>
            </tr>
            <tr>
                <td>PENYESUAIAN</td>
                <td align="right"><b><i><?php echo rupiah($penjualan->penyharga); ?></i></b></td>
            </tr>
            <tr>
                <td>PPN</td>
                <td align="right"><b><i><?php echo rupiah($penjualan->ppn); ?></i></b></td>
            </tr>
            <tr>
                <td>RETUR</td>
                <td align="right"><b><i><?php echo rupiah($retur->totalretur); ?></i></b></td>
            </tr>
            <tr>
                <td></td>

            </tr>
            <tr>
                <td><b>PENJUALAN BERSIH</b></td>
                <?php
                $totalnetto = $jumlahall - $penjualan->potongan - $penjualan->potistimewa - $penjualan->penyharga + $penjualan->ppn - $retur->totalretur;
                ?>
                <td align="right"><b><i><?php echo rupiah($totalnetto); ?></i></b></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
