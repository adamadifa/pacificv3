@extends('layouts.midone')
@section('titlepage', 'Dashboard')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
    </div>
    <div class="content-body">
        <!-- Dashboard Analytics Start -->
        <section id="dashboard-analytics">
            <div class="row">
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="card bg-analytics text-white">
                        <div class="card-content">
                            <div class="card-body text-center">
                                <img src="{{asset('app-assets/images/elements/decore-left.png')}}" class="img-left" alt="card-img-left">
                                <img src="{{asset('app-assets/images/elements/decore-right.png')}}" class="img-right" alt="card-img-right">
                                <div class="avatar avatar-xl bg-primary shadow mt-0">
                                    <div class="avatar-content">
                                        <i class="feather icon-award white font-large-1"></i>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <h3 class="mb-1 text-white">Selamat Datang, {{ Auth::user()->name }} </h3>
                                    <h4 class="text-white">{{ date('d F Y') }} </h4>
                                    <p class="m-auto w-75">Anda Masuk Sebagai Level {{ ucwords(Auth::user()->level) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Rekap DPPP</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-body">
                                    <input type="hidden" name="cabangdpp" id="cabangdppp" value="{{ Auth::user()->kode_cabang }}">
                                    <div class="row mb-1">
                                        <div class="col-12">
                                            <select class="form-control" id="bulandppp">
                                                <?php for ($a = 1; $a <= 12; $a++) { ?>
                                                <option <?php if (date("m") == $a) {
                                                                  echo "selected";
                                                                } ?> value="<?php echo $a;  ?>"><?php echo $bulan[$a]; ?></option>
                                                <?php } ?>
                                            </select>

                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-12">
                                            <select class="form-control" id="tahundppp">
                                                <?php
                                                    $tahun = date("Y");
                                                    for ($t = 2019; $t <= $tahun; $t++) { ?>
                                                <option <?php if (date("Y") == $t) {
                                                                  echo "selected";
                                                                } ?> value="<?php echo $t;  ?>"><?php echo $t; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <a href="#" id="tampilkandppp" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Rekap Kendaraan</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-body">
                                    <input type="hidden" name="cabangkendaraan" id="cabangkendaraan" value="{{ Auth::user()->kode_cabang }}">
                                    <div class="row mb-1">
                                        <div class="col-12">
                                            <select class="form-control" id="bulankendaraan">
                                                <?php for ($a = 1; $a <= 12; $a++) { ?>
                                                <option <?php if (date("m") == $a) {
                                                                  echo "selected";
                                                                } ?> value="<?php echo $a;  ?>"><?php echo $bulan[$a]; ?></option>
                                                <?php } ?>
                                            </select>

                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-12">
                                            <select class="form-control" id="tahunkendaraan">
                                                <?php
                                                    $tahun = date("Y");
                                                    for ($t = 2019; $t <= $tahun; $t++) { ?>
                                                <option <?php if (date("Y") == $t) {
                                                                  echo "selected";
                                                                } ?> value="<?php echo $t;  ?>"><?php echo $t; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <a href="#" id="tampilkankendaraan" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Rekap Penjualan</div>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <table class="table">
                                    <tr>
                                        <td>Total Bruto</td>
                                        <td></td>
                                        <td class="text-right">{{ rupiah($rekappenjualan->totalbruto) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total Retur</td>
                                        <td></td>
                                        <td class="text-right">{{ rupiah($rekappenjualan->totalretur) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Penyesuaian</td>
                                        <td></td>
                                        <td class="text-right">{{ rupiah($rekappenjualan->totalpenyharga) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Potongan</td>
                                        <td></td>
                                        <td class="text-right">{{ rupiah($rekappenjualan->totalpotongan) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Potongan Istimewa</td>
                                        <td></td>
                                        <td class="text-right">{{ rupiah($rekappenjualan->totalpotistimewa) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Netto</td>
                                        <td></td>
                                        @php
                                        $totalnetto = $rekappenjualan->totalbruto - $rekappenjualan->totalretur - $rekappenjualan->totalpenyharga - $rekappenjualan->totalpotongan - $rekappenjualan->totalpotistimewa;
                                        @endphp
                                        <td class="text-right">{{ rupiah($totalnetto) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Pending</td>
                                        <td></td>
                                        @php
                                        $totalnettopending = $rekappenjualan->totalbrutopending - $rekappenjualan->totalreturpending - $rekappenjualan->totalpenyhargapending - $rekappenjualan->totalpotonganpending - $rekappenjualan->totalpotistimewapending;
                                        @endphp
                                        <td class="text-right text-warning">{{ rupiah($totalnettopending) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Reguler</td>
                                        <td></td>
                                        <td class="text-right">{{ rupiah($totalnetto - $totalnettopending) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
