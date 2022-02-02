@extends('layouts.midone')
@section('titlepage','Data Barang')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Barang</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/barang">Barang</a>
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
        <div class="col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <a href="/barang/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Satuan</th>
                                    <th class="text-center">Jml Pcs/Dus</th>
                                    <th class="text-center">Jml Pack/Dus</th>
                                    <th class="text-center">Jml Pcs/Pack</th>
                                    <th>Jenis Produk</th>
                                    <th>Kategori Komisi</th>
                                    <th class="text-center">Kode Akun</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($barang as $d)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $d->kode_produk }}</td>
                                    <td>{{ $d->nama_barang }}</td>
                                    <td>{{ $d->satuan }}</td>
                                    <td class="text-center">{{ $d->isipcsdus }}</td>
                                    <td class="text-center">{{ $d->isipack }}</td>
                                    <td class="text-center">{{ $d->isipcs }}</td>
                                    <td>{{ $d->kategori_jenisproduk }}</td>
                                    <td>{{ $d->kategori_komisi }}</td>
                                    <td class="text-center">{{ $d->kode_akun }}</td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a class="ml-1" href="/barang/{{\Crypt::encrypt($d->kode_produk)}}/edit"><i class="feather icon-edit success"></i></a>
                                            <form method="POST" class="deleteform" action="/barang/{{Crypt::encrypt($d->kode_produk)}}/delete">
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
