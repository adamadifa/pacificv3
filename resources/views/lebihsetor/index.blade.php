@extends('layouts.midone')
@section('titlepage','Data Lebih Setor')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Lebih Setor</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/lebihsetor">Data Lebih Setor</a>
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
        <div class="col-md-6 col-sm-8">
            <div class="card">

                <div class="card-header">
                    <a href="#" id="inputlebihsetor" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>

                <div class="card-body">
                    <form action="/lebihsetor">
                        <div class="row">
                            <div class="col-lg-3 col-sm-12">
                                <div class="form-group">
                                    <select class="form-control" id="bulan" name="bulan">
                                        <option value="">Bulan</option>
                                        <?php
                                        $bulanini = date("m");
                                        for ($i = 1; $i < count($bulan); $i++) {
                                        ?>
                                        <option {{ Request('bulan') == $i ? 'selected' : '' }} value="<?php echo $i; ?>"><?php echo $bulan[$i]; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <select class="form-control" id="tahun" name="tahun">
                                        <option value="">Tahun</option>
                                        <?php
                                        $tahunmulai = 2020;
                                        for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                                        ?>
                                        <option {{ Request('tahun') == $thn ? 'selected' : '' }} value="<?php echo $thn; ?>"><?php echo $thn; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-12">
                                <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i></button>
                            </div>
                        </div>

                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Cabang</th>
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lebihsetor as $d)
                                <tr>
                                    <td>{{ $d->kode_cabang }}</td>
                                    <td>{{ $bulan[$d->bulan] }}</td>
                                    <td>{{ $d->tahun }}</td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a href="#" class="info detail" kode_ls="{{ $d->kode_ls }}"><i class="feather icon-file-text"></i></a>
                                            <form method="POST" class="deleteform" action="/lebihsetor/{{Crypt::encrypt($d->kode_ls)}}/delete">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" class="delete-confirm ml-1">
                                                    <i class="feather icon-trash danger"></i>
                                                </a>
                                            </form>
                                        </div>
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
<!-- Input uang Belum Setor -->
<div class="modal fade text-left" id="mdlinputlebihsetor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input Uang Lebih Disetorkan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputlebihsetor"></div>
            </div>
        </div>
    </div>
</div>
<!-- Detail Belum Setor -->
<div class="modal fade text-left" id="mdldetaillebihsetor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail Lebih Setor</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaddetaillebihsetor"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        $(".detail").click(function() {
            var kode_ls = $(this).attr("kode_ls");
            $("#loaddetaillebihsetor").load('/lebihsetor/' + kode_ls + '/show');
            $('#mdldetaillebihsetor').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        $('.delete-confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            event.preventDefault();
            swal({
                    title: `Are you sure you want to delete this record?`
                    , text: "If you delete this, it will be gone forever."
                    , icon: "warning"
                    , buttons: true
                    , dangerMode: true
                , })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
        });

        $("#inputlebihsetor").click(function(e) {
            e.preventDefault();
            $("#loadinputlebihsetor").load('/lebihsetor/create');
            $('#mdlinputlebihsetor').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });



    });

</script>
@endpush
