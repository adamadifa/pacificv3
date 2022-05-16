@extends('layouts.midone')
@section('titlepage','Data Kontrabon')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Kontrabon</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/kontrabon">Data Kontrabon</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <!-- Data list view starts -->
        <!-- DataTable starts -->
        @include('layouts.notification')
        <div class="col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <a href="/kontrabon/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                <div class="card-body">
                    <form action="/kontrabon">
                        <div class="row">
                            <div class="col-6">
                                <x-inputtext label="No. Kontrabon" value="{{ Request('no_kontrabon') }}" field="no_kontrabon" icon="feather icon-credit-card" />
                            </div>
                            <div class="col-6">
                                <x-inputtext label="No. Dokumen" value="{{ Request('no_dokumen') }}" field="no_dokumen" icon="feather icon-file" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <x-inputtext field="dari" value="{{ Request('dari') }}" label="Dari" icon="feather icon-calendar" datepicker />
                            </div>
                            <div class="col-6">
                                <x-inputtext field="sampai" value="{{ Request('sampai') }}" label="Sampai" icon="feather icon-calendar" datepicker />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-sm-12">
                                <div class="form-group">
                                    <select name="kode_supplier" id="kode_supplier" class="form-control select2">
                                        <option value="">Semua Supplier</option>
                                        @foreach ($supplier as $d)
                                        <option {{ Request('kode_supplier') == $d->kode_supplier ? 'selected' : '' }} value="{{ $d->kode_supplier }}">{{ $d->nama_supplier }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <div class="form-group">
                                    <select class="form-control" id="status" name="status" data-error=".errorTxt1">
                                        <option value="">Semua Status</option>
                                        <option {{ Request('status') == 1 ? 'selected' : '' }} value="1">Belum Di Proses</option>
                                        <option {{ Request('status') == 2 ? 'selected' : '' }} value="2">Sudah Di Proses</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <div class="form-group">
                                    <select class="form-control" id="kategori" name="kategori" data-error=".errorTxt1">
                                        <option value="">Semua Jenis Pengajuan</option>
                                        <option {{ Request('kategori') == 'KB' ? 'selected' : '' }} value="KB">Kontra BON</option>
                                        <option {{ Request('kategori') == 'IM' ? 'selected' : '' }} value="IM">Internal Memo</option>
                                        <option {{ Request('kategori') == 'TN' ? 'selected' : '' }} value="TN">Tunai</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search mr-1"></i>Cari</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="10px">No</th>
                                    <th>No Kontra BON</th>
                                    <th>No Dok</th>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Supplier</th>
                                    <th>Total Bayar</th>
                                    <th>Keterangan</th>
                                    <th>Jenis Bayar</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kontrabon as $d)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration + $kontrabon->firstItem() - 1 }}</td>
                                    <td>{{ strtoupper($d->no_kontrabon) }}</td>
                                    <td>{{ strtoupper($d->no_dokumen) }}</td>
                                    <td>{{ date("d-m-Y",strtotime($d->tgl_kontrabon)) }}</td>
                                    <td>{{ $d->kategori }}</td>
                                    <td>{{ ucwords(strtolower($d->nama_supplier)) }}</td>
                                    <td class="text-right">{{ desimal($d->totalbayar) }}</td>
                                    <td>
                                        @if (empty($d->tglbayar))
                                        <span class="badge bg-danger">Belum Bayar</span>
                                        @else
                                        <span class="badge bg-success">{{ date("d-m-Y",strtotime($d->tglbayar)) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ ucwords($d->jenisbayar) }} {{ !empty($d->via) ? '('.$d->via.')' : '' }}</td>
                                    <td>
                                        @if ($d->status==1 OR !empty($d->tglbayar))
                                        @if (!empty($d->tglbayar))
                                        <span class="badge bg-info">DONE</span>
                                        @else
                                        <span class="badge bg-success">Approved</span>
                                        @endif
                                        @else
                                        @if ($d->kategori != "TN")
                                        <i class="fa fa-history warning"></i>
                                        @else
                                        <span class="badge bg-success">Tunai</span>
                                        @endif
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a class="ml-1 detailkontrabon" href="#" no_kontrabon="{{ Crypt::encrypt($d->no_kontrabon) }}"><i class=" feather icon-file-text info"></i></a>



                                            {{-- Kontrabon Edit & Hapus --}}
                                            @if (in_array($level,$kontrabon_edit_hapus))
                                            @if (empty($d->tglbayar) && $d->kategori != "TN")
                                            <a class="ml-1" href="/kontrabon/{{\Crypt::encrypt($d->no_kontrabon)}}/edit"><i class="feather icon-edit success"></i></a>
                                            <form method="POST" class="deleteform" action="/kontrabon/{{Crypt::encrypt($d->no_kontrabon)}}/delete">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" class="delete-confirm ml-1">
                                                    <i class="feather icon-trash danger"></i>
                                                </a>
                                            </form>
                                            @endif
                                            @endif

                                            {{-- Approve Kontrabon --}}
                                            @if (in_array($level,$kontrabon_approve))
                                            @if (empty($d->tglbayar))
                                            @if ($d->status ==1 )
                                            <a href="/kontrabon/{{ Crypt::encrypt($d->no_kontrabon) }}/cancelkontrabon" class="danger ml-1"><i class="fa fa-close"></i></a>
                                            @else
                                            @if ($d->kategori != "TN")
                                            <a href="/kontrabon/{{ Crypt::encrypt($d->no_kontrabon) }}/approvekontrabon" class="success ml-1"><i class="fa fa-check"></i></a>
                                            @endif
                                            @endif
                                            @endif
                                            @endif


                                            {{-- Kontrabon Proses --}}
                                            @if (in_array($level,$kontrabon_proses))
                                            @if (empty($d->tglbayar))
                                            @if ($d->status==1)
                                            @if($d->kategori != 'TN')
                                            <a class="ml-1 proseskontrabon" href="#" no_kontrabon="{{ Crypt::encrypt($d->no_kontrabon) }}"><i class=" feather icon-external-link success"></i></a>
                                            @else
                                            <a class="ml-1 proseskontrabon" href="#" no_kontrabon="{{ Crypt::encrypt($d->no_kontrabon) }}"><i class=" feather icon-external-link success"></i></a>
                                            @endif
                                            @else
                                            @if ($d->kategori != 'TN')
                                            <span class="badge bg-warning ml-1"><i class="fa fa-history mr-1"></i>Waiting Approval</span>
                                            @else
                                            <a class="ml-1 proseskontrabon" href="#" no_kontrabon="{{ Crypt::encrypt($d->no_kontrabon) }}"><i class=" feather icon-external-link success"></i></a>
                                            @endif
                                            @endif
                                            @else
                                            <form method="POST" class="cancelkontrabon" action="/kontrabon/{{Crypt::encrypt($d->no_kontrabon)}}/batalkankontrabon">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" class="cancelkontrabon-confirm ml-1 danger">
                                                    <i class="fa fa-close danger"></i>
                                                </a>
                                            </form>
                                            @endif
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $kontrabon->links('vendor.pagination.vuexy') }}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Detail Kontrabon -->
<div class="modal fade text-left" id="mdldetailkontrabon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width:968px" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail Kontrabon</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaddetailkontrabon"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdlproseskontrabon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width:968px" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Proses Kontrabon</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadproseskontrabon"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        function loaddetailkontrabon(no_kontrabon) {
            $.ajax({
                type: 'POST'
                , url: '/kontrabon/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_kontrabon: no_kontrabon
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailkontrabon").html(respond);
                }
            });
        }

        function loadproseskontrabon(no_kontrabon) {
            $.ajax({
                type: 'POST'
                , url: '/kontrabon/proseskontrabon'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_kontrabon: no_kontrabon
                }
                , cache: false
                , success: function(respond) {
                    $("#loadproseskontrabon").html(respond);
                }
            });
        }
        $('.detailkontrabon').click(function(e) {
            var no_kontrabon = $(this).attr("no_kontrabon");
            e.preventDefault();
            loaddetailkontrabon(no_kontrabon);
            $('#mdldetailkontrabon').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        $('.proseskontrabon').click(function(e) {
            var no_kontrabon = $(this).attr("no_kontrabon");
            e.preventDefault();
            loadproseskontrabon(no_kontrabon);
            $('#mdlproseskontrabon').modal({
                backdrop: 'static'
                , keyboard: false
            });
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

        $('.cancelkontrabon-confirm').click(function(event) {
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
