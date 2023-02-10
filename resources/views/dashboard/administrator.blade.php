@extends('layouts.midone')
@section('titlepage', 'Dashboard')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
    </div>
    <div class="content-body">
        <!-- Dashboard Analytics Start -->
        {{-- <a href="/cetakstruk" class="btn btn-primary">Cetak</a> --}}
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
                                    <h3 class="mb-1 text-white">Selamat Datang,{{ Auth::user()->id }} {{ Auth::user()->name }} </h3>
                                    <h4 class="text-white">{{ date('d F Y H:i:s') }} </h4>
                                    <p class="m-auto w-75">Anda Masuk Sebagai Level {{ ucwords(Auth::user()->level) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                <p class="mb-0 line-ellipsis">Menunggu Persetujuan {{ ucwords(Auth::user()->level) }}<br><br><br></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-md-12 col-sm-12">
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
                                                for ($t = 2019; $t <= $tahun; $t++) { ?>
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
        </section>
        <section>
            <div class="row">
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Rekap Analisa Umur Piutang (AUP)</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-body">
                                    <div class="row mb-1">
                                        <div class="col-12">
                                            <select class="form-control" id="cabangaup">
                                                <option value="">Semua Cabang</option>
                                                @foreach ($cabang as $d)
                                                <option value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            @php
                                            $hariini = date("Y-m-d");
                                            @endphp
                                            <x-inputtext label="Tanggal AUP" field="tanggal_aup" icon="feather icon-calendar" datepicker="true" value="{{ $hariini }}" />
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-12">
                                            <select class="form-control" id="exclude">
                                                <option value="yes">Exclude Pusat</option>
                                                <option value="no">Include Pusat</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <a href="#" id="tampilkanaup" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Rekap DPPP</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-body">
                                    <div class="row mb-1">
                                        <div class="col-12">
                                            <select class="form-control" id="cabangdppp">
                                                <option value="">Semua Cabang</option>
                                                @foreach ($cabang as $d)
                                                <option value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-12">
                                            <select class="form-control" id="bulandppp">
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
                                            <select class="form-control" id="tahundppp">
                                                <?php
                                                    $tahun = date("Y");
                                                    for ($t = 2019; $t <= $tahun; $t++) { ?>
                                                <option <?php if (date("Y") == $t) {
                                                                  echo "selected";
                                                                } ?> value="<?php echo $t;  ?>"><?php echo $t; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-12">
                                            <select class="form-control" name="statusdppp" id="statusdppp">
                                                <option value="2">Berdasarkan Tunai Kredit / Omset</option>
                                                <option value="1">Berdasarkan Selling Out</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <a href="#" id="tampilkandppp" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Rekap Kendaraan</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-body">
                                    <div class="row mb-1">
                                        <div class="col-12">
                                            <select class="form-control" id="cabangkendaraan">
                                                <option value="">Semua Cabang</option>
                                                @foreach ($cabang as $d)
                                                <option value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-12">
                                            <select class="form-control" id="bulankendaraan">
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
                                            <select class="form-control" id="tahunkendaraan">
                                                <?php
                                                    $tahun = date("Y");
                                                    for ($t = 2019; $t <= $tahun; $t++) { ?>
                                                <option <?php if (date("Y") == $t) {
                                                                  echo "selected";
                                                                } ?> value="<?php echo $t;  ?>"><?php echo $t; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <a href="#" id="tampilkankendaraan" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
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
        </section>
    </div>
    </section>
</div>
</div>

<!-- Rekap Penjualan -->
<!-- Rekap Penjualan -->
<div class="modal fade text-left" id="mdlrekappenjualan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 1400px" role="document">
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
<!-- Rekap AUP -->
<div class="modal fade text-left" id="mdlaup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel16">Rekap Analisa Umur Piutang (AUP)</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center" id="loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="loadaup">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="mdldppp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel16">Rekap DPPP</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center" id="loadingdppp">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="loaddppp">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mdlkendaraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalScrollableTitle">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center" id="loadingkendaraan">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="loadrekapkendaraan">
                </div>
            </div>
            <div class="modal-footer">

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

        function loadaup() {
            $('#loadaup').html("");
            $('#loading').show();
            var cabang = $("#cabangaup").val();
            var tanggal_aup = $("#tanggal_aup").val();
            var exclude = $("#exclude").val();
            if (cabang == "") {
                var address = '/aupdashboardall';
            } else {
                var address = '/aupdashboardcabang'
            }
            $.ajax({
                type: 'POST'
                , url: address
                , data: {
                    _token: "{{ csrf_token() }}"
                    , cabang: cabang
                    , tanggal_aup: tanggal_aup
                    , exclude: exclude
                }
                , cache: false
                , success: function(respond) {
                    $('#loading').hide();
                    $("#loadaup").html(respond);
                }
            });
        }


        function loaddppp() {
            $('#loaddppp').html("");
            $('#loadingdppp').show();
            var cabang = $("#cabangdppp").val();
            var bulan = $("#bulandppp").val();
            var tahun = $("#tahundppp").val();
            var statusdppp = $("#statusdppp").val();
            $.ajax({
                type: 'POST'
                , url: '/dpppdashboard'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , bulan: bulan
                    , tahun: tahun
                    , cabang: cabang
                    , statusdppp: statusdppp
                }
                , cache: false
                , success: function(respond) {
                    $('#loadingdppp').hide();
                    $("#loaddppp").html(respond);
                }
            });
        }

        function loadrekapkendaraan() {
            $('#loadrekapkendaraan').html("");
            $('#loadingkendaraan').show();
            var cabang = $("#cabangkendaraan").val();
            var bulan = $("#bulankendaraan").val();
            var tahun = $("#tahunkendaraan").val();
            $.ajax({
                type: 'POST'
                , url: '/rekapkendaraandashboard'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , bulan: bulan
                    , tahun: tahun
                    , cabang: cabang
                }
                , cache: false
                , success: function(respond) {
                    $('#loadingkendaraan').hide();
                    $("#loadrekapkendaraan").html(respond);
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

        $("#tampilkanaup").click(function(e) {
            e.preventDefault();
            loadaup();
            $('#mdlaup').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        $("#tampilkandppp").click(function(e) {
            e.preventDefault();
            loaddppp();
            $('#mdldppp').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        $("#tampilkankendaraan").click(function(e) {
            e.preventDefault();
            loadrekapkendaraan();
            $('#mdlkendaraan').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });
    });

</script>
@endpush
