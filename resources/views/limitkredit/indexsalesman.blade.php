@extends('layouts.midone')
@section('titlepage','Data Penjualan')
@section('content')
<style>
    .form-group {
        margin-bottom: 5px !important;
    }

    .form-label-group {
        margin-bottom: 5px !important;
    }

</style>
<style>
    .card {
        margin-bottom: 0.8rem !important;
    }

</style>
<style>
    #border1 {
        border: 1px solid #b11036;
    }

    #border2 {
        border: 1px solid #04a55a;
    }

    #border3 {
        border: 1px solid #e5be10;
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
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h4 class="content-header-title float-left mb-0">Data Penjualan</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <input type="hidden" id="cektutuplaporan">
        <div class="row mb-1">
            <div class="col-12">
                <form action="" id="frmPenjualan">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12">
                            <x-inputtext label="Nama Pelanggan" field="nama_pelanggan" icon="feather icon-user" value="{{ Request('nama_pelanggan') }}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-sm-12">
                            <button type="submit" name="submit" value="1" class="btn btn-primary btn-block search"><i class="fa fa-search mr-1"></i> Cari Data </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @foreach ($limitkredit as $d)
        @php
        if($d->status==0){
        $border = "border3";
        }else if($d->status==1){
        $border = "border2";
        }else if($d->status==2){
        $border = "border1";
        }
        @endphp
        <div class="row">
            <div class="col-12">
                <a href="#">
                    <div class="card" id="{{ $border }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 d-flex justify-content-between">
                                    <div id="datapelanggan">
                                        <div style="font-weight: 600; color:black; font-size:0.9rem !important">
                                            <span>{{ $d->no_pengajuan }}</span>
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
                                            <span>{{ DateToIndo2($d->tgl_pengajuan) }}</span>
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
                                            @if ($d->jumlah >= 1000000)
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
                            <div class="row">
                                <div class="col-12">
                                    @if ($d->status==0)
                                    <span class="badge bg-warning">Pending</span>
                                    @elseif($d->status==1)
                                    <span class="badge bg-success">Disetujui</span>
                                    @elseif($d->status==2)
                                    <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
