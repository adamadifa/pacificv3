<!-- BEGIN: Main Menu-->


<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto"><a class="navbar-brand" href="{{asset('html/ltr/vertical-menu-template/index.html')}}">
                    <div class="brand-logo"></div>
                    <h2 class="brand-text mb-0">PACIFIC</h2>
                </a></li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary" data-ticon="icon-disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            @if (in_array($level,$dashboardadmin))
            <li class=" {{ request()->is(['dashboardadmin']) ? 'active' : '' }} nav-item"><a href="/dashboardadmin"><i class="feather icon-home"></i><span class="menu-title" data-i18n="Dashboard">Dashboard</span></a></li>
            @endif
            @if (in_array($level,$dashboardkepalapenjualan))
            <li class="{{ request()->is(['dashboardkepalapenjualan']) ? 'active' : '' }} nav-item"><a href="/dashboardkepalapenjualan"><i class="feather icon-home"></i><span class="menu-title" data-i18n="Dashboard">Dashboard</span></a></li>
            @endif
            @if (in_array($level,$dashboardkepalaadmin))
            <li class="{{ request()->is(['dashboardkepalaadmin']) ? 'active' : '' }} nav-item"><a href="/dashboardkepalaadmin"><i class="feather icon-home"></i><span class="menu-title" data-i18n="Dashboard">Dashboard</span></a></li>
            @endif
            @if (in_array($level,$dashboardadminpenjualan))
            <li class="{{ request()->is(['dashboardadminpenjualan']) ? 'active' : '' }} nav-item"><a href="/dashboardadminpenjualan"><i class="feather icon-home"></i><span class="menu-title" data-i18n="Dashboard">Dashboard</span></a></li>
            @endif
            @if (in_array($level,$dashboardaccounting))
            <li class="{{ request()->is(['dashboardaccounting']) ? 'active' : '' }} nav-item"><a href="/dashboardaccounting"><i class="feather icon-home"></i><span class="menu-title" data-i18n="Dashboard">Dashboard</span></a></li>
            @endif
            @if (in_array($level,$datamaster_view))
            <li class=" nav-item">
                <a href="#"><i class="feather icon-grid"></i><span class="menu-title">Data Master</span></a>
                <ul class="menu-content">
                    @if (in_array($level,$pelanggan_view))
                    <li class="{{ request()->is(['pelanggan','pelanggan/*']) ? 'active' : '' }}"><a href="/pelanggan"><i class="feather icon-users"></i><span class="menu-item">Pelanggan</span></a></li>
                    @endif
                    @if (in_array($level,$salesman_view))
                    <li class="{{ request()->is(['salesman','salesman/*']) ? 'active' : '' }}"><a href="/salesman"><i class="feather icon-users"></i><span class="menu-item">Salesman</span></a></li>
                    @endif
                    @if (in_array($level,$barang_view))
                    <li class="{{ request()->is(['barang','barang/*']) ? 'active' : '' }}"><a href="/barang"><i class="feather icon-grid"></i><span class="menu-item">Barang</span></a></li>
                    @endif
                    @if (in_array($level,$harga_view))
                    <li class="{{ request()->is(['harga','harga/*']) ? 'active' : '' }}"><a href="/harga"><i class="fa fa-money"></i><span class="menu-item">Harga</span></a></li>
                    @endif
                    @if (in_array($level,$kendaraan_view))
                    <li class="{{ request()->is(['kendaraan','kendaraan/*']) ? 'active' : '' }}"><a href="/kendaraan"><i class="feather icon-truck"></i><span class="menu-item">Kendaraan</span></a></li>
                    @endif
                    @if (in_array($level,$cabang_view))
                    <li class="{{ request()->is(['cabang','cabang/*']) ? 'active' : '' }}"><a href="/cabang"><i class="fa fa-bank"></i><span class="menu-item">Cabang</span></a></li>
                    @endif
                </ul>
            </li>
            @endif

            @if (in_array($level,$marketing))
            <li class=" nav-item">
                <a href="#"><i class="feather icon-radio"></i><span class="menu-title">Marketing</span></a>
                <ul class="menu-content">
                    @if (in_array($level,$penjualan_menu))
                    <li><a href="#"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Second Level">Penjualan</span></a>
                        <ul class="menu-content">
                            @if (in_array($level,$penjualan_input))
                            <li><a href="/penjualan/create"><i class="feather icon-shopping-cart"></i><span class="menu-item">Input Penjualan</span></a></li>
                            @endif
                            @if (in_array($level,$penjualan_view))
                            <li class="{{ request()->is(['penjualan','penjualan/*']) ? 'active' : '' }}"><a href="/penjualan"><i class="feather icon-shopping-bag"></i><span class="menu-item">Data Penjualan</span></a></li>
                            @endif
                            @if (in_array($level,$retur_view))
                            <li class="{{ request()->is(['retur','retur/*']) ? 'active' : '' }}"><a href="/retur"><i class="feather icon-package"></i><span class="menu-item">Data Retur</span></a></li>
                            @endif
                            @if (in_array($level,$limitkredit_view))
                            <li class="{{ request()->is(['limitkredit','limitkredit/*']) ? 'active' : '' }}"><a href="/limitkredit"><i class="feather icon-credit-card"></i><span class="menu-item">Limit Kredit</span></a></li>
                            @endif
                            @if (in_array($level,$laporan_penjualan))
                            <li class="{{ request()->is(['laporanpenjualan','laporanpenjualan/*','laporanretur','laporankasbesarpenjualan','laporankendaraan/*']) ? 'active' : '' }}"><a href="/laporanpenjualan/penjualan"><i class="feather icon-file-text"></i><span class="menu-item">Laporan</span></a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            @if (in_array($level,$keuangan))
            <li class=" nav-item">
                <a href="#"><i class="feather icon-dollar-sign"></i><span class="menu-title">Keuangan</span></a>
                <ul class="menu-content">
                    @if (in_array($level,$penjualan_keuangan))
                    <li><a href="#"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Second Level">Penjualan</span></a>
                        <ul class="menu-content">
                            @if (in_array($level,$giro_view))
                            <li class="{{ request()->is(['giro','giro/*']) ? 'active' : '' }}"><a href="/giro"><i class="feather icon-file-text"></i><span class="menu-item">Giro</span></a></li>
                            @endif
                            @if (in_array($level,$transfer_view))
                            <li class="{{ request()->is(['transfer','transfer/*']) ? 'active' : '' }}"><a href="/transfer"><i class="feather icon-file-text"></i><span class="menu-item">Transfer</span></a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            <li class=" nav-item">
                <a href="#"><i class="feather icon-settings"></i><span class="menu-title">Utilities</span></a>
                <ul class="menu-content">
                    @if (in_array($level,$kirimlpc))
                    <li class="{{ request()->is(['lpc','lpc/*']) ? 'active' : '' }}"><a href="/lpc"><i class="feather icon-file-text"></i><span class="menu-item">Kirim LPC</span></a></li>
                    @endif
                </ul>
            </li>

        </ul>
    </div>
</div>
<!-- END: Main Menu-->
