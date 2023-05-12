@extends('layouts.midone')
@section('titlepage','Data Karyawan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Monitoring Presensi</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/presensi/monitoring">Monitoring Presensi</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <!-- Data list view starts -->
        <!-- DataTable starts -->
        @include('layouts.notification')
        <div class="col-md-12 col-sm-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="/presensi/monitoring">
                                <div class="row">
                                    @php
                                    $level_search = ["admin","manager hrd","manager accounting","direktur"];
                                    @endphp
                                    @if (Auth::user()->kode_cabang=="PCF" && in_array($level,$level_search))
                                    <div class="col-lg-2 col-sm-12">
                                        <x-inputtext label="Nama Karyawan" field="nama_karyawan_search" icon="feather icon-users" value="{{ Request('nama_karyawan_search') }}" />
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <select name="kode_dept_search" id="kode_dept_search" class="form-control">
                                                <option value="">Departemen</option>
                                                @foreach ($departemen as $d)
                                                <option {{ Request('kode_dept_search')==$d->kode_dept ? 'selected' : '' }} value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <select name="id_perusahaan_search" id="id_perusahaan_search" class="form-control">
                                                <option value="">MP/PCF</option>
                                                <option value="MP" {{ Request('id_perusahaan_search') == "MP" ? "selected" : "" }}>MP</option>
                                                <option value="PCF" {{ Request('id_perusahaan_search') == "PCF" ? "selected" : "" }}>PCF</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <select name="id_kantor_search" id="id_kantor_search" class="form-control">
                                                <option value="">Kantor</option>
                                                @foreach ($kantor as $d)
                                                <option {{ Request('id_kantor_search')==$d->kode_cabang ? 'selected' : '' }} value="{{ $d->kode_cabang }}">{{ $d->kode_cabang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <select name="grup_search" id="grup_search" class="form-control">
                                                <option value="">Grup</option>
                                                @foreach ($group as $d)
                                                <option {{ Request('grup_search')==$d->id ? 'selected' : '' }} value="{{ $d->id }}">{{ $d->nama_group }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @else
                                    <div class="col-lg-8 col-sm-12">
                                        <x-inputtext label="Nama Karyawan" field="nama_karyawan_search" icon="feather icon-users" value="{{ Request('nama_karyawan_search') }}" />
                                    </div>
                                    @endif
                                    <div class="col-lg-2 col-sm-12">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-search mr-1"></i> Cari</button>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-hover-animation">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>NIK</th>
                                            <th>Nama Karyawan</th>
                                            <th>Dept</th>
                                            <th>Jabatan</th>
                                            <th>Kantor</th>
                                            <th>Jam Kerja</th>
                                            <th>Jam Masuk</th>
                                            <th>Jam Pulang</th>
                                            <th>Status</th>
                                            <th>Keterangan</th>
                                            <th>Denda</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($karyawan as $d)
                                        @php
                                        $jam_in = date("H:i:s",strtotime($d->jam_in));
                                        $jam_out = date("H:i:s",strtotime($d->jam_out));
                                        $status = $d->status_presensi ;
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration + $karyawan->firstItem()-1 }}</td>
                                            <td>{{ $d->nik }}</td>
                                            <td>{{ $d->nama_karyawan }}</td>
                                            <td>{{ $d->kode_dept }}</td>
                                            <td>{{ $d->nama_jabatan }}</td>
                                            <td>{{ $d->id_kantor }}</td>
                                            <td>{{ $d->nama_jadwal }} ({{ $d->jam_masuk }} s/d {{ $d->jam_pulang }})</td>
                                            <td>{!! $d->jam_in != null ? $jam_in : '<span class="danger">Belum Absen</span>' !!}</td>
                                            <td>{!! $d->jam_out != null ? $jam_out : '<span class="danger">Belum Absen</span>' !!}</td>
                                            <td>
                                                @if ($status == "h")
                                                <span class="badge bg-success">H</span>
                                                @elseif($status=="i")
                                                <span class="badge bg-info">I</span>
                                                @elseif($status=="c")
                                                <span class="badge bg-warning">C</span>
                                                @elseif($status=="s")
                                                <span class="badge bg-primary">S</span>
                                                @endif
                                            </td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $karyawan->links('vendor.pagination.vuexy') }}
                            </div>

                            <!-- DataTable ends -->
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Input Karyawan -->

@endsection
