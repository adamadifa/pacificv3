@extends('layouts.midone')
@section('titlepage', 'Laporan Gaji')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Laporan Gaji</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/laporanhrd/gaji">Laporan Gaji</a>
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
                                    <form action="/laporanhrd/gaji/cetak" method="POST" id="frmPresensi" target="_blank">
                                        @csrf

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <select name="id_kantor" id="id_kantor" class="form-control">
                                                        <option value="">Semua Kantor</option>
                                                        @foreach ($cabang as $c)
                                                            <option
                                                                {{ Request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}
                                                                value="{{ $c->kode_cabang }}">
                                                                {{ strtoupper($c->kode_cabang == 'PST' ? 'PUSAT' : $c->nama_cabang) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <select name="kode_dept" id="kode_dept" class="form-control">
                                                        <option value="">Semua Departemen</option>
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
                                                    <select class="form-control" id="tahun" name="tahun">
                                                        <?php
                                                    $tahunmulai = 2020;
                                                    for ($thn = $tahunmulai; $thn <= date('Y') +1; $thn++) {
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
                                        @if (Auth::user()->level == 'manager accounting' || Auth::user()->level == 'admin')
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <select name="manajemen" id="manajemen" class="form-control">
                                                            <option value="">Semua</option>
                                                            <option value="1">Manajemen</option>
                                                            <option value="2">Non Manajemen</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <select name="jenis_laporan_gaji" id="jenis_laporan_gaji"
                                                        class="form-control">
                                                        <option value="1">Detail</option>
                                                        <option value="2">Rekap</option>
                                                        <option value="3">Slip</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-8 col-sm-12">
                                                <div class="form-group">
                                                    <button type="submit" name="submit"
                                                        class="btn btn-primary btn-block"><i class="feather icon-send"></i>
                                                        Submit</button>
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
                    @include('layouts.nav_hrd')
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


            // function loadkantor() {
            //     var kode_dept = $("#kode_dept").val();
            //     $.ajax({
            //         type: 'POST'
            //         , url: '/laporanhrd/getkantor'
            //         , data: {
            //             _token: "{{ csrf_token() }}"
            //             , kode_dept: kode_dept
            //         }
            //         , cache: false
            //         , success: function(respond) {
            //             $("#id_kantor").html(respond);
            //         }
            //     });
            // }

            function loaddepartemen() {
                var id_kantor = $("#id_kantor").val();
                $.ajax({
                    type: 'POST',
                    url: '/laporanhrd/getdepartemen',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id_kantor: id_kantor
                    },
                    cache: false,
                    success: function(respond) {
                        $("#kode_dept").html(respond);
                    }
                });
            }


            function loadgroup() {
                var id_kantor = $("#id_kantor").val();
                $.ajax({
                    type: 'POST',
                    url: '/laporanhrd/getgroup',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id_kantor: id_kantor
                    },
                    cache: false,
                    success: function(respond) {
                        $("#id_group").html(respond);
                    }
                });
            }
            $("#id_kantor").change(function(e) {
                loaddepartemen();
                loadgroup();
            });
        });
    </script>
@endpush
