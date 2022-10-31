@extends('layouts.midone')
@section('titlepage', 'Detail Kendaraan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Detail Kendaraan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/kendaraan/{{ Crypt::encrypt($data->no_polisi) }}/show">Detail Kendaraan</a></li>
                            <li class="breadcrumb-item"><a href="#">Detail Kendaraan</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="col-12">
            <div class="row">
                <div class="col-lg-3 col-sm-12">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-content">
                                    <img class="card-img-top img-fluid" src="{{ asset('app-assets/images/truck.jpg') }}" alt="Card image cap">
                                    <div class="card-body">
                                        <h4 class="card-title">Data Kendaraan</h4>
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <span class="badge badge-pill bg-primary float-right">{{ $data->no_polisi }}</span>
                                            No. Polisi
                                        </li>
                                        <li class="list-group-item">
                                            <span class="badge badge-pill bg-primary float-right">{{ $data->merk }}</span>
                                            Merk
                                        </li>
                                        <li class="list-group-item">
                                            <span class="badge badge-pill bg-primary float-right">{{ $data->tipe_kendaraan }}</span>
                                            Tipe Kendaraan
                                        </li>
                                        <li class="list-group-item">
                                            <span class="badge badge-pill bg-primary float-right">{{ $data->tipe }}</span>
                                            Tipe
                                        </li>
                                        <li class="list-group-item">
                                            <span class="badge badge-pill bg-primary float-right">{{ $data->no_rangka }}</span>
                                            No. Rangka
                                        </li>
                                        <li class="list-group-item">
                                            <span class="badge badge-pill bg-primary float-right">{{ $data->no_mesi }}</span>
                                            No. Mesin
                                        </li>
                                        <li class="list-group-item">
                                            <span class="badge badge-pill bg-primary float-right">{{ $data->no_polisi }}</span>
                                            No. Polisi
                                        </li>
                                        <li class="list-group-item">
                                            <span class="badge badge-pill bg-primary float-right">{{ $data->atas_nama }}</span>
                                            Atas Nama
                                        </li>
                                        <li class="list-group-item">
                                            {{ $data->alamat }}
                                        </li>

                                        <li class="list-group-item">
                                            <span class="badge badge-pill bg-primary float-right">{{ $data->tahun_pembuatan }}</span>
                                            Tahun Pembuatan
                                        </li>
                                        <li class="list-group-item">
                                            <span class="badge badge-pill bg-primary float-right">{{ $data->jatuhtempo_kir != null ? DateToIndo2($data->jatuhtempo_kir)  : ''}}</span>
                                            KIR
                                        </li>
                                        <li class="list-group-item">
                                            <span class="badge badge-pill bg-primary float-right">{{ $data->jatuhtempo_pajak_satutahun != null ? DateToIndo2($data->jatuhtempo_pajak_satutahun)  : ''}}</span>
                                            Pajak 1 Tahun
                                        </li>
                                        <li class="list-group-item">
                                            <span class="badge badge-pill bg-primary float-right">{{ $data->jatuhtempo_pajak_limatahun != null ? DateToIndo2($data->jatuhtempo_pajak_limatahun)  : ''}}</span>
                                            Pajak 5 Tahun
                                        </li>
                                        <li class="list-group-item">
                                            <span class="badge badge-pill bg-primary float-right">{{ $data->jenis }}</span>
                                            Jenis
                                        </li>
                                        <li class="list-group-item">
                                            <span class="badge badge-pill bg-primary float-right">{{ $data->kode_cabang }}</span>
                                            Cabang
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Histori Service Kendaraan</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>No. Invoice</th>
                                        <th>Tanggal</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Histori Mutasi Kendaraan</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>No. Mutasi</th>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mutasikendaraan as $d)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $d->no_mutasi }}</td>
                                        <td>{{ DateToIndo2($d->tgl_mutasi) }}</td>
                                        <td>
                                            Mutasi Dari {{ $d->kode_cabang_old }} ke {{ $d->kode_cabang_new }}
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
@endsection
