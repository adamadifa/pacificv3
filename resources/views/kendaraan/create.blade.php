@extends('layouts.midone')
@section('titlepage', 'Tambah Data Kendaraan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Tambah Kendaraan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/kendaraan">Kendaraan</a></li>
                            <li class="breadcrumb-item"><a href="#">Tambah Kendaraan</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <form class="form" action="/kendaraan/store" method="POST">
        <div class="col-md-12">

            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data Kendaraan</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="No. Polisi" field="no_polisi" icon="feather icon-truck" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Merk" field="merk" icon="feather icon-truck" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Type Kendaraan" field="tipe_kendaraan" icon="feather icon-file" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Tipe" field="tipe" icon="feather icon-file" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="No. Rangka" field="no_rangka" icon="fa fa-barcode" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="No. Mesin" field="no_mesin" icon="fa fa-barcode" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Tahun Pembuatan" field="tahun_pembuatan" icon="feather icon-file" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Atas Nama" field="atas_nama" icon="feather icon-user" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Alamat" field="atas_nama" icon="feather icon-map" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Jatuhtempo KIR" field="jatuhtempo_kir" icon="feather icon-calendar" datepicker />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Jatuhtempo Pajak 1 Tahun" field="jatuhtempo_pajak_satutahun" icon="feather icon-calendar" datepicker />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Jatuhtempo Pajak 5 Tahun" field="jatuhtempo_pajak_limatahun" icon="feather icon-calendar" datepicker />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Jenis" field="jenis" icon="feather icon-file" />
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
