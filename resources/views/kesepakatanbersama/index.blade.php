@extends('layouts.midone')
@section('titlepage','Kesepakatan Bersama')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Kesepakatan Bersama</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/kesepakatanbersama">Kesepakatan Bersama</a>
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
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>No. KB</th>
                                    <th>Tanggal</th>
                                    <th>NIK</th>
                                    <th>Nama Karyawan</th>
                                    <th>Jabatan</th>
                                    <th>Pemutihan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kb as $d)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $d->no_kb }}</td>
                                    <td>{{ DateToIndo2($d->tgl_kb) }}</td>
                                    <td>{{ $d->nik }}</td>
                                    <td>{{ $d->nama_karyawan }}</td>
                                    <td>{{ $d->nama_jabatan }}</td>
                                    <td>{{ $d->tahun }}</td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a class="ml-1" href="/kesepakatanbersama/{{ Crypt::encrypt($d->no_kb) }}/cetak" target="_blank"><i class="feather icon-printer primary"></i></a>
                                            <a class="ml-1 edit" no_kb="{{ $d->no_kb }}" href="#"><i class="feather icon-edit success"></i></a>
                                            <a class="ml-1 potongan" no_kb="{{ $d->no_kb }}" href="#"><i class="feather icon-tag danger"></i></a>
                                            <form method="POST" class="deleteform" action="/kesepakatanbersama/{{Crypt::encrypt($d->no_kb)}}/delete">
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
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="mdleditkb" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit Kesepakatan Bersama</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="loadformedit">

            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdlpotongan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input Potongan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="loadformpotongan">

            </div>
        </div>
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

        $(".edit").click(function(e) {
            e.preventDefault();
            var no_kb = $(this).attr("no_kb");
            $.ajax({
                type: 'POST'
                , url: '/kesepakatanbersama/edit'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_kb: no_kb
                }
                , cache: false
                , success: function(respond) {
                    $('#mdleditkb').modal({
                        backdrop: 'static'
                        , keyboard: false
                    });
                    $("#loadformedit").html(respond);
                }
            });

        });


        $(".potongan").click(function(e) {
            e.preventDefault();
            var no_kb = $(this).attr("no_kb");
            $.ajax({
                type: 'POST'
                , url: '/kesepakatanbersama/potongan'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_kb: no_kb
                }
                , cache: false
                , success: function(respond) {
                    $('#mdlpotongan').modal({
                        backdrop: 'static'
                        , keyboard: false
                    });
                    $("#loadformpotongan").html(respond);
                }
            });

        });
    });

</script>
@endpush
