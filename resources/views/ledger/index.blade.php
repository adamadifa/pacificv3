@extends('layouts.midone')
@section('titlepage','Ledger')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Ledger</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/ledger">Ledger</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <input type="hidden" id="cektutuplaporan">
        <div class="col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <a href="#" id="inputledger" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                <div class="card-body">
                    <form action="/ledger" id="frmcari">
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
                                <div class="form-group">
                                    <select name="ledger" id="ledger" class="form-control">
                                        <option value="">Pilih Ledger</option>
                                        @foreach ($bank as $d)
                                        <option value="{{ $d->kode_bank }}" {{ $d->kode_bank == Request('ledger') ?'selected':'' }}>{{ $d->nama_bank }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i> Cari Data </button>
                            </div>
                        </div>
                    </form>
                    @include('layouts.notification')
                    <div class="table-responsive">
                        <table class="table table-hover-animation" style="font-size: 13px">
                            <thead class="thead-dark" id="thead">
                                <tr>
                                    <th style="width:1%">No</th>
                                    <th style="width:8%">Tgl</th>
                                    <th style="width:10%">Tgl Penerimaan</th>
                                    <th style="width:15%">Pelanggan</th>
                                    <th style="width:30%">Keterangan</th>
                                    <th style="width:20%">Kode Akun</th>
                                    <th style="width:5%">Peruntukan</th>
                                    <th style="width:10%">Debet</th>
                                    <th style="width:10%">Kredit</th>
                                    <th style="width:10%">Saldo</th>
                                    <th style="width:">Aksi</th>
                                </tr>
                                <tr>
                                    <th colspan="9">Saldo Awal</th>
                                    <th class="text-right">{{ rupiah($saldoawal) }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                                @php
                                $totalkredit = 0;
                                $totaldebet = 0;
                                $saldo = $saldoawal;
                                @endphp
                                @foreach ($ledger as $d)
                                @php
                                if ($d->status_dk == 'K') {
                                $kredit = $d->jumlah;
                                $debet = 0;
                                $jumlah = $d->jumlah;
                                } else {
                                $debet = $d->jumlah;
                                $kredit = 0;
                                $jumlah = -$d->jumlah;
                                }

                                $saldo += $jumlah;

                                if ($d->no_ref != "") {
                                if ($d->kategori == 'PMB') {
                                $color = "rgba(39, 196, 245, 0.08)";
                                $text = "black";
                                } else if ($d->kategori == 'PNJ') {
                                $color = "rgba(80, 39, 245, 0.08)";
                                $text = "black";
                                }else if ($d->kategori == 'GDJ') {
                                $color = "rgba(240, 95, 95, 0.24)";

                                $text = "black";
                                } else {
                                if (!empty($d->kode_cr)) {
                                $color = "rgba(209, 203, 11, 0.15)";
                                $text = "";
                                } else {
                                $color = "";
                                $text = "";
                                }
                                }
                                } else {
                                if (!empty($d->kode_cr)) {
                                $color = "rgba(209, 203, 11, 0.15)";
                                $text = "";
                                } else {
                                $color = "";
                                $text = "";
                                }
                                }

                                $totaldebet += $debet;
                                $totalkredit += $kredit;
                                @endphp
                                <tr style="background-color:{{ $color }}">
                                    <td scope="row">{{ $loop->iteration }}</td>
                                    <td>{{ date("d-m-Y",strtotime($d->tgl_ledger)) }}</td>

                                    <td>{{ !empty($d->tgl_penerimaan) ? date("d-m-Y",strtotime($d->tgl_penerimaan)) : '' }}</td>
                                    <td>{{ ucwords(strtolower($d->pelanggan)) }}</td>
                                    <td>{{ ucwords(strtolower($d->keterangan)) }}</td>
                                    <td>{{ $d->kode_akun }} {{ $d->nama_akun }}</td>
                                    <td>{{ $d->peruntukan }} {{ $d->peruntukan=="PC" ? "(".$d->ket_peruntukan. ")"  : "" }}</td>
                                    <td class="text-right danger">{{ !empty($debet) ? rupiah($debet) : '' }}</td>
                                    <td class="text-right success">{{ !empty($kredit) ? rupiah($kredit) : '' }}</td>
                                    <td class="text-right info">{{ rupiah($saldo) }}</td>
                                    <td>
                                        @if (empty($d->kategori))

                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a class="ml-1 edit" nobukti="{{ $d->no_bukti }}" href="#"><i class="feather icon-edit success"></i></a>
                                            <form method="POST" class="deleteform" action="/ledger/{{ Crypt::encrypt($d->no_bukti) }}/delete">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" tanggal="{{ $d->tgl_ledger }}" class="delete-confirm ml-1">
                                                    <i class="feather icon-trash danger"></i>
                                                </a>
                                            </form>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td colspan="7" style="font-weight: bold">TOTAL</td>
                                    <td style="font-weight: bold" class="text-right">{{ rupiah($totaldebet) }}</td>
                                    <td style="font-weight: bold" class="text-right">{{ rupiah($totalkredit) }}</td>
                                    <td style="font-weight: bold" class="text-right">{{ rupiah($saldo) }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="mdlinputledger" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input Ledger <span id="namaledger"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputledger"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdleditledger" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit Ledger <span id="namaledgeredit"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadeditledger"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        function cektutuplaporan(tanggal) {
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                    , jenislaporan: "ledger"
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#cektutuplaporan").val(respond);
                }
            });
        }
        $('.delete-confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            var tanggal = $(this).attr("tanggal");
            cektutuplaporan(tanggal);
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
                        var cektutuplaporan = $("#cektutuplaporan").val();
                        if (cektutuplaporan > 0) {
                            swal("Oops", "Laporan Periode Ini Sudah Di Tutup !", "warning");
                            return false;
                        } else {
                            form.submit();
                        }
                    }
                });
        });
        $("#inputledger").click(function(e) {
            e.preventDefault();
            var ledger = "{{ Request('ledger') }}";
            var kode_ledger = "{{ Crypt::encrypt(Request('ledger')) }}";
            var nama_ledger = $("#ledger option:selected").text();
            //alert(bank);
            if (ledger == "") {
                swal({
                    title: 'Oops'
                    , text: 'Ledger Harus Dipilih ! & Klik Cari Data'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_bank").focus();
                });
            } else {
                $("#namaledger").text(nama_ledger);
                $("#loadinputledger").load('/ledger/' + kode_ledger + '/create');
                $('#mdlinputledger').modal({
                    backdrop: 'static'
                    , keyboard: false
                });
            }
        });


        $(".edit").click(function(e) {
            e.preventDefault();
            var ledger = "{{ Request('ledger') }}";
            var kode_ledger = "{{ Crypt::encrypt(Request('ledger')) }}";
            var nama_ledger = $("#ledger option:selected").text();
            var no_bukti = $(this).attr("nobukti");
            //alert(bank);
            if (ledger == "") {
                swal({
                    title: 'Oops'
                    , text: 'Ledger Harus Dipilih ! & Klik Cari Data'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_bank").focus();
                });
            } else {
                $("#namaledgeredit").text(nama_ledger);
                $("#loadeditledger").load('/ledger/' + no_bukti + '/edit');
                $('#mdleditledger').modal({
                    backdrop: 'static'
                    , keyboard: false
                });
            }
        });

    });

</script>
@endpush
