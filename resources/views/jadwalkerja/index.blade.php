@extends('layouts.midone')
@section('titlepage','Jadwal Kerja')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Jadwal Kerja</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/jadwalkerja">Jadwal Kerja</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        @include('layouts.notification')
        <div class="row">
            <div class="col-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Non Shift</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <p>
                                    <b>Jumlah Karyawan : </b> {{ $jmlnonshift }}
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-striped table-hover-animation" id="tabelnonshift">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Kantor</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size:11px !Important">
                                        @foreach ($nonshift as $d)
                                        <tr nik="{{ $d->nik }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $d->nik }}</td>
                                            <td>{{ ucwords(strtolower($d->nama_karyawan)) }}</td>
                                            <td>{{ $d->id_kantor }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Shift 1</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <p>
                                    <b>Jumlah Karyawan : </b> {{ $jmlshift1 }}
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-striped table-hover-animation" id="tabelshift1">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Kantor</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size:11px !Important">
                                        @foreach ($shift1 as $d)
                                        <tr nik="{{ $d->nik }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $d->nik }}</td>
                                            <td>{{ ucwords(strtolower($d->nama_karyawan)) }}</td>
                                            <td>{{ $d->id_kantor }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Shift 2</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <p>
                                    <b>Jumlah Karyawan : </b> {{ $jmlshift2 }}
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-striped table-hover-animation" id="tabelshift2">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Kantor</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size:11px !Important">
                                        @foreach ($shift2 as $d)
                                        <tr nik="{{ $d->nik }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $d->nik }}</td>
                                            <td>{{ ucwords(strtolower($d->nama_karyawan)) }}</td>
                                            <td>{{ $d->id_kantor }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Shift 3</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <p>
                                    <b>Jumlah Karyawan : </b> {{ $jmlshift3 }}
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-striped table-hover-animation" id="tabelshift3">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Kantor</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size:11px !Important">
                                        @foreach ($shift3 as $d)
                                        <tr nik="{{ $d->nik }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $d->nik }}</td>
                                            <td>{{ ucwords(strtolower($d->nama_karyawan)) }}</td>
                                            <td>{{ $d->id_kantor }}</td>
                                        </tr>
                                        @endforeach
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

<div class="modal fade text-left" id="mdlpindahjadwal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Pindah Jadwal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="loadpindahjadwal">

            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        $('#tabelnonshift tbody > tr').click(function() {
            var nik = $(this).attr('nik');
            $("#mdlpindahjadwal").modal("show");
            $.ajax({
                type: 'POST'
                , url: '/jadwalkerja/pindahjadwal'
                , data: {
                    _token: '{{ csrf_token() }}'
                    , nik: nik
                }
                , cache: false
                , success: function(respond) {
                    $("#loadpindahjadwal").html(respond);
                }
            });
            //your code
        });

        $('#tabelshift3 tbody > tr').click(function() {
            var nik = $(this).attr('nik');
            $("#mdlpindahjadwal").modal("show");
            $.ajax({
                type: 'POST'
                , url: '/jadwalkerja/pindahjadwal'
                , data: {
                    _token: '{{ csrf_token() }}'
                    , nik: nik
                }
                , cache: false
                , success: function(respond) {
                    $("#loadpindahjadwal").html(respond);
                }
            });
            //your code
        });


        $('#tabelshift1 tbody > tr').click(function() {
            var nik = $(this).attr('nik');
            $("#mdlpindahjadwal").modal("show");
            $.ajax({
                type: 'POST'
                , url: '/jadwalkerja/pindahjadwal'
                , data: {
                    _token: '{{ csrf_token() }}'
                    , nik: nik
                }
                , cache: false
                , success: function(respond) {
                    $("#loadpindahjadwal").html(respond);
                }
            });
            //your code
        });


        $('#tabelshift2 tbody > tr').click(function() {
            var nik = $(this).attr('nik');
            $("#mdlpindahjadwal").modal("show");
            $.ajax({
                type: 'POST'
                , url: '/jadwalkerja/pindahjadwal'
                , data: {
                    _token: '{{ csrf_token() }}'
                    , nik: nik
                }
                , cache: false
                , success: function(respond) {
                    $("#loadpindahjadwal").html(respond);
                }
            });
            //your code
        });



    });

</script>
@endpush
