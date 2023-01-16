@if ($realisasitarget->count() == 0)
<div class="alert alert-info">
    Data Target Bulan Ini Belum Diset.!
</div>
@else
@foreach ($realisasitarget as $d)
@php
$realisasi = $d->realisasi / $d->isipcsdus;
$ratio = ($realisasi / $d->jumlah_target) *100;
@endphp
<div class="row">
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-content">
                <div class="card-body">
                    <p class="card-text d-flex justify-content-between">
                        {{ $d->nama_barang }}
                        <span class="badge bg-primary">{{ rupiah($d->jumlah_target) }}</span>
                        <span class="badge bg-info">{{ desimal($realisasi) }}</span>
                        @if ($ratio < 100) <span class="badge bg-danger">{{ round($ratio) }} %</span>
                            @else
                            <span class="badge bg-success">{{ round($ratio) }} %</span>
                            @endif
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>
{{-- <tr>
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
</tr> --}}
@endforeach

@endif
