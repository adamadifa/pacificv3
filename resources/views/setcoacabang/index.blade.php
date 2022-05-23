@extends('layouts.midone')
@section('titlepage','Data Salesman')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Chart Of Account Cabang</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/setcoacabang">Chart Of Account Cabang</a>
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
        <div class="col-md-5 col-sm-5">
            <div class="card">
                <div class="card-header">
                    <a href="/coa/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i>Tambah Data</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <form action="/setcoacabang">
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <select name="kode_cabang" id="kode_cabang" class="form-control">
                                                <option value="">Pilih Cabang</option>
                                                @foreach ($cabang as $d)
                                                <option {{ Request('kode_cabang') ==  $d->kode_cabang ? 'selected' : '' }} value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <select name="kategori" id="kategori" class="form-control">
                                                <option value="">Pilih Kategori</option>
                                                <option {{ Request('kategori') == "Kas Kecil" ? 'selected' : '' }} value="Kas Kecil">Kas Kecil</option>
                                                <option {{ Request('kategori') == "Mutasi Bank" ? 'selected' : '' }} value="Mutasi Bank">Mutasi Bank</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
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
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Kode Akun</th>
                                    <th>Nama Akun</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($setcoa as $d)
                                <tr>
                                    <td>{{ $d->kode_akun }}</td>
                                    <td>{{ $d->nama_akun }}</td>
                                    <td>
                                        <form method="POST" class="deleteform" action="/setcoacabang/{{Crypt::encrypt($d->id_setakuncabang)}}/delete">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" class="delete-confirm ml-1">
                                                <i class="feather icon-trash danger"></i>
                                            </a>
                                        </form>
                                    </td>
                                </tr>
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
