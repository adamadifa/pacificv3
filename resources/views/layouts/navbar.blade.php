<!-- BEGIN: Main Menu-->


<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto">
                <a class="navbar-brand" href="/home">
                    <div class="brand-logo"></div>
                    <h2 class="brand-text mb-0">PACIFIC</h2>
                </a>
            </li>
            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                    <i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i
                        class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary" data-ticon="icon-disc">
                    </i>
                </a>
            </li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class=" {{ request()->is(['home']) ? 'active' : '' }} nav-item">
                <a href="/home">
                    <i class="feather icon-home"></i>
                    <span class="menu-title" data-i18n="Dashboard">Dashboard</span>
                </a>
            </li>


            @if (in_array($level, $hrd_menu) || in_array($kat_jabatan, $hrd_menu) || Auth::user()->pic_presensi == 1)
                <li class=" nav-item">
                    <a href="#"><i class="fa fa-users" style="color:rgb(200, 5, 77)"></i><span class="menu-title">HRD</span></a>
                    <ul class="menu-content">
                        @if (in_array($level, $penilaian_karyawan) || in_array($kat_jabatan, $penilaian_karyawan))
                            <li class="{{ request()->is(['penilaiankaryawan', 'penilaiankaryawan/*']) ? 'active' : '' }}">
                                @if (Auth::user()->level == 'kepala admin')
                                    <a href="/penilaiankaryawan/12/MP/list">
                                        <i class="feather icon-edit"></i>
                                        <span class="menu-item" data-i18n="Second Level">Penilaian karyawan</span>
                                    </a>
                                @elseif(Auth::user()->level == 'kepala penjualan')
                                    <a href="/penilaiankaryawan/14/PCF/list">
                                        <i class="feather icon-edit"></i>
                                        <span class="menu-item" data-i18n="Second Level">Penilaian karyawan</span>
                                    </a>
                                @elseif(Auth::user()->level == 'rsm')
                                    <a href="/penilaiankaryawan/6/PCF/list">
                                        <i class="feather icon-edit"></i>
                                        <span class="menu-item" data-i18n="Second Level">Penilaian karyawan</span>
                                    </a>
                                @elseif(Auth::user()->level == 'manager marketing')
                                    <a href="/penilaiankaryawan/4/MP/list">
                                        <i class="feather icon-edit"></i>
                                        <span class="menu-item" data-i18n="Second Level">Penilaian karyawan</span>
                                    </a>
                                @elseif(Auth::user()->level == 'spv pdqc' || Auth::user()->level == 'admin pdqc')
                                    <a href="/penilaiankaryawan/15/MP/list">
                                        <i class="feather icon-edit"></i>
                                        <span class="menu-item" data-i18n="Second Level">Penilaian karyawan</span>
                                    </a>
                                @elseif(Auth::user()->level == 'spv maintenance')
                                    <a href="/penilaiankaryawan/5/MP/list">
                                        <i class="feather icon-edit"></i>
                                        <span class="menu-item" data-i18n="Second Level">Penilaian karyawan</span>
                                    </a>
                                @elseif(Auth::user()->level == 'staff keuangan' || Auth::user()->level == 'spv accounting')
                                    <a href="/penilaiankaryawan/8/MP/list">
                                        <i class="feather icon-edit"></i>
                                        <span class="menu-item" data-i18n="Second Level">Penilaian karyawan</span>
                                    </a>
                                @elseif(
                                    (Auth::user()->level == 'manager accounting' && Auth::user()->kategori_jabatan == 3) ||
                                        (Auth::user()->level == 'rom' && Auth::user()->kategori_jabatan == 3) ||
                                        (Auth::user()->level == 'kepala gudang' && Auth::user()->kategori_jabatan == 3) ||
                                        (Auth::user()->level == 'admin gudang bahan' && Auth::user()->kategori_jabatan == 3) ||
                                        (Auth::user()->level == 'manager ga' && Auth::user()->kategori_jabatan == 3) ||
                                        (Auth::user()->level == 'general affair' && Auth::user()->kategori_jabatan == 3) ||
                                        (Auth::user()->level == 'manager produksi' && Auth::user()->kategori_jabatan == 3) ||
                                        (Auth::user()->level == 'spv produksi' && Auth::user()->kategori_jabatan == 3) ||
                                        (Auth::user()->level == 'manager pembelian' && Auth::user()->kategori_jabatan == 3) ||
                                        (Auth::user()->level == 'spv gudang pusat' && Auth::user()->kategori_jabatan == 3))
                                    <a href="/penilaiankaryawan/5/MP/list">
                                        <i class="feather icon-edit"></i>
                                        <span class="menu-item" data-i18n="Second Level">Penilaian karyawan</span>
                                    </a>
                                @elseif(Auth::user()->level == 'manager audit' && Auth::user()->kategori_jabatan == 3)
                                    <a href="/penilaiankaryawan/8/MP/list">
                                        <i class="feather icon-edit"></i>
                                        <span class="menu-item" data-i18n="Second Level">Penilaian karyawan</span>
                                    </a>
                                @elseif(
                                    (Auth::user()->level == 'manager accounting' && Auth::user()->kategori_jabatan == 2) ||
                                        Auth::user()->level == 'manager hrd' ||
                                        Auth::user()->level == 'spv recruitment' ||
                                        Auth::user()->level == 'direktur' ||
                                        Auth::user()->level == 'emf')
                                    <a href="/penilaiankaryawan/3/MP/list">
                                        <i class="feather icon-edit"></i>
                                        <span class="menu-item" data-i18n="Second Level">Penilaian karyawan</span>
                                    </a>
                                @endif

                            </li>
                        @endif

                        @if (in_array($level, $kesepakatanbersama))
                            <li class="{{ request()->is(['kesepakatanbersama', 'kesepakatanbersama/*']) ? 'active' : '' }}">
                                <a href="/kesepakatanbersama">
                                    <i class="feather icon-file-text"></i>
                                    <span class="menu-item" data-i18n="Second Level">Kesepakatan Bersama</span>
                                </a>
                            </li>
                        @endif
                        @if (in_array($level, $kontrak_menu))
                            <li class="{{ request()->is(['kontrak', 'kontrak/*']) ? 'active' : '' }}">
                                <a href="/kontrak">
                                    <i class="feather icon-book-open"></i>
                                    <span class="menu-item" data-i18n="Second Level">Kontrak</span>
                                </a>
                            </li>
                        @endif

                        @if (in_array($level, $pengajuan_izin_menu) || Auth::user()->pic_presensi == 1)
                            <li class="{{ request()->is(['pengajuanizin', 'pengajuanizin/*']) ? 'active' : '' }}">
                                <a href="/pengajuanizin">
                                    <i class="feather icon-file-text"></i>
                                    <span class="menu-item" data-i18n="Second Level">Pengajuan</span>
                                </a>
                            </li>
                        @endif


                        @if (in_array($level, $jadwal_kerja_menu))
                            <li class="{{ request()->is(['konfigurasijadwal', 'konfigurasijadwal/*']) ? 'active' : '' }}">
                                <a href="/konfigurasijadwal">
                                    <i class="feather icon-calendar"></i>
                                    <span class="menu-item" data-i18n="Second Level">Jadwal Kerja</span>
                                </a>
                            </li>
                        @endif



                        @if (in_array($level, $lembur_menu) || Auth::user()->pic_presensi == 1)
                            <li class="{{ request()->is(['lembur', 'lembur/*']) ? 'active' : '' }}">
                                <a href="/lembur">
                                    <i class="feather icon-calendar"></i>
                                    <span class="menu-item" data-i18n="Second Level">Lembur</span>
                                </a>
                            </li>
                        @endif


                        @if (in_array($level, $hari_libur_menu) || Auth::user()->pic_presensi == 1)
                            <li class="{{ request()->is(['harilibur', 'harilibur/*']) ? 'active' : '' }}">
                                <a href="/harilibur">
                                    <i class="feather icon-calendar"></i>
                                    <span class="menu-item" data-i18n="Second Level">Hari Libur</span>
                                </a>
                            </li>
                        @endif

                        @if (in_array($level, $pembayaran_jmk))
                            <li class="{{ request()->is(['pembayaranjmk', 'pembayaranjmk/*']) ? 'active' : '' }}">
                                <a href="/pembayaranjmk">
                                    <i class="feather icon-dollar-sign"></i>
                                    <span class="menu-item" data-i18n="Second Level">Pembayaran JMK</span>
                                </a>
                            </li>
                        @endif
                        @if (in_array($level, $pelanggaran_menu))
                            <li class="{{ request()->is(['pelanggaran', 'pelanggaran/*']) ? 'active' : '' }}">
                                <a href="/pelanggaran">
                                    <i class="feather icon-thumbs-down"></i>
                                    <span class="menu-item" data-i18n="Second Level">Pelanggaran</span>
                                </a>
                            </li>
                        @endif

                        @if (in_array($level, $monitoring_presensi) || Auth::user()->pic_presensi == 1)
                            <li class="{{ request()->is(['presensi/monitoring']) ? 'active' : '' }}">
                                <a href="/presensi/monitoring">
                                    <i class="feather icon-monitor"></i>
                                    <span class="menu-item" data-i18n="Second Level">Monitoring Presensi</span>
                                </a>
                            </li>
                        @endif


                        @if (in_array($level, $presensi_karyawan_menu) || Auth::user()->pic_presensi == 1)
                            <li class="{{ request()->is(['presensi/presensikaryawan']) ? 'active' : '' }}">
                                <a href="/presensi/presensikaryawan">
                                    <i class="feather icon-monitor"></i>
                                    <span class="menu-item" data-i18n="Second Level">Presensi Karyawan</span>
                                </a>
                            </li>
                        @endif

                        @if (in_array($level, $slip_gaji))
                            <li class="{{ request()->is(['slipgaji']) ? 'active' : '' }}">
                                <a href="/slipgaji">
                                    <i class="feather icon-monitor"></i>
                                    <span class="menu-item" data-i18n="Second Level">Slip Gaji</span>
                                </a>
                            </li>
                        @endif
                        @if (in_array($level, $lap_hrd) || Auth::user()->pic_presensi == 1)
                            <li
                                class="{{ request()->is(['laporanhrd/presensi', 'laporanhrd/presensipsm', 'laporanhrd/rekapterlambat', 'laporanhrd/gaji']) ? 'active' : '' }}">
                                <a href="/laporanhrd/presensi">
                                    <i class="feather icon-file-text"></i>
                                    <span class="menu-item" data-i18n="Second Level">Laporan</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

        </ul>
    </div>
</div>
<!-- END: Main Menu-->
