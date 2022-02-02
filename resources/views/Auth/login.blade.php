<!DOCTYPE html>
<!-- saved from url=(0063)https://www.bootstrapdash.com/demo/login-templates-pro/login-1/ -->
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Template</title>
    <link rel="stylesheet" href="{{asset('pacific/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('pacific/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('pacific/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('pacific/css/login.css')}}">
</head>

<body class="d-flex align-items-center justify-content-center">
    <section class="overlay">
        <div class="container-fluid">
            <div class="card login-card">
                <img src="{{asset('pacific/css/login.png')}}" alt="login" class="login-card-img">
                <div class="card-body">
                    <img src="{{asset('pacific/css/logo.png')}}" width="250px" height="70px" alt="">
                    <h2 class="login-card-title">Login</h2>
                    <p class="login-card-description">Silahkan Login Untuk Melanjutkan</p>
                    <form action="/postlogin" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="email" class="sr-only">Email</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Email">
                        </div>
                        <div class="form-group">
                            <label for="password" class="sr-only">Password</label>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="Password">
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block">LOGIN</button>
                    </form>

                </div>
            </div>
        </div>
    </section>
    <script src="{{asset('pacific/js/jquery-3.4.1.min.js')}}"></script>
    <script src="{{asset('pacific/js/popper.min.js')}}"></script>
    <script src="{{asset('pacific/js/bootstrap.min.js')}}"></script>

</body>

</html>
