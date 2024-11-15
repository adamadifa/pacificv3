<style>
    body {
        background-color: #eee;
    }

    .mt-70 {
        margin-top: 70px;
    }

    .mb-70 {
        margin-bottom: 70px;
    }

    /* .card {
        box-shadow: 0 0.46875rem 2.1875rem rgba(4, 9, 20, 0.03), 0 0.9375rem 1.40625rem rgba(4, 9, 20, 0.03), 0 0.25rem 0.53125rem rgba(4, 9, 20, 0.05), 0 0.125rem 0.1875rem rgba(4, 9, 20, 0.03);
        border-width: 0;
        transition: all .2s;
    } */

    /* .card {
        position: relative;
        display: flex;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background-color: #fff;
        background-clip: border-box;
        border: 1px solid rgba(26, 54, 126, 0.125);
        border-radius: .25rem;
    }

    .card-body {
        flex: 1 1 auto;
        padding: 1.25rem;
    } */

    .vertical-timeline {
        width: 100%;
        position: relative;
        padding: 1.5rem 0 1rem;
    }

    .vertical-timeline::before {
        content: '';
        position: absolute;
        top: 0;
        left: 67px;
        height: 100%;
        width: 4px;
        background: #e9ecef;
        border-radius: .25rem;
    }

    .vertical-timeline-element {
        position: relative;
        margin: 0 0 1rem;
    }

    .vertical-timeline--animate .vertical-timeline-element-icon.bounce-in {
        visibility: visible;
        animation: cd-bounce-1 .8s;
    }

    .vertical-timeline-element-icon {
        position: absolute;
        top: 0;
        left: 60px;
    }

    .vertical-timeline-element-icon .badge-dot-xl {
        box-shadow: 0 0 0 5px #fff;
    }

    .badge-dot-xl {
        width: 18px;
        height: 18px;
        position: relative;
    }

    .badge:empty {
        display: none;
    }


    .badge-dot-xl::before {
        content: '';
        width: 10px;
        height: 10px;
        border-radius: .25rem;
        position: absolute;
        left: 50%;
        top: 50%;
        margin: -5px 0 0 -5px;
        background: #fff;
    }

    .vertical-timeline-element-content {
        position: relative;
        margin-left: 90px;
        font-size: .8rem;
    }

    .vertical-timeline-element-content .timeline-title {
        font-size: .8rem;
        text-transform: uppercase;
        margin: 0 0 .5rem;
        padding: 2px 0 0;
        font-weight: bold;
    }

    .vertical-timeline-element-content .vertical-timeline-element-date {
        display: block;
        position: absolute;
        left: -90px;
        top: 0;
        padding-right: 10px;
        text-align: right;
        color: #adb5bd;
        font-size: .7619rem;
        white-space: nowrap;
    }

    .vertical-timeline-element-content:after {
        content: "";
        display: table;
        clear: both;
    }
</style>
<div class="vertical-timeline vertical-timeline--animate vertical-timeline--one-column">
    @foreach ($kunjungan as $d)
        @php

            $awal = strtotime($d->checkin_time); //waktu awal
            $akhir = strtotime($d->checkout_time); //waktu akhir
            $diff = $akhir - $awal;
            $jam = floor($diff / (60 * 60));
            $menit = $diff - $jam * (60 * 60);
            $minutes = floor($menit / 60);
            $j = $jam <= 9 ? '0' . $jam : $jam;
            $m = $minutes <= 9 ? '0' . $minutes : $minutes;
        @endphp <div class="vertical-timeline-item vertical-timeline-element">
            <div>
                <span class="vertical-timeline-element-icon bounce-in">
                    <span class="badge bg-info">
                        <i class="feather icon-map-pin"></i>
                    </span>

                </span>
                <div class="vertical-timeline-element-content bounce-in">
                    <div class="card">
                        <div class="card-body">
                            <span class="d-flex justify-content-start">
                                @if (!empty($d->foto))
                                    @php
                                        $path = Storage::url('pelanggan/' . $d->foto);
                                    @endphp
                                    <img src="{{ url($path) }}" class="rounded mr-75" alt="profile image" height="40" width="40">
                                @else
                                    <img src="{{ asset('app-assets/images/slider/04.jpg') }}" class="rounded float-left mr-75" alt="profile image"
                                        height="50" width="50">
                                @endif
                                <span>
                                    <span class="timeline-title">{{ $d->kode_pelanggan }} {{ $d->nama_pelanggan }}</span>
                                    <br>
                                    <span>{{ $d->alamat_pelanggan }}</span>
                                    <br>
                                    <span class=" danger">
                                        <i class="feather icon-log-out danger "></i>
                                        {{ !empty($d->checkout_time) ? date('H:i', strtotime($d->checkout_time)) : 'Belum Checkout' }}
                                    </span>
                                    <span class="primary">
                                        @if (!empty($d->checkout_time))
                                            Durasi ({{ $j }} : {{ $m }})
                                        @else
                                            Durasi (00:00)
                                        @endif

                                    </span>
                                    <br>
                                    <span class="info">
                                        <i class="feather icon-map mr-1"></i>{{ $d->jarak }} meter
                                    </span>
                                </span>
                            </span>

                        </div>
                    </div>

                    <span class="vertical-timeline-element-date">{{ date('H:i', strtotime($d->checkin_time)) }} </span>
                </div>
            </div>
        </div>
    @endforeach
</div>
