<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Penjualan {{ date('d-m-y') }}</title>
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
            height: 300px;
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
                    <th rowspan="2">KLASIFIKASI</th>
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
                    <th rowspan="2" style="background-color: black;">SPIP</th>
                    <th rowspan="2" style="background-color: orange;">JUMLAH<br>POTONGAN</th>
                    <th rowspan="2" style="background-color: orange;">JUMLAH<br>BERSIH</th>

                </tr>
                <tr>
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

                    //Gaji Pokok
                    $total_gajipokok = 0;
                    $total_gajipokok_administrasi = 0;
                    $total_gajipokok_penjualan = 0;
                    $total_gajipokok_tkl = 0;
                    $total_gajipokok_tktl = 0;
                    $total_gajipokok_mp = 0;
                    $total_gajipokok_pcf = 0;

                    //Tunjangan Jabatan
                    $total_tunjangan_jabatan = 0;
                    $total_t_jabatan_administrasi = 0;
                    $total_t_jabatan_penjualan = 0;
                    $total_t_jabatan_tkl = 0;
                    $total_t_jabatan_tktl = 0;
                    $total_t_jabatan_mp = 0;
                    $total_t_jabatan_pcf = 0;

                    //Tunjangan Masa Kerja
                    $total_tunjangan_masakerja = 0;
                    $total_t_masakerja_administrasi = 0;
                    $total_t_masakerja_penjualan = 0;
                    $total_t_masakerja_tkl = 0;
                    $total_t_masakerja_tktl = 0;
                    $total_t_masakerja_mp = 0;
                    $total_t_masakerja_pcf = 0;

                    //Tunjangan Tanggung Jawab
                    $total_tunjangan_tanggungjawab = 0;
                    $total_t_tanggungjawab_administrasi = 0;
                    $total_t_tanggungjawab_penjualan = 0;
                    $total_t_tanggungjawab_tkl = 0;
                    $total_t_tanggungjawab_tktl = 0;
                    $total_t_tanggungjawab_mp = 0;
                    $total_t_tanggungjawab_pcf = 0;

                    //Tunjangan Makan
                    $total_tunjangan_makan = 0;
                    $total_t_makan_administrasi = 0;
                    $total_t_makan_penjualan = 0;
                    $total_t_makan_tkl = 0;
                    $total_t_makan_tktl = 0;
                    $total_t_makan_mp = 0;
                    $total_t_makan_pcf = 0;

                    //TUnjangan Istri
                    $total_tunjangan_istri = 0;
                    $total_t_istri_administrasi = 0;
                    $total_t_istri_penjualan = 0;
                    $total_t_istri_tkl = 0;
                    $total_t_istri_tktl = 0;
                    $total_t_istri_mp = 0;
                    $total_t_istri_pcf = 0;

                    //Tunjangan Skill Khusus
                    $total_tunjangan_skillkhusus = 0;
                    $total_t_skillkhusus_administrasi = 0;
                    $total_t_skillkhusus_penjualan = 0;
                    $total_t_skillkhusus_tkl = 0;
                    $total_t_skillkhusus_tktl = 0;
                    $total_t_skillkhusus_mp = 0;
                    $total_t_skillkhusus_pcf = 0;

                    //Insentif umum Masa Kjra
                    $total_insentif_masakerja = 0;
                    $total_i_masakerja_administrasi = 0;
                    $total_i_masakerja_penjualan = 0;
                    $total_i_masakerja_tkl = 0;
                    $total_i_masakerja_tktl = 0;
                    $total_i_masakerja_mp = 0;
                    $total_i_masakerja_pcf = 0;

                    //Insentif Lembur
                    $total_insentif_lembur = 0;
                    $total_i_lembur_administrasi = 0;
                    $total_i_lembur_penjualan = 0;
                    $total_i_lembur_tkl = 0;
                    $total_i_lembur_tktl = 0;
                    $total_i_lembur_mp = 0;
                    $total_i_lembur_pcf = 0;

                    $total_insentif_penempatan = 0;
                    $total_i_penempatan_administrasi = 0;
                    $total_i_penempatan_penjualan = 0;
                    $total_i_penempatan_tkl = 0;
                    $total_i_penempatan_tktl = 0;
                    $total_i_penempatan_mp = 0;
                    $total_i_penempatan_pcf = 0;

                    //Insentif KPI
                    $total_insentif_kpi = 0;
                    $total_i_kpi_administrasi = 0;
                    $total_i_kpi_penjualan = 0;
                    $total_i_kpi_tkl = 0;
                    $total_i_kpi_tktl = 0;
                    $total_i_kpi_mp = 0;
                    $total_i_kpi_pcf = 0;

                    //Insentif Ruang Lingkup Manager
                    $total_im_ruanglingkup = 0;
                    $total_im_ruanglingkup_administrasi = 0;
                    $total_im_ruanglingkup_penjualan = 0;
                    $total_im_ruanglingkup_tkl = 0;
                    $total_im_ruanglingkup_tktl = 0;
                    $total_im_ruanglingkup_mp = 0;
                    $total_im_ruanglingkup_pcf = 0;

                    $total_im_penempatan = 0;
                    $total_im_penempatan_administrasi = 0;
                    $total_im_penempatan_penjualan = 0;
                    $total_im_penempatan_tkl = 0;
                    $total_im_penempatan_tktl = 0;
                    $total_im_penempatan_mp = 0;
                    $total_im_penempatan_pcf = 0;

                    $total_im_kinerja = 0;
                    $total_im_kinerja_administrasi = 0;
                    $total_im_kinerja_penjualan = 0;
                    $total_im_kinerja_tkl = 0;
                    $total_im_kinerja_tktl = 0;
                    $total_im_kinerja_mp = 0;
                    $total_im_kinerja_pcf = 0;

                    //Upah
                    $total_upah = 0;
                    $total_upah_administrasi = 0;
                    $total_upah_penjualan = 0;
                    $total_upah_tkl = 0;
                    $total_upah_tktl = 0;
                    $total_upah_mp = 0;
                    $total_upah_pcf = 0;

                    //INSENTIF
                    $total_insentif = 0;
                    $total_insentif_administrasi = 0;
                    $total_insentif_penjualan = 0;
                    $total_insentif_tkl = 0;
                    $total_insentif_tktl = 0;
                    $total_insentif_mp = 0;
                    $total_insentif_pcf = 0;

                    $total_all_jamkerja = 0;
                    $total_jamkerja_administrasi = 0;
                    $total_jamkerja_penjualan = 0;
                    $total_jamkerja_tkl = 0;
                    $total_jamkerja_tktl = 0;
                    $total_jamkerja_mp = 0;
                    $total_jamkerja_pcf = 0;

                    $total_all_upahperjam = 0;
                    $total_upahperjam_administrasi = 0;
                    $total_upahperjam_penjualan = 0;
                    $total_upahperjam_tkl = 0;
                    $total_upahperjam_tktl = 0;
                    $total_upahperjam_mp = 0;
                    $total_upahperjam_pcf = 0;

                    $total_all_overtime_1 = 0;
                    $total_overtime_1_administrasi = 0;
                    $total_overtime_1_penjualan = 0;
                    $total_overtime_1_tkl = 0;
                    $total_overtime_1_tktl = 0;
                    $total_overtime_1_mp = 0;
                    $total_overtime_1_pcf = 0;

                    $total_all_upah_ot_1 = 0;
                    $total_upah_ot_1_administrasi = 0;
                    $total_upah_ot_1_penjualan = 0;
                    $total_upah_ot_1_tkl = 0;
                    $total_upah_ot_1_tktl = 0;
                    $total_upah_ot_1_mp = 0;
                    $total_upah_ot_1_pcf = 0;

                    //OVERTIME 2
                    $total_all_overtime_2 = 0;
                    $total_overtime_2_administrasi = 0;
                    $total_overtime_2_penjualan = 0;
                    $total_overtime_2_tkl = 0;
                    $total_overtime_2_tktl = 0;
                    $total_overtime_2_mp = 0;
                    $total_overtime_2_pcf = 0;

                    $total_all_upah_ot_2 = 0;
                    $total_upah_ot_2_administrasi = 0;
                    $total_upah_ot_2_penjualan = 0;
                    $total_upah_ot_2_tkl = 0;
                    $total_upah_ot_2_tktl = 0;
                    $total_upah_ot_2_mp = 0;
                    $total_upah_ot_2_pcf = 0;

                    $total_all_overtime_libur = 0;
                    $total_overtime_libur_administrasi = 0;
                    $total_overtime_libur_penjualan = 0;
                    $total_overtime_libur_tkl = 0;
                    $total_overtime_libur_tktl = 0;
                    $total_overtime_libur_mp = 0;
                    $total_overtime_libur_pcf = 0;

                    $total_all_upah_overtime_libur = 0;
                    $total_upah_overtime_libur_administrasi = 0;
                    $total_upah_overtime_libur_penjualan = 0;
                    $total_upah_overtime_libur_tkl = 0;
                    $total_upah_overtime_libur_tktl = 0;
                    $total_upah_overtime_libur_mp = 0;
                    $total_upah_overtime_libur_pcf = 0;

                    $total_all_upah_overtime = 0;
                    $total_all_upah_otl_administrasi = 0;
                    $total_all_upah_otl_penjualan = 0;
                    $total_all_upah_otl_tkl = 0;
                    $total_all_upah_otl_tktl = 0;
                    $total_all_upah_otl_mp = 0;
                    $total_all_upah_otl_pcf = 0;

                    $total_all_hari_shift_2 = 0;
                    $total_all_hari_shift_2_administrasi = 0;
                    $total_all_hari_shift_2_penjualan = 0;
                    $total_all_hari_shift_2_tkl = 0;
                    $total_all_hari_shift_2_tktl = 0;
                    $total_all_hari_shift_2_mp = 0;
                    $total_all_hari_shift_2_pcf = 0;

                    $total_all_premi_shift_2 = 0;
                    $total_all_premi_shift_2_administrasi = 0;
                    $total_all_premi_shift_2_penjualan = 0;
                    $total_all_premi_shift_2_tkl = 0;
                    $total_all_premi_shift_2_tktl = 0;
                    $total_all_premi_shift_2_mp = 0;
                    $total_all_premi_shift_2_pcf = 0;

                    $total_all_hari_shift_3 = 0;
                    $total_all_hari_shift_3_administrasi = 0;
                    $total_all_hari_shift_3_penjualan = 0;
                    $total_all_hari_shift_3_tkl = 0;
                    $total_all_hari_shift_3_tktl = 0;
                    $total_all_hari_shift_3_mp = 0;
                    $total_all_hari_shift_3_pcf = 0;

                    $total_all_premi_shift_3 = 0;
                    $total_all_premi_shift_3_administrasi = 0;
                    $total_all_premi_shift_3_penjualan = 0;
                    $total_all_premi_shift_3_tkl = 0;
                    $total_all_premi_shift_3_tktl = 0;
                    $total_all_premi_shift_3_mp = 0;
                    $total_all_premi_shift_3_pcf = 0;

                    $total_all_bruto = 0;
                    $total_all_bruto_administrasi = 0;
                    $total_all_bruto_penjualan = 0;
                    $total_all_bruto_tkl = 0;
                    $total_all_bruto_tktl = 0;
                    $total_all_bruto_mp = 0;
                    $total_all_bruto_pcf = 0;

                    $total_all_potongan_jam = 0;
                    $total_all_potonganjam_administrasi = 0;
                    $total_all_potonganjam_penjualan = 0;
                    $total_all_potonganjam_tkl = 0;
                    $total_all_potonganjam_tktl = 0;
                    $total_all_potonganjam_mp = 0;
                    $total_all_potonganjam_pcf = 0;

                    $total_all_bpjskesehatan = 0;
                    $total_all_bpjskesehatan_administrasi = 0;
                    $total_all_bpjskesehatan_penjualan = 0;
                    $total_all_bpjskesehatan_tkl = 0;
                    $total_all_bpjskesehatan_tktl = 0;
                    $total_all_bpjskesehatan_mp = 0;
                    $total_all_bpjskesehatan_pcf = 0;

                    $total_all_bpjstk = 0;
                    $total_all_bpjstk_administrasi = 0;
                    $total_all_bpjstk_penjualan = 0;
                    $total_all_bpjstk_tkl = 0;
                    $total_all_bpjstk_tktl = 0;
                    $total_all_bpjstk_mp = 0;
                    $total_all_bpjstk_pcf = 0;

                    $total_all_denda = 0;
                    $total_all_denda_administrasi = 0;
                    $total_all_denda_penjualan = 0;
                    $total_all_denda_tkl = 0;
                    $total_all_denda_tktl = 0;
                    $total_all_denda_mp = 0;
                    $total_all_denda_pcf = 0;

                    $total_all_pjp = 0;
                    $total_all_pjp_administrasi = 0;
                    $total_all_pjp_penjualan = 0;
                    $total_all_pjp_tkl = 0;
                    $total_all_pjp_tktl = 0;
                    $total_all_pjp_mp = 0;
                    $total_all_pjp_pcf = 0;

                    $total_all_kasbon = 0;
                    $total_all_kasbon_administrasi = 0;
                    $total_all_kasbon_penjualan = 0;
                    $total_all_kasbon_tkl = 0;
                    $total_all_kasbon_tktl = 0;
                    $total_all_kasbon_mp = 0;
                    $total_all_kasbon_pcf = 0;

                    $total_all_spip = 0;
                    $total_all_spip_administrasi = 0;
                    $total_all_spip_penjualan = 0;
                    $total_all_spip_tkl = 0;
                    $total_all_spip_tktl = 0;
                    $total_all_spip_mp = 0;
                    $total_all_spip_pcf = 0;

                    $total_all_potongan = 0;
                    $total_all_potongan_administrasi = 0;
                    $total_all_potongan_penjualan = 0;
                    $total_all_potongan_tkl = 0;
                    $total_all_potongan_tktl = 0;
                    $total_all_potongan_mp = 0;
                    $total_all_potongan_pcf = 0;

                    $total_all_bersih = 0;
                    $total_all_bersih_administrasi = 0;
                    $total_all_bersih_penjualan = 0;
                    $total_all_bersih_tkl = 0;
                    $total_all_bersih_tktl = 0;
                    $total_all_bersih_mp = 0;
                    $total_all_bersih_pcf = 0;

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
                    $total_overtime_1 = 0;
                    $total_overtime_2 = 0;
                    $total_overtime_libur_1 = 0;
                    $total_overtime_libur_2 = 0;
                    $izinsakit = 0;
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
                    for ($i = 0; $i < count($rangetanggal); $i++) {
                        $hari_ke = 'hari_' . $i + 1;
                        $tgl_presensi = $rangetanggal[$i];
                    
                        // // Menghitung Masa Kerja
                        // $start_kerja = date_create($d->tgl_masuk);
                        // $end_kerja = date_create($tgl_presensi);
                        // $cekmasakerja =  diffInMonths($start_kerja, $end_kerja);
                    
                        $start_kerja = date_create($d->tgl_masuk);
                        $end_kerja = date_create($tgl_presensi); // waktu sekarang
                        $diff = date_diff($start_kerja, $end_kerja);
                        $cekmasakerja = $diff->y * 12 + $diff->m;
                    
                        $tgllibur = "'" . $tgl_presensi . "'";
                    
                        $search_items = [
                            'nik' => $d->nik,
                            'id_kantor' => $d->id_kantor,
                            'tanggal_libur' => $tgl_presensi,
                        ];
                        $search_items_lembur = [
                            'nik' => $d->nik,
                            'id_kantor' => $d->id_kantor,
                            'tanggal_lembur' => $tgl_presensi,
                        ];
                        $search_items_minggumasuk = [
                            'nik' => $d->nik,
                            'id_kantor' => $d->id_kantor,
                            'tanggal_diganti' => $tgl_presensi,
                        ];
                        $search_items_all = [
                            'nik' => 'ALL',
                            'id_kantor' => $d->id_kantor,
                            'tanggal_libur' => $tgl_presensi,
                        ];
                    
                        $ceklibur = cektgllibur($datalibur, $search_items);
                        $cekliburpenggantiminggu = cektgllibur($dataliburpenggantiminggu, $search_items);
                        $cekminggumasuk = cektgllibur($dataminggumasuk, $search_items_minggumasuk);
                        $cekwfh = cektgllibur($datawfh, $search_items);
                        $cekwfhfull = cektgllibur($datawfhfull, $search_items);
                        $ceklembur = cektgllibur($datalembur, $search_items_lembur);
                    
                        //Menghitung Jumlah Jam Dirumahkan
                        $namahari = hari($tgl_presensi);
                        if ($namahari == 'Sabtu') {
                            $jamdirumahkan = 5;
                        } else {
                            $jamdirumahkan = 7;
                        }
                    
                        //Ceklembur
                        if (!empty($ceklembur)) {
                            $tgl_lembur_dari = $ceklembur[0]['tanggal_dari'];
                            $tgl_lembur_sampai = $ceklembur[0]['tanggal_sampai'];
                            $jamlembur_dari = date('H:i', strtotime($tgl_lembur_dari));
                            $jmljam_lbr = hitungjamdesimal($tgl_lembur_dari, $tgl_lembur_sampai);
                            $istirahatlbr = $ceklembur[0]['istirahat'] == 1 ? 1 : 0;
                            $jmljam_lembur = $jmljam_lbr > 7 ? 7 : $jmljam_lbr - $istirahatlbr;
                            $kategori_lembur = $ceklembur[0]['kategori'];
                            if (empty($ceklibur) && empty($cekliburpenggantiminggu) && empty($cekwfhfull) && $namahari != 'Minggu') {
                                if ($jamlembur_dari >= '22:00' && $jmljam_lbr >= 4.98) {
                                    $premilembur = 6000;
                                    $premilembur_shift_3 = 6000;
                                    $totalpremilembur_shift_3 += $premilembur_shift_3;
                                    $totalharilembur_shift_3 += 1;
                                } elseif ($jamlembur_dari >= '15:00' && $jmljam_lbr >= 4.98) {
                                    $premilembur = 5000;
                                    $premilembur_shift_2 = 5000;
                                    $totalpremilembur_shift_2 += $premilembur_shift_2;
                                    $totalharilembur_shift_2 += 1;
                                }
                            }
                            if ($kategori_lembur == 1) {
                                $overtime_1 = $jmljam_lembur > 1 ? 1 : $jmljam_lembur;
                                $overtime_2 = $jmljam_lembur > 1 ? $jmljam_lembur - 1 : 0;
                                $total_overtime_1 += $overtime_1;
                                $total_overtime_2 += $overtime_2;
                            } elseif ($kategori_lembur == 2) {
                                // $overtime_libur_1 = $jmljam_lembur >= 4 ? 4 : $jmljam_lembur;
                                // $overtime_libur_2 = $jmljam_lembur > 4 ? $jmljam_lembur-4 : 0;
                                $overtime_libur_1 = !empty($ceklibur) && $d->nama_jabatan == 'SECURITY' ? $jmljam_lembur * 2 : $jmljam_lembur;
                                $overtime_libur_2 = 0;
                                $total_overtime_libur_1 += $overtime_libur_1;
                                $total_overtime_libur_2 += $overtime_libur_2;
                            }
                    
                            //echo $d->nik."------". $tgl_presensi."-----".$jmljam_lembur."<br>";
                        } else {
                            $premilembur = 0;
                        }
                    
                        //Pewarnaan Kolom
                        if ($namahari == 'Minggu') {
                            // Jika Hari Minggu
                            if (!empty($cekminggumasuk)) {
                                // Cek Jika Minggu Harus Masuk
                                if ($d->$hari_ke != null) {
                                    // Cek Jika Melakukan Presensi
                                    $colorcolumn = ''; // Warna Kolom Putih
                                    $colortext = '';
                                } else {
                                    $colorcolumn = 'red'; // Warna Kolom Merah Jika TIdak Absen
                                    $colortext = '';
                                }
                            } else {
                                // Jika Minggu Libur atau Tidak Ada Jadwal Masuk
                                $colorcolumn = '#ffaf03'; // Warna Kolom Orange
                                $colortext = 'white';
                            }
                        } else {
                            // Jika Hari Bukan Hari Minggu
                            if ($d->$hari_ke != null) {
                                // Jika Karyawa Melakukan Presensi
                                if (!empty($ceklibur)) {
                                    // Jika Pada Hari Itu ada Libur Nasional
                                    $colorcolumn = 'rgb(4, 163, 65)';
                                    $colortext = 'white';
                                } else {
                                    $colorcolumn = '';
                                    $colortext = '';
                                }
                    
                                if (!empty($cekliburpenggantiminggu)) {
                                    // Jika Pada Hari Itu adalah Libur Pengganti Minggu
                                    $colorcolumn = '#ffaf03';
                                    $colortext = 'white';
                                } else {
                                    $colorcolumn = $colorcolumn;
                                    $colortext = $colortext;
                                }
                    
                                if (!empty($cekwfh)) {
                                    // Jika pada Hari Itu Sedang Dirumahkan
                                    $colorcolumn = '#fc0380';
                                    $colortext = 'black';
                                } else {
                                    $colorcolumn = $colorcolumn;
                                    $colortext = $colortext;
                                }
                    
                                if (!empty($cekwfhfull)) {
                                    // Jika pada Hari Itu Sedang WFH
                                    $colorcolumn = '#9f0ecf';
                                    $colortext = 'black';
                                } else {
                                    $colorcolumn = $colorcolumn;
                                    $colortext = $colortext;
                                }
                            } else {
                                // Jika Karyawan Tidak Melakukan Presensi
                    
                                if (!empty($ceklibur)) {
                                    // Jika Pada Hari Itu ada Libur Nasional
                                    $colorcolumn = 'rgb(4, 163, 65)';
                                    $colortext = 'white';
                                } else {
                                    $colorcolumn = 'red';
                                    $colortext = 'white';
                                }
                    
                                if (!empty($cekliburpenggantiminggu)) {
                                    // Jika Pada Hari Itu adalah Libur Pengganti Minggu
                                    $colorcolumn = '#ffaf03';
                                    $colortext = 'white';
                                } else {
                                    if (empty($ceklibur)) {
                                        $colorcolumn = $colorcolumn;
                                        $colortext = $colortext;
                                    }
                                }
                    
                                if (!empty($cekwfh)) {
                                    // Jika pada Hari Itu Sedang Dirumahkan
                                    $colorcolumn = '#fc0380';
                                    $colortext = 'black';
                                } else {
                                    $colorcolumn = $colorcolumn;
                                    $colortext = $colortext;
                                }
                    
                                if (!empty($cekwfhfull)) {
                                    // Jika pada Hari Itu Sedang WFH
                                    $colorcolumn = '#9f0ecf';
                                    $colortext = 'black';
                                } else {
                                    $colorcolumn = $colorcolumn;
                                    $colortext = $colortext;
                                }
                            }
                        }
                        if ($d->$hari_ke != null) {
                            //$tidakhadir = 0; // Jika Karyawan Absen Maka $tidakhadir dihitung 0
                    
                            $datapresensi = explode('|', $d->$hari_ke); // Split Data Presensi
                    
                            $lintashari = $datapresensi[16] != 'NA' ? $datapresensi[16] : ''; // Lintas Hari
                            $izinpulangdirut = $datapresensi[17] != 'NA' ? $datapresensi[17] : ''; //Izin Pulang Persetujuan Dirut
                            $izinabsendirut = $datapresensi[18] != 'NA' ? $datapresensi[18] : ''; // Izin Absen Persetujuan Dirut
                            $keperluankeluar = $datapresensi[19] != 'NA' ? $datapresensi[19] : ''; // Izin Absen Persetujuan Dirut
                            if (!empty($lintashari)) {
                                // Jika Jadwal Presesni Lintas Hari
                                $tgl_pulang = date('Y-m-d', strtotime('+1 day', strtotime($tgl_presensi)));
                                // Tanggal Pulang adalah Tanggal Berikutnya
                            } else {
                                $tgl_pulang = $tgl_presensi; // Tanggal Pulang adalah Tanggal Presensi
                            }
                    
                            //Jam Masuk Presensi
                            $jam_in = $datapresensi[0]; // Y-m-d H:i:s Jam dan Tanggal Karyawan Melakukan Presensi Masuk
                            $jam_in_presensi = $jam_in != 'NA' ? date('H:i', strtotime($jam_in)) : ''; // Jam Presensi Masuk
                            $jam_in_tanggal = $jam_in != 'NA' ? date('Y-m-d H:i', strtotime($jam_in)) : ''; // Jam Tgl Masuk Presensi
                    
                            //Jam Pulang Presensi
                            $jam_out = $datapresensi[1]; // Y-m-d H:i:s Jam dan Tangal Karyawan Melakukan Presensi Pulang
                            $jam_out_presensi = $jam_out != 'NA' ? date('H:i', strtotime($jam_out)) : ''; //Jam Presensi Pulang
                            $jam_out_tanggal = $jam_out != 'NA' ? date('Y-m-d H:i', strtotime($jam_out)) : ''; //Jam Tgl Presensi Pulang
                    
                            $nama_jadwal = $datapresensi[2];
                            if ($namahari == 'Minggu') {
                                if (!empty($cekminggumasuk)) {
                                    if ($d->nama_jabatan == 'SPG' || $d->nama_jabatan == 'SPB' || !empty($cekwfh)) {
                                        $jam_masuk = $jam_in_presensi;
                                        $jam_masuk_tanggal = $tgl_presensi . ' ' . $jam_masuk;
                                    } else {
                                        $jam_masuk = date('H:i', strtotime($datapresensi[3]));
                                        $jam_masuk_tanggal = $tgl_presensi . ' ' . $jam_masuk;
                                    }
                    
                                    if ($d->nama_jabatan == 'SPG' || $d->nama_jabatan == 'SPB' || !empty($cekwfh)) {
                                        $jam_pulang = !empty($jam_out_presensi) ? $jam_out_presensi : '';
                                        $jam_pulang_tanggal = !empty($jam_out_presensi) ? $tgl_pulang . ' ' . $jam_pulang : '';
                                    } else {
                                        $jam_pulang = $datapresensi[4] != 'NA' ? date('H:i', strtotime($datapresensi[4])) : '';
                                        $jam_pulang_tanggal = $datapresensi[4] != 'NA' ? $tgl_pulang . ' ' . $jam_pulang : '';
                                    }
                                } else {
                                    $jam_masuk = $jam_in_presensi;
                                    $jam_masuk_tanggal = $tgl_presensi . ' ' . $jam_masuk;
                                    $jam_pulang = !empty($jam_out_presensi) ? $jam_out_presensi : '';
                                    $jam_pulang_tanggal = !empty($jam_out_presensi) ? $tgl_pulang . ' ' . $jam_pulang : '';
                                }
                            } else {
                                if (!empty($ceklibur) || !empty($cekliburpenggantiminggu)) {
                                    $jam_masuk = $jam_in_presensi;
                                    $jam_masuk_tanggal = $tgl_presensi . ' ' . $jam_masuk;
                                    $jam_pulang = !empty($jam_out_presensi) ? $jam_out_presensi : '';
                                    $jam_pulang_tanggal = !empty($jam_out_presensi) ? $tgl_pulang . ' ' . $jam_pulang : '';
                                } else {
                                    if ($d->nama_jabatan == 'SPG' || $d->nama_jabatan == 'SPB' || !empty($cekwfh)) {
                                        $jam_masuk = $jam_in_presensi;
                                        $jam_masuk_tanggal = $tgl_presensi . ' ' . $jam_masuk;
                                    } else {
                                        $jam_masuk = date('H:i', strtotime($datapresensi[3]));
                                        $jam_masuk_tanggal = $tgl_presensi . ' ' . $jam_masuk;
                                    }
                    
                                    if ($d->nama_jabatan == 'SPG' || $d->nama_jabatan == 'SPB' || !empty($cekwfh)) {
                                        $jam_pulang = !empty($jam_out_presensi) ? $jam_out_presensi : '';
                                        $jam_pulang_tanggal = !empty($jam_out_presensi) ? $tgl_pulang . ' ' . $jam_pulang : '';
                                    } else {
                                        $jam_pulang = $datapresensi[4] != 'NA' ? date('H:i', strtotime($datapresensi[4])) : '';
                                        $jam_pulang_tanggal = $datapresensi[4] != 'NA' ? $tgl_pulang . ' ' . $jam_pulang : '';
                                    }
                                }
                            }
                    
                            //$jam_pulang_presensi =$jam_pulang != "NA" ? date("H:i",strtotime($jam_pulang)) : '';
                    
                            //Keluar Masuk Kantor
                            $jam_keluar = $datapresensi[9] != 'NA' ? date('H:i', strtotime($datapresensi[9])) : '';
                    
                            $jam_masuk_kk = $datapresensi[10] != 'NA' ? date('H:i', strtotime($datapresensi[10])) : '';
                    
                            $total_jam = $datapresensi[11] != 'NA' ? $datapresensi[11] : 0;
                    
                            $status = $datapresensi[5] != 'NA' ? $datapresensi[5] : '';
                    
                            $sid = $datapresensi[12] != 'NA' ? $datapresensi[12] : '';
                    
                            $kode_izin_terlambat = $datapresensi[7] != 'NA' ? $datapresensi[7] : '';
                    
                            $kode_izin_pulang = $datapresensi[8] != 'NA' ? $datapresensi[8] : '';
                    
                            $jam_istirahat = $datapresensi[14];
                            $jam_istirahat_presensi = $datapresensi[14] != 'NA' ? date('H:i', strtotime($datapresensi[14])) : '';
                            $jam_istirahat_presensi_tanggal = $datapresensi[14] != 'NA' ? $tgl_pulang . ' ' . $jam_istirahat_presensi : '';
                    
                            $jam_awal_istirahat = $datapresensi[13] != 'NA' ? date('H:i', strtotime($datapresensi[13])) : '';
                            $jam_awal_istirahat_tanggal = $datapresensi[14] != 'NA' ? $tgl_pulang . ' ' . $jam_awal_istirahat : '';
                    
                            $jam_akhir_istirahat = $datapresensi[14] != 'NA' ? date('H:i', strtotime($datapresensi[14])) : '';
                            $jam_akhir_istirahat_tanggal = $datapresensi[14] != 'NA' ? $tgl_pulang . ' ' . $jam_akhir_istirahat : '';
                    
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
                                    $menitterlambat = floor(($diffterlambat - $jamterlambat * (60 * 60)) / 60);
                                    //Tambah 0 Jika Jam < dari 10
                                    $jterlambat = $jamterlambat <= 9 ? '0' . $jamterlambat : $jamterlambat;
                                    //Tambah 0 Jika Menit Kurang Dari 10
                                    $mterlambat = $menitterlambat <= 9 ? '0' . $menitterlambat : $menitterlambat;
                    
                                    //Total Keterlambatan Dalam Jam dan Menit
                                    $terlambat = $jterlambat . ':' . $mterlambat;
                                    //Keterlambatan Menit Dalam Desimal
                                    $desimalterlambat = ROUND($menitterlambat / 60, 2);
                                    $colorterlambat = 'red';
                                } else {
                                    $terlambat = 'Tepat waktu';
                                    $jamterlambat = 0;
                                    $desimalterlambat = 0;
                                    $colorterlambat = 'green';
                                }
                            } else {
                                $terlambat = '';
                                $jamterlambat = 0;
                                $desimalterlambat = 0;
                                $colorterlambat = '';
                            }
                    
                            //Perhitungan Jam Keluar
                            if (!empty($jam_keluar)) {
                                $jam_keluar_tanggal = $datapresensi[9] != 'NA' ? $tgl_pulang . ' ' . $jam_keluar : '';
                                if (!empty($jam_masuk_kk)) {
                                    $jam_masuk_kk_tanggal = $tgl_pulang . ' ' . $jam_masuk_kk;
                                } else {
                                    $jam_masuk_kk_tanggal = $tgl_pulang . ' ' . $jam_pulang;
                                }
                    
                                $jk1 = strtotime($jam_keluar_tanggal);
                                $jk2 = strtotime($jam_masuk_kk_tanggal);
                                $difkeluarkantor = $jk2 - $jk1;
                    
                                //Total Jam Keluar Kantor
                                $jamkeluarkantor = floor($difkeluarkantor / (60 * 60));
                                //Total Menit Keluar Kantor
                                $menitkeluarkantor = floor(($difkeluarkantor - $jamkeluarkantor * (60 * 60)) / 60);
                    
                                //Tambah 0 di Depan Jika < 10
                                $jkeluarkantor = $jamkeluarkantor <= 9 ? '0' . $jamkeluarkantor : $jamkeluarkantor;
                                $mkeluarkantor = $menitkeluarkantor <= 9 ? '0' . $menitkeluarkantor : $menitkeluarkantor;
                    
                                if (empty($jam_masuk_kk)) {
                                    if ($total_jam == 7) {
                                        $totaljamkeluar = $jkeluarkantor - 1 . ':' . $mkeluarkantor;
                                        $desimaljamkeluar = ROUND($menitkeluarkantor / 60, 2) - 1;
                                    } else {
                                        $totaljamkeluar = $jkeluarkantor . ':' . $mkeluarkantor;
                                        $desimaljamkeluar = ROUND($menitkeluarkantor / 60, 2);
                                    }
                                } else {
                                    $totaljamkeluar = $jkeluarkantor . ':' . $mkeluarkantor;
                                    $desimaljamkeluar = ROUND($menitkeluarkantor / 60, 2);
                                }
                            } else {
                                $totaljamkeluar = '';
                                $desimaljamkeluar = 0;
                                $jamkeluarkantor = 0;
                            }
                    
                            //Tambah 0 Didepan Jika < 10 pada Jam Terlambat
                            $jamterlambat = $jamterlambat < 0 && !empty($kode_izin_terlambat) ? 0 : $jamterlambat;
                    
                            //Jam terlambat dalam Desimal
                    
                            $jt = round($jamterlambat + $desimalterlambat, 2, PHP_ROUND_HALF_DOWN);
                            if ($jamkeluarkantor > 0) {
                                if ($keperluankeluar == 'K') {
                                    $jk = 0;
                                } else {
                                    $jk = $jamkeluarkantor + $desimaljamkeluar;
                                }
                            } else {
                                $jk = 0;
                            }
                    
                            $jt = !empty($jt) ? $jt : 0;
                            //echo $jamterlambat."|<br>";
                            //echo $menitterlambat."|";
                            // menghitung Denda
                            if (!empty($jam_in) and $kode_dept != 'MKT') {
                                if ($jam_in_presensi > $jam_masuk and empty($kode_izin_terlambat)) {
                                    if ($jamterlambat <= 1) {
                                        if ($menitterlambat >= 5 and $menitterlambat < 10) {
                                            $denda = 5000;
                                            //echo "test5000|";
                                        } elseif ($menitterlambat >= 10 and $menitterlambat < 15) {
                                            $denda = 10000;
                                            //echo "test10ribu|";
                                        } elseif ($menitterlambat >= 15 and $menitterlambat <= 59) {
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
                            if ($jam_out_tanggal > $jam_awal_istirahat_tanggal && $jam_out_tanggal < $jam_akhir_istirahat_tanggal) {
                                // Shift 3 Belum Di Set
                                // $jout = $jam_awal_istirahat_tanggal;
                                $jout = $jam_out_tanggal;
                            } else {
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
                                $m = $diff - $jam * (60 * 60);
                                $menit = floor($m / 60);
                            }
                            if ($denda == 0 and empty($kode_izin_terlambat)) {
                                if ($kode_dept != 'MKT') {
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
                            if (!empty($cekwfh)) {
                                $totaljam = $jam - $jt - $jk;
                            } else {
                                $totaljam = $total_jam - $jt - $jk;
                            }
                    
                            if ($jam_out != 'NA') {
                                if ($jam_out_tanggal < $jam_pulang_tanggal) {
                                    //Shift 3 Belum Di Set | Coba
                                    if ($jam_out_tanggal > $jam_akhir_istirahat_tanggal && $jam_istirahat != 'NA') {
                                        $desimalmenit = ROUND(($menit * 100) / 60);
                                        $grandtotaljam = $jam - 1 . '.' . $desimalmenit;
                                        $cek = 'A';
                                    } else {
                                        $desimalmenit = ROUND(($menit * 100) / 60);
                                        $grandtotaljam = $jam . '.' . $desimalmenit;
                                        $cek = 'B' . $jam_out_tanggal . '------' . $jam_akhir_istirahat_tanggal;
                                    }
                    
                                    $grandtotaljam = $grandtotaljam - $jt - $jk;
                                } else {
                                    $cek = 'C';
                                    $desimalmenit = 0;
                                    $grandtotaljam = $totaljam;
                                }
                            } else {
                                $desimalmenit = 0;
                                $grandtotaljam = 0;
                            }
                    
                            if ($jam_in == 'NA') {
                                if ($status == 'i') {
                                    $grandtotaljam = 0;
                                } elseif ($status == 's') {
                                    if (!empty($sid)) {
                                        $grandtotaljam = 7;
                                    } else {
                                        $grandtotaljam = 0;
                                    }
                                } elseif ($status == 'c') {
                                    $grandtotaljam = 7;
                                } else {
                                    $grandtotaljam = 0;
                                }
                            }
                    
                            if ($nama_jadwal == 'SHIFT 2' && $grandtotaljam >= 4.98) {
                                $premi = 5000;
                                $premi_shift_2 = 5000;
                                $totalpremi_shift_2 += $premi_shift_2;
                                $totalhari_shift_2 += 1;
                            } elseif ($nama_jadwal == 'SHIFT 3' && $grandtotaljam >= 4.98) {
                                $premi = 6000;
                                $premi_shift_3 = 6000;
                                $totalpremi_shift_3 += $premi_shift_3;
                                $totalhari_shift_3 += 1;
                            } else {
                                $premi = 0;
                            }
                    
                            if ($jam_out != 'NA' && $jam_out_tanggal < $jam_pulang_tanggal) {
                                $pc = 'Pulang Cepat';
                                if (!empty($izinpulangdirut)) {
                                    $totalpc = 0;
                                    $menitpc = 0;
                                } else {
                                    $jpc1 = strtotime($jam_out_tanggal);
                                    $jpc2 = strtotime($jam_pulang_tanggal);
                    
                                    $diffpc = $jpc2 - $jpc1;
                                    //Jam Terlambat
                                    $jampc = floor($diffpc / (60 * 60));
                                    //Menit Terlambat
                                    $menitpc = floor(($diffpc - $jampc * (60 * 60)) / 60);
                    
                                    if ($jampc > 5 || $jampc >= 1) {
                                        $totalpc = $total_jam + $jk - $grandtotaljam;
                                    } else {
                                        $totalpc = 0;
                                    }
                                }
                            } else {
                                $pc = '';
                                $totalpc = 0;
                                $menitpc = 0;
                            }
                    
                            // echo $rangetanggal[$i]."-".$total_jam."+".$jk."+".$grandtotaljam."(".$cek.")----".$menitpc."<br>";
                    
                            if (!empty($cekwfh)) {
                                if ($cekmasakerja >= 3) {
                                    $totaljamdirumahkan = ROUND($jamdirumahkan / 2, 2) - ($grandtotaljam - ROUND($jamdirumahkan / 2, 2));
                                } else {
                                    $totaljamdirumahkan = $jamdirumahkan;
                                }
                                $totaldirumahkan += $totaljamdirumahkan;
                            }
                    
                            if ($status == 'a') {
                                if ($namahari == 'Sabtu') {
                                    $tidakhadir = 5;
                                } else {
                                    $tidakhadir = 7;
                                }
                            } else {
                                $tidakhadir = 0; // Jika Karyawan Absen Maka $tidakhadir dihitung 0
                            }
                    
                            if ($status == 'h') {
                                $izinabsen = 0;
                                $izinsakit = 0;
                            } elseif ($status == 's') {
                                if (empty($sid)) {
                                    if (empty($izinabsendirut)) {
                                        if ($namahari == 'Sabtu') {
                                            $izinsakit = 5;
                                        } elseif ($namahari == 'Minggu') {
                                            if (!empty($cekminggumasuk)) {
                                                $izinsakit = 7;
                                            } else {
                                                $izinsakit = 0;
                                            }
                                        } else {
                                            $izinsakit = 7;
                                        }
                                    } else {
                                        $izinsakit = 0;
                                    }
                                } else {
                                    $izinsakit = 0;
                                }
                    
                                $izinabsen = 0;
                            } elseif ($status == 'i') {
                                if (empty($izinabsendirut)) {
                                    if ($namahari == 'Sabtu') {
                                        $izinabsen = 5;
                                    } elseif ($namahari == 'Minggu') {
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
                            } elseif ($status == 'c') {
                                $izinabsen = 0;
                                $izinsakit = 0;
                            }
                        } else {
                            $jt = 0;
                            $jk = 0;
                            $denda = 0;
                            $premi = 0;
                            $premilembur = 0;
                            $totalpc = 0;
                            $izinabsen = 0;
                            $izinsakit = 0;
                            if ((!empty($ceklibur) && $cekmasakerja >= 3) || !empty($cekliburpenggantiminggu) || (!empty($cekwfh) && $cekmasakerja >= 3) || (!empty($cekwfhfull) && $cekmasakerja >= 3)) {
                                $tidakhadir = 0;
                            } else {
                                if ($namahari == 'Sabtu') {
                                    $tidakhadir = 5;
                                } elseif ($namahari == 'Minggu') {
                                    if (!empty($cekminggumasuk)) {
                                        $tidakhadir = 7;
                                    } else {
                                        $tidakhadir = 0;
                                    }
                                } else {
                                    $tidakhadir = 7;
                                }
                            }
                    
                            if (!empty($cekwfh)) {
                                if ($cekmasakerja >= 3) {
                                    $totaljamdirumahkan = ROUND($jamdirumahkan / 2, 2);
                                } else {
                                    $totaljamdirumahkan = $jamdirumahkan;
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
                    $totaljamkerja = $totaljam1bulan - $totalterlambat - $totalkeluar - $totaldirumahkan - $totaltidakhadir - $totalpulangcepat - $totalizinabsen - $totalizinsakit;
                    
                    $totalpotonganjam = $totalterlambat + $totalkeluar + $totaldirumahkan + $totaltidakhadir + $totalpulangcepat + $totalizinabsen + $totalizinsakit;
                    
                    $totalhariall_shift_2 = $totalhari_shift_2 + $totalharilembur_shift_2;
                    $totalpremiall_shift_2 = $totalpremi_shift_2 + $totalpremilembur_shift_2;
                    
                    $totalhariall_shift_3 = $totalhari_shift_3 + $totalharilembur_shift_3;
                    $totalpremiall_shift_3 = $totalpremi_shift_3 + $totalpremilembur_shift_3;
                    
                    //UPAH
                    $upah = $d->gaji_pokok + $d->t_jabatan + $d->t_masakerja + $d->t_tanggungjawab + $d->t_makan + $d->t_istri + $d->t_skill;
                    
                    //Upah Per Jam
                    
                    $upah_perjam = $upah / 173;
                    
                    //INSENTIF
                    $jmlinsentif = $d->iu_masakerja + $d->iu_lembur + $d->iu_penempatan + $d->iu_kpi + $d->im_ruanglingkup + $d->im_penempatan + $d->im_kinerja;
                    
                    //UPAH OVERTIME
                    
                    if ($d->nama_jabatan == 'SECURITY') {
                        $upah_ot_1 = 8000 * $total_overtime_1;
                    } else {
                        $upah_ot_1 = $upah_perjam * 1.5 * $total_overtime_1;
                    }
                    
                    if ($d->nama_jabatan == 'SECURITY') {
                        $upah_ot_2 = 8000 * $total_overtime_2;
                    } else {
                        $upah_ot_2 = $upah_perjam * 2 * $total_overtime_2;
                    }
                    
                    if ($d->nama_jabatan == 'SECURITY') {
                        $upah_otl_1 = 13143 * $total_overtime_libur_1;
                    } else {
                        $upah_otl_1 = floor($upah_perjam * 2 * $total_overtime_libur_1);
                    }
                    
                    if ($d->nama_jabatan == 'SECURITY') {
                        $upah_otl_2 = 0;
                    } else {
                        $upah_otl_2 = $upah_perjam * 2 * $total_overtime_libur_2;
                    }
                    
                    $total_upah_overtime = $upah_ot_1 + $upah_ot_2 + $upah_otl_1 + $upah_otl_2;
                    
                    $bruto = $upah_perjam * $totaljamkerja + $total_upah_overtime + $totalpremiall_shift_2 + $totalpremiall_shift_3;
                    
                    $bpjskesehatan = $d->iuran_kes;
                    $bpjstenagakerja = $d->iuran_tk;
                    
                    if (($d->id_kantor == 'PST' && $cekmasakerja >= 3) || ($d->id_kantor == 'TSM' && $cekmasakerja >= 3) || $d->spip == 1) {
                        $spip = 5000;
                    } else {
                        $spip = 0;
                    }
                    
                    $potongan = ROUND($bpjskesehatan + $bpjstenagakerja + $totaldenda + $d->cicilan_pjp + $d->jml_kasbon + $spip, 0);
                    $jmlbersih = $bruto - $potongan;
                    
                    //TOTAL
                    //Total Gaji Pokok
                    $total_gajipokok += $d->gaji_pokok;
                    $total_gajipokok_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $d->gaji_pokok : 0;
                    $total_gajipokok_penjualan += $d->klasifikasi == 'PENJUALAN' ? $d->gaji_pokok : 0;
                    $total_gajipokok_tkl += $d->klasifikasi == 'TKL' ? $d->gaji_pokok : 0;
                    $total_gajipokok_tktl += $d->klasifikasi == 'TKTL' ? $d->gaji_pokok : 0;
                    $total_gajipokok_mp += $d->id_perusahaan == 'MP' ? $d->gaji_pokok : 0;
                    $total_gajipokok_pcf += $d->id_perusahaan == 'PCF' ? $d->gaji_pokok : 0;
                    
                    $total_tunjangan_jabatan += $d->t_jabatan;
                    $total_t_jabatan_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $d->t_jabatan : 0;
                    $total_t_jabatan_penjualan += $d->klasifikasi == 'PENJUALAN' ? $d->t_jabatan : 0;
                    $total_t_jabatan_tkl += $d->klasifikasi == 'TKL' ? $d->t_jabatan : 0;
                    $total_t_jabatan_tktl += $d->klasifikasi == 'TKTL' ? $d->t_jabatan : 0;
                    $total_t_jabatan_mp += $d->id_perusahaan == 'MP' ? $d->t_jabatan : 0;
                    $total_t_jabatan_pcf += $d->id_perusahaan == 'PCF' ? $d->t_jabatan : 0;
                    
                    $total_tunjangan_masakerja += $d->t_masakerja;
                    $total_t_masakerja_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $d->t_masakerja : 0;
                    $total_t_masakerja_penjualan += $d->klasifikasi == 'PENJUALAN' ? $d->t_masakerja : 0;
                    $total_t_masakerja_tkl += $d->klasifikasi == 'TKL' ? $d->t_masakerja : 0;
                    $total_t_masakerja_tktl += $d->klasifikasi == 'TKTL' ? $d->t_masakerja : 0;
                    $total_t_masakerja_mp += $d->id_perusahaan == 'MP' ? $d->t_masakerja : 0;
                    $total_t_masakerja_pcf += $d->id_perusahaan == 'PCF' ? $d->t_masakerja : 0;
                    
                    $total_tunjangan_tanggungjawab += $d->t_tanggungjawab;
                    $total_t_tanggungjawab_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $d->t_tanggungjawab : 0;
                    $total_t_tanggungjawab_penjualan += $d->klasifikasi == 'PENJUALAN' ? $d->t_tanggungjawab : 0;
                    $total_t_tanggungjawab_tkl += $d->klasifikasi == 'TKL' ? $d->t_tanggungjawab : 0;
                    $total_t_tanggungjawab_tktl += $d->klasifikasi == 'TKTL' ? $d->t_tanggungjawab : 0;
                    $total_t_tanggungjawab_mp += $d->id_perusahaan == 'MP' ? $d->t_tanggungjawab : 0;
                    $total_t_tanggungjawab_pcf += $d->id_perusahaan == 'PCF' ? $d->t_tanggungjawab : 0;
                    
                    $total_tunjangan_makan += $d->t_makan;
                    $total_t_makan_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $d->t_makan : 0;
                    $total_t_makan_penjualan += $d->klasifikasi == 'PENJUALAN' ? $d->t_makan : 0;
                    $total_t_makan_tkl += $d->klasifikasi == 'TKL' ? $d->t_makan : 0;
                    $total_t_makan_tktl += $d->klasifikasi == 'TKTL' ? $d->t_makan : 0;
                    $total_t_makan_mp += $d->id_perusahaan == 'MP' ? $d->t_makan : 0;
                    $total_t_makan_pcf += $d->id_perusahaan == 'PCF' ? $d->t_makan : 0;
                    
                    $total_tunjangan_istri += $d->t_istri;
                    $total_t_istri_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $d->t_istri : 0;
                    $total_t_istri_penjualan += $d->klasifikasi == 'PENJUALAN' ? $d->t_istri : 0;
                    $total_t_istri_tkl += $d->klasifikasi == 'TKL' ? $d->t_istri : 0;
                    $total_t_istri_tktl += $d->klasifikasi == 'TKTL' ? $d->t_istri : 0;
                    $total_t_istri_mp += $d->id_perusahaan == 'MP' ? $d->t_istri : 0;
                    $total_t_istri_pcf += $d->id_perusahaan == 'PCF' ? $d->t_istri : 0;
                    
                    $total_tunjangan_skillkhusus += $d->t_skill;
                    $total_t_skillkhusus_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $d->t_skill : 0;
                    $total_t_skillkhusus_penjualan += $d->klasifikasi == 'PENJUALAN' ? $d->t_skill : 0;
                    $total_t_skillkhusus_tkl += $d->klasifikasi == 'TKL' ? $d->t_skill : 0;
                    $total_t_skillkhusus_tktl += $d->klasifikasi == 'TKTL' ? $d->t_skill : 0;
                    $total_t_skillkhusus_mp += $d->id_perusahaan == 'MP' ? $d->t_skill : 0;
                    $total_t_skillkhusus_pcf += $d->id_perusahaan == 'PCF' ? $d->t_skill : 0;
                    
                    $total_insentif_masakerja += $d->iu_masakerja;
                    $total_i_masakerja_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $d->iu_masakerja : 0;
                    $total_i_masakerja_penjualan += $d->klasifikasi == 'PENJUALAN' ? $d->iu_masakerja : 0;
                    $total_i_masakerja_tkl += $d->klasifikasi == 'TKL' ? $d->iu_masakerja : 0;
                    $total_i_masakerja_tktl += $d->klasifikasi == 'TKTL' ? $d->iu_masakerja : 0;
                    $total_i_masakerja_mp += $d->id_perusahaan == 'MP' ? $d->iu_masakerja : 0;
                    $total_i_masakerja_pcf += $d->id_perusahaan == 'PCF' ? $d->iu_masakerja : 0;
                    
                    $total_insentif_lembur += $d->iu_lembur;
                    $total_i_lembur_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $d->iu_lembur : 0;
                    $total_i_lembur_penjualan += $d->klasifikasi == 'PENJUALAN' ? $d->iu_lembur : 0;
                    $total_i_lembur_tkl += $d->klasifikasi == 'TKL' ? $d->iu_lembur : 0;
                    $total_i_lembur_tktl += $d->klasifikasi == 'TKTL' ? $d->iu_lembur : 0;
                    $total_i_lembur_mp += $d->id_perusahaan == 'MP' ? $d->iu_lembur : 0;
                    $total_i_lembur_pcf += $d->id_perusahaan == 'PCF' ? $d->iu_lembur : 0;
                    
                    $total_insentif_penempatan += $d->iu_penempatan;
                    $total_i_penempatan_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $d->iu_penempatan : 0;
                    $total_i_penempatan_penjualan += $d->klasifikasi == 'PENJUALAN' ? $d->iu_penempatan : 0;
                    $total_i_penempatan_tkl += $d->klasifikasi == 'TKL' ? $d->iu_penempatan : 0;
                    $total_i_penempatan_tktl += $d->klasifikasi == 'TKTL' ? $d->iu_penempatan : 0;
                    $total_i_penempatan_mp += $d->id_perusahaan == 'MP' ? $d->iu_penempatan : 0;
                    $total_i_penempatan_pcf += $d->id_perusahaan == 'PCF' ? $d->iu_penempatan : 0;
                    
                    $total_insentif_kpi += $d->iu_kpi;
                    $total_i_kpi_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $d->iu_kpi : 0;
                    $total_i_kpi_penjualan += $d->klasifikasi == 'PENJUALAN' ? $d->iu_kpi : 0;
                    $total_i_kpi_tkl += $d->klasifikasi == 'TKL' ? $d->iu_kpi : 0;
                    $total_i_kpi_tktl += $d->klasifikasi == 'TKTL' ? $d->iu_kpi : 0;
                    $total_i_kpi_mp += $d->id_perusahaan == 'MP' ? $d->iu_kpi : 0;
                    $total_i_kpi_pcf += $d->id_perusahaan == 'PCF' ? $d->iu_kpi : 0;
                    
                    $total_im_ruanglingkup += $d->im_ruanglingkup;
                    $total_im_ruanglingkup_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $d->im_ruanglingkup : 0;
                    $total_im_ruanglingkup_penjualan += $d->klasifikasi == 'PENJUALAN' ? $d->im_ruanglingkup : 0;
                    $total_im_ruanglingkup_tkl += $d->klasifikasi == 'TKL' ? $d->im_ruanglingkup : 0;
                    $total_im_ruanglingkup_tktl += $d->klasifikasi == 'TKTL' ? $d->im_ruanglingkup : 0;
                    $total_im_ruanglingkup_mp += $d->id_perusahaan == 'MP' ? $d->im_ruanglingkup : 0;
                    $total_im_ruanglingkup_pcf += $d->id_perusahaan == 'PCF' ? $d->im_ruanglingkup : 0;
                    
                    $total_im_penempatan += $d->im_penempatan;
                    $total_im_penempatan_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $d->im_penempatan : 0;
                    $total_im_penempatan_penjualan += $d->klasifikasi == 'PENJUALAN' ? $d->im_penempatan : 0;
                    $total_im_penempatan_tkl += $d->klasifikasi == 'TKL' ? $d->im_penempatan : 0;
                    $total_im_penempatan_tktl += $d->klasifikasi == 'TKTL' ? $d->im_penempatan : 0;
                    $total_im_penempatan_mp += $d->id_perusahaan == 'MP' ? $d->im_penempatan : 0;
                    $total_im_penempatan_pcf += $d->id_perusahaan == 'PCF' ? $d->im_penempatan : 0;
                    
                    $total_im_kinerja += $d->im_kinerja;
                    $total_im_kinerja_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $d->im_kinerja : 0;
                    $total_im_kinerja_penjualan += $d->klasifikasi == 'PENJUALAN' ? $d->im_kinerja : 0;
                    $total_im_kinerja_tkl += $d->klasifikasi == 'TKL' ? $d->im_kinerja : 0;
                    $total_im_kinerja_tktl += $d->klasifikasi == 'TKTL' ? $d->im_kinerja : 0;
                    $total_im_kinerja_mp += $d->id_perusahaan == 'MP' ? $d->im_kinerja : 0;
                    $total_im_kinerja_pcf += $d->id_perusahaan == 'PCF' ? $d->im_kinerja : 0;
                    
                    $total_upah += $upah;
                    $total_upah_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $upah : 0;
                    $total_upah_penjualan += $d->klasifikasi == 'PENJUALAN' ? $upah : 0;
                    $total_upah_tkl += $d->klasifikasi == 'TKL' ? $upah : 0;
                    $total_upah_tktl += $d->klasifikasi == 'TKTL' ? $upah : 0;
                    $total_upah_mp += $d->id_perusahaan == 'MP' ? $upah : 0;
                    $total_upah_pcf += $d->id_perusahaan == 'PCF' ? $upah : 0;
                    
                    $total_insentif += $jmlinsentif;
                    $total_insentif_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $jmlinsentif : 0;
                    $total_insentif_penjualan += $d->klasifikasi == 'PENJUALAN' ? $jmlinsentif : 0;
                    $total_insentif_tkl += $d->klasifikasi == 'TKL' ? $jmlinsentif : 0;
                    $total_insentif_tktl += $d->klasifikasi == 'TKTL' ? $jmlinsentif : 0;
                    $total_insentif_mp += $d->id_perusahaan == 'MP' ? $jmlinsentif : 0;
                    $total_insentif_pcf += $d->id_perusahaan == 'PCF' ? $jmlinsentif : 0;
                    
                    $total_all_jamkerja += $totaljamkerja;
                    $total_jamkerja_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $totaljamkerja : 0;
                    $total_jamkerja_penjualan += $d->klasifikasi == 'PENJUALAN' ? $totaljamkerja : 0;
                    $total_jamkerja_tkl += $d->klasifikasi == 'TKL' ? $totaljamkerja : 0;
                    $total_jamkerja_tktl += $d->klasifikasi == 'TKTL' ? $totaljamkerja : 0;
                    $total_jamkerja_mp += $d->id_perusahaan == 'MP' ? $totaljamkerja : 0;
                    $total_jamkerja_pcf += $d->id_perusahaan == 'PCF' ? $totaljamkerja : 0;
                    
                    $total_all_upahperjam += $upah_perjam;
                    $total_upahperjam_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $upah_perjam : 0;
                    $total_upahperjam_penjualan += $d->klasifikasi == 'PENJUALAN' ? $upah_perjam : 0;
                    $total_upahperjam_tkl += $d->klasifikasi == 'TKL' ? $upah_perjam : 0;
                    $total_upahperjam_tktl += $d->klasifikasi == 'TKTL' ? $upah_perjam : 0;
                    $total_upahperjam_mp += $d->id_perusahaan == 'MP' ? $upah_perjam : 0;
                    $total_upahperjam_pcf += $d->id_perusahaan == 'PCF' ? $upah_perjam : 0;
                    
                    $total_all_overtime_1 += $total_overtime_1;
                    $total_overtime_1_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $total_overtime_1 : 0;
                    $total_overtime_1_penjualan += $d->klasifikasi == 'PENJUALAN' ? $total_overtime_1 : 0;
                    $total_overtime_1_tkl += $d->klasifikasi == 'TKL' ? $total_overtime_1 : 0;
                    $total_overtime_1_tktl += $d->klasifikasi == 'TKTL' ? $total_overtime_1 : 0;
                    $total_overtime_1_mp += $d->id_perusahaan == 'MP' ? $total_overtime_1 : 0;
                    $total_overtime_1_pcf += $d->id_perusahaan == 'PCF' ? $total_overtime_1 : 0;
                    
                    $total_all_upah_ot_1 += $upah_ot_1;
                    $total_upah_ot_1_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $upah_ot_1 : 0;
                    $total_upah_ot_1_penjualan += $d->klasifikasi == 'PENJUALAN' ? $upah_ot_1 : 0;
                    $total_upah_ot_1_tkl += $d->klasifikasi == 'TKL' ? $upah_ot_1 : 0;
                    $total_upah_ot_1_tktl += $d->klasifikasi == 'TKTL' ? $upah_ot_1 : 0;
                    $total_upah_ot_1_mp += $d->id_perusahaan == 'MP' ? $upah_ot_1 : 0;
                    $total_upah_ot_1_pcf += $d->id_perusahaan == 'PCF' ? $upah_ot_1 : 0;
                    
                    $total_all_overtime_2 += $total_overtime_2;
                    $total_overtime_2_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $total_overtime_2 : 0;
                    $total_overtime_2_penjualan += $d->klasifikasi == 'PENJUALAN' ? $total_overtime_2 : 0;
                    $total_overtime_2_tkl += $d->klasifikasi == 'TKL' ? $total_overtime_2 : 0;
                    $total_overtime_2_tktl += $d->klasifikasi == 'TKTL' ? $total_overtime_2 : 0;
                    $total_overtime_2_mp += $d->id_perusahaan == 'MP' ? $total_overtime_2 : 0;
                    $total_overtime_2_pcf += $d->id_perusahaan == 'PCF' ? $total_overtime_2 : 0;
                    
                    $total_all_upah_ot_2 += $upah_ot_2;
                    $total_upah_ot_2_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $upah_ot_2 : 0;
                    $total_upah_ot_2_penjualan += $d->klasifikasi == 'PENJUALAN' ? $upah_ot_2 : 0;
                    $total_upah_ot_2_tkl += $d->klasifikasi == 'TKL' ? $upah_ot_2 : 0;
                    $total_upah_ot_2_tktl += $d->klasifikasi == 'TKTL' ? $upah_ot_2 : 0;
                    $total_upah_ot_2_mp += $d->id_perusahaan == 'MP' ? $upah_ot_2 : 0;
                    $total_upah_ot_2_pcf += $d->id_perusahaan == 'PCF' ? $upah_ot_2 : 0;
                    
                    $total_all_overtime_libur += $total_overtime_libur_1;
                    $total_overtime_libur_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $total_overtime_libur_1 : 0;
                    $total_overtime_libur_penjualan += $d->klasifikasi == 'PENJUALAN' ? $total_overtime_libur_1 : 0;
                    $total_overtime_libur_tkl += $d->klasifikasi == 'TKL' ? $total_overtime_libur_1 : 0;
                    $total_overtime_libur_tktl += $d->klasifikasi == 'TKTL' ? $total_overtime_libur_1 : 0;
                    $total_overtime_libur_mp += $d->id_perusahaan == 'MP' ? $total_overtime_libur_1 : 0;
                    $total_overtime_libur_pcf += $d->id_perusahaan == 'PCF' ? $total_overtime_libur_1 : 0;
                    
                    $total_all_upah_overtime_libur += $upah_otl_1;
                    $total_upah_overtime_libur_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $upah_otl_1 : 0;
                    $total_upah_overtime_libur_penjualan += $d->klasifikasi == 'PENJUALAN' ? $upah_otl_1 : 0;
                    $total_upah_overtime_libur_tkl += $d->klasifikasi == 'TKL' ? $upah_otl_1 : 0;
                    $total_upah_overtime_libur_tktl += $d->klasifikasi == 'TKTL' ? $upah_otl_1 : 0;
                    $total_upah_overtime_libur_mp += $d->id_perusahaan == 'MP' ? $upah_otl_1 : 0;
                    $total_upah_overtime_libur_mp += $d->id_perusahaan == 'PCF' ? $upah_otl_1 : 0;
                    
                    $total_all_upah_overtime += $total_upah_overtime;
                    $total_all_upah_otl_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $total_upah_overtime : 0;
                    $total_all_upah_otl_penjualan += $d->klasifikasi == 'PENJUALAN' ? $total_upah_overtime : 0;
                    $total_all_upah_otl_tkl += $d->klasifikasi == 'TKL' ? $total_upah_overtime : 0;
                    $total_all_upah_otl_tktl += $d->klasifikasi == 'TKTL' ? $total_upah_overtime : 0;
                    $total_all_upah_otl_mp += $d->id_perusahaan == 'MP' ? $total_upah_overtime : 0;
                    $total_all_upah_otl_pcf += $d->id_perusahaan == 'PCF' ? $total_upah_overtime : 0;
                    
                    $total_all_hari_shift_2 += $totalhariall_shift_2;
                    $total_all_hari_shift_2_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $totalhariall_shift_2 : 0;
                    $total_all_hari_shift_2_penjualan += $d->klasifikasi == 'PENJUALAN' ? $totalhariall_shift_2 : 0;
                    $total_all_hari_shift_2_tkl += $d->klasifikasi == 'TKL' ? $totalhariall_shift_2 : 0;
                    $total_all_hari_shift_2_tktl += $d->klasifikasi == 'TKTL' ? $totalhariall_shift_2 : 0;
                    $total_all_hari_shift_2_mp += $d->id_perusahaan == 'MP' ? $totalhariall_shift_2 : 0;
                    $total_all_hari_shift_2_pcf += $d->id_perusahaan == 'PCF' ? $totalhariall_shift_2 : 0;
                    
                    $total_all_premi_shift_2 += $totalpremiall_shift_2;
                    $total_all_premi_shift_2_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $totalpremiall_shift_2 : 0;
                    $total_all_premi_shift_2_penjualan += $d->klasifikasi == 'PENJUALAN' ? $totalpremiall_shift_2 : 0;
                    $total_all_premi_shift_2_tkl += $d->klasifikasi == 'TKL' ? $totalpremiall_shift_2 : 0;
                    $total_all_premi_shift_2_tktl += $d->klasifikasi == 'TKTL' ? $totalpremiall_shift_2 : 0;
                    $total_all_premi_shift_2_mp += $d->id_perusahaan == 'MP' ? $totalpremiall_shift_2 : 0;
                    $total_all_premi_shift_2_pcf += $d->id_perusahaan == 'PCF' ? $totalpremiall_shift_2 : 0;
                    
                    $total_all_hari_shift_3 += $totalhariall_shift_3;
                    $total_all_hari_shift_3_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $totalhariall_shift_3 : 0;
                    $total_all_hari_shift_3_penjualan += $d->klasifikasi == 'PENJUALAN' ? $totalhariall_shift_3 : 0;
                    $total_all_hari_shift_3_tkl += $d->klasifikasi == 'TKL' ? $totalhariall_shift_3 : 0;
                    $total_all_hari_shift_3_tktl += $d->klasifikasi == 'TKTL' ? $totalhariall_shift_3 : 0;
                    $total_all_hari_shift_3_mp += $d->id_perusahaan == 'MP' ? $totalhariall_shift_3 : 0;
                    $total_all_hari_shift_3_pcf += $d->id_perusahaan == 'PCF' ? $totalhariall_shift_3 : 0;
                    
                    $total_all_premi_shift_3 += $totalpremiall_shift_3;
                    $total_all_premi_shift_3_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $totalpremiall_shift_3 : 0;
                    $total_all_premi_shift_3_penjualan += $d->klasifikasi == 'PENJUALAN' ? $totalpremiall_shift_3 : 0;
                    $total_all_premi_shift_3_tkl += $d->klasifikasi == 'TKL' ? $totalpremiall_shift_3 : 0;
                    $total_all_premi_shift_3_tktl += $d->klasifikasi == 'TKTL' ? $totalpremiall_shift_3 : 0;
                    $total_all_premi_shift_3_mp += $d->id_perusahaan == 'MP' ? $totalpremiall_shift_3 : 0;
                    $total_all_premi_shift_3_pcf += $d->id_perusahaan == 'PCF' ? $totalpremiall_shift_3 : 0;
                    
                    $total_all_bruto += $bruto;
                    $total_all_bruto_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $bruto : 0;
                    $total_all_bruto_penjualan += $d->klasifikasi == 'PENJUALAN' ? $bruto : 0;
                    $total_all_bruto_tkl += $d->klasifikasi == 'TKL' ? $bruto : 0;
                    $total_all_bruto_tktl += $d->klasifikasi == 'TKTL' ? $bruto : 0;
                    $total_all_bruto_mp += $d->id_perusahaan == 'MP' ? $bruto : 0;
                    $total_all_bruto_pcf += $d->id_perusahaan == 'PCF' ? $bruto : 0;
                    
                    $total_all_potongan_jam += $totalpotonganjam;
                    $total_all_potonganjam_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $totalpotonganjam : 0;
                    $total_all_potonganjam_penjualan += $d->klasifikasi == 'PENJUALAN' ? $totalpotonganjam : 0;
                    $total_all_potonganjam_tkl += $d->klasifikasi == 'TKL' ? $totalpotonganjam : 0;
                    $total_all_potonganjam_tktl += $d->klasifikasi == 'TKTL' ? $totalpotonganjam : 0;
                    $total_all_potonganjam_mp += $d->id_perusahaan == 'MP' ? $totalpotonganjam : 0;
                    $total_all_potonganjam_pcf += $d->id_perusahaan == 'PCF' ? $totalpotonganjam : 0;
                    
                    $total_all_bpjskesehatan += $bpjskesehatan;
                    $total_all_bpjskesehatan_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $bpjskesehatan : 0;
                    $total_all_bpjskesehatan_penjualan += $d->klasifikasi == 'PENJUALAN' ? $bpjskesehatan : 0;
                    $total_all_bpjskesehatan_tkl += $d->klasifikasi == 'TKL' ? $bpjskesehatan : 0;
                    $total_all_bpjskesehatan_tktl += $d->klasifikasi == 'TKTL' ? $bpjskesehatan : 0;
                    $total_all_bpjskesehatan_mp += $d->id_perusahaan == 'MP' ? $bpjskesehatan : 0;
                    $total_all_bpjskesehatan_pcf += $d->id_perusahaan == 'PCF' ? $bpjskesehatan : 0;
                    
                    $total_all_bpjstk += $bpjstenagakerja;
                    $total_all_bpjstk_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $bpjstenagakerja : 0;
                    $total_all_bpjstk_penjualan += $d->klasifikasi == 'PENJUALAN' ? $bpjstenagakerja : 0;
                    $total_all_bpjstk_tkl += $d->klasifikasi == 'TKL' ? $bpjstenagakerja : 0;
                    $total_all_bpjstk_tktl += $d->klasifikasi == 'TKTL' ? $bpjstenagakerja : 0;
                    $total_all_bpjstk_mp += $d->id_perusahaan == 'MP' ? $bpjstenagakerja : 0;
                    $total_all_bpjstk_pcf += $d->id_perusahaan == 'PCF' ? $bpjstenagakerja : 0;
                    
                    $total_all_denda += $totaldenda;
                    $total_all_denda_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $totaldenda : 0;
                    $total_all_denda_penjualan += $d->klasifikasi == 'PENJUALAN' ? $totaldenda : 0;
                    $total_all_denda_tkl += $d->klasifikasi == 'TKL' ? $totaldenda : 0;
                    $total_all_denda_tktl += $d->klasifikasi == 'TKTL' ? $totaldenda : 0;
                    $total_all_denda_mp += $d->id_perusahaan == 'MP' ? $totaldenda : 0;
                    $total_all_denda_pcf += $d->id_perusahaan == 'PCF' ? $totaldenda : 0;
                    
                    $total_all_pjp += $d->cicilan_pjp;
                    $total_all_pjp_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $d->cicilan_pjp : 0;
                    $total_all_pjp_penjualan += $d->klasifikasi == 'PENJUALAN' ? $d->cicilan_pjp : 0;
                    $total_all_pjp_tkl += $d->klasifikasi == 'TKL' ? $d->cicilan_pjp : 0;
                    $total_all_pjp_tktl += $d->klasifikasi == 'TKTL' ? $d->cicilan_pjp : 0;
                    $total_all_pjp_mp += $d->id_perusahaan == 'MP' ? $d->cicilan_pjp : 0;
                    $total_all_pjp_pcf += $d->id_perusahaan == 'PCF' ? $d->cicilan_pjp : 0;
                    
                    $total_all_kasbon += $d->jml_kasbon;
                    $total_all_kasbon_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $d->jml_kasbon : 0;
                    $total_all_kasbon_penjualan += $d->klasifikasi == 'PENJUALAN' ? $d->jml_kasbon : 0;
                    $total_all_kasbon_tkl += $d->klasifikasi == 'TKL' ? $d->jml_kasbon : 0;
                    $total_all_kasbon_tktl += $d->klasifikasi == 'TKTL' ? $d->jml_kasbon : 0;
                    $total_all_kasbon_mp += $d->id_perusahaan == 'MP' ? $d->jml_kasbon : 0;
                    $total_all_kasbon_pcf += $d->id_perusahaan == 'PCF' ? $d->jml_kasbon : 0;
                    
                    $total_all_spip += $spip;
                    $total_all_spip_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $spip : 0;
                    $total_all_spip_penjualan += $d->klasifikasi == 'PENJUALAN' ? $spip : 0;
                    $total_all_spip_tkl += $d->klasifikasi == 'TKL' ? $spip : 0;
                    $total_all_spip_tktl += $d->klasifikasi == 'TKTL' ? $spip : 0;
                    $total_all_spip_mp += $d->id_perusahaan == 'MP' ? $spip : 0;
                    $total_all_spip_pcf += $d->id_perusahaan == 'PCF' ? $spip : 0;
                    
                    $total_all_potongan += $potongan;
                    $total_all_potongan_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $potongan : 0;
                    $total_all_potongan_penjualan += $d->klasifikasi == 'PENJUALAN' ? $potongan : 0;
                    $total_all_potongan_tkl += $d->klasifikasi == 'TKL' ? $potongan : 0;
                    $total_all_potongan_tktl += $d->klasifikasi == 'TKTL' ? $potongan : 0;
                    $total_all_potongan_mp += $d->id_perusahaan == 'MP' ? $potongan : 0;
                    $total_all_potongan_pcf += $d->id_perusahaan == 'PCF' ? $potongan : 0;
                    
                    $total_all_bersih += $jmlbersih;
                    $total_all_bersih_administrasi += $d->klasifikasi == 'ADMINISTRASI' ? $jmlbersih : 0;
                    $total_all_bersih_penjualan += $d->klasifikasi == 'PENJUALAN' ? $jmlbersih : 0;
                    $total_all_bersih_tkl += $d->klasifikasi == 'TKL' ? $jmlbersih : 0;
                    $total_all_bersih_tktl += $d->klasifikasi == 'TKTL' ? $jmlbersih : 0;
                    $total_all_bersih_mp += $d->id_perusahaan == 'MP' ? $jmlbersih : 0;
                    $total_all_bersih_pcf += $d->id_perusahaan == 'PCF' ? $jmlbersih : 0;
                    ?>
                @endforeach
                <tr>
                    <td>ADMINISTRASI</td>
                    <td style="text-align:right">{{ rupiah($total_gajipokok_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_jabatan_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_masakerja_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_tanggungjawab_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_makan_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_istri_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_skillkhusus_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_masakerja_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_lembur_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_penempatan_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_kpi_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_im_ruanglingkup_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_im_penempatan_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_im_kinerja_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_insentif_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_jamkerja_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upahperjam_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_overtime_1_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_ot_1_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_overtime_2_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_ot_2_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_overtime_libur_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_overtime_libur_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_upah_otl_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_hari_shift_2_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_premi_shift_2_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_hari_shift_3_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_premi_shift_3_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_bruto_administrasi) }}</td>
                    <td style="text-align:right">{{ desimal($total_all_potonganjam_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_bpjskesehatan_administrasi) }}</td>
                    <th></th>
                    <td style="text-align:right">{{ rupiah($total_all_bpjstk_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_denda_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_pjp_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_kasbon_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_spip_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_potongan_administrasi) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_bersih_administrasi) }}</td>
                </tr>
                <tr>
                    <td>PENJUALAN</td>
                    <td style="text-align:right">{{ rupiah($total_gajipokok_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_jabatan_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_masakerja_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_tanggungjawab_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_makan_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_istri_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_skillkhusus_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_masakerja_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_lembur_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_penempatan_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_kpi_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_im_ruanglingkup_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_im_penempatan_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_im_kinerja_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_insentif_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_jamkerja_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upahperjam_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_overtime_1_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_ot_1_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_overtime_2_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_ot_2_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_overtime_libur_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_overtime_libur_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_upah_otl_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_hari_shift_2_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_premi_shift_2_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_hari_shift_3_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_premi_shift_3_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_bruto_penjualan) }}</td>
                    <td style="text-align:right">{{ desimal($total_all_potonganjam_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_bpjskesehatan_penjualan) }}</td>
                    <th></th>
                    <td style="text-align:right">{{ rupiah($total_all_bpjstk_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_denda_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_pjp_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_kasbon_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_spip_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_potongan_penjualan) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_bersih_penjualan) }}</td>
                </tr>
                <tr>
                    <td>TKL</td>
                    <td style="text-align:right">{{ rupiah($total_gajipokok_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_jabatan_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_masakerja_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_tanggungjawab_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_makan_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_istri_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_skillkhusus_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_masakerja_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_lembur_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_penempatan_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_kpi_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_im_ruanglingkup_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_im_penempatan_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_im_kinerja_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_insentif_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_jamkerja_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upahperjam_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_overtime_1_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_ot_1_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_overtime_2_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_ot_2_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_overtime_libur_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_overtime_libur_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_upah_otl_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_hari_shift_2_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_premi_shift_2_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_hari_shift_3_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_premi_shift_3_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_bruto_tkl) }}</td>
                    <td style="text-align:right">{{ desimal($total_all_potonganjam_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_bpjskesehatan_tkl) }}</td>
                    <th></th>
                    <td style="text-align:right">{{ rupiah($total_all_bpjstk_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_denda_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_pjp_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_kasbon_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_spip_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_potongan_tkl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_bersih_tkl) }}</td>
                </tr>
                <tr>
                    <td>TKTL</td>
                    <td style="text-align:right">{{ rupiah($total_gajipokok_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_jabatan_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_masakerja_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_tanggungjawab_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_makan_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_istri_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_skillkhusus_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_masakerja_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_lembur_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_penempatan_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_kpi_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_im_ruanglingkup_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_im_penempatan_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_im_kinerja_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_insentif_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_jamkerja_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upahperjam_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_overtime_1_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_ot_1_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_overtime_2_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_ot_2_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_overtime_libur_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_overtime_libur_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_upah_otl_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_hari_shift_2_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_premi_shift_2_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_hari_shift_3_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_premi_shift_3_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_bruto_tktl) }}</td>
                    <td style="text-align:right">{{ desimal($total_all_potonganjam_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_bpjskesehatan_tktl) }}</td>
                    <th></th>
                    <td style="text-align:right">{{ rupiah($total_all_bpjstk_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_denda_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_pjp_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_kasbon_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_spip_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_potongan_tktl) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_bersih_tktl) }}</td>
                </tr>
                <tr>
                    <th>TOTAL</th>
                    @php
                        $total_gajipokok_rekap = $total_gajipokok_administrasi + $total_gajipokok_penjualan + $total_gajipokok_tkl + $total_gajipokok_tktl;

                        $total_t_jabatan_rekap = $total_t_jabatan_administrasi + $total_t_jabatan_penjualan + $total_t_jabatan_tkl + $total_t_jabatan_tktl;

                        $total_t_masakerja_rekap = $total_t_masakerja_administrasi + $total_t_masakerja_penjualan + $total_t_masakerja_tkl + $total_t_masakerja_tktl;

                        $total_t_tanggungjawab_rekap = $total_t_tanggungjawab_administrasi + $total_t_tanggungjawab_penjualan + $total_t_tanggungjawab_tkl + $total_t_tanggungjawab_tktl;

                        $total_t_makan_rekap = $total_t_makan_administrasi + $total_t_makan_penjualan + $total_t_makan_tkl + $total_t_makan_tktl;

                        $total_t_istri_rekap = $total_t_istri_administrasi + $total_t_istri_penjualan + $total_t_istri_tkl + $total_t_istri_tktl;

                        $total_t_skillkhusus_rekap = $total_t_skillkhusus_administrasi + $total_t_skillkhusus_penjualan + $total_t_skillkhusus_tkl + $total_t_skillkhusus_tktl;

                        $total_i_masakerja_rekap = $total_i_masakerja_administrasi + $total_i_masakerja_penjualan + $total_i_masakerja_tkl + $total_i_masakerja_tktl;

                        $total_i_lembur_rekap = $total_i_lembur_administrasi + $total_i_lembur_penjualan + $total_i_lembur_tkl + $total_i_lembur_tktl;

                        $total_i_penempatan_rekap = $total_i_penempatan_administrasi + $total_i_penempatan_penjualan + $total_i_penempatan_tkl + $total_i_penempatan_tktl;

                        $total_i_kpi_rekap = $total_i_kpi_administrasi + $total_i_kpi_penjualan + $total_i_kpi_tkl + $total_i_kpi_tktl;

                        $total_im_ruanglingkup_rekap = $total_im_ruanglingkup_administrasi + $total_im_ruanglingkup_penjualan + $total_im_ruanglingkup_tkl + $total_im_ruanglingkup_tktl;

                        $total_im_penempatan_rekap = $total_im_penempatan_administrasi + $total_im_penempatan_penjualan + $total_im_penempatan_tkl + $total_im_penempatan_tktl;

                        $total_im_kinerja_rekap = $total_im_kinerja_administrasi + $total_im_kinerja_penjualan + $total_im_kinerja_tkl + $total_im_kinerja_tktl;

                        $total_upah_rekap = $total_upah_administrasi + $total_upah_penjualan + $total_upah_tkl + $total_upah_tktl;

                        $total_insentif_rekap = $total_insentif_administrasi + $total_insentif_penjualan + $total_insentif_tkl + $total_insentif_tktl;

                        $total_jamkerja_rekap = $total_jamkerja_administrasi + $total_jamkerja_penjualan + $total_jamkerja_tkl + $total_jamkerja_tktl;

                        $total_upahperjam_rekap = $total_upahperjam_administrasi + $total_upahperjam_penjualan + $total_upahperjam_tkl + $total_upahperjam_tktl;

                        $total_overtime_1_rekap = $total_overtime_1_administrasi + $total_overtime_1_penjualan + $total_overtime_1_tkl + $total_overtime_1_tktl;

                        $total_upah_ot_1_rekap = $total_upah_ot_1_administrasi + $total_upah_ot_1_penjualan + $total_upah_ot_1_tkl + $total_upah_ot_1_tktl;

                        $total_overtime_2_rekap = $total_overtime_2_administrasi + $total_overtime_2_penjualan + $total_overtime_2_tkl + $total_overtime_2_tktl;

                        $total_upah_ot_2_rekap = $total_upah_ot_2_administrasi + $total_upah_ot_2_penjualan + $total_upah_ot_2_tkl + $total_upah_ot_2_tktl;

                        $total_overtime_libur_rekap = $total_overtime_libur_administrasi + $total_overtime_libur_penjualan + $total_overtime_libur_tkl + $total_overtime_libur_tktl;

                        $total_upah_overtime_libur_rekap = $total_upah_overtime_libur_administrasi + $total_upah_overtime_libur_penjualan + $total_upah_overtime_libur_tkl + $total_upah_overtime_libur_tktl;

                        $total_all_upah_otl_rekap = $total_all_upah_otl_administrasi + $total_all_upah_otl_penjualan + $total_all_upah_otl_tkl + $total_all_upah_otl_tktl;

                        $total_all_hari_shift_2_rekap = $total_all_hari_shift_2_administrasi + $total_all_hari_shift_2_penjualan + $total_all_hari_shift_2_tkl + $total_all_hari_shift_2_tktl;

                        $total_all_premi_shift_2_rekap = $total_all_premi_shift_2_administrasi + $total_all_premi_shift_2_penjualan + $total_all_premi_shift_2_tkl + $total_all_premi_shift_2_tktl;

                        $total_all_hari_shift_3_rekap = $total_all_hari_shift_3_administrasi + $total_all_hari_shift_3_penjualan + $total_all_hari_shift_3_tkl + $total_all_hari_shift_3_tktl;

                        $total_all_premi_shift_3_rekap = $total_all_premi_shift_3_administrasi + $total_all_premi_shift_3_penjualan + $total_all_premi_shift_3_tkl + $total_all_premi_shift_3_tktl;

                        $total_all_bruto_rekap = $total_all_bruto_administrasi + $total_all_bruto_penjualan + $total_all_bruto_tkl + $total_all_bruto_tktl;

                        $total_all_potonganjam_rekap = $total_all_potonganjam_administrasi + $total_all_potonganjam_penjualan + $total_all_potonganjam_tkl + $total_all_potonganjam_tktl;

                        $total_all_bpjskesehatan_rekap = $total_all_bpjskesehatan_administrasi + $total_all_bpjskesehatan_penjualan + $total_all_bpjskesehatan_tkl + $total_all_bpjskesehatan_tktl;

                        $total_all_bpjstk_rekap = $total_all_bpjstk_administrasi + $total_all_bpjstk_penjualan + $total_all_bpjstk_tkl + $total_all_bpjstk_tktl;

                        $total_all_denda_rekap = $total_all_denda_administrasi + $total_all_denda_penjualan + $total_all_denda_tkl + $total_all_denda_tktl;

                        $total_all_pjp_rekap = $total_all_pjp_administrasi + $total_all_pjp_penjualan + $total_all_pjp_tkl + $total_all_pjp_tktl;

                        $total_all_kasbon_rekap = $total_all_kasbon_administrasi + $total_all_kasbon_penjualan + $total_all_kasbon_tkl + $total_all_kasbon_tktl;

                        $total_all_spip_rekap = $total_all_spip_administrasi + $total_all_spip_penjualan + $total_all_spip_tkl + $total_all_spip_tktl;

                        $total_all_potongan_rekap = $total_all_potongan_administrasi + $total_all_potongan_penjualan + $total_all_potongan_tkl + $total_all_potongan_tktl;

                        $total_all_bersih_rekap = $total_all_bersih_administrasi + $total_all_bersih_penjualan + $total_all_bersih_tkl + $total_all_bersih_tktl;
                    @endphp
                    <th style="text-align: right">{{ rupiah($total_gajipokok_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_t_jabatan_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_t_masakerja_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_t_tanggungjawab_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_t_makan_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_t_istri_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_t_skillkhusus_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_i_masakerja_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_i_lembur_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_i_penempatan_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_i_kpi_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_im_ruanglingkup_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_im_penempatan_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_im_kinerja_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_upah_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_insentif_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_jamkerja_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_upahperjam_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_overtime_1_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_upah_ot_1_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_overtime_2_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_upah_ot_2_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_overtime_libur_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_upah_overtime_libur_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_upah_otl_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_hari_shift_2_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_premi_shift_2_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_hari_shift_3_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_premi_shift_3_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_bruto_rekap) }}</th>
                    <th style="text-align: right">{{ desimal($total_all_potonganjam_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_bpjskesehatan_rekap) }}</th>
                    <th></th>
                    <th style="text-align: right">{{ rupiah($total_all_bpjstk_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_denda_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_pjp_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_kasbon_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_spip_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_potongan_rekap) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_bersih_rekap) }}</th>

                </tr>

            </tbody>
        </table>
    </div>

    <div class="freeze-table">
        <table class="datatable3" style="width: 280%">
            <thead bgcolor="#024a75" style="color:white; font-size:12;">
                <tr bgcolor="#024a75" style="color:white; font-size:12;">
                    <th rowspan="2">PERUSAHAAN</th>
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
                    <th rowspan="2" style="background-color: black;">SPIP</th>
                    <th rowspan="2" style="background-color: orange;">JUMLAH<br>POTONGAN</th>
                    <th rowspan="2" style="background-color: orange;">JUMLAH<br>BERSIH</th>

                </tr>
                <tr>
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
                <tr>
                    <td>MAKMUR PERMATA</td>
                    <td style="text-align:right">{{ rupiah($total_gajipokok_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_jabatan_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_masakerja_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_tanggungjawab_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_makan_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_istri_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_skillkhusus_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_masakerja_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_lembur_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_penempatan_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_kpi_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_im_ruanglingkup_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_im_penempatan_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_im_kinerja_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_insentif_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_jamkerja_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upahperjam_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_overtime_1_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_ot_1_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_overtime_2_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_ot_2_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_overtime_libur_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_overtime_libur_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_upah_otl_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_hari_shift_2_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_premi_shift_2_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_hari_shift_3_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_premi_shift_3_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_bruto_mp) }}</td>
                    <td style="text-align:right">{{ desimal($total_all_potonganjam_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_bpjskesehatan_mp) }}</td>
                    <th></th>
                    <td style="text-align:right">{{ rupiah($total_all_bpjstk_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_denda_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_pjp_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_kasbon_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_spip_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_potongan_mp) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_bersih_mp) }}</td>
                </tr>
                <tr>
                    <td>PACIFIC</td>
                    <td style="text-align:right">{{ rupiah($total_gajipokok_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_jabatan_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_masakerja_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_tanggungjawab_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_makan_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_istri_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_t_skillkhusus_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_masakerja_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_lembur_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_penempatan_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_i_kpi_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_im_ruanglingkup_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_im_penempatan_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_im_kinerja_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_insentif_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_jamkerja_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upahperjam_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_overtime_1_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_ot_1_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_overtime_2_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_ot_2_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_overtime_libur_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_upah_overtime_libur_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_upah_otl_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_hari_shift_2_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_premi_shift_2_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_hari_shift_3_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_premi_shift_3_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_bruto_pcf) }}</td>
                    <td style="text-align:right">{{ desimal($total_all_potonganjam_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_bpjskesehatan_pcf) }}</td>
                    <th></th>
                    <td style="text-align:right">{{ rupiah($total_all_bpjstk_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_denda_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_pjp_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_kasbon_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_spip_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_potongan_pcf) }}</td>
                    <td style="text-align:right">{{ rupiah($total_all_bersih_pcf) }}</td>
                </tr>
                <tr>
                    <th>TOTAL</th>
                    @php

                        $total_gajipokok_perusahaan = $total_gajipokok_mp + $total_gajipokok_pcf;

                        $total_t_jabatan_perusahaan = $total_t_jabatan_mp + $total_t_jabatan_pcf;

                        $total_t_masakerja_perusahaan = $total_t_masakerja_mp + $total_t_masakerja_pcf;

                        $total_t_tanggungjawab_perusahaan = $total_t_tanggungjawab_mp + $total_t_tanggungjawab_pcf;

                        $total_t_makan_perusahaan = $total_t_makan_mp + $total_t_makan_pcf;

                        $total_t_istri_perusahaan = $total_t_istri_mp + $total_t_istri_pcf;

                        $total_t_skillkhusus_perusahaan = $total_t_skillkhusus_mp + $total_t_skillkhusus_pcf;

                        $total_i_masakerja_perusahaan = $total_i_masakerja_mp + $total_i_masakerja_pcf;

                        $total_i_lembur_perusahaan = $total_i_lembur_mp + $total_i_lembur_pcf;

                        $total_i_penempatan_perusahaan = $total_i_penempatan_mp + $total_i_penempatan_pcf;

                        $total_i_kpi_perusahaan = $total_i_kpi_mp + $total_i_kpi_pcf;

                        $total_im_ruanglingkup_perusahaan = $total_im_ruanglingkup_mp + $total_im_ruanglingkup_pcf;

                        $total_im_penempatan_perusahaan = $total_im_penempatan_mp + $total_im_penempatan_pcf;

                        $total_im_kinerja_perusahaan = $total_im_kinerja_mp + $total_im_kinerja_pcf;

                        $total_upah_perusahaan = $total_upah_mp + $total_upah_pcf;

                        $total_insentif_perusahaan = $total_insentif_mp + $total_insentif_pcf;

                        $total_jamkerja_perusahaan = $total_jamkerja_mp + $total_jamkerja_pcf;

                        $total_upahperjam_perusahaan = $total_upahperjam_mp + $total_upahperjam_pcf;

                        $total_overtime_1_perusahaan = $total_overtime_1_mp + $total_overtime_1_pcf;

                        $total_upah_ot_1_perusahaan = $total_upah_ot_1_mp + $total_upah_ot_1_pcf;

                        $total_overtime_2_perusahaan = $total_overtime_2_mp + $total_overtime_2_pcf;

                        $total_upah_ot_2_perusahaan = $total_upah_ot_2_mp + $total_upah_ot_2_pcf;

                        $total_overtime_libur_perusahaan = $total_overtime_libur_mp + $total_overtime_libur_pcf;

                        $total_upah_overtime_libur_perusahaan = $total_upah_overtime_libur_mp + $total_upah_overtime_libur_pcf;

                        $total_all_upah_otl_perusahaan = $total_all_upah_otl_mp + $total_all_upah_otl_pcf;

                        $total_all_hari_shift_2_perusahaan = $total_all_hari_shift_2_mp + $total_all_hari_shift_2_pcf;

                        $total_all_premi_shift_2_perusahaan = $total_all_premi_shift_2_mp + $total_all_premi_shift_2_pcf;

                        $total_all_hari_shift_3_perusahaan = $total_all_hari_shift_3_mp + $total_all_hari_shift_3_pcf;

                        $total_all_premi_shift_3_perusahaan = $total_all_premi_shift_3_mp + $total_all_premi_shift_3_pcf;

                        $total_all_bruto_perusahaan = $total_all_bruto_mp + $total_all_bruto_pcf;

                        $total_all_potonganjam_perusahaan = $total_all_potonganjam_mp + $total_all_potonganjam_pcf;

                        $total_all_bpjskesehatan_perusahaan = $total_all_bpjskesehatan_mp + $total_all_bpjskesehatan_pcf;

                        $total_all_bpjstk_perusahaan = $total_all_bpjstk_mp + $total_all_bpjstk_pcf;

                        $total_all_denda_perusahaan = $total_all_denda_mp + $total_all_denda_pcf;

                        $total_all_pjp_perusahaan = $total_all_pjp_mp + $total_all_pjp_pcf;

                        $total_all_kasbon_perusahaan = $total_all_kasbon_mp + $total_all_kasbon_pcf;

                        $total_all_spip_perusahaan = $total_all_spip_mp + $total_all_spip_pcf;

                        $total_all_potongan_perusahaan = $total_all_potongan_mp + $total_all_potongan_pcf;

                        $total_all_bersih_perusahaan = $total_all_bersih_mp + $total_all_bersih_pcf;
                    @endphp

                    <th style="text-align: right">{{ rupiah($total_gajipokok_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_t_jabatan_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_t_masakerja_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_t_tanggungjawab_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_t_makan_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_t_istri_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_t_skillkhusus_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_i_masakerja_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_i_lembur_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_i_penempatan_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_i_kpi_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_im_ruanglingkup_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_im_penempatan_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_im_kinerja_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_upah_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_insentif_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_jamkerja_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_upahperjam_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_overtime_1_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_upah_ot_1_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_overtime_2_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_upah_ot_2_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_overtime_libur_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_upah_overtime_libur_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_upah_otl_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_hari_shift_2_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_premi_shift_2_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_hari_shift_3_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_premi_shift_3_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_bruto_perusahaan) }}</th>
                    <th style="text-align: right">{{ desimal($total_all_potonganjam_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_bpjskesehatan_perusahaan) }}</th>
                    <th></th>
                    <th style="text-align: right">{{ rupiah($total_all_bpjstk_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_denda_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_pjp_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_kasbon_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_spip_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_potongan_perusahaan) }}</th>
                    <th style="text-align: right">{{ rupiah($total_all_bersih_perusahaan) }}</th>

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
            'scrollable': true,
            'columnNum': 1,
            'shadow': true,
        });
    });
</script>

</html>
