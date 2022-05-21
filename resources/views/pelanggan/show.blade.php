@extends('layouts.midone')
@section('titlepage', 'Detail Pelanggan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Detail Pelanggan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/pelanggan">Pelanggan</a></li>
                            <li class="breadcrumb-item"><a href="#">Detail Pelanggan</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <input type="hidden" id="cektutuplaporan">
    <div class="col-lg-12 col-sm-12">

        <div class="row">
            <div class="col-lg-3 col-sm-12">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-content">
                                @if (!empty($data->foto))
                                @php
                                $path = Storage::url('pelanggan/'.$data->foto);
                                @endphp
                                <img class="card-img img-fluid" src="{{ url($path) }}" alt="Card image">
                                @else
                                <img class="card-img img-fluid" src="{{ asset('app-assets/images/slider/04.jpg') }}" alt="Card image">
                                @endif
                                <div class="card-img-overlay overflow-hidden overlay-primary overlay-lighten-2">
                                    <h4 class="card-title text-white">{{ $data->nama_pelanggan }}</h4>
                                    <p class="card-text text-white">{{ $data->kode_pelanggan }} - {{ strtoupper($data->nama_cabang) }}
                                    </p>
                                    <p class="card-text"><small class="text-white">{{ $data->nama_karyawan }}</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <h4 class="card-title">Alamat</h4>
                                    <p class="card-text">{{ (!empty($data->alamat_pelanggan) ? $data->alamat_pelanggan : $data->alamat_toko) }}</p>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ $data->nik }}</span>
                                        NIK
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ $data->no_kk }}</span>
                                        No. KK
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ date("d-F-y",strtotime($data->tgl_lahir)) }}</span>
                                        Tanggal Lahir
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ $data->no_hp }}</span>
                                        No. HP
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ $data->pasar }}</span>
                                        Pasar
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ $data->hari }}</span>
                                        Hari
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ $data->latitude }}</span>
                                        Latitude
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ $data->longitude }}</span>
                                        Longitude
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ $data->jatuhtempo }} Hari</span>
                                        Jatuh Tempo Pembayaran
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">
                                            @if ($data->cara_pembayaran == 1)
                                            Bank Transfer
                                            @elseif ($data->cara_pembayaran == 2 )
                                            Advance Cash
                                            @elseif ($data->cara_pembayaran==3)
                                            Cheque / Billyet Giro
                                            @else
                                            Belum Di Tentukan
                                            @endif
                                        </span>
                                        Cara Pembayaran
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">
                                            @if ($data->status_outlet == 1)
                                            New Outlet
                                            @elseif ($data->status_outlet == 2 )
                                            Existing Outlet
                                            @else
                                            Belum Di Tentukan
                                            @endif
                                        </span>
                                        Status Outlet
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">
                                            @if ($data->type_outlet == 1)
                                            Grosir
                                            @elseif ($data->type_outlet == 2 )
                                            Retail
                                            @else
                                            Belum Di Tentukan
                                            @endif
                                        </span>
                                        Jenis Outlet
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ $data->lama_usaha }} </span>
                                        Lama Usaha
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">
                                            @if ($data->jaminan == 1)
                                            Ada
                                            @elseif ($data->jaminan == 2 )
                                            Tidak Ada
                                            @else
                                            Belum Di Tentukan
                                            @endif
                                        </span>
                                        Jaminan
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ $data->lama_langganan }} </span>
                                        Lama Berlangganan
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">
                                            @if ($data->cara_pembayaran == 1)
                                            Ada
                                            @elseif ($data->cara_pembayaran == 2 )
                                            Tidak Ada
                                            @else
                                            Belum Di Tentukan
                                            @endif
                                        </span>
                                        Jaminan
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ rupiah($data->omset_toko) }} </span>
                                        Omset Toko
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">{{ rupiah($data->limitpel) }} </span>
                                        Limit Pelanggan
                                    </li>
                                </ul>
                                <div class="card-body">
                                    <a href="#" class="card-link">Card link</a>
                                    <a href="#" class="card-link">Another link</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9-col-md-9 col-sm-9">
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
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>No. Faktur</th>
                                                        <th>Tanggal</th>
                                                        <th>Salesman</th>
                                                        <th>T/K</th>
                                                        <th>Total</th>
                                                        <th>Status</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($penjualan as $d)
                                                    <tr>
                                                        <td>{{ $loop->iteration; }}</td>
                                                        <td>{{ $d->no_fak_penj }}</td>
                                                        <td>{{ date("d-m-Y",strtotime($d->tgltransaksi)) }}</td>
                                                        <td>{{ ucwords(strtolower($d->nama_karyawan)) }}</td>
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
                                                                <a class="ml-1" href="/penjualan/{{\Crypt::encrypt($d->no_fak_penj)}}/edit"><i class="feather icon-edit success"></i></a>
                                                                <a class="ml-1 detailpenjualan" href="/penjualan/{{ Crypt::encrypt($d->no_fak_penj) }}/show"><i class=" feather icon-file-text info"></i></a>
                                                                <form method="POST" name="deleteform" class="deleteform" action="/penjualan/{{ Crypt::encrypt($d->no_fak_penj) }}/delete">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <a href="#" tanggal="{{ $d->tgltransaksi }}" class="delete-confirm ml-1">
                                                                        <i class="feather icon-trash danger"></i>
                                                                    </a>
                                                                </form>
                                                                <div class="dropdown ml-1">
                                                                    <a class="dropdown-toggle mr-1" type="button" id="dropdownMenuButton300" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        <i class="feather icon-printer primary"></i>
                                                                    </a>
                                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton300" x-placement="top-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -7px, 0px);">
                                                                        <a class="dropdown-item" target="_blank" href="/penjualan/cetakfaktur/{{ Crypt::encrypt($d->no_fak_penj) }}"><i class="feather icon-printer mr-1"></i>Cetak Faktur</a>
                                                                        <a class="dropdown-item" target="_blank" href="/penjualan/cetaksuratjalan/{{ Crypt::encrypt($d->no_fak_penj) }}/1"><i class="feather icon-printer mr-1"></i>Cetak Surat Jalan 1</a>
                                                                        <a class="dropdown-item" target="_blank" href="/penjualan/cetaksuratjalan/{{ Crypt::encrypt($d->no_fak_penj) }}/2"><i class="feather icon-printer mr-1"></i>Cetak Surat Jalan 2</a>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
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
                    , jenislaporan: "penjualan"
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
    });

</script>
@endpush
