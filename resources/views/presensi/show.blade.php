<style>
    #map {
        height: 250px;
    }

</style>
@if (!empty($presensi->foto_in))
<div class="foto">
    <img src="https://presensi.pacific-tasikmalaya.com/storage/uploads/absensi/{{ $presensi->foto_in }}" class="card-img" alt="">
</div>
@endif

<div id="map" class="mt-2"></div>
<script>
    var lokasi = "{{ $presensi->lokasi_in }}";
    var lok = lokasi.split(",");
    var latitude = lok[0];
    var longitude = lok[1];

    var latitude_kantor = "{{ $latitude }}";
    var longitude_kantor = "{{ $longitude }}";

    var rd = "{{ $presensi->radius_cabang }}";
    var map = L.map('map').setView([latitude, longitude], 18);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
        , attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
    var marker = L.marker([latitude, longitude]).addTo(map);
    var circle = L.circle([latitude_kantor, longitude_kantor], {
        color: 'red'
        , fillColor: '#f03'
        , fillOpacity: 0.5
        , radius: rd
    }).addTo(map);

</script>
