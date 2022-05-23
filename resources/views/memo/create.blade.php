@extends('layouts.midone')
@section('titlepage', 'Tambah Data Salesman')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">E-Manual Regulation Center</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/memo">E-Manual Regulation Center</a></li>
                            <li class="breadcrumb-item"><a href="#">E-Manual Regulation Center</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <form class="form" action="/memo/store" method="POST" id="frmMemo">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">E-Manual Regulation Center</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext field="no_memo" label="No. Dokumen" icon="feather icon-credit-card" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext field="tanggal" label="Tanggal" icon="feather icon-calendar" datepicker />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext field="judul_memo" label="Judul" icon="feather icon-file" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <select name="kategori" id="kategori" class="form-control">
                                                    <option value="">Kategori</option>
                                                    <option value="SOP">SOP</option>
                                                    <option value="SK">SK</option>
                                                    <option value="BERITA ACARA">BERITA ACARA</option>
                                                    <option value="IM">IM</option>
                                                    <option value="JOB DESK">JOB DESK</option>
                                                    <option value="WORK INSTRUCTION">WORK INSTRUCTION</option>
                                                    <option value="LAINNYA">LAINNYA</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <select name="kode_dept" id="kode_dept" class="form-control">
                                                    <option value="ALL">All Departemen</option>
                                                    <option value="MKT">Marketing</option>
                                                    <option value="ACC">Accounting</option>
                                                    <option value="KEU">Keuangan</option>
                                                    <option value="PMB">Pembelian</option>
                                                    <option value="GAF">General Affair</option>
                                                    <option value="PRD">Produksi</option>
                                                    <option value="GDG">Gudang</option>
                                                    <option value="MTC">Maintenance</option>
                                                    <option value="HRD">HRD</option>
                                                    <option value="AUDIT">AUDIT</option>
                                                    <option value="PDQC">PDQC</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext field="link" label="link" icon="feather icon-external-link" />
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
@push('myscript')
<script>
    $(function() {
        $("#frmMemo").submit(function() {
            var no_memo = $("#no_memo").val();
            var tanggal = $("#tanggal").val();
            var judul_memo = $("#judul_memo").val();
            var kategori = $("#kategori").val();
            var kode_dept = $("#kode_dept").val();
            var link = $("#link").val();
            if (no_memo == "") {
                swal("Oops", "No. Dokumen Harus Diisi", "warning");
                return false;
            } else if (tanggal == "") {
                swal("Oops", "Tanggal Harus Diisi", "warning");
                return false;
            } else if (judul_memo == "") {
                swal("Oops", "Judul Harus Diisi", "warning");
                return false;
            } else if (kategori == "") {
                swal("Oops", "Kategori Harus Diisi", "warning");
                return false;
            } else if (link == "") {
                swal("Oops", "Link Harus Diisi", "warning");
                return false;
            }
        });
    });

</script>
@endpush
