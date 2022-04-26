@extends('layouts.midone')
@section('titlepage','Data Barang Keluar')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Barang Keluar</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/pengeluaranproduksi">Data Barang Keluar</a>
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
        <div class="col-md-8 col-sm-8">
            <div class="card">
                <div class="card-header">
                    <a href="/pengeluaranproduksi/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                <div class="card-body">
                    <form action="/pengeluaranproduksi">

                        <div class="row">
                            <div class="col-12">
                                <x-inputtext field="nobukti_pengeluaran" label="No. Bukti" icon="feather icon-credit-card" value="{{ Request('nobukti_pengeluaran') }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <x-inputtext field="dari" label="Dari" icon="feather icon-calendar" datepicker value="{{ Request('dari') }}" />
                            </div>
                            <div class="col-6">
                                <x-inputtext field="sampai" label="Sampai" icon="feather icon-calendar" datepicker value="{{ Request('sampai') }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <button type="submit" name="submit" class="btn btn-primary btn-block"><i class="fa fa-search mr-1"></i>Cari</button>
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
                                    <th>Jenis Pengeluaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pengeluaranproduksi as $d)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration + $pengeluaranproduksi->firstItem() - 1 }}</td>
                                    <td>{{ $d->nobukti_pengeluaran }}</td>
                                    <td>{{ date("d-m-y",strtotime($d->tgl_pengeluaran)) }}</td>
                                    <td>{{ $d->kode_dept }}</td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">

                                            <a class="ml-1" href="/pengeluaranproduksi/{{\Crypt::encrypt($d->nobukti_pengeluaran)}}/edit"><i class="feather icon-edit success"></i></a>
                                            <a class="ml-1 detail" href="#" nobukti_pengeluaran="{{ Crypt::encrypt($d->nobukti_pengeluaran) }}"><i class=" feather icon-file-text info"></i></a>
                                            <form method="POST" class="deleteform" action="/pengeluaranproduksi/{{Crypt::encrypt($d->nobukti_pengeluaran)}}/delete">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" tanggal="{{ $d->tgl_pengeluaran }}" class="delete-confirm ml-1">
                                                    <i class="feather icon-trash danger"></i>
                                                </a>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                        {{ $pengeluaranproduksi->links('vendor.pagination.vuexy') }}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Detail Barang masuk -->
<div class="modal fade text-left" id="mdldetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail Pengeluaran Produksi</h4>
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
@endsection
@push('myscript')
<script>
    $(function() {
        function loaddetail(nobukti_pengeluaran) {
            $.ajax({
                type: 'POST'
                , url: '/pengeluaranproduksi/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , nobukti_pengeluaran: nobukti_pengeluaran
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetail").html(respond);
                }
            });
        }
        $('.detail').click(function(e) {
            var nobukti_pengeluaran = $(this).attr("nobukti_pengeluaran");
            //alert(nobukti_pemasukan);
            e.preventDefault();
            loaddetail(nobukti_pengeluaran);
            $('#mdldetail').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        function cektutuplaporan(tanggal) {
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                    , jenislaporan: "produksi"
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

    });

</script>
@endpush
