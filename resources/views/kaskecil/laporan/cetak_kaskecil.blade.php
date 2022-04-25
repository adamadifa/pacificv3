<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Kas Kecil {{ date("d-m-y") }}</title>
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
        @if ($cabang->kode_cabang=="PST")
        PACIFIC PUSAT
        @else
        PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
        @endif
        <br>
        LAPORAN KAS KECIL<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
    </b>
    <br>
    <table class="datatable3" style="width:80%">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>No</th>
                <th>TGL</th>
                <th>NO BUKTI</th>
                <th>KETERANGAN</th>
                <th>KODE AKUN</th>
                <th>NAMA AKUN</th>
                <th>PENERIMAAN</th>
                <th>PENGELUARAN</th>
                <th>SALDO</th>
                <th rowspan="2">TANGGAL INPUT</th>
                <th rowspan="2">TANGGAL UPDATE</th>
            </tr>
            <tr>
                <th bgcolor="orange" style="color:white; font-size:12;" colspan="8">SALDO AWAL</th>
                <th align="right">
                    @if (!empty($saldoawal->saldo_awal))
                    {{ rupiah($saldoawal->saldo_awal) }}
                    @endif
                </th>
            </tr>
        </thead>
        <tbody>
            @php
            $saldo = !empty($saldoawal->saldo_awal) ? $saldoawal->saldo_awal : 0;
            $totalpenerimaan = 0;
            $totalpengeluaran = 0;
            @endphp
            @foreach ($kaskecil as $d)
            @php
            if ($d->status_dk == 'K') {
            $penerimaan = $d->jumlah;
            $s = $penerimaan;
            $pengeluaran = "0";
            } else {
            $penerimaan = "0";
            $pengeluaran = $d->jumlah;
            $s = -$pengeluaran;
            }

            $totalpenerimaan = $totalpenerimaan + $penerimaan;
            $totalpengeluaran = $totalpengeluaran + $pengeluaran;
            $saldo = $saldo + $s;

            if ($d->no_ref != "") {
            $color = "#6db5c3";
            $text = "white";
            } else {
            $color = "";
            $text = "";
            }
            @endphp
            <tr style="color:{{ $text }}; background-color:{{ $color }} ">
                <td>{{ $loop->iteration }}</td>
                <td>{{ date("d-m-Y",strtotime($d->tgl_kaskecil)) }}</td>
                <td>{{ $d->nobukti; }}</td>
                <td style="width:25%">{{ ucwords(strtolower($d->keterangan)) }}</td>
                <td style="width:5%">{{ "'".$d->kode_akun }}</td>
                <td style="width:15%">{{ $d->nama_akun }}</td>
                <td align="right" class="success">
                    @if (!empty($penerimaan))
                    {{ rupiah($penerimaan) }}
                    @endif
                </td>
                <td align=" right" class="danger">
                    @if (!empty($pengeluaran))
                    {{ rupiah($pengeluaran) }}
                    @endif
                </td>
                <td align="right" class="info">
                    @if (!empty($saldo))
                    {{ rupiah($saldo) }}
                    @endif
                </td>
                <td>{{ date("d-M-Y H:i:s",strtotime($d->date_created)) }}</td>
                <td>{{ date("d-M-Y H:i:s",strtotime($d->date_updated)) }}</td>

            </tr>
            @endforeach
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td style="font-weight: bold" colspan="5">TOTAL</td>
                <td style="font-weight: bold; text-align:right">{{ rupiah($totalpenerimaan) }}</td>
                <td style="font-weight: bold; text-align:right">{{ rupiah($totalpengeluaran) }}</td>
                <td style="font-weight: bold; text-align:right">{{ rupiah($saldo) }}</td>
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>
    <br>
    <br>
    <b>
        REKAP KAS KECIL<br>
        PERIODE <?php echo DateToIndo2($dari) . " s/d " . DateToIndo2($sampai); ?><br>
    </b>
    <table class="datatable3">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>KODE AKUN</th>
                <th>AKUN</th>
                <th>PENERIMAAN</th>
                <th>PENGELUARAN</th>
            </tr>
        </thead>
        <tbody>
            @php
            $totalkredit = 0;
            $totaldebet = 0;
            @endphp
            @foreach ($rekap as $d)
            @php
            $totalkredit += $d->totalpemasukan;
            $totaldebet += $d->totalpengeluaran;
            @endphp
            <tr>
                <td>{{ "'".$d->kode_akun }}</td>
                <td>{{ $d->nama_akun }}</td>
                <td style="text-align:right">{{ !empty($d->totalpemasukan) ? rupiah($d->totalpemasukan) : '' }}</td>
                <td style="text-align:right">{{ !empty($d->totalpengeluaran) ? rupiah($d->totalpengeluaran) : '' }}</td>
            </tr>
            @endforeach
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th colspan="2">TOTAL</th>
                <th style="text-align:right">{{ !empty($totalkredit) ? rupiah($totalkredit) : '' }}</th>
                <th style="text-align:right">{{ !empty($totaldebet) ? rupiah($totaldebet) : '' }}</th>
            </tr>
        </tbody>
    </table>
</body>
</html>
