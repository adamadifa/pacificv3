@extends('layouts.midone')
@section('titlepage', 'Tambah Data Salesman')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Edit COA</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/coa/createt">Edit COA</a></li>
                                <li class="breadcrumb-item"><a href="#">Edit COA</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">

        <form class="form" action="/coa/store" method="POST" id="frmCoa">
            <div class="col-md-12">
                @include('layouts.notification')
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Data COA</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    @csrf
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <x-inputtext label="Kode Akun" field="kode_akun"
                                                    icon="feather icon-credit-card" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <x-inputtext label="Nama Akun" field="nama_akun" icon="feather icon-file" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <select name="sub_akun" id="sub_akun" class="form-control select2">
                                                        <option value="">Parent Account</option>
                                                        @foreach ($coa as $d)
                                                            <option value="{{ $d->kode_akun }}">{{ $d->kode_akun }}
                                                                {{ $d->nama_akun }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <select name="kode_kategori" id="kode_kategori" class="form-control">
                                                        <option value="">Pilih Kategori</option>
                                                        @foreach ($kategori as $d)
                                                            <option value="{{ $d->kode_kategori }}">{{ $d->nama_kategori }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary mr-1 mb-1"><i
                                                        class="fa fa-send mr-1"></i> Simpan</button>
                                                <a href="/coa" class="btn btn-outline-warning mr-1 mb-1"><i
                                                        class="fa fa-arrow-left mr-2"></i>Kembali</a>
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
@push('myscript')
    <script>
        $(function() {
            $("#frmCoa").submit(function() {
                var kode_akun = $("#kode_akun").val();
                var nama_akun = $("#nama_akun").val();
                var sub_akun = $("#sub_akun").val();
                if (kode_akun == "") {
                    swal("Oops", "Kode Akun Harus Diisi", "warning");
                    return false;
                } else if (nama_akun == "") {
                    swal("Oops", "Nama Akun Harus Diisi", "warning");
                    return false;
                } else if (sub_akun == "") {
                    swal("Oops", "Akun Parent Harus Diisi", "warning");
                    return false;
                }
            });
        });
    </script>
@endpush
