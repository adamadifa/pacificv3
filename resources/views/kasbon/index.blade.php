@extends('layouts.midone')
@section('titlepage','Kasbon Karyawan')
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

    /* .modal:nth-of-type(even) {
        z-index: 2000 !important;
    }

    .modal-backdrop.show:nth-of-type(even) {
        z-index: 1051 !important;
    }

    .modal {
        overflow-y: auto;
    } */

</style>
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Kasbon Karyawan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/kasbon">Kasbon Karyawan</a>
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
                <form action="/kasbon">
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

                        <div class="col-lg-3 col-sm-12">
                            <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search mr-2"></i> Search</button>
                        </div>
                    </div>
                    @else
                    <div class="row">
                        <div class="col-lg-9 col-sm-12">
                            <div class="form-group">
                                <x-inputtext label="Nama Karyawan" value="{{ Request('nama_karyawan') }}" field="nama_karyawan" icon="feather icon-user" />
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-12">
                            <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search mr-2"></i> Search</button>
                        </div>
                    </div>
                    @endif

                </form>

                <div class="table-responsive">
                    <table class="table table-hover-animation">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>No. Kasbon</th>
                                <th>Tanggal</th>
                                <th>Nik</th>
                                <th>Nama Karyawan</th>
                                <th>Jabatan</th>
                                <th>Departemen</th>
                                <th>Jumlah</th>
                                <th>Bayar</th>
                                <th>Sisa Tagihan</th>
                                <th>Jatuh Tempo</th>
                                <th>Tgl Bayar</th>
                                <th>Ket</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kasbon as $d)
                            <tr>
                                <td>{{ $loop->iteration + $kasbon->firstItem() -1 }}</td>
                                <td>{{ $d->no_kasbon }}</td>
                                <td>{{ DateToIndo2($d->tgl_kasbon) }}</td>
                                <td>{{ $d->nik }}</td>
                                <td>{{ $d->nama_karyawan }}</td>
                                <td>{{ $d->nama_jabatan }}</td>
                                <td>{{ $d->nama_dept }}</td>
                                <td class="text-right">{{ rupiah($d->jumlah_kasbon)  }}</td>
                                <td class="text-right">{{ rupiah($d->totalpembayaran) }}</td>
                                <td class="text-right">
                                    @php
                                    $sisatagihan = $d->jumlah_kasbon - $d->totalpembayaran;
                                    @endphp
                                    {{ rupiah($sisatagihan) }}
                                </td>
                                <td>
                                    {{ date('d-m-Y',strtotime($d->jatuh_tempo)) }}
                                </td>
                                <td>
                                    {{ !empty($d->tgl_bayar) ? date('d-m-Y',strtotime($d->tgl_bayar)) : ''  }}
                                </td>
                                <td>{!! $d->jumlah_kasbon - $d->totalpembayaran == 0 ? '<span class="badge bg-success">Lunas</span>' : '<span class="badge bg-danger">Belum Lunas</span>' !!}</td>
                                </td>
                                <td>
                                    @if ($d->status==0)
                                    <span class="badge bg-warning"><i class="fa fa-history"></i></span>
                                    @else
                                    <span class="badge bg-success">{{ DateToIndo2($d->tgl_ledger) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">

                                        @php
                                        $user_approve = [57,23,1,88,52];
                                        @endphp
                                        @if (in_array(Auth::user()->id,$user_approve))
                                        <a href="#" class="approve" no_kasbon="{{ $d->no_kasbon }}"><i class=" feather icon-external-link success"></i></a>
                                        @endif
                                        <a href="/kasbon/{{ Crypt::encrypt($d->no_kasbon) }}/cetakformulir" target="_blank" class="ml-1"><i class="feather icon-printer text-primary"></i></a>
                                        @if (empty($d->totalpembayaran) && Auth::user()->id == $d->id_user)
                                        <a class="ml-1 edit" no_kasbon="{{ $d->no_kasbon }}" href="#"><i class="feather icon-edit success"></i></a>
                                        <form method="POST" class="deleteform" action="/kasbon/{{Crypt::encrypt($d->no_kasbon)}}/delete">
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
                    {{ $kasbon->links('vendor.pagination.vuexy') }}
                </div>

                <!-- DataTable ends -->
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>

<div class="modal fade text-left" id="mdlajukankasbon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Ajukan Kasbon</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadajukankasbon"></div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade text-left" id="mdlproseskasbon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Proses Kasbon</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadproseskasbon"></div>
            </div>
        </div>
    </div>
</div>


@endsection
@push('myscript')
<script>
    $(function() {


        $(".approve").click(function(e) {
            e.preventDefault();
            var no_kasbon = $(this).attr("no_kasbon");
            proseskasbon(no_kasbon);
            $('#mdlproseskasbon').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        function proseskasbon(no_kasbon) {
            $.ajax({
                type: 'POST'
                , url: '/kasbon/proseskasbon'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_kasbon: no_kasbon
                }
                , cache: false
                , success: function(respond) {
                    $("#loadproseskasbon").html(respond);
                }
            });
        }

        function loadkasbon(no_kasbon) {
            $.ajax({
                type: 'POST'
                , url: '/kasbon/edit'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_kasbon: no_kasbon
                }
                , cache: false
                , success: function(respond) {
                    $("#loadajukankasbon").html(respond);
                }
            });
        }


        $('.edit').click(function(e) {
            var no_kasbon = $(this).attr("no_kasbon");
            e.preventDefault();
            loadkasbon(no_kasbon);
            $('#mdlajukankasbon').modal({
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
