@extends('layouts.midone')
@section('titlepage', 'Lembur')
@section('content')
    <style>
        .form-label {
            font-weight: bold;
            margin-top: 10px;
        }

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
                        <h2 class="content-header-title float-left mb-0">Lembur</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/lembur">Lembur</a>
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
                            <a href="#" class="btn btn-primary" id="tambahlembur"><i class="fa fa-plus mr-1"></i>
                                Tambah Data</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <form action="{{ URL::current() }}">
                                        @if (in_array($level, ['admin', 'manager hrd', 'spv presensi', 'direktur']))

                                            <div class="row">
                                                <div class="col-2">
                                                    {{-- <label for="" class="form-label mb-1">Omset Bulan</label> --}}
                                                    <x-inputtext label="Dari" field="dari" icon="feather icon-calendar"
                                                        datepicker value="{{ Request('dari') }}" />
                                                </div>
                                                <div class="col-2">
                                                    <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar"
                                                        datepicker value="{{ Request('sampai') }}" />
                                                </div>
                                                <div class="col-2">
                                                    <div class="form-group">
                                                        <select name="kategori_search" id="kategori_search"
                                                            class="form-control">
                                                            <option value="">Semua Kategori </option>
                                                            <option value="1"
                                                                {{ Request('kategori_search') == 1 ? 'selected' : '' }}>
                                                                REGULER</option>
                                                            <option value="2"
                                                                {{ Request('kategori_search') == 2 ? 'selected' : '' }}>
                                                                LEMBUR HARI LIBUR</option>

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-2">
                                                    <div class="form-group">
                                                        <select name="kode_cabang_search" id="kode_cabang_search"
                                                            class="form-control">
                                                            <option value="">Semua Kantor</option>
                                                            @foreach ($cb as $d)
                                                                <option value="{{ $d->kode_cabang }}"
                                                                    {{ Request('kode_cabang_search') == $d->kode_cabang ? 'selected' : '' }}>
                                                                    {{ $d->nama_cabang }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-2">
                                                    <div class="form-group">
                                                        <select name="kode_dept_search" id="kode_dept_search"
                                                            class="form-control">
                                                            <option value="">Departemen</option>
                                                            @foreach ($departemen as $d)
                                                                <option value="{{ $d->kode_dept }}"
                                                                    {{ Request('kode_dept_search') == $d->kode_dept ? 'selected' : '' }}>
                                                                    {{ $d->nama_dept }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-2">
                                                    <div class="form-group">
                                                        <button type="submit" name="submit" class="btn btn-primary"><i
                                                                class="fa fa-search"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="row">
                                                <div class="col-3">
                                                    {{-- <label for="" class="form-label mb-1">Omset Bulan</label> --}}
                                                    <x-inputtext label="Dari" field="dari" icon="feather icon-calendar"
                                                        datepicker value="{{ Request('dari') }}" />
                                                </div>
                                                <div class="col-3">
                                                    <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar"
                                                        datepicker value="{{ Request('sampai') }}" />
                                                </div>
                                                <div class="col-3">
                                                    <div class="form-group">
                                                        <select name="kategori_search" id="kategori_search"
                                                            class="form-control">
                                                            <option value="">Semua Kategori Libur</option>
                                                            <option value="1"
                                                                {{ Request('kategori_search') == 1 ? 'selected' : '' }}>
                                                                Libur
                                                                Nasional</option>
                                                            <option value="2"
                                                                {{ Request('kategori_search') == 2 ? 'selected' : '' }}>
                                                                Libur
                                                                Pengganti Minggu</option>
                                                            <option value="3"
                                                                {{ Request('kategori_search') == 3 ? 'selected' : '' }}>WFH
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-2">
                                                    <div class="form-group">
                                                        <button type="submit" name="submit" class="btn btn-primary"><i
                                                                class="fa fa-search"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    </form>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-hover-animation" id="tabelnonshift">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Kode Lembur</th>
                                                <th>Tanggal</th>
                                                <th>Dari</th>
                                                <th>Sampai</th>
                                                <th>Kantor</th>
                                                <th>Dept</th>
                                                <th>Kategori</th>
                                                <th>Istirahat</th>
                                                {{-- <th style="width:25%">Keterangan</th> --}}
                                                <th>Head</th>
                                                <th>GM</th>
                                                <th>HRD</th>
                                                <th>Dirut</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lembur as $d)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $d->kode_lembur }}</td>
                                                    <td>{{ date('d-m-Y', strtotime($d->tanggal)) }}</td>
                                                    <td>{{ date('d-m-Y H:i', strtotime($d->tanggal_dari)) }}</td>
                                                    <td>{{ date('d-m-Y H:i', strtotime($d->tanggal_sampai)) }}</td>
                                                    <td>{{ $d->id_kantor }}</td>
                                                    <td>{{ $d->kode_dept }}</td>
                                                    <td>
                                                        @if ($d->kategori == 1)
                                                            REGULER
                                                        @else
                                                            LEMBUR HARI LIBUR
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($d->istirahat == 1)
                                                            <span class="badge bg-success">Ada</span>
                                                        @else
                                                            <span><i class="fa fa-close danger"></i></span>
                                                        @endif
                                                    </td>
                                                    {{-- <td>{{ ucwords(strtolower($d->keterangan)) }}
                                                        <br>
                                                        {!! !empty($d->keterangan_hrd)
                                                            ? "<span class='danger'><b>HRD</b></span> : <span class='danger'>" . $d->keterangan_hrd . '</span>'
                                                            : '' !!}
                                                    </td> --}}
                                                    <td>
                                                        @if (empty($d->head))
                                                            <i class="fa fa-history warning"></i>
                                                        @else
                                                            @if ($d->head == 1)
                                                                <i class="feather icon-check success"></i>
                                                            @else
                                                                <i class="fa fa-close danger"></i>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (empty($d->gm))
                                                            <i class="fa fa-history warning"></i>
                                                        @else
                                                            @if ($d->gm == 1)
                                                                <i class="feather icon-check success"></i>
                                                            @else
                                                                <i class="fa fa-close danger"></i>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (empty($d->hrd))
                                                            <i class="fa fa-history warning"></i>
                                                        @else
                                                            @if ($d->hrd == 1)
                                                                <i class="feather icon-check success"></i>
                                                            @else
                                                                <i class="fa fa-close danger"></i>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (empty($d->dirut))
                                                            <i class="fa fa-history warning"></i>
                                                        @else
                                                            @if ($d->dirut == 1)
                                                                <i class="feather icon-check success"></i>
                                                            @else
                                                                <i class="fa fa-close danger"></i>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            @php
                                                                $level_head = [
                                                                    'spv maintenance',
                                                                    'manager produksi',
                                                                    'manager ga',
                                                                ];
                                                            @endphp
                                                            @if (in_array($level, $level_head))
                                                                @if (empty($d->head))
                                                                    <a href="#"
                                                                        kode_lembur="{{ Crypt::encrypt($d->kode_lembur) }}"
                                                                        class="approve"><i
                                                                            class="feather icon-external-link primary"></i></a>
                                                                @elseif(!empty($d->head) && empty($d->gm))
                                                                    <a href="/lembur/{{ Crypt::encrypt($d->kode_lembur) }}/batalkan"
                                                                        class="warning">Batalkan</a>
                                                                @endif
                                                            @endif
                                                            @if ($level == 'emf')
                                                                @if (empty($d->gm) && !empty($d->head))
                                                                    <a href="#"
                                                                        kode_lembur="{{ Crypt::encrypt($d->kode_lembur) }}"
                                                                        class="approve"><i
                                                                            class="feather icon-external-link primary"></i></a>
                                                                @elseif(!empty($d->gm) && empty($d->hrd))
                                                                    <a href="/lembur/{{ Crypt::encrypt($d->kode_lembur) }}/batalkan"
                                                                        class="warning">Batalkan</a>
                                                                @endif
                                                            @endif
                                                            @if ($level == 'manager hrd' || $level == 'spv presensi')
                                                                @if (empty($d->hrd) && !empty($d->gm))
                                                                    <a href="#"
                                                                        kode_lembur="{{ Crypt::encrypt($d->kode_lembur) }}"
                                                                        class="approve"><i
                                                                            class="feather icon-external-link primary"></i></a>
                                                                @elseif(!empty($d->hrd) && empty($d->dirut))
                                                                    <a href="/lembur/{{ Crypt::encrypt($d->kode_lembur) }}/batalkan"
                                                                        class="warning">Batalkan</a>
                                                                @endif
                                                            @endif
                                                            @if ($level == 'direktur')
                                                                @if (empty($d->dirut) && !empty($d->gm))
                                                                    <a href="#"
                                                                        kode_lembur="{{ Crypt::encrypt($d->kode_lembur) }}"
                                                                        class="approve"><i
                                                                            class="feather icon-external-link primary"></i></a>
                                                                @else
                                                                    <a href="/lembur/{{ Crypt::encrypt($d->kode_lembur) }}/batalkan"
                                                                        class="warning">Batalkan</a>
                                                                @endif
                                                            @endif
                                                            <a
                                                                href="/lembur/{{ Crypt::encrypt($d->kode_lembur) }}/tambahkaryawan">
                                                                <i class="feather icon-settings success ml-1"></i>
                                                            </a>

                                                            @if (empty($d->hrd) && Auth::user()->level=="spv presensi" || empty($d->hrd) && Auth::user()->level=="admin")
                                                                <form method="POST" class="deleteform"
                                                                    action="/lembur/{{ Crypt::encrypt($d->kode_lembur) }}/delete">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <a href="#" class="delete-confirm ml-1">
                                                                        <i class="feather icon-trash danger"></i>
                                                                    </a>
                                                                </form>
                                                            @endif
                                                            {{-- <a href="#" class="edit" kode_libur="{{ $d->kode_libur }}"><i class="feather icon-edit info"></i></a> --}}


                                                            @if ($level == 'manager hrd' || $level == 'admin' || $level == 'spv presensi')
                                                                <a href="#" class="ket_hrd"
                                                                    kode_lembur="{{ $d->kode_lembur }}"><i
                                                                        class="feather icon-message-square ml-1 info"></i></a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{ $lembur->links('vendor.pagination.vuexy') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="mdltambahlembur" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Tambah Data Lembur</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="/lembur/store" method="POST" id="frmLembur">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <x-inputtext label="Auto" field="kode_lembur" icon="feather icon-credit-card"
                                    readonly />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <x-inputtext label="Tanggal" field="tanggal" icon="feather icon-calendar" datepicker />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8">
                                <x-inputtext label="Dari" field="tanggal_dari" icon="feather icon-calendar"
                                    datepicker />
                            </div>
                            <div class="col-4">
                                <x-inputtext label="Jam Mulai" field="jam_dari" icon="feather icon-clock" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8">
                                <x-inputtext label="Sampai" field="tanggal_sampai" icon="feather icon-calendar"
                                    datepicker />
                            </div>
                            <div class="col-4">
                                <x-inputtext label="Jam Akhir" field="jam_sampai" icon="feather icon-clock" />
                            </div>
                        </div>
                        @if (Auth::user()->kode_cabang == 'PCF')
                            @if ($level == 'manager hrd' || $level == 'admin' || $level == 'spv presensi')
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <select name="id_kantor" id="id_kantor" class="form-control">
                                                <option value="">Pilih Kantor</option>
                                                @foreach ($cabang as $c)
                                                    <option value="{{ $c->kode_cabang }}">
                                                        {{ strtoupper($c->kode_cabang == 'PST' ? 'PUSAT' : $c->nama_cabang) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="id_kantor" value="PST">
                            @endif
                        @else
                            <input type="hidden" name="id_kantor" value="{{ Auth::user()->kode_cabang }}">
                        @endif

                        @if (Auth::user()->kode_cabang == 'PCF')
                            @if (Auth::user()->level == 'manager hrd' || Auth::user()->level == 'admin' || Auth::user()->level == 'spv presensi')
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <select name="kode_dept" id="kode_dept" class="form-control">
                                                <option value="">Departemen</option>
                                                @foreach ($departemen as $d)
                                                    <option value="{{ $d->kode_dept }}">{{ strtoupper($d->nama_dept) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="kode_dept" value="{{ Auth::user()->kode_dept_presensi }}">
                            @endif
                        @else
                            @if (Auth::user()->kode_cabang == 'PST')
                                <input type="hidden" name="kode_dept" value="{{ Auth::user()->kode_dept_presensi }}">
                            @else
                                <input type="hidden" name="kode_dept" value="">
                            @endif
                        @endif

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <select name="kategori" id="kategori" class="form-control">
                                        <option value="">Pilih Kategori Lembur</option>
                                        <option value="1">REGULER</option>
                                        <option value="2">LEMBUR HARI LIBUR</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <select name="istirahat" id="istirahat" class="form-control">
                                        <option value="">Istirahat / Tidak Istirahat</option>
                                        <option value="1">Istirahat</option>
                                        <option value="0">Tidak Istirahat</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <x-inputtext label="Keterangan" field="keterangan" icon="feather icon-file" />
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <button class="btn btn-primary btn-block" type="submit" name="submit"><i
                                        class="feather icon-send mr-1"></i>Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="mdlapprove" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Approve Pengajuan Lembur</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="/lembur/approve" method="POST">
                        @csrf
                        <input type="hidden" name="kode_lembur" id="kode_lembur_approve">
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
    <div class="modal fade text-left" id="mdl_kethrd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
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
                var kode_lembur = $(this).attr('kode_lembur');
                $("#load_kethrd").load('/lembur/' + kode_lembur + '/create_kethrd');
            });


            $("#tambahlembur").click(function(e) {
                e.preventDefault();
                $('#mdltambahlembur').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            });

            $(".approve").click(function(e) {
                e.preventDefault();
                $("#mdlapprove").modal("show");
                var kode_lembur = $(this).attr('kode_lembur');
                $("#kode_lembur_approve").val(kode_lembur);
            });
            $("#frmLembur").submit(function(e) {
                var tanggal_dari = $("#tanggal_dari").val();
                var tanggal_sampai = $("#tanggal_sampai").val();
                var jam_dari = $("#jam_dari").val();
                var jam_sampai = $("#jam_sampai").val();
                var id_kantor = $("#id_kantor").val();
                var kode_dept = $("#kode_dept").val();
                var keterangan = $("#keterangan").val();
                var kategori = $("#kategori").val();
                var istirahat = $("#istirahat").val();
                var level = "{{ $level }}";
                if (tanggal_dari == "") {
                    swal({
                        title: 'Oops',
                        text: 'Tanggal Dari Harus Dipilih !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#tanggal_dari").focus();
                    });
                    return false;
                } else if (jam_dari == "") {
                    swal({
                        title: 'Oops',
                        text: 'Jam Mulai Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#jam_dari").focus();
                    });
                    return false;
                } else if (tanggal_sampai == "") {
                    swal({
                        title: 'Oops',
                        text: 'Tanggal Sampai Harus Dipilih !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#tanggal_sampai").focus();
                    });
                    return false;
                } else if (jam_sampai == "") {
                    swal({
                        title: 'Oops',
                        text: 'Jam Sampai Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#jam_sampai").focus();
                    });
                    return false;
                } else if (level == "manager hrd" && id_kantor == "" || level == "spv presensi" &&
                    id_kantor == "" || level == "admin" && id_kantor ==
                    "") {
                    swal({
                        title: 'Oops',
                        text: 'Kantor Harus Dipilih !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#id_kantor").focus();
                    });
                    return false;
                } else if (level == "manager hrd" && kode_dept == "" || level == "spv presensi" &&
                    kode_dept == "" || level == "admin" && kode_dept ==
                    "") {
                    swal({
                        title: 'Oops',
                        text: 'Departemen Harus Dipilih !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#kode_dept").focus();
                    });
                    return false;
                } else if (kategori == "") {
                    swal({
                        title: 'Oops',
                        text: 'Kategori Lembur Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#kategori").focus();
                    });
                    return false;
                } else if (keterangan == "") {
                    swal({
                        title: 'Oops',
                        text: 'Keterangan Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#keterangan").focus();
                    });
                    return false;
                } else if (istirahat == "") {
                    swal({
                        title: 'Oops',
                        text: 'Istirahat / Tidak Istirahat Harus Dipilih !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#istirahat").focus();
                    });
                    return false;
                }
            });


            $('#jam_dari,#jam_sampai').mask('00:00');

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
        });
    </script>
@endpush
