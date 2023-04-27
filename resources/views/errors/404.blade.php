<!DOCTYPE html>
<html>

<head>
    <title>Halaman Tidak Ditemukan</title>
    <link type="text/css" rel="stylesheet" href="{{ asset('error/404.css') }}" />
    <link rel="icon" type="image/x-icon" href="{{ asset('error/404-1.png') }}">
</head>

<body class="permission_denied">
    <div id="tsparticles"></div>
    <div class="denied__wrapper">
        <h1>404</h1>
        <h3>LOST IN <span>SPACE</span> Halaman Yang Dituju Tidak Ditemukan !.</h3>
        <img id="astronaut" src="{{ asset('error/astronaut.svg') }}" />
        <img id="planet" src="{{ asset('error/planet.svg') }}" />
        @if (request()->is(['homesap', 'salesperformance', 'getsalesperfomance','sap/*']))
        <a href="/homesap"><button class="denied__link">Go Home</button></a>
        @else
        <a href="/home"><button class="denied__link">Go Home</button></a>
        @endif
        <form action="/postlogout" method="POST">
            @csrf
            <button type="submit" class="denied__link"> Logout</button>
        </form>
    </div>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/tsparticles@2.3.4/tsparticles.bundle.min.js"></script>
    <script type="text/javascript" src="js/404.js"></script>

</html>
