@foreach ($rekappelanggan as $d)
<div class="row">
    <div class="col-12">
        <div class="card">
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
                                <br>
                                @if (!empty($d->AB))
                                AB : {{ ROUND($d->AB,2) }}
                                @endif
                                @if (!empty($d->AR))
                                AR : {{ ROUND($d->AR,2) }}
                                @endif
                                @if (!empty($d->ASE))
                                AS : {{ ROUND($d->ASE,2) }}
                                @endif
                                @if (!empty($d->BB))
                                BB : {{ ROUND($d->BB,2) }}
                                @endif
                                @if (!empty($d->DEP))
                                DEP : {{ ROUND($d->DEP,2) }}
                                @endif
                                @if (!empty($d->DS))
                                DS : {{ ROUND($d->DS,2) }}
                                @endif

                                @if (!empty($d->SP))
                                SP : {{ ROUND($d->SP,2) }}
                                @endif

                                @if (!empty($d->SP500))
                                SP500 : {{ ROUND($d->SP500,2) }}
                                @endif

                                @if (!empty($d->SP8))
                                SP8 : {{ ROUND($d->SP8,2) }}
                                @endif

                                @if (!empty($d->SC))
                                SC : {{ ROUND($d->SC,2) }}
                                @endif


                            </span>
                        </span>
                        <span style="font-size: 16px; font-weight:bold">
                            {{ $d->jml_sku }}
                        </span>
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
