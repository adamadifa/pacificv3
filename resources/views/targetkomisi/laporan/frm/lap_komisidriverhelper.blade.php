@extends('layouts.midone')
@section('titlepage', 'Laporan Komisi')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Laporan Komisi Driver Helper</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Laporan Komisi Driver Helper</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Data list view starts -->
            <!-- DataTable starts -->
            @include('layouts.notification')
            <div class="row">
                <div class="col-lg-9 col-sm-12">
                    <div class="row">
                        <div class="col-lg-7 col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <form action="/laporankomisidriverhelper/cetak" method="POST" id="frmPenjualan"
                                        target="_blank">
                                        @csrf
                                        {{-- @csrf
                                    @if (Auth::user()->kode_cabang != 'PCF')
                                    <input type="hidden" name="kode_cabang" id="kode_cabang" value="{{ Auth::user()->kode_cabang }}">
                                    @else
                                    <div class="row" id="pilihcabang">
                                        <div class="col-lg-12 col-sm-12">
                                            <div class="form-group  ">
                                                <select name="kode_cabang" id="kode_cabang" class="form-control">
                                                    <option value="">Pilih Cabang</option>
                                                    @foreach ($cabang as $c)
                                                    <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    @endif --}}
                                        <div class="row" id="pilihcabang">
                                            <div class="col-lg-12 col-sm-12">
                                                <div class="form-group  ">
                                                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                                                        <option value="">Pilih Cabang</option>
                                                        @foreach ($cabang as $c)
                                                            <option
                                                                {{ Request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}
                                                                value="{{ $c->kode_cabang }}">
                                                                {{ strtoupper($c->nama_cabang) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" id="pilihperiode">
                                            <div class="col-6">
                                                <x-inputtext label="Dari" field="dari" icon="feather icon-calendar"
                                                    datepicker />
                                            </div>
                                            <div class="col-6">
                                                <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar"
                                                    datepicker />
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-8 col-sm-12">
                                                <div class="form-group">
                                                    <button type="submit" name="submit"
                                                        class="btn btn-primary btn-block"><i
                                                            class="feather icon-printer"></i> Cetak</button>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-sm-12">
                                                <div class="form-group">
                                                    <button type="submit" name="export"
                                                        class="btn btn-success btn-block"><i
                                                            class="feather icon-download"></i> Export</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- DataTable ends -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-12">
                    @if (!request()->is(['worksheetom/komisidriverhelper']))
                        @include('layouts.nav_penjualan.navright')
                    @endif
                </div>

                <div class="col-lg-8 col-sm-12">


                </div>
            </div>
            <!-- Data list view end -->
        </div>
    </div>
@endsection
