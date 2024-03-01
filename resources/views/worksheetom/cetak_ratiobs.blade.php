<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Ratio BS {{ date('d-m-y') }}</title>
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
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>

<body>

    <h4>
        RATIO BS
        <br>
        BULAN
        <br>
        TAHUN
    </h4>
    <table class="datatable3" style="width: 200%">
        <thead>
            <tr>
                <th rowspan="3">No.</th>
                <th rowspan="3">Cabang</th>
                <th colspan="{{ $produk->count() * 3 }}">Produk</th>
                <th rowspan="3">Total</th>
            </tr>
            <tr>
                @foreach ($produk->get() as $d)
                    <th colspan="3">{{ $d->kode_produk }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach ($produk->get() as $d)
                    <th>Reject</th>
                    <th>Harga</th>
                    <th>Total</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($ratiobs as $d)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ strtoupper($d->nama_cabang) }}</td>
                    @php
                        $totalharga = 0;
                    @endphp
                    @foreach ($produk->get() as $p)
                        @php
                            $jmlreject =
                                $d->{"reject_pasar_$p->kode_produk"} +
                                $d->{"reject_mobil_$p->kode_produk"} +
                                $d->{"reject_gudang_$p->kode_produk"} -
                                $d->{"repack_$p->kode_produk"};
                            $harga =
                                $d->{"retur_$p->kode_produk"} > 0
                                    ? $d->{"totalretur_$p->kode_produk"} / $d->{"retur_$p->kode_produk"}
                                    : 0;
                            $total = ROUND($jmlreject, 2) * $harga;
                            $totalharga += $total;
                        @endphp
                        <td align="center">{{ $jmlreject > 0 ? ROUND($jmlreject, 2) : '' }}</td>
                        <td align="right">{{ $harga > 0 ? rupiah($harga) : '' }}</td>
                        <td align="right">{{ $total > 0 ? rupiah($total) : '' }}</td>
                    @endforeach
                    <td align="right">{{ rupiah($totalharga) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
