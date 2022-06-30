<table class="table table-bordered">
    <tr>
        <td>No. Bukti</td>
        <td>{{ $pemasukan->nobukti_pemasukan }}</td>
    </tr>
    <tr>
        <td>Supplier</td>
        <td>{{ $pemasukan->nama_supplier }}</td>
    </tr>
    <tr>
        <td>Tanggal Pembelian</td>
        <td>{{ $pemasukan->tgl_pembelian != null ? DateToIndo2($pemasukan->tgl_pembelian) : '' }}</td>
    </tr>
    <tr>
        <td>Tanggal Approve</td>
        <td>{{ DateToIndo2($pemasukan->tgl_pemasukan) }}</td>
    </tr>
</table>
<table class="table table-hover-animation">
    <thead class="thead-dark">
        <tr>
            <th colspan="8">Data Pemasukan</th>
        </tr>
        <tr>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Keterangan</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Subtotal</th>
            <th>Penyesuaian</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @php
        $totalpembelian = 0;
        @endphp
        @foreach ($detail as $d)
        @php
        $total = ($d->qty * $d->harga) + $d->penyesuaian;
        $totalpembelian += $total;
        @endphp
        <tr>
            <td>{{ $d->kode_barang }}</td>
            <td>{{ $d->nama_barang }}</td>
            <td>{{ $d->keterangan }}</td>
            <td class="text-center">{{ desimal($d->qty) }}</td>
            <td class="text-right">{{ desimal($d->harga) }}</td>
            <td class="text-right">{{ desimal($d->harga * $d->qty) }}</td>
            <td class="text-right">{{ desimal($d->penyesuaian) }}</td>
            <td class="text-right">{{ desimal($total) }}</td>
        </tr>
        @endforeach
        <tr class="thead-dark">
            <th colspan="7">TOTAL</th>
            <th class="text-righ">{{ desimal($totalpembelian) }}</th>
        </tr>
    </tbody>
</table>
