<style>
    .mycontent-left {
        border-right: 1px dashed rgb(246, 246, 246);
    }

    .mycontent-right {
        border-right: 1px dashed rgb(246, 246, 246);
    }

</style>
@php
$lat_start = "";
$long_start = "";
$start_time = "";
@endphp
@foreach ($smactivity as $d)
<div class="row mt-2">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-auto">
                        <div class="avatar avatar-40 alert-danger text-danger rounded-circle">
                            @if (!empty($d->foto))
                            @php
                            $path = Storage::url('uploads/smactivity/'.$d->foto);
                            @endphp
                            <img src="{{ url($path) }}" class="avatar avatar-40 rounded-circle" alt="">
                            @else
                            <img src="{{ asset('app-assets/marker/marker.png') }}" class="avatar avatar-40 rounded-circle" alt="">
                            @endif
                        </div>
                    </div>
                    <div class="col align-self-center ps-0">
                        <div class="row mb-2">
                            <div class="col">
                                <p class="small  mb-0" style="font-weight: 400">{{ ucwords(strtolower($d->aktifitas)) }}</p>
                                <p class="small mt-1 mb-0" style="color:rgb(3, 121, 181)">Lat : {{ $d->latitude }}</p>
                                <p class="small mb-0" style="color:rgb(3, 121, 181)">Long : {{ $d->longitude }}</p>
                                @if ($loop->first)
                                @php
                                $jarak = hitungjarak($lokasi[0],$lokasi[1],$d->latitude,$d->longitude);
                                $totaljarak = round(round($jarak['meters']) / 1000);
                                $totalwaktu = 0;
                                @endphp
                                @else
                                @php
                                $jarak = hitungjarak($lat_start,$long_start,$d->latitude,$d->longitude);
                                $totaljarak = round(round($jarak['meters']) / 1000);
                                $totalwaktu = hitungjamdesimal($start_time,$d->tanggal);
                                @endphp
                                @endif
                                <p class="small mb-0" style="color:rgb(3, 121, 181)">Jarak : {{ $totaljarak }} km</p>
                                <p class="small" style="color:rgb(3, 121, 181)">Waktu : {{ $totalwaktu }} Jam</p>
                                @php
                                $tanggal = date("Y-m-d",strtotime($d->tanggal));
                                @endphp
                                <span class="badge bg-danger">{{ DateToIndo2($tanggal) }} {{ date("H:i:s",strtotime($d->tanggal)) }}</span>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@php
$lat_start = $d->latitude;
$long_start = $d->longitude;
$start_time = $d->tanggal;
@endphp
@endforeach
