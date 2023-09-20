@extends('layouts.midone')
@section('titlepage','Log Aktivitas User')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Log Aktivitas User</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/logaktivitas">Log Aktivitas User</a>
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
        <div class="col-md-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form action="/logaktivitas">
                        <div class="row">
                            <div class="col-lg-3 col-sm-12">
                                <x-inputtext label="Tanggal" field="tanggal" icon="feather icon-calendar" value="{{ Request('tanggal') }}" datepicker />
                            </div>
                            @if (Auth::user()->kode_cabang =="PCF")

                            <div class="col-lg-3 col-sm-12">
                                <div class="form-group  ">
                                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                                        <option value="">Semua Cabang</option>
                                        @foreach ($cabang as $c)
                                        <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @else
                            <input type="hidden" name="kode_cabang" id="kode_cabang" value="{{ Auth::user()->kode_cabang }}">
                            @endif
                            <div class="col-lg-3 col-sm-12">
                                <div class="form-group">
                                    <select name="id_user" id="id_user" class="form-control">
                                        <option value="">Pilih User</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search mr-2"></i> Search</button>
                            </div>
                        </div>

                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>Date Time</th>
                                    <th>User</th>
                                    <th>Aktivitas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($log as $d)
                                @if ($d->action=="c")
                                @php
                                $color = "success";
                                @endphp
                                @elseif($d->action=="d")
                                @php
                                $color="danger"
                                @endphp
                                @elseif($d->action=="u")
                                @php
                                $color="info";
                                @endphp
                                @endif
                                <tr class="{{ $color }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ date("d-m-y H:i:s",strtotime($d->datetime)) }}</td>
                                    <td>
                                        {{ $d->level=='salesman' ? $d->id_salesman."-" : '' }}
                                        {{ ucwords(strtolower($d->name)) }}
                                    </td>
                                    <td>{{ $d->activity }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        function loaduser() {
            var kode_cabang = $("#kode_cabang").val();
            var id_user = "{{ Request('id_user') }}";
            $.ajax({
                type: "POST"
                , url: "/user/getusercabang"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , id_user: id_user
                }
                , cache: false
                , success: function(respon) {
                    $("#id_user").html(respon);
                }

            });
        }

        loaduser();

        $("#kode_cabang").change(function() {
            loaduser();
        });
    });

</script>
@endpush
