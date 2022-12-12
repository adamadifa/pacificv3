@extends('layouts.midone')
@section('titlepage', 'Dashboard')
@section('content')
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
                </div>
                <div class="col-lg-2 col-sm-12 col-md-12">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-start pb-0">
                                    <div>
                                        <h2 class="text-bold-700 mb-0">{{ rupiah($jmlpelanggan) }}</h2>
                                        <p>Pelanggan Aktif</p>
                                    </div>
                                    <div class="avatar bg-rgba-info p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-users text-info font-medium-5"></i>
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
                                        <h2 class="text-bold-700 mb-0">{{ $jmlpelangganhariini }}</h2>
                                        <p>Pelanggan Baru</p>
                                    </div>
                                    <div class="avatar bg-rgba-success p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-users text-success font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-12 col-md-12">
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
                </div>
                <div class="col-lg-3 col-sm-12 col-md-12">
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
                    {{-- <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-start pb-0">
                                    <div>
                                        <h2 class="text-bold-700 mb-0">{{ rupiah($piutang->saldopiutang) }}</h2>
                    <p>Piutang s/d Hari Ini</p>
                </div>
                <div class="avatar bg-rgba-danger p-50 m-0">
                    <div class="avatar-content">
                        <i class="feather icon-dollar-sign text-danger font-medium-5"></i>
                    </div>
                </div>
            </div>
    </div>
</div>
</div> --}}
</div>
</div>
<div class="row">
    <div class="col-lg-5 col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Pencapaian Target</h4>
            </div>
            <div class="card-body">
                <div class="row" id="pilihbulan">
                    <div class="col-12">
                        {{-- <label for="" class="form-label mb-1">Omset Bulan</label> --}}
                        <div class="form-group" style="margin-bottom: 5px">
                            <select class="form-control" id="bulan" name="bulan">
                                <option value="">Bulan</option>
                                <?php
                                            $bulanini = date("m");
                                            for ($i = 1; $i < count($bulan); $i++) {
                                            ?>
                                <option <?php if ($bulanini == $i) {echo "selected";} ?> value="<?php echo $i; ?>"><?php echo $bulan[$i]; ?></option>
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
                                <option <?php if (date('Y') == $thn) { echo "Selected";} ?> value="<?php echo $thn; ?>"><?php echo $thn; ?></option>
                                <?php
                                            }
                                            ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Barang</th>
                                    <th>Target</th>
                                    <th>Realisasi</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody id="loadrealisasitargetsales"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7 col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Grafik Penjualan</h4>
            </div>
            <div class="card-body">
                <!-- Line Area Chart -->
                <div id="line-area-chart"></div>
            </div>
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
        var $primary = '#7367F0'
            , $success = '#28C76F'
            , $danger = '#EA5455'
            , $warning = '#FF9F43'
            , $info = '#00cfe8'
            , $label_color_light = '#dae1e7';

        var themeColors = [$primary, $success, $danger, $warning, $info];

        // RTL Support
        var yaxis_opposite = false;
        if ($('html').data('textdirection') == 'rtl') {
            yaxis_opposite = true;
        }

        var bln = <?php echo json_encode($bln) ?> ;
        var totalpenjnow = <?php echo json_encode($totalpenjnow) ?> ;
        var totalpenjlast = <?php echo json_encode($totalpenjlast) ?> ;

        // Line Area Chart
        // ----------------------------------
        var lineAreaOptions = {
            chart: {
                height: 350
                , type: 'area'
            , }
            , colors: themeColors
            , dataLabels: {
                enabled: false
            }
            , stroke: {
                curve: 'smooth'
            }
            , series: [{
                name: '2022'
                , data: totalpenjnow
            }, {
                name: '2022'
                , data: totalpenjlast
            }]
            , legend: {
                offsetY: -10
            }
            , xaxis: {
                categories: bln
            }
            , yaxis: {
                labels: {
                    formatter: function(value) {
                        var val = addCommas(value);
                        return val
                    }
                }
                , opposite: yaxis_opposite
            }

        }
        var lineAreaChart = new ApexCharts(
            document.querySelector("#line-area-chart")
            , lineAreaOptions
        );
        lineAreaChart.render();

        function loadrealisasitargetsales() {
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            $.ajax({
                type: 'POST'
                , url: '/getrealisasitargetsales'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , bulan: bulan
                    , tahun: tahun
                }
                , cache: false
                , success: function(respond) {
                    $("#loadrealisasitargetsales").html(respond);
                }
            });
        }

        loadrealisasitargetsales();

        $("#bulan").change(function() {
            loadrealisasitargetsales();
        });
    });

</script>
@endpush
