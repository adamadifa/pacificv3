@extends('layouts.midone')
@section('titlepage','Data Limit Kredit')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Limit Kredit</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/limitkredit">Data Limit Kredit</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <input type="hidden" id="cektutuplaporan">
        <div class="col-md-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form action="/limitkredit">
                        <div class="row">
                            <div class="col-lg-6">
                                <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker value="{{ Request('dari') }}" />
                            </div>
                            <div class="col-lg-6">
                                <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker value="{{ Request('sampai') }}" />
                            </div>
                        </div>
                        <div class="row">
                            @if (Auth::user()->kode_cabang=="PCF")
                            <div class="col-lg-2 col-sm-12">
                                <div class="form-group  ">
                                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                                        <option value="">Semua Cabang</option>
                                        @foreach ($cabang as $c)
                                        <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{
                                            $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                            <div class="col-lg-3 col-sm-12">
                                <x-inputtext label="Nama Pelanggan" field="nama_pelanggan" icon="feather icon-user" value="{{ Request('nama_pelanggan') }}" />
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <div class="form-group">
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Semua Status Pengajuan</option>
                                        <option {{ (Request('status')=='pending' ? 'selected' :'')}} value="pending">BELUM DISETUJUI {{ Str::upper(Auth::user()->level)  }}</option>
                                        <option {{ (Request('status')=='disetujui' ? 'selected' :'')}} value="disetujui">DISETUJUI {{ Str::upper(Auth::user()->level)  }}</option>
                                        <option {{ (Request('status')=='ditolak' ? 'selected' :'')}} value="ditolak">DITOLAK {{ Str::upper(Auth::user()->level)  }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-2 col-sm-12">
                                <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i> Cari Data </button>
                            </div>
                        </div>
                    </form>
                    @include('layouts.notification')
                    <table class="table table-hover-animation ">
                        <thead class="thead-dark">
                            <tr>

                                <th>No.Pengajuan</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Jumlah</th>
                                <th>Jatuhtempo</th>
                                @if (in_array($level,$penyesuaian_limit))
                                <th>% Peny</th>
                                @endif
                                <th>Skor</th>
                                <th>Ket</th>
                                <th>KP</th>
                                <th>RSM</th>
                                <th>MM</th>
                                <th>DIRUT</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($limitkredit as $d)
                            <tr>

                                <td>{{ $d->no_pengajuan }}</td>
                                <td>{{ date("d-m-Y",strtotime($d->tgl_pengajuan)) }}</td>
                                <td>{{ ucwords(strtolower($d->nama_pelanggan)) }}</td>
                                <td class="text-right">
                                    @if (!empty($d->jumlah_rekomendasi))
                                    <s>{{ rupiah($d->jumlah) }}</s> / {{ rupiah($d->jumlah_rekomendasi) }}
                                    @else
                                    {{ rupiah($d->jumlah) }}
                                    @endif
                                </td>
                                <td>
                                    @if (!empty($d->jatuhtempo_rekomendasi))
                                    <s>{{ $d->jatuhtempo }}</s> / {{ $d->jatuhtempo_rekomendasi }} Hari
                                    @else
                                    {{ $d->jatuhtempo }} Hari
                                    @endif
                                </td>
                                @if (in_array($level,$penyesuaian_limit))
                                <td>
                                    @if ($d->status!=2)
                                    @if (empty($d->jumlah_rekomendasi) || $d->jumlah_rekomendasi===0)
                                    <a href="#" class="penyesuaian_limit" no_pengajuan="{{ $d->no_pengajuan }}"><i class="feather icon-maximize-2 info"></i></a>
                                    @else

                                    @if ($d->jumlah_rekomendasi > $d->jumlah)
                                    @php
                                    $selisih = $d->jumlah_rekomendasi - $d->jumlah;
                                    @endphp
                                    @php
                                    $persentase = ($selisih / $d->jumlah) * 100;
                                    @endphp
                                    @if($d->cek_ajuan==1)
                                    <a href="#" class="#" no_pengajuan="{{ $d->no_pengajuan }}"><i class="feather icon-arrow-up-right success"></i> <span class="success">{{ round($persentase,2) }} %</span></a>
                                    @else
                                    <a href="#" class="penyesuaian_limit" no_pengajuan="{{ $d->no_pengajuan }}"><i class="feather icon-arrow-up-right success"></i> <span class="success">{{ round($persentase,2) }} %</span></a>
                                    @endif
                                    @else
                                    @php
                                    $selisih = $d->jumlah - $d->jumlah_rekomendasi;
                                    $persentase = ($selisih/ $d->jumlah ) *100;
                                    @endphp
                                    @if($d->cek_ajuan==1)
                                    <a href="#" class="#" no_pengajuan="{{ $d->no_pengajuan }}"><i class="feather icon-arrow-down-left danger"></i> <span class="danger">{{ round($persentase,2) }} %</span></a>
                                    @else
                                    <a href="#" class="penyesuaian_limit" no_pengajuan="{{ $d->no_pengajuan }}"><i class="feather icon-arrow-down-left danger"></i> <span class="danger">{{ round($persentase,2) }} %</span></a>
                                    @endif

                                    @endif
                                    @endif
                                    @endif
                                </td>
                                @endif
                                <td>
                                    <?php
                                    $scoreakhir =  $d['skor'];
                                    if ($scoreakhir <= 2) {
                                        $rekomendasi = "TL";
                                    } else if ($scoreakhir > 2 && $scoreakhir <= 4) {
                                        $rekomendasi = "TD";
                                    } else if ($scoreakhir > 4 && $scoreakhir <= 6) {
                                        $rekomendasi = "B";
                                    } else if ($scoreakhir > 6 && $scoreakhir <= 8.5) {
                                        $rekomendasi = "LDP";
                                    } else if ($scoreakhir > 8.5 && $scoreakhir <= 10) {
                                        $rekomendasi = "L";
                                    }
                                    if ($scoreakhir <= 4) {
                                        $bg = "danger";
                                    } else if ($scoreakhir <= 6) {
                                        $bg = "warning";
                                    } else {
                                        $bg = "blue";
                                    }
                                    //echo $scoreakhir;
                                    ?>
                                    <span class="badge bg-<?php echo $bg; ?>">
                                        <?php echo $scoreakhir; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $bg }}">{{ $rekomendasi }}</span>
                                </td>
                                <td>
                                    @if ($d->jumlah > 2000000)
                                    @if (empty($d->kacab))
                                    <i class="fa fa-history warning"></i>
                                    @elseif(
                                    !empty($d->kacab) && !empty($d->rsm) && $d->status==2 ||
                                    !empty($d->kacab) && empty($d->rsm) && $d->status== 0 ||
                                    !empty($d->kacab) && empty($d->rsm) && $d->status== 1 ||
                                    !empty($d->kacab) && !empty($d->rsm) && $d->status== 0 ||
                                    !empty($d->kacab) && !empty($d->rsm) && $d->status== 1
                                    )
                                    <i class="fa fa-check success"></i>
                                    @else
                                    <i class="fa fa-close danger"></i>
                                    @endif
                                    @endif
                                </td>
                                <td>
                                    @if (in_array($d->kode_cabang,$wilayah_barat))
                                    @if ($d->jumlah > 5000000)
                                    @if (empty($d->rsm))
                                    <i class="fa fa-history warning"></i>
                                    @elseif(
                                    !empty($d->rsm) && !empty($d->mm) && $d->status == 2
                                    || !empty($d->rsm) && empty($d->mm) && $d->status == 1
                                    || !empty($d->rsm) && empty($d->mm) && $d->status == 0
                                    || !empty($d->rsm) && !empty($d->mm) && $d->status == 0
                                    || !empty($d->rsm) && !empty($d->mm) && $d->status == 1
                                    )
                                    <i class="fa fa-check success"></i>
                                    @else
                                    <i class="fa fa-close danger"></i>
                                    @endif
                                    @endif
                                    @endif
                                </td>
                                <td>
                                    @if (in_array($d->kode_cabang,$wilayah_timur))
                                    @if ($d->jumlah > 5000000)
                                    @if (empty($d->mm))
                                    <i class="fa fa-history warning"></i>
                                    @elseif(
                                    !empty($d->mm) && !empty($d->dirut) && $d->status == 2
                                    || !empty($d->mm) && empty($d->dirut) && $d->status == 1
                                    || !empty($d->mm) && empty($d->dirut) && $d->status == 0
                                    || !empty($d->mm) && !empty($d->dirut) && $d->status == 0
                                    || !empty($d->mm) && !empty($d->dirut) && $d->status == 1
                                    )
                                    <i class="fa fa-check success"></i>
                                    @else
                                    <i class="fa fa-close danger"></i>
                                    @endif
                                    @endif
                                    @else
                                    @if ($d->jumlah > 10000000)
                                    @if (empty($d->mm))
                                    <i class="fa fa-history warning"></i>
                                    @elseif(
                                    !empty($d->mm) && !empty($d->dirut) && $d->status == 2
                                    || !empty($d->mm) && empty($d->dirut) && $d->status == 1
                                    || !empty($d->mm) && empty($d->dirut) && $d->status == 0
                                    || !empty($d->mm) && !empty($d->dirut) && $d->status == 0
                                    || !empty($d->mm) && !empty($d->dirut) && $d->status == 1
                                    )
                                    <i class="fa fa-check success"></i>
                                    @else
                                    <i class="fa fa-close danger"></i>
                                    @endif
                                    @endif
                                    @endif

                                </td>
                                <td>
                                    @if ($d->jumlah > 15000000)
                                    @if (empty($d->dirut))
                                    <i class="fa fa-history warning"></i>
                                    @elseif(!empty($d->dirut) && $d->status != 2 )
                                    <i class="fa fa-check success"></i>
                                    @else
                                    <i class="fa fa-close danger"></i>
                                    @endif
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <a href="/limitkredit/{{ Crypt::encrypt($d->no_pengajuan) }}/cetak" target="_blank"><i class="feather icon-printer info"></i></a>
                                        @if ($d->cek_ajuan != 1)

                                        {{-- <a class="ml-1" href="#"><i class=" feather icon-edit success"></i></a> --}}
                                        @if(in_array($level,$limitkredit_hapus))
                                        @if (empty($d->kacab))
                                        <form method="POST" name="deleteform" class="deleteform" action="/limitkredit/{{ Crypt::encrypt($d->no_pengajuan) }}/{{ Crypt::encrypt($d->kode_pelanggan) }}/delete">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" class="delete-confirm ml-1">
                                                <i class="feather icon-trash danger"></i>
                                            </a>
                                        </form>
                                        @endif
                                        @endif
                                        @if(in_array($level,$limitkredit_analisa))
                                        @if($d->status==0)
                                        <a class="ml-1 uraiananalisa" no_pengajuan="{{ $d->no_pengajuan }}" href="#"><i class=" feather icon-message-circle primary"></i></a>
                                        @endif
                                        @endif
                                        <!-- Kepala Cabang -->
                                        @if($level == "kepala penjualan" && empty($d->kacab) && $d->status==0
                                        || $level == "kepala cabang" && empty($d->kacab) && $d->status==0

                                        || $level == "kepala penjualan" && !empty($d->kacab) && empty($d->mm) && $d->status==2
                                        || $level == "kepala cabang" && !empty($d->kacab) && empty($d->mm) && $d->status==2

                                        || $level == "kepala penjualan" && !empty($d->kacab) && empty($d->mm) && $d->status==1
                                        || $level == "kepala cabang" && !empty($d->kacab) && empty($d->mm) && $d->status==1

                                        || $level == "kepala penjualan" && !empty($d->kacab) && empty($d->mm) && $d->status==0
                                        || $level == "kepala cabang" && !empty($d->kacab) && empty($d->mm) && $d->status==0)

                                        <a class="ml-1" href="/limitkredit/{{ Crypt::encrypt($d->no_pengajuan) }}/approve"><i class=" fa fa-check success"></i></a>
                                        <a class="ml-1" href="/limitkredit/{{ Crypt::encrypt($d->no_pengajuan) }}/decline"><i class=" fa fa-close danger"></i></a>
                                        @endif

                                        <!-- RSM -->
                                        @if($level=="rsm" && !empty($d->kacab) && empty($d->rsm) && empty($d->mm) && $d->status==0
                                        || $level == "rsm" && !empty($d->kacab) && !empty($d->rsm) && empty($d->mm) && $d->status==2
                                        || $level == "rsm" && !empty($d->kacab) && !empty($d->rsm) && empty($d->mm) && $d->status==0)
                                        <a class="ml-1" href="/limitkredit/{{ Crypt::encrypt($d->no_pengajuan) }}/approve"><i class=" fa fa-check success"></i></a>
                                        <a class="ml-1" href="/limitkredit/{{ Crypt::encrypt($d->no_pengajuan) }}/decline"><i class=" fa fa-close danger"></i></a>
                                        @endif

                                        <!-- General Manager -->

                                        @if ($level=="manager marketing" && !empty($d->rsm) && empty($d->mm) && empty($d->dirut) && $d->status==0
                                        || $level =="manager marketing" && !empty($d->rsm) && !empty($d->mm) && empty($d->dirut) && $d->status==2
                                        || $level =="manager marketing" && !empty($d->rsm) && !empty($d->rsm) && empty($d->dirut) && $d->status==0
                                        || $level =="manager marketing" && !empty($d->rsm) && !empty($d->rsm) && empty($d->dirut) && $d->status!=2)


                                        <a class="ml-1" href="/limitkredit/{{ Crypt::encrypt($d->no_pengajuan) }}/approve"><i class=" fa fa-check success"></i></a>
                                        <a class="ml-1" href="/limitkredit/{{ Crypt::encrypt($d->no_pengajuan) }}/decline"><i class=" fa fa-close danger"></i></a>
                                        @endif

                                        <!-- Direktur -->
                                        @if($level == "direktur" )
                                        <a class="ml-1" href="/limitkredit/{{ Crypt::encrypt($d->no_pengajuan) }}/approve"><i class=" fa fa-check success"></i></a>
                                        <a class="ml-1" href="/limitkredit/{{ Crypt::encrypt($d->no_pengajuan) }}/decline"><i class=" fa fa-close danger"></i></a>
                                        @endif

                                        @endif
                                    </div>

                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $limitkredit->links('vendor.pagination.vuexy') }}

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Input Uraian Analisa -->
<div class="modal fade text-left" id="mdluraiananalisa" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input Uraian Analisa</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaduraiananalisa"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdlpenyesuaianlimit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Penyesuaian Limit Kredit</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadpenyesuaianlimit"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {

        $('.uraiananalisa').click(function(e) {
            var no_pengajuan = $(this).attr("no_pengajuan");
            e.preventDefault();
            $.ajax({
                type: 'POST'
                , url: '/limitkredit/create_uraiananalisa'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_pengajuan: no_pengajuan
                }
                , cache: false
                , success: function(respond) {
                    $("#loaduraiananalisa").html(respond);
                }
            });
            $('#mdluraiananalisa').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        $('.penyesuaian_limit').click(function(e) {
            var no_pengajuan = $(this).attr("no_pengajuan");
            e.preventDefault();
            $.ajax({
                type: 'POST'
                , url: '/limitkredit/penyesuaian_limit'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_pengajuan: no_pengajuan
                }
                , cache: false
                , success: function(respond) {
                    $("#loadpenyesuaianlimit").html(respond);
                }
            });
            $('#mdlpenyesuaianlimit').modal({
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
