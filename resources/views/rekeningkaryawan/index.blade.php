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
            @if (in_array($level,$karyawan_pinjaman))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info" role="alert">
                        <h4 class="alert-heading">Info Pinjaman & Kasbon</h4>
                        <p class="mb-0">
                            <ul>
                                <li><i class="feather icon-external-link primary mr-1"></i> <span class="primary">Pinjaman</span></li>
                                <li><i class="feather icon-external-link warning mr-1"></i> <span class="warning">Kasbon</span></li>
                            </ul>
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <div class="card-header">
                            @if (in_array($level,$karyawan_tambah))
                            <a href="#" id="tambahkaryawan" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                            @endif
                            <a href="#" id="cekhabiskontrak" class="btn btn-danger"><i class="feather icon-user-x mr-1"></i>Karyawan Habis Kontrak</a>
                        </div>


                        <div class="card-body">
                            <form action="/rekeningkaryawan">

                                <div class="row">
                                    @php
                                    $level_search = ["admin","manager hrd","manager accounting","direktur"];
                                    @endphp
                                    @if (Auth::user()->kode_cabang=="PCF" && in_array($level,$level_search))
                                    <div class="col-lg-2 col-sm-12">
                                        <x-inputtext label="Nama Karyawan" field="nama_karyawan_search" icon="feather icon-users" value="{{ Request('nama_karyawan_search') }}" />
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <select name="kode_dept_search" id="kode_dept_search" class="form-control">
                                                <option value="">Departemen</option>
                                                @foreach ($departemen as $d)
                                                <option {{ Request('kode_dept_search')==$d->kode_dept ? 'selected' : '' }} value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <select name="id_perusahaan_search" id="id_perusahaan_search" class="form-control">
                                                <option value="">MP/PCF</option>
                                                <option value="MP" {{ Request('id_perusahaan_search') == "MP" ? "selected" : "" }}>MP</option>
                                                <option value="PCF" {{ Request('id_perusahaan_search') == "PCF" ? "selected" : "" }}>PCF</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
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
                                        <div class="form-group">
                                            <select name="grup_search" id="grup_search" class="form-control">
                                                <option value="">Grup</option>
                                                @foreach ($group as $d)
                                                <option {{ Request('grup_search')==$d->id ? 'selected' : '' }} value="{{ $d->id }}">{{ $d->nama_group }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <select name="status_aktif_karyawan" id="status_aktif_karyawan" class="form-control">
                                                <option value="">Status</option>
                                                <option value="1" {{ Request('status_aktif_karyawan') == "1" ? "selected" :"" }}>Aktif</option>
                                                <option value="0" {{ Request('status_aktif_karyawan') === "0" ? "selected" :"" }}>Non Aktif</option>
                                            </select>
                                        </div>
                                    </div>
                                    @else
                                    <div class="col-lg-8 col-sm-12">
                                        <x-inputtext label="Nama Karyawan" field="nama_karyawan_search" icon="feather icon-users" value="{{ Request('nama_karyawan_search') }}" />
                                    </div>
                                    @endif

                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-sm-12">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary w-100"><i class="fa fa-search mr-1"></i> Cari</button>
                                        </div>
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
                                            <th>Departemen</th>
                                            <th>Jabatan</th>
                                            <th>Kantor</th>
                                            <th>Status</th>
                                            <th>No. Rekening</th>
                                            <th>Nama Rekening</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($karyawan as $d)
                                        <tr style="background-color:{{ $d->status_aktif == 0 ? '#ff695e' : '' }}">
                                            <td class="text-center">{{ $loop->iteration + $karyawan->firstItem()-1 }}</td>
                                            <td>{{ $d->nik }}</td>
                                            <td>{{ $d->nama_karyawan }}</td>
                                            <td>{{ $d->nama_dept }}</td>
                                            <td>{{ $d->nama_jabatan }}</td>

                                            <td>{{ $d->id_kantor }}</td>
                                            <td>
                                                @if ($d->status_karyawan=='T')
                                                <span class="badge bg-green">T</span>
                                                @elseif($d->status_karyawan=="K")
                                                <span class="badge bg-warning">K</span>
                                                @endif
                                            </td>
                                            <td>{{ $d->no_rekening }}</td>
                                            <td>{{ $d->nama_rekening }}</td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    <a href="#" class="edit" nik="{{ Crypt::encrypt($d->nik) }}"><i class="feather icon-edit info"></i></a>
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
            </div>

        </div>
        <!-- Data list view end -->
    </div>
</div>
<div class="modal fade text-left" id="mdleditkaryawan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit Karyawan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadeditkaryawan"></div>
            </div>
        </div>
    </div>
</div>


@endsection
@push('myscript')
<script>
    $(function() {
        $('.edit').click(function(e) {
            var nik = $(this).attr("nik");
            e.preventDefault();
            $('#mdleditkaryawan').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadeditkaryawan").load('/rekeningkaryawan/' + nik + '/edit');
        });

    });

</script>
@endpush
