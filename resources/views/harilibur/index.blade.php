@extends('layouts.midone')
@section('titlepage','Hari Libur')
@section('content')
<style>
    .form-label {
        font-weight: bold;
        margin-top: 10px;
    }

    .form-group {
        margin-bottom: 5px !important;
    }

    .form-label-group {
        margin-bottom: 5px !important;
    }

</style>
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Hari Libur</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/harilibur">Hari Libur</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        @include('layouts.notification')
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <a href="#" class="btn btn-primary" id="tambahlibur"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-hover-animation" id="tabelnonshift">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Kode Libur</th>
                                            <th>Tanggal</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($harilibur as $d)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $d->kode_libur }}</td>
                                            <td>{{ date("d-m-Y",strtotime($d->tanggal_libur)) }}</td>
                                            <td>{{ $d->keterangan }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdltambahlibur" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Tambah Data Libur</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/harilibur/store" method="POST" id="frmLibur">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext label="Auto" field="kode_libur" icon="feather icon-credit-card" readonly />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext label="Tanggal" field="tanggal" icon="feather icon-calendar" datepicker />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext label="Keterangan" field="keterangan" icon="feather icon-file" />
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button class="btn btn-primary btn-block" type="submit" name="submit"><i class="feather icon-send mr-1"></i>Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        $("#tambahlibur").click(function(e) {
            e.preventDefault();
            $('#mdltambahlibur').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        $("#frmLibur").submit(function(e) {
            var tanggal = $("#tanggal").val();
            var keterangan = $("#keterangan").val();
            if (tanggal == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tanggal").focus();
                });
                return false;
            } else if (keterangan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Keterangan Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#dari").focus();
                });
                return false;
            }
        });

    });

</script>
@endpush
