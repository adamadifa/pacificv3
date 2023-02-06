<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="generator" content="">
    <title>SAP - Sales Automation Perfomance</title>

    <!-- manifest meta -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="manifest" href="manifest.json" />

    <!-- Favicons -->
    <link rel="apple-touch-icon" href="{{ asset('sap/assets/img/favicon180.png') }}" sizes="180x180">
    <link rel="icon" href="{{ asset('sap/assets/img/favicon32.png')}}" sizes="32x32" type="image/png">
    <link rel="icon" href="{{ asset('sap/assets/img/favicon16.png')}}" sizes="16x16" type="image/png">

    <!-- Google fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <!-- swiper carousel css -->
    <link rel="stylesheet" href="{{ asset('sap/assets/vendor/swiperjs-6.6.2/swiper-bundle.min.css')}}">
    <link rel="stylesheet" href="{{ asset('sap/dist/mc-calendar.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('sap/assets/vendor/daterangepicker/daterangepicker.css') }}" />
    <!-- style css for this template -->
    <link href="{{ asset('sap/assets/css/style.css')}}" rel="stylesheet" id="style">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />

</head>

<body class="body-scroll" data-page="index">

    <!-- loader section -->
    <div class="container-fluid loader-wrap">
        <div class="row h-100">
            <div class="col-10 col-md-6 col-lg-5 col-xl-3 mx-auto text-center align-self-center">
                <div class="loader-cube-wrap loader-cube-animate mx-auto">
                    <img src="{{ asset('sap/assets/img/logo.png') }}" alt="Logo">
                </div>
                <p class="mt-4">It's time for track budget<br><strong>Please wait...</strong></p>
            </div>
        </div>
    </div>
    <!-- loader section ends -->

    <!-- Sidebar main menu -->
    <div class="sidebar-wrap  sidebar-pushcontent">
        <!-- Add overlay or fullmenu instead overlay -->
        <div class="closemenu text-muted">Close Menu</div>
        <div class="sidebar dark-bg">
            <!-- user information -->
            <div class="row my-3">
                <div class="col-12 ">
                    <div class="card shadow-sm bg-opac text-white border-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-auto">
                                    <figure class="avatar avatar-44 rounded-15">
                                        <img src="assets/img/user1.jpg" alt="">
                                    </figure>
                                </div>
                                <div class="col px-0 align-self-center">
                                    <p class="mb-1">Maxartkiller</p>
                                    <p class="text-muted size-12">New York City, US</p>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-44 btn-light">
                                        <i class="bi bi-box-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card bg-opac text-white border-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h1 class="display-4">100.00</h1>
                                    </div>
                                    <div class="col-auto">
                                        <p class="text-muted">Wallet Balance</p>
                                    </div>
                                    <div class="col text-end">
                                        <p class="text-muted"><a href="addmoney.html">+ Top up</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- user emnu navigation -->
            @include('layouts.sap.sidebar')
        </div>
    </div>
    <!-- Sidebar main menu ends -->

    <!-- Begin page -->
    <main class="h-100">

        <!-- Header ends -->

        <!-- main page content -->
        <div class="main-container container">
            @yield('content')
        </div>
        <!-- main page content ends -->


    </main>
    <!-- Page ends-->

    <!-- Footer -->
    @include('layouts.sap.footer')
    <!-- Footer ends-->



    <!-- Required jquery and libraries -->
    <script src="{{ asset('sap/assets/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('sap/assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('sap/assets/vendor/bootstrap-5/js/bootstrap.bundle.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!-- cookie js -->
    <script src="{{ asset('sap/assets/js/jquery.cookie.js') }}"></script>

    <!-- Customized jquery file  -->
    <script src="{{  asset('sap/assets/js/main.js') }}"></script>
    <script src="{{ asset('sap/assets/js/color-scheme.js') }}"></script>

    <!-- PWA app service registration and works -->
    <script src="{{ asset('sap/assets/js/pwa-services.js') }}"></script>

    <!-- Chart js script -->
    <script src="{{ asset('sap/assets/vendor/chart-js-3.3.1/chart.min.js') }}"></script>

    <!-- Progress circle js script -->
    <script src="{{ asset('sap/assets/vendor/progressbar-js/progressbar.min.js') }}"></script>

    <!-- swiper js script -->
    <script src="{{ asset('sap/assets/vendor/swiperjs-6.6.2/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('sap/dist/mc-calendar.min.js') }}"></script>
    <!-- page level custom script -->
    <script src="{{ asset('sap/assets/js/app.js') }}"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    @stack('myscript')
</body>

</html>
