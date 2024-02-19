@extends('layouts.midone')
@section('titlepage', 'Data Slip Gaji')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Data Slip Gaji</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/gaji">Data Slip Gaji</a>
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
                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <div class="card-header">
                                <a href="#" id="buatslipgaji" class="btn btn-primary"><i class="fa fa-plus mr-1"></i>
                                    Buat Slip Gaji</a>
                            </div>


                            <div class="card-body">

                                <div class="table-responsive">
                                    <table class="table table-hover-animation">
                                        <thead class="thead-dark text-center">
                                            <tr>
                                                <th rowspan="2">No</th>
                                                <th rowspan="2">Kode</th>
                                                <th rowspan="2">Bulan</th>
                                                <th rowspan="2">Tahun</th>
                                                <th rowspan="2">Status</th>
                                                <th>#</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($slipgaji as $d)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td class="text-center">{{ $d->kode_gaji }}</td>
                                                    <td class="text-center">{{ $namabulan[$d->bulan] }}</td>
                                                    <td class="text-center">{{ $d->tahun }}</td>
                                                    <td class="text-center">
                                                        @if ($d->status === '1')
                                                            <span class="badge bg-success">Published</span>
                                                        @else
                                                            <span class="badge bg-warning">Pending</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a class="ml-1"
                                                                href="/slipgaji/{{ Crypt::encrypt($d->kode_gaji) }}/setpenambahpengurang"><i
                                                                    class="feather icon-settings info"></i></a>
                                                            <a class="ml-1 edit"
                                                                kode_gaji="{{ Crypt::encrypt($d->kode_gaji) }}"
                                                                href="#"><i class="feather icon-edit success"></i></a>
                                                            <form method="POST" class="deleteform"
                                                                action="/slipgaji/{{ Crypt::encrypt($d->kode_gaji) }}/delete">
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

                                </div>

                                <!-- DataTable ends -->
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Data list view end -->
        </div>
    </div>


    <!-- Input Gaji -->
    <div class="modal fade text-left" id="mdlbuatslipgaji" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Buat Slip Gaji</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadbuatslipgaji"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="mdleditgaji" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Edit Slip Gaji</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadeditslipgaji"></div>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('myscript')
    <script>
        $(function() {
            $('#buatslipgaji').click(function(e) {
                e.preventDefault();
                $('#mdlbuatslipgaji').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadbuatslipgaji").load('/slipgaji/create');
            });

            $('.edit').click(function(e) {
                var kode_gaji = $(this).attr("kode_gaji");
                e.preventDefault();
                $('#mdleditgaji').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadeditslipgaji").load('/slipgaji/' + kode_gaji + '/edit');
            });


            $('.delete-confirm').click(function(event) {
                var form = $(this).closest("form");
                var name = $(this).data("name");
                event.preventDefault();
                swal({
                        title: `Are you sure you want to delete this record?`,
                        text: "If you delete this, it will be gone forever.",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            form.submit();
                        }
                    });
            });
        });
    </script>
@endpush
