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
        <table class="datatable3" style="width: 280%">
            <thead bgcolor="#024a75" style="color:white; font-size:12;">
                <tr bgcolor="#024a75" style="color:white; font-size:12;">
                    <th rowspan="2">No</th>
                    <th rowspan="2">Nik</th>
                    <th rowspan="2" style="width: 4%">Nama karyawan</th>
                    <th rowspan="2">No. Rekening</th>
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
                    <th colspan="2">OT LIBUR 1</th>
                    <th rowspan="2">TOTAL<br>OVERTIME</th>
                    <th colspan="2">PREMI SHIFT 2</th>
                    <th colspan="2">PREMI SHIFT 3</th>
                    <th rowspan="2" style="background-color: orange;">BRUTO</th>
                    <th rowspan="2" style="background-color: black;">POTONGAN<br>JAM</th>
                    <th colspan="3" style="background-color: black;">BPJS</th>
                    <th rowspan="2" style="background-color: black;">DENDA<br>TERLAMBAT</th>
                    <th rowspan="2" style="background-color: black;">CICILAN<br>PJP</th>
                    <th rowspan="2" style="background-color: black;">KASBON</th>
                    <th rowspan="2" style="background-color: black;">PINJ.<br> PERUSAHAAN</th>
                    <th rowspan="2" style="background-color: black;">SPIP</th>
                    <th rowspan="2" style="background-color: orange;">JUMLAH<br>POTONGAN</th>
                    <th rowspan="2" style="background-color: orange;">JUMLAH<br>BERSIH</th>

                </tr>
                <tr>

                    <th>TANGGAL MASUK</th>
                    <th style="width: 3%">MASA KERJA</th>
                    <th>DEPARTEMEN</th>
                    <th style="width: 3%">JABATAN</th>
                    <th>KANTOR <br>CABANG</th>
                    <th>PERUSAHAAN</th>
                    <th>KLASIFIKASI</th>
                    <th>JENIS <br>KELAMIN</th>
                    <th style="width: 3%">STATUS</th>
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
                    <th style="background-color: black;">KESEHATAN</th>
                    <th style="background-color: black;">PERUSAHAAN</th>
                    <th style="background-color: black;">TENAGA KERJA</th>
                </tr>
            </thead>
            <tbody>
                @php
                $no = 1;
                $totaljam1bulan = 173;

                $total_gajipokok = 0;
                $total_tunjangan_jabatan = 0;
                $total_tunjangan_masakerja = 0;
                $total_tunjangan_tanggungjawab = 0;
                $total_tunjangan_makan = 0;
                $total_tunjangan_istri = 0;
                $total_tunjangan_skillkhusus = 0;

                $total_insentif_masakerja = 0;
                $total_insentif_lembur = 0;
                $total_insentif_penempatan = 0;
                $total_insentif_kpi = 0;

                $total_im_ruanglingkup = 0;
                $total_im_penempatan = 0;
                $total_im_kinerja = 0;

                $total_upah = 0;
                $total_insentif = 0;


                $total_all_jamkerja = 0;
                $total_all_upahperjam = 0;

                $total_all_overtime_1 = 0;
                $total_all_upah_ot_1 = 0;

                $total_all_overtime_2 = 0;
                $total_all_upah_ot_2 = 0;

                $total_all_overtime_libur = 0;
                $total_all_upah_overtime_libur = 0;


                $total_all_upah_overtime = 0;

                $total_all_hari_shift_2 = 0;
                $total_all_premi_shift_2 = 0;

                $total_all_hari_shift_3 = 0;
                $total_all_premi_shift_3 = 0;

                $total_all_bruto = 0;

                $total_all_potongan_jam = 0;

                $total_all_bpjskesehatan = 0;
                $total_all_bpjstk = 0;
                $total_all_denda = 0;

                $total_all_pjp = 0;
                $total_all_kasbon = 0;
                $total_all_nonpjp = 0;
                $total_all_spip = 0;

                $total_all_potongan = 0;
                $total_all_bersih = 0;




                @endphp
                @foreach ($presensi as $d)
                <?php
                    $totalterlambat = 0;
                    $totalkeluar = 0;
                    $totaldenda = 0;
                    $totalpremi = 0;
                    $totaldirumahkan = 0;
                    $jmldirumahkan =0;
                    $totaltidakhadir = 0;
                    $totalpulangcepat = 0;
                    $totalizinabsen = 0;
                    $total_overtime_1 = 0;
                    $total_overtime_2 = 0;
                    $total_overtime_libur_1 = 0;
                    $total_overtime_libur_2 = 0;
                    $izinsakit = 0;
                    $jmlsid = 0;
                    $totalizinsakit = 0;
                    // $jmlharipremi1 = 0;
                    // $jmlharipremi2 = 0;
                    // $jmlpremi1 = 0;
                    // $jmlpremi2 = 0;
                    $totalpremi_shift_2 = 0;
                    $totalpremilembur_shift_2 = 0;
                    $totalpremi_shift_3 = 0;
                    $totalpremilembur_shift_3 = 0;
                    $totalhari_shift_2 = 0;
                    $totalharilembur_shift_2 = 0;
                    $totalhari_shift_3 = 0;
                    $totalharilembur_shift_3 = 0;
                    for($i=0; $i < count($rangetanggal); $i++){
                        $hari_ke = "hari_".$i+1;
                        $tgl_presensi =  $rangetanggal[$i];

                        // // Menghitung Masa Kerja
                        // $start_kerja = date_create($d->tgl_masuk);
                        // $end_kerja = date_create($tgl_presensi);
                        // $cekmasakerja =  diffInMonths($start_kerja, $end_kerja);


                        $start_kerja = date_create($d->tgl_masuk);
                        $end_kerja = date_create($tgl_presensi); // waktu sekarang
                        $diff = date_diff( $start_kerja, $end_kerja );
                        $cekmasakerja = ($diff->y * 12 ) + $diff->m;

                        $tgllibur = "'".$tgl_presensi."'";

                        $search_items = array(
                            'nik'=>$d->nik,
                            'id_kantor' => $d->id_kantor,
                            'tanggal_libur' => $tgl_presensi
                        );
                        $search_items_lembur = array(
                            'nik'=>$d->nik,
                            'id_kantor' => $d->id_kantor,
                            'tanggal_lembur' => $tgl_presensi
                        );
                        $search_items_minggumasuk = array(
                            'nik'=>$d->nik,
                            'id_kantor' => $d->id_kantor,
                            'tanggal_diganti' => $tgl_presensi
                        );
                        $search_items_all = array(
                            'nik'=>'ALL',
                            'id_kantor' => $d->id_kantor,
                            'tanggal_libur' => $tgl_presensi
                        );


                    $ceklibur = cektgllibur($datalibur, $search_items);
                    $cekliburpenggantiminggu = cektgllibur($dataliburpenggantiminggu,$search_items);
                    $cekminggumasuk = cektgllibur($dataminggumasuk,$search_items_minggumasuk);
                    $cekwfh = cektgllibur($datawfh,$search_items);
                    $cekwfhfull = cektgllibur($datawfhfull,$search_items);
                    $ceklembur = cektgllibur($datalembur,$search_items_lembur);


                    //Menghitung Jumlah Jam Dirumahkan
                    $namahari = hari($tgl_presensi);
                    if($namahari == "Sabtu"){
                        $jamdirumahkan = 5;
                    }else{
                        $jamdirumahkan = 7;
                    }





                    //Ceklembur
                    if(!empty($ceklembur)){
                        $tgl_lembur_dari = $ceklembur[0]["tanggal_dari"];
                        $tgl_lembur_sampai = $ceklembur[0]["tanggal_sampai"];
                        $jamlembur_dari = date("H:i",strtotime($tgl_lembur_dari));
                        $jmljam_lbr = hitungjamdesimal($tgl_lembur_dari,$tgl_lembur_sampai);
                        $istirahatlbr = $ceklembur[0]["istirahat"] == 1 ? 1 : 0;
                        $jmljam_lembur = $jmljam_lbr > 7 ? 7 : $jmljam_lbr - $istirahatlbr;
                        $kategori_lembur = $ceklembur[0]["kategori"];
                        if(empty($ceklibur) && empty($cekliburpenggantiminggu) && empty($cekwfhfull) && $namahari != "Minggu" ){
                            if($jamlembur_dari >= "22:00" && $jmljam_lbr>=4.98){
                                $premilembur = 6000;
                                $premilembur_shift_3 = 6000;
                                $totalpremilembur_shift_3 += $premilembur_shift_3;
                                $totalharilembur_shift_3 += 1;
                            }else if($jamlembur_dari >= "15:00" && $jmljam_lbr>=4.98){
                                $premilembur = 5000;
                                $premilembur_shift_2 = 5000;
                                $totalpremilembur_shift_2 += $premilembur_shift_2;
                                $totalharilembur_shift_2 += 1;
                            }
                        }
                        if ($kategori_lembur==1) {
                            // $overtime_1 = $jmljam_lembur > 1 ? 1 : $jmljam_lembur;
                            // $overtime_2 = $jmljam_lembur > 1 ? $jmljam_lembur -1 : 0;
                            // $total_overtime_1 += $overtime_1;
                            // $total_overtime_2 += $overtime_2;

                            $overtime_1 = $jmljam_lembur > 1 ? 1 : $jmljam_lembur;
                            $overtime_1 = round($overtime_1,2,PHP_ROUND_HALF_DOWN);
                            $overtime_2 = $jmljam_lembur > 1 ? $jmljam_lembur -1 : 0;
                            $overtime_2 = round($overtime_2,2,PHP_ROUND_HALF_DOWN);
                            // echo $tgl_presensi."-".$overtime_1;
                            // echo "<br>";
                            $total_overtime_1 += $overtime_1;
                            $total_overtime_2 += $overtime_2;
                        }else if($kategori_lembur==2){
                            // $overtime_libur_1 = $jmljam_lembur >= 4 ? 4 : $jmljam_lembur;
                            // $overtime_libur_2 = $jmljam_lembur > 4 ? $jmljam_lembur-4 : 0;
                            $overtime_libur_1 = !empty($ceklibur) && $d->nama_jabatan =="SECURITY" ? $jmljam_lembur * 2 : $jmljam_lembur;
                            $overtime_libur_2 = 0;
                            $total_overtime_libur_1 += $overtime_libur_1;
                            $total_overtime_libur_2 += $overtime_libur_2;
                        }

                        //echo $d->nik."------". $tgl_presensi."-----".$jmljam_lembur."<br>";
                    }else{
                        $premilembur = 0;
                    }




                    //Pewarnaan Kolom
                    if($namahari=="Minggu"){ // Jika Hari Minggu
                        if(!empty($cekminggumasuk)){ // Cek Jika Minggu Harus Masuk
                            if($d->$hari_ke != NULL){ // Cek Jika Melakukan Presensi
                                $colorcolumn = ""; // Warna Kolom Putih
                                $colortext = "";
                            }else{
                                $colorcolumn = "red"; // Warna Kolom Merah Jika TIdak Absen
                                $colortext = "";
                            }
                        }else{ // Jika Minggu Libur atau Tidak Ada Jadwal Masuk
                            $colorcolumn = "#ffaf03"; // Warna Kolom Orange
                            $colortext = "white";
                        }
                    }else{ // Jika Hari Bukan Hari Minggu
                        if($d->$hari_ke != NULL){ // Jika Karyawa Melakukan Presensi
                            if (!empty($ceklibur)) { // Jika Pada Hari Itu ada Libur Nasional
                                $colorcolumn = "rgb(4, 163, 65)";
                                $colortext = "white";
                            }else{
                                $colorcolumn = "";
                                $colortext = "";
                            }

                            if (!empty($cekliburpenggantiminggu)) { // Jika Pada Hari Itu adalah Libur Pengganti Minggu
                                $colorcolumn = "#ffaf03";
                                $colortext = "white";
                            }else{
                                $colorcolumn = $colorcolumn;
                                $colortext = $colortext;
                            }

                            if (!empty($cekwfh)) { // Jika pada Hari Itu Sedang Dirumahkan
                                $colorcolumn = "#fc0380";
                                $colortext = "black";
                            }else{
                                $colorcolumn = $colorcolumn;
                                $colortext = $colortext;
                            }

                            if (!empty($cekwfhfull)) { // Jika pada Hari Itu Sedang WFH
                                $colorcolumn = "#9f0ecf";
                                $colortext = "black";
                            }else{
                                $colorcolumn = $colorcolumn;
                                $colortext = $colortext;
                            }
                        }else{ // Jika Karyawan Tidak Melakukan Presensi

                            if (!empty($ceklibur)) { // Jika Pada Hari Itu ada Libur Nasional
                                $colorcolumn = "rgb(4, 163, 65)";
                                $colortext = "white";
                            }else{
                                $colorcolumn = "red";
                                $colortext = "white";
                            }


                            if (!empty($cekliburpenggantiminggu)) { // Jika Pada Hari Itu adalah Libur Pengganti Minggu
                                $colorcolumn = "#ffaf03";
                                $colortext = "white";
                            }else{
                                if(empty($ceklibur)){
                                    $colorcolumn = $colorcolumn;
                                    $colortext = $colortext;
                                }

                            }

                            if (!empty($cekwfh)) { // Jika pada Hari Itu Sedang Dirumahkan
                                $colorcolumn = "#fc0380";
                                $colortext = "black";
                            }else{
                                $colorcolumn = $colorcolumn;
                                $colortext = $colortext;
                            }


                            if (!empty($cekwfhfull)) { // Jika pada Hari Itu Sedang WFH
                                $colorcolumn = "#9f0ecf";
                                $colortext = "black";
                            }else{
                                $colorcolumn = $colorcolumn;
                                $colortext = $colortext;
                            }
                        }

                    }



                    if($d->$hari_ke != NULL){
                        //$tidakhadir = 0; // Jika Karyawan Absen Maka $tidakhadir dihitung 0

                        $datapresensi = explode("|",$d->$hari_ke); // Split Data Presensi

                        $lintashari = $datapresensi[16] != "NA" ? $datapresensi[16] : ''; // Lintas Hari
                        $izinpulangdirut = $datapresensi[17] != "NA" ? $datapresensi[17] : ''; //Izin Pulang Persetujuan Dirut
                        $izinabsendirut = $datapresensi[18] != "NA" ? $datapresensi[18] : ''; // Izin Absen Persetujuan Dirut
                        $keperluankeluar = $datapresensi[19] != "NA" ? $datapresensi[19] : ''; // Izin Absen Persetujuan Dirut
                        $izinterlambatdirut = $datapresensi[20] != "NA" ? $datapresensi[20] : ''; // Izin Absen Persetujuan Dirut
                        if(!empty($lintashari)){ // Jika Jadwal Presesni Lintas Hari
                            $tgl_pulang = date('Y-m-d', strtotime('+1 day', strtotime($tgl_presensi)));
                            // Tanggal Pulang adalah Tanggal Berikutnya
                        }else{
                            $tgl_pulang = $tgl_presensi; // Tanggal Pulang adalah Tanggal Presensi
                        }

                        //Jam Masuk Presensi
                        $jam_in = $datapresensi[0]; // Y-m-d H:i:s Jam dan Tanggal Karyawan Melakukan Presensi Masuk
                        $jam_in_presensi = $jam_in != "NA" ? date("H:i",strtotime($jam_in)) : ""; // Jam Presensi Masuk
                        $jam_in_tanggal = $jam_in != "NA" ?  date("Y-m-d H:i",strtotime($jam_in)) : "";// Jam Tgl Masuk Presensi

                        //Jam Pulang Presensi
                        $jam_out = $datapresensi[1]; // Y-m-d H:i:s Jam dan Tangal Karyawan Melakukan Presensi Pulang
                        $jam_out_presensi = $jam_out != "NA" ? date("H:i",strtotime($jam_out)) : ""; //Jam Presensi Pulang
                        $jam_out_tanggal = $jam_out != "NA" ? date("Y-m-d H:i",strtotime($jam_out)) : "";//Jam Tgl Presensi Pulang


                        $nama_jadwal = $datapresensi[2];
                        if($namahari== "Minggu"){
                            if(!empty($cekminggumasuk)){
                                if($d->nama_jabatan=="SPG" || $d->nama_jabatan=="SPB" || !empty($cekwfh)){
                                    $jam_masuk = $jam_in_presensi;
                                    $jam_masuk_tanggal = $tgl_presensi." ".$jam_masuk;
                                }else{
                                    $jam_masuk = date("H:i",strtotime($datapresensi[3]));
                                    $jam_masuk_tanggal = $tgl_presensi." ".$jam_masuk;
                                }

                                if($d->nama_jabatan=="SPG" || $d->nama_jabatan=="SPB" || !empty($cekwfh)){
                                    $jam_pulang = !empty($jam_out_presensi) ? $jam_out_presensi : "";
                                    $jam_pulang_tanggal = !empty($jam_out_presensi) ? $tgl_pulang." ".$jam_pulang : "";
                                }else{
                                    $jam_pulang = $datapresensi[4] != "NA" ? date("H:i",strtotime($datapresensi[4])) : "";
                                    $jam_pulang_tanggal = $datapresensi[4] != "NA" ? $tgl_pulang." ".$jam_pulang : "";
                                }
                            }else{
                                $jam_masuk = $jam_in_presensi;
                                $jam_masuk_tanggal = $tgl_presensi." ".$jam_masuk;
                                $jam_pulang = !empty($jam_out_presensi) ? $jam_out_presensi : "";
                                $jam_pulang_tanggal = !empty($jam_out_presensi) ? $tgl_pulang." ".$jam_pulang : "";
                            }
                        }else{
                            if(!empty($ceklibur) || !empty($cekliburpenggantiminggu)){
                                $jam_masuk = $jam_in_presensi;
                                $jam_masuk_tanggal = $tgl_presensi." ".$jam_masuk;
                                $jam_pulang = !empty($jam_out_presensi) ? $jam_out_presensi : "";
                                $jam_pulang_tanggal = !empty($jam_out_presensi) ? $tgl_pulang." ".$jam_pulang : "";
                            }else{
                                if($d->nama_jabatan=="SPG" || $d->nama_jabatan=="SPB" || !empty($cekwfh)){
                                    $jam_masuk = $jam_in_presensi;
                                    $jam_masuk_tanggal = $tgl_presensi." ".$jam_masuk;
                                }else{
                                    $jam_masuk = date("H:i",strtotime($datapresensi[3]));
                                    $jam_masuk_tanggal = $tgl_presensi." ".$jam_masuk;
                                }

                                if($d->nama_jabatan=="SPG" || $d->nama_jabatan=="SPB" || !empty($cekwfh)){
                                    $jam_pulang = !empty($jam_out_presensi) ? $jam_out_presensi : "";
                                    $jam_pulang_tanggal = !empty($jam_out_presensi) ? $tgl_pulang." ".$jam_pulang : "";
                                }else{
                                    $jam_pulang = $datapresensi[4] != "NA" ? date("H:i",strtotime($datapresensi[4])) : "";
                                    $jam_pulang_tanggal = $datapresensi[4] != "NA" ? $tgl_pulang." ".$jam_pulang : "";
                                }
                            }
                        }


                        //$jam_pulang_presensi =$jam_pulang != "NA" ? date("H:i",strtotime($jam_pulang)) : '';

                        //Keluar Masuk Kantor
                        $jam_keluar = $datapresensi[9] != "NA" ? date("H:i",strtotime($datapresensi[9])) : '';


                        $jam_masuk_kk = $datapresensi[10] != "NA" ? date("H:i",strtotime($datapresensi[10])) : '';

                        $total_jam = $datapresensi[11] != "NA" ? $datapresensi[11] : 0;

                        $status = $datapresensi[5] != "NA" ? $datapresensi[5] : '';

                        $sid = $datapresensi[12] != "NA" ? $datapresensi[12] : '';

                        $kode_izin_terlambat = $datapresensi[7] != "NA" ? $datapresensi[7] : '';

                        $kode_izin_pulang = $datapresensi[8] != "NA" ? $datapresensi[8] : '';

                        $jam_istirahat = $datapresensi[14];
                        $jam_istirahat_presensi =$datapresensi[14] != "NA" ? date("H:i",strtotime($datapresensi[14])) : '';
                        $jam_istirahat_presensi_tanggal = $datapresensi[14] != "NA" ? $tgl_pulang." ".$jam_istirahat_presensi : '';

                        $jam_awal_istirahat = $datapresensi[13] != "NA" ? date("H:i",strtotime($datapresensi[13])) : '';;
                        $jam_awal_istirahat_tanggal = $datapresensi[14] != "NA" ? $tgl_pulang." ".$jam_awal_istirahat : '';

                        $jam_akhir_istirahat = $datapresensi[14] != "NA" ? date("H:i",strtotime($datapresensi[14])) : '';;
                        $jam_akhir_istirahat_tanggal = $datapresensi[14] != "NA" ? $tgl_pulang." ".$jam_akhir_istirahat :'';


                        $kode_dept = $d->kode_dept;

                        if (!empty($jam_in)) {
                            if ($jam_in_tanggal > $jam_masuk_tanggal) {
                                //Hitung Jam Keterlambatan
                                $j1 = strtotime($jam_masuk_tanggal);
                                $j2 = strtotime($jam_in_tanggal);

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
                            $jam_keluar_tanggal = $datapresensi[9] != "NA" ?  $tgl_pulang." ".$jam_keluar : "";
                            if(!empty($jam_masuk_kk)){
                                $jam_masuk_kk_tanggal = $tgl_pulang." ".$jam_masuk_kk ;
                            }else{
                                $jam_masuk_kk_tanggal = $tgl_pulang." ".$jam_pulang;
                            }

                            $jk1 = strtotime($jam_keluar_tanggal);
                            $jk2 = strtotime($jam_masuk_kk_tanggal);
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

                        if(!empty($izinterlambatdirut) && $izinterlambatdirut == 1){
                            $jt = 0;
                        }else{
                            $jt = round($jamterlambat + $desimalterlambat,2,PHP_ROUND_HALF_DOWN);
                        }
                        if($jamkeluarkantor > 0){
                            if($keperluankeluar == "K"){
                                $jk = 0;
                            }else{
                                $jk = $jamkeluarkantor + $desimaljamkeluar;
                            }
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
                        if($jam_out_tanggal > $jam_awal_istirahat_tanggal && $jam_out_tanggal <= $jam_akhir_istirahat_tanggal){ // Shift 3 Belum Di Set
                            // $jout = $jam_awal_istirahat_tanggal;
                            $jout = $jam_awal_istirahat_tanggal;
                        }else{
                            $jout = $jam_out_tanggal;
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
                        if(!empty($cekwfh)){
                            $totaljam = $jam - $jt - $jk;
                        }else{
                            $totaljam = $total_jam - $jt - $jk;
                        }


                        // if ($jam_out != "NA") {
                        //     if ($jam_out_tanggal < $jam_pulang_tanggal) { //Shift 3 Belum Di Set | Coba
                        //         if($jam_out_tanggal > $jam_akhir_istirahat_tanggal && $jam_istirahat != "NA"){
                        //             $desimalmenit = ROUND(($menit * 100) / 60);
                        //             $grandtotaljam = $jam-1 . "." . $desimalmenit;
                        //             $cek = "A";
                        //         }else{
                        //             $desimalmenit = ROUND(($menit * 100) / 60);
                        //             $grandtotaljam = $jam . "." . $desimalmenit;
                        //             $cek = "B".$jam_out_tanggal."------".$jam_akhir_istirahat_tanggal;
                        //         }

                        //         $grandtotaljam = $grandtotaljam - $jt - $jk;

                        //     } else {
                        //         $cek = "C";
                        //         $desimalmenit = 0;
                        //         $grandtotaljam = $totaljam;
                        //     }
                        // } else {
                        //     $desimalmenit = 0;
                        //     $grandtotaljam = 0;
                        // }

                        if ($jam_out != "NA") {
                            if ($jam_out_tanggal < $jam_pulang_tanggal) { //Shift 3 Belum Di Set | Coba
                                if($jam_out_tanggal > $jam_akhir_istirahat_tanggal && $jam_istirahat != "NA"){
                                    $desimalmenit = ROUND(($menit / 60),2);
                                    //$desimalmenit = ROUND(($menit * 100) / 60);
                                    $grandtotaljam = ($jam-1) + $desimalmenit;
                                }else{
                                    $desimalmenit = ROUND(($menit / 60),2);
                                    $grandtotaljam = $jam + $desimalmenit;

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
                                    $jmlsid += 1;
                                    $grandtotaljam = $jamdirumahkan;
                                    $ceksid =1;
                                    if(!empty($cekwfh)){
                                        $ceksid = 2;
                                        $grandtotaljam = $grandtotaljam / 2 ;
                                    }
                                    if($jmlsid > 5 && $d->nik == "21.10.460" && $bulan == 9 && $tahun = 2023){
                                        if($namahari != "Minggu"){
                                            if($namahari == "Sabtu"){
                                                $grandtotaljam = $grandtotaljam - 1.25;
                                            }else{
                                                $grandtotaljam = $grandtotaljam - 1.75;
                                            }
                                        }

                                        $ceksid = 3;
                                    }
                                }else{
                                    $grandtotaljam = 0;
                                }
                            }else if($status == "c"){
                                $grandtotaljam = $jamdirumahkan;
                                if(!empty($cekwfh)){
                                    $grandtotaljam = $grandtotaljam / 2 ;
                                }
                            }else{
                                $grandtotaljam = 0;
                            }
                        }


                        if ($nama_jadwal == "SHIFT 2" && $grandtotaljam >= 4.98) {
                            $premi = 5000;
                            $premi_shift_2 = 5000;
                            $totalpremi_shift_2 += $premi_shift_2;
                            $totalhari_shift_2 += 1;
                        }else if($nama_jadwal=="SHIFT 3" && $grandtotaljam >= 4.98){
                            $premi = 6000;
                            $premi_shift_3 = 6000;
                            $totalpremi_shift_3 += $premi_shift_3;
                            $totalhari_shift_3 += 1;
                        }else{
                            $premi = 0;
                        }



                        if($jam_out != "NA" && $jam_out_tanggal < $jam_pulang_tanggal){
                            $pc = "Pulang Cepat";
                            if(!empty($izinpulangdirut) && $izinpulangdirut == 1){
                                $totalpc = 0;
                                $menitpc = 0;
                            }else{
                                $jpc1 = strtotime($jam_out_tanggal);
                                $jpc2 = strtotime($jam_pulang_tanggal);

                                $diffpc = $jpc2 - $jpc1;
                                //Jam Terlambat
                                $jampc = floor($diffpc / (60 * 60));
                                //Menit Terlambat
                                $menitpc = floor(($diffpc - ($jampc * (60 * 60)))/60);


                                //Toleransi Pulang Cepat
                                // if($jampc > 5 || $jampc >= 1){
                                //     $totalpc = $total_jam + $jk - $grandtotaljam;
                                // }else{
                                //     $totalpc = 0;
                                // }



                                $totalpc = $total_jam + $jk - $grandtotaljam;


                            }

                        }else{
                            $pc = "";
                            $totalpc = 0;
                            $menitpc = 0;
                        }

                        // echo $rangetanggal[$i]."-".$total_jam."+".$jk."+".$grandtotaljam."(".$cek.")----".$menitpc."<br>";


                        //Update 20/11/2023
                        if(!empty($cekwfh)){

                            $search_items_next = array(
                                'nik'=>$d->nik,
                                'id_kantor' => $d->id_kantor,
                                'tanggal_libur' => date('Y-m-d', strtotime('+1 day', strtotime($tgl_presensi)))
                            );

                            $cekliburnext = cektgllibur($datalibur,$search_items_next);
                            if($namahari == "Jumat" && !empty($cekliburnext)){
                                $jamdirumahkan = 5;
                                $tambahjamdirumahkan =  2;
                            }else{
                                $tambahjamdirumahkan = 0;
                            }


                            $jmldirumahkan += 1;
                            // if($cekmasakerja >= 3){
                            //     $totaljamdirumahkan = ($jamdirumahkan + $tambahjamdirumahkan) - (ROUND(((50/100) * $jamdirumahkan),2) + $tambahjamdirumahkan);
                            //     if($bulan==10 && $tahun == 2023){
                            //         $group_aida = array('AIDA A','AIDA B','AIDA C');
                            //         if(in_array($d->nama_group,$group_aida) ){
                            //             if($tgl_presensi >= '2023-10-09'){
                            //                 $totaljamdirumahkan = ($jamdirumahkan + $tambahjamdirumahkan) -  (ROUND(((75/100) * $jamdirumahkan),2) + $tambahjamdirumahkan);
                            //             }
                            //         }else{
                            //             if($jmldirumahkan >= 12){
                            //                 $totaljamdirumahkan = ($jamdirumahkan + $tambahjamdirumahkan) - (ROUND(((75/100) * $jamdirumahkan),2) + $tambahjamdirumahkan);
                            //             }
                            //         }
                            //     }
                            // }else{
                            //     $totaljamdirumahkan = 0;
                            // }

                            $totaljamdirumahkan = ($jamdirumahkan + $tambahjamdirumahkan) - (ROUND(((50/100) * $jamdirumahkan),2) + $tambahjamdirumahkan);
                            if($bulan==10 && $tahun == 2023){
                                $group_aida = array('AIDA A','AIDA B','AIDA C');
                                if(in_array($d->nama_group,$group_aida) ){
                                    if($tgl_presensi >= '2023-10-09'){
                                        $totaljamdirumahkan = ($jamdirumahkan + $tambahjamdirumahkan) -  (ROUND(((75/100) * $jamdirumahkan),2) + $tambahjamdirumahkan);
                                    }
                                }else{
                                    if($jmldirumahkan >= 12){
                                        $totaljamdirumahkan = ($jamdirumahkan + $tambahjamdirumahkan) - (ROUND(((75/100) * $jamdirumahkan),2) + $tambahjamdirumahkan);
                                    }
                                }
                            }
                            $totaldirumahkan += $totaljamdirumahkan;
                        }

                        if($status== "a"){
                            if($namahari=="Sabtu"){
                                $tidakhadir = 5;
                            }else{
                                $tidakhadir = 7;
                            }
                        }else{
                            $tidakhadir = 0; // Jika Karyawan Absen Maka $tidakhadir dihitung 0
                        }

                        if($status=="h"){
                            $izinabsen = 0;
                            $izinsakit = 0;
                        }else if($status=="s"){
                            if($namahari != "Minggu"){
                                if(empty($sid)){
                                    if(empty($izinabsendirut) || $izinabsendirut == 2){
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
                                    }else{
                                        $izinsakit = 0;
                                    }
                                }else{
                                    if($jmlsid > 5 && $d->nik == "21.10.460" && $bulan == 9 && $tahun = 2023){
                                        if($namahari == "Sabtu"){
                                            $izinsakit = 1.25;
                                        }else{
                                            $izinsakit = 1.75;
                                        }
                                    }else{
                                        $izinsakit = 0;
                                    }
                                }
                            }else{
                                $izinsakit = 0;
                            }


                            $izinabsen = 0;
                        }else if($status=="i"){
                            if(empty($izinabsendirut) || $izinabsendirut == 2){
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
                        $premilembur = 0;
                        $totalpc = 0;
                        $izinabsen = 0;
                        $izinsakit = 0;
                        if(!empty($ceklibur) && $cekmasakerja >= 3 ||
                        !empty($cekliburpenggantiminggu) ||
                        !empty($cekwfh)  ||
                        !empty($cekwfhfull) && $cekmasakerja >= 3 ){
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

                        //Update 20/11/2023

                        if(!empty($cekwfh)){

                            $search_items_next = array(
                                'nik'=>$d->nik,
                                'id_kantor' => $d->id_kantor,
                                'tanggal_libur' => date('Y-m-d', strtotime('+1 day', strtotime($tgl_presensi)))
                            );

                            $cekliburnext = cektgllibur($datalibur,$search_items_next);
                            if($namahari == "Jumat" && !empty($cekliburnext)){
                                $jamdirumahkan = 5;
                                $tambahjamdirumahkan =  2;
                            }else{
                                $tambahjamdirumahkan = 0;
                            }


                            $jmldirumahkan += 1;
                            // if($cekmasakerja >= 3){
                            //     $totaljamdirumahkan = ($jamdirumahkan + $tambahjamdirumahkan) - (ROUND(((50/100) * $jamdirumahkan),2) + $tambahjamdirumahkan);
                            //     if($bulan==10 && $tahun == 2023){
                            //         $group_aida = array('AIDA A','AIDA B','AIDA C');
                            //         if(in_array($d->nama_group,$group_aida) ){
                            //             if($tgl_presensi >= '2023-10-09'){
                            //                 $totaljamdirumahkan = ($jamdirumahkan + $tambahjamdirumahkan) -  (ROUND(((75/100) * $jamdirumahkan),2) + $tambahjamdirumahkan);
                            //             }
                            //         }else{
                            //             if($jmldirumahkan >= 12){
                            //                 $totaljamdirumahkan = ($jamdirumahkan + $tambahjamdirumahkan) - (ROUND(((75/100) * $jamdirumahkan),2) + $tambahjamdirumahkan);
                            //             }
                            //         }
                            //     }
                            // }else{
                            //     $totaljamdirumahkan = 0;
                            // }

                            $totaljamdirumahkan = ($jamdirumahkan + $tambahjamdirumahkan) - (ROUND(((50/100) * $jamdirumahkan),2) + $tambahjamdirumahkan);
                            if($bulan==10 && $tahun == 2023){
                                $group_aida = array('AIDA A','AIDA B','AIDA C');
                                if(in_array($d->nama_group,$group_aida) ){
                                    if($tgl_presensi >= '2023-10-09'){
                                        $totaljamdirumahkan = ($jamdirumahkan + $tambahjamdirumahkan) -  (ROUND(((75/100) * $jamdirumahkan),2) + $tambahjamdirumahkan);
                                    }
                                }else{
                                    if($jmldirumahkan >= 12){
                                        $totaljamdirumahkan = ($jamdirumahkan + $tambahjamdirumahkan) - (ROUND(((75/100) * $jamdirumahkan),2) + $tambahjamdirumahkan);
                                    }
                                }
                            }


                            $totaldirumahkan += $totaljamdirumahkan;
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

                if($d->nama_jabatan=="DIREKTUR"){
                    $totaljamkerja = 173;
                }else{
                    $totaljamkerja = $totaljam1bulan - $totalterlambat - $totalkeluar - $totaldirumahkan - $totaltidakhadir - $totalpulangcepat - $totalizinabsen - $totalizinsakit;
                }


                if($d->nama_jabatan=="DIREKTUR"){
                    $totalpotonganjam = 0;
                }else{
                    $totalpotonganjam = $totalterlambat + $totalkeluar + $totaldirumahkan + $totaltidakhadir + $totalpulangcepat + $totalizinabsen + $totalizinsakit;
                }


                $totalhariall_shift_2 = $totalhari_shift_2 + $totalharilembur_shift_2;
                $totalpremiall_shift_2 = $totalpremi_shift_2 + $totalpremilembur_shift_2;

                $totalhariall_shift_3 = $totalhari_shift_3 + $totalharilembur_shift_3;
                $totalpremiall_shift_3 = $totalpremi_shift_3 + $totalpremilembur_shift_3;


                //UPAH
                $upah = $d->gaji_pokok + $d->t_jabatan+$d->t_masakerja + $d->t_tanggungjawab + $d->t_makan + $d->t_istri + $d->t_skill;

                //Upah Per Jam

                $upah_perjam =$upah/173;


                //INSENTIF
                $jmlinsentif = $d->iu_masakerja + $d->iu_lembur+$d->iu_penempatan + $d->iu_kpi + $d->im_ruanglingkup + $d->im_penempatan + $d->im_kinerja;


                //UPAH OVERTIME

                if ($d->nama_jabatan=="SECURITY") {
                    $upah_ot_1 = 8000 * $total_overtime_1;
                }else{
                    $upah_ot_1 = ($upah_perjam * 1.5) * $total_overtime_1;
                }

                if ($d->nama_jabatan=="SECURITY") {
                    $upah_ot_2 = 8000 * $total_overtime_2;
                }else{
                    $upah_ot_2 = ($upah_perjam * 2) * $total_overtime_2;
                }

                if ($d->nama_jabatan=="SECURITY") {
                    $upah_otl_1 = 13143 * $total_overtime_libur_1;
                }else{
                    $upah_otl_1 = floor(($upah_perjam * 2) * $total_overtime_libur_1);
                }

                if ($d->nama_jabatan=="SECURITY") {
                    $upah_otl_2 = 0;
                }else{
                    $upah_otl_2 = ($upah_perjam * 2) * $total_overtime_libur_2;
                }


                $total_upah_overtime = $upah_ot_1 + $upah_ot_2 + $upah_otl_1 + $upah_otl_2;

                $bruto = ($upah_perjam * $totaljamkerja) + $total_upah_overtime + $totalpremiall_shift_2 + $totalpremiall_shift_3;


                $bpjskesehatan = $d->iuran_kes;
                $bpjstenagakerja = $d->iuran_tk;

                if($d->id_kantor=="PST" && $cekmasakerja >=3
                || $d->id_kantor=="TSM" && $cekmasakerja >= 3
                || $d->spip == 1){
                    $spip = 5000;
                }else{
                    $spip = 0;
                }

                $potongan = ROUND($bpjskesehatan + $bpjstenagakerja + $totaldenda + $d->cicilan_pjp + $d->jml_kasbon + $d->jml_nonpjp + $spip,0);
                $jmlbersih = $bruto - $potongan;

                //TOTAL
                //Total Gaji Pokok
                $total_gajipokok += $d->gaji_pokok;
                $total_tunjangan_jabatan += $d->t_jabatan;
                $total_tunjangan_masakerja += $d->t_masakerja;
                $total_tunjangan_tanggungjawab += $d->t_tanggungjawab;
                $total_tunjangan_makan += $d->t_makan;
                $total_tunjangan_istri += $d->t_istri;
                $total_tunjangan_skillkhusus += $d->t_skill;

                $total_insentif_masakerja += $d->iu_masakerja;
                $total_insentif_lembur += $d->iu_lembur;
                $total_insentif_penempatan += $d->iu_penempatan;
                $total_insentif_kpi += $d->iu_kpi;

                $total_im_ruanglingkup += $d->im_ruanglingkup;
                $total_im_penempatan += $d->im_penempatan;
                $total_im_kinerja += $d->im_kinerja;


                $total_upah += $upah;
                $total_insentif += $jmlinsentif;

                $total_all_jamkerja += $totaljamkerja;
                $total_all_upahperjam += $upah_perjam;

                $total_all_overtime_1 += $total_overtime_1;
                $total_all_upah_ot_1 += $upah_ot_1;

                $total_all_overtime_2 += $total_overtime_2;
                $total_all_upah_ot_2 += $upah_ot_2;

                $total_all_overtime_libur += $total_overtime_libur_1;
                $total_all_upah_overtime_libur += $upah_otl_1;

                $total_all_upah_overtime += $total_upah_overtime;

                $total_all_hari_shift_2 += $totalhariall_shift_2;
                $total_all_premi_shift_2 += $totalpremiall_shift_2;

                $total_all_hari_shift_3 += $totalhariall_shift_3;
                $total_all_premi_shift_3 += $totalpremiall_shift_3;

                $total_all_bruto += $bruto;

                $total_all_potongan_jam += $totalpotonganjam;
                $total_all_bpjskesehatan += $bpjskesehatan;
                $total_all_bpjstk += $bpjstenagakerja;

                $total_all_denda += $totaldenda;

                $total_all_pjp += $d->cicilan_pjp;
                $total_all_kasbon += $d->jml_kasbon;
                $total_all_nonpjp += $d->jml_nonpjp;
                $total_all_spip += $spip;

                $total_all_potongan += $potongan;
                $total_all_bersih += $jmlbersih;

                ?>
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>'{{ $d->nama_jabatan=="SECURITY" ? $d->nik_security : $d->nik }}</td>
                    <td>{{ $d->nama_karyawan }}</td>
                    <td>{{ $d->no_rekening }}</td>
                    <td align="center">{{ $d->nama_group }}</td>
                    <td align="center">{{ date("d-m-Y",strtotime($d->tgl_masuk)) }}</td>
                    <td align="center">
                        @php
                        $awal = date_create($d->tgl_masuk);
                        $akhir = date_create($sampai); // waktu sekarang
                        $diff = date_diff( $awal, $akhir );
                        // echo $diff->y . ' tahun, '.$diff->m.' bulan, '.$diff->d.' Hari'
                        echo $diff->y . ' tahun, '.$diff->m.' bulan';
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
                        {{ !empty($upah) ? rupiah($upah) : "" }}
                    </td>
                    <td align="right">
                        {{ !empty($jmlinsentif) ? rupiah($jmlinsentif) : "" }}
                    </td>
                    <td style="text-align:center; font-weight:bold">
                        {{ !empty($totaljamkerja) ? desimal($totaljamkerja) : '' }}
                    </td>
                    <td align="right">
                        {{ !empty($upah_perjam) ? desimal($upah_perjam) : "" }}
                    </td>
                    <td style="text-align: center;">{{ !empty($total_overtime_1) ? desimal($total_overtime_1) : '' }}</td>

                    <td align=" right">
                        {{ !empty($upah_ot_1) ? rupiah($upah_ot_1) : "" }}
                        <br>

                    </td>
                    <td style="text-align: center;">{{ !empty($total_overtime_2) ? desimal($total_overtime_2) : '' }}</td>
                    <td align="right">
                        {{ !empty($upah_ot_2) ? rupiah($upah_ot_2) : "" }}
                    </td>
                    <td style="text-align: center;">
                        {{-- @php
                        if($d->nama_jabatan=="SECURITY"){
                        $total_overtime_libur_1 = 7;
                        }
                        @endphp --}}
                        {{ !empty($total_overtime_libur_1) ? desimal($total_overtime_libur_1) : "" }}
                    </td>
                    <td align="right">
                        {{ !empty($upah_otl_1) ? rupiah($upah_otl_1) : "" }}
                    </td>


                    <td align="right">
                        {{ !empty($total_upah_overtime) ?rupiah($total_upah_overtime) : "" }}
                    </td>
                    <td align="center">{{ !empty($totalhariall_shift_2) ? $totalhariall_shift_2 : "" }}</td>
                    <td align="right">{{ !empty($totalpremiall_shift_2) ? rupiah($totalpremiall_shift_2) : "" }}</td>
                    <td align="center">{{ !empty($totalhariall_shift_3) ? $totalhariall_shift_3 : "" }}</td>
                    <td align="right">{{ !empty($totalpremiall_shift_3) ? rupiah($totalpremiall_shift_3) : "" }}</td>
                    <td align="right">
                        {{ !empty($bruto) ?  rupiah($bruto) : "" }}
                    </td>
                    <td align="center">{{ !empty($totalpotonganjam) ? desimal($totalpotonganjam) : "" }}</td>
                    <td align="right">
                        <?php
                            // if($cekmasakerja <= 15){
                            //     $bpjskesehatan=$d->perusahaan + $d->pekerja + $d->keluarga;
                            // }else{
                            //     $bpjskesehatan = 0.5 * ($d->perusahaan + $d->pekerja) + $d->keluarga;;
                            // }

                            //$bpjskesehatan = round($bpjskesehatan,1,PHP_ROUND_HALF_UP);

                        ?>

                        {{ !empty($bpjskesehatan) ? rupiah($bpjskesehatan) : "" }}

                    </td>
                    <td></td>
                    <td align="right">
                        @php
                        //$bpjstenagakerja = $d->k_jht + $d->k_jp;

                        @endphp
                        {{ !empty($bpjstenagakerja) ? rupiah($bpjstenagakerja) : "" }}
                    </td>
                    <td align="right">{{ !empty($totaldenda) ? rupiah($totaldenda) : "" }}</td>

                    <td align="right">{{ !empty($d->cicilan_pjp) ? rupiah($d->cicilan_pjp) : "" }}</td>
                    <td align="right">{{ !empty($d->jml_kasbon) ? rupiah($d->jml_kasbon) : "" }}</td>
                    <td align="right">{{ !empty($d->jml_nonpjp) ? rupiah($d->jml_nonpjp) : "" }}</td>
                    <td align="right">
                        {{ !empty($spip) ? rupiah($spip) : "" }}

                    </td>
                    <td align="right">
                        {{ !empty($potongan) ? desimal($potongan) : "" }}
                    </td>
                    <td align="right">
                        {{ !empty($jmlbersih) ? rupiah($jmlbersih) : "" }}
                        {{-- {{ $jmlbersih }} --}}
                    </td>
                </tr>
                @endforeach
                <tr bgcolor="#024a75" style="color:white; font-size:12;">
                    <th colspan="14">TOTAL</th>
                    <th style="text-align: right">{{ rupiah($total_gajipokok) }}</th>
                    <th style="text-align: right">{{ rupiah($total_tunjangan_jabatan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_tunjangan_masakerja) }}</th>
                    <th style="text-align: right">{{ rupiah($total_tunjangan_tanggungjawab) }}</th>
                    <th style="text-align: right">{{ rupiah($total_tunjangan_makan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_tunjangan_istri) }}</th>
                    <th style="text-align: right">{{ rupiah($total_tunjangan_skillkhusus) }}</th>

                    <th style="text-align: right">{{ rupiah($total_insentif_masakerja) }}</th>
                    <th style="text-align: right">{{ rupiah($total_insentif_lembur) }}</th>
                    <th style="text-align: right">{{ rupiah($total_insentif_penempatan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_insentif_kpi) }}</th>

                    <th style="text-align: right">{{ rupiah($total_im_ruanglingkup) }}</th>
                    <th style="text-align: right">{{ rupiah($total_im_penempatan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_im_kinerja) }}</th>

                    <th style="text-align: right">{{ rupiah($total_upah) }}</th>
                    <th style="text-align: right">{{ rupiah($total_insentif) }}</th>

                    <th style="text-align: center">{{ desimal($total_all_jamkerja) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_upahperjam) }}</th>

                    <th style="text-align: right">{{ rupiah($total_all_overtime_1) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_upah_ot_1) }}</th>

                    <th style="text-align: right">{{ rupiah($total_all_overtime_2) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_upah_ot_2) }}</th>

                    <th style="text-align: right">{{ rupiah($total_all_overtime_libur) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_upah_overtime_libur) }}</th>

                    <th style="text-align: right">{{ rupiah($total_all_upah_overtime) }}</th>

                    <th style="text-align: right">{{ rupiah($total_all_hari_shift_2) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_premi_shift_2) }}</th>

                    <th style="text-align: right">{{ rupiah($total_all_hari_shift_3) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_premi_shift_3) }}</th>

                    <th style="text-align: right">{{ rupiah($total_all_bruto) }}</th>

                    <th style="text-align: center">{{ desimal($total_all_potongan_jam) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_bpjskesehatan) }}</th>
                    <th></th>
                    <th style="text-align: right">{{ rupiah($total_all_bpjstk) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_denda) }}</th>

                    <th style="text-align: right">{{ rupiah($total_all_pjp) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_kasbon) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_nonpjp) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_spip) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_potongan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_bersih) }}</th>
                </tr>
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
            , 'shadow': true
        , });
    });

</script>
</html>
