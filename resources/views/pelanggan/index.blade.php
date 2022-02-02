@extends('layouts.midone')
@section('titlepage','Data Pelanggan')
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
        @include('layouts.notification')
        <div class="col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <a href="/pelanggan/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                <div class="card-body">
                    <form action="/pelanggan">
                        <div class="row">
                            <div class="col-lg-5">
                                <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker value="{{ Request('dari') }}" />
                            </div>
                            <div class="col-lg-5">
                                <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker value="{{ Request('sampai') }}" />
                            </div>
                        </div>
                        <div class="row">
                            @if (Auth::user()->kode_cabang=="PCF")
                            <div class="col-lg-2 col-sm-12">
                                <div class="form-group  ">
                                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                                        <option value="">Semua Cabang</option>
                                        @foreach ($cabang as $c)
                                        <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                            <div class="col-lg-3 col-sm-12">
                                <div class="form-group  ">
                                    <select name="id_karyawan" id="id_karyawan" class="form-control">
                                        <option value="">Semua Salesman</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-12">
                                <div class="form-group">
                                    <select name="status_pelanggan" id="status_pelanggan" class="form-control">
                                        <option value="">Status</option>
                                        <option {{ (Request('status_pelanggan')=='1' ? 'selected':'')}} value="1">AKTIF</option>
                                        <option {{ (Request('status_pelanggan')=='0' ? 'selected':'')}} value="0">NON AKTIF</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <x-inputtext label="Nama Pelanggan" field="nama" icon="feather icon-user" value="{{ Request('nama') }}" />
                            </div>
                            <div class="col-lg-2 col-sm-12">
                                <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i> </button>
                                <button type="submit" name="export" value="2" class="btn btn-success"><i class="fa fa-download"></i> </button>
                            </div>
                        </div>

                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Kode Pelanggan</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Limit</th>
                                    <th>Foto</th>
                                    <th>Salesman</th>
                                    <th>Cabang</th>
                                    <th>Tanggal Input</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pelanggan as $d)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration + $pelanggan->firstItem() - 1 }}</td>
                                    <td>{{ $d->kode_pelanggan }}</td>
                                    <td>{{ $d->nama_pelanggan }}</td>
                                    <td>{{ (!empty($d->jatuhtempo) ? $d->jatuhtempo.' Hari' : '' )}} </td>
                                    <td class="text-right">{{ (!empty($d->limitpel) ? rupiah($d->limitpel) : '') }}</td>
                                    <td>
                                        @if (!empty($d->foto))
                                        @php
                                        $path = Storage::url('pelanggan/'.$d->foto);
                                        @endphp
                                        <ul class="list-unstyled users-list m-0  d-flex align-items-center">
                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Vinnie Mostowy" class="avatar pull-up">
                                                <img class="media-object rounded-circle" src="{{ url($path)}}" alt="Avatar" height="30" width="30">
                                            </li>
                                        </ul>
                                        @endif
                                    </td>
                                    <td>{{ $d->nama_karyawan }}</td>
                                    <td>{{ $d->kode_cabang }}</td>
                                    <td>{{ $d->time_stamps }}</td>
                                    <td>
                                        @if ($d->status_pelanggan == 1)
                                        <span class="badge bg-success">Aktif</span>
                                        @else
                                        <span class="badge bg-danger">Non Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a class="ml-1" href="/pelanggan/{{\Crypt::encrypt($d->kode_pelanggan)}}/edit"><i class="feather icon-edit success"></i></a>
                                            <a class="ml-1 detailpelanggan" href="pelanggan/{{ Crypt::encrypt($d->kode_pelanggan) }}/show"><i class=" feather icon-file-text info"></i></a>
                                            <form method="POST" name="deleteform" class="deleteform" action="/pelanggan/{{ Crypt::encrypt($d->kode_pelanggan) }}/delete">
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
                            </tbody>
                        </table>
                        {{ $pelanggan->links('vendor.pagination.vuexy') }}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>

@endsection
@push('myscript')
<script>
    $(function() {


        function loadsalesmancabang() {
            var cabang = "{{ Auth::user()->kode_cabang }}";
            if (cabang != 'PST') {
                var kode_cabang = cabang;
            } else {
                var kode_cabang = $("#kode_cabang").val();
            }
            var id_karyawan = "{{ Request('id_karyawan') }}";


            $.ajax({
                type: 'POST'
                , url: '/salesman/getsalescab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , id_karyawan: id_karyawan
                }
                , cache: false
                , success: function(respond) {
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
