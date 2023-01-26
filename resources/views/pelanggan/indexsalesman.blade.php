@extends('layouts.midone')
@section('titlepage','Data Pelanggan')
@section('content')
<style>
    .card {
        margin-bottom: 1rem !important;
    }

</style>
@push('mystyle')
@livewireStyles
@endpush
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h4 class="content-header-title float-left mb-0">Data Pelanggan</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <div class="row mb-2">
            <div class="col-12">
                <a href="/pelanggan/create" class="btn btn-success btn-block"><i class="fa fa-plus mr-1"></i> Register New Outlet</a>
            </div>
        </div>

        <div class="row mt-1">
            <div class="col-12">
                @include('layouts.notification')
                @livewire('pelanggan')
            </div>
        </div>
    </div>
</div>

@endsection
@push('myscript')
@livewireScripts
<script>
    $(function() {


        function loadsalesmancabang() {
            var cabang = "{{ Auth::user()->kode_cabang }}";
            if (cabang != 'PST') {
                var kode_cabang = cabang;
            } else {
                var kode_cabang = $("#kode_cabang").val();
            }
            var id_karyawan = "{{ Request('id_karyawan') }}";


            $.ajax({
                type: 'POST'
                , url: '/salesman/getsalescab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , id_karyawan: id_karyawan
                }
                , cache: false
                , success: function(respond) {
                    $("#id_karyawan").html(respond);
                }
            });
        }




        loadsalesmancabang();

        $("#kode_cabang").change(function() {
            loadsalesmancabang();
        });


        $('.delete-confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            event.preventDefault();
            swal({
                    title: `Are you sure you want to delete this record?`
                    , text: "If you delete this, it will be gone forever."
                    , icon: "warning"
                    , buttons: true
                    , dangerMode: true
                , })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
        });

        $(".detail").click(function(e) {
            e.preventDefault();
            window.location = $(this).data("href");
        });
    });

</script>
@endpush
