@extends('layouts.midone')
@section('titlepage', 'Data Order Management Cabang')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Buat Oman</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/oman/create">Buat Oman</a>
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
            <div class="col-md-10 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="/oman/store">
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 col-sm-12">
                                    <x-inputtext label="Tanggal Order" field="tgl_order" icon="feather icon-calendar"
                                        datepicker />
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    {{-- <label for="" class="form-label mb-1">Omset Bulan</label> --}}
                                    <div class="form-group">
                                        <select class="form-control" id="bulan" name="bulan">
                                            <option value="">Bulan</option>
                                            <?php
                                    $bulanini = date("m");
                                    for ($i = 1; $i < count($bulan); $i++) {
                                    ?>
                                            <option <?php if ($bulanini == $i) {
                                                echo 'selected';
                                            } ?> value="<?php echo $i; ?>"><?php echo $bulan[$i]; ?>
                                            </option>
                                            <?php
                                    }
                                    ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group">
                                        <select class="form-control" id="tahun" name="tahun">
                                            <?php
                                    $tahunmulai = 2020;
                                    for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                                    ?>
                                            <option <?php if (date('Y') == $thn) {
                                                echo 'Selected';
                                            } ?> value="<?php echo $thn; ?>"><?php echo $thn; ?>
                                            </option>
                                            <?php
                                    }
                                    ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <table class="table" id="mytable">
                                <thead>
                                    <tr>
                                        <th width="10px" rowspan="3" style="vertical-align: middle;">No</th>
                                        <th width="100px" rowspan="3" style="vertical-align: middle;">Kode Produk</th>
                                        <th rowspan="3" style="vertical-align: middle; text-align: center;">Produk</th>
                                        <th colspan="12" style="text-align: center">Jumlah Permintaan</th>
                                        <th rowspan="3" style="vertical-align: middle; text-align:center; width:10%">
                                            Total</th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" style="text-align:center">M1</th>
                                        <th colspan="3" style="text-align:center">M2</th>
                                        <th colspan="3" style="text-align:center">M3</th>
                                        <th colspan="3" style="text-align:center">M4</th>
                                    </tr>
                                    <tr>
                                        <th style="width:60px">
                                            <div class="form-group">
                                                <input type="text" style="color:black" id="darim1" maxlength="2"
                                                    name="darim1" class="form-control" />
                                            </div>
                                        </th>
                                        <th style="width:10px;vertical-align: middle;">
                                            s/d
                                        </th>
                                        <th style="width:60px">
                                            <div class="form-group">
                                                <input type="text" style="color:black" id="sampaim1" maxlength="2"
                                                    name="sampaim1" class="form-control" />
                                            </div>
                                        </th>

                                        <!-- Minggu Ke 2 -->
                                        <th style="width:60px">
                                            <div class="form-group">
                                                <input type="text" style="color:black" id="darim2" maxlength="2"
                                                    name="darim2" class="form-control" />
                                            </div>
                                        </th>
                                        <th style="width:10px;vertical-align: middle;">
                                            s/d
                                        </th>
                                        <th style="width:60px">
                                            <div class="form-group">
                                                <input type="text" style="color:black" id="sampaim2" maxlength="2"
                                                    name="sampaim2" class="form-control" />
                                            </div>
                                        </th>

                                        <!-- Minggu Ke 3-->
                                        <th style="width:60px">
                                            <div class="form-group">
                                                <input type="text" style="color:black" id="darim3" maxlength="2"
                                                    name="darim3" class="form-control" />
                                            </div>
                                        </th>
                                        <th style="width:10px;vertical-align: middle;">
                                            s/d
                                        </th>
                                        <th style="width:60px">
                                            <div class="form-group">
                                                <input type="text" style="color:black" id="sampaim3" maxlength="2"
                                                    name="sampaim3" class="form-control" />
                                            </div>
                                        </th>
                                        <!-- Minggu Ke 4-->
                                        <th style="width:60px">
                                            <div class="form-group">
                                                <input type="text" style="color:black" id="darim4" maxlength="2"
                                                    name="darim4" class="form-control" />
                                            </div>

                                        </th>
                                        <th style="width:10px;vertical-align: middle;">
                                            s/d
                                        </th>
                                        <th style="width:60px">
                                            <div class="form-group">
                                                <input type="text" style="color:black" id="sampaim4" maxlength="2"
                                                    name="sampaim4" class="form-control" />
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="loadform">

                                </tbody>
                            </table>

                            <input type="hidden" name="status" id="status">
                            <button class="btn btn-primary btn-block">Submit</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('myscript')
    <script type="text/javascript">
        $(function() {

            $('.subtotal').prop('readonly', true);

            var $tblrows = $("#mytable tbody tr");
            $tblrows.each(function(index) {
                var $tblrow = $(this);
                $tblrow.find('.jmlm1').on('input', function() {
                    var jmlm1 = $tblrow.find("[id=jmlm1]").val();
                    var jmlm2 = $tblrow.find("[id=jmlm2]").val();
                    var jmlm3 = $tblrow.find("[id=jmlm3]").val();
                    var jmlm4 = $tblrow.find("[id=jmlm4]").val();

                    if (jmlm1.length === 0) {
                        var jml1 = 0;
                    } else {
                        var jml1 = parseInt(jmlm1);
                    }
                    if (jmlm2.length === 0) {
                        var jml2 = 0;
                    } else {
                        var jml2 = parseInt(jmlm2);
                    }
                    if (jmlm3.length === 0) {
                        var jml3 = 0;
                    } else {
                        var jml3 = parseInt(jmlm3);
                    }

                    if (jmlm4.length === 0) {
                        var jml4 = 0;
                    } else {
                        var jml4 = parseInt(jmlm4);
                    }
                    var subTotal = jml1 + jml2 + jml3 + jml4;


                    if (!isNaN(subTotal)) {
                        $tblrow.find('.subtotal').val(subTotal);
                        var grandTotal = 0;
                        $(".subtotal").each(function() {
                            var stval = parseInt($(this).val());
                            grandTotal += isNaN(stval) ? 0 : stval;
                        });
                        //$('.grdtot').val(grandTotal.toFixed(2));
                    }

                });

                $tblrow.find('.jmlm2').on('input', function() {
                    var jmlm1 = $tblrow.find("[id=jmlm1]").val();
                    var jmlm2 = $tblrow.find("[id=jmlm2]").val();
                    var jmlm3 = $tblrow.find("[id=jmlm3]").val();
                    var jmlm4 = $tblrow.find("[id=jmlm4]").val();

                    if (jmlm1.length === 0) {

                        var jml1 = 0;

                    } else {
                        var jml1 = parseInt(jmlm1);
                    }
                    if (jmlm2.length === 0) {

                        var jml2 = 0;

                    } else {
                        var jml2 = parseInt(jmlm2);
                    }
                    if (jmlm3.length === 0) {

                        var jml3 = 0;

                    } else {
                        var jml3 = parseInt(jmlm3);
                    }

                    if (jmlm4.length === 0) {

                        var jml4 = 0;

                    } else {
                        var jml4 = parseInt(jmlm4);
                    }
                    var subTotal = jml1 + jml2 + jml3 + jml4;


                    if (!isNaN(subTotal)) {

                        $tblrow.find('.subtotal').val(subTotal);
                        var grandTotal = 0;
                        $(".subtotal").each(function() {
                            var stval = parseInt($(this).val());
                            grandTotal += isNaN(stval) ? 0 : stval;
                        });

                        //$('.grdtot').val(grandTotal.toFixed(2));
                    }

                });


                $tblrow.find('.jmlm3').on('input', function() {
                    var jmlm1 = $tblrow.find("[id=jmlm1]").val();
                    var jmlm2 = $tblrow.find("[id=jmlm2]").val();
                    var jmlm3 = $tblrow.find("[id=jmlm3]").val();
                    var jmlm4 = $tblrow.find("[id=jmlm4]").val();

                    if (jmlm1.length === 0) {
                        var jml1 = 0;
                    } else {
                        var jml1 = parseInt(jmlm1);
                    }
                    if (jmlm2.length === 0) {
                        var jml2 = 0;
                    } else {
                        var jml2 = parseInt(jmlm2);
                    }
                    if (jmlm3.length === 0) {
                        var jml3 = 0;
                    } else {
                        var jml3 = parseInt(jmlm3);
                    }

                    if (jmlm4.length === 0) {
                        var jml4 = 0;
                    } else {
                        var jml4 = parseInt(jmlm4);
                    }
                    var subTotal = jml1 + jml2 + jml3 + jml4;


                    if (!isNaN(subTotal)) {

                        $tblrow.find('.subtotal').val(subTotal);
                        var grandTotal = 0;
                        $(".subtotal").each(function() {
                            var stval = parseInt($(this).val());
                            grandTotal += isNaN(stval) ? 0 : stval;
                        });

                        //$('.grdtot').val(grandTotal.toFixed(2));
                    }

                });


                $tblrow.find('.jmlm4').on('input', function() {
                    var jmlm1 = $tblrow.find("[id=jmlm1]").val();
                    var jmlm2 = $tblrow.find("[id=jmlm2]").val();
                    var jmlm3 = $tblrow.find("[id=jmlm3]").val();
                    var jmlm4 = $tblrow.find("[id=jmlm4]").val();

                    if (jmlm1.length === 0) {

                        var jml1 = 0;
                    } else {
                        var jml1 = parseInt(jmlm1);
                    }
                    if (jmlm2.length === 0) {
                        var jml2 = 0;
                    } else {
                        var jml2 = parseInt(jmlm2);
                    }
                    if (jmlm3.length === 0) {
                        var jml3 = 0;
                    } else {
                        var jml3 = parseInt(jmlm3);
                    }

                    if (jmlm4.length === 0) {
                        var jml4 = 0;
                    } else {
                        var jml4 = parseInt(jmlm4);
                    }
                    var subTotal = jml1 + jml2 + jml3 + jml4;


                    if (!isNaN(subTotal)) {
                        $tblrow.find('.subtotal').val(subTotal);
                        var grandTotal = 0;
                        $(".subtotal").each(function() {
                            var stval = parseInt($(this).val());
                            grandTotal += isNaN(stval) ? 0 : stval;
                        });

                        //$('.grdtot').val(grandTotal.toFixed(2));
                    }
                });
            });


            function loadStatus() {
                var bulan = $("#bulan").val();
                var tahun = $("#tahun").val();
                $.ajax({
                    type: 'POST',
                    url: '/cekoman',
                    data: {
                        _token: "{{ csrf_token() }}",
                        tahun: tahun,
                        bulan: bulan
                    },
                    cache: false,
                    success: function(respond) {

                        $("#status").val(respond);
                    }
                });
            }

            function loadform() {
                var bulan = $("#bulan").val();
                var tahun = $("#tahun").val();
                $.ajax({
                    type: 'POST',
                    url: '/getomancabang',
                    data: {
                        _token: "{{ csrf_token() }}",
                        tahun: tahun,
                        bulan: bulan
                    },
                    cache: false,
                    success: function(respond) {

                        $("#loadform").html(respond);
                    }

                });
            }

            loadform();
            $("#bulan").change(function(e) {
                loadStatus();
                loadform();
            });

            $("#tahun").change(function(e) {
                loadStatus();
                loadform();
            });

            $("form").submit(function() {

                var darim1 = $("#darim1").val();
                var darim2 = $("#darim2").val();
                var darim3 = $("#darim3").val();
                var darim4 = $("#darim4").val();


                var sampai1 = $("#sampaim1").val();
                var sampai2 = $("#sampaim2").val();
                var sampai3 = $("#sampaim3").val();
                var sampai4 = $("#sampaim4").val();

                var bulan = $("#bulan").val();
                var tahun = $("#tahun").val();
                var status = $("#status").val();
                var tgl_order = $("#tgl_order").val();


                if (status == 1) {
                    swal("Oops!", "Data Oman Bulan " + bulan + "Tahun " + tahun + " Sudah Ada !",
                    "warning");
                    return false;

                } else if (tgl_order == "") {
                    swal("Oops!", "Tanggal Order Masih Kosong !", "warning");
                    return false;

                } else if (bulan == "") {
                    swal("Oops!", "Bulan Masih Kosong !", "warning");
                    return false;

                } else if (tahun == "") {
                    swal("Oops!", "Tahun Masih Kosong !", "warning");
                    return false;

                } else if (darim1 == "" || sampai1 == "") {
                    swal("Oops!", "Tanggal di Minggu Ke 1 Masih Kosong !", "warning");
                    return false;

                } else if (darim2 == "" || sampai2 == "") {
                    swal("Oops!", "Tanggal di Minggu Ke 2 Masih Kosong !", "warning");
                    return false;

                } else if (darim3 == "" || sampai3 == "") {
                    swal("Oops!", "Tanggal di Minggu Ke 3 Masih Kosong !", "warning");
                    return false;

                } else if (darim4 == "" || sampai4 == "") {
                    swal("Oops!", "Tanggal di Minggu Ke 4 Masih Kosong !", "warning");
                    return false;

                } else {
                    return true;
                }

            });


        });
    </script>
@endpush
