@extends('layouts.midone')
@section('titlepage', 'Edit Data Cabang')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Edit Cabang</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/cabang">Cabang</a></li>
                            <li class="breadcrumb-item"><a href="#">Edit Cabang</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <form class="form" action="/cabang/{{ Crypt::encrypt($data->kode_cabang) }}/update" method="POST">
        <div class="col-md-12">
            @include('layouts.notification')
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data Cabang</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Kode Cabang" field="kode_cabang" icon="fa fa-barcode" value="{{ $data->kode_cabang }}" readonly />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Nama Cabang" field="nama_cabang" icon="fa fa-bank" value="{{ $data->nama_cabang }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Alamat Cabang" field="alamat_cabang" icon="feather icon-map" value="{{ $data->alamat_cabang }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Telepon" field="telepon" icon="feather icon-phone" value="{{ $data->telepon }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1"><i class="fa fa-send mr-1"></i> Simpan</button>
                                            <a href="/cabang" class="btn btn-outline-warning mr-1 mb-1"><i class="fa fa-arrow-left mr-2"></i>Kembali</a>
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
@endsection
