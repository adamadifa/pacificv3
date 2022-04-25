<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Ledger Bank {{ $bank->nama_bank }} {{ date("d-m-y") }}</title>
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
        LEDGER {{ $bank->nama_bank }}
        <br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
    </b>
    <br>
    <table class="datatable3">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>No</th>
                <th>No Bukti</th>
                <th>TGL</th>
                <th>TGL Penerimaan</th>
                <th>Pelanggan</th>
                <th style="width:10%">Keterangan</th>
                <th>Peruntukan</th>
                <th>Kode Akun</th>
                <th>Akun</th>
                <th>Debet</th>
                <th>Kredit</th>
                <th>Saldo</th>
                <th rowspan="2">Tanggal Input</th>
                <th rowspan="2">Tanggal Update</th>
            <tr>
                <th colspan='11'>SALDO AWAL</th>
                <th style="text-align:right">{{ desimal($saldoawal) }}</th>
            </tr>
        </thead>
        <tbody>
            @php
            $totaldebet = 0;
            $totalkredit = 0;
            $saldo = $saldoawal;
            @endphp
            @foreach ($ledger as $d)
            @php
            if ($d->status_dk == 'K') {
            $kredit = $d->jumlah;
            $debet = 0;
            $jumlah = $d->jumlah;
            } else {
            $debet = $d->jumlah;
            $kredit = 0;
            $jumlah = -$d->jumlah;
            }
            $saldo = $saldo + $jumlah;
            $totaldebet = $totaldebet + $debet;
            $totalkredit = $totalkredit + $kredit;
            @endphp
            <tr>
                <td style="text-align:center">{{ $loop->iteration }}</td>
                <td style="text-align:center">{{ $d->no_bukti }}</td>
                <td style="text-align:center">{{ date("d-m-Y",strtotime($d->tgl_ledger)) }}</td>
                <td style="text-align:center">{{ !empty($d->tgl_penerimaan) ? date("d-m-Y",strtotime($d->tgl_penerimaan)) : '' }}</td>
                <td>{{ ucwords(strtolower($d->pelanggan)) }}</td>
                <td>{{ ucwords(strtoupper($d->keterangan)) }}</td>
                <td>
                    @if ($d->peruntukan =="PC")
                    PACIFIC {{ $d->ket_peruntukan }}
                    @else
                    {{ $d->peruntukan }}
                    @endif
                </td>
                <td>{{ "'".$d->kode_akun }}</td>
                <td>{{ $d->nama_akun }}</td>
                <td style="text-align:right">{{ !empty($debet) ? desimal($debet) : '' }}</td>
                <td style="text-align:right">{{ !empty($kredit) ? desimal($kredit) : '' }}</td>
                <td style="text-align:right">{{ !empty($saldo) ? desimal($saldo) : '' }}</td>
                <td>{{ date("d-m-Y H:i:s",strtotime($d->date_created)) }}</td>
                <td>{{ !empty($d->date_updated) ?  date("d-m-Y H:i:s",strtotime($d->date_updated)) : '' }}</td>
            </tr>
            @endforeach
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th colspan="9">TOTAL</th>
                <th style="text-align: right">{{ desimal($totaldebet) }}</th>
                <th style="text-align: right">{{ desimal($totalkredit) }}</th>
                <th style="text-align: right">{{ desimal($saldo) }}</th>
                <th></th>
                <th></th>
            </tr>
        </tbody>

</body>
</html>
