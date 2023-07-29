@extends('layouts.midone')
@section('titlepage','Atur Jadwal')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Atur Jadwal Periode {{ date('d-m-Y',strtotime($konfigurasijadwal->dari)) }} s/d {{ date('d-m-Y',strtotime($konfigurasijadwal->sampai)) }}</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/konfigurasijadwal">Atur Jadwal</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        @include('layouts.notification')
        <div class="row mb-2">
            <div class="col-12 text-right">
                <button class="btn btn-warning" id="gantishift"><i class="feather icon-refresh-cw mr-1"></i>Ganti Shift</button>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <div class="card">
                    <div class="card-header">
                        <a href="#" class="btn btn-primary aturshift" shift="1"><i class="fa fa-plus mr-1"></i> Atur Shift 1</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <table class="table  table-hover-animation" id="tabelshift1">
                                    <thead>
                                        <tr>
                                            <th>Nik</th>
                                            <th>Nama Karyawan</th>
                                            <th>Jabatan</th>
                                            <th>Grup</th>
                                        </tr>
                                    </thead>
                                    <tbody id="loadshift1" style="font-size:11px !important">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-header">
                        <a href="#" class="btn btn-primary aturshift" shift="2"><i class="fa fa-plus mr-1"></i> Atur Shift 2</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <table class="table  table-hover-animation" id="tableshift2">
                                    <thead>
                                        <tr>
                                            <th>Nik</th>
                                            <th>Nama Karyawan</th>
                                            <th>Jabatan</th>
                                            <th>Grup</th>
                                        </tr>
                                    </thead>
                                    <tbody id="loadshift2" style="font-size:11px !important">

                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-header">
                        <a href="#" class="btn btn-primary aturshift" shift="3"><i class="fa fa-plus mr-1"></i> Atur Shift 3</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <table class="table  table-hover-animation" id="tabelnonshift">
                                    <thead>
                                        <tr>
                                            <th>Nik</th>
                                            <th>Nama Karyawan</th>
                                            <th>Jabatan</th>
                                            <th>Grup</th>
                                        </tr>
                                    </thead>
                                    <tbody id="loadshift3" style="font-size:11px !important">

                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="mdlaturshift" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Atur Shift <span id="kategorishift"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="loadaturshift">

            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdlgantishift" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 800px !important">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Ganti Shift <span id="kategorishift"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="loadgantishift">

            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {
        $(".aturshift").click(function(e) {
            e.preventDefault();
            var shift = $(this).attr('shift');
            var kode_setjadwal = "{{ $konfigurasijadwal->kode_setjadwal }}";
            $("#kategorishift").text(shift);
            $('#mdlaturshift').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $.ajax({
                type: 'POST'
                , url: '/konfigurasijadwal/aturshift'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , shift: shift
                    , kode_setjadwal: kode_setjadwal
                }
                , cache: false
                , success: function(respond) {
                    $("#loadaturshift").html(respond);
                }
            });
        });


        $("#gantishift").click(function(e) {
            e.preventDefault();
            var kode_setjadwal = "{{ $konfigurasijadwal->kode_setjadwal }}";
            $('#mdlgantishift').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $.ajax({
                type: 'POST'
                , url: '/konfigurasijadwal/gantishift'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_setjadwal: kode_setjadwal
                }
                , cache: false
                , success: function(respond) {
                    $("#loadgantishift").html(respond);
                }
            });
        });


        function showshift(kode_jadwal) {
            var kode_setjadwal = "{{ $konfigurasijadwal->kode_setjadwal }}";
            $.ajax({
                type: 'POST'
                , url: '/konfigurasijadwal/showjadwal'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_jadwal: kode_jadwal
                    , kode_setjadwal: kode_setjadwal
                }
                , cache: false
                , success: function(respond) {
                    if (kode_jadwal == "JD002") {
                        $("#loadshift1").html(respond);
                    } else if (kode_jadwal == "JD003") {
                        $("#loadshift2").html(respond);

                    } else if (kode_jadwal == "JD004") {
                        $("#loadshift3").html(respond);

                    }
                }
            });
        }

        showshift("JD002");
        showshift("JD003");
        showshift("JD004");
    });

</script>
@endpush
