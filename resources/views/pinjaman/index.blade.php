@extends('layouts.midone')
@section('titlepage','Pinjaman Karyawan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Pinjaman Karyawan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/pinjaman">Pinjaman Karyawan</a>
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
        <div class="card">
            <div class="card-body">
                <form action="/pinjaman">
                    <div class="row">

                        <div class="col-lg-3 col-sm-12">
                            <div class="form-group  ">
                                <select name="kode_cabang" id="" class="form-control">
                                    @if (Auth::user()->kode_cabang=="PCF")
                                    <option value="">Semua Cabang</option>
                                    @else
                                    <option value="">Pilih Cabang</option>
                                    @endif
                                    @foreach ($cabang as $c)
                                    <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <div class="form-group">
                                <select name="kode_dept" id="kode_dept" class="form-control">
                                    <option value="">Departemen</option>
                                    @foreach ($departemen as $d)
                                    <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                    @endforeach
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
                                <th>No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Satuan</th>
                                <th>Harga/Dus</th>
                                <th>Harga/Pack</th>
                                <th>Harga/Pcs</th>
                                <th>Retur/Dus</th>
                                <th>Retur/Pack</th>
                                <th>Retur/Pcs</th>
                                <th>Kategori Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>


                        </tbody>
                    </table>
                    {{ $pinjaman->links('vendor.pagination.vuexy') }}
                </div>

                <!-- DataTable ends -->
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>

<!-- Detail Barang -->
<div class="modal fade text-left" id="mdldetailbarang" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail Barang</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaddetailbarang"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {

        function loaddetailbarang(kode_barang) {
            $.ajax({
                type: 'POST'
                , url: '/harga/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_barang: kode_barang
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailbarang").html(respond);
                }
            });
        }
        $('.detailbarang').click(function(e) {
            var kode_barang = $(this).attr("kodebarang");
            e.preventDefault();
            loaddetailbarang(kode_barang);
            $('#mdldetailbarang').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });
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
    });

</script>
@endpush
