<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Tunai Kredit {{ date('d-m-y') }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&display=swap');

        body {
            font-family: 'Poppins'
        }

        .datatable3 {
            border: 2px solid #D6DDE6;
            border-collapse: collapse;
            font-size: 14px;
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
        @if ($cabang != null)
            @if ($cabang->kode_cabang == 'PST')
                PACIFIC PUSAT
            @else
                PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
            @endif
        @else
            PACIFC ALL CABANG
        @endif
        <br>
        REKAP SERVICE KENDARAAN
        <br>
        @if ($kendaraan != null)
            NO. POLISI : {{ $kendaraan->no_polisi }}
            <br>
            KENDARAAN : {{ $kendaraan->merk }} {{ $kendaraan->tipe_kendaraan }} {{ $kendaraan->tipe }}
        @endif
        <br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
    </b>
    <br>

    <?php
    $arr = [];
    foreach ($service as $row) {
        $arr[$row->no_invoice][] = $row;
    }
    
    //dd($arr);
    
    ?>
    <table class="datatable3">
        <thead>
            <tr>
                <th>No. Invoice</th>
                <th>Tanggal</th>
                <th>No. Polisi</th>
                <th>Kendraan</th>
                <th>Bengkel</th>
                <th>Cabang</th>
                <th>Item</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandtotal = 0;
            @endphp
            @foreach ($arr as $key => $val)
                @foreach ($val as $k => $d)
                    <tr>
                        @if ($k == 0)
                            <td rowspan="{{ count($val) }}">{{ $d->no_invoice }}</td>
                            <td rowspan="{{ count($val) }}">{{ DateToIndo2($d->tgl_service) }}</td>
                            <td rowspan="{{ count($val) }}">{{ $d->no_polisi }}</td>
                            <td rowspan="{{ count($val) }}">{{ $d->merk }} {{ $d->tipe_kendaraan }} {{ $d->tipe }}</td>
                            <td rowspan="{{ count($val) }}">{{ $d->nama_bengkel }}</td>
                            <td rowspan="{{ count($val) }}">{{ $d->kode_cabang }}</td>
                        @endif
                        @php
                            $subtotal = $d->qty * $d->harga;

                        @endphp
                        <td>{{ $d->nama_item }}</td>
                        <td style="text-align: center">{{ $d->qty }}</td>
                        <td style="text-align: right">{{ rupiah($d->harga) }}</td>
                        <td style="text-align: right">{{ rupiah($subtotal) }}</td>
                        @if ($k == 0)
                            @php
                                $grandtotal += $d->total;
                            @endphp
                            <td rowspan="{{ count($val) }}" style="text-align:right">{{ rupiah($d->total) }}</td>
                        @endif
                    </tr>
                @endforeach
            @endforeach
            <tr>
                <th colspan="10">TOTAL</th>
                <th style="text-align: right">{{ rupiah($grandtotal) }}</th>
            </tr>
        </tbody>
    </table>
</body>

</html>
