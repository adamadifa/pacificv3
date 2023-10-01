@extends('layouts.midone')
@section('titlepage','Data Pelanggan')
@section('content')
<style>
    .card {
        margin-bottom: 1rem !important;
    }

</style>

<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h4 class="content-header-title float-left mb-0">Ajukan Jumlah Faktur</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <div class="row mt-1">
            <div class="col-12">
                @include('layouts.notification')
                <div class="row">
                    <div class="col-12">
                        <style>
                            .form-label {
                                font-weight: bold;
                                margin-top: 10px;
                            }

                            .form-group {
                                margin-bottom: 5px !important;
                            }

                            .form-label-group {
                                margin-bottom: 5px !important;
                            }

                        </style>

                        <form action="/ajuanfaktur/{{ Crypt::encrypt($pelanggan->kode_pelanggan) }}/store" method="POST" id="frmAjuanfaktur">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <table class="table">
                                        <tr>
                                            <th style="width: 40%">Kode Pelanggan</th>
                                            <td>{{ $pelanggan->kode_pelanggan }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Pelanggan</th>
                                            <td>{{ $pelanggan->nama_pelanggan }}</td>
                                        </tr>
                                        <tr>
                                            <th>Alamat</th>
                                            <td>{{ $pelanggan->alamat_pelanggan }}</td>
                                        </tr>
                                        <tr>
                                            <th>Salesman</th>
                                            <td>{{ $pelanggan->nama_karyawan }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="Tanggal Pengajuan" field="tgl_pengajuan" icon="feather icon-calendar" datepicker />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="Max Jumlah Faktur" field="jmlfaktur" icon="feather icon-file-text" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <textarea name="keterangan" id="keterangan" class="form-control" cols="30" rows="5" placeholder="Keterangan"></textarea>
                                    </div>
                                </div>
                            </div>
                            @if ($pelanggan->kategori_salesman=="TO" || $pelanggan->kategori_salesman=="TOCANVASER")
                            <div class="row mb-1">
                                <div class="col-12">
                                    <div class="vs-checkbox-con vs-checkbox-primary">

                                        <input type="checkbox" class="sikluspembayaran" name="sikluspembayaran" value="1">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">Pembayaran Saat Turun Barang Order Selanjutnya</span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary w-100" type="submit"><i class="feather icon-send mr-1"></i>Submit</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('myscript')
<script>
    $(function() {
        $("#frmAjuanfaktur").submit(function() {
            var tgl_pengajuan = $("#tgl_pengajuan").val();
            var jmlfaktur = $("#jmlfaktur").val();
            if (tgl_pengajuan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Pengajuan Harus Diisi!'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_pengajuan").focus();
                });
                return false;
            } else if (jmlfaktur == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Faktur Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jmlfaktur").focus();
                });
                return false;
            }
        });
    });

</script>
@endpush
