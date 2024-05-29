<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Cetak Kontrak PKWT</title>

    <!-- Normalize or reset CSS with your favorite library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

    <!-- Load paper.css for happy printing -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

    <!-- Set page size here: A5, A4 or A3 -->
    <!-- Set also "landscape" if you need -->
    <style>
        @page {
            size: A4
        }

        body {
            font-family: 'Arial';
            font-size: 14px
        }

        .datatable3 {
            border: 1px solid #161616;
            border-collapse: collapse;
            font-size: 12px;
            width: 100%
        }

        .datatable3 td {
            border: 1px solid #000000;
            padding: 5px;
        }

        .datatable3 th {
            border: 1px solid #4d4d4d;
            font-weight: bold;
            text-align: left;
            padding: 4px;
            text-align: center;
            font-size: 14px;
            background-color: #d4d3d3cf
        }


        .datatable4 {
            border: 1px solid #161616;
            border-collapse: collapse;
            font-size: 16px;
            width: 100%
        }

        .datatable4 td {
            border: 1px solid #000000;
            padding: 3px;
            padding: 15px;
        }


        .datatable5 {
            border: 0px solid #161616;
            border-collapse: collapse;
            font-size: 14px;
            width: 100%
        }

        .datatable5 td {
            border: 0px solid #000000;
            padding: 3px;
            padding: 5px;
        }

        hr {
            display: block;
            height: 1px;
            background: transparent;
            width: 100%;
            border: none;
            border-top: solid 2px #101010;
        }
    </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->

<body class="A4">

    <!-- Each sheet element should have the class "sheet" -->
    <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
    <section class="sheet padding-10mm">
        <h1 style="text-align: center; letter-spacing:4px">KONTRABON</h1>
        <table class="datatable4">
            <tr>
                <td style="width: 50%">
                    <b>TERIMA DARI :</b> {{ strtoupper($kontrabon->nama_supplier) }}
                </td>
                <td style="text-align: center">
                    <b>TANGGAL</b>
                    <br>
                    {{ DateToIndo2($kontrabon->tgl_kontrabon) }}
                </td>
                <td style="text-align: center">
                    <b>NO. KONTRABON</b>
                    <br>
                    {{ $kontrabon->no_kontrabon }}
                </td>
            </tr>
        </table>
        <br>
        <table class="datatable3">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. BPPB</th>
                <th>No. Surat Jalan</th>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Jumlah</th>
            </tr>
            @php
                $totalpembelian = 0;
            @endphp
            @foreach ($detailkontrabon as $d)
                @php
                    $total = $d->qty * $d->harga + $d->penyesuaian;
                    $totalpembelian += $total;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ date('d-m-y', strtotime($d->tgl_pembelian)) }}</td>
                    <td>{{ $d->nobukti_pembelian }}</td>
                    <td></td>
                    <td>{{ $d->nama_barang }}</td>
                    <td align="center">{{ desimal($d->qty) }}</td>
                    <td align="right"> {{ desimal($d->harga) }}</td>
                    <td align="right"> {{ desimal($total) }}</td>
                </tr>
            @endforeach
            <tr>
                <th colspan="7">JUMLAH</th>
                <th align="right"> {{ desimal($totalpembelian) }}</th>
            </tr>
        </table>
        <br>
        <br>
        <table class="datatable5">
            <tr>
                <td>TERBILANG</td>
                <td><i>{{ ucwords(strtolower(terbilang($totalpembelian))) }}</i></td>
            </tr>
        </table>
        <br>
        <table class="datatable4">
            <tr>
                <td>DIBAYAR TANGGAL</td>
                <td>
                    {{ DateToIndo2($kontrabon->tgl_kontrabon) }}
                </td>
            </tr>
        </table>
        <br>
        <div style="display: flex; justify-content:space-between">
            <div style="width: 20%">
                Tembusan<br>
                <ol>
                    <li>Rekanan</li>
                    <li>Accounting</li>
                    <li>Adm</li>
                </ol>
            </div>
            <div style="width: 60%">
                <table class="datatable3">
                    <tr>
                        <td style="font-weight: bold">DITERIMA</td>
                        <td style="font-weight: bold">DIBUAT</td>
                        <td style="font-weight: bold">DIPERIKSA</td>
                    </tr>
                    <tr>
                        <td style="height: 100px"></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>

    </section>
</body>

</html>
