@extends('layouts.midone')
@section('titlepage','Data Pelanggan')
@section('content')
<style>
    .card {
        margin-bottom: 1rem !important;
    }

</style>
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
        <div class="row">
            <div class="col-12">
                <div class="app-fixed-search">
                    <form action="/pelanggansalesman" method="GET">
                        <fieldset class="form-group position-relative has-icon-left m-0 mb-1">
                            <input type="text" class="form-control" name="nama_pelanggan" value="{{ Request('nama_pelanggan') }}" id="nama_pelanggan" placeholder="Cari Nama Pelanggan" autocomplete="off">
                            <div class="form-control-position">
                                <i class="feather icon-search"></i>
                            </div>
                        </fieldset>
                        <button class="btn btn-primary btn-block"><i class="feather icon-search"></i> Cari</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="row mt-1">
            <div class="col-12">
                @foreach ($pelanggan as $d)
                <a href="/pelanggan/showpelanggan?kode_pelanggan={{ Crypt::encrypt($d->kode_pelanggan) }}" style="color:rgb(107, 99, 99)">
                    <div class="row">
                        <div class="col-12">

                            <div class="card border-primary">
                                <div class="card-content">
                                    <div class="card-body" style="padding:8px 10px 8px 8px !important">
                                        <p class="card-text d-flex justify-content-between">
                                            <span class="d-flex justify-content-between">
                                                @if (!empty($d->foto))
                                                @php
                                                $path = Storage::url('pelanggan/'.$d->foto);
                                                @endphp
                                                <img src="{{ url($path) }}" class="rounded mr-75" alt="profile image" height="40" width="40">
                                                @else
                                                <img src="{{ asset('app-assets/images/slider/04.jpg') }}" class="rounded float-left mr-75" alt="profile image" height="50" width="50">
                                                @endif

                                                <span>
                                                    {{ $d->kode_pelanggan }} <br> {{ $d->nama_pelanggan }}
                                                </span>
                                            </span>


                                        </p>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
@push('myscript')
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
