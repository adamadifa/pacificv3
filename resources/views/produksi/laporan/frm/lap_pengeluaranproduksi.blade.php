@extends('layouts.midone')
@section('titlepage', 'Laporan Pengeluaran Barang Produksi')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Laporan Pengeluaran Barang Produksi</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/laporanproduksi/pemasukanproduksi">Laporan Pengeluaran Barang Produksi</a>
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
            <div class="row">
                <div class="col-lg-9 col-sm-12">
                    <div class="row">
                        <div class="col-lg-7 col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <form action="/laporanproduksi/pengeluaranproduksi/cetak" method="POST" id="frmMutasiproduksi" target="_blank">
                                        @csrf
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <select name="kode_barang" id="kode_barang" class="form-control">
                                                        <option value="">Semua Jenis Pengeluaran</option>
                                                        <option value="Pemakaian">Pemakaian</option>
                                                        <option value="Retur Out">Retur Out</option>
                                                        <option value="Lainnya">Lainnya</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12 col-sm-12">
                                                <div class="form-group">
                                                    <select name="kode_barang" id="kode_barang" class="form-control select2">
                                                        <option value="">Semua Barang</option>
                                                        @foreach ($barang as $d)
                                                            <option value="{{ $d->kode_barang }}">{{ $d->nama_barang }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" id="pilihperiode">
                                            <div class="col-6">
                                                <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker />
                                            </div>
                                            <div class="col-6">
                                                <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-8 col-sm-12">
                                                <div class="form-group">
                                                    <button type="submit" name="submit" class="btn btn-primary btn-block"><i
                                                            class="feather icon-printer"></i> Cetak</button>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-sm-12">
                                                <div class="form-group">
                                                    <button type="submit" name="export" class="btn btn-success btn-block"><i
                                                            class="feather icon-download"></i> Export</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- DataTable ends -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-12">
                    @include('layouts.nav_laporanproduksi')
                </div>

                <div class="col-lg-8 col-sm-12">


                </div>
            </div>
            <!-- Data list view end -->
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        $(function() {
            $("#frmMutasiproduksi").submit(function() {
                var dari = $("#dari").val();
                var sampai = $("#sampai").val();
                var start = new Date(dari);
                var end = new Date(sampai);
                if (dari == "" || sampai == "") {
                    swal({
                        title: 'Oops',
                        text: 'Periode Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#dari").focus();
                    });
                    return false;
                } else if (start.getTime() > end.getTime()) {
                    swal({
                        title: 'Oops',
                        text: 'Periode tidak Valid !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#dari").focus();
                    });
                    return false;
                } else if (start.getTime() < datestart.getTime()) {
                    swal({
                        title: 'Oops',
                        text: 'Periode tidak Valid !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#dari").focus();
                    });
                    return false;
                } else {
                    return true;
                }
            });
        });
    </script>
@endpush
