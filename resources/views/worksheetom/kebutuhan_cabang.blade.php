@extends('layouts.midone')
@section('titlepage', 'Kebutuhan Cabang')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Kebutuhan Cabang</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Kebutuhan Cabang</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <input type="hidden" id="cektutuplaporan">
            <div class="row">
                <div class="col-10">
                    <div class="card">
                        <div class="card-header">
                            <a href="#" class="btn btn-primary" id="tambahkebutuhancabang">
                                <i class="fa fa-plus mr-1"></i>
                                Tambah Data
                            </a>
                        </div>
                        <div class="card-body">
                            <form action="{{ URL::current() }}">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <select name="kode_jenis_kebutuhan" id="kode_jenis_kebutuhan"
                                                class="form-control">
                                                <option value="">Jenis Kebutuhan
                                                </option>
                                                @foreach ($jenis_kebutuhan as $d)
                                                    <option
                                                        {{ Request('kode_jenis_kebutuhan') == $d->kode_jenis_kebutuhan ? 'selected' : '' }}
                                                        value="{{ $d->kode_jenis_kebutuhan }}">{{ $d->jenis_kebutuhan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @if (Auth::user()->kode_cabang == 'PCF')
                                        <dvi class="col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <select name="kode_cabang" id="kode_cabang" class="form-control">
                                                    <option value="">Semua Cabang</option>
                                                    @foreach ($cabang as $d)
                                                        <option value="{{ $d->kode_cabang }}"
                                                            {{ Request('kode_cabang') == $d->kode_cabang ? 'selected' : '' }}>
                                                            {{ $d->nama_cabang }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </dvi>
                                    @endif
                                    <div class="col-lg-2 col-sm-2">
                                        <div class="btn-group">
                                            <button type="submit" name="submit" value="1" class="btn btn-primary">
                                                <i class="fa fa-search"></i>
                                            </button>
                                            <button type="submit" name="cetak" value="1" formtarget="_blank"
                                                class="btn btn-success">
                                                <i class="feather icon-printer"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            @include('layouts.notification')
                            <table class="table table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th>Jenis Kebutuhan</th>
                                        <th>Uraian</th>
                                        <th>Periode Akhir</th>
                                        <th>Sisa Waktu</th>
                                        <th>Cabang</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                @foreach ($kc as $d)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $d->jenis_kebutuhan }}</td>
                                        <td>{!! $d->uraian_kebutuhan !!}</td>
                                        <td>{{ DateToIndo2($d->periode_akhir) }}</td>
                                        <td>
                                            @php
                                                $start = date_create(date('Y-m-d')); //Tanggal Masuk Kerja
                                                $end = date_create($d->periode_akhir); // Tanggal Presensi
                                                $diff = date_diff($start, $end); //Hitung Masa Kerja
                                                $sisahari = $diff->days; // Value Masa Kerja
                                            @endphp
                                            {{ $sisahari }}
                                        </td>
                                        <td>{{ $d->kode_cabang }}</td>
                                        <td>
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                <a href="#" class="edit ml-1"
                                                    kode_kebutuhan="{{ $d->kode_kebutuhan }}">
                                                    <i class="feather icon-edit success"></i>
                                                </a>
                                                <form method="POST" name="deleteform" class="deleteform"
                                                    action="/worksheetom/{{ Crypt::encrypt($d->kode_kebutuhan) }}/deletekebutuhancabang">
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
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="mdlcreatekebutuhancabang" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Kebutuhan Cabang</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadcreatekebutuhancabang"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="mdleditkebutuhan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Edit Kebutuhan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadeditkebutuhan"></div>
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
            $("#tambahkebutuhancabang").click(function(e) {
                e.preventDefault();
                $('#mdlcreatekebutuhancabang').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadcreatekebutuhancabang").load('/worksheetom/createkebutuhancabang')
            });

            $(".edit").click(function(e) {
                e.preventDefault();
                var kode_kebutuhan = $(this).attr('kode_kebutuhan');
                $('#mdleditkebutuhan').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadeditkebutuhan").load('/worksheetom/' + kode_kebutuhan + '/editkebutuhancabang');
            });
        });
    </script>
@endpush
