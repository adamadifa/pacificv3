@extends('layouts.midone')
@section('titlepage', 'Input Kontrabon')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Input Kontrabon</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/kontrabon/create">Input Kontrabon</a>
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
            <form action="/kontrabon/store" method="POST" id="frmKontrabon">

                @csrf
                <input type="hidden" id="cektutuplaporan">
                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">

                                            <div class="col-12">
                                                <x-inputtext field="no_kontrabon" label="No. Kontrabon / Internal Memo" icon="feather icon-credit-card"
                                                    readonly />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <x-inputtext field="tgl_kontrabon" label="Tanggal" icon="feather icon-calendar" datepicker />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <select name="kategori" id="kategori" class="form-control">
                                                        <option value="">Jenis Pengajuan</option>
                                                        <option value="KB">KB</option>
                                                        <option value="IM">IM</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <input type="hidden" id="kode_supplier" name="kode_supplier">
                                                <x-inputtext field="nama_supplier" label="Supplier" icon="feather icon-user" readonly />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <x-inputtext field="no_dokumen" label="No. Dokumen" icon="feather icon-credit-card" />
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <select name="jenisbayar" id="jenisbayar" class="form-control">
                                                        <option value="">Jenis Bayar</option>
                                                        <option value="tunai">Tunai</option>
                                                        <option value="transfer">Transfer</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- DataTable ends -->
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="card">
                            <div class="card-header d-flex align-items-start pb-0">
                                <div class="avatar bg-rgba-danger m-2" style="padding:3rem ">
                                    <div class="avatar-content">
                                        <i class="feather icon-shopping-cart text-danger" style="font-size: 4rem"></i>
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
                            <div class="card-header">
                                <h4>Detail Pembelian</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-3 col-sm-12">
                                        <x-inputtext field="nobukti_pembelian" label="No. Bukti Pembelian" icon="feather icon-credit-card" readonly />
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <input type="hidden" id="totalbayar" name="totalbayar">
                                        <x-inputtext field="totalpembelian" label="Total Pembelian" icon="feather icon-file" right readonly />
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <x-inputtext field="jmlbayar" label="Jumlah Bayar" icon="feather icon-file" right />
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <x-inputtext field="keterangan" label="Keterangan" icon="feather icon-box" right />
                                    </div>
                                    <div class="col-lg-1 col-sm-12">
                                        <div class="form-group">
                                            <a href="#" class="btn btn-primary" id="tambahdetail"><i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-bordered">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>No</th>
                                                    <th>No. Bukti</th>
                                                    <th>Jumlah Bayar</th>
                                                    <th>Keterangan</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="loaddetailkontrabon"></tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-12">
                                        <button class="btn btn-primary btn-block"><i class="fa fa-send mr-1"></i>Submit</button>
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
    <!-- Data Supplier -->
    <div class="modal fade text-left" id="mdlsupplier" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class=" modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Data Supplier</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadsupplier"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Pembelian -->
    <div class="modal fade text-left" id="mdlpembelian" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class=" modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Data Pembelian <span id="namasupplier"></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadpembelian"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        var h = document.getElementById('jmlbayar');
        h.addEventListener('keyup', function(e) {
            h.value = formatRupiah(this.value, '');
            //alert(b);
        });

        /* Fungsi formatRupiah */
        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d-]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            // tambahkan titik jika yang di input sudah menjadi angka ribuan
            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? rupiah : '');
        }

        function convertToRupiah(angka) {
            var rupiah = '';
            var angkarev = angka.toString().split('').reverse().join('');
            for (var i = 0; i < angkarev.length; i++)
                if (i % 3 == 0) rupiah += angkarev.substr(i, 3) + '.';
            return rupiah.split('', rupiah.length - 1).reverse().join('');
        }
    </script>
    <script>
        $(function() {
            function loadNoKB() {
                var tgl_kontrabon = $("#tgl_kontrabon").val();
                var kategori = $("#kategori").val();
                //alert(tgl_kontrabon);
                $.ajax({
                    type: 'POST',
                    url: '/kontrabon/getNokontrabon',
                    data: {
                        _token: "{{ csrf_token() }}",
                        tgl_kontrabon: tgl_kontrabon,
                        kategori: kategori
                    },
                    cache: false,
                    success: function(respond) {
                        $("#no_kontrabon").val(respond);
                    }
                });
            }

            $("#kategori,#tgl_kontrabon").change(function(e) {
                loadNoKB();
            });

            function cektutuplaporan() {
                var tgltransaksi = $("#tgl_kontrabon").val();
                $.ajax({
                    type: "POST",
                    url: "/cektutuplaporan",
                    data: {
                        _token: "{{ csrf_token() }}",
                        tanggal: tgltransaksi,
                        jenislaporan: "pembelian"
                    },
                    cache: false,
                    success: function(respond) {
                        console.log(respond);
                        $("#cektutuplaporan").val(respond);
                    }
                });
            }

            $("#tgl_kontrabon").change(function() {
                cektutuplaporan();
            });
            $('#no_kontrabon').mask('AAAAAAAAAAAAAAAAAAAA', {
                'translation': {
                    A: {
                        pattern: /[A-Za/-z0-9]/
                    }
                }
            });

            function loadtotal() {
                var grandtotal = $("#grandtotaltemp").text();
                //alert(grandtotal);
                $("#grandtotal").text(grandtotal);
            }
            $('#nama_supplier').click(function(e) {
                e.preventDefault();
                $('#mdlsupplier').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadsupplier").load("/supplier/getsupplier");
            });

            $('#nobukti_pembelian').click(function(e) {
                var kode_supplier = $("#kode_supplier").val();
                var supplier = $("#nama_supplier").val();

                if (kode_supplier == "") {
                    swal({
                        title: 'Oops',
                        text: 'Supplier Harus Dipilih !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#nama_supplier").focus();
                    });
                } else {
                    $("#namasupplier").text(supplier);
                    e.preventDefault();
                    $('#mdlpembelian').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $("#loadpembelian").load("/pembelian/" + kode_supplier + "/getpembeliankontrabon");
                }
            });


            function loaddetailkontrabontemp() {
                var kode_supplier = $("#kode_supplier").val();
                $.ajax({
                    type: 'GET',
                    url: '/kontrabon/showtemp',
                    data: {
                        kode_supplier: kode_supplier
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loaddetailkontrabon").html(respond);
                        loadtotal();
                    }
                });

            }




            $("#tambahdetail").click(function(e) {
                e.preventDefault();
                var nobukti_pembelian = $("#nobukti_pembelian").val();
                var kode_supplier = $("#kode_supplier").val();
                var jmlbayar = $("#jmlbayar").val();
                var keterangan = $("#keterangan").val();
                var totalpembelian = $("#totalpembelian").val();
                var totalbayar = $("#totalbayar").val();

                if (totalpembelian.length === 0) {
                    var totalpembelian_1 = 0;
                    var totalpembelian_2 = 0;
                } else {
                    var totalpembelian_1 = totalpembelian.replace(/\./g, '');
                    var totalpembelian_2 = totalpembelian_1.replace(/\,/g, '.');

                }

                if (totalbayar.length === 0) {
                    var totalbayar_1 = 0;
                    var totalbayar_2 = 0;
                } else {
                    var totalbayar_1 = totalbayar.replace(/\./g, '');
                    var totalbayar_2 = totalbayar_1.replace(/\,/g, '.');
                }

                if (jmlbayar.length === 0) {
                    var jmlbayar_1 = 0;
                    var jmlbayar_2 = 0;
                } else {
                    var jmlbayar_1 = jmlbayar.replace(/\./g, '');
                    var jmlbayar_2 = jmlbayar_1.replace(/\,/g, '.');

                }
                var b = "10.5";

                var sisabayar = parseFloat(totalpembelian_2) - parseFloat(totalbayar_2);
                var jumlahbayar = parseFloat(jmlbayar_2);


                if (nobukti_pembelian == "") {
                    swal({
                        title: 'Oops',
                        text: 'No. Bukti Pembelian Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#nobukti_pembelian").focus();
                    });
                } else if (kode_supplier == "") {
                    swal({
                        title: 'Oops',
                        text: 'Supplier Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#nama_supplier").focus();
                    });
                } else if (jmlbayar == "") {
                    swal({
                        title: 'Oops',
                        text: 'Jumlah Bayar Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#jmlbayar").focus();
                    });
                } else if (jumlahbayar > sisabayar) {
                    swal({
                        title: 'Oops',
                        text: 'Jumlah Bayar Melebihi Sisa Pembayaran!',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#jmlbayar").focus();
                    });
                } else {
                    $.ajax({
                        type: 'POST',
                        url: '/kontrabon/storetemp',
                        data: {
                            _token: "{{ csrf_token() }}",
                            nobukti_pembelian: nobukti_pembelian,
                            kode_supplier: kode_supplier,
                            jmlbayar: jmlbayar,
                            keterangan: keterangan
                        },
                        cache: false,
                        success: function(respond) {
                            if (respond == 1) {
                                swal("Oops", "Data Sudah Ada !", "warning");
                            } else if (respond == 2) {
                                swal("Oops", "Data Gagal Disimpan, Hubungi Tim IT!", "danger");
                            } else {
                                swal("Berhasil", "Data Berhasil Disimpan !", "success");
                            }
                            loaddetailkontrabontemp();
                        }
                    });
                }
            });

            $("#frmKontrabon").submit(function() {
                var no_kontrabon = $("#no_kontrabon").val();
                var tgl_kontrabon = $("#tgl_kontrabon").val();
                var kategori = $("#kategori").val();
                var kode_supplier = $("#kode_supplier").val();
                var no_dokumen = $("#no_dokumen").val();
                var jenisbayar = $("#jenisbayar").val();
                var jmldata = $("#jmldata").val();
                var cektutuplaporan = $("#cektutuplaporan").val();
                if (cektutuplaporan > 0) {
                    swal({
                        title: 'Oops',
                        text: 'Laporan Periode Ini Sudah Ditutup !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#tgl_kontrabon").focus();
                    });
                    return false;
                } else if (no_kontrabon == "") {
                    swal({
                        title: 'Oops',
                        text: 'No. Kontrabon Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#no_kontrabon").focus();
                    });
                    return false;
                } else if (tgl_kontrabon == "") {
                    swal({
                        title: 'Oops',
                        text: 'Tanggal Kontrabon Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#tgl_kontrabon").focus();
                    });
                    return false;
                } else if (kategori == "") {
                    swal({
                        title: 'Oops',
                        text: 'Kategori Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#kategori").focus();
                    });
                    return false;
                } else if (kode_supplier == "") {
                    swal({
                        title: 'Oops',
                        text: 'Supplier Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#nama_supplier").focus();
                    });
                    return false;
                } else if (jenisbayar == "") {
                    swal({
                        title: 'Oops',
                        text: 'Jenis Bayar Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#jenisbayar").focus();
                    });
                    return false;
                } else if (jmldata == "" || jmldata == 0) {
                    swal({
                        title: 'Oops',
                        text: 'Data Masih Kosong !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#nama_barang").focus();
                    });
                    return false;
                }
            });
        });
    </script>
@endpush
