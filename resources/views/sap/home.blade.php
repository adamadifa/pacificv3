@extends('layouts.sap.sap')
@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-auto">
                <figure class="avatar avatar-44 rounded-10">
                    @if (!empty(Auth::user()->foto))
                    @php
                    $path = Storage::url('users/'.Auth::user()->foto);
                    @endphp
                    @else
                    $path = {{ asset('app-assets/images/avatar.png') }}
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
@endsection
