@extends('layouts.midone')
@section('titlepage', 'Detail Faktur')
@section('content')
    <style>
        @media only screen and (max-width: 800px) {
            table {
                font-size: 12px;
            }
        }
    </style>
    <style>
        #map {
            height: 180px;
        }
    </style>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Detail Faktur</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/penjualan">Penjualan</a></li>
                                <li class="breadcrumb-item"><a href="#">Detail Faktur</a></li>
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
                                            $path = Storage::url('pelanggan/' . $data->foto);
                                        @endphp
                                        @if (file_exists(url($path)))
                                            <img class="card-img img-fluid" src="{{ url($path) }}" alt="Card image" style="height:300px">
                                        @else
                                            <img class="card-img img-fluid" src="{{ asset('app-assets/images/slider/04.jpg') }}" alt="Card image">
                                        @endif
                                    @else
                                        <img class="card-img img-fluid" src="{{ asset('app-assets/images/slider/04.jpg') }}" alt="Card image">
                                    @endif
                                    <div class="card-img-overlay overflow-hidden overlay-primary overlay-lighten-2">
                                        <h4 class="card-title text-white">{{ $data->no_fak_penj }} -
                                            {{ strtoupper($data->jenistransaksi) }} - {{ strtoupper($data->jenisbayar) }}
                                        </h4>
                                        <p class="card-text text-white">{{ DateToIndo2($data->tgltransaksi) }}
                                        <h4 class="card-title text-white">{{ $data->nama_pelanggan }}</h4>
                                        <p class="card-text text-white">{{ $data->kode_pelanggan }} -
                                            {{ strtoupper($data->nama_cabang) }}
                                        </p>
                                        <p class="card-text"><small class="text-white">{{ $data->nama_karyawan }}</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (!empty($data->signature))
                        @php
                            $path = Storage::url('signature/' . $data->signature);
                        @endphp
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <img class="card-img" src="{{ url($path) }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div id="map"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (Auth::user()->level != 'salesman')
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <h4 class="card-title">Alamat</h4>
                                            <p class="card-text">
                                                {{ !empty($data->alamat_pelanggan) ? $data->alamat_pelanggan : $data->alamat_toko }}
                                            </p>
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
                                                <span
                                                    class="badge badge-pill bg-primary float-right">{{ date('d-F-y', strtotime($data->tgl_lahir)) }}</span>
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
                                                <span class="badge badge-pill bg-primary float-right">{{ $data->jatuhtempo }}
                                                    Hari</span>
                                                Jatuh Tempo Pembayaran
                                            </li>
                                            <li class="list-group-item">
                                                <span class="badge badge-pill bg-primary float-right">
                                                    @if ($data->cara_pembayaran == 1)
                                                        Bank Transfer
                                                    @elseif ($data->cara_pembayaran == 2)
                                                        Advance Cash
                                                    @elseif ($data->cara_pembayaran == 3)
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
                                                    @elseif ($data->status_outlet == 2)
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
                                                    @elseif ($data->type_outlet == 2)
                                                        Retail
                                                    @else
                                                        Belum Di Tentukan
                                                    @endif
                                                </span>
                                                Jenis Outlet
                                            </li>
                                            <li class="list-group-item">
                                                <span class="badge badge-pill bg-primary float-right">{{ $data->lama_usaha }}
                                                </span>
                                                Lama Usaha
                                            </li>
                                            <li class="list-group-item">
                                                <span class="badge badge-pill bg-primary float-right">
                                                    @if ($data->jaminan == 1)
                                                        Ada
                                                    @elseif ($data->jaminan == 2)
                                                        Tidak Ada
                                                    @else
                                                        Belum Di Tentukan
                                                    @endif
                                                </span>
                                                Jaminan
                                            </li>
                                            <li class="list-group-item">
                                                <span class="badge badge-pill bg-primary float-right">{{ $data->lama_langganan }}
                                                </span>
                                                Lama Berlangganan
                                            </li>
                                            <li class="list-group-item">
                                                <span class="badge badge-pill bg-primary float-right">
                                                    @if ($data->cara_pembayaran == 1)
                                                        Ada
                                                    @elseif ($data->cara_pembayaran == 2)
                                                        Tidak Ada
                                                    @else
                                                        Belum Di Tentukan
                                                    @endif
                                                </span>
                                                Jaminan
                                            </li>
                                            <li class="list-group-item">
                                                <span class="badge badge-pill bg-primary float-right">{{ rupiah($data->omset_toko) }}
                                                </span>
                                                Omset Toko
                                            </li>
                                            <li class="list-group-item">
                                                <span class="badge badge-pill bg-primary float-right">{{ rupiah($data->limitpel) }}
                                                </span>
                                                Limit Pelanggan
                                            </li>
                                            <li class="list-group-item">
                                                <span class="badge badge-pill bg-primary float-right">{{ $data->keterangan }}
                                                </span>
                                                Keterangan
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-lg-9 col-sm-12">
                    <div class="card overflow-hidden">
                        <div class="card-header">
                            <h4 class="card-title">
                                <div class="avatar bg-rgba-success p-50 m-0 mr-2">
                                    <div class="avatar-content">
                                        <i class="feather icon-shopping-cart text-success font-medium-5"></i>
                                    </div>
                                </div>
                                {{ rupiah($data->total) }}
                            </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                @include('layouts.notification')
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="penjualan-tab" data-toggle="tab" href="#penjualan" aria-controls="penjualan"
                                            role="tab" aria-selected="true">Data Penjualan</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="penjualan" aria-labelledby="penjualan-tab" role="tabpanel">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Nama Barang</th>
                                                        <th style="text-align:center">Dus</th>
                                                        <th>Harga/Dus</th>
                                                        <th class="text-center">Pack</th>
                                                        <th>Harga/Pack</th>
                                                        <th class="text-center">Pcs</th>
                                                        <th>Harga/Pcs</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $total = 0;
                                                    @endphp
                                                    @foreach ($detailpenjualan as $d)
                                                        @php
                                                            $jmldus = floor($d->jumlah / $d->isipcsdus);
                                                            $sisadus = $d->jumlah % $d->isipcsdus;

                                                            if ($d->isipack == 0) {
                                                                $jmlpack = 0;
                                                                $sisapack = $sisadus;
                                                            } else {
                                                                $jmlpack = floor($sisadus / $d->isipcs);
                                                                $sisapack = $sisadus % $d->isipcs;
                                                            }

                                                            $jmlpcs = $sisapack;
                                                            $total += $d->subtotal;
                                                        @endphp
                                                        <tr @if ($d->promo == 1) class="bg-warning" @endif>

                                                            <td>{{ $d->nama_barang }}</td>
                                                            <td class="text-center">{{ $jmldus }}</td>
                                                            <td class="text-right">{{ rupiah($d->harga_dus) }}</td>
                                                            <td class="text-center">{{ $jmlpack }}</td>
                                                            <td class="text-right">{{ rupiah($d->harga_pack) }}</td>
                                                            <td class="text-center">{{ $jmlpcs }}</td>
                                                            <td class="text-right">{{ rupiah($d->harga_pcs) }}</td>
                                                            <td class="text-right">{{ rupiah($d->subtotal) }}</td>
                                                        </tr>
                                                    @endforeach
                                                    <tr style="font-weight: bold">
                                                        <td colspan="7">Subtotal</td>
                                                        <td class="text-right">{{ rupiah($total) }}</td>
                                                    </tr>
                                                    <tr style="font-weight: bold">
                                                        <td colspan="7">Potongan</td>
                                                        <td class="text-right">{{ rupiah($data->potongan) }}</td>
                                                    </tr>
                                                    <tr style="font-weight: bold">
                                                        <td colspan="7">Potongan Istimewa</td>
                                                        <td class="text-right">{{ rupiah($data->potistimewa) }}</td>
                                                    </tr>
                                                    <tr style="font-weight: bold">
                                                        <td colspan="7">Penyesuaian</td>
                                                        <td class="text-right">{{ rupiah($data->penyharga) }}</td>
                                                    </tr>
                                                    <tr style="font-weight: bold">
                                                        <td colspan="7">Total</td>
                                                        <td class="text-right">
                                                            @php
                                                                $totalnonppn =
                                                                    $data->subtotal - $data->potongan - $data->potistimewa - $data->penyharga;
                                                            @endphp
                                                            {{ rupiah($totalnonppn) }}
                                                        </td>
                                                    </tr>
                                                    <tr style="font-weight: bold">
                                                        <td colspan="7">PPN</td>
                                                        <td class="text-right">
                                                            {{ rupiah($data->ppn) }}
                                                        </td>
                                                    </tr>
                                                    <tr style="font-weight: bold">
                                                        <td colspan="7">Grand Total</td>
                                                        <td class="text-right">
                                                            {{ rupiah($data->total) }}
                                                        </td>
                                                    </tr>
                                                    <tr style="font-weight: bold">
                                                        <td colspan="7">Retur</td>
                                                        <td class="text-right">{{ rupiah($data->totalretur) }}</td>
                                                    </tr>
                                                    <tr style="font-weight: bold">
                                                        <td colspan="7">Jumlah Bayar</td>
                                                        <td class="text-right">{{ rupiah($data->jmlbayar) }}</td>
                                                    </tr>
                                                    <tr style="font-weight: bold">
                                                        <td colspan="7">Sisa Bayar</td>
                                                        <td class="text-right">
                                                            @php
                                                                $sisabayar = $data->total - $data->totalretur - $data->jmlbayar;
                                                            @endphp
                                                            {{ rupiah($sisabayar) }}
                                                        </td>
                                                    </tr>
                                                    <tr style="font-weight: bold">
                                                        <td colspan="7">Keterangan</td>
                                                        <td class="text-right">
                                                            @if ($data->status_batal == 0)
                                                                @if ($sisabayar != 0)
                                                                    <span class="badge bg-danger">BELUM LUNAS</span>
                                                                @else
                                                                    <span class="badge bg-success">LUNAS</span>
                                                                @endif
                                                            @else
                                                                <span class="badge bg-danger">BATAL</span>
                                                            @endif

                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="retur-tab" data-toggle="tab" href="#retur" aria-controls="retur"
                                            role="tab" aria-selected="true">Data Retur</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="penjualan" aria-labelledby="penjualan-tab" role="tabpanel">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Tanggal</th>
                                                        <th>Kode Produk</th>
                                                        <th>Nama Barang</th>
                                                        <th>Jenis Retur</th>
                                                        <th class="text-center">Dus</th>
                                                        <th>Harga/Dus</th>
                                                        <th class="text-center">Pack</th>
                                                        <th>Harga/Pack</th>
                                                        <th class="text-center">Pcs</th>
                                                        <th>Harga/Pcs</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $totalpf = 0;
                                                        $totalgb = 0;
                                                        $total = 0;
                                                    @endphp
                                                    @foreach ($retur as $d)
                                                        @php
                                                            $jmldus = floor($d->jumlah / $d->isipcsdus);
                                                            $sisadus = $d->jumlah % $d->isipcsdus;

                                                            if ($d->isipack == 0) {
                                                                $jmlpack = 0;
                                                                $sisapack = $sisadus;
                                                            } else {
                                                                $jmlpack = floor($sisadus / $d->isipcs);
                                                                $sisapack = $sisadus % $d->isipcs;
                                                            }

                                                            $jmlpcs = $sisapack;
                                                            if ($d->jenis_retur == 'pf') {
                                                                $totalpf += $d->subtotal;
                                                            }

                                                            if ($d->jenis_retur == 'gb') {
                                                                $totalgb += $d->subtotal;
                                                            }

                                                            $total += $d->subtotal;

                                                        @endphp
                                                        <tr>
                                                            <td>{{ date('d-m-y', strtotime($d->tglretur)) }}</td>
                                                            <td>{{ $d->kode_produk }}</td>
                                                            <td>{{ $d->nama_barang }}</td>
                                                            <td class="text-center">
                                                                @if ($d->jenis_retur == 'pf')
                                                                    <span class="badge bg-danger">PF</span>
                                                                @else
                                                                    <span class="badge bg-success">GB</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">{{ $jmldus }}</td>
                                                            <td class="text-right">{{ rupiah($d->harga_dus) }}</td>
                                                            <td class="text-center">{{ $jmlpack }}</td>
                                                            <td class="text-right">{{ rupiah($d->harga_pack) }}</td>
                                                            <td class="text-center">{{ $jmlpcs }}</td>
                                                            <td class="text-right">{{ rupiah($d->harga_pcs) }}</td>
                                                            <td class="text-right">{{ rupiah($d->subtotal) }}</td>
                                                        </tr>
                                                    @endforeach
                                                    <tr style="font-weight: bold">
                                                        <td colspan="10">Retur Potong Faktur</td>
                                                        <td class="text-right">{{ rupiah($totalpf) }}</td>
                                                    </tr>
                                                    <tr style="font-weight: bold">
                                                        <td colspan="10">Retur Potong Ganti Barang</td>
                                                        <td class="text-right">{{ rupiah($totalgb) }}</td>
                                                    </tr>
                                                    <tr style="font-weight: bold">
                                                        <td colspan="10">Total Retur</td>
                                                        <td class="text-right">{{ rupiah($total - $totalgb) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="bayar-tab" data-toggle="tab" href="#bayar" aria-controls="bayar"
                                            role="tab" aria-selected="true">Histori
                                            Pembayaran</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="penjualan" aria-labelledby="penjualan-tab" role="tabpanel">
                                        @if ($data->status_lunas != 1 && $data->status_batal == 0)
                                            <a href="#" id="inputpembayaran" class="btn btn-primary mb-2" class="href"><i
                                                    class="feather icon-plus"></i></a>
                                        @endif
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>No. Bukti</th>
                                                        <th>Tanggal</th>
                                                        <th>Jenis Bayar</th>
                                                        <th>Jumlah</th>
                                                        <th>Keterangan</th>
                                                        <th>Penagih</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($historibayar as $d)
                                                        <tr>
                                                            <td>{{ $d->nobukti }}</td>
                                                            <td>{{ date('d-m-y', strtotime($d->tglbayar)) }}</td>
                                                            <td>{{ ucwords($d->jenisbayar) }}</td>
                                                            <td class="text-right">{{ rupiah($d->bayar) }}</td>
                                                            <td>
                                                                @if ($d->girotocash == 1)
                                                                    <span class="badge bg-success">Penggantian Giro Ke Cash
                                                                        {{ $d->no_giro }}</span>
                                                                @elseif($d->status_bayar == 'voucher')
                                                                    @if ($d->ket_voucher == 1)
                                                                        <span class="badge bg-success">{{ $d->status_bayar }}
                                                                            Penghapusan Piutang</span>
                                                                    @elseif($d->ket_voucher == 2)
                                                                        <span class="badge bg-success">{{ $d->status_bayar }}
                                                                            Diskon Program</span>
                                                                    @elseif($d->ket_voucher == 3)
                                                                        <span class="badge bg-success">{{ $d->status_bayar }}
                                                                            Penyelesaian Piutang Oleh Salesman</span>
                                                                    @elseif($d->ket_voucher == 4)
                                                                        <span class="badge bg-success">{{ $d->status_bayar }}
                                                                            Voucher Pengalihan Piutang Dgng Jd Piutang Kary
                                                                        </span>
                                                                    @elseif($d->ket_voucher == 7)
                                                                        <span class="badge bg-success">{{ $d->status_bayar }}
                                                                            Voucher PPN KPBPB </span>
                                                                    @elseif($d->ket_voucher == 8)
                                                                        <span class="badge bg-success">{{ $d->status_bayar }}
                                                                            Voucher PPN WAPU </span>
                                                                    @elseif($d->ket_voucher == 9)
                                                                        <span class="badge bg-success">{{ $d->status_bayar }}
                                                                            Voucher PPH PASAL 22 </span>
                                                                    @else
                                                                        <span class="badge bg-success">{{ $d->status_bayar }}
                                                                            Lainnya </span>
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if (empty($d->id_karyawan))
                                                                    {{ $data->nama_karyawan }}
                                                                @else
                                                                    {{ ucwords(strtolower($d->nama_karyawan)) }}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($d->jenisbayar == 'titipan' || $d->status_bayar == 'voucher')
                                                                    <div class="btn-group" role="group" aria-label="Basic example">
                                                                        <a class="ml-1 editbayar" href="#" nobukti="{{ $d->nobukti }}"
                                                                            kode_cabang="{{ $data->kode_cabang }}"
                                                                            no_fak_penj="{{ $data->no_fak_penj }}" sisabayar="{{ $sisabayar }}"><i
                                                                                class="feather icon-edit success"></i></a>

                                                                        <form method="POST" class="deleteform"
                                                                            action="/pembayaran/{{ Crypt::encrypt($d->nobukti) }}/delete">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <a href=" #" tanggal="{{ $d->tglbayar }}"
                                                                                class="delete-confirm ml-1">
                                                                                <i class="feather icon-trash danger"></i>
                                                                            </a>
                                                                        </form>

                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="giro-tab" data-toggle="tab" href="#giro" aria-controls="giro"
                                            role="tab" aria-selected="true">Histori Pembayaran
                                            Giro</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="giro" aria-labelledby="giro-tab" role="tabpanel">
                                        @if ($data->status_lunas != 1 && $data->jenisbayar != 'transfer' && $data->status_batal == 0)
                                            <a href="#" id="inputgiro" class="btn btn-primary mb-2" class="href"><i
                                                    class="feather icon-plus"></i></a>
                                        @endif
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>No. Giro</th>
                                                        <th>Tanggal</th>
                                                        <th>Bank</th>
                                                        <th>Jumlah</th>
                                                        <th>Jatuh Tempo</th>
                                                        <th>Status</th>
                                                        <th>Ket</th>
                                                        <th>Penagih</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($giro as $d)
                                                        <tr>
                                                            <td>{{ $d->no_giro }}</td>
                                                            <td>
                                                                @if (!empty($d->tgl_giro))
                                                                    {{ date('d-m-Y', strtotime($d->tgl_giro)) }}
                                                                @endif
                                                            </td>
                                                            <td>{{ $d->namabank }}</td>
                                                            <td class="text-right">{{ rupiah($d->jumlah) }}</td>
                                                            <td>
                                                                {{ date('d-m-Y', strtotime($d->tglcair)) }}
                                                            </td>
                                                            <td>
                                                                @if ($d->status == 0)
                                                                    <span class="badge bg-warning"> <i class="fa fa-history"></i> Pending </span>
                                                                @elseif($d->status == 1)
                                                                    <span class="badge bg-success"> <i class="fa fa-check"></i> Diterima
                                                                        {{ date('d-m-Y', strtotime($d->tglbayar)) }}
                                                                    </span>
                                                                @elseif($d->status == 2)
                                                                    <span class="badge bg-danger"> <i class="fa fa-close"></i> Ditolak</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $d->ket }}</td>
                                                            <td>{{ $d->nama_karyawan }}</td>
                                                            <td>
                                                                @if ($d->status === 0)
                                                                    <div class="btn-group" role="group" aria-label="Basic example">
                                                                        <a class="ml-1 editgiro" href="#" id_giro="{{ $d->id_giro }}"
                                                                            kode_cabang="{{ $data->kode_cabang }}" sisabayar="{{ $sisabayar }}"><i
                                                                                class="feather icon-edit success"></i></a>
                                                                        {{-- @if (in_array($level, $harga_hapus)) --}}
                                                                        <form method="POST" class="deleteform"
                                                                            action="/pembayaran/{{ Crypt::encrypt($d->id_giro) }}/deletegiro">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <a href=" #" tanggal="{{ $d->tgl_giro }}"
                                                                                class="delete-confirm ml-1">
                                                                                <i class="feather icon-trash danger"></i>
                                                                            </a>
                                                                        </form>
                                                                        {{-- @endif --}}
                                                                    </div>
                                                                @else
                                                                    <span class="badge bg-success">Keuangan</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="transfer-tab" data-toggle="tab" href="#transfer" aria-controls="transfer"
                                            role="tab" aria-selected="true">Histori
                                            Pembayaran Transfer</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="transfer" aria-labelledby="transfer-tab" role="tabpanel">
                                        @if ($data->status_lunas != 1 && $data->status_batal == 0)
                                            <a href="#" id="inputtransfer" class="btn btn-primary mb-2" class="href"><i
                                                    class="feather icon-plus"></i></a>
                                        @endif
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Tanggal</th>
                                                        <th>Bank</th>
                                                        <th>Jumlah</th>
                                                        <th>Jatuh Tempo</th>
                                                        <th>Status</th>
                                                        <th>Ket</th>
                                                        <th>Penagih</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($transfer as $d)
                                                        <tr>
                                                            <td>
                                                                @if (!empty($d->tgl_transfer))
                                                                    {{ date('d-m-Y', strtotime($d->tgl_transfer)) }}
                                                                @endif
                                                            </td>
                                                            <td>{{ $d->namabank }}</td>
                                                            <td class="text-right">{{ rupiah($d->jumlah) }}</td>
                                                            <td>
                                                                {{ date('d-m-Y', strtotime($d->tglcair)) }}
                                                            </td>
                                                            <td>
                                                                @if ($d->status == 0)
                                                                    <span class="badge bg-warning"> <i class="fa fa-history"></i> Pending </span>
                                                                @elseif($d->status == 1)
                                                                    <span class="badge bg-success"> <i class="fa fa-check"></i> Diterima
                                                                        {{ date('d-m-Y', strtotime($d->tglbayar)) }}
                                                                    </span>
                                                                @elseif($d->status == 2)
                                                                    <span class="badge bg-danger"> <i class="fa fa-close"></i> Ditolak</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $d->ket }}</td>
                                                            <td>{{ $d->nama_karyawan }}</td>
                                                            <td>
                                                                @if ($d->status === 0)
                                                                    <div class="btn-group" role="group" aria-label="Basic example">
                                                                        <a class="ml-1 edittransfer" href="#"
                                                                            id_transfer="{{ $d->id_transfer }}"
                                                                            kode_cabang="{{ $data->kode_cabang }}"
                                                                            sisabayar="{{ $sisabayar }}"><i
                                                                                class="feather icon-edit success"></i></a>

                                                                        <form method="POST" class="deleteform"
                                                                            action="/pembayaran/{{ Crypt::encrypt($d->id_transfer) }}/deletetransfer">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <a href=" #" tanggal="{{ $d->tgl_transfer }}"
                                                                                class="delete-confirm ml-1">
                                                                                <i class="feather icon-trash danger"></i>
                                                                            </a>
                                                                        </form>

                                                                    </div>
                                                                @else
                                                                    <span class="badge bg-success">Keuangan</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="cektutuplaporan">
    <!-- Input Pembayaran -->
    <div class="modal fade text-left" id="mdlinputpembayaran" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Input Pembayaran</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="/pembayaran/store" id="frmBayar" method="POST">
                        <input type="hidden" name="no_fak_penj" value="{{ $data->no_fak_penj }}">
                        <input type="hidden" name="jenistransaksi" id="jenistransaksi" value="{{ $data->jenistransaksi }}">
                        <input type="hidden" name="kode_cabang" id="kode_cabang" value="{{ $data->kode_cabang }}">
                        @csrf
                        <div class="form-body">
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="Tanggal Bayar" field="tglbayar" icon="feather icon-calendar" datepicker />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="Jumlah Bayar" field="bayar" icon="feather icon-shopping-cart" right />
                                </div>
                            </div>
                            @if ($data->jenistransaksi == 'kredit')
                                <input type="hidden" name="jenisbayar" value="titipan">
                            @else
                                <input type="hidden" name="jenisbayar" value="tunai">
                            @endif
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <select name="id_karyawan" id="id_karyawan" class="form-control">
                                            <option value="">Salesman Penagih</option>
                                            @foreach ($salesman as $d)
                                                <option value="{{ $d->id_karyawan }}">{{ $d->nama_karyawan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-12">
                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                        <input type="checkbox" class="voucher" name="voucher" value="voucher">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">Bayar Menggunakan Voucher ?</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="ketvoucher">
                                <div class="col-12">
                                    <div class="form-group">
                                        <select class="form-control" name="ket_voucher" id="ket_voucher">
                                            <option value="1">Penghapusan Piutang</option>
                                            <option value="2">Diskon Program</option>
                                            <option value="3">Penyelesaian Piutang Oleh Salesman</option>
                                            <option value="4">Pengalihan Piutang Dgng Jd Piutang Kary</option>
                                            <option value="6">Saus Premium TP 5-1</option>
                                            <option value="7">PPN KPBPB</option>
                                            <option value="8">PPN WAPU</option>
                                            <option value="9">PPH PASAL 22</option>
                                            <option value="5">Lainnya</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-12">
                                    <div class="vs-checkbox-con vs-checkbox-primary">

                                        <input type="checkbox" class="girotocash" name="girotocash" value="1">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">Ganti Giro Menjadi Cash ?</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="girotolak">
                                <div class="col-12">
                                    <div class="form-group">
                                        <select class="form-control" name="id_giro" id="id_giro">
                                            <option value="">Silahkan Pilih No. Giro</option>
                                            @foreach ($girotolak as $d)
                                                <option value="{{ $d->id_giro }}">{{ $d->no_giro }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <button class="btn btn-primary btn-block"><i class="feather icon-send"></i>
                                            Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Input Pembayaran -->
    <div class="modal fade text-left" id="mdleditpembayaran" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Edit Pembayaran</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadeditbayar"></div>
                </div>
            </div>
        </div>
    </div>


    <!--Input Bayar Giro-->

    <div class="modal fade text-left" id="mdlinputgiro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Input Giro</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="/pembayaran/storegiro" id="frmGiro" method="POST">
                        @csrf
                        <input type="hidden" name="no_fak_penj" value="{{ $data->no_fak_penj }}">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="Tanggal Pencatatan Giro" field="tgl_giro" icon="feather icon-calendar" datepicker />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <select name="id_karyawan" id="id_karyawan_giro" class="form-control">
                                            <option value="">Salesman</option>
                                            @foreach ($salesman as $d)
                                                <option value="{{ $d->id_karyawan }}">{{ $d->nama_karyawan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="No. Giro" field="no_giro" icon="feather icon-credit-card" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="Nama Bank" field="namabank_giro" icon="fa fa-bank" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="Jatuh Tempo" field="tglcair" icon="feather icon-calendar" datepicker />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="Jumlah" field="jumlah_giro" icon="feather icon-file" right />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <button class="btn btn-primary btn-block"><i class="feather icon-send"></i>
                                            Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Giro -->

    <div class="modal fade text-left" id="mdleditgiro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Edit Giro</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadeditgiro"></div>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Giro -->

    <div class="modal fade text-left" id="mdledittransfer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Edit Transfer</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadedittransfer"></div>
                </div>
            </div>
        </div>
    </div>


    <!--Input Bayar Transfer-->

    <div class="modal fade text-left" id="mdlinputtransfer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Input Transfer</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="/pembayaran/storetransfer" id="frmTransfer" method="POST">
                        @csrf
                        <input type="hidden" name="no_fak_penj" value="{{ $data->no_fak_penj }}">
                        <input type="hidden" name="kode_pelanggan" value="{{ $data->kode_pelanggan }}">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="Tanggal Pencatatan Transfer" field="tgl_transfer" icon="feather icon-calendar" datepicker />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <select name="id_karyawan" id="id_karyawan_transfer" class="form-control">
                                            <option value="">Salesman</option>
                                            @foreach ($salesman as $d)
                                                <option value="{{ $d->id_karyawan }}">{{ $d->nama_karyawan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="Nama Bank" field="namabank_transfer" icon="fa fa-bank" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="Jatuh Tempo" field="tglcair_transfer" icon="feather icon-calendar" datepicker />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="Jumlah" field="jumlah_transfer" icon="feather icon-file" right />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="Keterangan" field="ket" icon="feather icon-file" right />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <button class="btn btn-primary btn-block"><i class="feather icon-send"></i>
                                            Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        var latitude = "{{ !empty($data->latitude) ? $data->latitude : '-7.3665114' }}";
        var longitude = "{{ !empty($data->longitude) ? $data->longitude : '108.2148793' }}";
        var latitudecheckin = "{{ $checkin != null ? $checkin->latitude : '-7.3665114' }}";
        var longitudecheckin = "{{ $checkin != null ? $checkin->longitude : '108.2148793' }}";
        var markericon = "{{ $data->marker }}";

        var map = L.map('map').setView([latitude, longitude], 18);
        L.tileLayer('http://{s}.google.com/vt?lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        }).addTo(map);
        var marker = L.marker([latitude, longitude]).addTo(map);
        var salesmanicon = L.icon({
            iconUrl: '/app-assets/marker/' + markericon,
            iconSize: [75, 75], // size of the icon
            shadowSize: [50, 64], // size of the shadow
            iconAnchor: [22, 94], // point of the icon which will correspond to marker's location
            shadowAnchor: [4, 62], // the same for the shadow
            popupAnchor: [-3, -76] // point from which the popup should open relative to the iconAnchor
        });
        var marker = L.marker([latitudecheckin, longitudecheckin], {
            icon: salesmanicon
        }).addTo(map);

        var polygon = L.polygon([
            [latitude, longitude],
            [latitudecheckin, longitudecheckin]
        ]).addTo(map);
    </script>
    <script>
        $(function() {

            $('.delete-confirm').click(function(event) {
                event.preventDefault();
                var form = $(this).closest("form");
                var name = $(this).data("name");
                var tanggal = $(this).attr("tanggal");
                cektutuplaporan(tanggal);

                swal({
                        title: `Are you sure you want to delete this record?`,
                        text: "If you delete this, it will be gone forever.",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            var cektutup = $("#cektutuplaporan").val();
                            if (cektutup > 0) {
                                swal("Oops", "Laporan Periode Ini Sudah Di Tutup !", "warning");
                            } else {
                                form.submit();
                            }
                        }
                    });
            });

            $("#inputpembayaran").click(function(e) {
                e.preventDefault();
                $('#mdlinputpembayaran').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            });

            //Input Giro
            $("#inputgiro").click(function(e) {
                e.preventDefault();
                $('#mdlinputgiro').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            });

            //Input Transfer
            $("#inputtransfer").click(function(e) {
                e.preventDefault();
                $('#mdlinputtransfer').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            });

            $(".editbayar").click(function(e) {
                e.preventDefault();
                var nobukti = $(this).attr('nobukti');
                var kode_cabang = $(this).attr('kode_cabang');
                var no_fak_penj = $(this).attr('no_fak_penj');
                var sisabayar = $(this).attr('sisabayar');
                // alert(sisabayar);
                $.ajax({
                    type: 'POST',
                    url: '/pembayaran/edit',
                    data: {
                        _token: "{{ csrf_token() }}",
                        nobukti: nobukti,
                        kode_cabang: kode_cabang,
                        no_fak_penj: no_fak_penj,
                        sisabayar: sisabayar
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loadeditbayar").html(respond);
                        $('#mdleditpembayaran').modal({
                            backdrop: 'static',
                            keyboard: false
                        });
                    }
                });

            });

            //Edit Giro
            $(".editgiro").click(function(e) {
                e.preventDefault();
                var id_giro = $(this).attr('id_giro');
                var kode_cabang = $(this).attr('kode_cabang');
                var sisabayar = $(this).attr('sisabayar');
                $.ajax({
                    type: 'POST',
                    url: '/pembayaran/editgiro',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id_giro: id_giro,
                        kode_cabang: kode_cabang,
                        sisabayar: sisabayar
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loadeditgiro").html(respond);
                        $('#mdleditgiro').modal({
                            backdrop: 'static',
                            keyboard: false
                        });
                    }
                });

            });


            //Edit Transfer
            $(".edittransfer").click(function(e) {
                e.preventDefault();
                var id_transfer = $(this).attr('id_transfer');
                var kode_cabang = $(this).attr('kode_cabang');
                var sisabayar = $(this).attr('sisabayar');
                var kode_pelanggan = "{{ $data->kode_pelanggan }}"


                $.ajax({
                    type: 'POST',
                    url: '/pembayaran/edittransfer',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id_transfer: id_transfer,
                        kode_cabang: kode_cabang,
                        kode_pelanggan: kode_pelanggan,
                        sisabayar: sisabayar
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loadedittransfer").html(respond);
                        $('#mdledittransfer').modal({
                            backdrop: 'static',
                            keyboard: false
                        });
                    }
                });

            });


            $("#ketvoucher").hide();
            $("#girotolak").hide();
            $('.voucher').change(function() {
                if (this.checked) {
                    $("#ketvoucher").show();
                } else {
                    $("#ketvoucher").hide();
                }

            });

            $('.girotocash').change(function() {
                if (this.checked) {
                    $("#girotolak").show();
                } else {
                    $("#girotolak").hide();
                }

            });

            $("#bayar").maskMoney();
            $("#jumlah_giro").maskMoney();
            $("#jumlah_transfer").maskMoney();

            function cektutuplaporan(tanggal) {
                $.ajax({
                    type: "POST",
                    url: "/cektutuplaporan",
                    data: {
                        _token: "{{ csrf_token() }}",
                        tanggal: tanggal,
                        jenislaporan: "penjualan"
                    },
                    cache: false,
                    success: function(respond) {
                        console.log(respond);
                        $("#cektutuplaporan").val(respond);
                    }
                });
            }

            $("#tglbayar").change(function() {
                cektutuplaporan($(this).val());
            });
            $("#frmBayar").submit(function(e) {
                //e.preventDefault();
                var tglbayar = $("#tglbayar").val();
                var bayar = $("#bayar").val();
                var id_karyawan = $("#id_karyawan").val();
                var id_giro = $("#id_giro").val();
                var sisabayar = "{{ $sisabayar }}";
                var cektutuplaporan = $("#cektutuplaporan").val();
                var jmlbayar = parseInt(bayar.replace(/\./g, ''));
                var sisabayar = "{{ $sisabayar }}";

                //alert(sisabayar);
                if (cektutuplaporan > 0) {
                    swal("Peringatan", "Laporan Periode Ini Sudah Ditutup !", "warning");
                    return false;
                } else if (tglbayar == "") {
                    swal({
                        title: 'Oops',
                        text: 'Tanggal Bayar Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#tglbayar").focus();
                    });
                    return false;
                } else if (bayar == "" || bayar === 0) {
                    swal({
                        title: 'Oops',
                        text: 'Jumlah Bayar Harus Diisi  !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#bayar").focus();
                    });
                    return false;
                } else if (id_karyawan == "") {
                    swal({
                        title: 'Oops',
                        text: 'Salesman Penagih Harus Diisi  !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#id_karyawan").focus();
                    });
                    return false;
                } else if ($(".girotocash").is(':checked') && id_giro == "") {
                    swal({
                        title: 'Oops',
                        text: 'No. Giro Harus Dipilih  !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#id_giro").focus();
                    });
                    return false;
                } else if (jmlbayar > parseInt(sisabayar)) {
                    swal({
                        title: 'Oops',
                        text: 'Jumlah Bayar Melebihi Sisa Bayar  !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#bayar").focus();
                    });
                    return false;
                } else {
                    return true;
                }
            })


            $("#tgl_giro").change(function() {
                cektutuplaporan($(this).val());
            });
            $("#frmGiro").submit(function(e) {
                //e.preventDefault();
                var tgl_giro = $("#tgl_giro").val();
                var id_karyawan = $("#id_karyawan_giro").val();
                var no_giro = $("#no_giro").val();
                var namabank = $("#namabank_giro").val();
                var tglcair = $("#tglcair").val();
                var sisabayar = "{{ $sisabayar }}";
                var jumlah = $("#jumlah_giro").val();
                var jmlbayar = parseInt(jumlah.replace(/\./g, ''));
                var cektutuplaporan = $("#cektutuplaporan").val();
                var sisabayar = "{{ $sisabayar }}";
                //alert(sisabayar);
                if (cektutuplaporan > 0) {
                    swal("Peringatan", "Laporan Periode Ini Sudah Ditutup !", "warning");
                    return false;
                } else if (tgl_giro == "") {
                    swal({
                        title: 'Oops',
                        text: 'Tanggal Giro Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#tgl_giro").focus();
                    });
                    return false;
                } else if (id_karyawan == "") {
                    swal({
                        title: 'Oops',
                        text: 'Salesman Penagih Harus Diisi  !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#id_karyawan_giro").focus();
                    });
                    return false;
                } else if (no_giro == "") {
                    swal({
                        title: 'Oops',
                        text: 'No. Giro Harus Diisi  !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#no_giro").focus();
                    });
                    return false;
                } else if (namabank == "") {
                    swal({
                        title: 'Oops',
                        text: 'Bank Harus Diisi  !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#namabank_giro").focus();
                    });
                    return false;
                } else if (tglcair == "") {
                    swal({
                        title: 'Oops',
                        text: 'Jatuh Temp Harus Diisi   !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#tglcair").focus();
                    });
                    return false;
                } else if (jumlah == "" || jumlah === 0) {
                    swal({
                        title: 'Oops',
                        text: 'Jumlah  Harus Diisi  !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#jumlah_giro").focus();
                    });
                    return false;
                } else if (jmlbayar > parseInt(sisabayar)) {
                    swal({
                        title: 'Oops',
                        text: 'Jumlah Bayar Melebihi Sisa Bayar  !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#jumlah_giro").focus();
                    });
                    return false;
                } else {
                    return true;
                }
            });

            $("#tgl_transfer").change(function() {
                cektutuplaporan($(this).val());
            });
            $("#frmTransfer").submit(function(e) {
                //e.preventDefault();
                var tgl_transfer = $("#tgl_transfer").val();
                var id_karyawan = $("#id_karyawan_transfer").val();
                var namabank = $("#namabank_transfer").val();
                var tglcair = $("#tglcair_transfer").val();
                var sisabayar = "{{ $sisabayar }}";
                var jumlah = $("#jumlah_transfer").val();
                var jmlbayar = parseInt(jumlah.replace(/\./g, ''));
                var cektutuplaporan = $("#cektutuplaporan").val();
                var sisabayar = "{{ $sisabayar }}";
                //alert(sisabayar);
                if (cektutuplaporan > 0) {
                    swal("Peringatan", "Laporan Periode Ini Sudah Ditutup !", "warning");
                    return false;
                } else if (tgl_transfer == "") {
                    swal({
                        title: 'Oops',
                        text: 'Tanggal Transfer Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#tgl_transfer").focus();
                    });
                    return false;
                } else if (id_karyawan == "") {
                    swal({
                        title: 'Oops',
                        text: 'Salesman Penagih Harus Diisi  !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#id_karyawan_transfer").focus();
                    });
                    return false;
                } else if (namabank == "") {
                    swal({
                        title: 'Oops',
                        text: 'Bank Harus Diisi  !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#namabank_transfer").focus();
                    });
                    return false;
                } else if (tglcair == "") {
                    swal({
                        title: 'Oops',
                        text: 'Jatuh Tempo Harus Diisi   !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#tglcair_transfer").focus();
                    });
                    return false;
                } else if (jumlah == "" || jumlah === 0) {
                    swal({
                        title: 'Oops',
                        text: 'Jumlah  Harus Diisi  !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#jumlah_transfer").focus();
                    });
                    return false;
                } else if (jmlbayar > parseInt(sisabayar)) {
                    swal({
                        title: 'Oops',
                        text: 'Jumlah Bayar Melebihi Sisa Bayar  !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#jumlah_transfer").focus();
                    });
                    return false;
                } else {
                    return true;
                }
            });
        });
    </script>
@endpush
