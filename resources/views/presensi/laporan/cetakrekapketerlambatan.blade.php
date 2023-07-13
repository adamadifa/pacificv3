<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Penjualan {{ date("d-m-y") }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&display=swap');

        body {
            font-family: 'Poppins'
        }

        .datatable3 {
            border: 2px solid #D6DDE6;
            border-collapse: collapse;
            font-size: 11px;
        }

        .datatable3 td {
            border: 1px solid #000000;
            padding: 6px;
        }

        .datatable3 th {
            border: 2px solid #828282;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }


        /* @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&display=swap');

        body {
            font-family: 'Poppins'
        }

        .datatable3 {
            border: 2px solid #D6DDE6;
            border-collapse: collapse;
            font-size: 11px;
        }

        .datatable3 td {
            border: 1px solid #000000;
            padding: 6px;
        }

        .datatable3 th {
            border: 2px solid #828282;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }

        .table-scroll {
            position: relative;
            max-width: 100%;
            margin: auto;
            overflow: hidden;

        }

        .table-wrap {
            width: 100%;
            overflow: auto;
        }

        .table-scroll table {
            width: 100%;
            margin: auto;
            border-collapse: separate;
            border-spacing: 0;
        }


        .clone {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
        }

        .clone th,
        .clone td {
            visibility: hidden
        }

        .clone td,
        .clone th {
            border-color: transparent
        }

        .clone tbody th {
            visibility: visible;
            color: red;
        }

        .clone .fixed-side {
            border: 1px solid #000;
            background: #eee;
            visibility: visible;
        } */

    </style>
</head>
<body>
    <b style="font-size:14px;">
        LAPORAN PRESENSI
        <br>
        @if ($departemen != null)
        DEPARTEMEN {{ $departemen->nama_dept }}
        @else
        SEMUA DEPARTEMEN
        @endif
        <br>
        @if ($kantor != null)
        KANTOR {{ $kantor->nama_cabang }}
        @else
        SEMUA KANTOR
        @endif
        <br>
        @if ($group != null)
        GRUP {{ $group->nama_group }}
        @else
        SEMUA GRUP
        @endif
    </b>
    <br>
    <table class="datatable3">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>No.</th>
                <th>Tanggal</th>
                <th>Nik</th>
                <th>Nama</th>
                <th>Dept</th>
                <th>Kantor</th>
                <th>Jadwal</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Terlambat</th>
                <th>Denda</th>

            </tr>
        </thead>
        <tbody>
            @php
            $totaldenda = 0;
            @endphp
            @foreach ($presensi as $key => $d)
            @php
            $nik = @$presensi[$key + 1]->nik;
            @endphp
            <?php
                //Jam In adalah Jam Ketika Melakukan Presensi
                // Jam Masuk adalah Jam Masuk Seharusnya
                $jam_in = date("H:i", strtotime($d->jam_in));
                $jam_out = date("H:i", strtotime($d->jam_out));
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
                    if ($jam_out < $jam_pulang) {
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
                $totaldenda += $denda;
                ?>


            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ DateToIndo2($d->tgl_presensi) }}</td>
                <td>{{ $d->nik }}</td>
                <td>{{ $d->nama_karyawan }}</td>
                <td>{{ $d->kode_dept }}</td>
                <td>{{ $d->id_kantor }}</td>
                <td>{{ $d->nama_jadwal }} {{ $d->jadwalcabang }} ({{ $d->jam_masuk }} s/d {{ $d->jam_pulang }})</td>
                <td>
                    {!! $d->jam_in != null ? $jam_in : '<span style="color:red">Belum Absen</span>' !!}
                    @if (!empty($d->kode_izin_terlambat))
                    (Izin)
                    @endif
                </td>
                <td style="color:{{ $jam_out < $jam_pulang ? 'red' : '' }}">{!! $d->jam_out != null ? $jam_out : '<span style="color:red">Belum Absen</span>' !!}
                    @if (!empty($pc))
                    (PC)
                    @endif
                    @if (!empty($d->kode_izin_pulang))
                    (Izin)
                    @endif
                </td>
                <td style="color:{{ $terlambat != "Tepat waktu" ? "red" : "green" }}">
                    {{ $terlambat }}
                </td>
                <td style="text-align: right">{{ !empty($denda)  ? rupiah($denda) : '' }}</td>
            </tr>
            @if ($nik != $d->nik)

            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th colspan="10"></th>
                <th style="text-align: right">{{ rupiah($totaldenda) }}</th>
            </tr>
            @php
            $totaldenda = 0;
            @endphp
            @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>
