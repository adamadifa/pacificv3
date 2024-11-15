@extends('layouts.midone')
@section('titlepage', 'Input Saldo Awal Mutasi Barang Produksi')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Input Saldo Awal Barang Produksi</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/saldoawalmutasibarangproduksi/create">Input Saldo Awal
                                        Barang Produksi</a>
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
            <div class="col-md-12 col-sm-8 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <form action="/saldoawalmutasibarangproduksi/store" method="POST" id="frmsabarangproduksi">
                            @csrf
                            <input type="hidden" id="getsa">
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="Kode Saldo Awal" field="kode_saldoawal"
                                        icon="feather icon-credit-card" value="Auto" readonly />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <select class="form-control" id="bulan" name="bulan">
                                            <option value="">Bulan</option>
                                            <?php
                                    $bulanini = date("m");
                                    for ($i = 1; $i < count($bulan); $i++) {
                                    ?>
                                            <option value="<?php echo $i; ?>"><?php echo $bulan[$i]; ?></option>
                                            <?php
                                    }
                                    ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <select class="form-control" id="tahun" name="tahun">
                                            <option value="">Tahun</option>
                                            <?php
                                    $tahunmulai = 2020;
                                    for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                                    ?>
                                            <option value="<?php echo $thn; ?>"><?php echo $thn; ?></option>
                                            <?php
                                    }
                                    ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="Tanggal" field="tanggal" icon="feather icon-calendar" datepicker />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <a href="#" class="btn btn-success btn-block" id="getsaldo"><i
                                                class="feather icon-refresh-ccw mr-1"></i> Get Saldo</a>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-hover-animation">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Qty</th>
                                    </tr>
                                </thead>
                                <tbody id="loaddetailsaldo">

                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <button class="btn btn-primary btn-block" type="submit" name="submit"><i
                                                class="fa fa-send mr-1"></i> Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Data list view end -->
        </div>
    </div>

@endsection

@push('myscript')
    <script>
        $(function() {
            function loaddetailsaldo() {
                var bulan = $("#bulan").val();
                var tahun = $("#tahun").val();
                var thn = tahun.substr(2, 2);
                if (bulan == "") {
                    swal("Oops!", "Bulan Harus Diisi !", "warning");
                    return false;
                } else if (tahun == "") {
                    swal("Oops!", "Tahun Harus Diisi !", "warning");
                    return false;
                } else {
                    $.ajax({
                        type: 'POST',
                        url: '/saldoawalmutasibarangproduksi/getdetailsaldo',
                        data: {
                            _token: "{{ csrf_token() }}",
                            bulan: bulan,
                            tahun: tahun
                        },
                        cache: false,
                        success: function(respond) {
                            if (respond == 1) {
                                $("#getsa").val(0);
                                swal("Oops!",
                                    "Saldo Bulan Sebelumnya Belum Diset! Atau Saldo Bulan Tersebut Sudah Ada",
                                    "warning");
                            } else {
                                $("#getsa").val(1);
                                $("#loaddetailsaldo").html(respond);
                            }
                        }
                    });
                }
            }

            $("#getsaldo").click(function(e) {
                e.preventDefault();
                loaddetailsaldo();
            });

            $("#frmsabarangproduksi").submit(function() {
                var getsa = $("#getsa").val();
                var tanggal = $("#tanggal").val();
                if (getsa == "" || getsa == 0) {
                    swal("Oops", "Silahkan Lakukan Get Saldo Terlebih Dahulu!", "warning");
                    return false;
                } else if (tanggal == "") {
                    swal("Oops", "Tanggal  Harus Diisi !", "warning");
                    return false;
                }
            });
        });
    </script>
@endpush
