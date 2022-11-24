@extends('layouts.midone')
@section('titlepage','Input Penjualan V2')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Input Penjualan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/inputpenjualanv2">Input Penjualan</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <!-- Data list view starts -->
        <!-- DataTable starts -->
        @include('layouts.notification')
        <form action="/penjualan/storev2" method="POST" id="frmPenjualan">
            <div class="row">
                <div class="col-lg-3 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data Penjualan</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="No. Faktur" field="no_fak_penj" icon="fa fa-barcode" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="Tanggal Transaksi" field="tgltransaksi" icon="feather icon-calendar" datepicker />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="Pelanggan" field="nama_pelanggan" icon="feather icon-user" readonly />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="Salesman" field="nama_karyawan" icon="feather icon-users" readonly />
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
                                    <div class="avatar bg-rgba-info m-2" style="padding:3rem ">
                                        <div class="avatar-content">
                                            <i class="feather icon-shopping-cart text-info" style="font-size: 4rem"></i>
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
                                    <div class="row">
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group" style="margin-bottom:5px !important">
                                                <div class=" form-label-group position-relative has-icon-left" style="margin-bottom:5px !important">
                                                    <div class="controls">
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
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                                        <input type="checkbox" class="voucher" name="voucher" value="voucher">
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
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <a href="#" class="btn btn-info btn-block"><i class="feather icon-plus ml-1"></i> Tambah item</a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <table class="table table-bordered">
                                                <thead class="text-center">
                                                    <tr>
                                                        <th rowspan="2">No.</th>
                                                        <th rowspan="2">Kode</th>
                                                        <th rowspan="2">Nama Barang</th>
                                                        <th colspan="6">Quantity</th>
                                                        <th rowspan="2">Subtotal</th>
                                                        <th rowspan="2">Aksi</th>
                                                    </tr>
                                                    <tr>
                                                        <th>Dus</th>
                                                        <th>Harga</th>
                                                        <th>Pack</th>
                                                        <th>Harga</th>
                                                        <th>Pcs</th>
                                                        <th>Harga</th>
                                                    </tr>
                                                </thead>
                                            </table>
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
                                                            <input type="text" id="potaida" class="form-control text-right money" name="potaida" placeholder="Aida">
                                                            <div class="form-control-position">
                                                                <img src="{{asset('app-assets/images/icons/diskonaida.png')}}" width="18px" height="18px" alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-bottom:5px">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="potswan" class="form-control text-right money" name="potswan" placeholder="Swan">
                                                            <div class="form-control-position">
                                                                <img src="{{asset('app-assets/images/icons/diskonswan.png')}}" width="18px" height="18px" alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-bottom:5px">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="potstick" class="form-control text-right money" name="potstick" placeholder="Stick">
                                                            <div class="form-control-position">
                                                                <img src="{{asset('app-assets/images/icons/diskonstik.png')}}" width="18px" height="18px" alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-bottom:5px">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="potsp" class="form-control text-right money" name="potsp" placeholder="Premium">
                                                            <div class="form-control-position">
                                                                <img src="{{asset('app-assets/images/icons/diskonsp.png')}}" width="18px" height="18px" alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-bottom:5px">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="potsb" class="form-control text-right" name="potsb" placeholder="Sambal">
                                                            <div class="form-control-position">
                                                                <img src="{{asset('app-assets/images/icons/diskonsambal.png')}}" width="18px" height="18px" alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-lg-3 col-sm-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <span class="font-size:1rem;"><b><i class="feather icon-tag mr-1"></i>Potongan Istimewa</b></span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group" style="margin-bottom: 5px">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="potisaida" class="form-control text-right money" name="potisaida" placeholder="Aida">
                                                            <div class="form-control-position">
                                                                <img src="{{asset('app-assets/images/icons/diskonaida.png')}}" width="18px" height="18px" alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-bottom: 5px">
                                                        <div class="position-relative has-icon-left">
                                                            <input type="text" id="potisswan" class="form-control text-right money" name="potisswan" placeholder="Swan">
                                                            <div class="form-control-position">
                                                                <img src="{{asset('app-assets/images/icons/diskonswan.png')}}" width="18px" height="18px" alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-bottom: 5px">
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
                                        <div class="col-lg-3 col-sm-12">
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
                                                    <div class="form-group">
                                                        <select class="form-control" name="jenistransaksi" id="jenistransaksi">
                                                            <option value="">Jenis Transaksi</option>
                                                            <option value="tunai">Tunai</option>
                                                            <option value="kredit">Kredit</option>
                                                        </select>
                                                        <input type="hidden" id="jenisbayar" name="jenisbayar">
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
@endsection
