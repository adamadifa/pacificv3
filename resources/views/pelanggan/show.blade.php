@extends('layouts.midone')
@section('titlepage', 'Detail Pelanggan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Detail Pelanggan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/pelanggan">Pelanggan</a></li>
                            <li class="breadcrumb-item"><a href="#">Detail Pelanggan</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <div class="col-lg-12 col-sm-12">

        <div class="row">
            <div class="col-lg-3 col-sm-12">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-content">
                                @if (!empty($data->foto))
                                @php
                                $path = Storage::url('pelanggan/'.$data->foto);
                                @endphp
                                <img class="card-img img-fluid" src="{{ url($path) }}" alt="Card image">
                                @else
                                <img class="card-img img-fluid" src="{{ asset('app-assets/images/slider/04.jpg') }}" alt="Card image">
                                @endif
                                <div class="card-img-overlay overflow-hidden overlay-primary overlay-lighten-2">
                                    <h4 class="card-title text-white">{{ $data->nama_pelanggan }}</h4>
                                    <p class="card-text text-white">{{ $data->kode_pelanggan }} - {{ strtoupper($data->nama_cabang) }}
                                    </p>
                                    <p class="card-text"><small class="text-white">{{ $data->nama_karyawan }}</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <h4 class="card-title">Alamat</h4>
                                    <p class="card-text">{{ (!empty($data->alamat_pelanggan) ? $data->alamat_pelanggan : $data->alamat_toko) }}</p>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ $data->nik }}</span>
                                        NIK
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ $data->no_kk }}</span>
                                        No. KK
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ date("d-F-y",strtotime($data->tgl_lahir)) }}</span>
                                        Tanggal Lahir
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ $data->no_hp }}</span>
                                        No. HP
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ $data->pasar }}</span>
                                        Pasar
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ $data->hari }}</span>
                                        Hari
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ $data->latitude }}</span>
                                        Latitude
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ $data->longitude }}</span>
                                        Longitude
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ $data->jatuhtempo }} Hari</span>
                                        Jatuh Tempo Pembayaran
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">
                                            @if ($data->cara_pembayaran == 1)
                                            Bank Transfer
                                            @elseif ($data->cara_pembayaran == 2 )
                                            Advance Cash
                                            @elseif ($data->cara_pembayaran==3)
                                            Cheque / Billyet Giro
                                            @else
                                            Belum Di Tentukan
                                            @endif
                                        </span>
                                        Cara Pembayaran
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">
                                            @if ($data->status_outlet == 1)
                                            New Outlet
                                            @elseif ($data->status_outlet == 2 )
                                            Existing Outlet
                                            @else
                                            Belum Di Tentukan
                                            @endif
                                        </span>
                                        Status Outlet
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">
                                            @if ($data->type_outlet == 1)
                                            Grosir
                                            @elseif ($data->type_outlet == 2 )
                                            Retail
                                            @else
                                            Belum Di Tentukan
                                            @endif
                                        </span>
                                        Jenis Outlet
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ $data->lama_usaha }} </span>
                                        Lama Usaha
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">
                                            @if ($data->jaminan == 1)
                                            Ada
                                            @elseif ($data->jaminan == 2 )
                                            Tidak Ada
                                            @else
                                            Belum Di Tentukan
                                            @endif
                                        </span>
                                        Jaminan
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ $data->lama_langganan }} </span>
                                        Lama Berlangganan
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">
                                            @if ($data->cara_pembayaran == 1)
                                            Ada
                                            @elseif ($data->cara_pembayaran == 2 )
                                            Tidak Ada
                                            @else
                                            Belum Di Tentukan
                                            @endif
                                        </span>
                                        Jaminan
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ rupiah($data->omset_toko) }} </span>
                                        Omset Toko
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ rupiah($data->limitpel) }} </span>
                                        Limit Pelanggan
                                    </li>
                                </ul>
                                <div class="card-body">
                                    <a href="#" class="card-link">Card link</a>
                                    <a href="#" class="card-link">Another link</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9-col-md-9 col-sm-9">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <h4 class="card-title">Data Histori Transaksi Pelanggan</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="penjualan-tab" data-toggle="tab" href="#penjualan" aria-controls="penjualan" role="tab" aria-selected="true">Transaksi Penjualan</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="limitkredit-tab" data-toggle="tab" href="#limitkredit" aria-controls="limitkredit" role="tab" aria-selected="false">Pengajuan Limit Kredit</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="penjualan" aria-labelledby="penjualan-tab" role="tabpanel">

                                </div>
                                <div class="tab-pane" id="limitkredit" aria-labelledby="limitkredit-tab" role="tabpanel">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
