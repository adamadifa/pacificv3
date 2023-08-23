@extends('layouts.midone')
@section('titlepage','Data Pengiriman LHP')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Kirim LHP</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/lpc">Data Laporan Kirim LHP</a>
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
        <div class="col-md-8 col-sm-12">
            <div class="card">
                @if (in_array($level,$kirimlpc_tambah))
                <div class="card-header">
                    <a href="#" class="btn btn-primary" id="inputlhp"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
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
                                    <th>Jam Kirim</th>
                                    <th>Status</th>
                                    <th>Resi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="loadlhp">

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
<div class="modal fade text-left" id="mdlinputlhp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input LHP</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputlhp"></div>
            </div>
        </div>
    </div>
</div>

<!-- Edit LPC -->
<div class="modal fade text-left" id="mdleditlhp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit LHP</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadeditlhp"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {



        function loadlhp() {
            var tahun = $("#tahun").val();
            var bulan = $("#bulan").val();
            $.ajax({
                type: 'POST'
                , url: '/lhp/kirimlhp/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , bulan: bulan
                    , tahun: tahun
                }
                , cache: false
                , success: function(respond) {
                    $("#loadlhp").html(respond);
                }
            });
        }

        function loadinputlhp() {
            $.ajax({
                type: 'GET'
                , url: '/lhp/kirimlhp/create'
                , cache: false
                , success: function(respond) {
                    $("#loadinputlhp").html(respond);
                }
            });
        }
        loadlhp();

        $("#bulan").change(function() {
            loadlhp();
        });

        $("#tahun").change(function() {
            loadlhp();
        });

        $("#inputlhp").click(function(e) {
            e.preventDefault();
            loadinputlhp();
            $('#mdlinputlhp').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });



    });

</script>
@endpush
