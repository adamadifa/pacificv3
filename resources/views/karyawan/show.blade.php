@extends('layouts.midone')
@section('titlepage', 'Detail Faktur')
@section('content')
<style>
    @media only screen and (max-width: 800px) {
        table {
            font-size: 12px;
        }
    }

</style>
@push('mystyle')
@livewireStyles
@endpush
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Detail Karyawan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/karyawan">Karyawan</a></li>
                            <li class="breadcrumb-item"><a href="#">Detail Karyawan</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="row">
            <div class="col-lg-2 col-sm-12">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-content">
                                @if($karyawan->foto == null)
                                @if($karyawan->jenis_kelamin == "1")
                                <img src="{{ asset('app-assets/images/male.jpg') }}" class="card-img" style="height: 350px !important">
                                @else
                                <img src="{{ asset('app-assets/images/female.jpg') }}" class="card-img" style="height: 350px !important">
                                @endif
                                @else
                                @php
                                $path = Storage::url('karyawan/'.$karyawan->foto);
                                @endphp
                                <img src="{{ url($path) }}" class="card-img" style="height: 350px !important">
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-10 col-sm-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" aria-controls="home" role="tab" aria-selected="true">Data Karyawan</a>
                                </li>
                                {{-- <li class="nav-item">
                                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" aria-controls="profile" role="tab" aria-selected="false">Penilaian</a>
                                </li> --}}
                                <li class="nav-item">
                                    <a class="nav-link" id="kontrak-tab" data-toggle="tab" href="#kontrak" aria-controls="kontrak" role="tab" aria-selected="false">Kontrak</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">Mutasi/Promosi/Demosi</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="about-tab" data-toggle="tab" href="#about" aria-controls="about" role="tab" aria-selected="false">Histori Gaji</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="home" aria-labelledby="home-tab" role="tabpanel">
                                    <table class="table">
                                        <tr>
                                            <th style="width: 20%">NIK</th>
                                            <td>{{ $karyawan->nik }}</td>
                                        </tr>
                                        <tr>
                                            <th>No. KTP</th>
                                            <td>{{ $karyawan->no_ktp }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Karyawan</th>
                                            <td>{{ $karyawan->nama_karyawan }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tempat / Tanggal Lahir</th>
                                            <td>{{ $karyawan->tempat_lahir }} / {{ !empty($karyawan->tgl_lahir) ? DateToIndo2($karyawan->tgl_lahir) : '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jenis Kelamin</th>
                                            <td>{{ $karyawan->jenis_kelamin ==1 ? 'Laki - Laki' : 'Perempuan' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Alamat</th>
                                            <td>{{ $karyawan->alamat }}</td>
                                        </tr>
                                        <tr>
                                            <th>No. HP</th>
                                            <td>{{ $karyawan->no_hp }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pendidikan Terakhir</th>
                                            <td>{{ $karyawan->pendidikan_terakhir }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status Kawin</th>
                                            @php
                                            if($karyawan->status_kawin == 1){
                                            $status_kawin = "Belum Menikah";
                                            }else if($karyawan->status_kawin == 2){
                                            $status_kawin = "Menikah";
                                            }else if($karyawan->status_kawin == 3){
                                            $status_kawin = "Cerai Hidup";
                                            }else if($karyawan->status_kawin == 4){
                                            $status_kawin = "Duda";
                                            }else if($karyawan->status_kawin == 4){
                                            $status_kawin = "Janda";
                                            }
                                            @endphp
                                            <td>{{ $status_kawin }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jabatan</th>
                                            <td>{{ $karyawan->nama_jabatan }}</td>
                                        </tr>
                                        <tr>
                                            <th>Departemen</th>
                                            <td>{{ $karyawan->nama_dept }}</td>
                                        </tr>
                                        <tr>
                                            <th>Perusahaan</th>
                                            <td>{{ $karyawan->id_perusahaan == "MP" ? 'Makmur Permata' : 'Pacific' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kantor Pusat / Cabang</th>
                                            <td>{{ $karyawan->nama_cabang == "PCF PUSAT" ? "PUSAT" : $karyawan->nama_cabang }}</td>
                                        </tr>
                                        <tr>
                                            <th>Grup</th>
                                            <td>{{ $karyawan->nama_group }}</td>
                                        </tr>
                                        <tr>
                                            <th>Klasifikasi</th>
                                            <td>{{ $karyawan->klasifikasi }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="tab-pane" id="kontrak" aria-labelledby="kontrak-tab" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-hover-animation">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>No.</th>
                                                    <th>No. Kontrak</th>
                                                    <th>Tanggal</th>
                                                    <th>Jabatan</th>
                                                    <th>Kantor</th>
                                                    <th>Perusahaan</th>
                                                    <th>Periode</th>
                                                    <th>Ket</th>
                                                    <th>Status</th>
                                                    <th></th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($kontrak as $d)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $d->no_kontrak }}</td>
                                                    <td>{{ DateToIndo2($d->dari) }}</td>
                                                    <td>{{ $d->nama_jabatan }}</td>
                                                    <td>{{ $d->id_kantor }}</td>
                                                    <td>{{ $d->id_perusahaan }}</td>
                                                    <td>{{ date("d-m-Y",strtotime($d->dari)) }} s/d {{ date("d-m-Y",strtotime($d->sampai)) }}</td>
                                                    <td>
                                                        @php
                                                        $start = date_create($d->dari);
                                                        $end = date_create($d->sampai);
                                                        @endphp
                                                        {{ diffInMonths($start, $end). " bulan"; }}
                                                    </td>
                                                    <td>
                                                        @if ($d->status_kontrak==1)
                                                        <i class="fa fa-circle success"></i>
                                                        @else
                                                        <i class="fa fa-circle danger"></i>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (!empty($d->id_jabatan))
                                                        <a class="ml-1" href="/kontrak/{{ Crypt::encrypt($d->no_kontrak) }}/cetak" target="_blank"><i class="feather icon-printer primary"></i></a>
                                                        @endif
                                                    </td>

                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                                <div class="tab-pane" id="dropdown31" role="tabpanel" aria-labelledby="dropdown31-tab" aria-expanded="false">

                                </div>
                                <div class="tab-pane" id="dropdown32" role="tabpanel" aria-labelledby="dropdown32-tab" aria-expanded="false">

                                </div>
                                <div class="tab-pane" id="about" aria-labelledby="about-tab" role="tabpanel">
                                    <p>Carrot cake dragée chocolate. Lemon drops ice cream wafer gummies dragée. Chocolate bar liquorice
                                        cheesecake cookie chupa chups marshmallow oat cake biscuit. Dessert toffee fruitcake ice cream
                                        powder
                                        tootsie roll cake.</p>
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

@livewireScripts

@endpush
