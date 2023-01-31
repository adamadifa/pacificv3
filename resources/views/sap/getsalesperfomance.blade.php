<style>
    .mycontent-left {
        border-right: 1px dashed rgb(246, 246, 246);
    }

    .mycontent-right {
        border-right: 1px dashed rgb(246, 246, 246);
    }

</style>
@foreach ($karyawan as $d)
<div class="row mt-2">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-auto">
                        <div class="avatar avatar-40 alert-danger text-danger rounded-circle">
                            <img src="{{ asset('app-assets/marker/marker.png') }}" class="avatar avatar-40 rounded-circle" alt="">
                        </div>
                    </div>
                    <div class="col align-self-center ps-0">
                        <div class="row mb-2">
                            <div class="col">
                                <p class="small text-muted mb-0">{{ $d->nama_karyawan }}</p>
                                <span class="badge bg-danger">Salesman</span>
                            </div>
                            <div class="col-auto text-end">
                                <p class="small text-muted mb-0">Total Penjualan</p>
                                <p style="font-weight: bold">{{ rupiah($d->totalpenjualan) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" style="background-color: #9e022e;color:white; position: relative; width:100%; margin-top:-20px">
            <div class="card-body">
                <div class="row">
                    <div class="col mycontent-left text-center">
                        <h3>20</h3>
                        <span style="font-size: 0.8rem">Customer</span>
                    </div>
                    <div class="col mycontent-left text-center">
                        <h3>{{ rupiah($d->totalorder) }}</h3>
                        <span style="font-size: 0.8rem">Order</span>
                    </div>
                    <div class="col mycontent-left text-center">
                        <h3>20</h3>
                        <span style="font-size: 0.8rem">Call</span>
                    </div>
                    <div class="col text-center">
                        <h3>{{ rupiah($d->totalorder) }}</h3>
                        <span style="font-size: 0.8rem">Eff. Call</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endforeach
