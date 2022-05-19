@extends('layouts.midone')
@section('titlepage','Data Pembelian')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Pembelian</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/maintenance/pembelian">Data Pembelian</a>
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
        <div class="col-md-8 col-sm-8">
            <div class="card">
                <div class="card-body">
                    <form action="/maintenance/pembelian">
                        <div class="row">
                            <div class="col-12">
                                <x-inputtext label="No. Bukti Pembelian" field="nobukti_pembelian" icon="feather icon-credit-card" value="{{ Request('nobukti_pembelian') }}" />
                            </div>
                        </div>
                        <div class=" row">
                            <div class="col-6">
                                <x-inputtext field="dari" value="{{ Request('dari') }}" label="Dari" icon="feather icon-calendar" datepicker />
                            </div>
                            <div class="col-6">
                                <x-inputtext field="sampai" value="{{ Request('sampai') }}" label="Sampai" icon="feather icon-calendar" datepicker />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <button class="btn btn-primary btn-block"><i class="fa fa-search mr-1"></i>Cari Data</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>No. Bukti</th>
                                    <th>Tanggal</th>
                                    <th>Supplier</th>
                                    <th>Dept</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pembelian as $d)
                                <tr>
                                    <td>{{ $loop->iteration + $pembelian->firstItem()-1 }}</td>
                                    <td>{{ $d->nobukti_pembelian }}</td>
                                    <td>{{ date("d-m-Y",strtotime($d->tgl_pembelian)) }}</td>
                                    <td>{{ $d->nama_supplier }}</td>
                                    <td>{{ $d->kode_dept }}</td>
                                    <td>
                                        <a href="#" class="success detail" nobukti_pembelian="{{ $d->nobukti_pembelian }}"><i class="feather icon-external-link"></i></a>
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
                , url: '/maintenance/showpembelian'
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

        $('.detail').click(function(e) {
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
