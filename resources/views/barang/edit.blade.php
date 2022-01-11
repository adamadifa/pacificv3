@extends('layouts.midone')
@section('titlepage', 'Tambah Data Barang')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Edit Barang</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/barang">Barang</a></li>
                            <li class="breadcrumb-item"><a href="#">Edit Barang</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <form class="form" action="/barang/{{ Crypt::encrypt($data->kode_produk) }}/update" method="POST">
        <div class="col-md-12">
            @include('layouts.notification')
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
                                            <x-inputtext label="Kode Produk" field="kode_produk" icon="feather icon-credit-card" value="{{ $data->kode_produk }}" readonly />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Nama Barang" field="nama_barang" icon="feather icon-file" value="{{ $data->nama_barang }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group  @error('kategori_jenisproduk') error @enderror">
                                                <select name="kategori_jenisproduk" id="" class="form-control">
                                                    <option value="">Jenis Produk</option>
                                                    <option @isset($data->kategori_jenisproduk) @if (old('kategori_jenisproduk'))
                                                        {{ old('kategori_jenisproduk') == 'CABE GILING' ? 'selected' : '' }} @else
                                                        {{ $data->kategori_jenisproduk == 'CABE GILING' ? 'selected' : '' }} @endif @else
                                                        {{ old('kategori_jenisproduk') == 'CABE GILING' ? 'selected' : '' }}
                                                        @endisset value="CABE GILING">CABE GILING</option>
                                                    <option @isset($data->kategori_jenisproduk) @if (old('kategori_jenisproduk'))
                                                        {{ old('kategori_jenisproduk') == 'SAOS BAWANG' ? 'selected' : '' }} @else
                                                        {{ $data->kategori_jenisproduk == 'SAOS BAWANG' ? 'selected' : '' }} @endif @else
                                                        {{ old('kategori_jenisproduk') == 'SAOS BAWANG' ? 'selected' : '' }}
                                                        @endisset value="SAOS BAWANG">SAOS BAWANG</option>
                                                    <option @isset($data->kategori_jenisproduk) @if (old('kategori_jenisproduk'))
                                                        {{ old('kategori_jenisproduk') == 'SAOS PREMIUM' ? 'selected' : '' }} @else
                                                        {{ $data->kategori_jenisproduk == 'SAOS PREMIUM' ? 'selected' : '' }} @endif @else
                                                        {{ old('kategori_jenisproduk') == 'SAOS PREMIUM' ? 'selected' : '' }}
                                                        @endisset value="SAOS PREMIUM">SAOS PREMIUM</option>
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
                                                    <option @isset($data->jenis_produk) @if (old('jenis_produk'))
                                                        {{ old('jenis_produk') == 'AIDA' ? 'selected' : '' }} @else
                                                        {{ $data->jenis_produk == 'AIDA' ? 'selected' : '' }} @endif @else
                                                        {{ old('jenis_produk') == 'AIDA' ? 'selected' : '' }}
                                                        @endisset value="AIDA">AIDA</option>
                                                    <option @isset($data->jenis_produk) @if (old('jenis_produk'))
                                                        {{ old('jenis_produk') == 'SWAN' ? 'selected' : '' }} @else
                                                        {{ $data->jenis_produk == 'SWAN' ? 'selected' : '' }} @endif @else
                                                        {{ old('jenis_produk') == 'SWAN' ? 'selected' : '' }}
                                                        @endisset value="SWAN">SWAN</option>
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
                                                    <option @isset($data->satuan) @if (old('satuan'))
                                                        {{ old('satuan') == 'DUS' ? 'selected' : '' }} @else
                                                        {{ $data->satuan == 'DUS' ? 'selected' : '' }} @endif @else
                                                        {{ old('satuan') == 'DUS' ? 'selected' : '' }}
                                                        @endisset value="DUS">DUS</option>
                                                    <option @isset($data->satuan) @if (old('satuan'))
                                                        {{ old('satuan') == 'BALL' ? 'selected' : '' }} @else
                                                        {{ $data->satuan == 'BALL' ? 'selected' : '' }} @endif @else
                                                        {{ old('satuan') == 'BALL' ? 'selected' : '' }}
                                                        @endisset value="BALL">BALL</option>
                                                    <option @isset($data->satuan) @if (old('satuan'))
                                                        {{ old('satuan') == 'PCS' ? 'selected' : '' }} @else
                                                        {{ $data->satuan == 'PCS' ? 'selected' : '' }} @endif @else
                                                        {{ old('satuan') == 'PCS' ? 'selected' : '' }}
                                                        @endisset value="PCS">PCS</option>
                                                </select>
                                                @error('satuan')
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
                                                    <option @isset($data->kategori_komisi) @if (old('kategori_komisi'))
                                                        {{ old('kategori_komisi') == $d->kategori_komisi ? 'selected' : '' }} @else
                                                        {{ $data->kategori_komisi == $d->kategori_komisi ? 'selected' : '' }} @endif @else
                                                        {{ old('kategori_komisi') == $d->kategori_komisi ? 'selected' : '' }}
                                                        @endisset value="{{ $d->kategori_komisi}}">
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
                                            <x-inputtext label="Jml Pcs / Dus" field="isipcsdus" icon="feather icon-file" value="{{ $data->isipcsdus }}" />
                                        </div>
                                        <div class="col-4">
                                            <x-inputtext label="Jml Pack / Dus" field="isipack" icon="feather icon-file" value="{{ $data->isipack }}" />
                                        </div>
                                        <div class="col-4">
                                            <x-inputtext label="Jml Pcs / Pack" field="isipcs" icon="feather icon-file" value="{{ $data->isipcs }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1"><i class="fa fa-send mr-1"></i> Simpan</button>
                                            <a href="/barang" class="btn btn-outline-warning mr-1 mb-1"><i class="fa fa-arrow-left mr-2"></i>Kembali</a>
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
