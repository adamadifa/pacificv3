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
