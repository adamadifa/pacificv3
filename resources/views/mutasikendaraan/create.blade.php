@extends('layouts.midone')
@section('titlepage', 'Input Mutasi Kendaraan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Input Mutasi Kendaraan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/mutasikendaraan/create">Input Mutasi Kendaraan</a></li>
                            <li class="breadcrumb-item"><a href="#">Input Mutasi Kendaraan</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <form class="form" id="frmMutasikendaraan" action="/mutasikendaraan/store" method="POST">
        <div class="col-md-12">

            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Input Mutasi Kendaraan</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <select name="no_polisi" id="no_polisi" class="form-control select2">
                                                    <option value="">Pilih No. Polisi / No. Kendaraan</option>
                                                    @foreach ($kendaraan as $d)
                                                    <option value="{{ $d->no_polisi }}">{{ $d->no_polisi. " ".$d->merk." ".$d->tipe_kendaraan. " ".$d->tipe. " | ".$d->kode_cabang }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <select name="kode_cabang" id="kode_cabang" class="form-control select2">
                                                    <option value="">Mutasi Ke Cabang </option>
                                                    @foreach ($cabang as $d)
                                                    <option value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Tanggal Mutasi" field="tgl_mutasi" icon="feather icon-calendar" datepicker />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Keterangan" field="keterangan" icon="feather icon-file-text" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1"><i class="fa fa-send mr-1"></i> Simpan</button>
                                            <a href="{{ url()->previous() }}" class="btn btn-outline-warning mr-1 mb-1"><i class="fa fa-arrow-left mr-2"></i>Kembali</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        $("#frmMutasikendaraan").submit(function() {

            var no_polisi = $("#no_polisi").val();
            var kode_cabang = $("#kode_cabang").val();
            var tgl_mutasi = $("#tgl_mutasi").val();
            if (no_polisi == "") {
                swal({
                    title: 'Oops'
                    , text: 'Pilih No. Polisi Terlebih Dahulu !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#no_polisi").focus();
                });

                return false;
            } else if (kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Pilih Cabang Terlebih Dahulu !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_cabang").focus();
                });

                return false;
            } else if (tgl_mutasi == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_mutasi").focus();
                });

                return false;
            }
        });
    });

</script>
@endpush
