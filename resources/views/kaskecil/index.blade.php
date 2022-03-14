@extends('layouts.midone')
@section('titlepage','Kas Kecil')
@section('content')

<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Kas Kecil</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/kaskecil">Kas Kecil</a>
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
                <div class="card-header">
                    <a href="#" class="btn btn-primary" id="inputkaskecil"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                <div class="card-body">
                    <form action="/kaskecil" id="frmcari">
                        <div class="row">
                            <div class="col-lg-6 col-sm-12">
                                <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker value="{{ Request('dari') }}" />
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker value="{{ Request('sampai') }}" />
                            </div>
                        </div>
                        <div class="row">
                            @if (Auth::user()->kode_cabang =="PCF")
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
                            @endif
                            <div class="col-lg-4 col-sm-12">
                                <x-inputtext field="nobukti" label="No. Bukti" icon="feather icon-credit-card" value="{{ Request('nobukti') }}" />
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i> Cari Data </button>
                            </div>
                        </div>
                    </form>
                    @include('layouts.notification')
                    <table class="table table-hover-animation">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>No. Bukti</th>
                                <th>Keterangan</th>
                                <th>Akun</th>
                                <th>Penerimaan</th>
                                <th>Pengeluaran</th>
                                <th>Saldo</th>
                                @if (Auth::user()->kode_cabang=="PCF")
                                <th>Peruntukan</th>
                                @endif
                                <th>CR</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($kaskecil == null)
                            <tr>
                                <td colspan="11">
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
                                <td></td>
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
                                @if (Auth::user()->kode_cabang=="PCF")
                                <td>{{ $d->peruntukan }}</td>
                                @endif
                                <td class="success">
                                    @if (!empty($d->kode_cr))
                                    <i class="fa fa-check"></i>
                                    @endif
                                </td>
                                <td>

                                    @if (empty($d->kode_klaim) && $d->keterangan != "Penerimaan Kas Kecil")
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        @if(empty($d->no_ref))
                                        <a href="#" data-status="0" data-id="{{ $d->id }}" class="success edit"><i class="feather icon-edit"></i></a>
                                        <form method="POST" name="deleteform" class="deleteform" action="/kaskecil/{{ Crypt::encrypt($d->id) }}/delete">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" class="delete-confirm ml-1">
                                                <i class="feather icon-trash danger"></i>
                                            </a>
                                        </form>
                                        @endif
                                    </div>
                                    @else
                                    <a href="#" data-status="1" data-id="{{ $d->id }}" class="success edit"><i class="feather icon-edit"></i></a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Input Kas Kecil -->
<div class="modal fade text-left" id="mdlinputkaskecil" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input Kas Kecil</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputkaskecil"></div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Kas Kecil -->
<div class="modal fade text-left" id="mdleditkaskecil" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit Kas Kecil</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadeditkaskecil"></div>
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
        $("#inputkaskecil").click(function() {
            $('#mdlinputkaskecil').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadinputkaskecil").load('/kaskecil/create');
        });

        $(".edit").click(function() {
            var id = $(this).attr("data-id");
            $('#mdleditkaskecil').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadeditkaskecil").load('/kaskecil/' + id + '/edit');
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
