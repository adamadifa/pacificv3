@extends('layouts.midone')
@section('titlepage', 'Dashboard')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
    </div>
    <div class="content-body">
        <!-- Dashboard Analytics Start -->
        <section>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="row">
                        <div class="col-12">
                            <form action="/dashboardsfa">
                                <div class="row">
                                    <div class="col-4">
                                        <x-inputtext field="tanggal" value="{{ Request('tanggal') }}" icon="feather icon-calendar" label="Tanggal" datepicker />
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <select name="kode_cabang" id="kode_cabang" class="form-control">
                                                <option value="">Pilih Cabang</option>
                                                @foreach ($cabang as $d)
                                                <option {{ Request('kode_cabang') == $d->kode_cabang ? "selected" : "" }} value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <select name="id_karyawan" id="id_karyawan" class="form-control">
                                                <option value="">Salesman</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <button class="btn btn-primary w-100">
                                                <i class="feather icon-search mr-1"></i>
                                                Get Data
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-hover-animation table-bordered">
                                <thead>
                                    <tr>
                                        <th rowspan="2">No.</th>
                                        {{-- <th rowspan="2">Tanggal</th>
                                        <th rowspan="2">Cabang</th>
                                        <th rowspan="2">ID Salesman</th>
                                        <th rowspan="2">Salesman</th> --}}
                                        <th colspan="2" style="text-align: center">Pelanggan</th>
                                        <th rowspan="2">Durasi</th>
                                        <th colspan="9" style="text-align: center">Transaksi</th>
                                        <th colspan="2" style="text-align: center">Penjualan</th>
                                        <th colspan="5" style="text-align: center">Pembayaran</th>
                                    </tr>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Pelanggan</th>
                                        {{-- <th style="width: 15%">Alamat</th> --}}
                                        <th class="text-center">AR</th>
                                        <th class="text-center">AS</th>
                                        <th class="text-center">AB</th>
                                        <th class="text-center">BB</th>
                                        <th class="text-center">DP</th>
                                        <th class="text-center">SP8</th>
                                        <th class="text-center">SP1000</th>
                                        <th class="text-center">SP500</th>
                                        <th class="text-center">SC</th>
                                        <th class="text-center">Tunai</th>
                                        <th class="text-center">Kredit</th>
                                        <th class="text-center">Tunai</th>
                                        <th class="text-center">Titipan</th>
                                        <th class="text-center">Transfer</th>
                                        <th class="text-center">Giro</th>
                                        <th class="text-center">Voucher</th>
                                    </tr>

                                </thead>
                                <tbody style="font-size: 11px">
                                    @foreach ($rekap as $d)
                                    @php
                                    if ($d->checkin_time != "NA") {
                                    $checkin = new DateTime ($d->checkin_time);
                                    $checkout = new DateTime ($d->checkout_time);
                                    $diff = $checkin->diff($checkout);
                                    $minutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
                                    }else {
                                    $minutes = "NA";
                                    }

                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        {{-- <td>{{ date("d-m-y",strtotime($d->tgltransaksi)) }}</td>
                                        <td>{{ $d->kode_cabang }}</td>
                                        <td>{{ $d->id_karyawan }}</td>
                                        <td>{{ $d->nama_karyawan }}</td> --}}
                                        <td>{{ $d->kode_pelanggan }}</td>
                                        <td>{{ $d->nama_pelanggan }}</td>
                                        {{-- <td>{{ ucwords(strtolower($d->alamat_pelanggan)) }}</td> --}}
                                        <td>{!! $minutes != "NA" ? $minutes." Menit" : "<span class='danger'>Tidak Checkin</span>" !!} </td>
                                        <td class="text-center">{{ !empty($d->qty_AR) && $d->qty_AR > 0 ?  desimal($d->qty_AR) : "" }}</td>
                                        <td class="text-center">{{ !empty($d->qty_AS) && $d->qty_AS > 0 ?  desimal($d->qty_AS) : "" }}</td>
                                        <td class="text-center">{{ !empty($d->qty_AB) && $d->qty_AB > 0 ?  desimal($d->qty_AB) : "" }}</td>
                                        <td class="text-center">{{ !empty($d->qty_BB) && $d->qty_BB > 0 ?  desimal($d->qty_BB) : "" }}</td>
                                        <td class="text-center">{{ !empty($d->qty_DEP) && $d->qty_DEP > 0 ? desimal($d->qty_DEP) : "" }}</td>
                                        <td class="text-center">{{ !empty($d->qty_SP8) && $d->qty_SP8 > 0 ? desimal($d->qty_SP8) : "" }}</td>
                                        <td class="text-center">{{ !empty($d->qty_SP) && $d->qty_SP > 0 ?  desimal($d->qty_SP) : "" }}</td>
                                        <td class="text-center">{{ !empty($d->qty_SP500) && $d->qty_SP500 > 0 ? desimal($d->qty_SP500) : "" }}</td>
                                        <td class="text-center">{{ !empty($d->qty_SC) && $d->qty_SC > 0 ?  desimal($d->qty_SC) : "" }}</td>
                                        <td class="text-right">{{ !empty($d->jml_tunai) ? rupiah($d->jml_tunai) : "" }}</td>
                                        <td class="text-right">{{ !empty($d->jml_kredit) ? rupiah($d->jml_kredit) : "" }}</td>
                                        <td class="text-right">{{ !empty($d->bayar_tunai) ? rupiah($d->bayar_tunai) : "" }}</td>
                                        <td class="text-right">{{ !empty($d->bayar_titipan) ? rupiah($d->bayar_titipan) : "" }}</td>
                                        <td class="text-right">{{ !empty($d->bayar_transfer) ? rupiah($d->bayar_transfer) : "" }}</td>
                                        <td class="text-right">{{ !empty($d->bayar_giro) ? rupiah($d->bayar_giro) : "" }}</td>
                                        <td class="text-right">{{ !empty($d->bayar_voucher) ? rupiah($d->bayar_voucher) : "" }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {

        var kode_cabang = $("#kode_cabang").val();
        loadsalesmancabang(kode_cabang);

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
