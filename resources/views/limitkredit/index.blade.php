@extends('layouts.midone')
@section('titlepage','Data Limit Kredit')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Limit Kredit</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/limitkredit">Data Limit Kredit</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <input type="hidden" id="cektutuplaporan">
        <div class="col-md-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form action="/limitkredit">
                        <div class="row">
                            <div class="col-lg-6">
                                <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker value="{{ Request('dari') }}" />
                            </div>
                            <div class="col-lg-6">
                                <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker value="{{ Request('sampai') }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-sm-12">
                                <x-inputtext label="Nama Pelanggan" field="nama_pelanggan" icon="feather icon-user" value="{{ Request('nama_pelanggan') }}" />
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <div class="form-group">
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Status</option>
                                        <option {{ (Request('status')=='1' ? 'selected':'')}} value="1">PENDING</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-2 col-sm-12">
                                <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i> Cari Data </button>
                            </div>
                        </div>
                    </form>
                    @include('layouts.notification')
                    <table class="table ">
                        <thead class="thead-dark">
                            <tr>
                                <th>No Faktur</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Salesman</th>
                                <th>Cabang</th>
                                <th>T/K</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                    </table>
                    {{-- {{ $penjualan->links('vendor.pagination.vuexy') }} --}}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
