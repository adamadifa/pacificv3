<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Akun {{ date("d-m-y") }}</title>
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
        REKAP AKUN<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:70%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12; text-align:center">
                <td>KODE AKUN</td>
                <td>NAMA AKUN</td>
                <td>TOTAL DEBET</td>
                <td>TOTAL KREDIT</td>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalkredit   = 0;
            $totaldebet    = 0;
            $no            = 1;
            foreach ($pmb as $key => $d) {
                if ($d->status == 'PNJ') {
                $debet      = $d->jurnaldebet;
                $kredit     = $d->total + $d->jurnalkredit;
                } else {
                $debet      = $d->total + $d->jurnaldebet;
                $kredit     = $d->jurnalkredit;
                }
                $totaldebet += $debet;
                $totalkredit += $kredit;

            ?>
            <tr>
                <td><?php echo "'" . $d->kode_akun; ?></td>
                <td><?php echo $d->nama_akun; ?></td>
                <td align="right"><?php if (!empty($debet)) {echo desimal($debet);} ?></td>
                <td align="right"><?php if (!empty($kredit)) {echo desimal($kredit);} ?></td>
            </tr>
            <?php
            $no++;
            }
            ?>
            <?php
            $totalkredits   = 0;
            $totaldebets    = 0;
            $no            = 1;
             ?>
            @php
            $totalhk = 0;
            $totalhd = 0;
            @endphp
            @foreach ($hutang as $d)
            @php
            $hutangkredit = $d->pmb + $d->jurnalkredit;
            $hutangdebet = $d->jurnaldebet;
            $totalhk += $hutangkredit;
            $totalhd += $hutangdebet;
            @endphp
            <tr>
                <td>{{ $d->kode_akun }}</td>
                <td>{{ $d->nama_akun }}</td>
                <td style="text-align: right">{{ rupiah($hutangdebet) }}</td>
                <td style="text-align: right">{{ rupiah($hutangkredit) }}</td>
            </tr>
            @endforeach
            <tr bgcolor="#024a75" style="color:white">
                <td colspan="2"><b>TOTAL</b></td>
                <td align="right"><b><?php echo desimal($totaldebet + $totalhd); ?></b></td>
                <td align="right"><b><?php echo desimal($totalkredit + $totalhk); ?></b></td>
            </tr>
        </tbody>

    </table>
</body>
</html>
