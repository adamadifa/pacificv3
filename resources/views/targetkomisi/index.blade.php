@extends('layouts.midone')
@section('titlepage', 'Target Komisi')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Target Komisi</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/targetkomisi">Target Komisi</a>
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
        <div class="col-md-6 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <form action="/targetkomisi">
                        <div class="row">
                            <div class="col-lg-8 col-sm-12">
                                <div class="form-group">
                                    <select class="form-control" id="tahun" name="tahun">
                                        <?php
                                        $tahunmulai = 2020;
                                        for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                                        ?>
                                        <option <?php if (empty(Request('tahun'))) { if (date('Y')==$thn) {
                                            echo 'Selected' ; } } else { if (Request('tahun')==$thn) { echo 'selected' ;
                                            } } ?> value="<?php echo $thn; ?>">
                                            <?php echo $thn; ?>
                                        </option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <div class="form-group">
                                    <button class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- <div class="col-lg-4 col-sm-12">
                            <button type="submit" name="submit" value="1" class="btn btn-primary"><i
                                    class="fa fa-search mr-2"></i> Search</button>
                        </div> --}}
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">KODE</th>
                                    <th>BULAN</th>
                                    <th>TAHUN</th>
                                    <th>KP</th>
                                    <th>RSM</th>
                                    <th>GM</th>
                                    <th>DIREKTUR</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($target as $d)
                                <tr>
                                    <td>{{$d->kode_target}}</td>
                                    <td>{{$bulan[$d->bulan]}}</td>
                                    <td>{{$d->tahun}}</td>
                                    <td>
                                        @if ($d->kp > 0)
                                        <a href="#" kodetarget="{{$d->kode_target}}" class="detailapprovecabang"><i class="fa fa-history warning"></i></a>
                                        @else
                                        <a href="#" kodetarget="{{$d->kode_target}}" class="detailapprovecabang"><i class="fa fa-check success"></i></a>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($d->mm > 0)
                                        <i class="fa fa-history warning"></i>
                                        @else
                                        <i class="fa fa-check success"></i>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($d->em > 0)
                                        <i class="fa fa-history warning"></i>
                                        @else
                                        <i class="fa fa-check success"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($d->direktur > 0)
                                        <i class="fa fa-history warning"></i>
                                        @else
                                        <i class="fa fa-check success"></i>
                                        @endif
                                    </td>
                                    <td>

                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            @if (in_array($level,$targetkomisiinput))
                                            <a href="#" class="ml-1 btn btn-primary btn-sm settarget" kodetarget="{{$d->kode_target}}"><i class="feather icon-settings"></i> Input Qty</a>
                                            @endif
                                            @if (in_array($level,$generatecashin))
                                            <a href="/targetkomisi/{{Crypt::encrypt($d->kode_target)}}/generatecashin" class="ml-1 btn btn-warning btn-sm"><i class="feather icon-settings"></i> Generate Cashin</a>
                                            @endif
                                            <a href="#" class="ml-1 detailtarget info" kodetarget="{{$d->kode_target}}"><i class="feather icon-file-text"></i></a>
                                            @php
                                            $kode_cabang = Auth::user()->kode_cabang;
                                            @endphp
                                            @if ($level=="kepala penjualan" && $d->mm > 0)
                                            @if ($d->kp > 0)
                                            <a href="/targetkomisi/{{Crypt::encrypt($d->kode_target)}}/{{Crypt::encrypt($kode_cabang)}}/approvetarget" class="ml-1 success"><i class="fa fa-check"></i></a>
                                            @else
                                            <a href="/targetkomisi/{{Crypt::encrypt($d->kode_target)}}/{{Crypt::encrypt($kode_cabang)}}/canceltarget" class="ml-1 danger"><i class="fa fa-close"></i></a>
                                            @endif
                                            @elseif($level=="manager marketing" && $d->em > 0 && $d->kp==0)
                                            @if ($d->mm > 0)
                                            <a href="/targetkomisi/{{Crypt::encrypt($d->kode_target)}}/{{Crypt::encrypt($kode_cabang)}}/approvetarget" class="ml-1 success"><i class="fa fa-check"></i></a>
                                            @endif

                                            @endif
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
<!-- Detail Approve Cabang -->
<div class="modal fade text-left" id="mdlapprovecabang" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail Approved Cabang</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadapprovecabang"></div>
            </div>
        </div>
    </div>
</div>

<!-- Input Target Cabang -->
<div class="modal fade text-left" id="mdlsettarget" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input Target Cabang</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputtarget"></div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Target -->
<div class="modal fade text-left" id="mdldetailtarget" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 950px" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail Target</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaddetailtarget"></div>
            </div>
        </div>
    </div>
</div>


@endsection
@push('myscript')
<script>
    $(function() {
        function loadapprovecabang(kode_target) {
            $.ajax({
                type: 'POST'
                , url: '/targetkomisi/detailapprovecabang'
                , data: {
                    _token: "{{csrf_token()}}"
                    , kode_target: kode_target
                }
                , cache: false
                , success: function(respond) {
                    $("#loadapprovecabang").html(respond);
                }
            });
        }


        $(".detailapprovecabang").click(function() {
            var kode_target = $(this).attr("kodetarget");
            loadapprovecabang(kode_target);
            $('#mdlapprovecabang').modal({
                backdrop: 'static'
                , keyboard: false
            });

        });


        function loaddetailtarget(kode_target) {
            $.ajax({
                type: 'POST'
                , url: '/targetkomisi/show'
                , data: {
                    _token: "{{csrf_token()}}"
                    , kode_target: kode_target
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailtarget").html(respond);
                }
            });

        }
        $(".detailtarget").click(function() {
            var kode_target = $(this).attr("kodetarget");
            loaddetailtarget(kode_target);
            $('#mdldetailtarget').modal({
                backdrop: 'static'
                , keyboard: false
            });

        });

        function loadinputtarget(kode_target) {
            $.ajax({
                type: 'POST'
                , url: '/targetkomisi/create'
                , data: {
                    _token: "{{csrf_token()}}"
                    , kode_target: kode_target
                }
                , cache: false
                , success: function(respond) {
                    $("#loadinputtarget").html(respond);
                }
            });
        }
        $(".settarget").click(function(e) {
            e.preventDefault();
            var kode_target = $(this).attr("kodetarget");
            $('#mdlsettarget').modal({
                backdrop: 'static'
                , keyboard: false
            });
            loadinputtarget(kode_target);

        });

    });

</script>
@endpush
