@extends('layouts.midone')
@section('titlepage','Input Pembelian')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Input Pembelian</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/pembelian/create">Input Pembelian</a>
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
            <div class="col-md-4 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext field="nobukti_pembelian" label="No. Bukti Pembelian" icon="feather icon-credit-card" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext field="tgl_pembelian" label="Tanggal Pembelian" icon="feather icon-calendar" datepicker />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext field="kode_supplier" label="Supplier" icon="feather icon-user" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <select name="kode_dept" id="kode_dept" class="form-control">
                                                <option value="">Pilih Departemen</option>
                                                @foreach ($departemen as $d)
                                                <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <select name="ppn" id="ppn" class="form-control">
                                                <option value="">Jenis Transaksi</option>
                                                <option value="tunai">Tunai</option>
                                                <option value="kredit">Kredit</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext field="tgl_jatuhtempo" label="Tanggal Jatuh Tempo" icon="feather icon-calendar" datepicker />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
            <div class="col-8">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-0">
                        <div class="avatar bg-rgba-danger m-2" style="padding:3rem ">
                            <div class="avatar-content">
                                <i class="feather icon-shopping-cart text-danger" style="font-size: 4rem"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-bold-700" style="font-size: 6rem; padding:2rem">0,00</h2>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Detail Pembelian</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-sm-12">
                                <x-inputtext field="nama_barang" label="Nama Barang" icon="feather icon-box" />
                            </div>
                            <div class="col-lg-1 col-sm-12">
                                <x-inputtext field="jumlah" label="Qty" icon="feather icon-box" />
                            </div>
                            <div class="col-lg-2 col-sm-12">
                                <x-inputtext field="harga" label="Harga" icon="feather icon-box" right />
                            </div>
                            <div class="col-lg-2 col-sm-12">
                                <x-inputtext field="peny_harga" label="Penyesuaian Harga" icon="feather icon-box" right />
                            </div>
                            <div class="col-lg-2 col-sm-12">
                                <div class="form-group">
                                    <select name="kode_akun" id="kode_akun" class="form-control select2">
                                        <option value="">Kode Akun</option>
                                        @foreach ($coa as $d)
                                        <option value="{{ $d->kode_akun }}"><b>{{ $d->kode_akun }}</b> - {{ $d->nama_akun }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-1 col-sm-12">
                                <div class="form-group">
                                    <button class="btn btn-primary"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-sm-12">
                                <x-inputtext field="keterangan" label="Keterangan" icon="feather icon-file" />
                            </div>
                            <div class="col-1">
                                <div class="vs-checkbox-con vs-checkbox-primary">
                                    <input type="checkbox" class="cabangcheck" name="cabangcheck" value="1">
                                    <span class="vs-checkbox">
                                        <span class="vs-checkbox--check">
                                            <i class="vs-icon feather icon-check"></i>
                                        </span>
                                    </span>
                                    <span class="">Cabang ?</span>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-12" id="pilihcabang">
                                <div class="form-group">
                                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                                        <option value="">Cabang</option>
                                        @foreach ($cabang as $d)
                                        <option value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table class="table">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Barang</th>
                                            <th>Nama Barang</th>
                                            <th>Keterangan</th>
                                            <th>Qty</th>
                                            <th>Harga</th>
                                            <th>Subtotal</th>
                                            <th>Penyesuaian</th>
                                            <th>Total</th>
                                            <th>Kode Akun</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-1">
                                <div class="vs-checkbox-con vs-checkbox-primary">
                                    <input type="checkbox" class="ppn" name="ppn" value="1">
                                    <span class="vs-checkbox">
                                        <span class="vs-checkbox--check">
                                            <i class="vs-icon feather icon-check"></i>
                                        </span>
                                    </span>
                                    <span class="">PPN ?</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-12">
                                <button class="btn btn-primary btn-block"><i class="fa fa-send mr-1"></i>Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <!-- Data list view end -->
</div>
</div>

@endsection
@push('myscript')
<script>
    $(function() {
        $('.cabangcheck').change(function() {
            if (this.checked) {
                $("#pilihcabang").show();
            } else {
                $("#pilihcabang").hide();
            }
        });

        function hidecabang() {
            $("#pilihcabang").hide();
        }

        hidecabang();
    });

</script>
@endpush
