@extends('layouts.midone')
@section('titlepage','Pengajuan Izin')
@section('content')
<style>
    .form-group {
        margin-bottom: 5px !important;
    }

    .form-label-group {
        margin-bottom: 5px !important;
    }

    .col-4,
    .col-5,
    .col-6,
    .col-3 {
        padding-right: 1px !important;
    }

    .header-fixed {
        width: 100%
    }

    .header-fixed>thead,
    .header-fixed>tbody,
    .header-fixed>thead>tr,
    .header-fixed>tbody>tr,
    .header-fixed>thead>tr>th,
    .header-fixed>tbody>tr>td {
        display: block;
    }

    .header-fixed>tbody>tr:after,
    .header-fixed>thead>tr:after {
        content: ' ';
        display: block;
        visibility: hidden;
        clear: both;
    }

    .header-fixed>tbody {
        overflow-y: auto;
        height: 400px;
    }

    .header-fixed>tbody>tr>td:nth-child(1),
    .header-fixed>thead>tr>th:nth-child(1) {
        width: 3%;
        float: left;
    }

    .header-fixed>tbody>tr>td:nth-child(2),
    .header-fixed>thead>tr>th:nth-child(2) {
        width: 6%;
        float: left;
    }

    .header-fixed>tbody>tr>td:nth-child(3),
    .header-fixed>thead>tr>th:nth-child(3) {
        width: 12%;
        float: left;
    }

    .header-fixed>tbody>tr>td:nth-child(4),
    .header-fixed>thead>tr>th:nth-child(4) {
        width: 6%;
        float: left;
    }

    .header-fixed>tbody>tr>td:nth-child(5),
    .header-fixed>thead>tr>th:nth-child(5) {
        width: 13%;
        float: left;
    }

    .header-fixed>tbody>tr>td:nth-child(6),
    .header-fixed>thead>tr>th:nth-child(6) {
        width: 8%;
        float: left;
    }

    .header-fixed>tbody>tr>td:nth-child(7),
    .header-fixed>thead>tr>th:nth-child(7) {
        width: 5%;
        float: left;
    }

    .header-fixed>tbody>tr>td:nth-child(8),
    .header-fixed>thead>tr>th:nth-child(8) {
        width: 5%;
        float: left;
    }

    .header-fixed>tbody>tr>td:nth-child(9),
    .header-fixed>thead>tr>th:nth-child(9) {
        width: 5%;
        float: left;
    }

    .header-fixed>tbody>tr>td:nth-child(10),
    .header-fixed>thead>tr>th:nth-child(10) {
        width: 6%;
        float: left;
    }

    .header-fixed>tbody>tr>td:nth-child(11),
    .header-fixed>thead>tr>th:nth-child(11) {
        width: 10%;
        float: left;
    }

    .header-fixed>tbody>tr>td:nth-child(12),
    .header-fixed>thead>tr>th:nth-child(12) {
        width: 6%;
        float: left;
    }

    .header-fixed>tbody>tr>td:nth-child(13),
    .header-fixed>thead>tr>th:nth-child(13) {
        width: 5%;
        float: left;
    }

    .header-fixed>tbody>tr>td:nth-child(14),
    .header-fixed>thead>tr>th:nth-child(14) {
        width: 5%;
        float: left;
    }

</style>

<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Pengajuan Izin</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/pengajuanizin">Pengajuan Izin</a>
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
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        @if (Auth::user()->pic_presensi == 1)
                        <a href="#" class="btn btn-primary" id="buatizin"><i class="fa fa-plus mr-1"></i> Buat Pengajuan</a>
                        @endif

                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            @include('pengajuanizin.nav_izin')
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active">
                                <div class="row mb-1">
                                    <div class="col-12">
                                        <form action="/pengajuanizin/sakit">
                                            <div class="row">
                                                <div class="col-lg-6 col-sm-12">
                                                    <x-inputtext label="Dari" field="dari_search" value="{{ Request('dari_search') }}" icon="feather icon-calendar" datepicker />
                                                </div>
                                                <div class="col-lg-6 col-sm-12">
                                                    <x-inputtext label="Sampai" field="sampai_search" value="{{ Request('sampai_search') }}" icon="feather icon-calendar" datepicker />
                                                </div>
                                            </div>
                                            @php
                                            $level_search = ["admin","manager hrd","manager accounting","direktur"];
                                            @endphp
                                            @if (Auth::user()->kode_cabang=="PCF" && in_array($level,$level_search))
                                            <div class="row">
                                                <div class="col-lg-3 col-sm-12">
                                                    <div class="form-group  ">
                                                        <select name="kode_cabang" id="" class="form-control">
                                                            @if (Auth::user()->kode_cabang=="PCF")
                                                            <option value="">Semua Cabang</option>
                                                            @else
                                                            <option value="">Pilih Cabang</option>
                                                            @endif
                                                            @foreach ($cabang as $c)
                                                            <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{ $c->kode_cabang }}">{{ strtoupper($c->kode_cabang=="PST" ? "PUSAT" : $c->nama_cabang) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-12">
                                                    <div class="form-group">
                                                        <select name="kode_dept" id="kode_dept" class="form-control">
                                                            <option value="">Departemen</option>
                                                            @foreach ($departemen as $d)
                                                            <option {{ Request('kode_dept') == $d->kode_dept ? 'selected' : ''  }} value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-12">
                                                    <div class="form-group">
                                                        <x-inputtext label="Nama Karyawan" value="{{ Request('nama_karyawan') }}" field="nama_karyawan" icon="feather icon-user" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-sm-12">
                                                    <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                            @else
                                            <div class="row">
                                                <div class="col-lg-8 col-sm-12">
                                                    <div class="form-group">
                                                        <x-inputtext label="Nama Karyawan" value="{{ Request('nama_karyawan') }}" field="nama_karyawan" icon="feather icon-user" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-sm-12">
                                                    <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                            @endif
                                        </form>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped header-fixed">
                                                <thead>
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Kode</th>
                                                        <th>Tanggal</th>
                                                        <th>NIK</th>
                                                        <th>Nama Karyawan</th>
                                                        <th>Jabatan</th>
                                                        <th>Dept</th>
                                                        <th>Kantor</th>
                                                        <th>Jml Hari</th>
                                                        <th>SID</th>
                                                        <th>Ket</th>
                                                        <th class="text-center">Head</th>
                                                        <th class="text-center">HRD</th>
                                                        <th class="text-center">Dirut</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($pengajuan_izin as $d)
                                                    <tr>
                                                        <td class="filterable-cell">{{ $loop->iteration }}</td>
                                                        <td class="filterable-cell">{{ $d->kode_izin }}</td>
                                                        <td class="filterable-cell">{{ date('d-m-y',strtotime($d->dari)) }} s/d {{ date('d-m-y',strtotime($d->sampai)) }}</td>
                                                        <td class="filterable-cell">{{ $d->nik }}</td>
                                                        <td class="filterable-cell">{{ $d->nama_karyawan }}</td>
                                                        <td class="filterable-cell">{{ $d->nama_jabatan }}</td>
                                                        <td class="filterable-cell">{{ $d->kode_dept }}</td>
                                                        <td class="filterable-cell">{{ $d->id_kantor }}</td>
                                                        <td class="filterable-cell">{{ $d->jmlhari }} Hari</td>
                                                        <td class="filterable-cell">
                                                            @if (!empty($d->sid))
                                                            @php
                                                            $path = Storage::url('uploads/sid/'.$d->sid);
                                                            $src = "uploads/sid/".$d->sid;
                                                            $cekimage = Storage::disk('public')->exists($src);
                                                            @endphp
                                                            @if ($cekimage)
                                                            <a href="{{ url($path) }}" class="text-info">
                                                                <i class="feather icon-paperclip text-info"></i> Lihat SID
                                                            </a>
                                                            @else
                                                            <a href="https://presensi.pacific-tasikmalaya.com/storage/uploads/sid/{{ $d->sid }}" class="text-info">
                                                                <i class="feather icon-paperclip text-info"></i> Lihat SID
                                                            </a>
                                                            @endif

                                                            @else
                                                            <i class="fa fa-close text-danger"></i>
                                                            @endif
                                                        </td>
                                                        <td class="filterable-cell">{{ $d->keterangan }} <br>
                                                            {!! !empty($d->keterangan_hrd) ? "<span class='danger'><b>HRD</b></span> : <span class='danger'>".$d->keterangan_hrd."</span>":"" !!}
                                                        </td>
                                                        <td class="text-center filterable-cell">
                                                            @if ($d->nama_jabatan == "GENERAL MANAGER")
                                                            <i class="fa fa-minus-circle text-danger"></i>
                                                            @else
                                                            @if (empty($d->head_dept))
                                                            <i class="fa fa-history text-warning"></i>
                                                            @elseif($d->head_dept == 1)
                                                            <i class="fa fa-check text-success"></i>
                                                            @elseif($d->head_dept == 2)
                                                            <i class="fa fa-close text-danger"></i>
                                                            @endif
                                                            @endif

                                                        </td>
                                                        <td class="text-center filterable-cell">
                                                            @if (empty($d->hrd))
                                                            <i class="fa fa-history text-warning"></i>
                                                            @elseif($d->hrd == 1)
                                                            <i class="fa fa-check text-success"></i>
                                                            @elseif($d->hrd == 2)
                                                            <i class="fa fa-close text-danger"></i>
                                                            @endif
                                                        </td>
                                                        <td class="text-center filterable-cell">

                                                            @php
                                                            $jabatan = array("MANAGER","GENERAL MANAGER","ASST. MANAGER");
                                                            @endphp
                                                            @if (in_array($d->nama_jabatan,$jabatan))
                                                            @if (empty($d->direktur))
                                                            <i class="fa fa-history text-warning"></i>
                                                            @elseif($d->direktur == 1)
                                                            <i class="fa fa-check text-success"></i>
                                                            @elseif($d->direktur == 2)
                                                            <i class="fa fa-close text-danger"></i>
                                                            @endif
                                                            @else
                                                            <i class="fa fa-minus-circle text-danger"></i>
                                                            @endif

                                                        </td>
                                                        <td class="filterable-cell">
                                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                                {{-- Jika Level Bukan Manager HRD --}}
                                                                @if ($level != "manager hrd" && $level != "direktur")
                                                                {{-- Jika Bukan PIC atau PIC dan Level Kepala Admin --}}
                                                                @if (empty(Auth::user()->pic_presensi) || !empty(Auth::user()->pic_presensi) && $level=="kepala admin" && $d->id_perusahaan == "MP" )
                                                                {{-- Jika Hed Dept Belum Approve dan HRD Belum Approve --}}
                                                                @if (empty($d->head_dept) && empty($d->hrd))
                                                                <a href="#" class="approveizin" kode_izin="{{ $d->kode_izin }}">
                                                                    <i class="feather icon-external-link text-primary"></i>
                                                                </a>
                                                                {{-- Jika Head Dept Sudah Approve dan HRD Belum Approve --}}
                                                                @elseif(!empty($d->head_dept) && empty($d->hrd) )
                                                                <a href="/izinabsen/{{ $d->kode_izin }}/batalkan" class="warning"><i class="fa fa-close text-danger"></i> </a>
                                                                @endif
                                                                @endif
                                                                @elseif($level=="direktur")
                                                                @if (empty($d->direktur))
                                                                <a href="#" class="approveizin" kode_izin="{{ $d->kode_izin }}">
                                                                    <i class="feather icon-external-link text-primary"></i>
                                                                </a>
                                                                @else
                                                                <a href="/izinabsen/{{ $d->kode_izin }}/batalkan" class="warning"><i class="fa fa-close text-danger"></i></a>
                                                                @endif
                                                                @else
                                                                {{-- Level Manager HRD --}}
                                                                {{-- Jika Head Dept Sudah Approve dan HRD Belum Approve --}}
                                                                @if (!empty($d->head_dept) && empty($d->hrd) || empty($d->head_dept) && $d->kode_dept=="HRD" || empty($d->head_dept) && $d->nama_jabatan=="GENERAL MANAGER")
                                                                <a href="#" class="approveizin" kode_izin="{{ $d->kode_izin }}">
                                                                    <i class="feather icon-external-link text-primary"></i>
                                                                </a>
                                                                {{-- Jika Heade Dept Belum Approve --}}
                                                                @elseif(empty($d->head_dept))
                                                                {{-- <i class="fa fa-history text-warning"></i> --}}
                                                                {{-- Jika HRD sudah Approve --}}
                                                                @elseif(!empty($d->hrd))
                                                                <a href="/izinabsen/{{ $d->kode_izin }}/batalkan" class="warning"><i class="fa fa-close text-danger"></i></a>
                                                                @endif
                                                                @endif


                                                                @if ($level == "manager hrd")
                                                                <a href="#" class="ket_hrd" kode_izin="{{ $d->kode_izin }}"><i class="feather icon-message-square ml-1 info"></i></a>
                                                                @endif

                                                                @if (empty($d->status_approved) || $d->status_approved==2 )
                                                                <form method="POST" class="deleteform" action="/pengajuanizin/{{Crypt::encrypt($d->kode_izin)}}/delete">
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
                                            {{ $pengajuan_izin->links('vendor.pagination.vuexy') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="mdlapprove" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Approve Pengajuan Izin</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/pengajuanizin/approve" method="POST">
                    @csrf
                    <input type="hidden" name="kode_izin" id="kode_izin">
                    <div class="row">
                        <div class="col-12">
                            <div class="btn-group w-100">
                                <button name="approve" value="approve" class="btn btn-success w-100">
                                    <i class="feather icon-check mr-1"></i>
                                    Setuju
                                </button>
                                <button name="decline" value="decline" class="btn btn-danger w-100">
                                    <i class="fa fa-close mr-1"></i>
                                    Tolak
                                </button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="mdlbuatizin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Buat Izin</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="loadbuatizin">
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdl_kethrd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Keterangan HRD</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="load_kethrd">
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {
        $(".ket_hrd").click(function(e) {
            $("#mdl_kethrd").modal("show");
            var kode_izin = $(this).attr('kode_izin');
            $("#load_kethrd").load('/pengajuanizin/' + kode_izin + '/create_kethrd');
        });

        $("#buatizin").click(function(e) {
            $('#mdlbuatizin').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadbuatizin").load('/pengajuanizin/createizinsakit');
        });
        $(".approveizin").click(function(e) {
            $("#mdlapprove").modal("show");
            var kode_izin = $(this).attr('kode_izin');
            $("#kode_izin").val(kode_izin);
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
