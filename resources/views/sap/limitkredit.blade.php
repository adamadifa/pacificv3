@extends('layouts.sap.sap')
@section('content')
<style>
    #border1 {
        border: 1px solid #b11036;
    }

    .cardapprove {
        font-size: 0.7rem;
        padding: 3px;
        /* border: 1px solid #ccc; */
        border-radius: 2px;
        color: white;
        margin-right: 2px;
    }

</style>
<form action="/sap/limitkredit" />
<div class="row">
    <div class="col-12">
        <div class="inputWithIcon">
            <i class="bi bi-user"></i>
            <input type="text" value="{{ Request('nama_pelanggan') }}" name="nama_pelanggan" id="nama_pelanggan" autocomplete="off" />
            <label>Nama Pelanggan</label>
        </div>
    </div>
</div>
<div class="row mt-2">
    <div class="col-12">
        <div class="group">
            <select name="kode_cabang" class="select_join" id="kode_cabang">
                <option value="">Pilih Cabang</option>
                @foreach ($cabang as $d)
                <option value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="row mt-1">
    <div class="col-12">
        <div class="group">
            <select name="status" class="select_join" id="status">
                <option value="">Semua Status Pengajuan</option>
                <option {{ (Request('status')=='pending' ? 'selected' :'')}} value="pending">BELUM DISETUJUI {{ Str::upper(Auth::user()->level)  }}</option>
                <option {{ (Request('status')=='disetujui' ? 'selected' :'')}} value="disetujui">DISETUJUI {{ Str::upper(Auth::user()->level)  }}</option>
                <option {{ (Request('status')=='ditolak' ? 'selected' :'')}} value="ditolak">DITOLAK {{ Str::upper(Auth::user()->level)  }}</option>
            </select>
        </div>
    </div>
</div>
<div class="row mt-2">
    <div class="col-12">
        <button class="btn w-100" style="background-color:#b11036; color:white">Cari Data</button>
    </div>
</div>
</form>
<div class="row mt-2">
    <div class="col-12">
        @include('layouts.notification')
        @foreach ($limitkredit as $d)
        <div class="col-12 mb-1">
            <div class="card" id="border1">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 d-flex justify-content-between">
                            <div id="datapelanggan">
                                <div style="font-weight: 600; font-size:0.8rem !important">
                                    <span class="text-muted">{{ $d->no_pengajuan }}</span>
                                    <br>
                                    {{ strtoupper($d->nama_pelanggan) }}
                                    <br>
                                    <small class="text-muted">{{ DateToIndo2($d->tgl_pengajuan) }}</small>
                                </div>
                            </div>
                            <div id="datapengajuan">
                                <span style="font-weight:bold">
                                    @if (!empty($d->jumlah_rekomendasi))
                                    <s>{{ rupiah($d->jumlah) }}</s> / {{ rupiah($d->jumlah_rekomendasi) }}
                                    @else
                                    {{ rupiah($d->jumlah) }}
                                    @endif
                                </span>
                                <br>
                                <div class="d-flex">
                                    <div class="cardapprove bg-success">
                                        KP
                                    </div>
                                    <div class="cardapprove bg-success">
                                        RSM
                                    </div>
                                    <div class="cardapprove bg-success">
                                        GM
                                    </div>
                                    <div class="cardapprove bg-success">
                                        DIRUT
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
