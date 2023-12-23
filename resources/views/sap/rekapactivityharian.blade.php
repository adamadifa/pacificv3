@extends('layouts.midone')
@section('titlepage', 'Dashboard')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <!-- Dashboard Analytics Start -->
            <section>
                @include('dashboard.nav_sfa')
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="row">
                            <div class="col-12">
                                <form action="#">
                                    <div class="row">
                                        <div class="col-5">
                                            <x-inputtext field="tanggal" value="{{ Request('tanggal') }}"
                                                icon="feather icon-calendar" label="Tanggal" datepicker />
                                        </div>
                                        <div class="col-5">
                                            <div class="form-group">
                                                <select name="kode_wilayah" id="kode_wilayah" class="form-control">
                                                    <option value="">Wilayah</option>
                                                    @foreach ($kategori_wilayah as $d)
                                                        <option
                                                            {{ Request('kode_wilayah') == $d->kode_wilayah ? 'selected' : '' }}
                                                            value="{{ $d->kode_wilayah }}">{{ $d->nama_wilayah }}
                                                        </option>
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
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            @foreach ($aktifitas as $d)
                                                <th style="width: 25%">{{ $d->name }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>

                                            @foreach ($aktifitas as $d)
                                                <td style="vertical-align: top">
                                                    @php
                                                        $dataaktifitas = explode('|', $d->aktifitas);
                                                    @endphp
                                                    <table>
                                                        @foreach ($dataaktifitas as $act)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $act }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </table>
                                                </td>
                                            @endforeach

                                        </tr>

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
