<style>
    body {
        letter-spacing: 0px;
        font-family: Calibri;
        font-size: 14px;
    }

    table {
        font-family: Tahoma;
        font-size: 14px;
    }

    .garis5,
    .garis5 td,
    .garis5 tr,
    .garis5 th {
        border: 2px solid black;
        border-collapse: collapse;
    }

    .table {
        border: solid 1px #000000;
        width: 100%;
        font-size: 12px;
        margin: auto;
    }

    .table th {
        border: 1px #000000;
        font-size: 12px;

        font-family: Arial;
    }

    .table td {
        border: solid 1px #000000;
    }

</style>
<table border="0" width="100%">
    <tr>
        <td style="width:150px">
            <table class="garis5">
                <tr>
                    <td>SURAT JALAN</td>
                </tr>
                <tr>
                    <td>NOMOR {{ $faktur->no_fak_penj }}</td>
                </tr>
            </table>
        </td>
        <td colspan="6" align="left">
            <b>
                @if (in_array($faktur->kode_pelanggan,$pelangganmp))
                <b>CV MAKMUR PERMATA </b><br>
                <b>Jln. Perintis Kemerdekaan RT 001 / RW 003 Kelurahan Karsamenak Kecamatan Kawalu Kota Tasikmalaya 46182 <br>
                    NPWP : 863860342425000</b>
                @else
                <b>
                    <b>CV PACIFIC CABANG {{ strtoupper($faktur->nama_cabang) }}</b><br>
                    <b>{{ $faktur->alamat_cabang }}</b>
                </b>
                @endif
            </b>
        </td>
    </tr>
    <tr>
        <td colspan="7" align="center">
            <hr>
        </td>
    </tr>
    @if ($faktur->kode_cabang=="BDG")
    <tr>
        <td width="10%">Tgl Faktur</td>
        <td width="1%">:</td>
        <td width="25%">{{ DateToIndo2($faktur->tgltransaksi) }}</td>
        <td>Nama Customer</td>
        <td>:</td>
        <td><b>{{ $faktur->kode_pelanggan }}</b> - {{ $faktur->nama_pelanggan }}</td>
    </tr>
    <tr>
        <td>No. Kendaraan</td>
        <td>:</td>
        <td></td>
        <td>Salesman</td>
        <td>:</td>
        <td><b>{{ $faktur->id_karyawan }}</b> - {{ $faktur->nama_karyawan }}</td>
    </tr>
    <tr>
        <td>Jenis Transaksi</td>
        <td>:</td>
        <td>{{ ucwords($faktur->jenistransaksi) }}</td>
        <td>Alamat</td>
        <td>:</td>
        <td>
            @if (!empty($faktur->alamat_toko))
            {{ $faktur->alamat_toko }}
            @else
            {{ $faktur->alamat_pelanggan }}
            @endif
        </td>
    </tr>
    <tr>
        <td>Pola Operasi</td>
        <td>:</td>
        <td>{{ $faktur->kategori_salesman }}</td>
    </tr>
    @else
    <tr>
        <td width="10%">Tgl Faktur</td>
        <td width="1%">:</td>
        <td width="25%">{{ DateToIndo2($faktur->tgltransaksi) }}</td>
        <td>Nama Customer</td>
        <td>:</td>
        <td>{{ $faktur->nama_pelanggan }}</td>
    </tr>
    <tr>
        <td>No. Kendaraan</td>
        <td>:</td>
        <td></td>
        <td>Alamat</td>
        <td>:</td>
        <td>
            @if (!empty($faktur->alamat_toko))
            {{ $faktur->alamat_toko }}
            @else
            {{ $faktur->alamat_pelanggan }}
            @endif
        </td>
    </tr>
    <tr>
        <td>Jenis Transaksi</td>
        <td>:</td>
        <td>{{ ucwords($faktur->jenistransaksi) }}</td>
    </tr>
    @endif

    <tr>
        <td colspan="7">
            <table class="garis5" width="100%">
                <thead>
                    <tr style="padding: 10px">
                        <th rowspan="2">NO</th>
                        <th rowspan="2">KODE BARANG</th>
                        <th rowspan="2">NAMA BARANG</th>
                        <th rowspan="2">HARGA</th>
                        <th colspan="3">JUMLAH</th>
                        <th rowspan="2">TOTAL</th>
                    </tr>
                    <tr>
                        <th>DUS</th>
                        <th>PACK</th>
                        <th>PCS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detail as $b)
                    @php
                    $jmldus = floor($b->jumlah / $b->isipcsdus);
                    $sisadus = $b->jumlah % $b->isipcsdus;
                    if ($b->isipack == 0) {
                    $jmlpack = 0;
                    $sisapack = $sisadus;
                    } else {

                    $jmlpack = floor($sisadus / $b->isipcs);
                    $sisapack = $sisadus % $b->isipcs;
                    }
                    $jmlpcs = $sisapack;
                    @endphp
                    <tr style="padding:  10px">
                        <td align="center">{{ $loop->iteration }}</td>
                        <td>{{ $b->kode_barang }}</td>
                        <td>{{ $b->nama_barang }}</td>
                        <td align="right">{{ rupiah($b->harga_dus) }}</td>
                        <td align="center"><?php echo $jmldus; ?></td>
                        <td align="center"><?php echo $jmlpack; ?></td>
                        <td align="center"><?php echo $jmlpcs; ?></td>
                        <td align="right">{{ rupiah($b->subtotal) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <table class="garis5" width="100%">
            <tr style="font-weight:bold; text-align:center">
                <td>Dibuat</td>
                <td>Diserahkan</td>
                <td>Diterima</td>
                <td>Mengetahui</td>
                <td>Jam Masuk</td>
            </tr>
            <tr style="font-weight:bold;">
                <td rowspan="3"></td>
                <td rowspan="3"></td>
                <td rowspan="3"></td>
                <td rowspan="3"></td>

            </tr>
            <tr>
                <td style="height: 20px"></td>
            </tr>
            <tr>
                <td style="font-weight:bold; text-align:center">Jam Keluar</td>
            </tr>
            <tr style="font-weight:bold; text-align:center">
                <td>Penjualan</td>
                <td>Pengirim</td>
                <td>Pelanggan</td>
                <td>Security</td>
                <td></td>
            </tr>
        </table>
    </tr>
</table>
<br>
<p style="font-weight: bold; font-size: 18px"><i>Catatan : Mohon tidak untuk menulis Retur / Bs di surat Jalan !</i></p>
