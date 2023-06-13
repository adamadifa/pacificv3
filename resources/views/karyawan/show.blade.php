@extends('layouts.midone')
@section('titlepage', 'Detail Karyawan')
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
                                $src = 'https://presensi.pacific-tasikmalaya.com/storage/uploads/karyawan/'.$karyawan->foto;
                                //$path = Storage::url('karyawan/'.$karyawan->foto);
                                @endphp
                                {{-- <img src="{{ url($path) }}" class="card-img" style="height: 350px !important"> --}}
                                @if (@getimagesize($src))
                                <img src="https://presensi.pacific-tasikmalaya.com/storage/uploads/karyawan/{{ $karyawan->foto }}" style="height: 350px" alt="" class="card-img">
                                @else
                                @if($karyawan->jenis_kelamin == " 1") <img src="{{ asset('app-assets/images/male.jpg') }}" class="card-img" style="width: 120px; height:150px; object-fit:cover; border-radius:10px; position:absolute; right:70px; top:80px;">
                                @else
                                <img src="{{ asset('app-assets/images/female.jpg') }}" class="card-img" style="width: 120px; height:150px; object-fit:cover; border-radius:10px; position:absolute; right:70px; top:80px;">
                                @endif
                                @endif
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
                            @include('layouts.notification')
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
                                            }else{
                                            $status_kawin = "";
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
                                    <div class="row mb-2">
                                        <div class="col-12">
                                            <a href="#" class="btn btn-primary" id="tambahhistorikontrak"><i class="feather icon-plus mr-1 mb-1"></i>Tambah Histori Kontrak</a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
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
                                                                <div class="btn-group">
                                                                    @if (!empty($d->id_jabatan))
                                                                    <a class="ml-1" href="/kontrak/{{ Crypt::encrypt($d->no_kontrak) }}/cetak" target="_blank"><i class="feather icon-printer primary"></i></a>
                                                                    @endif
                                                                    @if ($loop->last)
                                                                    <a class="ml-1 edit" no_kontrak="{{ $d->no_kontrak }}" href="#"><i class="feather icon-edit success"></i></a>
                                                                    <form method="POST" class="deleteform" action="/kontrak/{{Crypt::encrypt($d->no_kontrak)}}/deletehistorikontrak">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <a href="#" class="delete-confirm ml-1">
                                                                            <i class="feather icon-trash danger"></i>
                                                                        </a>
                                                                    </form>
                                                                    @endif
                                                                </div>

                                                            </td>

                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
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
<div class="modal fade text-left" id="mdleditkontrak" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit Kontrak</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="loadeditkontrak">

            </div>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="mdltambahhistorikontrak" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Tambah Histori Kontrak</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="/kontrak/{{ Crypt::encrypt($karyawan->nik) }}/storehistorikontrak">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12">
                                    <table class="table">
                                        <tr>
                                            <td>NIK</td>
                                            <td>{{ $karyawan->nik }}</td>
                                        </tr>
                                        <tr>
                                            <td>Nama Karyawan</td>
                                            <td>{{ $karyawan->nama_karyawan }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <x-inputtext field="kontrak_dari" label="Dari" icon="feather icon-calendar" datepicker />
                                </div>
                                <div class="col-6">
                                    <x-inputtext field="kontrak_sampai" label="Sampai" icon="feather icon-calendar" datepicker />
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <button class="btn btn-primary btn-block" type="submit"><i class="feather icon-send mr-1"></i>Tambah Histori Kontrak</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')

<script>
    $(function() {
        $('.delete-confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
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
                        form.submit();
                    }
                });
        });
        $("#tambahhistorikontrak").click(function(e) {
            e.preventDefault();
            $("#mdltambahhistorikontrak").modal("show");
        });
        $(".edit").click(function(e) {
            e.preventDefault();
            var no_kontrak = $(this).attr("no_kontrak");
            $.ajax({
                type: 'POST'
                , url: '/kontrak/editlastkontrak'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_kontrak: no_kontrak
                }
                , cache: false
                , success: function(respond) {
                    $('#mdleditkontrak').modal({
                        backdrop: 'static'
                        , keyboard: false
                    });
                    $("#loadeditkontrak").html(respond);
                }
            });
        });
    });

</script>

@endpush
