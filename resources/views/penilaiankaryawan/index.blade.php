@extends('layouts.midone')
@section('titlepage','Penilaian Karyawan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Penilaian Karyawan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/penilaiankaryawan">Penilaian Karyawan</a>
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
                <div class="card-header">
                    <a href="#" class="btn btn-primary" id="buatpenilaian"><i class="fa fa-plus mr-1"></i> Buat Penilaian</a>
                </div>
                <div class="card-body">
                    <form action="/penilaiankaryawan">
                        <div class="row">
                            <div class="col-lg-4 col-sm-12">
                                <x-inputtext label="Nama Karyawan" field="nama_karyawan" icon="feather icon-user" value="{{ Request('nama') }}" />
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
                                    <th>Kode Penilaian</th>
                                    <th>Tanggal</th>
                                    <th>Nik</th>
                                    <th>Nama Karyawan</th>
                                    <th>Periode</th>
                                    <th>Kategori</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        {{-- {{ $salesman->links('vendor.pagination.vuexy') }} --}}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Detail Salesman -->
<div class="modal fade text-left" id="mdlbuatpenilaian" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Buat Penilaian</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/penilaiankaryawan/create" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext label="Tanggal" field="tanggal" icon="feather icon-calendar" datepicker />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <x-inputtext label="Periode Dari" field="dari" icon="feather icon-calendar" datepicker />
                        </div>
                        <div class="col-6">
                            <x-inputtext label="Periode Sampai" field="sampai" icon="feather icon-calendar" datepicker />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <select name="nik" id="nik" class="form-control">
                                    <option value="">Pilih Karyawan</option>
                                    @foreach ($karyawan as $d)
                                    <option value="{{ $d->nik }}">{{ $d->nik }} {{ $d->nama_karyawan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <select name="kategori_penilaian" id="kategori_penilaian" class="form-control">
                                    <option value="">Kategori Penilaian</option>
                                    @foreach ($kategori_penilaian as $d)
                                    <option value="{{ $d->id }}">{{ $d->kategori_penilaian }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <button class="btn btn-primary btn-block" type="submit"><i class="feather icon-send mr-1"></i> Buat Penialian</button>
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
        $('#buatpenilaian').click(function(e) {
            e.preventDefault();
            $('#mdlbuatpenilaian').modal({
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
