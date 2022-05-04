@extends('layouts.midone')
@section('titlepage','Form Serah Terima Hasil Produksi (FSTHP)')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">FSTHP</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/fsthp">FSTHP</a>
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
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form action="/fsthp/store" method="POST" id="frmFsthp">
                            @csrf
                            <input type="hidden" id="cekfsthptemp">
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext field="no_fsthp" label="No. FSTHP" icon="feather icon-credit-card" readonly />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext field="tgl_mutasi_produksi" label="Tanggal Mutasi Produksi" icon="feather icon-calendar" datepicker />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <input type="hidden" id="kode_produk" name="kode_produk">
                                    <x-inputtext field="nama_barang" label="Barang" icon="feather icon-file" readonly />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <select name="unit" id="unit" class="form-control">
                                            <option value="">Unit</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <select name="shift" id="shift" class="form-control">
                                            <option value="">Shift</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-10 col-sm-12 col-md-12">
                                    <x-inputtext field="jumlah" label="Jumlah" icon="feather icon-file" right />
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <a href="#" class="btn btn-primary" id="tambahbarang"><i class="fa fa-plus"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-hover-animation">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Kode Produk</th>
                                                <th>Nama Barang</th>
                                                <th>Shift</th>
                                                <th>Jumlah</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="loadfsthp"></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-12">
                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                        <input type="checkbox" class="aggrement" name="aggrement" value="aggrement">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">Yakin Akan Disimpan ?</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-5" id="tombolsimpan">
                                <div class="col-12">
                                    <div class="form-group">
                                        <button class="btn btn-primary btn-block"><i class="feather icon-send mr-1"></i> Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{URL::current()}}">
                            <div class="row">
                                <div class="col-8">
                                    <x-inputtext label="Tanggal" field="tanggal" icon="feather icon-calendar" value="{{ Request('tanggal') }}" datepicker />
                                </div>
                                <div class="col-4">
                                    <div class="row">
                                        <div class="form-group">
                                            <button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-search mr-1"></i> Cari</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>No. Fsthp</th>
                                    <th>Tanggal</th>
                                    <th>Unit</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($fsthp as $d)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration + $fsthp->firstItem() - 1 }}</td>
                                    <td>{{ $d->no_mutasi_produksi }}</td>
                                    <td>{{ date("d-m-Y",strtotime($d->tgl_mutasi_produksi)) }}</td>
                                    <td>{{ $d->unit }}</td>
                                    <td>
                                        @if ($d->status==1)
                                        <span class="badge bg-success"><i class="fa fa-check"></i> Diterima Gudang</span>
                                        @else
                                        <span class="badge bg-warning"><i class="fa fa-history"></i> Menunggu Approval Gudang</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a class="ml-1 detail" href="#" no_mutasi_produksi="{{ Crypt::encrypt($d->no_mutasi_produksi) }}"><i class=" feather icon-file-text info"></i></a>
                                            @if ($d->status != 1)
                                            <form method="POST" class="deleteform" action="/bpbj/{{Crypt::encrypt($d->no_mutasi_produksi)}}/delete">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" tanggal="{{ $d->tgl_mutasi_produksi }}" class="delete-confirm ml-1">
                                                    <i class="feather icon-trash danger"></i>
                                                </a>
                                            </form>
                                            <a href="/fsthpgj/{{Crypt::encrypt($d->no_mutasi_produksi)}}/approve" class="ml-1"><i class="fa fa-check success"></i></a>
                                            @else
                                            <a href="/fsthpgj/{{Crypt::encrypt($d->no_mutasi_produksi)}}/batalkanapprove" class="ml-1"><i class="fa fa-close danger"></i></a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $fsthp->links('vendor.pagination.vuexy') }}


                        <!-- DataTable ends -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Detail Fsthp -->
<div class="modal fade text-left" id="mdldetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail FSTHP</h4>
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

<!-- Pilih Barang  -->
<div class="modal fade text-left" id="mdlpilihbarang" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Pilih Barang</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadpilihbarang"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        function buatnomorfsthp() {
            var tgl_mutasi_produksi = $("#tgl_mutasi_produksi").val();
            var kode_produk = $("#kode_produk").val();
            var shift = $("#shift").val();
            $.ajax({
                type: 'POST'
                , url: '/fsthp/buat_nomor_fsthp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tgl_mutasi_produksi: tgl_mutasi_produksi
                    , kode_produk: kode_produk
                    , shift: shift
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#no_fsthp").val(respond);
                }

            });
        }

        function loaddetail(no_mutasi_produksi) {
            $.ajax({
                type: 'POST'
                , url: '/fsthp/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_mutasi_produksi: no_mutasi_produksi
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetail").html(respond);
                }
            });
        }

        function loadBarang() {
            $("#loadpilihbarang").load("/fsthp/getbarang");
        }

        function loadFsthp() {
            var kode_produk = $("#kode_produk").val();
            var unit = $("#unit").val();
            var shift = $("#shift").val();
            //alert(shift);
            $("#loadfsthp").load('/fsthp/' + kode_produk + '/' + unit + '/' + shift + '/showtemp');
            cekfsthptemp();
        }

        function cekfsthptemp() {
            var kode_produk = $("#kode_produk").val();
            var unit = $("#unit").val();
            var shift = $("#shift").val();
            $.ajax({
                type: 'POST'
                , url: '/fsthp/cekfsthptemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_produk: kode_produk
                    , unit: unit
                    , shift: shift
                }
                , cache: false
                , success: function(respond) {
                    $("#cekfsthptemp").val(respond);
                }
            });
        }
        $("#tgl_mutasi_produksi").change(function(e) {
            e.preventDefault();
            var tgl_mutasi_produksi = $(this).val();
            buatnomorfsthp();
            cektutuplaporan(tgl_mutasi_produksi);
        });

        $("#shift").change(function(e) {
            e.preventDefault();
            buatnomorfsthp();
            loadFsthp();
        });

        $("#unit").change(function() {
            loadFsthp();
        });
        $(".detail").click(function(e) {
            e.preventDefault();
            var no_mutasi_produksi = $(this).attr("no_mutasi_produksi");
            loaddetail(no_mutasi_produksi);
            $('#mdldetail').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        function cektutuplaporan(tanggal) {
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                    , jenislaporan: "produksi"
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#cektutuplaporan").val(respond);
                }
            });
        }

        $("#nama_barang").click(function(e) {
            e.preventDefault();
            var tgl_mutasi_produksi = $("#tgl_mutasi_produksi").val();
            if (tgl_mutasi_produksi == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal BPBJ Harus Diisi Dulu !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_mutasi_produksi").focus();
                });
            } else {
                loadBarang();
                $('#mdlpilihbarang').modal({
                    backdrop: 'static'
                    , keyboard: false
                });
            }
        });

        $("#tambahbarang").click(function(e) {
            e.preventDefault();
            var kode_produk = $("#kode_produk").val();
            var shift = $("#shift").val();
            var unit = $("#unit").val();
            var jumlah = $("#jumlah").val();

            if (kode_produk == "") {
                swal({
                    title: 'Oops'
                    , text: 'Barang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_barang").focus();
                });
            } else if (unit == "") {
                swal({
                    title: 'Oops'
                    , text: 'Unit Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#unit").focus();
                });
            } else if (shift == "") {
                swal({
                    title: 'Oops'
                    , text: 'Shift Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#shift").focus();
                });
            } else if (jumlah == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#shift").focus();
                });
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/fsthp/storetemp'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , kode_produk: kode_produk
                        , unit: unit
                        , shift: shift
                        , jumlah: jumlah
                    }
                    , cache: false
                    , success: function(respond) {
                        if (respond == 1) {
                            swal("Oops", "Data Sudah Ada", "warning");
                        } else if (respond == 2) {
                            swal("Oops", "Data Gagal Disimpan", "warning");
                        } else {
                            swal("Berhasil", "Data Berhasil Disimpan", "success");

                            $("#jumlah").val("");
                            $("#jumlah").focus();
                        }
                        loadFsthp();
                    }
                });
            }
        });
        $('.aggrement').change(function() {
            if (this.checked) {
                $("#tombolsimpan").show();
            } else {
                $("#tombolsimpan").hide();
            }
        });

        function hidetombolsimpan() {
            $("#tombolsimpan").hide();
        }

        hidetombolsimpan();

        $("#frmFsthp").submit(function() {
            var tgl_mutasi_produksi = $("#tgl_mutasi_produksi").val();
            var kode_produk = $("#kode_produk").val();
            var unit = $("#unit").val();
            var shift = $("#shift").val();
            var cekfsthptemp = $("#cekfsthptemp").val();
            var cektutuplaporan = $("#cektutuplaporan").val();

            if (cektutuplaporan > 0) {
                swal({
                    title: 'Oops'
                    , text: 'Laporan Periode Ini Sudah Ditutup !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_mutasi_produksi").focus();
                });
                return false;
            } else if (tgl_mutasi_produksi == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_mutasi_produksi").focus();
                });
                return false;
            } else if (kode_produk == "") {
                swal({
                    title: 'Oops'
                    , text: 'Barang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_barang").focus();
                });
                return false;
            } else if (unit == "") {
                swal({
                    title: 'Oops'
                    , text: 'Unit Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_barang").focus();
                });
                return false;
            } else if (shift == "") {
                swal({
                    title: 'Oops'
                    , text: 'Shift Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_barang").focus();
                });
                return false;
            } else if (cekfsthptemp == "" || cekfsthptemp == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Data Masih Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_barang").focus();
                });
                return false;
            }


        });


        $("#jumlah").maskMoney();
        $('.delete-confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            var tanggal = $(this).attr("tanggal");
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
    });

</script>
@endpush
