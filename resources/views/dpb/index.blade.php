@extends('layouts.midone')
@section('titlepage','Data Pengambilan Barang (DPB)')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Pengambilan Barang (DPB)</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/dpb">Data Pengambilan Barang (DPB)</a>
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
        <div class="col-md-12 col-sm-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <a href="#" class="btn btn-primary" id="inputdpb"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                <div class="card-body">
                    <form action="/dpb">
                        <div class="row">
                            <div class="col-lg-6">
                                <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker value="{{ Request('dari') }}" />
                            </div>
                            <div class="col-lg-6">
                                <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker value="{{ Request('sampai') }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-sm-12">
                                <x-inputtext label="No. DPB" field="no_dpb" icon="fa fa-barcode" value="{{ Request('no_dpb') }}" />
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <div class="form-group  ">
                                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                                        @if (Auth::user()->kode_cabang!="PCF")
                                        <option value="">Pilih Cabang</option>
                                        @else
                                        <option value="">Semua Cabang</option>
                                        @endif
                                        @foreach ($cabang as $c)
                                        <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <div class="form-group  ">
                                    <select name="id_karyawan" id="id_karyawan" class="form-control">
                                        <option value="">Semua Salesman</option>
                                    </select>
                                </div>
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
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No. DPB</th>
                                    <th>Tanggal</th>
                                    <th>Salesman</th>
                                    <th>Cabang</th>
                                    <th style="width: 10%">Tujuan</th>
                                    <th>No. Kendaraan</th>
                                    <th>Driver</th>
                                    <th>Helper 1</th>
                                    <th>Helper 2</th>
                                    <th>Helper 3</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dpb as $d)
                                <tr>
                                    <td>{{ $d->no_dpb }}</td>
                                    <td>{{ date("d-m-Y",strtotime($d->tgl_pengambilan)) }}</td>
                                    <td>{{ $d->nama_karyawan }}</td>
                                    <td>{{ $d->kode_cabang }}</td>
                                    <td>{{ $d->tujuan }}</td>
                                    <td>{{ $d->no_kendaraan }}</td>
                                    <td>{{ $d->nama_driver }}</td>
                                    <td>{{ $d->nama_helper_1 }}</td>
                                    <td>{{ $d->nama_helper_2 }}</td>
                                    <td>{{ $d->nama_helper_3 }}</td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a class="ml-1 edit" no_dpb="{{ Crypt::encrypt($d->no_dpb) }}" href="#"><i class="feather icon-edit success"></i></a>
                                            <a class="ml-1 detail" href="#" no_dpb="{{ Crypt::encrypt($d->no_dpb) }}"><i class=" feather icon-file-text info"></i></a>
                                            <form method="POST" class="deleteform" action="/dpb/{{Crypt::encrypt($d->no_dpb)}}/delete">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" tanggal="{{ $d->tgl_pengambilan }}" class="delete-confirm ml-1">
                                                    <i class="feather icon-trash danger"></i>
                                                </a>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                        {{ $dpb->links('vendor.pagination.vuexy') }}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Detail DPB -->
<div class="modal fade text-left" id="mdldetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail DPB</h4>
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
<!-- Detail DPB -->
<div class="modal fade text-left" id="mdlinput" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Buat DPB</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinput"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="mdledit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Update DPB</h4>
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
        var kode_cabang = $("#kode_cabang").val();
        loadsalesmancabang(kode_cabang);

        function loadsalesmancabang(kode_cabang) {
            var id_karyawan = "{{ Request('id_karyawan') }}";
            $.ajax({
                type: 'POST'
                , url: '/salesman/getsalescab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , id_karyawan: id_karyawan
                }
                , cache: false
                , success: function(respond) {
                    $("#id_karyawan").html(respond);
                }
            });
        }

        $("#kode_cabang").change(function() {
            var kode_cabang = $(this).val();
            loadsalesmancabang(kode_cabang);
        });

        $(".detail").click(function(e) {
            var no_dpb = $(this).attr("no_dpb");
            e.preventDefault();
            $("#loaddetail").load("/dpb/" + no_dpb + "/show");
            $('#mdldetail').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        $("#inputdpb").click(function(e) {
            e.preventDefault();
            $("#loadinput").load("/dpb/create");
            $('#mdlinput').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        $(".edit").click(function(e) {
            e.preventDefault();
            var no_dpb = $(this).attr("no_dpb");
            $("#loadedit").load("/dpb/" + no_dpb + "/edit");
            $('#mdledit').modal({
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
                    , jenislaporan: "gudangcabang"
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#cektutuplaporan").val(respond);
                }
            });
        }
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
