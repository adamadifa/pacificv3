@extends('layouts.midone')
@section('titlepage','Buat LHP')
@section('content')
<style>
    .float {
        position: fixed;
        bottom: 40px;
        right: 40px;
        text-align: center;
        z-index: 9000;
    }

</style>
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

                    <form action="/lhp/store" method="POST" id="frmLhp">
                        <input type="hidden" name="tanggal" value="{{ Request('tanggal') }}" id="tanggal">
                        <input type="hidden" name="kode_cabang" value="{{ Request('kode_cabang') }}" id="kode_cabang">
                        <input type="hidden" name="id_karyawan" value="{{ Request('id_karyawan') }}" id="id_karyawan">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <x-inputtext field="rute" label="Rute" icon="feather icon-map" />
                            </div>
                        </div>
                        <button type="submit" name="submit" class=" float btn btn-primary">
                            <i class="fa fa-send my-float"></i> Buat LHP
                        </button>
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
                                                @if (empty($d->kode_lhp))
                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                    <input type="checkbox" name="no_fak_penj[]" value="{{ $d->no_fak_penj }}">
                                                    <span class="vs-checkbox">
                                                        <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                        </span>
                                                    </span>
                                                </div>
                                                @else
                                                <span class="badge bg-success">
                                                    {{ $d->kode_lhp }}
                                                </span>
                                                @endif
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
            var kode_cabang = $("#kode_cabang").val();
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

        $("#frmLhp").submit(function() {
            var tanggal = $(this).find("#tanggal").val();
            var kode_cabang = $("frmLhp").find("#kode_cabang").val();
            var id_karyawan = $("frmLhp").find("#id_karyawan").val();
            var rute = $("#rute").val();

            if (tanggal == "") {
                swal({
                    title: 'Oops'
                    , text: 'Get Data Terlebih Dahulu !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmLhp").find("#tanggal").focus();
                });
                return false;
            } else if (kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Get Data Terlebih Dahulu !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmLhp").find("#kode_cabang").focus();
                });
                return false;
            } else if (id_karyawan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Get Data terlebih Dahlu !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmLhp").find("#id_karyawan").focus();
                });

                return false;
            } else if (rute == "") {
                swal({
                    title: 'Oops'
                    , text: 'Rute Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmLhp").find("#rute").focus();
                });

                return false;
            }
        });
    });

</script>
@endpush
