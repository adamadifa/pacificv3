<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Buku Besar {{ date("d-m-y") }}</title>
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

        .table-scroll {
            position: relative;
            max-width: 100%;
            margin: auto;
            overflow: hidden;

        }

        .table-wrap {
            width: 100%;
            overflow: auto;
        }

        .table-scroll table {
            width: 100%;
            margin: auto;
            border-collapse: separate;
            border-spacing: 0;
        }


        .clone {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
        }

        .clone th,
        .clone td {
            visibility: hidden
        }

        .clone td,
        .clone th {
            border-color: transparent
        }

        .clone tbody th {
            visibility: visible;
            color: red;
        }

        .clone .fixed-side {
            border: 1px solid #000;
            background: #eee;
            visibility: visible;
        }

    </style>
</head>
<body>
    <b style="font-size:14px;">
        JURNAL UMUM <br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }} <br>
        {{ $departemen != null ? 'DEPARTEMEN '. $departemen->nama_dept : '' }}
        <br>
        <table class="datatable3" style="width:90%" border="1">
            <thead>
                <tr>
                    <th style="width: 10%;">TGL</th>
                    <th>NO BUKTI</th>
                    <th>KETERANGAN</th>
                    <th>KODE AKUN</th>
                    <th>NAMA AKUN</th>
                    <th>DEBET</th>
                    <th>KREDIT</th>
                </tr>
            </thead>
            <tbody>
                @php
                $totaldebet = 0;
                $totalkredit = 0;
                @endphp
                @foreach ($jurnalumum as $d)
                @php
                if($d->status_dk=="D"){
                $debet = $d->jumlah;
                $kredit = 0;
                }else{
                $debet = 0;
                $kredit = $d->jumlah;
                }
                $totaldebet += $debet;
                $totalkredit += $kredit;
                @endphp
                <tr>
                    <td>{{ DateToIndo2($d->tanggal) }}</td>
                    <td>{{ $d->kode_jurnal }}</td>
                    <td>{{ $d->keterangan }}</td>
                    <td>{{ $d->kode_akun }}</td>
                    <td>{{ $d->nama_akun }}</td>
                    <td align="right">{{ !empty($debet) ? desimal($debet) : '' }}</td>
                    <td align="right">{{ !empty($kredit) ? desimal($kredit) : '' }}</td>
                </tr>
                @endforeach
                <tr>
                    <th colspan="5">TOTAL</th>
                    <th style="text-align: right">{{ desimal($totaldebet)}}</th>
                    <th style="text-align: right">{{ desimal($totalkredit)}}</th>
                </tr>
            </tbody>
        </table>

</body>
</html>
