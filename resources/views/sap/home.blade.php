@extends('layouts.sap.sap')
@section('content')
<style>
    .count-indicator_2 {
        height: 25px;
        width: 25px;
        border-radius: 25px;
        position: absolute;
        top: 10px;
        right: 10px;
        color: white;
        font-size: 14px;
        background-color: var(--fimobile-red);
    }

</style>
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-auto">
                        <figure class="avatar avatar-44 rounded-10">

                            @if (Auth::user()->foto != null)
                            @php
                            $path = Storage::url('users/'.Auth::user()->foto);
                            @endphp
                            @else
                            @php
                            $path = asset('app-assets/images/avatar.png');
                            @endphp
                            @endif
                            <img src="{{ url($path) }}" alt="">
                        </figure>
                    </div>
                    <div class="col px-0 align-self-center">
                        <p class="mb-0 text-color-theme">{{ Auth::user()->name }}</p>
                        <p class="text-muted size-12">{{ ucwords(Auth::user()->level) }}</p>
                    </div>
                    <div class="col-auto">
                        <a href="addmoney.html" class="btn btn-44 btn-light shadow-sm">
                            <i class="bi bi-plus-circle"></i>
                        </a>
                        <a href="withdraw.html" class="btn btn-44 btn-default shadow-sm ms-1">
                            <i class="bi bi-arrow-down-circle"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card theme-bg text-white border-0 text-center">
                <div class="card-body">
                    <h1 class="display-1 my-2">{{ rupiah($penjualan->totalpenjualan) }}</h1>
                    <p class="text-muted mb-2">Penjualan Bulan Ini</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-12 px-0">
        <!-- swiper users connections -->
        <div class="swiper-container connectionwiper swiper-container-initialized swiper-container-horizontal">
            <div class="swiper-wrapper" id="swiper-wrapper-8e11048de6e4c4984" aria-live="polite" style="transform: translate3d(0px, 0px, 0px);">
                @foreach ($penjualancabang as $d)
                <div class="swiper-slide swiper-slide-active" role="group" aria-label="1 / 8">

                    @php
                    $persen = round($d->totalpenjualan / $penjualan->totalpenjualan * 100);
                    @endphp
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
                                                <p class="small text-muted mb-0" style="text-align: left !important">{{ $d->nama_cabang }}</p>
                                                <p style="text-align: left !important">{{ rupiah($d->totalpenjualan) }}</p>
                                            </div>
                                            <div class="col-auto text-end">
                                                <p class="small text-muted mb-0">{{ $d->jmlorder }}</p>
                                                <p class="small text-muted">Order</p>
                                            </div>
                                        </div>
                                        <div class="progress alert-danger h-4">
                                            <div class="progress-bar bg-danger w-50" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                </div>
                @endforeach
            </div>
            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
        </div>
    </div>
</div>
<div class="row">

    <div class="col-6 col-md-3">
        <a href="/sap/limitkredit" class="card text-center">
            <div class="card-body">
                <i class="bi bi-bag-dash text-color-theme" style="font-size: 2rem;"></i>
                <span class="count-indicator_2">{{ $jmlpengajuan }}</span>
                <p class="text-color-theme size-12 small" style="margin-top: 10px">Menunggu Persetujuan</p>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="/salesperformance" class="card text-center">
            <div class="card-body">
                <i class="bi bi-bar-chart-line text-color-theme" style="font-size: 2rem;"></i>
                <p class="text-color-theme size-12 small" style="margin-top: 10px">Sales Performance</p>
            </div>
        </a>
    </div>
</div>
@endsection
