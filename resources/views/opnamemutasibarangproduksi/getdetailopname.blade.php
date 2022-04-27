@php
$no=1;
@endphp
@foreach ($detail as $d)
@php
$qty = $d->saldoawal + $d->gudang + $d->seasoning + $d->trial - $d->pemakaian - $d->retur - $d->lainnya;
@endphp
<tr>
    <td>{{ $no }}</td>
    <td>
        <input type="hidden" name="kode_barang{{ $loop->iteration }}" value="{{ $d->kode_barang }}">
        {{ $d->kode_barang }}
    </td>
    <td>{{ $d->nama_barang }}</td>
    <td class="text-right">
        <input type="text" name="qty{{ $loop->iteration }}" value="{{ !empty($qty) ? ROUND($qty,2) : 0 }}" class="form-control" style="text-align: right">
        {{-- {{ desimal($qty) }} --}}
    </td>
</tr>
@php
$no++;
@endphp
@endforeach
<tr>
    <td colspan="4"><input type="hidden" name="jumlahdata" id="jumlahdata" value="{{ $no-1 }}"></td>
</tr>
