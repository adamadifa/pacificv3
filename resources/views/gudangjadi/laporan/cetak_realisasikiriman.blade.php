<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Realiasi Kiriman {{ date('d-m-y') }}</title>
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
        REALISASI KIRIMAN PRODUK {{ $cbg != null ? strtoupper($cbg->nama_cabang) : 'ALL CABANG' }}<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:60%" border="1">
        <thead>
            <tr bgcolor="#eda310" style="color:white; font-size:12;">
                <th>NO</th>
                <th>JENIS PRODUK</th>
                <th>TARGET</th>
                <th>PERMINTAAN</th>
                <th>REALISASI</th>
                <th>SISA TARGET</th>
                <th>REALISASI(%)</th>
                <th>TARGET(%)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $no =1;
                foreach($rekap as $r){

                    if(!empty($r->permintaan)){
                        $pmvsrealisasi = ($r->realisasi / $r->permintaan) * 100;
                    }else{
                        $pmvsrealisasi = 0;
                    }

                    if(!empty($r->target)){
                        $targetvsrealisasi = ($r->realisasi / $r->target) * 100;
                    }else{
                        $targetvsrealisasi = 0;
                    }
            ?>
            <tr style="  font-weight: bold; font-size:11px">
                <td><?php echo $no; ?></td>
                <td><?php echo $r->nama_barang; ?></td>
                <td><?php echo $r->target; ?></td>
                <td align="right"><?php if ($r->permintaan != 0) {
                    echo rupiah($r->permintaan);
                } ?></td>
                <td align="right"><?php if ($r->realisasi != 0) {
                    echo rupiah($r->realisasi);
                } ?></td>
                <td align="right"><?php if ($r->target != 0) {
                    $sisa = $r->target - $r->realisasi;
                    if ($sisa >= 0) {
                        echo rupiah($sisa);
                    }
                } ?></td>
                <td align="right"><?php if ($pmvsrealisasi != 0) {
                    echo round($pmvsrealisasi, 2) . '%';
                } ?></td>
                <td align="right"><?php if ($targetvsrealisasi != 0) {
                    echo round($targetvsrealisasi, 2) . '%';
                } ?></td>
            </tr>
            <?php
                $no++;
             }
            ?>
        </tbody>
</body>

</html>
