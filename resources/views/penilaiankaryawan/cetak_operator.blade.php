<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Penilaian Karyawan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.3.0/paper.css">
    <style>
        @page {
            size: A4
        }

        body {
            font-family: "Tahoma"
        }

        .datatable3 {
            border: 1px solid #09090a;
            border-collapse: collapse;
            font-size: 12px;
        }

        .datatable3 td {
            border: 1px solid #000000;
            padding: 4px;
        }

        .datatable3 th {
            border: 2px solid #828282;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }

        .datatable4 {

            border-collapse: collapse;
            font-size: 12px;
        }

        .datatable4 td {

            padding: 4px;
        }

        .datatable4 th {

            font-weight: bold;
            text-align: left;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }

    </style>
</head>
<body class="A4">

    <!-- Each sheet element should have the class "sheet" -->
    <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->

    <section class="sheet padding-10mm">

        <table border=0>
            <tr>
                <td style="width: 10%">
                    @if ($penilaian->id_perusahaan=="MP")
                    <img src="{{ asset('app-assets/images/logo/mp.png') }}" width="80" height="80" alt="">
                    @else
                    <img src="{{ asset('app-assets/images/logo/pcf.png') }}" width="80" height="80" alt="">
                    @endif
                </td>
                <td style="font-weight: bold; text-align:center; width:55%">
                    <h4>FORMULIR EVALUASI KARYAWAN MASA PERCOBAAN DAN KONTRAK</h4>
                </td>
                <td style="width: 35%" valign="top">
                    <table style="border: 1px solid; border-collapse:collapse;">
                        <tr>
                            <td style="font-size:14px; padding:3px">No. Dok : FRM.HRD.01.04. Rev.05</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br>
        <table class="datatable3">
            <tr>
                <td style="font-weight: bold">Periode Kontrak / Masa Percobaan</td>
                <td>
                    {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
                </td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>{{ $penilaian->nik }}</td>
            </tr>
            <tr>
                <td>Nama Karyawan</td>
                <td>{{ $karyawan->nama_karyawan }}</td>
            </tr>
            <tr>
                <td>Departemen / Posisi</td>
                <td>{{ $karyawan->nama_dept  }} / {{ $karyawan->nama_jabatan }}</td>
            </tr>
        </table>
        <br>
        <b style="font-size:14px">A. Penilaian</b>
        <br>
        <br>
        <table class="datatable3">
            <thead>
                <tr>
                    <th style="width:5%" rowspan="2">No</th>
                    <th style="width:40%" rowspan="2">Faktor Penilaian</th>
                    <th style="width:30%" colspan="2" align="center">Hasil Penilaian</th>
                </tr>
                <tr style="font-weight: bold">
                    <td>Tidak Memuaskan</td>
                    <td>Sangat Memuaskan</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($kategori_penilaian as $d)
                <tr>
                    <td rowspan="2">{{ $loop->iteration }}</td>
                    <td style="background-color: rgb(41, 155, 212)">{{ $d->jenis_penilaian }}</td>
                    <td style="text-align: center" rowspan="2">{!! $d->nilai == 0 ? "&#10004" : "" !!}</td>
                    <td style="text-align: center" rowspan="2">{!! $d->nilai == 1 ? "&#10004" : "" !!}</td>
                </tr>
                <tr>
                    <td>
                        <input type="hidden" name="id_penilaian[]" value="{{ $d->id }}">
                        {{ $d->penilaian }}
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </section>

    <section class="sheet padding-10mm">
        <b style="font-size:14px">B. Kehadiran Absensi</b>
        <br>
        <br>
        <table style="font-size:11px" class="datatable4">
            <tr>
                <td>SID</td>
                <td>:</td>
                <td>{{ $penilaian->sid }}</td>
                <td>Izin</td>
                <td>:</td>
                <td>{{ $penilaian->izin }}</td>
            </tr>
            <tr>
                <td>Sakit</td>
                <td>:</td>
                <td>{{ $penilaian->sakit }}</td>
                <td>Alfa</td>
                <td>:</td>
                <td>{{ $penilaian->alfa }}</td>
            </tr>
        </table>
        <br>
        <b style="font-size:14px">C. Masa Kontrak Kerja</b>
        <br>
        <br>
        <table class="datatable3" style="width: 100%">
            <thead>
                <tr>
                    <th>Tidak Di Perpanjang</th>
                    <th>3 Bulan</th>
                    <th>6 Bulan</th>
                    <th>Karyawan Tetap</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center">{!! $penilaian->masa_kontrak_kerja == "Tidak Diperpanjang" ? "&#10004" : "" !!}</td>
                    <td align="center">{!! $penilaian->masa_kontrak_kerja == "3 Bulan" ? "&#10004" : "" !!}</td>
                    <td align="center">{!! $penilaian->masa_kontrak_kerja == "6 Bulan" ? "&#10004" : "" !!}</td>
                    <td align="center">{!! $penilaian->masa_kontrak_kerja == "Karyawan Tetap" ? "&#10004" : "" !!}</td>

                </tr>
            </tbody>
        </table>
        <br>
        <b style="font-size:14px">D. Riwayat Absensi dan Rekomendasi User</b>
        <br>
        <br>
        <div style="border:1px solid; border-collapse:collapse; height:100px; font-size:12px; padding:8px">
            {{ $penilaian->rekomendasi }}
        </div>
        <br>
        <b style="font-size:14px">E. Evaluasi Skill Teknis / Kinerja (Wajib Diisi Oleh User)</b>
        <br>
        <br>
        <div style="border:1px solid; border-collapse:collapse; height:100px; font-size:12px; padding:8px">
            {{ $penilaian->evaluasi }}
        </div>
        <br>
        <br>
        <table style="width:100%">
            <tr>
                <?php
                    // $inisial = ["manager"=>"M","general manager"=>"GM","manager hrd"=>"HRD","direktur"=>"DIRUT"];
                    for ($i = 0; $i < count($approve); $i++){
                        $level = strtolower($inisial[$approve[$i]]);
                        if($i < count($approve) - 1){
                            $test = $i +1;
                            $nextlevel = strtolower($inisial[$approve[$i + 1]]);
                        }else{
                            $nextlevel = strtolower($inisial[$approve[$i]]);
                            $test = 0;
                        }

                        if($i==0 && $karyawan->status==2 && !empty($karyawan->$level) && empty($karyawan->$nextlevel)){
                            $y = "";
                            $t = "&#10004";
                            $cek = 1;
                        }else if($i==0 && $karyawan->status==2 && !empty($karyawan->$level) && !empty($karyawan->$nextlevel) ){
                        //echo "<i class='fa fa-check success'></i>";
                            $y = "&#10004";
                            $t = "";
                            $cek = 2;
                        }else if($karyawan->status == 2 && !empty($karyawan->$level) && $level=="dirut"){
                        //echo "<i class='fa fa-close danger'></i>";
                            $y = "";
                            $t = "&#10004";
                            $cek = 3;
                        }else if($karyawan->status == 2 && !empty($karyawan->$level) && empty($karyawan->$nextlevel)){
                        //echo "<i class='fa fa-close danger'></i>";
                            $y = "";
                            $t = "&#10004";
                            $cek = 4;
                        }else if($karyawan->status == 2 && !empty($karyawan->$level) && !empty($karyawan->$nextlevel)){
                        //echo "<i class='fa fa-check success'></i>";
                            $y = "&#10004";
                            $t = "";
                            $cek = 5;
                        }else if($karyawan->status == NULL && empty($karyawan->$level)){
                            $y = "";
                            $t = "";
                            $cek = 6;
                        //echo "<i class='fa fa-history warning'></i>";
                        }else if($karyawan->status == NULL && !empty($karyawan->$level)){
                            $y = "&#10004";
                            $t = "";
                            $cek = 7;
                        //echo "<i class='fa fa-check success'></i>";
                        }else if($karyawan->status == 1 && !empty($karyawan->$level)){
                            $y = "&#10004";
                            $t = "";
                            $cek = 8;
                        //echo "<i class='fa fa-check success'></i>";
                        }


                        // echo $karyawan->$level. " ".$cek." ".$y;

                ?>
                <td>
                    <table class="datatable3" style="width: 100%">
                        <tr>
                            <td style="height: 120px; vertical-align:top; text-align:center">
                                <div style="display: flex; justify-content:space-between">
                                    <div style="width:30px; height:20px; border-style:solid; border-width:1px; padding:10px; text-align:center">
                                        Y <?php echo $y ?></div>
                                    <div style="width:30px; height:20px; border-style:solid; border-width:1px; padding:10px; text-align:center">T <?php echo $t ?> </div>
                                </div>
                                <br>
                                {!! !empty($karyawan->$level) ? QrCode::size(80)->generate('sahretech.com') : '' !!}
                                <br>
                                @php
                                $cekapproval = DB::table('users')->where('id',$karyawan->$level)->first();
                                @endphp
                                <br>
                                <b><?php echo $cekapproval != null ? $cekapproval->name : ''; ?></b>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align:center; font-weight:bold">
                                <?php echo ucwords($approve[$i]) ?>
                            </td>
                        </tr>
                    </table>
                </td>
                <?php }?>
            </tr>
        </table>
    </section>
</body>
</html>
