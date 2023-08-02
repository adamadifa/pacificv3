<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Penjualan {{ date("d-m-y") }}</title>
    <style>
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
        } */


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

        .freeze-table {
            height: 800px;
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
    <div class="freeze-table">
        <table class="datatable3" style="width: 250%">
            <thead bgcolor="#024a75" style="color:white; font-size:12;">
                <tr bgcolor="#024a75" style="color:white; font-size:12;">
                    <th rowspan="2">No</th>
                    <th rowspan="2">Nik</th>
                    <th rowspan="2">Nama karyawan</th>
                    <th rowspan="2">Grup</th>
                    <th colspan="9">DATA KARYAWAN</th>
                    <th rowspan="2">GAJI POKOK</th>
                    <th colspan="6">TUNJANGAN</th>
                    <th colspan="4">INSENTIF UMUM</th>
                    <th colspan="3">INSENTIF MANAGER</th>
                    <th rowspan="2">UPAH</th>
                    <th rowspan="2">JUMLAH<br>INSENTIF</th>
                    <th rowspan="2">Σ JAM KERJA</th>
                    <th rowspan="2">UPAH / JAM<br> (173)</th>
                    <th colspan="2">OVERTIME 1</th>
                    <th colspan="2">OVERTIME 2</th>
                    <th colspan="2">LEMBUR HARI LIBUR</th>
                    <th rowspan="2">TOTAL<br>OVERTIME</th>
                    <th colspan="2">PREMI SHIFT 2</th>
                    <th colspan="2">PREMI SHIFT 3</th>

                </tr>
                <tr>

                    <th>TANGGAL MASUK</th>
                    <th>MASA KERJA</th>
                    <th>DEPARTEMEN</th>
                    <th>JABATAN</th>
                    <th>KANTOR <br>CABANG</th>
                    <th>PERUSAHAAN</th>
                    <th>KLASIFIKASI</th>
                    <th>JENIS <br>KELAMIN</th>
                    <th>STATUS</th>
                    <th>JABATAN</th>
                    <th>MASA KERJA</th>
                    <th>TANGGUNG<br> JAWAB</th>
                    <th>MAKAN</th>
                    <th>ISTRI</th>
                    <th>SKILL <br>KHUSUS</th>
                    <th>MASA KERJA</th>
                    <th>LEMBUR</th>
                    <th>PENEMPATAN</th>
                    <th>KPI</th>
                    <th>RUANG<br> LINGKUP</th>
                    <th>PENEMPATAN</th>
                    <th>KINERJA</th>
                    <th>JAM</th>
                    <th>JUMLAH</th>
                    <th>JAM</th>
                    <th>JUMLAH</th>
                    <th>JAM</th>
                    <th>JUMLAH</th>
                    <th>HARI</th>
                    <th>JUMLAH</th>
                    <th>HARI</th>
                    <th>JUMLAH</th>
                </tr>
            </thead>
            <tbody>
                @php
                $no = 1;
                $totaljam1bulan = 173;
                @endphp
                @foreach ($presensi as $d)
                <?php
                    $totalterlambat = 0;
                    $totalkeluar = 0;
                    $totaldenda = 0;
                    $totalpremi = 0;
                    $totaldirumahkan = 0;
                    $totaltidakhadir = 0;
                    $totalpulangcepat = 0;
                    $totalizinabsen = 0;
                    //$izinsakit = 0;
                    $totalizinsakit = 0;
                    $jmlharipremi1 = 0;
                    $jmlharipremi2 = 0;
                    $jmlpremi1 = 0;
                    $jmlpremi2 = 0;
                    for($i=0; $i < count($rangetanggal); $i++){
                        $hari_ke = "hari_".$i+1;
                    $tgl_presensi =  $rangetanggal[$i];
                    $start_kerja = date_create($d->tgl_masuk);
                    $end_kerja = date_create($tgl_presensi);
                    $cekmasakerja =  diffInMonths($start_kerja, $end_kerja);
                    $tgllibur = "'".$tgl_presensi."'";
                    $search_items = array('nik'=>$d->nik,'id_kantor' => $d->id_kantor, 'tanggal_libur' => $tgl_presensi);

                    $search_items_minggumasuk = array('nik'=>$d->nik,'id_kantor' => $d->id_kantor, 'tanggal_diganti' => $tgl_presensi);
                    $search_items_all = array('nik'=>'ALL','id_kantor' => $d->id_kantor, 'tanggal_libur' => $tgl_presensi);
                    $ceklibur = cektgllibur($datalibur, $search_items);
                    // if(empty($ceklibur)){
                    //     $ceklibur = cektgllibur($datalibur,$search_items_all);
                    // }

                    $cekliburpenggantiminggu = cektgllibur($dataliburpenggantiminggu,$search_items);
                    // if(empty($cekliburpenggantiminggu)){
                    //     $cekliburpenggantiminggu = cektgllibur($dataliburpenggantiminggu,$search_items_all);
                    // }
                    $cekminggumasuk = cektgllibur($dataminggumasuk,$search_items_minggumasuk);
                    $cekwfh = cektgllibur($datawfh,$search_items);
                    $cekwfhfull = cektgllibur($datawfhfull,$search_items);
                    // if(empty($cekwfh)){
                    //     $cekwfh = cektgllibur($datawfh,$search_items_all);
                    // }
                    //dd($ceklibur);
                    $namahari = hari($tgl_presensi);
                    if($namahari == "Sabtu"){
                        $jamdirumahkan = 5;
                    }else{
                        $jamdirumahkan = 7;
                    }

                    if(!empty($cekwfh)){
                        if($cekmasakerja > 3){
                            $totaljamdirumahkan = ROUND(($jamdirumahkan / 2),2);
                        }else{
                            $totaljamdirumahkan = $jamdirumahkan;
                        }
                        $totaldirumahkan += $totaljamdirumahkan;
                    }
                    if($namahari=="Minggu"){
                        if(!empty($cekminggumasuk)){
                            if($d->$hari_ke != NULL){
                            $colorcolumn = "";
                            $colortext = "";
                            }else{
                                $colorcolumn = "red";
                                $colortext = "";
                            }
                        }else{
                            $colorcolumn = "#ffaf03";
                            $colortext = "white";
                        }
                    }else{
                        if($d->$hari_ke != NULL){
                            if (!empty($ceklibur)) {
                                $colorcolumn = "rgb(4, 163, 65)";
                                $colortext = "white";
                            }else{
                                $colorcolumn = "";
                                $colortext = "";
                            }

                            if (!empty($cekliburpenggantiminggu)) {
                                $colorcolumn = "#ffaf03";
                                $colortext = "white";
                            }else{
                                $colorcolumn = $colorcolumn;
                                $colortext = $colortext;
                            }

                            if (!empty($cekwfh)) {
                                $colorcolumn = "#fc0380";
                                $colortext = "black";
                            }else{
                                $colorcolumn = $colorcolumn;
                                $colortext = $colortext;
                            }

                            if (!empty($cekwfhfull)) {
                                $colorcolumn = "#9f0ecf";
                                $colortext = "black";
                            }else{
                                $colorcolumn = $colorcolumn;
                                $colortext = $colortext;
                            }
                        }else{
                            if (!empty($ceklibur)) {
                                $colorcolumn = "rgb(4, 163, 65)";
                                $colortext = "white";
                            }else{
                                $colorcolumn = "red";
                                $colortext = "white";
                            }


                            if (!empty($cekliburpenggantiminggu)) {
                                $colorcolumn = "#ffaf03";
                                $colortext = "white";
                            }else{
                                if(empty($ceklibur)){
                                    $colorcolumn = $colorcolumn;
                                    $colortext = $colortext;
                                }

                            }

                            if (!empty($cekwfh)) {
                                $colorcolumn = "#fc0380";
                                $colortext = "black";
                            }else{
                                $colorcolumn = $colorcolumn;
                                $colortext = $colortext;
                            }


                            if (!empty($cekwfhfull)) {
                                $colorcolumn = "#9f0ecf";
                                $colortext = "black";
                            }else{
                                $colorcolumn = $colorcolumn;
                                $colortext = $colortext;
                            }


                        }

                    }
                    if($d->$hari_ke != NULL){
                        $tidakhadir = 0;
                        $datapresensi = explode("|",$d->$hari_ke);
                        $lintashari = $datapresensi[16] != "NA" ? $datapresensi[16] : '';
                        $izinpulangdirut = $datapresensi[17] != "NA" ? $datapresensi[17] : '';
                        $izinabsendirut = $datapresensi[18] != "NA" ? $datapresensi[18] : '';
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
                        $kode_izin_pulang = $datapresensi[8] != "NA" ? $datapresensi[8] : '';
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
                                $desimalterlambat = ROUND(($menitterlambat / 60),2);
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
                                    $desimaljamkeluar = ROUND(($menitkeluarkantor/ 60),2) - 1;
                                }else{
                                    $totaljamkeluar = $jkeluarkantor.":".$mkeluarkantor;
                                    $desimaljamkeluar = ROUND(($menitkeluarkantor/ 60),2);
                                }
                            }else{
                                $totaljamkeluar = $jkeluarkantor.":".$mkeluarkantor;
                                $desimaljamkeluar = ROUND(($menitkeluarkantor/ 60),2);
                            }
                        }else{
                            $totaljamkeluar = "";
                            $desimaljamkeluar = 0;
                            $jamkeluarkantor = 0;
                        }




                        //Tambah 0 Didepan Jika < 10 pada Jam Terlambat
                        $jamterlambat = $jamterlambat < 0 && !empty($kode_izin_terlambat) ? 0 : $jamterlambat;

                        //Jam terlambat dalam Desimal

                        $jt = $jamterlambat + $desimalterlambat;
                        if($jamkeluarkantor > 0){
                            $jk = $jamkeluarkantor + $desimaljamkeluar;
                        }else{
                            $jk = 0;
                        }

                        $jt = !empty($jt) ? $jt : 0;
                        //echo $jamterlambat."|<br>";
                        //echo $menitterlambat."|";
                        // menghitung Denda
                        if (!empty($jam_in) and $kode_dept != 'MKT') {
                            if ($jam_in_presensi > $jam_masuk and empty($kode_izin_terlambat)) {

                                if ($jamterlambat < 1) {
                                    if($menitterlambat >= 5 AND $menitterlambat < 10){
                                        $denda = 5000;
                                        //echo "test5000|";
                                    }else if($menitterlambat >= 10 AND $menitterlambat <15){
                                        $denda = 10000;
                                        //echo "test10ribu|";
                                    }else if($menitterlambat >= 15 AND $menitterlambat <= 59){
                                        $denda = 15000;
                                        //echo "Test15ribu|";
                                    }else{
                                        $denda = 0;
                                    }
                                }else{
                                    $denda = 0;
                                    //echo "testlebihdari1jam";
                                }
                            } else {
                                $denda = 0;
                            }
                        } else {
                            $denda = 0;
                        }

                        //echo $denda."|<br>";

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
                            $premi1 = 5000;
                            $jmlharipremi1 +=1;
                            $jmlpremi1 += $premi;
                        }else if($nama_jadwal=="SHIFT 3" && $grandtotaljam > 5){
                            $premi = 6000;
                            $premi2 = 6000;
                            $jmlharipremi2 +=1;
                            $jmlpremi2 += $premi2;
                        }else{
                            $premi = 0;
                        }



                        if($jam_out != "NA" && $jam_out_tanggal < $jam_pulang_tanggal){
                            $pc = "Pulang Cepat";
                            // $jp1 = strtotime($jam_out_tanggal);
                            // $jp2 = strtotime($jam_pulang_tanggal);
                            // $diffpc = $jp2 - $jp1;

                            // //Total Jam Keluar Kantor
                            // $jampulangcepat = floor($diffpc / (60 * 60));
                            // //Total Menit Keluar Kantor
                            // $menitpulangcepat = floor(($diffpc - ($jampulangcepat * (60 * 60)))/60);

                            // //Tambah 0 di Depan Jika < 10
                            // $jpulangcepat = $jampulangcepat <= 9 ? "0" . $jampulangcepat : $jampulangcepat;
                            // $mpulangcepat = $menitpulangcepat <= 9 ? "0" . $menitpulangcepat : $menitpulangcepat;

                            // if($total_jam == 7){
                            //     $totaljamkeluar = ($jkeluarkantor-1).":".$mkeluarkantor;
                            //     $desimaljamkeluar = ROUND(($menitkeluarkantor/ 60),2) - 1;
                            // }else{
                            //     $totaljamkeluar = $jkeluarkantor.":".$mkeluarkantor;
                            //     $desimaljamkeluar = ROUND(($menitkeluarkantor/ 60),2);
                            // }

                            if(!empty($izinpulangdirut)){
                                $totalpc = 0;
                            }else{
                                $totalpc = $total_jam + $jk - $grandtotaljam;
                            }

                        }else{
                            $pc = "";
                            $totalpc = 0;
                        }

                        if($status=="h"){
                            $izinabsen = 0;
                            $izinsakit = 0;
                        }else if($status=="s"){
                            if($namahari=="Sabtu"){
                                $izinsakit = 5;
                            }elseif($namahari=="Minggu"){
                                if(!empty($cekminggumasuk)){
                                    $izinsakit = 7;
                                }else{
                                    $izinsakit = 0;
                                }
                            }else{
                                $izinsakit = 7;
                            }

                            $izinabsen = 0;
                        }else if($status=="i"){
                            if(empty($izinabsendirut)){
                                if($namahari=="Sabtu"){
                                $izinabsen = 5;
                                }elseif($namahari=="Minggu"){
                                    if(!empty($cekminggumasuk)){
                                        $izinabsen = 7;
                                    }else{
                                        $izinabsen = 0;
                                    }
                                }else{
                                    $izinabsen = 7;
                                }
                            }else{
                                $izinabsen = 0;
                            }

                            $izinsakit = 0;
                        }else if($status=="c"){
                            $izinabsen = 0;
                            $izinsakit = 0;
                        }
                    }else{
                        $jt = 0;
                        $jk = 0;
                        $denda = 0;
                        $premi = 0;
                        $totalpc = 0;
                        $izinabsen = 0;
                        $izinsakit = 0;
                        if(!empty($ceklibur) && $cekmasakerja > 3 || !empty($cekliburpenggantiminggu) && $cekmasakerja > 3 || !empty($cekwfh) || !empty($cekwfhfull) && $cekmasakerja > 3 ){
                        $tidakhadir = 0;
                        }else{
                            if($namahari=="Sabtu"){
                                $tidakhadir = 5;
                            }elseif($namahari=="Minggu"){
                                if(!empty($cekminggumasuk)){
                                    $tidakhadir = 7;
                                }else{
                                    $tidakhadir = 0;
                                }
                            }else{
                                $tidakhadir = 7;
                            }
                        }
                    }
                    $totalterlambat += $jt;
                    $totalkeluar += $jk;
                    $totaldenda += $denda;
                    $totalpremi += $premi;
                    $totaltidakhadir += $tidakhadir;
                    $totalpulangcepat += $totalpc;
                    $totalizinabsen += $izinabsen;
                    $totalizinsakit += $izinsakit;
                }
                $totaljamkerja = $totaljam1bulan - $totalterlambat - $totalkeluar - $totaldirumahkan - $totaltidakhadir - $totalpulangcepat - $totalizinabsen - $izinsakit;
                ?>
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $d->nik }}</td>
                    <td>{{ $d->nama_karyawan }}</td>
                    <td align="center">{{ $d->nama_group }}</td>
                    <td align="center">{{ date("d-m-Y",strtotime($d->tgl_masuk)) }}</td>
                    <td align="center">
                        @php
                        $awal = date_create($d->tgl_masuk);
                        $akhir = date_create(date('Y-m-d')); // waktu sekarang
                        $diff = date_diff( $awal, $akhir );
                        echo $diff->y . ' tahun, '.$diff->m.' bulan, '.$diff->d.' Hari'
                        @endphp
                    </td>
                    <td align="center">{{ $d->nama_dept }}</td>
                    <td align="center">{{ $d->nama_jabatan }}</td>
                    <td align="center">{{ $d->id_kantor=="PST" ? "PUSAT" : strtoupper($d->nama_cabang) }}</td>
                    <td align="center">{{ $d->id_perusahaan }}</td>
                    <td align="center">{{ $d->klasifikasi }}</td>
                    <td align="center">
                        {{ strtoupper($d->jenis_kelamin == "1" ? "Laki-Laki" : "Perempuan") }}
                    </td>
                    <td align="center">

                        @if ($d->status_kawin==1)
                        BELUM MENIKAH
                        @elseif($d->status_kawin==2)
                        MENIKAH
                        @elseif($d->status_kawin==3)
                        CERAI HIDUP
                        @elseif($d->status_kawin==4)
                        DUDA
                        @elseif($d->status_kawin==5)
                        JANDA
                        @endif
                    </td>
                    <td align="right">{{ !empty($d->gaji_pokok) ? rupiah($d->gaji_pokok) : "" }}</td>
                    <td align="right">{{ !empty($d->t_jabatan) ? rupiah($d->t_jabatan) : "" }}</td>
                    <td align="right">{{ !empty($d->t_masakerja) ? rupiah($d->t_masakerja) : "" }}</td>
                    <td align="right">{{ !empty($d->t_tanggungjawab) ? rupiah($d->t_tanggungjawab) : "" }}</td>
                    <td align="right">{{ !empty($d->t_makan) ? rupiah($d->t_makan) : "" }}</td>
                    <td align="right">{{ !empty($d->t_istri) ? rupiah($d->t_istri) : "" }}</td>
                    <td align="right">{{ !empty($d->t_skill) ? rupiah($d->t_skill) : "" }}</td>
                    <td align="right">{{ !empty($d->iu_masakerja) ? rupiah($d->iu_masakerja) : "" }}</td>
                    <td align="right">{{ !empty($d->iu_lembur) ? rupiah($d->iu_lembur) : "" }}</td>
                    <td align="right">{{ !empty($d->iu_penempatan) ? rupiah($d->iu_penempatan) : "" }}</td>
                    <td align="right">{{ !empty($d->iu_kpi) ? rupiah($d->iu_kpi) : "" }}</td>
                    <td align="right">{{ !empty($d->im_ruanglingkup) ? rupiah($d->im_ruanglingkup) : "" }}</td>
                    <td align="right">{{ !empty($d->im_penempatan) ? rupiah($d->im_penempatan) : "" }}</td>
                    <td align="right">{{ !empty($d->im_kinerja) ? rupiah($d->im_kinerja) : "" }}</td>
                    <td align="right">
                        @php
                        $upah = $d->gaji_pokok + $d->t_jabatan+$d->t_masakerja + $d->t_tanggungjawab + $d->t_makan + $d->t_istri + $d->t_skill;
                        @endphp
                        {{ !empty($upah) ? rupiah($upah) : "" }}
                    </td>
                    <td align="right">
                        @php
                        $jmlinsentif = $d->iu_masakerja + $d->iu_lembur+$d->iu_penempatan + $d->iu_kpi + $d->im_ruanglingkup + $d->im_penempatan + $d->im_kinerja;
                        @endphp
                        {{ !empty($jmlinsentif) ? rupiah($jmlinsentif) : "" }}
                    </td>
                    <td style="text-align:center; font-weight:bold">
                        {{ !empty($totaljamkerja) ? $totaljamkerja : '' }}
                    </td>
                    <td align="right">
                        {{ !empty($upah) ? rupiah($upah/173) : "" }}
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="center">{{ !empty($jmlharipremi1) ? $jmlharipremi1 : "" }}</td>
                    <td align="right">{{ !empty($jmlpremi1) ? rupiah($jmlpremi1) : "" }}</td>
                    <td align="center">{{ !empty($jmlharipremi2) ? $jmlharipremi2 : "" }}</td>
                    <td align="right">{{ !empty($jmlpremi2) ? rupiah($jmlpremi2) : "" }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script type="text/javascript" src="{{ asset('dist/js/freeze/js/freeze-table.js') }}"></script>
<script>
    $(function() {
        $('.freeze-table').freezeTable({
            'scrollable': true
            , 'columnNum': 4
        });
    });

</script>
</html>
