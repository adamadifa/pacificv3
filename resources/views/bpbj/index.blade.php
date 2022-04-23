@extends('layouts.midone')
@section('titlepage','Bukti Penyerhaan Barang Jadi (BPBJ)')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">BPBJ</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/bpbj">BPBJ</a>
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
        <div class="col-lg-5 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form action="/bpbj">

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
                                <th>No. BPBJ</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bpbj as $d)
                            <tr>
                                <td class="text-center">{{ $loop->iteration + $bpbj->firstItem() - 1 }}</td>
                                <td>{{ $d->no_mutasi_produksi }}</td>
                                <td>{{ date("d-m-Y",strtotime($d->tgl_mutasi_produksi)) }}</td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <a class="ml-1 detailbpbj" href="#" no_mutasi_produksi="{{ Crypt::encrypt($d->no_mutasi_produksi) }}"><i class=" feather icon-file-text info"></i></a>
                                        <form method="POST" class="deleteform" action="/bpbj/{{Crypt::encrypt($d->no_mutasi_produksi)}}/delete">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" tanggal="{{ $d->tgl_mutasi_produksi }}" class="delete-confirm ml-1">
                                                <i class="feather icon-trash danger"></i>
                                            </a>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $bpbj->links('vendor.pagination.vuexy') }}


                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Detail Salesman -->
<div class="modal fade text-left" id="mdldetailbpbj" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail BPBJ</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaddetailbpbj"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {

        function loaddetailbpbj(no_mutasi_produksi) {
            $.ajax({
                type: 'POST'
                , url: '/bpbj/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_mutasi_produksi: no_mutasi_produksi
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailbpbj").html(respond);
                }
            });
        }
        $(".detailbpbj").click(function(e) {
            e.preventDefault();
            var no_mutasi_produksi = $(this).attr("no_mutasi_produksi");
            loaddetailbpbj(no_mutasi_produksi);
            $('#mdldetailbpbj').modal({
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
