<style>
    #map {
        height: 200px;
    }

</style>
<style>
    .cardpenjualan {
        width: 100%;
        background-color: #ffffff;
        color: black;
        padding: 8px 10px;
        margin: 5px;
        border-radius: 10px;
        box-shadow: 2px 2px 3px rgba(88, 88, 88, 0.3);
    }

    .totalcashin {
        line-height: normal;
        margin-right: 2px;
    }

    #border1 {
        border: 1px solid #b11036;
    }

</style>
<div class="row mt-0 mb-3" id="map"></div>
@foreach ($kunjungan as $d)
<div class="detailcashin d-flex justify-content-between">
    <div class="cardpenjualan d-flex" id="border1">
        <div class=" avatar avatar-40 alert-success text-success rounded-circle">
            <i class="bi bi-pin-map"></i>
        </div>
        <div class="totalcashin" style="margin-left:30px !important">
            <p style="text-align: left !important; font-weight:600" class="mb-0 text-success">{{ $d->nama_pelanggan }}</p>
            <span class="badge bg-success">{{ $d->checkin_time }}</span>
        </div>
    </div>
</div>
@endforeach
<script>
    var id_salesman = "{{ Request('id_karyawan') }}";
    var dari = "{{ Request('dari') }}";
    var sampai = "{{ Request('sampai') }}";
    var map = L.map('map').setView([-7.3665114, 108.2148793], 14);
    var layerGroup = L.layerGroup();
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    L.marker([-7.3665114, 108.2148793]).addTo(map);


    $.getJSON('/getlocationcheckinsalesman?dari=' + dari + '&sampai=' + sampai + '&id_salesman=' + id_salesman, function(data) {
        $.each(data, function(index) {
            var salesmanicon = L.icon({
                iconUrl: '/app-assets/marker/' + data[index].marker
                , iconSize: [75, 75], // size of the icon
                shadowSize: [50, 64], // size of the shadow
                iconAnchor: [22, 94], // point of the icon which will correspond to marker's location
                shadowAnchor: [4, 62], // the same for the shadow
                popupAnchor: [-3, -76] // point from which the popup should open relative to the iconAnchor
            });

            var imagepath = "{{ Storage::url('pelanggan/') }}" + data[index].foto;

            var marker = L.marker([parseFloat(data[index].latitude), parseFloat(data[index].longitude)], {
                icon: salesmanicon
            }).bindPopup("<b>" + data[index].kode_pelanggan + " - " + data[index].nama_pelanggan + "</b><br><br>" + "<img width='200px' height='200px' src='" + imagepath + "'/><br><br>" + "Latitude : " + data[index].latitude + " <br>Longitude : " + data[index].longitude + "<br> Alamat :" + data[index].alamat_pelanggan + "<br> Checkin Time :" + data[index].checkin_time, {
                maxWidth: 200
            });


            layerGroup.addLayer(marker);
            map.addLayer(layerGroup);
        });
    });

</script>
