@extends('layouts.midone')
@section('titlepage', 'Data Anggota')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Tambah Harga</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/harga">Harga</a></li>
                            <li class="breadcrumb-item"><a href="#">Tambah Harga</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <form class="form" action="/harga/store" method="POST">
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
                                            <x-inputtext label="Kode Barang" field="kode_barang" icon="feather icon-credit-card" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group  @error('kode_produk') error @enderror">
                                                <select name="kode_produk" id="kode_produk" class="form-control">
                                                    <option value="">Kode Produk | Nama Produk</option>
                                                    @foreach ($barang as $p)
                                                    <option {{ old('kode_produk') == $p->kode_produk ? 'selected' : '' }} value="{{ $p->kode_produk."|".$p->nama_barang }}">
                                                        {{ $p->kode_produk."|".$p->nama_barang }}
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
                                        <div class="col-6">
                                            <div class="form-group  @error('kategori') error @enderror">
                                                <select name="kategori" id="" class="form-control">
                                                    <option value="">Kategori</option>
                                                    <option @if (old('kategori')=='SWAN' ) selected @endif value="AIDA">AIDA</option>
                                                    <option @if (old('kategori')=='AIDA' ) selected @endif value="SWAN">SWAN</option>
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
                                        <div class="col-6">
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
                                            <div class="form-group  @error('kategori_harga') error @enderror">
                                                <select name="kategori_harga" id="" class="form-control">
                                                    <option value="">Kategori Harga</option>
                                                    <option @if (old('kategori_harga')=='NORMAL' ) selected @endif value="NORMAL">HARGA LAMA</option>
                                                    <option @if (old('kategori_harga')=='TO' ) selected @endif value="TO">TO</option>
                                                    <option @if (old('kategori_harga')=='CANVASER' ) selected @endif value="CANVASER">CANVASER</option>
                                                    <option @if (old('kategori_harga')=='RETAIL' ) selected @endif value="RETAIL">RETAIL</option>
                                                    <option @if (old('kategori_harga')=='MOTORIS' ) selected @endif value="MOTORIS">MOTORIS</option>
                                                </select>
                                                @error('kategori_harga')
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
                                            <div class="form-group  @error('kode_cabang') error @enderror">
                                                <select name="kode_cabang" id="kode_cabang" class="form-control">
                                                    <option value="">Cabang</option>
                                                    @foreach ($cabang as $c)
                                                    <option {{ old('kode_cabang') == $c->kode_cabang ? 'selected' : '' }} value="{{ $c->kode_cabang}}">
                                                        {{ $c->nama_cabang}}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('kode_cabang')
                                                <div class="help-block">
                                                    <ul role="alert">
                                                        <li>{{ $message }}</li>
                                                    </ul>
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data Harga</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-4">
                                        <x-inputtext label="Harga / Dus" field="harga_dus" icon="feather icon-file" right="true" money />
                                    </div>
                                    <div class="col-4">
                                        <x-inputtext label="Harga / Pack" field="harga_pack" icon="feather icon-file" right="true" money />
                                    </div>
                                    <div class="col-4">
                                        <x-inputtext label="Harga / Pcs" field="harga_pcs" icon="feather icon-file" right="true" money />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <x-inputtext label="Harga Retur / Dus" field="harga_returdus" icon="feather icon-file" right="true" money />
                                    </div>
                                    <div class="col-4">
                                        <x-inputtext label="Harga Retur / Pack" field="harga_returpack" icon="feather icon-file" right="true" money />
                                    </div>
                                    <div class="col-4">
                                        <x-inputtext label="Harga Retur / Pcs" field="harga_returpcs" icon="feather icon-file" right="true" money />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
                                        <button type="reset" class="btn btn-outline-warning mr-1 mb-1">Reset</button>
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

@push('myscript')
<script>
    $(function() {
        $('.money').mask("#.##0", {
            reverse: true
        });
    });

</script>
@endpush
