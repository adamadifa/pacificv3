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
                    <div class="row">
                        <div class="col-12">
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
                    <div class="row">
                        <div class="col-12">
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
        </section>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
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
        loadsaldogs();

        $("#cabanggs").change(function() {
            loadsaldogs();
        });
    });

</script>
@endpush
