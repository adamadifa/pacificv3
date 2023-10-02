@extends('layouts.midone')
@section('titlepage','Input Penjualan V2')
@section('content')
<style>
    #no_fak_penj {
        text-transform: uppercase
    }

    @media only screen and (max-width: 600px) {
        #grandtotal {
            font-size: 2rem !important;
        }

        #iconchart {
            font-size: 1.5rem !important;
        }

        #bgiconcart {
            padding: 0.5rem !important;
        }
    }

</style>
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h4 class="content-header-title float-left mb-0">Input Penjualan Salesman</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <!-- Data list view starts -->
        <!-- DataTable starts -->
        @include('layouts.notification')
        <form action="/penjualan/store" method="POST" id="frmPenjualan">
            @csrf
            <input type="hidden" id="sisapiutang" name="sisapiutang" value="{{ $piutang != null ? $piutang->sisapiutang : 0 }}">
            <input type="hidden" id="sikluspembayaran" name="sikluspembayaran" value="{{ $sikluspembayaran }}">
            <input type="hidden" id="sisafakturkredit" name="sisafakturkredit" value="{{ $fakturkredit }}">
            <input type="hidden" id="jmlfaktur" name="jmlfaktur" value="{{ $jmlfaktur }}">
            <input type="hidden" id="cektutuplaporan" name="cektutuplaporan">
            <input type="hidden" id="bruto" name="bruto">
            <input type="hidden" id="subtotal" name="subtotal">
            <input type="hidden" id="cektemp" name="cektemp" value="0">
            <input type="hidden" id="cekpajak" name="cekpajak" value="{{ $pajak }}">
            <div class="row">
                <div class="col-lg-3 col-sm-12">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Data Penjualan</h4>
                                </div>
                                <div class="card-body">

                                    <div class="row" id="ceknofak">
                                        <input type="hidden" id="ceknofakval" value="0">
                                        <div class="col">
                                            <div class="alert alert-danger">
                                                <h4 class="alert-heading"><i class="feather icon-info mr-1"></i> Warning !</h4>
                                                <p>No. Faktur Sudah Digunakan, Silahkan Hubungi Admin Untuk mengetahun No. Faktur Terakhir</p>
                                            </div>

                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="No. Faktur" field="no_fak_penj" value="{{ $no_fak_penj_auto }}" icon="fa fa-barcode" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Tanggal Transaksi" field="tgltransaksi" icon="feather icon-calendar" value="{{ date('Y-m-d') }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <input type="hidden" name="kode_pelanggan" id="kode_pelanggan" value="{{ $pelanggan != null ?  $pelanggan->kode_pelanggan : '' }}">
                                            <input type="hidden" id="kode_cabang" class="form-control" name="kode_cabang" value="{{ $pelanggan != null ?  $pelanggan->kode_cabang : ''  }}">
                                            <input type="hidden" id="jatuhtempo" class="form-control" name="jatuhtempo" value="{{ $pelanggan != null ?  $pelanggan->jatuhtempo : '' }}">
                                            <input type="hidden" id="limitpel" class="form-control" name="limitpel" value="{{ $pelanggan != null ?  $pelanggan->limitpel : '' }}">
                                            <x-inputtext label="Pelanggan" field="nama_pelanggan" icon="feather icon-user" value="{{$pelanggan != null ? $pelanggan->kode_pelanggan .'|'. $pelanggan->nama_pelanggan : ''}}" readonly />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <input type="hidden" value="{{ $pelanggan != null ? $pelanggan->id_sales : '' }}" name="id_karyawan" id="id_karyawan">
                                            <input type="hidden" value="{{ $pelanggan != null ? $pelanggan->kategori_salesman : '' }}" name="kategori_salesman" id="kategori_salesman">
                                            <input type="hidden" value="{{ $pelanggan != null ? $pelanggan->status_promo : '' }}" name="status_promo" id="status_promo">
                                            <x-inputtext label="Salesman" field="nama_karyawan" icon="feather icon-users" value="{{ $pelanggan != null ? $pelanggan->id_sales. '|'.$pelanggan->nama_karyawan.'|'.$pelanggan->kategori_salesman : ''}}" readonly />
                                        </div>
                                    </div>
                                    @if (Auth::user()->kode_cabang=="BDG" && $pelanggan->kategori_salesman=="TO")
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="No. PO Pelanggan" field="no_po" icon="fa fa-barcode" />
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row d-none">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-content">
                                    <img class="card-img-top img-fluid" id="foto" src="{{ asset('app-assets/images/slider/04.jpg') }}" alt="Card image cap">
                                    <div class="card-body">
                                        <h4 class="card-title">
                                            <span id="pelanggan_text"></span>
                                        </h4>
                                        <b>Alamat</b>
                                        <p class="card-text" id="alamat_text">{{ $pelanggan != null ? $pelanggan->alamat_pelanggan : '' }}</p>
                                        <b>No. HP</b>
                                        <p class="card-text" id="no_hp">{{ $pelanggan != null ? $pelanggan->no_hp : '' }}</p>
                                        <b>Koordinat</b>
                                        <p class="card-text" id="koordinat">{{ $pelanggan != null ? $pelanggan->latitude : '' }},{{ $pelanggan != null ? $pelanggan->longitude : '' }}</p>
                                        <b>Limit Pelanggan</b>
                                        <p class="card-text" id="limitpelanggan">{{ rupiah($pelanggan != null ? $pelanggan->limitpel : 0) }}</p>
                                        <b>Piutang Pelanggan</b>
                                        <p class="card-text" id="piutangpelanggan">{{ rupiah($piutang != null ? $piutang->sisapiutang : 0) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-sm-12">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-start pb-0">
                                    <div class="avatar bg-rgba-info m-2" style="padding:3rem" id="bgiconcart">
                                        <div class="avatar-content">
                                            <i class="feather icon-shopping-cart text-info" id="iconchart" style="font-size: 4rem"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h2 class="text-bold-700" style="font-size: 6rem; padding:2rem" id="grandtotal">0,00</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    {{-- <div class="row d-none">
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group" style="margin-bottom:5px !important">
                                                <div class=" form-label-group position-relative has-icon-left" style="margin-bottom:5px !important">
                                                    <div class="controls">
                                                        <input type="hidden" name="kode_barang" id="kode_barang">
                                                        <input type="hidden" name="isipcsdus" id="isipcsdus">
                                                        <input type="hidden" name="isipcs" id="isipcs">
                                                        <input type="text" autocomplete="off" id="nama_barang" value="" readonly class="form-control" name="nama_barang" placeholder="Produk" style="height: 80px">
                                                        <div class="form-control-position" style="top:23px !important">
                                                            <i class="fa fa-barcode"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-sm-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group" style="margin-bottom:5px !important">
                                                        <div class=" form-label-group position-relative has-icon-left" style="margin-bottom:5px !important">
                                                            <div class="controls">
                                                                <input type="text" autocomplete="off" id="jml_dus" value="" class="form-control text-right" name="jml_dus" placeholder="Dus">
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-file"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group" style="margin-bottom:5px !important">
                                                        <div class=" form-label-group position-relative has-icon-left" style="margin-bottom:5px !important">
                                                            <div class="controls">
                                                                <input type="hidden" id="harga_dus_old" name="harga_dus_old">
                                                                <input type="text" autocomplete="off" id="harga_dus" value="" class="form-control text-right" name="harga_dus" placeholder="Harga / Dus">
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-tag"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!--- Pack-->
                                        <div class="col-lg-2 col-sm-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group" style="margin-bottom:5px !important">
                                                        <div class=" form-label-group position-relative has-icon-left" style="margin-bottom:5px !important">
                                                            <div class="controls">
                                                                <input type="text" autocomplete="off" id="jml_pack" class="form-control text-right" name="jml_pack" placeholder="Pack">
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-file"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group" style="margin-bottom:5px !important">
                                                        <div class=" form-label-group position-relative has-icon-left" style="margin-bottom:5px !important">
                                                            <div class="controls">
                                                                <input type="hidden" id="harga_pack_old" name="harga_pack_old">
                                                                <input type="text" autocomplete="off" id="harga_pack" class="form-control text-right" name="harga_pack" placeholder="Harga / Pack">
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-tag"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!--- Pcs-->
                                        <div class="col-lg-2 col-sm-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group" style="margin-bottom:5px !important">
                                                        <div class=" form-label-group position-relative has-icon-left" style="margin-bottom:5px !important">
                                                            <div class="controls">
                                                                <input type="text" autocomplete="off" id="jml_pcs" class="form-control text-right" name="jml_pcs" placeholder="Pcs">
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-file"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group" style="margin-bottom:5px !important">
                                                        <div class=" form-label-group position-relative has-icon-left" style="margin-bottom:5px !important">
                                                            <div class="controls">
                                                                <input type="hidden" id="harga_pcs_old" name="harga_pcs_old">
                                                                <input type="text" autocomplete="off" id="harga_pcs" class="form-control text-right" name="harga_pcs" placeholder="Harga / Pcs">
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-tag"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-sm-12">
                                            <div class="row mb-1">
                                                <div class="col-12">
                                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                                        <input type="checkbox" class="promo" id="promo" name="promo" value="1">
                                                        <span class="vs-checkbox">
                                                            <span class="vs-checkbox--check">
                                                                <i class="vs-icon feather icon-check"></i>
                                                            </span>
                                                        </span>
                                                        <span class="">Promosi</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}
                                    <div class="row mb-2">
                                        <div class="col-12">
                                            <div class="btn-group btn-block">
                                                <a href="#" id="addproduct" class="btn btn-info">
                                                    <i class="feather icon-plus ml-1"></i> Tambah item Produk
                                                </a>
                                                <a href="#" id="resetproduct" class="btn btn-danger">
                                                    <i class="feather icon-refresh-ccw"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <tbody id="loadbarangtemp"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-lg-3 col-sm-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <span class="font-size:1rem;"><b><i class="feather icon-tag mr-1"></i>Potongan</b></span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group" style="margin-bottom:5px">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="potaida" class="form-control text-right money" name="potaida" placeholder="Aida" readonly>
                                                            <div class="form-control-position">
                                                                <img src="{{asset('app-assets/images/icons/diskonaida.png')}}" width="18px" height="18px" alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-bottom:5px">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="potswan" class="form-control text-right money" name="potswan" placeholder="Swan" readonly>
                                                            <div class="form-control-position">
                                                                <img src="{{asset('app-assets/images/icons/diskonswan.png')}}" width="18px" height="18px" alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-bottom:5px">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="potstick" class="form-control text-right money" name="potstick" placeholder="Stick" readonly>
                                                            <div class="form-control-position">
                                                                <img src="{{asset('app-assets/images/icons/diskonstik.png')}}" width="18px" height="18px" alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-bottom:5px">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="potsp" class="form-control text-right money" name="potsp" placeholder="Premium" readonly>
                                                            <div class="form-control-position">
                                                                <img src="{{asset('app-assets/images/icons/diskonsp.png')}}" width="18px" height="18px" alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-bottom:5px">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="potsb" class="form-control text-right money" name="potsb" placeholder="Sambal" readonly>
                                                            <div class="form-control-position">
                                                                <img src="{{asset('app-assets/images/icons/diskonsambal.png')}}" width="18px" height="18px" alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        {{-- Potongan Istimewa --}}
                                        <div class="col-lg-3 col-sm-12">
                                            {{-- <div class="row">
                                <div class="col-12">
                                    <span class="font-size:1rem;"><b><i class="feather icon-tag mr-1"></i>Potongan Istimewa</b></span>
                                </div>
                            </div>
                            <hr> --}}
                                            <div class="row d-none">
                                                <div class="col-12">
                                                    <div class="form-group" style="margin-bottom: 5px;">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="potisaida" class="form-control text-right money" name="potisaida" placeholder="Aida">
                                                            <div class="form-control-position">
                                                                <img src="{{asset('app-assets/images/icons/diskonaida.png')}}" width="18px" height="18px" alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-bottom: 5px;">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="potisswan" class="form-control text-right money" name="potisswan" placeholder="Swan">
                                                            <div class="form-control-position">
                                                                <img src="{{asset('app-assets/images/icons/diskonswan.png')}}" width="18px" height="18px" alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-bottom: 5px;">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="potisstick" class="form-control text-right money" name="potisstick" placeholder="Stick">
                                                            <div class="form-control-position">
                                                                <img src="{{asset('app-assets/images/icons/diskonstik.png')}}" width="18px" height="18px" alt="">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-sm-12 d-none">
                                            <div class="row">
                                                <div class="col-12">
                                                    <span class="font-size:1rem;"><b><i class="feather icon-tag mr-1"></i>Penyesuaian</b></span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group" style="margin-bottom: 5px">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="penyaida" class="form-control text-right money" name="penyaida" placeholder="Aida">
                                                            <div class="form-control-position">
                                                                <img src="{{asset('app-assets/images/icons/diskonaida.png')}}" width="18px" height="18px" alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-bottom: 5px">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="penyswan" class="form-control text-right money" name="penyswan" placeholder="Swan">
                                                            <div class="form-control-position">
                                                                <img src="{{asset('app-assets/images/icons/diskonswan.png')}}" width="18px" height="18px" alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-bottom: 5px">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="penystick" class="form-control text-right money" name="penystick" placeholder="Stick">
                                                            <div class="form-control-position">
                                                                <img src="{{asset('app-assets/images/icons/diskonstik.png')}}" width="18px" height="18px" alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-sm-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <span class="font-size:1rem;"><b><i class="feather icon-tag mr-1"></i>Pembayaran</b></span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group" style="margin-bottom: 5px">
                                                        <select class="form-control" name="jenistransaksi" id="jenistransaksi">
                                                            <option value="">Jenis Transaksi</option>
                                                            <option value="tunai">Tunai</option>
                                                            <option value="kredit">Kredit</option>
                                                        </select>
                                                        <input type="hidden" id="jenisbayar" name="jenisbayar">
                                                    </div>
                                                    <div class="form-group tunai" style="margin-bottom: 5px">
                                                        <select class="form-control" name="jenisbayartunai" id="jenisbayartunai">
                                                            <option value="tunai">Cash</option>
                                                            <option value="transfer">Transfer</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group tunai" style="margin-bottom: 5px">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="voucher" class="form-control text-right money" name="voucher" placeholder="Voucher">
                                                            <div class="form-control-position" style="top:5px">
                                                                <i class="feather icon-tag"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @php
                                                    $kode_cabang_pelanggan = substr($pelanggan->kode_pelanggan,0,3);
                                                    @endphp
                                                    @if ($pajak == 1 )
                                                    <div class="form-group" style="margin-bottom: 5px">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="totalnonppn" class="form-control text-right money" style="font-weight: bold" readonly name="totalnonppn" placeholder="Total">
                                                            <div class="form-control-position" style="top:5px">
                                                                <i class="feather icon-shopping-cart"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="input-group" style="margin-bottom: 5px">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="basic-addon2" style="text-align: right">PPN 11%</span>
                                                        </div>
                                                        <input type="text" class="form-control text-right" style="font-weight: bold; padding-right:27px" readonly placeholder="PPN 11%" id="ppn" name="ppn">
                                                    </div>
                                                    {{-- <div class="form-group" style="margin-bottom: 5px">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="ppn" class="form-control text-right money" style="font-weight: bold" readonly name="ppn" placeholder="PPN (11%)">
                                                            <div class="form-control-position" style="top:5px">
                                                                <i class="feather icon-percent"></i>
                                                            </div>
                                                        </div>
                                                    </div> --}}
                                                    @else
                                                    <input type="hidden" name="ppn" value="0">
                                                    <input type="hidden" name="totalnonppn" id="totalnonppn">
                                                    @endif
                                                    <div class="form-group" style="margin-bottom: 5px">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="total" class="form-control text-right money" style="font-weight: bold" readonly name="total" placeholder="Total">
                                                            <div class="form-control-position" style="top:5px">
                                                                <i class="feather icon-shopping-cart"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group kredit" style="margin-bottom: 5px">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="titipan" class="form-control text-right money" name="titipan" placeholder="Titipan">
                                                            <div class="form-control-position" style="top:5px">
                                                                <i class="feather icon-tag"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <button class="btn btn-block btn-primary" id="btnsimpan"><i class="feather icon-send mr-1"></i>Simpan</button>
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
        </form>
    </div>
</div>
</div>

<!--- Modal Pilih Pelanggan -->
<div class="modal fade text-left" id="mdlpelanggan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Data Pelanggan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover-animation tabelpelanggan" style="width:100% !important" id="tabelpelanggan" style="font-size: 11px !important">
                        <thead class="thead-dark">
                            <tr>
                                <th>Kode Pelanggan</th>
                                <th>Nama Pelanggan</th>
                                <th>Pasar</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih Barang -->
<div class="modal fade text-left" id="mdlbarang" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Data Barang</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-hover-animation" style="width:100% !important">
                    <thead class="thead-dark">
                        <tr>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Harga / Dus</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="loadbarang"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!--- Modal Pilih Pelanggan -->
<div class="modal fade text-left" id="mdleditbarang" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit Barang</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="loadeditbarang">
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdlinputbarang" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Tambah Produk</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="loadinputbarang">
            </div>
        </div>
    </div>
</div>

@php
$tglhariini = date("Y-m-d");
$hariini = explode("-",$tglhariini);
$tahun_dari = $hariini[0];
$bulan_dari = $hariini[1]-1;
$hari_dari = $hariini[2];

$nextday = date('Y-m-d', strtotime('+1 day', strtotime($tglhariini)));
$besok = explode("-",$nextday);
$tahun_sampai = $besok[0];
$bulan_sampai = $besok[1]-1;
$hari_sampai = $besok[2];
@endphp
@endsection

@push('myscript')
<script>
    $(function() {
        var tahun_dari = "{{ $tahun_dari }}";
        var bulan_dari = "{{ $bulan_dari }}";
        var hari_dari = "{{ $hari_dari }}";
        var tahun_sampai = "{{ $tahun_sampai }}";
        var bulan_sampai = "{{ $bulan_sampai }}";
        var hari_sampai = "{{ $hari_sampai }}";
        $('#tgltransaksi').pickadate({
            min: [tahun_dari, bulan_dari, hari_dari]
            , format: 'yyyy-mm-dd'
        });

        function nonaktifbutton() {
            $("#btnsimpan").prop('disabled', true);
            $("#btnsimpan").html('<i class="fa fa-spinner mr-1"></i><i>Loading...</i>');
        }

        function aktifbutton() {
            $("#btnsimpan").prop('disabled', false);
            $("#btnsimpan").html('<i class="feather icon-send mr-1"></i> Simpan');
        }
        //Reset Product
        function cektemp() {
            nonaktifbutton();
            $.ajax({
                type: 'GET'
                , url: '/cekpenjtemp'
                , success: function(respond) {
                    aktifbutton();
                    $("#cektemp").val(respond);
                }
            });
        }
        $("#resetproduct").click(function(e) {
            e.preventDefault();
            nonaktifbutton();
            $.ajax({
                type: 'POST'
                , url: '/resetpenjualantemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                }
                , cache: false
                , success: function(respond) {
                    aktifbutton();
                    showtemp();
                }
            });
        });
        //Add Product
        $("#addproduct").click(function(e) {
            e.preventDefault();
            var kode_pelanggan = $("#kode_pelanggan").val();
            var kategori_salesman = $("#kategori_salesman").val();
            var kode_cabang = $("#kode_cabang").val();
            var status_promo = $("#status_promo").val();
            var pajak = "{{ $pajak }}";
            //alert(pajak);
            if (kode_pelanggan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Pelanggan Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_pelanggan").focus();
                });
                return false;
            } else {
                nonaktifbutton();
                $.ajax({
                    type: 'POST'
                    , url: '/penjualan/inputbarangtemp'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , kode_pelanggan: kode_pelanggan
                        , kategori_salesman: kategori_salesman
                        , kode_cabang: kode_cabang
                        , pajak: pajak
                        , status_promo: status_promo

                    }
                    , cache: false
                    , success: function(respond) {
                        aktifbutton();
                        $("#loadinputbarang").html(respond);
                        $('#mdlinputbarang').modal({
                            backdrop: 'static'
                            , keyboard: false
                        });
                    }
                });

            }

        });


        // $("#no_fak_penj").on('change', function(e) {
        //     if (e.keyCode == 32) return false;
        //     var no_fak_penj = $("#no_fak_penj").val();

        //     $.ajax({
        //         type: 'POST'
        //         , url: '/penjualan/ceknofaktur'
        //         , data: {
        //             _token: "{{ csrf_token() }}"
        //             , no_fak_penj: no_fak_penj
        //         }
        //         , cache: false
        //         , success: function(respond) {
        //             var status = respond;
        //             console.log(status);
        //             if (status > 0) {
        //                 swal({
        //                     title: 'Oops'
        //                     , text: 'No Faktur ' + no_fak_penj + " Sudah Digunakan !"
        //                     , icon: 'warning'
        //                     , showConfirmButton: false
        //                 }).then(function() {
        //                     $("#no_fak_penj").val("");
        //                     $("#no_fak_penj").focus();
        //                 });
        //             }
        //         }
        //     });
        // });

        //Konversi Ke Format Rupiah

        $("#no_fak_penj").on('keyup', function(e) {
            if (e.keyCode == 32) return false;
            ceknofakpenj();
        });

        $("#ceknofak").hide();

        function ceknofakpenj() {
            var no_fak_penj = $("#no_fak_penj").val();
            $.ajax({
                type: 'POST'
                , url: '/penjualan/ceknofaktur'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_fak_penj: no_fak_penj
                }
                , cache: false
                , success: function(respond) {
                    var status = respond;
                    console.log(status);
                    if (status > 0) {
                        swal({
                            title: 'Oops'
                            , text: 'No Faktur ' + no_fak_penj + " Sudah Digunakan !"
                            , icon: 'warning'
                            , showConfirmButton: false
                        }).then(function() {
                            //$("#no_fak_penj").val("");
                            $("#ceknofak").show();
                            $("#ceknofakval").val(1);
                            $("#no_fak_penj").focus();
                        });
                    } else {
                        $("#ceknofakval").val(0);
                        $("#ceknofak").hide();
                    }
                }
            });
        }

        ceknofakpenj();

        function convertToRupiah(number) {
            if (number) {
                var rupiah = "";
                var numberrev = number
                    .toString()
                    .split("")
                    .reverse()
                    .join("");
                for (var i = 0; i < numberrev.length; i++)
                    if (i % 3 == 0) rupiah += numberrev.substr(i, 3) + ".";
                return (
                    rupiah
                    .split("", rupiah.length - 1)
                    .reverse()
                    .join("")
                );
            } else {
                return number;
            }
        }

        //Cek Piutang Pelanggan
        function cekpiutang(kode_pelanggan) {
            nonaktifbutton();
            $("#piutangpelanggan").text("Loading..");
            $.ajax({
                type: 'POST'
                , url: '/cekpiutangpelanggan'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_pelanggan: kode_pelanggan
                }
                , cache: false
                , success: function(respond) {
                    aktifbutton();
                    console.log(respond);
                    $("#sisapiutang").val(respond);
                    $("#piutangpelanggan").text(convertToRupiah(respond));

                }
            });
        }

        //Cek Tutup Laporan
        function cektutuplaporan() {
            var tgltransaksi = $("#tgltransaksi").val();
            nonaktifbutton();
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tgltransaksi
                    , jenislaporan: "penjualan"
                }
                , cache: false
                , success: function(respond) {
                    aktifbutton();
                    console.log(respond);
                    $("#cektutuplaporan").val(respond);
                }
            });
        }

        //Cek Tanggal Tutup Laporan
        $("#tgltransaksi").change(function() {
            cektutuplaporan();
        });

        //Format No. Faktur Tidak Boleh Pakai Spasi
        $('#no_fak_penj').mask('AAAAAAAAAAAAA', {
            'translation': {
                A: {
                    pattern: /[A-Za-z0-9]/
                }
            }
        });

        //Pilih Pelanggan Saat Diklik
        $('#nama_pelanggan').click(function(e) {
            e.preventDefault();
            $('#mdlpelanggan').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        //Pilih Pelanggan Saat Focus
        $('#nama_pelanggan').focus(function(e) {
            e.preventDefault();
            $('#mdlpelanggan').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        //Tampilkan Tabel Pelanggan
        $('.tabelpelanggan').DataTable({
            processing: true
            , serverSide: true
            , ajax: '/pelanggan/json', // memanggil route yang menampilkan data json
            bAutoWidth: false

            , columns: [{ // mengambil & menampilkan kolom sesuai tabel database
                    data: 'kode_pelanggan'
                    , name: 'kode_pelanggan'
                }
                , {
                    data: 'nama_pelanggan'
                    , name: 'nama_pelanggan'
                }, {
                    data: 'pasar'
                    , name: 'pasar'
                }
                , {
                    data: 'action'
                    , name: 'action'
                    , orderable: false
                    , searchable: false
                }

            ]
        });

        //Tampilkan Pelanggan Yang Dipilih
        $('.tabelpelanggan tbody').on('click', 'a', function() {
            var kode_pelanggan = $(this).attr("kode_pelanggan");
            cekpiutang(kode_pelanggan);
            var nama_pelanggan = $(this).attr("nama_pelanggan");
            var id_karyawan = $(this).attr("id_karyawan");
            var nama_karyawan = $(this).attr("nama_karyawan");
            var kategori_salesman = $(this).attr("kategori_salesman");
            var alamat_pelanggan = $(this).attr("alamat_pelanggan");
            var no_hp = $(this).attr("no_hp");
            var pasar = $(this).attr("pasar");
            var latitude = $(this).attr("latitude");
            var longitude = $(this).attr("longitude");
            var image = $(this).attr("foto")
            var kode_cabang = $(this).attr("kode_cabang")
            var limitpel = $(this).attr("limitpel");
            var limitpelanggan = $(this).attr("limitpelanggan");
            var jatuhtempo = $(this).attr("jatuhtempo");
            if (nama_pelanggan.includes('KPBN')) {
                $("#harga_dus").prop('readonly', false);
                $("#harga_pack").prop('readonly', false);
                $("#harga_pcs").prop('readonly', false);
            } else {
                $("#harga_dus").prop('readonly', true);
                $("#harga_pack").prop('readonly', true);
                $("#harga_pcs").prop('readonly', true);
            }
            var foto = "{{ url(Storage::url('pelanggan/')) }}/" + image;
            var nofoto = "{{ asset('app-assets/images/slider/04.jpg') }}";
            $("#kode_pelanggan").val(kode_pelanggan);
            $("#pelanggan_text").text(kode_pelanggan + " | " + nama_pelanggan);
            $("#nama_pelanggan").val(kode_pelanggan + " | " + nama_pelanggan);
            $("#id_karyawan").val(id_karyawan);
            $("#nama_karyawan").val(id_karyawan + " | " + nama_karyawan + " | " + kategori_salesman);
            $("#alamat_text").text(alamat_pelanggan);
            $("#no_hp").text(no_hp);

            $("#kode_cabang").val(kode_cabang);
            $("#kategori_salesman").val(kategori_salesman);
            $("#limitpel").val(limitpel);
            $("#jatuhtempo").val(jatuhtempo);
            $("#limitpelanggan").text(limitpelanggan);

            $("#koordinat").text(latitude + " - " + longitude);
            if (image != "") {
                $("#foto").attr("src", foto);
            } else {
                $("#foto").attr("src", nofoto);
            }

            $("#mdlpelanggan").modal("hide");
            //hitungdiskon();
        });

        //Ketika Form Di Submit

        //Cek TO CANVASER
        function cektocanvaser() {
            var kategori_saleman = $("#kategori_salesman").val();
            if (kategori_saleman == "TO") {
                $("#no_fak_penj").prop("readonly", true);
                $("#no_fak_penj").prop("placeholder", "Auto");
            } else {
                $("#no_fak_penj").prop("readonly", false);
            }
        }

        cektocanvaser();
        $("form").submit(function(e) {
            $("#btnsimpan").prop('disabled', true);
            var no_fak_penj = $("#no_fak_penj").val();
            var tgltransaksi = $("#tgltransaksi").val();
            var kode_pelanggan = $("#kode_pelanggan").val();
            var cektutuplaporan = $("#cektutuplaporan").val();
            var pelanggan = $("#nama_pelanggan").val();
            var pl = pelanggan.split("|");
            var nama_pelanggan = pl[1];
            var jenistransaksi = $("#jenistransaksi").val();
            var cektemp = $("#cektemp").val();
            var kategori_salesman = $("#kategori_salesman").val();
            var ceknofak = $("#ceknofakval").val();
            var sisapiutang = $("#sisapiutang").val();
            var limitpel = $("#limitpel").val();
            var subtotal = $("#subtotal").val();
            var sikluspembayaran = $("#sikluspembayaran").val();
            var sisafakturkredit = $("#sisafakturkredit").val();
            var jmlfaktur = $("#jmlfaktur").val();
            var totalpiutang = parseInt(sisapiutang) + parseInt(subtotal);

            //alert(limitpel);
            // alert(nama_pelanggan);
            if (cektutuplaporan > 0) {
                swal("Peringatan", "Laporan Periode Ini Sudah Ditutup !", "warning");
                return false;
            } else if (parseInt(sisafakturkredit) >= parseInt(jmlfaktur) && sikluspembayaran == 0 && jenistransaksi == 'kredit') {
                swal({
                    title: 'Oops'
                    , text: 'Melebihi Limit Faktur Kredit, Silahkan Ajukan Penambahan Jumlah Faktur Kredit !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#no_fak_penj").focus();
                });
                $("#btnsimpan").prop('disabled', false);
                return false;
            } else if (parseInt(totalpiutang) >= parseInt(limitpel) && sikluspembayaran == 0 && jenistransaksi == 'kredit') {
                swal({
                    title: 'Oops'
                    , text: 'Melebihi Limit, Silahkan Ajukan Penambahan Limit !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#no_fak_penj").focus();
                });
                $("#btnsimpan").prop('disabled', false);
                return false;
            } else if (ceknofak > 0) {
                swal({
                    title: 'Oops'
                    , text: 'No. Faktur Sudah Digunakan Silahkan Hubungi Admin Untuk  Mengetahui Nomor Faktur Terakhir !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#no_fak_penj").focus();
                });
                $("#btnsimpan").prop('disabled', false);
                return false;
            } else if (no_fak_penj == "" && kategori_salesman == "CANVASER") {
                swal({
                    title: 'Oops'
                    , text: 'No. Faktur Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#no_fak_penj").focus();
                });
                $("#btnsimpan").prop('disabled', false);
                return false;
            } else if (tgltransaksi == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgltransaksi").focus();
                });
                $("#btnsimpan").prop('disabled', false);
                return false;
            } else if (jenistransaksi == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jenis Transaksi Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jenistransaksi").focus();
                });
                $("#btnsimpan").prop('disabled', false);
                return false;
            } else if (nama_pelanggan != "BATAL" && cektemp == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Data Masih Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jenistransaksi").focus();
                });
                $("#btnsimpan").prop('disabled', false);
                return false;
            } else if (kode_pelanggan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Pelanggan Harus DIpilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_pelanggan").focus();
                });
                $("#btnsimpan").prop('disabled', false);
                return false;
            } else if (kode_pelanggan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Pelanggan Harus DIpilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_pelanggan").focus();
                });
                $("#btnsimpan").prop('disabled', false);
                return false;
            }


            //return false;
        });


        //Pilih Pelanggan Saat Diklik
        $('#nama_barang').click(function(e) {
            e.preventDefault();
            var kode_pelanggan = $("#kode_pelanggan").val();
            var kategori_salesman = $("#kategori_salesman").val();
            var kode_cabang = $("#kode_cabang").val();
            if (kode_pelanggan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Pelanggan Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_pelanggan").focus();
                });
                return false;
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/getbarangcabang'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , kategori_salesman: kategori_salesman
                        , kode_cabang: kode_cabang
                        , kode_pelanggan: kode_pelanggan
                    }
                    , cache: false
                    , success: function(respond) {
                        $("#loadbarang").html(respond);
                        $('#mdlbarang').modal({
                            backdrop: 'static'
                            , keyboard: false
                        });
                    }
                });
            }

        });


        $('#nama_barang').focus(function(e) {
            e.preventDefault();
            var kode_pelanggan = $("#kode_pelanggan").val();
            var kategori_salesman = $("#kategori_salesman").val();
            var kode_cabang = $("#kode_cabang").val();
            if (kode_pelanggan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Pelanggan Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_pelanggan").focus();
                });
                return false;
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/getbarangcabang'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , kategori_salesman: kategori_salesman
                        , kode_cabang: kode_cabang
                        , kode_pelanggan: kode_pelanggan
                    }
                    , cache: false
                    , success: function(respond) {
                        $("#loadbarang").html(respond);
                        $('#mdlbarang').modal({
                            backdrop: 'static'
                            , keyboard: false
                        });
                    }
                });
            }

        });

        $("#promo").change(function() {
            var kode_barang = $("#kode_barang").val();
            var harga_dus = $("#harga_dus_old").val();
            var harga_pack = $("#harga_pack_old").val();
            var harga_pcs = $("#harga_pcs_old").val();
            if ($('#promo').is(":checked")) {
                if (kode_barang == "") {
                    swal({
                        title: 'Oops'
                        , text: 'Barang Harus Dipilih !'
                        , icon: 'warning'
                        , showConfirmButton: false
                    }).then(function() {
                        $("#nama_barang").focus();
                    });
                    $('#promo').prop('checked', false); // Unchecks it
                } else {
                    $("#harga_dus").val(0);
                    $("#harga_pack").val(0);
                    $("#harga_pcs").val(0);

                    $("#harga_dus").prop('readonly', true);
                    $("#harga_pack").prop('readonly', true);
                    $("#harga_pcs").prop('readonly', true);
                }
            } else {
                $("#harga_dus").val(0);
                $("#harga_pack").val(0);
                $("#harga_pcs").val(0);
                $("#jml_dus").val("");
                $("#jml_pack").val("");
                $("#jml_pcs").val("");
                $("#nama_barang").val("");
                $("#kode_barang").val("");
                $("#harga_dus").prop('readonly', true);
                $("#harga_pack").prop('readonly', true);
                $("#harga_pcs").prop('readonly', true);
            }
        });
        //Tambah Item
        $("#tambahitem").click(function(e) {
            e.preventDefault();
            var kode_barang = $("#kode_barang").val();
            var jml_dus = $("#jml_dus").val();
            var jml_pack = $("#jml_pack").val();
            var jml_pcs = $("#jml_pcs").val();
            var harga_dus = $("#harga_dus").val();
            var harga_pack = $("#harga_pack").val();
            var harga_pcs = $("#harga_pcs").val();
            var isipcsdus = $("#isipcsdus").val();
            var isipcs = $("#isipcs").val();
            var nama_pelanggan = $("#nama_pelanggan").val();
            if ($('#promo').is(":checked")) {
                var promo = $("#promo").val();
            } else {
                var promo = "";
            }


            var jmldus = jml_dus != "" ? parseInt(jml_dus.replace(/\./g, '')) : 0;
            var jmlpack = jml_pack != "" ? parseInt(jml_pack.replace(/\./g, '')) : 0;
            var jmlpcs = jml_pcs != "" ? parseInt(jml_pcs.replace(/\./g, '')) : 0;

            var hargadus = harga_dus != "" ? parseInt(harga_dus.replace(/\./g, '')) : 0;
            var hargapack = harga_pack != "" ? parseInt(harga_pack.replace(/\./g, '')) : 0;
            var hargapcs = harga_pcs != "" ? parseInt(harga_pcs.replace(/\./g, '')) : 0;



            var jumlah = (jmldus * parseInt(isipcsdus)) + (jmlpack * (parseInt(isipcs))) + jmlpcs;
            var subtotal = (jmldus * hargadus) + (jmlpack * hargapack) + (jmlpcs * hargapcs);
            //alert(totalpcs);

            if (kode_barang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Barang Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_barang").focus();
                });
                return false;
            } else if (jumlah == "" && !nama_pelanggan.includes('BATAL')) {
                swal({
                    title: 'Oops'
                    , text: 'Qty Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jml_dus").focus();
                });
                return false;
            } else {
                //Simpan Barang Temp
                nonaktifbutton();
                $.ajax({
                    type: 'POST'
                    , url: '/penjualan/storebarangtempv2'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , kode_barang: kode_barang
                        , hargadus: hargadus
                        , hargapack: hargapack
                        , hargapcs: hargapcs
                        , jumlah: jumlah
                        , subtotal: subtotal
                        , promo: promo
                    }
                    , cache: false
                    , success: function(respond) {
                        aktifbutton();
                        if (respond == 0) {
                            swal({
                                title: 'Success'
                                , text: 'Item Berhasil Disimpan !'
                                , icon: 'success'
                                , showConfirmButton: false
                            }).then(function() {
                                showtemp();
                                $("#kode_barang").val("");
                                $("#nama_barang").val("");
                                $("#jml_dus").val("");
                                $("#jml_pack").val("");
                                $("#jml_pcs").val("");

                                $("#harga_dus").val("");
                                $("#harga_pack").val("");
                                $("#harga_pcs").val("");

                                $("#harga_dus_old").val("");
                                $("#harga_pack_old").val("");
                                $("#harga_pcs_old").val("");

                                //$("#jml_dus").focus();

                            });


                        } else if (respond == 1) {
                            swal({
                                title: 'Oops'
                                , text: 'Item Sudah Ada !'
                                , icon: 'warning'
                                , showConfirmButton: false
                            }).then(function() {
                                $("#kode_barang").val("");
                                $("#nama_barang").val("");
                                $("#jml_dus").val("");
                                $("#jml_pack").val("");
                                $("#jml_pcs").val("");

                                $("#harga_dus").val("");
                                $("#harga_pack").val("");
                                $("#harga_pcs").val("");

                                $("#harga_dus_old").val("");
                                $("#harga_pack_old").val("");
                                $("#harga_pcs_old").val("");

                                $("#nama_barang").focus();

                            });
                        } else {
                            swal({
                                title: 'Oops'
                                , text: respond
                                , icon: 'warning'
                                , showConfirmButton: false
                            }).then(function() {

                                $("#jml_dus").focus();

                            });
                        }
                    }
                });
            }
        });

        //Set Format Uang
        $("#harga_dus, #harga_pack, #harga_pcs, #jml_dus, #jml_pack, #jml_pcs").maskMoney();
        $(".money").maskMoney();
        //Tampilkan Detail Barang Temporary
        function showtemp() {
            nonaktifbutton();
            $.ajax({
                type: 'GET'
                , url: '/penjualan/showbarangtempv2'
                , cache: false
                , success: function(respond) {
                    aktifbutton();
                    $("#loadbarangtemp").html(respond);
                    hitungdiskon();
                }
            });
        }

        showtemp();

        //Hitung Diskon

        function hitungdiskon() {
            var jenistransaksi = $("#jenistransaksi").val();
            var pelanggan = $("#nama_pelanggan").val();
            var pl = pelanggan.split("|");
            var nama_pelanggan = pl[1] != undefined ? pl[1] : '';
            var kode_pelanggan = pl[0] != undefined ? pl[0] : '';
            var kode_cabang = kode_pelanggan.substr(0, 3);
            $("#btnsimpan").prop('disabled', true);
            $("#btnsimpan").html('<i class="fa fa-spinner mr-1"></i><i>Loading...</i>');
            $.ajax({
                type: 'POST'
                , url: '/hitungdiskon'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , jenistransaksi: jenistransaksi
                }
                , cache: false
                , success: function(respond) {

                    $("#btnsimpan").prop('disabled', false);
                    $("#btnsimpan").html('<i class="feather icon-send mr-1"></i> Simpan');
                    var result = respond.split("|");
                    console.log(result);
                    if (nama_pelanggan.includes("KPBN") && kode_cabang == "TSM") {
                        $("#potswan").val(0);
                        $("#potaida").val(0);
                        $("#potstick").val(0);
                        $("#potsp").val(0);
                        $("#potsb").val(0);
                    } else {
                        $("#potswan").val(result[0]);
                        $("#potaida").val(result[1]);
                        $("#potstick").val(result[2]);
                        $("#potsp").val(result[3]);
                        $("#potsb").val(result[4]);
                    }
                    loadtotal();
                }
            });
        }



        //Hitung Total


        function loadtotal() {
            var subtotal = $("#totaltemp").val();
            var potswan = $("#potswan").val();
            var potaida = $("#potaida").val();
            var potstick = $("#potstick").val();
            var potsp = $("#potsp").val();
            var potsb = $("#potsb").val();
            var potisaida = $("#potisaida").val();
            var potisswan = $("#potisswan").val();
            var potisstick = $("#potisstick").val();
            var penyaida = $("#penyaida").val();
            var penyswan = $("#penyswan").val();
            var penystick = $("#penystick").val();
            var voucher = $("#voucher").val();
            var cekpajak = $("#cekpajak").val();

            if (potswan.length === 0) {
                var potswan = 0;
            } else {
                var potswan = parseInt(potswan.replace(/\./g, ''));
            }

            if (potaida.length === 0) {
                var potaida = 0;
            } else {
                var potaida = parseInt(potaida.replace(/\./g, ''));
            }

            if (potstick.length === 0) {
                var potstick = 0;
            } else {
                var potstick = parseInt(potstick.replace(/\./g, ''));
            }

            if (potsp.length === 0) {
                var potsp = 0;
            } else {
                var potsp = parseInt(potsp.replace(/\./g, ''));
            }

            if (potsb.length === 0) {
                var potsb = 0;
            } else {
                var potsb = parseInt(potsb.replace(/\./g, ''));
            }

            if (potisaida.length === 0) {
                var potisaida = 0;
            } else {
                var potisaida = parseInt(potisaida.replace(/\./g, ''));
            }

            if (potisswan.length === 0) {
                var potisswan = 0;
            } else {
                var potisswan = parseInt(potisswan.replace(/\./g, ''));
            }

            if (potisstick.length === 0) {
                var potisstick = 0;
            } else {
                var potisstick = parseInt(potisstick.replace(/\./g, ''));
            }

            if (penyaida.length === 0) {
                var penyaida = 0;
            } else {
                var penyaida = parseInt(penyaida.replace(/\./g, ''));
            }

            if (penyswan.length === 0) {
                var penyswan = 0;
            } else {
                var penyswan = parseInt(penyswan.replace(/\./g, ''));
            }

            if (penystick.length === 0) {
                var penystick = 0;
            } else {
                var penystick = parseInt(penystick.replace(/\./g, ''));
            }

            if (voucher.length === 0) {
                var voucher = 0;
            } else {
                var voucher = parseInt(voucher.replace(/\./g, ''));
            }

            var potongan = potswan + potaida + potstick + potsp + potsb;
            var potonganistimewa = potisaida + potisswan + potisstick;
            var penyesuaian = penyaida + penyswan + penystick;
            var total = subtotal - potongan - potonganistimewa - penyesuaian;
            var grandtotal = total - voucher;
            if (cekpajak == 1) {
                var hitungppn = parseInt(total) * (11 / 100);
                var ppn = Math.round(hitungppn);
            } else {
                var ppn = 0;
            }
            console.log(ppn);
            var totalwithppn = parseInt(grandtotal) + parseInt(ppn);
            var bruto = total;
            $("#grandtotal").text(convertToRupiah(totalwithppn));
            $("#totalnonppn").val(convertToRupiah(total));
            $("#ppn").val(convertToRupiah(ppn));
            $("#total").val(convertToRupiah(totalwithppn));
            $("#bruto").val(subtotal);
            $("#subtotal").val(totalwithppn);
            cektemp();
        }

        $(".tunai").hide();
        $(".kredit").hide();
        $("#jenistransaksi").change(function() {
            var jenistransaksi = $(this).val();
            if (jenistransaksi == "tunai") {
                $("#jenisbayar").val("tunai");
                $(".tunai").show();
                $(".kredit").hide();
            } else if (jenistransaksi == "kredit") {
                $("#jenisbayar").val("titipan");
                $(".tunai").hide();
                $(".kredit").show();
                $("#voucher").val(0);
                $("#titipan").focus();
            }
            showtemp();
        });



        $("#potaida,#potswan,#potstick,#potsp,#potsb,#potisaida,#potisswan,#potisstick,#penyswan,#penyaida,#penystick,#voucher").keyup(function(e) {
            loadtotal();
        });
    });

</script>
@endpush
