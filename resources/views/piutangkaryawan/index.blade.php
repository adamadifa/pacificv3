@extends('layouts.midone')
@section('titlepage','Piutang Karyawan')
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
                    <h2 class="content-header-title float-left mb-0">Piutang Karyawan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/piutangkaryawan">Piutang Karyawan</a>
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
            <div class="card-header">
                <a href="/karyawan" class="btn btn-primary"><i class="feather icon-plus mr-1"></i> Tambah Data</a>
            </div>
            <div class="card-body">
                <form action="/piutangkaryawan">
                    <div class="row">
                        <div class="col-lg-6 col-sm-12">
                            <x-inputtext label="Dari" field="dari" value="{{ Request('dari') }}" icon="feather icon-calendar" datepicker />
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <x-inputtext label="Sampai" field="sampai" value="{{ Request('sampai') }}" icon="feather icon-calendar" datepicker />
                        </div>
                    </div>
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
                        <div class="col-lg-1 col-sm-12">
                            <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i></button>
                        </div>
                    </div>

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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pinjaman as $d)
                            <tr>
                                <td>{{ $loop->iteration + $pinjaman->firstItem() -1 }}</td>
                                <td>{{ $d->no_pinjaman_nonpjp }}</td>
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
                                    <div class="btn-group">
                                        <a class="ml-1 show" no_pinjaman_nonpjp="{{ $d->no_pinjaman_nonpjp }}" href="#"><i class="feather icon-file-text info"></i></a>
                                        @if (empty($d->totalpembayaran) && Auth::user()->id == $d->id_user)
                                        <form method="POST" class="deleteform" action="/piutangkaryawan/{{Crypt::encrypt($d->no_pinjaman_nonpjp)}}/delete">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" class="delete-confirm ml-1">
                                                <i class="feather icon-trash danger"></i>
                                            </a>
                                        </form>
                                        @endif
                                    </div>
                                </td>
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

<div class="modal fade text-left" id="mdlshowpinjaman" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
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


        function showpinjaman(no_pinjaman_nonpjp) {
            $.ajax({
                type: 'POST'
                , url: '/piutangkaryawan/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_pinjaman_nonpjp: no_pinjaman_nonpjp
                }
                , cache: false
                , success: function(respond) {
                    $("#showpinjaman").html(respond);
                }
            });
        }


        $('.show').click(function(e) {
            var no_pinjaman_nonpjp = $(this).attr("no_pinjaman_nonpjp");
            e.preventDefault();
            showpinjaman(no_pinjaman_nonpjp);
            $('#mdlshowpinjaman').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });
    });

</script>
@endpush
