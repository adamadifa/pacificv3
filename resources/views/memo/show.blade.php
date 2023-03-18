@extends('layouts.midone')
@section('titlepage','Data Salesman')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">E-Manual Regulation Center</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/setcoacabang">E-Manual Regulation Center</a>
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
        <div class="col-12">
            <div class="card">
                {{-- <div class="card-header">
                    @if (in_array($level,$memo_tambah_hapus))
                    <a href="/memo/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i>Tambah Data</a>
                    @endif
                </div> --}}
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <form action="{{ URL::current(); }}">
                                <div class="row">
                                    <div class="col-6">
                                        <x-inputtext field="dari" label="Dari" icon="feather icon-calendar" value="{{ Request('dari') }}" datepicker />
                                    </div>
                                    <div class="col-6">
                                        <x-inputtext field="sampai" label="Sampai" icon="feather icon-calendar" value="{{ Request('sampai') }}" datepicker />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext field="judul_memo" label="Judul Memo" icon="feather icon-file" value="{{ Request('judul_memo') }}" />
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
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>No.Dokumen</th>
                                    <th style="width:40%">Judul</th>
                                    <th>Dep</th>
                                    <th>Kategori</th>
                                    <th>Uploaded By</th>
                                    <th>File</th>
                                    <th>Read</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $kategori = "";
                                @endphp
                                @foreach ($memo as $d)
                                @if ($kategori != $d->kategori)
                                <tr>
                                    <thead class="thead-dark">
                                        <th colspan="10">KATEGORI {{ strtoupper($d->kategori) }}</th>
                                    </thead>
                                </tr>
                                @endif
                                <tr>
                                    <td>{{ $loop->iteration + $memo->firstItem() -1 }}</td>
                                    <td>{{ date("d-m-Y",strtotime($d->tanggal)) }}</td>
                                    <td>{{ $d->no_memo }}</td>
                                    <td>{{ ucwords(strtolower($d->judul_memo)) }}</td>
                                    <td>{{ $d->kode_dept }}</td>
                                    <td>{{ $d->kategori }}</td>
                                    <td>{{ $d->name }}</td>
                                    <td>
                                        <a href="<?php echo $d->link; ?>" target="_blank" data-id="<?php echo $d->id; ?>" class="downloadcount"><i class="feather icon-download success"></i></a>
                                        <a href="#" data-id="<?php echo $d->id ?>" class="detaildownload mt-3"><span class="badge badge-info badge-pill"><?php echo $d->totaldownload ?></span></a>
                                    </td>
                                    <td>
                                        <?php
                                        if (empty($d->status_read)) {
                                            echo "<i class='feather icon-clock danger'></i>";
                                        } else {
                                            echo "<i class='feather icon-check-circle success'></i>";
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @if (in_array($level,$memo_tambah_hapus))
                                            <form method="POST" class="deleteform" action="/memo/{{Crypt::encrypt($d->id)}}/delete">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" class="delete-confirm ml-1">
                                                    <i class="feather icon-trash danger"></i>
                                                </a>
                                            </form>
                                            @endif
                                            @if (empty($d->id_memo))
                                            @if (Auth::user()->kode_cabang != 'PCF')
                                            <a href="#" class="uploadsosialisasi" id_memo="{{ $d->id }}"> <i class="feather icon-upload info ml-1"></i></a>
                                            @else
                                            <a href="#" class="detailsosialisasi" id_memo="{{ $d->id }}"><i class="feather icon-book-open ml-1"></i></a>
                                            @endif
                                            @else
                                            <a href=" #" class="showsosialisasi" id_sosialisasi="{{ $d->id_sosialisasi }}"><i class="feather icon-link success ml-1"></i></a>
                                            @endif
                                        </div>

                                    </td>
                                </tr>
                                @php
                                $kategori = $d->kategori;
                                @endphp
                                @endforeach
                            </tbody>
                        </table>
                        {{ $memo->links('vendor.pagination.vuexy') }}
                    </div>
                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>

<!-- Input Uraian Analisa -->
<div class="modal fade text-left" id="mdluploadsosialisasi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Upload Daftar Hadir Sosialisasi</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputuploadsosialisasi"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdledituploadsosialisasi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Update Daftar Hadir Sosialisasi</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadedituploadsosialisasi"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdldetailsosialisasi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18"> Daftar Hadir Sosialisasi</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaddetailsosialisasi"></div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('myscript')
<script>
    $(function() {

        $('.uploadsosialisasi').click(function(e) {
            e.preventDefault();
            var id_memo = $(this).attr('id_memo');
            $.ajax({
                type: 'POST'
                , url: '/memo/uploadsosialisasi/create'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , id_memo: id_memo
                }
                , cache: false
                , success: function(respond) {
                    $("#loadinputuploadsosialisasi").html(respond);
                    $('#mdluploadsosialisasi').modal({
                        backdrop: 'static'
                        , keyboard: false
                    });
                }
            });

        });

        $('.showsosialisasi').click(function(e) {
            e.preventDefault();
            var id_sosialisasi = $(this).attr('id_sosialisasi');
            $.ajax({
                type: 'POST'
                , url: '/memo/uploadsosialisasi/edit'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , id_sosialisasi: id_sosialisasi
                }
                , cache: false
                , success: function(respond) {
                    $("#loadedituploadsosialisasi").html(respond);
                    $('#mdledituploadsosialisasi').modal({
                        backdrop: 'static'
                        , keyboard: false
                    });
                }
            });

        });


        $('.detailsosialisasi').click(function(e) {
            e.preventDefault();
            var id_memo = $(this).attr('id_memo');
            $.ajax({
                type: 'POST'
                , url: '/memo/uploadsosialisasi/detail'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , id_memo: id_memo
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailsosialisasi").html(respond);
                    $('#mdldetailsosialisasi').modal({
                        backdrop: 'static'
                        , keyboard: false
                    });
                }
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

        $(".downloadcount").click(function() {
            var id = $(this).attr("data-id");
            //alert(id);
            $.ajax({
                type: 'POST'
                , url: '/memo/downloadcount'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , id: id
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    location.reload();
                }
            });
        });
    });

</script>
@endpush
