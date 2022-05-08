@extends('layouts.midone')
@section('titlepage','Kontrabon Angkutan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Kontrabon Angkutan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/kontrabonangkutan">Kontrabon Angkutan</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <input type="hidden" id="cektutuplaporan">
        <!-- Data list view starts -->
        <!-- DataTable starts -->
        @include('layouts.notification')
        <div class="col-md-12 col-sm-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <a href="/kontrabonangkutan/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>

                <div class="card-body">
                    <form action="/kontrabonangkutan">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <x-inputtext label="Dari" field="dari" value="{{Request('dari')}}" icon="feather icon-calendar" datepicker />
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <x-inputtext label="Sampai" field="sampai" value="{{Request('sampai')}}" icon="feather icon-calendar" datepicker />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <button type="submit" name="submit" class="btn btn-primary btn-block"><i class="fa fa-search mr-1"></i> Cari</button>
                                </div>
                            </div>
                        </div>

                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No. Kontrabon</th>
                                    <th>Tgl Kontrabon</th>
                                    <th>Angkutan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kontrabon as $d )
                                <tr>
                                    <td>{{ $d->no_kontrabon }}</td>
                                    <td>{{ date("d-m-Y",strtotime($d->tgl_kontrabon)) }}</td>
                                    <td>{{ $d->keterangan }}</td>
                                    <td>
                                        @if($d->status==1)
                                        <span class="badge bg-success">Sudah Di Proses</span>
                                        @else
                                        <span class="badge bg-warning">Belum Di Proses</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a class="ml-1 detail" href="#" no_kontrabon="{{ Crypt::encrypt($d->no_kontrabon) }}"><i class=" feather icon-file-text info"></i></a>
                                            @if ($d->status!=1)
                                            <form method="POST" class="deleteform" action="/kontrabonangkutan/{{Crypt::encrypt($d->no_kontrabon)}}/delete">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" class="delete-confirm ml-1">
                                                    <i class="feather icon-trash danger"></i>
                                                </a>
                                            </form>
                                            <a href="#" class="ml-1 proses" no_kontrabon="{{ Crypt::encrypt($d->no_kontrabon) }}"><i class="feather icon-external-link success"></i></a>
                                            @else
                                            <a href="/kontrabonangkutan/{{ Crypt::encrypt($d->no_kontrabon) }}/batalkan" class="ml-1"><i class="fa fa-close danger"></i></a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $kontrabon->links('vendor.pagination.vuexy') }}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Edit Angkutan -->
<div class="modal fade text-left" id="mdldetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail Kontrabon</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaddetail"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="mdlproses" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Proses Kontrabon</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadproses"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        $(".detail").click(function(e) {
            e.preventDefault();
            var no_kontrabon = $(this).attr("no_kontrabon");
            $("#loaddetail").load("/kontrabonangkutan/" + no_kontrabon + "/show");
            $('#mdldetail').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        $(".proses").click(function(e) {
            e.preventDefault();
            var no_kontrabon = $(this).attr("no_kontrabon");
            $("#loadproses").load("/kontrabonangkutan/" + no_kontrabon + "/proses");
            $('#mdlproses').modal({
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
