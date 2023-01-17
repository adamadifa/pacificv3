@extends('layouts.midone')
@section('titlepage','Data Penjualan')
@section('content')
<style>
    .form-group {
        margin-bottom: 5px !important;
    }

    .form-label-group {
        margin-bottom: 5px !important;
    }

</style>
<style>
    .card {
        margin-bottom: 0.8rem !important;
    }

</style>
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h4 class="content-header-title float-left mb-0">Data Penjualan</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <input type="hidden" id="cektutuplaporan">
        <div class="row">
            <div class="col-12">
                <form action="/penjualan" id="frmPenjualan">
                    <div class="row">
                        <div class="col-lg-6 col-sm-6">
                            <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker value="{{ Request('dari') }}" />
                        </div>
                        <div class="col-lg-6 col-sm-6">
                            <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker value="{{ Request('sampai') }}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-sm-12">
                            <x-inputtext label="No Faktur" field="no_fak_penj" icon="feather icon-credit-card" value="{{ Request('no_fak_penj') }}" />
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <x-inputtext label="Nama Pelanggan" field="nama_pelanggan" icon="feather icon-user" value="{{ Request('nama_pelanggan') }}" />
                        </div>

                        <div class="col-lg-1 col-sm-12">
                            <button type="submit" name="submit" value="1" class="btn btn-primary btn-block search"><i class="fa fa-search mr-1"></i> Cari Data </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row mt-1">
            <div class="col-12">
                @include('layouts.notification')
                @foreach ($penjualan as $d)
                <a href="/penjualan/{{ Crypt::encrypt($d->no_fak_penj) }}/showforsales" style="color: inherit">
                    <div class="row">
                        <div class="col-12">
                            <div class="card {{ $d->status_lunas == 1 ? 'border-primary' : 'bg-gradient-danger' }}">
                                <div class="card-content">
                                    <div class="card-body" style="padding:8px 10px 8px 8px !important">
                                        <p class="card-text d-flex justify-content-between">
                                            <span class="d-flex justify-content-between">
                                                <span>
                                                    <b>{{ $d->no_fak_penj }} -{{ $d->nama_pelanggan }}</b> <br> {{ DateToIndo2($d->tgltransaksi) }}
                                                </span>
                                            </span>
                                            <span style="text-align: right">
                                                @if ($d->jenistransaksi=="tunai")
                                                <span class="badge bg-success">Tunai</span>
                                                @else
                                                <span class="badge bg-warning">Kredit</span>
                                                @endif
                                                <br>
                                                <span style="font-weight: bold">{{rupiah($d->total)}}</span>
                                                {{-- <span class="badge bg-success">{{ date("H:i:s",strtotime($d->checkin_time)) }}</span> <br>
                                            <span class="badge bg-info">{{ !empty($d->checkout_time) ? date("H:i",strtotime($d->checkout_time)) : 0 }}</span> --}}
                                            </span>

                                        </p>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
