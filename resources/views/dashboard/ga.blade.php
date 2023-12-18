@extends('layouts.midone')
@section('titlepage', 'Dashboard')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <!-- Dashboard Analytics Start -->
            <section id="nav-justified">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card overflow-hidden">
                                    <div class="card-header">
                                        <h4 class="card-title">Jatuh Tempo KIR</h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body">

                                            <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link" id="profile-tab-justified" data-toggle="tab"
                                                        href="#kir-lewat" role="tab" aria-controls="profile-just"
                                                        aria-selected="false">Lewat JT<span
                                                            class="badge badge-pill bg-danger">{{ $jml_kir_sudahlewat }}</span></a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link active" id="home-tab-justified" data-toggle="tab"
                                                        href="#home-just" role="tab" aria-controls="home-just"
                                                        aria-selected="true">Bulan Ini <span
                                                            class="badge badge-pill bg-danger">{{ $jml_kir_bulanini }}</span></a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="profile-tab-justified" data-toggle="tab"
                                                        href="#profile-just" role="tab" aria-controls="profile-just"
                                                        aria-selected="false">Bulan Depan <span
                                                            class="badge badge-pill bg-warning">{{ $jml_kir_bulandepan }}</span></a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="messages-tab-justified" data-toggle="tab"
                                                        href="#messages-just" role="tab" aria-controls="messages-just"
                                                        aria-selected="false">2 Bulan Lagi <span
                                                            class="badge badge-pill bg-success">{{ $jml_kir_duabulan }}</span></a>
                                                </li>
                                            </ul>

                                            <!-- Tab panes -->

                                            <div class="tab-content pt-1">
                                                <div class="tab-pane" id="kir-lewat" role="tabpanel"
                                                    aria-labelledby="profile-tab-justified">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>No. Polisi</th>
                                                                <th>Kendaraan</th>
                                                                <th>JT KIR</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="font-size: 12px !important">
                                                            @foreach ($kir_sudahlewat as $d)
                                                                @php
                                                                    $hariini = date('Y-m-d');
                                                                    $tgl1 = strtotime($hariini);
                                                                    $tgl2 = strtotime($d->jatuhtempo_kir);
                                                                    $selisih = $tgl2 - $tgl1;
                                                                    $jmlhari = $selisih / 60 / 60 / 24;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $d->no_polisi }}</td>
                                                                    <td>{{ $d->merk }} {{ $d->tipe_kendaraan }}
                                                                        {{ $d->tipe }}</td>
                                                                    <td>{{ date('d-m-Y', strtotime($d->jatuhtempo_kir)) }}
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge {{ $hariini > $d->jatuhtempo_kir ? 'bg-danger' : 'bg-success' }}">
                                                                            {{ $hariini > $d->jatuhtempo_kir ? 'Sudah lewat' : $jmlhari . ' Hari Lagi' }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="tab-pane active" id="home-just" role="tabpanel"
                                                    aria-labelledby="home-tab-justified">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>No. Polisi</th>
                                                                <th>Kendaraan</th>
                                                                <th>JT KIR</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="font-size: 12px !important">
                                                            @foreach ($kir_bulanini as $d)
                                                                @php
                                                                    $hariini = date('Y-m-d');
                                                                    $tgl1 = strtotime($hariini);
                                                                    $tgl2 = strtotime($d->jatuhtempo_kir);
                                                                    $selisih = $tgl2 - $tgl1;
                                                                    $jmlhari = $selisih / 60 / 60 / 24;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $d->no_polisi }}</td>
                                                                    <td>{{ $d->merk }} {{ $d->tipe_kendaraan }}
                                                                        {{ $d->tipe }}</td>
                                                                    <td>{{ date('d-m-Y', strtotime($d->jatuhtempo_kir)) }}
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge {{ $hariini > $d->jatuhtempo_kir ? 'bg-danger' : 'bg-success' }}">
                                                                            {{ $hariini > $d->jatuhtempo_kir ? 'Sudah lewat' : $jmlhari . ' Hari Lagi' }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="tab-pane" id="profile-just" role="tabpanel"
                                                    aria-labelledby="profile-tab-justified">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>No. Polisi</th>
                                                                <th>Kendaraan</th>
                                                                <th>JT KIR</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="font-size: 12px !important">
                                                            @foreach ($kir_bulandepan as $d)
                                                                @php
                                                                    $hariini = date('Y-m-d');
                                                                    $tgl1 = strtotime($hariini);
                                                                    $tgl2 = strtotime($d->jatuhtempo_kir);
                                                                    $selisih = $tgl2 - $tgl1;
                                                                    $jmlhari = $selisih / 60 / 60 / 24;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $d->no_polisi }}</td>
                                                                    <td>{{ $d->merk }} {{ $d->tipe_kendaraan }}
                                                                        {{ $d->tipe }}</td>
                                                                    <td>{{ date('d-m-Y', strtotime($d->jatuhtempo_kir)) }}
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge {{ $hariini > $d->jatuhtempo_kir ? 'bg-danger' : 'bg-success' }}">
                                                                            {{ $hariini > $d->jatuhtempo_kir ? 'Sudah lewat' : $jmlhari . ' Hari Lagi' }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="tab-pane" id="messages-just" role="tabpanel"
                                                    aria-labelledby="messages-tab-justified">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>No. Polisi</th>
                                                                <th>Kendaraan</th>
                                                                <th>JT KIR</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="font-size: 12px !important">
                                                            @foreach ($kir_duabulan as $d)
                                                                @php
                                                                    $hariini = date('Y-m-d');
                                                                    $tgl1 = strtotime($hariini);
                                                                    $tgl2 = strtotime($d->jatuhtempo_kir);
                                                                    $selisih = $tgl2 - $tgl1;
                                                                    $jmlhari = $selisih / 60 / 60 / 24;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $d->no_polisi }}</td>
                                                                    <td>{{ $d->merk }} {{ $d->tipe_kendaraan }}
                                                                        {{ $d->tipe }}</td>
                                                                    <td>{{ date('d-m-Y', strtotime($d->jatuhtempo_kir)) }}
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge {{ $hariini > $d->jatuhtempo_kir ? 'bg-danger' : 'bg-success' }}">
                                                                            {{ $hariini > $d->jatuhtempo_kir ? 'Sudah lewat' : $jmlhari . ' Hari Lagi' }}
                                                                        </span>
                                                                    </td>
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
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card overflow-hidden">
                                    <div class="card-header">
                                        <h4 class="card-title">Jatuh Tempo Pajak 1 Tahun</h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body">

                                            <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link" id="profile-tab-justified" data-toggle="tab"
                                                        href="#pajak-sudahlewat" role="tab" aria-controls="profile-just"
                                                        aria-selected="false">Lewat JT <span
                                                            class="badge badge-pill bg-warning">{{ $jml_pajak_satutahun_sudahlewat }}</span></a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link active" id="home-tab-justified" data-toggle="tab"
                                                        href="#pajak-11" role="tab" aria-controls="home-just"
                                                        aria-selected="true">Bulan Ini <span
                                                            class="badge badge-pill bg-danger">{{ $jml_pajak_satutahun_bulanini }}</span></a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="profile-tab-justified" data-toggle="tab"
                                                        href="#pajak-12" role="tab" aria-controls="profile-just"
                                                        aria-selected="false">Bulan Depan <span
                                                            class="badge badge-pill bg-warning">{{ $jml_pajak_satutahun_bulandepan }}</span></a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="messages-tab-justified" data-toggle="tab"
                                                        href="#pajak-13" role="tab" aria-controls="messages-just"
                                                        aria-selected="false">2 Bulan Lagi <span
                                                            class="badge badge-pill bg-success">{{ $jml_pajak_satutahun_duabulan }}</span></a>
                                                </li>
                                            </ul>

                                            <!-- Tab panes -->
                                            <div class="tab-content pt-1">
                                                <div class="tab-pane" id="pajak-sudahlewat" role="tabpanel"
                                                    aria-labelledby="profile-tab-justified">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>No. Polisi</th>
                                                                <th>Kendaraan</th>
                                                                <th>JT pajak_satutahun</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="font-size: 12px !important">
                                                            @foreach ($pajak_satutahun_sudahlewat as $d)
                                                                @php
                                                                    $hariini = date('Y-m-d');
                                                                    $tgl1 = strtotime($hariini);
                                                                    $tgl2 = strtotime($d->jatuhtempo_pajak_satutahun);
                                                                    $selisih = $tgl2 - $tgl1;
                                                                    $jmlhari = $selisih / 60 / 60 / 24;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $d->no_polisi }}</td>
                                                                    <td>{{ $d->merk }} {{ $d->tipe_kendaraan }}
                                                                        {{ $d->tipe }}</td>
                                                                    <td>{{ date('d-m-Y', strtotime($d->jatuhtempo_pajak_satutahun)) }}
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge {{ $hariini > $d->jatuhtempo_pajak_satutahun ? 'bg-danger' : 'bg-success' }}">
                                                                            {{ $hariini > $d->jatuhtempo_pajak_satutahun ? 'Sudah lewat' : $jmlhari . ' Hari Lagi' }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="tab-pane active" id="pajak-11" role="tabpanel"
                                                    aria-labelledby="home-tab-justified">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>No. Polisi</th>
                                                                <th>Kendaraan</th>
                                                                <th>JT pajak_satutahun</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="font-size: 12px !important">
                                                            @foreach ($pajak_satutahun_bulanini as $d)
                                                                @php
                                                                    $hariini = date('Y-m-d');
                                                                    $tgl1 = strtotime($hariini);
                                                                    $tgl2 = strtotime($d->jatuhtempo_pajak_satutahun);
                                                                    $selisih = $tgl2 - $tgl1;
                                                                    $jmlhari = $selisih / 60 / 60 / 24;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $d->no_polisi }}</td>
                                                                    <td>{{ $d->merk }} {{ $d->tipe_kendaraan }}
                                                                        {{ $d->tipe }}</td>
                                                                    <td>{{ date('d-m-Y', strtotime($d->jatuhtempo_pajak_satutahun)) }}
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge {{ $hariini > $d->jatuhtempo_pajak_satutahun ? 'bg-danger' : 'bg-success' }}">
                                                                            {{ $hariini > $d->jatuhtempo_pajak_satutahun ? 'Sudah lewat' : $jmlhari . ' Hari Lagi' }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="tab-pane" id="pajak-12" role="tabpanel"
                                                    aria-labelledby="profile-tab-justified">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>No. Polisi</th>
                                                                <th>Kendaraan</th>
                                                                <th>JT pajak_satutahun</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="font-size: 12px !important">
                                                            @foreach ($pajak_satutahun_bulandepan as $d)
                                                                @php
                                                                    $hariini = date('Y-m-d');
                                                                    $tgl1 = strtotime($hariini);
                                                                    $tgl2 = strtotime($d->jatuhtempo_pajak_satutahun);
                                                                    $selisih = $tgl2 - $tgl1;
                                                                    $jmlhari = $selisih / 60 / 60 / 24;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $d->no_polisi }}</td>
                                                                    <td>{{ $d->merk }} {{ $d->tipe_kendaraan }}
                                                                        {{ $d->tipe }}</td>
                                                                    <td>{{ date('d-m-Y', strtotime($d->jatuhtempo_pajak_satutahun)) }}
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge {{ $hariini > $d->jatuhtempo_pajak_satutahun ? 'bg-danger' : 'bg-success' }}">
                                                                            {{ $hariini > $d->jatuhtempo_pajak_satutahun ? 'Sudah lewat' : $jmlhari . ' Hari Lagi' }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="tab-pane" id="pajak-13" role="tabpanel"
                                                    aria-labelledby="messages-tab-justified">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>No. Polisi</th>
                                                                <th>Kendaraan</th>
                                                                <th>JT pajak_satutahun</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="font-size: 12px !important">
                                                            @foreach ($pajak_satutahun_duabulan as $d)
                                                                @php
                                                                    $hariini = date('Y-m-d');
                                                                    $tgl1 = strtotime($hariini);
                                                                    $tgl2 = strtotime($d->jatuhtempo_pajak_satutahun);
                                                                    $selisih = $tgl2 - $tgl1;
                                                                    $jmlhari = $selisih / 60 / 60 / 24;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $d->no_polisi }}</td>
                                                                    <td>{{ $d->merk }} {{ $d->tipe_kendaraan }}
                                                                        {{ $d->tipe }}</td>
                                                                    <td>{{ date('d-m-Y', strtotime($d->jatuhtempo_pajak_satutahun)) }}
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge {{ $hariini > $d->jatuhtempo_pajak_satutahun ? 'bg-danger' : 'bg-success' }}">
                                                                            {{ $hariini > $d->jatuhtempo_pajak_satutahun ? 'Sudah lewat' : $jmlhari . ' Hari Lagi' }}
                                                                        </span>
                                                                    </td>
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
                        <div class="row">

                            <div class="col-sm-12">
                                <div class="card overflow-hidden">
                                    <div class="card-header">
                                        <h4 class="card-title">Jatuh Tempo Pajak 5 Tahun</h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body">

                                            <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link" id="profile-tab-justified" data-toggle="tab"
                                                        href="#pajak5-sudahlewat" role="tab"
                                                        aria-controls="profile-just" aria-selected="false">Lewat JT <span
                                                            class="badge badge-pill bg-danger">{{ $jml_pajak_limatahun_sudahlewat }}</span></a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link active" id="home-tab-justified" data-toggle="tab"
                                                        href="#pajak-51" role="tab" aria-controls="home-just"
                                                        aria-selected="true">Bulan Ini <span
                                                            class="badge badge-pill bg-danger">{{ $jml_pajak_limatahun_bulanini }}</span></a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="profile-tab-justified" data-toggle="tab"
                                                        href="#pajak-52" role="tab" aria-controls="profile-just"
                                                        aria-selected="false">Bulan Depan <span
                                                            class="badge badge-pill bg-warning">{{ $jml_pajak_limatahun_bulandepan }}</span></a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="messages-tab-justified" data-toggle="tab"
                                                        href="#pajak-53" role="tab" aria-controls="messages-just"
                                                        aria-selected="false">2 Bulan Lagi <span
                                                            class="badge badge-pill bg-success">{{ $jml_pajak_limatahun_duabulan }}</span></a>
                                                </li>
                                            </ul>

                                            <!-- Tab panes -->
                                            <div class="tab-content pt-1">
                                                <div class="tab-pane" id="pajak5-sudahlewat" role="tabpanel"
                                                    aria-labelledby="profile-tab-justified">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>No. Polisi</th>
                                                                <th>Kendaraan</th>
                                                                <th>JT pajak_limatahun</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="font-size: 12px !important">
                                                            @foreach ($pajak_limatahun_sudahlewat as $d)
                                                                @php
                                                                    $hariini = date('Y-m-d');
                                                                    $tgl1 = strtotime($hariini);
                                                                    $tgl2 = strtotime($d->jatuhtempo_pajak_limatahun);
                                                                    $selisih = $tgl2 - $tgl1;
                                                                    $jmlhari = $selisih / 60 / 60 / 24;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $d->no_polisi }}</td>
                                                                    <td>{{ $d->merk }} {{ $d->tipe_kendaraan }}
                                                                        {{ $d->tipe }}</td>
                                                                    <td>{{ date('d-m-Y', strtotime($d->jatuhtempo_pajak_limatahun)) }}
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge {{ $hariini > $d->jatuhtempo_pajak_limatahun ? 'bg-danger' : 'bg-success' }}">
                                                                            {{ $hariini > $d->jatuhtempo_pajak_limatahun ? 'Sudah lewat' : $jmlhari . ' Hari Lagi' }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="tab-pane active" id="pajak-51" role="tabpanel"
                                                    aria-labelledby="home-tab-justified">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>No. Polisi</th>
                                                                <th>Kendaraan</th>
                                                                <th>JT pajak_limatahun</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="font-size: 12px !important">
                                                            @foreach ($pajak_limatahun_bulanini as $d)
                                                                @php
                                                                    $hariini = date('Y-m-d');
                                                                    $tgl1 = strtotime($hariini);
                                                                    $tgl2 = strtotime($d->jatuhtempo_pajak_limatahun);
                                                                    $selisih = $tgl2 - $tgl1;
                                                                    $jmlhari = $selisih / 60 / 60 / 24;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $d->no_polisi }}</td>
                                                                    <td>{{ $d->merk }} {{ $d->tipe_kendaraan }}
                                                                        {{ $d->tipe }}</td>
                                                                    <td>{{ date('d-m-Y', strtotime($d->jatuhtempo_pajak_limatahun)) }}
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge {{ $hariini > $d->jatuhtempo_pajak_limatahun ? 'bg-danger' : 'bg-success' }}">
                                                                            {{ $hariini > $d->jatuhtempo_pajak_limatahun ? 'Sudah lewat' : $jmlhari . ' Hari Lagi' }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="tab-pane" id="pajak-52" role="tabpanel"
                                                    aria-labelledby="profile-tab-justified">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>No. Polisi</th>
                                                                <th>Kendaraan</th>
                                                                <th>JT pajak_limatahun</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="font-size: 12px !important">
                                                            @foreach ($pajak_limatahun_bulandepan as $d)
                                                                @php
                                                                    $hariini = date('Y-m-d');
                                                                    $tgl1 = strtotime($hariini);
                                                                    $tgl2 = strtotime($d->jatuhtempo_pajak_limatahun);
                                                                    $selisih = $tgl2 - $tgl1;
                                                                    $jmlhari = $selisih / 60 / 60 / 24;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $d->no_polisi }}</td>
                                                                    <td>{{ $d->merk }} {{ $d->tipe_kendaraan }}
                                                                        {{ $d->tipe }}</td>
                                                                    <td>{{ date('d-m-Y', strtotime($d->jatuhtempo_pajak_limatahun)) }}
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge {{ $hariini > $d->jatuhtempo_pajak_limatahun ? 'bg-danger' : 'bg-success' }}">
                                                                            {{ $hariini > $d->jatuhtempo_pajak_limatahun ? 'Sudah lewat' : $jmlhari . ' Hari Lagi' }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="tab-pane" id="pajak-53" role="tabpanel"
                                                    aria-labelledby="messages-tab-justified">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>No. Polisi</th>
                                                                <th>Kendaraan</th>
                                                                <th>JT pajak_limatahun</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="font-size: 12px !important">
                                                            @foreach ($pajak_limatahun_duabulan as $d)
                                                                @php
                                                                    $hariini = date('Y-m-d');
                                                                    $tgl1 = strtotime($hariini);
                                                                    $tgl2 = strtotime($d->jatuhtempo_pajak_limatahun);
                                                                    $selisih = $tgl2 - $tgl1;
                                                                    $jmlhari = $selisih / 60 / 60 / 24;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $d->no_polisi }}</td>
                                                                    <td>{{ $d->merk }} {{ $d->tipe_kendaraan }}
                                                                        {{ $d->tipe }}</td>
                                                                    <td>{{ date('d-m-Y', strtotime($d->jatuhtempo_pajak_limatahun)) }}
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge {{ $hariini > $d->jatuhtempo_pajak_limatahun ? 'bg-danger' : 'bg-success' }}">
                                                                            {{ $hariini > $d->jatuhtempo_pajak_limatahun ? 'Sudah lewat' : $jmlhari . ' Hari Lagi' }}
                                                                        </span>
                                                                    </td>
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
                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Rekap Kendaraan Cabang</div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Kode Cabang</th>
                                            <th>Cabang</th>
                                            <th>Jml</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rekapkendaraancabang as $d)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $d->kode_cabang }}</td>
                                                <td>{{ $d->nama_cabang }}</td>
                                                <td style="text-align: center">{{ rupiah($d->jmlkendaraan) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card text-center">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="avatar bg-rgba-info p-50 m-0 mb-1">
                                        <div class="avatar-content">
                                            <i class="feather icon-truck text-info font-medium-5"></i>
                                        </div>
                                    </div>
                                    <h2 class="text-bold-700">{{ rupiah($jmlkendaraan) }}</h2>
                                    <p class="mb-0 line-ellipsis">Kendaraan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </section>
        </div>
    </div>
@endsection
