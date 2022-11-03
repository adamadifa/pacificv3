@extends('layouts.midone')
@section('titlepage','Bad Stock')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Bad Stock</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/badstok">Bad Stock</a>
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
        <div class="col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <a href="/badstok/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                <div class="card-body">
                    <form action="/badstok">

                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>No. BS</th>
                                    <th>Tanggal</th>
                                    <th>Cabang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($badstok as $d)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration + $badstok->firstItem() - 1 }}</td>
                                    <td>{{ $d->no_bs }}</td>
                                    <td>{{ DateToIndo2($d->tanggal) }}</td>
                                    <td>{{ $d->kode_cabang }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="#" class="info detail" no_bs="{{ $d->no_bs }}"><i class="feather icon-file"></i></a>
                                            <form method="POST" name="deleteform" class="deleteform" action="/badstok/{{ Crypt::encrypt($d->no_bs) }}/delete">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" class="delete-confirm ml-1">
                                                    <i class="feather icon-trash danger"></i>
                                                </a>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- {{ $badstok->links('vendor.pagination.vuexy') }} --}}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Detail Kendaraan -->
<!-- Detail Kendaraan -->
<div class="modal fade text-left" id="mdldetailbadstok" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail Bad Stok</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaddetailbadstok"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        function loaddetail(no_bs) {
            $.ajax({
                type: 'POST'
                , url: '/badstok/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_bs: no_bs
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailbadstok").html(respond);
                }
            });
        }
        $('.detail').click(function(e) {
            var no_bs = $(this).attr("no_bs");

            e.preventDefault();
            loaddetail(no_bs);
            $('#mdldetailbadstok').modal({
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
