<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{asset('pacific/style.css')}}">
    <title>
        Portal Pacific Versi 3.0
    </title>
</head>

<body>
    <div id="container" class="container">
        <!-- FORM SECTION -->
        <div class="row">
            <!-- SIGN UP -->
            <div class="col align-items-center flex-col sign-up">
                <div class="form-wrapper align-items-center">

                </div>
                <div class="form-wrapper">
                    <div class="social-list align-items-center sign-up">
                        <div class="align-items-center facebook-bg">
                            <i class='bx bxl-facebook'></i>
                        </div>
                        <div class="align-items-center google-bg">
                            <i class='bx bxl-google'></i>
                        </div>
                        <div class="align-items-center twitter-bg">
                            <i class='bx bxl-twitter'></i>
                        </div>
                        <div class="align-items-center insta-bg">
                            <i class='bx bxl-instagram-alt'></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END SIGN UP -->
            <!-- SIGN IN -->
            <div class="col align-items-center flex-col sign-in">
                @include('layouts.notification') <br>
                <form method="POST" action="/postlogin">
                    @csrf
                    <div class="form-wrapper align-items-center">
                        <div class="form sign-in">
                            <div class="input-group">
                                <i class='bx bxs-user'></i>
                                <input type="text" name="email" id="email" placeholder="Email">
                            </div>
                            <div class="input-group">
                                <i class='bx bxs-lock-alt'></i>
                                <input type="password" name="password" id="password" placeholder="Password">
                            </div>
                            <button>
                                Sign in
                            </button>

                        </div>
                    </div>
                </form>
                <div class="form-wrapper">
                    <div class="social-list align-items-center sign-in">
                        <div class="align-items-center facebook-bg">
                            <i class='bx bxl-facebook'></i>
                        </div>
                        <div class="align-items-center google-bg">
                            <i class='bx bxl-google'></i>
                        </div>
                        <div class="align-items-center twitter-bg">
                            <i class='bx bxl-twitter'></i>
                        </div>
                        <div class="align-items-center insta-bg">
                            <i class='bx bxl-instagram-alt'></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END SIGN IN -->
        </div>
        <!-- END FORM SECTION -->
        <!-- CONTENT SECTION -->
        <div class="row content-row">
            <!-- SIGN IN CONTENT -->
            <div class="col align-items-center flex-col">
                <div class="text sign-in">
                    <h2>
                        Selamat Datang
                    </h2>
                    <p>di Portal Pacific Tasikmalaya,<br> Silahkan Login Untuk Melanjutkan !</p>

                </div>
                <div class="img sign-in">
                    <img src="{{asset('pacific/css/login.png')}}" alt="welcome">
                </div>
            </div>
        </div>
    </div>

    <script src="{{asset('pacific/index.js')}}"></script>
</body>

</html>
