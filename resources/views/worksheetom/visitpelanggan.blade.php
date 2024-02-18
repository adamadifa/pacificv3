@extends('layouts.midone')
@section('titlepage', 'Visit Pelanggan')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Visit Pelanggan</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Visit Pelanggan</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="card">

                <div class="card-body">
                    <form action="{{ URL::current() }}">
                        <div class="row">
                            <div class="col-3">
                                <x-inputtext label="Dari" field="dari" icon="feather icon-calendar"
                                    value="{{ Request('dari') }}" datepicker />
                            </div>
                            <div class="col-3">
                                <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar"
                                    value="{{ Request('sampai') }}" datepicker />
                            </div>
                            @if (Auth::user()->kode_cabang == 'PCF')
                                <div class="col-3">
                                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                                        <option value="">Semua Cabang</option>
                                        @foreach ($cabang as $c)
                                            <option {{ Request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}
                                                value="{{ $c->kode_cabang }}">
                                                {{ strtoupper($c->nama_cabang) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="col-lg-2 col-sm-12">
                                <div class="btn-group">
                                    <button type="submit" name="submit" value="1" class="btn btn-primary">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    <button type="submit" name="cetak" formtarget="_blank" value="1"
                                        class="btn btn-success">
                                        <i class="feather icon-printer"></i>
                                    </button>
                                    <button type="submit" name="export" formtarget="_blank" value="1"
                                        class="btn btn-success">
                                        <i class="feather icon-download"></i>
                                    </button>
                                </div>

                            </div>
                        </div>
                    </form>
                    @include('layouts.notification')
                    <table class="table table-hover-animation">
                        <thead class="thead-dark">
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Faktur</th>
                                <th>Pelanggan</th>
                                <th>Salesman</th>
                                <th>Pasar</th>
                                <th>Tgl Faktur</th>
                                <th>Nilai Faktur</th>
                                <th>Jenis Transaksi</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($visitpelanggan as $d)
                                <tr>
                                    <td>{{ date('d-m-Y', strtotime($d->tanggal_visit)) }}</td>
                                    <td>{{ $d->no_fak_penj }}</td>
                                    <td>{{ $d->nama_pelanggan }}</td>
                                    <td>{{ $d->nama_karyawan }}</td>
                                    <td>{{ $d->pasar }}</td>
                                    <td>{{ date('d-m-Y', strtotime($d->tgltransaksi)) }}</td>
                                    <td class="text-right">{{ rupiah($d->total) }}</td>
                                    <td>{{ $d->jenistransaksi }}</td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a class="ml-1 edit" href="#" kode_visit="{{ $d->kode_visit }}">
                                                <i class="feather icon-edit success"></i>
                                            </a>
                                            <form method="POST" name="deleteform" class="deleteform"
                                                action="/worksheetom/{{ Crypt::encrypt($d->kode_visit) }}/deletevisitpelanggan">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" class="delete-confirm ml-1">
                                                    <i class="feather icon-trash danger"></i>
                                                </a>
                                            </form>
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
    <div class="modal fade text-left" id="mdleditvisitpelanggan" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Edit Visit Pelanggan </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadeditvisitpelanggan"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        $(function() {
            $(".edit").click(function(e) {
                e.preventDefault();
                var kode_visit = $(this).attr('kode_visit');
                $('#mdleditvisitpelanggan').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadeditvisitpelanggan").load('/worksheetom/' + kode_visit + '/editvisitpelanggan')
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
        });
    </script>
@endpush
