@extends('layouts.midone')
@section('titlepage','Data Order Management Cabang')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Order Management</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/oman">Order Management</a>
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
                <div class="card-header">
                    <a href="/oman/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                <div class="card-body">
                    <form action="/oman">
                        <div class="row">
                            <div class="col-lg-9 col-sm-12">
                                <div class="form-group">
                                    <select class="form-control" id="tahun" name="tahun">
                                        <?php
                                        $tahunmulai = 2020;
                                        for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                                        ?>
                                        <option <?php if(empty(Request('tahun'))){ if (date('Y') == $thn) { echo "Selected";} } else{ if(Request('tahun') == $thn){echo "selected";}} ?> value="<?php echo $thn; ?>"><?php echo $thn; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search mr-2"></i> Search</button>
                            </div>
                        </div>

                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>No. Order</th>

                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($oman_marketing as $d)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $d->no_order }}</td>

                                    <td>{{ $bulan[$d->bulan] }}</td>
                                    <td>{{ $d->tahun }}</td>
                                    <td>
                                        @if ($d->status==0)
                                        <span class="badge bg-warning"><i class="fa fa-history"></i> Pending</span>
                                        @elseif($d->status==1)
                                        <span class="badge bg-info"><i class="fa fa-check"></i> Sudah Di Proses Gudang</span>
                                        @elseif($d->status==2)
                                        <span class="badge bg-success"><i class="fa fa-check"></i> Sudah Di Proses Produksi</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            @if ($d->status ==0)
                                            <a class="ml-1" href="/oman/{{\Crypt::encrypt($d->no_order)}}/edit"><i class="feather icon-edit success"></i></a>
                                            <form method="POST" name="deleteform" class="deleteform" action="/oman/{{ Crypt::encrypt($d->no_order) }}/delete">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" class="delete-confirm ml-1">
                                                    <i class="feather icon-trash danger"></i>
                                                </a>
                                            </form>
                                            @endif
                                            <a class="ml-1 detailoman" no_order="{{ $d->no_order }}" href="#"><i class=" feather icon-file-text info"></i></a>
                                            @if ($d->status==2)
                                            <a class="ml-1 detail" no_permintaan="{{ $d->no_permintaan }}" href="#"><i class=" feather icon-file-text warning"></i></a>
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
    </div>
</div>
<div class="modal fade text-left" id="mdloman" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadoman"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdldetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaddetail"></div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('myscript')
<script>
    $(function() {
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

        function loadoman(no_order) {
            $.ajax({
                type: 'POST'
                , url: '/oman/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_order: no_order
                }
                , cache: false
                , success: function(respond) {
                    $("#loadoman").html(respond);
                }
            });
        }
        $('.detailoman').click(function(e) {
            var no_order = $(this).attr("no_order");
            e.preventDefault();
            loadoman(no_order);
            $('#mdloman').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        function loaddetail(no_permintaan) {
            $.ajax({
                type: 'POST'
                , url: '/permintaanproduksi/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_permintaan: no_permintaan
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetail").html(respond);
                }
            });
        }

        $('.detail').click(function(e) {
            var no_permintaan = $(this).attr("no_permintaan");
            e.preventDefault();
            loaddetail(no_permintaan);
            $('#mdldetail').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

    })

</script>
@endpush
