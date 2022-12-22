@extends('layouts.midone')
@section('titlepage','Map Pelanggan')
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
                    <h2 class="content-header-title float-left mb-0">Map Pelanggan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Map Pelanggan</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <form action="">
            <div class="row">
                <div class="col-lg-3">
                    <x-inputtext label="Tanggal" field="tanggal" icon="feather icon-calendar" value="{{ date('Y-m-d') }}" datepicker />
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <select name="kode_cabang" id="kode_cabang" class="form-control">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabang as $d)
                            <option {{ Request('kode_cabang')==$d->kode_cabang ? 'selected' : '' }} value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <select name="id_karyawan" id="id_karyawan" class="form-control">
                            <option value="">Pilih Salesman</option>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary"><i class="feather icon-search"></i></button>
                    </div>
                </div>
            </div>
        </form>
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
    var map = L.map('map', {
        preferCanvas: true
    }).setView([-7.3665114, 108.2148793], 14);
    var layerGroup = L.layerGroup();
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
        var tgl = $("#tanggal").val();
        var cbg = $("#kode_cabang").val();


        function show(tanggal, kode_cabang) {
            if (map.hasLayer(layerGroup)) {
                console.log('already have one, clear it');
                layerGroup.clearLayers();
            } else {
                console.log('never have it before');
            }
            $.getJSON('/getmappelanggan?tanggal=' + tanggal + '&kode_cabang=' + kode_cabang, function(data) {
                $.each(data, function(index) {
                    var salesmanicon = L.icon({
                        iconUrl: 'app-assets/marker/customer-pin.png'
                        , iconSize: [60, 75], // size of the icon
                        shadowSize: [50, 64], // size of the shadow
                        iconAnchor: [22, 94], // point of the icon which will correspond to marker's location
                        shadowAnchor: [4, 62], // the same for the shadow
                        popupAnchor: [-3, -76] // point from which the popup should open relative to the iconAnchor
                    });

                    var imagepath = "{{ Storage::url('pelanggan/') }}" + data[index].foto;

                    var marker = L.marker([parseFloat(data[index].latitude), parseFloat(data[index].longitude)], {
                        icon: salesmanicon
                    }).bindPopup("<b>" + data[index].kode_pelanggan + " - " + data[index].nama_pelanggan + "</b><br><br>" + "<img width='200px' src='" + imagepath + "'/><br><br>" + "Latitude : " + data[index].latitude + " <br>Longitude : " + data[index].longitude + "<br> Alamat :" + data[index].alamat_pelanggan, {
                        maxWidth: 200
                    });
                    layerGroup.addLayer(marker);
                    map.addLayer(layerGroup);
                });
            });
        }


        $("#kode_cabang").change(function() {
            var kode_cabang = $(this).val();
            show(tgl, kode_cabang);
        });

        show(tgl, cbg);
    });

</script>
@endpush
