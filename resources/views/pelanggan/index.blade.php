@extends('layouts.midone')
@section('titlepage', 'Data Pelanggan')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Data Pelanggan</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/pelanggan">Pelanggan</a>
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
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="card text-center">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="avatar bg-rgba-info p-50 m-0 mb-1">
                                    <div class="avatar-content">
                                        <i class="feather icon-users text-info font-medium-5"></i>
                                    </div>
                                </div>
                                <h2 class="text-bold-700">{{ rupiah($jmlpelanggan) }}</h2>
                                <p class="mb-0 line-ellipsis">Database Pelanggan</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="card text-center">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="avatar bg-rgba-success p-50 m-0 mb-1">
                                    <div class="avatar-content">
                                        <i class="feather icon-users text-success font-medium-5"></i>
                                    </div>
                                </div>
                                <h2 class="text-bold-700">{{ rupiah($jmlaktif) }}</h2>
                                <p class="mb-0 line-ellipsis">Pelanggan Aktif</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <div class="card text-center">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="avatar bg-rgba-danger p-50 m-0 mb-1">
                                    <div class="avatar-content">
                                        <i class="feather icon-users text-danger font-medium-5"></i>
                                    </div>
                                </div>
                                <h2 class="text-bold-700">{{ rupiah($jmlnonaktif) }}</h2>
                                <p class="mb-0 line-ellipsis">Pelanggan Non Aktif</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-lg-12 col-sm-12">
                    @include('layouts.notification')
                    <div class="card">

                        @php
                            $latitude1 = -6.646223122320823;
                            $longitude1 = 107.36571020894631;
                            $latitude2 = -6.6462205999999995;
                            $longitude2 = 107.36570549999999;
                            $jarak = hitungjarak($latitude1, $longitude1, $latitude2, $longitude2);
                        @endphp
                        {{-- {{ $jarak["meters"] }} --}}
                        @if (in_array($level, $pelanggan_tambah))
                            <div class="card-header">
                                <a href="/pelanggan/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah
                                    Data</a>
                            </div>
                        @endif
                        <div class="card-body">
                            <form action="/pelanggan">
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
                                    @if (Auth::user()->level != 'salesman')
                                        <div class="col-lg-3 col-sm-12">
                                            <div class="form-group  ">
                                                <select name="id_karyawan" id="id_karyawan" class="form-control">
                                                    <option value="">Semua Salesman</option>
                                                </select>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <select name="status_pelanggan" id="status_pelanggan" class="form-control">
                                                <option value="">Status</option>
                                                <option {{ Request('status_pelanggan') == '1' ? 'selected' : '' }}
                                                    value="1">
                                                    AKTIF
                                                </option>
                                                <option {{ Request('status_pelanggan') == '0' ? 'selected' : '' }}
                                                    value="0">
                                                    NON
                                                    AKTIF</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <x-inputtext label="Kode Pelanggan" field="kode_pelanggan"
                                            icon="feather icon-credit-card" value="{{ Request('kode_pelanggan') }}" />
                                    </div>
                                    <div class="col-lg-3 col-sm-12">
                                        <x-inputtext label="Nama Pelanggan" field="nama" icon="feather icon-user"
                                            value="{{ Request('nama') }}" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-5">
                                        <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker
                                            value="{{ Request('dari') }}" />
                                    </div>
                                    <div class="col-lg-5">
                                        <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker
                                            value="{{ Request('sampai') }}" />
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <button type="submit" name="submit" value="1" class="btn btn-primary"><i
                                                    class="fa fa-search"></i> </button>
                                            <button type="submit" name="export" value="2"
                                                class="btn btn-success"><i class="fa fa-download"></i> </button>
                                            <a href="#" id="shownonaktif" class="btn btn-danger"><i
                                                    class="feather icon-slash"></i> </a>
                                        </div>
                                    </div>
                                </div>


                            </form>
                            <div class="table-responsive" id="mytable">
                                <table class="table table-hover-animation"
                                    @if (Auth::user()->level == 'salesman') style="font-size: 11px" @endif>
                                    <thead class="thead-dark">
                                        <tr>
                                            @if (Auth::user()->level != 'salesman')
                                                <th class="text-center">No</th>
                                            @endif
                                            <th>Kode Pelanggan</th>
                                            <th>Nama Pelanggan</th>
                                            @if (Auth::user()->level != 'salesman')
                                                <th>Jatuh Tempo</th>
                                                <th>Pasar</th>
                                                <th>Limit</th>
                                            @endif

                                            <th>Foto</th>
                                            @if (Auth::user()->level != 'salesman')
                                                <th>Salesman</th>
                                                <th>Cabang</th>
                                                <th>Tanggal Input</th>
                                            @endif
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pelanggan as $d)
                                            <tr>
                                                @if (Auth::user()->level != 'salesman')
                                                    <td class="text-center">
                                                        {{ $loop->iteration + $pelanggan->firstItem() - 1 }}
                                                    </td>
                                                @endif
                                                <td>{{ $d->kode_pelanggan }}</td>
                                                <td class="detail"
                                                    @if ($level == 'salesman') data-href="/pelanggan/showpelanggan?kode_pelanggan={{ Crypt::encrypt($d->kode_pelanggan) }} @endif">
                                                    {{ $d->nama_pelanggan }}</td>
                                                @if (Auth::user()->level != 'salesman')
                                                    <td>{{ !empty($d->jatuhtempo) ? $d->jatuhtempo . ' Hari' : '' }} </td>
                                                    <td>{{ $d->pasar }}</td>
                                                    <td class="text-right">
                                                        {{ !empty($d->limitpel) ? rupiah($d->limitpel) : '' }}
                                                    </td>
                                                @endif

                                                <td>
                                                    @if (!empty($d->foto))
                                                        @php
                                                            $path = Storage::url('pelanggan/' . $d->foto);
                                                        @endphp
                                                        <ul
                                                            class="list-unstyled users-list m-0  d-flex align-items-center">
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom"
                                                                data-placement="bottom"
                                                                data-original-title="Vinnie Mostowy"
                                                                class="avatar pull-up">
                                                                <img class="media-object rounded-circle"
                                                                    src="{{ url($path) }}" alt="Avatar"
                                                                    height="30" width="30">
                                                            </li>
                                                        </ul>
                                                    @endif
                                                </td>
                                                @if (Auth::user()->level != 'salesman')
                                                    <td>{{ $d->nama_karyawan }}</td>
                                                    <td>{{ $d->kode_cabang }}</td>
                                                    <td>{{ $d->time_stamps }}</td>
                                                @endif
                                                <td>
                                                    @if ($d->status_pelanggan == 1)
                                                        <span class="badge bg-success">Aktif</span>
                                                    @else
                                                        <span class="badge bg-danger">Non Aktif</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="Basic example">
                                                        @if (in_array($level, $pelanggan_edit))
                                                            <a class="ml-1"
                                                                href="/pelanggan/{{ \Crypt::encrypt($d->kode_pelanggan) }}/edit"><i
                                                                    class="feather icon-edit success"></i></a>
                                                        @endif
                                                        @if (Auth::user()->level != 'salesman')
                                                            <a class="ml-1 detailpelanggan"
                                                                href="pelanggan/{{ Crypt::encrypt($d->kode_pelanggan) }}/show"><i
                                                                    class=" feather icon-file-text info"></i></a>
                                                        @else
                                                            <a class="ml-1"
                                                                href="/pelanggan/{{ Crypt::encrypt($d->kode_pelanggan) }}/capturetoko"><i
                                                                    class="feather icon-camera info"></i></a>
                                                        @endif
                                                        @if (in_array($level, $pelanggan_hapus))
                                                            <form method="POST" name="deleteform" class="deleteform"
                                                                action="/pelanggan/{{ Crypt::encrypt($d->kode_pelanggan) }}/delete">
                                                                @csrf
                                                                @method('DELETE')
                                                                <a href="#" class="delete-confirm ml-1">
                                                                    <i class="feather icon-trash danger"></i>
                                                                </a>
                                                            </form>
                                                        @endif
                                                        @if (Auth::user()->level != 'salesman')
                                                            @if (in_array($level, $pelanggan_ajuanlimit))
                                                                <a class="ml-1"
                                                                    href="/limitkredit/{{ \Crypt::encrypt($d->kode_pelanggan) }}/create"><i
                                                                        class="feather icon-external-link primary"></i></a>

                                                                <a class="ml-1 ajukanfaktur"
                                                                    kode_pelanggan="{{ Crypt::encrypt($d->kode_pelanggan) }}"
                                                                    href="#"><i
                                                                        class="feather icon-external-link warning"></i></a>

                                                                <a class="ml-1 ajuanrouting"
                                                                    kode_pelanggan="{{ Crypt::encrypt($d->kode_pelanggan) }}"
                                                                    href="#"><i
                                                                        class="feather icon-external-link info"></i></a>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $pelanggan->links('vendor.pagination.vuexy') }}
                            </div>

                            <!-- DataTable ends -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Data list view end -->
        </div>
    </div>
    <div class="modal fade text-left" id="mdlshownonaktif" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Data Pelanggan Tidak Transaksi > 90 Hari</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadshownonaktif"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="mdlajukanfaktur" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Ajukan Faktur <span id="tglupdatepresensi"></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="loadformajukanfaktur">
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade text-left" id="mdlajuanrouting" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Ajukan Routing </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="loadformajuanrouting">
                </div>
            </div>
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        $(function() {
            $(".ajukanfaktur").click(function(e) {
                e.preventDefault();
                var kode_pelanggan = $(this).attr("kode_pelanggan");
                $("#mdlajukanfaktur").modal("show");
                // /alert(kode_pelanggan);
                $("#loadformajukanfaktur").load('/ajuanfaktur/' + kode_pelanggan + '/create');
            });

            $(".ajuanrouting").click(function(e) {
                e.preventDefault();
                var kode_pelanggan = $(this).attr("kode_pelanggan");
                $("#mdlajuanrouting").modal("show");
                // /alert(kode_pelanggan);
                $("#loadformajuanrouting").load('/ajuanrouting/' + kode_pelanggan + '/create');
            });

            $("#shownonaktif").click(function(e) {
                $("#mdlshownonaktif").modal("show");
                $("#loadshownonaktif").load('/pelanggan/shownonaktif');
            });

            function loadsalesmancabang() {
                var cabang = "{{ Auth::user()->kode_cabang }}";
                if (cabang != 'PST') {
                    var kode_cabang = cabang;
                } else {
                    var kode_cabang = $("#kode_cabang").val();
                }
                var id_karyawan = "{{ Request('id_karyawan') }}";


                $.ajax({
                    type: 'POST',
                    url: '/salesman/getsalescab',
                    data: {
                        _token: "{{ csrf_token() }}",
                        kode_cabang: kode_cabang,
                        id_karyawan: id_karyawan
                    },
                    cache: false,
                    success: function(respond) {
                        $("#id_karyawan").html(respond);
                    }
                });
            }




            loadsalesmancabang();

            $("#kode_cabang").change(function() {
                loadsalesmancabang();
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

            // $(".detail").click(function(e) {
            //     e.preventDefault();
            //     window.location = $(this).data("href");
            // });
        });
    </script>
@endpush
