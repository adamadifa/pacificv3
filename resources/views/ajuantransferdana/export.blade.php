<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cost Ratio {{ date('d-m-y') }}</title>
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
        AJUAN TRANSFER DANA<br>
    </b>
    <br>

    <table class="datatable3" border="1">
        <tr>
            <th>No.Pengajuan</th>
            <th>Tanggal</th>
            <th>Nama</th>
            <th>Bank</th>
            <th>No. Rekening</th>
            <th>Jumlah</th>
            <th>Keterangan</th>
            <th>Cabang</th>
            <th>Validasi</th>
            <th>Tgl Proses</th>
        </tr>
        @php
            $total = 0;
        @endphp
        <tbody>
            @foreach ($ajuantransferdana as $d)
                @php
                    $total += $d->jumlah;
                @endphp
                <tr>
                    <td>{{ $d->no_pengajuan }}</td>
                    <td>{{ date('d-m-Y', strtotime($d->tgl_pengajuan)) }}</td>
                    <td>{{ $d->nama }}</td>
                    <td>{{ $d->nama_bank }}</td>
                    <td>{{ $d->no_rekening }}</td>
                    <td style="text-align: right">{{ rupiah($d->jumlah) }}</td>
                    <td>{{ $d->keterangan }}</td>
                    <td>{{ $d->kode_cabang }}</td>
                    <td>
                        @if (empty($d->validasi_manager))
                            <span class="badge bg-danger">Belum di Validasi</span>
                        @else
                            <span class="badge bg-success">Sudah di Validasi</span>
                        @endif
                    </td>
                    <td>
                        @if (empty($d->tgl_proses))
                            <span class="badge bg-danger">Belum di Proses</span>
                        @else
                            <span class="badge bg-success">{{ date('d-m-y', strtotime($d->tgl_proses)) }}</span>
                        @endif
                    </td>

                </tr>
            @endforeach
            <tr>
                <th colspan="5">TOTAL</th>
                <th style="text-align: right">{{ rupiah($total) }}</th>
            </tr>
    </table>
</body>

</html>
