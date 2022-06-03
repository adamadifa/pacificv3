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
            </div>
        </section>
        <div class="row">
            <div class="col-lg-4 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Data Persediaan Good Stok Gudang Cabang</div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row" id="pilihcabang">
                                <div class="col-lg-12 col-sm-12">
                                    <div class="form-group  ">
                                        <select name="cabanggs" id="cabanggs" class="form-control">
                                            @if (Auth::user()->kode_cabang!="PCF")
                                            <option value="">Pilih Cabang</option>
                                            @else
                                            <option value="">Semua Cabang</option>
                                            @endif
                                            @foreach ($cabang as $c)
                                            <option {{ (Auth::user()->kode_cabang ==$c->kode_cabang ? 'selected':'')}} value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center" id="loading">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                    <div id="loadsaldostok"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Data Persediaan Bad Stok Gudang Cabang</div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row" id="pilihcabang">
                                <div class="col-lg-12 col-sm-12">
                                    <div class="form-group  ">
                                        <select name="cabangbs" id="cabangbs" class="form-control">
                                            @if (Auth::user()->kode_cabang!="PCF")
                                            <option value="">Pilih Cabang</option>
                                            @else
                                            <option value="">Semua Cabang</option>
                                            @endif
                                            @foreach ($cabang as $c)
                                            <option {{ (Auth::user()->kode_cabang ==$c->kode_cabang ? 'selected':'')}} value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center" id="loadingbs">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                    <div id="loadsaldostokbs"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="mdldppp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" style="max-width:60%" role="document">
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
<!-- Rekap AUP -->
<div class="modal fade text-left" id="mdlaup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" style="max-width:60%" role="document">
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
@endsection
@push('myscript')
<script>
    $(function() {
        $("#showpending").click(function(e) {
            $("#frmpending").submit();
        });

        function loaddppp() {
            $('#loaddppp').html("");
            $('#loadingdppp').show();
            var cabang = $("#cabangdppp").val();
            var bulan = $("#bulandppp").val();
            var tahun = $("#tahundppp").val();
            $.ajax({
                type: 'POST'
                , url: '/dpppdashboard'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , bulan: bulan
                    , tahun: tahun
                    , cabang: cabang
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

        function loadsaldogs() {
            $('#loadsaldostok').html("");
            $('#loading').show();
            var kode_cabang = $("#cabanggs").val();
            var status = 'GS';
            $.ajax({
                type: 'POST'
                , url: '/getsaldogudangcabang'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , status: status
                }
                , cache: false
                , success: function(respond) {
                    $('#loading').hide();
                    $("#loadsaldostok").html(respond);
                }
            });
        }

        function loadsaldobs() {
            $('#loadsaldostokbs').html("");
            $('#loadingbs').show();
            var kode_cabang = $("#cabangbs").val();
            var status = 'BS';
            $.ajax({
                type: 'POST'
                , url: '/getsaldogudangcabangbs'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , status: status
                }
                , cache: false
                , success: function(respond) {
                    $('#loadingbs').hide();
                    $("#loadsaldostokbs").html(respond);
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
        loadsaldogs();
        loadsaldobs();
        $("#cabanggs").change(function() {
            loadsaldogs();
        });

        $("#cabangbs").change(function() {
            loadsaldobs();
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

        $("#tampilkanaup").click(function(e) {
            e.preventDefault();
            loadaup();
            $('#mdlaup').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

    })

</script>
@endpush
