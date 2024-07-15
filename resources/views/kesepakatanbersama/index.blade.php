@extends('layouts.midone')
@section('titlepage', 'Kesepakatan Bersama')
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
                        <form action="/kesepakatanbersama">
                            <div class="row">
                                <div class="col-lg-4 col-sm-12">
                                    <x-inputtext label="Nama Karyawan" field="nama_karyawan_search" icon="feather icon-users"
                                        value="{{ Request('nama_karyawan_search') }}" />
                                </div>
                                <div class="col-lg-3 col-sm-12">
                                    <div class="form-group">
                                        <select name="kode_dept_search" id="kode_dept_search" class="form-control">
                                            <option value="">Departemen</option>
                                            @foreach ($departemen as $d)
                                                <option {{ Request('kode_dept_search') == $d->kode_dept ? 'selected' : '' }} value="{{ $d->kode_dept }}">
                                                    {{ $d->nama_dept }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-12">
                                    <div class="form-group">
                                        <select name="id_kantor_search" id="id_kantor_search" class="form-control">
                                            <option value="">Kantor</option>
                                            @foreach ($kantor as $d)
                                                <option {{ Request('id_kantor_search') == $d->kode_cabang ? 'selected' : '' }}
                                                    value="{{ $d->kode_cabang }}">{{ $d->kode_cabang }}</option>
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
                                        <th>No. KB</th>
                                        <th>Tanggal</th>
                                        <th>NIK</th>
                                        <th>Nama Karyawan</th>
                                        <th>Jabatan</th>

                                        <th>Jeda</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kb as $d)
                                        @php
                                            $hariini = date('Y-m-d');
                                            $tgl1 = date_create($d->tgl_kb);
                                            $tgl2 = date_create($hariini);
                                            $jarak = date_diff($tgl1, $tgl2);

                                            $jmlhari = $jarak->days;

                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $d->no_kb }}</td>
                                            <td>{{ DateToIndo2($d->tgl_kb) }}</td>
                                            <td>{{ $d->nik }}</td>
                                            <td>{{ $d->nama_karyawan }}</td>
                                            <td>{{ $d->nama_jabatan }}</td>

                                            <td>{{ $jmlhari < 30 ? $jmlhari : 30 }} Hari</td>
                                            <td>
                                                {{ $d->no_kontrak }}
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    <a class="ml-1" href="/kesepakatanbersama/{{ Crypt::encrypt($d->no_kb) }}/cetak" target="_blank"><i
                                                            class="feather icon-printer primary"></i></a>
                                                    @if (!empty($d->no_kontrak))
                                                        <a class="ml-1" href="/kontrak/{{ Crypt::encrypt($d->no_kontrak) }}/cetak" target="_blank"><i
                                                                class="feather icon-printer success"></i></a>
                                                    @endif
                                                    <a class="ml-1 edit" no_kb="{{ $d->no_kb }}" href="#"><i
                                                            class="feather icon-edit success"></i></a>
                                                    <a class="ml-1 potongan" no_kb="{{ $d->no_kb }}" href="#"><i
                                                            class="feather icon-tag danger"></i></a>
                                                    <form method="POST" class="deleteform"
                                                        action="/kesepakatanbersama/{{ Crypt::encrypt($d->no_kb) }}/delete">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a href="#" class="delete-confirm ml-1">
                                                            <i class="feather icon-trash danger"></i>
                                                        </a>
                                                    </form>
                                                    {{-- @if ($jmlhari >= 30) --}}
                                                    @if (empty($d->no_kontrak))
                                                        <a href="#" nik="{{ $d->nik }}" kode_penilaian="{{ $d->kode_penilaian }}"
                                                            class="danger buatkontrak ml-1">Buat Kontrak</a>
                                                    @endif

                                                    {{-- @endif --}}

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div style="float: right;">
                            {{ $kb->links('vendor.pagination.vuexy') }}
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
@endsection

@push('myscript')
    <script>
        $(function() {
            $('.buatkontrak').click(function(e) {
                var kode_penilaian = $(this).attr("kode_penilaian");
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: '/kontrak/createfromkb',
                    data: {
                        _token: '{{ csrf_token() }}',
                        kode_penilaian: kode_penilaian
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loadbuatkontrak").html(respond);
                        $('#mdlbuatkontrak').modal({
                            backdrop: 'static',
                            keyboard: false
                        });
                    }
                });

            });

            $('.delete-confirm').click(function(event) {
                var form = $(this).closest("form");
                var name = $(this).data("name");
                event.preventDefault();
                swal({
                        title: `Are you sure you want to delete this record?`,
                        text: "If you delete this, it will be gone forever.",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
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
                    type: 'POST',
                    url: '/kesepakatanbersama/edit',
                    data: {
                        _token: "{{ csrf_token() }}",
                        no_kb: no_kb
                    },
                    cache: false,
                    success: function(respond) {
                        $('#mdleditkb').modal({
                            backdrop: 'static',
                            keyboard: false
                        });
                        $("#loadformedit").html(respond);
                    }
                });

            });


            $(".potongan").click(function(e) {
                e.preventDefault();
                var no_kb = $(this).attr("no_kb");
                $.ajax({
                    type: 'POST',
                    url: '/kesepakatanbersama/potongan',
                    data: {
                        _token: "{{ csrf_token() }}",
                        no_kb: no_kb
                    },
                    cache: false,
                    success: function(respond) {
                        $('#mdlpotongan').modal({
                            backdrop: 'static',
                            keyboard: false
                        });
                        $("#loadformpotongan").html(respond);
                    }
                });

            });
        });
    </script>
@endpush
