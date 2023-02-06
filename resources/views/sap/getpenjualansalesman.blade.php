<style>
    .cardpenjualan {
        width: 100%;
        background-color: #ffffff;
        color: black;
        padding: 8px 10px;
        margin: 5px;
        border-radius: 10px;
        box-shadow: 2px 2px 3px rgba(88, 88, 88, 0.3);
    }

    .totaljenistransaksi {
        line-height: normal;
        margin-right: 2px;
    }

    #border1 {
        border: 1px solid #b11036;
    }

</style>
<div class="row">
    <div class="col-12 text-center">
        <h1 style="color:#b11036; font-size:3rem !important">{{rupiah($penjualan->totalpenjualan)}}</h1>

    </div>
</div>
<div class="row mb-3">
    <div class="col-12 px-0">
        <!-- swiper users connections -->
        <div class="swiper-container connectionwiper swiper-container-initialized swiper-container-horizontal swiper-container-pointer-events swiper-container-ios">
            <div class="swiper-wrapper" id="swiper-wrapper-8e11048de6e4c4984" aria-live="polite" style="transform: translate3d(0px, 0px, 0px); transition-duration: 0ms;">
                <div class="swiper-slide swiper-slide-active" role="group" aria-label="1 / 2">

                    <a href="#" class="card text-center">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="avatar avatar-40 alert-success text-success rounded-circle">
                                            <i class="bi bi-cart"></i>
                                        </div>
                                    </div>
                                    <div class="col ps-0">
                                        <div class="row mb-2">
                                            <div class="col">

                                                <p style="text-align: left !important; font-weight:600" class="mb-0 text-success">{{ rupiah($penjualan->totaltunai) }}</p>
                                                <p class="small text-muted mb-0" style="text-align: left !important">Tunai</p>
                                            </div>
                                            <div class="col-auto text-end">
                                                <p class="small text-muted mb-0">{{ $penjualan->ordertunai }}</p>
                                                <p class="small text-muted">Order</p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                </div>
                <div class="swiper-slide swiper-slide-next" role="group" aria-label="2 / 2">

                    <a href="#" class="card text-center">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="avatar avatar-40 alert-warning text-warning rounded-circle">
                                            <i class="bi bi-cart"></i>
                                        </div>
                                    </div>
                                    <div class="col ps-0">
                                        <div class="row mb-2">
                                            <div class="col">

                                                <p style="text-align: left !important; font-weight:600" class="mb-0 text-warning">{{ rupiah($penjualan->totalkredit) }}</p>
                                                <p class="small text-muted mb-0" style="text-align: left !important">Kredit</p>
                                            </div>
                                            <div class="col-auto text-end">
                                                <p class="small text-muted mb-0">{{ $penjualan->orderkredit }}</p>
                                                <p class="small text-muted">Order</p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                </div>
                <div class="swiper-slide swiper-slide-next" role="group" aria-label="2 / 2">
                    <a href="#" class="card text-center">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="avatar avatar-40 alert-danger text-danger rounded-circle">
                                            <i class="bi bi-cart"></i>
                                        </div>
                                    </div>
                                    <div class="col ps-0">
                                        <div class="row mb-2">
                                            <div class="col">

                                                <p style="text-align: left !important; font-weight:600" class="mb-0 text-danger">{{ rupiah($penjualan->totalpotongan) }}</p>
                                                <p class="small text-muted mb-0" style="text-align: left !important">Potongan</p>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                </div>
                <div class="swiper-slide swiper-slide-next" role="group" aria-label="2 / 2">
                    <a href="#" class="card text-center">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="avatar avatar-40 alert-danger text-danger rounded-circle">
                                            <i class="bi bi-cart"></i>
                                        </div>
                                    </div>
                                    <div class="col ps-0">
                                        <div class="row mb-2">
                                            <div class="col">

                                                <p style="text-align: left !important; font-weight:600" class="mb-0 text-danger">{{ rupiah($penjualan->totalpotis) }}</p>
                                                <p class="small text-muted mb-0" style="text-align: left !important">Potongan Istimewa</p>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                </div>
                <div class="swiper-slide swiper-slide-next" role="group" aria-label="2 / 2">
                    <a href="#" class="card text-center">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="avatar avatar-40 alert-danger text-danger rounded-circle">
                                            <i class="bi bi-cart"></i>
                                        </div>
                                    </div>
                                    <div class="col ps-0">
                                        <div class="row mb-2">
                                            <div class="col">

                                                <p style="text-align: left !important; font-weight:600" class="mb-0 text-danger">{{ rupiah($penjualan->totalpeny) }}</p>
                                                <p class="small text-muted mb-0" style="text-align: left !important">Penyesuaian</p>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                </div>
                <div class="swiper-slide swiper-slide-next" role="group" aria-label="2 / 2">
                    <a href="#" class="card text-center">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="avatar avatar-40 alert-danger text-danger rounded-circle">
                                            <i class="bi bi-cart"></i>
                                        </div>
                                    </div>
                                    <div class="col ps-0">
                                        <div class="row mb-2">
                                            <div class="col">

                                                <p style="text-align: left !important; font-weight:600" class="mb-0 text-danger">{{ rupiah($penjualan->totalppn) }}</p>
                                                <p class="small text-muted mb-0" style="text-align: left !important">PPN</p>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                </div>
            </div>
            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
        </div>
    </div>
</div>

<div class="mt-2">
    @foreach ($detailpenjualan as $d)
    <div class="col-12 mb-1">
        <div class="card" id="border1">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <span style="font-weight: 600; font-size:0.8rem !important">{{ strtoupper($d->nama_barang) }}</span>
                    </div>
                    <div class="col-2">
                        <span class="badge {{ $d->qty  > 0 ? 'bg-success' :'bg-danger' }}">{{ desimal(ROUND($d->qty / $d->isipcsdus,2)) }}</span>
                    </div>
                    <div class="col-4 ps-0 text-end">
                        <span class="badge {{ $d->qty  > 0 ? 'bg-success' :'bg-danger' }}"> {{ rupiah($d->subtotal) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
