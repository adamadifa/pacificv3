@extends('layouts.midone')
@section('titlepage','Mutasi Kendaraan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Mutasi Kendaraan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/mutasikendaraan">Mutasi Kendaraan</a>
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
        <div class="col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <a href="/mutasikendaraan/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                <div class="card-body">
                    <form action="/kendaraan">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <select name="no_polisi" id="no_polisi" class="form-control select2">
                                        <option value="">Pilih No. Polisi / No. Kendaraan</option>
                                        @foreach ($kendaraan as $d)
                                        <option value="{{ $d->no_polisi }}">{{ $d->no_polisi. " ".$d->merk." ".$d->tipe_kendaraan. " ".$d->tipe }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>No. Mutasi</th>
                                    <th>No. Polisi</th>
                                    <th>Tgl Mutasi</th>
                                    <th>Asal Cabang</th>
                                    <th>Pindah Ke Cabang</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mutasikendaraan as $d)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $d->no_mutasi }}</td>
                                    <td>{{ $d->no_polisi }}</td>
                                    <td>{{ DateToIndo2($d->tgl_mutasi) }}</td>
                                    <td>{{ $d->kode_cabang_old }}</td>
                                    <td>{{ $d->kode_cabang_new }}</td>
                                    <td>{{ $d->keterangan }}</td>
                                    <td>
                                        <form method="POST" name="deleteform" class="deleteform" action="/mutasikendaraan/{{ Crypt::encrypt($d->no_mutasi) }}/delete">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" class="delete-confirm ml-1">
                                                <i class="feather icon-trash danger"></i>
                                            </a>
                                        </form>
                                    </td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                        {{ $mutasikendaraan->links('vendor.pagination.vuexy') }}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Detail Kendaraan -->

@endsection
@push('myscript')
<script>
    $(function() {

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
