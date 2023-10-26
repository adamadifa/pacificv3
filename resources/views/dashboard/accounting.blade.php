@extends('layouts.midone')
@section('titlepage', 'Dashboard')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
    </div>
    <div class="content-body">
        <!-- Dashboard Analytics Start -->

        <div class="row">
            <div class="col-lg-4 col-md-12 col-sm-12">
                <section id="dashboard-analytics">
                    <div class="card bg-analytics text-white">
                        <div class="card-content">
                            <div class="card-body text-center">
                                <img src="{{asset('app-assets/images/elements/decore-left.png')}}" class="img-left" alt="card-img-left">
                                <img src="{{asset('app-assets/images/elements/decore-right.png')}}" class="img-right" alt="card-img-right">
                                <div class="avatar avatar-xl bg-primary shadow mt-0">
                                    <div class="avatar-content">
                                        <i class="feather icon-award white font-large-1"></i>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <h3 class="mb-1 text-white">Selamat Datang, {{ Auth::user()->name }} </h3>
                                    <h4 class="text-white">{{ date('d F Y') }} </h4>
                                    <p class="m-auto w-75">Anda Masuk Sebagai Level {{ ucwords(Auth::user()->level) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-lg-3 col-md-12 col-sm-12">
                <div class="card text-center">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="avatar bg-rgba-danger p-50 m-0 mb-1 mt-2">
                                <div class="avatar-content">
                                    <i class="feather icon-shopping-bag text-danger font-large-3"></i>
                                </div>
                            </div>
                            <h1 class="text-bold-700"><a href="/limitkredit?status=pending">{{ $jmlpengajuan }}</a></h1>
                            <p class="mb-0 line-ellipsis">Menunggu Persetujuan <br><br><br></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Rekap Penjualan</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row mb-1">
                                <div class="col-12">
                                    <select class="form-control" id="bulanpenjualan">
                                        <?php for ($a = 1; $a <= 12; $a++) { ?>
                                        <option <?php if (date("m") == $a) {
                                                              echo "selected";
                                                            } ?> value="<?php echo $a;  ?>"><?php echo $bulan[$a]; ?></option>
                                        <?php } ?>
                                    </select>

                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-12">
                                    <select class="form-control" id="tahunpenjualan">
                                        <?php
                                                $tahun = date("Y");
                                                for ($t = 2021; $t <= $tahun; $t++) { ?>
                                        <option <?php if (date("Y") == $t) {
                                                              echo "selected";
                                                            } ?> value="<?php echo $t;  ?>"><?php echo $t; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <a href="#" id="tampilkanpenjualancashin" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-sm-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Data Terakhir Penginputan Data</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <table class="table table-hover-animation">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Cabang</th>
                                        <th>Penjualan</th>
                                        <th>Kas Besar</th>
                                        <th>Kas Kecil</th>
                                        <th>Persediaan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lastupdate as $d) { ?>
                                    <tr>
                                        <td><?php echo strtoupper($d->kode_cabang); ?></td>
                                        <td><?php echo (!empty($d->penjualan) ? date("d-m-y",strtotime($d->penjualan)) : "" ); ?></td>
                                        <td><?php echo (!empty($d->kasbesar) ? date("d-m-y",strtotime($d->kasbesar)) : "" ); ?></td>
                                        <td><?php echo (!empty($d->kaskecil) ? date("d-m-y",strtotime($d->kaskecil)) : "" ); ?></td>
                                        <td><?php echo (!empty($d->persediaan) ? date("d-m-y",strtotime($d->persediaan)) : "" ); ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Data Persediaan All Cabang Berdasarkan DPB</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="text-center" id="loadingrekappersediaan">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                            <div id="loadrekappersediaan">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rekap Penjualan -->
<div class="modal fade text-left" id="mdlrekappenjualan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 1300px" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Rekap Penjualan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center" id="loadingrekappenjualan">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="loadrekappenjualan"></div>
                <div class="text-center" id="loadingrekapkasbesar">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="loadrekapkasbesar"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        function loadrekappenjualan() {
            var bulan = $("#bulanpenjualan").val();
            var tahun = $("#tahunpenjualan").val();
            $('#loadingrekappenjualan').show();
            $.ajax({
                type: 'POST'
                , url: '/rekappenjualandashboard'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , bulan: bulan
                    , tahun: tahun
                }
                , cache: false
                , success: function(respond) {
                    $("#loadrekappenjualan").html(respond);
                    $('#loadingrekappenjualan').hide();
                }
            });
        }

        function loadrekapkasbesar() {
            var bulan = $("#bulanpenjualan").val();
            var tahun = $("#tahunpenjualan").val();
            $('#loadingrekapkasbesar').show();
            $.ajax({
                type: 'POST'
                , url: '/rekapkasbesardashboard'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , bulan: bulan
                    , tahun: tahun
                }
                , cache: false
                , success: function(respond) {
                    $("#loadrekapkasbesar").html(respond);
                    $('#loadingrekapkasbesar').hide();
                }
            });
        }



        function loadrekappersediaan() {
            $('#loadrekappersediaan').html("");
            $('#loadingrekappersediaan').show();
            $.ajax({
                type: 'GET'
                , url: '/rekappersediaandashboard'
                , cache: false
                , success: function(respond) {
                    $('#loadingrekappersediaan').hide();
                    $("#loadrekappersediaan").html(respond);
                }
            });
        }

        loadrekappersediaan();
        $("#tampilkanpenjualancashin").click(function(e) {
            e.preventDefault();
            loadrekappenjualan();
            loadrekapkasbesar();
            $('#mdlrekappenjualan').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });


    });

</script>
@endpush
