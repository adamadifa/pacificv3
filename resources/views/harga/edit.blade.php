@extends('layouts.midone')
@section('titlepage', 'Data Anggota')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Edit Harga</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/harga">Harga</a></li>
                            <li class="breadcrumb-item"><a href="#">Edit Harga</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <form class="form" action="/harga/{{ Crypt::encrypt($data->kode_barang) }}/update" method="POST">
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
                                            <x-inputtext label="Kode Barang" field="kode_barang" icon="feather icon-credit-card" value="{{ $data->kode_barang }}" readonly />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group  @error('kode_produk') error @enderror">
                                                <select name="kode_produk" id="kode_produk" class="form-control" disabled>
                                                    <option value="">Kode Produk | Nama Produk</option>
                                                    @foreach ($barang as $p)
                                                    <option @isset($data->kode_produk) @if (old('kode_produk'))
                                                        {{ old('kode_produk') == $p->kode_produk ? 'selected' : '' }} @else
                                                        {{ $data->kode_produk == $p->kode_produk ? 'selected' : '' }} @endif @else
                                                        {{ old('kode_produk') == $p->kode_produk ? 'selected' : '' }}
                                                        @endisset value="{{ $p->kode_produk."|".$p->nama_barang }}">
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
                                                    <option @isset($data->kategori) @if (old('kategori'))
                                                        {{ old('kategori') == 'AIDA' ? 'selected' : '' }} @else
                                                        {{ $data->kategori == 'AIDA' ? 'selected' : '' }} @endif @else
                                                        {{ old('kategori') == 'AIDA' ? 'selected' : '' }}
                                                        @endisset value="AIDA">AIDA</option>
                                                    <option @isset($data->kategori) @if (old('kategori'))
                                                        {{ old('kategori') == 'SWAN' ? 'selected' : '' }} @else
                                                        {{ $data->kategori == 'SWAN' ? 'selected' : '' }} @endif @else
                                                        {{ old('kategori') == 'SWAN' ? 'selected' : '' }}
                                                        @endisset value="SWAN">SWAN</option>
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
                                            <div class="form-group  @error('kategori_harga') error @enderror">
                                                <select name="kategori_harga" id="" class="form-control">
                                                    <option value="">Kategori Harga</option>
                                                    <option @isset($data->kategori_harga) @if (old('kategori_harga'))
                                                        {{ old('kategori_harga') == 'NORMAL' ? 'selected' : '' }} @else
                                                        {{ $data->kategori_harga == 'NORMAL' ? 'selected' : '' }} @endif @else
                                                        {{ old('kategori_harga') == 'NORMAL' ? 'selected' : '' }}
                                                        @endisset value="NORMAL">HARGA LAMA</option>
                                                    <option @isset($data->kategori_harga) @if (old('kategori_harga'))
                                                        {{ old('kategori_harga') == 'TO' ? 'selected' : '' }} @else
                                                        {{ $data->kategori_harga == 'TO' ? 'selected' : '' }} @endif @else
                                                        {{ old('kategori_harga') == 'TO' ? 'selected' : '' }}
                                                        @endisset value="TO">TO</option>
                                                    <option @isset($data->kategori_harga) @if (old('kategori_harga'))
                                                        {{ old('kategori_harga') == 'CANVASER' ? 'selected' : '' }} @else
                                                        {{ $data->kategori_harga == 'CANVASER' ? 'selected' : '' }} @endif @else
                                                        {{ old('kategori_harga') == 'CANVASER' ? 'selected' : '' }}
                                                        @endisset value="CANVASER">CANVASER</option>
                                                    <option @isset($data->kategori_harga) @if (old('kategori_harga'))
                                                        {{ old('kategori_harga') == 'RETAIL' ? 'selected' : '' }} @else
                                                        {{ $data->kategori_harga == 'RETAIL' ? 'selected' : '' }} @endif @else
                                                        {{ old('kategori_harga') == 'RETAIL' ? 'selected' : '' }}
                                                        @endisset value="RETAIL">RETAIL</option>
                                                    <option @isset($data->kategori_harga) @if (old('kategori_harga'))
                                                        {{ old('kategori_harga') == 'MOTORIS' ? 'selected' : '' }} @else
                                                        {{ $data->kategori_harga == 'MOTORIS' ? 'selected' : '' }} @endif @else
                                                        {{ old('kategori_harga') == 'MOTORIS' ? 'selected' : '' }}
                                                        @endisset value="MOTORIS">MOTORIS</option>
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
                                    @if (Auth::user()->cabang =="PCF")
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group  @error('kode_cabang') error @enderror">
                                                <select name="kode_cabang" id="kode_cabang" class="form-control">
                                                    <option value="">Cabang</option>
                                                    @foreach ($cabang as $c)
                                                    <option @isset($data->kode_cabang) @if (old('kode_cabang'))
                                                        {{ old('kode_cabang') == $c->kode_cabang ? 'selected' : '' }} @else
                                                        {{ $data->kode_cabang == $c->kode_cabang ? 'selected' : '' }} @endif @else
                                                        {{ old('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}
                                                        @endisset value="{{ $c->kode_cabang}}">
                                                        {{ $c->nama_cabang}}
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
                                    @else
                                    <input type="hidden" name="kode_cabang" id="kode_cabang" value="{{ $data->kode_cabang }}">
                                    @endif
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
                                        <x-inputtext label="Harga / Dus" field="harga_dus" icon="feather icon-file" right="true" value="{{ rupiah($data->harga_dus) }}" money />
                                    </div>
                                    <div class="col-4">
                                        <x-inputtext label="Harga / Pack" field="harga_pack" icon="feather icon-file" right="true" value="{{ rupiah($data->harga_pack) }}" money />
                                    </div>
                                    <div class="col-4">
                                        <x-inputtext label="Harga / Pcs" field="harga_pcs" icon="feather icon-file" right="true" value="{{ rupiah($data->harga_pcs) }}" money />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <x-inputtext label="Harga Retur / Dus" field="harga_returdus" icon="feather icon-file" right="true" value="{{ rupiah($data->harga_returdus) }}" money />
                                    </div>
                                    <div class="col-4">
                                        <x-inputtext label="Harga Retur / Pack" field="harga_returpack" icon="feather icon-file" right="true" value="{{ rupiah($data->harga_returpack) }}" money />
                                    </div>
                                    <div class="col-4">
                                        <x-inputtext label="Harga Retur / Pcs" field="harga_returpcs" icon="feather icon-file" right="true" value="{{ rupiah($data->harga_returpcs) }}" money />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1"><i class="fa fa-refresh mr-1"></i> Update</button>
                                        <a href="/harga" class="btn btn-outline-warning mr-1 mb-1">Kembali</a>
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
