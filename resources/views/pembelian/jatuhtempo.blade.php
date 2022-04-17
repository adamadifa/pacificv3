@extends('layouts.midone')
@section('titlepage','Data Jatuh Tempo')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Jatuh Tempo</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/pembelian/jatuhtempo">Data Jatuh Tempo</a>
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
                <div class="card-body">
                    <form action="/jatuhtempo">
                        <div class="row">
                            <div class="col-4">
                                <x-inputtext field="nobukti_pembelian" value="{{ Request('nobukti_pembelian') }}" label="No. Bukti Pembelian" icon="feather icon-credit-card" />
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <div class="form-group">
                                    <select name="kode_dept" id="kode_dept" class="form-control">
                                        <option value="">Semua Departemen</option>
                                        @foreach ($departemen as $d)
                                        <option {{ Request('kode_dept') == $d->kode_dept ? 'selected' : '' }} value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <div class="form-group">
                                    <select name="kode_supplier" id="kode_supplier" class="form-control select2">
                                        <option value="">Semua Supplier</option>
                                        @foreach ($supplier as $d)
                                        <option {{ Request('kode_supplier') == $d->kode_supplier ? 'selected' : '' }} value="{{ $d->kode_supplier }}">{{ $d->nama_supplier }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <x-inputtext field="dari" value="{{ Request('dari') }}" label="Dari" icon="feather icon-calendar" datepicker />
                            </div>
                            <div class="col-4">
                                <x-inputtext field="sampai" value="{{ Request('sampai') }}" label="Sampai" icon="feather icon-calendar" datepicker />
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <button class="btn btn-primary"><i class="fa fa-search mr-1"></i> Cari</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>No. Bukti</th>
                                    <th>Tgl Pembellian</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Supplier</th>
                                    <th>DEPT</th>
                                    <th>PPN</th>
                                    <th>Total</th>
                                    <th>Bayar</th>
                                    <th>KB</th>
                                    <th>Ket</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pembelian as $d)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration + $pembelian->firstItem() - 1 }}</td>
                                    <td>{{ $d->nobukti_pembelian }}</td>
                                    <td>{{ date("d-m-Y",strtotime($d->tgl_pembelian)) }}</td>
                                    <td>{{ date("d-m-Y",strtotime($d->tgl_jatuhtempo)) }}</td>
                                    <td>{{ ucwords(strtolower($d->nama_supplier)) }}</td>
                                    <td>{{ $d->kode_dept }}</td>
                                    <td>
                                        @if (!empty($d->ppn))
                                        <i class="fa fa-check success"></i>
                                        @endif
                                    </td>
                                    <td class="text-right">{{ desimal($d->harga + $d->penyesuaian)  }}</td>
                                    <td class="text-right">{{ desimal($d->jmlbayar)  }}</td>
                                    <td>
                                        @if (!empty($d->kontrabon))
                                        <span class="badge bg-success">{{ $d->kontrabon }}</span>
                                        @else
                                        <i class="fa fa-history warning"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                        $totalharga = $d->harga + $d->penyesuaian;
                                        if($totalharga == $d->jmlbayar){
                                        echo "<span class='badge bg-success'>L</span>";
                                        }else{
                                        echo "<span class='badge bg-danger'>BL</span>";
                                        }
                                        @endphp

                                    </td>
                                    <td>
                                        <a class="ml-1 detailpembelian" href="#" nobukti_pembelian="{{ $d->nobukti_pembelian }}"><i class=" feather icon-file-text info"></i></a>
                                    </td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                        {{ $pembelian->links('vendor.pagination.vuexy') }}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Detail Salesman -->
<div class="modal fade text-left" id="mdldetailpembelian" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width:968px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail Pembelian</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaddetailpembelian"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        function loaddetailpembelian(nobukti_pembelian) {
            $.ajax({
                type: 'POST'
                , url: '/pembelian/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , nobukti_pembelian: nobukti_pembelian
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailpembelian").html(respond);
                }
            });
        }
        $('.detailpembelian').click(function(e) {
            var nobukti_pembelian = $(this).attr("nobukti_pembelian");
            //alert(nobukti_pembelian);
            e.preventDefault();
            loaddetailpembelian(nobukti_pembelian);
            $('#mdldetailpembelian').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });
    });

</script>
@endpush
