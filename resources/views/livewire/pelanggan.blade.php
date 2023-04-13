<div>
    <div class="row">
        <div class="col-12">
            <div class="app-fixed-search">
                {{-- <form action="/pelanggansalesman" method="GET"> --}}
                <fieldset class="form-group position-relative has-icon-left m-0 mb-1">
                    <input type="text" class="form-control" wire:model="search" placeholder="Cari Nama Pelanggan" autocomplete="off">
                    <div class="form-control-position">
                        <i class="feather icon-search"></i>
                    </div>
                </fieldset>
                {{-- <button class="btn btn-primary btn-block"><i class="feather icon-search"></i> Cari</button>
                </form> --}}
            </div>
        </div>
    </div>
    @if ($pelanggan->isEmpty())
    <div class="alert alert-warning">
        <p>Data Tidak Ditemukan</p>
    </div>
    @else
    @foreach ($pelanggan as $d)
    <a href="/pelanggan/showpelanggan?kode_pelanggan={{ Crypt::encrypt($d->kode_pelanggan) }}" style="color:rgb(107, 99, 99)">
        <div class="row">
            <div class="col-12">

                <div class="card {{ $d->status_pelanggan == 1 ? 'border-primary' : 'bg-gradient-danger' }}">
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

                                    </span>
                                </span>
                                <span>
                                    <span>{{ $d->nama_karyawan }}</span><br>
                                    <span class="badge bg-info">{{ ucwords(strtolower($d->pasar)) }}</span>
                                </span>


                            </p>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </a>
    @endforeach
    @endif

</div>
