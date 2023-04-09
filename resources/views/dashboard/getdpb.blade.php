<style>
    #border1 {
        border: 1px solid #b11036;
    }

</style>
@foreach ($dpb as $d)
<div class="row">
    <div class="col-12 mb-1">
        <div class="card" id="border1">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <span style="font-weight: 600; font-size:0.8rem !important">{{ strtoupper($d->nama_barang) }}</span>
                    </div>
                    <div class="col-2">
                        <span class="badge bg-success">{{ desimal($d->jml_pengambilan) }}</span>
                    </div>
                    <div class="col-2">
                        <span class="badge bg-danger">
                            @php
                            $penjualan = ROUND(($d->qtyjual / $d->isipcsdus),3);
                            @endphp
                            {{ desimal($penjualan)}}
                        </span>
                    </div>
                    <div class="col-2 ps-0 text-end">
                        <span class="badge">
                            @php
                            $sisa = floatval($d->jml_pengambilan) - floatval($penjualan);


                            @endphp
                            {{ desimal($sisa) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
