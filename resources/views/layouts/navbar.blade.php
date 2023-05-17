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
                    <i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary" data-ticon="icon-disc">
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
            @if (in_array($level,$scan))
            <li class=" {{ request()->is(['scan']) ? 'active' : '' }} nav-item">
                <a href="/scan">
                    <i class="feather icon-maximize"></i>
                    <span class="menu-title">Scan Qr Code</span>
                </a>
            </li>
            @endif
            @if (in_array($level,$tracking_salesman))
            <li class=" {{ request()->is(['tracking']) ? 'active' : '' }} nav-item">
                <a href="/tracking">
                    <i class="feather icon-map"></i>
                    <span class="menu-title" data-i18n="Tracking Salesman">Tracking Salesman</span>
                </a>
            </li>
            @endif

            @if (in_array($level,$map_pelanggan))
            <li class=" {{ request()->is(['mappelanggan']) ? 'active' : '' }} nav-item">
                <a href="/mappelanggan">
                    <i class="feather icon-map"></i>
                    <span class="menu-title">Map Pelanggan</span>
                </a>
            </li>
            @endif
            @if (Auth::user()->level != "salesman")
            <li class=" {{ request()->is(['memo','memo/*']) ? 'active' : '' }} nav-item">
                <a href="/memo">
                    <i class="feather icon-book"></i>
                    <span class="menu-title" data-i18n="Memo">E-Manual <span class="badge badge-pill bg-danger">{{ $memo_unread }}</span></span>
                </a>
            </li>
            @endif

            @if (in_array($level, $datamaster_view))
            <li class=" nav-item">
                <a href="#"><i class="feather icon-grid primary"></i><span class="menu-title">Data Master</span></a>
                <ul class="menu-content">
                    @if (in_array($level, $pelanggan_view))
                    <li class="{{ request()->is(['pelanggan', 'pelanggan/*']) ? 'active' : '' }}">
                        <a href="/pelanggan">
                            <i class="feather icon-users"></i>
                            <span class="menu-item">Pelanggan</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $salesman_view))
                    <li class="{{ request()->is(['salesman', 'salesman/*']) ? 'active' : '' }}">
                        <a href="/salesman">
                            <i class="feather icon-users"></i>
                            <span class="menu-item">Salesman</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $supplier_view))
                    <li class="{{ request()->is(['supplier', 'supplier/*']) ? 'active' : '' }}">
                        <a href="/supplier">
                            <i class="feather icon-users"></i>
                            <span class="menu-item">Supplier</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $barang_view))
                    <li class="{{ request()->is(['barang', 'barang/*']) ? 'active' : '' }}">
                        <a href="/barang">
                            <i class="feather icon-grid"></i>
                            <span class="menu-item">Barang <small>(Penjualan)</small></span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $barangpembelian))
                    <li class="{{ request()->is(['barangpembelian', 'barangpembelian/*']) ? 'active' : '' }}">
                        <a href="/barangpembelian">
                            <i class="feather icon-grid"></i>
                            <span class="menu-item">Barang <small>(Pembelian)</small></span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $harga_view))
                    <li class="{{ request()->is(['harga', 'harga/*']) ? 'active' : '' }}">
                        <a href="/harga">
                            <i class="fa fa-money"></i>
                            <span class="menu-item">Harga</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $kendaraan_view))
                    <li class="{{ request()->is(['kendaraan', 'kendaraan/*']) ? 'active' : '' }}">
                        <a href="/kendaraan">
                            <i class="feather icon-truck"></i>
                            <span class="menu-item">Kendaraan</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $cabang_view))
                    <li class="{{ request()->is(['cabang', 'cabang/*']) ? 'active' : '' }}">
                        <a href="/cabang">
                            <i class="fa fa-bank"></i>
                            <span class="menu-item">Cabang</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $pasar_menu))
                    <li class="{{ request()->is(['pasar', 'pasar/*']) ? 'active' : '' }}">
                        <a href="/pasar">
                            <i class="fa fa-bank"></i>
                            <span class="menu-item">Data Wilayah / Rute</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $karyawan_view))
                    <li class="{{ request()->is(['karyawan', 'karyawan/*']) ? 'active' : '' }}">
                        <a href="/karyawan">
                            <i class="feather icon-users"></i>
                            <span class="menu-item">Data Karyawan</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level,$gaji_menu))
                    <li class="{{ request()->is(['gaji', 'gaji/*']) ? 'active' : '' }}">
                        <a href="/gaji">
                            <i class="feather icon-dollar-sign"></i>
                            <span class="menu-item" data-i18n="Second Level">Gaji</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level,$insentif_menu))
                    <li class="{{ request()->is(['insentif', 'insentif/*']) ? 'active' : '' }}">
                        <a href="/insentif">
                            <i class="feather icon-dollar-sign"></i>
                            <span class="menu-item" data-i18n="Second Level">Insentif</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if ($level == "salesman")
            <li class="{{ request()->is(['pelanggansalesman', 'pelanggansalesman/*']) ? 'active' : '' }}">
                <a href="/pelanggansalesman">
                    <i class="feather icon-users info"></i>
                    <span class="menu-item">Pelanggan</span>
                </a>
            </li>
            {{-- <li class=" {{ request()->is(['inputpenjualanv2']) ? 'active' : '' }} nav-item">
            <a href="/inputpenjualanv2">
                <i class="feather icon-shopping-cart danger"></i>
                <span class="menu-title">Input Penjualan</span>
            </a>
            </li> --}}

            <li class="{{ request()->is(['penjualan', 'penjualan/*']) ? 'active' : '' }}">
                <a href="/penjualan"><i class="feather icon-shopping-bag success">
                    </i><span class="menu-item">Data Penjualan</span></a>
            </li>
            <li class="{{ request()->is(['retur', 'retur/*']) ? 'active' : '' }}">
                <a href="/retur">
                    <i class="feather icon-package warning"></i>
                    <span class="menu-item">Data Retur</span>
                </a>
            </li>
            <li class="{{ request()->is(['laporanpenjualan','laporanpenjualan/*','laporanretur','laporankasbesarpenjualan','laporankendaraan/*','laporaninsentif','laporankomisi'])? 'active': '' }}">
                <a href="/laporanpenjualan/penjualan">
                    <i class="feather icon-file-text info"></i>
                    <span class="menu-item">Laporan</span>
                </a>
            </li>
            @endif
            @if (in_array($level, $produksi_menu))
            <li class=" nav-item">
                <a href="#"><i class="fa fa-cubes success"></i><span class="menu-title">Produksi</span></a>
                <ul class="menu-content">
                    @if (in_array($level,$produksi_analytics))
                    <li class="{{ request()->is(['produksi','produksi/*']) ? 'active' : '' }}">
                        <a href="/produksi/analytics">
                            <i class="feather icon-pie-chart"></i>
                            <span class="menu-item">Analytics</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $permintaan_produksi_view))
                    <li class="{{ request()->is(['permintaanproduksi', 'permintaanproduksi/*']) ? 'active' : '' }}">
                        <a href="/permintaanproduksi">
                            <i class="feather icon-clipboard"></i>
                            <span class="menu-item">Permintaan Produksi</span>
                        </a>
                    </li>
                    @endif

                    @if (in_array($level, $mutasi_produk))
                    <li>
                        <a href="#">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Second Level">Mutasi Produk</span>
                        </a>
                        <ul class="menu-content">
                            @if (in_array($level, $bpbj_view))
                            <li class="{{ request()->is(['bpbj', 'bpbj/*']) ? 'active' : '' }}">
                                <a href="/bpbj">
                                    <i class="feather icon-arrow-right"></i>
                                    <span class="menu-item">BPBJ</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $fsthp_view))
                            <li class="{{ request()->is(['fsthp', 'fsthp/*']) ? 'active' : '' }}">
                                <a href="/fsthp">
                                    <i class="feather icon-arrow-left"></i>
                                    <span class="menu-item">FSTHP</span>
                                </a>
                            </li>
                            @endif

                        </ul>
                    </li>
                    @endif

                    @if (in_array($level, $mutasi_barang))
                    <li>
                        <a href="#">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Second Level">Mutasi Barang</span>
                        </a>
                        <ul class="menu-content">
                            @if (in_array($level, $saldoawal_mutasibarang_produksi))
                            <li class="{{ request()->is(['saldoawalmutasibarangproduksi', 'saldoawalmutasibarangproduksi/*']) ? 'active' : '' }}">
                                <a href="/saldoawalmutasibarangproduksi">
                                    <i class="feather icon-settings"></i>
                                    <span class="menu-item">Saldo Awal</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $opname_mutasibarang_produksi))
                            <li class="{{ request()->is(['opnamemutasibarangproduksi', 'opnamemutasibarangproduksi/*']) ? 'active' : '' }}">
                                <a href="/opnamemutasibarangproduksi">
                                    <i class="feather icon-clipboard"></i>
                                    <span class="menu-item">Opname</span>
                                </a>
                            </li>
                            @endif

                            @if (in_array($level, $pemasukan_produksi))
                            <li class="{{ request()->is(['pemasukanproduksi', 'pemasukanproduksi/*']) ? 'active' : '' }}">
                                <a href="/pemasukanproduksi">
                                    <i class="feather icon-arrow-right"></i>
                                    <span class="menu-item">Barang Masuk</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $pengeluaran_produksi))
                            <li class="{{ request()->is(['pengeluaranproduksi', 'pengeluaranproduksi/*']) ? 'active' : '' }}">
                                <a href="/pengeluaranproduksi">
                                    <i class="feather icon-arrow-left"></i>
                                    <span class="menu-item">Barang Keluar</span>
                                </a>
                            </li>
                            @endif


                        </ul>
                    </li>
                    @endif
                    @if (in_array($level, $laporan_produksi))
                    <li class="{{ request()->is(['laporanproduksi', 'laporanproduksi/*']) ? 'active' : '' }}">
                        <a href="/laporanproduksi/mutasiproduksi">
                            <i class="feather icon-file-text"></i>
                            <span class="menu-item">Laporan</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if (in_array($level,$gudang_menu))
            <li class=" nav-item">
                <a href="#"><i class="fa fa-building-o" style="color:rgb(167, 69, 4)"></i><span class="menu-title">Gudang</span></a>
                <ul class="menu-content">
                    @if (in_array($level,$gudang_logistik_menu))
                    <li>
                        <a href="/gudanglogistik">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item">Gudang Logistik</span>
                        </a>
                        <ul class="menu-content">
                            @if (in_array($level, $pemasukan_gudanglogistik))
                            <li class="{{ request()->is(['saldoawalgudanglogistik', 'saldoawalgudanglogistik/*']) ? 'active' : '' }}">
                                <a href="/saldoawalgudanglogistik">
                                    <i class="feather icon-settings"></i>
                                    <span class="menu-item">Saldo Awal</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $opname_gudanglogistik))
                            <li class="{{ request()->is(['opnamegudanglogistik', 'opnamegudanglogistik/*']) ? 'active' : '' }}">
                                <a href="/opnamegudanglogistik">
                                    <i class="feather icon-clipboard"></i>
                                    <span class="menu-item">Opname</span>
                                </a>
                            </li>
                            @endif

                            @if (in_array($level, $pemasukan_gudanglogistik))
                            <li class="{{ request()->is(['pemasukangudanglogistik', 'pemasukangudanglogistik/*']) ? 'active' : '' }}">
                                <a href="/pemasukangudanglogistik">
                                    <i class="feather icon-arrow-right"></i>
                                    <span class="menu-item">Barang Masuk</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $pengeluaran_gudanglogistik))
                            <li class="{{ request()->is(['pengeluarangudanglogistik', 'pengeluarangudanglogistik/*']) ? 'active' : '' }}">
                                <a href="/pengeluarangudanglogistik">
                                    <i class="feather icon-arrow-left"></i>
                                    <span class="menu-item">Barang Keluar</span>
                                </a>
                            </li>
                            @endif

                        </ul>
                    </li>
                    @endif
                    @if (in_array($level,$gudang_bahan_menu))
                    <li>
                        <a href="/gudangbahan">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item">Gudang Bahan</span>
                        </a>
                        <ul class="menu-content">
                            @if (in_array($level, $saldoawal_gudangbahan))
                            <li class="{{ request()->is(['saldoawalgudangbahan', 'saldoawalgudangbahan/*']) ? 'active' : '' }}">
                                <a href="/saldoawalgudangbahan">
                                    <i class="feather icon-settings"></i>
                                    <span class="menu-item">Saldo Awal</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $opname_gudangbahan))
                            <li class="{{ request()->is(['opnamegudangbahan', 'opnamegudangbahan/*']) ? 'active' : '' }}">
                                <a href="/opnamegudangbahan">
                                    <i class="feather icon-clipboard"></i>
                                    <span class="menu-item">Opname</span>
                                </a>
                            </li>
                            @endif

                            @if (in_array($level, $pemasukan_gudangbahan))
                            <li class="{{ request()->is(['pemasukangudangbahan', 'pemasukangudangbahan/*']) ? 'active' : '' }}">
                                <a href="/pemasukangudangbahan">
                                    <i class="feather icon-arrow-right"></i>
                                    <span class="menu-item">Barang Masuk</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $pengeluaran_gudangbahan))
                            <li class="{{ request()->is(['pengeluarangudangbahan', 'pengeluarangudangbahan/*']) ? 'active' : '' }}">
                                <a href="/pengeluarangudangbahan">
                                    <i class="feather icon-arrow-left"></i>
                                    <span class="menu-item">Barang Keluar</span>
                                </a>
                            </li>
                            @endif

                        </ul>
                    </li>
                    @endif
                    @if (in_array($level,$gudang_jadi_menu))
                    <li>
                        <a href="#">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item">Gudang Jadi</span>
                        </a>
                        <ul class="menu-content">
                            @if (in_array($level, $permintaanpengiriman_gj))
                            <li class="{{ request()->is(['permintaanpengirimangj', 'permintaanpengirimangj/*']) ? 'active' : '' }}">
                                <a href="/permintaanpengirimangj">
                                    <i class="feather icon-truck"></i>
                                    <span class="menu-item">Permintaan Kirim</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $angkutan_view))
                            <li class="{{ request()->is(['angkutan', 'angkutan/*']) ? 'active' : '' }}">
                                <a href="/angkutan">
                                    <i class="feather icon-truck"></i>
                                    <span class="menu-item">Angkutan</span>
                                </a>
                            </li>
                            @endif

                            @if (in_array($level, $mutasi_produk_gj))
                            <li>
                                <a href="#">
                                    <i class="feather icon-circle"></i>
                                    <span class="menu-item" data-i18n="Second Level">Mutasi Produk</span>
                                </a>
                                <ul class="menu-content">
                                    @if (in_array($level, $fsthp_gj_view))
                                    <li class="{{ request()->is(['fsthpgj', 'fsthpgj/*']) ? 'active' : '' }}">
                                        <a href="/fsthpgj">
                                            <i class="feather icon-file"></i>
                                            <span class="menu-item">FSTHP</span>
                                        </a>
                                    </li>
                                    @endif
                                    @if (in_array($level, $suratjalan_view))
                                    <li class="{{ request()->is(['suratjalan', 'suratjalan/*']) ? 'active' : '' }}">
                                        <a href="/suratjalan">
                                            <i class="feather icon-truck"></i>
                                            <span class="menu-item">Surat Jalan</span>
                                        </a>
                                    </li>
                                    @endif
                                    @if (in_array($level, $repackgj_view))
                                    <li class="{{ request()->is(['repackgj', 'repackgj/*']) ? 'active' : '' }}">

                                        <a href="/repackgj/repack">
                                            <i class="feather icon-file"></i>
                                            <span class="menu-item">Repack</span>
                                        </a>
                                    </li>
                                    @endif
                                    @if (in_array($level, $rejectgj_view))
                                    <li class="{{ request()->is(['rejectgj', 'rejectgj/*']) ? 'active' : '' }}">
                                        <a href="/rejectgj/reject">
                                            <i class="feather icon-file"></i>
                                            <span class="menu-item">Reject</span>
                                        </a>
                                    </li>
                                    @endif
                                    @if (in_array($level, $lainnyagj_view))
                                    <li class="{{ request()->is(['lainnyagj', 'lainnyagj/*']) ? 'active' : '' }}">
                                        <a href="/lainnyagj">
                                            <i class="feather icon-file"></i>
                                            <span class="menu-item">Lainnya</span>
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </li>
                            @endif

                        </ul>
                    </li>
                    @endif
                    @if (in_array($level,$gudang_cabang_menu))
                    <li>
                        <a href="#">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item">Gudang Cabang</span>
                        </a>
                        <ul class="menu-content">
                            @if (in_array($level, $saldoawal_gs_view))
                            <li class="{{ request()->is(['saldoawalgs', 'saldoawalgs/*']) ? 'active' : '' }}">
                                <a href="/saldoawalgs/GS">
                                    <i class="feather icon-settings"></i>
                                    <span class="menu-item">Saldo Awal <span class="badge bg-success">GS</span></span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $saldoawal_bs_view))
                            <li class="{{ request()->is(['saldoawalbs', 'saldoawalbs/*']) ? 'active' : '' }}">
                                <a href="/saldoawalbs/BS">
                                    <i class="feather icon-settings"></i>
                                    <span class="menu-item">Saldo Awal <span class="badge bg-danger">BS</span></span>
                                </a>
                            </li>
                            @endif
                            @if (Auth::user()->kode_cabang == "BDG" || Auth::user()->kode_cabang=="PCF")
                            @if (in_array($level, $fpb_menu))
                            <li class="{{ request()->is(['fpb', 'fpb/*']) ? 'active' : '' }}">
                                <a href="/fpb">
                                    <i class="feather icon-clipboard"></i>
                                    <span class="menu-item">FPB</span>
                                </a>
                            </li>
                            @endif
                            @endif

                            @if (in_array($level, $dpb_view))
                            <li class="{{ request()->is(['dpb', 'dpb/*']) ? 'active' : '' }}">
                                <a href="/dpb">
                                    <i class="feather icon-clipboard"></i>
                                    <span class="menu-item">DPB</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $suratjalancab_view))
                            <li class="{{ request()->is(['suratjalancab', 'suratjalancab/*']) ? 'active' : '' }}">
                                <a href="/suratjalancab">
                                    <i class="feather icon-truck"></i>
                                    <span class="menu-item">Surat Jalan</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $mutasi_barang_cab_view))
                            <li class="{{ request()->is(['mutasigudangcabang', 'mutasigudangcabang/*','repack','kirimpusat','rejectgudang']) ? 'active' : '' }}">
                                <a href="/mutasigudangcabang/transitin">
                                    <i class="feather icon-repeat"></i>
                                    <span class="menu-item">Mutasi Produk</span>
                                </a>
                            </li>
                            @endif

                        </ul>
                    </li>
                    @endif

                    @if (in_array($level, $laporan_gudang))
                    <li class="{{ request()->is(['laporangudangbahan','laporangudangbahan/*',
                    'laporangudanglogistik','laporangudanglogistik/*',
                    'laporangudangjadi','laporangudangjadi/*','laporangudangcabang','laporangudangcabang/*'])? 'active': '' }}">
                        @if (in_array($level,$laporan_gudang_cabang))
                        <a href="/laporangudangcabang/persediaan">
                            <i class="feather icon-file-text"></i>
                            <span class="menu-item">Laporan</span>
                        </a>
                        @else
                        @if ($level=="admin pembelian" || $level=="admin gudang bahan" || $level=="manager pembelian")
                        <a href="/laporangudangbahan/pemasukan">
                            <i class="feather icon-file-text"></i>
                            <span class="menu-item">Laporan</span>
                        </a>
                        @else
                        <a href="/laporangudanglogistik/pemasukan">
                            <i class="feather icon-file-text"></i>
                            <span class="menu-item">Laporan</span>
                        </a>
                        @endif
                        @endif

                    </li>
                    @endif

                </ul>
            </li>
            @endif

            @if (in_array($level, $marketing))
            <li class=" nav-item">
                <a href="#"><i class="feather icon-radio info"></i><span class="menu-title">Marketing</span></a>
                <ul class="menu-content">
                    @if ($level=="kepala admin" && $getcbg == "TSM")
                    <li class="{{ request()->is(['permintaanpengiriman', 'permintaanpengiriman/*']) ? 'active' : '' }}">
                        <a href="/permintaanpengiriman">
                            <i class="feather icon-truck"></i>
                            <span class="menu-item">Permintaan Kirim</span></a>
                    </li>
                    @endif
                    @if (in_array($level, $permintaanpengiriman))
                    <li class="{{ request()->is(['permintaanpengiriman', 'permintaanpengiriman/*']) ? 'active' : '' }}">
                        <a href="/permintaanpengiriman">
                            <i class="feather icon-truck"></i>
                            <span class="menu-item">Permintaan Kirim</span></a>
                    </li>
                    @endif
                    @if (in_array($level, $komisi))
                    <li>
                        <a href="#">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Second Level">Komisi</span>
                        </a>
                        <ul class="menu-content">
                            @if (in_array($level, $targetkomisi))
                            <li class="{{ request()->is(['targetkomisi', 'targetkomisi/*']) ? 'active' : '' }}">
                                <a href="/targetkomisi">
                                    <i class="feather icon-activity"></i>
                                    <span class="menu-item">Target Komisi</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $inputpotongankomisi))
                            <li class="{{ request()->is(['komisiapprove', 'komisiapprove/*']) ? 'active' : '' }}">
                                <a href="/komisiapprove">
                                    <i class="feather icon-activity"></i>
                                    <span class="menu-item">Approve Komisi</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $ratiokomisi))
                            <li class="{{ request()->is(['ratiokomisi', 'ratiokomisi/*']) ? 'active' : '' }}">
                                <a href="/ratiokomisi">
                                    <i class="feather icon-pie-chart"></i>
                                    <span class="menu-item">Ratio Komisi</span>
                                </a>
                            </li>
                            @endif

                        </ul>
                    </li>
                    @endif
                    @if (in_array($level, $oman))
                    <li>
                        <a href="#">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Second Level">OMAN</span>
                        </a>
                        <ul class="menu-content">
                            @if (in_array($level, $omanmarketing))
                            <li class="{{ request()->is(['oman', 'oman/*']) ? 'active' : '' }}">
                                <a href="/oman">
                                    <i class="feather icon-box"></i>
                                    <span class="menu-item">Oman Marketing</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $omancabang))
                            <li class="{{ request()->is(['omancabang', 'omancabang/*']) ? 'active' : '' }}">
                                <a href="/omancabang">
                                    <i class="feather icon-box"></i>
                                    <span class="menu-item">Oman Cabang</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (in_array($level, $penjualan_menu))
                    <li><a href="#"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Second Level">Penjualan</span></a>
                        <ul class="menu-content">
                            @if (in_array($level, $penjualan_input))
                            {{-- <li>
                                <a href="/penjualan/create">
                                    <i class="feather icon-shopping-cart"></i>
                                    <span class="menu-item">Input Penjualan</span>
                                </a>
                            </li> --}}
                            @if (in_array(Auth::user()->kode_cabang,$cabangpkp))
                            <li class="{{ request()->is(['inputpenjualanppn']) ? 'active' : '' }}">
                                <a href="/inputpenjualanppn">
                                    <i class="feather icon-shopping-cart"></i>
                                    <span class="menu-item danger">Penjualan (PPN)</span>
                                </a>
                            </li>
                            @else
                            <li class="{{ request()->is(['inputpenjualanv2']) ? 'active' : '' }}">
                                <a href="/inputpenjualanv2">
                                    <i class="feather icon-shopping-cart"></i>
                                    <span class="menu-item">Input Penjualan</span>
                                </a>
                            </li>
                            @endif
                            @if ($level== "admin pusat" || Auth::user()->kode_cabang=="PST")
                            <li class="{{ request()->is(['inputpenjualanppn']) ? 'active' : '' }}">
                                <a href="/inputpenjualanppn">
                                    <i class="feather icon-shopping-cart"></i>
                                    <span class="menu-item danger">Penjualan (PPN)</span>
                                </a>
                            </li>
                            @endif

                            @endif
                            @if (in_array($level, $penjualan_view))
                            <li class="{{ request()->is(['penjualan', 'penjualan/*']) ? 'active' : '' }}">
                                <a href="/penjualan"><i class="feather icon-shopping-bag">
                                    </i><span class="menu-item">Data Penjualan</span></a>
                            </li>
                            @endif
                            @if (in_array($level, $retur_view))
                            <li class="{{ request()->is(['retur', 'retur/*']) ? 'active' : '' }}">
                                <a href="/retur">
                                    <i class="feather icon-package"></i>
                                    <span class="menu-item">Data Retur</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $limitkredit_view))
                            <li class="{{ request()->is(['limitkredit', 'limitkredit/*']) ? 'active' : '' }}">
                                <a href="/limitkredit">
                                    <i class="feather icon-credit-card"></i>
                                    <span class="menu-item">LimitKredit</span>
                                </a>
                            </li>
                            @endif

                        </ul>
                    </li>
                    @endif
                    @if (in_array($level, $laporan_penjualan))
                    <li class="{{ request()->is(['laporanpenjualan','laporanpenjualan/*','laporanretur','laporankasbesarpenjualan','laporankendaraan/*','laporaninsentif','laporankomisi'])? 'active': '' }}">
                        <a href="/laporanpenjualan/penjualan">
                            <i class="feather icon-file-text"></i>
                            <span class="menu-item">Laporan</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if (in_array($level, $pembelian_menu))
            <li class=" nav-item">
                <a href="#"><i class="feather icon-shopping-bag danger"></i><span class="menu-title">Pembelian</span></a>
                <ul class="menu-content">
                    @if (in_array($level,$pembelian_view))
                    <li class="{{ request()->is(['pembelian','pembelian/*']) ? 'active' : '' }}">
                        <a href="/pembelian?ppn=-">
                            <i class="feather icon-shopping-cart"></i>
                            <span class="menu-item">Data Pembelian</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level,$jatuhtempo_view))
                    <li class="{{ request()->is(['jatuhtempo','jatuhtempo/*']) ? 'active' : '' }}">
                        <a href="/jatuhtempo">
                            <i class="feather icon-clock"></i>
                            <span class="menu-item">Jatuh Tempo</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level,$jurnalkoreksi_view))
                    <li class="{{ request()->is(['jurnalkoreksi','jurnalkoreksi/*']) ? 'active' : '' }}">
                        <a href="/jurnalkoreksi">
                            <i class="feather icon-edit"></i>
                            <span class="menu-item">Jurnal Koreksi</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level,$laporan_pembelian))
                    <li class="{{ request()->is(['laporanpembelian','laporanpembelian/*']) ? 'active' : '' }}">
                        <a href="/laporanpembelian">
                            <i class="feather icon-file-text"></i>
                            <span class="menu-item">Laporan</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if (in_array($level, $keuangan))
            <li class=" nav-item">
                <a href="#"><i class="feather icon-dollar-sign warning"></i><span class="menu-title">Keuangan</span></a>
                <ul class="menu-content">
                    @if (in_array($level, $gudang_jadi_keuangan))
                    <li>
                        <a href="#">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Second Level">Gudang Jadi</span>
                        </a>
                        <ul class="menu-content">
                            @if (in_array($level, $kontrabon_angkutan_view))
                            <li class="{{ request()->is(['kontrabonangkutan', 'kontrabonangkutan/*']) ? 'active' : '' }}">
                                <a href="/kontrabonangkutan">
                                    <i class="feather icon-file-text"></i>
                                    <span class="menu-item">KontraBon Angkutan</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    @if (in_array($level, $penjualan_keuangan))
                    <li>
                        <a href="#">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Second Level">Penjualan</span>
                        </a>
                        <ul class="menu-content">
                            @if (in_array($level, $giro_view))
                            <li class="{{ request()->is(['giro', 'giro/*']) ? 'active' : '' }}">
                                <a href="/giro">
                                    <i class="feather icon-file-text"></i>
                                    <span class="menu-item">Giro</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $transfer_view))
                            <li class="{{ request()->is(['transfer', 'transfer/*']) ? 'active' : '' }}">
                                <a href="/transfer">
                                    <i class="feather icon-file-text"></i>
                                    <span class="menu-item">Transfer</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (in_array($level, $pembelian_keuangan))
                    <li>
                        <a href="#">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Second Level">Pembelian</span>
                        </a>
                        <ul class="menu-content">
                            @if (in_array($level, $kontrabon_view))
                            <li class="{{ request()->is(['kontrabon', 'kontrabon/*']) ? 'active' : '' }}">
                                <a href="/kontrabon">
                                    <i class="feather icon-file-text"></i>
                                    <span class="menu-item">Kontra Bon</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (in_array($level, $kasbesar_menu))
                    <li>
                        <a href="#">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Second Level">Kas Besar</span>
                        </a>
                        <ul class="menu-content">
                            @if (in_array($level, $saldoawalkasbesar_view))
                            <li class="{{ request()->is(['saldoawalkasbesar', 'saldoawalkasbesar/*']) ? 'active' : '' }}">
                                <a href="/saldoawalkasbesar">
                                    <i class="feather icon-settings"></i>
                                    <span class="menu-item">S. Awal Kas Besar</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $setoran_menu))
                            <li>
                                <a href="#">
                                    <i class="feather icon-circle"></i>
                                    <span class="menu-item" data-i18n="Second Level">Setoran</span>
                                </a>
                                <ul class="menu-content">

                                    @if (in_array($level, $setoranpenjualan_view))
                                    <li class="{{ request()->is(['setoranpenjualan', 'setoranpenjualan/*']) ? 'active' : '' }}">
                                        <a href="/setoranpenjualan">
                                            <i class="feather icon-file-text"></i>
                                            <span class="menu-item">Setoran Penjualan</span>
                                        </a>
                                    </li>
                                    @endif
                                    @if (in_array($level, $setoranpusat_view))
                                    <li class="{{ request()->is(['setoranpusat', 'setoranpusat/*']) ? 'active' : '' }}">
                                        <a href="/setoranpusat">
                                            <i class="feather icon-file-text"></i>
                                            <span class="menu-item">Setoran Pusat</span>
                                        </a>
                                    </li>
                                    @endif
                                    @if (in_array($level, $setorangiro_view))
                                    <li class="{{ request()->is(['setorangiro', 'setorangiro/*']) ? 'active' : '' }}">
                                        <a href="/setorangiro">
                                            <i class="feather icon-file-text"></i>
                                            <span class="menu-item">Setoran Giro</span>
                                        </a>
                                    </li>
                                    @endif
                                    @if (in_array($level, $setorantransfer_view))
                                    <li class="{{ request()->is(['setorantransfer', 'setorantransfer/*']) ? 'active' : '' }}">
                                        <a href="/setorantransfer">
                                            <i class="feather icon-file-text"></i>
                                            <span class="menu-item">Setoran Transfer</span>
                                        </a>
                                    </li>
                                    @endif
                                    @if (in_array($level, $belum_disetorkan))
                                    <li class="{{ request()->is(['belumsetor', 'belumsetor/*']) ? 'active' : '' }}">
                                        <a href="/belumsetor">
                                            <i class="feather icon-file-text"></i>
                                            <span class="menu-item">Belum Setor</span>
                                        </a>
                                    </li>
                                    @endif
                                    @if (in_array($level, $lebih_disetorkan))
                                    <li class="{{ request()->is(['lebihsetor', 'lebihsetor/*']) ? 'active' : '' }}">
                                        <a href="/lebihsetor">
                                            <i class="feather icon-file-text"></i>
                                            <span class="menu-item">Lebih Setor</span>
                                        </a>
                                    </li>
                                    @endif
                                    @if (in_array($level, $gantilogamtokertas))
                                    <li class="{{ request()->is(['logamtokertas', 'logamtokertas/*']) ? 'active' : '' }}">
                                        <a href="/logamtokertas">
                                            <i class="feather icon-file-text"></i>
                                            <span class="menu-item">Ganti Logam </span>
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (in_array($level, $kaskecil_menu))
                    <li>
                        <a href="#">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Second Level">Kas Kecil</span>
                        </a>
                        <ul class="menu-content">
                            @if (in_array($level, $kaskecil_view))
                            <li class="{{ request()->is(['kaskecil', 'kaskecil/*']) ? 'active' : '' }}">
                                <a href="/kaskecil">
                                    <i class="feather icon-book"></i>
                                    <span class="menu-item">Data Kas Kecil</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $klaim_view))
                            <li class="{{ request()->is(['klaim', 'klaim/*']) ? 'active' : '' }}">
                                <a href="/klaim">
                                    <i class="feather icon-archive"></i>
                                    <span class="menu-item">Klaim</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (in_array($level, $ledger_menu))
                    <li>
                        <a href="#">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Second Level">Ledger</span>
                        </a>
                        <ul class="menu-content">
                            @if (in_array($level, $ledger_saldoawal))
                            <li class="{{ request()->is(['saldoawalledger', 'saldoawalledger/*']) ? 'active' : '' }}">
                                <a href="/saldoawalledger">
                                    <i class="feather icon-settings"></i>
                                    <span class="menu-item">Saldo Awal Ledger</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $ledger_view))
                            <li class="{{ request()->is(['ledger', 'ledger/*']) ? 'active' : '' }}">
                                <a href="/ledger">
                                    <i class="feather icon-book"></i>
                                    <span class="menu-item">Data Ledger</span>
                                </a>
                            </li>
                            @endif

                        </ul>
                    </li>
                    @endif

                    @if (in_array($level, $pinjaman_view))
                    <li>
                        <a href="#">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Second Level">Pinjaman</span>
                        </a>
                        <ul class="menu-content">
                            @if (in_array($level, $pinjaman_view))
                            <li class="{{ request()->is(['pinjaman', 'pinjaman/*']) ? 'active' : '' }}">
                                <a href="/pinjaman">
                                    <i class="feather icon-book"></i>
                                    <span class="menu-item" data-i18n="Second Level">Pinjaman</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $pembayaranpinjaman_view))
                            <li class="{{ request()->is(['pembayaranpinjaman', 'pembayaranpinjaman/*']) ? 'active' : '' }}">
                                <a href="/pembayaranpinjaman">
                                    <i class="feather icon-book"></i>
                                    <span class="menu-item" data-i18n="Second Level">Pembayaran</span>
                                </a>
                            </li>
                            @endif

                        </ul>
                    </li>
                    @endif

                    @if (in_array($level, $kasbon_view))
                    <li>
                        <a href="#">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Second Level">Kasbon</span>
                        </a>
                        <ul class="menu-content">
                            @if (in_array($level, $kasbon_view))
                            <li class="{{ request()->is(['kasbon', 'kasbon/*']) ? 'active' : '' }}">
                                <a href="/kasbon">
                                    <i class="feather icon-book"></i>
                                    <span class="menu-item" data-i18n="Second Level">Kasbon</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $pembayarankasbon_view))
                            <li class="{{ request()->is(['pembayarankasbon', 'pembayarankasbon/*']) ? 'active' : '' }}">
                                <a href="/pembayarankasbon">
                                    <i class="feather icon-book"></i>
                                    <span class="menu-item" data-i18n="Second Level">Pembayaran</span>
                                </a>
                            </li>
                            @endif

                        </ul>
                    </li>
                    @endif
                    @if (in_array($level, $mutasibank_view))
                    <li class="{{ request()->is(['mutasibank', 'mutasibank/*']) ? 'active' : '' }}">
                        <a href="/mutasibank">
                            <i class="feather icon-book"></i>
                            <span class="menu-item" data-i18n="Second Level">Mutasi Bank</span>
                        </a>
                    </li>
                    @endif


                    @if (in_array($level, $laporankeuangan_view))
                    <li class="{{ request()->is(['laporankeuangan', 'laporankeuangan/*']) ? 'active' : '' }}">
                        @if ($level == "kasir" || $level == "admin persediaan dan kasir" || $level == "admin penjualan dan kasir")
                        <a href="/laporankeuangan/saldokasbesar">
                            <i class="feather icon-file-text"></i>
                            <span class="menu-item" data-i18n="Second Level">Laporan</span>
                        </a>
                        @elseif($level=="admin pajak 2")
                        <a href="/laporankeuangan/ledger">
                            <i class="feather icon-file-text"></i>
                            <span class="menu-item" data-i18n="Second Level">Laporan</span>
                        </a>
                        @elseif($level=="manager hrd" || $level=="manager produksi")
                        <a href="/laporankeuangan/pinjaman">
                            <i class="feather icon-file-text"></i>
                            <span class="menu-item" data-i18n="Second Level">Laporan</span>
                        </a>
                        @else
                        <a href="/laporankeuangan/kaskecil">
                            <i class="feather icon-file-text"></i>
                            <span class="menu-item" data-i18n="Second Level">Laporan</span>
                        </a>
                        @endif

                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if (in_array($level, $accounting_menu))
            <li class=" nav-item">
                <a href="#"><i class="fa fa-balance-scale" style="color:blue"></i><span class="menu-title">Accounting</span></a>
                <ul class="menu-content">
                    @if (in_array($level, $coa_menu))
                    <li class="{{ request()->is(['coa', 'coa/*']) ? 'active' : '' }}">
                        <a href="/coa">
                            <i class="feather icon-settings"></i>
                            <span class="menu-item" data-i18n="Second Level">Chart Of Account</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $setcoacabang))
                    <li class="{{ request()->is(['setcoacabang', 'setcoacabang/*']) ? 'active' : '' }}">
                        <a href="/setcoacabang">
                            <i class="feather icon-settings"></i>
                            <span class="menu-item" data-i18n="Second Level">Set Coa Cabang</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $saldoawal_bukubesar_menu))
                    <li class="{{ request()->is(['saldoawalbb', 'saldoawalbb/*']) ? 'active' : '' }}">
                        <a href="/saldoawalbb">
                            <i class="feather icon-settings"></i>
                            <span class="menu-item" data-i18n="Second Level">S. Awal Buku Besar</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $jurnalumum_menu))
                    <li class="{{ request()->is(['jurnalumum', 'jurnalumum/*']) ? 'active' : '' }}">
                        <a href="/jurnalumum">
                            <i class="feather icon-book"></i>
                            <span class="menu-item" data-i18n="Second Level">Jurnal Umum</span>
                        </a>
                    </li>
                    @endif

                    @if (in_array($level, $hpp_menu))
                    <li>
                        <a href="#">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Second Level">HPP</span>
                        </a>
                        <ul class="menu-content">
                            @if (in_array($level, $hpp_input))
                            <li class="{{ request()->is(['hpp', 'hpp/*']) ? 'active' : '' }}">
                                <a href="/hpp">
                                    <i class="feather icon-clipboard"></i>
                                    <span class="menu-item" data-i18n="Second Level">Input HPP</span>
                                </a>
                            </li>
                            @endif
                            @if (in_array($level, $hargaawal_input))
                            <li class="{{ request()->is(['hargaawal', 'hargaawal/*']) ? 'active' : '' }}">
                                <a href="/hargaawal">
                                    <i class="feather icon-dollar-sign"></i>
                                    <span class="menu-item" data-i18n="Second Level">Input Harga</span>
                                </a>
                            </li>
                            @endif

                        </ul>
                    </li>
                    @endif
                    @if (in_array($level, $costratio_menu))
                    <li class="{{ request()->is(['costratio', 'costratio/*']) ? 'active' : '' }}">
                        <a href="/costratio">
                            <i class="feather icon-dollar-sign"></i>
                            <span class="menu-item" data-i18n="Second Level">Cost Ratio</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $laporan_accounting))
                    <li class="{{ request()->is(['laporanaccounting', 'laporanaccounting/*']) ? 'active' : '' }}">
                        @if ($level =="general affair" || $level=="hrd")
                        <a href="/laporanaccounting/jurnalumum">
                            <i class="feather icon-file"></i>
                            <span class="menu-item" data-i18n="Second Level">Laporan</span>
                        </a>
                        @elseif ($level =="kepala admin" || $level=="kepala penjualan" || $level=="rsm" || $level=="manager marketing")
                        <a href="/laporanaccounting/costratio">
                            <i class="feather icon-file"></i>
                            <span class="menu-item" data-i18n="Second Level">Laporan</span>
                        </a>
                        @else
                        <a href="/laporanaccounting/rekapbj">
                            <i class="feather icon-file"></i>
                            <span class="menu-item" data-i18n="Second Level">Laporan</span>
                        </a>
                        @endif
                    </li>
                    @endif

                </ul>
            </li>
            @endif

            @if (in_array($level, $hrd_menu) || in_array($kat_jabatan,$hrd_menu))
            <li class=" nav-item">
                <a href="#"><i class="fa fa-users" style="color:rgb(200, 5, 77)"></i><span class="menu-title">HRD</span></a>
                <ul class="menu-content">
                    @if (in_array($level, $penilaian_karyawan) || in_array($kat_jabatan, $penilaian_karyawan))
                    <li class="{{ request()->is(['penilaiankaryawan', 'penilaiankaryawan/*']) ? 'active' : '' }}">
                        @if (Auth::user()->level=="kepala admin")
                        <a href="/penilaiankaryawan/12/MP/list">
                            <i class="feather icon-edit"></i>
                            <span class="menu-item" data-i18n="Second Level">Penilaian karyawan</span>
                        </a>
                        @elseif(Auth::user()->level=="kepala penjualan")
                        <a href="/penilaiankaryawan/14/PCF/list">
                            <i class="feather icon-edit"></i>
                            <span class="menu-item" data-i18n="Second Level">Penilaian karyawan</span>
                        </a>
                        @elseif(Auth::user()->level=="rsm")
                        <a href="/penilaiankaryawan/6/PCF/list">
                            <i class="feather icon-edit"></i>
                            <span class="menu-item" data-i18n="Second Level">Penilaian karyawan</span>
                        </a>
                        @elseif(Auth::user()->level=="manager marketing")
                        <a href="/penilaiankaryawan/4/PCF/list">
                            <i class="feather icon-edit"></i>
                            <span class="menu-item" data-i18n="Second Level">Penilaian karyawan</span>
                        </a>
                        @elseif(Auth::user()->level=="manager accounting" && Auth::user()->kategori_jabatan == 3 ||Auth::user()->level=="kepala gudang" && Auth::user()->kategori_jabatan == 3 || Auth::user()->level=="manager ga" && Auth::user()->kategori_jabatan == 3 || Auth::user()->level=="manager produksi" && Auth::user()->kategori_jabatan == 3 || Auth::user()->level=="spv produksi" && Auth::user()->kategori_jabatan == 3 || Auth::user()->level=="manager pembelian" && Auth::user()->kategori_jabatan == 3)
                        <a href="/penilaiankaryawan/5/MP/list">
                            <i class="feather icon-edit"></i>
                            <span class="menu-item" data-i18n="Second Level">Penilaian karyawan</span>
                        </a>
                        @elseif(Auth::user()->level=="manager accounting" && Auth::user()->kategori_jabatan == 2 || Auth::user()->level=="manager hrd" || Auth::user()->level=="direktur" || Auth::user()->level=="emf"
                        )
                        <a href="/penilaiankaryawan/3/MP/list">
                            <i class="feather icon-edit"></i>
                            <span class="menu-item" data-i18n="Second Level">Penilaian karyawan</span>
                        </a>
                        @endif

                    </li>
                    @endif

                    @if (in_array($level,$kesepakatanbersama))
                    <li class="{{ request()->is(['kesepakatanbersama', 'kesepakatanbersama/*']) ? 'active' : '' }}">
                        <a href="/kesepakatanbersama">
                            <i class="feather icon-file-text"></i>
                            <span class="menu-item" data-i18n="Second Level">Kesepakatan Bersama</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level,$kontrak_menu))
                    <li class="{{ request()->is(['kontrak', 'kontrak/*']) ? 'active' : '' }}">
                        <a href="/kontrak">
                            <i class="feather icon-book-open"></i>
                            <span class="menu-item" data-i18n="Second Level">Kontrak</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level,$pengajuan_izin_menu))
                    <li class="{{ request()->is(['pengajuanizin', 'pengajuanizin/*']) ? 'active' : '' }}">
                        <a href="/pengajuanizin">
                            <i class="feather icon-file-text"></i>
                            <span class="menu-item" data-i18n="Second Level">Pengajuan</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level,$jadwal_kerja_menu))
                    <li class="{{ request()->is(['jadwalkerja', 'jadwalkerja/*']) ? 'active' : '' }}">
                        <a href="/jadwalkerja">
                            <i class="feather icon-calendar"></i>
                            <span class="menu-item" data-i18n="Second Level">Jadwal Kerja</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level,$pembayaran_jmk))
                    <li class="{{ request()->is(['pembayaranjmk', 'pembayaranjmk/*']) ? 'active' : '' }}">
                        <a href="/pembayaranjmk">
                            <i class="feather icon-dollar-sign"></i>
                            <span class="menu-item" data-i18n="Second Level">Pembayaran JMK</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level,$pelanggaran_menu))
                    <li class="{{ request()->is(['pelanggaran', 'pelanggaran/*']) ? 'active' : '' }}">
                        <a href="/pelanggaran">
                            <i class="feather icon-thumbs-down"></i>
                            <span class="menu-item" data-i18n="Second Level">Pelanggaran</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level,$monitoring_presensi))
                    <li class="{{ request()->is(['presensi/monitoring']) ? 'active' : '' }}">
                        <a href="/presensi/monitoring">
                            <i class="feather icon-monitor"></i>
                            <span class="menu-item" data-i18n="Second Level">Monitoring Presensi</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if (in_array($level, $ga_menu))
            <li class=" nav-item">
                <a href="#"><i class="feather icon-box" style="color:rgb(200, 5, 77)"></i><span class="menu-title">General Affair</span></a>
                <ul class="menu-content">
                    @if (in_array($level, $dashboard_ga))
                    <li class="{{ request()->is(['dashboardga', 'dashboardga/*']) ? 'active' : '' }}">
                        <a href="/dashboardga">
                            <i class="feather icon-home"></i>
                            <span class="menu-item" data-i18n="Second Level">Dashboard</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $mutasi_kendaraan))
                    <li class="{{ request()->is(['mutasikendaraan', 'mutasikendaraan/*']) ? 'active' : '' }}">
                        <a href="/mutasikendaraan">
                            <i class="feather icon-truck"></i>
                            <span class="menu-item" data-i18n="Second Level">Mutasi Kendaraan</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $service_kendaraan))
                    <li class="{{ request()->is(['servicekendaraan', 'servicekendaraan/*']) ? 'active' : '' }}">
                        <a href="/servicekendaraan">
                            <i class="fa fa-ambulance"></i>
                            <span class="menu-item" data-i18n="Second Level">Service Kendaraan</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $bad_stock))
                    <li class="{{ request()->is(['badstock', 'badstok/*']) ? 'active' : '' }}">
                        <a href="/badstock">
                            <i class="feather icon-box"></i>
                            <span class="menu-item" data-i18n="Second Level">Bad Stock</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $laporan_ga))
                    <li class="{{ request()->is(['laporanga', 'laporanga/*']) ? 'active' : '' }}">
                        <a href="/laporanga/servicekendaraan">
                            <i class="feather icon-file"></i>
                            <span class="menu-item" data-i18n="Second Level">Laporan</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if (in_array($level,$maintenance_menu))
            <li class=" nav-item">
                <a href="#"><i class="feather icon-umbrella"></i><span class="menu-title">Maintenance</span></a>
                <ul class="menu-content">
                    @if (in_array($level, $maintenance_pembelian))
                    <li class="{{ request()->is(['maintenance/pembelian']) ? 'active' : '' }}">
                        <a href="/maintenance/pembelian">
                            <i class="feather icon-clipboard"></i>
                            <span class="menu-item">Pembelian</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $maintenance_pemasukan))
                    <li class="{{ request()->is(['pemasukanmaintenance', 'pemasukanmaintenance/*']) ? 'active' : '' }}">
                        <a href="/pemasukanmaintenance">
                            <i class="feather icon-arrow-right"></i>
                            <span class="menu-item">Barang Masuk</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $maintenance_pengeluaran))
                    <li class="{{ request()->is(['pengeluaranmaintenance', 'pengeluaranmaintenance/*']) ? 'active' : '' }}">
                        <a href="/pengeluaranmaintenance">
                            <i class="feather icon-arrow-left"></i>
                            <span class="menu-item">Barang Keluar</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $laporan_maintenance))
                    <li class="{{ request()->is(['laporanmaintenance', 'laporanmaintenance/*']) ? 'active' : '' }}">
                        <a href="/laporanmaintenance/rekapbahanbakar">
                            <i class="feather icon-file"></i>
                            <span class="menu-item">Laporan</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            <li class=" nav-item">
                <a href="#"><i class="feather icon-settings"></i><span class="menu-title">Utilities</span></a>
                <ul class="menu-content">
                    @if (in_array($level, $saldoawalpiutang))
                    <li class="{{ request()->is(['saldoawalpiutang']) ? 'active' : '' }}">
                        <a href="/saldoawalpiutang">
                            <i class="feather icon-clipboard"></i>
                            <span class="menu-item">Saldo Awal Piutang</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $kirimlpc))
                    <li class="{{ request()->is(['lpc', 'lpc/*']) ? 'active' : '' }}">
                        <a href="/lpc">
                            <i class="feather icon-file-text"></i>
                            <span class="menu-item">Kirim LPC</span>
                        </a>
                    </li>
                    @endif
                    @if (in_array($level, $tutuplaporan))
                    <li class="{{ request()->is(['tutuplaporan', 'tutuplaporan/*']) ? 'active' : '' }}">
                        <a href="/tutuplaporan">
                            <i class="feather icon-file-text"></i>
                            <span class="menu-item">Tutup Laporan</span>
                        </a>
                    </li>
                    @endif

                    @if (in_array($level, $datausers))
                    <li class="{{ request()->is(['user', 'user/index']) ? 'active' : '' }}">
                        <a href="/user">
                            <i class="feather icon-users"></i>
                            <span class="menu-item">Users</span>
                        </a>
                    </li>
                    @endif
                    <li class="{{ request()->is(['user/gantipassword']) ? 'active' : '' }}">
                        <a href="/user/gantipassword">
                            <i class="fa fa-key"></i>
                            <span class="menu-item">Ganti Password</span>
                        </a>
                    </li>
                    <li class="{{ request()->is(['user/editprofile']) ? 'active' : '' }}">
                        <a href="/user/editprofile">
                            <i class="feather icon-user"></i>
                            <span class="menu-item">Edit Profile</span>
                        </a>
                    </li>
                </ul>
            </li>

        </ul>
    </div>
</div>
<!-- END: Main Menu-->
