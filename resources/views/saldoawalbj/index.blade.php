@extends('layouts.midone')
@section('titlepage','Saldo Awal BJ')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Saldo Awal {{ $jenis_bj=="GS" ? 'Good Stok' : 'Bad Stok' }}</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Saldo Awal {{ $jenis_bj=="GS" ? 'Good Stok' : 'Bad Stok' }}</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <input type="hidden" id="cektutuplaporan">
        <!-- Data list view starts -->
        <!-- DataTable starts -->
        @include('layouts.notification')
        <div class="col-md-12 col-sm-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <a href="/{{ $jenis_bj=="GS" ? 'saldoawalgs' : 'saldoawalbs' }}/{{ $jenis_bj }}/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                <div class="card-body">
                    <form action="{{ URL::current() }}">
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                                        <option value="">Semua Cabang</option>
                                        @foreach ($cabang as $d)
                                        <option {{ Request('kode_cabang') == $d->kode_cabang ? "selected" : "" }} value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-3">
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
                            <div class="col-3">
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

                            <div class="col-2">
                                <div class="form-group">
                                    <button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </div>


                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Kode. Saldo Awal</th>
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    <th>Cabang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($saldoawal as $d)
                                <tr>
                                    <td>{{ $loop->iteration + $saldoawal->firstItem()-1 }}</td>
                                    <td>{{ $d->kode_saldoawal }}</td>
                                    <td>{{ $bulan[$d->bulan] }}</td>
                                    <td>{{ $d->tahun }}</td>
                                    <td>{{ $d->kode_cabang }}</td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a class="ml-1 detail" kode_saldoawal="{{ Crypt::encrypt($d->kode_saldoawal) }}"><i class=" feather icon-file-text info"></i></a>
                                            <form method="POST" class="deleteform" action="/saldoawalbj/{{Crypt::encrypt($d->kode_saldoawal)}}/delete">
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
                        {{ $saldoawal->links('vendor.pagination.vuexy') }}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Detail Saldo Aawal -->
<div class="modal fade text-left" id="mdldetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail Saldo Awal</h4>
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

        $(".detail").click(function(e) {
            var kode_saldoawal = $(this).attr("kode_saldoawal");
            e.preventDefault();
            $("#loaddetail").load("/saldoawalbj/" + kode_saldoawal + "/show");
            $('#mdldetail').modal({
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
    });

</script>
@endpush
