<div class="detail">
    <table class="table">
        @if ($status == "in")
        <tr>
            <th>Jam Masuk</th>
            <td>{{ date("d-m-Y H:i",strtotime($presensi->jam_in)) }}</td>
        </tr>
        @else
        <tr>
            <th>Jam Pulang</th>
            <td>{{ date("d-m-Y H:i",strtotime($presensi->jam_out)) }}</td>
        </tr>
        @endif

    </table>
</div>
<div class="fotomesin">
    <img src="{{ asset('app-assets/images/revo.png') }}" alt="" class="card-img">
</div>
