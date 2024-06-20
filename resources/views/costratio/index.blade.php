@extends('layouts.midone')
@section('titlepage', 'Data Cost Ratio')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Data Cost Ratio</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Data Cost Ratio</a>
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
            <div class="col-md-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="/costratio">
                            <div class="row">
                                <div class="col-lg-12 col-sm-12">
                                    <div class="form-group  ">
                                        <select name="kode_cabang" id="" class="form-control">
                                            <option value="">Pilih Cabang</option>
                                            @foreach ($cabang as $c)
                                                <option {{ Request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }} value="{{ $c->kode_cabang }}">
                                                    {{ strtoupper($c->nama_cabang) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-ms-12">
                                    <div class="form-group">
                                        <select name="id_sumber_costratio" id="id_sumber_costratio" class="form-control">
                                            <option value="">Sumber Cost Ratio</option>
                                            @foreach ($sumber as $d)
                                                <option {{ Request('id_sumber_costratio') == $d->id_sumber_costratio ? 'selected' : '' }}
                                                    value="{{ $d->id_sumber_costratio }}">{{ $d->nama_sumber }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-sm-12">
                                    <x-inputtext field="dari" label="Dari" icon="feather icon-calendar" value="{{ Request('dari') }}" datepicker />
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <x-inputtext field="sampai" label="Sampai" icon="feather icon-calendar" value="{{ Request('sampai') }}" datepicker />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-sm-12">
                                    <div class="form-group">
                                        <button type="submit" name="submit" value="1" class="btn btn-primary btn-block"><i
                                                class="fa fa-search mr-2"></i> Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="d-flex justify-content-between">
                            <a href="#" class="btn btn-primary" id="tambahcostratio"><i class="feather icon-plus mr-1"></i>Tambah Data</a>
                            <a href="/costratio/cetak?dari={{ Request('dari') }}&sampai={{ Request('sampai') }}&kode_cabang={{ Request('kode_cabang') }}&id_sumber_costratio={{ Request('id_sumber_costratio') }}&excel=true"
                                class="btn btn-success"><i class="feather icon-download"></i></a>
                        </div>


                        <div class="table-responsive">
                            <table class="table table-hover-animation mt-2">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>Tanggal</th>
                                        <th>Kode Akun</th>
                                        <th>Nama Akun</th>
                                        <th>Keterangan</th>
                                        <th>Jumlah</th>
                                        <th>Sumber</th>
                                        <th>Cabang</th>
                                        <th>#</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($costratio as $d)
                                        @if ($d->id_sumber_costratio == 1)
                                            @php
                                                $color = 'bg-info';
                                            @endphp
                                        @elseif($d->id_sumber_costratio == 2)
                                            @php
                                                $color = 'bg-success';
                                            @endphp
                                        @elseif($d->id_sumber_costratio == 4)
                                            @php
                                                $color = 'bg-danger';
                                            @endphp
                                        @else
                                            @php
                                                $color = '';
                                            @endphp
                                        @endif
                                        <tr class="{{ $color }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ date('d-m-Y', strtotime($d->tgl_transaksi)) }}</td>
                                            <td>{{ $d->kode_akun }}</td>
                                            <td>{{ $d->nama_akun }}</td>
                                            <td style="width: 40%">{{ ucwords(strtolower($d->keterangan)) }}</td>
                                            <td class="text-right">{{ rupiah($d->jumlah) }}</td>
                                            <td>{{ $d->nama_sumber }}</td>
                                            <td>{{ $d->kode_cabang }}</td>
                                            <td>
                                                @if ($d->id_sumber_costratio == 3)
                                                    <div class="btn-group" role="group" aria-label="Basic example">
                                                        <form method="POST" class="deleteform"
                                                            action="/costratio/{{ Crypt::encrypt($d->kode_cr) }}/delete">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a href="#" class="delete-confirm ml-1">
                                                                <i class="feather icon-trash danger"></i>
                                                            </a>
                                                        </form>

                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{-- {{ $salesman->links('vendor.pagination.vuexy') }} --}}
                        </div>

                        <!-- DataTable ends -->
                    </div>
                </div>
            </div>
            <!-- Data list view end -->
        </div>
    </div>
    <div class="modal fade text-left" id="mdlinputcostratio" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Tambah Costratio</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadinputcostratio"></div>
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
            $('#tambahcostratio').click(function(e) {
                e.preventDefault();
                $('#mdlinputcostratio').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadinputcostratio").load('/costratio/create');
            });
        });
    </script>
@endpush
