@extends('layouts.sap.sap_noheader')
@section('content')
<style>
    .menu>a {
        color: inherit;
        font-family: 'Poppins';
        font-size: 0.8rem
    }

    .menu>.active {
        color: rgb(253, 190, 0) !important;
        border-bottom: 2px solid rgb(253, 190, 0);
        font-size: 0.9rem;
    }

    #main-profile {
        box-shadow: 2px 2px 3px rgba(88, 88, 88, 0.8);
    }

    .backbutton {
        color: white !important;
        position: absolute;
        top: 10px;
        font-size: 1.5rem
    }

</style>
<div class="row text-white" style="background-color:#b11036;" id="main-profile">
    <a href="/salesperformance" class="backbutton">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div class="col-12 text-center" style="padding:20px">
        <div class="avatar avatar-80 alert-danger text-danger rounded-circle">
            <img src="{{ asset('app-assets/marker/marker.png') }}" class="avatar avatar-80 rounded-circle" alt="">
        </div>
        <div class="datasalesman mt-2">
            <h3>{{ $salesman->nama_karyawan }}</h3>
            <span>{{ $salesman->nama_cabang }}</span>
        </div>
    </div>
    <div class="menu d-flex justify-content-between mb-2">
        <a href="#" class="active" id="penjualan">Penjualan</a>
        <a href="#" id="cashin">Cashin</a>
        <a href="#" id="target">Target</a>
        <a href="#" id="kunjungan">Kunjungan</a>
    </div>
</div>
<div class="" id="loaddata">
</div>
@endsection
@push('myscript')

<script>
    $(function() {
        var id_karyawan = "{{ Request('id_karyawan') }}";
        var dari = "{{ Request('dari') }}";
        var sampai = "{{ Request('sampai') }}";

        function getpenjualansalesman() {
            $.ajax({
                type: 'POST'
                , url: '/sap/getpenjualansalesman'
                , data: {
                    _token: '{{ csrf_token() }}'
                    , id_karyawan: id_karyawan
                    , dari: dari
                    , sampai: sampai
                }
                , success: function(respond) {
                    $("#loaddata").html(respond);
                    var swiper2 = new Swiper(".connectionwiper", {
                        slidesPerView: "auto"
                        , spaceBetween: 0
                        , pagination: false
                    });
                }
            , });

        }

        function getcashinsalesman() {
            $.ajax({
                type: 'POST'
                , url: '/sap/getcashinsalesman'
                , data: {
                    _token: '{{ csrf_token() }}'
                    , id_karyawan: id_karyawan
                    , dari: dari
                    , sampai: sampai
                }
                , success: function(respond) {
                    $("#loaddata").html(respond);
                }
            , });
        }

        function getkunjungansalesman() {
            $.ajax({
                type: 'POST'
                , url: '/sap/getkunjungansalesman'
                , data: {
                    _token: '{{ csrf_token() }}'
                    , id_karyawan: id_karyawan
                    , dari: dari
                    , sampai: sampai
                }
                , success: function(respond) {
                    $("#loaddata").html(respond);
                }
            , });
        }

        function gettargetsalesman() {
            $.ajax({
                type: 'POST'
                , url: '/sap/gettargetsalesman'
                , data: {
                    _token: '{{ csrf_token() }}'
                    , id_karyawan: id_karyawan
                    , dari: dari
                    , sampai: sampai
                }
                , success: function(respond) {
                    $("#loaddata").html(respond);
                }
            , });
        }
        getpenjualansalesman();

        $("#cashin").click(function(e) {
            $("a").removeClass("active");
            $(this).addClass("active");
            getcashinsalesman();
        });

        $("#penjualan").click(function(e) {
            $("a").removeClass("active");
            $(this).addClass("active");
            getpenjualansalesman();

        });

        $("#kunjungan").click(function(e) {
            $("a").removeClass("active");
            $(this).addClass("active");
            getkunjungansalesman();
        });

        $("#target").click(function(e) {
            $("a").removeClass("active");
            $(this).addClass("active");
            gettargetsalesman();
        });


    });

</script>
@endpush
