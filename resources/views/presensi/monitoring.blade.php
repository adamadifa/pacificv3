@extends('layouts.midone')
@section('titlepage','Monitoring Presesensi')
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
            <div class="row mb-2">
                <div class=" col-12 d-flex">
                    @php
                    $day=date('D', strtotime(date('Y-m-d')));
                    $dayList = array(
                    'Sun' => 'Minggu',
                    'Mon' => 'Senin',
                    'Tue' => 'Selasa',
                    'Wed' => 'Rabu',
                    'Thu' => 'Kamis',
                    'Fri' => 'Jumat',
                    'Sat' => 'Sabtu'
                    );
                    $namahari = $dayList[$day];
                    @endphp
                    <h1 style="font-size:40px; font-family:arial; margin-right:10px;">{{ $namahari }}, {{ DateToIndo2(date('Y-m-d')) }}</h1>
                    <h1 style="font-size: 40px; font-family: arial;" id="jam"></h1>
                </div>
            </div>
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
                                    <div class="col-3">
                                        <x-inputtext label="Tanggal" field="tanggal" icon="feather icon-calendar" datepicker value="{{ Request('tanggal') }}" />
                                    </div>
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

                                    <div class="col-lg-1 col-sm-12">
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

                                </div>
                                <div class="row mb-2">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search mr-1"></i> Get Data</button>
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
                                            <th>Kantor</th>
                                            <th>Jam Kerja</th>
                                            <th>Jam Masuk</th>
                                            <th>Jam Pulang</th>
                                            <th>Status</th>
                                            <th>Terlambat</th>
                                            <th>Denda</th>
                                            <th>Keluar</th>
                                            <th>Total Jam</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($karyawan as $d)
                                        <?php
                                        $jam_in = date("H:i",strtotime($d->jam_in));
                                        $jam_out = date("H:i",strtotime($d->jam_out));
                                        $status = $d->status_presensi ;
                                        //Menghitung total Jam
                                        $awal = strtotime($d->jam_in);
                                        $akhir = strtotime($d->jam_out);
                                        $diff = $akhir - $awal;

                                        if(empty($d->jam_out)){
                                            $jam = 0;
                                            $menit = 0;
                                        }else{
                                            $jam = floor($diff / (60 * 60));
                                            $m = $diff - ( $jam * (60 * 60) );
                                            $menit = floor( $m / 60 );
                                        }

                                        if(!empty($d->jam_in)){
                                            if($jam_in > $d->jam_masuk){
                                                $jam_masuk = $d->tgl_presensi." ".$d->jam_masuk;
                                                $j1 = strtotime($jam_masuk);
                                                $j2 = strtotime($d->jam_in);
                                                $diffterlambat = $j2 - $j1;
                                                $jamterlambat = floor($diffterlambat / (60 * 60));
                                                $menitterlambat = $diffterlambat - ( $jamterlambat * (60 * 60) );
                                                $menitterlambatfix = floor( $menitterlambat / 60 );
                                                $jterlambat = $jamterlambat <= 9 ? "0" .$jamterlambat : $jamterlambat ; $mterlambat=$menitterlambatfix <=9 ? "0" .$menitterlambatfix : $menitterlambatfix;
                                                $terlambat=$jterlambat.":".$mterlambat;
                                                $desimalterlambat = ($menitterlambatfix * 100) / 60;
                                            }else{
                                                $terlambat="Tepat waktu";
                                                $jamterlambat=0;
                                                $desimalterlambat = 0;
                                            }
                                        }else{
                                            $terlambat="";
                                            $jamterlambat=0;
                                            $desimalterlambat = 0;
                                        }

                                            $day=date('D', strtotime($d->tgl_presensi));
                                            $dayList = array(
                                            'Sun' => 'Minggu',
                                            'Mon' => 'Senin',
                                            'Tue' => 'Selasa',
                                            'Wed' => 'Rabu',
                                            'Thu' => 'Kamis',
                                            'Fri' => 'Jumat',
                                            'Sat' => 'Sabtu'
                                            );

                                            $namahari = $dayList[$day];
                                            $jamterlambat = $jamterlambat < 0 && !empty($d->kode_izin_terlambat) ? 0 : $jamterlambat;


                                            $jt = $jamterlambat.".".$desimalterlambat;
                                            if($namahari != 'Sabtu'){
                                            $totaljam = 7.00 - $jt ;
                                            }else if( $namahari == "Sabtu"){
                                            $totaljam = 5.00 - $jt;
                                            }else{
                                            $totaljam = $jam.":".$menit;
                                            }

                                            if(!empty($d->jam_out)){
                                                if($jam_out < $d->jam_pulang){
                                                    $desimalmenit = ($menit * 100) / 60;
                                                    $grandtotaljam = $jam.".".$menit;
                                                }else{
                                                    $grandtotaljam = $totaljam;
                                                }
                                            }else{
                                                $grandtotaljam = 0;
                                            }

                                            if(!empty($d->jam_in) AND $d->kode_dept != 'MKT'){
                                                if($jam_in > $d->jam_masuk AND empty($d->kode_izin_terlambat)){
                                                    if ($jamterlambat < 1) {
                                                        if($menitterlambatfix >= 5 AND $menitterlambatfix < 10){
                                                            $denda = 5000;
                                                        }else if($menitterlambatfix >= 10 AND $menitterlambatfix <15){
                                                            $denda = 10000;
                                                        }else if($menitterlambatfix >= 15 AND $menitterlambatfix <= 59){
                                                            $denda = 15000;
                                                        }
                                                    }else{
                                                        $denda = 0;
                                                    }

                                                }else{
                                                    $denda = 0;
                                                }
                                            }else{
                                                $denda = 0;
                                            }


                                        ?>
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration + $karyawan->firstItem()-1 }}</td>
                                            <td>{{ $d->nik }}</td>
                                            <td>{{ $d->nama_karyawan }}</td>
                                            <td>{{ $d->kode_dept }}</td>

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
                                            <td>
                                                {{ $terlambat }}
                                            </td>
                                            <td>{{ !empty($denda)  ? rupiah($denda) : '' }}</td>
                                            <td></td>
                                            <td class="text-center">{{ desimal($grandtotaljam) }}</td>
                                            <td>
                                                {{ $d->Jenis_izin }}
                                                @if ($d->status_izin =="i")
                                                @if ($d->jenis_izin == "PL")
                                                <span class="badge bg-danger">Izin Pulang</span>
                                                @elseif($d->jenis_izin=="TL")
                                                <span class="badge bg-info">Izin Terlambat</span>
                                                @elseif($d->jenis_izin=="KL")
                                                <?php
                                                if(!empty($d->jam_masuk_kk)){
                                                    $jk1 = strtotime($d->jam_keluar);
                                                    $jk2 = strtotime($d->jam_masuk_kk);
                                                    $difjk = $jk2 - $jk1;
                                                    $jamkk = floor($difjk / (60 * 60));
                                                    $menitkk = $difjk - ( $jamkk * (60 * 60) );
                                                    $menitkkfix = floor( $menitkk / 60 );
                                                    $jkk = $jamkk <= 9 ? "0" .$jamkk : $jamkk ;
                                                    $mkk=$menitkkfix <=9 ? "0" .$menitkkfix : $menitkkfix;
                                                    $lamakk=$jkk.":".$mkk;
                                                }else{
                                                    $lamakk= "";
                                                }

                                                ?>
                                                <span class="badge bg-info" data-toggle="popover" data-content="Jam Keluar : {{ $d->jam_keluar }} Jam Masuk : {{ $d->jam_masuk_kk }}" data-trigger="hover" data-original-title="Informasi Keluar Kantor">Izin Keluar {{ $lamakk }}</span>
                                                @endif
                                                @endif

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

@endsection
@push('myscript')
<script type="text/javascript">
    window.onload = function() {
        jam();
    }

    function jam() {
        var e = document.getElementById('jam')
            , d = new Date()
            , h, m, s;
        h = d.getHours();
        m = set(d.getMinutes());
        s = set(d.getSeconds());

        e.innerHTML = h + ':' + m + ':' + s;

        setTimeout('jam()', 1000);
    }

    function set(e) {
        e = e < 10 ? '0' + e : e;
        return e;
    }

</script>
@endpush
