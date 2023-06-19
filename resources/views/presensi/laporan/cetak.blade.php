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
                <th rowspan="2">No</th>
                <th rowspan="2">Nik</th>
                <th rowspan="2">Nama karyawan</th>
                <th colspan="{{ $jmlrange }}">Bulan {{ $namabulan[$bulan]}} {{ $tahun }}</th>
            </tr>
            <tr>
                @foreach ($rangetanggal as $d)
                <th>{{ date("d",strtotime($d)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
            $no = 1;
            @endphp
            @foreach ($presensi as $d)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->nik }}</td>
                <td>{{ $d->nama_karyawan }}</td>
                <?php
                for($i=0; $i < count($rangetanggal); $i++){
                    $hari_ke = "hari_".$i+1;
                    $tgl_presensi =  $rangetanggal[$i];
                    if($d->$hari_ke != NULL){
                        $datapresensi = explode("|",$d->$hari_ke);
                        $jam_in = $datapresensi[0];
                        $jam_in_presensi = $jam_in != "NA" ? date("H:i",strtotime($jam_in)) : '';
                        $jam_out = $datapresensi[1];
                        $jam_out_presensi = $jam_out != "NA" ? date("H:i",strtotime($jam_out)) : '';
                        $jam_out_tanggal = $jam_out != "NA" ? $jam_out : '';
                        $nama_jadwal = $datapresensi[2];
                        $jam_masuk = $datapresensi[3];
                        $jam_pulang = $datapresensi[4];
                        $jam_pulang_tanggal = $jam_pulang != "NA" ? $tgl_presensi." ".$jam_pulang : '';
                        $jam_keluar = $datapresensi[9] != "NA" ? $datapresensi[9] : '';
                        $jam_masuk_kk = $datapresensi[10] != "NA" ? $datapresensi[10] : '';
                        $total_jam = $datapresensi[11] != "NA" ? $datapresensi[11] : 0;
                        $status = $datapresensi[5] != "NA" ? $datapresensi[5] : '';
                        $sid = $datapresensi[12] != "NA" ? $datapresensi[12] : '';
                        $kode_izin_terlambat = $datapresensi[7] != "NA" ? $datapresensi[7] : '';
                        $kode_dept = $d->kode_dept;
                        if (!empty($jam_in)) {
                            if ($jam_in_presensi > $jam_masuk) {
                                //Hitung Jam Keterlambatan
                                $j1 = strtotime($jam_masuk);
                                $j2 = strtotime($jam_in_presensi);

                                $diffterlambat = $j2 - $j1;
                                //Jam Terlambat
                                $jamterlambat = floor($diffterlambat / (60 * 60));
                                //Menit Terlambat
                                $menitterlambat = floor(($diffterlambat - ($jamterlambat * (60 * 60)))/60);
                                //Tambah 0 Jika Jam < dari 10
                                $jterlambat = $jamterlambat <= 9 ? "0" . $jamterlambat : $jamterlambat;
                                //Tambah 0 Jika Menit Kurang Dari 10
                                $mterlambat = $menitterlambat <= 9 ? "0" . $menitterlambat : $menitterlambat;

                                //Total Keterlambatan Dalam Jam dan Menit
                                $terlambat = $jterlambat . ":" . $mterlambat;
                                //Keterlambatan Menit Dalam Desimal
                                $desimalterlambat = ROUND(($menitterlambat * 100) / 60);
                                $colorterlambat ="red";
                            } else {
                                $terlambat = "Tepat waktu";
                                $jamterlambat = 0;
                                $desimalterlambat = 0;
                                $colorterlambat = "green";
                            }
                        } else {
                            $terlambat = "";
                            $jamterlambat = 0;
                            $desimalterlambat = 0;
                            $colorterlambat="";
                        }

                        //Perhitungan Jam Keluar
                        if(!empty($jam_keluar)){
                            $jam_keluar_presensi = $tgl_presensi." ".$jam_keluar;
                            if(!empty($jam_masuk_kk)){
                                $jam_masuk_kk_presensi = $tgl_presensi." ".$jam_masuk_kk;
                            }else{
                                $jam_masuk_kk_presensi = $tgl_presensi." ".$jam_pulang;
                            }

                            $jk1 = strtotime($jam_keluar_presensi);
                            $jk2 = strtotime($jam_masuk_kk_presensi);
                            $difkeluarkantor = $jk2 - $jk1;

                            //Total Jam Keluar Kantor
                            $jamkeluarkantor = floor($difkeluarkantor / (60 * 60));
                            //Total Menit Keluar Kantor
                            $menitkeluarkantor = floor(($difkeluarkantor - ($jamkeluarkantor * (60 * 60)))/60);

                            //Tambah 0 di Depan Jika < 10
                            $jkeluarkantor = $jamkeluarkantor <= 9 ? "0" . $jamkeluarkantor : $jamkeluarkantor;
                            $mkeluarkantor = $menitkeluarkantor <= 9 ? "0" . $menitkeluarkantor : $menitkeluarkantor;

                            if(empty($jam_masuk_kk)){
                                if($total_jam == 7){
                                    $totaljamkeluar = ($jkeluarkantor-1).":".$mkeluarkantor;
                                }else{
                                    $totaljamkeluar = $jkeluarkantor.":".$mkeluarkantor;
                                }
                            }else{
                                $totaljamkeluar = $jkeluarkantor.":".$mkeluarkantor;
                            }
                            $desimaljamkeluar = ROUND(($menitkeluarkantor * 100) / 60);
                        }else{
                            $totaljamkeluar = "";
                            $desimaljamkeluar = 0;
                            $jamkeluarkantor = 0;
                        }


                        if(!empty($jam_out) && $jam_out_tanggal < $jam_pulang_tanggal){
                            $pc = "Pulang Cepat";
                        }else{
                            $pc = "";
                        }

                        //Tambah 0 Didepan Jika < 10 pada Jam Terlambat
                        $jamterlambat = $jamterlambat < 0 && !empty($kode_izin_terlambat) ? 0 : $jamterlambat;

                        //Jam terlambat dalam Desimal

                        $jt = !empty($jamterlambat) || !empty($desimalterlambat) ? $jamterlambat . "." . $desimalterlambat : 0;
                        if($jamkeluarkantor > 0){
                            $jk = $jamkeluarkantor.".".$desimaljamkeluar;
                        }else{
                            $jk = 0;
                        }

                        $jt = !empty($jt) ? $jt : 0;

                        // menghitung Denda
                        if (!empty($jam_in) and $kode_dept != 'MKT') {
                            if ($jam_in_presensi > $jam_masuk and empty($kode_izin_terlambat)) {
                                if ($jamterlambat < 1) {
                                    if($menitterlambat >= 5 AND $menitterlambat < 10){
                                        $denda = 5000;
                                    }else if($menitterlambat >= 10 AND $menitterlambat <15){
                                        $denda = 10000;
                                    }else if($menitterlambat >= 15 AND $menitterlambat <= 59){
                                        $denda = 15000;
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
                ?>
                <td>
                    @if ($status == "h")
                    {{-- <span>{{ $rangetanggal[$i] }}</span><br>
                    <span>{{ $jam_out_tanggal }} s.d {{ $jam_pulang_tanggal }}</span> --}}
                    <span style="font-weight: bold">{{ $nama_jadwal }}</span>
                    <br>
                    <span style="color:green">{{ $jam_masuk != "NA" ? date("H:i",strtotime($jam_masuk)) : '' }}</span> -
                    <span style="color:green">{{ $jam_pulang != "NA" ? date("H:i",strtotime($jam_pulang)) : '' }}</span>
                    <br>
                    <span>{!! $jam_in != "NA" ? date("H:i",strtotime($jam_in)) : '<span style="color:red">Belum Scan</span>' !!}</span> -
                    <span>{!! $jam_out != "NA" ? date("H:i",strtotime($jam_out)) : '<span style="color:red">Belum Scan</span>' !!}</span>
                    <br>
                    @if (!empty($terlambat))
                    <span style="color:{{ $colorterlambat }}">{{ $terlambat != "Tepat Waktu" ? "Telat : ".$terlambat : $terlambat }}</span>
                    <br>
                    @endif
                    @if (!empty($denda))
                    <span style="color:{{ $colorterlambat }}">{{ rupiah($denda) }}</span>
                    <br>
                    @endif
                    @if (!empty($pc))
                    <span style="color:red">{{ $pc }}</span>
                    <br>
                    @endif
                    @if (!empty($jam_keluar))
                    <span style="color:#ce7c01">Keluar : {{ $totaljamkeluar }}</span>
                    @endif
                    @elseif($status=="s")
                    <span style="color:rgb(195, 63, 27)">SAKIT
                        @if (!empty($sid))
                        <span style="color:green">- SID</span>

                        @endif
                    </span>
                    @elseif($status=="i")
                    <span style="color:rgb(27, 5, 171);">IZIN</span>
                    @elseif($status=="c")
                    <span style="color:rgb(154, 56, 4);">CUTI</span>
                    @endif

                </td>
                <?php
                    }else{
                ?>
                <td style="background-color: red"></td>
                <?Php
                    }
                }
                ?>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
