@extends('layouts.midone')
@section('titlepage','Data Master BPJS Kesehatan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Master BPJS Kesehatan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/insentif">Data Master BPJS Kesehatan</a>
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
                            <form action="/bpjskesehatan">
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
                                            <th>Tgl Berlaku</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bpjskes as $d)
                                        <tr>
                                            <td class="text-center">
                                                {{ $loop->iteration + $bpjskes->firstItem() - 1 }}
                                            </td>
                                            <td class="text-center">{{ $d->kode_bpjs_kes }}</td>
                                            <td class="text-center">{{ $d->nik }}</td>
                                            <td>{{ $d->nama_karyawan }}</td>
                                            <td class="text-center">{{ $d->id_kantor }}</td>
                                            <td>{{ $d->nama_jabatan }}</td>
                                            <td>{{ $d->nama_dept }}</td>
                                            <td class="text-right">{{ rupiah($d->iuran) }}</td>
                                            <td>{{ date("d-m-Y",strtotime($d->tgl_berlaku)) }}</td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    @if (in_array($level,$insentif_edit))
                                                    <a class="ml-1 edit" kode_bpjs_kes="{{ Crypt::encrypt($d->kode_bpjs_kes) }}" href="#"><i class="feather icon-edit success"></i></a>
                                                    @endif
                                                    @if (in_array($level,$insentif_hapus))
                                                    <form method="POST" class="deleteform" action="/bpjstk/{{Crypt::encrypt($d->kode_bpjs_kes)}}/delete">
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
                                {{ $bpjskes->links('vendor.pagination.vuexy') }}
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
                <h4 class="modal-title" id="myModalLabel18">Tambah BPJS Kesehatan</h4>
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
                <h4 class="modal-title" id="myModalLabel18">Edit BPJS Kesehatan</h4>
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
            var kode_bpjs_kes = $(this).attr("kode_bpjs_kes");
            e.preventDefault();
            $('#mdleditbpjstk').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadeditbpjstk").load('/bpjskesehatan/' + kode_bpjs_kes + '/edit');
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
