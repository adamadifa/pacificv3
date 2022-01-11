@extends('layouts.midone')
@section('titlepage', 'Edit Data Kendaraan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Edit Kendaraan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/kendaraan">Kendaraan</a></li>
                            <li class="breadcrumb-item"><a href="#">Edit Kendaraan</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <form class="form" action="/kendaraan/{{ Crypt::encrypt($data->id) }}/update" method="POST">
        <div class="col-md-12">
            @include('layouts.notification')
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
                                            <x-inputtext label="No. Polisi" field="no_polisi" icon="feather icon-truck" value="{{ $data->no_polisi }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Type Kendaraan" field="type" icon="feather icon-file" value="{{ $data->type }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Model Kendaraan" field="model" icon="feather icon-file" value="{{ $data->model }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Tahun" field="tahun" icon="feather icon-file" value="{{ $data->tahun }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="No. Mesin" field="no_mesin" icon="fa fa-barcode" value="{{ $data->no_mesin }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="No. Rangka" field="no_rangka" icon="fa fa-barcode" value="{{ $data->no_rangka }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="No. STNK" field="no_stnk" icon="fa fa-barcode" value="{{ $data->no_stnk }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Tanggal Pajak" field="pajak" icon="feather icon-calendar" datepicker value="{{ $data->pajak }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Atas Nama" field="atas_nama" icon="feather icon-user" value="{{ $data->atas_nama }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Tanggal KEUR" field="keur" icon="feather icon-calendar" datepicker value="{{ $data->pajak }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="No. Uji" field="no_uji" icon="fa fa-barcode" value="{{ $data->no_uji }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Tanggal KIR" field="kir" icon="feather icon-calendar" datepicker value="{{ $data->kir }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Tanggal STNK" field="stnk" icon="feather icon-calendar" datepicker value="{{ $data->stnk }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Tanggal SIPA" field="sipa" icon="feather icon-calendar" datepicker value="{{ $data->sipa }}" />
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Pengguna" field="pemakai" icon="feather icon-user" value="{{ $data->pemakai }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Jabatan" field="jabatan" icon="feather icon-file" value="{{ $data->jabatan }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Keterangan" field="keterangan" icon="feather icon-file" value="{{ $data->keterangan }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group  @error('status') error @enderror">
                                                <select name="status" id="" class="form-control">
                                                    <option value="">Status</option>
                                                    <option @isset($data->status) @if (old('status'))
                                                        {{ old('status') == 'Non Operasional' ? 'selected' : '' }} @else
                                                        {{ $data->status == 'Non Operasional' ? 'selected' : '' }} @endif @else
                                                        {{ old('status') == 'Non Operasional' ? 'selected' : '' }}
                                                        @endisset value="Non Operasional">Non Operasional</option>
                                                    <option @isset($data->status) @if (old('status'))
                                                        {{ old('status') == 'Operasional' ? 'selected' : '' }} @else
                                                        {{ $data->status == 'Operasional' ? 'selected' : '' }} @endif @else
                                                        {{ old('status') == 'Operasional' ? 'selected' : '' }}
                                                        @endisset value="Operasional">Operasional</option>
                                                </select>
                                                @error('status')
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
                                                    <option @isset($data->kode_cabang) @if (old('kode_cabang'))
                                                        {{ old('kode_cabang') == $c->kode_cabang ? 'selected' : '' }} @else
                                                        {{ $data->kode_cabang == $c->kode_cabang ? 'selected' : '' }} @endif @else
                                                        {{ old('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}
                                                        @endisset value="{{ $c->kode_cabang}}">
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
                                            <a href="/kendaraan" class="btn btn-outline-warning mr-1 mb-1"><i class="fa fa-arrow-left mr-2"></i>Kembali</a>
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
