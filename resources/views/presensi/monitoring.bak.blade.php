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
                                            $level_search = ['admin', 'manager hrd', 'manager accounting', 'direktur'];
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

                                            @foreach ($karyawan as $d)
                                                <?php
                                                //Jam In adalah Jam Ketika Melakukan Presensi
                                                // Jam Masuk adalah Jam Masuk Seharusnya
                                                $jam_in = !empty($d->jam_in) ? date('H:i', strtotime($d->jam_in)) : '';
                                                $jam_out = !empty($d->jam_out) ? date('H:i', strtotime($d->jam_out)) : '';
                                                
                                                $jam_in_tanggal = !empty($d->jam_in) ? date('Y-m-d H:i', strtotime($d->jam_in)) : '';
                                                $jam_out_tanggal = !empty($d->jam_out) ? date('Y-m-d H:i', strtotime($d->jam_out)) : '';
                                                
                                                $tgl_presensi = $d->tgl_presensi;
                                                $lintashari = $d->lintashari;
                                                if (!empty($lintashari)) {
                                                    $tgl_pulang = date('Y-m-d', strtotime('+1 day', strtotime($tgl_presensi)));
                                                } else {
                                                    $tgl_pulang = $tgl_presensi;
                                                }
                                                
                                                $jam_pulang = !empty($d->jam_pulang) ? date('H:i', strtotime($d->jam_pulang)) : '';
                                                $jam_pulang_tanggal = !empty($d->jam_pulang) ? $tgl_pulang . ' ' . $jam_pulang : '';
                                                
                                                $jam_masuk = !empty($d->jam_masuk) ? date('H:i', strtotime($d->jam_masuk)) : '';
                                                $jam_masuk_tanggal = !empty($d->jam_masuk) ? $d->tgl_presensi . ' ' . $jam_masuk : '';
                                                
                                                $search_items_minggumasuk = [
                                                    'nik' => $d->nik,
                                                    'id_kantor' => $d->id_kantor,
                                                    'tanggal_diganti' => $tgl_presensi,
                                                ];
                                                
                                                $search_items = [
                                                    'nik' => $d->nik,
                                                    'id_kantor' => $d->id_kantor,
                                                    'tanggal_libur' => $tgl_presensi,
                                                ];
                                                
                                                $cekminggumasuk = cektgllibur($dataminggumasuk, $search_items_minggumasuk);
                                                $ceklibur = cektgllibur($datalibur, $search_items);
                                                $cekliburpenggantiminggu = cektgllibur($dataliburpenggantiminggu, $search_items);
                                                $cekdirumahkan = cektgllibur($datawfh, $search_items);
                                                
                                                $day = date('D', strtotime($tgl_presensi));
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
                                                
                                                if ($namahari == 'Minggu') {
                                                    if (!empty($cekminggumasuk)) {
                                                        $j_masuk = $d->nama_jabatan == 'SPG' || $d->nama_jabatan == 'SPB' ? $jam_in : $jam_masuk;
                                                        $j_pulang = $d->nama_jabatan == 'SPG' || $d->nama_jabatan == 'SPB' ? $jam_out : $jam_pulang;
                                                    } else {
                                                        $j_masuk = $jam_in;
                                                        $j_pulang = $jam_out;
                                                    }
                                                } else {
                                                    if ($d->nama_jabatan == 'SPG' || $d->nama_jabatan == 'SPB' || !empty($cekdirumahkan)) {
                                                        $j_masuk = $jam_in;
                                                        $j_pulang = $jam_out;
                                                    } else {
                                                        $j_masuk = $jam_masuk;
                                                        $j_pulang = $jam_pulang;
                                                    }
                                                    // $j_masuk = $d->nama_jabatan=="SPG" || $d->nama_jabatan=="SPB" ? $jam_in : $jam_masuk;
                                                    // $j_pulang = $d->nama_jabatan=="SPG" || $d->nama_jabatan=="SPB" ? $jam_out : $jam_pulang;
                                                }
                                                
                                                $j_masuk_tanggal = $tgl_presensi . ' ' . $j_masuk;
                                                $j_pulang_tanggal = $tgl_presensi . ' ' . $j_pulang;
                                                
                                                $jam_istirahat = !empty($d->jam_istirahat) ? date('H:i', strtotime($d->jam_istirahat)) : '';
                                                $jam_awal_istirahat = !empty($d->jam_awal_istirahat) ? date('H:i', strtotime($d->jam_awal_istirahat)) : '';
                                                $jam_akhir_istirahat = !empty($d->jam_akhir_istirahat) ? date('H:i', strtotime($d->jam_akhir_istirahat)) : '';
                                                
                                                $jam_istirahat_tanggal = !empty($d->jam_istirahat) ? $tgl_pulang . ' ' . $jam_istirahat : '';
                                                $jam_awal_istirahat_tanggal = !empty($d->jam_awal_istirahat) ? $tgl_pulang . ' ' . $jam_awal_istirahat : '';
                                                $jam_akhir_istirahat_tanggal = !empty($d->jam_akhir_istirahat) ? $tgl_pulang . ' ' . $jam_akhir_istirahat : '';
                                                
                                                $status = $d->status_presensi;
                                                
                                                $tgl_in = date('Y-m-d', strtotime($d->jam_in));
                                                $tgl_out = date('Y-m-d', strtotime($d->jam_out));
                                                
                                                if (!empty($d->jam_in)) {
                                                    if ($jam_in_tanggal > $j_masuk_tanggal) {
                                                        $j1 = strtotime($j_masuk_tanggal);
                                                        $j2 = strtotime($jam_in_tanggal);
                                                
                                                        $diffterlambat = $j2 - $j1;
                                                
                                                        $jamterlambat = floor($diffterlambat / (60 * 60));
                                                        $menitterlambat = floor(($diffterlambat - $jamterlambat * (60 * 60)) / 60);
                                                
                                                        $jterlambat = $jamterlambat <= 9 ? '0' . $jamterlambat : $jamterlambat;
                                                        $mterlambat = $menitterlambat <= 9 ? '0' . $menitterlambat : $menitterlambat;
                                                
                                                        $terlambat = $jterlambat . ':' . $mterlambat;
                                                        $desimalterlambat = ROUND($menitterlambat / 60, 2);
                                                    } else {
                                                        $terlambat = 'Tepat waktu';
                                                        $jamterlambat = 0;
                                                        $desimalterlambat = 0;
                                                    }
                                                } else {
                                                    $terlambat = '';
                                                    $jamterlambat = 0;
                                                    $desimalterlambat = 0;
                                                }
                                                
                                                if (!empty($d->jam_keluar)) {
                                                    $jam_keluar_kk = date('H:i', strtotime($d->jam_keluar));
                                                    $jam_keluar_kk_tanggal = $tgl_pulang . ' ' . $jam_keluar_kk;
                                                
                                                    if (!empty($d->jam_masuk_kk)) {
                                                        $jam_masuk_kk = date('H:i', strtotime($d->jam_masuk_kk));
                                                        $jam_masuk_kk_tanggal = $tgl_pulang . ' ' . $jam_masuk_kk;
                                                    } else {
                                                        $jam_masuk_kk_tanggal = $tgl_pulang . ' ' . $j_pulang;
                                                    }
                                                
                                                    $jk1 = strtotime($jam_keluar_kk_tanggal);
                                                    $jk2 = strtotime($jam_masuk_kk_tanggal);
                                                    $difkeluarkantor = $jk2 - $jk1;
                                                
                                                    $jamkeluarkantor = floor($difkeluarkantor / (60 * 60));
                                                    $menitkeluarkantor = floor(($difkeluarkantor - $jamkeluarkantor * (60 * 60)) / 60);
                                                
                                                    $jkeluarkantor = $jamkeluarkantor <= 9 ? '0' . $jamkeluarkantor : $jamkeluarkantor;
                                                    $mkeluarkantor = $menitkeluarkantor <= 9 ? '0' . $menitkeluarkantor : $menitkeluarkantor;
                                                
                                                    if (empty($d->jam_masuk_kk)) {
                                                        if ($d->total_jam == 7) {
                                                            $totaljamkeluar = $jkeluarkantor - 1 . ':' . $mkeluarkantor;
                                                        } else {
                                                            $totaljamkeluar = $jkeluarkantor . ':' . $mkeluarkantor;
                                                        }
                                                    } else {
                                                        $totaljamkeluar = $jkeluarkantor . ':' . $mkeluarkantor;
                                                    }
                                                    $desimaljamkeluar = ROUND($menitkeluarkantor / 60, 2);
                                                } else {
                                                    $totaljamkeluar = '';
                                                    $desimaljamkeluar = 0;
                                                    $jamkeluarkantor = 0;
                                                }
                                                
                                                if (!empty($d->jam_out) && $jam_out_tanggal < $j_pulang_tanggal) {
                                                    $pc = 'Pulang Cepat';
                                                } else {
                                                    $pc = '';
                                                }
                                                
                                                $jamterlambat = $jamterlambat < 0 && !empty($d->kode_izin_terlambat) ? 0 : $jamterlambat;
                                                
                                                //Jam terlambat dalam Desimal
                                                
                                                $jt = $jamterlambat + $desimalterlambat;
                                                if ($jamkeluarkantor > 0) {
                                                    if ($d->keperluan == 'K') {
                                                        $jk = 0;
                                                    } else {
                                                        $jk = $jamkeluarkantor + $desimaljamkeluar;
                                                    }
                                                } else {
                                                    $jk = 0;
                                                }
                                                $jt = !empty($jt) ? $jt : 0;
                                                // menghitung Denda
                                                if (!empty($d->jam_in) and $d->kode_dept != 'MKT') {
                                                    if ($jam_in_tanggal > $j_masuk_tanggal and empty($d->kode_izin_terlambat)) {
                                                        if ($jamterlambat < 1) {
                                                            if ($menitterlambat >= 5 and $menitterlambat < 10) {
                                                                $denda = 5000;
                                                            } elseif ($menitterlambat >= 10 and $menitterlambat < 15) {
                                                                $denda = 10000;
                                                            } elseif ($menitterlambat >= 15 and $menitterlambat <= 59) {
                                                                $denda = 15000;
                                                            } else {
                                                                $denda = 0;
                                                            }
                                                        } else {
                                                            $denda = 0;
                                                        }
                                                    } else {
                                                        $denda = 0;
                                                    }
                                                } else {
                                                    $denda = 0;
                                                }
                                                
                                                //Menghitung total Jam
                                                if ($jam_out_tanggal > $jam_awal_istirahat_tanggal && $jam_out_tanggal < $jam_akhir_istirahat_tanggal) {
                                                    $jout = $jam_awal_istirahat_tanggal;
                                                } else {
                                                    $jout = $jam_out_tanggal;
                                                }
                                                
                                                //echo $jam_awal_istirahat."|";
                                                $awal = strtotime($j_masuk_tanggal);
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
                                                
                                                // if ($namahari != 'Sabtu') {
                                                //     $totaljam = 7.00 - $jt;
                                                // } else if ($namahari == "Sabtu") {
                                                //     $totaljam = 5.00 - $jt;
                                                // } else {
                                                //     $totaljam = $jam . ":" . $menit;
                                                // }
                                                
                                                if ($denda == 0 and empty($d->kode_izin_terlambat)) {
                                                    if ($d->kode_dept != 'MKT') {
                                                        if ($jamterlambat <= 1) {
                                                            $jt = 0;
                                                        } else {
                                                            $jt = $jt;
                                                        }
                                                    } else {
                                                        if ($jamterlambat <= 1) {
                                                            $jt = 0;
                                                        } else {
                                                            $jt = $jt;
                                                        }
                                                    }
                                                } else {
                                                    if ($jamterlambat <= 1) {
                                                        $jt = 0;
                                                    } else {
                                                        $jt = $jt;
                                                    }
                                                }
                                                
                                                if (!empty($cekdirumahkan)) {
                                                    $totaljam = $jam - $jt - $jk;
                                                } else {
                                                    $totaljam = $d->total_jam - $jt - $jk;
                                                }
                                                
                                                if (!empty($d->jam_out)) {
                                                    if ($jam_out_tanggal < $j_pulang_tanggal) {
                                                        if ($jam_out_tanggal > $jam_istirahat_tanggal && !empty($jam_istirahat)) {
                                                            $desimalmenit = ROUND(($menit * 100) / 60);
                                                            $grandtotaljam = $jam - 1 . '.' . $desimalmenit;
                                                        } else {
                                                            $desimalmenit = ROUND(($menit * 100) / 60);
                                                            $grandtotaljam = $jam . '.' . $desimalmenit;
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
                                                    <td class="text-center">
                                                        {{ $loop->iteration + $karyawan->firstItem() - 1 }}</td>
                                                    <td>{{ $d->nik }}</td>
                                                    <td>{{ $d->nama_karyawan }}</td>
                                                    <td>{{ $d->kode_dept }}</td>

                                                    <td>{{ $d->id_kantor }}</td>
                                                    <td>
                                                        {{ $d->nama_jadwal }} {{ $d->jadwalcabang }} ({{ $j_masuk }}
                                                        s/d {{ $j_pulang }})</td>
                                                    <td>
                                                        {!! $d->jam_in != null
                                                            ? '<a href="#" class="showpresensi" id="' . $d->id . '" status="in">' . $jam_in . '</a>'
                                                            : '<span class="danger">Belum Absen</span>' !!}
                                                        @if (!empty($d->kode_izin_terlambat))
                                                            (Izin)
                                                        @endif
                                                    </td>
                                                    <td style="color:{{ $jam_out < $jam_pulang ? 'red' : '' }}">
                                                        {!! $d->jam_out != null
                                                            ? '<a href="#" class="showpresensi" id="' . $d->id . '" status="out">' . $jam_out . '</a>'
                                                            : '<span class="danger">Belum Absen</span>' !!}
                                                        @if (!empty($pc))
                                                            (PC)
                                                        @endif
                                                        @if (!empty($d->kode_izin_pulang))
                                                            (Izin)
                                                        @endif

                                                        @if ($tgl_out > $tgl_in)
                                                            <i class="feather icon-arrow-up-right text-info"></i>
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
                                                        {{ $terlambat }}
                                                    </td>
                                                    <td>{{ !empty($denda) ? rupiah($denda) : '' }}</td>
                                                    <td>{{ $totaljamkeluar }}</td>
                                                    <td
                                                        style="color:{{ $grandtotaljam < $d->total_jam ? 'red' : '' }}; text-align:center">
                                                        {{ $grandtotaljam > 0 ? $grandtotaljam : 0 }}
                                                        {{-- {{ $jout }} - {{ $jam_masuk_tanggal }} - {{ $j_masuk_tanggal }} --}}
                                                    </td>
                                                    <td>
                                                        @if ($level == 'manager hrd' || $level == 'admin' || Auth::user()->pic_presensi == 1)
                                                            <a href="#" class="edit" nik="{{ $d->nik }}"
                                                                kode_jadwal="{{ $d->kode_jadwal }}"><i
                                                                    class="feather icon-edit info"></i></a>
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
