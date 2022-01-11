 <!-- BEGIN: Main Menu-->
 <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
     <div class="navbar-header">
         <ul class="nav navbar-nav flex-row">
             <li class="nav-item mr-auto"><a class="navbar-brand" href="{{asset('html/ltr/vertical-menu-template/index.html')}}">
                     <div class="brand-logo"></div>
                     <h2 class="brand-text mb-0">Vuexy</h2>
                 </a></li>
             <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary" data-ticon="icon-disc"></i></a></li>
         </ul>
     </div>
     <div class="shadow-bottom"></div>
     <div class="main-menu-content">
         <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
             <li class=" nav-item"><a href="/dashboardadmin"><i class="feather icon-home"></i><span class="menu-title" data-i18n="Dashboard">Dashboard</span></a>
             </li>
             <li class=" navigation-header"><span>Data Master</span>
             <li class=" nav-item">
                 <a href="#"><i class="feather icon-grid"></i><span class="menu-title">Data Master</span></a>
                 <ul class="menu-content">
                     <li class="{{ request()->is(['pelanggan','pelanggan/*']) ? 'active' : '' }}"><a href="/pelanggan"><i class="feather icon-users"></i><span class="menu-item">Pelanggan</span></a></li>
                     <li class="{{ request()->is(['salesman','salesman/*']) ? 'active' : '' }}"><a href="/salesman"><i class="feather icon-users"></i><span class="menu-item">Salesman</span></a></li>
                     <li class="{{ request()->is(['barang','barang/*']) ? 'active' : '' }}"><a href="/barang"><i class="feather icon-grid"></i><span class="menu-item">Barang</span></a></li>
                     <li class="{{ request()->is(['harga','harga/*']) ? 'active' : '' }}"><a href="/harga"><i class="fa fa-money"></i><span class="menu-item">Harga</span></a></li>
                     <li class="{{ request()->is(['kendaraan','kendaraan/*']) ? 'active' : '' }}"><a href="/kendaraan"><i class="feather icon-truck"></i><span class="menu-item">Kendaraan</span></a></li>
                     <li class="{{ request()->is(['cabang','cabang/*']) ? 'active' : '' }}"><a href="/cabang"><i class="fa fa-bank"></i><span class="menu-item">Cabang</span></a></li>
                 </ul>
             </li>

         </ul>
     </div>
 </div>
 <!-- END: Main Menu-->
