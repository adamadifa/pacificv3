<style>
    #border1 {
        border: 1px solid #b11036;
    }

</style>
@if ($realisasitarget->count() == 0)
<div class="alert alert-warning">
    Data Target Bulan Ini Belum Diset.!
</div>
@else
<div class="mt-2">
    @foreach ($realisasitarget as $d)
    @php
    $realisasi = $d->realisasi / $d->isipcsdus;
    $ratio = ($realisasi / $d->jumlah_target) *100;
    @endphp
    <div class="col-12 mb-1">
        <div class="card" id="border1">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <span style="font-weight: 600; font-size:0.8rem !important">{{ strtoupper($d->nama_barang) }}</span>
                    </div>
                    <div class="col-2">
                        <span class="badge bg-success">{{ rupiah($d->jumlah_target) }}</span>
                    </div>
                    <div class="col-2">
                        <span class="badge bg-success">{{ rupiah($realisasi) }}</span>
                    </div>
                    <div class="col-2 ps-0 text-end">
                        <span class="badge {{ $ratio  < 100 ? 'bg-success' :'bg-danger' }}"> {{ round($ratio) }} %</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
