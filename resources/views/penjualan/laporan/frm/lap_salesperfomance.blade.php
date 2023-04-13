@extends('layouts.midone')
@section('titlepage','Laporan Sales Perfomance')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Laporan Sales Perfomance</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/laporanpenjualan/routingsalesman">Laporan Sales Perfomance</a>
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
                                <form action="/laporanpenjualan/salesperfomance/cetak" method="POST" id="frmPenjualan" target="_blank">
                                    @csrf
                                    <input type="hidden" name="cabang" id="cabang" value="{{ Auth::user()->kode_cabang }}">
                                    <div class="row" id="pilihcabang">
                                        <div class="col-lg-12 col-sm-12">
                                            <div class="form-group  ">
                                                <select name="kode_cabang" id="kode_cabang" class="form-control">
                                                    @if (Auth::user()->kode_cabang!="PCF" && Auth::user()->kode_cabang!="PST")
                                                    <option value="">Pilih Cabang</option>
                                                    @else
                                                    <option value="">Semua Cabang</option>
                                                    @endif
                                                    @foreach ($cabang as $c)
                                                    <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="pilihsalesman">
                                        <div class="col-12">
                                            <div class="form-group  ">
                                                <select name="id_karyawan" id="id_karyawan" class="form-control">
                                                    <option value="">Semua Salesman</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="pilihperiode">
                                        <div class="col-6">
                                            <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker />
                                        </div>
                                        <div class="col-6">
                                            <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-8 col-sm-12">
                                            <div class="form-group">
                                                <button type="submit" name="submit" class="btn btn-primary btn-block"><i class="feather icon-send"></i> Submit</button>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <button type="submit" name="export" class="btn btn-success btn-block"><i class="feather icon-download"></i> Export</button>
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


        function loadlaporan() {
            var jenis_laporan = $("#jenis_laporan").val();
            if (jenis_laporan == "rekap") {
                $("#pilihsalesman").hide();
                $("#id_karyawan").val("");
            } else {
                $("#pilihsalesman").show();
            }
        }

        $("#jenis_laporan").change(function(e) {
            loadlaporan();
        });
        $("#frmPenjualan").submit(function() {
            var cabang = $("#cabang").val();
            var kode_cabang = $("#kode_cabang").val();
            var dari = $("#dari").val();
            var sampai = $("#sampai").val();

            var start = new Date(dari);
            var end = new Date(sampai);
            var jenis_laporan = $("#jenis_laporan").val();
            var datestart = new Date('2018-09-01');
            if (cabang != "PCF" && kode_cabang == "" && cabang != "PST" && kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Pilih Cabang Terlebih Dahulu !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_cabang").focus();
                });
                return false;
            } else if (jenis_laporan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jenis Laporan Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jenis_laporan").focus();
                });

                return false;
            } else if (dari == "" && sampai == "") {
                swal({
                    title: 'Oops'
                    , text: 'Periode Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#dari").focus();
                });
                return false;
            } else if (start.getTime() > end.getTime()) {
                swal({
                    title: 'Oops'
                    , text: 'Periode tidak Valid !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#dari").focus();
                });
                return false;
            } else if (start.getTime() < datestart.getTime()) {
                swal({
                    title: 'Oops'
                    , text: 'Periode tidak Valid !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#dari").focus();
                });
                return false;
            } else {
                return true;
            }
        });

        function loadsalesmancabang(kode_cabang) {
            $.ajax({
                type: 'POST'
                , url: '/salesman/getsalescab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                }
                , cache: false
                , success: function(respond) {
                    $("#id_karyawan").html(respond);
                }
            });
        }



        $("#kode_cabang").change(function() {
            var kode_cabang = $(this).val();
            loadsalesmancabang(kode_cabang);
        });


    });

</script>
@endpush
