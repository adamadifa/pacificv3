@extends('layouts.midone')
@section('titlepage','Input Bad Stock')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Input Bad Stock</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/badstok/create">Input Bad Stock</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <!-- Data list view starts -->
        <!-- DataTable starts -->
        @include('layouts.notification')
        <div class="col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Input Bad Stock</h4>
                </div>
                <div class="card-body">
                    <form action="/badstok/store" method="post" id="frmBadstok">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <x-inputtext label="Tanggal" field="tanggal" datepicker icon="feather icon-calendar" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                                        <option value="">Pilih Cabang</option>
                                        <option value="GDG">Gudang</option>
                                        @foreach ($cabang as $d)
                                        <option value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Kode Produk</th>
                                            <th>Nama Produk</th>
                                            <th>Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($produk as $d)
                                        <tr>
                                            <td>{{ $d->kode_produk }}</td>
                                            <td>{{ $d->nama_barang }}</td>
                                            <td style="width:15%">
                                                <input type="hidden" name="kode_produk[]" value="{{ $d->kode_produk }}">
                                                <input type="text" class="form-control text-right" autocomplete="off" name="jumlah[]">
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-block"><i class="feather icon-send mr-1"></i> Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Detail Kendaraan -->

@endsection
@push('myscript')
<script>
    $(function() {
        $("#frmBadstok").submit(function() {
            var tanggal = $("#tanggal").val();
            var kode_cabang = $("#kode_cabang").val();
            if (tanggal == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tanggal").focus();
                });
                return false;
            } else if (kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cabang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_cabang").focus();
                });

                return false;
            }
        });
    });

</script>
@endpush
