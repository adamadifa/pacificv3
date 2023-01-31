@extends('layouts.midone')
@section('titlepage','Data Karyawan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Karyawan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/karyawan">Data Karyawan</a>
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
                @if (in_array($level,$karyawan_tambah))
                <div class="card-header">
                    <a href="#" id="tambahkaryawan" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                @endif
                <div class="card-body">
                    <form action="/karyawan">
                        <div class="row">
                            <div class="col-lg-9 col-sm-12">
                                <x-inputtext label="Nama Karyawan" field="nama_karyawan" icon="feather icon-users" value="{{ Request('nama_karyawan') }}" />
                            </div>

                            <div class="col-lg-3 col-sm-12">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search mr-1"></i> Cari</button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>NIK</th>
                                    <th>Nama Karyawan</th>
                                    <th>JK</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Departemen</th>
                                    <th>Jabatan</th>
                                    <th>MP/PCF</th>
                                    <th>Kantor</th>
                                    <th>Klasifikasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($karyawan as $d)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration + $karyawan->firstItem()-1 }}</td>
                                    <td>{{ $d->nik }}</td>
                                    <td>{{ $d->nama_karyawan }}</td>
                                    <td>{{ $d->jenis_kelamin == 1 ? 'L' : 'P' }}</td>
                                    <td>{{ date("d-m-Y",strtotime($d->tgl_masuk)) }}</td>
                                    <td>{{ $d->nama_dept }}</td>
                                    <td>{{ $d->nama_jabatan }}</td>
                                    <td>{{ $d->id_perusahaan }}</td>
                                    <td>{{ $d->id_kantor }}</td>
                                    <td>{{ $d->klasifikasi }}</td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            @if (in_array($level,$karyawan_edit))
                                            <a class="ml-1 edit" nik="{{ $d->kode_supplier }}" href="#"><i class="feather icon-edit success"></i></a>
                                            @endif
                                            @if (in_array($level,$karyawan_hapus))
                                            <form method="POST" class="deleteform" action="/supplier/{{Crypt::encrypt($d->nik)}}/delete">
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
                        {{ $karyawan->links('vendor.pagination.vuexy') }}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Input Supplier -->
<div class="modal fade text-left" id="mdlinputkaryawan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Tambah Karyawan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputkaryawan"></div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Supplier -->
<div class="modal fade text-left" id="mdleditsupplier" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit Supplier</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadeditsupplier"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {

        $('#tambahkaryawan').click(function(e) {
            e.preventDefault();
            $('#mdlinputkaryawan').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadinputkaryawan").load('/karyawan/create');
        });
    });

</script>
@endpush
