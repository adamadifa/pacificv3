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
                    <a href="#" class="btn btn-primary" id="inputpermintaan"><i class="fa fa-plus mr-1"></i> Tambah
                        Data</a>
                </div>
                @endif
                <div class="card-body">
                    <form action="/permintaanpengiriman">
                        <div class="row">
                            <div class="col-lg-4 col-sm-12">
                                <x-inputtext field="tanggal" value="{{ Request('tanggal') }}" label="Tanggal Permintaan Pengiriman" icon="feather icon-calendar" datepicker />
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <div class="form-group">
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Semua Status</option>
                                        <option {{ (Request('status') === '0' ? 'selected' : '') }} value="0">Belum Di Proses</option>
                                        <option {{ (Request('status') == 1 ? 'selected' : '') }} value="1">Sudah Di Proses</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-12">
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
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pp as $d)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration + $pp->firstItem() - 1 }}</td>
                                    <td>{{ $d->no_permintaan_pengiriman }}</td>
                                    <td>{{ date("d-m-Y",strtotime($d->tgl_permintaan_pengiriman)) }}</td>
                                    <td>{{ $d->kode_cabang }}</td>
                                    <td>{{ $d->keterangan }}</td>
                                    <td>
                                        @if ($d->status==1)
                                        <span class="badge bg-success"><i class="feather icon-check"></i> Sudah Di Proses</span>
                                        @endif
                                    </td>
                                    <td>{{ $d->nama_karyawan }}</td>
                                    <td>
                                        @if ($d->status==0)
                                        <form method="POST" class="deleteform" action="/permintaanpengiriman/{{Crypt::encrypt($d->no_permintaan_pengiriman)}}/delete">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" class="delete-confirm ml-1">
                                                <i class="feather icon-trash danger"></i>
                                            </a>
                                        </form>
                                        @endif
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
<!-- Detail Salesman -->
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
                <form action="/permintaanpengiriman/store" id="frmPermintaanpengiriman">
                    <input type="text" id="cektemp" />
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
                                    <option value="{{ $c->kode_cabang}}">
                                        {{ $c->nama_cabang}}
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
                                <select name="kode_produk" id="kode_produk" class="form-control">
                                    <option value="">Pilih Barang</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <x-inputtext field="jumlah" label="Jumlah" icon="feather icon-file" right />
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <div class="form-group">
                                <a href="#" class="btn btn-primary" id="tambahbarang"><i class="feather icon-plus"></i> Tambah</a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-hover-animation">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Kode Produk</th>
                                        <th>Jumlah</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
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
@endsection

@push('myscript')
<script>
    $(function() {
        $('#inputpermintaan').click(function(e) {
            e.preventDefault();
            $('#mdlinputpengiriman').modal({
                backdrop: 'static'
                , keyboard: false
            });
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
            loadpilihsalesman();
        });

        $("#frmPermintaanpengiriman").submit(function() {
            var tgl_permintaan_pengiriman = $("#tgl_permintaan_pengiriman").val();
            var kode_cabang = $("#kode_cabang").val();
            var keterangan = $("#keterangan").val();
            var id_karyawan = $("#id_karyawan").val();
            var cektemp
            cektemp();
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
            }
        });
    });

</script>
@endpush
