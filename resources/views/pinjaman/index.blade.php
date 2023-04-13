@extends('layouts.midone')
@section('titlepage','Pinjaman Karyawan')
@section('content')
<style>
    .form-group {
        margin-bottom: 5px !important;
    }

    .form-label-group {
        margin-bottom: 5px !important;
    }

    .col-4,
    .col-5,
    .col-6,
    .col-3 {
        padding-right: 1px !important;
    }

    .modal:nth-of-type(even) {
        z-index: 2000 !important;
    }

    .modal-backdrop.show:nth-of-type(even) {
        z-index: 1051 !important;
    }

    .modal {
        overflow-y: auto;
    }

</style>
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Pinjaman Karyawan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/pinjaman">Pinjaman Karyawan</a>
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
        <div class="card">
            <div class="card-body">
                <form action="/pinjaman">
                    <div class="row">
                        <div class="col-lg-6 col-sm-12">
                            <x-inputtext label="Dari" field="dari" value="{{ Request('dari') }}" icon="feather icon-calendar" datepicker />
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <x-inputtext label="Sampai" field="sampai" value="{{ Request('sampai') }}" icon="feather icon-calendar" datepicker />
                        </div>
                    </div>
                    @php
                    $level_search = ["admin","manager hrd","manager accounting","direktur"];
                    @endphp
                    @if (Auth::user()->kode_cabang=="PCF" && in_array($level,$level_search))
                    <div class="row">
                        <div class="col-lg-3 col-sm-12">
                            <div class="form-group  ">
                                <select name="kode_cabang" id="" class="form-control">
                                    @if (Auth::user()->kode_cabang=="PCF")
                                    <option value="">Semua Cabang</option>
                                    @else
                                    <option value="">Pilih Cabang</option>
                                    @endif
                                    @foreach ($cabang as $c)
                                    <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <div class="form-group">
                                <select name="kode_dept" id="kode_dept" class="form-control">
                                    <option value="">Departemen</option>
                                    @foreach ($departemen as $d)
                                    <option {{ Request('kode_dept') == $d->kode_dept ? 'selected' : ''  }} value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <div class="form-group">
                                <x-inputtext label="Nama Karyawan" value="{{ Request('nama_karyawan') }}" field="nama_karyawan" icon="feather icon-user" />
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-12">
                            <div class="form-group">
                                <select name="status" id="status" class="form-control">
                                    <option value="">Semua</option>
                                    <option value="0" {{ Request('status')== 0 ? 'selected' : '' }}>Belum di Proses</option>
                                    <option value="1" {{ Request('status')== 1 ? 'selected' : '' }}>Sudah di Proses</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-1 col-sm-12">
                            <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                    @else
                    <div class="row">
                        <div class="col-lg-8 col-sm-12">
                            <div class="form-group">
                                <x-inputtext label="Nama Karyawan" value="{{ Request('nama_karyawan') }}" field="nama_karyawan" icon="feather icon-user" />
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <div class="form-group">
                                <select name="status" id="status" class="form-control">
                                    <option value="">Semua</option>
                                    <option value="0" {{ Request('status')== 0 ? 'selected' : '' }}>Belum di Proses</option>
                                    <option value="1" {{ Request('status')== 1 ? 'selected' : '' }}>Sudah di Proses</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-1 col-sm-12">
                            <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                    @endif
                </form>

                <div class="table-responsive">
                    <table class="table table-hover-animation">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>No. Pinjaman</th>
                                <th>Tanggal</th>
                                <th>Nik</th>
                                <th>Nama Karyawan</th>
                                <th>Jabatan</th>
                                <th>Departemen</th>
                                <th>Jumlah</th>
                                <th>Bayar</th>
                                <th>Sisa Tagihan</th>
                                <th>Ket</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pinjaman as $d)
                            <tr>
                                <td>{{ $loop->iteration + $pinjaman->firstItem() -1 }}</td>
                                <td>{{ $d->no_pinjaman }}</td>
                                <td>{{ DateToIndo2($d->tgl_pinjaman) }}</td>
                                <td>{{ $d->nik }}</td>
                                <td>{{ $d->nama_karyawan }}</td>
                                <td>{{ $d->nama_jabatan }}</td>
                                <td>{{ $d->nama_dept }}</td>
                                <td class="text-right">{{ rupiah($d->jumlah_pinjaman)  }}</td>
                                <td class="text-right">{{ rupiah($d->totalpembayaran) }}</td>
                                <td class="text-right">
                                    @php
                                    $sisatagihan = $d->jumlah_pinjaman - $d->totalpembayaran;
                                    @endphp
                                    {{ rupiah($sisatagihan) }}
                                </td>
                                <td>{!! $d->jumlah_pinjaman - $d->totalpembayaran == 0 ? '<span class="badge bg-success">L</span>' : '<span class="badge bg-danger">BL</span>' !!}</td>
                                <td>
                                    @if ($d->status==0)
                                    <span class="badge bg-warning"><i class="fa fa-history"></i></span>
                                    @else
                                    <span class="badge bg-success"><i class="fa fa-check"></i></span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        @php
                                        $user_approve = [57];
                                        @endphp
                                        @if (in_array(Auth::user()->id,$user_approve))
                                        @if ($d->status==0 )
                                        <a href="/pinjaman/{{ Crypt::encrypt($d->no_pinjaman) }}/approve"><i class=" feather icon-check success"></i></a>
                                        @else
                                        @if (empty($d->totalpembayaran))
                                        <a href="/pinjaman/{{ Crypt::encrypt($d->no_pinjaman) }}/decline"><i class="fa fa-close danger"></i></a>
                                        @endif
                                        @endif
                                        @endif

                                        <a class="ml-1 show" no_pinjaman="{{ $d->no_pinjaman }}" href="#"><i class="feather icon-file-text info"></i></a>
                                        @if (empty($d->totalpembayaran) && Auth::user()->id == $d->id_user)
                                        <a class="ml-1 edit" no_pinjaman="{{ $d->no_pinjaman }}" href="#"><i class="feather icon-edit success"></i></a>
                                        <form method="POST" class="deleteform" action="/pinjaman/{{Crypt::encrypt($d->no_pinjaman)}}/delete">
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
                    {{ $pinjaman->links('vendor.pagination.vuexy') }}
                </div>

                <!-- DataTable ends -->
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>

<div class="modal fade text-left" id="mdlajukanpinjaman" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Ajukan Pinjaman</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadajukanpinjaman"></div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade text-left" id="mdlshowpinjaman" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width:1200px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Data Pinjaman</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="showpinjaman"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade text-left" style="z-index: 1052 !important" id="mdlinputbayarpinjaman" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input Bayar Pinjaman</h4>
                <button type="button" class="close tutupmdlinputbayarpinjaman" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputbayarpinjaman"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {

        function loadpinjaman(no_pinjaman) {
            $.ajax({
                type: 'POST'
                , url: '/pinjaman/edit'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_pinjaman: no_pinjaman
                }
                , cache: false
                , success: function(respond) {
                    $("#loadajukanpinjaman").html(respond);
                }
            });
        }

        function showpinjaman(no_pinjaman) {
            $.ajax({
                type: 'POST'
                , url: '/pinjaman/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_pinjaman: no_pinjaman
                }
                , cache: false
                , success: function(respond) {
                    $("#showpinjaman").html(respond);
                }
            });
        }

        $('.edit').click(function(e) {
            var no_pinjaman = $(this).attr("no_pinjaman");
            e.preventDefault();
            loadpinjaman(no_pinjaman);
            $('#mdlajukanpinjaman').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });


        $('.show').click(function(e) {
            var no_pinjaman = $(this).attr("no_pinjaman");
            e.preventDefault();
            showpinjaman(no_pinjaman);
            $('#mdlshowpinjaman').modal({
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

        $(".tutupmdlinputbayarpinjaman").click(function(e) {
            $("#mdlinputbayarpinjaman").modal("toggle");
        });
    });

</script>
@endpush
