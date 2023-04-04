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
                                    <p class="m-auto w-75">Anda Masuk Sebagai Level {{ ucwords(Auth::user()->level) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="card text-center">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="avatar bg-rgba-info p-50 m-0 mb-1">
                                    <div class="avatar-content">
                                        <i class="feather icon-users text-info font-medium-5"></i>
                                    </div>
                                </div>
                                <h2 class="text-bold-700">{{ $karyawan->jmlkaryawan }}</h2>
                                <p class="mb-0 line-ellipsis">Data Karyawan</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="card text-center">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="avatar bg-rgba-success p-50 m-0 mb-1">
                                    <div class="avatar-content">
                                        <i class="feather icon-users text-success font-medium-5"></i>
                                    </div>
                                </div>
                                <h2 class="text-bold-700">{{ $karyawan->jmlkaryawantetap }}</h2>
                                <p class="mb-0 line-ellipsis">Tetap</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="card text-center">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="avatar bg-rgba-warning p-50 m-0 mb-1">
                                    <div class="avatar-content">
                                        <i class="feather icon-users text-warning font-medium-5"></i>
                                    </div>
                                </div>
                                <h2 class="text-bold-700">{{ $karyawan->jmlkaryawankontrak }}</h2>
                                <p class="mb-0 line-ellipsis">Kontrak</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="card text-center">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="avatar bg-rgba-danger p-50 m-0 mb-1">
                                    <div class="avatar-content">
                                        <i class="feather icon-users text-danger font-medium-5"></i>
                                    </div>
                                </div>
                                <h2 class="text-bold-700">{{ $karyawan->jmlkaryawanos }}</h2>
                                <p class="mb-0 line-ellipsis">Outsourcing</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card overflow-hidden">
                                <div class="card-header">
                                    <h4 class="card-title">Karyawan Habis Kontrak</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link" id="profile-tab-justified" data-toggle="tab" href="#kontrak-lewat" role="tab" aria-controls="profile-just" aria-selected="false">Lewat JT<span class="badge badge-pill bg-danger"></span></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link active" id="home-tab-justified" data-toggle="tab" href="#home-just" role="tab" aria-controls="home-just" aria-selected="true">Bulan Ini <span class="badge badge-pill bg-danger"></span></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="profile-tab-justified" data-toggle="tab" href="#profile-just" role="tab" aria-controls="profile-just" aria-selected="false">Bulan Depan <span class="badge badge-pill bg-warning"></span></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="messages-tab-justified" data-toggle="tab" href="#messages-just" role="tab" aria-controls="messages-just" aria-selected="false">2 Bulan Lagi <span class="badge badge-pill bg-success"></span></a>
                                            </li>
                                        </ul>
                                        <div class="tab-content pt-1">
                                            <div class="tab-pane" id="kontrak-lewat" role="tabpanel" aria-labelledby="profile-tab-justified">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>NIK</th>
                                                            <th>Nama Karyawan</th>
                                                            <th>Jabatan</th>
                                                            <th>Dept</th>
                                                            <th>MP/PCF</th>
                                                            <th>Kantor</th>
                                                            <th>Akhir Kontrak</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($kontrak_lewat as $d)
                                                        <tr>
                                                            <td>{{ $d->nik }}</td>
                                                            <td>{{ $d->nama_karyawan }}</td>
                                                            <td>{{ $d->nama_jabatan }}</td>
                                                            <td>{{ $d->kode_dept }}</td>
                                                            <td>{{ $d->id_perusahaan }}</td>
                                                            <td>{{ $d->id_kantor }}</td>
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
        </section>
    </div>
</div>
@endsection
