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
    <table class="datatable3" style="width: 150%">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th rowspan="2">No</th>
                <th rowspan="2">Nik</th>
                <th rowspan="2">Nama karyawan</th>
                <th rowspan="2">Kantor</th>
                <th colspan="{{ $jmlrange }}">Bulan {{ $namabulan[$bulan*1]}} {{ $tahun }}</th>
                <th rowspan="2">Total Jam</th>
                <th rowspan="2">Terlambat</th>
                <th rowspan="2">Keluar</th>
                <th rowspan="2">Denda</th>
                <th rowspan="2">Premi</th>
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
                <td>{{ $d->id_kantor }}</td>
                <?php
                $totalterlambat = 0;
                $totalkeluar = 0;
                $totaldenda = 0;
                for($i=0; $i < count($rangetanggal); $i++){
                    $hari_ke = "hari_".$i+1;
                    $tgl_presensi =  $rangetanggal[$i];
                    $tgllibur = "'".$tgl_presensi."'";
                    $search_items = array('id_kantor' => $d->id_kantor, 'tanggal_libur' => $tgl_presensi);
                    $ceklibur = cektgllibur($datalibur, $search_items);
                    // /dd($ceklibur);
                    $namahari = hari($tgl_presensi);
                    if($namahari=="Minggu"){
                        $colorcolumn = "#ffaf03";
                        $colortext = "white";
                    }else{
                        if($d->$hari_ke != NULL){
                            if (!empty($ceklibur)) {
                                $colorcolumn = "rgb(4, 163, 65)";
                                $colortext = "white";
                            }else{
                                $colorcolumn = "";
                                $colortext = "";
                            }
                        }else{
                            if (!empty($ceklibur)) {
                                $colorcolumn = "rgb(4, 163, 65)";
                                $colortext = "white";
                            }else{
                                $colorcolumn = "red";
                                $colortext = "white";
                            }
                        }

                    }
                    if($d->$hari_ke != NULL){
                        $datapresensi = explode("|",$d->$hari_ke);
                        $lintashari = $datapresensi[16] != "NA" ? $datapresensi[16] : '';
                        if(!empty($lintashari)){
                            $tgl_pulang = date('Y-m-d', strtotime('+1 day', strtotime($tgl_presensi)));
                        }else{
                            $tgl_pulang = $tgl_presensi;
                        }
                        $jam_in = $datapresensi[0];
                        $jam_in_presensi = $jam_in != "NA" ? date("H:i",strtotime($jam_in)) : '';
                        $jam_out = $datapresensi[1];
                        $jam_out_presensi = $jam_out != "NA" ? date("H:i",strtotime($jam_out)) : '';
                        $jam_out_tanggal = $jam_out != "NA" ? $jam_out : '';
                        $nama_jadwal = $datapresensi[2];
                        $jam_masuk = $datapresensi[3];
                        $jam_masuk_tanggal =$tgl_presensi." ".$datapresensi[3];
                        $jam_pulang = $datapresensi[4];
                        $jam_pulang_presensi =$jam_pulang != "NA" ? date("H:i",strtotime($jam_pulang)) : '';
                        $jam_pulang_tanggal = $jam_pulang != "NA" ? $tgl_pulang." ".$jam_pulang : '';
                        $jam_keluar = $datapresensi[9] != "NA" ? $datapresensi[9] : '';
                        $jam_masuk_kk = $datapresensi[10] != "NA" ? $datapresensi[10] : '';
                        $total_jam = $datapresensi[11] != "NA" ? $datapresensi[11] : 0;
                        $status = $datapresensi[5] != "NA" ? $datapresensi[5] : '';
                        $sid = $datapresensi[12] != "NA" ? $datapresensi[12] : '';
                        $kode_izin_terlambat = $datapresensi[7] != "NA" ? $datapresensi[7] : '';
                        $jam_istirahat = $datapresensi[14];
                        $jam_istirahat_presensi =$jam_istirahat != "NA" ? date("H:i",strtotime($jam_istirahat)) : '';
                        $jam_awal_istirahat = $datapresensi[13] != "NA" ? $tgl_presensi." ".$datapresensi[13] : '';
                        $jam_akhir_istirahat = $datapresensi[14] != "NA" ? $tgl_presensi." ".$datapresensi[14] : '';
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


                        if($jam_out != "NA" && $jam_out_tanggal < $jam_pulang_tanggal){
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

                        //Menghitung total Jam
                        if($d->jam_out > $jam_awal_istirahat && $d->jam_out < $jam_akhir_istirahat){ // Shift 3 Belum Di Set
                            $jout = $jam_awal_istirahat;
                        }else{
                            $jout = $jam_out;
                        }


                        $awal = strtotime($jam_masuk_tanggal);
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

                        if ($denda == 0 and empty($kode_izin_terlambat)) {
                            if($kode_dept != "MKT"){
                                $jt = $jt;
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
                        $totaljam = $total_jam - $jt - $jk;


                        if ($jam_out != "NA") {
                            if ($jam_out < $jam_pulang_tanggal) { //Shift 3 Belum Di Set | Coba
                                if($jam_out > $jam_akhir_istirahat && $jam_istirahat != "NA"){
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

                        if ($jam_in == "NA") {
                            if($status == "i"){
                                $grandtotaljam = 0;
                            }else if($status == "s"){
                                if(!empty($sid)){
                                    $grandtotaljam = 7;
                                }else{
                                    $grandtotaljam = 0;
                                }
                            }else if($status == "c"){
                                $grandtotaljam = 7;
                            }else{
                                $grandtotaljam = 0;
                            }
                        }


                        if ($nama_jadwal == "SHIFT 2" && $grandtotaljam > 5) {
                            $premi = 5000;
                        }else if($nama_jadwal=="SHIFT 3" && $grandtotaljam > 5){
                            $premi = 6000;
                        }else{
                            $premi = 0;
                        }
                        // echo "Total Jam :" .$total_jam."<br>" ;
                        // echo "Jam Terlambat :".$jt."<br>";
                        // echo "___________________________- <br>";

                ?>
                <td style="background-color: {{ $colorcolumn }}; color:{{ $colortext }}">

                    @if ($status == "h")
                    {{-- <span>{{ $jam_out ."|". $jam_akhir_istirahat }}</span><br> --}}
                    {{-- <span>{{ $rangetanggal[$i] }}</span><br>
                    <span>{{ $jam_out_tanggal }} s.d {{ $jam_pulang_tanggal }}</span> --}}
                    {{-- <span>{{ $jam_masuk_tanggal."--".$jout }}</span> --}}
                    <span style="font-weight: bold">{{ $nama_jadwal }}</span>
                    <br>
                    <span style="color:green">{{ $jam_masuk != "NA" ? date("H:i",strtotime($jam_masuk)) : '' }}</span> -
                    <span style="color:green">{{ $jam_pulang != "NA" ? date("H:i",strtotime($jam_pulang)) : '' }}</span>
                    <br>
                    <span>{!! $jam_in != "NA" ? date("H:i",strtotime($jam_in)) : '<span style="color:red">Belum Scan</span>' !!}</span> -
                    <span>{!! $jam_out != "NA" ? date("H:i",strtotime($jam_out)) : '<span style="color:red">Belum Scan</span>' !!}</span>
                    <br>
                    @if ($jam_in != "NA")
                    @if (!empty($terlambat))

                    <span style="color:{{ $colorterlambat }}">{{ $terlambat != "Tepat waktu" ? "Telat : ".$terlambat."(".$jt.")" : $terlambat }}
                        @if (!empty($kode_izin_terlambat))
                        <span style="color:green"> - Sudah Izin</span>
                        @endif
                    </span>
                    <br>
                    @endif
                    @if (!empty($denda))
                    <span style="color:{{ $colorterlambat }}">Denda :{{ rupiah($denda) }}</span>
                    <br>
                    @endif
                    @endif

                    @if (!empty($pc))
                    <span style="color:red">{{ $pc }}</span>
                    <br>
                    @endif
                    @if (!empty($jam_keluar))
                    <span style="color:#ce7c01">Keluar : {{ $totaljamkeluar }} ({{ $jk }})</span>
                    <br>
                    @endif
                    <span style="color:blue">Total Jam : {{ $grandtotaljam }}</span>
                    @elseif($status=="s")
                    <span style="color:rgb(195, 63, 27)">SAKIT
                        @if (!empty($sid))
                        <span style="color:green">- SID</span><br>
                        <span style="color:blue">Total Jam : {{ $grandtotaljam }}</span>
                        @else
                        <br>
                        <span style="color:blue">Total Jam : {{ $grandtotaljam }}</span>
                        @endif
                    </span>
                    @elseif($status=="i")
                    <span style="color:rgb(27, 5, 171);">IZIN</span><br>
                    <span style="color:blue">Total Jam : {{ $grandtotaljam }}</span>
                    @elseif($status=="c")
                    <span style="color:rgb(154, 56, 4);">CUTI</span><br>
                    <span style="color:blue">Total Jam : {{ $grandtotaljam }}</span>
                    @endif
                    @if (!empty($premi))
                    <br>
                    <span style="color: blue">Premi : {{ rupiah($premi) }}</span>
                    @endif
                </td>
                <?php
                    }else{
                    $jt = 0;
                    $jk = 0;
                    $denda = 0;
                    $premi = 0;
                ?>
                <td style="background-color:{{ $colorcolumn }}; color:white">{{ !empty($ceklibur) ? $ceklibur[0]["keterangan"] : "" }}</td>
                <?Php
                    }

                    $totalterlambat += $jt;
                    $totalkeluar += $jk;
                    $totaldenda += $denda;
                }


                ?>
                <td></td>
                <td style="text-align: center; color:red; font-size:16px">{{ !empty($totalterlambat) ? $totalterlambat : '' }}</td>
                <td style="text-align: center; color:rgb(255, 140, 0);font-size:16px">{{ !empty($totalkeluar) ? $totalkeluar : '' }}</td>
                <td style="text-align: right; color:red; font-size:16px">{{ !empty($totaldenda) ? rupiah($totaldenda) : '' }}</td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>