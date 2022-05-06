@extends('layouts.midone')
@section('titlepage', 'Data Lainnya')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Lainnya</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/lainnyagj">Data Lainnya</a>
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
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form action="/lainnyagj/store" id="frm" method="POST">
                            @csrf
                            <input type="hidden" id="cektutuplaporan">
                            <input type="hidden" id="cektemp">
                            <input type="hidden" id="jenis_mutasi" name="jenis_mutasi" value="LAINLAIN">
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext field="no_mutasi_gudang" label="No. Mutasi Lainnya" icon="fa fa-barcode" readonly />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext field="tgl_mutasi_gudang" label="Tanggal" icon="feather icon-calendar" datepicker />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <select name="inout" id="inout" class="form-control">
                                            <option value="">IN/OUT</option>
                                            <option value="IN">IN</option>
                                            <option value="OUT">OUT</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext label="Keterangan" field="keterangan" icon="feather icon-file" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <select name="kode_produk" id="kode_produk" class="form-control select2">
                                            <option value="">Produk</option>
                                            @foreach ($produk as $d)
                                            <option value="{{$d->kode_produk}}">{{$d->nama_barang}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12 col-md-12">
                                    <x-inputtext field="jumlah" label="Jumlah" icon="feather icon-file" right />
                                </div>
                                <div class="col-lg-2 col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <a href="#" id="tambahproduk" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-hover-animation">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No.</th>
                                                <th>Kode Produk</th>
                                                <th>Nama Barang</th>
                                                <th>Jumlah</th>
                                                <th>#</th>
                                            </tr>
                                        </thead>
                                        <tbody id="loadmutasi"></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <button type="submit" name="submit" class="btn btn-primary btn-block"><i class="fa fa-send mr-1"></i>Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <form action="{{URL::current()}}">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-12 col-sm-12">
                                            <x-inputtext label="Dari" field="dari" value="{{Request('dari')}}" icon="feather icon-calendar" datepicker />
                                        </div>
                                        <div class="col-lg-6 col-md-12 col-sm-12">
                                            <x-inputtext label="Sampai" field="sampai" value="{{Request('sampai')}}" icon="feather icon-calendar" datepicker />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" name="submit" class="btn btn-primary btn-block"><i class="fa fa-search mr-1"></i> Cari</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover-animation">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No.</th>
                                                <th>No. Mutasi Gudang</th>
                                                <th>Tanggal</th>
                                                <th>IN/OUT</th>
                                                <th style="width:30%">Keterangan</th>
                                                <th>#</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($mutasi as $d)
                                            <tr>
                                                <td>{{$loop->iteration + $mutasi->firstItem()-1}}</td>
                                                <td><a href="#" class="detail" no_mutasi_gudang="{{Crypt::encrypt($d->no_mutasi_gudang)}}">{{$d->no_mutasi_gudang}}</a></td>
                                                <td>{{date("d-m-Y",strtotime($d->tgl_mutasi_gudang))}}</td>
                                                <td>
                                                    @if ($d->inout=='IN')
                                                    <span class="badge bg-success">IN</span>
                                                    @else
                                                    <span class="badge bg-danger">OUT</span>
                                                    @endif
                                                </td>
                                                <td>{{$d->keterangan}}</td>
                                                <td>
                                                    <form method="POST" class="deleteform" action="/lainnyagj/{{Crypt::encrypt($d->no_mutasi_gudang)}}/delete">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a href="#" tanggal="{{ $d->tgl_mutasi_gudang }}" class="delete-confirm ml-1">
                                                            <i class="feather icon-trash danger"></i>
                                                        </a>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{ $mutasi->links('vendor.pagination.vuexy') }}
                                </div>
                            </div>
                        </div>

                        <!-- DataTable ends -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Detail Surat Jalan -->
<div class="modal fade text-left" id="mdldetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail Lainnya</h4>
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

        function loaddetail(no_mutasi_gudang) {
            $("#loaddetail").load("/lainnyagj/" + no_mutasi_gudang + "/show");
        }
        $('.detail').click(function(e) {
            e.preventDefault();
            var no_mutasi_gudang = $(this).attr("no_mutasi_gudang");
            $('#mdldetail').modal({
                backdrop: 'static'
                , keyboard: false
            });
            loaddetail(no_mutasi_gudang);

        });

        $("#tgl_mutasi_gudang").change(function() {
            var tgl_mutasi_gudang = $("#tgl_mutasi_gudang").val();
            var jenis_mutasi = "LAINLAIN";
            $.ajax({
                type: 'POST'
                , url: '/lainnyagj/buatnomormutasi'
                , data: {
                    _token: "{{csrf_token()}}"
                    , tgl_mutasi_gudang: tgl_mutasi_gudang
                    , jenis_mutasi: jenis_mutasi
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#no_mutasi_gudang").val("");
                    $("#no_mutasi_gudang").val(respond);
                }
            });
        });

        function cektutuplaporan(tanggal) {
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                    , jenislaporan: "gudangpusat"
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#cektutuplaporan").val(respond);
                }
            });
        }

        function cektemp() {
            var jenis_mutasi = $("#jenis_mutasi").val();
            $.ajax({
                type: 'POST'
                , url: "/lainnyagj/" + jenis_mutasi + "/cektemp"
                , data: {
                    _token: "{{ csrf_token() }}"

                }
                , cache: false
                , success: function(respond) {
                    $("#cektemp").val(respond);
                }
            });
        }

        function loadmutasi() {
            var jenis_mutasi = $("#jenis_mutasi").val();
            $("#loadmutasi").load("/lainnyagj/" + jenis_mutasi + "/showtemp");
            cektemp();
        }

        loadmutasi();


        $('.delete-confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            var tanggal = $(this).attr("tanggal");
            // alert('test');
            cektutuplaporan(tanggal);
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
                        var cektutuplaporan = $("#cektutuplaporan").val();
                        if (cektutuplaporan > 0) {
                            swal("Oops", "Laporan Periode Ini Sudah Di Tutup !", "warning");
                            return false;
                        } else {
                            form.submit();
                        }
                    }
                });
        });


        $("#tambahproduk").click(function(e) {
            e.preventDefault();
            var kode_produk = $("#kode_produk").val();
            var jumlah = $("#jumlah").val();
            var jenis_mutasi = $("#jenis_mutasi").val();
            if (kode_produk == "") {
                swal({
                    title: 'Oops'
                    , text: 'Produk Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_produk").focus();
                });
            } else if (jumlah == "" || jumlah == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jumlah").focus();
                });
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/lainnyagj/storetemp'
                    , data: {
                        _token: "{{csrf_token()}}"
                        , kode_produk: kode_produk
                        , jumlah: jumlah
                        , jenis_mutasi: jenis_mutasi
                    }
                    , cache: false
                    , success: function(respond) {
                        if (respond == 0) {
                            swal("Success", "Data Berhasil Disimpan", "success");
                            $("#kode_produk").val("").change();
                            $("#jumlah").val("");
                        } else if (respond == 1) {
                            swal("Oops", "Data Data Sudah Ada", "warning");
                        } else {
                            swal("Oops", "Data Gagal Disimpan", "warning");
                        }
                        loadmutasi();
                    }
                });
            }

        });

        $("#tgl_mutasi_gudang").change(function() {
            var tgl_mutasi_gudang = $("#tgl_mutasi_gudang").val();
            cektutuplaporan(tgl_mutasi_gudang);
        });

        $("#frm").submit(function() {
            var tgl_mutasi_gudang = $("#tgl_mutasi_gudang").val();
            var inout = $("#inout").val();
            var keterangan = $("#keterangan").val();
            var cektutuplaporan = $("#cektutuplaporan").val();
            var cektemp = $("#cektemp").val();
            if (cektutuplaporan > 0) {
                swal({
                    title: 'Oops'
                    , text: 'Laporan Periode Ini Sudah Ditutup !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_mutasi_gudang").focus();
                });
                return false;
            } else if (cektemp == 0 || cektemp == "") {
                swal({
                    title: 'Oops'
                    , text: 'Data Masih Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_produk").focus();
                });
                return false;
            } else if (tgl_mutasi_gudang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_mutasi_gudang").focus();
                });

                return false;
            } else if (inout == "") {
                swal({
                    title: 'Oops'
                    , text: 'IN/OUT Harus  Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#inout").focus();
                });

                return false;
            }
        });
    });

</script>
@endpush
