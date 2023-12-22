@extends('layouts.midone')
@section('titlepage', 'Monitoring Program')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Monitoring Porgram</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/retur">Monitoring Program</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <input type="hidden" id="cektutuplaporan">

            <div class="card">
                <div class="card-header">
                    <a href="#" class="btn btn-primary" id="tambahprogram"><i class="fa fa-plus mr-1"></i> Tambah
                        Data</a>
                </div>
                <div class="card-body">
                    <form action="{{ URL::current() }}">
                        <div class="row">
                            <div class="col-lg-5 col-sm-12">
                                <x-inputtext label="Dari" field="periode_dari" icon="feather icon-calendar" datepicker
                                    value="{{ Request('periode_dari') }}" />
                            </div>
                            <div class="col-lg-5 col-sm-12">
                                <x-inputtext label="Sampai" field="periode_sampai" icon="feather icon-calendar" datepicker
                                    value="{{ Request('periode_sampai') }}" />
                            </div>
                            <div class="col-lg-2 col-sm-2">
                                <div class="form-group">
                                    <button type="submit" name="submit" value="1" class="btn btn-primary"><i
                                            class="fa fa-search"></i> Cari Data </button>
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
                                <th>Kode Program</th>
                                <th>Nama Program</th>
                                <th>Produk</th>
                                <th>Target</th>
                                <th>Periode</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($program as $d)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ DateToIndo2($d->tanggal) }}</td>
                                    <td>{{ $d->kode_program }}</td>
                                    <td>{{ $d->nama_program }}</td>
                                    <td>
                                        @php
                                            $produk = unserialize($d->kode_produk);
                                        @endphp
                                        @foreach ($produk as $p)
                                            {{ $p . ',' }}
                                        @endforeach
                                    </td>
                                    <td>{{ rupiah($d->jml_target) }}</td>
                                    <td>
                                        {{ date('d-m-Y', strtotime($d->dari)) }} s/d
                                        {{ date('d-m-Y', strtotime($d->sampai)) }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a class="ml-1 tambahpeserta" href="#"
                                                kode_program="{{ $d->kode_program }}">
                                                <i class=" feather icon-users info"></i>
                                            </a>
                                            <a class="ml-1"
                                                href="/worksheetom/{{ Crypt::encrypt($d->kode_program) }}/cetakprogram"
                                                target="_blank">
                                                <i class=" feather icon-printer info"></i>
                                            </a>
                                            @if ($level == 'admin')
                                                <a class="ml-1 edit" href="#" kode_program="{{ $d->kode_program }}">
                                                    <i class="feather icon-edit success"></i>
                                                </a>
                                                <form method="POST" name="deleteform" class="deleteform"
                                                    action="/worksheetom/{{ Crypt::encrypt($d->kode_program) }}/deleteprogram">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="#" class="delete-confirm ml-1">
                                                        <i class="feather icon-trash danger"></i>
                                                    </a>
                                                </form>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $program->links('vendor.pagination.vuexy') }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="mdlcreateprogram" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Buat Program </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadcreateretur"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="mdltambahpeserta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Tambah Peserta Program </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadtambahpeserta"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="mdleditprogram" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Edit Program </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadeditprogram"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        $(function() {
            $("#tambahprogram").click(function(e) {
                e.preventDefault();
                $('#mdlcreateprogram').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadcreateretur").load('/worksheetom/createprogram')
            });

            $('.delete-confirm').click(function(event) {
                var form = $(this).closest("form");
                var name = $(this).data("name");
                event.preventDefault();
                swal({
                        title: `Are you sure you want to delete this record?`,
                        text: "If you delete this, it will be gone forever.",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            form.submit();
                        }
                    });
            });

            $(".edit").click(function(e) {
                e.preventDefault();
                var kode_program = $(this).attr('kode_program');
                $('#mdleditprogram').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadeditprogram").load('/worksheetom/' + kode_program + '/editprogram')
            });
            $(".tambahpeserta").click(function(e) {
                e.preventDefault();
                var kode_program = $(this).attr('kode_program');
                $('#mdltambahpeserta').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadtambahpeserta").load('/worksheetom/' + kode_program + '/tambahpeserta');
            });
        });
    </script>
@endpush
