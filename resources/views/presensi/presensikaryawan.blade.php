@extends('layouts.midone')
@section('titlepage','Presensi Karyawan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Presensi Karyawan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/presensi/presensikaryawan">Presensi Karyawan</a>
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
                            <form action="/presensi/presensikaryawan">
                                <div class="row">
                                    <div class="col-6">
                                        <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker value="{{ Request('dari') }}" />
                                    </div>
                                    <div class="col-6">
                                        <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker value="{{ Request('sampai') }}" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <select name="nik" id="nik" class="form-control">
                                                <option value="">Pilih Karyawan</option>
                                                @foreach ($listkaryawan as $d)
                                                <option {{ Request('nik') == $d->nik ? "selected" : "" }} value="{{ $d->nik }}">{{ $d->nama_karyawan }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
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
                                            <th>Tgl</th>
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
                                            <th>#</th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($karyawan as $d)
                                        <?php
                                        //Jam In adalah Jam Ketika Melakukan Presensi
                                        // Jam Masuk adalah Jam Masuk Seharusnya
                                        $jam_in = date("H:i", strtotime($d->jam_in));
                                        $jam_out = date("H:i", strtotime($d->jam_out));
                                        $jam_out_tanggal = $d->jam_out;
                                        $tgl_presensi = $d->tgl_presensi;
                                        $lintashari = $d->lintashari;
                                        if(!empty($lintashari)){
                                            $tgl_pulang = date('Y-m-d', strtotime('+1 day', strtotime($tgl_presensi)));
                                        }else{
                                            $tgl_pulang = $tgl_presensi;
                                        }
                                        $jam_pulang_tanggal = $tgl_pulang. " ".$d->jam_pulang;
                                        $jam_istirahat = date("H:i",strtotime($d->jam_istirahat));
                                        $jam_pulang = date("H:i", strtotime($d->jam_pulang));
                                        $jam_masuk = $d->tgl_presensi . " " . $d->jam_masuk;
                                        $jam_awal_istirahat = $d->tgl_presensi. " ".$d->jam_awal_istirahat;
                                        $jam_akhir_istirahat = $d->tgl_presensi. " ".$d->jam_istirahat;
                                        $status = $d->status_presensi;
                                        if (!empty($d->jam_in)) {
                                            if ($jam_in > $d->jam_masuk) {



                                                $j1 = strtotime($jam_masuk);
                                                $j2 = strtotime($d->jam_in);

                                                $diffterlambat = $j2 - $j1;

                                                $jamterlambat = floor($diffterlambat / (60 * 60));
                                                $menitterlambat = floor(($diffterlambat - ($jamterlambat * (60 * 60)))/60);

                                                $jterlambat = $jamterlambat <= 9 ? "0" . $jamterlambat : $jamterlambat;
                                                $mterlambat = $menitterlambat <= 9 ? "0" . $menitterlambat : $menitterlambat;


                                                $terlambat = $jterlambat . ":" . $mterlambat;
                                                $desimalterlambat = ROUND(($menitterlambat / 60),2);
                                            } else {
                                                $terlambat = "Tepat waktu";
                                                $jamterlambat = 0;
                                                $desimalterlambat = 0;
                                            }
                                        } else {
                                            $terlambat = "";
                                            $jamterlambat = 0;
                                            $desimalterlambat = 0;
                                        }


                                        if(!empty($d->jam_keluar)){
                                            $jamkeluar = $d->tgl_presensi." ".$d->jam_keluar;
                                            if(!empty($d->jam_masuk_kk)){
                                                $jam_masuk_kk = $d->tgl_presensi." ".$d->jam_masuk_kk;
                                            }else{
                                                $jam_masuk_kk = $d->tgl_presensi." ".$d->jam_pulang;
                                            }

                                            $jk1 = strtotime($jamkeluar);
                                            $jk2 = strtotime($jam_masuk_kk);
                                            $difkeluarkantor = $jk2 - $jk1;

                                            $jamkeluarkantor = floor($difkeluarkantor / (60 * 60));
                                            $menitkeluarkantor = floor(($difkeluarkantor - ($jamkeluarkantor * (60 * 60)))/60);

                                            $jkeluarkantor = $jamkeluarkantor <= 9 ? "0" . $jamkeluarkantor : $jamkeluarkantor;
                                            $mkeluarkantor = $menitkeluarkantor <= 9 ? "0" . $menitkeluarkantor : $menitkeluarkantor;

                                            if(empty($d->jam_masuk_kk)){
                                                if($d->total_jam == 7){
                                                    $totaljamkeluar = ($jkeluarkantor-1).":".$mkeluarkantor;
                                                }else{
                                                    $totaljamkeluar = $jkeluarkantor.":".$mkeluarkantor;
                                                }
                                            }else{
                                                $totaljamkeluar = $jkeluarkantor.":".$mkeluarkantor;
                                            }
                                            $desimaljamkeluar = ROUND(($menitkeluarkantor / 60),2);

                                        }else{
                                            $totaljamkeluar = "";
                                            $desimaljamkeluar = 0;
                                            $jamkeluarkantor = 0;
                                        }


                                        if(!empty($d->jam_out) && $jam_out < $jam_pulang){
                                            $pc = "Pulang Cepat";
                                        }else{
                                            $pc = "";
                                        }


                                        $day = date('D', strtotime($d->tgl_presensi));
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

                                        //Jam terlambat dalam Desimal

                                        $jt = $jamterlambat + $desimalterlambat;
                                        if($jamkeluarkantor > 0){
                                            $jk = $jamkeluarkantor+$desimaljamkeluar;
                                        }else{
                                            $jk = 0;
                                        }
                                        $jt = !empty($jt) ? $jt : 0;
                                        // menghitung Denda
                                        if (!empty($d->jam_in) and $d->kode_dept != 'MKT') {
                                            if ($jam_in > $d->jam_masuk and empty($d->kode_izin_terlambat)) {
                                                if ($jamterlambat < 1) {
                                                    if($menitterlambat >= 5 AND $menitterlambat < 10){
                                                        $denda = 5000;
                                                    }else if($menitterlambat >= 10 AND $menitterlambat <15){
                                                        $denda = 10000;
                                                    }else if($menitterlambat >= 15 AND $menitterlambat <= 59){
                                                        $denda = 15000;
                                                    }else{
                                                        $denda = 0;
                                                    }
                                                }else{
                                                    $denda = 0;
                                                }
                                            } else {
                                                $denda = 0;
                                            }
                                        } else {
                                            $denda = 0;
                                        }

                                         //Menghitung total Jam
                                        if($d->jam_out > $jam_awal_istirahat && $d->jam_out < $jam_akhir_istirahat){
                                            $jout = $jam_awal_istirahat;
                                        }else{
                                            $jout = $d->jam_out;
                                        }



                                        //echo $jam_awal_istirahat."|";
                                        $awal = strtotime($jam_masuk);
                                        $akhir = strtotime($jout);
                                        $diff = $akhir - $awal;
                                        if (empty($jout)) {
                                            $jam = 0;
                                            $menit = 0;
                                        } else {
                                            $jam = floor($diff / (60 * 60));
                                            $m = $diff - ($jam * (60 * 60));
                                            $menit = floor($m / 60);
                                        }

                                        // if ($namahari != 'Sabtu') {
                                        //     $totaljam = 7.00 - $jt;
                                        // } else if ($namahari == "Sabtu") {
                                        //     $totaljam = 5.00 - $jt;
                                        // } else {
                                        //     $totaljam = $jam . ":" . $menit;
                                        // }

                                        if ($denda == 0 and empty($d->kode_izin_terlambat)) {
                                            if($d->kode_dept != "MKT"){
                                                if($jamterlambat < 1){
                                                    $jt = 0;
                                                }else{
                                                    $jt = $jt;
                                                }
                                            }else{
                                                if($jamterlambat < 1){
                                                    $jt = 0;
                                                }else{
                                                    $jt = $jt;
                                                }
                                            }
                                        }else{
                                            if($jamterlambat < 1){
                                                $jt = 0;
                                            }else{
                                                $jt = $jt;
                                            }
                                        }
                                        $totaljam = $d->total_jam - $jt - $jk;

                                        if (!empty($d->jam_out)) {
                                            if ($jam_out_tanggal < $jam_pulang_tanggal) {
                                                if($jam_out > $jam_istirahat && !empty($d->jam_istirahat)){
                                                    $desimalmenit = ROUND(($menit * 100) / 60);
                                                    $grandtotaljam = $jam-1 . "." . $desimalmenit;
                                                }else{
                                                    $desimalmenit = ROUND(($menit * 100) / 60);
                                                    $grandtotaljam = $jam . "." . $desimalmenit;
                                                }

                                                $grandtotaljam = $grandtotaljam - $jt - $jk;
                                            } else {
                                                $desimalmenit = 0;
                                                $grandtotaljam = $totaljam;
                                            }
                                        } else {
                                            $desimalmenit = 0;
                                            $grandtotaljam = 0;
                                        }


                                        if (empty($d->jam_in)) {
                                            $grandtotaljam = 0;
                                        }
                                        //echo $jam."|";
                                        //echo $jam.$menit;
                                        //echo $jam_istirahat;
                                        //echo $desimalmenit."|";
                                        ?>
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ date("d/m/y",strtotime($d->tgl_presensi)) }}</td>
                                            <td>{{ $d->nik }}</td>
                                            <td>{{ $d->nama_karyawan }}</td>
                                            <td>{{ $d->kode_dept }}</td>

                                            <td>{{ $d->id_kantor }}</td>
                                            <td>{{ $d->nama_jadwal }} {{ $d->jadwalcabang }} ({{ $d->jam_masuk }} s/d {{ $d->jam_pulang }})</td>
                                            <td>
                                                {!! $d->jam_in != null ? '<a href="#" class="showpresensi" id="'.$d->id.'" status="in">'.$jam_in.'</a>' : '<span class="danger">Belum Absen</span>' !!}
                                                @if (!empty($d->kode_izin_terlambat))
                                                (Izin)
                                                @endif
                                            </td>
                                            <td style="color:{{ $jam_out < $jam_pulang ? 'red' : '' }}">{!! $d->jam_out != null ? '<a href="#" class="showpresensi" id="'.$d->id.'" status="out">'.$jam_out.'</a>' : '<span class="danger">Belum Absen</span>' !!}
                                                @if (!empty($pc))
                                                (PC)
                                                @endif
                                                @if (!empty($d->kode_izin_pulang))
                                                (Izin)
                                                @endif
                                            </td>

                                            <td>
                                                @if ($status == "h")
                                                <span class="badge bg-success">H</span>
                                                @elseif($status=="i")
                                                <span class="badge bg-info">I</span>
                                                @elseif($status=="a")
                                                <span class="badge bg-danger">A</span>
                                                @elseif($status=="c")
                                                <span class="badge bg-warning">C</span>
                                                @elseif($status=="s")
                                                @if (!empty($d->sid))
                                                <span class="badge bg-primary">SID</span>
                                                @else
                                                <span class="badge bg-primary">S</span>
                                                @endif

                                                @endif
                                            </td>
                                            <td style="color:{{ $terlambat != "Tepat waktu" ? "red" : "green" }}">
                                                {{ $terlambat }}
                                            </td>
                                            <td>{{ !empty($denda)  ? rupiah($denda) : '' }}</td>
                                            <td>{{ $totaljamkeluar }}</td>
                                            <td style="color:{{ $grandtotaljam < $d->total_jam ?  'red' : '' }}; text-align:center">{{ $grandtotaljam > 0 ? $grandtotaljam : 0 }}</td>
                                            <td>
                                                @if ($level == "manager hrd" || $level=="admin" || Auth::user()->pic_presensi==1)
                                                <a href="#" class="edit" nik="{{ $d->nik }}" kode_jadwal="{{ $d->kode_jadwal }}" tanggal="{{ $d->tgl_presensi }}"><i class="feather icon-edit info"></i></a>
                                                <a href="#" class="checkmesin" pin="{{ $d->pin }}" tanggal="{{ $d->tgl_presensi }}"><i class="feather icon-monitor success"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

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
<div class="modal fade text-left" id="mdlupdatepresensi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Update Presensi Presensi <span id="tglupdatepresensi"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="loadupdatepresensi">
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdlshowpresensi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Data Presensi <span id="tglupdatepresensi"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="loadpresensi">
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdlcheckmesin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Data Presensi <span id="tglupdatepresensi"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="loadcheckmesin">
            </div>
        </div>
    </div>
</div>
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

<script>
    $(function() {

        $("#nik").selectize();

        $(".checkmesin").click(function(e) {
            e.preventDefault();
            var pin = $(this).attr("pin");
            var tanggal = $(this).attr("tanggal");
            $("#mdlcheckmesin").modal({
                backdrop: 'static'
                , keyboard: false
            });
            $.ajax({
                type: 'POST'
                , url: '/presensi/checkmesin'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , pin: pin
                    , tanggal: tanggal
                }
                , cache: false
                , success: function(respond) {
                    $("#loadcheckmesin").html(respond);
                }
            });
        });



        $(".edit").click(function(e) {
            e.preventDefault();
            var nik = $(this).attr('nik');
            var tgl = $(this).attr('tanggal');
            // var tgl = tanggal == "" ? "{{ date('Y-m-d') }}" : tanggal;
            var kode_jadwal = $(this).attr("kode_jadwal");
            // /alert(kode_jadwal);
            $("#tglupdatepresensi").text(tgl);
            $("#mdlupdatepresensi").modal({
                backdrop: 'static'
                , keyboard: false
            });
            $.ajax({
                type: 'POST'
                , url: '/presensi/updatepresensi'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , nik: nik
                    , tgl: tgl
                    , kode_jadwal: kode_jadwal
                }
                , cache: false
                , success: function(respond) {
                    $("#loadupdatepresensi").html(respond);
                }
            });
        });

        $(".showpresensi").click(function(e) {
            e.preventDefault();
            var id = $(this).attr("id");
            var status = $(this).attr("status");
            $("#mdlshowpresensi").modal({
                backdrop: 'static'
                , keyboard: false
            });

            $.ajax({
                type: 'POST'
                , url: '/presensi/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , id: id
                    , status: status
                }
                , cache: false
                , success: function(respond) {
                    $("#loadpresensi").html(respond);
                }
            });
        });
    });

</script>
@endpush
