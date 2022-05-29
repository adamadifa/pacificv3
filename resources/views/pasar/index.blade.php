@extends('layouts.midone')
@section('titlepage','Data Cabang')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Pasar</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/pasar">Data Pasar</a>
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
        <div class="col-lg-6 col-sm-12">
            <div class="card">
                <div class="card-header">
                    @if (in_array($level,$pasar_tambah))
                    <a href="/pasar/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <form action="/pasar">
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext field="nama_pasar" value="{{ Request('nama_pasar') }}" label="Nama Pasar" icon="feather icon-file" />
                                    </div>
                                </div>
                                @if (Auth::user()->kode_cabang=="PCF")
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <select name="kode_cabang" id="kode_cabang" class="form-control">
                                                <option value="">Semua Cabang</option>
                                                @foreach ($cabang as $d)
                                                <option {{ Request('kode_cabang') == $d->kode_cabang ? 'selected' : '' }} value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <button class="btn btn-primary btn-block"><i class="fa fa-search mr-1"></i>Cari</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover-animation" id="datatable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pasar</th>
                                    <th>Cabang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pasar as $d)
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $d->nama_pasar }}</td>
                                <td>{{ $d->kode_cabang }}</td>
                                <td>
                                    @if (in_array($level,$pasar_hapus))
                                    <form method="POST" class="deleteform" action="/pasar/{{Crypt::encrypt($d->id)}}/delete">
                                        @csrf
                                        @method('DELETE')
                                        <a href="#" class="delete-confirm ml-1">
                                            <i class="feather icon-trash danger"></i>
                                        </a>
                                    </form>
                                    @endif
                                </td>
                                @endforeach
                            </tbody>
                        </table>

                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {

        $('.delete-confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            event.preventDefault();
            swal({
                    title: `Are you sure you want to delete this record?`
                    , text: "If you delete this, it will be gone forever."
                    , icon: "warning"
                    , buttons: true
                    , dangerMode: true
                , })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
        });
    });

</script>
@endpush
