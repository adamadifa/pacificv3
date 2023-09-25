@extends('layouts.midone')
@section('titlepage','Data Master BPJS Tenaga Kerja')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Master BPJS Tenaga Kerja</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/insentif">Data Master BPJS Tenaga Kerja</a>
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
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        @if (in_array($level,$insentif_tambah))
                        <div class="card-header">
                            <a href="#" id="tambahbpjstk" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                        </div>
                        @endif

                        <div class="card-body">
                            <form action="/bpjstk">
                                <div class="row">
                                    <div class="col-lg-10 col-sm-10">
                                        <x-inputtext label="Nama Karyawan" field="nama_karyawan_search" icon="feather icon-users" value="{{ Request('nama_karyawan_search') }}" />
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-search mr-1"></i> Cari</button>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-hover-animation">
                                    <thead class="thead-dark text-center">
                                        <tr>
                                            <th>No</th>
                                            <th>Kode</th>
                                            <th>NIK</th>
                                            <th class="text-left">Nama Karyawan</th>
                                            <th>Kantor</th>
                                            <th class="text-left">Jabatan</th>
                                            <th class="text-left">Departemen</th>
                                            <th>Iuran</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bpjstk as $d)
                                        <tr>
                                            <td class="text-center">
                                                {{ $loop->iteration + $bpjstk->firstItem() - 1 }}
                                            </td>
                                            <td class="text-center">{{ $d->kode_bpjs_tk }}</td>
                                            <td class="text-center">{{ $d->nik }}</td>
                                            <td>{{ $d->nama_karyawan }}</td>
                                            <td class="text-center">{{ $d->id_kantor }}</td>
                                            <td>{{ $d->nama_jabatan }}</td>
                                            <td>{{ $d->nama_dept }}</td>
                                            <td class="text-right">{{ rupiah($d->iuran) }}</td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    @if (in_array($level,$insentif_edit))
                                                    <a class="ml-1 edit" kode_bpjs_tk="{{ Crypt::encrypt($d->kode_bpjs_tk) }}" href="#"><i class="feather icon-edit success"></i></a>
                                                    @endif
                                                    @if (in_array($level,$insentif_hapus))
                                                    <form method="POST" class="deleteform" action="/bpjstk/{{Crypt::encrypt($d->kode_bpjs_tk)}}/delete">
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
                                {{ $bpjstk->links('vendor.pagination.vuexy') }}
                            </div>

                            <!-- DataTable ends -->
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- Data list view end -->
    </div>
</div>


<!-- Input Gaji -->
<div class="modal fade text-left" id="mdlinputbpjstk" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Tambah BPJS Tenaga Kerja</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputbpjstk"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdleditbpjstk" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit BPJS Tenaga Kerja</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadeditbpjstk"></div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('myscript')
<script>
    $(function() {
        $('#tambahbpjstk').click(function(e) {
            e.preventDefault();
            $('#mdlinputbpjstk').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadinputbpjstk").load('/bpjstk/create');
        });

        $('.edit').click(function(e) {
            var kode_bpjs_tk = $(this).attr("kode_bpjs_tk");
            e.preventDefault();
            $('#mdleditbpjstk').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadeditbpjstk").load('/bpjstk/' + kode_bpjs_tk + '/edit');
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
