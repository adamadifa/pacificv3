@extends('layouts.midone')
@section('titlepage','Laporan Presensi')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Laporan Presensi</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/laporanhrd/presensi">Laporan Presensi</a>
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
                                <form action="/laporanhrd/presensi/cetak" method="POST" id="frmPresensi" target="_blank">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <select name="kode_dept" id="kode_dept" class="form-control">
                                                    <option value="">Semua Departemen</option>
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
                                                <select name="id_kantor" id="id_kantor" class="form-control">
                                                    <option value="">Semua Cabang</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <select name="id_group" id="id_group" class="form-control">
                                                    <option value="">Semua Grup</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="pilihbulan">
                                        <div class="col-12">
                                            {{-- <label for="" class="form-label mb-1">Omset Bulan</label> --}}
                                            <div class="form-group">
                                                <select class="form-control" id="bulan" name="bulan">
                                                    <option value="">Bulan</option>
                                                    <?php
                                                    $bulanini = date("m");
                                                    for ($i = 1; $i < count($bulan); $i++) {
                                                    ?>
                                                    <option <?php if ($bulanini == $i) {echo "selected";} ?> value="<?php echo $i; ?>"><?php echo $bulan[$i]; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="pilihtahun">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <select class="form-control" id="tahun" name="tahun">
                                                    <?php
                                                    $tahunmulai = 2020;
                                                    for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                                                    ?>
                                                    <option <?php if (date('Y') == $thn) { echo "Selected";} ?> value="<?php echo $thn; ?>"><?php echo $thn; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
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


        function loadkantor() {
            var kode_dept = $("#kode_dept").val();
            $.ajax({
                type: 'POST'
                , url: '/laporanhrd/getkantor'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_dept: kode_dept
                }
                , cache: false
                , success: function(respond) {
                    $("#id_kantor").html(respond);
                }
            });
        }


        function loadgroup() {
            var kode_dept = $("#kode_dept").val();
            $.ajax({
                type: 'POST'
                , url: '/laporanhrd/getgroup'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_dept: kode_dept
                }
                , cache: false
                , success: function(respond) {
                    $("#id_group").html(respond);
                }
            });
        }
        $("#kode_dept").change(function(e) {
            loadkantor();
            loadgroup();
        });
    });

</script>
@endpush