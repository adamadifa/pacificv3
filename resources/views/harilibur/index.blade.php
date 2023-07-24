@extends('layouts.midone')
@section('titlepage','Hari Libur')
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
                    <h2 class="content-header-title float-left mb-0">Hari Libur</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/harilibur">Hari Libur</a>
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
                        <a href="#" class="btn btn-primary" id="tambahlibur"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <form action="{{ URL::current() }}">
                                    <div class="row">
                                        <div class="col-2">
                                            {{-- <label for="" class="form-label mb-1">Omset Bulan</label> --}}
                                            <div class="form-group">
                                                <select class="form-control" id="bulan" name="bulan">
                                                    <option value="">Bulan</option>
                                                    <?php
                                                    $bulanini = date("m");
                                                    for ($i = 1; $i < count($bulan); $i++) {
                                                    ?>
                                                    <option {{ Request('bulan') == $i ? "selected" : "" }} value="<?php echo $i; ?>"><?php echo $bulan[$i]; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <select class="form-control" id="tahun" name="tahun">
                                                    <option value="">Tahun</option>
                                                    <?php
                                                    $tahunmulai = 2023;
                                                    for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                                                    ?>
                                                    <option {{ Request('tahun') == $thn ? "selected" : "" }} value="<?php echo $thn; ?>"><?php echo $thn; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <select name="kategori_search" id="kategori_search" class="form-control">
                                                    <option value="">Semua Kategori Libur</option>
                                                    <option value="1" {{ Request('kategori_search') ==1 ? 'selected' : '' }}>Libur Nasional</option>
                                                    <option value="2" {{ Request('kategori_search') ==2  ? 'selected' : '' }}>Libur Pengganti Minggu</option>
                                                    <option value="3" {{ Request('kategori_search') ==3  ? 'selected' : '' }}>WFH</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <select id="id_kantor_search" name="id_kantor_search" class="form-control">
                                                    <option value="">Semua Kantor</option>
                                                    @foreach ($cabang as $d)
                                                    <option {{ Request('id_kantor_search') == $d->kode_cabang  ? 'selected' : '' }} value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>


                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-hover-animation" id="tabelnonshift">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Kode Libur</th>
                                            <th>Tanggal</th>
                                            <th>Pengganti Tgl</th>
                                            <th>Kantor</th>
                                            <th>Kategori</th>
                                            <th style="width:15%">Keterangan</th>
                                            <th>HRD</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($harilibur as $d)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $d->kode_libur }}</td>
                                            <td>{{ date("d-m-Y",strtotime($d->tanggal_libur)) }}</td>
                                            <td>{{ !empty($d->tanggal_diganti) ? date("d-m-Y",strtotime($d->tanggal_diganti)) : "" }}</td>
                                            <td>{{ $d->id_kantor }}</td>
                                            <td>
                                                @if ($d->kategori==1)
                                                <span class="badge bg-success">Libur Nasional</span>
                                                @elseif($d->kategori==2)
                                                <span class="badge bg-info">Libur Pengganti Minggu</span>
                                                @elseif($d->kategori==3)
                                                <span class="badge bg-warning">Dirumahkan</span>
                                                @elseif($d->kategori==4)
                                                <span class="badge bg-primary">WFH</span>
                                                @endif
                                            </td>
                                            <td>{{ ucwords(strtolower($d->keterangan)) }}</td>
                                            <td>
                                                @if (empty($d->hrd))
                                                <i class="fa fa-history warning"></i>
                                                @else
                                                @if ($d->hrd==1)
                                                <i class="feather icon-check success"></i>
                                                @else
                                                <i class="fa fa-close danger"></i>
                                                @endif
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    @if ($level == "manager hrd")
                                                    @if (empty($d->hrd))
                                                    <a href="#" kode_libur="{{ Crypt::encrypt($d->kode_libur) }}" class="approve"><i class="feather icon-external-link primary"></i></a>
                                                    @else
                                                    <a href="/harilibur/{{ Crypt::encrypt($d->kode_libur) }}/batalkan" class="warning">Batalkan</a>
                                                    @endif
                                                    @endif
                                                    <a href="/harilibur/{{ Crypt::encrypt($d->kode_libur) }}/tambahkaryawan">
                                                        <i class="feather icon-settings success ml-1"></i>
                                                    </a>

                                                    {{-- <a href="#" class="edit" kode_libur="{{ $d->kode_libur }}"><i class="feather icon-edit info"></i></a> --}}
                                                    <form method="POST" class="deleteform" action="/harilibur/{{Crypt::encrypt($d->kode_libur)}}/delete">
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
                                {{ $harilibur->links('vendor.pagination.vuexy') }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdltambahlibur" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Tambah Data Libur</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/harilibur/store" method="POST" id="frmLibur">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext label="Auto" field="kode_libur" icon="feather icon-credit-card" readonly />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext label="Tanggal" field="tanggal" icon="feather icon-calendar" datepicker />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <select name="id_kantor" id="id_kantor" class="form-control">
                                    <option value="">Pilih Kantor</option>
                                    @foreach ($cabang as $c)
                                    <option value="{{ $c->kode_cabang }}">{{ strtoupper($c->kode_cabang=="PST" ? "PUSAT" : $c->nama_cabang) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <select name="kategori" id="kategori" class="form-control">
                                    <option value="">Pilih Kategori Libur</option>
                                    <option value="1">Libur Nasional</option>
                                    <option value="2">Libur Pengganti Minggu</option>
                                    <option value="3">Dirumahkan</option>
                                    <option value="4">WFH</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="tgldiganti">
                        <div class="col-12">
                            <x-inputtext label="Tanggal Libur Yang Diganti" field="tanggal_diganti" icon="feather icon-calendar" datepicker />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext label="Keterangan" field="keterangan" icon="feather icon-file" />
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button class="btn btn-primary btn-block" type="submit" name="submit"><i class="feather icon-send mr-1"></i>Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdlapprove" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Approve Pengajuan Libur</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/harilibur/approve" method="POST">
                    @csrf
                    <input type="hidden" name="kode_libur" id="kode_libur_approve">
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

@endsection
@push('myscript')
<script>
    $(function() {

        $(".approve").click(function(e) {
            e.preventDefault();
            $("#mdlapprove").modal("show");
            var kode_libur = $(this).attr('kode_libur');
            $("#kode_libur_approve").val(kode_libur);
        });

        //$("#tglminggu").hide();
        $("#tambahlibur").click(function(e) {
            e.preventDefault();
            $('#mdltambahlibur').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        // $(".edit").click(function(e) {
        //     e.preventDefault();
        //     var kode_libur = $(this).attr('kode_libur');
        //     $("#mdleditlibur").modal({
        //         backdrop: 'static'
        //         , keyboard: false
        //     });
        //     $("#loadeditlibur").load('/harilibur/' + kode_libur + '/edit');
        // });
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

        $("#kategori").change(function(e) {
            var kategori = $(this).val();
            //alert(kategori);
            if (kategori == 3 || kategori == 4) {
                $("#tanggal_diganti").hide();
            } else {
                $("#tanggal_diganti").show();

            }
        });
        $("#frmLibur").submit(function(e) {
            var tanggal = $("#tanggal").val();
            var keterangan = $("#keterangan").val();
            var id_kantor = $("#id_kantor").val();
            var kategori = $("#kategori").val();
            var tanggal_diganti = $("#tanggal_diganti").val();
            if (tanggal == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tanggal").focus();
                });
                return false;
            } else if (id_kantor == "") {
                swal({
                    title: 'Oops'
                    , text: 'Kantor Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#id_kantor").focus();
                });
                return false;
            } else if (kategori == "") {
                swal({
                    title: 'Oops'
                    , text: 'Kategori Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kategori").focus();
                });
                return false;
            } else if (tanggal_diganti == "" && kategori != 3) {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Libur Yang Di Ganti Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tanggal_minggu").focus();
                });
                return false;
            } else if (keterangan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Keterangan Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#dari").focus();
                });
                return false;
            }
        });

    });

</script>
@endpush
