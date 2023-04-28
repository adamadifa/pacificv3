@extends('layouts.midone')
@section('titlepage','Pengajuan Izin')
@section('content')
<style>
    .form-group {
        margin-bottom: 5px !important;
    }

    .form-label-group {
        margin-bottom: 5px !important;
    }

    .col-4,
    .col-5,
    .col-6,
    .col-3 {
        padding-right: 1px !important;
    }

</style>

<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Pengajuan Izin</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/pengajuanizin">Pengajuan Izin</a>
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
                                <div class="row mb-1">
                                    <div class="col-12">
                                        <form action="/pengajuanizin">
                                            <div class="row">
                                                <div class="col-lg-6 col-sm-12">
                                                    <x-inputtext label="Dari" field="dari" value="{{ Request('dari') }}" icon="feather icon-calendar" datepicker />
                                                </div>
                                                <div class="col-lg-6 col-sm-12">
                                                    <x-inputtext label="Sampai" field="sampai" value="{{ Request('sampai') }}" icon="feather icon-calendar" datepicker />
                                                </div>
                                            </div>
                                            @php
                                            $level_search = ["admin","manager hrd","manager accounting","direktur"];
                                            @endphp
                                            @if (Auth::user()->kode_cabang=="PCF" && in_array($level,$level_search))
                                            <div class="row">
                                                <div class="col-lg-3 col-sm-12">
                                                    <div class="form-group  ">
                                                        <select name="kode_cabang" id="" class="form-control">
                                                            @if (Auth::user()->kode_cabang=="PCF")
                                                            <option value="">Semua Cabang</option>
                                                            @else
                                                            <option value="">Pilih Cabang</option>
                                                            @endif
                                                            @foreach ($cabang as $c)
                                                            <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-12">
                                                    <div class="form-group">
                                                        <select name="kode_dept" id="kode_dept" class="form-control">
                                                            <option value="">Departemen</option>
                                                            @foreach ($departemen as $d)
                                                            <option {{ Request('kode_dept') == $d->kode_dept ? 'selected' : ''  }} value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-12">
                                                    <div class="form-group">
                                                        <x-inputtext label="Nama Karyawan" value="{{ Request('nama_karyawan') }}" field="nama_karyawan" icon="feather icon-user" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-sm-12">
                                                    <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                            @else
                                            <div class="row">
                                                <div class="col-lg-8 col-sm-12">
                                                    <div class="form-group">
                                                        <x-inputtext label="Nama Karyawan" value="{{ Request('nama_karyawan') }}" field="nama_karyawan" icon="feather icon-user" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-sm-12">
                                                    <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                            @endif
                                        </form>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Kode</th>
                                                    <th>Tanggal</th>
                                                    <th>NIK</th>
                                                    <th>Nama Karyawan</th>
                                                    <th>Jabatan</th>
                                                    <th>Dept</th>
                                                    <th>Jml Hari</th>
                                                    <th>Ket</th>
                                                    <th>Head Dept</th>
                                                    <th>HRD</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pengajuan_izin as $d)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $d->kode_izin }}</td>
                                                    <td>{{ date('d-m-Y',strtotime($d->dari)) }} s/d {{ date('d-m-Y',strtotime($d->sampai)) }}</td>
                                                    <td>{{ $d->nik }}</td>
                                                    <td>{{ $d->nama_karyawan }}</td>
                                                    <td>{{ $d->nama_jabatan }}</td>
                                                    <td>{{ $d->kode_dept }}</td>
                                                    <td>{{ $d->jmlhari }} Hari</td>
                                                    <td>{{ $d->keterangan }}</td>
                                                    <td class="text-center">
                                                        @if (empty($d->head_dept))
                                                        <i class="fa fa-history text-warning"></i>
                                                        @elseif($d->head_dept == 1)
                                                        <i class="fa fa-check text-success"></i>
                                                        @elseif($d->head_dept == 2)
                                                        <i class="fa fa-close text-danger"></i>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if (empty($d->hrd))
                                                        <i class="fa fa-history text-warning"></i>
                                                        @elseif($d->hrd == 1)
                                                        <i class="fa fa-check text-success"></i>
                                                        @elseif($d->hrd == 2)
                                                        <i class="fa fa-close text-danger"></i>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="">
                                                            <i class="feather icon-external-link text-primary"></i>
                                                        </a>
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
</div>
@endsection
