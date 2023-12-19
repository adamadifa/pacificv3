@extends('layouts.midone')
@section('titlepage', 'Evaluasi & Sharing')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Evaluasi & Sharing</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Evaluasi & Sharing</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <input type="hidden" id="cektutuplaporan">
            <div class="row">
                <div class="col-8">
                    <div class="card">
                        <div class="card-header">
                            <a href="#" class="btn btn-primary" id="tambahevaluasi"><i class="fa fa-plus mr-1"></i>
                                Tambah
                                Data</a>
                        </div>
                        <div class="card-body">
                            <form action="{{ URL::current() }}">
                                <div class="row">
                                    <div class="col-lg-5 col-sm-12">
                                        <x-inputtext label="Dari" field="periode_dari" icon="feather icon-calendar"
                                            datepicker value="{{ Request('periode_dari') }}" />
                                    </div>
                                    <div class="col-lg-5 col-sm-12">
                                        <x-inputtext label="Sampai" field="periode_sampai" icon="feather icon-calendar"
                                            datepicker value="{{ Request('periode_sampai') }}" />
                                    </div>
                                    <div class="col-lg-2 col-sm-2">
                                        <div class="form-group">
                                            <button type="submit" name="submit" value="1" class="btn btn-primary"><i
                                                    class="fa fa-search"></i> Cari </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            @include('layouts.notification')
                            <table class="table table-hover-animation">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Cabang</th>
                                        <th>Tempat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($evaluasi as $d)
                                        <tr>
                                            <td>{{ $loop->iteration + $evaluasi->firstItem() - 1 }}</td>
                                            <td>{{ DateToIndo2($d->tanggal) }}</td>
                                            <td>{{ date('H:i', strtotime($d->jam)) }}</td>
                                            <td>{{ $d->kode_cabang }}</td>
                                            <td>{!! $d->tempat !!}</td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    <a class="ml-1 detailevaluasi" href="#"
                                                        kode_evaluasi="{{ $d->kode_evaluasi }}">
                                                        <i class=" feather icon-file-text info"></i>
                                                    </a>
                                                </div>
                                            </td>
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
    <div class="modal fade text-left" id="mdlcreateevaluasi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Buat Evaluasi</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadcreateevaluasi"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="mdldetailevaluasi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" style="max-width: 1100px" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Detail Evaluasi </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loaddetailevaluasi"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        $(function() {
            $("#tambahevaluasi").click(function(e) {
                e.preventDefault();
                $('#mdlcreateevaluasi').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadcreateevaluasi").load('/worksheetom/createevaluasi')
            });

            $(".detailevaluasi").click(function(e) {
                var kode_evaluasi = $(this).attr('kode_evaluasi');
                e.preventDefault();
                $('#mdldetailevaluasi').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loaddetailevaluasi").load('/worksheetom/' + kode_evaluasi + '/detailevaluasi');
            });

        });
    </script>
@endpush
