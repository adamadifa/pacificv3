<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Angkutan {{ date("d-m-y") }}</title>
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
        LAPORAN ANGKUTAN<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        {{ !empty($angkutan) ? 'ANGKUTAN '. $angkutan : 'SEMUA ANGKUTAN' }}
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:100%" border="1">
        <thead>
            <tr>
                <th bgcolor="#024a75" style="color:white; font-size:14;">NO</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">TANGGAL MUTASI</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">NO. POL</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">ANGKUTAN</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">TUJUAN</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">NO SJ</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">KETERANGAN</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">TARIF</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">TEPUNG</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">BS</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">TOTAL</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">TGL KONTRABON</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">TGL BAYAR</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $tarif    = 0;
            $tepung   = 0;
            $total    = 0;
            $bs       = 0;
            $no       = 1;
            foreach ($rekap as $d ) {
                $tepung     += $d->tepung;
                $tarif      += $d->tarif;
                $bs         += $d->bs;
                $total      += $d->bs + $d->tepung + $d->tarif;
                $tgl_kontrabon    = $d->tgl_kontrabon;
                $tgl_bayar        = $d->tgl_bayar;

                if($d->tgl_bayar != NULL){
                    $bgcolor = "skyblue";
                }else{
                    $bgcolor = "";
                }
            ?>
            <tr>
                <td><?php echo $no++;?></td>
                <td><?php echo DateToIndo2($d->tgl_mutasi_gudang); ?></td>
                <td><?php echo $d->nopol; ?></td>
                <td><?php echo $d->angkutan; ?></td>
                <td><?php echo $d->tujuan; ?></td>
                <td><?php echo $d->no_surat_jalan; ?></td>
                <td><?php echo $d->keterangan; ?></td>
                <td align="right">{{ !empty($d->tarif) ? rupiah($d->tarif) : '' }}</td>
                <td align="right">{{ !empty($d->tepung) ? rupiah($d->tepung) : '' }}</td>
                <td align="right">{{ !empty($d->bs) ? rupiah($d->bs) : '' }}</td>
                <td align="right"><?php echo rupiah($d->bs+$d->tepung+$d->tarif); ?></td>
                <td><?php if($d->tgl_kontrabon != "") {  echo DateToIndo2($d->tgl_kontrabon); }else{ echo ""; } ?></td>
                <td bgcolor="<?php echo $bgcolor;?>"><?php if($d->tgl_bayar != "") {  echo DateToIndo2($d->tgl_bayar); }else{ echo ""; } ?></td>
            </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr bgcolor="#31869b">
                <th colspan="7" style="color:white; font-size:14;">TOTAL</th>
                <th style="color:white; font-size:14;text-align:right"><?php echo rupiah($tarif); ?></th>
                <th style="color:white; font-size:14;text-align:right"><?php echo rupiah($tepung); ?></th>
                <th style="color:white; font-size:14;text-align:right"><?php echo rupiah($bs); ?></th>
                <th style="color:white; font-size:14;text-align:right"><?php echo rupiah($total); ?></th>
                <th colspan="2" style="color:white; font-size:14;"></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
