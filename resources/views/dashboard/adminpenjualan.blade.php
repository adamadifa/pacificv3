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
            </div>
        </section>
        <section>
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
