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
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:5%">No</th>
                                    <th style="width:85%">Faktor Penilaian</th>
                                    <th style="width:10%">Bobot Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kategori_penilaian as $d)
                                <tr>
                                    <td rowspan="2">{{ $loop->iteration }}</td>
                                    <td class="bg-info">{{ $d->jenis_penilaian }}</td>
                                    <td rowspan="2">
                                        <div class="form-group" style="margin-bottom: 0 !important">
                                            <select name="skor[]" id="skor" class="form-control skor">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ $d->penilaian }}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <th colspan="2">TOTAL</th>
                                    <th style="text-align: right"><span id="total_skor"></span></th>
                                </tr>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-6">
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
                            </div>
                            <div class="col-6">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nilai</th>
                                            <th>Parameter Waktu Perpanjangan Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td> {{ "< 45" }} </td>
                                            <td>Tidak diperpanjang</td>
                                        </tr>
                                        <tr>
                                            <td> {{ "46 - 50" }} </td>
                                            <td>3 Bulan</td>
                                        </tr>
                                        <tr>
                                            <td> {{ "51 -  55" }} </td>
                                            <td>6 Bulan</td>
                                        </tr>
                                        <tr>
                                            <td> {{ "56 >" }} </td>
                                            <td>1 Tahun</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <b>B. Masa Kontrak Kerja</b>
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tidak Diperpanjang</th>
                                            <th>3 Bulan</th>
                                            <th>6 Bulan</th>
                                            <th>1 Tahun</th>
                                            <th>Karyawan Tetap</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="td" style="text-align: center"></td>
                                            <td id="tigabulan" style="text-align: center"></td>
                                            <td id="enambulan" style="text-align: center"></td>
                                            <td id="satutahun" style="text-align: center"></td>
                                            <td id="karyawantetap" style="text-align: center"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <b>C. Riwayat Absensi dan Rekomendasi User</b>
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <textarea name="rekomendasi_user" id="" cols="30" rows="10" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <b>D. Evaluasi Skill Teknis / Kinerja (Wajib Diisi User)</b>
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <textarea name="evaluasi_kinerja" id="" cols="30" rows="10" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        function summary() {
            var sum = 0;
            $('.skor').each(function() {
                sum += parseInt($(this).val());
            });
            $("#total_skor").text(sum);
            if (sum < 45) {
                $("#td").html("<i class='fa fa-check'></i>");
                $("#tigabulan").html("");
                $("#enambulan").html("");
                $("#satutahun").html("");
                $("#karyawantetap").html("");
            } else if (sum < 50) {
                $("#tigabulan").html("<i class='fa fa-check'></i>");
                $("#td").html("");
                $("#enambulan").html("");
                $("#satutahun").html("");
                $("#karyawantetap").html("");
            } else if (sum < 55) {
                $("#enambulan").html("<i class='fa fa-check'></i>");
                $("#td").html("");
                $("#tigabulan").html("");
                $("#satutahun").html("");
                $("#karyawantetap").html("");
            } else if (sum > 55) {
                $("#satutahun").html("<i class='fa fa-check'></i>");
                $("#td").html("");
                $("#tigabulan").html("");
                $("#enambulan").html("");
                $("#karyawantetap").html("");
            }
        }
        summary();
        $('.skor').change(function(e) {
            summary();
        });
    });

</script>
@endpush
