@extends('layouts.midone')
@section('titlepage','Data Ganti Logam ke Kertas')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Ganti Logam ke Kertas</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/logamtokertas">Ganti Logam ke Kertas</a>
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
        <div class="col-md-6 col-sm-8">
            <div class="card">

                <div class="card-header">
                    <a href="#" id="inputgantilogam" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>

                <div class="card-body">
                    <input type="hidden" id="cektutuplaporan">
                    <form action="/logamtokertas">
                        <div class="row">
                            <div class="col-6">
                                <x-inputtext field="dari" label="Dari" icon="feather icon-calendar" value="{{ Request('dari') }}" datepicker />
                            </div>
                            <div class="col-6">
                                <x-inputtext field="sampai" label="Sampai" icon="feather icon-calendar" value="{{ Request('sampai') }}" datepicker />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                                <div class="form-group">
                                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                                        <option value="">Pilih Cabang</option>
                                        @foreach ($cabang as $d)
                                        <option {{ Request('kode_cabang') == $d->kode_cabang ? 'selected' : '' }} value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                                <div class="form-group">
                                    <button type="submit" name="submit" value="1" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Cari</button>
                                </div>
                            </div>
                        </div>

                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jumlah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logamtokertas as $d)
                                <tr>
                                    <td>{{ $loop->iteration + $logamtokertas->firstItem()-1 }}</td>
                                    <td>{{ date("d-m-Y",strtotime($d->tgl_logamtokertas)) }}</td>
                                    <td class="right"> {{ desimal($d->jumlah_logamtokertas) }}</td>
                                    <td>
                                        <form method="POST" class="deleteform" action="/logamtokertas/{{Crypt::encrypt($d->kode_logamtokertas)}}/delete">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" tanggal="{{ $d->tgl_logamtokertas }}" class="delete-confirm ml-1">
                                                <i class="feather icon-trash danger"></i>
                                            </a>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Input uang Belum Setor -->
<div class="modal fade text-left" id="mdlinputgantilogam" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input Ganti Logam Ke Kertas</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputgantilogam"></div>
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

        $("#inputgantilogam").click(function(e) {
            e.preventDefault();
            $("#loadinputgantilogam").load('/logamtokertas/create');
            $('#mdlinputgantilogam').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });



    });

</script>
@endpush
