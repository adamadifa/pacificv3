@php
$no=1;
@endphp
@foreach ($detail as $d)
@php
$qty = $d->qtysaldoawal + $d->qtypemasukan - $d->qtypengeluaran;

@endphp
<tr>
    <td>{{ $no }}</td>
    <td>
        <input type="hidden" name="kode_barang{{ $loop->iteration }}" value="{{ $d->kode_barang }}">
        {{ $d->kode_barang }}
    </td>
    <td>{{ $d->nama_barang }}</td>
    <td class="text-right">
        <input type="hidden" name="qty{{ $loop->iteration }}" value="{{ !empty($qty) ? ROUND($qty,2) : 0 }}">
        {{ desimal($qty) }}
    </td>
</tr>
@php
$no++;
@endphp
@endforeach
<tr>
    <td colspan="4"><input type="hidden" name="jumlahdata" id="jumlahdata" value="{{ $no-1 }}"></td>
</tr>
