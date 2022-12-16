<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Rekap Per Pelanggan</title>
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
        REKAP PENJUALAN PER PELANGGAN<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
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
    <table class="datatable3" style="width:100%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td>No</td>
                <td>Kode Pel.</td>
                <td>Nama Pelanggan</td>
                <td>Nama Sales</td>
                <td>Pasar/Daerah</td>
                <td>Hari</td>
                <td>Total</td>
                <td>Potongan</td>
                <td>Potongan Istimewa</td>
                <td>Penyesuaian</td>
                <td>PPN 11%</td>
                <td>Penjualan Netto</td>
                <td>Total Retur</td>
                <td>Grand Total</td>
                <td>Rata-Rata</td>
            </tr>
        </thead>
        <tbody>
            @php
            $totalpenjualan = 0;
            $totalpotongan = 0;
            $totalpotistimewa = 0;
            $totalpenyharga = 0;
            $totalnetto = 0;
            $totalretur = 0;
            $grandtotalall = 0;
            $totalrata2=0;
            $rata2 = 0;
            $totalppn = 0;
            @endphp
            @foreach ($penjualan as $p)
            @php
            $totalpenjualan = $totalpenjualan + $p->totalpenjualan;
            $totalpotongan = $totalpotongan + $p->totalpotongan;
            $totalpotistimewa = $totalpotistimewa + $p->totalpotistimewa;
            $totalpenyharga = $totalpenyharga + $p->totalpenyharga;
            $totalnetto = $totalnetto + $p->totalpenjualannetto;
            $totalrata2 = $totalrata2 + $rata2;
            $grandtotal = $p->totalpenjualannetto - $p->totalretur;
            $totalretur = $totalretur + $p->totalretur;
            $totalppn = $totalppn + $p->ppn;
            $grandtotalall = $grandtotalall + $grandtotal;
            $pembagi = substr($sampai,5,2);;
            $rata2 = $grandtotal/$pembagi;
            @endphp
            <tr>
                <td>{{ $pembagi}}</td>
                <td>{{ $p->kode_pelanggan}}</td>
                <td>{{ $p->nama_pelanggan}}</td>
                <td>{{ $p->nama_karyawan}}</td>
                <td>{{ $p->pasar}}</td>
                <td>{{ $p->hari}}</td>
                <td align="right">{{ rupiah($p->totalpenjualan)}}</td>
                <td align="right">{{ rupiah($p->totalpotongan)}}</td>
                <td align="right">{{ rupiah($p->totalpotistimewa)}}</td>
                <td align="right">{{ rupiah($p->totalpenyharga)}}</td>
                <td align="right">{{ rupiah($p->ppn)}}</td>
                <td align="right">{{ rupiah($p->totalpenjualannetto)}}</td>
                <td align="right">{{ rupiah($p->totalretur)}}</td>
                <td align="right">{{ rupiah($grandtotal)}}</td>
                <td align="right">{{ rupiah($rata2)}}</td>
            </tr>
            @endforeach
            <tr bgcolor="#024a75" style="color:white; font-weight:bold">
                <td colspan="6">TOTAL</td>
                <td align="right">{{ rupiah($totalpenjualan)}}</td>
                <td align="right">{{ rupiah($totalpotongan)}}</td>
                <td align="right">{{ rupiah($totalpotistimewa)}}</td>
                <td align="right">{{ rupiah($totalpenyharga)}}</td>
                <td align="right">{{ rupiah($totalppn)}}</td>
                <td align="right">{{ rupiah($totalnetto)}}</td>
                <td align="right">{{ rupiah($totalretur)}}</td>
                <td align="right">{{ rupiah($grandtotalall)}}</td>
                <td align="right">{{ rupiah($totalrata2)}}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
