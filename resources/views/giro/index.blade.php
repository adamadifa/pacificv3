@extends('layouts.midone')
@section('titlepage','Data Giro')
@section('content')

<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Giro</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/giro">Data Giro</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <div class="col-md-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form action="/giro">
                        <div class="row">
                            <div class="col-lg-6">
                                <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker value="{{ Request('dari') }}" />
                            </div>
                            <div class="col-lg-6">
                                <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker value="{{ Request('sampai') }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-sm-12">
                                <x-inputtext label="No Giro" field="no_giro" icon="feather icon-credit-card" value="{{ Request('no_giro') }}" />
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <x-inputtext label="Nama Pelanggan" field="nama_pelanggan" icon="feather icon-user" value="{{ Request('nama_pelanggan') }}" />
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <div class="form-group">
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Status Giro</option>
                                        <option {{ (Request('status')=='0' ? 'selected':'')}} value="0">Pending</option>
                                        <option {{ (Request('status')=='1' ? 'selected':'')}} value="1">Diterima</option>
                                        <option {{ (Request('status')=='2' ? 'selected':'')}} value="2">Ditolak</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-12">
                                <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i> Cari Data </button>
                            </div>
                        </div>
                    </form>
                    @include('layouts.notification')
                    <table class="table table-hover-animation">
                        <thead class="thead-dark">
                            <tr>
                                <th>No Giro</th>
                                <th>Tgl Pencatatan</th>
                                <th>Pelanggan</th>
                                <th>Bank</th>
                                <th>Jumlah</th>
                                <th>Jatuh Tempo</th>
                                <th>Cabang</th>
                                <th>Status</th>
                                <th>Ledger</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($giro as $d)
                            <tr>
                                <td>{{ $d->no_giro }}</td>
                                <td>{{ date("d-m-Y",strtotime($d->tgl_giro)) }}</td>
                                <td>{{ ucwords(strtolower($d->nama_pelanggan)) }}</td>
                                <td>{{ strtoupper($d->namabank) }}</td>
                                <td class="text-right" style="font-weight: bold">{{ rupiah($d->jumlah) }}</td>
                                <td>{{ date("d-m-Y",strtotime($d->tglcair)) }}</td>
                                <td>{{ $d->kode_cabang }}</td>
                                <td>
                                    @if ($d->status==0)
                                    <span class="badge bg-warning"><i class="fa fa-history"></i> Pending</span>
                                    @elseif($d->status==1)
                                    <span class="badge bg-success">{{ date("d-m-Y",strtotime($d->tglbayar)) }}</span>
                                    @elseif($d->status==2)
                                    <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $d->no_bukti }}</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <a class="ml-1 prosesgiro" href="#" no_giro="{{ $d->no_giro }}"><i class=" feather icon-external-link success"></i></a>
                                        <a class="ml-1 detailfaktur" href="#" no_giro="{{ $d->no_giro }}"><i class=" feather icon-file-text info"></i></a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $giro->links('vendor.pagination.vuexy') }}

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Detail Giro -->
<div class="modal fade text-left" id="mdldetailfaktur" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail Giro <span id="nogiro"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaddetailfaktur"></div>
            </div>
        </div>
    </div>
</div>

<!-- Proses Giro -->
<div class="modal fade text-left" id="mdlprosesgiro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Proses Giro <span id="nogiroproses"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadprosesgiro"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {

        function loaddetailfaktur(no_giro) {
            $.ajax({
                type: 'POST'
                , url: '/giro/detailfaktur'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_giro: no_giro
                }
                , cache: false
                , success: function(respond) {
                    $("#nogiro").text(no_giro);
                    $("#loaddetailfaktur").html(respond);
                }
            });
        }

        function loadprosesgiro(no_giro) {
            $.ajax({
                type: 'POST'
                , url: '/giro/prosesgiro'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_giro: no_giro
                }
                , cache: false
                , success: function(respond) {
                    $("#nogiroproses").text(no_giro);
                    $("#loadprosesgiro").html(respond);
                }
            });
        }


        $('.detailfaktur').click(function(e) {
            var no_giro = $(this).attr("no_giro");
            e.preventDefault();
            loaddetailfaktur(no_giro);
            $('#mdldetailfaktur').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });


        $('.prosesgiro').click(function(e) {
            var no_giro = $(this).attr("no_giro");
            e.preventDefault();
            loadprosesgiro(no_giro);
            $('#mdlprosesgiro').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });
    });

</script>
@endpush
