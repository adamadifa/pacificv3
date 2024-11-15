<style>
    #map {
        height: 300px;
        width: 100%;
    }
</style>
@if (!empty($presensi->foto_in))
    <div class="detail">
        <table class="table">
            <tr>
                <th>Jam Masuk</th>
                <td>{{ date('d-m-Y H:i', strtotime($presensi->jam_in)) }}</td>
            </tr>
        </table>
    </div>
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
    console.log(latitude_kantor + "," + longitude_kantor);
    var rd = "{{ $presensi->radius_cabang }}";
    var map = L.map('map', {
        center: [latitude, longitude],
        zoom: 15
    });

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
    var marker = L.marker([latitude, longitude]).addTo(map);
    var circle = L.circle([latitude_kantor, longitude_kantor], {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.5,
        radius: rd
    }).addTo(map);

    setInterval(function() {
        map.invalidateSize();
    }, 100);
</script>
