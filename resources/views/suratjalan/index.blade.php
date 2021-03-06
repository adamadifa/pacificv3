@extends('layouts.midone')
@section('titlepage', 'Data Surat Jalan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Surat Jalan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/suratjalan">Surat Jalan</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <input type="hidden" id="cektutuplaporan">
        <!-- Data list view starts -->
        <!-- DataTable starts -->
        @include('layouts.notification')
        <div class="col-md-12 col-sm-12 col-lg-12">
            <div class="card">

                <div class="card-body">
                    <form action="{{URL::current()}}">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <x-inputtext label="Dari" field="dari" value="{{Request('dari')}}" icon="feather icon-calendar" datepicker />
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <x-inputtext label="Sampai" field="sampai" value="{{Request('sampai')}}" icon="feather icon-calendar" datepicker />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-md-12 col-sm-12">
                                <x-inputtext label="No. Dokumen" field="no_dok" value="{{Request('no_dok')}}" icon="fa fa-barcode" />
                            </div>
                            <div class="col-lg-3 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <select name="status_sj" id="status_sj" class="form-control">
                                        <option value="">Status</option>
                                        <option {{Request('status_sj')=='BTC' ? 'selected' : ''}} value="BTC">Belum Diterima Cabang</option>
                                        <option {{Request('status_sj')=='STC' ? 'selected' : ''}} value="STC">Sudah Diterima Cabang</option>
                                        <option {{Request('status_sj')=='TO' ? 'selected' : ''}} value="TO">Transit Out</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-12 col-sm-12">
                                <div class="form-group  ">
                                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                                        @if (Auth::user()->kode_cabang!="PCF")
                                        <option value="">Pilih Cabang</option>
                                        @else
                                        <option value="">Semua Cabang</option>
                                        @endif
                                        @foreach ($cabang as $c)
                                        <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-search"></i> Cari</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>No. SJ</th>
                                    <th>No. Dok</th>
                                    <th>Tanggal</th>
                                    <th>Cabang</th>
                                    <th>No. Permintaan</th>
                                    <th>Status</th>
                                    <th>Tgl Diterima / Transit Out</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mutasi as $d)
                                <tr>
                                    <td>{{ $loop->iteration + $mutasi->firstItem() - 1 }} </td>
                                    <td><a href="#" no_mutasi_gudang="{{Crypt::encrypt($d->no_mutasi_gudang)}}" class="detail">{{$d->no_mutasi_gudang}}</a></td>
                                    <td>{{$d->no_dok}}</td>
                                    <td>{{date("d-m-Y",strtotime($d->tgl_mutasi_gudang))}}</td>
                                    <td>{{$d->kode_cabang}}</td>
                                    <td>{{$d->no_permintaan_pengiriman}}</td>
                                    <td>
                                        @if ($d->status_sj==0)
                                        <span class="badge bg-danger">Belum Diterima Cabang</span>
                                        @elseif($d->status_sj==1)
                                        <span class="badge bg-success">Sudah Diterima Cabang</span>
                                        @elseif($d->status_sj ==2)
                                        <span class="badge bg-info">Transit Out</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (!empty($d->tgl_mutasi_gudang_cabang))
                                        @if ($d->status_sj==1)
                                        @if (empty($d->tgl_transitin))
                                        <span class="badge bg-success">{{date("d-m-Y",strtotime($d->tgl_mutasi_gudang_cabang))}}</span>
                                        @else
                                        <span class="badge bg-success">{{date("d-m-Y",strtotime($d->tgl_transitin))}}</span>
                                        @endif
                                        @elseif($d->status_sj==2)
                                        <span class="badge bg-info">{{date("d-m-Y",strtotime($d->tgl_mutasi_gudang_cabang))}}</span>
                                        @endif
                                        @else
                                        <i class="fa fa-history warning"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            @if (in_array($level,$suratjalan_cetak))
                                            <a href="/suratjalan/{{Crypt::encrypt($d->no_mutasi_gudang)}}/cetak" target="_blank" class="ml-1"><i class="feather icon-printer info"></i></a>
                                            @endif
                                            @if ($d->status_sj == 0)
                                            @if (in_array($level,$suratjalan_hapus))
                                            <a href="/suratjalan/{{Crypt::encrypt($d->no_mutasi_gudang)}}/batalkansuratjalan" class="ml-1"><i class="feather icon-trash danger"></i></a>
                                            @endif
                                            <a href="#" class="ml-1 prosescabang" no_mutasi_gudang="{{Crypt::encrypt($d->no_mutasi_gudang)}}"><i class="feather icon-external-link success"></i></a>
                                            @else

                                            <form method="POST" class="deleteform" action="/suratjalan/{{Crypt::encrypt($d->no_mutasi_gudang)}}/batalkansjcabang">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" tanggal="{{ $d->tgl_mutasi_gudang_cabang }}" class="delete-confirm-batalkansj ml-1">
                                                    <i class="fa fa-close danger"></i>
                                                </a>
                                            </form>

                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $mutasi->links('vendor.pagination.vuexy') }}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Detail Surat Jalan -->
<div class="modal fade text-left" id="mdldetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail Surat Jalan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaddetail"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="mdlapprove" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Approve Surat Jalan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadapprove"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        function loaddetail(no_mutasi_gudang) {
            $("#loaddetail").load("/suratjalan/" + no_mutasi_gudang + "/show");
        }

        function loadapprove(no_mutasi_gudang) {
            $("#loadapprove").load("/suratjalan/" + no_mutasi_gudang + "/prosescabang");
        }
        $('.detail').click(function(e) {
            e.preventDefault();
            var no_mutasi_gudang = $(this).attr("no_mutasi_gudang");
            $('#mdldetail').modal({
                backdrop: 'static'
                , keyboard: false
            });
            loaddetail(no_mutasi_gudang);

        });

        $('.prosescabang').click(function(e) {
            e.preventDefault();
            var no_mutasi_gudang = $(this).attr("no_mutasi_gudang");
            $('#mdlapprove').modal({
                backdrop: 'static'
                , keyboard: false
            });
            loadapprove(no_mutasi_gudang);

        });

        function cektutuplaporan(tanggal) {
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                    , jenislaporan: "gudangcabang"
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#cektutuplaporan").val(respond);
                }
            });
        }

        $('.delete-confirm-batalkansj').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            var tanggal = $(this).attr("tanggal");
            cektutuplaporan(tanggal);
            event.preventDefault();
            swal({
                    title: `Apakah Kamu Yakin Akan Membatalkan Data Ini ?`
                    , text: "Jika Kamu Batalkan, Maka Data Ini Akan dikembalikan seperti semula."
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


    });

</script>
@endpush
