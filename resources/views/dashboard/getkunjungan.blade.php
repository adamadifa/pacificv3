@foreach ($kunjungan as $d)
@php

$awal = strtotime($d->checkin_time); //waktu awal
$akhir = strtotime($d->checkout_time); //waktu akhir
$diff = $akhir - $awal;
$jam = floor($diff / (60 * 60));
$menit = $diff - $jam * (60 * 60) ;
$minutes = floor($menit/60);
$j = $jam <= 9 ? '0' .$jam : $jam; $m=$minutes<=9 ? '0' .$minutes : $minutes; @endphp < class="row">
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-content">
                <div class="card-body" style="padding:8px 10px 8px 8px !important">
                    <p class="card-text d-flex justify-content-between">
                        <span class="d-flex justify-content-between">
                            @if (!empty($d->foto))
                            @php
                            $path = Storage::url('pelanggan/'.$d->foto);
                            @endphp
                            <img src="{{ url($path) }}" class="rounded mr-75" alt="profile image" height="40" width="40">
                            @else
                            <img src="{{ asset('app-assets/images/slider/04.jpg') }}" class="rounded float-left mr-75" alt="profile image" height="50" width="50">
                            @endif

                            <span>
                                {{ $d->kode_pelanggan }} <br> {{ $d->nama_pelanggan }}
                            </span>
                        </span>
                        <span>
                            <span class="badge bg-success">{{ date("H:i:s",strtotime($d->checkin_time)) }}</span> <br>
                            <span class="badge bg-info">{{ !empty($d->checkout_time) ? date("H:i",strtotime($d->checkout_time)) : 0 }}</span>
                        </span>

                    </p>

                </div>
            </div>
        </div>
    </div>


    {{-- <tr>
    <td>{{ $d->kode_pelanggan }}</td>
    <td>{{ $d->nama_pelanggan }}</td>
    <td>{{ date("H:i",strtotime($d->checkin_time)) }}</td>
    <td>{{ !empty($d->checkout_time) ? date("H:i",strtotime($d->checkout_time)) : 0 }}</td>
    <td>{{ !empty($d->checkout_time) ? $j.':'.$m : 0}}</td>
    </tr> --}}
    @endforeach
