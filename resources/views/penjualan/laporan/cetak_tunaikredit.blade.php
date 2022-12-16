<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Tunai Kredit {{ date("d-m-y") }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&display=swap');

        body {
            font-family: 'Poppins'
        }

        .datatable3 {
            border: 2px solid #D6DDE6;
            border-collapse: collapse;
            font-size: 14px;
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
        @if ($cabang!=null)
        @if ($cabang->kode_cabang=="PST")
        PACIFIC PUSAT
        @else
        PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
        @endif
        @else
        PACIFC ALL CABANG
        @endif
        <br>
        LAPORAN TUNAI KREDIT<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        @if ($salesman != null)
        SALESMAN {{ strtoupper($salesman->nama_karyawan) }}
        @else
        SEMUA SALESMAN
        @endif
        <br />
    </b>
    <br>
    <table class="datatable3">
        <thead bgcolor="#295ea9" style="color:white;">
            <tr bgcolor="#295ea9" style="color:white;">
                <td rowspan="3" align="center">Kode Barang</td>
                <td rowspan="3" align="center">Nama Barang</td>
                <td colspan="4" align="center">Penjualan Tunai</td>
                <td colspan="4" align="center">Penjualan Kredit</td>
                <td colspan="4">Total Penjualan Tunai Kredit</td>

            </tr>
            <tr bgcolor="#295ea9" style="color:white;">
                <td colspan="3" align="center">Qty</td>
                <td rowspan="2" align="center">Total</td>
                <td colspan="3" align="center">Qty</td>
                <td rowspan="2" align="center">Total</td>
                <td colspan="3" align="center">Total Qty</td>
                <td rowspan="2" align="center">Total Penjualan</td>
            </tr>
            <tr bgcolor="#295ea9" style="color:white;">
                <td align="center">Dus</td>
                <td align="center">Pack</td>
                <td align="center">Pcs</td>
                <td align="center">Dus</td>
                <td align="center">Pack</td>
                <td align="center">Pcs</td>
                <td align="center">Dus</td>
                <td align="center">Pack</td>
                <td align="center">Pcs</td>
            </tr>
        </thead>
        <tbody>
            @php
            $totaldust = 0;
            $totalpackt = 0;
            $totalpcst = 0;
            $totalt = 0;

            $totaldusk = 0;
            $totalpackk = 0;
            $totalpcsk = 0;
            $totalk = 0;

            $totaldusall = 0;
            $totalpackall = 0;
            $totalpcsall = 0;
            $totalall = 0;
            @endphp
            @foreach ($tunaikredit as $t)
            @php
            if ($t->jumlah_tunai != 0) {
            $jmldust = floor($t->jumlah_tunai / $t->isipcsdus);
            $sisadus = $t->jumlah_tunai % $t->isipcsdus;
            if ($t->isipack == 0) {
            $jmlpackt = 0;
            $sisapack = $sisadus;
            } else {

            $jmlpackt = floor($sisadus / $t->isipcs);
            $sisapack = $sisadus % $t->isipcs;
            }
            $jmlpcst = $sisapack;
            $subtotalt = $t->totaljual_tunai;

            if ($t->satuan == 'PCS') {

            $jmldust = 0;
            $jmlpackt = 0;
            $jmlpcst = $t->jumlah_tunai;
            }
            } else {

            $jmldust = 0;
            $jmlpackt = 0;
            $jmlpcst = 0;
            $subtotalt = 0;
            }

            if ($t->jumlah_kredit != 0) {
            $jmldusk = floor($t->jumlah_kredit / $t->isipcsdus);
            $sisadus = $t->jumlah_kredit % $t->isipcsdus;
            if ($t->isipack == 0) {
            $jmlpackk = 0;
            $sisapack = $sisadus;
            } else {

            $jmlpackk = floor($sisadus / $t->isipcs);
            $sisapack = $sisadus % $t->isipcs;
            }
            $jmlpcsk = $sisapack;
            $subtotalk = $t->totaljual_kredit;

            if ($t->satuan == 'PCS') {

            $jmldusk = 0;
            $jmlpackk = 0;
            $jmlpcsk = $t->jumlah_kredit;
            }
            } else {

            $jmldusk = 0;
            $jmlpackk = 0;
            $jmlpcsk = 0;
            $subtotalk = 0;
            }

            if ($t->jumlah != 0) {
            $jmldusall = floor($t->jumlah / $t->isipcsdus);
            $sisadus = $t->jumlah % $t->isipcsdus;
            if ($t->isipack == 0) {
            $jmlpackall = 0;
            $sisapack = $sisadus;
            } else {

            $jmlpackall = floor($sisadus / $t->isipcs);
            $sisapack = $sisadus % $t->isipcs;
            }
            $jmlpcsall = $sisapack;
            $subtotalall = $t->totaljual;

            if ($t->satuan == 'PCS') {

            $jmldusall = 0;
            $jmlpackall = 0;
            $jmlpcsall = $t->jumlah_tunai;
            }
            } else {

            $jmldusall = 0;
            $jmlpackall = 0;
            $jmlpcsall = 0;
            $subtotalall = 0;
            }

            $totaldust = $totaldust + $jmldust;
            $totalpackt = $totalpackt + $jmlpackt;
            $totalpcst = $totalpcst + $jmlpcst;
            $totalt = $totalt + $subtotalt;

            $totaldusk = $totaldusk + $jmldusk;
            $totalpackk = $totalpackk + $jmlpackk;
            $totalpcsk = $totalpcsk + $jmlpcsk;
            $totalk = $totalk + $subtotalk;

            $totaldusall = $totaldusall + $jmldusall;
            $totalpackall = $totalpackall + $jmlpackall;
            $totalpcsall = $totalpcsall + $jmlpcsall;
            $totalall = $totalall + $subtotalall;

            @endphp
            <tr>
                <td><b><?php echo $t->kode_produk; ?></b></td>
                <td><b><?php echo $t->nama_barang; ?></b></td>
                <td align="center"><?php if ($jmldust != 0) {echo rupiah($jmldust);} else {echo "";} ?></td>
                <td align="center"><?php if ($jmlpackt != 0) {echo rupiah($jmlpackt);} else {echo "";} ?></td>
                <td align="center"><?php if ($jmlpcst != 0) {echo rupiah($jmlpcst);} else {echo "";} ?></td>
                <td align="right"><?php if ($subtotalt != 0) {echo rupiah($subtotalt);} else {echo "";} ?></td>
                <td align="center"><?php if ($jmldusk != 0) {echo rupiah($jmldusk);} else {echo "";} ?></td>
                <td align="center"><?php if ($jmlpackk != 0) {echo rupiah($jmlpackk);} else {echo "";} ?></td>
                <td align="center"><?php if ($jmlpcsk != 0) {echo rupiah($jmlpcsk);} else {echo "";} ?></td>
                <td align="right"><?php if ($subtotalk != 0) {echo rupiah($subtotalk);} else {echo "";} ?></td>
                <td align="center"><?php if ($jmldusall != 0) {echo rupiah($jmldusall);} else {echo "";} ?></td>
                <td align="center"><?php if ($jmlpackall != 0) {echo rupiah($jmlpackall);} else {echo "";} ?></td>
                <td align="center"><?php if ($jmlpcsall != 0) {echo rupiah($jmlpcsall);} else {echo "";} ?></td>
                <td align="right"><?php if ($subtotalall != 0) {echo rupiah($subtotalall);} else {echo "";} ?></td>
            </tr>
            @endforeach
            @php
            $totalallretur = $retur->totalretur_tunai + $retur->totalretur_kredit;
            $totalallpenyharga = $potongan->totpenyharga_tunai + $potongan->totpenyharga_kredit;
            $totalallpotongan = $potongan->totpotongan_tunai + $potongan->totpotongan_kredit;
            $totalallpotistimewa = $potongan->totpotistimewa_tunai + $potongan->totpotistimewa_kredit;
            $totalallppn = $potongan->ppn_tunai + $potongan->ppn_kredit;

            $totalallt = $totalt - $retur->totalretur_tunai - $potongan->totpenyharga_tunai - $potongan->totpotongan_tunai - $potongan->totpotistimewa_tunai + $potongan->ppn_tunai;
            $totalallk = $totalk - $retur->totalretur_kredit - $potongan->totpenyharga_kredit - $potongan->totpotongan_kredit - $potongan->totpotistimewa_kredit + $potongan->ppn_kredit;
            @endphp
            <tr bgcolor="#06b947" style="color:white; font-size:12;">
                <td colspan="2">Penjualan Bruto</td>
                <td align="center"></td>
                <td align="center"></td>
                <td align="center"></td>
                <td align="right"><?php echo rupiah($totalt); ?></td>
                <td align="center"></td>
                <td align="center"></td>
                <td align="center"></td>
                <td align="right"><?php echo rupiah($totalk); ?></td>
                <td align="center"></td>
                <td align="center"></td>
                <td align="center"></td>
                <td align="right"><?php echo rupiah($totalall); ?></td>
            </tr>
            <tr>
                <td colspan="5"><b>Retur Penjualan</b></td>
                <td align="right"><?php echo rupiah($retur->totalretur_tunai); ?></td>
                <td colspan="3"></td>
                <td align="right"><?php echo rupiah($retur->totalretur_kredit); ?></td>
                <td colspan="3"></td>
                <td align="right"><?php echo rupiah($totalallretur); ?></td>
            </tr>
            <tr>
                <td colspan="5">Penyesuaian Harga</td>
                <td align="right"><?php echo rupiah($potongan->totpenyharga_tunai); ?></td>
                <td colspan="3"></td>
                <td align="right"><?php echo rupiah($potongan->totpenyharga_kredit); ?></td>
                <td colspan="3"></td>
                <td align="right"><?php echo rupiah($totalallpenyharga); ?></td>
            </tr>
            <tr>
                <td colspan="5">Potongan Harga</td>
                <td align="right"><?php echo rupiah($potongan->totpotongan_tunai); ?></td>
                <td colspan="3"></td>
                <td align="right"><?php echo rupiah($potongan->totpotongan_kredit); ?></td>
                <td colspan="3"></td>
                <td align="right"><?php echo rupiah($totalallpotongan); ?></td>
            </tr>
            <tr>
                <td colspan="5">Potongan Isitimwa</td>
                <td align="right"><?php echo rupiah($potongan->totpotistimewa_tunai); ?></td>
                <td colspan="3"></td>
                <td align="right"><?php echo rupiah($potongan->totpotistimewa_kredit); ?></td>
                <td colspan="3"></td>
                <td align="right"><?php echo rupiah($totalallpotistimewa); ?></td>
            </tr>
            <tr>
                <td colspan="5">PPN</td>
                <td align="right"><?php echo rupiah($potongan->ppn_tunai); ?></td>
                <td colspan="3"></td>
                <td align="right"><?php echo rupiah($potongan->ppn_kredit); ?></td>
                <td colspan="3"></td>
                <td align="right"><?php echo rupiah($totalallppn); ?></td>
            </tr>
            <tr bgcolor="#06b947" style="color:white; font-size:12;">
                <td colspan="5">Penjualan Netto</td>
                <td align="right"><?php echo rupiah($totalallt); ?></td>
                <td colspan="3"></td>
                <td align="right"><?php echo rupiah($totalallk); ?></td>
                <td colspan="3"></td>
                <td align="right"><?php echo rupiah($totalallt + $totalallk); ?></td>
            </tr>
        </tbody>
    </table>

</body>
</html>
