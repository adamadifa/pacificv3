@extends('layouts.midone')
@section('titlepage', 'Detail Faktur')
@section('content')
<link rel="stylesheet" href="{{ asset('app-assets/signature/signature.css') }}">
<style>
    @media only screen and (max-width: 800px) {
        table {
            font-size: 12px;
        }
    }

    .kbw-signature {
        width: 100%;
        height: 200px;
    }

    #sign canvas {
        width: 100% !important;
        height: auto;
    }

</style>
<style>
    .card {
        margin-bottom: 1rem !important;
    }

</style>
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h4 class="content-header-title float-left mb-0">Detail Faktur</h4>
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
                                <img class="card-img img-fluid" style="height: 200px; object-fit:cover" src="{{ url($path) }}" alt="Card image">
                                @else
                                <img class="card-img img-fluid" src="{{ asset('app-assets/images/slider/04.jpg') }}" alt="Card image">
                                @endif
                                <div class="card-img-overlay overflow-hidden overlay-primary overlay-lighten-2">
                                    <h4 class="card-title text-white">{{ $data->no_fak_penj }} - {{ strtoupper($data->jenistransaksi) }} - {{ strtoupper($data->jenisbayar) }}</h4>
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
                <div class="row mb-1">
                    <div class="col d-flex justify-content-arround">
                        <a href="/penjualan/{{ Crypt::encrypt($data->no_fak_penj) }}/editv2" class="btn  btn-success mr-1">
                            <i class="feather icon-edit"></i>
                        </a>
                        <a href="#" class="btn btn-info btn-block" id="cetakfaktur">
                            <i class="feather icon-printer mr-1"></i>
                            Cetak Faktur
                        </a>
                        <form method="POST" class="deleteform" action="/penjualan/{{ Crypt::encrypt($data->no_fak_penj) }}/delete">
                            @csrf
                            @method('DELETE')
                            <a href=" #" tanggal="{{ $data->tgltransaksi }}" class="btn btn-danger  delete-confirm ml-1">
                                <i class="feather icon-trash"></i>
                            </a>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        @php
                        $path = Storage::url('signature/'.$data->signature);
                        @endphp
                        @if (!empty($data->signature))
                        <div class="row mb-1">
                            <div class="col-12">
                                <img class="card-img img-fluid" src="{{ url($path) }}" alt="Card image">
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-2">
                                <form method="POST" class="deleteform" action="/penjualan/{{ Crypt::encrypt($data->no_fak_penj) }}/deletesignature">
                                    @csrf
                                    @method('DELETE')
                                    <a href=" #" tanggal="{{ $data->tgltransaksi }}" class="btn btn-danger btn-block  delete-confirm">
                                        <i class="feather icon-trash"></i>
                                    </a>
                                </form>
                            </div>
                            @php
                            $file_path = storage_path('signature/'.$data->signature);
                            $image = base64_encode($path);
                            @endphp
                            <div class="col-10">
                                <a href="#" onclick="return sendUrlToPrint('{{ url($path) }}');" class="btn btn-info btn-block"><i class="feather icon-printer mr-1"></i>Cetak Tanda Tangan</a>
                            </div>
                        </div>

                        @else
                        <form action="/penjualan/uploadsignature" id="frmSignature" method="POST">
                            @csrf
                            <input type="hidden" value="{{ $data->no_fak_penj }}" name="no_fak_penj">
                            <div class="row">
                                <div class="col-12">
                                    <div id="sign"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <textarea style="display: none" name="signed" id="signature" cols="30" rows="10"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <div class="btn-group d-flex justify-content-center">
                                            <button id="clear" class="btn btn-danger">Clear</button>
                                            <button id="save" class="btn btn-success">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>
                @if (Auth::user()->level != "salesman")
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
                                    <a class="nav-link active" id="penjualan-tab" data-toggle="tab" href="#penjualan" aria-controls="penjualan" role="tab" aria-selected="true">Data Penjualan</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="penjualan" aria-labelledby="penjualan-tab" role="tabpanel">
                                    <table class="table table-bordered">
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
                                            <tr @if ($d->promo ==1)
                                                class="bg-warning"
                                                @endif>

                                                <td colspan="8" style="font-weight: bold">{{ $d->kode_produk }} - {{ $d->nama_barang }}</td>
                                            </tr>
                                            @if (!empty($jmldus))
                                            <tr @if ($d->promo ==1)
                                                class="bg-warning"
                                                @endif>
                                                <td colspan="7">{{ $jmldus }} Dus x {{ rupiah($d->harga_dus) }}</td>
                                                <td style="font-weight: bold; text-align:right">{{ rupiah($jmldus * $d->harga_dus) }}</td>
                                            </tr>
                                            @endif
                                            @if (!empty($jmlpack))
                                            <tr @if ($d->promo ==1)
                                                class="bg-warning"
                                                @endif>
                                                <td colspan="7">{{ $jmlpack }} Pack x {{ rupiah($d->harga_pack) }}</td>
                                                <td style="font-weight: bold; text-align:right">{{ rupiah($jmlpack * $d->harga_pack) }}</td>
                                            </tr>
                                            @endif

                                            @if (!empty($jmlpcs))
                                            <tr @if ($d->promo ==1)
                                                class="bg-warning"
                                                @endif>
                                                <td colspan="7">{{ $jmlpcs }} Pcs x {{ rupiah($d->harga_pcs) }}</td>
                                                <td style="font-weight: bold; text-align:right">{{ rupiah($jmlpcs * $d->harga_pcs) }}</td>
                                            </tr>
                                            @endif
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
                                                    $totalnonppn = $data->subtotal - $data->potongan - $data->potistimewa - $data->penyharga;
                                                    @endphp
                                                    {{ rupiah($totalnonppn)  }}
                                                </td>
                                            </tr>
                                            <tr style="font-weight: bold">
                                                <td colspan="7">PPN</td>
                                                <td class="text-right">{{ rupiah($data->ppn) }}</td>
                                            </tr>
                                            <tr style="font-weight: bold">
                                                <td colspan="7">Grand Total</td>
                                                <td class="text-right">{{ rupiah($data->total) }}</td>
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
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="bayar-tab" data-toggle="tab" href="#bayar" aria-controls="bayar" role="tab" aria-selected="true">Histori Pembayaran</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="penjualan" aria-labelledby="penjualan-tab" role="tabpanel">
                                @if ($data->status_lunas != 1)
                                <a href="#" id="inputpembayaran" class="btn btn-md btn-primary mb-2" class="href">
                                    <i class="feather icon-plus mr-1"></i>
                                    Input Pembayaran
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        @foreach ($historibayar as $d)

                        <div class="row">
                            <div class="col-12">
                                <div class="card border-success">
                                    <div class="card-content">
                                        <div class="card-body" style="padding:8px 10px 8px 8px !important">
                                            <div class="card-text d-flex justify-content-between">
                                                <div class="ml-1">
                                                    <b>{{ $d->nobukti }}</b> <br> {{ DateToIndo2($d->tglbayar) }}
                                                </div>
                                                <div>
                                                    <span style="font-weight: bold">{{rupiah($d->bayar)}}</span>
                                                    <br>
                                                    <span class="badge bg-info">{{ $d->jenisbayar }}</span>
                                                </div>
                                                <div class="btn-group dropup float-left">
                                                    <i class="feather icon-more-vertical dropdown-toggle mr-2 mt-1 primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                                                    <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -7px, 0px);">

                                                        <a class="ml-1 editbayar dropdown-item" href="#" nobukti="{{ $d->nobukti; }}" kode_cabang="{{ $data->kode_cabang }}" no_fak_penj="{{ $data->no_fak_penj }}" sisabayar="{{ $sisabayar - $d->bayar }}">
                                                            <i class="feather icon-edit success mr-1"></i>
                                                            Edit
                                                        </a>
                                                        <form method="POST" class="deleteform" action="/pembayaran/{{ Crypt::encrypt($d->nobukti) }}/delete">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a href="#" tanggal="{{ $d->tglbayar }}" class=" dropdown-item delete-confirm ml-1">
                                                                <i class="feather icon-trash danger mr-1"></i> Hapus
                                                            </a>
                                                        </form>

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
                <div class="row">
                    <div class="col-12">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="giro-tab" data-toggle="tab" href="#giro" aria-controls="giro" role="tab" aria-selected="true">Histori Pembayaran Giro</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="giro" aria-labelledby="giro-tab" role="tabpanel">
                                @if ($data->status_lunas != 1 && $data->jenisbayar != "transfer" )
                                <a href="#" id="inputgiro" class="btn btn-md btn-primary mb-2" class="href"><i class="feather icon-plus mr-1"></i> Input Giro</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        @foreach ($giro as $d)
                        <div class="row">
                            <div class="col-12">
                                <div class="card border-success">
                                    <div class="card-content">
                                        <div class="card-body" style="padding:8px 10px 8px 8px !important">
                                            <div class="card-text d-flex justify-content-between">
                                                <div class="ml-1">
                                                    <b>{{ $d->no_giro }}</b> - <span class="badge bg-info">{{ $d->namabank }}</span> <br> {{ !empty($d->tgl_giro) ? DateToIndo2($d->tgl_giro) : '' }}
                                                </div>
                                                <div>
                                                    <span style="font-weight: bold">{{rupiah($d->jumlah)}}</span>
                                                    <br>
                                                    @if ($d->status==0)
                                                    <span class="badge bg-warning"> <i class="fa fa-history"></i> Pending </span>
                                                    @elseif($d->status==1)
                                                    <span class="badge bg-success"> <i class="fa fa-check"></i> Diterima {{date("d-m-Y",strtotime( $d->tglbayar)) }} </span>
                                                    @elseif($d->status==2)
                                                    <span class="badge bg-danger"> <i class="fa fa-close"></i> Ditolak</span>
                                                    @endif
                                                </div>
                                                @if ($d->status===0)
                                                <div class="btn-group dropup float-left">
                                                    <i class="feather icon-more-vertical dropdown-toggle mr-2 mt-1 primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                                                    <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -7px, 0px);">

                                                        <a class="ml-1 editgiro dropdown-item" href="#" id_giro="{{ $d->id_giro; }}" kode_cabang="{{ $data->kode_cabang }}" sisabayar="{{ $sisabayar - $d->jumlah }}"><i class="feather icon-edit success mr-1"></i>Edit</a>
                                                        {{-- @if (in_array($level,$harga_hapus)) --}}
                                                        <form method="POST" class="deleteform" action="/pembayaran/{{ Crypt::encrypt($d->id_giro) }}/deletegiro">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a href=" #" tanggal="{{ $d->tgl_giro }}" class="delete-confirm dropdown-item ml-1">
                                                                <i class="feather icon-trash danger mr-1"></i> Hapus
                                                            </a>
                                                        </form>

                                                    </div>
                                                </div>
                                                @else
                                                <div>
                                                    <span class="badge bg-success">Keuangan</span>
                                                </div>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="giro-tab" data-toggle="tab" href="#giro" aria-controls="giro" role="tab" aria-selected="true">Histori Pembayaran Transfer</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="giro" aria-labelledby="giro-tab" role="tabpanel">
                                @if ($data->status_lunas != 1)
                                <a href="#" id="inputtransfer" class="btn btn-primary mb-2" class="href"><i class="feather icon-plus mr-1"></i>Input Transfer</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        @foreach ($transfer as $d)
                        <div class="row">
                            <div class="col-12">
                                <div class="card border-success">
                                    <div class="card-content">
                                        <div class="card-body" style="padding:8px 10px 8px 8px !important">
                                            <div class="card-text d-flex justify-content-between">
                                                <div class="ml-1">
                                                    <b>{{ $d->namabank }}</b><br> {{ !empty($d->tgl_transfer) ? DateToIndo2($d->tgl_transfer) : '' }}
                                                </div>
                                                <div>
                                                    <span style="font-weight: bold">{{rupiah($d->jumlah)}}</span>
                                                    <br>
                                                    @if ($d->status==0)
                                                    <span class="badge bg-warning"> <i class="fa fa-history"></i> Pending </span>
                                                    @elseif($d->status==1)
                                                    <span class="badge bg-success"> <i class="fa fa-check"></i> Diterima {{date("d-m-Y",strtotime( $d->tglbayar)) }} </span>
                                                    @elseif($d->status==2)
                                                    <span class="badge bg-danger"> <i class="fa fa-close"></i> Ditolak</span>
                                                    @endif
                                                </div>
                                                @if ($d->status===0)
                                                <div class="btn-group dropup float-left">
                                                    <i class="feather icon-more-vertical dropdown-toggle mr-2 mt-1 primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                                                    <div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -7px, 0px);">

                                                        <a class="ml-1 edittransfer dropdown-item" href="#" id_transfer="{{ $d->id_transfer; }}" kode_cabang="{{ $data->kode_cabang }}" sisabayar="{{ $sisabayar - $d->jumlah }}"><i class="feather icon-edit success mr-1"></i>Edit</a>

                                                        <form method="POST" class="deleteform" action="/pembayaran/{{ Crypt::encrypt($d->id_transfer) }}/deletetransfer">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a href=" #" tanggal="{{ $d->tgl_transfer }}" class="delete-confirm ml-1 dropdown-item">
                                                                <i class="feather icon-trash danger mr-1"></i> Hapus
                                                            </a>
                                                        </form>

                                                    </div>
                                                </div>
                                                @else
                                                <div>
                                                    <span class="badge bg-success">Keuangan</span>
                                                </div>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
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
                                <x-inputtext label="Tanggal Bayar" value="{{ date('Y-m-d') }}" field="tglbayar" icon="feather icon-calendar" datepicker />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <x-inputtext label="Jumlah Bayar" field="bayar" icon="feather icon-shopping-cart" right />
                            </div>
                        </div>
                        @if ($data->jenistransaksi=="kredit")
                        <input type="hidden" name="jenisbayar" value="titipan">
                        @else
                        <input type="hidden" name="jenisbayar" value="tunai">
                        @endif
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    @if (Auth::user()->level=="salesman")
                                    <input type="hidden" id="id_karyawan" name="id_karyawan" value="{{ Auth::user()->id_salesman }}">
                                    @else
                                    <select name="id_karyawan" id="id_karyawan" class="form-control">
                                        <option value="">Salesman Penagih</option>
                                        @foreach ($salesman as $d)
                                        <option value="{{ $d->id_karyawan }}">{{ $d->nama_karyawan }}</option>
                                        @endforeach
                                    </select>
                                    @endif
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
                                    <button class="btn btn-primary btn-block" id="btnsimpanbayar"><i class="feather icon-send"></i> Simpan</button>
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
                                <x-inputtext label="Tanggal Pencatatan Giro" value="{{ date('Y-m-d') }}" field="tgl_giro" icon="feather icon-calendar" datepicker />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    @if (Auth::user()->level=="salesman")
                                    <input type="hidden" id="id_karyawan" name="id_karyawan" value="{{ Auth::user()->id_salesman }}">
                                    @else
                                    <select name="id_karyawan" id="id_karyawan_giro" class="form-control">
                                        <option value="">Salesman</option>
                                        @foreach ($salesman as $d)
                                        <option value="{{ $d->id_karyawan }}">{{ $d->nama_karyawan }}</option>
                                        @endforeach
                                    </select>
                                    @endif
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
                                    <button class="btn btn-primary btn-block" id="btnsimpangiro"><i class="feather icon-send"></i> Simpan</button>
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
                                <x-inputtext label="Tanggal Pencatatan Transfer" value="{{ date('Y-m-d') }}" field="tgl_transfer" icon="feather icon-calendar" datepicker />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    @if (Auth::user()->level=="salesman")
                                    <input type="hidden" id="id_karyawan" name="id_karyawan" value="{{ Auth::user()->id_salesman }}">
                                    @else
                                    <select name="id_karyawan" id="id_karyawan_giro" class="form-control">
                                        <option value="">Salesman</option>
                                        @foreach ($salesman as $d)
                                        <option value="{{ $d->id_karyawan }}">{{ $d->nama_karyawan }}</option>
                                        @endforeach
                                    </select>
                                    @endif
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
                                <x-inputtext label="Jatuh Tempo" field="tglcair_transfer" readonly icon="feather icon-calendar" readonly />
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
                                    <button class="btn btn-primary btn-block" id="btnsimpantransfer"><i class="feather icon-send"></i> Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdlcetakfaktur" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Cetak Faktur</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div id="loadcetak" style="height: 800px"></div>

            </div>
        </div>
    </div>
</div>
@endsection


@push('myscript')
<script>
    function sendUrlToPrint(url) {
        var beforeUrl = 'intent:';
        var afterUrl = '#Intent;';
        // Intent call with component
        afterUrl += 'component=ru.a402d.rawbtprinter.activity.PrintDownloadActivity;'
        afterUrl += 'package=ru.a402d.rawbtprinter;end;';
        document.location = beforeUrl + encodeURI(url) + afterUrl;
        return false;
    }

</script>
<script>
    var sign = $("#sign").signature({
        syncField: '#signature'
        , syncFormat: 'PNG'
    })

    $("#clear").click(function(e) {
        e.preventDefault();
        sign.signature('clear');
        $("#signature").val();
    });

</script>
<script>
    $(function() {
        $("#tgl_transfer").change(function() {
            $("#tglcair_transfer").val($(this).val());
        });

        function loadjt() {
            $("#tglcair_transfer").val($("#tgl_transfer").val());
        }
        loadjt();
        $('.delete-confirm').click(function(event) {
            event.preventDefault();
            var form = $(this).closest("form");
            var name = $(this).data("name");
            var tanggal = $(this).attr("tanggal");
            cektutuplaporan(tanggal);

            swal({
                    title: `Are you sure you want to delete this record?`
                    , text: "If you delete this, it will be gone forever."
                    , icon: "warning"
                    , buttons: true
                    , dangerMode: true
                , })
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
                backdrop: 'static'
                , keyboard: false
            });
        });

        //Input Giro
        $("#inputgiro").click(function(e) {
            e.preventDefault();
            $('#mdlinputgiro').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        //Input Transfer
        $("#inputtransfer").click(function(e) {
            e.preventDefault();
            $('#mdlinputtransfer').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        $(".editbayar").click(function(e) {
            e.preventDefault();
            var nobukti = $(this).attr('nobukti');
            var kode_cabang = $(this).attr('kode_cabang');
            var no_fak_penj = $(this).attr('no_fak_penj');
            var sisabayar = $(this).attr('sisabayar');
            $.ajax({
                type: 'POST'
                , url: '/pembayaran/edit'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , nobukti: nobukti
                    , kode_cabang: kode_cabang
                    , no_fak_penj: no_fak_penj
                    , sisabayar: sisabayar
                }
                , cache: false
                , success: function(respond) {
                    $("#loadeditbayar").html(respond);
                    $('#mdleditpembayaran').modal({
                        backdrop: 'static'
                        , keyboard: false
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
                type: 'POST'
                , url: '/pembayaran/editgiro'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , id_giro: id_giro
                    , kode_cabang: kode_cabang
                    , sisabayar: sisabayar
                }
                , cache: false
                , success: function(respond) {
                    $("#loadeditgiro").html(respond);
                    $('#mdleditgiro').modal({
                        backdrop: 'static'
                        , keyboard: false
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
                type: 'POST'
                , url: '/pembayaran/edittransfer'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , id_transfer: id_transfer
                    , kode_cabang: kode_cabang
                    , kode_pelanggan: kode_pelanggan
                    , sisabayar: sisabayar
                }
                , cache: false
                , success: function(respond) {
                    $("#loadedittransfer").html(respond);
                    $('#mdledittransfer').modal({
                        backdrop: 'static'
                        , keyboard: false
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
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                    , jenislaporan: "penjualan"
                }
                , cache: false
                , success: function(respond) {
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
            $("#btnsimpanbayar").prop('disabled', true);
            var tglbayar = $("#tglbayar").val();
            var bayar = $("#bayar").val();
            var id_karyawan = $("#id_karyawan").val();
            var id_giro = $("#id_giro").val();
            var sisabayar = "{{ $sisabayar }}";
            var cektutuplaporan = $("#cektutuplaporan").val();
            var jmlbayar = parseInt(bayar.replace(/\./g, ''));
            //alert(sisabayar);
            if (cektutuplaporan > 0) {
                swal("Peringatan", "Laporan Periode Ini Sudah Ditutup !", "warning");
                $("#btnsimpanbayar").prop('disabled', false);
                return false;
            } else if (tglbayar == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Bayar Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tglbayar").focus();
                });
                return false;
            } else if (bayar == "" || bayar === 0) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Bayar Harus Diisi  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#bayar").focus();
                });
                $("#btnsimpanbayar").prop('disabled', false);
                return false;
            } else if (id_karyawan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Salesman Penagih Harus Diisi  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#id_karyawan").focus();
                });
                $("#btnsimpanbayar").prop('disabled', false);
                return false;
            } else if ($(".girotocash").is(':checked') && id_giro == "") {
                swal({
                    title: 'Oops'
                    , text: 'No. Giro Harus Dipilih  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#id_giro").focus();
                });
                $("#btnsimpanbayar").prop('disabled', false);
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
            $("#btnsimpangiro").prop('disabled', true);
            var tgl_giro = $("#tgl_giro").val();
            var id_karyawan = $("#id_karyawan_giro").val();
            var no_giro = $("#no_giro").val();
            var namabank = $("#namabank_giro").val();
            var tglcair = $("#tglcair").val();
            var sisabayar = "{{ $sisabayar }}";
            var jumlah = $("#jumlah_giro").val();
            var jmlbayar = parseInt(jumlah.replace(/\./g, ''));
            var cektutuplaporan = $("#cektutuplaporan").val();
            //alert(sisabayar);
            if (cektutuplaporan > 0) {
                swal("Peringatan", "Laporan Periode Ini Sudah Ditutup !", "warning");
                $("#btnsimpangiro").prop('disabled', false);
                return false;
            } else if (tgl_giro == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Giro Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_giro").focus();
                });
                $("#btnsimpangiro").prop('disabled', false);
                return false;
            } else if (id_karyawan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Salesman Penagih Harus Diisi  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#id_karyawan_giro").focus();
                });
                $("#btnsimpangiro").prop('disabled', false);
                return false;
            } else if (no_giro == "") {
                swal({
                    title: 'Oops'
                    , text: 'No. Giro Harus Diisi  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#no_giro").focus();
                });
                $("#btnsimpangiro").prop('disabled', false);
                return false;
            } else if (namabank == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bank Harus Diisi  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#namabank_giro").focus();
                });
                $("#btnsimpangiro").prop('disabled', false);
                return false;
            } else if (tglcair == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jatuh Temp Harus Diisi   !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tglcair").focus();
                });
                $("#btnsimpangiro").prop('disabled', false);
                return false;
            } else if (jumlah == "" || jumlah === 0) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah  Harus Diisi  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jumlah_giro").focus();
                });
                $("#btnsimpangiro").prop('disabled', false);
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
            $("#btnsimpantransfer").prop('disabled', true);
            var tgl_transfer = $("#tgl_transfer").val();
            var id_karyawan = $("#id_karyawan_transfer").val();
            var namabank = $("#namabank_transfer").val();
            var tglcair = $("#tglcair_transfer").val();
            var sisabayar = "{{ $sisabayar }}";
            var jumlah = $("#jumlah_transfer").val();
            var jmlbayar = parseInt(jumlah.replace(/\./g, ''));
            var cektutuplaporan = $("#cektutuplaporan").val();
            //alert(sisabayar);
            if (cektutuplaporan > 0) {
                swal("Peringatan", "Laporan Periode Ini Sudah Ditutup !", "warning");
                $("#btnsimpantransfer").prop('disabled', false);
                return false;
            } else if (tgl_transfer == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Transfer Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_transfer").focus();
                });
                $("#btnsimpantransfer").prop('disabled', false);
                return false;
            } else if (id_karyawan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Salesman Penagih Harus Diisi  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#id_karyawan_transfer").focus();
                });
                $("#btnsimpantransfer").prop('disabled', false);
                return false;
            } else if (namabank == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bank Harus Diisi  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#namabank_transfer").focus();
                });
                $("#btnsimpantransfer").prop('disabled', false);
                return false;
            } else if (tglcair == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jatuh Tempo Harus Diisi   !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tglcair_transfer").focus();
                });
                $("#btnsimpantransfer").prop('disabled', false);
                return false;
            } else if (jumlah == "" || jumlah === 0) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah  Harus Diisi  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jumlah_transfer").focus();
                });
                $("#btnsimpantransfer").prop('disabled', false);
                return false;
            } else {
                return true;
            }
        });

        $("#cetakfaktur").click(function(e) {
            e.preventDefault();
            var no_fak_penj = "{{ $data->no_fak_penj }}";
            $.ajax({
                type: 'POST'
                , url: '/cetakstruk'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_fak_penj: no_fak_penj
                }
                , cache: false
                , success: function(respond) {
                    $('#mdlcetakfaktur').modal({
                        backdrop: 'static'
                        , keyboard: false
                    });
                    $("#loadcetak").html(respond);
                }
            });
        });

        $("#frmSignature").submit(function() {
            var signature = $("#signature").val();
            if (signature == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanda Tangan Masih Kosong  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                });
                return false;
            }
        });
    });

</script>
@endpush
