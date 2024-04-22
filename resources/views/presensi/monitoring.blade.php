@extends('layouts.midone')
@section('titlepage', 'Monitoring Presesensi')
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
                            $day = date('D', strtotime(date('Y-m-d')));
                            $dayList = [
                                'Sun' => 'Minggu',
                                'Mon' => 'Senin',
                                'Tue' => 'Selasa',
                                'Wed' => 'Rabu',
                                'Thu' => 'Kamis',
                                'Fri' => 'Jumat',
                                'Sat' => 'Sabtu',
                            ];
                            $namahari = $dayList[$day];
                        @endphp
                        <h1 style="font-size:40px; font-family:arial; margin-right:10px;">{{ $namahari }},
                            {{ DateToIndo2(date('Y-m-d')) }}</h1>
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
                                            $level_search = ['admin', 'manager hrd', 'spv presensi', 'manager accounting', 'direktur'];
                                        @endphp
                                        @if (Auth::user()->kode_cabang == 'PCF' && in_array($level, $level_search))
                                            <div class="col-3">
                                                <x-inputtext label="Tanggal" field="tanggal" icon="feather icon-calendar"
                                                    datepicker value="{{ Request('tanggal') }}" />
                                            </div>
                                            <div class="col-lg-2 col-sm-12">
                                                <x-inputtext label="Nama Karyawan" field="nama_karyawan_search"
                                                    icon="feather icon-users"
                                                    value="{{ Request('nama_karyawan_search') }}" />
                                            </div>
                                            <div class="col-lg-2 col-sm-12">
                                                <div class="form-group">
                                                    <select name="kode_dept_search" id="kode_dept_search"
                                                        class="form-control">
                                                        <option value="">Departemen</option>
                                                        @foreach ($departemen as $d)
                                                            <option
                                                                {{ Request('kode_dept_search') == $d->kode_dept ? 'selected' : '' }}
                                                                value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-1 col-sm-12">
                                                <div class="form-group">
                                                    <select name="id_perusahaan_search" id="id_perusahaan_search"
                                                        class="form-control">
                                                        <option value="">MP/PCF</option>
                                                        <option value="MP"
                                                            {{ Request('id_perusahaan_search') == 'MP' ? 'selected' : '' }}>
                                                            MP</option>
                                                        <option value="PCF"
                                                            {{ Request('id_perusahaan_search') == 'PCF' ? 'selected' : '' }}>
                                                            PCF</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-sm-12">
                                                <div class="form-group">
                                                    <select name="id_kantor_search" id="id_kantor_search"
                                                        class="form-control">
                                                        <option value="">Kantor</option>
                                                        @foreach ($kantor as $d)
                                                            <option
                                                                {{ Request('id_kantor_search') == $d->kode_cabang ? 'selected' : '' }}
                                                                value="{{ $d->kode_cabang }}">{{ $d->kode_cabang }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="col-lg-2 col-sm-12">
                                                <div class="form-group">
                                                    <select name="grup_search" id="grup_search" class="form-control">
                                                        <option value="">Grup</option>
                                                        @foreach ($group as $d)
                                                            <option
                                                                {{ Request('grup_search') == $d->id ? 'selected' : '' }}
                                                                value="{{ $d->id }}">{{ $d->nama_group }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @else
                                            @if (Auth::user()->kode_dept_presensi == 'PRD')
                                                <div class="col-3">
                                                    <x-inputtext label="Tanggal" field="tanggal"
                                                        icon="feather icon-calendar" datepicker
                                                        value="{{ Request('tanggal') }}" />
                                                </div>
                                                <div class="col-lg-6 col-sm-12">
                                                    <x-inputtext label="Nama Karyawan" field="nama_karyawan_search"
                                                        icon="feather icon-users"
                                                        value="{{ Request('nama_karyawan_search') }}" />
                                                </div>
                                                <div class="col-lg-3 col-sm-12">
                                                    <div class="form-group">
                                                        <select name="grup_search" id="grup_search" class="form-control">
                                                            <option value="">Grup</option>
                                                            @foreach ($group as $d)
                                                                <option
                                                                    {{ Request('grup_search') == $d->id ? 'selected' : '' }}
                                                                    value="{{ $d->id }}">{{ $d->nama_group }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-3">
                                                    <x-inputtext label="Tanggal" field="tanggal"
                                                        icon="feather icon-calendar" datepicker
                                                        value="{{ Request('tanggal') }}" />
                                                </div>
                                                <div class="col-lg-9 col-sm-12">
                                                    <x-inputtext label="Nama Karyawan" field="nama_karyawan_search"
                                                        icon="feather icon-users"
                                                        value="{{ Request('nama_karyawan_search') }}" />
                                                </div>
                                            @endif

                                        @endif

                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary btn-block"><i
                                                    class="fa fa-search mr-1"></i> Get Data</button>
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
                                                <th>#</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $jmlsid = 0;
                                            @endphp
                                            @foreach ($karyawan as $d)
                                                @php
                                                    $kode_dept = $d->kode_dept;
                                                    $tgl_presensi = $d->tgl_presensi; // Mengambil Value Tanggal
                                                    $nama_jadwal = $d->nama_jadwal; // Nama Jadwal
                                                    $status = $d->status_presensi; // Status Presensi
                                                    $lintashari = $d->lintashari; // Status Lintas Hari
                                                    $izinpulangdirut = $d->izinpulangdirut; // Izin Pulang Approve Direktur
                                                    $keperluankeluar = $d->keperluan; //Izin Pulang Persetujuan Dirut
                                                    $izinabsendirut = $d->izinabsendirut; // Izin Absen Persetujuan Dirut
                                                    $izinterlambatdirut = $d->izinterlambatdirut; // Izin Absen Persetujuan Dirut

                                                    //Proses Perhitungan Masa Kerja
                                                    $start_kerja = date_create($d->tgl_masuk); //Tanggal Masuk Kerja
                                                    $end_kerja = date_create($tgl_presensi); // Tanggal Presensi
                                                    $diff = date_diff($start_kerja, $end_kerja); //Hitung Masa Kerja
                                                    $cekmasakerja = $diff->y * 12 + $diff->m; // Value Masa Kerja

                                                    $tgllibur = "'" . $tgl_presensi . "'"; // Tanggal Libur

                                                    //Parameter Pencarian Data Libur
                                                    $search_items = [
                                                        'nik' => $d->nik,
                                                        'id_kantor' => $d->id_kantor,
                                                        'tanggal_libur' => $tgl_presensi,
                                                    ];

                                                    //Parameter Pencarian Data Lembur
                                                    $search_items_lembur = [
                                                        'nik' => $d->nik,
                                                        'id_kantor' => $d->id_kantor,
                                                        'tanggal_lembur' => $tgl_presensi,
                                                    ];

                                                    //Parameter Penggantian Libur Minggu
                                                    $search_items_minggumasuk = [
                                                        'nik' => $d->nik,
                                                        'id_kantor' => $d->id_kantor,
                                                        'tanggal_diganti' => $tgl_presensi,
                                                    ];

                                                    //Parameter Pencarian Data Libur
                                                    $search_items_all = [
                                                        'nik' => 'ALL',
                                                        'id_kantor' => $d->id_kantor,
                                                        'tanggal_libur' => $tgl_presensi,
                                                    ];

                                                    $ceklibur = cektgllibur($datalibur, $search_items); // Cek Libur Nasional
                                                    $cekliburpenggantiminggu = cektgllibur($dataliburpenggantiminggu, $search_items); // Cek Libur Pengganti Minggu
                                                    $cekminggumasuk = cektgllibur($dataminggumasuk, $search_items_minggumasuk); // Cek Minggu Masuk
                                                    $cekwfh = cektgllibur($datawfh, $search_items); // Cek Dirumahkan
                                                    $cekwfhfull = cektgllibur($datawfhfull, $search_items); // Cek WFH
                                                    $ceklembur = cektgllibur($datalembur, $search_items_lembur); // Cek Lembur

                                                    //Menghitung Jumlah Jam Dirumahkan
                                                    $namahari = hari($tgl_presensi); // Cek Nama Hari
                                                    if ($namahari == 'Sabtu') {
                                                        $jamdirumahkan = 5;
                                                    } else {
                                                        $jamdirumahkan = 7;
                                                    }

                                                    //Jika Shift 3
                                                    if (!empty($lintashari)) {
                                                        $tgl_pulang = date('Y-m-d', strtotime('+1 day', strtotime($tgl_presensi))); // Tanggal Pulang adalah Tanggal Berikutnya
                                                    } else {
                                                        $tgl_pulang = $tgl_presensi; // Tanggal Pulang adalah Tanggal Presensi
                                                    }

                                                    //Jam Masuk Presensi
                                                    $jam_in = !empty($d->jam_in) ? $d->jam_in : 'NA'; // Y-m-d H:i:s Jam dan Tanggal Karyawan Melakukan Presensi Masuk
                                                    $jam_in_presensi = $jam_in != 'NA' ? date('H:i', strtotime($d->jam_in)) : ''; // Jam Presensi Masuk
                                                    $jam_in_tanggal = $jam_in != 'NA' ? date('Y-m-d H:i', strtotime($jam_in)) : ''; // Jam Tgl Masuk Presensi

                                                    //Jam Pulang Presensi
                                                    $jam_out = !empty($d->jam_out) ? $d->jam_out : 'NA'; // Y-m-d H:i:s Jam dan Tangal Karyawan Melakukan Presensi Pulang
                                                    $jam_out_presensi = $jam_out != 'NA' ? date('H:i', strtotime($jam_out)) : ''; //Jam Presensi Pulang
                                                    $jam_out_tanggal = $jam_out != 'NA' ? date('Y-m-d H:i', strtotime($jam_out)) : ''; //Jam Tgl Presensi Pulang

                                                    if ($namahari == 'Minggu') {
                                                        if (!empty($cekminggumasuk)) {
                                                            if ($d->nama_jabatan == 'SPG' || $d->nama_jabatan == 'SPB') {
                                                                $jam_masuk = $jam_in_presensi;
                                                                $jam_masuk_tanggal = $tgl_presensi . ' ' . $jam_masuk;
                                                            } else {
                                                                $jam_masuk = date('H:i', strtotime($d->jam_masuk));
                                                                $jam_masuk_tanggal = $tgl_presensi . ' ' . $jam_masuk;
                                                            }

                                                            if ($d->nama_jabatan == 'SPG' || $d->nama_jabatan == 'SPB') {
                                                                $jam_pulang = !empty($jam_out_presensi) ? $jam_out_presensi : '';
                                                                $jam_pulang_tanggal = !empty($jam_out_presensi) ? $tgl_pulang . ' ' . $jam_pulang : '';
                                                            } else {
                                                                $jam_pulang = !empty($d->jam_pulang) ? date('H:i', strtotime($d->jam_pulang)) : '';
                                                                $jam_pulang_tanggal = !empty($d->jam_pulang) ? $tgl_pulang . ' ' . $jam_pulang : '';
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
                                                                $jam_masuk = date('H:i', strtotime($d->jam_masuk));
                                                                $jam_masuk_tanggal = $tgl_presensi . ' ' . $jam_masuk;
                                                            }

                                                            if ($d->nama_jabatan == 'SPG' || $d->nama_jabatan == 'SPB' || !empty($cekwfh)) {
                                                                $jam_pulang = !empty($jam_out_presensi) ? $jam_out_presensi : '';
                                                                $jam_pulang_tanggal = !empty($jam_out_presensi) ? $tgl_pulang . ' ' . $jam_pulang : '';
                                                            } else {
                                                                $jam_pulang = !empty($d->jam_pulang) ? date('H:i', strtotime($d->jam_pulang)) : '';
                                                                $jam_pulang_tanggal = !empty($d->jam_pulang) ? $tgl_pulang . ' ' . $jam_pulang : '';
                                                            }
                                                        }
                                                    }

                                                    //Keluar Kantor
                                                    $jam_keluar = !empty($d->jam_keluar) ? date('H:i', strtotime($d->jam_keluar)) : ''; // Jam Keluar Kantor
                                                    $jam_masuk_kk = !empty($d->jam_masuk_kk) ? date('H:i', strtotime($d->jam_masuk_kk)) : ''; //Jam masuk Keluar Kantor

                                                    $total_jam = !empty($d->total_jam) ? $d->total_jam : 0; // Total Jam Kerja Dalam 1 Hari

                                                    //Pengajuan Izin
                                                    $sid = $d->sid; //SID
                                                    $kode_izin_terlambat = $d->kode_izin_terlambat; // Izin Terlambat
                                                    $kode_izin_pulang = $d->kode_izin_pulang; // Iizn Pulang

                                                    //Jam Istirahat
                                                    $jam_istirahat = $d->jam_istirahat;
                                                    $jam_istirahat_presensi = date('H:i', strtotime($d->jam_istirahat));
                                                    $jam_istirahat_presensi_tanggal = !empty($d->jam_istirahat) ? $tgl_pulang . ' ' . $jam_istirahat_presensi : '';

                                                    $jam_awal_istirahat = date('H:i', strtotime($d->jam_awal_istirahat));
                                                    $jam_awal_istirahat_tanggal = !empty($d->jam_awal_istirahat) ? $tgl_pulang . ' ' . $jam_awal_istirahat : '';

                                                    $jam_akhir_istirahat = date('H:i', strtotime($d->jam_istirahat));
                                                    $jam_akhir_istirahat_tanggal = !empty($d->jam_istirahat) ? $tgl_pulang . ' ' . $jam_akhir_istirahat : '';

                                                    //Menghitung Jam Keterlambatan
                                                    // echo $jam_in_tanggal . '<br>';
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

                                                    //Tambah 0 Didepan Jika < 10 pada Jam Terlambat
                                                    $jamterlambat = $jamterlambat < 0 && !empty($kode_izin_terlambat) ? 0 : $jamterlambat;

                                                    //Jam terlambat dalam Desimal
                                                    if (!empty($izinterlambatdirut) && $izinterlambatdirut == 1) {
                                                        $jt = 0;
                                                    } else {
                                                        $jt = round($jamterlambat + $desimalterlambat, 2, PHP_ROUND_HALF_DOWN);
                                                    }

                                                    $jt = !empty($jt) ? $jt : 0;

                                                    //Perhitungan Jam Keluar Kantor
                                                    if (!empty($jam_keluar)) {
                                                        $jam_keluar_tanggal = $d->jam_keluar ? $tgl_pulang . ' ' . $jam_keluar : '';
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

                                                    if ($jamkeluarkantor > 0) {
                                                        if ($keperluankeluar == 'K') {
                                                            $jk = 0;
                                                        } else {
                                                            $jk = $jamkeluarkantor + $desimaljamkeluar;
                                                        }
                                                    } else {
                                                        $jk = 0;
                                                    }

                                                    // Menghitung Denda
                                                    if (!empty($jam_in) and $kode_dept != 'MKT') {
                                                        if ($jam_in_presensi > $jam_masuk and empty($kode_izin_terlambat)) {
                                                            if ($jamterlambat < 1) {
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

                                                    //Jika Terlambat dan Mengajukan Izin
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

                                                    //Menghitung total Jam
                                                    //Menentukan Jam Absen Pulang
                                                    if ($jam_out_tanggal > $jam_awal_istirahat_tanggal && $jam_out_tanggal <= $jam_akhir_istirahat_tanggal) {
                                                        $jout = $jam_awal_istirahat_tanggal; //Jika Pulang Lebih dari Jam Awal Istirhat dan Kurang dari Jam AKhir Istirahat
                                                    } else {
                                                        $jout = $jam_out_tanggal; // Jam Absen Pulang Sesuai Dengan Jam Absen Pulang
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

                                                    //Cek Dirumahkan / Tidak
                                                    if (!empty($cekwfh)) {
                                                        $totaljam = $jam - $jt - $jk;
                                                    } else {
                                                        $totaljam = $total_jam - $jt - $jk;
                                                    }

                                                    if ($jam_out != 'NA') {
                                                        if ($jam_out_tanggal < $jam_pulang_tanggal) {
                                                            //Shift 3 Belum Di Set | Coba
                                                            if ($jam_out_tanggal > $jam_akhir_istirahat_tanggal && $jam_istirahat != 'NA') {
                                                                $desimalmenit = ROUND($menit / 60, 2);
                                                                //$desimalmenit = ROUND(($menit * 100) / 60);
                                                                $grandtotaljam = $jam - 1 + $desimalmenit;
                                                            } else {
                                                                $desimalmenit = ROUND($menit / 60, 2);
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

                                                    if ($jam_in == 'NA') {
                                                        if ($status == 'i') {
                                                            $grandtotaljam = 0;
                                                        } elseif ($status == 's') {
                                                            if (!empty($sid)) {
                                                                $jmlsid += 1;
                                                                $grandtotaljam = $jamdirumahkan;
                                                                $ceksid = 1;
                                                                if (!empty($cekwfh)) {
                                                                    $ceksid = 2;
                                                                    $grandtotaljam = $grandtotaljam / 2;
                                                                }
                                                            } else {
                                                                $grandtotaljam = 0;
                                                            }
                                                        } elseif ($status == 'c') {
                                                            $grandtotaljam = $jamdirumahkan;
                                                            // if(!empty($cekwfh)){
                                                            //     $grandtotaljam = $grandtotaljam / 2 ;
                                                            // }
                                                        } else {
                                                            $grandtotaljam = 0;
                                                        }
                                                    }

                                                    //Menghitung Premi
                                                    if ($nama_jadwal == 'SHIFT 2' && $grandtotaljam >= 5) {
                                                        $premi = 5000;
                                                        $premi_shift_2 = 5000;
                                                        //$totalpremi_shift_2 += $premi_shift_2;
                                                        //$totalhari_shift_2 += 1;
                                                    } elseif ($nama_jadwal == 'SHIFT 3' && $grandtotaljam >= 5) {
                                                        $premi = 6000;
                                                        $premi_shift_3 = 6000;
                                                        //$totalpremi_shift_3 += $premi_shift_3;
                                                        //$totalhari_shift_3 += 1;
                                                    } else {
                                                        $premi = 0;
                                                    }

                                                    //Menghitung Total Jam Pulang Cepat

                                                    if ($jam_out != 'NA' && $jam_out_tanggal < $jam_pulang_tanggal) {
                                                        $pc = 'Pulang Cepat';
                                                        if (!empty($izinpulangdirut) && $izinpulangdirut == 1) {
                                                            $totalpc = 0;
                                                        } else {
                                                            $totalpc = $total_jam + $jk - $grandtotaljam;
                                                            // if($totalpc <= 0.02){
                                                            //     $totalpc = 0;
                                                            // }
                                                        }
                                                    } else {
                                                        $pc = '';
                                                        $totalpc = 0;
                                                    }

                                                    //Menghitung Jumlah Tidak Hadir
                                                    if ($status == 'a') {
                                                        if ($namahari == 'Sabtu') {
                                                            $tidakhadir = 5;
                                                        } else {
                                                            $tidakhadir = 7;
                                                        }
                                                    } else {
                                                        $tidakhadir = 0; // Jika Karyawan Absen Maka $tidakhadir dihitung 0
                                                    }

                                                @endphp
                                                <tr>
                                                    <td class="text-center">
                                                        {{ $loop->iteration + $karyawan->firstItem() - 1 }}</td>
                                                    <td>{{ $d->nik }}</td>
                                                    <td>{{ $d->nama_karyawan }}</td>
                                                    <td>{{ $d->kode_dept }}</td>
                                                    <td>{{ $d->id_kantor }}</td>
                                                    <td>
                                                        @if ($jam_in != 'NA')
                                                            {{ $nama_jadwal }} {{ $d->jadwalcabang }}
                                                            ({{ $jam_masuk }}
                                                            s/d {{ $jam_pulang }})
                                                        @else
                                                            <span class="danger">Belum Absen</span>
                                                        @endif

                                                    </td>
                                                    <td>
                                                        {!! $jam_in != 'NA'
                                                            ? '<a href="#" class="showpresensi" id="' .
                                                                $d->id .
                                                                '" status="in">' .
                                                                date('d-m-y H:i', strtotime($jam_in)) .
                                                                '</a>'
                                                            : '<span class="danger">Belum Absen</span>' !!}
                                                        @if (!empty($d->kode_izin_terlambat))
                                                            (Izin)
                                                        @endif
                                                    </td>
                                                    <td style="color:{{ $jam_out_presensi < $jam_pulang ? 'red' : '' }}">
                                                        {!! $jam_out != 'NA'
                                                            ? '<a href="#" class="showpresensi" id="' .
                                                                $d->id .
                                                                '" status="out">' .
                                                                date('d-m-y H:i', strtotime($jam_out)) .
                                                                '</a>'
                                                            : '<span class="danger">Belum Absen</span>' !!}
                                                        @if (!empty($pc))
                                                            (PC)
                                                        @endif
                                                        @if (!empty($d->kode_izin_pulang))
                                                            (Izin)
                                                        @endif

                                                    </td>
                                                    <td>
                                                        @if ($status == 'h')
                                                            <span class="badge bg-success">H</span>
                                                        @elseif($status == 'i')
                                                            <span class="badge bg-info">I</span>
                                                        @elseif($status == 'a')
                                                            <span class="badge bg-danger">A</span>
                                                        @elseif($status == 'c')
                                                            <span class="badge bg-warning">C</span>
                                                        @elseif($status == 's')
                                                            @if (!empty($d->sid))
                                                                <span class="badge bg-primary">SID</span>
                                                            @else
                                                                <span class="badge bg-primary">S</span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td style="color:{{ $terlambat != 'Tepat waktu' ? 'red' : 'green' }}">
                                                        {{ $jam_in != "NA" ? $terlambat : '' }}
                                                    </td>
                                                    <td>{{ !empty($denda) ? rupiah($denda) : '' }}</td>
                                                    <td>{{ $totaljamkeluar }}</td>
                                                    <td
                                                        style="color:{{ $grandtotaljam < $d->total_jam ? 'red' : '' }}; text-align:center">
                                                        {{ $grandtotaljam > 0 ? $grandtotaljam : 0 }}
                                                        {{-- {{ $jout }} - {{ $jam_masuk_tanggal }} - {{ $j_masuk_tanggal }} --}}
                                                    </td>
                                                    <td>
                                                        @if ($level == 'manager hrd' || $level == 'spv presensi' || $level == 'admin' || Auth::user()->pic_presensi == 1)
                                                            <a href="#" class="edit" nik="{{ $d->nik }}"
                                                                kode_jadwal="{{ $d->kode_jadwal }}"><i
                                                                    class="feather icon-edit info"></i></a>
                                                            @if (!empty($d->id_presensi))
                                                            <a href="/presensi/{{ Crypt::encrypt($d->id_presensi) }}/delete" class="hapus ml-3"><i
                                                                class="feather icon-trash danger"></i></a>
                                                            @endif

                                                            <a href="#" class="checkmesin"
                                                                pin="{{ $d->pin }}"
                                                                tanggal="{{ !empty(Request('tanggal')) ? Request('tanggal') : date('Y-m-d') }}"
                                                                kode_jadwal="{{ $d->kode_jadwal }}"><i
                                                                    class="feather icon-monitor success"></i></a>
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
    <div class="modal fade text-left" id="mdlupdatepresensi" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Update Presensi Presensi <span
                            id="tglupdatepresensi"></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="loadupdatepresensi">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="mdlshowpresensi" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel18" aria-hidden="true">
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

    <div class="modal fade text-left" id="mdlcheckmesin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
        aria-hidden="true">
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
            var e = document.getElementById('jam'),
                d = new Date(),
                h, m, s;
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

            $(".checkmesin").click(function(e) {
                e.preventDefault();
                var pin = $(this).attr("pin");
                var tanggal = $(this).attr("tanggal");
                var kode_jadwal = $(this).attr("kode_jadwal");
                //alert(kode_jadwal);
                $("#mdlcheckmesin").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $.ajax({
                    type: 'POST',
                    url: '/presensi/checkmesin',
                    data: {
                        _token: "{{ csrf_token() }}",
                        pin: pin,
                        tanggal: tanggal,
                        kode_jadwal: kode_jadwal
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loadcheckmesin").html(respond);
                    }
                });
            });



            $(".edit").click(function(e) {
                e.preventDefault();
                var nik = $(this).attr('nik');
                var tanggal = "{{ Request('tanggal') }}";
                var tgl = tanggal == "" ? "{{ date('Y-m-d') }}" : tanggal;
                var kode_jadwal = $(this).attr("kode_jadwal");
                // /alert(kode_jadwal);
                $("#tglupdatepresensi").text(tgl);
                $("#mdlupdatepresensi").modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $.ajax({
                    type: 'POST',
                    url: '/presensi/updatepresensi',
                    data: {
                        _token: "{{ csrf_token() }}",
                        nik: nik,
                        tgl: tgl,
                        kode_jadwal: kode_jadwal
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loadupdatepresensi").html(respond);
                    }
                });
            });

            $(".showpresensi").click(function(e) {
                e.preventDefault();
                var id = $(this).attr("id");
                var status = $(this).attr("status");
                $("#mdlshowpresensi").modal({
                    backdrop: 'static',
                    keyboard: false
                });

                $.ajax({
                    type: 'POST',
                    url: '/presensi/show',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        status: status
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loadpresensi").html(respond);
                    }
                });
            });
        });
    </script>
@endpush
