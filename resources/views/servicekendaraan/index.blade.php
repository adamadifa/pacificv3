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
                                    <th>No. Polisi</th>
                                    <th>Merk</th>
                                    <th>Tipe Kendaraan</th>
                                    <th>Type</th>
                                    <th>Tahun</th>
                                    <th>KIR</th>
                                    <th>Pajak 1 Th</th>
                                    <th>Pajak 5 Th</th>
                                    <th>Cabang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>


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
<div class="modal fade text-left" id="mdldetailkendaraan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail Kendaraan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaddetailkendaraan"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        function loaddetailkendaraan(id) {
            $.ajax({
                type: 'POST'
                , url: '/kendaraan/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , id: id
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailkendaraan").html(respond);
                }
            });
        }
        $('.detailkendaraan').click(function(e) {
            var id = $(this).attr("data-id");

            e.preventDefault();
            loaddetailkendaraan(id);
            $('#mdldetailkendaraan').modal({
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
