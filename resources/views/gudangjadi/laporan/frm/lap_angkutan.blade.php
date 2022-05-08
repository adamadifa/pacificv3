@extends('layouts.midone')
@section('titlepage','Laporan Angkutan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Laporan Angkutan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/laporangudangjadi/angkutan">Laporan Angkutan</a>
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
                                <form action="/laporangudangjadi/angkutan/cetak" method="POST" id="frmPembelian" target="_blank">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <select name="angkutan" id="angkutan" class="form-control select2">
                                                    <option value="">Semua Angkutan</option>
                                                    <option value="KS">ANGKUTAN KS</option>
                                                    <option value="KWN SUAKA">ANGKUTAN KAWAN SWAKA</option>
                                                    <option value="AS">ANGKUTAN AS</option>
                                                    <option value="SD">ANGKUTAN SD</option>
                                                    <option value="WAWAN">ANGKUTAN WAWAN</option>
                                                    <option value="RTP">ANGKUTAN RTP</option>
                                                    <option value="KWN GOBRAS">ANGKUTAN KWN GOBRAS</option>
                                                    <option value="LH">ANGKUTAN LH</option>
                                                    <option value="TSN">ANGKUTAN TSN</option>
                                                    <option value="MANDIRI">ANGKUTAN MANDIRI</option>
                                                    <option value="GS">ANGKUTAN GS</option>
                                                    <option value="CV TRESNO">ANGKUTAN CV TRESNO</option>
                                                    <option value="KS">ANGKUTAN KS</option>
                                                    <option value="MSA">ANGKUTAN MSA</option>
                                                    <option value="MITRA KOMANDO">ANGKUTAN MITRA KOMANDO</option>
                                                    <option value="ARP MANDIRI">ANGKUTAN ARP MANDIRI</option>
                                                    <option value="CAHAYA BIRU">ANGKUTAN CAHAYA BIRU</option>
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
                @include('layouts.nav_laporangudang')
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
            var dari = $("#dari").val();
            var sampai = $("#sampai").val();
            var kode_produk = $("#kode_produk").val();
            var start = new Date(dari);
            var end = new Date(sampai);
            if (kode_produk == "") {
                swal({
                    title: 'Oops'
                    , text: 'Produk Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_produk").focus();
                });
                return false;
            } else if (dari == "" || sampai == "") {
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
    });

</script>
@endpush
