<style>
    .sheet {

        overflow: hidden;
        position: relative;
        box-sizing: border-box;
    }

    /** Paper sizes **/
    body.A3 .sheet {
        width: 297mm;
        height: 419mm
    }

    body.A3.landscape .sheet {
        width: 420mm;
        height: 296mm
    }

    body.A4 .sheet {
        width: 210mm;
        height: 296mm
    }

    body.A4.landscape .sheet {
        width: 297mm;
        height: auto
    }

    body.A5 .sheet {
        width: 148mm;
        height: 209mm
    }

    body.A5.landscape .sheet {
        width: 210mm;
        height: 147mm
    }

    /** Padding area **/
    .sheet.padding-10mm {
        padding: 10mm
    }

    .sheet.padding-15mm {
        padding: 15mm
    }

    .sheet.padding-20mm {
        padding: 20mm
    }

    .sheet.padding-25mm {
        padding: 25mm
    }

    /** For screen preview **/
    @media screen {
        body {
            background: #e0e0e0
        }

        .sheet {
            background: white;
            box-shadow: 0 .5mm 2mm rgba(0, 0, 0, .3);
            margin: 5mm;
        }
    }



    .judul {
        font-family: Verdana, Geneva, Tahoma, sans-serif;
        font-size: 20px;
        text-align: center;
        color: #005e2f
    }

    .judul2 {
        font-family: Verdana, Geneva, Tahoma, sans-serif;
        font-size: 16px;
        text-align: center;

    }

    .huruf {
        font-family: Verdana, Geneva, Tahoma, sans-serif;
    }

    .ukuranhuruf {
        font-size: 12px;
    }

    .datatable3 {
        border: 2px solid #D6DDE6;
        border-collapse: collapse;
        /* font-size: 10px; */
        /*float:left; */
        font-family: 'Poppins';
        width: 100%;


    }

    .datatable3 td {
        border: 1px solid #000000;
        padding: 6px;
        font-size: 10px;
        font-family: 'Poppins';
        font-weight: 500
    }


    .datatable3 th {
        border: 1px solid #000000;
        font-weight: bold;
        padding: 4px;
        text-align: center;
        font-size: 12px;
        background-color: rgb(0, 68, 121);
        color: white;
    }

    hr.style2 {
        border-top: 3px double #8c8b8b;
    }

    body {
        font-family: 'Poppins' !important;
    }

    table {
        font-family: 'Poppins' !important,
            font-size:5px;
    }

    .box {
        border: 1px solid green;
        position: absolute;
        color: white;
        top: 19px;
        left: 30px;
        background-color: black;
    }

</style>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<body class="A4 landscape">
    <div class="row">
        <div class="col-10">
            <section class="sheet padding-10mm">
                <form action="/penjualan/store" method="POST">
                    <input type="hidden" name="jatuhtempo" value="{{ $data['jatuhtempo'] }}">
                    <input type="hidden" name="bruto" value="{{ $data['bruto'] }}">
                    <input type="hidden" name="kode_cabang" value="{{ $data['kode_cabang'] }}">
                    @csrf
                    <table style="width: 100%">
                        <tr>
                            <td style="width:5%" style="text-align: center">
                                <img src="{{asset('app-assets/images/logo/mp.png')}}" alt="" style="width: 80px; margin-left: 15px; margin-top:10px">
                            </td>
                            <td style="width: 40%">
                                <b>CV. MAKMUR PERMATA & CV. PACIFIC</b>
                                <br>
                                <span style="font-size: 12px">
                                    Jl. Perintis Kemerdekaan No.160, Karsamenak, Kec. Kawalu, Kab. Tasikmalaya, Jawa Barat 46182
                                </span>
                            </td>
                            <td style="width: 50%; text-align: right">
                                <span style="font-size: 12px">
                                    <b>Petugas</b> : {{ Auth::user()->name }}
                                    <br>
                                    {{ DateToIndo2(date("Y-m-d"))}},{{ date("H:i:s") }}
                                </span>
                            </td>
                        </tr>

                    </table>
                    <br>
                    <table style="width:100%; font-size:12px">
                        <tr>
                            <td style="font-weight: bold">No. Faktur</td>
                            <td>
                                {{ $data['no_fak_penj'] }}
                                <input type="hidden" name="no_fak_penj" value="{{ $data['no_fak_penj'] }}">
                            </td>
                            <td style="font-weight: bold">Kode Pelanggan</td>
                            <td>
                                {{ $data['kode_pelanggan'] }}
                                <input type="hidden" name="kode_pelanggan" value="{{ $data['kode_pelanggan'] }}">
                            </td>
                            <td style="font-weight: bold">ID Salesman</td>
                            <td>
                                {{ $data['id_karyawan'] }}
                                <input type="hidden" name="id_karyawan" value="{{ $data['id_karyawan'] }}">
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold">Tanggal</td>
                            <td>
                                {{ DateToIndo2($data['tgltransaksi']) }}
                                <input type="hidden" name="tgltransaksi" value="{{ $data['tgltransaksi'] }}">
                            </td>
                            <td style="font-weight: bold">Nama Pelanggan</td>
                            <td>
                                {{ $data['pelanggan']['nama_pelanggan'] }}
                                <input type="hidden" name="nama_pelanggan" value="{{ $data['pelanggan']['nama_pelanggan'] }}">
                            </td>
                            <td style="font-weight: bold">Salesman</td>
                            <td>
                                {{ $data['salesman']['nama_karyawan'] }} ({{ $data['salesman']['kategori_salesman'] }})
                                <input type="hidden" name="nama_salesman" value="{{ $data['salesman']['nama_karyawan'] }}">
                            </td>
                        </tr>
                    </table>
                    <table class="datatable3">
                        <tr>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Dus/Ball</th>
                            <th>Harga /Dus/Ball</th>
                            <th>Pack</th>
                            <th>Harga/Pack</th>
                            <th>Pcs</th>
                            <th>Harga/Pcs</th>
                            <th>Subtotal</th>
                        </tr>

                        @php
                        $total = 0;
                        @endphp
                        @foreach ($barang as $d)
                        @php
                        $jmldus = floor($d->jumlah / $d->isipcsdus);
                        $sisadus = $d->jumlah % $d->isipcsdus;

                        if ($d->isipack == 0) {
                        $jmlpack = 0;
                        $sisapack = $sisadus;
                        } else {

                        $jmlpack = floor($sisadus / $d->isipcs);
                        $sisapack = $sisadus % $d->isipcs;
                        }

                        $jmlpcs = $sisapack;
                        $total += $d->subtotal;
                        @endphp
                        <tr style="background-color:{{ $d->promo ==1 ? 'yellow' : '' }};">
                            <td>{{ $d->kode_barang }}</td>
                            <td>{{ $d->nama_barang }}</td>
                            <td style="text-align: center">{{ $jmldus }}</td>

                            <td style="text-align: right; {{ $d->harga_dus < $d->harga_dus_db ? 'background-color:red; color:white' : '' }}"><a href="#" @if($d->harga_dus < $d->harga_dus_db) data-toggle="tooltip" title="Harga Faktur Lebih Rendah Dari Harga Database" @endif style="text-decoration: none; color:inherit">{{ rupiah($d->harga_dus) }}</a></td>


                            <td style="text-align: center">{{ $jmlpack }}</td>

                            <td style="text-align: right; {{ $d->harga_pack < $d->harga_pack_db ? 'background-color:red; color:white' : '' }}"><a href="#" @if($d->harga_pack < $d->harga_pack_db) data-toggle="tooltip" title="Harga Faktur Lebih Rendah Dari Harga Database" @endif style="text-decoration: none; color:inherit">{{ rupiah($d->harga_pack) }}</a></td>

                            <td style="text-align: center">{{ $jmlpcs }}</td>

                            <td style="text-align: right; {{ $d->harga_pcs < $d->harga_pcs_db ? 'background-color:red; color:white' : '' }}"><a href="#" @if($d->harga_pcs < $d->harga_pcs_db) data-toggle="tooltip" title="Harga Faktur Lebih Rendah Dari Harga Database" @endif style="text-decoration: none; color:inherit">{{ rupiah($d->harga_pcs) }}</a></td>

                            <td style="text-align: right">{{ rupiah($d->subtotal) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <th colspan="8">TOTAL</th>
                            <th style="text-align: right">{{ rupiah($total) }}</th>
                        </tr>
                    </table>
                    <br>
                    <table class="table">
                        <tr>
                            <td style="vertical-align: top" width="35%">
                                <table style="width: 100%">
                                    <tr>
                                        <td style="font-size:12px; font-weight:bold; vertical-align:top">Alamat</td>
                                        <td style="font-size:12px; font-weight:500; text-align:right">{{ ucwords(strtolower($data['pelanggan']['alamat_pelanggan'])); }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px; font-weight:bold; vertical-align:top">No. HP</td>
                                        <td style="font-size:12px; font-weight:500;text-align:right">{{ ucwords(strtolower($data['pelanggan']['no_hp'])); }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px; font-weight:bold; vertical-align:top">Koordinat</td>
                                        <td style="font-size:12px; font-weight:500;text-align:right">{{ ucwords(strtolower($data['pelanggan']['latitude'])); }},{{ ucwords(strtolower($data['pelanggan']['longitude'])); }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px; font-weight:bold; vertical-align:top">Limit</td>
                                        <td style="font-size:12px; font-weight:500; text-align:right">
                                            {{ ucwords(rupiah($data['pelanggan']['limitpel'])); }}
                                            <input type="hidden" name="limitpel" value="{{ $data['pelanggan']['limitpel'] }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px; font-weight:bold; vertical-align:top">Piutang</td>
                                        <td style="font-size:12px; font-weight:500; text-align:right">{{ ucwords(rupiah($data['sisapiutang'])); }}
                                            <input type="hidden" name="sisapiutang" value="{{ $data['sisapiutang'] }}">
                                        </td>
                                    </tr>

                                </table>
                            </td>
                            <td style="vertical-align: top" width="15%">
                                <table style="width: 100%">
                                    <tr>
                                        <td style="font-size: 12px; font-weight:bold" colspan="2">Potongan</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-size:12px; vertical-align:top">AIDA</td>
                                        <td style="text-align: right; font-size:12px; font-weight:500">
                                            {{ rupiah($data['potaida']) }}
                                            <input type="hidden" name="potaida" value="{{ $data['potaida'] }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-size:12px; vertical-align:top">SWAN</td>
                                        <td style="text-align: right; font-size:12px; font-weight:500">
                                            {{ rupiah($data['potswan']) }}
                                            <input type="hidden" name="potswan" value="{{ $data['potswan'] }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-size:12px; vertical-align:top">STICK</td>
                                        <td style="text-align: right; font-size:12px; font-weight:500">
                                            {{ rupiah($data['potstick']) }}
                                            <input type="hidden" name="potstick" value="{{ $data['potstick'] }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-size:12px; vertical-align:top">SAUS PREMIUM</td>
                                        <td style="text-align: right; font-size:12px; font-weight:500">
                                            {{ rupiah($data['potsp']) }}
                                            <input type="hidden" name="potsp" value="{{ $data['potsp'] }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-size:12px; vertical-align:top">SAMBAL CABE</td>
                                        <td style="text-align: right; font-size:12px; font-weight:500">
                                            {{ rupiah($data['potsb']) }}
                                            <input type="hidden" name="potsb" value="{{ $data['potsb'] }}">
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="vertical-align: top" width="15%">
                                <table style="width: 100%">
                                    <tr>
                                        <td style="font-size: 12px; font-weight:bold" colspan="2">Potongan Istimewa</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-size:12px; vertical-align:top">AIDA</td>
                                        <td style="text-align: right; font-size:12px; font-weight:500">
                                            {{ rupiah($data['potisaida']) }}
                                            <input type="hidden" name="potisaida" value="{{ $data['potisaida'] }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-size:12px; vertical-align:top">SWAN</td>
                                        <td style="text-align: right; font-size:12px; font-weight:500">
                                            {{ rupiah($data['potisswan']) }}
                                            <input type="hidden" name="potisswan" value="{{ $data['potisswan'] }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-size:12px; vertical-align:top">STICK</td>
                                        <td style="text-align: right; font-size:12px; font-weight:500">
                                            {{ rupiah($data['potisstick']) }}
                                            <input type="hidden" name="potisstick" value="{{ $data['potisstick'] }}">
                                        </td>
                                    </tr>

                                </table>
                            </td>
                            <td style="vertical-align: top" width="15%">
                                <table style="width: 100%">
                                    <tr>
                                        <td style="font-size: 12px; font-weight:bold" colspan="2">Penyesuaian</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-size:12px; vertical-align:top">AIDA</td>
                                        <td style="text-align: right; font-size:12px; font-weight:500">
                                            {{ rupiah($data['penyaida']) }}
                                            <input type="hidden" name="penyaida" value="{{ $data['penyaida'] }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-size:12px; vertical-align:top">SWAN</td>
                                        <td style="text-align: right; font-size:12px; font-weight:500">
                                            {{ rupiah($data['penyswan']) }}
                                            <input type="hidden" name="penyswan" value="{{ $data['penyswan'] }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-size:12px; vertical-align:top">STICK</td>
                                        <td style="text-align: right; font-size:12px; font-weight:500">
                                            {{ rupiah($data['penystick']) }}
                                            <input type="hidden" name="penystick" value="{{ $data['penystick'] }}">
                                        </td>
                                    </tr>

                                </table>
                            </td>
                            <td style="vertical-align: top" width="35%">
                                <table style="width: 100%">
                                    <tr>
                                        <td style="font-size: 12px; font-weight:bold" colspan="2">Total & Pembayaran</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-size:12px; vertical-align:top">Jenis Transaksi</td>
                                        <td style="text-align: right; font-size:12px; font-weight:500">
                                            {{ ucwords(strtolower($data['jenistransaksi'])) }}
                                            <input type="hidden" name="jenistransaksi" value="{{ $data['jenistransaksi'] }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-size:12px; vertical-align:top">Jenis Pembayaran</td>
                                        <td style="text-align: right; font-size:12px; font-weight:500">
                                            {{ ucwords(strtolower($data['jenisbayar'])) }}
                                            <input type="hidden" name="jenisbayartunai" value="{{ $data['jenisbayartunai'] }}">
                                            <input type="hidden" name="jenisbayar" value="{{ $data['jenisbayar'] }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-size:12px; vertical-align:top">Voucher</td>
                                        <td style="text-align: right; font-size:12px; font-weight:500">
                                            {{ rupiah($data['voucher']) }}
                                            <input type="hidden" name="voucher" value="{{ $data['voucher'] }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-size:12px; vertical-align:top">Total</td>
                                        <td style="text-align: right; font-size:12px; font-weight:500">
                                            {{ rupiah($data['totalnonppn']) }}
                                            <input type="hidden" name="totalnonppn" value="{{ $data['totalnonppn'] }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; font-size:12px; vertical-align:top">PPN 11%</td>
                                        <td style="text-align: right; font-size:12px; font-weight:500">
                                            {{ rupiah($data['ppn']) }}
                                            <input type="hidden" name="ppn" value="{{ rupiah($data['ppn']) }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        @php
                                        $grandtotal = $total - $data['totalpotongan'] - $data['totalpotis'] - $data['totalpeny'] - $data['voucher'] + $data['ppn'];


                                        @endphp
                                        <td style="font-weight: bold; font-size:12px; vertical-align:top">Grand Total</td>
                                        <td style="text-align: right; font-size:12px; font-weight:700; {{ $data['subtotal'] != $grandtotal ? 'background-color:red; color:white' : '' }}">
                                            <a href="#" @if($data['subtotal'] !=$grandtotal) data-toggle="tooltip" title="Total Tidak Sama Dengan Rincian Penjualan - Potongan" @endif style="text-decoration: none; color:inherit">{{ rupiah($data['subtotal']) }}</a>
                                            <input type="hidden" name="subtotal" value="{{ $data['subtotal'] }}">
                                        </td>
                                    </tr>
                                    @if ($data['jenistransaksi']=="kredit")
                                    <tr>
                                        <td style="font-weight: bold; font-size:12px; vertical-align:top">Titipan</td>
                                        <td style="text-align: right; font-size:12px; font-weight:500">
                                            {{ rupiah($data['titipan']) }}
                                            <input type="hidden" name="titipan" value="{{ $data['titipan'] }}">
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </td>
                        </tr>
                    </table>

                    <br>

                    <table class="datatable3" style="width: 40%">
                        <tr>
                            <th colspan="3">List Faktur Belum Lunas</th>
                        </tr>
                        <tr>
                            <th>No. Faktur</th>
                            <th style="text-align:left">Tgl Transaksi</th>
                            <th>Sisa Piutang</th>
                        </tr>
                        @php
                        $totalpiutang = 0;
                        @endphp
                        @foreach ($piutang as $d)
                        @php
                        $totalpiutang += $d->sisapiutang;
                        @endphp
                        <tr>
                            <td style="text-align: center">{{ $d->no_fak_penj }}</td>
                            <td>{{ DateToIndo2($d->tgltransaksi) }}</td>
                            <td style="text-align: right">{{ rupiah($d->sisapiutang) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <th colspan="3" style="text-align: right">{{ rupiah($totalpiutang) }}</th>
                        </tr>
                    </table>
                    <br>
                    <br>
                    <br>
                    <div class="col-12" style="display: flex; justify-content:space-between;">
                        <a href="{{ URL::previous() }}" class="btn btn-danger" style="display: absolute; bottom:0">Kembali dan Perbaiki</a>
                        <button class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</body>
<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });

</script>
