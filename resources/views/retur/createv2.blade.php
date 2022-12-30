@extends('layouts.midone')
@section('titlepage','Input Retur')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Input Retur</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/retur/createv2">Input Retur</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <form name="autoSumForm" autocomplete="off" action="/retur/store" class="formValidate form-horizontal" id="formValidate" method="POST">
            @csrf
            <input type="hidden" id="cektutuplaporan">
            <input type="hidden" id="cektemp">
            <input type="hidden" id="sisapiutang" name="sisapiutang">
            <input type="hidden" id="limitpel" name="limitpel">
            <input type="hidden" id="bruto" name="bruto">
            <input type="hidden" id="subtotal" name="subtotal">
            <div class="row">
                <div class="col-lg-3 col-sm-12">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Data Retur</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="No. Retur" field="no_retur_penj" icon="fa fa-barcode" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Tanggal Retur" field="tglretur" icon="feather icon-calendar" readonly value="{{ date('Y-m-d') }}" />
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
                                            <x-inputtext label="Salesman" field="nama_karyawan" icon="feather icon-users" value="{{ $pelanggan != null ? $pelanggan->id_sales. '|'.$pelanggan->nama_karyawan.'|'.$pelanggan->kategori_salesman : ''}}" readonly />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
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
                                            <div class="row">
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
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <a href="#" id="tambahitem" class="btn btn-info btn-block"><i class="feather icon-plus ml-1"></i> Tambah item</a>
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
                                    <div class="row mt-1 d-flex justify-content-end">
                                        <div class="col-lg-3 col-sm-12">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group" style="margin-bottom: 5px">
                                                        <select class="form-control" name="jenis_retur" id="jenis_retur">
                                                            <option value="">Jenis Retur</option>
                                                            <option value="gb">Ganti Barang</option>
                                                            <option value="pf">Potong Faktur</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group" style="margin-bottom: 5px">
                                                        <select class="form-control" name="no_fak_penj" id="no_fak_penj">
                                                            <option value="">No. Faktur</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group" style="margin-bottom: 5px">
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
            </div>
        </form>
    </div>
</div>
@endsection
