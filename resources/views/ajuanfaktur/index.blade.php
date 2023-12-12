@extends('layouts.midone')
@section('titlepage', 'Data Ajuan Faktur')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Data Ajuan Faktur</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/limitkredit">Data Ajuan Faktur</a>
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
                    <div class="card-body">
                        <form action="/ajuanfaktur">
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
                                                    <option {{ Request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}
                                                        value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-lg-3 col-sm-12">
                                    <x-inputtext label="Nama Pelanggan" field="nama_pelanggan" icon="feather icon-user"
                                        value="{{ Request('nama_pelanggan') }}" />
                                </div>
                                <div class="col-lg-3 col-sm-12">
                                    <div class="form-group">
                                        <select name="status" id="status" class="form-control">
                                            <option value="">Semua Status Pengajuan</option>
                                            <option {{ Request('status') == 'pending' ? 'selected' : '' }} value="pending">
                                                BELUM DISETUJUI {{ Str::upper(Auth::user()->level) }}</option>
                                            <option {{ Request('status') == 'disetujui' ? 'selected' : '' }}
                                                value="disetujui">DISETUJUI {{ Str::upper(Auth::user()->level) }}</option>
                                            <option {{ Request('status') == 'ditolak' ? 'selected' : '' }} value="ditolak">
                                                DITOLAK {{ Str::upper(Auth::user()->level) }}</option>
                                        </select>
                                    </div>
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
                                        <th>Kode Pelanggan</th>
                                        <th>Pelanggan</th>
                                        <th>Jml Faktur</th>
                                        <th style="width:30%">Keterangan</th>
                                        <th>Histori</th>
                                        <th>KP</th>
                                        <th>RSM</th>
                                        <th>GM</th>
                                        <th>DIRUT</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ajuanfaktur as $d)
                                        <tr>
                                            <td>{{ $d->no_pengajuan }}</td>
                                            <td>{{ date('d-m-Y', strtotime($d->tgl_pengajuan)) }}</td>
                                            <td>{{ $d->kode_pelanggan }}</td>
                                            <td>{{ $d->nama_pelanggan }}</td>
                                            <td>{{ $d->jmlfaktur }}</td>
                                            <td>
                                                @if ($d->sikluspembayaran == 1)
                                                    <span class="badge bg-success">Pembayaran Saat Turun Barang Order
                                                        Selanjutnya</span>
                                                    <br>
                                                @endif
                                                {{ $d->keterangan }}
                                            </td>
                                            <td>
                                                <a href="/pelanggan/{{ Crypt::encrypt($d->kode_pelanggan) }}/show"><span
                                                        class="badge bg-success">Lihat Histori</span></a>
                                            </td>
                                            <td>
                                                @if (empty($d->kacab))
                                                    <i class="fa fa-history warning"></i>
                                                @elseif(
                                                    (!empty($d->kacab) && !empty($d->rsm) && $d->status == 2) ||
                                                        (!empty($d->kacab) && empty($d->rsm) && $d->status == 0) ||
                                                        (!empty($d->kacab) && empty($d->rsm) && $d->status == 1) ||
                                                        (!empty($d->kacab) && !empty($d->rsm) && $d->status == 0) ||
                                                        (!empty($d->kacab) && !empty($d->rsm) && $d->status == 1))
                                                    <i class="fa fa-check success"></i>
                                                @else
                                                    <i class="fa fa-close danger"></i>
                                                @endif
                                            </td>
                                            <td>
                                                @if (empty($d->rsm))
                                                    <i class="fa fa-history warning"></i>
                                                @elseif(
                                                    (!empty($d->rsm) && !empty($d->mm) && $d->status == 2) ||
                                                        (!empty($d->rsm) && empty($d->mm) && $d->status == 1) ||
                                                        (!empty($d->rsm) && empty($d->mm) && $d->status == 0) ||
                                                        (!empty($d->rsm) && !empty($d->mm) && $d->status == 0) ||
                                                        (!empty($d->rsm) && !empty($d->mm) && $d->status == 1))
                                                    <i class="fa fa-check success"></i>
                                                @else
                                                    <i class="fa fa-close danger"></i>
                                                @endif
                                            </td>
                                            <td>
                                                @if (empty($d->mm))
                                                    <i class="fa fa-history warning"></i>
                                                @elseif(
                                                    (!empty($d->mm) && !empty($d->dirut) && $d->status == 2) ||
                                                        (!empty($d->mm) && empty($d->dirut) && $d->status == 1) ||
                                                        (!empty($d->mm) && empty($d->dirut) && $d->status == 0) ||
                                                        (!empty($d->mm) && !empty($d->dirut) && $d->status == 0) ||
                                                        (!empty($d->mm) && !empty($d->dirut) && $d->status == 1))
                                                    <i class="fa fa-check success"></i>
                                                @else
                                                    <i class="fa fa-close danger"></i>
                                                @endif
                                            </td>
                                            <td>
                                                @if (empty($d->dirut))
                                                    <i class="fa fa-history warning"></i>
                                                @elseif(!empty($d->dirut) && $d->status != 2)
                                                    <i class="fa fa-check success"></i>
                                                @else
                                                    <i class="fa fa-close danger"></i>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    @if (in_array($level, $limitkredit_hapus))
                                                        @if (empty($d->rsm))
                                                            <a class="ml-1 editajuan" href="#"
                                                                no_pengajuan="{{ Crypt::encrypt($d->no_pengajuan) }}"><i
                                                                    class="feather icon-edit success"></i></a>
                                                            <form method="POST" name="deleteform" class="deleteform"
                                                                action="/ajuanfaktur/{{ Crypt::encrypt($d->no_pengajuan) }}/delete">
                                                                @csrf
                                                                @method('DELETE')
                                                                <a href="#" class="delete-confirm ml-1">
                                                                    <i class="feather icon-trash danger"></i>
                                                                </a>
                                                            </form>
                                                        @endif
                                                    @endif

                                                    {{-- //Approve Cabang --}}
                                                    @if (
                                                        ($level == 'kepala penjualan' && empty($d->kacab) && $d->status == 0) ||
                                                            ($level == 'kepala admin' && empty($d->kacab) && $d->status == 0) ||
                                                            ($level == 'kepala penjualan' && !empty($d->kacab) && empty($d->mm) && $d->status == 2) ||
                                                            ($level == 'kepala admin' && !empty($d->kacab) && empty($d->mm) && $d->status == 2) ||
                                                            ($level == 'kepala penjualan' && !empty($d->kacab) && empty($d->mm) && $d->status == 1) ||
                                                            ($level == 'kepala admin' && !empty($d->kacab) && empty($d->mm) && $d->status == 1) ||
                                                            ($level == 'kepala penjualan' && !empty($d->kacab) && empty($d->mm) && $d->status == 0) ||
                                                            ($level == 'kepala admin' && !empty($d->kacab) && empty($d->mm) && $d->status == 0))
                                                        <a class="ml-1"
                                                            href="/ajuanfaktur/{{ Crypt::encrypt($d->no_pengajuan) }}/approve"><i
                                                                class=" fa fa-check success"></i></a>
                                                        <a class="ml-1"
                                                            href="/ajuanfaktur/{{ Crypt::encrypt($d->no_pengajuan) }}/decline"><i
                                                                class=" fa fa-close danger"></i></a>
                                                    @endif


                                                    <!-- RSM -->
                                                    @if (
                                                        ($level == 'rsm' && !empty($d->kacab) && empty($d->rsm) && empty($d->mm) && $d->status == 0) ||
                                                            ($level == 'rsm' && !empty($d->kacab) && !empty($d->rsm) && empty($d->mm) && $d->status == 2) ||
                                                            ($level == 'rsm' && !empty($d->kacab) && !empty($d->rsm) && empty($d->mm) && $d->status == 0))
                                                        <a class="ml-1"
                                                            href="/ajuanfaktur/{{ Crypt::encrypt($d->no_pengajuan) }}/approve"><i
                                                                class=" fa fa-check success"></i></a>
                                                        <a class="ml-1"
                                                            href="/ajuanfaktur/{{ Crypt::encrypt($d->no_pengajuan) }}/decline"><i
                                                                class=" fa fa-close danger"></i></a>
                                                    @endif

                                                    <!-- General Manager -->

                                                    @if (
                                                        ($level == 'manager marketing' && !empty($d->rsm) && empty($d->mm) && empty($d->dirut) && $d->status == 0) ||
                                                            ($level == 'manager marketing' && !empty($d->rsm) && !empty($d->mm) && empty($d->dirut) && $d->status == 2) ||
                                                            ($level == 'manager marketing' && !empty($d->rsm) && !empty($d->mm) && empty($d->dirut) && $d->status == 0) ||
                                                            ($level == 'manager marketing' && !empty($d->rsm) && !empty($d->mm) && empty($d->dirut) && $d->status != 2))
                                                        <a class="ml-1"
                                                            href="/ajuanfaktur/{{ Crypt::encrypt($d->no_pengajuan) }}/approve"><i
                                                                class=" fa fa-check success"></i></a>
                                                        <a class="ml-1"
                                                            href="/ajuanfaktur/{{ Crypt::encrypt($d->no_pengajuan) }}/decline"><i
                                                                class=" fa fa-close danger"></i></a>
                                                    @endif

                                                    <!-- Direktur -->
                                                    @if (
                                                        ($level == 'direktur' && !empty($d->mm) && empty($d->dirut) && $d->status == 0) ||
                                                            ($level == 'direktur' && !empty($d->mm) && !empty($d->dirut) && $d->status == 2) ||
                                                            ($level == 'direktur' && !empty($d->mm) && !empty($d->dirut) && $d->status == 0) ||
                                                            ($level == 'direktur' && !empty($d->mm) && !empty($d->dirut) && $d->status != 2))
                                                        <a class="ml-1"
                                                            href="/ajuanfaktur/{{ Crypt::encrypt($d->no_pengajuan) }}/approve"><i
                                                                class=" fa fa-check success"></i></a>
                                                        <a class="ml-1"
                                                            href="/ajuanfaktur/{{ Crypt::encrypt($d->no_pengajuan) }}/decline"><i
                                                                class=" fa fa-close danger"></i></a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{-- {{ $limitkredit->links('vendor.pagination.vuexy') }} --}}
                        {{ $ajuanfaktur->links('vendor.pagination.vuexy') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="mdlajukanfaktur" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Edit Ajukan Faktur <span id="tglupdatepresensi"></span>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="loadformajukanfaktur">
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
                $("#mdlajukanfaktur").modal("show");
                // /alert(kode_pelanggan);
                $("#loadformajukanfaktur").load('/ajuanfaktur/' + no_pengajuan + '/edit');
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
