@extends('layouts.sap.sap')
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

</style>
<div class="row text-white" style="background-color:#b11036" id="main-profile">
    <div class="col-12 text-center" style="padding:20px">
        <div class="avatar avatar-80 alert-danger text-danger rounded-circle">
            <img src="http://127.0.0.1:8000/app-assets/marker/marker.png" class="avatar avatar-80 rounded-circle" alt="">
        </div>
        <div class="datasalesman mt-2">
            <h3>{{ $salesman->nama_karyawan }}</h3>
            <span>{{ $salesman->nama_cabang }}</span>
        </div>
    </div>
    <div class="menu d-flex justify-content-between mb-2">
        <a href="#" class="active" id="penjualan">Penjualan</a>
        <a href="#" id="cashin">Cashin</a>
        <a href="#" id="kunjungan">Kunjungan</a>
    </div>
</div>
<div class="mt-3" id="loaddata">
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
                }
            , });
        }

        getpenjualansalesman();

        $("#cashin").click(function(e) {
            $("a").removeClass("active");
            $(this).addClass("active");
        });

        $("#penjualan").click(function(e) {
            $("a").removeClass("active");
            $(this).addClass("active");

        });

        $("#kunjungan").click(function(e) {
            $("a").removeClass("active");
            $(this).addClass("active");
        });


    });

</script>
@endpush
