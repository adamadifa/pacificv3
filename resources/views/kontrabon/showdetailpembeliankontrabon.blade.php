<table class="table table-hover-animation">
    <thead class="thead-dark">
        <tr>
            <th colspan="9">Data Pembelian <span id="nobuktipembelian"></span></th>
        </tr>
        <tr>
            <th>No</th>
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
    @php
    $grandtotal = 0;
    $jmldata = 1;
    @endphp
    @foreach ($detail as $d)
    @php
    $subtotal = $d->harga * $d->qty;
    $total = $subtotal + $d->penyesuaian;
    $grandtotal += $total;
    @endphp
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $d->kode_barang }}</td>
        <td>{{ $d->nama_barang }}</td>
        <td>{{ $d->keterangan }}</td>
        <td class="text-center">{{ desimal($d->qty) }}</td>
        <td class="text-right">{{ desimal($d->harga) }}</td>
        <td class="text-right">{{ desimal($subtotal) }}</td>
        <td class="text-right">{{ desimal($d->penyesuaian) }}</td>
        <td class="text-right">{{ desimal($total) }}</td>
    </tr>
    @php
    $jmldata++;
    @endphp
    @endforeach
    <tr class="thead-dark">
        <th colspan="8" style="font-size: 14px">TOTAL</th>
        <th class="text-right" style="font-size: 14px" id="grandtotaltemp">{{ desimal($grandtotal) }}<input type="hidden" id="jmldata" value="{{ $jmldata -1 }}"></th>
    </tr>
</table>
<table class="table table-hover-animation">
    <thead class="thead-danger">
        <tr>
            <th colspan="4">POTONGAN</th>
        </tr>
        <tr>
            <th>Keterangan</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Total</th>
        </tr>
    </thead>
    @php
    $totalpenjualan =0;
    @endphp
    @foreach ($detailpenjualan as $d)
    @php
    $total = $d->qty * $d->harga;
    $totalpenjualan += $total;
    @endphp
    <tr>
        <td>{{ $d->ket_penjualan }}</td>
        <td class="text-center">{{ desimal($d->qty) }}</td>
        <td class="text-right">{{ desimal($d->harga) }}</td>
        <td class="text-right">{{ desimal($total) }}</td>
    </tr>
    @endforeach
    <tr class="thead-danger">
        <th colspan="3">TOTAL POTONGAN</th>
        <th class="text-right">{{ desimal($totalpenjualan) }}</th>
    </tr>
    <tr class="thead-info">
        <th colspan="3">GRAND TOTAL</th>
        <th class="text-right">{{ desimal($grandtotal - $totalpenjualan) }}</th>
    </tr>
</table>
