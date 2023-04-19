@extends('layouts.midone')
@section('titlepage','Data Master Insentif')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Master Insentif</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/insentif">Data Master Insentif</a>
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
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        @if (in_array($level,$insentif_tambah))
                        <div class="card-header">
                            <a href="#" id="tambahinsentif" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                        </div>
                        @endif

                        <div class="card-body">
                            <form action="/gaji">
                                <div class="row">
                                    <div class="col-lg-10 col-sm-10">
                                        <x-inputtext label="Nama Karyawan" field="nama_karyawan_search" icon="feather icon-users" value="{{ Request('nama_karyawan_search') }}" />
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-search mr-1"></i> Cari</button>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-hover-animation">
                                    <thead class="thead-dark text-center">
                                        <tr>
                                            <th rowspan="2">No</th>
                                            <th rowspan="2">Kode</th>
                                            <th rowspan="2">NIK</th>
                                            <th rowspan="2">Nama Karyawan</th>
                                            <th rowspan="2">Jabatan</th>
                                            <th colspan="4">Insentif Umum</th>
                                            <th colspan="3">Insentif Manager</th>
                                            <th rowspan="2">Aksi</th>
                                        </tr>
                                        <tr>
                                            <th>Masa Kerja</th>
                                            <th>Lembur</th>
                                            <th>Penempatan</th>
                                            <th>KPI</th>
                                            <th>Ruang Lingkup</th>
                                            <th>Penempatan</th>
                                            <th>Kinerja</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($insentif as $d)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration + $insentif->firstItem()-1 }}</td>
                                            <td>{{ $d->kode_insentif }}</td>
                                            <td>{{ $d->nik }}</td>
                                            <td>{{ $d->nama_karyawan }}</td>
                                            <td>{{ $d->nama_jabatan }}</td>
                                            <td class="text-right">{{ rupiah($d->iu_masakerja) }}</td>
                                            <td class="text-right">{{ rupiah($d->iu_lembur) }}</td>
                                            <td class="text-right">{{ rupiah($d->iu_penempatan) }}</td>
                                            <td class="text-right">{{ rupiah($d->iu_kpi) }}</td>
                                            <td class="text-right">{{ rupiah($d->im_ruanglingkup) }}</td>
                                            <td class="text-right">{{ rupiah($d->im_penempatan) }}</td>
                                            <td class="text-right">{{ rupiah($d->im_kinerja) }}</td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    @if (in_array($level,$insentif_edit))
                                                    <a class="ml-1 edit" kode_insentif="{{ Crypt::encrypt($d->kode_insentif) }}" href="#"><i class="feather icon-edit success"></i></a>
                                                    @endif
                                                    @if (in_array($level,$insentif_hapus))
                                                    <form method="POST" class="deleteform" action="/insentif/{{Crypt::encrypt($d->kode_insentif)}}/delete">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a href="#" class="delete-confirm ml-1">
                                                            <i class="feather icon-trash danger"></i>
                                                        </a>
                                                    </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $insentif->links('vendor.pagination.vuexy') }}
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
<div class="modal fade text-left" id="mdlinputinsentif" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Tambah Insentif</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputinsentif"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdleditinsentif" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit Master Insentif</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadeditinsentif"></div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('myscript')
<script>
    $(function() {
        $('#tambahinsentif').click(function(e) {
            e.preventDefault();
            $('#mdlinputinsentif').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadinputinsentif").load('/insentif/create');
        });

        $('.edit').click(function(e) {
            var kode_insentif = $(this).attr("kode_insentif");
            e.preventDefault();
            $('#mdleditinsentif').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadeditinsentif").load('/insentif/' + kode_insentif + '/edit');
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
