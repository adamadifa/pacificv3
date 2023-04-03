@extends('layouts.midone')
@section('titlepage','Kontrak')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Kontrak</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/kontrak">Kontrak</a>
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
                    <a href="#" class="btn btn-primary" id="buatkontrak"><i class="fa fa-plus mr-1"></i> Buat Kontrak</a>
                </div>
                <div class="card-body">
                    <form action="/kontrak">
                        <div class="row">
                            <div class="col-lg-4 col-sm-12">
                                <x-inputtext label="Nama Karyawan" field="nama_karyawan_search" icon="feather icon-users" value="{{ Request('nama_karyawan_search') }}" />
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <div class="form-group">
                                    <select name="id_perusahaan_search" id="id_perusahaan_search" class="form-control">
                                        <option value="">Perusahaan</option>
                                        <option value="MP">MP</option>
                                        <option value="PCF">PCF</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <div class="form-group">
                                    <select name="id_kantor_search" id="id_kantor_search" class="form-control">
                                        <option value="">Kantor</option>
                                        @foreach ($kantor as $d)
                                        <option {{ Request('id_kantor_search')==$d->kode_cabang ? 'selected' : '' }} value="{{ $d->kode_cabang }}">{{ $d->kode_cabang }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-12">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search mr-1"></i> Cari</button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>No. Kontrak</th>
                                    <th>Tanggal</th>
                                    <th>NIK</th>
                                    <th>Nama Karyawan</th>
                                    <th>Jabatan</th>
                                    <th>Kantor</th>
                                    <th>Perusahaan</th>
                                    <th>Periode</th>
                                    <th>Ket</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($datakontrak as $d)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $d->no_kontrak }}</td>
                                    <td>{{ DateToIndo2($d->dari) }}</td>
                                    <td>{{ $d->nik }}</td>
                                    <td>{{ $d->nama_karyawan }}</td>
                                    <td>{{ $d->nama_jabatan }}</td>
                                    <td>{{ $d->id_kantor }}</td>
                                    <td>{{ $d->id_perusahaan }}</td>
                                    <td>{{ date("d-m-Y",strtotime($d->dari)) }} s/d {{ date("d-m-Y",strtotime($d->sampai)) }}</td>
                                    <td>
                                        @php
                                        $start = date_create($d->dari);
                                        $end = date_create($d->sampai);
                                        @endphp
                                        {{ diffInMonths($start, $end). " bulan"; }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a class="ml-1" href="/kontrak/{{ Crypt::encrypt($d->no_kontrak) }}/cetak" target="_blank"><i class="feather icon-printer primary"></i></a>


                                            <a class="ml-1 edit" no_kontrak="{{ $d->no_kontrak }}" href="#"><i class="feather icon-edit success"></i></a>


                                            <form method="POST" class="deleteform" action="/kontrak/{{Crypt::encrypt($d->no_kontrak)}}/delete">
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
                        {{ $datakontrak->links('vendor.pagination.vuexy') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="mdlbuatkontrak" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Buat Kontrak</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="loadbuatkontrak">

            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdleditkontrak" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit Kontrak</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="loadeditkontrak">

            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        $('#buatkontrak').click(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST'
                , url: '/kontrak/create'
                , data: {
                    _token: '{{ csrf_token() }}'
                }
                , cache: false
                , success: function(respond) {
                    $("#loadbuatkontrak").html(respond);
                    $('#mdlbuatkontrak').modal({
                        backdrop: 'static'
                        , keyboard: false
                    });
                }
            });

        });

        $(".edit").click(function(e) {
            e.preventDefault();
            var no_kontrak = $(this).attr("no_kontrak");
            $.ajax({
                type: 'POST'
                , url: '/kontrak/edit'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_kontrak: no_kontrak
                }
                , cache: false
                , success: function(respond) {
                    $('#mdleditkontrak').modal({
                        backdrop: 'static'
                        , keyboard: false
                    });
                    $("#loadeditkontrak").html(respond);
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
    });

</script>
@endpush
