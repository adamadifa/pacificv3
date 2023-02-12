@extends('layouts.sap.sap')
@section('content')
<style>
    #border1 {
        border: 1px solid #b11036;
    }

</style>
<form action="/sap/pelanggan" />
<div class="row">
    <div class="col-12">
        <div class="inputWithIcon">
            <i class="bi bi-user"></i>
            <input type="text" value="{{ Request('nama') }}" name="nama" id="nama" autocomplete="off" />
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
                <option {{ Request('kode_cabang') == $d->kode_cabang ? 'selected' : '' }} value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-12">
        <button class="btn w-100" type="submit" name="submit" style="background-color:#b11036; color:white">Cari Data</button>
    </div>
</div>
</form>
<div class="row mt-2">
    <div class="col-12">
        @foreach ($pelanggan as $d)
        <div class="col-12 mb-1">
            <a href="/sap/limitkredit/show">
                <div class="card" id="border1">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 d-flex justify-content-between">
                                <div id="datapelanggan" class="d-flex">
                                    @if (!empty($d->foto))
                                    @php
                                    $path = Storage::url('pelanggan/'.$d->foto);
                                    @endphp
                                    <div class="avatar avatar-40 alert-danger text-danger rounded-circle">
                                        <img src="{{ url($path) }}" class="avatar avatar-40 rounded-circle" alt="">
                                    </div>
                                    @else
                                    <div class="avatar avatar-40 alert-danger text-danger rounded-circle">
                                        <img src="{{ asset('app-assets/marker/marker.png') }}" class="avatar avatar-40 rounded-circle" alt="">
                                    </div>
                                    @endif
                                    <div class="detailpelanggan" style="margin-left:10px">
                                        <span style="font-weight:bold">{{ $d->nama_pelanggan }}</span>
                                        <br>
                                        <small class="text-muted">{{ $d->pasar }} | {{ $d->nama_karyawan }} | </small>
                                        <small style="font-weight:bold">{{ rupiah($d->limitpel) }} </small>
                                    </div>
                                </div>
                                <div id="datapengajuan" class="text-end">


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach

    </div>
</div>
<div class="row">
    <div class="col-12">
        {{ $pelanggan->links('vendor.pagination.vuexy') }}
    </div>
</div>
@endsection
