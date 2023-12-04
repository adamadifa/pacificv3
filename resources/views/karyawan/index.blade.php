@extends('layouts.midone')
@section('titlepage', 'Data Karyawan')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Data Karyawan</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/karyawan">Data Karyawan</a>
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
                @if (in_array($level, $karyawan_pinjaman))
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info" role="alert">
                                <h4 class="alert-heading">Info Pinjaman & Kasbon</h4>
                                <p class="mb-0">
                                <ul>
                                    <li><i class="feather icon-external-link primary mr-1"></i> <span
                                            class="primary">Pinjaman</span></li>
                                    <li><i class="feather icon-external-link warning mr-1"></i> <span
                                            class="warning">Kasbon</span></li>
                                </ul>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <div class="card-header">
                                @if (in_array($level, $karyawan_tambah))
                                    <a href="#" id="tambahkaryawan" class="btn btn-primary"><i
                                            class="fa fa-plus mr-1"></i> Tambah Data</a>
                                @endif
                                <a href="#" id="cekhabiskontrak" class="btn btn-danger"><i
                                        class="feather icon-user-x mr-1"></i>Karyawan Habis Kontrak</a>
                            </div>


                            <div class="card-body">
                                <form action="/karyawan">

                                    <div class="row">
                                        @php
                                            $level_search = ['admin', 'manager hrd', 'manager accounting', 'direktur'];
                                        @endphp
                                        @if (Auth::user()->kode_cabang == 'PCF' && in_array($level, $level_search))
                                            <div class="col-lg-2 col-sm-12">
                                                <x-inputtext label="Nama Karyawan" field="nama_karyawan_search"
                                                    icon="feather icon-users"
                                                    value="{{ Request('nama_karyawan_search') }}" />
                                            </div>
                                            <div class="col-lg-2 col-sm-12">
                                                <div class="form-group">
                                                    <select name="kode_dept_search" id="kode_dept_search"
                                                        class="form-control">
                                                        <option value="">Departemen</option>
                                                        @foreach ($departemen as $d)
                                                            <option
                                                                {{ Request('kode_dept_search') == $d->kode_dept ? 'selected' : '' }}
                                                                value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-2 col-sm-12">
                                                <div class="form-group">
                                                    <select name="id_perusahaan_search" id="id_perusahaan_search"
                                                        class="form-control">
                                                        <option value="">MP/PCF</option>
                                                        <option value="MP"
                                                            {{ Request('id_perusahaan_search') == 'MP' ? 'selected' : '' }}>
                                                            MP</option>
                                                        <option value="PCF"
                                                            {{ Request('id_perusahaan_search') == 'PCF' ? 'selected' : '' }}>
                                                            PCF</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-sm-12">
                                                <div class="form-group">
                                                    <select name="id_kantor_search" id="id_kantor_search"
                                                        class="form-control">
                                                        <option value="">Kantor</option>
                                                        @foreach ($kantor as $d)
                                                            <option
                                                                {{ Request('id_kantor_search') == $d->kode_cabang ? 'selected' : '' }}
                                                                value="{{ $d->kode_cabang }}">{{ $d->kode_cabang }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="col-lg-2 col-sm-12">
                                                <div class="form-group">
                                                    <select name="grup_search" id="grup_search" class="form-control">
                                                        <option value="">Grup</option>
                                                        @foreach ($group as $d)
                                                            <option {{ Request('grup_search') == $d->id ? 'selected' : '' }}
                                                                value="{{ $d->id }}">{{ $d->nama_group }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-sm-12">
                                                <div class="form-group">
                                                    <select name="status_aktif_karyawan" id="status_aktif_karyawan"
                                                        class="form-control">
                                                        <option value="">Status</option>
                                                        <option value="1"
                                                            {{ Request('status_aktif_karyawan') == '1' ? 'selected' : '' }}>
                                                            Aktif</option>
                                                        <option value="0"
                                                            {{ Request('status_aktif_karyawan') === '0' ? 'selected' : '' }}>
                                                            Non Aktif</option>
                                                    </select>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-lg-8 col-sm-12">
                                                <x-inputtext label="Nama Karyawan" field="nama_karyawan_search"
                                                    icon="feather icon-users"
                                                    value="{{ Request('nama_karyawan_search') }}" />
                                            </div>
                                        @endif

                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary w-100"><i
                                                        class="fa fa-search mr-1"></i> Cari</button>
                                            </div>
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
                                                <th>JK</th>
                                                <th>Tanggal Masuk</th>
                                                <th>Departemen</th>
                                                <th>Jabatan</th>
                                                <th>MP/PCF</th>
                                                <th>Kantor</th>
                                                <th>Klasifikasi</th>
                                                <th>Status</th>
                                                <th>Loc</th>
                                                <th>Pin</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($karyawan as $d)
                                                <tr style="background-color:{{ $d->status_aktif == 0 ? '#ff695e' : '' }}">
                                                    <td class="text-center">
                                                        {{ $loop->iteration + $karyawan->firstItem() - 1 }}</td>
                                                    <td>{{ $d->nik }}</td>
                                                    <td>{{ $d->nama_karyawan }}</td>
                                                    <td>{{ $d->jenis_kelamin == 1 ? 'L' : 'P' }}</td>
                                                    <td>{{ date('d-m-Y', strtotime($d->tgl_masuk)) }}</td>
                                                    <td>{{ $d->nama_dept }}</td>
                                                    <td>{{ $d->nama_jabatan }}</td>
                                                    <td>{{ $d->id_perusahaan }}</td>
                                                    <td>{{ $d->id_kantor }}</td>
                                                    <td>{{ $d->klasifikasi }}</td>
                                                    <td>
                                                        @if ($d->status_karyawan == 'T')
                                                            <span class="badge bg-green">T</span>
                                                        @elseif($d->status_karyawan == 'K')
                                                            <span class="badge bg-warning">K</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($level == 'manager hrd')
                                                            @if ($d->lock_location == 0)
                                                                <a href="/karyawan/{{ Crypt::encrypt($d->nik) }}/unlocklocation"
                                                                    class="ml-1">
                                                                    <i class="feather icon-lock danger"></i>
                                                                </a>
                                                            @else
                                                                <a href="/karyawan/{{ Crypt::encrypt($d->nik) }}/locklocation"
                                                                    class="ml-1">
                                                                    <i class="feather icon-unlock success"></i>
                                                                </a>
                                                            @endif
                                                        @else
                                                            @if ($d->kode_dept == 'MKT')
                                                                @if ($d->lock_location == 0)
                                                                    <a href="/karyawan/{{ Crypt::encrypt($d->nik) }}/unlocklocation"
                                                                        class="ml-1">
                                                                        <i class="feather icon-lock danger"></i>
                                                                    </a>
                                                                @else
                                                                    <a href="/karyawan/{{ Crypt::encrypt($d->nik) }}/locklocation"
                                                                        class="ml-1">
                                                                        <i class="feather icon-unlock success"></i>
                                                                    </a>
                                                                @endif
                                                            @else
                                                                @if ($d->lock_location == 0)
                                                                    <i class="feather icon-lock danger"></i>
                                                                @else
                                                                    <i class="feather icon-unlock success"></i>
                                                                @endif
                                                            @endif
                                                        @endif

                                                    </td>
                                                    <td>{{ $d->pin }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group" aria-label="Basic example">
                                                            {{-- <a href="#" class="info setjadwal" nik="{{ $d->nik }}" id_kantor="{{ $d->id_kantor }}"><i class="feather icon-watch"></i></a> --}}
                                                            @if (in_array($level, $karyawan_edit))
                                                                <a class="ml-1 edit" nik="{{ Crypt::encrypt($d->nik) }}"
                                                                    href="#"><i
                                                                        class="feather icon-edit success"></i></a>
                                                            @endif
                                                            <a href="/karyawan/{{ Crypt::encrypt($d->nik) }}/show"
                                                                class="ml-1"><i
                                                                    class="feather icon-file-text info"></i></a>
                                                            @if (in_array($level, $karyawan_hapus))
                                                                <form method="POST" class="deleteform"
                                                                    action="/supplier/{{ Crypt::encrypt($d->nik) }}/delete">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <a href="#" class="delete-confirm ml-1">
                                                                        <i class="feather icon-trash danger"></i>
                                                                    </a>
                                                                </form>
                                                            @endif
                                                            @if (in_array($level, $karyawan_pinjaman))


                                                                @if ($level == 'manager audit')
                                                                    <a href="#" nik="{{ Crypt::encrypt($d->nik) }}"
                                                                        class="ajukanpinjaman"><i
                                                                            class="feather icon-external-link primary ml-1"></i></a>

                                                                    <a href="#" nik="{{ Crypt::encrypt($d->nik) }}"
                                                                        class="ajukankasbon"><i
                                                                            class="feather icon-external-link warning ml-1"></i></a>
                                                                @endif

                                                                @if ($level == 'admin')
                                                                    <a href="#" nik="{{ Crypt::encrypt($d->nik) }}"
                                                                        class="ajukanpinjaman"><i
                                                                            class="feather icon-external-link primary ml-1"></i></a>

                                                                    <a href="#" nik="{{ Crypt::encrypt($d->nik) }}"
                                                                        class="ajukankasbon"><i
                                                                            class="feather icon-external-link warning ml-1"></i></a>
                                                                @endif



                                                                @if ($level == 'admin pdqc')
                                                                    @php
                                                                        $listkaryawan = ['08.12.100', '11.10.090', '13.02.198', '91.01.016', '03.04.045', '08.05.042', '12.09.182', '05.01.055', '13.03.202'];
                                                                    @endphp

                                                                    @if (in_array($d->nik, $listkaryawan))
                                                                        <a href="#"
                                                                            nik="{{ Crypt::encrypt($d->nik) }}"
                                                                            class="ajukanpinjaman"><i
                                                                                class="feather icon-external-link primary ml-1"></i></a>

                                                                        <a href="#"
                                                                            nik="{{ Crypt::encrypt($d->nik) }}"
                                                                            class="ajukankasbon"><i
                                                                                class="feather icon-external-link warning ml-1"></i></a>
                                                                    @endif
                                                                @endif


                                                                @if ($level == 'spv pdqc')
                                                                    @php
                                                                        $listkaryawan = ['13.03.200', '14.08.220', '13.07.021', '15.05.174', '10.08.128', '13.09.206', '13.09.209', '19.09.303', '21.06.304', '16.01.069', '18.03.305'];
                                                                    @endphp

                                                                    @if (in_array($d->nik, $listkaryawan))
                                                                        <a href="#"
                                                                            nik="{{ Crypt::encrypt($d->nik) }}"
                                                                            class="ajukanpinjaman"><i
                                                                                class="feather icon-external-link primary ml-1"></i></a>

                                                                        <a href="#"
                                                                            nik="{{ Crypt::encrypt($d->nik) }}"
                                                                            class="ajukankasbon"><i
                                                                                class="feather icon-external-link warning ml-1"></i></a>
                                                                    @endif
                                                                @endif
                                                                @if ($level == 'kepala admin')
                                                                    @if ($d->nama_jabatan != 'KEPALA ADMIN')
                                                                        <a href="#"
                                                                            nik="{{ Crypt::encrypt($d->nik) }}"
                                                                            class="ajukanpinjaman"><i
                                                                                class="feather icon-external-link primary ml-1"></i></a>

                                                                        <a href="#"
                                                                            nik="{{ Crypt::encrypt($d->nik) }}"
                                                                            class="ajukankasbon"><i
                                                                                class="feather icon-external-link warning ml-1"></i></a>
                                                                    @endif
                                                                @endif

                                                                @if ($level == 'kepala penjualan')
                                                                    @if ($d->nama_jabatan != 'KEPALA PENJUALAN')
                                                                        <a href="#"
                                                                            nik="{{ Crypt::encrypt($d->nik) }}"
                                                                            class="ajukanpinjaman"><i
                                                                                class="feather icon-external-link primary ml-1"></i></a>

                                                                        <a href="#"
                                                                            nik="{{ Crypt::encrypt($d->nik) }}"
                                                                            class="ajukankasbon"><i
                                                                                class="feather icon-external-link warning ml-1"></i></a>
                                                                    @endif
                                                                @endif

                                                                @if ($level == 'rsm')
                                                                    @if ($d->nama_jabatan == 'KEPALA PENJUALAN')
                                                                        <a href="#"
                                                                            nik="{{ Crypt::encrypt($d->nik) }}"
                                                                            class="ajukanpinjaman"><i
                                                                                class="feather icon-external-link primary ml-1"></i></a>

                                                                        <a href="#"
                                                                            nik="{{ Crypt::encrypt($d->nik) }}"
                                                                            class="ajukankasbon"><i
                                                                                class="feather icon-external-link warning ml-1"></i></a>
                                                                    @endif
                                                                @endif



                                                                @if (
                                                                    $level == 'manager pembelian' ||
                                                                        $level == 'manager produksi' ||
                                                                        $level == 'spv produksi' ||
                                                                        $level == 'manager ga' ||
                                                                        $level == 'spv maintenance')
                                                                    @if ($d->nama_jabatan != 'MANAGER')
                                                                        @if ($d->status_karyawan != 'O')
                                                                            <a href="#"
                                                                                nik="{{ Crypt::encrypt($d->nik) }}"
                                                                                class="ajukanpinjaman"><i
                                                                                    class="feather icon-external-link primary ml-1"></i></a>
                                                                        @endif


                                                                        <a href="#"
                                                                            nik="{{ Crypt::encrypt($d->nik) }}"
                                                                            class="ajukankasbon"><i
                                                                                class="feather icon-external-link warning ml-1"></i></a>
                                                                    @endif
                                                                @endif

                                                                @php
                                                                    $level_kepalagudang = ['ASST. MANAGER', 'MANAGER'];
                                                                @endphp
                                                                @if ($level == 'kepala gudang')
                                                                    @if (!in_array($d->nama_jabatan, $level_kepalagudang))
                                                                        <a href="#"
                                                                            nik="{{ Crypt::encrypt($d->nik) }}"
                                                                            class="ajukanpinjaman"><i
                                                                                class="feather icon-external-link primary ml-1"></i></a>

                                                                        <a href="#"
                                                                            nik="{{ Crypt::encrypt($d->nik) }}"
                                                                            class="ajukankasbon"><i
                                                                                class="feather icon-external-link warning ml-1"></i></a>
                                                                    @endif
                                                                @endif
                                                                @php
                                                                    $level_emf = ['ASST. MANAGER', 'MANAGER'];
                                                                @endphp
                                                                @if ($level == 'emf')
                                                                    @if (in_array($d->nama_jabatan, $level_emf) || $d->kode_dept == 'PDQ')
                                                                        <a href="#"
                                                                            nik="{{ Crypt::encrypt($d->nik) }}"
                                                                            class="ajukanpinjaman"><i
                                                                                class="feather icon-external-link primary ml-1"></i></a>

                                                                        <a href="#"
                                                                            nik="{{ Crypt::encrypt($d->nik) }}"
                                                                            class="ajukankasbon"><i
                                                                                class="feather icon-external-link warning ml-1"></i></a>
                                                                    @endif
                                                                @endif


                                                                @if ($level == 'manager marketing')
                                                                    @if ($d->nama_jabatan == 'REGIONAL SALES MANAGER')
                                                                        <a href="#"
                                                                            nik="{{ Crypt::encrypt($d->nik) }}"
                                                                            class="ajukanpinjaman"><i
                                                                                class="feather icon-external-link primary ml-1"></i></a>

                                                                        <a href="#"
                                                                            nik="{{ Crypt::encrypt($d->nik) }}"
                                                                            class="ajukankasbon"><i
                                                                                class="feather icon-external-link warning ml-1"></i></a>
                                                                    @endif
                                                                @endif


                                                                {{-- @if (Auth::user()->id == '57')
                                                    @if ($d->kode_dept == 'AKT' || $d->kode_dept == 'KEU')
                                                    @if ($d->id_kantor == 'PST' || $d->nama_jabatan == 'KEPALA ADMIN')
                                                    @if ($d->nama_jabatan != 'MANAGER' and $d->nama_jabatan != 'GENERAL MANAGER')
                                                    <a href="#" nik="{{ Crypt::encrypt($d->nik) }}" class="ajukanpinjaman"><i class="feather icon-external-link primary ml-1"></i></a>

                                                    <a href="#" nik="{{ Crypt::encrypt($d->nik) }}" class="ajukankasbon"><i class="feather icon-external-link warning ml-1"></i></a>
                                                    @endif

                                                    @endif

                                                    @endif
                                                    @endif --}}

                                                                @if (Auth::user()->id == 57 || Auth::user()->id == 20)
                                                                    <a href="#" nik="{{ Crypt::encrypt($d->nik) }}"
                                                                        class="ajukanpinjaman"><i
                                                                            class="feather icon-external-link primary ml-1"></i></a>

                                                                    <a href="#" nik="{{ Crypt::encrypt($d->nik) }}"
                                                                        class="ajukankasbon"><i
                                                                            class="feather icon-external-link warning ml-1"></i></a>
                                                                @endif

                                                                {{-- @if (Auth::user()->id == '20')
                                                    @if ($d->kode_dept == 'KEU' && $d->nama_jabatan == 'MANAGER' && $d->nama_jabatan == 'KEPALA')
                                                    <a href="#" nik="{{ Crypt::encrypt($d->nik) }}" class="ajukanpinjaman"><i class="feather icon-external-link primary ml-1"></i></a>

                                                    <a href="#" nik="{{ Crypt::encrypt($d->nik) }}" class="ajukankasbon"><i class="feather icon-external-link warning ml-1"></i></a>
                                                    @endif
                                                    @endif --}}

                                                            @endif


                                                            @if ($level == 'direktur')
                                                                @if ($d->nama_jabatan == 'GENERAL MANAGER')
                                                                    <a href="#" nik="{{ Crypt::encrypt($d->nik) }}"
                                                                        class="ajukanpinjaman"><i
                                                                            class="feather icon-external-link primary ml-1"></i></a>

                                                                    <a href="#" nik="{{ Crypt::encrypt($d->nik) }}"
                                                                        class="ajukankasbon"><i
                                                                            class="feather icon-external-link warning ml-1"></i></a>
                                                                @endif
                                                            @endif
                                                            @if (Auth::user()->id == '90')
                                                                @if ($d->kode_dept == 'HRD')
                                                                    <a href="#" nik="{{ Crypt::encrypt($d->nik) }}"
                                                                        class="ajukanpinjaman"><i
                                                                            class="feather icon-external-link primary ml-1"></i></a>

                                                                    <a href="#" nik="{{ Crypt::encrypt($d->nik) }}"
                                                                        class="ajukankasbon"><i
                                                                            class="feather icon-external-link warning ml-1"></i></a>
                                                                @endif
                                                            @endif


                                                            @if (Auth::user()->id == 57 || Auth::user()->id == 1)
                                                                <a href="#" nik="{{ Crypt::encrypt($d->nik) }}"
                                                                    class="ajukanpinjamannonpjp"><i
                                                                        class="feather icon-external-link success ml-1"></i></a>
                                                            @endif
                                                        </div>
                                                    </td>
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
    <div class="modal fade text-left" id="mdlinputkaryawan" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Tambah Karyawan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadinputkaryawan"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="mdlsetjadwal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Konfigurasi Jadwal</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadsetjadwal"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="mdleditkaryawan" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Edit Karyawan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadeditkaryawan"></div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade text-left" id="mdlajukanpinjaman" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Ajukan Pinjaman</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadajukanpinjaman"></div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade text-left" id="mdlajukankasbon" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Ajukan Kasbon</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadajukankasbon"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="mdlcekhabiskontrak" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Data Karyawan Habis Kontrak</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadhabiskontrak"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="mdlajukanpinjamannonpjp" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Ajukan Pinjaman</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loadajukanpinjamannonpjp"></div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('myscript')
    <script>
        $(function() {

            // $('.setjadwal').click(function(e) {
            //     e.preventDefault();
            //     var nik = $(this).attr(nik);
            //     $('#mdlsetjadwal').modal({
            //         backdrop: 'static'
            //         , keyboard: false
            //     });
            //     $("#loadsetjadwal").load('/konfigurasijadwal/' + nik + '/setjadwal');
            // });
            $(".setjadwal").click(function(e) {
                e.preventDefault();
                var nik = $(this).attr("nik");

                $('#mdlsetjadwal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadsetjadwal").load('/konfigurasijadwal/' + nik + '/setjadwal');
            });

            $('#tambahkaryawan').click(function(e) {
                e.preventDefault();
                $('#mdlinputkaryawan').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadinputkaryawan").load('/karyawan/create');
            });


            $('#cekhabiskontrak').click(function(e) {
                e.preventDefault();
                $('#mdlcekhabiskontrak').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadhabiskontrak").load('/karyawan/habiskontrak');
            });

            $('.edit').click(function(e) {
                var nik = $(this).attr("nik");
                e.preventDefault();
                $('#mdleditkaryawan').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadeditkaryawan").load('/karyawan/' + nik + '/edit');
            });


            $('.ajukanpinjaman').click(function(e) {
                var nik = $(this).attr("nik");
                e.preventDefault();
                $('#mdlajukanpinjaman').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadajukanpinjaman").load('/pinjaman/' + nik + '/create');
            });


            $('.ajukanpinjamannonpjp').click(function(e) {
                var nik = $(this).attr("nik");
                e.preventDefault();
                $('#mdlajukanpinjamannonpjp').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadajukanpinjamannonpjp").load('/pinjamannonpjp/' + nik + '/create');
            });

            $('.ajukankasbon').click(function(e) {
                var nik = $(this).attr("nik");
                e.preventDefault();
                $('#mdlajukankasbon').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $("#loadajukankasbon").load('/kasbon/' + nik + '/create');
            });
        });
    </script>
@endpush
