@extends('layouts.midone')
@section('titlepage','Pengajuan Izin')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Kontrak</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/kontrak">Kontrak</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        @include('layouts.notification')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <a href="#" class="btn btn-primary" id="buatizin"><i class="fa fa-plus mr-1"></i> Buat Izin</a>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="#home">Izin Absen</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#home">Izin Keluar</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#home">Izin Pulang</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#home">Sakit</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#home">Cuti</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Kode</th>
                                            <th>Tanggal</th>
                                            <th>NIK</th>
                                            <th>Nama Karyawan</th>
                                            <th>Jml Hari</th>
                                            <th>Ket</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
