@extends('layouts.midone')
@section('titlepage', 'Laporan Kendaraan')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Laporan Kendaraan</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Laporan Kendaraan</a>
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
                                    <form action="/kendaraan/cetakLaporanKendaraan" method="POST" id="frmPenjualan"
                                        target="_blank">
                                        @csrf
                                        <input type="hidden" name="cabang" id="cabang"
                                            value="{{ Auth::user()->kode_cabang }}">
                                        <div class="row" id="pilihcabang">
                                            <div class="col-lg-12 col-sm-12">
                                                <div class="form-group  ">
                                                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                                                        @if (Auth::user()->kode_cabang != 'PCF')
                                                            <option value="">Pilih Cabang</option>
                                                        @else
                                                            <option value="">Semua Cabang</option>
                                                        @endif
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
@push('myscript')
    <script>
        $(function() {

            $("#frmPenjualan").submit(function() {
                var cabang = $("#cabang").val();
                var kode_cabang = $("#kode_cabang").val();
                var dari = $("#dari").val();
                var sampai = $("#sampai").val();
                var no_polisi = $("#no_polisi").val();
                var start = new Date(dari);
                var end = new Date(sampai);

                var datestart = new Date('2018-09-01');
                if (kode_cabang == "") {
                    swal({
                        title: 'Oops',
                        text: 'Pilih Cabang Terlebih Dahulu !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#kode_cabang").focus();
                    });
                    return false;
                } else {
                    return true;
                }
            });

            function loadkendaraan(kode_cabang) {
                $.ajax({
                    type: 'POST',
                    url: '/kendaraan/getkendaraancab',
                    data: {
                        _token: "{{ csrf_token() }}",
                        kode_cabang: kode_cabang
                    },
                    cache: false,
                    success: function(respond) {
                        $("#no_polisi").html(respond);
                    }
                });
            }



            $("#kode_cabang").change(function() {
                var kode_cabang = $(this).val();
                loadkendaraan(kode_cabang);
            });


        });
    </script>
@endpush
