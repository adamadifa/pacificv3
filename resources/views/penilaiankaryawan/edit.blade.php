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
                    <form action="/penilaiankaryawan/{{ Crypt::encrypt($penilaian->kode_penilaian) }}/update" method="POST">
                        @csrf
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                        <input type="hidden" name="periode_kontrak" value="{{ $dari }}/{{ $sampai }}">
                        <input type="hidden" name="nik" value="{{ $karyawan->nik }}">
                        <input type="hidden" name="kode_dept" value="{{ $karyawan->kode_dept }}">
                        <input type="hidden" name="id_jabatan" value="{{ $karyawan->id_jabatan }}">
                        <input type="hidden" name="id_kategori_jabatan" value="{{ $karyawan->id_kategori_jabatan }}">
                        <input type="hidden" name="kategori" value="{{ $kategori }}">
                        <input type="hidden" name="id_kantor" value="{{ $karyawan->id_kantor }}">
                        <input type="hidden" name="id_perusahaan" value="{{ $karyawan->id_perusahaan }}">
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
                                <td>{{ $karyawan->kode_dept }} - {{ $karyawan->nama_dept }} / {{ $karyawan->nama_jabatan }}</td>
                            </tr>
                        </table>

                        <b>A. Penilaian</b>
                        <br>
                        <br>
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
                                    <td>
                                        <input type="hidden" name="id_penilaian[]" value="{{ $d->id }}">
                                        {{ $d->penilaian }}
                                    </td>
                                    <td>
                                        <div class="form-group" style="margin-bottom: 0 !important">
                                            <select name="skor[]" required id="skor" class="form-control skor {{ $d->nilai== 0 ? 'danger' : 'success' }}">
                                                <option value="">Pilih Nilai</option>
                                                <option value="0" {{ $d->nilai == 0 ? 'selected' : '' }} class="danger">Tidak Memuaskan</option>
                                                <option value="1" {{ $d->nilai == 1 ? 'selected' : '' }} class="success">Memuaskan</option>
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
                        <div class="row">
                            <div class="col-6">
                                <table class="table">
                                    <tr>
                                        <td style="font-weight: bold">SID</td>
                                        <td>
                                            <input type="number" class="form-control" name="sid" value="{{ $penilaian->sid }}">
                                        </td>
                                        <td style="font-weight: bold">Izin</td>
                                        <td>
                                            <input type="number" class="form-control" name="izin" value="{{ $penilaian->izin }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold">Sakit</td>
                                        <td>
                                            <input type="number" class="form-control" name="sakit" value="{{ $penilaian->sakit }}">
                                        </td>
                                        <td style="font-weight: bold">Alfa</td>
                                        <td>
                                            <input type="number" class="form-control" name="alfa" value="{{ $penilaian->alfa }}">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-6">
                                {{-- <table class="table table-bordered">
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
                                </table> --}}
                            </div>
                        </div>
                        <b>B. Masa Kontrak Kerja</b>
                        <br>
                        <br>
                        <div class="row mb-2">
                            {{-- <div class="col-12">
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
                            </div> --}}
                            <div class="col-12">


                                <ul class="list-unstyled mb-0">
                                    <li class="d-inline-block mr-2">
                                        <fieldset>
                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                <input type="checkbox" name="masa_kontrak_kerja" class="chb" value="Tidak Diperpanjang" {{ $penilaian->masa_kontrak_kerja == "Tidak Diperpanjang" ?  "checked" : "" }}>
                                                <span class="vs-checkbox">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="">Tidak Diperpanjang</span>
                                            </div>
                                        </fieldset>
                                    </li>
                                    <li class="d-inline-block mr-2">
                                        <fieldset>
                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                <input type="checkbox" name="masa_kontrak_kerja" class="chb" value="3 Bulan" {{ $penilaian->masa_kontrak_kerja == "3 Bulan" ? "checked" : "" }}>
                                                <span class="vs-checkbox">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="">3 Bulan</span>
                                            </div>
                                        </fieldset>
                                    </li>
                                    <li class="d-inline-block mr-2">
                                        <fieldset>
                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                <input type="checkbox" name="masa_kontrak_kerja" class="chb" value="6 Bulan" {{ $penilaian->masa_kontrak_kerja == "6 Bulan" ? "checked" : "" }}>
                                                <span class="vs-checkbox">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="">6 Bulan</span>
                                            </div>
                                        </fieldset>
                                    </li>
                                    <li class="d-inline-block mr-2">
                                        <fieldset>
                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                <input type="checkbox" name="masa_kontrak_kerja" class="chb" value="Karyawan Tetap" {{ $penilaian->masa_kontrak_kerja == "Karyawan Tetap" ? "checked" : "" }}>
                                                <span class="vs-checkbox">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="">Karyawan Tetap</span>
                                            </div>
                                        </fieldset>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <b>C. Riwayat Absensi dan Rekomendasi User</b>
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <textarea name="rekomendasi" id="" cols="30" rows="10" class="form-control">{{ $penilaian->rekomendasi }}</textarea>
                                </div>
                            </div>
                        </div>
                        <b>D. Evaluasi Skill Teknis / Kinerja (Wajib Diisi User)</b>
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <textarea name="evaluasi" id="" cols="30" rows="10" class="form-control">{{ $penilaian->evaluasi }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <button class="btn btn-primary btn-block"><i class="feather icon-send"></i></button>
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
            //summary();
            $(this).removeClass("danger");
            $(this).removeClass("success");
            var val = $(this).val();
            if (val == 0) {
                $(this).addClass("danger");
            } else if (val == 1) {
                $(this).addClass("success");
            }
        });


        $(".chb").change(function() {
            $(".chb").prop('checked', false);
            $(this).prop('checked', true);
        });
    });

</script>
@endpush
