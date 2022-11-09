@extends('layouts.midone')
@section('titlepage','Penilaian Karyawan')
@section('content')

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
        <div class="row">
            <div class="col-md-2 col-sm-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <h4 class="card-title">Kategori Jabatan PST</h4>
                            <p class="card-text">Data Penilaian Karyawan Berdasarkan Kategori Jabatan Kantor Pusat</p>
                        </div>
                        <ul class="list-group list-group-flush">
                            @foreach ($kategori_approval as $d)
                            <a href="/penilaiankaryawan/{{ $d->id }}/{{ $d->kantor }}/list" style="color:#2c2c2c">
                                <li class="list-group-item {{ $kategori_jabatan == $d->id && $kantor == $d->kantor ? 'active' : '' }}">
                                    <span class="badge badge-pill bg-danger float-right">{{ $d->jml }}</span>
                                    {{ $d->kategori_jabatan }}
                                </li>
                            </a>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-10 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <a href="#" class="btn btn-primary" id="buatpenilaian"><i class="fa fa-plus mr-1"></i> Buat Penilaian</a>
                    </div>
                    <div class="card-body">
                        <form action="/penilaiankaryawan">
                            <div class="row">
                                <div class="col-lg-4 col-sm-12">
                                    <x-inputtext label="Nama Karyawan" field="nama_karyawan" icon="feather icon-user" value="{{ Request('nama') }}" />
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
                                        <th rowspan="2">Periode</th>
                                        <th rowspan="2">Departemen</th>
                                        <th rowspan="2">Jabatan</th>
                                        <th colspan="{{ count($approve) }}">Approval</th>
                                        <th rowspan="2">Aksi</th>
                                    </tr>
                                    <tr>
                                        @php
                                        $inisial = ["manager"=>"M","general manager"=>"GM","manager hrd"=>"HRD","direktur"=>"DIRUT"];

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
                                            echo "<i class='fa fa-close danger'></i>";
                                            }else if($i==0 && $d->status==2 && !empty($d->$level) && !empty($d->$nextlevel) ){
                                            echo "<i class='fa fa-check success'></i>";
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
                                        <td>
                                            <div class="btn-group">
                                                <a href="/penilaiankaryawan/{{ Crypt::encrypt($d->kode_penilaian) }}/cetak" class="info"><i class="feather icon-printer"></i></a>
                                                @if (array_search(strtolower($kat_jab_user),$approve) == 0)
                                                @if (empty($d->$field_kategori))
                                                <a href="/penilaiankaryawan/{{ Crypt::encrypt($d->kode_penilaian)}}/edit" class="success mr-1"><i class="feather icon-edit"></i></a>

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
                                                if (empty($d->$ceklevel) || $ceklevel=="dirut") {
                                                ?>
                                                <a href="/penilaiankaryawan/{{ Crypt::encrypt($d->kode_penilaian) }}/{{ Crypt::encrypt($field_kategori) }}/batalkan" class="warning ml-1">Batalkan</a>
                                                <?php } ?>

                                                @else
                                                <?php

                                                $lastindex = $cekindex - 1;

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
                                                        echo "<span class='badge bg-warning'>Waiting..</span>";
                                                    }
                                                }
                                                ?>
                                                @endif

                                            </div>
                                        </td>
                                    </tr>

                                    @endforeach
                                </tbody>
                            </table>
                            {{-- {{ $salesman->links('vendor.pagination.vuexy') }} --}}
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
                <form action="/penilaiankaryawan/create" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext label="Tanggal" field="tanggal" icon="feather icon-calendar" datepicker />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <x-inputtext label="Periode Dari" field="dari" icon="feather icon-calendar" datepicker />
                        </div>
                        <div class="col-6">
                            <x-inputtext label="Periode Sampai" field="sampai" icon="feather icon-calendar" datepicker />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <select name="nik" id="nik" class="form-control select2">
                                    <option value="">Pilih Karyawan</option>
                                    @foreach ($karyawan as $d)
                                    <option value="{{ $d->nik }}">{{ $d->nik }} {{ $d->nama_karyawan }}</option>
                                    @endforeach
                                </select>
                            </div>
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
