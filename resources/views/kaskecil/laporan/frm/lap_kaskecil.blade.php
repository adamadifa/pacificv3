@extends('layouts.midone')
@section('titlepage','Laporan Kas Kecil')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Laporan Kas Kecil</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/laporankeuangan/kaskecil">Laporan Kas Kecil</a>
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
                                <form action="/laporankeuangan/kaskecil/cetak" method="POST" id="frmKaskecil" target="_blank">
                                    @csrf
                                    <div class="row" id="pilihcabang">
                                        <div class="col-lg-12 col-sm-12">
                                            <div class="form-group  ">
                                                <select name="kode_cabang" id="kode_cabang" class="form-control">
                                                    @if ($getcbg!="PCF")
                                                    <option value="">Pilih Cabang</option>
                                                    @else
                                                    <option value="">Semua Cabang</option>
                                                    @endif
                                                    @foreach ($cabang as $c)
                                                    <option value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <select name="dari_kode_akun" id="dari_kode_akun" class="form-control select2">
                                                    <option value="">Semua Akun</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="form-group">
                                                <select name="sampai_kode_akun" id="sampai_kode_akun" class="form-control select2">
                                                    <option value="">Semua Akun</option>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
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
                                                <button type="submit" name="submit" class="btn btn-primary btn-block"><i class="feather icon-printer"></i> Cetak</button>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <button type="submit" name="export" class="btn btn-success btn-block"><i class="feather icon-download"></i> Export</button>
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
        function loadAkun() {
            var kode_cabang = $("#kode_cabang").val();
            $.ajax({
                type: 'POST'
                , url: '/coa/getcoacabang'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                }
                , cache: false
                , success: function(respond) {
                    $("#dari_kode_akun").html(respond);
                    $("#sampai_kode_akun").html(respond);
                }
            });
        }
        loadAkun();

        $("#kode_cabang").change(function() {
            // alert('test');
            loadAkun();
        });

        $("#frmKaskecil").submit(function() {
            var kode_cabang = $("#kode_cabang").val();
            var dari_kode_akun = $("#dari_kode_akun").val();
            var sampai_kode_akun = $("#sampai_kode_akun").val();
            var dari = $("#dari").val();
            var sampai = $("#sampai").val();
            var cabang = "{{ Auth::user()->kode_cabang }}";
            if (cabang !== "PCF" && kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cabang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#sampai_kode_akun").focus();
                });
                return false;
            } else if (dari_kode_akun != "" && sampai_kode_akun == "" || dari_kode_akun == "" && sampai_kode_akun != "") {
                swal({
                    title: 'Oops'
                    , text: 'Range Akun Harus Lengkap !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#sampai_kode_akun").focus();
                });
                return false;
            } else if (dari == "" || sampai == "") {
                swal({
                    title: 'Oops'
                    , text: 'Periode Laporan Harus lengkap !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#dari").focus();
                });
                return false;
            }
        });
    });

</script>
@endpush
