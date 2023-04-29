@extends('layouts.midone')
@section('titlepage','Setoran Penjualan')
@section('content')

<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Setoran Pusat</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/setoranpusat">Setoran Pusat</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <input type="hidden" id="cektutuplaporan">
        <div class="col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    @if (in_array($level,$setoranpusat_add))
                    <a href="#" class="btn btn-primary" id="inputsetoranpusat"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                    @endif
                </div>
                <div class="card-body">
                    <form action="/setoranpusat" id="frmcari">
                        <div class="row">
                            <div class="col-lg-6 col-sm-12">
                                <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker value="{{ Request('dari') }}" />
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker value="{{ Request('sampai') }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-sm-12">
                                <div class="form-group  ">
                                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                                        <option value="">Pilih Cabang</option>
                                        @foreach ($cabang as $c)
                                        <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{
                                            $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <div class="form-group">
                                    <select name="kode_bank" id="kode_bank" class="form-control">
                                        <option value="">Pilih Bank</option>
                                        @foreach ($bank as $d)
                                        <option {{ Request('kode_bank')==$d->kode_bank ? 'selected' :'' }} value="{{ $d->kode_bank }}">{{ $d->nama_bank }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i> Cari Data </button>
                            </div>
                        </div>
                    </form>
                    @include('layouts.notification')
                    <a href="/setoranpusat/cetak?dari={{ Request('dari') }}&sampai={{ Request('sampai') }}&kode_cabang={{ Request('kode_cabang') }}&kode_bank={{ Request('kode_bank') }}&excel=false" target="_blank" class="btn btn-primary"><i class="feather icon-printer"></i></a>
                    <a href="/setoranpusat/cetak?dari={{ Request('dari') }}&sampai={{ Request('sampai') }}&kode_cabang={{ Request('kode_cabang') }}&kode_bank={{ Request('kode_bank') }}&excel=true" class="btn btn-success"><i class="feather icon-download"></i></a>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover-animation mt-2">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width:10%">Tanggal</th>
                                    <th style="width:25%">Setoran</th>
                                    <th style="width: 15%">Bank</th>
                                    <th>Kertas</th>
                                    <th>Logam</th>
                                    <th>Transfer</th>
                                    <th>Giro</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($setoranpusat as $d)
                                @php
                                $totalsetoran = $d->uang_kertas + $d->uang_logam + $d->transfer + $d->giro;
                                @endphp
                                <tr>
                                    <td>{{ date("d-m-Y",strtotime($d->tgl_setoranpusat)) }}</td>
                                    <td>{{ ucwords(strtolower($d->keterangan)) }}</td>
                                    <td>{{ $d->nama_bank }}</td>
                                    <td class="text-right">{{ !empty($d->uang_kertas) ? rupiah($d->uang_kertas) : '' }}</td>
                                    <td class="text-right">{{ !empty($d->uang_logam) ? rupiah($d->uang_logam) : '' }}</td>
                                    <td class="text-right">{{ !empty($d->transfer) ? rupiah($d->transfer) : '' }}</td>
                                    <td class="text-right">{{ !empty($d->giro) ? rupiah($d->giro) : '' }}</td>
                                    <td class="text-right">{{ !empty($totalsetoran) ? rupiah($totalsetoran) : '' }}</td>
                                    <td>
                                        @if ($d->status==1)
                                        <span class="badge bg-success"><i class="fa fa-check"></i> Diterima {{ date("d-m-Y",strtotime($d->tgl_diterimapusat)) }}</span>
                                        @elseif($d->status==2)
                                        <span class="badge bg-danger"><i class="fa fa-close"></i> Ditolak</span>
                                        @else
                                        <span class="badge bg-warning"><i class="fa fa-history"></i> Belum Diterima</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            @if (in_array($level,$setoranpusat_edit))
                                            @if (empty($d->no_ref))
                                            <a class="ml-1 edit" status="{{ $d->status }}" kode_setoranpusat="{{ $d->kode_setoranpusat }}" href="#"><i class="feather icon-edit success"></i></a>
                                            @endif
                                            @endif
                                            @if (in_array($level,$setoranpusat_hapus))
                                            @if ($d->status==0)
                                            <form method="POST" class="deleteform" action="/setoranpusat/{{Crypt::encrypt($d->kode_setoranpusat)}}/delete">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" tanggal="{{ $d->tgl_setoranpusat }}" class="delete-confirm ml-1">
                                                    <i class="feather icon-trash danger"></i>
                                                </a>
                                            </form>
                                            @endif
                                            @endif
                                            @if (in_array($level,$setoranpusat_terimasetoran))
                                            @if (empty($d->no_ref))
                                            @if ($d->status==0)
                                            <a href="#" kodesetoranpusat="{{ $d->kode_setoranpusat }}" class="success terimasetoran ml-1"><i class="feather icon-external-link"></i></a>
                                            @else
                                            <a href="/setoranpusat/{{ Crypt::encrypt($d->kode_setoranpusat) }}/batalkansetoran" class="danger batalkansetoran ml-1"><i class="fa fa-close"></i></a>
                                            @endif
                                            @endif
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
<!-- Input Setoran Penjualan -->
<div class="modal fade text-left" id="mdlinputsetoranpusat" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input Setoran Pusat</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputsetoranpusat"></div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Setoran Pusat -->
<div class="modal fade text-left" id="mdleditsetoranpusat" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit Setoran Pusat</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadeditsetoranpusat"></div>
            </div>
        </div>
    </div>
</div>

<!-- Penerimaan Setoran -->
<div class="modal fade text-left" id="mdlterimasetoran" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input Terima Seoran</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadterimasetoeran"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {
        $("#inputsetoranpusat").click(function(e) {
            e.preventDefault();
            $('#mdlinputsetoranpusat').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadinputsetoranpusat").load("/setoranpusat/create");
        });


        $(".terimasetoran").click(function(e) {
            e.preventDefault();
            var kode_setoranpusat = $(this).attr("kodesetoranpusat");
            $('#mdlterimasetoran').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadterimasetoeran").load("/setoranpusat/" + kode_setoranpusat + "/createterimasetoran");
        });


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

        $(".edit").click(function(e) {
            e.preventDefault();
            var kode_setoranpusat = $(this).attr("kode_setoranpusat");
            var status = $(this).attr("status");
            $('#mdleditsetoranpusat').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadeditsetoranpusat").load("/setoranpusat/" + kode_setoranpusat + "/edit");
        });
    });

</script>
@endpush
