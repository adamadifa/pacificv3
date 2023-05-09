@extends('layouts.midone')
@section('titlepage','Pembayaran JMK')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Pembayaran JMK</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/pembayaranjmk">Pembayaran JMK</a>
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
                    <a href="#" class="btn btn-primary" id="inputbayarjmk"><i class="fa fa-plus mr-1"></i> Input Bayar JMK</a>
                </div>
                <div class="card-body">
                    <form action="/pembayaranjmk">
                        <div class="row">
                            <div class="col-lg-3 col-sm-12">
                                <x-inputtext label="Nama Karyawan" field="nama_karyawan_search" icon="feather icon-users" value="{{ Request('nama_karyawan_search') }}" />
                            </div>
                            <div class="col-lg-2 col-sm-12">
                                <div class="form-group">
                                    <select name="id_perusahaan_search" id="id_perusahaan_search" class="form-control">
                                        <option value="">Perusahaan</option>
                                        <option value="MP">MP</option>
                                        <option value="PCF">PCF</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-12">
                                <div class="form-group">
                                    <select name="id_kantor_search" id="id_kantor_search" class="form-control">
                                        <option value="">Kantor</option>
                                        @foreach ($kantor as $d)
                                        <option {{ Request('id_kantor_search')==$d->kode_cabang ? 'selected' : '' }} value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-12">
                                <div class="form-group">
                                    <select name="kode_dept_search" id="kode_dept_search" class="form-control">
                                        <option value="">Departemen</option>
                                        @foreach ($departemen as $d)
                                        <option {{ Request('kode_dept_search')==$d->kode_dept ? 'selected' : '' }} value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-12">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search mr-1"></i> Cari</button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>No. Bukti</th>
                                    <th>Tanggal</th>
                                    <th>NIK</th>
                                    <th>Nama Karyawan</th>
                                    <th>Jabatan</th>
                                    <th>Perusahaan</th>
                                    <th>Kantor</th>
                                    <th>Dept</th>
                                    <th>Jumlah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jmk as $d)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $d->no_bukti }}</td>
                                    <td>{{ $d->tgl_pembayaran }}</td>
                                    <td>{{ $d->nik }}</td>
                                    <td>{{ $d->nama_karyawan }}</td>
                                    <td>{{ $d->nama_jabatan }}</td>
                                    <td>{{ $d->id_perusahaan }}</td>
                                    <td>{{ $d->id_kantor }}</td>
                                    <td>{{ $d->nama_dept }}</td>
                                    <td class="text-right">{{ rupiah($d->jumlah) }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a class="ml-1 edit" no_kontrak="{{ $d->no_kontrak }}" href="#"><i class="feather icon-edit success"></i></a>
                                            <form method="POST" class="deleteform" action="/kontrak/{{Crypt::encrypt($d->no_kontrak)}}/delete">
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
                        {{-- {{ $datakontrak->links('vendor.pagination.vuexy') }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdlinputbayarjmk" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Pembayaran JMK</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="loadinputbayarjmk">

            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        $('#inputbayarjmk').click(function(e) {
            e.preventDefault();
            $("#loadinputbayarjmk").load('/pembayaranjmk/create');
            $('#mdlinputbayarjmk').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });
    })

</script>
@endpush
