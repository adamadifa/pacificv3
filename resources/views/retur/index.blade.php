@extends('layouts.midone')
@section('titlepage','Data Retur')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Retur</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/retur">Data Retur</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <input type="hidden" id="cektutuplaporan">
        <div class="col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <a href="/retur/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                <div class="card-body">
                    <form action="/retur">
                        <div class="row">
                            <div class="col-lg-6">
                                <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker value="{{ Request('dari') }}" />
                            </div>
                            <div class="col-lg-6">
                                <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker value="{{ Request('sampai') }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-sm-12">
                                <x-inputtext label="No Faktur" field="no_fak_penj" icon="feather icon-credit-card" value="{{ Request('no_fak_penj') }}" />
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <x-inputtext label="Nama Pelanggan" field="nama_pelanggan" icon="feather icon-user" value="{{ Request('nama_pelanggan') }}" />
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <div class="form-group">
                                    <select name="jenis_retur" id="status" class="form-control">
                                        <option value="">Jenis Retur</option>
                                        <option {{ (Request('jenis_retur')=='BG' ? 'selected':'')}} value="GB">Ganti Barang</option>
                                        <option {{ (Request('jenis_retur')=='PF' ? 'selected':'')}} value="PF">Potong Faktur</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-2 col-sm-12">
                                <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i> Cari Data </button>
                            </div>
                        </div>
                    </form>
                    @include('layouts.notification')
                    <table class="table table-hover-animation">
                        <thead class="thead-dark">
                            <tr>
                                <th>No Retur</th>
                                <th>No Faktur</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Salesman</th>
                                <th>Cabang</th>
                                <th>Jenis Retur</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($retur as $d)
                            <tr>
                                <td>{{ $d->no_retur_penj }}</td>
                                <td>{{ $d->no_fak_penj }}</td>
                                <td>{{ date("d-m-Y",strtotime($d->tglretur)) }}</td>
                                <td>{{ $d->nama_pelanggan }}</td>
                                <td>{{ $d->nama_karyawan }}</td>
                                <td>{{ $d->kode_cabang }}</td>
                                <td>
                                    @if ($d->jenis_retur=="pf")
                                    <span class="badge bg-danger">PF</span>
                                    @else
                                    <span class="badge bg-success">GB</span>
                                    @endif
                                </td>
                                <td class="text-right">{{ rupiah($d->subtotal_pf) }}</td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <a class="ml-1 detailretur" href="#" no_retur_penj="{{ $d->no_retur_penj }}"><i class=" feather icon-file-text info"></i></a>
                                        <form method="POST" name="deleteform" class="deleteform" action="/retur/{{ Crypt::encrypt($d->no_retur_penj) }}/delete">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" tanggal="{{ $d->tglretur }}" class="delete-confirm ml-1">
                                                <i class="feather icon-trash danger"></i>
                                            </a>
                                        </form>
                                    </div>

                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $retur->links('vendor.pagination.vuexy') }}

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Detail Retur -->
<div class="modal fade text-left" id="mdldetailretur" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail Retur <span id="no_retur_penj"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaddetailretur"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {
        function cektutuplaporan(tanggal) {
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                    , jenislaporan: "penjualan"
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

        function loaddetailretur(no_retur_penj) {
            $.ajax({
                type: 'POST'
                , url: '/retur/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_retur_penj: no_retur_penj
                }
                , cache: false
                , success: function(respond) {
                    $("#no_retur_penj").text(no_retur_penj);
                    $("#loaddetailretur").html(respond);
                }
            });
        }
        $('.detailretur').click(function(e) {
            var no_retur_penj = $(this).attr("no_retur_penj");
            e.preventDefault();
            loaddetailretur(no_retur_penj);
            $('#mdldetailretur').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });
    });

</script>
@endpush
