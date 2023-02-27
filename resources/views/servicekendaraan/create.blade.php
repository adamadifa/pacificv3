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
            <form action="/servicekendaraan/store" method="POST" id="frmService">
                @csrf
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
                                        <x-inputtext label="Tanggal Service" field="tgl_service" icon="feather icon-calendar" datepicker />
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
                                    <div class="col-9">
                                        <div class="form-group">
                                            <select name="kode_bengkel" id="kode_bengkel" class="form-control select2">
                                                <option value="">Pilih Bengkel</option>
                                                @foreach ($bengkel as $d)
                                                <option value="{{ $d->kode_bengkel }}">{{ $d->nama_bengkel }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <a href="#" class="btn btn-info mr-2" id="addnewbengkel"><i class="feather icon-plus"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <textarea name="keterangan" id="keterangan" cols="10" rows="5" placeholder="Keterangan" class="form-control"></textarea>
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
                                <div class="row mb-3">
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
                                                    <th>#</th>
                                                </tr>
                                            </thead>
                                            <tr id="loading">
                                                <td colspan="7" style="text-align:center">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tbody id="loadtemp">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-12">
                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                            <input type="checkbox" class="aggrement" name="aggrement" value="aggrement">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">Yakin Akan Disimpan ?</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="tombolsimpan">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <button class="btn btn-block btn-primary"><i class="feather icon-send mr-1"></i> Simpan</button>
                                        </div>
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


<div class="modal fade text-left" id="mdlinputnewbengkel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Tambah Bengkel</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <x-inputtext label="Nama Bengkel" field="nama_bengkel" icon="feather icon-file" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <a href="#" class="btn btn-primary btn-block" id="simpannewbengkel"><i class="feather icon-send mr-1"></i>Simpan</a>
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


        $("#frmService").submit(function() {
            var no_invoice = $("#no_invoice").val();
            var tgl_service = $("#tgl_service").val();
            var no_polisi = $("#no_polisi").val();
            var kode_bengkel = $("#kode_bengkel").val();
            if (no_invoice == "") {
                swal({
                    title: 'Oops'
                    , text: 'No. Invoice Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#no_invoice").focus();
                });

                return false;
            } else if (tgl_service == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Service Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_service").focus();
                });
                return false;
            } else if (no_polisi == "") {
                swal({
                    title: 'Oops'
                    , text: 'No. Polisi Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#no_polisi").focus();
                });

                return false;
            } else if (kode_bengkel == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bengkel Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_bengkel").focus();
                });

                return false;
            }
        });

        $('.aggrement').change(function() {
            if (this.checked) {
                $("#tombolsimpan").show();
            } else {
                $("#tombolsimpan").hide();
            }
        });

        function hidetombolsimpan() {
            $("#tombolsimpan").hide();
        }

        hidetombolsimpan();
        $("#harga").maskMoney();

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

        function loadbengkel() {
            $.ajax({
                type: 'GET'
                , url: '/getbengkel'
                , cache: false
                , success: function(respond) {
                    $("#kode_bengkel").html(respond);
                }
            });
        }

        loadbengkel();
        loaditem();

        $('#addnewitem').click(function(e) {
            e.preventDefault();
            $('#mdlinputnewitem').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        $('#addnewbengkel').click(function(e) {
            e.preventDefault();
            $('#mdlinputnewbengkel').modal({
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
                    , url: '/servicekendaraan/storetemp'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , no_invoice: no_invoice
                        , kode_item: kode_item
                        , qty: qty
                        , harga: harga
                    }
                    , cache: false
                    , success: function(respond) {
                        if (respond == 3) {
                            swal({
                                title: 'Oops'
                                , text: 'Data Item Sudah Ada !'
                                , icon: 'warning'
                                , showConfirmButton: false
                            }).then(function() {
                                $("#kode_item").focus();
                            });
                        }
                        $("#qty").val("");
                        $("#harga").val("");
                        loadtemp();
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

        $("#simpannewbengkel").click(function(e) {
            e.preventDefault();
            var nama_bengkel = $("#nama_bengkel").val();
            if (nama_bengkel == "") {
                swal({
                    title: 'Oops'
                    , text: 'Nama Bengkel Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_bengkel").focus();
                });
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/storenewbengkel'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , nama_bengkel: nama_bengkel
                    }
                    , cache: false
                    , success: function(respond) {
                        console.log(respond);
                        loadbengkel();
                        $("#mdlinputnewbengkel").modal('hide');
                    }
                });
            }
        });

        function loadtemp() {
            var no_invoice = $("#no_invoice").val();
            $("#loading").show();
            $.ajax({
                type: 'POST'
                , url: '/servicekendaraan/showtemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_invoice: no_invoice
                }
                , cache: false
                , success: function(respond) {
                    $("#loadtemp").html(respond);
                    $("#loading").hide();
                }
            });
        }

        loadtemp();

        $("#no_invoice").keyup(function() {
            loadtemp();
        });

        // $('#no_invoice').mask('AAAAAAAAAAAAAAAAAAAAAAAAAAA', {
        //     'translation': {
        //         A: {
        //             pattern: /[A-Za-z0-9]/
        //         }
        //     }
        // });

        $("input[type=text]").keyup(function() {
            $(this).val($(this).val().toUpperCase());
        });
    });

</script>
@endpush
