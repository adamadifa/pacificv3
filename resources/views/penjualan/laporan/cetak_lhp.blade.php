<!-- Normalize or reset CSS with your favorite library -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

<!-- Load paper.css for happy printing -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

<!-- Set page size here: A5, A4 or A3 -->
<!-- Set also "landscape" if you need -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&display=swap');

    .sheet.padding-5mm {
        padding: 5mm
    }

    body.A4 .sheet {
        height: auto !important;
        width: 290mm !important;
    }

    .sheet {
        overflow: auto !important;
    }

    .datatable3 {
        border: 1px solid #2f2f2f;
        border-collapse: collapse;

    }

    .datatable3 td {
        border: 1px solid #000000;
        padding: 6px;
        font-size: 9px;
    }

    .datatable3 th {
        border: 2px solid #828282;
        font-weight: bold;
        text-align: left;
        padding: 2px;
        text-align: center;
        font-size: 10px;
    }


    .datatable2 {
        border: 1px solid #2f2f2f;
        border-collapse: collapse;

    }

    .datatable2 td {
        /* border: 1px solid #000000; */
        padding: 6px;
        font-size: 9px;
    }


    body {
        background: rgb(204, 204, 204);
        font-family: 'Poppins';
    }

    @page {
        size: A4
    }

</style>

</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak LHP</title>
</head>
<body class="A4">
    <section class="sheet padding-5mm">
        <!-- Write HTML just like a web page -->
        <article>
            <table class="datatable3" style="width: 100%">
                <tr>
                    <td style="text-align:center; width:5%">
                        <img src="{{ asset('app-assets/images/logo/pcf.png') }}" alt="" width="70px" height="70px">
                    </td>

                    <td style="text-align:center; width:90%" colspan="2">
                        <h1 style="line-height:1px">CV. PACIFIC</h1>
                        <h2>LAPORAN HARIAN PENJUALAN</h2>
                    </td>
                    <td style="text-align:center;width:5%">
                        <img src="{{ asset('app-assets/images/logo/pcf.png') }}" alt="" width="70px" height="70px">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="width: 70%">
                        RUTE : _____________________
                    </td>

                    <td colspan="2" style="width: 30%; text-align:center">{{ DateToIndo2($tanggal) }}</td>
                <tr>
            </table>
            <table class="datatable3" style="margin-top:5px; width:100%">
                <thead>
                    <tr>
                        <th rowspan="2">PELANGGAN</th>
                        <th rowspan="2">No. BON</th>
                        <th colspan="10">NAMA PRODUK</th>
                        <th colspan="2">PENJUALAN</th>
                        <th rowspan="2">TITIPAN</th>
                        <th rowspan="2">TRANSFER</th>
                        <th rowspan="2">GIRO</th>
                        <th rowspan="2">VOUCHER</th>
                    </tr>
                    <tr>
                        <th>AB</th>
                        <th>AR</th>
                        <th>AS</th>
                        <th>BB</th>
                        <th>DP</th>
                        <th>SC</th>
                        <th>SP8P</th>
                        <th>SP8</th>
                        <th>SP</th>
                        <th>SP500</th>
                        <th>TUNAI</th>
                        <th>KREDIT</th>
                    </tr>
                </thead>
                @php
                $totalAB = 0;
                $totalAR = 0;
                $totalASE = 0;
                $totalBB = 0;
                $totalDEP = 0;
                $totalSC = 0;
                $totalSP8P = 0;
                $totalSP8 =0;
                $totalSP = 0;
                $totalSP500 = 0;
                $totaltunai = 0;
                $totalkredit = 0;
                $totaltagihan1 = 0;
                $totaltagihan2 = 0;
                $totaltransfer = 0;
                $totalgiro =0;

                $totaltitipan1 =0;
                $totaltransfer1 = 0;
                $totalgiro1 = 0;
                $totalvoucher1 = 0;


                $totaltitipan2 =0;
                $totaltransfer2 = 0;
                $totalgiro2 = 0;
                $totalvoucher2 = 0;

                $totaltransfer3 = 0;
                $totalgiro3 = 0;

                @endphp
                @foreach ($penjualan as $d)
                @php
                $totalAB += $d->AB;
                $totalAR += $d->AR;
                $totalASE += $d->ASE;
                $totalBB += $d->BB;
                $totalDEP += $d->DEP;
                $totalSC += $d->SC;
                $totalSP8P += $d->SP8P;
                $totalSP8 += $d->SP8;
                $totalSP += $d->SP;
                $totalSP500 += $d->SP500;
                $totaltunai += $d->totaltunai;
                $totalkredit += $d->totalkredit;
                $totaltagihan1 += ($d->totalbayar + $d->totalgiro + $d->totaltransfer);

                $totaltitipan1 += $d->totalbayar;
                $totaltransfer1 += $d->totaltransfer;
                $totalgiro1 += $d->totalgiro;
                $totalvoucher1 += $d->totalvoucher;
                @endphp
                <tr>
                    <td>{{ ucwords(strtolower($d->nama_pelanggan)) }}</td>
                    <td>{{ $d->no_fak_penj }}</td>
                    <td style="text-align:right; font-weight:bold">
                        <?php if (!empty($d->AB)) { echo desimal($d->AB); } ?>
                    </td>
                    <td style="text-align:right; font-weight:bold">
                        <?php if (!empty($d->AR)) { echo desimal($d->AR); } ?>
                    </td>
                    <td style="text-align:right; font-weight:bold">
                        <?php if (!empty($d->ASE)) { echo desimal($d->ASE);} ?>
                    </td>
                    <td style="text-align:right; font-weight:bold">
                        <?php if (!empty($d->BB)) { echo desimal($d->BB);} ?>
                    </td>
                    <td style="text-align:right; font-weight:bold">
                        <?php if (!empty($d->DEP)) { echo desimal($d->DEP);} ?>
                    </td>
                    <td style="text-align:right; font-weight:bold">
                        <?php if (!empty($d->SC)) { echo desimal($d->SC);} ?>
                    </td>
                    <td style="text-align:right; font-weight:bold">
                        <?php if (!empty($d->SP8P)) { echo desimal($d->SP8P);} ?>
                    </td>
                    <td style="text-align:right; font-weight:bold">
                        <?php if (!empty($d->SP8)) { echo desimal($d->SP8);} ?>
                    </td>
                    <td style="text-align:right; font-weight:bold">
                        <?php if (!empty($d->SP)) { echo desimal($d->SP);} ?>
                    </td>
                    <td style="text-align:right; font-weight:bold">
                        <?php if (!empty($d->SP500)) { echo desimal($d->SP500);} ?>
                    </td>
                    <td style="text-align:right">
                        <?php if (!empty($d->totaltunai)) { echo rupiah($d->totaltunai);} ?>
                    </td>
                    <td style="text-align:right">
                        <?php if (!empty($d->totalkredit)) { echo rupiah($d->totalkredit);} ?>
                    </td>
                    <td style="text-align:right">
                        <?php if (!empty($d->totalbayar)) { echo rupiah($d->totalbayar);} ?>
                    </td>
                    <td style="text-align:right">
                        <?php if (!empty($d->totaltransfer)) { echo rupiah($d->totaltransfer);} ?>
                    </td>
                    <td style="text-align:right">
                        <?php if (!empty($d->totalgiro)) { echo rupiah($d->totalgiro);} ?>
                    </td>
                    <td style="text-align:right">
                        <?php if (!empty($d->totalvoucher)) { echo rupiah($d->totalvoucher);} ?>
                    </td>
                </tr>
                @endforeach
                @foreach ($historibayar as $d)
                @php
                $tagihan2 = ($d->totalbayar + $d->totalgiro + $d->totaltransfer);
                $totaltagihan2 +=$tagihan2;
                $totaltitipan2 += $d->totalbayar;
                $totaltransfer2 += $d->totaltransfer;
                $totalgiro2 += $d->totalgiro;
                $totalvoucher2 += $d->totalvoucher;
                @endphp
                <tr>
                    <td>{{ ucwords(strtolower($d->nama_pelanggan)) }}</td>
                    <td>{{ $d->no_fak_penj }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align:right">
                        <?php if (!empty($d->totalbayar)) { echo rupiah($d->totalbayar);} ?>
                    </td>
                    <td>
                        <?php if (!empty($d->totaltransfer)) { echo rupiah($d->totaltransfer);} ?>
                    </td>
                    <td>
                        <?php if (!empty($d->totalgiro)) { echo rupiah($d->totalgiro);} ?>
                    </td>
                    <td>
                        <?php if (!empty($d->totalvoucher)) { echo rupiah($d->totalvoucher);} ?>
                    </td>
                </tr>
                @endforeach
                @foreach ($giro as $d)
                @php
                $totalgiro += $d->totalgiro;
                @endphp
                <tr>
                    <td>{{ ucwords(strtolower($d->nama_pelanggan)) }}</td>
                    <td>{{ $d->no_fak_penj }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align:right">
                        <?php if (!empty($d->totalgiro)) { echo rupiah($d->totalgiro);} ?>
                    </td>
                    <td></td>
                </tr>
                @endforeach

                @foreach ($transfer as $d)
                @php
                $totaltransfer += $d->totaltransfer;
                $totaltransfer3 += $d->totaltransfer;
                @endphp
                <tr>
                    <td>{{ ucwords(strtolower($d->nama_pelanggan)) }}</td>
                    <td>{{ $d->no_fak_penj }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align:right">
                        <?php if (!empty($d->totaltransfer)) { echo rupiah($d->totaltransfer);} ?>
                    </td>
                    <td></td>
                    <td></td>
                </tr>

                @endforeach
                @if (Auth::user()->kode_cabang=="TSM")
                <tr>
                    <td style="padding:15px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                </tr>
                <tr>
                    <td style="padding:15px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                </tr>
                <tr>
                    <td style="padding:15px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                    <td style="padding:5px"></td>
                </tr>
                @endif

                <tr>
                    <th colspan="2">TERJUAL</th>
                    <th>{{ desimal($totalAB) }}</th>
                    <th>{{ desimal($totalAR) }}</th>
                    <th>{{ desimal($totalASE) }}</th>
                    <th>{{ desimal($totalBB) }}</th>
                    <th>{{ desimal($totalDEP) }}</th>
                    <th>{{ desimal($totalSC) }}</th>
                    <th>{{ desimal($totalSP8P) }}</th>
                    <th>{{ desimal($totalSP8) }}</th>
                    <th>{{ desimal($totalSP) }}</th>
                    <th>{{ desimal($totalSP500) }}</th>
                    <th>{{ desimal($totaltunai) }}</th>
                    <th>{{ desimal($totalkredit) }}</th>
                    <th>{{ rupiah($totaltitipan1 + $totaltitipan2) }}</th>
                    <th>{{ rupiah($totaltransfer1 + $totaltransfer2 + $totaltransfer3) }}</th>
                    <th>{{ rupiah($totalgiro1 + $totalgiro2 + $totalgiro3) }}</th>
                    <th>{{ rupiah($totalvoucher1+$totalvoucher2) }}</th>
                </tr>
                <tr>
                    <th colspan="2">BS</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </table>
            <br>
            <br>
            <div style="display: flex; gap:1">
                <table class="datatable3">
                    <thead>
                        <tr>
                            <th rowspan="2">Kode Produk</th>
                            <th rowspan="2">Nama Barang</th>
                            <th colspan="3">Qty</th>
                        </tr>
                        <tr>
                            <th>Dus</th>
                            <th>Pack</th>
                            <th>Pcs</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekapdp as $d)
                        @php
                        $jumlah = $d->jumlah / $d->isipcsdus;
                        $jmldus = floor($d->jumlah / $d->isipcsdus);
                        if ($d->jumlah != 0) {
                        $sisadus = $d->jumlah % $d->isipcsdus;
                        } else {
                        $sisadus = 0;
                        }
                        if ($d->isipack == 0) {
                        $jmlpack = 0;
                        $sisapack = $sisadus;
                        $s = "A";
                        } else {
                        $jmlpack = floor($sisadus / $d->isipcs);
                        $sisapack = $sisadus % $d->isipcs;
                        $s = "B";
                        }
                        $jmlpcs = $sisapack;
                        @endphp
                        <tr>
                            <td>{{ $d->kode_produk }}</td>
                            <td>{{ $d->nama_barang }}</td>
                            <td><?php if (!empty($jmldus)) {
                            echo $jmldus;
                        } ?></td>
                            <td><?php if (!empty($jmlpack)) {
                            echo $jmlpack;
                        } ?></td>
                            <td><?php if (!empty($jmlpcs)) {
                            echo $jmlpcs;
                        } ?></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <table class="datatable2" style="margin-left: 10px; width:70%">
                    <tr>
                        <td style="width: 60%">
                            <table style="width:100%">
                                <tr>
                                    <td style="width:25%">Uang Kertas</td>
                                    <td>Rp.........................................................................................................</td>
                                </tr>
                                <tr>
                                    <td style="width:20%">Uang Logam</td>
                                    <td>Rp.........................................................................................................</td>
                                </tr>
                                <tr>
                                    <td style="width:20%">Cek/BG</td>
                                    <td>Rp. {{ rupiah($allgiro->totalgiro) }}</td>
                                </tr>
                                <tr>
                                    <td style="width:20%">Transfer</td>
                                    <td>Rp. {{ rupiah($alltransfer->totaltransfer) }}</td>
                                </tr>

                                <tr>
                                    <td style="width:20%">Jumlah</td>
                                    <td>Rp.........................................................................................................</td>
                                </tr>
                                <tr>
                                    <td style="width:20%">Setor</td>
                                    <td>Rp.........................................................................................................</td>
                                </tr>
                                <tr>
                                    <td style="width:20%">Selisih</td>
                                    <td>Rp.........................................................................................................</td>
                                </tr>
                            </table>
                        </td>
                        <td valign="top" style="width: 40%">
                            <table style="width:100%">
                                <tr>
                                    <td>Penjualan Tunai</td>
                                    <td>Rp. {{ rupiah($totaltunai) }}</td>
                                </tr>
                                {{-- <tr>
                                    <td style="width:40%">Penjualan Botol / Peti</td>
                                    <td>Rp.....................................................</td>
                                </tr> --}}
                                <tr>
                                    <td>Tagihan</td>
                                    <td>
                                        Rp.{{ rupiah($totaltitipan1 + $totaltitipan2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Dikurangi</td>
                                    <td>Rp.....................................................</td>
                                </tr>
                                <tr>
                                    <td>Retur / BS</td>
                                    <td>Rp.....................................................</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <br>
            <table class="datatable2" style="width:100%">
                <tr>
                    <td style="text-align:center">Dibuat Oleh</td>
                    <td style="text-align:center" colspan="2">Mengetahui</td>
                </tr>
                <tr>
                    <td style="text-align: center">
                        <br>
                        <br>
                        <br>
                        <br>

                        <u>({{ $karyawan->nama_karyawan }})</u>
                        <br>
                        Salesman
                    </td>
                    <td style="text-align: center">
                        <br>
                        <br>
                        <br>
                        <br>

                        <u>(------------------)</u>
                        <br>
                        Salesman SPV
                    </td>
                    <td style="text-align: center">
                        <br>
                        <br>
                        <br>
                        <br>

                        <u>(------------------)</u>
                        <br>
                        Kepala Penjualan
                    </td>
                </tr>
            </table>
        </article>
    </section>
</body>
</html>
