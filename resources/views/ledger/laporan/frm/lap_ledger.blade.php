@extends('layouts.midone')
@section('titlepage', 'Laporan Ledger')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Laporan Ledger</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/laporankeuangan/ledger">Laporan Ledger</a>
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
                                    <form action="/laporankeuangan/ledger/cetak" method="POST" id="frmLedger"
                                        target="_blank">
                                        @csrf
                                        <div class="row" id="pilihbank">
                                            <div class="col-lg-12 col-sm-12">
                                                <div class="form-group  ">
                                                    <select name="kode_bank" id="kode_bank" class="form-control select2">
                                                        @if ($getcbg != 'PCF')
                                                            <option value="-">Pilih Bank</option>
                                                        @else
                                                            <option value="">Semua Ledger</option>
                                                        @endif

                                                        @foreach ($bank as $d)
                                                            <option value="{{ $d->kode_bank }}">
                                                                {{ strtoupper($d->nama_bank) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" id="pilihjenislaporan">
                                            <div class="col-lg-12 col-sm-12">
                                                <div class="form-group">
                                                    <select name="jenislaporan" id="jenislaporan" class="form-control">
                                                        <option value="">Jenis Laporan</option>
                                                        <option value="detail">Detail</option>
                                                        <option value="rekap">Rekap</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" id="pilihakun">
                                            <div class="col-lg-6 col-sm-12">
                                                <div class="form-group">
                                                    <select name="dari_kode_akun" id="dari_kode_akun"
                                                        class="form-control select2">
                                                        <option value="">Semua Akun</option>
                                                        @foreach ($coa as $d)
                                                            <option value="{{ $d->kode_akun }}">
                                                                {{ $d->kode_akun . ' - ' . $d->nama_akun }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-sm-12">
                                                <div class="form-group">
                                                    <select name="sampai_kode_akun" id="sampai_kode_akun"
                                                        class="form-control select2">
                                                        <option value="">Semua Akun</option>
                                                        @foreach ($coa as $d)
                                                            <option value="{{ $d->kode_akun }}">
                                                                {{ $d->kode_akun . ' - ' . $d->nama_akun }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" id="pilihperiode">
                                            <div class="col-6">
                                                <x-inputtext label="Dari" field="dari" icon="feather icon-calendar"
                                                    datepicker />
                                            </div>
                                            <div class="col-6">
                                                <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar"
                                                    datepicker />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-8 col-sm-12">
                                                <div class="form-group">
                                                    <button type="submit" name="submit"
                                                        class="btn btn-primary btn-block"><i
                                                            class="feather icon-printer"></i> Cetak</button>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-sm-12">
                                                <div class="form-group">
                                                    <button type="submit" name="export"
                                                        class="btn btn-success btn-block"><i
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
                    @include('layouts.nav_laporankeuangan')
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
            function loadjenislaporan() {
                var kode_bank = $("#kode_bank").val();
                if (kode_bank == "") {
                    //$("#pilihjenislaporan").hide();
                    $("#jenislaporan").val("rekap").change();
                } else {
                    $("#pilihjenislaporan").show();
                    $("#jenislaporan").val("").change();
                }

            }

            function loadpilihakun() {
                var jenislaporan = $("#jenislaporan").val();
                if (jenislaporan == "rekap") {
                    $("#pilihakun").hide();
                } else {
                    $("#pilihakun").show();
                }
            }

            loadjenislaporan();
            loadpilihakun();

            $("#kode_bank").change(function() {
                loadjenislaporan();
                loadpilihakun();
            });

            $("#jenislaporan").change(function() {
                loadpilihakun();
            });
            $("#frmLedger").submit(function() {
                var kode_bank = $("#kode_bank").val();
                var jenislaporan = $("#jenislaporan").val();
                var dari_kode_akun = $("#dari_kode_akun").val();
                var sampai_kode_akun = $("#sampai_kode_akun").val();
                var dari = $("#dari").val();
                var sampai = $("#sampai").val();
                var cabang = "{{ Auth::user()->kode_cabang }}";
                //alert(kode_bank);
                if (cabang !== "PCF" && kode_bank == "-") {
                    swal({
                        title: 'Oops',
                        text: 'Bank Harus Diisi !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#sampai_kode_akun").focus();
                    });
                    return false;
                } else if (jenislaporan == "") {
                    swal({
                        title: 'Oops',
                        text: 'Pilih Jenis Laporan Terlebih Dahulu !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#jenislaporan").focus();
                    });
                    return false;
                } else if (dari_kode_akun != "" && sampai_kode_akun == "" || dari_kode_akun == "" &&
                    sampai_kode_akun != "") {
                    swal({
                        title: 'Oops',
                        text: 'Range Akun Harus Lengkap !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#sampai_kode_akun").focus();
                    });
                    return false;
                } else if (dari == "" || sampai == "") {
                    swal({
                        title: 'Oops',
                        text: 'Periode Laporan Harus lengkap !',
                        icon: 'warning',
                        showConfirmButton: false
                    }).then(function() {
                        $("#dari").focus();
                    });
                    return false;
                }
            });
        });
    </script>
@endpush
