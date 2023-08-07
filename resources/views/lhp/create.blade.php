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
                            <i class="fa fa-send my-float"></i> SUBMIT LHP
                        </button>
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-bordered">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th rowspan="2">PELANGGAN</th>
                                            <th rowspan="2">AREA</th>
                                            <th rowspan="2">No. BON</th>
                                            <th colspan="10">NAMA PRODUK</th>
                                            <th colspan="2">PENJUALAN</th>
                                            <th rowspan="2">TITIPAN</th>
                                            <th rowspan="2">TRANSFER</th>
                                            <th rowspan="2">GIRO</th>
                                            <th rowspan="2">VOUCHER</th>
                                            <th rowspan="2">AKSI</th>
                                        </tr>
                                        <tr>
                                            <th>AB</th>
                                            <th>AR</th>
                                            <th>AS</th>
                                            <th>BB</th>
                                            <th>DP</th>
                                            <th>SC</th>
                                            <th>SP8P</th>
                                            <th>SP8</th>
                                            <th>SP</th>
                                            <th>SP500</th>
                                            <th>TUNAI</th>
                                            <th>KREDIT</th>
                                        </tr>
                                    </thead>
                                    @php
                                    $totalAB = 0;
                                    $totalAR = 0;
                                    $totalASE = 0;
                                    $totalBB = 0;
                                    $totalDEP = 0;
                                    $totalSC = 0;
                                    $totalSP8P = 0;
                                    $totalSP8 =0;
                                    $totalSP = 0;
                                    $totalSP500 = 0;
                                    $totaltunai = 0;
                                    $totalkredit = 0;
                                    $totaltagihan1 = 0;
                                    $totaltagihan2 = 0;
                                    $totaltransfer = 0;
                                    $totalgiro =0;

                                    $totaltitipan1 =0;
                                    $totaltransfer1 = 0;
                                    $totalgiro1 = 0;
                                    $totalvoucher1 = 0;


                                    $totaltitipan2 =0;
                                    $totaltransfer2 = 0;
                                    $totalgiro2 = 0;
                                    $totalvoucher2 = 0;

                                    $totaltransfer3 = 0;
                                    $totalgiro3 = 0;

                                    @endphp
                                    <tbody>
                                        @foreach ($penjualan as $d)
                                        @php
                                        $totalAB += $d->AB;
                                        $totalAR += $d->AR;
                                        $totalASE += $d->ASE;
                                        $totalBB += $d->BB;
                                        $totalDEP += $d->DEP;
                                        $totalSC += $d->SC;
                                        $totalSP8P += $d->SP8P;
                                        $totalSP8 += $d->SP8;
                                        $totalSP += $d->SP;
                                        $totalSP500 += $d->SP500;
                                        $totaltunai += $d->totaltunai;
                                        $totalkredit += $d->totalkredit;
                                        $totaltagihan1 += ($d->totalbayar + $d->totalgiro + $d->totaltransfer);

                                        $totaltitipan1 += $d->totalbayar;
                                        $totaltransfer1 += $d->totaltransfer;
                                        $totalgiro1 += $d->totalgiro;
                                        $totalvoucher1 += $d->totalvoucher;
                                        @endphp

                                        <tr>
                                            <td>{{ ucwords(strtolower($d->nama_pelanggan)) }}</td>
                                            <td>{{ ucwords(strtolower($d->pasar)) }}</td>
                                            <td>{{ $d->no_fak_penj }}</td>
                                            <td style="text-align:right; font-weight:bold">
                                                <?php if (!empty($d->AB)) { echo desimal($d->AB); } ?>
                                            </td>
                                            <td style="text-align:right; font-weight:bold">
                                                <?php if (!empty($d->AR)) { echo desimal($d->AR); } ?>
                                            </td>
                                            <td style="text-align:right; font-weight:bold">
                                                <?php if (!empty($d->ASE)) { echo desimal($d->ASE);} ?>
                                            </td>
                                            <td style="text-align:right; font-weight:bold">
                                                <?php if (!empty($d->BB)) { echo desimal($d->BB);} ?>
                                            </td>
                                            <td style="text-align:right; font-weight:bold">
                                                <?php if (!empty($d->DEP)) { echo desimal($d->DEP);} ?>
                                            </td>
                                            <td style="text-align:right; font-weight:bold">
                                                <?php if (!empty($d->SC)) { echo desimal($d->SC);} ?>
                                            </td>
                                            <td style="text-align:right; font-weight:bold">
                                                <?php if (!empty($d->SP8P)) { echo desimal($d->SP8P);} ?>
                                            </td>
                                            <td style="text-align:right; font-weight:bold">
                                                <?php if (!empty($d->SP8)) { echo desimal($d->SP8);} ?>
                                            </td>
                                            <td style="text-align:right; font-weight:bold">
                                                <?php if (!empty($d->SP)) { echo desimal($d->SP);} ?>
                                            </td>
                                            <td style="text-align:right; font-weight:bold">
                                                <?php if (!empty($d->SP500)) { echo desimal($d->SP500);} ?>
                                            </td>
                                            <td style="text-align:right">
                                                <?php if (!empty($d->totaltunai)) { echo rupiah($d->totaltunai);} ?>
                                            </td>
                                            <td style="text-align:right">
                                                <?php if (!empty($d->totalkredit)) { echo rupiah($d->totalkredit);} ?>
                                            </td>
                                            <td style="text-align:right">
                                                <?php if (!empty($d->totalbayar)) { echo rupiah($d->totalbayar);} ?>
                                            </td>
                                            <td style="text-align:right">
                                                <?php if (!empty($d->totaltransfer)) { echo rupiah($d->totaltransfer);} ?>
                                            </td>
                                            <td style="text-align:right">
                                                <?php if (!empty($d->totalgiro)) { echo rupiah($d->totalgiro);} ?>
                                            </td>
                                            <td style="text-align:right">
                                                <?php if (!empty($d->totalvoucher)) { echo rupiah($d->totalvoucher);} ?>
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
                                        @foreach ($historibayar as $d)
                                        @php
                                        $tagihan2 = ($d->totalbayar + $d->totalgiro + $d->totaltransfer);
                                        $totaltagihan2 +=$tagihan2;
                                        $totaltitipan2 += $d->totalbayar;
                                        $totaltransfer2 += $d->totaltransfer;
                                        $totalgiro2 += $d->totalgiro;
                                        $totalvoucher2 += $d->totalvoucher;
                                        @endphp
                                        <tr>
                                            <td>{{ ucwords(strtolower($d->nama_pelanggan)) }}</td>
                                            <td>{{ ucwords(strtolower($d->pasar)) }}</td>
                                            <td>{{ $d->no_fak_penj }}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td style="text-align:right">
                                                <?php if (!empty($d->totalbayar)) { echo rupiah($d->totalbayar);} ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($d->totaltransfer)) { echo rupiah($d->totaltransfer);} ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($d->totalgiro)) { echo rupiah($d->totalgiro);} ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($d->totalvoucher)) { echo rupiah($d->totalvoucher);} ?>
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
                                        @foreach ($giro as $d)
                                        @php
                                        $totalgiro += $d->totalgiro;
                                        @endphp
                                        <tr>
                                            <td>{{ ucwords(strtolower($d->nama_pelanggan)) }}</td>
                                            <td>{{ ucwords(strtolower($d->pasar)) }}</td>
                                            <td>{{ $d->no_fak_penj }}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td style="text-align:right">
                                                <?php if (!empty($d->totalgiro)) { echo rupiah($d->totalgiro);} ?>
                                            </td>
                                            <td></td>
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

                                        @foreach ($transfer as $d)
                                        @php
                                        $totaltransfer += $d->totaltransfer;
                                        $totaltransfer3 += $d->totaltransfer;
                                        @endphp
                                        <tr>
                                            <td>{{ ucwords(strtolower($d->nama_pelanggan)) }}</td>
                                            <td>{{ ucwords(strtolower($d->pasar)) }}</td>
                                            <td>{{ $d->no_fak_penj }}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td style="text-align:right">
                                                <?php if (!empty($d->totaltransfer)) { echo rupiah($d->totaltransfer);} ?>
                                            </td>
                                            <td></td>
                                            <td></td>
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
