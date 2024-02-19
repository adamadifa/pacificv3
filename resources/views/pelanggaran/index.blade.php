@extends('layouts.midone')
@section('titlepage', 'Pelanggaran')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Pelanggaran</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/pelanggaran">Pelanggaran</a>
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
                        <a href="#" class="btn btn-primary" id="inputpelanggaran"><i class="fa fa-plus mr-1"></i>
                            Input Pelanggaran</a>
                    </div>
                    <div class="card-body">
                        <form action="/pelanggaran">
                            <div class="row">
                                <div class="col-lg-3 col-sm-12">
                                    <x-inputtext label="Nama Karyawan" field="nama_karyawan_search"
                                        icon="feather icon-users" value="{{ Request('nama_karyawan_search') }}" />
                                </div>
                                <div class="col-lg-2 col-sm-12">
                                    <div class="form-group">
                                        <select name="id_perusahaan_search" id="id_perusahaan_search" class="form-control">
                                            <option value="">Perusahaan</option>
                                            <option value="MP">MP</option>
                                            <option value="PCF">PCF</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-sm-12">
                                    <div class="form-group">
                                        <select name="id_kantor_search" id="id_kantor_search" class="form-control">
                                            <option value="">Kantor</option>
                                            @foreach ($kantor as $d)
                                                <option {{ Request('id_kantor_search') == $d->kode_cabang ? 'selected' : '' }}
                                                    value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-sm-12">
                                    <div class="form-group">
                                        <select name="kode_dept_search" id="kode_dept_search" class="form-control">
                                            <option value="">Departemen</option>
                                            @foreach ($departemen as $d)
                                                <option {{ Request('kode_dept_search') == $d->kode_dept ? 'selected' : '' }}
                                                    value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-sm-12">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search mr-1"></i>
                                        Cari</button>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-hover-animation">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th>No. Surat</th>
                                        <th>Tanggal</th>
                                        <th>NIK</th>
                                        <th>Nama Karyawan</th>
                                        <th>Jabatan</th>

                                        <th>Kantor</th>
                                        <th>Dept</th>
                                        <th>Kategori</th>
                                        <th>Berlaku</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pelanggaran as $d)
                                        <tr>
                                            <td>{{ $loop->iteration + $pelanggaran->firstItem() - 1 }}</td>
                                            <td>{{ $d->no_sp }}</td>
                                            <td>{{ date('d-m-Y', strtotime($d->dari)) }}</td>
                                            <td>{{ $d->nik }}</td>
                                            <td>{{ $d->nama_karyawan }}</td>
                                            <td>{{ $d->nama_jabatan }}</td>
                                            <td>{{ $d->id_kantor }}</td>
                                            <td>{{ $d->nama_dept }}</td>
                                            <td>{{ $d->ket }}</td>
                                            <td>{{ date('d-m-Y', strtotime($d->dari)) }} s/d
                                                {{ date('d-m-Y', strtotime($d->sampai)) }}</td>
                                            <td>
                                                @php
                                                    $hariini = date('Y-m-d');
                                                @endphp

                                                @if ($hariini > $d->sampai)
                                                    <span class="badge bg-success">Selesai</span>
                                                @else
                                                    <span class="badge bg-danger">Belum Selesai</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a class="ml-1 edit" no_sp="{{ $d->no_sp }}" href="#"><i
                                                            class="feather icon-edit success"></i></a>
                                                    <form method="POST" class="deleteform"
                                                        action="/pelanggaran/{{ Crypt::encrypt($d->no_sp) }}/delete">
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
                            {{ $pelanggaran->links('vendor.pagination.vuexy') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="mdlinputpelanggaran" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Input Pelanggaran</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="loadinputpelanggaran">

                </div>
            </div>
        </div>
    </div>


    <div class="modal fade text-left" id="mdleditpelanggaran" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Edit Pelanggaran</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="loadeditpelanggaran">

                </div>
            </div>
        </div>
    </div>

@endsection

@push('myscript')
    <script>
        $(function() {
            $('#inputpelanggaran').click(function(e) {
                e.preventDefault();
                $("#loadinputpelanggaran").load('/pelanggaran/create');
                $('#mdlinputpelanggaran').modal({
                    backdrop: 'static',
                    keyboard: false
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
                var no_sp = $(this).attr("no_sp");
                $.ajax({
                    type: 'POST',
                    url: '/pelanggaran/edit',
                    data: {
                        _token: "{{ csrf_token() }}",
                        no_sp: no_sp
                    },
                    cache: false,
                    success: function(respond) {
                        $('#mdleditpelanggaran').modal({
                            backdrop: 'static',
                            keyboard: false
                        });
                        $("#loadeditpelanggaran").html(respond);
                    }
                });
            });
        })
    </script>
@endpush
