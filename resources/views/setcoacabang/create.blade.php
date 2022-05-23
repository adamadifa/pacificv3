@extends('layouts.midone')
@section('titlepage', 'Tambah Data Salesman')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Tambah Akun Cabang</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Tambah Akun Cabang</a></li>
                            <li class="breadcrumb-item"><a href="#">Tambah Akun Cabang</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">

    <form class="form" action="/setcoacabang/store" method="POST" id="frmCoa">
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
                                            <div class="form-group">
                                                <select name="kode_cabang" id="kode_cabang" class="form-control select2">
                                                    <option value="">Pilih Cabang</option>
                                                    @foreach ($cabang as $d)
                                                    <option value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <select name="kategori" id="kategori" class="form-control">
                                                    <option value="">Pilih Kategori</option>
                                                    <option value="Kas Kecil">Kas Kecil</option>
                                                    <option value="Mutasi bank">Mutasi Bank</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <select name="kode_akun" id="kode_akun" class="form-control select2">
                                                    <option value="">Pilih Akun</option>
                                                    @foreach ($coa as $d)
                                                    <option value="{{ $d->kode_akun }}">{{ $d->kode_akun }} {{ $d->nama_akun }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1"><i class="fa fa-send mr-1"></i> Simpan</button>
                                            <a href="/set_coa_cabang" class="btn btn-outline-warning mr-1 mb-1"><i class="fa fa-arrow-left mr-2"></i>Kembali</a>
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
            var kode_cabang = $("#kode_cabang").val();
            var kategori = $("#kategori").val();
            if (kode_cabang == "") {
                swal("Oops", "Kode Cabang Harus Diisi", "warning");
                return false;
            } else if (kategori == "") {
                swal("Oops", "Kategori Harus Diisi", "warning");
                return false;
            } else if (kode_akun == "") {
                swal("Oops", "Kode Akun Harus Diisi", "warning");
                return false;
            }
        });
    });

</script>
@endpush
