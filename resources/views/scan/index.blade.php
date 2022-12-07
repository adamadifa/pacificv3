@extends('layouts.midone')
@section('titlepage','Scan QR CODE')
@section('content')
<style>
    #qr-video {
        width: 100%;
    }

</style>

<div class="content-wrapper">

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Scan Qrcode</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Scan Qrcode</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body" id="loadpelanggan">
        <div class="text-center" id="loading">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <input type="hidden" id="status">
        <div class="row">
            <div class="col-12">
                <video id="qr-video"></video>
                <h4 id="cam-qr-result" class="text-center">None</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <select id="cam-list" class="form-control">
                        <option value="environment" selected>Environment Facing (default)</option>
                        <option value="user">User Facing</option>
                    </select>
                </div>
                <div class="form-group">
                    <span id="cam-has-flash" style="visibility: hidden"></span>
                    <button id="flash-toggle" class="btn btn-primary">📸 Flash: <span id="flash-state">off</span></button>
                </div>
                <div class="form-group">
                    <input type="hidden" id="latitude">
                    <input type="hidden" id="longitude">
                </div>
            </div>
        </div>
    </div>
    <audio id="myAudio">
        <source src="{{ asset('app-assets/sound/found.mp3') }}" type="audio/mpeg">
    </audio>
</div>


@endsection

@push('myscript')
<script src="{{ asset('app-assets/js/external/qr-scanner.umd.min.js') }}"></script>
<script src="{{ asset('app-assets/js/external/qr-scanner.legacy.min.js') }}"></script>
<script type="module">
    //import QrScanner from "../qr-scanner.min.js";
    var x = document.getElementById("demo");

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            x.innerHTML = "Geolocation is not supported by this browser.";
        }
    }
    function showPosition(position) {
        const latitude = document.getElementById('latitude').value = position.coords.latitude;
        const longitude = document.getElementById('longitude').value = position.coords.longitude;
        var cek = "Latitude: " + position.coords.latitude +
        "<br>Longitude: " + position.coords.longitude;
        //alert(cek);
    }
    getLocation();

    const video = document.getElementById('qr-video');
    const videoContainer = document.getElementById('video-container');
    const camHasCamera = document.getElementById('cam-has-camera');
    const camList = document.getElementById('cam-list');
    const camHasFlash = document.getElementById('cam-has-flash');
    const flashToggle = document.getElementById('flash-toggle');
    const flashState = document.getElementById('flash-state');
    const camQrResult = document.getElementById('cam-qr-result');
    const camQrResultTimestamp = document.getElementById('cam-qr-result-timestamp');
    const fileSelector = document.getElementById('file-selector');
    const fileQrResult = document.getElementById('file-qr-result');
    const statusResult = document.getElementById('status');
    var x = document.getElementById("myAudio");
    $("#loading").hide();
    function loadpelanggan(kode_pelanggan){
        var status = $("#status").val();
        var latitude = $("#latitude").val();
        var longitude = $("#longitude").val();
        $("#loading").show();
        window.location.href = "/pelanggan/"+kode_pelanggan+"/getpelanggan?latitude="+latitude+"&longitude="+longitude;

        // $.ajax({
        //     type:'POST',
        //     url:'/pelanggan/getpelanggan',
        //     data:{
        //         _token:"{{ csrf_token() }}",
        //         kode_pelanggan:kode_pelanggan,
        //         latitude:latitude,
        //         longitude:longitude
        //     },
        //     success:function(respond){
        //         $("#loading").hide();
        //         if(status == 1){
        //             $("#status").val(2);
        //             $("#loadpelanggan").html(respond);
        //         }
        //     }
        // });
    }
    function setResult(label, result) {
        //console.log(label);
        console.log(result.data);
        label.textContent = result.data;
        // camQrResultTimestamp.textContent = new Date().toString();
        if(result.data != "No QR code found."){
            $("#status").val(1);
            x.play();
            loadpelanggan(result.data);
            scanner.stop();
        }
        label.style.color = 'red';
        // clearTimeout(label.highlightTimeout);
        // label.highlightTimeout = setTimeout(() => label.style.color = 'inherit', 100);
    }

    // ####### Web Cam Scanning #######

    const scanner = new QrScanner(video, result => setResult(camQrResult, result), {
        onDecodeError: error => {
            camQrResult.textContent = error;
            camQrResult.style.color = 'inherit';
        },
        highlightScanRegion: true,
        highlightCodeOutline: true,
    });

    const updateFlashAvailability = () => {
        scanner.hasFlash().then(hasFlash => {
            camHasFlash.textContent = hasFlash;
            flashToggle.style.display = hasFlash ? 'inline-block' : 'none';

        });
    };

    scanner.start().then(() => {
        updateFlashAvailability();
        // List cameras after the scanner started to avoid listCamera's stream and the scanner's stream being requested
        // at the same time which can result in listCamera's unconstrained stream also being offered to the scanner.
        // Note that we can also start the scanner after listCameras, we just have it this way around in the demo to
        // start the scanner earlier.
        QrScanner.listCameras(true).then(cameras => cameras.forEach(camera => {
            const option = document.createElement('option');
            option.value = camera.id;
            option.text = camera.label;
            camList.add(option);
        }));
    });

    // QrScanner.hasCamera().then(hasCamera => camHasCamera.textContent = hasCamera);

    // for debugging
    window.scanner = scanner;

    // document.getElementById('scan-region-highlight-style-select').addEventListener('change', (e) => {
    //     videoContainer.className = e.target.value;
    //     scanner._updateOverlay(); // reposition the highlight because style 2 sets position: relative
    // });

    // document.getElementById('show-scan-region').addEventListener('change', (e) => {
    //     const input = e.target;
    //     const label = input.parentNode;
    //     label.parentNode.insertBefore(scanner.$canvas, label.nextSibling);
    //     scanner.$canvas.style.display = input.checked ? 'block' : 'none';
    // });

    // document.getElementById('inversion-mode-select').addEventListener('change', event => {
    //     scanner.setInversionMode(event.target.value);
    // });

    camList.addEventListener('change', event => {
        scanner.setCamera(event.target.value).then(updateFlashAvailability);
    });

    flashToggle.addEventListener('click', () => {
        scanner.toggleFlash().then(() => flashState.textContent = scanner.isFlashOn() ? 'on' : 'off');
    });

    // document.getElementById('start-button').addEventListener('click', () => {
    //     scanner.start();
    // });

    // document.getElementById('stop-button').addEventListener('click', () => {
    //     scanner.stop();
    // });

    // ####### File Scanning #######

    // fileSelector.addEventListener('change', event => {
    //     const file = fileSelector.files[0];
    //     if (!file) {
    //         return;
    //     }
    //     QrScanner.scanImage(file, { returnDetailedScanResult: true })
    //         .then(result => setResult(fileQrResult, result))
    //         .catch(e => setResult(fileQrResult, { data: e || 'No QR code found.' }));
    // });
</script>
@endpush
