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

    L.tileLayer("http://{s}.tile.osm.org/{z}/{x}/{y}.png").addTo(map);

    L.marker([-7.3665114, 108.2148793]).addTo(map);

    $(document).ready(function() {
        function show(kode_cabang, id_salesman) {
            if (map.hasLayer(layerGroup)) {
                console.log('already have one, clear it');
                layerGroup.clearLayers();
            } else {
                console.log('never have it before');
            }
            $.getJSON('/getmappelanggan?kode_cabang=' + kode_cabang + '&id_salesman=' + id_salesman, function(data) {
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

                    var marker = L.circleMarker([parseFloat(data[index].latitude), parseFloat(data[index].longitude)]).bindPopup("<b>" + data[index].kode_pelanggan + " - " + data[index].nama_pelanggan + "</b><br><br>" + "<img width='200px' src='" + imagepath + "'/><br><br>" + "Latitude : " + data[index].latitude + " <br>Longitude : " + data[index].longitude + "<br> Alamat :" + data[index].alamat_pelanggan, {
                        maxWidth: 200
                    });
                    marker.setStyle({
                        fillColor: '#' + data[index].colormarker
                        , color: '#' + data[index].colormarker
                    });
                    layerGroup.addLayer(marker);
                    map.addLayer(layerGroup);
                });
            });
        }


        $("#kode_cabang").change(function() {
            var kode_cabang = $(this).val();
            var id_salesman = $("#id_karyawan").val();
            loadsalesmancabang(kode_cabang);
            show(kode_cabang, id_salesman);
        });

        $("#id_karyawan").change(function() {
            var id_salesman = $(this).val();
            var kode_cabang = $("#kode_cabang").val();
            show(kode_cabang, id_salesman);
        });




        show("", "");

        function loadsalesmancabang(kode_cabang) {
            $.ajax({
                type: 'POST'
                , url: '/salesman/getsalescab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                }
                , cache: false
                , success: function(respond) {
                    $("#id_karyawan").html(respond);
                }
            });
        }
    });

</script>
@endpush
