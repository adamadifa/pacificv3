@extends('layouts.midone')
@section('titlepage','Penilaian Karyawan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Penilaian Karyawan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/penilaiankaryawan">Penilaian Karyawan</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <!-- Data list view starts -->
        <!-- DataTable starts -->
        @include('layouts.notification')
        <div class="col-md-8 col-sm-8">
            <div class="card">
                <div class="card-header">
                </div>
                <div class="card-body">
                    <form action="/penilaiankaryawan/store">
                        <table class="table">
                            <tr>
                                <td>Periode Kontrak</td>
                                <td>{{ date("d-m-Y",strtotime($dari)) }} s/d {{ date("d-m-Y",strtotime($sampai)) }}</td>
                            </tr>
                            <tr>
                                <td>NIK</td>
                                <td>{{ $karyawan->nik }}</td>
                            </tr>
                            <tr>
                                <td>Nama Karyawan</td>
                                <td>{{ $karyawan->nama_karyawan }}</td>
                            </tr>
                            <tr>
                                <td>Departemen</td>
                                <td></td>
                            </tr>
                        </table>

                        <b>A. Penilaian</b>
                        <br>
                        <br>
                        <small>Checklist bobot penilaian dibawah ini (semakin besar angka yang dipilih semakin baik penilaian karyawan tersebut)</small>
                        <table class="table mt-3">
                            <tbody>
                                @php
                                $no = 1;
                                $id_jenis_penilaian = "";
                                $id_jenis_kompetensi = "";
                                @endphp
                                @foreach ($kategori_penilaian as $d)
                                @if ($id_jenis_penilaian != $d->id_jenis_penilaian)
                                @php
                                $no = 1;
                                @endphp
                                <tr>
                                    <th colspan="3" style="text-align: center; background-color:rgba(0, 255, 72, 0.235)">{{ $d->jenis_penilaian }}</th>
                                </tr>
                                <tr>
                                    <th>No.</th>
                                    <th>Sasaran Kerja</th>
                                    <th>Nilai</th>

                                </tr>
                                @endif

                                @if (!empty($d->id_jenis_kompetensi) && $id_jenis_kompetensi != $d->id_jenis_kompetensi)
                                <tr>
                                    <td colspan="3" style="text-align: center">{{ $d->id_jenis_kompetensi == 1 ? 'Kompentensi Wajib' : 'Kompetensi' }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td>{{ $no }}</td>
                                    <td>{{ $d->penilaian }}</td>
                                    <td>
                                        <div class="form-group" style="margin-bottom: 0 !important">
                                            <select name="skor[]" id="skor" class="form-control">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                @php
                                $no++;
                                $id_jenis_penilaian = $d->id_jenis_penilaian;
                                $id_jenis_kompetensi = $d->id_jenis_kompetensi;
                                @endphp
                                @endforeach
                            </tbody>
                        </table>
                        <table class="table">
                            <tr>
                                <td style="font-weight: bold">SID</td>
                                <td>
                                    <input type="number" class="form-control" name="sid">
                                </td>
                                <td style="font-weight: bold">Izin</td>
                                <td>
                                    <input type="number" class="form-control" name="izin">
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Sakit</td>
                                <td>
                                    <input type="number" class="form-control" name="sakit">
                                </td>
                                <td style="font-weight: bold">Alfa</td>
                                <td>
                                    <input type="number" class="form-control" name="alfa">
                                </td>
                            </tr>
                        </table>
                    </form>
                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
@endsection
