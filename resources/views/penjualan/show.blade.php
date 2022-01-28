@extends('layouts.midone')
@section('titlepage', 'Detail Faktur')
@section('content')
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
                                $path = Storage::url('pelanggan/'.$data->foto);
                                @endphp
                                <img class="card-img img-fluid" src="{{ url($path) }}" alt="Card image">
                                @else
                                <img class="card-img img-fluid" src="{{ asset('app-assets/images/slider/04.jpg') }}" alt="Card image">
                                @endif
                                <div class="card-img-overlay overflow-hidden overlay-primary overlay-lighten-2">
                                    <h4 class="card-title text-white">{{ $data->no_fak_penj }} - {{ strtoupper($data->jenistransaksi) }}</h4>
                                    <p class="card-text text-white">{{ DateToIndo2($data->tgltransaksi) }}
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9-col-md-9 col-sm-9">
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
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="penjualan-tab" data-toggle="tab" href="#penjualan" aria-controls="penjualan" role="tab" aria-selected="true">Data Penjualan</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="penjualan" aria-labelledby="penjualan-tab" role="tabpanel">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Kode Barang</th>
                                                <th>Nama Barang</th>
                                                <th style="text-align:center">Dus/Ball</th>
                                                <th>Harga/Dus/Ball</th>
                                                <th class="text-center">Pack</th>
                                                <th>Harga/Pack</th>
                                                <th class="text-center">Pcs</th>
                                                <th>Harga/Pcs</th>
                                                <th>Total</th>
                                                <th></th>
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
                                            <tr>
                                                <td>{{ $d->kode_produk }}</td>
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
                                                <td colspan="8">Subtotal</td>
                                                <td class="text-right">{{ rupiah($total) }}</td>
                                            </tr>
                                            <tr style="font-weight: bold">
                                                <td colspan="8">Potongan</td>
                                                <td class="text-right">{{ rupiah($data->potongan) }}</td>
                                            </tr>
                                            <tr style="font-weight: bold">
                                                <td colspan="8">Potongan Istimewa</td>
                                                <td class="text-right">{{ rupiah($data->potistimewa) }}</td>
                                            </tr>
                                            <tr style="font-weight: bold">
                                                <td colspan="8">Penyesuaian</td>
                                                <td class="text-right">{{ rupiah($data->penyharga) }}</td>
                                            </tr>
                                            <tr style="font-weight: bold">
                                                <td colspan="8">Total</td>
                                                <td class="text-right">{{ rupiah($data->total) }}</td>
                                            </tr>
                                            <tr style="font-weight: bold">
                                                <td colspan="8">Retur</td>
                                                <td class="text-right">{{ rupiah($data->totalretur) }}</td>
                                            </tr>
                                            <tr style="font-weight: bold">
                                                <td colspan="8">Jumlah Bayar</td>
                                                <td class="text-right">{{ rupiah($data->jmlbayar) }}</td>
                                            </tr>
                                            <tr style="font-weight: bold">
                                                <td colspan="8">Sisa Bayar</td>
                                                <td class="text-right">
                                                    @php
                                                    $sisabayar = $data->total - $data->totalretur - $data->jmlbayar;
                                                    @endphp
                                                    {{ rupiah($sisabayar) }}
                                                </td>
                                            </tr>
                                            <tr style="font-weight: bold">
                                                <td colspan="8">Keterangan</td>
                                                <td class="text-right">
                                                    @if ($sisabayar != 0)
                                                    <span class="badge bg-danger">BELUM LUNAS</span>
                                                    @else
                                                    <span class="badge bg-success">LUNAS</span>
                                                    @endif
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="retur-tab" data-toggle="tab" href="#retur" aria-controls="retur" role="tab" aria-selected="true">Data Retur</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="penjualan" aria-labelledby="penjualan-tab" role="tabpanel">
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
                                            if($d->jenis_retur=="pf"){
                                            $totalpf += $d->subtotal;
                                            }

                                            if($d->jenis_retur=="gb"){
                                            $totalgb += $d->subtotal;
                                            }

                                            $total += $d->subtotal;

                                            @endphp
                                            <tr>
                                                <td>{{ date("d-m-y",strtotime($d->tglretur)) }}</td>
                                                <td>{{ $d->kode_produk }}</td>
                                                <td>{{ $d->nama_barang }}</td>
                                                <td class="text-center">
                                                    @if ($d->jenis_retur=="pf")
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
                                                <td class="text-right">{{ rupiah($total-$totalgb) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="bayar-tab" data-toggle="tab" href="#bayar" aria-controls="bayar" role="tab" aria-selected="true">Histori Pembayaran</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="penjualan" aria-labelledby="penjualan-tab" role="tabpanel">
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
                                                <td>{{ date("d-m-y",strtotime($d->tglbayar)) }}</td>
                                                <td>{{ ucwords($d->jenisbayar) }}</td>
                                                <td class="text-right">{{ rupiah($d->bayar) }}</td>
                                                <td>
                                                    @if ($d->girotocash==1)
                                                    <span class="badge bg-success">Penggantian Giro Ke Cash {{ $d->no_giro }}</span>
                                                    @elseif($d->status_bayar=="voucher")
                                                    @if ($d->ket_voucher == 1)
                                                    <span class="badge bg-success">{{ $d->status_bayar }} Penghapusan Piutang</span>
                                                    @elseif($d->ket_voucher==2)
                                                    <span class="badge bg-success">{{ $d->status_bayar }} Diskon Program</span>
                                                    @elseif($d->ket_voucher==3)
                                                    <span class="badge bg-success">{{ $d->status_bayar }} Penyelesaian Piutang Oleh Salesman</span>
                                                    @elseif($d->ket_voucher==4)
                                                    <span class="badge bg-success">{{ $d->status_bayar }} Voucher Pengalihan Piutang Dgng Jd Piutang Kary </span>
                                                    @else
                                                    <span class="badge bg-success">{{ $d->status_bayar }} Lainnya </span>
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
                                                    <div class="btn-group" role="group" aria-label="Basic example">
                                                        <a class="ml-1" href="#"><i class="feather icon-edit success"></i></a>
                                                        @if (in_array($level,$harga_hapus))
                                                        <form method="POST" class="deleteform" action="#">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a href=" #" class="delete-confirm ml-1">
                                                                <i class="feather icon-trash danger"></i>
                                                            </a>
                                                        </form>
                                                        @endif
                                                    </div>
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
@endsection
