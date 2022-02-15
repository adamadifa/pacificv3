<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Rekap Penjualan All Cabang</title>
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

    </style>
</head>
<body>
    <b style="font-size:14px;">
        PACIFC ALL CABANG
        <br>
        LAPORAN REKAP PENJUALAN<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:60%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>CABANG</th>
                <th>TOTAL BRUTO</th>
                <th>TOTAL RETUR</th>
                <th>PENYESUAIAN</th>
                <th>POTONGAN</th>
                <th>POTONGAN ISTIMEWA</th>
                <th>TOTAL NETTO</th>
            </tr>
        </thead>
        <tbody>
            @php
            $totalbruto = 0;
            $totalretur = 0;
            $totalpenyharga = 0;
            $totalpotongan = 0;
            $totalpotistimewa = 0;
            $grandnetto = 0;
            @endphp
            @foreach ($penjualan as $r)
            @php
            $totalbruto = $totalbruto + $r->totalbruto;
            $totalretur = $totalretur + $r->totalretur;
            $totalpenyharga = $totalpenyharga + $r->totalpenyharga;
            $totalpotongan = $totalpotongan + $r->totalpotongan;
            $totalpotistimewa = $totalpotistimewa + $r->totalpotistimewa;

            $totalnetto = $r->totalbruto - $r->totalretur - $r->totalpenyharga - $r->totalpotongan - $r->totalpotistimewa;
            $grandnetto = $grandnetto + $totalnetto;
            @endphp
            <tr>
                <td style="font-weight:bold">{{ strtoUpper($r->nama_cabang)}}</td>
                <td style="text-align:right; font-weight:bold">{{ rupiah($r->totalbruto)}}</td>
                <td style="text-align:right; font-weight:bold">{{ rupiah($r->totalretur)}}</td>
                <td style="text-align:right; font-weight:bold">{{ rupiah($r->totalpenyharga)}}</td>
                <td style="text-align:right; font-weight:bold">{{ rupiah($r->totalpotongan)}}</td>
                <td style="text-align:right; font-weight:bold">{{ rupiah($r->totalpotistimewa)}}</td>
                <td style="text-align:right; font-weight:bold">{{ rupiah($totalnetto)}}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td style="font-weight:bold">TOTAL</td>
                <td style="text-align:right; font-weight:bold">{{ rupiah($totalbruto)}}</td>
                <td style="text-align:right; font-weight:bold">{{ rupiah($totalretur)}}</td>
                <td style="text-align:right; font-weight:bold">{{ rupiah($totalpenyharga)}}</td>
                <td style="text-align:right; font-weight:bold">{{ rupiah($totalpotongan)}}</td>
                <td style="text-align:right; font-weight:bold">{{ rupiah($totalpotistimewa)}}</td>
                <td style="text-align:right; font-weight:bold">{{ rupiah($grandnetto)}}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
