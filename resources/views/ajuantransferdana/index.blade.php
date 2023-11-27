@extends('layouts.midone')
@section('titlepage', 'Data Ajuan Transfer Dana')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Data Ajuan Transfer Dana</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/ajuantransferdana">Data Ajuan Transfer Dana</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <input type="hidden" id="cektutuplaporan">
            <div class="col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        @if (in_array($level, $ajuantransferdana_crud))
                            <a href="#" class="btn btn-primary" id="tambahdata"><i class="fa fa-plus mr-1"></i> Tambah
                                Data</a>
                        @endif

                    </div>
                    <div class="card-body">
                        <form action="/ajuantransferdana">
                            <div class="row">
                                <div class="col-lg-6">
                                    <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker
                                        value="{{ Request('dari') }}" />
                                </div>
                                <div class="col-lg-6">
                                    <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker
                                        value="{{ Request('sampai') }}" />
                                </div>
                            </div>
                            <div class="row">
                                @if (Auth::user()->kode_cabang == 'PCF')
                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group  ">
                                            <select name="kode_cabang" id="kode_cabang" class="form-control">
                                                <option value="">Semua Cabang</option>
                                                @foreach ($cabang as $c)
                                                    <option
                                                        {{ Request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}
                                                        value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-lg-3 col-sm-12">
                                    <x-inputtext label="Nama" field="nama_penerima" icon="feather icon-user"
                                        value="{{ Request('nama') }}" />
                                </div>


                                <div class="col-lg-2 col-sm-12">
                                    <button type="submit" name="submit" value="1" class="btn btn-primary w-100"><i
                                            class="fa fa-search"></i> Cari Data </button>
                                </div>
                            </div>
                        </form>
                        @include('layouts.notification')

                        <div class="table-responsive mt-2">
                            <table class="table table-hover-animation ">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No.Pengajuan</th>
                                        <th style="width: 10%">Tanggal</th>
                                        <th>Nama</th>
                                        <th>Bank</th>
                                        <th>No. Rekening</th>
                                        <th>Jumlah</th>
                                        <th>Keterangan</th>
                                        <th>Cabang</th>
                                        <th>Tgl Proses</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ajuantransferdana as $d)
                                        <tr>
                                            <td>{{ $d->no_pengajuan }}</td>
                                            <td>{{ date('d-m-Y', strtotime($d->tgl_pengajuan)) }}</td>
                                            <td>{{ $d->nama }}</td>
                                            <td>{{ $d->nama_bank }}</td>
                                            <td>{{ $d->no_rekening }}</td>
                                            <td>{{ rupiah($d->jumlah) }}</td>
                                            <td>{{ $d->keterangan }}</td>
                                            <td>{{ $d->kode_cabang }}</td>
                                            <td>
                                                @if (empty($d->tgl_proses))
                                                    <span class="badge bg-danger">Belum di Proses</span>
                                                @else
                                                    <span
                                                        class="badge bg-success">{{ date('d-m-y', strtotime($d->tgl_proses)) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">

                                                    @if (empty($d->tgl_proses))
                                                        @if (in_array($level, $ajuantransferdana_crud))
                                                            <a class="ml-1 editajuan" href="#"
                                                                no_pengajuan="{{ Crypt::encrypt($d->no_pengajuan) }}"><i
                                                                    class="feather icon-edit success"></i></a>
                                                            <form method="POST" name="deleteform" class="deleteform"
                                                                action="/ajuanrouting/{{ Crypt::encrypt($d->no_pengajuan) }}/delete">
                                                                @csrf
                                                                @method('DELETE')
                                                                <a href="#" class="delete-confirm ml-1">
                                                                    <i class="feather icon-trash danger"></i>
                                                                </a>

                                                            </form>
                                                        @endif
                                                        @if (in_array($level, $ajuantransferdana_proses))
                                                            <a class="ml-1 prosesajuan" href="#"
                                                                no_pengajuan="{{ Crypt::encrypt($d->no_pengajuan) }}"><i
                                                                    class="feather icon-external-link info"></i></a>
                                                        @endif
                                                    @else
                                                        @if (in_array($level, $ajuantransferdana_proses))
                                                            <a href="/ajuantransferdana/{{ Crypt::encrypt($d->no_pengajuan) }}/batalkan"
                                                                class="ml-1"><span
                                                                    class="badge bg-danger">Batalkan</span></a>
                                                        @endif
                                                    @endif

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{-- {{ $ajuanrouting->links('vendor.pagination.vuexy') }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="mdlajuantransferdana" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Ajuan Transfer Dana
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="loadformajuantransferdana">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        $(function() {

            $(".editajuan").click(function(e) {
                e.preventDefault();
                var no_pengajuan = $(this).attr("no_pengajuan");
                $("#mdlajuantransferdana").modal("show");
                // /alert(kode_pelanggan);
                $("#loadformajuantransferdana").load('/ajuantransferdana/' + no_pengajuan + '/edit');
            });


            $(".prosesajuan").click(function(e) {
                e.preventDefault();
                var no_pengajuan = $(this).attr("no_pengajuan");
                $("#mdlajuantransferdana").modal("show");
                // /alert(kode_pelanggan);
                $("#loadformajuantransferdana").load('/ajuantransferdana/' + no_pengajuan + '/prosesajuan');
            });

            $("#tambahdata").click(function(e) {
                e.preventDefault();
                $("#mdlajuantransferdana").modal("show");
                // /alert(kode_pelanggan);
                $("#loadformajuantransferdana").load('/ajuantransferdana/create');
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
