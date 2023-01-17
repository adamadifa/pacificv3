@extends('layouts.midone')
@section('titlepage','Data Retur')
@section('content')
<style>
    .form-group {
        margin-bottom: 5px !important;
    }

    .form-label-group {
        margin-bottom: 5px !important;
    }

</style>
<style>
    .card {
        margin-bottom: 0.8rem !important;
    }

</style>
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h4 class="content-header-title float-left mb-0">Data Retur</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <input type="hidden" id="cektutuplaporan">
        <div class="row">
            <div class="col-12">
                <form action="/retur">
                    <div class="row">
                        <div class="col-lg-6 col-sm-6">
                            <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker value="{{ Request('dari') }}" />
                        </div>
                        <div class="col-lg-6 col-sm-6">
                            <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker value="{{ Request('sampai') }}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-sm-12">
                            <x-inputtext label="No Faktur" field="no_fak_penj" icon="feather icon-credit-card" value="{{ Request('no_fak_penj') }}" />
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <x-inputtext label="Nama Pelanggan" field="nama_pelanggan" icon="feather icon-user" value="{{ Request('nama_pelanggan') }}" />
                        </div>
                        <div class="col-lg-1 col-sm-12">
                            <button type="submit" name="submit" value="1" class="btn btn-primary btn-block search"><i class="fa fa-search mr-1"></i> Cari Data </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row mt-1">
            <div class="col-12">
                @include('layouts.notification')
                @foreach ($retur as $d)
                <a href="#" no_retur_penj="{{ $d->no_retur_penj }}" class="detailretur" style="color: inherit">
                    <div class="row">
                        <div class="col-12">
                            <div class="card {{ $d->jenis_retur == 'pf' ? 'bg-gradient-danger' : 'bg-gradient-success' }}">
                                <div class="card-content">
                                    <div class="card-body" style="padding:8px 10px 8px 8px !important">
                                        <p class="card-text d-flex justify-content-between">
                                            <span class="d-flex justify-content-between">
                                                <span>
                                                    <b>{{ $d->no_fak_penj }} -{{ $d->nama_pelanggan }}</b> <br> {{ DateToIndo2($d->tglretur) }}
                                                </span>
                                            </span>
                                            <span style="text-align: right">
                                                @if ($d->jenis_retur=="pf")
                                                <span class="badge bg-danger">PF</span>
                                                @else
                                                <span class="badge bg-success">GB</span>
                                                @endif
                                                <br>
                                                <span style="font-weight: bold">{{rupiah($d->subtotal_pf)}}</span>
                                                {{-- <span class="badge bg-success">{{ date("H:i:s",strtotime($d->checkin_time)) }}</span> <br>
                                            <span class="badge bg-info">{{ !empty($d->checkout_time) ? date("H:i",strtotime($d->checkout_time)) : 0 }}</span> --}}
                                            </span>

                                        </p>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Detail Retur -->
<div class="modal fade text-left" id="mdldetailretur" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail Retur <span id="no_retur_penj"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaddetailretur"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {
        function cektutuplaporan(tanggal) {
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                    , jenislaporan: "penjualan"
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#cektutuplaporan").val(respond);
                }
            });
        }
        $('.delete-confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            var tanggal = $(this).attr("tanggal");
            cektutuplaporan(tanggal);
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
                        var cektutuplaporan = $("#cektutuplaporan").val();
                        if (cektutuplaporan > 0) {
                            swal("Oops", "Laporan Periode Ini Sudah Di Tutup !", "warning");
                            return false;
                        } else {
                            form.submit();
                        }
                    }
                });
        });

        function loaddetailretur(no_retur_penj) {
            $.ajax({
                type: 'POST'
                , url: '/retur/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_retur_penj: no_retur_penj
                }
                , cache: false
                , success: function(respond) {
                    $("#no_retur_penj").text(no_retur_penj);
                    $("#loaddetailretur").html(respond);
                }
            });
        }
        $('.detailretur').click(function(e) {
            var no_retur_penj = $(this).attr("no_retur_penj");
            e.preventDefault();
            loaddetailretur(no_retur_penj);
            $('#mdldetailretur').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });
    });

</script>
@endpush
