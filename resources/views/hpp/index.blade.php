@extends('layouts.midone')
@section('titlepage','Data HPP')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Input HPP</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/inputhpp">Input HPP</a>
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
        <div class="col-md-12 col-sm-12 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="/hpp/store" id="frmHpp">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                {{-- <label for="" class="form-label mb-1">Omset Bulan</label> --}}
                                <div class="form-group">
                                    <select class="form-control" id="bulan" name="bulan">
                                        <option value="">Bulan</option>
                                        <?php
                                    $bulanini = date("m");
                                    for ($i = 1; $i < count($bulan); $i++) {
                                    ?>
                                        <option {{ Request('bulan') == $i ? "selected" : "" }} value="<?php echo $i; ?>"><?php echo $bulan[$i]; ?></option>
                                        <?php
                                    }
                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <select class="form-control" id="tahun" name="tahun">
                                        <option value="">Tahun</option>
                                        <?php
                                    $tahunmulai = 2020;
                                    for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                                    ?>
                                        <option {{ Request('tahun') == $thn ? "selected" : "" }} value="<?php echo $thn; ?>"><?php echo $thn; ?></option>
                                        <?php
                                    }
                                    ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover-animation">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>Kode Produk</th>
                                        <th>Nama Barang</th>
                                        <th style="width: 20%">HPP</th>
                                    </tr>
                                </thead>
                                <tbody id="loadhpp">
                                </tbody>
                            </table>
                            {{-- {{ $salesman->links('vendor.pagination.vuexy') }} --}}
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <button class="btn btn-primary btn-block" type="submit" name="submit"><i class="fa fa-send mr-1"></i>Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {
        function loadhpp() {
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            $.ajax({
                type: 'POST'
                , url: '/hpp/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , bulan: bulan
                    , tahun: tahun
                }
                , cache: false
                , success: function(respond) {
                    $("#loadhpp").html(respond);
                }
            });
        }
        loadhpp();
        $("#bulan,#tahun").change(function() {
            loadhpp();
        });

        $("#frmHpp").submit(function() {
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            if (bulan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bulan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#bulan").focus();
                });
                return false;
            } else if (tahun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tahun Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tahun").focus();
                });

                return false;
            }
        });
    });

</script>
@endpush
