@extends('layouts.midone')
@section('titlepage', 'Laporan RSM Activity')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Laporan RSM Activity</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="/laporanpenjualan/smmactivity">Laporan RSM Activity
                                    </a>
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
                                    <form action="/laporanpenjualan/rsmactivity/cetak" method="POST" id="frmPenjualan"
                                        target="_blank">
                                        @csrf
                                        <div class="row" id="pilihcabang">
                                            <div class="col-lg-12 col-sm-12">
                                                <div class="form-group  ">
                                                    <select name="id_rsm" id="id_rsm" class="form-control">
                                                        <option value="">Pilih RSM</option>

                                                        @foreach ($rsm as $d)
                                                            <option value="{{ $d->id }}">
                                                                {{ strtoupper($d->name) }}</option>
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
                                                        class="btn btn-primary btn-block"><i class="feather icon-send"></i>
                                                        Submit</button>
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
                    @include('layouts.nav_penjualan.navright')
                </div>

                <div class="col-lg-8 col-sm-12">


                </div>
            </div>
            <!-- Data list view end -->
        </div>
    </div>
@endsection
