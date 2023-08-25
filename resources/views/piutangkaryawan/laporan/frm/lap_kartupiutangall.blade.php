@extends('layouts.midone')
@section('titlepage','Laporan Kartu Piutang Karyawan All')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Laporan Kartu Piutang Karyawan All</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/laporankeuangan/kartupiutangkaryawan">Laporan Kartu Piutang Karyawan All</a>
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
                                <form action="/laporankeuangan/piutangkaryawan/cetakkartupiutangkaryawanall" method="POST" id="frmLaporan" target="_blank">
                                    @csrf

                                    @php
                                    $level_search = ["admin","manager hrd","manager accounting","direktur"];
                                    @endphp
                                    @if (in_array($level,$level_search))
                                    <div class="row" id="pilihcabang">
                                        <div class="col-lg-12 col-sm-12">
                                            <div class="form-group  ">
                                                <select name="id_kantor" id="id_kantor" class="form-control">
                                                    <option value="">Semua Kantor</option>
                                                    @foreach ($cabang as $c)
                                                    <option value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="pilihcabang">
                                        <div class="col-lg-12 col-sm-12">
                                            <div class="form-group  ">
                                                <select name="kode_dept" id="kode_dept" class="form-control">
                                                    <option value="">Semua Departemen</option>
                                                    @foreach ($departemen as $d)
                                                    <option value="{{ $d->kode_dept }}">{{ strtoupper($d->nama_dept) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

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

        $("#frmLaporan").submit(function() {
            var dari = $("#dari").val();
            var sampai = $("#sampai").val();
            if (dari == "" || sampai == "") {
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
