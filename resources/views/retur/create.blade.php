@include('layouts.style')
<link rel="stylesheet" href="{{ asset('app-assets/css/penjualan.css') }}">
<body style="margin:1px 4px 1px 4px">
    <form name="autoSumForm" autocomplete="off" action="/retur/store" class="formValidate form-horizontal" id="formValidate" method="POST">
        @csrf
        <input type="hidden" id="cektutuplaporan">
        <input type="hidden" id="cektemp">
        <input type="hidden" id="sisapiutang" name="sisapiutang">
        <input type="hidden" id="limitpel" name="limitpel">
        <input type="hidden" id="bruto" name="bruto">
        <div class="box box-info main-body" style="box-shadow: none;height:488px">
            <div class="box-header section-head" style="padding-bottom: 0px !important; height: auto">
                <div class="pull-left logo">
                    <img src="{{asset('app-assets/images/logo/mp.png')}}" alt="" style="width: 80px; margin-left: 15px; margin-top:10px">
                </div>
                <div class="pull-left" style="margin-top: 20px; margin-left: 10px;">
                    <a href="/retur" class="btn btn-success"><i class="fa fa-home"></i></a>
                    <button type="button" id="reset" class="btn btn-warning" style="margin:5px 5px 5px 5px"><i class="fa fa-refresh"></i></button> </div>

                <div class="pull-left" style="margin-left: 10px;padding-right: 10px !important; margin-top: 20px; border-right: 2px rgba(0,0,0,.4) solid">
                    <h6 class="font1" style="margin-top: 3.5px; margin-bottom: 5px;">Petugas :
                        {{Auth::user()->name}}</h6>
                    <h6 class="font1" style="margin-top: 0;">{{date("d-m-Y")}}</h6>
                </div>
                <div class="pull-left">
                    <div class="jam-digital-malasngoding">
                        <div class="kotak">
                            <p id="jam"></p>
                        </div>

                        <div class="kotak">
                            <p id="menit"></p>
                        </div>
                        <div class="kotak">
                            <p id="detik"></p>
                        </div>
                    </div>
                </div>
                <div class="pull-right  box-grandtotal" style="width: 50%; border: 1px rgba(0,0,0,.1) solid; background-color:#dff0d8; margin-top:10px">
                    <h1 align="center" id="grandtotal" style="margin-right: 5px; margin-top: 10px; padding-right: 5px; font-size: 50px; text-align: right;">
                        0
                    </h1>
                    <input type="hidden" id="subtotal" name="subtotal">
                </div>
            </div>
            <div class="box-body section-main" style="height:auto;">
                @include('layouts.notification')
                <div class="row">
                    <div class="col-2">
                        <div class="form-group">
                            <div class="form-label-group position-relative has-icon-left">
                                <input type="text" id="no_retur_penj" class="form-control" name="no_retur_penj" placeholder="No. Retur Penjualan">
                                <div class="form-control-position" style="top:10px">
                                    <i class="feather icon-credit-card"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <div class="form-label-group position-relative has-icon-left">
                                <input type="text" id="tglretur" class="form-control pickadate-months-year picker__input" name="tglretur" placeholder="Tanggal Retur">
                                <div class="form-control-position" style="top:10px">
                                    <i class="feather icon-calendar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <div class="form-label-group position-relative has-icon-left">
                                <input type="hidden" id="kode_cabang" class="form-control" name="kode_cabang" readonly>
                                <input type="hidden" id="kode_pelanggan" class="form-control" name="kode_pelanggan" readonly>
                                <input type="hidden" id="jatuhtempo" class="form-control" name="jatuhtempo" readonly>
                                <input type="text" id="nama_pelanggan" class="form-control" name="nama_pelanggan" placeholder="Pelanggan" readonly>
                                <div class="form-control-position" style="top:10px">
                                    <i class="feather icon-user"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <div class="form-label-group position-relative has-icon-left">
                                <input type="hidden" id="kategori_salesman" class="form-control" name="kategori_salesman" readonly>
                                <input type="hidden" id="id_karyawan" class="form-control" name="id_karyawan" readonly>
                                <input type="text" id="nama_karyawan" class="form-control" name="nama_karyawan" placeholder="Salesman" readonly>
                                <div class="form-control-position" style="top:10px">
                                    <i class="feather icon-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-info" style="border: 1px rgba(0,0,0,.1) solid !important;">
                    <div class="row" id="frmcariObat" style="margin-top:10px; margin-left:3px; margin-right:3px">
                        <div class="col-12">
                            <div class="form-group">
                                <div class="form-label-group position-relative has-icon-left">
                                    <input type="text" id="kode_barang" class="form-control" name="kode_barang" placeholder="Ketikan Nama Barang / Scan Barcode [F2]">
                                    <div class="form-control-position" style="top:12px">
                                        <i class="feather icon-search"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="grid-view" id="w2">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="responsive" style="height: 70%">
                                    <table class="table table-bordered  table-hover-animation" id="tabelproduktemp">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Kode Barang</th>
                                                <th>Nama Barang</th>
                                                <th>Pcs/Dus</th>
                                                <th>Pack/Dus</th>
                                                <th>Pcs/pack</th>
                                                <th></th>
                                                <th style="text-align:center">Dus/Ball</th>
                                                <th>Harga/Dus/Ball</th>
                                                <th class="text-center">Pack</th>
                                                <th>Harga/Pack</th>
                                                <th class="text-center">Pcs</th>
                                                <th>Harga/Pcs</th>
                                                <th>Total</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="loadbarangtemp" class="font3">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body section-footer" style="padding-top: 0px !important;height: 201px;">
                <div class="row">
                    <div class="col-md-2">
                        <div class="card text-white border-0 box-shadow-0">
                            <div class="card-content">
                                <img class="card-img img-fluid" id="foto" src="{{ asset('app-assets/images/slider/04.jpg') }}" alt="Card image" style="height: 300px">
                                <div class="card-img-overlay overflow-hidden overlay-danger" style="background: #008b9cd9">

                                    <p class="card-text text-white" style="font-family: 'Poppins';">
                                        Alamat <br>
                                        <span id="alamat_pelanggan"></span><br>
                                        No. HP <br>
                                        <span id="no_hp"></span><br>

                                        Koordinat <br>
                                        <span id="koordinat"></span><br>
                                        Limit Pelanggan <br>
                                        <span id="limitpelanggan"></span><br>
                                        Piutang Pelanggan <br>
                                        <span id="piutangpelanggan"></span><br>
                                    </p>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">

                    </div>
                    <div class="col-md-2">

                    </div>
                    <div class="col-md-2">

                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-header">
                                    <h3 class="card-title" style="font-size: 14px; font-family:Poppins">Total Retur</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <select class="form-control" name="jenis_retur" id="jenis_retur">
                                            <option value="">Jenis Retur</option>
                                            <option value="gb">Ganti Barang</option>
                                            <option value="pf">Potong Faktur</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <select class="form-control" name="no_fak_penj" id="no_fak_penj">
                                            <option value="">No. Faktur</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-block"><i class="feather icon-send mr-1"></i>Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </form>
    <!-- Detail Salesman -->
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
                    <table class="table table-hover-animation tabelpelanggan" style="width:100% !important" id="tabelpelanggan">
                        <thead class="thead-dark">
                            <tr>
                                <th>Kode Pelanggan</th>
                                <th>Nama Pelanggan</th>
                                <th>Pasar</th>
                                <th>Salesman</th>
                                <th>Kode Cabang</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.script');
    <script>
        window.setTimeout("waktu()", 1000);

        function waktu() {
            var waktu = new Date();
            setTimeout("waktu()", 1000);
            document.getElementById("jam").innerHTML = waktu.getHours();
            document.getElementById("menit").innerHTML = waktu.getMinutes();
            document.getElementById("detik").innerHTML = waktu.getSeconds();
        }

    </script>
    <script>
        $(function() {
            $("body").removeClass("dark-layout");

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
                    }, {
                        data: 'nama_karyawan'
                        , name: 'karyawan.nama_karyawan'
                    }, {
                        data: 'kode_cabang'
                        , name: 'kode_cabang'
                    }
                    , {
                        data: 'action'
                        , name: 'action'
                        , orderable: false
                        , searchable: false
                    }

                ],

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
                loadbarangtemp(kode_pelanggan);
                loadfaktur(kode_pelanggan);
                loadtotal();
                cektemp();
                $("#mdlpelanggan").modal("hide");

            });

            function simpanbarangtemp(kode_barang) {
                var kode_pelanggan = $("#kode_pelanggan").val();
                $.ajax({
                    type: 'POST'
                    , url: '/retur/storebarangtemp'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , kode_barang: kode_barang
                        , kode_pelanggan: kode_pelanggan
                    }
                    , cache: false
                    , success: function(respond) {
                        if (respond == "1") {
                            swal("Oops", "Data Barang Sudah Ada !", "warning");
                            $("#kode_barang").focus();
                        } else {
                            //loadproduktemp();
                            $("#kode_barang").val("");
                            $("#kode_barang").focus();
                            loadbarangtemp();
                        }
                    }
                });
            }

            function loadbarangtemp() {
                var kode_pelanggan = $("#kode_pelanggan").val();
                $.ajax({
                    type: 'POST'
                    , url: '/retur/showbarangtemp'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , kode_pelanggan: kode_pelanggan
                    , }
                    , cache: false
                    , success: function(respond) {
                        $("#loadbarangtemp").html(respond);
                        cektemp();
                    }
                });
            }



            loadbarangtemp();
            $("#kode_barang").autocomplete({
                source: function(request, response) {
                    // Fetch data
                    $.ajax({
                        url: "/getautocompletehargaretur"
                        , type: 'post'
                        , dataType: "json"
                        , data: {
                            _token: "{{ csrf_token() }}"
                            , search: request.term
                            , kode_cabang: $("#kode_cabang").val()
                            , kategori_salesman: $("#kategori_salesman").val()
                            , kode_pelanggan: $("#kode_pelanggan").val()
                        }
                        , success: function(data) {
                            response(data);
                        }
                    });
                }
                , select: function(event, ui) {
                    $('#kode_barang').val(ui.item.label);
                    var kode_barang = ui.item.val;
                    simpanbarangtemp(kode_barang);
                    return false;
                }
            });


        });

    </script>
</body>
