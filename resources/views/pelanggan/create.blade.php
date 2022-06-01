@extends('layouts.midone')
@section('titlepage', 'Tambah Data Pelanggan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Tambah Pelanggan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/pelanggan">Pelanggan</a></li>
                            <li class="breadcrumb-item"><a href="#">Tambah Pelanggan</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <form class="form" action="/pelanggan/store" method="POST" enctype="multipart/form-data">
        <div class="col-md-12">

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
                                            <x-inputtext label="Kode Pelanggan" field="kode_pelanggan" icon="feather icon-credit-card" value="Auto" readonly />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <x-inputtext label="NIK" field="nik" icon="feather icon-credit-card" />
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <x-inputtext label="No. KK" field="no_kk" icon="feather icon-credit-card" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Nama Pelanggan" field="nama_pelanggan" icon="feather icon-user" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Tanggal Lahir" field="tgl_lahir" icon="feather icon-calendar" datepicker />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Alamat Pelanggan" field="alamat_pelanggan" icon="feather icon-map" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="Alamat Toko" field="alamat_toko" icon="feather icon-map" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext label="No. HP" field="no_hp" icon="feather icon-phone" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <select name="pasar" id="pasar" class="form-control select2">
                                                    <option value="">Pilih Pasar</option>
                                                    @foreach ($pasar as $d)
                                                    <option value="{{ $d->nama_pasar }}">{{ $d->nama_pasar }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group  @error('hari') error @enderror">
                                                <select name="hari" id="" class="form-control">
                                                    <option value="">Hari</option>
                                                    <option @if (old('hari')=='Senin' ) selected @endif value="Senin">Senin</option>
                                                    <option @if (old('hari')=='Selasa' ) selected @endif value="Selasa">Selasa</option>
                                                    <option @if (old('hari')=='Rabu' ) selected @endif value="Rabu">Rabu</option>
                                                    <option @if (old('hari')=='Kamis' ) selected @endif value="Kamis">Kamis</option>
                                                    <option @if (old('hari')=='Jumat' ) selected @endif value="Jumat">Jumat</option>
                                                    <option @if (old('hari')=='Sabtu' ) selected @endif value="Sabtu">Sabtu</option>
                                                    <option @if (old('hari')=='Minggu' ) selected @endif value="Minggu">Minggu</option>
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
                                        @else
                                        <input type="hidden" name="kode_cabang" id="kode_cabang" value="{{ Auth::user()->kode_cabang=="GRT" ? "TSM" :  Auth::user()->kode_cabang   }}" readonly>
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
                                            <x-inputtext label="Limit Pelanggan" field="limitpel" icon="feather icon-file" right />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group  @error('jatuhtempo') error @enderror">
                                                <select name="jatuhtempo" id="" class="form-control">
                                                    <option value="">Jatuh Tempo</option>
                                                    <option @if (old('jatuhtempo')=='14' ) selected @endif value="14">14</option>
                                                    <option @if (old('jatuhtempo')=='30' ) selected @endif value="30">30</option>
                                                    <option @if (old('jatuhtempo')=='45' ) selected @endif value="45">45</option>
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
                                    <input type="hidden" name="limitpel" id="limitpel">
                                    <input type="hidden" name="jatuhtempo" id="jatuhtempo">
                                    @endif


                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group  @error('status_pelanggan') error @enderror">
                                                <select name="status_pelanggan" id="" class="form-control">
                                                    <option value="">Status</option>
                                                    <option @if (old('status_pelanggan')=='1' ) selected @endif value="1">AKTIF</option>
                                                    <option @if (old('status_pelanggan')=='0' ) selected @endif value="0">NON AKTIF</option>
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
                                                <option @if (old('kepemilikan')=='Sewa' ) selected @endif value="Sewa">Sewa</option>
                                                <option @if (old('kepemilikan')=='Milik Sendiri' ) selected @endif value="Milik Sendiri">Milik Sendiri</option>
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
                                                <option @if (old('lama_usaha')=='< 2 Tahun' ) selected @endif value="< 2 Tahun">
                                                    < 2 Tahun</option>
                                                <option @if (old('lama_usaha')=='2-5 Tahun' ) selected @endif value="2-5 Tahun">2-5 Tahun</option>
                                                <option @if (old('lama_usaha')=='> 5 Tahun' ) selected @endif value="> 5 Tahun">> 5 Tahun</option>
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
                                                <option @if (old('status_outlet')=='1' ) selected @endif value="1">New Outlet</option>
                                                <option @if (old('status_outlet')=='2' ) selected @endif value="2">Existing Outlet</option>
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
                                                <option @if (old('type_outlet')=='1' ) selected @endif value="1">Grosir</option>
                                                <option @if (old('type_outlet')=='2' ) selected @endif value="2">Retail</option>
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
                                                <option @if (old('cara_pembayaran')=='1' ) selected @endif value="1">Bank Transfer</option>
                                                <option @if (old('cara_pembayaran')=='2' ) selected @endif value="2">Advance Cash</option>
                                                <option @if (old('cara_pembayaran')=='3' ) selected @endif value="3">Cheque / Bilyet Giro</option>
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
                                                <option @if (old('lama_langganan')=='< 2 Tahun' ) selected @endif value="< 2 Tahun">
                                                    < 2 Tahun</option>
                                                <option @if (old('lama_langganan')=='> 2 Tahun' ) selected @endif value="> 2 Tahun">> 2 Tahun</option>

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
                                                <option @if (old('jaminan')=='1' ) selected @endif value="1">Ada</option>

                                                <option @if (old('jaminan')=='2' ) selected @endif value="2">Tidak Ada</option>

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
                                    <div class="col-lg-6 col-sm-12">
                                        <x-inputtext label="Latitude" field="latitude" icon="feather icon-map-pin" />
                                    </div>
                                    <div class="col-lg-6 col-sm-12">
                                        <x-inputtext label="Longitude" field="longitude" icon="feather icon-map-pin" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext label="Omset Toko" field="omset_toko" icon="feather icon-file" right />
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
    </form>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        function loadsalesmancabang() {
            var kode_cabang = $("#kode_cabang").val();
            var id_karyawan = "";


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
    });

</script>
@endpush
