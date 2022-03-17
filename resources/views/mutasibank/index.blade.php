@extends('layouts.midone')
@section('titlepage','Mutasi Bank')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Mutasi Bank</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/mutasibank">Mutasi Bank</a>
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
                    <a href="#" id="inputmutasibank" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                <div class="card-body">
                    <form action="/mutasibank" id="frmcari">
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
                            <input type="hidden" name="kode_cabang" id="kode_cabang" value="{{ Auth::user()->kode_cabang }}">
                            @endif

                            <div class="col-lg-4 col-sm-12">
                                <div class="form-group">
                                    <select name="bank" id="bank" class="form-control">
                                        <option value="">Pilih Bank</option>
                                    </select>
                                </div>
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
                                <th style="width:40%">Keterangan</th>
                                <th>Akun</th>
                                <th>Penerimaan</th>
                                <th>Pengeluaran</th>
                                <th>Saldo</th>
                                <th>Aksi</th>
                            </tr>
                            <tr>
                                <th colspan="6">Saldo Awal</th>
                                <th class="text-right">{{ rupiah($saldoawal) }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $totalpenerimaan = 0;
                            $totalpengeluaran = 0;
                            @endphp
                            @foreach ($mutasibank as $d)
                            @php
                            if ($d->status_dk == 'K') {
                            $penerimaan = $d->jumlah;
                            $s = $penerimaan;
                            $pengeluaran = 0;
                            } else {
                            $penerimaan = 0;
                            $pengeluaran = $d->jumlah;
                            $s = -$pengeluaran;
                            }

                            $saldo = $saldoawal + $s;
                            $totalpenerimaan += $penerimaan;
                            $totalpengeluaran += $pengeluaran;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ date("d-m-Y",strtotime($d->tgl_ledger)) }}</td>
                                <td>{{ ucwords(strtolower($d->keterangan)) }}</td>
                                <td>{{ $d->kode_akun }} {{ $d->nama_akun }}</td>
                                <td class="success text-right">{{(!empty($penerimaan)) ? rupiah($penerimaan) : '' }}</td>
                                <td class="danger text-right">{{(!empty($pengeluaran)) ? rupiah($pengeluaran) : '' }}</td>
                                <td class="text-right">{{ rupiah($saldo) }}</td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <a class="ml-1 editmutasibank" href="#" no_bukti="{{ $d->no_bukti }}"><i class="feather icon-edit success"></i></a>
                                        <form method="POST" class="deleteform" action="/mutasibank/{{Crypt::encrypt($d->no_bukti)}}/delete">
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
                            <tr style="font-weight:bold">
                                <td colspan="4">TOTAL</td>
                                <td class="text-right success">{{ rupiah($totalpenerimaan) }}</td>
                                <td class="text-right danger">{{ rupiah($totalpengeluaran) }}</td>
                                <td class="text-right">{{ rupiah($saldo) }}</td>
                                <td class="text-right"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Input Mutasi Bank -->
<div class="modal fade text-left" id="mdlinputmutasibank" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input Mutasi Bank <span id="namabank"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputmutasibank"></div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Mutasi Bank -->
<div class="modal fade text-left" id="mdleditmutasibank" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit Mutasi Bank <span id="namabankedit"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadeditmutasibank"></div>
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

        function loadbank() {
            var kode_cabang = $("#kode_cabang").val();
            var bank = "{{ Request('bank') }}";
            $.ajax({
                type: 'POST'
                , url: '/bank/getbankcabang'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , bank: bank
                }
                , cache: false
                , success: function(respond) {
                    $("#bank").html(respond);
                }
            });
        }
        loadbank();
        $("#kode_cabang").change(function() {
            loadbank();
        });

        $("#inputmutasibank").click(function(e) {
            e.preventDefault();
            var bank = "{{ Request('bank') }}";
            var kode_bank = "{{ Crypt::encrypt(Request('bank')) }}";
            var nama_bank = $("#bank option:selected").text();
            var kode_cabang = "{{ Request('kode_cabang') }}";
            //alert(bank);
            if (kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cabang Harus Diisi !, Klik Cari Data'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_cabang").focus();
                });
            } else if (bank == "") {
                swal({
                    title: 'Oops'
                    , text: 'Nomor Rekening / Bank  Harus Dipilih !, Klik Cari Data'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_bank").focus();
                });
            } else {
                $("#namabank").text(nama_bank);
                $("#loadinputmutasibank").load('/mutasibank/' + kode_bank + '/' + kode_cabang + '/create');
                $('#mdlinputmutasibank').modal({
                    backdrop: 'static'
                    , keyboard: false
                });
            }
        });

        $(".editmutasibank").click(function(e) {
            e.preventDefault();
            var nama_bank = $("#bank option:selected").text();
            var no_bukti = $(this).attr("no_bukti");
            //alert(bank);
            $("#namabankedit").text(nama_bank);
            $('#mdleditmutasibank').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $.ajax({
                type: 'POST'
                , url: '/mutasibank/edit'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_bukti: no_bukti

                }
                , cache: false
                , success: function(respond) {
                    $("#loadeditmutasibank").html(respond);
                }
            });

        });
    });

</script>
@endpush
