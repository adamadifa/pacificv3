@extends('layouts.midone')
@section('titlepage', 'Laporan BBM')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Laporan BBM</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/bbm/laporanBBM">Laporan BBM</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            @include('layouts.notification')
            <div class="row">
                <div class="col-lg-9 col-sm-12">
                    <div class="row">
                        <div class="col-lg-7 col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <form action="/bbm/cetakBBM" method="POST" id="frmLaporan" target="_blank">
                                        @csrf
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <select name="kode_cabang" id="kode_cabang" class="form-control"
                                                        required>
                                                        <option value="">Pilih Cabang</option>
                                                        @foreach ($cabang as $d)
                                                            <option value="{{ $d->kode_cabang }}">
                                                                {{ $d->nama_cabang }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    @php
                                                        $kode_cabang = Auth::user()->kode_cabang;
                                                        $kendaraan = DB::select("SELECT * FROM kendaraan WHERE kode_cabang = 'TSM' ORDER BY no_polisi ASC");
                                                    @endphp
                                                    <select class="form-control" name="no_polisi" id="no_polisi" required>
                                                        <option value="">Pilih Kendaraan</option>
                                                        @foreach ($kendaraan as $k)
                                                            <option value="{{ $k->no_polisi }}">{{ $k->no_polisi }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" id="pilihbulan">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <select class="form-control" id="bulan" name="bulan" required>
                                                        <option value="">Bulan</option>
                                                        <?php
                                                    $bulanini = date("m");
                                                    for ($i = 1; $i < count($bulan); $i++) {
                                                    ?>
                                                        <option <?php if ($bulanini == $i) {
                                                            echo 'selected';
                                                        } ?> value="<?php echo $i; ?>">
                                                            <?php echo $bulan[$i]; ?></option>
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
                                                    <select class="form-control" id="tahun" name="tahun" required>
                                                        <?php
                                                    $tahunmulai = 2020;
                                                    for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                                                    ?>
                                                        <option <?php if (date('Y') == $thn) {
                                                            echo 'Selected';
                                                        } ?> value="<?php echo $thn; ?>">
                                                            <?php echo $thn; ?></option>
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
                                </div>
                            </div>
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
            $("#frmLaporan").submit(function() {
                var no_polisi = $("#no_polisi").val();
                var kode_cabang = $("#kode_cabang").val();
                var bulan = $("#bulan").val();
                var tahun = $("#tahun").val();
                if (kode_cabang == "") {
                    swal("Oops", "Cabang Harus Dipilih !", "warning");
                    return false;
                } else if (no_polisi == "") {
                    swal("Oops", "Pilih Kendaraan Harus Dipilih !", "warning");
                    return false;
                } else if (bulan == "") {
                    swal("Oops", "Bulan Harus Dipilih !", "warning");
                    return false;
                } else if (tahun == "") {
                    swal("Oops", "Bulan Harus Dipilih !", "warning");
                    return false;
                }
            });

        });
    </script>
@endpush
