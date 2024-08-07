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
        <table class="datatable3" style="width: 350%">
            <thead bgcolor="#024a75" style="color:white; font-size:12;">
                <tr bgcolor="#024a75" style="color:white; font-size:12;">
                    <th rowspan="2" class="fixed-side" style="width:1%">No</th>
                    <th rowspan="2" class="fixed-side" style="width:1%">Nik</th>
                    <th rowspan="2" class="fixed-side" style="width:3%">Nama karyawan</th>
                    <th rowspan="2" class="fixed-side" style="width:1%">Kantor</th>
                    <th colspan="{{ $jmlrange }}">Bulan {{ $namabulan[$bulan*1]}} {{ $tahun }}</th>
                    <th rowspan="2" style="width:2%">Total Jam<br> 1 Bulan</th>
                    <th rowspan="2" style="width:1%">Telat</th>
                    <th rowspan="2" style="width:1%">Dirumahkan</th>
                    <th rowspan="2" style="width:1%">Keluar</th>
                    <th rowspan="2" style="width:1%">PC</th>
                    <th rowspan="2" style="width:1%">Alfa</th>
                    <th rowspan="2" style="width:1%">Izin<br>Absen</th>
                    <th rowspan="2" style="width:2%">Sakit<br>Non SID</th>
                    <th rowspan="2" style="width:2%">Total<br> Jam Kerja</th>
                    <th rowspan="2" style="width:2%">Denda</th>
                    <th colspan="2" style="width:3%">Premi SHIFT 2</th>
                    <th colspan="2" style="width:3%">Premi SHIFT 3</th>
                    <th rowspan="2" style="width:2%">Overtime 1</th>
                    <th rowspan="2" style="width:2%">Overtime 2</th>
                    <th rowspan="2" style="width:2%">OT Libur 1</th>
                    <th rowspan="2" style="width:2%">OT Libur 2</th>
                </tr>
                <tr bgcolor="#024a75" style="color:white;">
                    @foreach ($rangetanggal as $d)
                    <th style="width:2%">{{ date("d",strtotime($d)) }}</th>
                    @endforeach
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
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $d->nik }}</td>
                    <td>{{ $d->nama_karyawan }}</td>
                    <td>{{ $d->id_kantor }}</td>
                    <?php
                    $totalterlambat = 0;
                    $totalkeluar = 0;
                    $totaldenda = 0;
                    $totalpremi = 0;
                    $totaldirumahkan = 0;
                    $totaltidakhadir = 0;
                    $totalpulangcepat = 0;
                    $totalizinabsen = 0;
                    $total_overtime_1 = 0;
                    $total_overtime_2 = 0;
                    $total_overtime_libur_1 = 0;
                    $total_overtime_libur_2 = 0;
                    $totalpremi_shift_2 = 0;
                    $totalpremi_shift_3 = 0;
                    $totalhari_shift_2 = 0;
                    $totalhari_shift_3 = 0;
                    //$izinsakit = 0;
                    $totalizinsakit = 0;
                    for ($i = 0; $i < count($rangetanggal); $i++) {
                        $hari_ke = "hari_" . $i + 1;
                        $tgl_presensi =  $rangetanggal[$i];

                        // Menghitung Masa Kerja
                        $start_kerja = date_create($d->tgl_masuk);
                        $end_kerja = date_create($tgl_presensi);
                        $cekmasakerja =  diffInMonths($start_kerja, $end_kerja);

                        $tgllibur = "'" . $tgl_presensi . "'";

                        $search_items = array(
                            'nik' => $d->nik,
                            'id_kantor' => $d->id_kantor,
                            'tanggal_libur' => $tgl_presensi
                        );
                        $search_items_lembur = array(
                            'nik' => $d->nik,
                            'id_kantor' => $d->id_kantor,
                            'tanggal_lembur' => $tgl_presensi
                        );
                        $search_items_minggumasuk = array(
                            'nik' => $d->nik,
                            'id_kantor' => $d->id_kantor,
                            'tanggal_diganti' => $tgl_presensi
                        );
                        $search_items_all = array(
                            'nik' => 'ALL',
                            'id_kantor' => $d->id_kantor,
                            'tanggal_libur' => $tgl_presensi
                        );


                        $ceklibur = cektgllibur($datalibur, $search_items);
                        $cekliburpenggantiminggu = cektgllibur($dataliburpenggantiminggu, $search_items);
                        $cekminggumasuk = cektgllibur($dataminggumasuk, $search_items_minggumasuk);
                        $cekwfh = cektgllibur($datawfh, $search_items);
                        $cekwfhfull = cektgllibur($datawfhfull, $search_items);
                        $ceklembur = cektgllibur($datalembur, $search_items_lembur);

                        //Menghitung Jumlah Jam Dirumahkan
                        $namahari = hari($tgl_presensi);
                        if ($namahari == "Sabtu") {
                            $jamdirumahkan = 5;
                        } else {
                            $jamdirumahkan = 7;
                        }

                        //---1---
                        if (!empty($cekwfh)) {
                            if ($cekmasakerja > 3) {
                                $totaljamdirumahkan = ROUND(($jamdirumahkan / 2), 2);
                            } else {
                                $totaljamdirumahkan = $jamdirumahkan;
                            }
                            $totaldirumahkan += $totaljamdirumahkan;
                        }



                        //Pewarnaan Kolom
                        if ($namahari == "Minggu") { // Jika Hari Minggu
                            if (!empty($cekminggumasuk)) { // Cek Jika Minggu Harus Masuk
                                if ($d->$hari_ke != NULL) { // Cek Jika Melakukan Presensi
                                    $colorcolumn = ""; // Warna Kolom Putih
                                    $colortext = "";
                                } else {
                                    $colorcolumn = "red"; // Warna Kolom Merah Jika TIdak Absen
                                    $colortext = "";
                                }
                            } else { // Jika Minggu Libur atau Tidak Ada Jadwal Masuk
                                $colorcolumn = "#ffaf03"; // Warna Kolom Orange
                                $colortext = "white";
                            }
                        } else { // Jika Hari Bukan Hari Minggu
                            if ($d->$hari_ke != NULL) { // Jika Karyawa Melakukan Presensi
                                if (!empty($ceklibur)) { // Jika Pada Hari Itu ada Libur Nasional
                                    $colorcolumn = "rgb(4, 163, 65)";
                                    $colortext = "white";
                                } else {
                                    $colorcolumn = "";
                                    $colortext = "";
                                }

                                if (!empty($cekliburpenggantiminggu)) { // Jika Pada Hari Itu adalah Libur Pengganti Minggu
                                    $colorcolumn = "#ffaf03";
                                    $colortext = "white";
                                } else {
                                    $colorcolumn = $colorcolumn;
                                    $colortext = $colortext;
                                }

                                if (!empty($cekwfh)) { // Jika pada Hari Itu Sedang Dirumahkan
                                    $colorcolumn = "#fc0380";
                                    $colortext = "black";
                                } else {
                                    $colorcolumn = $colorcolumn;
                                    $colortext = $colortext;
                                }

                                if (!empty($cekwfhfull)) { // Jika pada Hari Itu Sedang WFH
                                    $colorcolumn = "#9f0ecf";
                                    $colortext = "black";
                                } else {
                                    $colorcolumn = $colorcolumn;
                                    $colortext = $colortext;
                                }
                            } else { // Jika Karyawan Tidak Melakukan Presensi

                                if (!empty($ceklibur)) { // Jika Pada Hari Itu ada Libur Nasional
                                    $colorcolumn = "rgb(4, 163, 65)";
                                    $colortext = "white";
                                } else {
                                    $colorcolumn = "red";
                                    $colortext = "white";
                                }


                                if (!empty($cekliburpenggantiminggu)) { // Jika Pada Hari Itu adalah Libur Pengganti Minggu
                                    $colorcolumn = "#ffaf03";
                                    $colortext = "white";
                                } else {
                                    if (empty($ceklibur)) {
                                        $colorcolumn = $colorcolumn;
                                        $colortext = $colortext;
                                    }
                                }

                                if (!empty($cekwfh)) { // Jika pada Hari Itu Sedang Dirumahkan
                                    $colorcolumn = "#fc0380";
                                    $colortext = "black";
                                } else {
                                    $colorcolumn = $colorcolumn;
                                    $colortext = $colortext;
                                }


                                if (!empty($cekwfhfull)) { // Jika pada Hari Itu Sedang WFH
                                    $colorcolumn = "#9f0ecf";
                                    $colortext = "black";
                                } else {
                                    $colorcolumn = $colorcolumn;
                                    $colortext = $colortext;
                                }
                            }
                        }
                        if ($d->$hari_ke != NULL) {

                            $tidakhadir = 0; // Jika Karyawan Absen Maka $tidakhadir dihitung 0

                            $datapresensi = explode("|", $d->$hari_ke); // Split Data Presensi

                            $lintashari = $datapresensi[16] != "NA" ? $datapresensi[16] : ''; // Lintas Hari
                            $izinpulangdirut = $datapresensi[17] != "NA" ? $datapresensi[17] : ''; //Izin Pulang Persetujuan Dirut
                            $izinabsendirut = $datapresensi[18] != "NA" ? $datapresensi[18] : ''; // Izin Absen Persetujuan Dirut

                            if (!empty($lintashari)) { // Jika Jadwal Presesni Lintas Hari
                                $tgl_pulang = date('Y-m-d', strtotime('+1 day', strtotime($tgl_presensi)));
                                // Tanggal Pulang adalah Tanggal Berikutnya
                            } else {
                                $tgl_pulang = $tgl_presensi; // Tanggal Pulang adalah Tanggal Presensi
                            }

                            //Jam Masuk Presensi
                            $jam_in = $datapresensi[0]; // Y-m-d H:i:s Jam dan Tanggal Karyawan Melakukan Presensi Masuk
                            $jam_in_presensi = $jam_in != "NA" ? date("H:i", strtotime($jam_in)) : ""; // Jam Presensi Masuk
                            $jam_in_tanggal = $jam_in != "NA" ?  date("Y-m-d H:i", strtotime($jam_in)) : ""; // Jam Tgl Masuk Presensi

                            //Jam Pulang Presensi
                            $jam_out = $datapresensi[1]; // Y-m-d H:i:s Jam dan Tangal Karyawan Melakukan Presensi Pulang
                            $jam_out_presensi = $jam_out != "NA" ? date("H:i", strtotime($jam_out)) : ''; //Jam Presensi Pulang
                            $jam_out_tanggal = $jam_out != "NA" ? date("Y-m-d H:i", strtotime($jam_out)) : ''; //Jam Tgl Presensi Pulang


                            $nama_jadwal = $datapresensi[2];
                            if ($d->nama_jabatan == "SPG") {
                                $jam_masuk = $jam_in_presensi;
                                $jam_masuk_tanggal = $tgl_presensi . " " . $jam_masuk;
                            } else {
                                $jam_masuk = date("H:i", strtotime($datapresensi[3]));
                                $jam_masuk_tanggal = $tgl_presensi . " " . $jam_masuk;
                            }



                            if ($d->nama_jabatan == "SPG") {
                                $jam_pulang = !empty($jam_out_presensi) ? $jam_out_presensi : "NA";
                                $jam_pulang_tanggal = !empty($jam_out_presensi) ? $tgl_pulang . " " . $jam_pulang : "NA";
                            } else {
                                $jam_pulang = !empty($datapresensi[4]) ? date("H:i", strtotime($datapresensi[4])) : "NA";
                                $jam_pulang_tanggal = !empty($datapresensi[4]) ? $tgl_pulang . " " . $jam_pulang : "NA";
                            }


                            //$jam_pulang_presensi =$jam_pulang != "NA" ? date("H:i",strtotime($jam_pulang)) : '';

                            $jam_keluar = $datapresensi[9] != "NA" ? date("H:i", strtotime($datapresensi[9])) : '';
                            $jam_masuk_kk = $datapresensi[10] != "NA" ? $datapresensi[10] : '';
                            $total_jam = $datapresensi[11] != "NA" ? $datapresensi[11] : 0;
                            $status = $datapresensi[5] != "NA" ? $datapresensi[5] : '';
                            $sid = $datapresensi[12] != "NA" ? $datapresensi[12] : '';
                            $kode_izin_terlambat = $datapresensi[7] != "NA" ? $datapresensi[7] : '';
                            $kode_izin_pulang = $datapresensi[8] != "NA" ? $datapresensi[8] : '';
                            $jam_istirahat = $datapresensi[14];
                            $jam_istirahat_presensi = $jam_istirahat != "NA" ? date("H:i", strtotime($jam_istirahat)) : '';
                            $jam_awal_istirahat = $datapresensi[13] != "NA" ? $tgl_presensi . " " . $datapresensi[13] : '';
                            $jam_akhir_istirahat = $datapresensi[14] != "NA" ? $tgl_presensi . " " . $datapresensi[14] : '';
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
                                    $menitterlambat = floor(($diffterlambat - ($jamterlambat * (60 * 60))) / 60);
                                    //Tambah 0 Jika Jam < dari 10
                                    $jterlambat = $jamterlambat <= 9 ? "0" . $jamterlambat : $jamterlambat;
                                    //Tambah 0 Jika Menit Kurang Dari 10
                                    $mterlambat = $menitterlambat <= 9 ? "0" . $menitterlambat : $menitterlambat;

                                    //Total Keterlambatan Dalam Jam dan Menit
                                    $terlambat = $jterlambat . ":" . $mterlambat;
                                    //Keterlambatan Menit Dalam Desimal
                                    $desimalterlambat = ROUND(($menitterlambat / 60), 2);
                                    $colorterlambat = "red";
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
                                $colorterlambat = "";
                            }

                            //Perhitungan Jam Keluar
                            if (!empty($jam_keluar)) {
                                $jam_keluar_presensi = $tgl_presensi . " " . $jam_keluar;
                                if (!empty($jam_masuk_kk)) {
                                    $jam_masuk_kk_presensi = $tgl_presensi . " " . $jam_masuk_kk;
                                } else {
                                    $jam_masuk_kk_presensi = $tgl_presensi . " " . $jam_pulang;
                                }

                                $jk1 = strtotime($jam_keluar_presensi);
                                $jk2 = strtotime($jam_masuk_kk_presensi);
                                $difkeluarkantor = $jk2 - $jk1;

                                //Total Jam Keluar Kantor
                                $jamkeluarkantor = floor($difkeluarkantor / (60 * 60));
                                //Total Menit Keluar Kantor
                                $menitkeluarkantor = floor(($difkeluarkantor - ($jamkeluarkantor * (60 * 60))) / 60);

                                //Tambah 0 di Depan Jika < 10
                                $jkeluarkantor = $jamkeluarkantor <= 9 ? "0" . $jamkeluarkantor : $jamkeluarkantor;
                                $mkeluarkantor = $menitkeluarkantor <= 9 ? "0" . $menitkeluarkantor : $menitkeluarkantor;

                                if (empty($jam_masuk_kk)) {
                                    if ($total_jam == 7) {
                                        $totaljamkeluar = ($jkeluarkantor - 1) . ":" . $mkeluarkantor;
                                        $desimaljamkeluar = ROUND(($menitkeluarkantor / 60), 2) - 1;
                                    } else {
                                        $totaljamkeluar = $jkeluarkantor . ":" . $mkeluarkantor;
                                        $desimaljamkeluar = ROUND(($menitkeluarkantor / 60), 2);
                                    }
                                } else {
                                    $totaljamkeluar = $jkeluarkantor . ":" . $mkeluarkantor;
                                    $desimaljamkeluar = ROUND(($menitkeluarkantor / 60), 2);
                                }
                            } else {
                                $totaljamkeluar = "";
                                $desimaljamkeluar = 0;
                                $jamkeluarkantor = 0;
                            }




                            //Tambah 0 Didepan Jika < 10 pada Jam Terlambat
                            $jamterlambat = $jamterlambat < 0 && !empty($kode_izin_terlambat) ? 0 : $jamterlambat;

                            //Jam terlambat dalam Desimal

                            $jt = $jamterlambat + $desimalterlambat;
                            if ($jamkeluarkantor > 0) {
                                $jk = $jamkeluarkantor + $desimaljamkeluar;
                            } else {
                                $jk = 0;
                            }

                            $jt = !empty($jt) ? $jt : 0;
                            //echo $jamterlambat."|<br>";
                            //echo $menitterlambat."|";
                            // menghitung Denda
                            if (!empty($jam_in) and $kode_dept != 'MKT') {
                                if ($jam_in_presensi > $jam_masuk and empty($kode_izin_terlambat)) {

                                    if ($jamterlambat < 1) {
                                        if ($menitterlambat >= 5 and $menitterlambat < 10) {
                                            $denda = 5000;
                                            //echo "test5000|";
                                        } else if ($menitterlambat >= 10 and $menitterlambat < 15) {
                                            $denda = 10000;
                                            //echo "test10ribu|";
                                        } else if ($menitterlambat >= 15 and $menitterlambat <= 59) {
                                            $denda = 15000;
                                            //echo "Test15ribu|";
                                        } else {
                                            $denda = 0;
                                        }
                                    } else {
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
                            if ($d->jam_out > $jam_awal_istirahat && $d->jam_out < $jam_akhir_istirahat) { // Shift 3 Belum Di Set
                                $jout = $jam_awal_istirahat;
                            } else {
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
                                if ($kode_dept != "MKT") {
                                    if ($jamterlambat < 1) {
                                        $jt = 0;
                                    } else {
                                        $jt = $jt;
                                    }
                                } else {
                                    if ($jamterlambat < 1) {
                                        $jt = 0;
                                    } else {
                                        $jt = $jt;
                                    }
                                }
                            } else {
                                if ($jamterlambat < 1) {
                                    $jt = 0;
                                } else {
                                    $jt = $jt;
                                }
                            }
                            $totaljam = $total_jam - $jt - $jk;


                            if ($jam_out != "NA") {
                                if ($jam_out < $jam_pulang_tanggal) { //Shift 3 Belum Di Set | Coba
                                    if ($jam_out > $jam_akhir_istirahat && $jam_istirahat != "NA") {
                                        $desimalmenit = ROUND(($menit * 100) / 60);
                                        $grandtotaljam = $jam - 1 . "." . $desimalmenit;
                                    } else {
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
                                if ($status == "i") {
                                    $grandtotaljam = 0;
                                } else if ($status == "s") {
                                    if (!empty($sid)) {
                                        $grandtotaljam = 7;
                                    } else {
                                        $grandtotaljam = 0;
                                    }
                                } else if ($status == "c") {
                                    $grandtotaljam = 7;
                                } else {
                                    $grandtotaljam = 0;
                                }
                            }


                            if ($nama_jadwal == "SHIFT 2" && $grandtotaljam > 5) {
                                $premi = 5000;
                                $premi_shift_2 = 5000;
                                $totalpremi_shift_2 += $premi_shift_2;
                                $totalhari_shift_2 += 1;
                            } else if ($nama_jadwal == "SHIFT 3" && $grandtotaljam > 5) {
                                $premi = 6000;
                                $premi_shift_3 = 6000;
                                $totalpremi_shift_3 += $premi_shift_3;
                                $totalhari_shift_3 += 1;
                            } else {
                                $premi = 0;
                            }



                            if ($jam_out != "NA" && $jam_out_tanggal < $jam_pulang_tanggal) {
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

                                if (!empty($izinpulangdirut)) {
                                    $totalpc = 0;
                                } else {
                                    $totalpc = $total_jam + $jk - $grandtotaljam;
                                }
                            } else {
                                $pc = "";
                                $totalpc = 0;
                            }
                            // echo "Total Jam :" .$total_jam."<br>" ;
                            // echo "Jam Terlambat :".$jt."<br>";
                            // echo "___________________________- <br>";

                    ?>
                            <td style="background-color: {{ $colorcolumn }}; color:{{ $colortext }};">
                                {{-- {{ $cekmasakerja }} --}}
                                @if ($status == "h")
                                @php
                                $izinabsen = 0;
                                $izinsakit = 0;
                                @endphp

                                {{-- {{ var_dump($ceklibur); }} --}}
                                {{-- {{ $kode_izin_pulang }} {{ $izinpulangdirut }} --}}
                                {{-- {{ $totalpc }} --}}
                                {{-- <span>{{ var_dump($ceklibur) }}</span> --}}
                                {{-- <span>{{ $desimalterlambat }}</span> --}}
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
                                @if (!empty($ceklembur))
                                <?php
                                $tgl_lembur_dari = $ceklembur[0]["tanggal_dari"];
                                $tgl_lembur_sampai = $ceklembur[0]["tanggal_sampai"];
                                $jmljam_lembur = hitungjamdesimal($tgl_lembur_dari, $tgl_lembur_sampai);

                                $kategori_lembur = $ceklembur[0]["kategori"];

                                if ($kategori_lembur == 1) {
                                    $overtime_1 = $jmljam_lembur > 1 ? 1 : $jmljam_lembur;
                                    $overtime_2 = $jmljam_lembur > 1 ? $jmljam_lembur - 1 : 0;
                                    $total_overtime_1 += $overtime_1;
                                    $total_overtime_2 += $overtime_2;
                                ?>
                                    <span style="color:rgb(255, 255, 255)">OT 1 : {{ $overtime_1 }}</span>
                                    <br>
                                    <span style="color:rgb(255, 255, 255)">OT 2 : {{ $overtime_2 }}</span>
                                <?php
                                } else if ($kategori_lembur == 2) {
                                    $overtime_libur_1 = $jmljam_lembur >= 4 ? 4 : $jmljam_lembur;
                                    $overtime_libur_2 = $jmljam_lembur > 4 ? $jmljam_lembur - 4 : 0;
                                    $total_overtime_libur_1 += $overtime_libur_1;
                                    $total_overtime_libur_2 += $overtime_libur_2;
                                ?>
                                    <span style="color:rgb(255, 255, 255)">OTL 1 : {{ $total_overtime_libur_1 }}</span>
                                    <br>
                                    <span style="color:rgb(255, 255, 255)">OTL 2 : {{ $total_overtime_libur_2 }}</span>
                                <?php
                                }

                                ?>

                                @endif
                                @elseif($status=="s")
                                <span style="color:rgb(195, 63, 27)">SAKIT
                                    @if (!empty($sid))
                                    <span style="color:green">- SID</span><br>
                                    <span style="color:blue">Total Jam : {{ $grandtotaljam }}</span>
                                    @else
                                    <br>
                                    <?php
                                    if ($namahari == "Sabtu") {
                                        $izinsakit = 5;
                                    } elseif ($namahari == "Minggu") {
                                        if (!empty($cekminggumasuk)) {
                                            $izinsakit = 7;
                                        } else {
                                            $izinsakit = 0;
                                        }
                                    } else {
                                        $izinsakit = 7;
                                    }
                                    ?>
                                    <span style="color:blue">Total Jam : {{ $grandtotaljam }}</span>
                                    @endif
                                    @php
                                    $izinabsen = 0;

                                    @endphp
                                </span>
                                @elseif($status=="i")
                                <span style="color:rgb(27, 5, 171);">IZIN</span><br>
                                <span style="color:blue">Total Jam : {{ $grandtotaljam }}</span>

                                <?php
                                if (empty($izinabsendirut)) {
                                    if ($namahari == "Sabtu") {
                                        $izinabsen = 5;
                                    } elseif ($namahari == "Minggu") {
                                        if (!empty($cekminggumasuk)) {
                                            $izinabsen = 7;
                                        } else {
                                            $izinabsen = 0;
                                        }
                                    } else {
                                        $izinabsen = 7;
                                    }
                                } else {
                                    $izinabsen = 0;
                                }

                                $izinsakit = 0;

                                ?>
                                @elseif($status=="c")
                                <span style="color:rgb(154, 56, 4);">CUTI</span><br>
                                <span style="color:blue">Total Jam : {{ $grandtotaljam }}</span>
                                @php
                                $izinabsen = 0;
                                $izinsakit = 0;
                                @endphp
                                @endif

                                @if (!empty($premi))
                                <br>
                                <span style="color: blue">Premi : {{ rupiah($premi) }}</span>
                                @endif
                            </td>
                        <?php
                        } else {
                            $jt = 0;
                            $jk = 0;
                            $denda = 0;
                            $premi = 0;
                            $totalpc = 0;
                            $izinabsen = 0;
                            $izinsakit = 0;
                            if (!empty($ceklibur) && $cekmasakerja > 3 || !empty($cekliburpenggantiminggu) && $cekmasakerja > 3 || !empty($cekwfh) || !empty($cekwfhfull) && $cekmasakerja > 3) {
                                $tidakhadir = 0;
                            } else {
                                if ($namahari == "Sabtu") {
                                    $tidakhadir = 5;
                                } elseif ($namahari == "Minggu") {
                                    if (!empty($cekminggumasuk)) {
                                        $tidakhadir = 7;
                                    } else {
                                        $tidakhadir = 0;
                                    }
                                } else {
                                    $tidakhadir = 7;
                                }
                            }


                        ?>
                            <td style="background-color:{{ $colorcolumn }}; color:white;">
                                {{-- <span>{{ var_dump(empty($ceklibur)) }}</span> --}}
                                {{-- {{ $cekmasakerja }} --}}
                                {{-- {{ var_dump($ceklembur); }} --}}
                                {{ !empty($ceklibur) ? $ceklibur[0]["keterangan"] : "" }}
                                {{ !empty($cekwfh) ? "Dirumahkan" : "" }}
                                {{ !empty($cekwfhfull) ? "WFH" : "" }}
                                {{ !empty($cekliburpenggantiminggu) ? $cekliburpenggantiminggu[0]["keterangan"] : "" }}
                                @if (!empty($ceklembur))
                                <?php
                                $tgl_lembur_dari = $ceklembur[0]["tanggal_dari"];
                                $tgl_lembur_sampai = $ceklembur[0]["tanggal_sampai"];
                                $jmljam_lembur = hitungjamdesimal($tgl_lembur_dari, $tgl_lembur_sampai);
                                $kategori_lembur = $ceklembur[0]["kategori"];
                                if ($kategori_lembur == 1) {
                                    $overtime_1 = $jmljam_lembur > 1 ? 1 : $jmljam_lembur;
                                    $overtime_2 = $jmljam_lembur > 1 ? $jmljam_lembur - 1 : 0;
                                    $total_overtime_1 += $overtime_1;
                                    $total_overtime_2 += $overtime_2;
                                ?>
                                    <span style="color:rgb(255, 255, 255)">OT 1 : {{ $overtime_1 }}</span>
                                    <br>
                                    <span style="color:rgb(255, 255, 255)">OT 2 : {{ $overtime_2 }}</span>
                                <?php
                                } else if ($kategori_lembur == 2) {
                                    $overtime_libur_1 = $jmljam_lembur >= 4 ? 4 : $jmljam_lembur;
                                    $overtime_libur_2 = $jmljam_lembur > 4 ? $jmljam_lembur - 4 : 0;
                                    $total_overtime_libur_1 += $overtime_libur_1;
                                    $total_overtime_libur_2 += $overtime_libur_2;
                                ?>
                                    <span style="color:rgb(255, 255, 255)">OTL 1 : {{ $total_overtime_libur_1 }}</span>
                                    <br>
                                    <span style="color:rgb(255, 255, 255)">OTL 2 : {{ $total_overtime_libur_2 }}</span>
                                <?php
                                }
                                ?>

                                @endif
                            </td>
                    <?Php
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
                    <td style="font-size: 16px; text-align:center; font-weight:bold">{{ $totaljam1bulan }}</td>
                    <td style="text-align: center; color:red; font-size:16px">{{ !empty($totalterlambat) ? $totalterlambat : '' }}</td>
                    <td style="text-align: center; color:rgb(255, 140, 0);font-size:16px">{{ !empty($totaldirumahkan) ? $totaldirumahkan : '' }}</td>
                    <td style="text-align: center; color:rgb(255, 140, 0);font-size:16px">{{ !empty($totalkeluar) ? $totalkeluar : '' }}</td>
                    <td style="text-align: center; color:rgb(255, 140, 0);font-size:16px">{{ !empty($totalpulangcepat) ? $totalpulangcepat : '' }}</td>
                    <td style="text-align: center; color:rgb(255, 140, 0);font-size:16px">{{ !empty($totaltidakhadir) ? $totaltidakhadir : '' }}</td>
                    <td style="text-align: center; color:rgb(255, 140, 0);font-size:16px">{{ !empty($totalizinabsen) ? $totalizinabsen : '' }}</td>
                    <td style="text-align: center; color:rgb(255, 140, 0);font-size:16px">{{ !empty($totalizinsakit) ? $totalizinsakit : '' }}</td>
                    <td style="font-size: 16px; text-align:center; font-weight:bold">{{ !empty($totaljamkerja) ? $totaljamkerja : '' }}</td>
                    <td style="text-align: right; color:red; font-size:16px">{{ !empty($totaldenda) ? rupiah($totaldenda) : '' }}</td>
                    <td style="text-align: center;  font-size:16px">{{ !empty($totalhari_shift_2) ? rupiah($totalhari_shift_2) : '' }}</td>
                    <td style="text-align: right;  font-size:16px">{{ !empty($totalpremi_shift_2) ? rupiah($totalpremi_shift_2) : '' }}</td>
                    <td style="text-align: center;  font-size:16px">{{ !empty($totalhari_shift_3) ? rupiah($totalhari_shift_3) : '' }}</td>
                    <td style="text-align: right;  font-size:16px">{{ !empty($totalpremi_shift_3) ? rupiah($totalpremi_shift_3) : '' }}</td>
                    <td style="text-align: center;  font-size:16px">{{ !empty($total_overtime_1) ? rupiah($total_overtime_1) : '' }}</td>
                    <td style="text-align: center;  font-size:16px">{{ !empty($total_overtime_2) ? rupiah($total_overtime_2) : '' }}</td>
                    <td style="text-align: center;  font-size:16px">{{ !empty($total_overtime_libur_1) ? rupiah($total_overtime_libur_1) : '' }}</td>
                    <td style="text-align: center;  font-size:16px">{{ !empty($total_overtime_libur_2) ? rupiah($total_overtime_libur_2) : '' }}</td>

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
            'scrollable': true,
            'columnNum': 4
        });
    });
</script>

</html>
