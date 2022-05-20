@extends('layouts.midone')
@section('titlepage', 'Data Order Management Cabang')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Order Management Cabang</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/omancabang">Order Management Cabang</a>
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
                    <a href="/omancabang/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                <div class="card-body">
                    <form action="/omancabang">
                        <div class="row">
                            <div class="col-lg-3 col-sm-12">
                                <div class="form-group  ">
                                    <select name="kode_cabang" id="" class="form-control">
                                        @if ($getcbg == "PCF")
                                        <option value="">Semua Cabang</option>
                                        @else
                                        <option value="">Pilih Cabang</option>
                                        @endif
                                        @foreach ($cabang as $c)
                                        <option {{ Request('kode_cabang')==$c->kode_cabang ? 'selected' : '' }} value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                {{-- <label for="" class="form-label mb-1">Omset Bulan</label> --}}
                                <div class="form-group">
                                    <select class="form-control" id="bulan" name="bulan">
                                        <option value="">Bulan</option>
                                        <?php
                                                $bulanini = date("m");
                                                for ($i = 1; $i < count($bulan); $i++) {
                                            ?>
                                        <option <?php if (empty(Request('bulan'))) { if ($bulanini==$i) {echo 'selected' ; } } else { if (Request('bulan')==$i) { echo 'selected' ; }} ?> value="<?php echo $i; ?>">
                                            <?php echo $bulan[$i]; ?>
                                        </option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-12">
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
                                    <th>Cabang</th>
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($oman_cabang as $d)
                                <tr>
                                    <td class="text-center">
                                        {{ $loop->iteration + $oman_cabang->firstItem() - 1 }}</td>
                                    <td>{{ $d->no_order }}</td>
                                    <td>{{ $d->kode_cabang }}</td>
                                    <td>{{ $bulan[$d->bulan] }}</td>
                                    <td>{{ $d->tahun }}</td>
                                    <td>
                                        @if ($d->status == 0)
                                        <span class="badge bg-warning"><i class="fa fa-history"></i>
                                            Pending</span>
                                        @else
                                        <span class="badge bg-success"><i class="fa fa-check"></i> Sudah Di
                                            Proses</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            @if ($d->status != 1)
                                            <a class="ml-1" href="/omancabang/{{ \Crypt::encrypt($d->no_order) }}/edit"><i class="feather icon-edit success"></i></a>
                                            <form method="POST" name="deleteform" class="deleteform" action="/omancabang/{{ Crypt::encrypt($d->no_order) }}/delete">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" class="delete-confirm ml-1">
                                                    <i class="feather icon-trash danger"></i>
                                                </a>
                                            </form>
                                            @endif
                                            <a class="ml-1 detailomancabang" no_order="{{ $d->no_order }}" href="#"><i class=" feather icon-file-text info"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                        {{ $oman_cabang->links('vendor.pagination.vuexy') }}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="mdlomancabang" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadomancabang"></div>
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

        function loadomancabang(no_order) {
            $.ajax({
                type: 'POST'
                , url: '/omancabang/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_order: no_order
                }
                , cache: false
                , success: function(respond) {
                    $("#loadomancabang").html(respond);
                }
            });
        }
        $('.detailomancabang').click(function(e) {
            var no_order = $(this).attr("no_order");
            e.preventDefault();
            loadomancabang(no_order);
            $('#mdlomancabang').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });
    })

</script>
@endpush
