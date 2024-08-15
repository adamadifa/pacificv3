@extends('layouts.midone')
@section('titlepage', 'Dashboard')
@section('content')
    <style>
        .card {
            margin-bottom: 1rem !important;
        }
    </style>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <!-- Dashboard Analytics Start -->
            <section id="dashboard-analytics">
                <div class="row">
                    <div class="col-lg-4 col-md-12 col-sm-12">
                        <div class="card bg-analytics text-white">
                            <div class="card-content">
                                <div class="card-body text-center">
                                    <img src="{{ asset('app-assets/images/elements/decore-left.png') }}" class="img-left" alt="card-img-left">
                                    <img src="{{ asset('app-assets/images/elements/decore-right.png') }}" class="img-right" alt="card-img-right">
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
                    </div>
            </section>
            <section id="statistic">
                <div class="row">
                    <div class="col-6">
                        <div class="card text-center">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="avatar bg-rgba-info p-50 m-0 mb-1">
                                        <div class="avatar-content">
                                            <i class="feather icon-users text-info font-medium-5"></i>
                                        </div>
                                    </div>
                                    <h2 class="text-bold-700">{{ rupiah($jmlpelanggan) }}</h2>
                                    <p class="mb-0 line-ellipsis">Pelanggan Aktif</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card text-center">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="avatar bg-rgba-success p-50 m-0 mb-1">
                                        <div class="avatar-content">
                                            <i class="feather icon-users text-success font-medium-5"></i>
                                        </div>
                                    </div>
                                    <h2 class="text-bold-700">{{ rupiah($jmlpelangganhariini) }}</h2>
                                    <p class="mb-0 line-ellipsis">Pelanggan Baru</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-start pb-0">
                                <div>
                                    <h2 class="text-bold-700 mb-0">{{ rupiah($penjualanhariini->totalpenjualan) }}</h2>
                                    <p>Penjualan Hari ini</p>
                                </div>
                                <div class="avatar bg-rgba-success p-50 m-0">
                                    <div class="avatar-content">
                                        <i class="feather icon-shopping-bag text-success font-medium-5"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-start pb-0">
                                <div>
                                    <h2 class="text-bold-700 mb-0">{{ rupiah($bayarhariini->totalbayar) }}</h2>
                                    <p>Pembayaran Hari Ini</p>
                                </div>
                                <div class="avatar bg-rgba-primary p-50 m-0">
                                    <div class="avatar-content">
                                        <i class="feather icon-dollar-sign text-primary font-medium-5"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-start pb-0">
                                <div>
                                    <h2 class="text-bold-700 mb-0">{{ rupiah($jmltransaksi) }}</h2>
                                    <p>Transaksi Hari ini</p>
                                </div>
                                <div class="avatar bg-rgba-warning p-50 m-0">
                                    <div class="avatar-content">
                                        <i class="feather icon-shopping-bag text-warning font-medium-5"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section id="target">
                <div class="row">

                    <div class="col-12">
                        <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab-justified" data-toggle="tab" href="#home-just" role="tab"
                                    aria-controls="home-just" aria-selected="true">Realisasi</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab-justified" data-toggle="tab" href="#profile-just" role="tab"
                                    aria-controls="profile-just" aria-selected="true">Histori</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab-justified" data-toggle="tab" href="#dpb" role="tab"
                                    aria-controls="profile-just" aria-selected="true">DPB</a>
                            </li>
                        </ul>
                        <div class="tab-content pt-1">
                            <div class="tab-pane active" id="home-just" role="tabpanel" aria-labelledby="home-tab-justified">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group" style="margin-bottom: 5px">
                                            <select class="form-control" id="bulan" name="bulan">
                                                <option value="">Bulan</option>
                                                <?php
                                                $bulanini = date("m");
                                                for ($i = 1; $i < count($bulan); $i++) {
                                                ?>
                                                <option <?php if ($bulanini == $i) {
                                                    echo 'selected';
                                                } ?> value="<?php echo $i; ?>"><?php echo $bulan[$i]; ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select class="form-control" id="tahun" name="tahun">
                                                <?php
                                                $tahunmulai = 2020;
                                                for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                                                ?>
                                                <option <?php if (date('Y') == $thn) {
                                                    echo 'Selected';
                                                } ?> value="<?php echo $thn; ?>"><?php echo $thn; ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12" id="loadrealisasitargetsales">
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="profile-just" role="tabpanel" aria-labelledby="profile-tab-justified">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group" style="margin-bottom: 5px">
                                            <x-inputtext label="Tanggal" field="tanggalkunjungan" icon="feather icon-calendar" datepicker
                                                value="{{ date('Y-m-d') }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12" id="loadkunjungan"></div>
                                </div>
                            </div>
                            <div class="tab-pane" id="dpb" role="tabpanel" aria-labelledby="dpb-tab-justified">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group" style="margin-bottom: 5px">
                                            <x-inputtext label="Tanggal" field="tgl_dpb" icon="feather icon-calendar" datepicker
                                                value="{{ date('Y-m-d') }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12" id="loaddpb"></div>
                                </div>
                            </div>

                        </div>

            </section>
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        $(function() {

            function addCommas(nStr) {
                nStr += '';
                x = nStr.split('.');
                x1 = x[0];
                x2 = x.length > 1 ? '.' + x[1] : '';
                var rgx = /(\d+)(\d{3})/;
                while (rgx.test(x1)) {
                    x1 = x1.replace(rgx, '$1' + '.' + '$2');
                }
                return x1 + x2;
            }
            // var $primary = '#7367F0'
            //     , $success = '#28C76F'
            //     , $danger = '#EA5455'
            //     , $warning = '#FF9F43'
            //     , $info = '#00cfe8'
            //     , $label_color_light = '#dae1e7';

            // var themeColors = [$primary, $success, $danger, $warning, $info];

            // // RTL Support
            // var yaxis_opposite = false;
            // if ($('html').data('textdirection') == 'rtl') {
            //     yaxis_opposite = true;
            // }

            // var bln = < ? php echo json_encode($bln) ? > ;
            // var totalpenjnow = < ? php echo json_encode($totalpenjnow) ? > ;
            // var totalpenjlast = < ? php echo json_encode($totalpenjlast) ? > ;

            // // Line Area Chart
            // // ----------------------------------
            // var lineAreaOptions = {
            //     chart: {
            //         height: 350
            //         , type: 'area'
            //     , }
            //     , colors: themeColors
            //     , dataLabels: {
            //         enabled: false
            //     }
            //     , stroke: {
            //         curve: 'smooth'
            //     }
            //     , series: [{
            //         name: '2022'
            //         , data: totalpenjnow
            //     }, {
            //         name: '2022'
            //         , data: totalpenjlast
            //     }]
            //     , legend: {
            //         offsetY: -10
            //     }
            //     , xaxis: {
            //         categories: bln
            //     }
            //     , yaxis: {
            //         labels: {
            //             formatter: function(value) {
            //                 var val = addCommas(value);
            //                 return val
            //             }
            //         }
            //         , opposite: yaxis_opposite
            //     }

            // }
            // var lineAreaChart = new ApexCharts(
            //     document.querySelector("#line-area-chart")
            //     , lineAreaOptions
            // );
            // lineAreaChart.render();

            function loadrealisasitargetsales() {
                var bulan = $("#bulan").val();
                var tahun = $("#tahun").val();
                $.ajax({
                    type: 'POST',
                    url: '/getrealisasitargetsales',
                    data: {
                        _token: "{{ csrf_token() }}",
                        bulan: bulan,
                        tahun: tahun
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loadrealisasitargetsales").html(respond);
                    }
                });
            }


            function loadkunjungan() {
                //var bulan = $("#bulan").val();
                //var tahun = $("#tahun").val();
                var tanggalkunjungan = $("#tanggalkunjungan").val();
                $.ajax({
                    type: 'POST',
                    url: '/getkunjungan',
                    data: {
                        _token: "{{ csrf_token() }}",
                        tanggalkunjungan: tanggalkunjungan
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loadkunjungan").html(respond);
                    }
                });
            }

            function loaddpb() {
                //var bulan = $("#bulan").val();
                //var tahun = $("#tahun").val();
                var tgl_dpb = $("#tgl_dpb").val();
                $.ajax({
                    type: 'POST',
                    url: '/getdpb',
                    data: {
                        _token: "{{ csrf_token() }}",
                        tgl_dpb: tgl_dpb
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loaddpb").html(respond);
                    }
                });
            }

            loaddpb();

            loadkunjungan();

            loadrealisasitargetsales();

            $("#tanggalkunjungan").change(function(e) {
                loadkunjungan();
            });

            $("#tgl_dpb").change(function(e) {
                loaddpb();
            });
            $("#bulan, #tahun").change(function() {
                loadrealisasitargetsales();
            });
        });
    </script>
@endpush
