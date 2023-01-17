@extends('layouts.midone')
@section('content')
<style>
    .card {
        margin-bottom: 0.8rem !important;
    }

</style>
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h4 class="content-header-title float-left mb-0">Data Pelanggan</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body" id="loadpelanggan">
        <audio id="myAudio">
            <source src="{{ asset('app-assets/sound/found.mp3') }}" type="audio/mpeg">
        </audio>
        <div class="row">
            <div class="col-lg-3 col-sm-12">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-content">
                                @if ($pelanggan->foto != null)
                                <img class="card-img-top img-fluid" style="height: 200px; object-fit:cover" id="foto" src="{{ url(Storage::url('pelanggan/'.$pelanggan->foto)) }}" alt="Card image cap">
                                @else
                                <img class="card-img-top img-fluid" id="foto" src="{{ asset('app-assets/images/slider/04.jpg') }}" alt="Card image cap">
                                @endif
                                <div class="card-body">
                                    <h4 class="card-title">
                                        <span id="pelanggan_text"></span>
                                    </h4>
                                    <b>Kode Pelanggan</b>
                                    <p class="card-text" id="kode_pelanggan">{{ $pelanggan->kode_pelanggan }}</p>
                                    <b>Nama Pelanggan</b>
                                    <p class="card-text" id="nama_pelanggan">{{ $pelanggan->nama_pelanggan }}</p>
                                    <b>Alamat</b>
                                    <p class="card-text" id="alamat_text">{{ $pelanggan->alamat_pelanggan }}</p>
                                    <b>No. HP</b>
                                    <p class="card-text" id="no_hp">{{ $pelanggan->no_hp }}</p>
                                    {{-- <b>Koordinat</b>
                                    <p class="card-text" id="koordinat">{{ $pelanggan->latitude }},{{ $pelanggan->longitude }}</p>
                                    <b>Limit Pelanggan</b>
                                    <p class="card-text" id="limitpelanggan">{{ rupiah($pelanggan->limitpel) }}</p>
                                    <b>Piutang Pelanggan</b>
                                    <p class="card-text" id="piutangpelanggan">{{ rupiah($piutang->sisapiutang) }}</p>
                                    <b>Sisa Limit</b>
                                    <p class="card-text" id="sisalimit">{{ rupiah($pelanggan->limitpel - $piutang->sisapiutang) }}</p> --}}

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    @if ($salesmancheckin == 0)
                    <div class="col-12" id="checkinsection">
                        <span id="latitude" class="d-none"></span>
                        <input type="hidden" id="lokasi">
                        <a href="#" id="checkin" class="btn btn-success btn-block"><i class="feather icon-lock mr-1"></i>Checkin</a>
                        <a class="btn btn-primary btn-block" href="/pelanggan/{{ Crypt::encrypt($pelanggan->kode_pelanggan) }}/capturetoko"><i class="feather icon-camera"></i> Capture Toko</a>
                        <a class="btn btn-info btn-block" href="/pelanggan/{{\Crypt::encrypt($pelanggan->kode_pelanggan)}}/edit"><i class="feather icon-edit"></i> Edit Pelanggan</a>
                    </div>

                    @else
                    @if (!empty(Cookie::get('kodepelanggan')))
                    @if (Crypt::decrypt(Cookie::get('kodepelanggan')) == $pelanggan->kode_pelanggan)
                    <div class="col-6">
                        <a href="/inputpenjualanv2" class="btn btn-success btn-block"><i class="feather icon-shopping-cart mr-1"></i>Penjualan</a>
                    </div>
                    <div class="col-6">
                        <a href="/retur/createv2" class="btn btn-danger btn-block"><i class="feather icon-refresh-cw mr-1"></i>Retur</a>
                    </div>
                    @else
                    <div class="col-12" id="checkinsection">
                        <span id="latitude" class="d-none"></span>
                        <input type="hidden" id="lokasi">
                        <a href="#" id="checkin" class="btn btn-success btn-block"><i class="feather icon-lock mr-1"></i>Checkin</a>
                    </div>
                    @endif
                    @else
                    <div class="col-12" id="checkinsection">
                        <span id="latitude" class="d-none"></span>
                        <input type="hidden" id="lokasi">
                        <a href="#" id="checkin" class="btn btn-success btn-block"><i class="feather icon-lock mr-1"></i>Checkin</a>
                    </div>
                    @endif
                    @endif

                </div>

            </div>
            @if ($salesmancheckin != 0)
            <div class="col-lg-9 col-sm-12">
                <div class="app-fixed-search mb-2">
                    <form action="{{ url()->current() }}" method="GET">
                        <input type="hidden" name="kode_pelanggan" value="{{ Crypt::encrypt($pelanggan->kode_pelanggan) }}">
                        <fieldset class="form-group position-relative has-icon-left m-0 mb-1">
                            <input type="text" class="form-control" name="no_fak_penj" value="{{ Request('no_fak_penj') }}" id="nama_pelanggan" placeholder="Cari No. Faktur" autocomplete="off">
                            <div class="form-control-position">
                                <i class="feather icon-search"></i>
                            </div>
                        </fieldset>
                        <button class="btn btn-primary btn-block"><i class="feather icon-search"></i> Cari</button>
                    </form>
                </div>

                @foreach ($penjualan as $d)
                <a href="/penjualan/{{ Crypt::encrypt($d->no_fak_penj) }}/showforsales" style="color: inherit">
                    <div class="row">
                        <div class="col-12">
                            <div class="card {{ $d->status_lunas == 1 ? 'border-primary' : 'bg-gradient-danger' }}">
                                <div class="card-content">
                                    <div class="card-body" style="padding:8px 10px 8px 8px !important">
                                        <p class="card-text d-flex justify-content-between">
                                            <span class="d-flex justify-content-between">
                                                <span>
                                                    <b>{{ $d->no_fak_penj }}</b> <br> {{ DateToIndo2($d->tgltransaksi) }}
                                                </span>
                                            </span>
                                            <span style="text-align: right">
                                                @if ($d->jenistransaksi=="tunai")
                                                <span class="badge bg-success">Tunai</span>
                                                @else
                                                <span class="badge bg-warning">Kredit</span>
                                                @endif
                                                <br>
                                                <span style="font-weight: bold">{{rupiah($d->total)}}</span>
                                                {{-- <span class="badge bg-success">{{ date("H:i:s",strtotime($d->checkin_time)) }}</span> <br>
                                            <span class="badge bg-info">{{ !empty($d->checkout_time) ? date("H:i",strtotime($d->checkout_time)) : 0 }}</span> --}}
                                            </span>

                                        </p>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
                {{ $penjualan->links('vendor.pagination.vuexy') }}
            </div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection
@push('myscript')
<script>
    $(document).ready(function() {
        var x = document.getElementById("myAudio");
        var kode_pelanggan = "{{ Cookie::get('kodepelanggan') }}";
        var result = document.getElementById("latitude");
        var lokasi = document.getElementById("lokasi");
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(successCallback, errorCallback);

        } else {
            swal({
                title: 'Oops!'
                , text: 'Maaf, browser Anda tidak mendukung geolokasi HTML5.'
                , icon: 'error'
                , timer: 3000
            , });
        }

        function successCallback(position) {
            result.innerHTML = "" + position.coords.latitude + "," + position.coords.longitude + "";
            lokasi.value = "" + position.coords.latitude + "," + position.coords.longitude + "";
        }


        // Define callback function for failed attempt
        function errorCallback(error) {
            if (error.code == 1) {
                swal({
                    title: 'Oops!'
                    , text: 'Anda telah memutuskan untuk tidak membagikan posisi Anda, tetapi tidak apa-apa. Kami tidak akan meminta Anda lagi.'
                    , icon: 'error'
                    , timer: 3000
                , });
            } else if (error.code == 2) {
                swal({
                    title: 'Oops!'
                    , text: 'Jaringan tidak aktif atau layanan penentuan posisi tidak dapat dijangkau.'
                    , icon: 'error'
                    , timer: 3000
                , });
            } else if (error.code == 3) {
                swal({
                    title: 'Oops!'
                    , text: 'Waktu percobaan habis sebelum bisa mendapatkan data lokasi.'
                    , icon: 'error'
                    , timer: 3000
                , });
            } else {
                swal({
                    title: 'Oops!'
                    , text: 'Waktu percobaan habis sebelum bisa mendapatkan data lokasi.'
                    , icon: 'error'
                    , timer: 3000
                , });
            }
        }
        if (kode_pelanggan == "") {
            x.play();
        }
        $("#transactionsection").hide();

        $("#checkin").click(function() {
            var lokasi = $("#lokasi").val();
            var kode_pelanggan = "{{ $pelanggan->kode_pelanggan }}";
            $("#checkinsection").hide();
            $.ajax({
                type: 'POST'
                , url: '/pelanggan/checkinstore'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_pelanggan: kode_pelanggan
                    , lokasi: lokasi
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    var result = respond.split("|");
                    if (result[0] == 'success') {
                        swal({
                            title: 'Berhasil!'
                            , text: result[1]
                            , icon: 'success'
                            , timer: 2000
                        , }).then(() => {
                            /* Read more about isConfirmed, isDenied below */
                            location.reload();
                        });
                        //$("#transactionsection").show();
                    } else {
                        swal({
                            title: 'Oops!'
                            , text: respond
                            , icon: 'error'

                        , }).then(() => {
                            /* Read more about isConfirmed, isDenied below */
                            location.reload();
                        });
                    }
                }
            });
        });
    });

</script>
@endpush
