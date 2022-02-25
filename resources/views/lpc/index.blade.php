@extends('layouts.midone')
@section('titlepage','Data Pengiriman LPC')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Kirim LPC</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/lpc">Data Laporan Kirim LPC</a>
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
        <div class="col-md-6 col-sm-12">
            <div class="card">
                @if (in_array($level,$kirimlpc_tambah))
                <div class="card-header">
                    <a href="#" class="btn btn-primary" id="inputlpc"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                @endif
                <div class="card-body">
                    <form action="#">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <select name="bulan" id="bulan" class="form-control">
                                        <option value="">Bulan</option>
                                        <?php
                                        $bl = date("m");
                                        for ($i = 1; $i < count($bln); $i++) {
                                        ?>
                                        <option <?php if ($bl == $i) {
                                                    echo "selected";
                                                } ?> value="<?php echo $i; ?>"><?php echo $bln[$i]; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <select name="tahun" id="tahun" class="form-control">
                                        <option value="">Tahun</option>
                                        <?php
                                        $tahun = date("Y");
                                        $tahunmulai = 2021;
                                        for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                                        ?>
                                        <option <?php if ($tahun == $thn) {
                                                    echo "selected";
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
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    <th>Tgl Kirim</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="loadlpc">

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
<!-- Input LPC -->
<div class="modal fade text-left" id="mdlinputlpc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input LPC</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputlpc"></div>
            </div>
        </div>
    </div>
</div>

<!-- Edit LPC -->
<div class="modal fade text-left" id="mdleditlpc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit LPC</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadeditlpc"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        function loadlpc() {
            var tahun = $("#tahun").val();
            var bulan = $("#bulan").val();
            $.ajax({
                type: 'POST'
                , url: '/lpc/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , bulan: bulan
                    , tahun: tahun
                }
                , cache: false
                , success: function(respond) {
                    $("#loadlpc").html(respond);
                }
            });
        }

        function loadinputlpc() {
            $.ajax({
                type: 'GET'
                , url: '/lpc/create'
                , cache: false
                , success: function(respond) {
                    $("#loadinputlpc").html(respond);
                }
            });
        }
        loadlpc();

        $("#bulan").change(function() {
            loadlpc();
        });

        $("#tahun").change(function() {
            loadlpc();
        });

        $("#inputlpc").click(function(e) {
            e.preventDefault();
            loadinputlpc();
            $('#mdlinputlpc').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });



    });

</script>
@endpush
