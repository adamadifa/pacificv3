@extends('layouts.sap.sap')
@section('content')
<style>
    td,
    th {
        font-size: 14px !important;
        font-family: 'Poppins';
    }

    th {
        width: 50%;
        font-family: 'Poppins';
    }

    td {
        text-align: right !important;
    }

    /* center the blockquote in the page */
    .blockquote-wrapper {
        display: flex;

    }

    /* Blockquote main style */
    .blockquote {
        position: relative;
        font-family: 'Barlow Condensed', sans-serif;
        max-width: 620px;

        align-self: center;
    }

    /* Blockquote header */
    .blockquote h1 {
        font-family: 'Abril Fatface', cursive;
        position: relative;
        /* for pseudos */
        color: #8f0606;
        font-size: 1rem;
        font-weight: normal;
        line-height: 1;
        margin: 0;
        border: 2px solid #fff;
        border: solid 2px;
        border-radius: 20px;
        padding: 25px;
    }

    /* Blockquote right double quotes */




    /* increase header size after 600px */
    @media all and (min-width: 600px) {
        .blockquote h1 {
            font-size: 3rem;
            line-height: 1.2;
        }

    }

    /* Blockquote subheader */
    .blockquote h4 {
        position: relative;
        color: #ffffff;
        font-size: 1.3rem;
        font-weight: 400;
        line-height: 1.2;
        margin: 0;
        padding-top: 15px;
        z-index: 1;
        margin-left: 150px;
        padding-left: 12px;
    }


    .blockquote h4:first-letter {
        margin-left: -12px;
    }

</style>
<div class="row">
    <div class="col-12">
        <ul class="nav nav-pills nav-justified tabs mb-3" id="assetstabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#cards" type="button" role="tab" aria-controls="cards" aria-selected="true">Kualitatif</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="currency-tab" data-bs-toggle="tab" data-bs-target="#currency" type="button" role="tab" aria-controls="currency" aria-selected="false">Kuantitatif</button>
            </li>
        </ul>
    </div>
</div>
<div class="tab-content" id="assetstabsContent">
    <div class="tab-pane fade active show" id="cards" role="tabpanel">
        <div class="row">
            <div class="col-12">
                <table class="table table-striped">
                    <tr>
                        <th>No. Pengajuan</th>
                        <td>{{ $limitkredit->no_pengajuan }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>{{ DateToIndo2($limitkredit->tgl_pengajuan) }}</td>
                    </tr>
                    <tr>
                        <th>Cabang</th>
                        <td>{{ $limitkredit->kode_cabang }}</td>
                    </tr>
                    <tr>
                        <th>Salesman</th>
                        <td>{{ ucwords(strtolower($limitkredit->nama_karyawan)) }}</td>
                    </tr>
                    <tr>
                        <th>Alamat KTP</th>
                        <td>{{ ucwords(strtolower($limitkredit->alamat_pelanggan)) }}</td>
                    </tr>
                    <tr>
                        <th>ID Pelanggan</th>
                        <td>{{ $limitkredit->kode_pelanggan }}</td>
                    </tr>
                    <tr>
                        <th>Pelanggan</th>
                        <td>{{ $limitkredit->nama_pelanggan }}</td>
                    </tr>
                    <tr>
                        <th>Alamat Toko</th>
                        <td>{{ ucwords(strtolower($limitkredit->alamat_toko)) }}</td>
                    </tr>
                    <tr>
                        <th>Koordinat</th>
                        <td>{{ $limitkredit->latitude }},{{ $limitkredit->longitude }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="tab-pane fade  show" id="currency" role="tabpanel">
        <div class="row">
            <div class="col-12">
                <table class="table table-striped">
                    <tr>
                        <th>Status</th>
                        <td>
                            @if ($limitkredit->status_outlet==1)
                            New Outlet
                            @else
                            Existing Outlet
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Pembayaran</th>
                        <td>
                            @if ($limitkredit->cara_pembayaran==1)
                            Bank Transfer
                            @elseif($limitkredit->cara_pembayaran==2)
                            Advance Cash
                            @else
                            Cheque / Bilyet Giro
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Histori</th>
                        <td>{{ $limitkredit->histori_transaksi }}</td>
                    </tr>
                    <tr>
                        <th>Terakhir Top UP</th>
                        <td>
                            @php
                            $k = "<"; $l=">" ; @endphp @if ($limitkredit->lama_topup >= 31)
                                {{ $l }} 1 Bulan
                                @else
                                {{ $k }} 1 Bulan @endif </td>
                    </tr>
                    <tr>
                        <th>Lama Usaha</th>
                        <td>{{ $limitkredit->lama_usaha }}</td>
                    </tr>
                    <tr>
                        <th>Jumlah Faktur</th>
                        <td>{{ $limitkredit->jml_faktur }}</td>
                    </tr>
                    <tr>
                        <th>TOP</th>
                        <td>{{ $limitkredit->jatuhtempo }} Hari</td>
                    </tr>
                    <tr>
                        <th>Tempat Usaha</th>
                        <td>{{ $limitkredit->kepemilikan }}</td>
                    </tr>
                    <tr>
                        <th>Omset Toko</th>
                        <td>{{ rupiah($limitkredit->last_omset) }}</td>
                    </tr>
                    <tr>
                        <th>Mulai Langganan</th>
                        <td>{{ $limitkredit->lama_langganan }}</td>
                    </tr>
                    <tr>
                        <th>Type Outlet</th>
                        <td>
                            @if ($limitkredit->type_outlet==1)
                            Grosir
                            @else
                            Retail
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <tr>
                <th style="background-color:#b11036; color:white">Limit Sebelumnya</th>
                <td style="font-weight:bold; text-align:right !important">{{ rupiah($limitkredit->last_limit) }}</td>
            </tr>

            <tr>
                <th style="background-color:#b11036; color:white">Pengajuan Tambahan</th>
                <td style="font-weight:bold; text-align:right !important">{{ rupiah($limitkredit->jumlah - $limitkredit->last_limit) }}</td>
            </tr>

            <tr>
                <th style="background-color:#b11036; color:white">Total Limit</th>
                <td style="font-weight:bold; text-align:right !important">{{ rupiah($limitkredit->jumlah) }}</td>
            </tr>

            <tr>
                <th style="background-color:#b11036; color:white">Level Otorisasi</th>
                <td style="font-weight:bold; text-align:right !important">
                    @if ($limitkredit->jumlah > 15000000)
                    Direktur
                    @elseif($limitkredit->jumlah > 10000000)
                    General Manager
                    @elseif($limitkredit->jumlah >5000000)
                    RSM
                    @elseif($limitkredit->jumlah > 2000000)
                    Kepala Penjualan
                    @endif</td>
            </tr>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-12">
        @if ($limitkredit->jumlah > 2000000)
        @if (empty($limitkredit->kacab))
        <div class="alert alert-warning text-center">
            <i class="bi bi-arrow-clockwise"></i> Pending
        </div>
        @elseif(
        !empty($limitkredit->kacab) && !empty($limitkredit->rsm) && $limitkredit->status==2 ||
        !empty($limitkredit->kacab) && empty($limitkredit->rsm) && $limitkredit->status== 0 ||
        !empty($limitkredit->kacab) && empty($limitkredit->rsm) && $limitkredit->status== 1 ||
        !empty($limitkredit->kacab) && !empty($limitkredit->rsm) && $limitkredit->status== 0 ||
        !empty($limitkredit->kacab) && !empty($limitkredit->rsm) && $limitkredit->status== 1
        )
        <div class="alert alert-success text-center">
            <i class="bi bi-check-all"></i> Disetujui Kepala Penjualan
        </div>
        @else
        <div class="alert alert-danger text-center">
            <i class="bi bi-x-circle"></i> Ditolak Kepala Penjualan
        </div>
        @endif
        @endif

        @if ($limitkredit->jumlah > 5000000)
        @if (empty($limitkredit->rsm))
        <div class="alert alert-warning text-center">
            <i class="bi bi-arrow-clockwise"></i> Pending
        </div>
        @elseif(
        !empty($limitkredit->rsm) && !empty($limitkredit->mm) && $limitkredit->status == 2
        || !empty($limitkredit->rsm) && empty($limitkredit->mm) && $limitkredit->status == 1
        || !empty($limitkredit->rsm) && empty($limitkredit->mm) && $limitkredit->status == 0
        || !empty($limitkredit->rsm) && !empty($limitkredit->mm) && $limitkredit->status == 0
        || !empty($limitkredit->rsm) && !empty($limitkredit->mm) && $limitkredit->status == 1
        )
        <div class="alert alert-success text-center">
            <i class="bi bi-check-all"></i> Disetujui RSM
        </div>
        @else
        <div class="alert alert-danger text-center">
            <i class="bi bi-x-circle"></i> Ditolak RSM
        </div>
        @endif
        @endif

        @if ($limitkredit->jumlah > 10000000)
        @if (empty($limitkredit->mm))
        <div class="alert alert-warning text-center">
            <i class="bi bi-arrow-clockwise"></i> Pending
        </div>
        @elseif(
        !empty($limitkredit->mm) && !empty($limitkredit->dirut) && $limitkredit->status == 2
        || !empty($limitkredit->mm) && empty($limitkredit->dirut) && $limitkredit->status == 1
        || !empty($limitkredit->mm) && empty($limitkredit->dirut) && $limitkredit->status == 0
        || !empty($limitkredit->mm) && !empty($limitkredit->dirut) && $limitkredit->status == 0
        || !empty($limitkredit->mm) && !empty($limitkredit->dirut) && $limitkredit->status == 1
        )
        <div class="alert alert-success text-center">
            <i class="bi bi-check-all"></i> Disetujui GM Marketing
        </div>
        @else
        <div class="alert alert-danger text-center">
            <i class="bi bi-x-circle"></i> Ditolak GM Marketing
        </div>
        @endif
        @endif

        @if ($limitkredit->jumlah > 15000000)
        @if (empty($limitkredit->dirut))
        <div class="alert alert-warning text-center">
            <i class="bi bi-arrow-clockwise"></i> Pending
        </div>
        @elseif(!empty($limitkredit->dirut) && $limitkredit->status != 2 )
        <div class="alert alert-success text-center">
            <i class="bi bi-check-all"></i> Disetujui Direktur
        </div>
        @else
        <div class="alert alert-danger text-center">
            <i class="bi bi-x-circle"></i> Ditolak Direktur
        </div>
        @endif
        @endif


    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="blockquote-wrapper">
            <div class="blockquote">
                <h1>
                    @foreach ($komentar as $d)
                    {{ ucwords(strtolower($d->uraian_analisa)) }}<br>
                    @endforeach
                </h1>
            </div>
        </div>
    </div>
</div>
@if($level == "kepala penjualan" && empty($limitkredit->kacab) && $limitkredit->status==0
|| $level == "kepala cabang" && empty($limitkredit->kacab) && $limitkredit->status==0

|| $level == "kepala penjualan" && !empty($limitkredit->kacab) && empty($limitkredit->mm) && $limitkredit->status==2
|| $level == "kepala cabang" && !empty($limitkredit->kacab) && empty($limitkredit->mm) && $limitkredit->status==2

|| $level == "kepala penjualan" && !empty($limitkredit->kacab) && empty($limitkredit->mm) && $limitkredit->status==1
|| $level == "kepala cabang" && !empty($limitkredit->kacab) && empty($limitkredit->mm) && $limitkredit->status==1

|| $level == "kepala penjualan" && !empty($limitkredit->kacab) && empty($limitkredit->mm) && $limitkredit->status==0
|| $level == "kepala cabang" && !empty($limitkredit->kacab) && empty($limitkredit->mm) && $limitkredit->status==0)
<div class="row mb-4">
    <div class="col-6">
        <a href="/limitkredit/{{ Crypt::encrypt($limitkredit->no_pengajuan) }}/approve" class="btn  w-100 text-white btn-success"><i class="bi bi-check-all"></i> Setuju</a>
    </div>
    <div class="col-6">
        <a href="/limitkredit/{{ Crypt::encrypt($limitkredit->no_pengajuan) }}/decline" class="btn w-100 text-white" style="background-color:#b11036"><i class="bi bi-x-circle"></i> Tolak</a>
    </div>
</div>
@endif

@if($level=="rsm" && !empty($limitkredit->kacab) && empty($limitkredit->rsm) && empty($limitkredit->mm) && $limitkredit->status==0
|| $level == "rsm" && !empty($limitkredit->kacab) && !empty($limitkredit->rsm) && empty($limitkredit->mm) && $limitkredit->status==2
|| $level == "rsm" && !empty($limitkredit->kacab) && !empty($limitkredit->rsm) && empty($limitkredit->mm) && $limitkredit->status==0)
<div class="row mb-4">
    <div class="col-6">
        <a href="/limitkredit/{{ Crypt::encrypt($limitkredit->no_pengajuan) }}/approve" class="btn btn-success  w-100 text-white"><i class="bi bi-check-all"></i> Setuju</a>
    </div>
    <div class="col-6">
        <a href="/limitkredit/{{ Crypt::encrypt($limitkredit->no_pengajuan) }}/decline" class="btn w-100 text-white" style="background-color:#b11036"><i class="bi bi-x-circle"></i> Tolak</a>
    </div>
</div>
@endif

@if ($level=="manager marketing" && !empty($limitkredit->rsm) && empty($limitkredit->mm) && empty($limitkredit->dirut) && $limitkredit->status==0
|| $level =="manager marketing" && !empty($limitkredit->rsm) && !empty($limitkredit->mm) && empty($limitkredit->dirut) && $limitkredit->status==2
|| $level =="manager marketing" && !empty($limitkredit->rsm) && !empty($limitkredit->mm) && empty($limitkredit->dirut) && $limitkredit->status==0
|| $level =="manager marketing" && !empty($limitkredit->rsm) && !empty($limitkredit->mm) && empty($limitkredit->dirut) && $limitkredit->status!=2)

<div class="row mb-4">
    <div class="col-6">
        <a href="/limitkredit/{{ Crypt::encrypt($limitkredit->no_pengajuan) }}/approve" class="btn btn-success  w-100 text-white"><i class="bi bi-check-all"></i> Setuju</a>
    </div>
    <div class="col-6">
        <a href="/limitkredit/{{ Crypt::encrypt($limitkredit->no_pengajuan) }}/decline" class="btn w-100 text-white" style="background-color:#b11036"> <i class="bi bi-x-circle"></i> Tolak</a>
    </div>
</div>
@endif

@if ($level=="direktur" && !empty($limitkredit->mm) && empty($limitkredit->dirut) && $limitkredit->status==0
|| $level =="direktur" && !empty($limitkredit->mm) && !empty($limitkredit->dirut) && $limitkredit->status==2
|| $level =="direktur" && !empty($limitkredit->mm) && !empty($limitkredit->dirut) && $limitkredit->status==0
|| $level =="direktur" && !empty($limitkredit->mm) && !empty($limitkredit->dirut) && $limitkredit->status!=2)

<div class="row mb-4">
    <div class="col-6">
        <a href="/limitkredit/{{ Crypt::encrypt($limitkredit->no_pengajuan) }}/approve" class="btn btn-success  w-100 text-white"><i class="bi bi-check-all"></i> Setuju</a>
    </div>
    <div class="col-6">
        <a href="/limitkredit/{{ Crypt::encrypt($limitkredit->no_pengajuan) }}/decline" class="btn w-100 text-white" style="background-color:#b11036"><i class="bi bi-x-circle"></i> Tolak</a>
    </div>
</div>
@endif
@endsection
