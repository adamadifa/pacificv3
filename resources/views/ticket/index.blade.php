@extends('layouts.midone')
@section('titlepage', 'Data Maintenance Portal')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Data Maintenance Portal</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Data Maintenance Portal</a>
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
                        <a href="#" class="btn btn-primary" id="inputticket"><i class="fa fa-plus mr-1"></i> Tambah
                            Data</a>
                    </div>

                    <div class="card-body">
                        <form action="#">
                            <div class="row">
                                <div class="col-6">
                                    <x-inputtext field="dari" label="Dari" icon="feather icon-calendar" datepicker
                                        value="{{ Request('dari') }}" />
                                </div>
                                <div class="col-6">
                                    <x-inputtext field="sampai" label="Sampai" icon="feather icon-calendar" datepicker
                                        value="{{ Request('sampai') }}" />
                                </div>
                            </div>
                            {{-- <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <select name="status" id="status" class="form-control">
                                            <option value="">Semua Status</option>
                                            <option value="pending">Pending</option>
                                            <option value="disetujui">Disetujui</option>
                                            <option value="selesai">Selesai</option>
                                        </select>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <button class="btn btn-block btn-primary"><i
                                                class="fa fa-search mr-1"></i>Cari</button>
                                    </div>
                                </div>
                            </div>

                        </form>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th>No Pengajuan</th>
                                        <th>Tanggal</th>
                                        <th>Pemohon</th>
                                        <th style="width: 30%">Keterangan</th>
                                        <th>GM</th>
                                        <th>Direktur</th>
                                        <th>Status</th>
                                        <th>Tgl Selesai</th>
                                        <th>Diselesaikan Oleh</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ticket as $d)
                                        <tr>
                                            <td>{{ $loop->iteration + $ticket->firstItem() - 1 }}</td>
                                            <td>{{ $d->kode_pengajuan }}</td>
                                            <td>{{ date('d-m-Y', strtotime($d->tanggal_pengajuan)) }}</td>
                                            <td>{{ ucwords(strtolower($d->nama_user)) }}</td>
                                            <td>{{ $d->keterangan }}</td>
                                            <td style="text-align: center">
                                                @if ($d->gm == 0)
                                                    <i class="feather icon-refresh-ccw warning"></i>
                                                @elseif ($d->gm == 1)
                                                    <i class="feather icon-check success"></i>
                                                @elseif ($d->gm == 2)
                                                    <i class="fa fa-close danger"></i>
                                                @endif
                                            </td>
                                            <td style="text-align: center">
                                                @if ($d->dirut == 0)
                                                    <i class="feather icon-refresh-ccw warning"></i>
                                                @elseif ($d->dirut == 1)
                                                    <i class="feather icon-check success"></i>
                                                @elseif ($d->dirut == 2)
                                                    <i class="fa fa-close danger"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($d->status != 1)
                                                    <i class="feather icon-refresh-ccw warning"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if (!empty($d->tanggal_selesai))
                                                    {{ date('d-m-Y', strtotime($d->tanggal_selesai)) }}
                                                @else
                                                    <i class="feather icon-refresh-cw warning"></i>
                                                @endif
                                            </td>
                                            <td>
                                                @if (!empty($d->nama_admin))
                                                    {{ ucwords(strtolower($d->nama_admin)) }}
                                                @else
                                                    <i class="feather icon-refresh-cw warning"></i>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    @if (!in_array($level, $ticket_hapus))
                                                        @if ($d->status == 0)
                                                            <form method="POST" class="deleteform"
                                                                action="/ticket/{{ Crypt::encrypt($d->kode_pengajuan) }}/delete">
                                                                @csrf
                                                                @method('DELETE')
                                                                <a href="#" class="delete-confirm ml-1">
                                                                    <i class="feather icon-trash danger"></i>
                                                                </a>
                                                            </form>
                                                        @endif
                                                    @endif

                                                    @if (in_array($level, $ticket_approve))
                                                        @if ($level == 'manager accounting' && empty($d->dirut))
                                                            <a href="#" class="approve ml-1"
                                                                kode_pengajuan = "{{ $d->kode_pengajuan }}">
                                                                <i class="feather icon-external-link success"></i>
                                                            </a>
                                                        @elseif ($level == 'direktur' && empty($d->status))
                                                            <a href="#" class="approve ml-1"
                                                                kode_pengajuan = "{{ $d->kode_pengajuan }}">
                                                                <i class="feather icon-external-link success"></i>
                                                            </a>
                                                        @endif
                                                    @endif

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
            <!-- Data list view end -->
        </div>
    </div>
    <!-- Input LPC -->
    <div class="modal fade text-left" id="mdlinput" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Input Permohonan Maintenance Portal</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadinput"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="mdlapprove" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Aproove Permintaan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="loadapproveticket">

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

            function loadinput() {
                $.ajax({
                    type: 'GET',
                    url: '/ticket/create',
                    cache: false,
                    success: function(respond) {
                        $("#loadinput").html(respond);
                    }
                });
            }


            $("#inputticket").click(function(e) {
                e.preventDefault();
                loadinput();
                $('#mdlinput').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            });


            $(".approve").click(function(e) {
                e.preventDefault();
                var kode_pengajuan = $(this).attr('kode_pengajuan');
                $('#mdlapprove').modal({
                    backdrop: 'static',
                    keyboard: false
                });

                $.ajax({
                    type: 'POST',
                    url: '/ticket/approveform',
                    data: {
                        _token: "{{ csrf_token() }}",
                        kode_pengajuan: kode_pengajuan
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loadapproveticket").html(respond);
                    }
                });
            });
        });
    </script>
@endpush
