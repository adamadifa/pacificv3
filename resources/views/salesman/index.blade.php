@extends('layouts.midone')
@section('titlepage','Data Salesman')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Salesman</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/salesman">Salesman</a>
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
        <div class="col-md-8 col-sm-8">
            <div class="card">
                @if (in_array($level,$salesman_tambah))
                <div class="card-header">
                    <a href="/salesman/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                @endif
                <div class="card-body">
                    <form action="/salesman">
                        <div class="row">
                            <div class="col-lg-4 col-sm-12">
                                <x-inputtext label="Nama Salesman" field="nama" icon="feather icon-user" value="{{ Request('nama') }}" />
                            </div>
                            @if (Auth::user()->kode_cabang =="PCF")

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
                                <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search mr-2"></i> Search</button>
                            </div>
                        </div>

                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Id Salesman</th>
                                    <th>Nama Salesman</th>
                                    <th>No HP</th>
                                    <th>Cabang</th>
                                    <th>Status</th>
                                    <th>Kategori</th>
                                    <th>Komisi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($salesman as $d)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration + $salesman->firstItem() - 1 }}</td>
                                    <td>{{ $d->id_karyawan }}</td>
                                    <td>{{ $d->nama_karyawan }}</td>
                                    <td>{{ $d->no_hp }}</td>
                                    <td>{{ $d->kode_cabang }}</td>
                                    <td>
                                        @if ($d->status_aktif_sales == 1)
                                        <span class="badge bg-success">Aktif</span>
                                        @else
                                        <span class="badge bg-danger">Non Aktif</span>
                                        @endif
                                    </td>
                                    <td>{{ $d->kategori_salesman }}</td>
                                    <td>
                                        @if ($d->status_komisi == 1)
                                        <span class="badge bg-success">Aktif</span>
                                        @else
                                        <span class="badge bg-danger">Non Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            @if (in_array($level,$salesman_edit))
                                            <a class="ml-1" href="/salesman/{{\Crypt::encrypt($d->id_karyawan)}}/edit"><i class="feather icon-edit success"></i></a>
                                            @endif
                                            <a class="ml-1 detailsalesman" href="#" idkaryawan="{{ Crypt::encrypt($d->id_karyawan) }}"><i class=" feather icon-file-text info"></i></a>
                                            @if (in_array($level,$salesman_hapus))
                                            <form method="POST" class="deleteform" action="/salesman/{{Crypt::encrypt($d->id_karyawan)}}/delete">
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
                        {{ $salesman->links('vendor.pagination.vuexy') }}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Detail Salesman -->
<div class="modal fade text-left" id="mdldetailsalesman" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail Salesman</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaddetailsalesman"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {

        function loaddetailsalesman(id_karyawan) {
            $.ajax({
                type: 'POST'
                , url: '/salesman/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , id_karyawan: id_karyawan
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailsalesman").html(respond);
                }
            });
        }
        $('.detailsalesman').click(function(e) {
            var id_karyawan = $(this).attr("idkaryawan");
            e.preventDefault();
            loaddetailsalesman(id_karyawan);
            $('#mdldetailsalesman').modal({
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
