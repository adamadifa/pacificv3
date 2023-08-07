@extends('layouts.midone')
@section('titlepage','Buat Klaim')
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
                    <h2 class="content-header-title float-left mb-0">Buat Klaim</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/klaim/create">Buat Klaim</a>
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
                    <form action="/klaim/create" id="frmcari">
                        <div class="row">
                            <div class="col-lg-6 col-sm-12">
                                <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker value="{{ Request('dari') }}" />
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker value="{{ Request('sampai') }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-sm-12">
                                <div class="form-group  ">
                                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                                        <option value="">Pilih Cabang</option>
                                        @foreach ($cabang as $c)
                                        @if ($c->kode_cabang=="PCF")
                                        @php
                                        $kode_cabang = "PST";
                                        $nama_cabang = "PUSAT";
                                        @endphp
                                        @else
                                        @php
                                        $kode_cabang = $c->kode_cabang;
                                        $nama_cabang = $c->nama_cabang;
                                        @endphp
                                        @endif
                                        <option {{ (Request('kode_cabang')==$kode_cabang ? 'selected':'')}} value="{{
                                            $kode_cabang }}">{{ strtoupper($nama_cabang) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- @if (Auth::user()->kode_cabang =="PCF")
                            <div class="col-lg-4 col-sm-12">
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
                @else
                @if (Auth::user()->level=="admin keuangan")
                @php
                $kode_cabang = "PST";
                @endphp
                @else
                @php
                $kode_cabang = Auth::user()->kode_cabang;
                @endphp
                @endif
                <input type="hidden" name="kode_cabang" id="kode_cabang" value="{{ $kode_cabang }}">
                @endif --}}
                <div class="col-lg-4 col-sm-12">
                    <x-inputtext field="nobukti" label="No. Bukti" icon="feather icon-credit-card" value="{{ Request('nobukti') }}" />
                </div>
                <div class="col-lg-4 col-sm-12">
                    <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i> Cari Data </button>
                </div>
            </div>
            </form>

            <hr>
            <form action="/klaim/store" method="POST" id="frmKlaim">
                @csrf
                <input type="hidden" name="kode_cabang" id="kode_cabang">
                <div class="row">
                    <div class="col-lg-6 col-sm-12">
                        <x-inputtext label="Tanggal Klaim" field="tgl_klaim" icon="feather icon-calendar" datepicker />
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <x-inputtext label="Keterangan" field="keterangan" icon="feather icon-file" />
                    </div>
                </div>

                <button type="submit" name="submit" class=" float btn btn-primary">
                    <i class="fa fa-plus my-float"></i> Buat Klaim
                </button>
                @include('layouts.notification')
                <table class="table" style="font-size:12px">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width:1%">No</th>
                            <th style="width:8%">Tanggal</th>
                            <th style="width:7%">No. Bukti</th>
                            <th style="width:50%">Keterangan</th>
                            <th style="width:5%">Akun</th>
                            <th style="width:6%">Penerimaan</th>
                            <th style="width:6%">Pengeluaran</th>
                            <th style="width:6%">Saldo</th>
                            <th style="width:3%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($kaskecil == null)
                        <tr>
                            <td colspan="10">
                                <div class="alert alert-info">
                                    <i class="fa fa-info mr-2"></i>
                                    Silahkan Pilih Periode Pencarian Terlebih Dahulu !
                                </div>
                            </td>
                        </tr>
                        @else
                        <tr>
                            <td colspan="7"><b>SALDO AWAL</b></td>
                            <td align="right" style="font-weight:bold">
                                @if (!empty($saldoawal->saldo_awal))
                                {{ rupiah($saldoawal->saldo_awal) }}
                                @endif
                            </td>
                            <td></td>


                        </tr>
                        @php
                        $saldo = $saldoawal->saldo_awal;
                        $totalpenerimaan = 0;
                        $totalpengeluaran = 0;
                        @endphp
                        @foreach ($kaskecil as $d)
                        @php
                        if ($d->status_dk == 'K') {
                        $penerimaan = $d->jumlah;
                        $s = $penerimaan;
                        $pengeluaran = "0";
                        } else {
                        $penerimaan = "0";
                        $pengeluaran = $d->jumlah;
                        $s = -$pengeluaran;
                        }

                        $totalpenerimaan = $totalpenerimaan + $penerimaan;
                        $totalpengeluaran = $totalpengeluaran + $pengeluaran;
                        $saldo = $saldo + $s;

                        if ($d->no_ref != "") {
                        $color = "#6db5c3";
                        $text = "white";
                        } else {
                        $color = "";
                        $text = "";
                        }
                        @endphp
                        <tr style="color:{{ $text }}; background-color:{{ $color }} ">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ date("d-m-Y",strtotime($d->tgl_kaskecil)) }}</td>
                            <td>{{ $d->nobukti; }}</td>
                            <td style="width:25%">{{ ucwords(strtolower($d->keterangan)) }}</td>
                            <td style="width:20%">{{ $d->kode_akun }} - {{ $d->nama_akun }}</td>
                            <td align="right" class="success">
                                @if (!empty($penerimaan))
                                {{ rupiah($penerimaan) }}
                                @endif
                            </td>
                            <td align=" right" class="danger">
                                @if (!empty($pengeluaran))
                                {{ rupiah($pengeluaran) }}
                                @endif
                            </td>
                            <td align="right" class="info">
                                @if (!empty($saldo))
                                {{ rupiah($saldo) }}
                                @endif
                            </td>

                            <td>
                                @if (empty($d->kode_klaim))
                                <div class="vs-checkbox-con vs-checkbox-primary">
                                    <input type="checkbox" name="id[]" value="{{ $d->id }}">
                                    <span class="vs-checkbox">
                                        <span class="vs-checkbox--check">
                                            <i class="vs-icon feather icon-check"></i>
                                        </span>
                                    </span>
                                </div>
                                @else
                                <span class="badge bg-success">
                                    {{ $d->kode_klaim }}
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>
</div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        function loadcabang() {
            var kode_cabang = $('#frmcari').find('#kode_cabang').val();
            $('#frmKlaim').find('#kode_cabang').val(kode_cabang);
        }

        loadcabang();
        $("#frmKlaim").submit(function() {
            var tgl_klaim = $("#tgl_klaim").val();
            var keterangan = $("#keterangan").val();
            var kode_cabang = $("#kode_cabang").val();

            if (kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cabang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_klaim").focus();
                });
                return false;
            } else if (tgl_klaim == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Klaim Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_klaim").focus();
                });
                return false;
            } else if (keterangan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Keterangan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#keterangan").focus();
                });

                return false;
            }
        });
        $("#frmcari").submit(function() {
            var kode_cabang = $("#kode_cabang").val();
            var dari = $("#dari").val();
            var sampai = $("#sampai").val();

            if (dari == "" && sampai == "") {
                swal({
                    title: 'Oops'
                    , text: 'Periode Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#dari").focus();
                });
                return false;
            } else if (kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cabang Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_cabang").focus();
                });

                return false;
            }
        });
    });

</script>
@endpush
