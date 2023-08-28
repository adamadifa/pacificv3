@extends('layouts.midone')
@section('titlepage', 'Data Visit Pelanggan')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Data Visit Pelanggan</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('visit') }}">Data Visit Pelanggan</a>
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
            <div class="col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <a href="#" class="btn btn-primary" id="inputvisit"><i class="fa fa-plus mr-1"></i> Tambah
                            Data</a>
                    </div>
                    <div class="card-body">
                        <form action="#">
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <select name="kode_cabang" id="kode_cabang" class="form-control">
                                            <option value="">Cabang</option>
                                            @foreach ($cabang as $d)
                                                <option value="{{ $d->kode_cabang }}">
                                                    {{ $d->nama_cabang }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <select name="bulan" id="bulan" class="form-control">
                                            <option value="">Bulan</option>
                                            <?php
                                            $bl = date("m");
                                            for ($i = 1; $i < count($bln); $i++) {
                                            ?>
                                            <option <?php if ($bl == $i) {
                                                echo 'selected';
                                            } ?> value="<?php echo $i; ?>"><?php echo $bln[$i]; ?>
                                            </option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-group">
                                        <select name="tahun" id="tahun" class="form-control">
                                            <option value="">Tahun</option>
                                            <?php
                                            $tahun = date("Y");
                                            $tahunmulai = 2021;
                                            for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                                            ?>
                                            <option <?php if ($tahun == $thn) {
                                                echo 'selected';
                                            } ?> value="<?php echo $thn; ?>"><?php echo $thn; ?>
                                            </option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-hover-animation">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Cabang</th>
                                        <th>Tgl Visit</th>
                                        <th>Nama Pelanggan</th>
                                        <th>Sales</th>
                                        <th>Pasar</th>
                                        <th>Tgl Transaksi</th>
                                        <th>No Faktur</th>
                                        <th>Nominal</th>
                                        <th>Jenis Transaksi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="loadvisit">

                                </tbody>
                            </table>
                        </div>

                        <!-- DataTable ends -->
                    </div>
                </div>
            </div>
            <!-- Data list view end -->
        </div>
    </div>
    <!-- Input visit -->
    <div class="modal fade text-left" id="mdlinputvisit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Input Visit</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadinputvisit"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit visit -->
    <div class="modal fade text-left" id="mdleditvisit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Edit visit</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadeditvisit"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        $(function() {
            loadvisit();

            function loadvisit() {
                var kode_cabang = $("#kode_cabang").val();
                var bulan = $("#bulan").val();
                var tahun = $("#tahun").val();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('visit.show') }}',
                    data: {
                        _token: "{{ csrf_token() }}",
                        kode_cabang: kode_cabang,
                        bulan: bulan,
                        tahun: tahun,
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loadvisit").html(respond);
                    }
                });
            }

            function loadinputvisit() {
                $.ajax({
                    type: 'GET',
                    url: '/visit/create',
                    cache: false,
                    success: function(respond) {
                        $("#loadinputvisit").html(respond);
                    }
                });
            }

            $("#kode_cabang,#bulan,#tahun").change(function() {
                loadvisit();
            });

            $("#inputvisit").click(function(e) {
                e.preventDefault();
                loadinputvisit();
                $('#mdlinputvisit').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            });



        });
    </script>
@endpush
