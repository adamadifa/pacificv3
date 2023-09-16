@extends('layouts.midone')
@section('titlepage','Penilaian Karyawan')
@section('content')
<style>
    .kategorimenu {
        list-style-type: none;
        margin: 0;
        padding: 0;
        overflow: hidden;
        background-color: #504d91;
        border-radius: 10px;
    }

    .kategorimenu>li {
        float: left;
    }

    .kategorimenu>li a {
        display: block;
        color: white;
        text-align: center;
        padding: 14px 16px;
        text-decoration: none;
    }

    .kategorimenu>li a:hover:not(.active) {
        background-color: #111;
    }

    .active {
        background-color: #04AA6D;
    }

</style>
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Penilaian Karyawan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/penilaiankaryawan">Penilaian Karyawan</a>
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
        @if (count($kategori_approval) > 0)
        <div class="row mb-2">
            <div class="col-12">

                <ul class="kategorimenu">
                    @foreach ($kategori_approval as $d)
                    <li>
                        <a href="/penilaiankaryawan/{{ $d->id }}/{{ $d->id_perusahaan }}/list" class="{{ $kategori_jabatan == $d->id ? 'active' : '' }}"> {{ $d->kategori_jabatan }}
                            <span class="badge bg-danger badge-pill" style="margin-left:2px;">{{ $d->jml }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <a href="#" class="btn btn-primary" id="buatpenilaian"><i class="fa fa-plus mr-1"></i> Buat Penilaian</a>
                    </div>
                    <div class="card-body">
                        <form action="{{ url()->current() }}">
                            <div class="row">
                                <div class="col-lg-6 col-sm-12">
                                    <x-inputtext label="Dari" field="dari_search" value="{{ Request('dari_search') }}" icon="feather icon-calendar" datepicker />
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <x-inputtext label="Sampai" field="sampai_search" value="{{ Request('sampai_search') }}" icon="feather icon-calendar" datepicker />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-sm-12">
                                    <x-inputtext label="Nama Karyawan" field="nama_karyawan" icon="feather icon-user" value="{{ Request('nama_karyawan') }}" />
                                </div>
                                <div class="col-lg-3 col-sm-12">
                                    <select name="filter" id="filter" class="form-control">
                                        <option value="">Filter</option>
                                        <option value="1" {{ Request('filter') ==1 ? 'selected' : '' }}>Yang harus Di Setujui</option>
                                        <option value="2" {{ Request('filter') ==2 ? 'selected' : '' }}>Yang Sudah Di Setujui</option>
                                        <option value="3" {{ Request('filter') ==3 ? 'selected' : '' }}>Waiting</option>
                                    </select>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search mr-2"></i> Search</button>
                                </div>
                            </div>

                        </form>
                        <div class="table-responsive">
                            <table class="table table-hover-animation">
                                <thead class="thead-dark" style="font-size: 14px">
                                    <tr>
                                        <th class="text-center" rowspan="2">No</th>
                                        <th rowspan="2">Kode</th>
                                        <th rowspan="2">Tanggal</th>
                                        <th rowspan="2">Nik</th>
                                        <th rowspan="2">Nama Karyawan</th>
                                        <th rowspan="2">Kantor</th>
                                        <th rowspan="2">Periode</th>
                                        <th rowspan="2">Departemen</th>
                                        <th rowspan="2">Jabatan</th>
                                        <th colspan="{{ count($approve) }}">Approval</th>
                                        <th rowspan="2">Pemutihan</th>
                                        <th rowspan="2">Aksi</th>
                                    </tr>
                                    <tr>
                                        @php
                                        //$inisial = ["manager"=>"M","general manager"=>"GM","manager hrd"=>"HRD","direktur"=>"DIRUT"];

                                        @endphp
                                        @for ($i = 0; $i < count($approve); $i++) <th>{{ $inisial[$approve[$i]] }}</th>
                                            @endfor
                                    </tr>
                                </thead>
                                <tbody style="font-size: 12px">

                                    @foreach ($penilaian as $d)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $d->kode_penilaian }}</td>
                                        <td>{{ DateToIndo2($d->tanggal) }}</td>
                                        <td>{{ $d->nik }}</td>
                                        <td>{{ $d->nama_karyawan }}</td>
                                        <td>
                                            {{ $d->id_kantor }}
                                        </td>
                                        <td>
                                            @php
                                            $periode = explode("/",$d->periode_kontrak);
                                            $dari = $periode[0];
                                            $sampai = $periode[1];
                                            @endphp
                                            {{ date("d-m-Y",strtotime($dari)) }} / {{ date("d-m-Y",strtotime($sampai)) }}
                                        </td>
                                        <td>{{ $d->nama_dept }}</td>
                                        <td>{{ $d->nama_jabatan }}</td>
                                        <?php
                                        for($i=0; $i<count($approve); $i++){
                                        $level = strtolower($inisial[$approve[$i]]);
                                        if($i < count($approve) - 1){
                                            $test = $i +1;
                                            $nextlevel = strtolower($inisial[$approve[$i + 1]]);
                                        }else{
                                            $nextlevel = strtolower($inisial[$approve[$i]]);
                                            $test = 0;
                                        }

                                        //echo $test;
                                        ?>
                                        <td>
                                            <?php
                                            if($i==0 && $d->status==2 && !empty($d->$level) && empty($d->$nextlevel)){
                                                //Jika Index 0 dan Stataus ==2 dan Level Tidak Kosong dan Level Selanjutnya Kosong Maka X
                                                echo "<i class='fa fa-close danger'></i>";
                                            }else if($i==0 && $d->status==2 && !empty($d->$level) && !empty($d->$nextlevel) ){
                                                // Jika Index 0 dan Status == 2 dan LEvel Tidak Kosong dan Level Berikutnya Tidak Kosong Maka V
                                                echo "<i class='fa fa-check success'></i>";
                                            }else if($d->status == 2 && !empty($d->$level) && $level=="dirut"){
                                                //Jika Status == 2 Level Tidak Kosong  dan Level == "DIRUT" maka X
                                                echo "<i class='fa fa-close danger'></i>";
                                            }else if($d->status == 2 && !empty($d->$level) && empty($d->$nextlevel)){
                                                echo "<i class='fa fa-close danger'></i>";
                                            }else if($d->status == 2 && !empty($d->$level) && !empty($d->$nextlevel)){
                                                echo "<i class='fa fa-check success'></i>";
                                            }else if($d->status == NULL && empty($d->$level)){
                                                echo "<i class='fa fa-history warning'></i>";
                                            }else if($d->status == NULL && !empty($d->$level)){
                                                echo "<i class='fa fa-check success'></i>";
                                            }else if($d->status == 1 && !empty($d->$level)){
                                                echo "<i class='fa fa-check success'></i>";
                                            }
                                            ?>
                                        </td>
                                        <?php
                                        }
                                        ?>
                                        <td align="center">
                                            @if ($d->pemutihan==1)
                                            @if (!empty($d->dirut))
                                            @if (!empty($d->no_kb))
                                            <a href="/kesepakatanbersama/{{ Crypt::encrypt($d->no_kb) }}/cetak" target="_blank"><i class="feather icon-printer primary"></i></a>
                                            @else
                                            <a href="#" class="buatkb" nik="{{ $d->nik }}" kode_penilaian="{{ $d->kode_penilaian }}">Buat KB</a>
                                            @endif

                                            @else
                                            <i class="fa fa-check success"></i>
                                            @endif
                                            @endif
                                        </td>
                                        <td>

                                            <div class="btn-group">
                                                <a href="/penilaiankaryawan/{{ Crypt::encrypt($d->kode_penilaian) }}/cetak" target="_blank" class="info mr-1"><i class="feather icon-printer"></i></a>
                                                @if (array_search(strtolower($kat_jab_user),$approve) == 0 || Auth::user()->level=="manager hrd" || Auth::user()->level=="direktur")
                                                @if (empty($d->$field_kategori))
                                                <a href="/penilaiankaryawan/{{ Crypt::encrypt($d->kode_penilaian)}}/edit" class="success"><i class="feather icon-edit"></i></a>
                                                @endif
                                                @endif

                                                @if (array_search(strtolower($kat_jab_user),$approve) == 0)
                                                @if (empty($d->$field_kategori))
                                                <form method="POST" name="deleteform" class="deleteform" action="/penilaiankaryawan/{{ Crypt::encrypt($d->kode_penilaian) }}/delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="#" class="delete-confirm ml-1">
                                                        <i class="feather icon-trash danger"></i>
                                                    </a>
                                                </form>
                                                @endif
                                                @endif

                                                @if (!empty($d->$field_kategori))
                                                <?php
                                                if($cekindex < count($approve) -1) {
                                                    $nextindex= $cekindex + 1;
                                                    $ceklevel =  strtolower($inisial[$approve[$nextindex]]);
                                                }else{
                                                    $nextindex=$cekindex;
                                                    $ceklevel =  strtolower($inisial[$approve[$nextindex]]);
                                                }


                                                if (empty($d->$ceklevel) || $field_kategori=="dirut") {
                                                ?>
                                                @if (empty($d->no_kontrak))
                                                @if(Auth::user()->level != 'spv pdqc' && Auth::user()->level!="spv produksi" && Auth::user()->level!="spv maintenance")
                                                <a href="/penilaiankaryawan/{{ Crypt::encrypt($d->kode_penilaian) }}/{{ Crypt::encrypt($field_kategori) }}/batalkan" class="warning ml-1">Batalkan</a>
                                                @endif
                                                @endif
                                                <?php } ?>
                                                @else

                                                <?php
                                                $lastindex = $cekindex - 1;

                                                if($d->kode_dept=="HRD" && Auth::user()->level=="manager hrd"){
                                                //var_dump($field_kategori);
                                                $field_kategori = "m";
                                                ?>

                                                <a href="/penilaiankaryawan/{{ Crypt::encrypt($d->kode_penilaian) }}/{{ Crypt::encrypt($field_kategori) }}/approve" class="success ml-1"><i class="fa fa-check"></i></a>
                                                <a href="/penilaiankaryawan/{{ Crypt::encrypt($d->kode_penilaian) }}/{{ Crypt::encrypt($field_kategori) }}/decline" class="danger ml-1"><i class="fa fa-close"></i></a>
                                                <?php
                                                }
                                                if(Auth::user()->level != 'spv pdqc' && Auth::user()->level!="spv produksi"  && Auth::user()->level!="spv maintenance"){
                                                if($cekindex == 0){
                                                ?>
                                                <a href="/penilaiankaryawan/{{ Crypt::encrypt($d->kode_penilaian) }}/{{ Crypt::encrypt($field_kategori) }}/approve" class="success ml-1"><i class="fa fa-check"></i></a>
                                                <a href="/penilaiankaryawan/{{ Crypt::encrypt($d->kode_penilaian) }}/{{ Crypt::encrypt($field_kategori) }}/decline" class="danger ml-1"><i class="fa fa-close"></i></a>
                                                <?php
                                                }else{
                                                    $ceklevel = strtolower($inisial[$approve[$lastindex]]);
                                                    if(!empty($d->$ceklevel)){
                                                ?>
                                                <a href="/penilaiankaryawan/{{ Crypt::encrypt($d->kode_penilaian) }}/{{ Crypt::encrypt($field_kategori) }}/approve" class="success ml-1"><i class="fa fa-check"></i></a>
                                                <a href="/penilaiankaryawan/{{ Crypt::encrypt($d->kode_penilaian) }}/{{ Crypt::encrypt($field_kategori) }}/decline" class="danger ml-1"><i class="fa fa-close"></i></a>
                                                <?php
                                                    }else{
                                                        echo "<span class='badge bg-warning ml-1'>Waiting</span>";
                                                    }
                                                }
                                                }
                                                ?>
                                                @endif
                                                @if (Auth::user()->level =="manager hrd" && !empty($d->dirut) && empty($d->pemutihan))
                                                @if (empty($d->no_kontrak))
                                                @if ($d->masa_kontrak_kerja=="Tidak Diperpanjang")
                                                <span class="danger">Tidak Diperpanjang</span>
                                                @else
                                                <a href="#" nik="{{ $d->nik }}" kode_penilaian="{{ $d->kode_penilaian }}" class="danger buatkontrak">Buat Kontrak</a>
                                                @endif


                                                @else
                                                <a href="/kontrak/{{ Crypt::encrypt($d->no_kontrak) }}/cetak" target="_blank" class="success">
                                                    <i class="feather icon-printer"></i>
                                                </a>
                                                @endif
                                                @else
                                                @if (!empty($d->no_kontrak))
                                                <a href="/kontrak/{{ Crypt::encrypt($d->no_kontrak) }}/cetak" target="_blank" class="success">
                                                    <i class="feather icon-printer"></i>
                                                </a>
                                                @endif
                                                @endif

                                            </div>
                                        </td>
                                    </tr>

                                    @endforeach
                                </tbody>
                            </table>
                            {{ $penilaian->links('vendor.pagination.vuexy') }}
                        </div>

                        <!-- DataTable ends -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Data list view end -->
    </div>
</div>
<!-- Detail Salesman -->
<div class="modal fade text-left" id="mdlbuatpenilaian" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Buat Penilaian</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/penilaiankaryawan/create" method="post" id="frmBuatpenilaian">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext label="Tanggal" field="tanggal" icon="feather icon-calendar" datepicker />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <select name="nik" id="nik" class="form-control select2">
                                    <option value="">Pilih Karyawan</option>
                                    @foreach ($karyawan as $d)
                                    <option value="{{ $d->nik }}">{{ $d->nik }} {{ $d->nama_karyawan }} ({{ $d->nama_jabatan }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext label="No. Kontrak" field="no_kontrak" icon="feather icon-credit-card" readonly />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <x-inputtext label="Periode Dari" field="dari" icon="feather icon-calendar" readonly />
                        </div>
                        <div class="col-6">
                            <x-inputtext label="Periode Sampai" field="sampai" icon="feather icon-calendar" readonly />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <button class="btn btn-primary btn-block" type="submit"><i class="feather icon-send mr-1"></i> Buat Penialian</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdlbuatkb" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Buat Kesepakatan Bersama</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/kesepakatanbersama/store" method="post" id="frmKesepakatanBersama">
                    @csrf
                    <input type="hidden" name="kode_penilaian" id="kode_penilaian_kb">
                    <input type="hidden" name="nik" id="nik_kb">
                    <div class="row">

                        <div class="col-12">
                            <x-inputtext label="Tanggal Kesepakatan Bersama" field="tanggal" icon="feather icon-calendar" datepicker />
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-12">
                            <select name="no_kontrak" id="no_kontrak_pemutihan" class="form-control">

                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <button class="btn btn-primary btn-block" type="submit"><i class="feather icon-send mr-1"></i> Buat Kesepakatan Bersama</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Buat Kontrak --}}
<div class="modal fade text-left" id="mdlbuatkontrak" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Buat Kontrak</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="loadbuatkontrak">

            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {


        $('#buatpenilaian').click(function(e) {
            e.preventDefault();
            $('#mdlbuatpenilaian').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        $("#frmKesepakatanBersama").submit(function(e) {
            var tanggal = $("#frmKesepakatanBersama").find("#tanggal").val();
            var tahun = $("#tahun_kb").val();
            if (tanggal == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi!'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmKesepakatanBersama").find("#tanggal").focus();
                });
                return false;
            } else if (tahun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tahun Pemutihan Harus Diisi!'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tahun_kb").focus();
                });
                return false;
            }
        });
        $('.buatkb').click(function(e) {
            var nik = $(this).attr("nik");
            var kode_penilaian = $(this).attr("kode_penilaian");
            $("#nik_kb").val(nik);
            $("#kode_penilaian_kb").val(kode_penilaian);
            e.preventDefault();
            $('#mdlbuatkb').modal({
                backdrop: 'static'
                , keyboard: false
            });

            loadkontrak(nik);
        });


        function loadkontrak(nik) {
            $.ajax({
                type: 'POST'
                , url: '/kontrak/getkontrakpemutihan'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , nik: nik
                }
                , cache: false
                , success: function(respond) {
                    $("#no_kontrak_pemutihan").html(respond);
                }
            });
        }

        $('.buatkontrak').click(function(e) {
            var kode_penilaian = $(this).attr("kode_penilaian");
            e.preventDefault();
            $.ajax({
                type: 'POST'
                , url: '/kontrak/createfrompenilaian'
                , data: {
                    _token: '{{ csrf_token() }}'
                    , kode_penilaian: kode_penilaian
                }
                , cache: false
                , success: function(respond) {
                    $("#loadbuatkontrak").html(respond);
                    $('#mdlbuatkontrak').modal({
                        backdrop: 'static'
                        , keyboard: false
                    });
                }
            });

        });

        $("#frmBuatpenilaian").find("#nik").change(function(e) {
            var nik = $(this).val();
            $.ajax({
                type: 'POST'
                , url: '/kontrak/getkontrakpenilaian'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , nik: nik
                }
                , cache: false
                , success: function(respond) {
                    if (respond == 0) {
                        swal({
                            title: 'Oops'
                            , text: 'Data Kontrak Tidak Ditemukan, Silahkan Hubungi Tim IT, Atau HRD Dept.!'
                            , icon: 'warning'
                            , showConfirmButton: false
                        }).then(function() {
                            $("#frmBuatpenilaian").find("#no_kontrak").val("");
                            $("#frmBuatpenilaian").find("#dari").val("");
                            $("#frmBuatpenilaian").find("#sampai").val("");
                        });
                    } else {
                        var data = respond.split("|");
                        $("#frmBuatpenilaian").find("#dari").val(data[1]);
                        $("#frmBuatpenilaian").find("#sampai").val(data[2]);
                        $("#frmBuatpenilaian").find("#no_kontrak").val(data[0]);
                    }

                }
            });

        });

        $("#frmBuatpenilaian").submit(function() {
            var dari = $("#dari").val();
            var sampai = $("#sampai").val();
            var tanggal = $("#tanggal").val();
            if (tanggal == "") {
                alert('Tanggal Harus Diisi');
                return false;
            } else if (dari == "" || sampai == "") {
                alert('Periode Kontrak Harus Diisi');
                return false;
            } else {
                return true;
            }
        });
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
    });

</script>
@endpush
