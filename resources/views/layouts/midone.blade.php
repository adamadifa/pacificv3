<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>@yield('titlepage')</title>
    @include('layouts.style')
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->
@php
$level = Auth::user()->level;

@endphp
<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static" data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">
    <div id="layout">


        @include('layouts.navheader')
        @include('layouts.navbar')




        <!-- BEGIN: Content-->

        <div class="app-content content">
            <div class="content-overlay"></div>
            <div class="header-navbar-shadow"></div>
            @yield('content')
            <!-- BEGIN: Customizer-->
            <div class="customizer d-none d-md-block"><a class="customizer-toggle d-flex align-items-center justify-content-center" href="#"><i class="spinner-grow white"></i></a>
                <div class="customizer-content">
                    <!-- Customizer header -->
                    <div class="customizer-header px-2 pt-1 pb-0 position-relative">
                        <h4 class="mb-0">Daftar User Online</h4>
                        <p class="m-0">User Online</p>

                        <a class="customizer-close" href="#"><i data-feather="x"></i></a>
                    </div>
                    <hr>
                    @foreach ($users as $d)
                    <div class="customizer-menu px-2">

                        <div id="customizer-menu-collapsible" class="d-flex justify-content-start align-items-center">
                            <div class="avatar mr-50">
                                <img src="{{ asset('app-assets/images/avatar.png') }}" alt="avtar img holder" height="35" width="35">
                            </div>
                            <div class="user-page-info ml-1">
                                <p class="mt-1">{{ $d->name }}<br><small>Last Seen {{ Carbon\Carbon::parse($d->last_seen)->diffForHumans() }}</small></p>
                            </div>
                            @if(Cache::has('user-is-online-' . $d->id))
                            <div class="ml-auto"><i class="fa fa-circle success"></i></div>
                            @else
                            <div class="ml-auto"><i class="fa fa-circle danger"></i></div>
                            @endif


                        </div>

                    </div>
                    @endforeach
                </div>
            </div>
            <!-- End: Customizer-->

        </div>
        <!-- END: Content-->



        @include('layouts.footer')

        @include('layouts.script')
    </div>
</body>
<!-- END: Body-->

</html>
