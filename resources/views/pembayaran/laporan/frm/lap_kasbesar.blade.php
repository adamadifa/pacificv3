@extends('layouts.midone')
@section('titlepage','Laporan Kas Besar')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Laporan Kas Besar</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/laporankasbesarpenjualan">Laporan Kas Besar</a>
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
                                <form action="/laporankasbesarpenjualan/cetak" method="POST" id="frmKasBesar" target="_blank">
                                    @csrf
                                    <input type="hidden" name="cabang" id="cabang" value="{{ Auth::user()->kode_cabang }}">
                                    <div class="row" id="pilihcabang">
                                        <div class="col-lg-12 col-sm-12">
                                            <div class="form-group  ">
                                                <select name="kode_cabang" id="kode_cabang" class="form-control">
                                                    @if (Auth::user()->kode_cabang!="PCF" && Auth::user()->kode_cabang!="PST")
                                                    <option value="">Pilih Cabang</option>
                                                    @else
                                                    <option value="">Semua Cabang</option>
                                                    @endif
                                                    @foreach ($cabang as $c)
                                                    <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="pilihsalesman">
                                        <div class="col-12">
                                            <div class="form-group  ">
                                                <select name="id_karyawan" id="id_karyawan" class="form-control">
                                                    <option value="">Semua Salesman</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="pilihpelanggan">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <select name="kode_pelanggan" id="kode_pelanggan" class="form-control select2">
                                                    <option value="">Semua Pelanggan</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="pilihjenisbayar">
                                        <div class="col-12">
                                            <div class="form-group  ">
                                                <select name="jenisbayar" id="jenisbayar" class="form-control">
                                                    <option value="">Semua Jenis Pembayaran</option>
                                                    <option value="tunai">Tunai</option>
                                                    <option value="titipan">Titipan</option>
                                                    <option value="transfer">Transfer</option>
                                                    <option value="giro">Giro</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="pilihjenislaporan">
                                        <div class="col-12">
                                            <div class="form-group  ">
                                                <select name="jenislaporan" id="jenislaporan" class="form-control jenislaporan">
                                                    <option value="">Pilih Jenis Laporan</option>
                                                    <option value="detail">Detail</option>
                                                    <option value="rekap">Rekap</option>
                                                    <option value="lhp">LHP</option>
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
                                                <button type="submit" name="submit" class="btn btn-primary btn-block"><i class="feather icon-send"></i> Submit</button>
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
                @include('layouts.nav_penjualan.navright')
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
        function disabledform() {
            var kode_cabang = $("#kode_cabang").val();
            //alert(kode_cabang);
            if (kode_cabang == "") {
                $("#id_karyawan").prop("disabled", true);
                $("#kode_pelanggan").prop("disabled", true);
                //$("#jenislaporan").prop("disabled", true);
                $('.jenislaporan option[value="rekap"]').attr('selected', 'selected');
            } else {
                $("#id_karyawan").prop("disabled", false);
                $("#kode_pelanggan").prop("disabled", false);
                $('#jenislaporan option[value=""]').attr('selected', 'selected');
                //$(".jenislaporan").prop("disabled", false);
            }
        }

        disabledform();
        $("#frmKasBesar").submit(function() {
            var cabang = $("#cabang").val();
            var kode_cabang = $("#kode_cabang").val();
            var jenislaporan = $("#jenislaporan").val();
            var dari = $("#dari").val();
            var sampai = $("#sampai").val();

            var start = new Date(dari);
            var end = new Date(sampai);

            var datestart = new Date('2018-09-01');
            if (cabang != "PCF" && kode_cabang == "" && cabang != "PST" && kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Pilih Cabang Terlebih Dahulu !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_cabang").focus();
                });
                return false;
            } else if (jenislaporan == "" && kode_cabang != "") {
                swal({
                    title: 'Oops'
                    , text: 'Jenis Laporan Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jenislaporan").focus();
                });
                return false;
            } else if (dari == "" && sampai == "") {
                swal({
                    title: 'Oops'
                    , text: 'Periode Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#dari").focus();
                });
                return false;
            } else if (start.getTime() > end.getTime()) {
                swal({
                    title: 'Oops'
                    , text: 'Periode tidak Valid !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#dari").focus();
                });
                return false;
            } else if (start.getTime() < datestart.getTime()) {
                swal({
                    title: 'Oops'
                    , text: 'Periode tidak Valid !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#dari").focus();
                });
                return false;
            } else {
                return true;
            }
        });

        function loadsalesmancabang(kode_cabang) {
            $.ajax({
                type: 'POST'
                , url: '/salesman/getsalescab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                }
                , cache: false
                , success: function(respond) {
                    $("#id_karyawan").html(respond);
                }
            });
        }

        function loadpelanggansalesman(kode_cabang, id_karyawan) {
            $.ajax({
                type: 'POST'
                , url: '/pelanggan/getpelanggansalesman'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , id_karyawan: id_karyawan
                }
                , cache: false
                , success: function(respond) {
                    $("#kode_pelanggan").html(respond);
                }
            });
        }

        $("#kode_cabang").change(function() {
            var kode_cabang = $(this).val();
            disabledform();
            loadsalesmancabang(kode_cabang);
            loadpelanggansalesman(kode_cabang, id_karyawan = "");
        });

        $("#id_karyawan").change(function() {
            var id_karyawan = $(this).val();
            loadpelanggansalesman(kode_cabang = "", id_karyawan);
        });

    });

</script>
@endpush
