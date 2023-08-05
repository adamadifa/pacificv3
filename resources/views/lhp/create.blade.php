@extends('layouts.midone')
@section('titlepage','Buat LHP')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">BUAT LHP</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/lhp/create">BUAT LHP</a>
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

                <div class="card-body">
                    <form action="/lhp/create" id="frmcari">
                        <div class="row">
                            <div class="col-lg-6 col-sm-12">
                                <x-inputtext label="Tanggal" field="tanggal" icon="feather icon-calendar" datepicker value="{{ Request('tanggal') }}" />
                            </div>
                            <div class="col-lg-2 col-sm-2">
                                <div class="form-group  ">
                                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                                        <option value="">Pilih Cabang</option>
                                        @foreach ($cabang as $c)
                                        <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{
                                            $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-12">
                                <div class="form-group  ">
                                    <select name="id_karyawan" id="id_karyawan" class="form-control">
                                        <option value="">Semua Salesman</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-12">
                                <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                    <hr>

                    @include('layouts.notification')

                    <form action="">
                        <div class="row">
                            <div class="col-12">
                                <x-inputtext field="rute" label="Rute" icon="feather icon-maps" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-hover-animation">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>No Faktur</th>
                                            <th>Tanggal</th>
                                            <th>Pelanggan</th>
                                            <th>Salesman</th>
                                            <th>Cabang</th>
                                            <th>T/K</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($penjualan as $d)
                                        <tr>
                                            <td>{{ $d->no_fak_penj }}</td>
                                            <td>{{ date("d-m-Y",strtotime($d->tgltransaksi)) }}</td>
                                            <td>{{ strtoupper($d->nama_pelanggan) }}</td>
                                            <td>{{ strtoupper($d->nama_karyawan) }}</td>
                                            <td>{{ strtoupper($d->kode_cabang) }}</td>
                                            <td>
                                                @if ($d->jenistransaksi=="tunai")
                                                <span class="badge bg-success">T</span>
                                                @else
                                                <span class="badge bg-warning">K</span>
                                                @endif
                                            </td>
                                            <td class="text-right">{{rupiah($d->total)}}</td>
                                            <td>
                                                @if ($d->status_lunas=="1")
                                                <span class="badge bg-success">Lunas</span>
                                                @else
                                                <span class="badge bg-danger">Belum Lunas</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                    <input type="checkbox" name="id[]" value="{{ $d->id }}">
                                                    <span class="vs-checkbox">
                                                        <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                        </span>
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Kas Kecil -->

@endsection

@push('myscript')
<script>
    $(function() {
        function loadsalesmancabang(kode_cabang) {
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

        $("#kode_cabang").change(function() {
            var kode_cabang = $(this).val();
            loadsalesmancabang(kode_cabang);
        });
    });

</script>
@endpush
