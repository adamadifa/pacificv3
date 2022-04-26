@extends('layouts.midone')
@section('titlepage','Input Data Barang Keluar')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Input Data Barang Keluar</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/pengeluaranproduksi/create">Input Data Barang Keluar</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        @include('layouts.notification')
        <form action="/pengeluaranproduksi/store" method="POST" id="frmBarangkeluarproduksi">
            @csrf
            <input type="hidden" id="cektemp">
            <input type="hidden" id="cektutuplaporan">
            <div class="row">
                <div class="col-lg-4 col-sm-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext field="nobukti_pengeluaran" label="Auto" icon="feather icon-credit-card" readonly />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext field="tgl_pengeluaran" label="Tanggal pengeluaran" icon="feather icon-calendar" datepicker />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <select name="kode_dept" id="kode_dept" class="form-control">
                                            <option value="">Jenis Pengeluaran</option>
                                            <option value="Pemakaian">Pemakaian</option>
                                            <option value="Retur Out">Retur Out</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <select name="kode_supplier" id="kode_supplier" class="form-control">
                                            <option value="">Supplier</option>
                                            <option value="SP0186">SURYA BUANA CV</option>
                                            <option value="SP0142">PT PURINUSA EKA PERSADA</option>
                                            <option value="SP0185">SAKU MAS JAYA, PT</option>
                                            <option value="SP0140">PT MULIAPACK GRAVURINDO</option>
                                            <option value="SP0032">PT EKADHARMA INTERNATIONAL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 col-sm-12 col-md-12">
                                    <input type="hidden" name="kode_barang" id="kode_barang">
                                    <x-inputtext field="nama_barang" label="Nama Barang" icon="feather icon-box" readonly />
                                </div>
                                <div class="col-lg-3 col-sm-12 col-md-12">
                                    <x-inputtext field="keterangan" label="Keterangan" icon="feather icon-file" />
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <x-inputtext field="qty" label="Qty" icon="feather icon-file" />
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <x-inputtext field="berat" label="Berat" icon="fa fa-balance-scale" />
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <a href="#" id="tambahbarang" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-hover-animation">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No.</th>
                                                <th>Kode barang</th>
                                                <th>Nama Barang</th>
                                                <th>Keterangan</th>
                                                <th>Qty</th>
                                                <th>Berat</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="loaddetailpengeluaran"></tbody>
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
                            <div class="row mt-5" id="tombolsimpan">
                                <div class="col-12">
                                    <div class="form-group">
                                        <button class="btn btn-primary btn-block"><i class="feather icon-send mr-1"></i> Submit</button>
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


<!-- Pilih Barang  -->
<div class="modal fade text-left" id="mdlpilihbarang" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Pilih Barang</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadpilihbarang"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {

        function cektemp() {
            $.ajax({
                type: 'POST'
                , url: '/pengeluaranproduksi/cektemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                }
                , cache: false
                , success: function(respond) {
                    $("#cektemp").val(respond);
                }
            });
        }

        function loadsupplier() {
            var kode_dept = $("#kode_dept").val();
            if (kode_dept == "Retur Out") {
                $("#kode_supplier").show();
            } else {
                $("#kode_supplier").hide();
            }
        }

        loadsupplier();
        $("#kode_dept").change(function() {
            loadsupplier();
        });

        function loadBarang() {
            $("#loadpilihbarang").load("/pengeluaranproduksi/getbarang");
        }

        function loaddetail() {
            $("#loaddetailpengeluaran").load("/pengeluaranproduksi/showtemp");
            cektemp();
        }

        loaddetail();
        $("#nama_barang").click(function(e) {
            loadBarang();
            $('#mdlpilihbarang').modal({
                backdrop: 'static'
                , keyboard: false
            });
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

        $("#tambahbarang").click(function(e) {
            e.preventDefault();
            var kode_barang = $("#kode_barang").val();
            var keterangan = $("#keterangan").val();
            var qty = $("#qty").val();
            var berat = $("#berat").val();
            if (kode_barang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Barang Harus Diisi Dulu !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_barang").focus();
                });
            } else if (qty == "" || qty == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Qty Harus Diisi Dulu !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#qty").focus();
                });
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/pengeluaranproduksi/storetemp'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , kode_barang: kode_barang
                        , keterangan: keterangan
                        , qty: qty
                        , berat: berat
                    }
                    , cache: false
                    , success: function(respond) {
                        if (respond == 1) {
                            swal("Oops", "Data Sudah Ada", "warning");
                        } else if (respond == 2) {
                            swal("Oops", "Data Gagal Disimpan", "warning");
                        } else {
                            swal("Berhasil", "Data Berhasil Disimpan", "success");
                            $("#kode_barang").val("");
                            $("#nama_barang").val("");
                            $("#keterangan").val("");
                            $("#qty").val("");
                            $("#nama_barang").focus();
                        }
                        loaddetail();

                    }
                });
            }
        });

        $("#tgl_pengeluaran").change(function() {
            var tgl_pengeluaran = $(this).val();
            cektutuplaporan(tgl_pengeluaran);
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
        $("#frmBarangkeluarproduksi").submit(function() {
            var tgl_pengeluaran = $("#tgl_pengeluaran").val();
            var kode_dept = $("#kode_dept").val();
            var cektemp = $("#cektemp").val();
            var cektutuplaporan = $("#cektutuplaporan").val();
            if (cektutuplaporan > 0) {
                swal({
                    title: 'Oops'
                    , text: 'Laporan Periode Ini Sudah Ditutup !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_pengeluaran").focus();
                });
                return false;
            } else if (tgl_pengeluaran == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi Dulu !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_pengeluaran").focus();
                });
                return false;
            } else if (kode_dept == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jenis Pengeluaran Harus Diisi Dulu !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_dept").focus();
                });
                return false;
            } else if (cektemp == "" || cektemp == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Data Masih Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_dept").focus();
                });
                return false;
            }
        });
    });

</script>
@endpush
