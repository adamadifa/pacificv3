@extends('layouts.midone')
@section('titlepage','Data Permintaan Produksi')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Buat Permintaan Produksi</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/permintaanproduksi/create">Buat Permintaan Produksi</a>
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
        <div class="col-lg-8 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="/permintaanproduksi/store" id="frm">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <x-inputtext field="no_permintaan" label="No. Permintaan" readonly value="{{$no_permintaan}}" icon="fa fa-barcode" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <x-inputtext field="tgl_permintaan" label="Tanggal Permintaan" icon="feather icon-calendar" datepicker />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table class="table">
                                    <tr>
                                        <td>No.Order</td>
                                        <td>
                                            <input type="hidden" name="no_order" value="{{$dataoman->no_order}}" />
                                            {{$dataoman->no_order}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Bulan</td>
                                        <td>{{$bulan[$dataoman->bulan]}}</td>
                                    </tr>
                                    <tr>
                                        <td>Tahun</td>
                                        <td>{{$dataoman->tahun}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table class="table" id="mytable">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Produk</th>
                                            <th>Nama Barang</th>
                                            <th>Oman Mkt</th>
                                            <th>Stok Gudang</th>
                                            <th>Total</th>
                                            <th style="width:10%">Buffer Stok</th>
                                            <th style="width:10%">Grand Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($detailoman as $d)
                                        @php
                                        $totalmkt = $d->jumlah - $d->saldoakhir;
                                        @endphp
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>
                                                <input type="hidden" name="kode_produk[]" value="{{$d->kode_produk}}">
                                                {{$d->kode_produk}}
                                            </td>
                                            <td>{{$d->nama_barang}}</td>
                                            <td class="text-right">
                                                <input type="hidden" name="oman_mkt[]" value="{{$d->jumlah}}">
                                                {{rupiah($d->jumlah)}}
                                            </td>
                                            <td class="text-right">
                                                <input type="hidden" name="saldoakhir[]" value="{{$d->saldoakhir}}">

                                                {{rupiah($d->saldoakhir)}}
                                            </td>
                                            <td class="text-right">
                                                {{rupiah($totalmkt)}}
                                                <input type="hidden" id="totalpermintaan" value="{{$totalmkt}}" class="totalpermintaan" name="totalpermintaan[]">
                                            </td>
                                            <td>
                                                <input type="text" id="bufferstok" class="form-control bufferstok" name="bufferstok[]">
                                            </td>
                                            <td>
                                                <input type="text" id="subtotal" class="form-control subtotal" name="subtotal[]" readonly>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-block">Submit</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script type="text/javascript">
    $(function() {
        $("#frm").submit(function() {
            var tgl_permintaan = $("#tgl_permintaan").val();
            if (tgl_permintaan == "") {
                swal("Oops", "Tanggal Permintaan Harus Diisi !", "warning");
                return false;
            }
        });
        var $tblrows = $("#mytable tbody tr");
        $tblrows.each(function(index) {
            var $tblrow = $(this);

            function loadsubtotal() {
                var totalpermintaan = $tblrow.find("[id=totalpermintaan]").val();
                var bufferstok = $tblrow.find("[id=bufferstok]").val();
                if (totalpermintaan.length === 0) {
                    var totalpermintaan = 0;
                } else {
                    var totalpermintaan = parseInt(totalpermintaan);
                }
                if (bufferstok.length === 0) {
                    var bufferstok = 0;
                } else {
                    var bufferstok = parseInt(bufferstok);
                }

                var subTotal = totalpermintaan + bufferstok;


                if (!isNaN(subTotal)) {

                    $tblrow.find('.subtotal').val(subTotal);
                    var grandTotal = 0;
                    $(".subtotal").each(function() {
                        var stval = parseInt($(this).val());
                        grandTotal += isNaN(stval) ? 0 : stval;
                    });

                    //$('.grdtot').val(grandTotal.toFixed(2));
                }

            }

            loadsubtotal();
            $tblrow.find('.bufferstok').on('input', function() {
                var totalpermintaan = $tblrow.find("[id=totalpermintaan]").val();
                var bufferstok = $tblrow.find("[id=bufferstok]").val();
                if (totalpermintaan.length === 0) {
                    var totalpermintaan = 0;
                } else {
                    var totalpermintaan = parseInt(totalpermintaan);
                }
                if (bufferstok.length === 0) {
                    var bufferstok = 0;
                } else {
                    var bufferstok = parseInt(bufferstok);
                }

                var subTotal = totalpermintaan + bufferstok;


                if (!isNaN(subTotal)) {

                    $tblrow.find('.subtotal').val(subTotal);
                    var grandTotal = 0;
                    $(".subtotal").each(function() {
                        var stval = parseInt($(this).val());
                        grandTotal += isNaN(stval) ? 0 : stval;
                    });

                    //$('.grdtot').val(grandTotal.toFixed(2));
                }

            });

        });

    });

</script>

@endpush
