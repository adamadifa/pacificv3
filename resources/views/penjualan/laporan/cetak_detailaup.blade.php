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
        @php
        if ($kategori == "duaminggu") {
        $kategori = "2 MINGGU";
        } else if ($kategori == "satubulan") {
        $kategori = "1 BULAN";
        } else if ($kategori == "satubulan15") {
        $kategori = "> 1 BULAN s/d 46 HARI";
        } else if ($kategori == "duabulan") {
        $kategori = "> 46 HARI s/d 2 BULAN";
        } else if ($kategori == "tigabulan") {
        $kategori = "> 2 BULAN s/d 3 BULAN";
        } else if ($kategori == "enambulan") {
        $kategori = "> 3 BULAN s/d 6 BULAN";
        } else if ($kategori == "duabelasbulan") {
        $kategori = "> 6 BULAN s/d 1 TAHUN";
        } else if ($kategori == "duatahun") {
        $kategori = "> 1 TAHUN s/d 2 TAHUN";
        } else if ($kategori == "lebihduatahun") {
        $kategori = "> 2 TAHUN";
        } else {
        $kategori = "";
        }
        @endphp
        LAPORAN ANALISA UMUR PIUTANG {{ $kategori }}<br>
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
    <table class="datatable3">

        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th rowspan="2">No Faktur</th>
                <th rowspan="2">Tanggal Transaksi</th>
                <th rowspan="2">Jatuh Tempo</th>
                <th rowspan="2">Kode Pelanggan</th>
                <th rowspan="2">Nama Pelanggan</th>
                <th rowspan="2">Nama Salesman</th>
                <th rowspan="2">Pasar/Daerah</th>
                <th rowspan="2">HARI</th>
                <th rowspan="2">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            foreach ($detailaup as $r) {


                $total = $total + $r->jumlah;
                $jatuhtempo = date('Y-m-d', strtotime('+' . $r->jt + 1 . 'day', strtotime($r->tgltransaksi)));
                $hariini = date('Y-m-d');
                if ($hariini >= $jatuhtempo or empty($r->jt)) {
                    $color = "white";
                    $bg = "#c72c2c";
                } else {
                    $color = "";
                    $bg = "";
                }
            ?>
            <tr style="color:<?php echo $color; ?>; background-color:<?php echo $bg; ?>">
                <td><?php echo $r->no_fak_penj; ?></td>
                <td><?php echo DateToIndo2($r->tgltransaksi); ?></td>
                <td>
                    <?php
                        if (!empty($r->jt)) {
                            echo DateToIndo2($jatuhtempo);
                        } else {
                            echo '<span style="color:white; background-color:#f1881c; font-weight:bold">Belum Di Ajukan</span>';
                        }
                        ?></td>
                <td><?php echo $r->kode_pelanggan; ?></td>
                <td><?php echo $r->nama_pelanggan; ?></td>
                <td><?php echo $r->nama_karyawan; ?></td>
                <td><?php echo $r->pasar; ?></td>
                <td><?php echo $r->hari; ?></td>
                <td style="text-align: right"><?php echo number_format($r->jumlah, '0', '', '.'); ?></td>
            </tr>
            <?php

            }
            ?>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td colspan="8">TOTAL</td>
                <td style="text-align: right"><?php echo number_format($total, '0', '', '.'); ?></td>

            </tr>
        </tbody>
    </table>
</body>
</html>
