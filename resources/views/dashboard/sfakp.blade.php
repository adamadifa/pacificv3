@extends('layouts.midone')
@section('titlepage', 'Dashboard')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
    </div>
    <div class="content-body">
        <!-- Dashboard Analytics Start -->
        <section>
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is(['dashboardsfa']) ? 'active' : '' }}" href="/dashboardsfa">SFA Salesman</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is(['dashboardsfakp']) ? 'active' : '' }}" href="/dashboardsfakp">SFA SMM</a>
                </li>
            </ul>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="row">
                        <div class="col-12">
                            <form action="/dashboardsfakp">
                                <div class="row">
                                    <div class="col-4">
                                        <x-inputtext field="tanggal" value="{{ Request('tanggal') }}" icon="feather icon-calendar" label="Tanggal" datepicker />
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <select name="kode_cabang" id="kode_cabang" class="form-control">
                                                <option value="">Pilih Cabang</option>
                                                @foreach ($cabang as $d)
                                                <option {{ Request('kode_cabang') == $d->kode_cabang ? "selected" : "" }} value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group">
                                            <button class="btn btn-primary w-100">
                                                <i class="feather icon-search mr-1"></i>
                                                Get Data
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-hover-animation table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Aktifitas</th>
                                        <th>Foto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($smactivity as $d)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ date("d-m-Y H:i:s",strtotime($d->tanggal)) }}</td>
                                        <td>{{ $d->aktifitas }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
</div>
@endsection
