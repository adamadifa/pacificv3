@extends('layouts.midone')
@section('titlepage','Jadwal Kerja')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Jadwal Kerja</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/jadwalkerja">Jadwal Kerja</a>
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
                        <a href="#" class="btn btn-primary" id="buatjadwal"><i class="fa fa-plus mr-1"></i> Buat Jadwal</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-striped table-hover-animation" id="tabelnonshift">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Kode Jadwal</th>
                                            <th>Periode</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size:11px !Important">

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

<div class="modal fade text-left" id="mdlbuatjadwal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Buat Jadwal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/konfigurasijadwal/store" method="POST" id="frmSetjadwal">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext label="Auto" field="kode_setjadwal" icon="feather icon-credit-card" readonly />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker />
                        </div>
                        <div class="col-6">
                            <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker />
                        </div>
                    </div>
                    <div class="row">
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
        $("#buatjadwal").click(function(e) {
            e.preventDefault();
            $('#mdlbuatjadwal').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        $("#frmSetjadwal").submit(function(e) {
            var dari = $("#dari").val();
            var sampai = $("#sampai").val();
            if (dari == "" || sampai == "") {
                swal({
                    title: 'Oops'
                    , text: 'Periode Harus Dipilih !'
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
