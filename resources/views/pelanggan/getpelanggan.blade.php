@extends('layouts.midone')
@section('content')


<div class="content-wrapper">

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Scan Qrcode</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Scan Qrcode</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body" id="loadpelanggan">
        <div class="row">
            <div class="col-lg-3 col-sm-12">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-content">
                                @if ($pelanggan->foto != null)
                                <img class="card-img-top img-fluid" style="height: 400px; object-fit:cover" id="foto" src="{{ url(Storage::url('pelanggan/'.$pelanggan->foto)) }}" alt="Card image cap">
                                @else
                                <img class="card-img-top img-fluid" id="foto" src="{{ asset('app-assets/images/slider/04.jpg') }}" alt="Card image cap">
                                @endif
                                <div class="card-body">
                                    <h4 class="card-title">
                                        <span id="pelanggan_text"></span>
                                    </h4>
                                    <b>Kode Pelanggan</b>
                                    <p class="card-text" id="kode_pelanggan">{{ $pelanggan->kode_pelanggan }}</p>
                                    <b>Nama Pelanggan</b>
                                    <p class="card-text" id="nama_pelanggan">{{ $pelanggan->nama_pelanggan }}</p>
                                    <b>Alamat</b>
                                    <p class="card-text" id="alamat_text">{{ $pelanggan->alamat_pelanggan }}</p>
                                    <b>No. HP</b>
                                    <p class="card-text" id="no_hp">{{ $pelanggan->no_hp }}</p>
                                    <b>Koordinat</b>
                                    <p class="card-text" id="koordinat">{{ $pelanggan->latitude }},{{ $pelanggan->longitude }}</p>
                                    <b>Limit Pelanggan</b>
                                    <p class="card-text" id="limitpelanggan">{{ rupiah($pelanggan->limitpel) }}</p>
                                    <b>Piutang Pelanggan</b>
                                    <p class="card-text" id="piutangpelanggan">{{ rupiah($piutang->sisapiutang) }}</p>
                                    <b>Sisa Limit</b>
                                    <p class="card-text" id="sisalimit">{{ rupiah($pelanggan->limitpel - $piutang->sisapiutang) }}</p>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-12">
                        <a href="#" class="btn btn-danger btn-block"><i class="feather icon-shopping-cart mr-1"></i><span id="demo"></span></a>
                    </div>
                </div>

            </div>
            <div class="col-lg-9 col-sm-12">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <h4 class="card-title">Data Histori Transaksi Pelanggan</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="penjualan-tab" data-toggle="tab" href="#penjualan" aria-controls="penjualan" role="tab" aria-selected="true">Transaksi Penjualan</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="limitkredit-tab" data-toggle="tab" href="#limitkredit" aria-controls="limitkredit" role="tab" aria-selected="false">Pengajuan Limit Kredit</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="penjualan" aria-labelledby="penjualan-tab" role="tabpanel">
                                    <form action="">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker value="{{ Request('dari') }}" />
                                            </div>
                                            <div class="col-lg-6">
                                                <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker value="{{ Request('sampai') }}" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12 col-sm-12">
                                                <x-inputtext label="No Faktur" field="no_fak_penj" icon="feather icon-credit-card" value="{{ Request('no_fak_penj') }}" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12 col-sm-12">
                                                <div class="form-group">
                                                    <button type="submit" name="submit" value="1" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Cari Data </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>No. Faktur</th>
                                                            <th>Tanggal</th>
                                                            <th>T/K</th>
                                                            <th>Total</th>
                                                            <th>Status</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody style="font-size: 12px">
                                                        @foreach ($penjualan as $d)
                                                        <tr>
                                                            <td>{{ $d->no_fak_penj }}</td>
                                                            <td>{{ date("d-m-Y",strtotime($d->tgltransaksi)) }}</td>

                                                            <td>
                                                                @if ($d->jenistransaksi=="tunai")
                                                                <span class="badge bg-success">Tunai</span>
                                                                @else
                                                                <span class="badge bg-warning">Kredit</span>
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
                                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                                    <a class="ml-1 detailpenjualan" href="/penjualan/{{ Crypt::encrypt($d->no_fak_penj) }}/showforsales"><i class=" fa fa-file-text info"></i></a>
                                                                </div>

                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            {{ $penjualan->links('vendor.pagination.vuexy') }}
                                        </div>
                                    </div>

                                </div>
                                <div class="tab-pane" id="limitkredit" aria-labelledby="limitkredit-tab" role="tabpanel">
                                    <table class="table table-hover-animation">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>No.Pengajuan</th>
                                                <th>Tanggal</th>
                                                <th>Jumlah</th>
                                                <th>Jatuhtempo</th>
                                                <th>Skor</th>
                                                <th>KP</th>
                                                <th>RSM</th>
                                                <th>GM</th>
                                                <th>DIRUT</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($limitkredit as $d)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $d->no_pengajuan }}</td>
                                                <td>{{ date("d-m-Y",strtotime($d->tgl_pengajuan)) }}</td>
                                                <td class="text-right">
                                                    @if (empty($d->jumlah_rekomendasi))
                                                    {{ rupiah($d->jumlah) }}
                                                    @else
                                                    {{ rupiah($d->jumlah_rekomendasi) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (empty($d->jatuhtempo_rekomendasi))
                                                    {{ rupiah($d->jatuhtempo) }} Hari
                                                    @else
                                                    {{ rupiah($d->jatuhtempo_rekomendasi) }} Hari
                                                    @endif
                                                </td>
                                                <td>{{ desimal($d->skor) }}</td>
                                                <td>
                                                    @if ($d->jumlah > 2000000)
                                                    @if (empty($d->kacab))
                                                    <span class="badge bg-warning"><i class="fa fa-history"></i></span>
                                                    @elseif(
                                                    !empty($d->kacab) && !empty($d->mm) && $d->status==2 ||
                                                    !empty($d->kacab) && empty($d->mm) && $d->status== 0 ||
                                                    !empty($d->kacab) && empty($d->mm) && $d->status== 1 ||
                                                    !empty($d->kacab) && empty($d->mm) && $d->status== 0 ||
                                                    !empty($d->kacab) && !empty($d->mm) && $d->status== 0 ||
                                                    !empty($d->kacab) && !empty($d->mm) && $d->status== 1
                                                    )
                                                    <span class="badge bg-success"><i class="fa fa-check"></i></span>
                                                    @else
                                                    <span class="badge bg-danger"><i class="fa fa-close"></i></span>
                                                    @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($d->jumlah > 5000000)
                                                    @if (empty($d->mm))
                                                    <span class="badge bg-warning"><i class="fa fa-history"></i></span>
                                                    @elseif(
                                                    !empty($d->mm) && !empty($d->gm) && $d->status == 2
                                                    || !empty($d->mm) && empty($d->gm) && $d->status == 1
                                                    || !empty($d->mm) && empty($d->gm) && $d->status == 0
                                                    || !empty($d->mm) && !empty($d->gm) && $d->status == 0
                                                    || !empty($d->mm) && !empty($d->gm) && $d->status == 1
                                                    )
                                                    <span class="badge bg-success"><i class="fa fa-check"></i></span>
                                                    @else
                                                    <span class="badge bg-danger"><i class="fa fa-close"></i></span>
                                                    @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($d->jumlah > 10000000)
                                                    @if (empty($d->gm))
                                                    <span class="badge bg-warning"><i class="fa fa-history"></i></span>
                                                    @elseif(
                                                    !empty($d->gm) && !empty($d->dirut) && $d->status == 2
                                                    || !empty($d->gm) && empty($d->dirut) && $d->status == 1
                                                    || !empty($d->gm) && empty($d->dirut) && $d->status == 0
                                                    || !empty($d->gm) && !empty($d->dirut) && $d->status == 0
                                                    || !empty($d->gm) && !empty($d->dirut) && $d->status == 1
                                                    )
                                                    <span class="badge bg-success"><i class="fa fa-check"></i></span>
                                                    @else
                                                    <span class="badge bg-danger"><i class="fa fa-close"></i></span>
                                                    @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($d->jumlah > 15000000)
                                                    @if (empty($d->dirut))
                                                    <span class="badge bg-warning"><i class="fa fa-history"></i></span>
                                                    @elseif(!empty($d->dirut) && $d->status != 2 )
                                                    <span class="badge bg-success"><i class="fa fa-check"></i></span>
                                                    @else
                                                    <span class="badge bg-danger"><i class="fa fa-close"></i></span>
                                                    @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="#"><i class="feather icon-printer info"></i></a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
