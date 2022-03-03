@extends('layouts.midone')
@section('titlepage', 'Data Permintaan Pengiriman')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Permintaan Pengiriman</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/permintaanpengiriman">Permintaan Pengiriman</a>
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
            <div class="col-md-8 col-sm-8">
                <div class="card">
                    @if (in_array($level, $salesman_tambah))
                        <div class="card-header">
                            <a href="#" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah
                                Data</a>
                        </div>
                    @endif
                    <div class="card-body">
                        <form action="/permintaanpengiriman">
                            <div class="row">



                                {{-- <div class="col-lg-4 col-sm-12">
                                    <button type="submit" name="submit" value="1" class="btn btn-primary"><i
                                            class="fa fa-search mr-2"></i> Search</button>
                                </div> --}}
                            </div>

                        </form>
                        <div class="table-responsive">
                            <table class="table table-hover-animation">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>No. Permintaan</th>
                                        <th>Tanggal</th>
                                        <th>Cabang</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>

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
    <!-- Detail Salesman -->
    <div class="modal fade text-left" id="mdlinputpengiriman" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Detail Salesman</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadinputpengiriman"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
