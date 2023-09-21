@extends('layouts.midone')
@section('titlepage','Tambah Karyawan Lembur')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Tambah Karyawan Lembur</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Tambah Karyawan Lembur</a>
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
                                        <th>Kode lembur</th>
                                        <td>{{ $lembur->kode_lembur }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal</th>
                                        <td>{{ date("d-m-Y H:i",strtotime($lembur->tanggal_dari)) }} s.d {{ date("d-m-Y H:i",strtotime($lembur->tanggal_sampai)) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Istirahat</th>
                                        <td>
                                            @if ($lembur->istirahat == 1)
                                            @php
                                            $istirahat = 1;
                                            @endphp
                                            <span class=" badge bg-success">Ada</span>
                                            @else
                                            @php
                                            $istirahat = 0;
                                            @endphp
                                            <span class="badge bg-danger"><i class="fa fa-close danger"></i></span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Jumlah Jam</th>
                                        <td>
                                            @php
                                            $jmljam = hitungjamdesimal($lembur->tanggal_dari,$lembur->tanggal_sampai);
                                            $jmljam = $jmljam > 7 ? 7 : $jmljam-$istirahat;
                                            @endphp
                                            {{ $jmljam }} Jam
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Kategori</th>
                                        <td>
                                            @if ($lembur->kategori==1)
                                            REGULER
                                            @elseif($lembur->kategori==2)
                                            LEMBUR HARI LIBUR
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Kantor</th>
                                        <td>{{ $lembur->id_kantor }}</td>
                                    </tr>
                                    <tr>
                                        <th>Departemen</th>
                                        <td>{{ !empty($lembur->kode_dept) ? $lembur->kode_dept : "ALL"  }}</td>
                                    </tr>
                                    <tr>
                                        <th>Keterangan</th>
                                        <td>{{ $lembur->keterangan }}</td>
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
                                    <tbody id="loadlemburkaryawan" style="font-size:14px !important">
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
            var kode_lembur = "{{ $lembur->kode_lembur }}";
            var id_kantor = "{{ $lembur->id_kantor }}";
            var kode_dept = "{{ !empty($lembur->kode_dept) ? $lembur->kode_dept : 'ALL' }}";
            $("#mdltambahkaryawan").modal({
                backdrop: 'static'
                , keyboard: false
            , });

            $("#loadkaryawan").load('/lembur/' + kode_lembur + '/' + id_kantor + '/' + kode_dept + '/getkaryawan');
        });


        function loadlemburkaryawan() {
            var kode_lembur = "{{ $lembur->kode_lembur}}";
            $("#loadlemburkaryawan").load('/lembur/' + kode_lembur + '/getlemburkaryawan');
        }


        loadlemburkaryawan();
    });

</script>
@endpush
