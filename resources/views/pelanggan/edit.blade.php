@extends('layouts.midone')
@section('titlepage', 'Edit Data Pelanggan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 calass="content-header-title float-left mb-0">Edit Pelanggan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/pelanggan">Pelanggan</a></li>
                            <li class="breadcrumb-item"><a href="#">Edit Pelanggan</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <form class="form" action="/pelanggan/{{ Crypt::encrypt($data->kode_pelanggan) }}/update" method="POST" enctype="multipart/form-data">
        <div class="col-md-12">
            @include('layouts.notification')
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data Pelanggan</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Kode Pelanggan" field="kode_pelanggan" icon="feather icon-credit-card" value="{{ $data->kode_pelanggan }}" readonly />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <x-inputtext label="NIK" field="nik" icon="feather icon-credit-card" value="{{ $data->nik }}" />
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <x-inputtext label="No. KK" field="no_kk" icon="feather icon-credit-card" value="{{ $data->no_kk }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Nama Pelanggan" field="nama_pelanggan" icon="feather icon-user" value="{{ $data->nama_pelanggan }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Tanggal Lahir" field="tgl_lahir" icon="feather icon-calendar" datepicker value="{{ $data->tgl_lahir }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Alamat Pelanggan" field="alamat_pelanggan" icon="feather icon-map" value="{{ $data->alamat_pelanggan }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Alamat Toko" field="alamat_toko" icon="feather icon-map" value="{{ $data->alamat_toko }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-10">
                                            <x-inputtext label="No. HP" field="no_hp" icon="feather icon-phone" value="{{ $data->no_hp }}" />
                                        </div>
                                        <div class="col-2">
                                            <div class="vs-checkbox-con vs-checkbox-primary">

                                                <input type="checkbox" class="na_nohp" @if ($data->no_hp == 'NA')
                                                checked
                                                @endif name="na_nohp" value="1">
                                                <span class="vs-checkbox">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="">NA</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group @error('pasar') error @enderror">
                                                <select name=" pasar" id="pasar" class="form-control select2">
                                                    <option value="">Pilih Wilayah / Rute</option>
                                                    @foreach ($pasar as $d)
                                                    <option {{ $data->pasar == $d->nama_pasar  ? 'selected' : ''}} value="{{ $d->nama_pasar }}">{{ $d->nama_pasar }}</option>
                                                    @endforeach
                                                </select>
                                                @error('pasar')
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
                                            <div class="form-group  @error('hari') error @enderror">
                                                <select name="hari" id="" class="form-control">
                                                    <option value="">Hari</option>
                                                    <option @isset($data->hari) @if (old('hari'))
                                                        {{ old('hari') == 'Senin' ? 'selected' : '' }} @else
                                                        {{ $data->hari == 'Senin' ? 'selected' : '' }} @endif @else
                                                        {{ old('hari') == 'Senin' ? 'selected' : '' }}
                                                        @endisset value="Senin">Senin</option>
                                                    <option @isset($data->hari) @if (old('hari'))
                                                        {{ old('hari') == 'Selasa' ? 'selected' : '' }} @else
                                                        {{ $data->hari == 'Selasa' ? 'selected' : '' }} @endif @else
                                                        {{ old('hari') == 'Selasa' ? 'selected' : '' }}
                                                        @endisset value="Selasa">Selasa</option>
                                                    <option @isset($data->hari) @if (old('hari'))
                                                        {{ old('hari') == 'Rabu' ? 'selected' : '' }} @else
                                                        {{ $data->hari == 'Rabu' ? 'selected' : '' }} @endif @else
                                                        {{ old('hari') == 'Rabu' ? 'selected' : '' }}
                                                        @endisset value="Rabu">Rabu</option>
                                                    <option @isset($data->hari) @if (old('hari'))
                                                        {{ old('hari') == 'Kamis' ? 'selected' : '' }} @else
                                                        {{ $data->hari == 'Kamis' ? 'selected' : '' }} @endif @else
                                                        {{ old('hari') == 'Kamis' ? 'selected' : '' }}
                                                        @endisset value="Kamis">Kamis</option>
                                                    <option @isset($data->hari) @if (old('hari'))
                                                        {{ old('hari') == 'Jumat' ? 'selected' : '' }} @else
                                                        {{ $data->hari == 'Jumat' ? 'selected' : '' }} @endif @else
                                                        {{ old('hari') == 'Jumat' ? 'selected' : '' }}
                                                        @endisset value="Jumat">Jumat</option>
                                                    <option @isset($data->hari) @if (old('hari'))
                                                        {{ old('hari') == 'Sabtu' ? 'selected' : '' }} @else
                                                        {{ $data->hari == 'Sabtu' ? 'selected' : '' }} @endif @else
                                                        {{ old('hari') == 'Sabtu' ? 'selected' : '' }}
                                                        @endisset value="Sabtu">Sabtu</option>
                                                    <option @isset($data->hari) @if (old('hari'))
                                                        {{ old('hari') == 'Minggu' ? 'selected' : '' }} @else
                                                        {{ $data->hari == 'Minggu' ? 'selected' : '' }} @endif @else
                                                        {{ old('hari') == 'Minggu' ? 'selected' : '' }}
                                                        @endisset value="Minggu">Minggu</option>
                                                </select>
                                                @error('hari')
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
                                        @if (Auth::user()->kode_cabang=="PCF")
                                        <div class="col-lg-6 col-sm-12">
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
                                        @else
                                        <input type="hidden" name="kode_cabang" id="kode_cabang" value="{{ Auth::user()->kode_cabang   }}" readonly>
                                        @endif
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group   @error('id_karyawan') error @enderror"">
                                                <select name=" id_karyawan" id="id_karyawan" class="form-control">
                                                <option value="">Salesman</option>
                                                </select>
                                                @error('id_karyawan')
                                                <div class="help-block">
                                                    <ul role="alert">
                                                        <li>{{ $message }}</li>
                                                    </ul>
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    @if (Auth::user()->kode_cabang=="PCF")
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Limit Pelanggan" field="limitpel" icon="feather icon-file" value="{{ rupiah($data->limitpel) }}" right money />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group  @error('jatuhtempo') error @enderror">
                                                <select name="jatuhtempo" id="" class="form-control">
                                                    <option value="">Jatuh Tempo</option>
                                                    <option @isset($data->jatuhtempo) @if (old('jatuhtempo'))
                                                        {{ old('jatuhtempo') == '14' ? 'selected' : '' }} @else
                                                        {{ $data->jatuhtempo == '14' ? 'selected' : '' }} @endif @else
                                                        {{ old('jatuhtempo') == '14' ? 'selected' : '' }}
                                                        @endisset value="14">14</option>
                                                    <option @isset($data->jatuhtempo) @if (old('hari'))
                                                        {{ old('jatuhtempo') == '30' ? 'selected' : '' }} @else
                                                        {{ $data->jatuhtempo == '30' ? 'selected' : '' }} @endif @else
                                                        {{ old('jatuhtempo') == '30' ? 'selected' : '' }}
                                                        @endisset value="30">30</option>
                                                    <option @isset($data->jatuhtempo) @if (old('hari'))
                                                        {{ old('jatuhtempo') == '45' ? 'selected' : '' }} @else
                                                        {{ $data->jatuhtempo == '45' ? 'selected' : '' }} @endif @else
                                                        {{ old('jatuhtempo') == '45' ? 'selected' : '' }}
                                                        @endisset value="45">45</option>
                                                </select>
                                                @error('jatuhtempo')
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
                                    <input type="hidden" name="limitpel" id="limitpel" value="{{ rupiah($data->limitpel) }}">
                                    <input type="hidden" name="jatuhtempo" id="jatuhtempo" value="{{ $data->jatuhtempo }}">
                                    @endif
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group  @error('status_pelanggan') error @enderror">
                                                <select name="status_pelanggan" id="" class="form-control">
                                                    <option value="">Status</option>
                                                    <option @isset($data->status_pelanggan) @if (old('status_pelanggan'))
                                                        {{ old('status_pelanggan') == '1' ? 'selected' : '' }} @else
                                                        {{ $data->status_pelanggan == '1' ? 'selected' : '' }} @endif @else
                                                        {{ old('status_pelanggan') == '1' ? 'selected' : '' }}
                                                        @endisset value="1">AKTIF</option>
                                                    <option @isset($data->status_pelanggan) @if (old('status_pelanggan'))
                                                        {{ old('status_pelanggan') == '0' ? 'selected' : '' }} @else
                                                        {{ $data->status_pelanggan == '0' ? 'selected' : '' }} @endif @else
                                                        {{ old('status_pelanggan') == '0' ? 'selected' : '' }}
                                                        @endisset value="0">NON AKTIF</option>
                                                </select>
                                                @error('status_pelanggan')
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
                        <div class="card-content">
                            <div class="card-body">
                                <div class="alert alert-warning" role="alert">
                                    <h4 class="alert-heading">Informasi</h4>
                                    <p class="mb-0">
                                        Data Ini Bisa Diisi Saat Pengajuan Limit Kredit
                                    </p>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group  @error('kepemilikan') error @enderror">
                                            <select name="kepemilikan" id="" class="form-control">
                                                <option value="">Kepemilikan</option>
                                                <option @isset($data->kepemilikan) @if (old('kepemilikan'))
                                                    {{ old('kepemilikan') == 'Sewa' ? 'selected' : '' }} @else
                                                    {{ $data->kepemilikan == 'Sewa' ? 'selected' : '' }} @endif @else
                                                    {{ old('kepemilikan') == 'Sewa' ? 'selected' : '' }}
                                                    @endisset value="Sewa">Sewa</option>
                                                <option @isset($data->kepemilikan) @if (old('kepemilikan'))
                                                    {{ old('kepemilikan') == 'Milik Sendiri' ? 'selected' : '' }} @else
                                                    {{ $data->kepemilikan == 'Milik Sendiri' ? 'selected' : '' }} @endif @else
                                                    {{ old('kepemilikan') == 'Milik Sendiri' ? 'selected' : '' }}
                                                    @endisset value="Milik Sendiri">Milik Sendiri</option>
                                            </select>
                                            @error('kepemilikan')
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
                                        <div class="form-group  @error('lama_usaha') error @enderror">
                                            <select name="lama_usaha" id="" class="form-control">
                                                <option value="">Lama Usaha</option>
                                                <option @isset($data->lama_usaha) @if (old('lama_usaha'))
                                                    {{ old('lama_usaha') == '< 2 Tahun' ? 'selected' : '' }} @else
                                                    {{ $data->lama_usaha == '< 2 Tahun' ? 'selected' : '' }} @endif @else
                                                    {{ old('lama_usaha') == '< 2 Tahun' ? 'selected' : '' }}
                                                    @endisset value="< 2 Tahun">
                                                        < 2 Tahun</option>
                                                <option @isset($data->lama_usaha) @if (old('lama_usaha'))
                                                    {{ old('lama_usaha') == '2-5 Tahun' ? 'selected' : '' }} @else
                                                    {{ $data->lama_usaha == '2-5 Tahun' ? 'selected' : '' }} @endif @else
                                                    {{ old('lama_usaha') == '2-5 Tahun' ? 'selected' : '' }}
                                                    @endisset value="2-5 Tahun">2-5 Tahun</option>
                                                <option @isset($data->lama_usaha) @if (old('lama_usaha'))
                                                    {{ old('lama_usaha') == '> 5 Tahun' ? 'selected' : '' }} @else
                                                    {{ $data->lama_usaha == '> 5 Tahun' ? 'selected' : '' }} @endif @else
                                                    {{ old('lama_usaha') == '> 5 Tahun' ? 'selected' : '' }}
                                                    @endisset value="> 5 Tahun">> 5 Tahun</option>
                                            </select>
                                            @error('lama_usaha')
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
                                        <div class="form-group  @error('status_outlet') error @enderror">
                                            <select name="status_outlet" id="" class="form-control">
                                                <option value="">Status Outlet</option>
                                                <option @isset($data->status_outlet) @if (old('status_outlet'))
                                                    {{ old('status_outlet') == '1' ? 'selected' : '' }} @else
                                                    {{ $data->status_outlet == '1' ? 'selected' : '' }} @endif @else
                                                    {{ old('status_outlet') == '1' ? 'selected' : '' }}
                                                    @endisset value="1">New Outlet</option>
                                                <option @isset($data->status_outlet) @if (old('status_outlet'))
                                                    {{ old('status_outlet') == '2' ? 'selected' : '' }} @else
                                                    {{ $data->status_outlet == '2' ? 'selected' : '' }} @endif @else
                                                    {{ old('status_outlet') == '2' ? 'selected' : '' }}
                                                    @endisset value="2">Existing Outlet</option>
                                            </select>
                                            @error('status_outlet')
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
                                        <div class="form-group  @error('type_outlet') error @enderror">
                                            <select name="type_outlet" id="" class="form-control">
                                                <option value="">Type Outlet</option>
                                                <option @isset($data->type_outlet) @if (old('type_outlet'))
                                                    {{ old('type_outlet') == '1' ? 'selected' : '' }} @else
                                                    {{ $data->type_outlet == '1' ? 'selected' : '' }} @endif @else
                                                    {{ old('type_outlet') == '1' ? 'selected' : '' }}
                                                    @endisset value="1">Grosir</option>
                                                <option @isset($data->type_outlet) @if (old('type_outlet'))
                                                    {{ old('type_outlet') == '2' ? 'selected' : '' }} @else
                                                    {{ $data->type_outlet == '2' ? 'selected' : '' }} @endif @else
                                                    {{ old('type_outlet') == '2' ? 'selected' : '' }}
                                                    @endisset @if (old('type_outlet')=='2' ) selected @endif value="2">Retail</option>
                                            </select>
                                            @error('type_outlet')
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
                                        <div class="form-group  @error('cara_pembayaran') error @enderror">
                                            <select name="cara_pembayaran" id="" class="form-control">
                                                <option value="">Cara Pembayaran</option>
                                                <option @isset($data->cara_pembayaran) @if (old('cara_pembayaran'))
                                                    {{ old('cara_pembayaran') == '1' ? 'selected' : '' }} @else
                                                    {{ $data->cara_pembayaran == '1' ? 'selected' : '' }} @endif @else
                                                    {{ old('cara_pembayaran') == '1' ? 'selected' : '' }}
                                                    @endisset value="1">Bank Transfer</option>
                                                <option @isset($data->cara_pembayaran) @if (old('cara_pembayaran'))
                                                    {{ old('cara_pembayaran') == '2' ? 'selected' : '' }} @else
                                                    {{ $data->cara_pembayaran == '2' ? 'selected' : '' }} @endif @else
                                                    {{ old('cara_pembayaran') == '2' ? 'selected' : '' }}
                                                    @endisset value="2">Advance Cash</option>
                                                <option @isset($data->cara_pembayaran) @if (old('cara_pembayaran'))
                                                    {{ old('cara_pembayaran') == '3' ? 'selected' : '' }} @else
                                                    {{ $data->cara_pembayaran == '3' ? 'selected' : '' }} @endif @else
                                                    {{ old('cara_pembayaran') == '3' ? 'selected' : '' }}
                                                    @endisset value="3">Cheque / Bilyet Giro</option>
                                            </select>
                                            @error('cara_pembayaran')
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
                                        <div class="form-group  @error('lama_langganan') error @enderror">
                                            <select name="lama_langganan" id="" class="form-control">
                                                <option value="">Lama Langganan</option>
                                                <option @isset($data->lama_langganan) @if (old('lama_langganan'))
                                                    {{ old('lama_langganan') == '< 2 Tahun' ? 'selected' : '' }} @else
                                                    {{ $data->lama_langganan == '< 2 Tahun' ? 'selected' : '' }} @endif @else
                                                    {{ old('lama_langganan') == '< 2 Tahun' ? 'selected' : '' }}
                                                    @endisset value="< 2 Tahun">
                                                        < 2 Tahun</option>
                                                <option @isset($data->lama_langganan) @if (old('lama_langganan'))
                                                    {{ old('lama_langganan') == '> 2 Tahun' ? 'selected' : '' }} @else
                                                    {{ $data->lama_langganan == '> 2 Tahun' ? 'selected' : '' }} @endif @else
                                                    {{ old('lama_langganan') == '> 2 Tahun' ? 'selected' : '' }} @endisset value="> 2 Tahun">> 2 Tahun</option>

                                            </select>
                                            @error('lama_langganan')
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
                                        <div class="form-group  @error('jaminan') error @enderror">
                                            <select name="jaminan" id="" class="form-control">
                                                <option value="">Jaminan</option>
                                                <option @isset($data->jaminan) @if (old('jaminan'))
                                                    {{ old('jaminan') == '1' ? 'selected' : '' }} @else
                                                    {{ $data->jaminan == '1' ? 'selected' : '' }} @endif @else
                                                    {{ old('jaminan') == '1' ? 'selected' : '' }}
                                                    @endisset value="1">Ada</option>

                                                <option @isset($data->jaminan) @if (old('jaminan'))
                                                    {{ old('jaminan') == '2' ? 'selected' : '' }} @else
                                                    {{ $data->jaminan == '2' ? 'selected' : '' }} @endif @else
                                                    {{ old('jaminan') == '2' ? 'selected' : '' }}
                                                    @endisset value="2">Tidak Ada</option>

                                            </select>
                                            @error('lama_langganan')
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
                                    <div class="col-lg-12 col-sm-12">
                                        <x-inputtext label="Lokasi" field="lokasi" icon="feather icon-map-pin" value="{{ $data->latitude }},{{ $data->longitude }}" />
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext label="Omset Toko" field="omset_toko" icon="feather icon-file" right value="{{ rupiah($data->omset_toko) }}" money />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group  @error('foto') error @enderror">
                                            <div class="custom-file">
                                                <input type="file" name="foto" class="custom-file-input" id="inputGroupFile01">
                                                <label class="custom-file-label" for="inputGroupFile01">Upload Foto</label>
                                            </div>
                                            @error('foto')
                                            <div class="help-block">
                                                <ul role="alert">
                                                    <li>{{ $message }}</li>
                                                </ul>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3 col-sm-12">
                                        @if (!empty($data->foto))
                                        @php
                                        $path = Storage::url('pelanggan/'.$data->foto);
                                        @endphp
                                        <img class="card-img img-fluid" src="{{ url($path) }}" alt="Card image">
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1"><i class="fa fa-refresh mr-1"></i> Update</button>
                                        <a href="{{ url()->previous() }}" class="btn btn-outline-warning mr-1 mb-1"><i class="fa fa-arrow-left mr-2"></i>Kembali</a>
                                        @if (in_array($level,$pelanggan_ajuanlimit))
                                        <a href="/limitkredit/{{\Crypt::encrypt($data->kode_pelanggan)}}/create" class="btn btn-outline-info mr-1 mb-1"><i class="feather icon-external-link mr-2"></i>Ajukan Limit</a>
                                        @endif
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

        function loadsalesmancabang() {
            var kode_cabang = $("#kode_cabang").val();
            var id_karyawan = "{{ $data->id_sales }}";


            $.ajax({
                type: 'POST'
                , url: '/salesman/getsalescab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , id_karyawan: id_karyawan
                }
                , cache: false
                , success: function(respond) {
                    $("#id_karyawan").html(respond);
                }
            });
        }

        loadsalesmancabang();

        $("#kode_cabang").change(function() {
            loadsalesmancabang();
        });

        $('.na_nohp').change(function() {
            if (this.checked) {
                $("#no_hp").val("NA");
                $("#no_hp").attr("readonly", true);
            } else {
                $("#no_hp").val("");
                $("#no_hp").attr("readonly", false);
            }

        });
    });

</script>
@endpush
