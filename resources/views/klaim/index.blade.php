@extends('layouts.midone')
@section('titlepage', 'Klaim Kas Kecil')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Klaim Kas Kecil</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/klaim">Klaim Kas Kecil</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        @if (in_array($level, $klaim_add))
                            <a href="/klaim/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Buat Klaim</a>
                        @endif
                    </div>
                    <div class="card-body">
                        <form action="/klaim" id="frmcari">
                            <div class="row">
                                <div class="col-lg-6 col-sm-12">
                                    <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker
                                        value="{{ Request('dari') }}" />
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker
                                        value="{{ Request('sampai') }}" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form-group  ">
                                        <select name="kode_cabang" id="kode_cabang" class="form-control">
                                            <option value="">Pilih Cabang</option>
                                            @foreach ($cabang as $c)
                                                @if ($c->kode_cabang == 'PCF')
                                                    @php
                                                        $kode_cabang = 'PST';
                                                        $nama_cabang = 'PUSAT';
                                                    @endphp
                                                @else
                                                    @php
                                                        $kode_cabang = $c->kode_cabang;
                                                        $nama_cabang = $c->nama_cabang;
                                                    @endphp
                                                @endif
                                                <option {{ Request('kode_cabang') == $kode_cabang ? 'selected' : '' }}
                                                    value="{{ $kode_cabang }}">{{ strtoupper($nama_cabang) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <button type="submit" name="submit" value="1" class="btn btn-primary"><i
                                            class="fa fa-search"></i> Cari Data </button>
                                </div>
                            </div>
                        </form>
                        @include('layouts.notification')
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Kode Klaim</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>No. Bukti</th>
                                    <th>Tgl Proses</th>
                                    <th>Status Validasi</th>
                                    <th>Jumlah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($klaim as $d)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $d->kode_klaim }}</td>
                                        <td>{{ date('d-m-Y', strtotime($d->tgl_klaim)) }}</td>
                                        <td>{{ $d->keterangan }}</td>
                                        <td>
                                            @if ($d->status == 1)
                                                <span class="badge bg-success"><i class="fa fa-check mr-1"></i> Sudah di
                                                    Process</span>
                                            @else
                                                <span class="badge bg-danger"><i class="fa fa-history mr-1"></i> Belum di
                                                    Process</span>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-info">{{ $d->no_bukti }}</span></td>
                                        <td>{{ !empty($d->tgl_ledger) ? date('d-m-Y', strtotime($d->tgl_ledger)) : '' }}
                                        </td>
                                        <td>
                                            @if ($d->status_validasi == 1)
                                                <span class="badge bg-success"><i class="fa fa-check mr-1"></i> Sudah di
                                                    Validasi</span>
                                            @else
                                                <span class="badge bg-danger"><i class="fa fa-history mr-1"></i> Belum di
                                                    Validasi</span>
                                            @endif
                                        </td>
                                        <td align="right">
                                            @if ($d->status != 1)
                                                <span class="badge bg-warning"><i class="fa fa-history"></i></span>
                                            @else
                                                {{ rupiah($d->jumlah) }}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group" aria-label="Basic example">

                                                <a class="ml-1"
                                                    href="/klaim/{{ Crypt::encrypt($d->kode_klaim) }}/false/cetak"
                                                    target="_blank"><i class=" feather icon-printer primary"></i></a>
                                                <a class="ml-1"
                                                    href="/klaim/{{ Crypt::encrypt($d->kode_klaim) }}/true/cetak"
                                                    target="_blank"><i class=" feather icon-download success"></i></a>
                                                <a class="ml-1 detailklaim" href="#"
                                                    kodeklaim="{{ Crypt::encrypt($d->kode_klaim) }}"><i
                                                        class=" feather icon-file-text info"></i></a>

                                                @if ($d->status != 1)
                                                    @if (in_array($level, $klaim_proses))
                                                        <a class="ml-1 prosesklaim" href="#"
                                                            kodeklaim="{{ Crypt::encrypt($d->kode_klaim) }}"><i
                                                                class=" feather icon-send success"></i></a>
                                                    @endif

                                                    @if (in_array($level, $klaim_hapus))
                                                        <form method="POST" class="deleteform"
                                                            action="/klaim/{{ Crypt::encrypt($d->kode_klaim) }}/delete">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a href="#" class="delete-confirm ml-1">
                                                                <i class="feather icon-trash danger"></i>
                                                            </a>
                                                        </form>
                                                    @endif
                                                @else
                                                    @if ($d->status_validasi != 1)
                                                        @if (in_array($level, $klaim_proses))
                                                            <a class="ml-1"
                                                                href="/klaim/{{ Crypt::encrypt($d->kode_klaim) }}/batalkanproses"><i
                                                                    class="fa fa-close danger"></i></a>
                                                        @endif
                                                        @if (in_array($level, $klaim_validasi))
                                                            <a class="ml-1"
                                                                href="/klaim/{{ Crypt::encrypt($d->kode_klaim) }}/validasikaskecil"><i
                                                                    class="fa fa-check success"></i></a>
                                                        @endif
                                                    @else
                                                        @if (in_array($level, $klaim_validasi))
                                                            <a class="ml-1"
                                                                href="/klaim/{{ Crypt::encrypt($d->no_bukti) }}/batalkanvalidasi"><i
                                                                    class="fa fa-close danger"></i></a>
                                                        @endif
                                                    @endif
                                                @endif


                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Kas Kecil -->
    <div class="modal fade text-left" id="mdldetailklaim" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog " style="max-width:960px" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Detail Klaim</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loaddetailklaim"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        $(function() {
            $(".detailklaim").click(function(e) {
                e.preventDefault();
                var kode_klaim = $(this).attr("kodeklaim");
                $('#mdldetailklaim').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loaddetailklaim").load('/klaim/' + kode_klaim + '/show');
            });

            $(".prosesklaim").click(function(e) {
                e.preventDefault();
                var kode_klaim = $(this).attr("kodeklaim");
                $('#mdldetailklaim').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loaddetailklaim").load('/klaim/' + kode_klaim + '/prosesklaim');
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
