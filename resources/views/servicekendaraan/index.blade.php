@extends('layouts.midone')
@section('titlepage','Service Kendaraan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Service Kendaraan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/servicekendaraan">Service Kendaraan</a>
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
                    <a href="/servicekendaraan/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                <div class="card-body">
                    <form action="/kendaraan">
                        <div class="row mb-1">
                            <div class="col-lg-3 col-sm-12">
                                <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker value="{{ Request('dari') }}" />
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker value="{{ Request('sampai') }}" />
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <select name="no_polisi" id="no_polisi" class="form-control select2">
                                    <option value="">Pilih Kendaraan</option>
                                    @foreach ($kendaraan as $d)
                                    <option value="{{ $d->no_polisi }}">{{ $d->no_polisi }} {{ $d->merk }} {{ $d->tipe_kendaraan }} {{ $d->tipe }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search mr-2"></i> Search</button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>No. Invoice</th>
                                    <th>Tanggal</th>
                                    <th>No. Polisi</th>
                                    <th>Kendaraan</th>
                                    <th>Nama Bengkel</th>
                                    <th>Kode Cabang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($service as $d)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $d->no_invoice }}</td>
                                    <td>{{ DateToIndo2($d->tgl_service) }}</td>
                                    <td>{{ $d->no_polisi }}</td>
                                    <td>{{ $d->merk }} {{ $d->tipe_kendaraan }} {{ $d->tipe }}</td>
                                    <td>{{ $d->nama_bengkel }}</td>
                                    <td>{{ $d->kode_cabang }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="#" class="info detail" no_invoice={{ $d->no_invoice }}><i class="feather icon-file"></i></a>
                                            <form method="POST" name="deleteform" class="deleteform" action="/servicekendaraan/{{ Crypt::encrypt($d->no_invoice) }}/delete">
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
                        {{ $service->links('vendor.pagination.vuexy') }}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Detail Kendaraan -->
<div class="modal fade text-left" id="mdldetailservice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail Service Kendaraan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaddetailservice"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        function loaddetailservice(no_invoice) {
            $.ajax({
                type: 'POST'
                , url: '/servicekendaraan/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_invoice: no_invoice
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailservice").html(respond);
                }
            });
        }
        $('.detail').click(function(e) {
            var no_invoice = $(this).attr("no_invoice");

            e.preventDefault();
            loaddetailservice(no_invoice);
            $('#mdldetailservice').modal({
                backdrop: 'static'
                , keyboard: false
            });
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
