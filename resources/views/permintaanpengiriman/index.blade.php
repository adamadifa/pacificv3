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
        <div class="col-md-12 col-sm-12 col-lg-12">
            <div class="card">



                <div class="card-header">
                    @if (in_array($level,$permintaanpengiriman_tambah))
                    <a href="#" class="btn btn-primary" id="inputpermintaan"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                    @endif
                </div>

                <div class="card-body">
                    <form action="{{URL::current()}}">
                        <div class="row">
                            <div class="col-lg-3 col-sm-12">
                                <x-inputtext field="tanggal" value="{{ Request('tanggal') }}" label="Tanggal Permintaan Pengiriman" icon="feather icon-calendar" datepicker />
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <select name="cabang" id="cabang" class="form-control">
                                        <option value="">Cabang</option>
                                        @foreach ($cabang as $d)
                                        <option {{Request('cabang')==$d->kode_cabang ? 'selected' : ''}} value="{{$d->kode_cabang}}">{{$d->nama_cabang}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <div class="form-group">
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Semua Status</option>
                                        <option {{ Request('status') === '0' ? 'selected' : '' }} value="0">Belum Di
                                            Proses</option>
                                        <option {{ Request('status') == 1 ? 'selected' : '' }} value="1">Sudah Di
                                            Proses</option>
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
                                    <th>No. Permintaan</th>
                                    <th>Tanggal</th>
                                    <th>Cabang</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Salesman</th>
                                    <th>No. SJ</th>
                                    <th>No.Dok / No. Faktur</th>
                                    <th>Tanggal SJ</th>
                                    <th>Status SJ</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pp as $d)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration + $pp->firstItem() - 1 }}</td>
                                    <td><a class="ml-1 detail" href="#" no_permintaan_pengiriman="{{ Crypt::encrypt($d->no_permintaan_pengiriman) }}">{{$d->no_permintaan_pengiriman}}</a></td>

                                    <td>
                                        <a href="#" no_permintaan_pengiriman="{{ $d->no_permintaan_pengiriman }}" class="ubahtanggal">
                                            {{ date('d-m-Y', strtotime($d->tgl_permintaan_pengiriman)) }}
                                        </a>
                                    </td>
                                    <td>{{ $d->kode_cabang }}</td>
                                    <td>{{ $d->keterangan }}</td>
                                    <td>
                                        @if ($d->status == 1)
                                        <span class="badge bg-success"><i class="feather icon-check"></i> Sudah
                                            Di Proses</span>
                                        @else
                                        <span class="badge bg-danger"><i class="feather icon-history"></i>
                                            Belum Di Proses</span>
                                        @endif
                                    </td>
                                    <td>{{ $d->nama_karyawan }}</td>
                                    <td>{{$d->no_mutasi_gudang}}</td>
                                    <td>{{$d->no_dok}}</td>
                                    <td>{{!empty($d->tgl_mutasi_gudang) ? date("d-m-Y",strtotime($d->tgl_mutasi_gudang)) : ''}}</td>
                                    <td>
                                        @if ($d->status==1)
                                        @if ($d->status_sj==0)
                                        <span class="badge bg-danger">Belum Diterima Cabang</span>
                                        @elseif($d->status_sj==1)
                                        <span class="badge bg-success">Sudah Diterima Cabang</span>
                                        @elseif($d->status_sj ==2)
                                        <span class="badge bg-info">Transit Out</span>
                                        @endif
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            @if (in_array($level,$permintaanpengiriman_hapus))
                                            @if ($d->status == 0)
                                            <form method="POST" class="deleteform" action="/permintaanpengiriman/{{ Crypt::encrypt($d->no_permintaan_pengiriman) }}/delete">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" class="delete-confirm ml-1">
                                                    <i class="feather icon-trash danger"></i>
                                                </a>
                                            </form>
                                            @endif
                                            @endif
                                            @if (in_array($level,$permintaanpengiriman_proses))
                                            @if ($d->status==0)
                                            <a href="#" class="input_sj ml-1" no_permintaan_pengiriman="{{ Crypt::encrypt($d->no_permintaan_pengiriman) }}"><i class="feather icon-external-link success"></i></a>
                                            @else
                                            @if ($d->status==1 AND $d->status_sj==0)
                                            <a href="/suratjalan/{{Crypt::encrypt($d->no_mutasi_gudang)}}/batalkansuratjalan" class="ml-1"><i class="fa fa-close danger"></i></a>
                                            @endif
                                            @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $pp->links('vendor.pagination.vuexy') }}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Detail Permintaan Pengiriman -->
<div class="modal fade text-left" id="mdldetailpp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail Permintaan Pengiriman</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaddetailpp"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdlubahtanggal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Ubah Tanggal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadubahtanggal"></div>
            </div>
        </div>
    </div>
</div>
<!-- Input Permintaan Pengiriman -->
<div class="modal fade text-left" id="mdlinputpengiriman" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input Permintaan Pengiriman</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/permintaanpengiriman/store" id="frmPermintaanpengiriman" method="POST">
                    @csrf
                    <input type="hidden" id="cektemp" />
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext field="no_permintaan_pengiriman" label="Auto" readonly icon="feather icon-credit-card" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext field="tgl_permintaan_pengiriman" label="Tanggal Permintaan Pengiriman" icon="feather icon-calendar" datepicker />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <select name="kode_cabang" id="kode_cabang" class="form-control">
                                    <option value="">Pilih Cabang</option>
                                    @foreach ($cabang as $c)
                                    <option value="{{ $c->kode_cabang }}">
                                        {{ $c->nama_cabang }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="pilihsalesman">
                        <div class="col-12">
                            <div class="form-group">
                                <select name="id_karyawan" id="id_karyawan" class="form-control">
                                    <option value="">Pilih Salesman</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext field="keterangan" label="Keterangan" icon="feather icon-file" />
                        </div>
                    </div>

                    <div class="divider divider-left">
                        <div class="divider-text">Detail Barang</div>
                    </div>
                    <div class="row">
                        <div class="col-lg-5 col-sm-12">
                            <div class="form-group">
                                <select name="kode_produk" id="kode_produk" class="form-control select2">
                                    <option value="">Pilih Barang</option>
                                    @foreach ($produk as $d)
                                    <option value="{{ $d->kode_produk }}">{{ $d->nama_barang }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <x-inputtext field="jumlah" label="Jumlah" icon="feather icon-file" right />
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <div class="form-group">
                                <a href="#" class="btn btn-primary" id="tambahproduk"><i class="feather icon-plus"></i>
                                    Tambah</a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-hover-animation">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Kode Produk</th>
                                        <th>Nama Produk</th>
                                        <th>Jumlah</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="loadproduktemp"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <button class="btn btn-primary btn-block" type="submit" name="submit">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Input Surat Jalan -->
<div class="modal fade text-left" id="mdlinputsj" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Buat Surat Jalan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputsj"></div>
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

        $(".ubahtanggal").click(function(e) {
            e.preventDefault();
            var no_permintaan_pengiriman = $(this).attr("no_permintaan_pengiriman");
            $.ajax({
                type: 'POST'
                , url: '/permintaanpengiriman/ubahtanggal'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_permintaan_pengiriman: no_permintaan_pengiriman
                , }
                , cache: false
                , success: function(respond) {
                    $("#loadubahtanggal").html(respond);
                }
            });
            $("#mdlubahtanggal").modal("show");
        });

        $('#inputpermintaan').click(function(e) {
            e.preventDefault();
            tampilkanproduk();
            $('#mdlinputpengiriman').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });


        function detailpp(no_permintaan_pengiriman) {
            $.ajax({
                type: 'GET'
                , url: '/permintaanpengiriman/' + no_permintaan_pengiriman + '/show'
                , cache: false
                , success: function(respond) {
                    $("#loaddetailpp").html(respond);
                }
            });
        }
        $('.detail').click(function(e) {
            e.preventDefault();
            var no_permintaan_pengiriman = $(this).attr("no_permintaan_pengiriman");
            $('#mdldetailpp').modal({
                backdrop: 'static'
                , keyboard: false
            });
            detailpp(no_permintaan_pengiriman);
        });

        function loadpilihsalesman() {
            var kode_cabang = $("#kode_cabang").val();
            if (kode_cabang == "TSM") {
                $("#pilihsalesman").show();
            } else {
                $("#pilihsalesman").hide();
            }
        }

        function cektemp() {
            $.ajax({
                type: 'GET'
                , url: '/permintaanpengiriman/cektemp'
                , cache: false
                , success: function(respond) {
                    $("#cektemp").val(respond);
                }
            });
        }
        cektemp();

        function loadsalesmancabang(kode_cabang) {
            $.ajax({
                type: 'POST'
                , url: '/salesman/getsalescab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                }
                , cache: false
                , success: function(respond) {
                    $("#id_karyawan").html(respond);
                }
            });
        }
        loadpilihsalesman();

        $("#kode_cabang").change(function(e) {
            var kode_cabang = $(this).val();
            if (kode_cabang == "TSM") {
                loadsalesmancabang(kode_cabang);
            }
            buat_no_permintaan();
            loadpilihsalesman();
        });
        $("#jumlah").maskMoney();

        function tampilkanproduk() {
            $.ajax({
                type: 'GET'
                , url: '/permintaanpengiriman/showtemp'
                , cache: false
                , success: function(respond) {
                    $("#loadproduktemp").html(respond);
                    cektemp();
                }
            });
        }

        function buat_no_permintaan() {
            var tgl_permintaan_pengiriman = $("#tgl_permintaan_pengiriman").val();
            var kode_cabang = $("#kode_cabang").val();
            $.ajax({
                type: 'POST'
                , url: '/permintaanpengiriman/buatnopermintaan'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tgl_permintaan_pengiriman: tgl_permintaan_pengiriman
                    , kode_cabang: kode_cabang
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#no_permintaan_pengiriman").val("");
                    $("#no_permintaan_pengiriman").val(respond);
                }
            });
        }

        $("#tgl_permintaan_pengiriman").change(function() {
            buat_no_permintaan();
        });

        function tambahproduk() {
            var kode_produk = $("#kode_produk").val();
            var jumlah = $("#jumlah").val();
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
                    , text: 'Jumlah Tidak Boleh 0 !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jumlah").focus();
                });
            } else {
                $.ajax({
                    type: 'POST'
                    , url: 'permintaanpengiriman/storetemp'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , kode_produk: kode_produk
                        , jumlah: jumlah
                    }
                    , cache: false
                    , success: function(respond) {
                        if (respond == 1) {
                            swal({
                                title: 'Oops'
                                , text: 'Data Sudah Ada!'
                                , icon: 'warning'
                                , showConfirmButton: false
                            }).then(function() {
                                $("#kode_produk").focus();
                            });
                        } else {
                            $("#jumlah").val(0);
                            $("#kode_produk").focus()
                        }
                    }
                });
            }
        }

        $("#tambahproduk").click(function(e) {
            tambahproduk();
            tampilkanproduk();
        });
        $("#frmPermintaanpengiriman").submit(function() {
            var tgl_permintaan_pengiriman = $("#tgl_permintaan_pengiriman").val();
            var kode_cabang = $("#kode_cabang").val();
            var keterangan = $("#keterangan").val();
            var id_karyawan = $("#id_karyawan").val();
            var cek = $("#cektemp").val();

            if (tgl_permintaan_pengiriman == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Permintaan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_permintaan_pengiriman").focus();
                });
                return false;
            } else if (kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cabang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_cabang").focus();
                });
                return false;
            } else if (kode_cabang == "TSM" && id_karyawan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Salesman  Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#id_karyawan").focus();
                });
                return false;
            } else if (keterangan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Keterangan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#keterangan").focus();
                });
                return false;
            } else if (cek == "" || cek == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Data Barang Masih Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_produk").focus();
                });
                return false;
            } else {
                return true;
            }
        });

        $(".input_sj").click(function(e) {
            e.preventDefault();
            var no_permintaan_pengiriman = $(this).attr("no_permintaan_pengiriman");
            $("#loadinputsj").load('/suratjalan/' + no_permintaan_pengiriman + '/create');
            $('#mdlinputsj').modal({
                backdrop: 'static'
                , keyboard: false
            });

        });
    });

</script>
@endpush
