@extends('layouts.midone')
@section('titlepage', 'Monitoring Produk')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Monitoring Produk Expired</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Monitoring Produk</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="card">
                <div class="card-header">
                    <a href="#" class="btn btn-primary" id="tambah"><i class="fa fa-plus mr-1"></i> Tambah
                        Data</a>
                </div>
                <div class="card-body">
                    <form action="{{ URL::current() }}">
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <select name="kode_produk" id="kode_produk" class="form-control">
                                        <option value="">Pilih Produk</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <x-inputtext label="Dari" field="periode_dari" icon="feather icon-calendar" datepicker
                                    value="{{ Request('periode_dari') }}" />
                            </div>
                            <div class="col-lg-3 col-sm-12">
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
                                <th rowspan="2">No.</th>
                                <th rowspan="2">Tanggal Datang</th>
                                <th rowspan="2">Kode Produk</th>
                                <th rowspan="2">Nama Produk</th>
                                <th rowspan="2">Tgl Expired</th>
                                <th colspan="3" class="text-center">Qty</th>
                                <th rowspan="2"></th>
                            </tr>
                            <tr>
                                <th class="text-center">Dus</th>
                                <th class="text-center">Pack</th>
                                <th class="text-center">Pcs</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="mdlcreateprodukexpired" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Buat Program </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadcreateexpired"></div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('myscript')
    <script>
        $(function() {
            $("#tambah").click(function(e) {
                e.preventDefault();
                $('#mdlcreateprodukexpired').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadcreateexpired").load('/worksheetom/createprodukexpired')
            });
        });
    </script>
@endpush
