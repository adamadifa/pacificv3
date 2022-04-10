@extends('layouts.midone')
@section('titlepage','Data Supplier')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Supplier</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/supplier">Data Supplier</a>
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
                @if (in_array($level,$supplier_tambah))
                <div class="card-header">
                    <a href="#" id="tambahsupplier" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                @endif
                <div class="card-body">
                    <form action="/supplier">
                        <div class="row">
                            <div class="col-lg-9 col-sm-12">
                                <x-inputtext label="Nama Supplier" field="nama_supplier" icon="feather icon-users" value="{{ Request('nama_supplier') }}" />
                            </div>

                            <div class="col-lg-3 col-sm-12">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search mr-1"></i> Cari</button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Kode Supplier</th>
                                    <th>Nama Supplier</th>
                                    <th style="width: 30%">Alamat</th>
                                    <th>Contact Person</th>
                                    <th>No. HP</th>
                                    <th>Email</th>
                                    <th>No.Rekening</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($supplier as $d)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration + $supplier->firstItem() - 1 }}</td>
                                    <td>{{ $d->kode_supplier }}</td>
                                    <td>{{ ucwords(strtolower($d->nama_supplier)) }}</td>
                                    <td>{{ ucwords(strtolower($d->alamat_supplier)) }}</td>
                                    <td>{{ $d->contact_supplier }}</td>
                                    <td>{{ $d->nohp_supplier }}</td>
                                    <td>{{ $d->email }}</td>
                                    <td>{{ $d->norekening }}</td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            @if (in_array($level,$supplier_edit))
                                            <a class="ml-1 edit" kodesupplier="{{ $d->kode_supplier }}" href="#"><i class="feather icon-edit success"></i></a>
                                            @endif
                                            @if (in_array($level,$supplier_hapus))
                                            <form method="POST" class="deleteform" action="/supplier/{{Crypt::encrypt($d->kode_supplier)}}/delete">
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
                        {{ $supplier->links('vendor.pagination.vuexy') }}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Input Supplier -->
<div class="modal fade text-left" id="mdlinputsupplier" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Tambah Supplier</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputsupplier"></div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Supplier -->
<div class="modal fade text-left" id="mdleditsupplier" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit Supplier</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadeditsupplier"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {

        $('#tambahsupplier').click(function(e) {
            e.preventDefault();
            $('#mdlinputsupplier').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadinputsupplier").load('/supplier/create');
        });

        $('.edit').click(function(e) {
            var kode_supplier = $(this).attr("kodesupplier");
            e.preventDefault();
            $('#mdleditsupplier').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadeditsupplier").load('/supplier/' + kode_supplier + '/edit');
        });
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
