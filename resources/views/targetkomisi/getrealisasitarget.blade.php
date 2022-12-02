@if ($realisasitarget->count() == 0)
<tr>
    <td colspan="5">
        <div class="alert alert-info">
            Data Target Bulan Ini Belum Diset.!
        </div>
    </td>
</tr>
@else
@foreach ($realisasitarget as $d)
@php
$realisasi = $d->realisasi / $d->isipcsdus;
$ratio = ($realisasi / $d->jumlah_target) *100;
@endphp
<tr>
    <td>{{ $d->kode_produk }}</td>
    <td>{{ $d->nama_barang }}</td>
    <td class="text-right">{{ rupiah($d->jumlah_target) }}</td>
    <td class="text-right">{{ desimal($realisasi) }}</td>
    <td class="text-right">
        @if ($ratio < 100) <span class="badge bg-danger">{{ round($ratio) }} %</span>
            @else
            <span class="badge bg-success">{{ round($ratio) }} %</span>
            @endif

    </td>
</tr>
@endforeach

@endif
