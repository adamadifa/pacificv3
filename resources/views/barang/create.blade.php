@extends('layouts.midone')
@section('titlepage', 'Tambah Data Barang')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Tambah Barang</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/barang">Barang</a></li>
                            <li class="breadcrumb-item"><a href="#">Tambah Barang</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <form class="form" action="/barang/store" method="POST">
        <div class="col-md-12">

            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data Barang</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Kode Produk" field="kode_produk" icon="feather icon-credit-card" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Nama Barang" field="nama_barang" icon="feather icon-file" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group  @error('kategori_jenisproduk') error @enderror">
                                                <select name="kategori_jenisproduk" id="" class="form-control">
                                                    <option value="">Jenis Produk</option>
                                                    <option @if (old('kategori_jenisproduk')=='CABE GILING' ) selected @endif value="CABE GILING">CABE GILING</option>
                                                    <option @if (old('kategori_jenisproduk')=='SAOS BAWANG' ) selected @endif value="SAOS BAWANG">SAOS BAWANG</option>
                                                    <option @if (old('kategori_jenisproduk')=='SAOS PREMIUM' ) selected @endif value="SAOS PREMIUM">SAOS PREMIUM</option>
                                                </select>
                                                @error('kategori_jenisproduk')
                                                <div class="help-block">
                                                    <ul role="alert">
                                                        <li>{{ $message }}</li>
                                                    </ul>
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group  @error('jenis_produk') error @enderror">
                                                <select name="jenis_produk" id="" class="form-control">
                                                    <option value="">Kategori</option>
                                                    <option @if (old('jenis_produk')=='SWAN' ) selected @endif value="AIDA">AIDA</option>
                                                    <option @if (old('jenis_produk')=='AIDA' ) selected @endif value="SWAN">SWAN</option>
                                                </select>
                                                @error('jenis_produk')
                                                <div class="help-block">
                                                    <ul role="alert">
                                                        <li>{{ $message }}</li>
                                                    </ul>
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group  @error('satuan') error @enderror">
                                                <select name="satuan" id="" class="form-control">
                                                    <option value="">Satuan</option>
                                                    <option @if (old('satuan')=='DUS' ) selected @endif value="DUS">DUS</option>
                                                    <option @if (old('satuan')=='BALL' ) selected @endif value="BALL">BALL</option>
                                                    <option @if (old('satuan')=='PCS' ) selected @endif value="PCS">PCS</option>
                                                </select>
                                                @error('kategori')
                                                <div class="help-block">
                                                    <ul role="alert">
                                                        <li>{{ $message }}</li>
                                                    </ul>
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group  @error('kategori_komisi') error @enderror">
                                                <select name="kategori_komisi" id="kategori_komisi" class="form-control">
                                                    <option value="">Kategori Komisi</option>
                                                    @foreach ($kategorikomisi as $d)
                                                    <option {{ old('kategori_komisi') == $d->kategori_komisi ? 'selected' : '' }} value="{{ $d->kategori_komisi}}">
                                                        {{ $d->kategori_komisi}}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('kode_produk')
                                                <div class="help-block">
                                                    <ul role="alert">
                                                        <li>{{ $message }}</li>
                                                    </ul>
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <x-inputtext label="Jml Pcs / Dus" field="isipcsdus" icon="feather icon-file" />
                                        </div>
                                        <div class="col-4">
                                            <x-inputtext label="Jml Pack / Dus" field="isipack" icon="feather icon-file" />
                                        </div>
                                        <div class="col-4">
                                            <x-inputtext label="Jml Pcs / Pack" field="isipcs" icon="feather icon-file" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1"><i class="fa fa-send mr-1"></i> Simpan</button>
                                            <a href="{{ url()->previous() }}" class="btn btn-outline-warning mr-1 mb-1"><i class="fa fa-arrow-left mr-2"></i>Kembali</a>
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
