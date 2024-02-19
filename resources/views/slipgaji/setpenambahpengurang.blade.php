@extends('layouts.midone')
@section('titlepage', 'Set Penambah Pengurang')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Set Penambah Pengurang</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/gaji">Set Penambah Pengurang</a>
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
                                <a href="#" id="tambahkaryawan" class="btn btn-primary"><i
                                        class="fa fa-plus mr-1"></i>
                                    Tambah Karyawan</a>
                            </div>


                            <div class="card-body">
                                <table class="table">
                                    <tr>
                                        <th>Kode Gaji</th>
                                        <td>{{ $slipgaji->kode_gaji }}</td>
                                    </tr>
                                    <tr>
                                        <th>Bulan</th>
                                        <td>{{ $namabulan[$slipgaji->bulan] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tahun</th>
                                        <td>{{ $slipgaji->tahun }}</td>
                                    </tr>
                                </table>
                                <div class="table-responsive">
                                    <table class="table table-hover-animation">
                                        <thead class="thead-dark text-center">
                                            <tr>
                                                <th rowspan="2">No</th>
                                                <th rowspan="2">NIK</th>
                                                <th rowspan="2">Nama</th>
                                                <th rowspan="2">Pengurang</th>
                                                <th rowspan="2">Penambah</th>
                                                <th>#</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tambahkurang as $d)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $d->nik }}</td>
                                                    <td>{{ $d->nama_karyawan }}</td>
                                                    <td class="text-right">{{ rupiah($d->jumlah) }}</td>
                                                    <td class="text-right">{{ rupiah($d->jumlah_penambah) }}</td>
                                                    <td>
                                                        @if ($slipgaji->status === '0')
                                                            <form method="POST" class="deleteform"
                                                                action="/slipgaji/{{ Crypt::encrypt($d->kode_gaji) }}/{{ Crypt::encrypt($d->nik) }}/deletepenambahpengurang">
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
    <div class="modal fade text-left" id="mdltambahkaryawan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Tambah Data</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadtambahkaryawan"></div>
                </div>
            </div>
        </div>
    </div>




@endsection

@push('myscript')
    <script>
        $(function() {
            $('#tambahkaryawan').click(function(e) {
                e.preventDefault();
                var kode_gaji = "{{ $slipgaji->kode_gaji }}";
                $('#mdltambahkaryawan').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadtambahkaryawan").load('/slipgaji/' + kode_gaji + '/tambahkaryawan');
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
