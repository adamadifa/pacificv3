@extends('layouts.sap.sap')
@section('content')
<style>
    .webcam-capture,
    .webcam-capture video {
        display: inline-block;
        width: 100% !important;
        margin: auto;
        height: auto !important;
        border-radius: 15px;

    }

    #map {
        height: 80px;
        width: 150px;
        position: absolute;
        top: 100px;
        left: 20px;
        opacity: 0.5;

    }

</style>
<style>
    .jam-digital-malasngoding {

        background-color: #27272783;
        position: absolute;
        top: 210px;
        left: 20px;
        z-index: 9999;
        width: 150px;
        border-radius: 10px;
        padding: 5px;
    }



    .jam-digital-malasngoding p {
        color: #fff;
        font-size: 16px;
        text-align: center;
        margin-top: 0;
        margin-bottom: 0;
    }

</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>


<input type="hidden" id="lokasi">

<div class="row" style="margin-top:5px">
    <div class="col">
        <div class="webcam-capture"></div>
    </div>
</div>
<div class="jam-digital-malasngoding">
    <p>{{ Auth::user()->name }}</p>
    <p>{{ DateToIndo2(date('Y-m-d'))}}</p>
    <p id="jam"></p>
    <p id="maptext" style="font-size:12px"></p>
</div>
<div id="map"></div>
<div class="row">
    <div class="col">
        <textarea name="activity" id="activity" cols="30" rows="4" style="background-color:white" placeholder="Input Aktivitas"></textarea>
    </div>
</div>
<div class="row mt-2">
    <div class="col-12">
        <button class="btn w-100" type="submit" name="submit" id="sendactivity" style="background-color:#b11036; color:white">Kirim</button>
    </div>
</div>
@endsection

@push('myscript')
<script type="text/javascript">
    window.onload = function() {
        jam();
    }

    function jam() {
        var e = document.getElementById('jam')
            , d = new Date()
            , h, m, s;
        h = d.getHours();
        m = set(d.getMinutes());
        s = set(d.getSeconds());

        e.innerHTML = h + ':' + m + ':' + s;

        setTimeout('jam()', 1000);
    }

    function set(e) {
        e = e < 10 ? '0' + e : e;
        return e;
    }

</script>
<script>
    Webcam.set({
        height: 480
        , width: 640
        , image_format: 'jpeg'
        , jpeg_quality: 80
    });

    Webcam.attach('.webcam-capture');

    var lokasi = document.getElementById('lokasi');
    var maptext = document.getElementById('maptext');
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
    }

    function successCallback(position) {
        lokasi.value = position.coords.latitude + "," + position.coords.longitude;
        maptext.innerHTML = position.coords.latitude + ",<br>" + position.coords.longitude;
        var map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 18);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
            , attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        var marker = L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);
        setInterval(function() {
            map.invalidateSize();
        }, 100);
    }

    function errorCallback() {

    }
    $("#sendactivity").click(function(e) {
        Webcam.snap(function(uri) {
            image = uri;
        });
        var lokasi = $("#lokasi").val();
        var activity = $("#activity").val();
        if (activity == "") {
            Swal.fire({
                title: 'Berhasil !'
                , text: 'Aktifitas Harus Diisi'
                , icon: 'error'
            })
        } else {
            $("#sendactivity").prop('disabled', true);
            $.ajax({
                type: 'POST'
                , url: '/sap/smactivity/store'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , lokasi: lokasi
                    , activity: activity
                    , image: image
                }
                , cache: false
                , success: function(respond) {
                    var status = respond.split("|");
                    if (status[0] == "success") {
                        Swal.fire({
                            title: 'Berhasil !'
                            , text: status[1]
                            , icon: 'success'
                        })
                        setTimeout("location.href='/sap/smactivity'", 3000);
                    } else {
                        Swal.fire({
                            title: 'Error !'
                            , text: status[1]
                            , icon: 'error'
                        })
                        $("#sendactivity").prop('disabled', false);
                    }
                }
            });
        }

    });

</script>
@endpush
