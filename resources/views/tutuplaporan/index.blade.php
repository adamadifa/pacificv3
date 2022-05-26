@extends('layouts.midone')
@section('titlepage','Data Pengiriman LPC')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Tutup Laporan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/tutuplaporan">Data Tutup Laporan</a>
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

                <div class="card-header">
                    <a href="#" class="btn btn-primary" id="inputtutuplaporan"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>

                <div class="card-body">
                    <form action="#">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <select name="bulan" id="bulan" class="form-control">
                                        <option value="">Bulan</option>
                                        <?php
                                        if(empty(Request('bulan'))){
                                        $bl = date("m");
                                        }else{
                                            $bl = Request("bulan");
                                        }
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
                                        if(empty(Request('tahun'))){
                                            $tahun = date("Y");
                                        }else{
                                            $tahun = Request('tahun');
                                        }
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
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <button class="btn btn-block btn-primary"><i class="fa fa-search mr-1"></i>Cari</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Jenis Laporan</th>
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    <th>Tgl Penutupan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tutuplap as $d)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ ucwords(strtolower($d->jenis_laporan)) }}</td>
                                    <td>{{ $bln[$d->bulan] }}</td>
                                    <td>{{ $d->tahun }}</td>
                                    <td>{{ date("d-m-Y",strtotime($d->tgl_penutupan)) }}</td>
                                    <td>
                                        @if ($d->status==1)
                                        <a href="/tutuplaporan/{{ Crypt::encrypt($d->kode_tutuplaporan) }}/bukalaporan"><i class="feather icon-lock danger"></i></a>
                                        @else
                                        <a href="/tutuplaporan/{{ Crypt::encrypt($d->kode_tutuplaporan) }}/tutuplaporan"><i class="feather icon-unlock success"></i></a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
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
<div class="modal fade text-left" id="mdlinputtutuplaporan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input Tutup Laporan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputtutuplaporan"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {

        function loadinputtutuplaporan() {
            $.ajax({
                type: 'GET'
                , url: '/tutuplaporan/create'
                , cache: false
                , success: function(respond) {
                    $("#loadinputtutuplaporan").html(respond);
                }
            });
        }


        $("#inputtutuplaporan").click(function(e) {
            e.preventDefault();
            loadinputtutuplaporan();
            $('#mdlinputtutuplaporan').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });



    });

</script>
@endpush
