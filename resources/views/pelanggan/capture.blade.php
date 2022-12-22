@extends('layouts.midone')
@section('titlepage','Capture Pelanggan')
@section('content')
<style>
    .webcam-capture,
    .webcam-capture video {
        display: inline-block;
        width: 100% !important;
        height: 100% !important;
        margin: auto;
        text-align: center;
        border-radius: 15px;
        overflow: hidden;
    }

    #map {
        height: 220px;
        z-index: 0;
    }

</style>
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">{{ $pelanggan->kode_pelanggan }} | {{ $pelanggan->nama_pelanggan }}</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <div class="row">
            <div class="col">
                <span class="latitude d-none" id="latitude"></span>
                <input type="hidden" id="lokasi">
                <div class="webcam-capture">
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col">
                <a href="#" class="btn btn-info shadow-sm w-100 text-white" id="takeabsen"><i class="feather icon-camera mr-1"></i>Capture</a>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div id="map"></div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('myscript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
<script>
    $(document).ready(function() {
        var result = document.getElementById("latitude");
        var lokasi = document.getElementById("lokasi");
        var x = document.getElementById("myAudio");
        var y = document.getElementById("pulang");
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
            var lok = lokasi.value;
            var latlong = lok.split(",");
            var map = L.map('map').setView([latlong[0], latlong[1]], 15);
            L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20
                , subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            }).addTo(map);

            var marker = L.marker([latlong[0], latlong[1]]).addTo(map);
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







        var cameras = new Array(); //create empty array to later insert available devices
        navigator.mediaDevices.enumerateDevices() // get the available devices found in the machine
            .then(function(devices) {
                devices.forEach(function(device) {
                    var i = 0;
                    if (device.kind === "videoinput") { //filter video devices only
                        cameras[i] = device.deviceId; // save the camera id's in the camera array
                        i++;
                    }
                });
            })

        Webcam.set('constraints', {
            width: 590
            , height: 460
            , image_format: 'jpeg'
            , jpeg_quality: 80
            , facingMode: {
                exact: 'environment'
            },

        });

        Webcam.attach('.webcam-capture');

        $("#takeabsen").click(function() {
            Webcam.snap(function(data_uri) {
                console.log(data_uri);
                image = data_uri;
            });
            var latitude = $("#latitude").text();
            var kode_pelanggan = "{{ Crypt::encrypt($pelanggan->kode_pelanggan) }}";
            $("#takeabsen").hide();
            $.ajax({
                type: 'POST'
                , url: '/pelanggan/storecapture'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_pelanggan: kode_pelanggan
                    , image: image
                    , latitude: latitude
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
                            , timer: 3500
                        , });
                        setTimeout("location.href = '/pelanggan/showpelanggan?kode_pelanggan=" + kode_pelanggan + "';", 3600);
                    } else {
                        swal({
                            title: 'Oops!'
                            , text: text
                            , icon: 'error'
                            , timer: 3500
                        , });
                    }
                }
            });
        });


    });

</script>
@endpush
