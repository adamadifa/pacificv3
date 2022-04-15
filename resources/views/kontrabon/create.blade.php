@extends('layouts.midone')
@section('titlepage','Input Kontrabon')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Input Kontrabon</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/kontrabon/create">Input Kontrabon</a>
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
        <form action="/kontrabon/store" method="POST" id="frmKontrabon">

            @csrf
            <input type="hidden" id="cektutuplaporan">
            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext field="no_kontrabon" label="No. Kontrabon / Internal Memo" icon="feather icon-credit-card" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext field="tgl_kontrabon" label="Tanggal" icon="feather icon-calendar" datepicker />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <select name="kategori" id="kategori" class="form-control">
                                                    <option value="">Jenis Pengajuan</option>
                                                    <option value="KB">KB</option>
                                                    <option value="IM">IM</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <input type="hidden" id="kode_supplier" name="kode_supplier">
                                            <x-inputtext field="nama_supplier" label="Supplier" icon="feather icon-user" readonly />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-inputtext field="no_dokumen" label="No. Dokumen" icon="feather icon-credit-card" />
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <select name="jenisbayar" id="jenisbayar" class="form-control">
                                                    <option value="">Jenis Bayar</option>
                                                    <option value="tunai">Tunai</option>
                                                    <option value="transfer">Transfer</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DataTable ends -->
                    </div>
                </div>
                <div class="col-8">
                    <div class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div class="avatar bg-rgba-danger m-2" style="padding:3rem ">
                                <div class="avatar-content">
                                    <i class="feather icon-shopping-cart text-danger" style="font-size: 4rem"></i>
                                </div>
                            </div>
                            <div>
                                <h2 class="text-bold-700" style="font-size: 6rem; padding:2rem" id="grandtotal">0,00</h2>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Detail Pembelian</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 col-sm-12">
                                    <x-inputtext field="nobukti_pembelian" label="No. Bukti Pembelian" icon="feather icon-credit-card" readonly />
                                </div>
                                <div class="col-lg-2 col-sm-12">
                                    <input type="hidden" id="totalbayar" name="totalbayar">
                                    <x-inputtext field="totalpembelian" label="Total Pembelian" icon="feather icon-file" right readonly />
                                </div>
                                <div class="col-lg-2 col-sm-12">
                                    <x-inputtext field="jmlbayar" label="Jumlah Bayar" icon="feather icon-file" right />
                                </div>
                                <div class="col-lg-2 col-sm-12">
                                    <x-inputtext field="keterangan" label="Keterangan" icon="feather icon-box" right />
                                </div>
                                <div class="col-lg-1 col-sm-12">
                                    <div class="form-group">
                                        <a href="#" class="btn btn-primary" id="tambahdetail"><i class="fa fa-plus"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-hover-animation">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>No. Bukti</th>
                                                <th>Keterangan</th>
                                                <th>Jumlah Bayar</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="loaddetailkontrabon"></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-12">
                                    <button class="btn btn-primary btn-block"><i class="fa fa-send mr-1"></i>Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
    <!-- Data list view end -->
</div>
</div>
<!-- Data Supplier -->
<div class="modal fade text-left" id="mdlsupplier" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class=" modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Data Supplier</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadsupplier"></div>
            </div>
        </div>
    </div>
</div>

<!-- Data Pembelian -->
<div class="modal fade text-left" id="mdlpembelian" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class=" modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Data Pembelian <span id="namasupplier"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadpembelian"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {
        $('#nama_supplier').click(function(e) {
            e.preventDefault();
            $('#mdlsupplier').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadsupplier").load("/supplier/getsupplier");
        });
    });
    $('#nobukti_pembelian').click(function(e) {
        var kode_supplier = $("#kode_supplier").val();
        var supplier = $("#nama_supplier").val();

        if (kode_supplier == "") {
            swal({
                title: 'Oops'
                , text: 'Supplier Harus Dipilih !'
                , icon: 'warning'
                , showConfirmButton: false
            }).then(function() {
                $("#nama_supplier").focus();
            });
        } else {
            $("#namasupplier").text(supplier);
            e.preventDefault();
            $('#mdlpembelian').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadpembelian").load("/pembelian/" + kode_supplier + "/getpembeliankontrabon");
        }
    });

</script>
@endpush
