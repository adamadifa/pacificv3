@extends('layouts.midone')
@section('titlepage', 'Data Jaminan')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Data Jaminan</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('jaminan') }}">Data Jaminan</a>
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
                        <a href="#" class="btn btn-primary" id="inputjaminan"><i class="fa fa-plus mr-1"></i> Tambah
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
                                        <th>Jenis Jaminan</th>
                                        <th>Nama Pelanggan</th>
                                        <th>Total Piutang</th>
                                        <th>Nilai Jaminan</th>
                                        <th>Pengikat Jaminan</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="loadjaminan">

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
    <!-- Input jaminan -->
    <div class="modal fade text-left" id="mdlinputjaminan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Input Jaminan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadinputjaminan"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit jaminan -->
    <div class="modal fade text-left" id="mdleditjaminan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Edit Jaminan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadeditjaminan"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        $(function() {
            loadjaminan();

            function loadjaminan() {
                var kode_cabang = $("#kode_cabang").val();
                var bulan = $("#bulan").val();
                var tahun = $("#tahun").val();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('jaminan.show') }}',
                    data: {
                        _token: "{{ csrf_token() }}",
                        kode_cabang: kode_cabang,
                        bulan: bulan,
                        tahun: tahun,
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loadjaminan").html(respond);
                    }
                });
            }

            function loadinputjaminan() {
                $.ajax({
                    type: 'GET',
                    url: '/jaminan/create',
                    cache: false,
                    success: function(respond) {
                        $("#loadinputjaminan").html(respond);
                    }
                });
            }

            $("#kode_cabang,#bulan,#tahun").change(function() {
                loadjaminan();
            });

            $("#inputjaminan").click(function(e) {
                e.preventDefault();
                loadinputjaminan();
                $('#mdlinputjaminan').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            });



        });
    </script>
@endpush
