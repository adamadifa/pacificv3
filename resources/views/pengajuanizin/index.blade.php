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
                                        <form action="/pengajuanizin">
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
                                        <table class="table table-striped">
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
                                                    <th>Ket</th>
                                                    <th>Head Dept</th>
                                                    <th>HRD</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pengajuan_izin as $d)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $d->kode_izin }}</td>
                                                    <td>{{ date('d-m-Y',strtotime($d->dari)) }} s/d {{ date('d-m-Y',strtotime($d->sampai)) }}</td>
                                                    <td>{{ $d->nik }}</td>
                                                    <td>{{ $d->nama_karyawan }}</td>
                                                    <td>{{ $d->nama_jabatan }}</td>
                                                    <td>{{ $d->kode_dept }}</td>
                                                    <td>{{ $d->id_kantor }}</td>
                                                    <td>{{ $d->jmlhari }} Hari</td>
                                                    <td>{{ $d->keterangan }} <br>
                                                        {!! !empty($d->keterangan_hrd) ? "<span class='danger'><b>HRD</b></span> : <span class='danger'>".$d->keterangan_hrd."</span>":"" !!}
                                                    </td>
                                                    <td class="text-center">
                                                        @if (empty($d->head_dept))
                                                        <i class="fa fa-history text-warning"></i>
                                                        @elseif($d->head_dept == 1)
                                                        <i class="fa fa-check text-success"></i>
                                                        @elseif($d->head_dept == 2)
                                                        <i class="fa fa-close text-danger"></i>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if (empty($d->hrd))
                                                        <i class="fa fa-history text-warning"></i>
                                                        @elseif($d->hrd == 1)
                                                        <i class="fa fa-check text-success"></i>
                                                        @elseif($d->hrd == 2)
                                                        <i class="fa fa-close text-danger"></i>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group" aria-label="Basic example">
                                                            @if ($level != "manager hrd")
                                                            @if (empty(Auth::user()->pic_presensi) || !empty(Auth::user()->pic_presensi) && $level=="kepala admin")
                                                            @if (empty($d->head_dept) && empty($d->hrd))
                                                            <a href="#" class="approveizin" kode_izin="{{ $d->kode_izin }}">
                                                                <i class="feather icon-external-link text-primary"></i>
                                                            </a>
                                                            @elseif(!empty($d->head_dept) && empty($d->hrd))
                                                            <a href="/izinabsen/{{ $d->kode_izin }}/batalkan" class="warning">Batalkan</a>
                                                            @endif
                                                            @endif
                                                            @else
                                                            @if (!empty($d->head_dept) && empty($d->hrd) || empty($d->head_dept) && $d->kode_dept=="HRD")
                                                            <a href="#" class="approveizin" kode_izin="{{ $d->kode_izin }}">
                                                                <i class="feather icon-external-link text-primary"></i>
                                                            </a>
                                                            @elseif(empty($d->head_dept))
                                                            <span class="badge bg-warning">Waiting</span>
                                                            @elseif(!empty($d->hrd))
                                                            <a href="/izinabsen/{{ $d->kode_izin }}/batalkan" class="warning">Batalkan</a>
                                                            @endif
                                                            @endif


                                                            @if ($level == "manager hrd")
                                                            <a href="#" class="ket_hrd" kode_izin="{{ $d->kode_izin }}"><i class="feather icon-message-square ml-1 info"></i></a>
                                                            @endif

                                                            @if (empty($d->head_dept) && $level != "manager hrd")
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
        $(".approveizin").click(function(e) {
            $("#mdlapprove").modal("show");
            var kode_izin = $(this).attr('kode_izin');
            $("#kode_izin").val(kode_izin);
        });
    });

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
        $("#loadbuatizin").load('/pengajuanizin/createizinabsen');
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

</script>
@endpush
