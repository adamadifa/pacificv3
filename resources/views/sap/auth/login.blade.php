<!doctype html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="generator" content="">
    <title>Sales Automation Platform</title>

    <!-- manifest meta -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="manifest" href="{{ asset('sap/manifest.json') }}" />

    <!-- Favicons -->
    <link rel="apple-touch-icon" href="{{ asset('sap/assets/img/favicon180.png') }}" sizes="180x180">
    <link rel="icon" href="{{ asset('sap/assets/img/favicon32.png') }}" sizes="32x32" type="image/png">
    <link rel="icon" href="{{ asset('sap/assets/img/favicon16.png') }}" sizes="16x16" type="image/png">

    <!-- Google fonts-->

    <link rel="preconnect" href="{{ asset('sap/') }}https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <!-- style css for this template -->
    <link href="{{ asset('sap/assets/css/style.css') }}" rel="stylesheet" id="style">
</head>

<body class="body-scroll d-flex flex-column h-100" data-page="signin">

    <!-- loader section -->
    <div class="container-fluid loader-wrap">
        <div class="row h-100">
            <div class="col-10 col-md-6 col-lg-5 col-xl-3 mx-auto text-center align-self-center">
                <div class="loader-cube-wrap loader-cube-animate mx-auto">
                    <img src="{{ asset('sap/assets/img/logo.png') }}" alt="Logo">
                </div>
                <p class="mt-4">www.pedasalami.com<br><strong>Please wait...</strong></p>
            </div>
        </div>
    </div>
    <!-- loader section ends -->

    <!-- Begin page content -->
    <main class="container-fluid h-100">
        <div class="row h-100 overflow-auto">
            <div class="col-12 text-center mb-auto px-0">
                <header class="header">

                </header>
            </div>
            <div class="col-10 col-md-6 col-lg-5 col-xl-3 mx-auto align-self-center text-center py-4">
                <div class="loader-cube-wrap loader-cube-animate" style="height:40px !important">
                    <img src="{{ asset('sap/assets/img/logo.png') }}" alt="Logo">
                </div>
                <h4 class="mb-4 text-color-theme mt-2" style="font-size: 14px">Silahkan Login</h4>
                <form class="was-validated needs-validation" novalidate method="POST" action="/postloginsap">
                    @csrf
                    <div class="form-group form-floating mb-3 is-valid">
                        <input type="text" name="email" class="form-control" id="email" placeholder="Username">
                        <label class="form-control-label" for="email">Username</label>
                    </div>

                    <div class="form-group form-floating is-invalid mb-3">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                        <label class="form-control-label" for="password">Password</label>
                        <button type="button" class="text-danger tooltip-btn" data-bs-toggle="tooltip" data-bs-placement="left" title="Enter valid Password" id="passworderror">
                            <i class="bi bi-info-circle"></i>
                        </button>
                    </div>
                    {{-- <p class="mb-3 text-center">
                        <a href="forgot-password.html" class="">
                            Forgot your password?
                        </a>
                    </p> --}}

                    <button type="submit" class="btn btn-lg btn-default w-100 mb-4 shadow">
                        Sign in
                    </button>
                </form>
                {{-- <p class="mb-2 text-muted">Don't have account?</p>
                <a href="signup.html" target="_self" class="">
                    Sign up <i class="bi bi-arrow-right"></i>
                </a> --}}

            </div>

        </div>
    </main>


    <!-- Required jquery and libraries -->
    <script src="{{ asset('sap/assets/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('sap/assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('sap/assets/vendor/bootstrap-5/js/bootstrap.bundle.min.js') }}"></script>

    <!-- cookie js -->
    <script src="{{ asset('sap/assets/js/jquery.cookie.js') }}"></script>

    <!-- Customized jquery file  -->
    <script src="{{ asset('sap/assets/js/main.js') }}"></script>
    <script src="{{ asset('sap/assets/js/color-scheme.js') }}"></script>

    <!-- PWA app service registration and works -->
    <script src="{{ asset('sap/assets/js/pwa-services.js') }}"></script>

    <!-- page level custom script -->
    <script src="{{ asset('sap/assets/js/app.js') }}"></script>

</body>

</html>
