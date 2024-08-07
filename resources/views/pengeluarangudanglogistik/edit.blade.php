@extends('layouts.midone')
@section('titlepage','Edit Data Barang Keluar')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Edit Data Barang Keluar</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Edit Data Barang Keluar</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        @include('layouts.notification')
        <form action="/pengeluarangudanglogistik/{{ Crypt::encrypt($pengeluaran->nobukti_pengeluaran) }}/update" method="POST" id="frmBarangkeluargl">
            @csrf
            <input type="hidden" id="cekbarang">
            <input type="hidden" id="cektutuplaporan">
            <div class="row">
                <div class="col-lg-4 col-sm-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <input type="hidden" value="{{ Crypt::encrypt($pengeluaran->nobukti_pengeluaran) }}" id="no_bukti">
                                    <x-inputtext field="nobukti_pengeluaran" label="Auto" icon="feather icon-credit-card" value="{{ $pengeluaran->nobukti_pengeluaran }}" readonly />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext field="tgl_pengeluaran" value="{{ $pengeluaran->tgl_pengeluaran }}" label="Tanggal pengeluaran" icon="feather icon-calendar" datepicker />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <select name="kode_dept" id="kode_dept" class="form-control">
                                            <option value="">Departemen</option>
                                            @foreach ($departemen as $d)
                                            <option {{ $pengeluaran->kode_dept==$d->kode_dept ? 'selected' : '' }} value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                            @endforeach
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
                                <div class="col-lg-5 col-sm-12 col-md-12">
                                    <x-inputtext field="keterangan" label="Keterangan" icon="feather icon-file" />
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <x-inputtext field="qty" label="Qty" icon="feather icon-file" />
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <a href="#" id="tambahbarang" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-2">
                                    <div class="form-group">
                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                            <input type="checkbox" class="cabangcheck" name="cabangcheck" value="1">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">Cabang ?</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12" id="pilihcabang">
                                    <div class="form-group">
                                        <select name="kode_cabang" id="kode_cabang" class="form-control">
                                            <option value="">Cabang</option>
                                            @foreach ($cabang as $d)
                                            <option value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                            @endforeach
                                        </select>
                                    </div>
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
                                                <th>Cabang</th>
                                                <th>Qty</th>
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

<!-- Edit Barang  -->
<div class="modal fade text-left" id="mdledit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit Barang</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadedit"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        var tgl_pengeluaran = $("#tgl_pengeluaran").val();
        cektutuplaporan(tgl_pengeluaran);

        $('.cabangcheck').change(function() {
            if (this.checked) {
                $("#pilihcabang").show();
            } else {
                $("#pilihcabang").hide();
                $("#kode_cabang").val("").change();
            }
        });

        function hidecabang() {
            $("#pilihcabang").hide();
        }

        hidecabang();


        function cektutuplaporan(tanggal) {
            // alert(tanggal);
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                    , jenislaporan: "gudanglogistik"
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#cektutuplaporan").val(respond);
                }
            });
        }


        $("#tgl_pengeluaran").change(function() {
            var tgl_pengeluaran = $("#tgl_pengeluaran").val();
            //alert(tgl_pengeluaran);
            cektutuplaporan(tgl_pengeluaran);
        });

        function cekbarang() {
            var nobukti_pengeluaran = $("#nobukti_pengeluaran").val();
            $.ajax({
                type: 'POST'
                , url: '/pengeluarangudanglogistik/cekbarang'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , nobukti_pengeluaran: nobukti_pengeluaran
                }
                , cache: false
                , success: function(respond) {
                    $("#cekbarang").val(respond);
                }
            });
        }

        function loadBarang() {
            $("#loadpilihbarang").load("/pengeluarangudanglogistik/getbarang");
        }

        function loaddetail() {
            var nobukti_pengeluaran = $("#no_bukti").val();
            $("#loaddetailpengeluaran").load("/pengeluarangudanglogistik/" + nobukti_pengeluaran + "/showbarang");
            cekbarang();
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
            var nobukti_pengeluaran = $("#nobukti_pengeluaran").val();
            var kode_barang = $("#kode_barang").val();
            var keterangan = $("#keterangan").val();
            var qty = $("#qty").val();
            var kode_cabang = $("#kode_cabang").val();
            var cektutuplaporan = $("#cektutuplaporan").val();
            if (cektutuplaporan > 0) {
                swal("Oops", "Laporan Periode Ini Sudah Di Tutup !", "warning");
            } else if (kode_barang == "") {
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
            } else if ($('.cabangcheck').is(':checked') && kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cabang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_cabang").focus();
                });
            } else {

                $.ajax({
                    type: 'POST'
                    , url: '/pengeluarangudanglogistik/storebarang'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , nobukti_pengeluaran: nobukti_pengeluaran
                        , kode_barang: kode_barang
                        , keterangan: keterangan
                        , qty: qty
                        , kode_cabang: kode_cabang
                    }
                    , cache: false
                    , success: function(respond) {
                        if (respond == 2) {
                            swal("Oops", "Data Gagal Disimpan", "warning");
                        } else {
                            swal("Berhasil", "Data Berhasil Disimpan", "success");
                            $("#kode_barang").val("");
                            $("#nama_barang").val("");
                            $("#keterangan").val("");
                            $("#qty").val("");
                            $("#kode_cabang").val("").change();
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

        $("#frmBarangkeluargl").submit(function() {
            var tgl_pengeluaran = $("#tgl_pengeluaran").val();
            var kode_dept = $("#kode_dept").val();
            var cekbarang = $("#cekbarang").val();
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
                    , text: 'Sumber barang Harus Diisi Dulu !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_dept").focus();
                });
                return false;
            } else if (cekbarang == "" || cekbarang == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Data Masih Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_barang").focus();
                });
                return false;
            }
        });
    });

</script>
@endpush
