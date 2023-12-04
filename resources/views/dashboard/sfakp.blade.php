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
                    @include('dashboard.nav_sfa')
                </ul>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="row">
                            <div class="col-12">
                                <form action="/dashboardsfakp">
                                    <div class="row">
                                        <div class="col-4">
                                            <x-inputtext field="tanggal" value="{{ Request('tanggal') }}"
                                                icon="feather icon-calendar" label="Tanggal" datepicker />
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <select name="kode_cabang" id="kode_cabang" class="form-control">
                                                    <option value="">Pilih Cabang</option>
                                                    @foreach ($cabang as $d)
                                                        <option
                                                            {{ Request('kode_cabang') == $d->kode_cabang ? 'selected' : '' }}
                                                            value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
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
                                            <th>Jarak</th>
                                            <th>Durasi</th>
                                            <th>Foto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $lat_start = '';
                                            $long_start = '';
                                            $start_time = '';
                                        @endphp
                                        @foreach ($smactivity as $d)
                                            @if ($loop->first)
                                                @php
                                                    $jarak = hitungjarak($lokasi[0], $lokasi[1], $d->latitude, $d->longitude);
                                                    $totaljarak = round(round($jarak['meters']) / 1000);
                                                    $totalwaktu = 0;
                                                @endphp
                                            @else
                                                @php
                                                    $jarak = hitungjarak($lat_start, $long_start, $d->latitude, $d->longitude);
                                                    $totaljarak = round(round($jarak['meters']) / 1000);
                                                    $totalwaktu = hitungjamdesimal($start_time, $d->tanggal);
                                                @endphp
                                            @endif
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ date('d-m-Y H:i:s', strtotime($d->tanggal)) }}</td>
                                                <td>{{ $d->aktifitas }}</td>
                                                <td>
                                                    {{ $totaljarak }} KM
                                                </td>
                                                <td>{{ $totalwaktu }} Jam</td>
                                                <td>
                                                    @if (!empty($d->foto))
                                                        @php
                                                            $path = Storage::url('uploads/smactivity/' . $d->foto);
                                                        @endphp
                                                        <ul class="list-unstyled users-list m-0  d-flex align-items-center showactivity"
                                                            kode_act_sm="{{ $d->kode_act_sm }}">
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom"
                                                                data-placement="bottom" data-original-title="Vinnie Mostowy"
                                                                class="avatar pull-up">
                                                                <img class="media-object rounded-circle"
                                                                    src="{{ url($path) }}" alt="Avatar" height="30"
                                                                    width="30">
                                                            </li>
                                                        </ul>
                                                    @endif
                                                </td>
                                            </tr>
                                            @php
                                                $lat_start = $d->latitude;
                                                $long_start = $d->longitude;
                                                $start_time = $d->tanggal;
                                            @endphp
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
