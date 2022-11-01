@extends('layouts.midone')
@section('titlepage','Input Service Kendaraan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Input Service Kendaraan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/servicekendaraan/create">Service Kendaraan</a>
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
            <form action="/servicekendaraaan/store" method="POST">
                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Input Service Kendaraan</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext label="No. Invoice" field="no_invoice" icon="fa fa-barcode" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext label="Tanggal Service" field="tgl_servie" icon="feather icon-calendar" datepicker />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <select name="no_polisi" id="no_polisi" class="form-control select2">
                                                <option value="">Pilih Kendaraan</option>
                                                @foreach ($kendaraan as $d)
                                                <option value="{{ $d->no_polisi }}">{{ $d->no_polisi }} {{ $d->merk }} {{ $d->tipe_kendaraan }} {{ $d->tipe }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <select name="no_polisi" id="no_polisi" class="form-control select2">
                                                <option value="">Pilih Bengkel</option>
                                                @foreach ($bengkel as $d)
                                                <option value="{{ $d->kode_bengkel }}">{{ $d->nama_bengkel }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Detail Service</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <select name="kode_item" id="kode_item" class="form-control select2">
                                            <option value="">Pilih Item</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-1 col-sm-12">
                                        <a href="#" class="btn btn-info" id="addnewitem"><i class="feather icon-plus"></i></a>
                                    </div>
                                    <div class="col-lg-2">
                                        <x-inputtext label="Qty" field="qty" icon="feather icon-file" right />
                                    </div>
                                    <div class="col-lg-3">
                                        <x-inputtext label="Harga" field="harga" icon="feather icon-file" right />
                                    </div>
                                    <div class="col-lg-2">
                                        <a href="#" class="btn btn-primary" id="simpantemp"><i class="fa fa-cart-plus"></i></a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Kode Item</th>
                                                    <th>Nama Item</th>
                                                    <th>Qty</th>
                                                    <th>Biaya</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<div class="modal fade text-left" id="mdlinputnewitem" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Tambah Item</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <x-inputtext label="Nama Item" field="nama_item" icon="feather icon-file" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <select name="jenis" id="jenis" class="form-control">
                                <option value="">Pilih Jenis</option>
                                <option value="JASA">JASA</option>
                                <option value="OLI">OLI</option>
                                <option value="PART">PART</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <a href="#" class="btn btn-primary btn-block" id="simpannewitem"><i class="feather icon-send mr-1"></i>Simpan</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('myscript')
<script>
    $(function() {
        function loaditem() {
            $.ajax({
                type: 'GET'
                , url: '/getitemservice'
                , cache: false
                , success: function(respond) {
                    $("#kode_item").html(respond);
                }
            });
        }

        loaditem();

        $('#addnewitem').click(function(e) {
            e.preventDefault();
            $('#mdlinputnewitem').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        $("#simpantemp").click(function(e) {
            e.preventDefault();
            var no_invoice = $("#no_invoice").val();
            var kode_item = $("#kode_item").val();
            var qty = $("#qty").val();
            var harga = $("#harga").val();
            if (no_invoice == "") {
                swal({
                    title: 'Oops'
                    , text: 'No. Invoice Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#no_invoice").focus();
                });
            } else if (kode_item == "") {
                swal({
                    title: 'Oops'
                    , text: 'Item Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_item").focus();
                });
            } else if (qty == "") {
                swal({
                    title: 'Oops'
                    , text: 'Qty Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#qty").focus();
                });
            } else if (harga == "") {
                swal({
                    title: 'Oops'
                    , text: 'Harga Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#harga").focus();
                });
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/storetemp'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , no_invoice: no_invoice
                        , kode_item: kode_item
                        , qty: qty
                        , harga: harga
                    }
                    , cache: false
                    , success: function(respond) {

                    }
                });
            }

        });

        $("#simpannewitem").click(function(e) {
            e.preventDefault();
            var nama_item = $("#nama_item").val();
            var jenis = $("#jenis").val();
            if (nama_item == "") {
                swal({
                    title: 'Oops'
                    , text: 'Nama Item Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_item").focus();
                });
            } else if (jenis == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jenis Item Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jenis").focus();
                });
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/storeitemservice'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , nama_item: nama_item
                        , jenis: jenis
                    }
                    , cache: false
                    , success: function(respond) {
                        console.log(respond);
                        loaditem();
                        $("#mdlinputnewitem").modal('hide');
                    }
                });
            }
        });
    });

</script>
@endpush
