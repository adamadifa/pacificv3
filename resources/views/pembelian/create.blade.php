@extends('layouts.midone')
@section('titlepage', 'Input Pembelian')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Input Pembelian</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/pembelian/create">Input Pembelian</a>
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
            <form action="/pembelian/store" method="POST" id="frmPembelian">

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
                                                <x-inputtext field="nobukti_pembelian" label="No. Bukti Pembelian" icon="feather icon-credit-card" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <x-inputtext field="tgl_pembelian" label="Tanggal Pembelian" icon="feather icon-calendar" datepicker />
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
                                                <div class="form-group">
                                                    <select name="kode_dept" id="kode_dept" class="form-control">
                                                        <option value="">Pilih Departemen</option>
                                                        @foreach ($departemen as $d)
                                                            <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <select name="jenistransaksi" id="jenistransaksi" class="form-control">
                                                        <option value="">Jenis Transaksi</option>
                                                        <option value="tunai">Tunai</option>
                                                        <option value="kredit">Kredit</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <x-inputtext field="tgl_jatuhtempo" label="Tanggal Jatuh Tempo" icon="feather icon-calendar" datepicker />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <ul class="list-unstyled mb-0">
                                                    <li class="d-inline-block mr-2">
                                                        <fieldset>
                                                            <div class="vs-radio-con">
                                                                <input type="radio" name="kategori_transaksi" id="kategori_transaksi" value="MP">
                                                                <span class="vs-radio">
                                                                    <span class="vs-radio--border"></span>
                                                                    <span class="vs-radio--circle"></span>
                                                                </span>
                                                                <span class="">MP</span>
                                                            </div>
                                                        </fieldset>
                                                    </li>
                                                    <li class="d-inline-block mr-2">
                                                        <fieldset>
                                                            <div class="vs-radio-con">
                                                                <input type="radio" name="kategori_transaksi" id="kategori_transaksi" value="IP">
                                                                <span class="vs-radio">
                                                                    <span class="vs-radio--border"></span>
                                                                    <span class="vs-radio--circle"></span>
                                                                </span>
                                                                <span class="">IP</span>
                                                            </div>
                                                        </fieldset>
                                                    </li>
                                                    <li class="d-inline-block mr-2">
                                                        <fieldset>
                                                            <div class="vs-radio-con">
                                                                <input type="radio" name="kategori_transaksi" id="kategori_transaksi" value="P">
                                                                <span class="vs-radio">
                                                                    <span class="vs-radio--border"></span>
                                                                    <span class="vs-radio--circle"></span>
                                                                </span>
                                                                <span class="">Pribadi</span>
                                                            </div>
                                                        </fieldset>
                                                    </li>
                                                    <li class="d-inline-block mr-2">
                                                        <fieldset>
                                                            <div class="vs-radio-con">
                                                                <input type="radio" name="kategori_transaksi" id="kategori_transaksi" value="PCF">
                                                                <span class="vs-radio">
                                                                    <span class="vs-radio--border"></span>
                                                                    <span class="vs-radio--circle"></span>
                                                                </span>
                                                                <span class="">Pacific</span>
                                                            </div>
                                                        </fieldset>
                                                    </li>
                                                </ul>
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
                                        <input type="hidden" name="kode_barang" id="kode_barang">
                                        <x-inputtext field="nama_barang" label="Nama Barang" icon="feather icon-box" readonly />
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <x-inputtext field="qty" label="Qty" icon="feather icon-box" />
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <x-inputtext field="harga" label="Harga" icon="feather icon-box" right />
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <x-inputtext field="peny_harga" label="Penyesuaian Harga" icon="feather icon-box" right />
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <select name="kode_akun" id="kode_akun" class="form-control select2">
                                                <option value="">Kode Akun</option>
                                                @foreach ($coa as $d)
                                                    <option value="{{ $d->kode_akun }}"><b>{{ $d->kode_akun }}</b> - {{ $d->nama_akun }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-sm-12">
                                        <div class="form-group">
                                            <a href="#" class="btn btn-primary" id="tambahbarang"><i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <x-inputtext field="keterangan" label="Keterangan" icon="feather icon-file" />
                                    </div>
                                    <div class="col-1">
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
                                    <div class="col-lg-2 col-sm-12" id="pilihcabang">
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
                                                    <th>No</th>
                                                    <th>Kode Barang</th>
                                                    <th>Nama Barang</th>
                                                    <th>Keterangan</th>
                                                    <th>Qty</th>
                                                    <th>Harga</th>
                                                    <th>Subtotal</th>
                                                    <th>Penyesuaian</th>
                                                    <th>Total</th>
                                                    <th>Kode Akun</th>
                                                    <th>Cabang</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="loaddetailpembelian"></tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-1">
                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                            <input type="checkbox" class="ppn" name="ppn" value="1">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">PPN ?</span>
                                        </div>
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

    <!-- Data Barang -->
    <div class="modal fade text-left" id="mdlbarang" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class=" modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Data Barang <span id="dept"></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadbarang"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        var h = document.getElementById('harga');
        h.addEventListener('keyup', function(e) {
            h.value = formatRupiah(this.value, '');
            //alert(b);
        });

        var p = document.getElementById('peny_harga');
        p.addEventListener('keyup', function(e) {
            p.value = formatRupiah(this.value, '');
            //alert(b);
        });

        var q = document.getElementById('qty');
        q.addEventListener('keyup', function(e) {
            q.value = formatRupiah(this.value, '');
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

            function cektutuplaporan() {
                var tgltransaksi = $("#tgl_pembelian").val();
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

            $("#tgl_pembelian").change(function() {
                cektutuplaporan();
            });
            $('.cabangcheck').change(function() {
                if (this.checked) {
                    $("#pilihcabang").show();
                } else {
                    $("#pilihcabang").hide();
                }
            });

            function hidecabang() {
                $("#pilihcabang").hide();
            }

            hidecabang();
            // $("#harga,#peny_harga").maskMoney({
            //     decimal: ','
            // });

            // $("#harga").maskMoney({
            //     prefix: 'R$ '
            //     , allowNegative: true
            //     , thousands: '.'
            //     , decimal: ','
            //     , affixesStay: false
            // });


            $('#nama_supplier').click(function(e) {
                e.preventDefault();
                $('#mdlsupplier').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadsupplier").load("/supplier/getsupplier");
            });

            $('#nama_barang').click(function(e) {
                var kode_dept = $("#kode_dept").val();
                var departemen = $("#kode_dept option:selected").text();
                if (kode_dept == "") {
                    swal({
                        title: 'Oops',
                        text: 'Departemen Harus Dipilih !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#kode_dept").focus();
                    });
                } else {
                    $("#dept").text(departemen);
                    e.preventDefault();
                    $('#mdlbarang').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $("#loadbarang").load("/barangpembelian/" + kode_dept + "/getbarang");
                }
            });

            function reset() {
                $("#kode_barang").val("");
                $("#nama_barang").val("");
                $("#qty").val("");
                $("#harga").val("");
                $("#peny_harga").val("");
                $("#keterangan").val("");
                $("#kode_cabang").val("").change();
                $("#kode_akun").val("").change();
            }


            function loadtotal() {
                var grandtotal = $("#grandtotaltemp").text();
                $("#grandtotal").text(grandtotal);
            }

            function loaddetailpembeliantemp() {
                var kode_dept = $("#kode_dept").val();
                $.ajax({
                    type: 'POST',
                    url: '/pembelian/showtemp',
                    data: {
                        _token: "{{ csrf_token() }}",
                        kode_dept: kode_dept
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loaddetailpembelian").html(respond);
                        loadtotal();
                    }
                });
            }
            loaddetailpembeliantemp();
            $("#kode_dept").change(function() {
                loaddetailpembeliantemp();
            });


            $('#nobukti_pembelian').mask('AAAAAAAAAAAAAAAAAAAA', {
                'translation': {
                    A: {
                        pattern: /[A-Za/-z0-9]/
                    }
                }
            });

            $("#frmPembelian").submit(function() {
                var nobukti_pembelian = $("#nobukti_pembelian").val();
                var tgl_pembelian = $("#tgl_pembelian").val();
                var kode_supplier = $("#kode_supplier").val();
                var kode_dept = $("#kode_dept").val();
                var jenistransaksi = $("#jenistransaksi").val();
                var tgl_jatuhtempo = $("#tgl_jatuhtempo").val();
                var cektutuplaporan = $("#cektutuplaporan").val();
                var jmldata = $("#jmldata").val();

                if ($('input[name="kategori_transaksi"]:checked').length == 0) {
                    swal({
                        title: 'Oops',
                        text: 'Kategori Transaksi Harus Dipilih !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#kategori_transaksi").focus();
                    });
                    return false;
                } else if (cektutuplaporan > 0) {
                    swal({
                        title: 'Oops',
                        text: 'Laporan Periode Ini Sudah Ditutup !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#tgl_pembelian").focus();
                    });
                    return false;
                } else if (nobukti_pembelian == "") {
                    swal({
                        title: 'Oops',
                        text: 'No. Bukti Pembelian Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#nobukti_pembelian").focus();
                    });
                    return false;
                } else if (tgl_pembelian == "") {
                    swal({
                        title: 'Oops',
                        text: 'Tanggal Pembelian Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#tgl_pembelian").focus();
                    });
                    return false;
                } else if (kode_supplier == "") {
                    swal({
                        title: 'Oops',
                        text: 'Supplier Harus Dipilih !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#nama_supplier").focus();
                    });
                    return false;
                } else if (kode_dept == "") {
                    swal({
                        title: 'Oops',
                        text: 'Departemen Harus Dipilih !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#kode_dept").focus();
                    });
                    return false;
                } else if (jenistransaksi == "") {
                    swal({
                        title: 'Oops',
                        text: 'Jenis Transaksi Harus Dipilih !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#jenistransaksi").focus();
                    });
                    return false;
                } else if (tgl_jatuhtempo == "" && jenistransaksi == "kredit") {
                    swal({
                        title: 'Oops',
                        text: 'Jatuh Temp Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#tgl_jatuhtempo").focus();
                    });
                    return false;
                } else if (jmldata == 0 || jmldata == "") {
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


            $("#tambahbarang").click(function(e) {
                e.preventDefault();
                var kode_barang = $("#kode_barang").val();
                var qty = $("#qty").val();
                var harga = $("#harga").val();
                var peny_harga = $("#peny_harga").val();
                var kode_akun = $("#kode_akun").val();
                var keterangan = $("#keterangan").val();
                var kode_cabang = $("#kode_cabang").val();
                var kode_dept = $("#kode_dept").val();
                // if ($('.cabangcheck').is(':checked')) {
                //     alert('test');
                // }
                if (kode_barang == "") {
                    swal({
                        title: 'Oops',
                        text: 'Kode Barang Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#nama_barang").focus();
                    });
                } else if (qty == "" || qty == 0) {
                    swal({
                        title: 'Oops',
                        text: 'Qty Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#qty").focus();
                    });
                } else if (harga == "") {
                    swal({
                        title: 'Oops',
                        text: 'Harga Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#harga").focus();
                    });
                } else if (kode_akun == "") {
                    swal({
                        title: 'Oops',
                        text: 'Kode Akun Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#kode_akun").focus();
                    });
                } else if ($('.cabangcheck').is(':checked') && kode_cabang == "") {
                    swal({
                        title: 'Oops',
                        text: 'Cabang Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#kode_cabang").focus();
                    });
                } else {
                    $.ajax({
                        type: 'POST',
                        url: '/pembelian/storetemp',
                        data: {
                            _token: "{{ csrf_token() }}",
                            kode_barang: kode_barang,
                            keterangan: keterangan,
                            kode_dept: kode_dept,
                            qty: qty,
                            harga: harga,
                            peny_harga: peny_harga,
                            kode_akun: kode_akun,
                            kode_cabang: kode_cabang
                        },
                        cache: false,
                        success: function(respond) {
                            console.log(respond);
                            if (respond == 1) {
                                swal({
                                    title: 'Oops',
                                    text: 'Data Sudah Ada !',
                                    icon: 'warning',
                                    showConfirmButton: false
                                }).then(function() {
                                    $("#nama_barang").focus();
                                });
                            } else if (respond == 2) {
                                swal({
                                    title: 'Oops',
                                    text: 'Data Gagal Disimpan, Hubungi Tim IT !',
                                    icon: 'warning',
                                    showConfirmButton: false
                                }).then(function() {
                                    $("#nama_barang").focus();
                                });
                            } else {
                                loaddetailpembeliantemp();
                                reset();
                            }
                        }
                    });
                }

            });
        });
    </script>
@endpush
