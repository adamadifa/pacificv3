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
                                        <div class="col-4">
                                            <x-inputtext field="dari" value="{{ Request('dari') }}"
                                                icon="feather icon-calendar" label="Dari" datepicker />
                                        </div>
                                        <div class="col-4">
                                            <x-inputtext field="sampai" value="{{ Request('sampai') }}"
                                                icon="feather icon-calendar" label="Sampai" datepicker />
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
                                            <th colspan="{{ 3 + $jmlrange }}">Rekap Activiti SMM</th>
                                        </tr>
                                        <tr>
                                            <th rowspan="2">No</th>
                                            <th rowspan="2">Nama</th>
                                            <th rowspan="2">Cabang</th>
                                            <th colspan="{{ $jmlrange }}" style="text-align: center">Tanggal</th>
                                        </tr>
                                        <tr>
                                            @foreach ($rangetanggal as $d)
                                                <th style="width:2%">{{ date('d', strtotime($d)) }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($rekapsmm as $d)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $d->name }}</td>
                                                <td>{{ $d->kode_cabang }}</td>
                                                @php
                                                    $i = 1;
                                                @endphp
                                                @foreach ($rangetanggal as $h)
                                                    @php
                                                        $field = 'tgl_' . $i;
                                                    @endphp
                                                    <td
                                                        style="background-color: {{ !empty($d->$field) ? 'green;color:white' : '' }}">
                                                        <a style="text-decoration: none; color:white" target="_blank"
                                                            href="/dashboardsfakp?tanggal={{ $h }}&kode_cabang={{ $d->kode_cabang }}">
                                                            {{ !empty($d->$field) ? $d->$field : '' }}
                                                        </a>
                                                    </td>

                                                    @php
                                                        $i++;
                                                    @endphp
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
                                            <th colspan="{{ 3 + $jmlrange }}">Rekap Activiti RSM</th>
                                        </tr>
                                        <tr>
                                            <th rowspan="2">No</th>
                                            <th rowspan="2">Nama</th>
                                            <th rowspan="2">Cabang</th>
                                            <th colspan="{{ $jmlrange }}" style="text-align: center">Tanggal</th>
                                        </tr>
                                        <tr>
                                            @foreach ($rangetanggal as $d)
                                                <th style="width:2%">{{ date('d', strtotime($d)) }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($rekaprsm as $d)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $d->name }}</td>
                                                <td>{{ $d->kode_cabang }}</td>
                                                @php
                                                    $i = 1;
                                                @endphp
                                                @foreach ($rangetanggal as $h)
                                                    @php
                                                        $field = 'tgl_' . $i;
                                                    @endphp
                                                    <td
                                                        style="background-color: {{ !empty($d->$field) ? 'green;color:white' : '' }}">
                                                        <a style="text-decoration: none; color:white" target="_blank"
                                                            href="/dashboardsfarsm?tanggal={{ $h }}&kode_cabang={{ $d->kode_cabang }}">
                                                            {{ !empty($d->$field) ? $d->$field : '' }}
                                                        </a>
                                                    </td>

                                                    @php
                                                        $i++;
                                                    @endphp
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
                                            <th colspan="{{ 2 + $jmlrange }}">Rekap Activity Wilayah</th>
                                        </tr>
                                        <tr>
                                            <th rowspan="2">No</th>
                                            <th rowspan="2">Wilayah</th>

                                            <th colspan="{{ $jmlrange }}" style="text-align: center">Tanggal</th>
                                        </tr>
                                        <tr>
                                            @foreach ($rangetanggal as $d)
                                                <th style="width:2%">{{ date('d', strtotime($d)) }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rekapwilayah as $d)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $d->nama_wilayah }}</td>
                                                @php
                                                    $i = 1;
                                                @endphp
                                                @foreach ($rangetanggal as $h)
                                                    @php
                                                        $field = 'tgl_' . $i;
                                                    @endphp
                                                    <td
                                                        style="background-color: {{ !empty($d->$field) ? 'green;color:white' : '' }}">
                                                        <a style="text-decoration: none; color:white" target="_blank"
                                                            href="#">
                                                            {{ !empty($d->$field) ? $d->$field : '' }}
                                                        </a>
                                                    </td>

                                                    @php
                                                        $i++;
                                                    @endphp
                                                @endforeach
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
    <div class="modal fade text-left" id="mdlshowactivity" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Detail Activity</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadshowactivity"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        $(function() {
            $(".showactivity").click(function(e) {
                var kode_act_sm = $(this).attr("kode_act_sm");
                $("#mdlshowactivity").modal("show");
                $("#loadshowactivity").load('/smactivity/' + kode_act_sm + "/show");
            });
        });
    </script>
@endpush
