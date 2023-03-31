@extends('layouts.midone')
@section('titlepage', 'Tambah Data Salesman')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Tambah Salesman</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/salesman">Salesman</a></li>
                            <li class="breadcrumb-item"><a href="#">Tambah Salesman</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">

    <form class="form" action="/salesman/{{ Crypt::encrypt($data->id_karyawan) }}/update" method="POST">
        <div class="col-md-12">
            @include('layouts.notification')
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data Salesman</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="ID Salesman" field="id_karyawan" icon="feather icon-credit-card" value="{{ $data->id_karyawan }}" readonly />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Nama Salesman" field="nama_karyawan" icon="feather icon-user" value="{{ $data->nama_karyawan }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Alamat Salesman" field="alamat_karyawan" icon="feather icon-map" value="{{ $data->alamat_karyawan }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="No. HP" field="no_hp" icon="feather icon-phone" value="{{ $data->no_hp }}" />
                                        </div>
                                    </div>
                                    @if ($getcbg == "PCF")

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
                                    <input type="hidden" id="kode_cabang" name="kode_cabang" value="{{ $getcbg }}">
                                    @endif
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group  @error('kategori_salesman') error @enderror">
                                                <select name="kategori_salesman" id="" class="form-control">
                                                    <option value="">Kategori Salesman</option>
                                                    <option @isset($data->kategori_salesman) @if (old('kategori_salesman'))
                                                        {{ old('kategori_salesman') == 'TO' ? 'selected' : '' }} @else
                                                        {{ $data->kategori_salesman == 'TO' ? 'selected' : '' }} @endif @else
                                                        {{ old('kategori_salesman') == 'TO' ? 'selected' : '' }}
                                                        @endisset value="TO">TO</option>
                                                    <option @isset($data->kategori_salesman) @if (old('kategori_salesman'))
                                                        {{ old('kategori_salesman') == 'CANVASER' ? 'selected' : '' }} @else
                                                        {{ $data->kategori_salesman == 'CANVASER' ? 'selected' : '' }} @endif @else
                                                        {{ old('kategori_salesman') == 'CANVASER' ? 'selected' : '' }}
                                                        @endisset value="CANVASER">CANVASER</option>
                                                    <option @isset($data->kategori_salesman) @if (old('kategori_salesman'))
                                                        {{ old('kategori_salesman') == 'RETAIL' ? 'selected' : '' }} @else
                                                        {{ $data->kategori_salesman == 'RETAIL' ? 'selected' : '' }} @endif @else
                                                        {{ old('kategori_salesman') == 'RETAIL' ? 'selected' : '' }}
                                                        @endisset value="RETAIL">RETAIL</option>
                                                    <option @isset($data->kategori_salesman) @if (old('kategori_salesman'))
                                                        {{ old('kategori_salesman') == 'MOTORIS' ? 'selected' : '' }} @else
                                                        {{ $data->kategori_salesman == 'MOTORIS' ? 'selected' : '' }} @endif @else
                                                        {{ old('kategori_salesman') == 'MOTORIS' ? 'selected' : '' }}
                                                        @endisset value="MOTORIS">MOTORIS</option>
                                                </select>
                                                @error('kategori_salesman')
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
                                            <div class="form-group  @error('status_aktif_sales') error @enderror">
                                                <select name="status_aktif_sales" id="" class="form-control">
                                                    <option value="">Status</option>
                                                    <option @isset($data->status_aktif_sales) @if (old('status_aktif_sales'))
                                                        {{ old('status_aktif_sales') == '1' ? 'selected' : '' }} @else
                                                        {{ $data->status_aktif_sales == '1' ? 'selected' : '' }} @endif @else
                                                        {{ old('status_aktif_sales') == '1' ? 'selected' : '' }}
                                                        @endisset value="1">AKTIF</option>
                                                    <option @isset($data->status_aktif_sales) @if (old('status_aktif_sales'))
                                                        {{ old('status_aktif_sales') == '0' ? 'selected' : '' }} @else
                                                        {{ $data->status_aktif_sales == '0' ? 'selected' : '' }} @endif @else
                                                        {{ old('status_aktif_sales') == '0' ? 'selected' : '' }}
                                                        @endisset value="0">NON AKTIF</option>
                                                </select>
                                                @error('status_aktif_sales')
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
                                            <div class="form-group  @error('status_komisi') error @enderror">
                                                <select name="status_komisi" id="" class="form-control">
                                                    <option value="">Status Komisi</option>
                                                    <option @isset($data->status_komisi) @if (old('status_komisi'))
                                                        {{ old('status_komisi') == '1' ? 'selected' : '' }} @else
                                                        {{ $data->status_komisi == '1' ? 'selected' : '' }} @endif @else
                                                        {{ old('status_komisi') == '1' ? 'selected' : '' }}
                                                        @endisset value="1">AKTIF</option>
                                                    <option @isset($data->status_komisi) @if (old('status_komisi'))
                                                        {{ old('status_komisi') == '0' ? 'selected' : '' }} @else
                                                        {{ $data->status_komisi == '0' ? 'selected' : '' }} @endif @else
                                                        {{ old('status_komisi') == '0' ? 'selected' : '' }}
                                                        @endisset value="0">NON AKTIF</option>
                                                </select>
                                                @error('status_komisi')
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
                                        <div class="col-2">
                                            <x-inputtext label="" field="no_fak_cabang" value="{{ $data->kode_cabang }}" readonly icon="feather icon-file" />
                                        </div>
                                        <div class="col-2">
                                            <x-inputtext label="Kode" field="kode_faktur" value="{{ substr($data->no_fak_awal,3,1) }}" icon="feather icon-file" />
                                        </div>
                                        <div class="col-8">
                                            <x-inputtext label="Nomor Awal" field="nomor_awal" value="{{ substr($data->no_fak_awal,4) }}" icon="feather icon-file" />
                                        </div>
                                    </div>
                                    <div class=" row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1"><i class="fa fa-send mr-1"></i> Simpan</button>
                                            <a href="/salesman" class="btn btn-outline-warning mr-1 mb-1"><i class="fa fa-arrow-left mr-2"></i>Kembali</a>
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
