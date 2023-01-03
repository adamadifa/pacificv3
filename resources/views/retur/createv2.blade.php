@extends('layouts.midone')
@section('titlepage','Input Retur')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Input Retur</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/retur/createv2">Input Retur</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <form name="autoSumForm" autocomplete="off" action="/retur/store" class="formValidate form-horizontal" id="formValidate" method="POST">
            @csrf
            <input type="hidden" id="cektutuplaporan">
            <input type="hidden" id="cektemp">
            <input type="hidden" id="sisapiutang" name="sisapiutang">
            <input type="hidden" id="limitpel" name="limitpel">
            <input type="hidden" id="bruto" name="bruto">
            <input type="hidden" id="subtotal" name="subtotal">
            <div class="row">
                <div class="col-lg-3 col-sm-12">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Data Retur</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="No. Retur" field="no_retur_penj" icon="fa fa-barcode" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Tanggal Retur" field="tglretur" icon="feather icon-calendar" readonly datepicker" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <input type="hidden" name="kode_pelanggan" id="kode_pelanggan" value="{{ $pelanggan != null ?  $pelanggan->kode_pelanggan : '' }}">
                                            <input type="hidden" id="kode_cabang" class="form-control" name="kode_cabang" value="{{ $pelanggan != null ?  $pelanggan->kode_cabang : ''  }}">
                                            <input type="hidden" id="jatuhtempo" class="form-control" name="jatuhtempo" value="{{ $pelanggan != null ?  $pelanggan->jatuhtempo : '' }}">
                                            <input type="hidden" id="limitpel" class="form-control" name="limitpel" value="{{ $pelanggan != null ?  $pelanggan->limitpel : '' }}">
                                            <x-inputtext label="Pelanggan" field="nama_pelanggan" icon="feather icon-user" value="{{$pelanggan != null ? $pelanggan->kode_pelanggan .'|'. $pelanggan->nama_pelanggan : ''}}" readonly />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <input type="hidden" value="{{ $pelanggan != null ? $pelanggan->id_sales : '' }}" name="id_karyawan" id="id_karyawan">
                                            <input type="hidden" value="{{ $pelanggan != null ? $pelanggan->kategori_salesman : '' }}" name="kategori_salesman" id="kategori_salesman">
                                            <x-inputtext label="Salesman" field="nama_karyawan" icon="feather icon-users" value="{{ $pelanggan != null ? $pelanggan->id_sales. '|'.$pelanggan->nama_karyawan.'|'.$pelanggan->kategori_salesman : ''}}" readonly />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-content">
                                    <img class="card-img-top img-fluid" id="foto" src="{{ asset('app-assets/images/slider/04.jpg') }}" alt="Card image cap">
                                    <div class="card-body">
                                        <h4 class="card-title">
                                            <span id="pelanggan_text"></span>
                                        </h4>
                                        <b>Alamat</b>
                                        <p class="card-text" id="alamat_text">{{ $pelanggan != null ? $pelanggan->alamat_pelanggan : '' }}</p>
                                        <b>No. HP</b>
                                        <p class="card-text" id="no_hp">{{ $pelanggan != null ? $pelanggan->no_hp : '' }}</p>
                                        <b>Koordinat</b>
                                        <p class="card-text" id="koordinat">{{ $pelanggan != null ? $pelanggan->latitude : '' }},{{ $pelanggan != null ? $pelanggan->longitude : '' }}</p>
                                        <b>Limit Pelanggan</b>
                                        <p class="card-text" id="limitpelanggan">{{ rupiah($pelanggan != null ? $pelanggan->limitpel : 0) }}</p>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-sm-12">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-start pb-0">
                                    <div class="avatar bg-rgba-info m-2" style="padding:3rem ">
                                        <div class="avatar-content">
                                            <i class="feather icon-shopping-cart text-info" style="font-size: 4rem"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h2 class="text-bold-700" style="font-size: 6rem; padding:2rem" id="grandtotal">0,00</h2>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group" style="margin-bottom:5px !important">
                                                <div class=" form-label-group position-relative has-icon-left" style="margin-bottom:5px !important">
                                                    <div class="controls">
                                                        <input type="hidden" name="kode_barang" id="kode_barang">
                                                        <input type="hidden" name="isipcsdus" id="isipcsdus">
                                                        <input type="hidden" name="isipcs" id="isipcs">
                                                        <input type="text" autocomplete="off" id="nama_barang" value="" readonly class="form-control" name="nama_barang" placeholder="Produk" style="height: 80px">
                                                        <div class="form-control-position" style="top:23px !important">
                                                            <i class="fa fa-barcode"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-sm-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group" style="margin-bottom:5px !important">
                                                        <div class=" form-label-group position-relative has-icon-left" style="margin-bottom:5px !important">
                                                            <div class="controls">
                                                                <input type="text" autocomplete="off" id="jml_dus" value="" class="form-control text-right" name="jml_dus" placeholder="Dus">
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-file"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group" style="margin-bottom:5px !important">
                                                        <div class=" form-label-group position-relative has-icon-left" style="margin-bottom:5px !important">
                                                            <div class="controls">
                                                                <input type="hidden" id="harga_dus_old" name="harga_dus_old">
                                                                <input type="text" autocomplete="off" id="harga_dus" value="" class="form-control text-right" name="harga_dus" placeholder="Harga / Dus">
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-tag"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!--- Pack-->
                                        <div class="col-lg-2 col-sm-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group" style="margin-bottom:5px !important">
                                                        <div class=" form-label-group position-relative has-icon-left" style="margin-bottom:5px !important">
                                                            <div class="controls">
                                                                <input type="text" autocomplete="off" id="jml_pack" class="form-control text-right" name="jml_pack" placeholder="Pack">
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-file"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group" style="margin-bottom:5px !important">
                                                        <div class=" form-label-group position-relative has-icon-left" style="margin-bottom:5px !important">
                                                            <div class="controls">
                                                                <input type="hidden" id="harga_pack_old" name="harga_pack_old">
                                                                <input type="text" autocomplete="off" id="harga_pack" class="form-control text-right" name="harga_pack" placeholder="Harga / Pack">
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-tag"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!--- Pcs-->
                                        <div class="col-lg-2 col-sm-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group" style="margin-bottom:5px !important">
                                                        <div class=" form-label-group position-relative has-icon-left" style="margin-bottom:5px !important">
                                                            <div class="controls">
                                                                <input type="text" autocomplete="off" id="jml_pcs" class="form-control text-right" name="jml_pcs" placeholder="Pcs">
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-file"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group" style="margin-bottom:5px !important">
                                                        <div class=" form-label-group position-relative has-icon-left" style="margin-bottom:5px !important">
                                                            <div class="controls">
                                                                <input type="hidden" id="harga_pcs_old" name="harga_pcs_old">
                                                                <input type="text" autocomplete="off" id="harga_pcs" class="form-control text-right" name="harga_pcs" placeholder="Harga / Pcs">
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-tag"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <a href="#" id="tambahitem" class="btn btn-info btn-block"><i class="feather icon-plus ml-1"></i> Tambah item</a>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <tbody id="loadbarangtemp"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1 d-flex justify-content-end">
                                        <div class="col-lg-3 col-sm-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group" style="margin-bottom: 5px">
                                                        <select class="form-control" name="jenis_retur" id="jenis_retur">
                                                            <option value="">Jenis Retur</option>
                                                            <option value="gb">Ganti Barang</option>
                                                            <option value="pf">Potong Faktur</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group" style="margin-bottom: 5px">
                                                        <select class="form-control select2" name="no_fak_penj" id="no_fak_penj">
                                                            <option value="">No. Faktur</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group" style="margin-bottom: 5px">
                                                        <button class="btn btn-block btn-primary" id="btnsimpan"><i class="feather icon-send mr-1"></i>Simpan</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
<!--- Modal Pilih Pelanggan -->
@if ($level!="salesman")
<div class="modal fade text-left" id="mdlpelanggan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document" style="max-width: 960px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Data Pelanggan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover-animation tabelpelanggan" style="width:100% !important" id="tabelpelanggan">
                        <thead class="thead-dark">
                            <tr>
                                <th>Kode Pelanggan</th>
                                <th>Nama Pelanggan</th>
                                <th>Pasar</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="modal fade text-left" id="mdlpelanggan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Data Pelanggan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover-animation tabelpelanggan" style="width:100% !important" id="tabelpelanggan" style="font-size: 11px !important">
                        <thead class="thead-dark">
                            <tr>
                                <th>Kode Pelanggan</th>
                                <th>Nama Pelanggan</th>
                                <th>Pasar</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal Pilih Barang -->
<div class="modal fade text-left" id="mdlbarang" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document" style="max-width: 960px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Data Barang</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="loadbarang">
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {

        var kode_pelanggan = $("#kode_pelanggan").val();

        function loadbarangtemp() {
            var kode_pelanggan = $("#kode_pelanggan").val();
            $.ajax({
                type: 'POST'
                , url: '/retur/showbarangtempv2'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_pelanggan: kode_pelanggan
                , }
                , cache: false
                , success: function(respond) {
                    $("#loadbarangtemp").html(respond);
                    loadtotal();
                }
            });
        }




        function loadfaktur(kode_pelanggan) {
            $.ajax({
                type: 'POST'
                , url: '/retur/getfakturpelanggan'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_pelanggan: kode_pelanggan
                }
                , success: function(respond) {
                    $("#no_fak_penj").html(respond);
                }
            });
        }


        function loadtotal() {
            var kode_pelanggan = $("#kode_pelanggan").val();
            $.ajax({
                type: 'POST'
                , url: '/loadtotalreturtemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_pelanggan: kode_pelanggan
                }
                , success: function(respond) {
                    var total = parseInt(respond.replace(/\./g, ''));
                    $("#grandtotal").text(convertToRupiah(total));
                    // $("#total").val(convertToRupiah(grandtotal));
                    // $("#bruto").val(bruto);
                    $("#subtotal").val(total);
                    cektemp();
                }
            });
        }

        function cektemp() {
            var kode_pelanggan = $("#kode_pelanggan").val();
            $.ajax({
                type: 'POST'
                , url: '/cekreturtemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_pelanggan: kode_pelanggan
                }
                , success: function(respond) {
                    $("#cektemp").val(respond);
                }
            });
        }


        loadbarangtemp();
        loadfaktur(kode_pelanggan);


        function convertToRupiah(number) {
            if (number) {
                var rupiah = "";
                var numberrev = number
                    .toString()
                    .split("")
                    .reverse()
                    .join("");
                for (var i = 0; i < numberrev.length; i++)
                    if (i % 3 == 0) rupiah += numberrev.substr(i, 3) + ".";
                return (
                    rupiah
                    .split("", rupiah.length - 1)
                    .reverse()
                    .join("")
                );
            } else {
                return number;
            }
        }


        function cektutuplaporan() {
            var tgltransaksi = $("#tglretur").val();
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tgltransaksi
                    , jenislaporan: "penjualan"
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#cektutuplaporan").val(respond);
                }
            });
        }




        $("#tglretur").change(function() {
            cektutuplaporan();
        });
        $('#no_retur_penj').mask('AAAAAAAAAAA', {
            'translation': {
                A: {
                    pattern: /[A-Za-z0-9]/
                }
            }
        });



        $("form").submit(function(e) {
            var no_retur_penj = $("#no_retur_penj").val();
            var no_fak_penj = $("#no_fak_penj").val();
            var tglretur = $("#tglretur").val();
            var kode_pelanggan = $("#kode_pelanggan").val();
            var cektutuplaporan = $("#cektutuplaporan").val();
            var nama_pelanggan = $("#nama_pelanggan").val();
            var cektemp = $("#cektemp").val();
            var jenis_retur = $("#jenis_retur").val();
            if (cektutuplaporan > 0) {
                swal("Peringatan", "Laporan Periode Ini Sudah Ditutup !", "warning");
                return false;
            } else if (cektemp == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Data Masih Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_barang").focus();
                });
                return false;
            } else if (tglretur == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tglretur").focus();
                });
                return false;
            } else if (kode_pelanggan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Pelanggan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_pelanggan").focus();
                });
                return false;
            } else if (jenis_retur == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jenis Retur Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jenis_retur").focus();
                });
                return false;
            } else if (no_fak_penj == "") {
                swal({
                    title: 'Oops'
                    , text: 'No Faktur Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#no_fak_penj").focus();
                });
                return false;
            } else {
                return true;
            }
        });

        $('#nama_pelanggan').click(function(e) {
            e.preventDefault();
            $('#mdlpelanggan').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        $('#nama_pelanggan').focus(function(e) {
            e.preventDefault();
            $('#mdlpelanggan').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        $('.tabelpelanggan').DataTable({
            processing: true
            , serverSide: true
            , ajax: '/pelanggan/json', // memanggil route yang menampilkan data json
            bAutoWidth: false

            , columns: [{ // mengambil & menampilkan kolom sesuai tabel database
                    data: 'kode_pelanggan'
                    , name: 'kode_pelanggan'
                }
                , {
                    data: 'nama_pelanggan'
                    , name: 'nama_pelanggan'
                }, {
                    data: 'pasar'
                    , name: 'pasar'
                }
                , {
                    data: 'action'
                    , name: 'action'
                    , orderable: false
                    , searchable: false
                }

            ]
        });


        $('.tabelpelanggan tbody').on('click', 'a', function() {
            var kode_pelanggan = $(this).attr("kode_pelanggan");
            var nama_pelanggan = $(this).attr("nama_pelanggan");
            var id_karyawan = $(this).attr("id_karyawan");
            var nama_karyawan = $(this).attr("nama_karyawan");
            var kategori_salesman = $(this).attr("kategori_salesman");
            var alamat_pelanggan = $(this).attr("alamat_pelanggan");
            var no_hp = $(this).attr("no_hp");
            var pasar = $(this).attr("pasar");
            var latitude = $(this).attr("latitude");
            var longitude = $(this).attr("longitude");
            var image = $(this).attr("foto")
            var kode_cabang = $(this).attr("kode_cabang")
            var limitpel = $(this).attr("limitpel");
            var limitpelanggan = $(this).attr("limitpelanggan");
            var jatuhtempo = $(this).attr("jatuhtempo");

            var foto = "{{ url(Storage::url('pelanggan/')) }}/" + image;
            var nofoto = "{{ asset('app-assets/images/slider/04.jpg') }}";
            $("#kode_pelanggan").val(kode_pelanggan);
            $("#nama_pelanggan").val(kode_pelanggan + " | " + nama_pelanggan);
            $("#id_karyawan").val(id_karyawan);
            $("#nama_karyawan").val(id_karyawan + " | " + nama_karyawan + " | " + kategori_salesman);
            $("#alamat_pelanggan").text(alamat_pelanggan);
            $("#no_hp").text(no_hp);

            $("#kode_cabang").val(kode_cabang);
            $("#kategori_salesman").val(kategori_salesman);
            $("#limitpel").val(limitpel);
            $("#jatuhtempo").val(jatuhtempo);
            $("#limitpelanggan").text(limitpelanggan);

            $("#koordinat").text(latitude + " - " + longitude);
            if (image != "") {
                $("#foto").attr("src", foto);
            } else {
                $("#foto").attr("src", nofoto);
            }
            loadbarangtemp();
            loadfaktur(kode_pelanggan);
            $("#mdlpelanggan").modal("hide");

        });



        //Pilih Pelanggan Saat Diklik
        $('#nama_barang').click(function(e) {
            e.preventDefault();
            var kode_pelanggan = $("#kode_pelanggan").val();
            var kategori_salesman = $("#kategori_salesman").val();
            var kode_cabang = $("#kode_cabang").val();
            if (kode_pelanggan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Pelanggan Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_pelanggan").focus();
                });
                return false;
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/getbarangcabangretur'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , kategori_salesman: kategori_salesman
                        , kode_cabang: kode_cabang
                        , kode_pelanggan: kode_pelanggan
                    }
                    , cache: false
                    , success: function(respond) {
                        $("#loadbarang").html(respond);
                        $('#mdlbarang').modal({
                            backdrop: 'static'
                            , keyboard: false
                        });
                    }
                });
            }

        });


        $('#nama_barang').focus(function(e) {
            e.preventDefault();
            var kode_pelanggan = $("#kode_pelanggan").val();
            var kategori_salesman = $("#kategori_salesman").val();
            var kode_cabang = $("#kode_cabang").val();
            if (kode_pelanggan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Pelanggan Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_pelanggan").focus();
                });
                return false;
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/getbarangcabangretur'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , kategori_salesman: kategori_salesman
                        , kode_cabang: kode_cabang
                        , kode_pelanggan: kode_pelanggan
                    }
                    , cache: false
                    , success: function(respond) {
                        $("#loadbarang").html(respond);
                        $('#mdlbarang').modal({
                            backdrop: 'static'
                            , keyboard: false
                        });
                    }
                });
            }

        });




        //Tambah Item
        $("#tambahitem").click(function(e) {
            e.preventDefault();
            var kode_barang = $("#kode_barang").val();
            var jml_dus = $("#jml_dus").val();
            var jml_pack = $("#jml_pack").val();
            var jml_pcs = $("#jml_pcs").val();
            var harga_dus = $("#harga_dus").val();
            var harga_pack = $("#harga_pack").val();
            var harga_pcs = $("#harga_pcs").val();
            var isipcsdus = $("#isipcsdus").val();
            var isipcs = $("#isipcs").val();
            var kode_pelanggan = $("#kode_pelanggan").val();

            var jmldus = jml_dus != "" ? parseInt(jml_dus.replace(/\./g, '')) : 0;
            var jmlpack = jml_pack != "" ? parseInt(jml_pack.replace(/\./g, '')) : 0;
            var jmlpcs = jml_pcs != "" ? parseInt(jml_pcs.replace(/\./g, '')) : 0;

            var hargadus = harga_dus != "" ? parseInt(harga_dus.replace(/\./g, '')) : 0;
            var hargapack = harga_pack != "" ? parseInt(harga_pack.replace(/\./g, '')) : 0;
            var hargapcs = harga_pcs != "" ? parseInt(harga_pcs.replace(/\./g, '')) : 0;



            var jumlah = (jmldus * parseInt(isipcsdus)) + (jmlpack * (parseInt(isipcs))) + jmlpcs;
            var subtotal = (jmldus * hargadus) + (jmlpack * hargapack) + (jmlpcs * hargapcs);
            //alert(totalpcs);

            if (kode_barang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Barang Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_barang").focus();
                });
                return false;
            } else if (jumlah == "" && !nama_pelanggan.includes('BATAL')) {
                swal({
                    title: 'Oops'
                    , text: 'Qty Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jml_dus").focus();
                });
                return false;
            } else {
                //Simpan Barang Temp
                $.ajax({
                    type: 'POST'
                    , url: '/retur/storebarangtempv2'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , kode_barang: kode_barang
                        , hargadus: hargadus
                        , hargapack: hargapack
                        , hargapcs: hargapcs
                        , jumlah: jumlah
                        , subtotal: subtotal
                        , kode_pelanggan: kode_pelanggan
                    }
                    , cache: false
                    , success: function(respond) {
                        if (respond == 0) {
                            swal({
                                title: 'Success'
                                , text: 'Item Berhasil Disimpan !'
                                , icon: 'success'
                                , showConfirmButton: false
                            }).then(function() {
                                loadbarangtemp();
                                $("#kode_barang").val("");
                                $("#nama_barang").val("");
                                $("#jml_dus").val("");
                                $("#jml_pack").val("");
                                $("#jml_pcs").val("");

                                $("#harga_dus").val("");
                                $("#harga_pack").val("");
                                $("#harga_pcs").val("");

                                $("#harga_dus_old").val("");
                                $("#harga_pack_old").val("");
                                $("#harga_pcs_old").val("");

                                //$("#jml_dus").focus();

                            });


                        } else if (respond == 1) {
                            swal({
                                title: 'Oops'
                                , text: 'Item Sudah Ada !'
                                , icon: 'warning'
                                , showConfirmButton: false
                            }).then(function() {
                                $("#kode_barang").val("");
                                $("#nama_barang").val("");
                                $("#jml_dus").val("");
                                $("#jml_pack").val("");
                                $("#jml_pcs").val("");

                                $("#harga_dus").val("");
                                $("#harga_pack").val("");
                                $("#harga_pcs").val("");

                                $("#harga_dus_old").val("");
                                $("#harga_pack_old").val("");
                                $("#harga_pcs_old").val("");

                                $("#nama_barang").focus();

                            });
                        } else {
                            swal({
                                title: 'Oops'
                                , text: respond
                                , icon: 'warning'
                                , showConfirmButton: false
                            }).then(function() {

                                $("#jml_dus").focus();

                            });
                        }
                    }
                });
            }
        });

        //Set Format Uang
        $("#harga_dus, #harga_pack, #harga_pcs, #jml_dus, #jml_pack, #jml_pcs").maskMoney();
        $(".money").maskMoney();


    });

</script>
@endpush
