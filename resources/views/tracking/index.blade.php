@extends('layouts.midone')
@section('titlepage','Tracking Salesman')
@section('content')


<style>
    #map {
        height: 800px;
    }

</style>
<div class="content-wrapper">

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Tracking Salesmman</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Tracking Salesman</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <div class="row">
            <div class="col-12">
                <div id="map"></div>
            </div>
        </div>
    </div>
</div>


@endsection
@push('myscript')
<script>
    var map = L.map('map').setView([-7.3665114, 108.2148793], 14);
    // L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    //     maxZoom: 19
    //     , attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    // }).addTo(map);

    L.tileLayer('http://{s}.google.com/vt?lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20
        , subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    }).addTo(map);

    L.marker([-7.3665114, 108.2148793]).addTo(map);

    $(document).ready(function() {
        $.getJSON('/getlocationcheckin', function(data) {
            $.each(data, function(index) {
                var salesmanicon = L.icon({
                    iconUrl: 'app-assets/marker/adam.png'
                    , iconSize: [60, 75], // size of the icon
                    shadowSize: [50, 64], // size of the shadow
                    iconAnchor: [22, 94], // point of the icon which will correspond to marker's location
                    shadowAnchor: [4, 62], // the same for the shadow
                    popupAnchor: [-3, -76] // point from which the popup should open relative to the iconAnchor
                });

                var imagepath = "{{ Storage::url('pelanggan/') }}" + data[index].foto;

                L.marker([parseFloat(data[index].latitude), parseFloat(data[index].longitude)], {
                    icon: salesmanicon
                }).bindPopup("<b>" + data[index].kode_pelanggan + " - " + data[index].nama_pelanggan + "</b><br><br>" + "<img width='200px' src='" + imagepath + "'/><br><br>" + "Latitude : " + data[index].latitude + " <br>Longitude : " + data[index].longitude + "<br> Alamat :" + data[index].alamat_pelanggan, {
                    maxWidth: 200
                }).addTo(map);
            });
        });
    });

</script>
@endpush
