@extends('layouts.midone')
@section('titlepage','Jurnal Umum')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Jurnal Umum</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/laporanaccounting/jurnalumum">Jurnal Umum </a>
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
                                <form action="/laporanaccounting/jurnalumum/cetak" method="POST" id="frmPembelian" target="_blank">
                                    @csrf

                                    <div class="row" id="pilihperiode">
                                        <div class="col-6">
                                            <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker />
                                        </div>
                                        <div class="col-6">
                                            <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker />
                                        </div>
                                    </div>
                                    {{-- <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <select name="kode_dept" id="kode_dept" class="form-control select2">
                                                    <option value="">Semua Departemen</option>
                                                    @foreach ($departemen as $d)
                                                    <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                    @endforeach
                                    </select>
                            </div>
                        </div>
                    </div> --}}
                    <div class="row">
                        <div class="col-lg-8 col-sm-12">
                            <div class="form-group">
                                <button type="submit" name="submit" class="btn btn-primary btn-block"><i class="feather icon-printer"></i> Cetak</button>
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
    @include('layouts.nav_laporanaccounting')
</div>
</div>
<!-- Data list view end -->
</div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {

        $("#frmPembelian").submit(function() {
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            if (bulan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bulan Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#bulan").focus();
                });
                return false;
            } else if (tahun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tahun Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tahun").focus();
                });
                return false;
            } else {
                return true;
            }
        });
    });

</script>
@endpush
