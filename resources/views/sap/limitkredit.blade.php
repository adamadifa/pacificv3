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
            <a href="/sap/limitkredit/{{ Crypt::encrypt($d->no_pengajuan) }}/show">
                <div class="card" id="border1">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 d-flex justify-content-between">
                                <div id="datapelanggan">
                                    <div style="font-weight: 600; font-size:0.8rem !important">
                                        <span class="text-muted">{{ $d->no_pengajuan }}</span>
                                        <?php
                                    $scoreakhir =  $d['skor'];
                                    if ($scoreakhir <= 2) {
                                        $rekomendasi = "TL";
                                    } else if ($scoreakhir > 2 && $scoreakhir <= 4) {
                                        $rekomendasi = "TD";
                                    } else if ($scoreakhir > 4 && $scoreakhir <= 6) {
                                        $rekomendasi = "B";
                                    } else if ($scoreakhir > 6 && $scoreakhir <= 8.5) {
                                        $rekomendasi = "LDP";
                                    } else if ($scoreakhir > 8.5 && $scoreakhir <= 10) {
                                        $rekomendasi = "L";
                                    }
                                    if ($scoreakhir <= 4) {
                                        $bg = "danger";
                                    } else if ($scoreakhir <= 6) {
                                        $bg = "warning";
                                    } else {
                                        $bg = "success";
                                    }
                                    //echo $scoreakhir;
                                    ?>
                                        <span class="badge bg-<?php echo $bg; ?>">
                                            <?php echo $scoreakhir; ?>
                                        </span>
                                        <br>
                                        {{ strtoupper($d->nama_pelanggan) }}
                                        <br>
                                        <small class="text-muted">{{ DateToIndo2($d->tgl_pengajuan) }}</small>
                                    </div>
                                </div>
                                <div id="datapengajuan" class="text-end">
                                    <span style="font-weight:bold">
                                        @if (!empty($d->jumlah_rekomendasi))
                                        <s>{{ rupiah($d->jumlah) }}</s> / {{ rupiah($d->jumlah_rekomendasi) }}
                                        @else
                                        {{ rupiah($d->jumlah) }}
                                        @endif
                                    </span>
                                    <br>
                                    <div class="d-flex justify-content-end">
                                        @if ($d->jumlah > 2000000)
                                        @if (empty($d->kacab))
                                        <div class="cardapprove bg-warning">
                                            KP
                                        </div>
                                        @elseif(
                                        !empty($d->kacab) && !empty($d->rsm) && $d->status==2 ||
                                        !empty($d->kacab) && empty($d->rsm) && $d->status== 0 ||
                                        !empty($d->kacab) && empty($d->rsm) && $d->status== 1 ||
                                        !empty($d->kacab) && !empty($d->rsm) && $d->status== 0 ||
                                        !empty($d->kacab) && !empty($d->rsm) && $d->status== 1
                                        )
                                        <div class="cardapprove bg-success">
                                            KP
                                        </div>
                                        @else
                                        <div class="cardapprove bg-danger">
                                            KP
                                        </div>
                                        @endif
                                        @endif



                                        @if ($d->jumlah > 5000000)
                                        @if (empty($d->rsm))
                                        <div class="cardapprove bg-warning">
                                            RSM
                                        </div>
                                        @elseif(
                                        !empty($d->rsm) && !empty($d->mm) && $d->status == 2
                                        || !empty($d->rsm) && empty($d->mm) && $d->status == 1
                                        || !empty($d->rsm) && empty($d->mm) && $d->status == 0
                                        || !empty($d->rsm) && !empty($d->mm) && $d->status == 0
                                        || !empty($d->rsm) && !empty($d->mm) && $d->status == 1
                                        )
                                        <div class="cardapprove bg-success">
                                            RSM
                                        </div>
                                        @else
                                        <div class="cardapprove bg-danger">
                                            RSM
                                        </div>
                                        @endif
                                        @endif

                                        @if ($d->jumlah > 10000000)
                                        @if (empty($d->mm))
                                        <div class="cardapprove bg-warning">
                                            GM
                                        </div>
                                        @elseif(
                                        !empty($d->mm) && !empty($d->dirut) && $d->status == 2
                                        || !empty($d->mm) && empty($d->dirut) && $d->status == 1
                                        || !empty($d->mm) && empty($d->dirut) && $d->status == 0
                                        || !empty($d->mm) && !empty($d->dirut) && $d->status == 0
                                        || !empty($d->mm) && !empty($d->dirut) && $d->status == 1
                                        )
                                        <div class="cardapprove bg-success">
                                            GM
                                        </div>
                                        @else
                                        <div class="cardapprove bg-danger">
                                            GM
                                        </div>
                                        @endif
                                        @endif
                                        @if ($d->jumlah > 15000000)
                                        @if (empty($d->dirut))
                                        <div class="cardapprove bg-warning">
                                            DIRUT
                                        </div>
                                        @elseif(!empty($d->dirut) && $d->status != 2 )
                                        <div class="cardapprove bg-success">
                                            DIRUT
                                        </div>
                                        @else
                                        <div class="cardapprove bg-danger">
                                            DIRUT
                                        </div>
                                        @endif
                                        @endif

                                    </div>
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
@endsection
