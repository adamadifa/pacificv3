@extends('layouts.midone')
@section('titlepage','Edit Opname Mutasi Barang Produksi')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Edit Opname Barang Produksi</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Edit Opname Barang Produksi</a>
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
        <div class="col-md-6 col-sm-8">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Kode Opname</th>
                            <th>{{ $opname->kode_opname }}</th>
                        </tr>
                        <tr>
                            <th>Bulan</th>
                            <th>{{ $bulan[$opname->bulan] }}</th>
                        </tr>
                        <tr>
                            <th>Tahun</th>
                            <th>{{ $opname->tahun }}</th>
                        </tr>
                        <tr>
                            <th>Tanggal Input</th>
                            <th>{{ DateToIndo2($opname->tanggal) }}</th>
                        </tr>
                    </table>
                    <table class="table table-hover-animation">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Qty</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detail as $d)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $d->kode_barang }}</td>
                                <td>{{ $d->nama_barang }}</td>
                                <td class="text-right">{{ desimal($d->qty) }}</td>
                                <td>
                                    <a href="#" kode_barang="{{ $d->kode_barang }}" class="edit"><i class="feather icon-edit success"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Edit Saldo Aawal -->
<div class="modal fade text-left" id="mdledit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit Opname</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadedit"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {
        $(".edit").click(function(e) {
            e.preventDefault();
            var kode_barang = $(this).attr("kode_barang");
            var kode_opname = "{{ $opname->kode_opname }}";
            $("#loadedit").load("/opnamemutasibarangproduksi/" + kode_opname + "/" + kode_barang + "/editbarang");
            $('#mdledit').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });
    });

</script>
@endpush
