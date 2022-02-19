<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Analisa Umur Piutang (AUP) {{ date("d-m-y") }}</title>
    <style>
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
        LAPORAN ANALISA UMUR PIUTANG<br>
        PER TANGGAL {{ DateToIndo2($tgl_aup) }}
        <br>
        @if ($salesman != null)
        SALESMAN {{ strtoupper($salesman->nama_karyawan) }}
        @else
        SEMUA SALESMAN
        @endif
        <br />
        @if ($pelanggan != null)
        PELANGGAN {{ strtoupper($pelanggan->nama_pelanggan) }}
        @else
        SEMUA PELANGGAN
        @endif
    </b>
    <br>
    <table class="datatable3" style="width:150% !important">

        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">

                <th rowspan="2">Kode Pelanggan</th>
                <th rowspan="2">Nama Pelanggan</th>
                <th rowspan="2">Pasar/Daerah</th>
                <th rowspan="2">Salesman</th>
                <th rowspan="2" style="width:8%">Jatuh Tempo</th>
                <th colspan="9">Saldo Piutang</th>
                <th rowspan="2">Total</th>
            </tr>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th> 1 s/d 15 Hari</th>
                <th> 16 Hari s/d 1 Bulan</th>
                <th> > 1 Bulan s/d 46 Hari</th>
                <th> > 46 s/d 2 Bulan</th>
                <th> > 2 Bulan s/d 3 Bulan </th>
                <th> > 3 Bulan s/d 6 Bulan </th>
                <th> > 6 Bulan s/d 1 Tahun </th>
                <th> > 1 Tahun s/d 2 Tahun</th>
                <th> > 2 Tahun</th>
            </tr>
        </thead>
        <tbody>
            @php
            $totalallduaminggu = 0;
            $totalallsatubulan = 0;
            $totalallsatubulan15 = 0;
            //$totalallsatusetengahbulan = 0;
            $totalallduabulan = 0;
            $totalalllebih3bulan = 0;
            $totalallenambulan = 0;
            $totalallduabelasbulan = 0;
            //$totalalldelapanbelasbulan = 0;
            $totalallduatahun = 0;
            $totalalllebihduatahun = 0;

            $totalall = 0;
            $totalpiutang = 0;
            $totalbayar = 0;
            $totalsisabayar = 0;
            $duaminggu = 0;
            $satubulan = 0;
            $satusetengahbulan = 0;
            $satubulan15 = 0;
            $duabulan = 0;
            $lebihtigabulan = 0;
            $duabelasbulan = 0;
            $delapanbelasbulan = 0;
            $enambulan = 0;
            $lebihduatahun = 0;
            $duatahun=0;
            $total = 0;
            $kode_pelanggan = "";
            @endphp
            @foreach ($aup as $key => $a)
            @php
            $pel = @$aup[$key + 1]->kode_pelanggan;

            $totalpiutang = $totalpiutang + $a->totalpiutang;
            $totalbayar = $totalbayar + $a->jmlbayar;
            $totalsisabayar = $totalpiutang - $totalbayar;
            $duaminggu += $a->duaminggu;
            $satubulan += $a->satubulan;
            //$satusetengahbulan += $a->satusetengahbulan;
            $satubulan15 += $a->satubulan15;
            $duabulan += $a->duabulan;
            $lebihtigabulan += $a->lebihtigabulan;
            $enambulan += $a->enambulan;
            $duabelasbulan += $a->duabelasbulan;
            $duatahun += $a->duatahun;
            $lebihduatahun += $a->lebihduatahun;
            $total = $duaminggu + $satubulan + $satubulan15 + $duabulan + $lebihtigabulan + $enambulan + $duabelasbulan + $lebihduatahun + $duatahun;
            @endphp

            @if ($pel != $a->kode_pelanggan)
            @php
            $totalallduaminggu = $totalallduaminggu + $duaminggu;
            $totalallsatubulan = $totalallsatubulan + $satubulan;
            $totalallsatubulan15 = $totalallsatubulan15 + $satubulan15;
            //$totalallsatusetengahbulan = $totalallsatusetengahbulan + $satusetengahbulan;
            $totalallduabulan = $totalallduabulan + $duabulan;
            $totalalllebih3bulan = $totalalllebih3bulan + $lebihtigabulan;
            $totalallenambulan = $totalallenambulan + $enambulan;
            $totalallduabelasbulan = $totalallduabelasbulan + $duabelasbulan;
            $totalallduatahun = $totalallduatahun + $duatahun;
            $totalalllebihduatahun = $totalalllebihduatahun + $lebihduatahun;
            @endphp

            <tr>

                <td><?php echo $a->kode_pelanggan; ?></td>
                <td><?php echo $a->nama_pelanggan; ?></td>
                <td><?php echo $a->pasar; ?></td>
                <td><?php echo $a->nama_karyawan; ?></td>
                <td align="center">
                    <?php
                    $jatuhtempo = $a->jatuhtempo;
                    if ($jatuhtempo == 14) {
                        $lama = "14 Hari";
                    } else if ($jatuhtempo == 30) {
                        $lama = "30 Hari";
                    } else if ($jatuhtempo == 45) {
                        $lama = "45 Hari";
                    } else if ($jatuhtempo == 60) {
                        $lama = "2 Bulan";
                    } else if ($jatuhtempo == 90) {
                        $lama = "3 Bulan";
                    } else if ($jatuhtempo == 180) {
                        $lama = "6 Bulan";
                    } else if ($jatuhtempo == 360) {
                        $lama = "1 Tahun";
                    } else {
                        $lama = "";
                    }
                    if ($lama == "") {
                        echo '<span style="color:white; background-color:red; font-weight:bold">Belum Di Ajukan</span>';
                    } else {
                        echo $lama;
                    }
                    ?>
                </td>
                <td align="right"><?php if ($duaminggu != 0) {echo rupiah($duaminggu);} ?></td>
                <td align="right"><?php if ($satubulan != 0) {echo rupiah($satubulan);} ?></td>
                <td align="right"><?php if ($satubulan15 != 0) {echo rupiah($satubulan15);} ?></td>
                <td align="right"><?php if ($duabulan != 0) {echo rupiah($duabulan);} ?></td>
                <td align="right"><?php if ($lebihtigabulan != 0) {echo rupiah($lebihtigabulan);} ?></td>
                <td align="right"><?php if ($enambulan != 0) {echo rupiah($enambulan);} ?></td>
                <td align="right"><?php if ($duabelasbulan != 0) {echo rupiah($duabelasbulan);} ?></td>
                <td align="right"><?php if ($duatahun != 0) {echo rupiah($duatahun);} ?></td>
                <td align="right"><?php if ($lebihduatahun != 0) {echo rupiah($lebihduatahun);} ?></td>
                <td align="right"><?php echo rupiah($total); ?></td>
            </tr>
            @php
            $totalpiutang = 0;
            $totalbayar = 0;
            $totalsisabayar = 0;
            $duaminggu = 0;
            $satubulan = 0;
            $satubulan15 = 0;
            $satusetengahbulan = 0;
            $duabulan = 0;
            $lebihtigabulan = 0;
            $enambulan = 0;
            $duabelasbulan = 0;
            $duatahun = 0;
            $lebihduatahun = 0;
            $total = 0;
            @endphp
            @endif
            @endforeach
            @php
            $totalall = $totalallduaminggu + $totalallsatubulan + $totalallsatubulan15 + $totalallduabulan + $totalalllebih3bulan + $totalallenambulan +
            $totalallduabelasbulan + $totalallduatahun + $totalalllebihduatahun;
            @endphp
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td colspan="5"><b>TOTAL</b></td>
                <td style="text-align: right;"><a href="/laporanpenjualan/detailaup/{{ $cbg }}/{{ $sales }}/{{ $idpel }}/{{ $tgl_aup }}/duaminggu/{{ $exclude }}" target="_blank"><?php echo number_format($totalallduaminggu, '0', '', '.');  ?></a></td>
                <td style="text-align: right;"><a href="/laporanpenjualan/detailaup/{{ $cbg }}/{{ $sales }}/{{ $idpel }}/{{ $tgl_aup }}/satubulan/{{ $exclude }}" target="_blank"><?php echo number_format($totalallsatubulan, '0', '', '.');  ?></a></td>

                <td style="text-align: right;"><a href="/laporanpenjualan/detailaup/{{ $cbg }}/{{ $sales }}/{{ $idpel }}/{{ $tgl_aup }}/satubulan15/{{ $exclude }}" target="_blank"><?php echo number_format($totalallsatubulan15, '0', '', '.');  ?></a></td>
                <td style="text-align: right;"><a href="/laporanpenjualan/detailaup/{{ $cbg }}/{{ $sales }}/{{ $idpel }}/{{ $tgl_aup }}/duabulan/{{ $exclude }}" target="_blank"><?php echo number_format($totalallduabulan, '0', '', '.');  ?></a></td>
                <td style="text-align: right;"><a href="/laporanpenjualan/detailaup/{{ $cbg }}/{{ $sales }}/{{ $idpel }}/{{ $tgl_aup }}/tigabulan/{{ $exclude }}" target="_blank"><?php echo number_format($totalalllebih3bulan, '0', '', '.');  ?></a></td>
                <td style="text-align: right;"><a href="/laporanpenjualan/detailaup/{{ $cbg }}/{{ $sales }}/{{ $idpel }}/{{ $tgl_aup }}/enambulan/{{ $exclude }}" target="_blank"><?php echo number_format($totalallenambulan, '0', '', '.');  ?></a></td>
                <td style="text-align: right;"><a href="/laporanpenjualan/detailaup/{{ $cbg }}/{{ $sales }}/{{ $idpel }}/{{ $tgl_aup }}/duabelasbulan/{{ $exclude }}" target="_blank"><?php echo number_format($totalallduabelasbulan, '0', '', '.');  ?></a></td>
                <td style="text-align: right;"><a href="/laporanpenjualan/detailaup/{{ $cbg }}/{{ $sales }}/{{ $idpel }}/{{ $tgl_aup }}/duatahun/{{ $exclude }}" target="_blank"><?php echo number_format($totalallduatahun, '0', '', '.');  ?></a></td>
                <td style="text-align: right;"><a href="/laporanpenjualan/detailaup/{{ $cbg }}/{{ $sales }}/{{ $idpel }}/{{ $tgl_aup }}/lebihduatahun/{{ $exclude }}" target="_blank"><?php echo number_format($totalalllebihduatahun, '0', '', '.');  ?></a></td>
                <td style="text-align: right;"><a href="/laporanpenjualan/detailaup/{{ $cbg }}/{{ $sales }}/{{ $idpel }}/{{ $tgl_aup }}/all/{{ $exclude }}" target="_blank"><?php echo number_format($totalall, '0', '', '.');  ?></a></td>

            </tr>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td colspan="5"><b>PERSENTASE</b></td>
                <td style="text-align: right"><?php echo round($totalallduaminggu / $totalall * 100) . "%";  ?></td>
                <td style="text-align: right"><?php echo round($totalallsatubulan / $totalall * 100) . "%"; ?></td>

                <td style="text-align: right"><?php echo round($totalallsatubulan15 / $totalall * 100) . "%";  ?></td>
                <td style="text-align: right"><?php echo round($totalallduabulan / $totalall * 100) . "%";  ?></td>
                <td style="text-align: right"><?php echo round($totalalllebih3bulan / $totalall * 100) . "%";   ?></td>
                <td style="text-align: right"><?php echo round($totalallenambulan / $totalall * 100) . "%";   ?></td>
                <td style="text-align: right"><?php echo round($totalallduabelasbulan / $totalall * 100) . "%";   ?></td>
                <td style="text-align: right"><?php echo round($totalallduatahun / $totalall * 100) . "%";   ?></td>
                <td style="text-align: right"><?php echo round($totalalllebihduatahun / $totalall * 100) . "%";   ?></td>
                <td style="text-align: right"></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
