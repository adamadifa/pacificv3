<style>
    #map {
        height: 300px;
        width: 100%;
    }

</style>
@if (!empty($smactivity->foto))
@php
$path = Storage::url('uploads/smactivity/'.$smactivity->foto);
@endphp
<div class="foto">
    <img src="{{ url($path) }}" class="card-img" alt="">
</div>
@endif
<div id="map" class="mt-2"></div>
<script>
    var latitude = "{{ $smactivity->latitude }}";
    var longitude = "{{ $smactivity->longitude }}";


    var map = L.map('map', {
        center: [latitude, longitude]
        , zoom: 15
    });

    L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20
        , subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    }).addTo(map);
    // L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    //     maxZoom: 19
    //     , attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    // }).addTo(map);
    var marker = L.marker([latitude, longitude]).addTo(map);


    setInterval(function() {
        map.invalidateSize();
    }, 100);

</script>
