@extends('layouts.midone')
@section('titlepage','Data Kendaraan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Kendaraan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/kendaraan">Kendaraan</a>
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

                @if (in_array($level,$kendaraan_tambah))
                <div class="card-header">
                    <a href="/kendaraan/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                @endif
                <div class="card-body">
                    <form action="/kendaraan">
                        <div class="row">
                            @if ($level == "PCF")
                            <div class="col-lg-4 col-sm-12">
                                <div class="form-group  ">
                                    <select name="kode_cabang" id="" class="form-control">
                                        <option value="">Semua Cabang</option>
                                        @foreach ($cabang as $c)
                                        <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                            <div class="col-lg-4 col-sm-12">
                                <x-inputtext label="No. Polisi" field="no_polisi" icon="fa fa-truck" value="{{ Request('no_polisi') }}" />
                            </div>
                            <div class="col-lg-4 col-sm-12">
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
                                    <th>Type</th>
                                    <th>Model</th>
                                    <th>Tahun</th>
                                    <th>Pajak</th>
                                    <th>Pengguna</th>
                                    <th>Status</th>
                                    <th>Cabang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kendaraan as $d)
                                <tr>
                                    <td>{{ $loop->iteration + $kendaraan->firstItem() - 1 }}</td>
                                    <td>{{ $d->no_polisi }}</td>
                                    <td>{{ $d->type }}</td>
                                    <td>{{ $d->model }}</td>
                                    <td>{{ $d->tahun }}</td>
                                    <td>{{ date("d-m-Y",strtotime($d->pajak)) }}</td>
                                    <td>{{ strtoupper($d->pemakai) }}</td>
                                    <td>{{ strtoupper($d->status) }}</td>
                                    <td>{{ strtoupper($d->kode_cabang) }}</td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a class="ml-1" href="/kendaraan/{{\Crypt::encrypt($d->id)}}/edit"><i class="feather icon-edit success"></i></a>
                                            <a class="ml-1 detailkendaraan" href="#" data-id="{{ $d->id }}"><i class=" feather icon-file-text info"></i></a>

                                            @if (in_array($level,$kendaraan_hapus))
                                            <form method="POST" name="deleteform" class="deleteform" action="/kendaraan/{{ Crypt::encrypt($d->id) }}/delete">
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
                        {{ $kendaraan->links('vendor.pagination.vuexy') }}
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
