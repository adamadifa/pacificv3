@extends('layouts.midone')
@section('titlepage','Tambah Karyawan Libur')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Tambah Karyawan Libur</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Tambah Karyawan Libur</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        @include('layouts.notification')
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <a href="#" class="btn btn-primary" id="tambahkaryawan"><i class="fa fa-plus mr-1"></i> Tambah Karyawan</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <table class="table">
                                    <tr>
                                        <th>Kode Libur</th>
                                        <td>{{ $harilibur->kode_libur }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal</th>
                                        <td>{{ DateToIndo2($harilibur->tanggal_libur) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kategori</th>
                                        <td>
                                            @if ($harilibur->kategori==1)
                                            Libur Nasional
                                            @elseif($harilibur->kategori==2)
                                            Libur Pengganti Minggu
                                            @elseif($harilibur->kategori==3)
                                            Dirumahkan
                                            @elseif($harilibur->kategori==3)
                                            WFH
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Kantor</th>
                                        <td>{{ $harilibur->id_kantor }}</td>
                                    </tr>
                                    <tr>
                                        <th>Departemen</th>
                                        <td>{{ !empty($harilibur->kode_dept) ? $harilibur->kode_dept : "ALL"  }}</td>
                                    </tr>
                                    <tr>
                                        <th>Keterangan</th>
                                        <td>{{ $harilibur->keterangan }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table class="table  table-hover-animation" id="tabelshift1">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Nik</th>
                                            <th>Nama Karyawan</th>
                                            <th>Kode Dept</th>
                                            <th>Jabatan</th>
                                            <th>Grup</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody id="loadliburkaryawan" style="font-size:14px !important">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="mdltambahkaryawan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 960px !important">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Tambah Karyawan</span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="loadkaryawan">

            </div>
        </div>
    </div>
</div>


@endsection

@push('myscript')
<script>
    $(function() {
        $("#tambahkaryawan").click(function(e) {
            e.preventDefault();
            var kode_libur = "{{ $harilibur->kode_libur }}";
            var id_kantor = "{{ $harilibur->id_kantor }}";
            var kode_dept = "{{ !empty($harilibur->kode_dept) ? $harilibur->kode_dept : 'ALL' }}";
            $("#mdltambahkaryawan").modal({
                backdrop: 'static'
                , keyboard: false
            , });

            $("#loadkaryawan").load('/harilibur/' + kode_libur + '/' + id_kantor + '/' + kode_dept + '/getkaryawan');
        });


        function loadliburkaryawan() {
            var kode_libur = "{{ $harilibur->kode_libur }}";
            $("#loadliburkaryawan").load('/harilibur/' + kode_libur + '/getliburkaryawan');
        }

        loadliburkaryawan();
    });

</script>
@endpush
