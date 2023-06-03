<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Cetak Kesepakatan Bersama</title>

    <!-- Normalize or reset CSS with your favorite library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

    <!-- Load paper.css for happy printing -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

    <!-- Set page size here: A5, A4 or A3 -->
    <!-- Set also "landscape" if you need -->
    <style>
        @page {
            size: A4
        }

        body {
            font-family: 'Times New Roman';
            font-size: 14px
        }

        .datatable3 {
            border: 1px solid #161616;
            border-collapse: collapse;
            font-size: 12px;
            width: 100%
        }

        .datatable3 td {
            border: 1px solid #000000;
            padding: 3px;
        }

        .datatable3 th {
            border: 1px solid #4d4d4d;
            font-weight: bold;
            text-align: left;
            padding: 3px;
            text-align: center;
            font-size: 12px;
            background-color: #d4d3d3cf
        }


        .datatable4 {
            border: 0px solid #161616;
            border-collapse: collapse;
            font-size: 12px;
            width: 100%
        }

        .datatable4 td {
            border: 0px solid #000000;
            padding: 3px;
        }

    </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="A4">

    <!-- Each sheet element should have the class "sheet" -->
    <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
    <section class="sheet padding-10mm">

        <table style="width: 100%">
            <tr>
                <td>
                    <img src="{{ asset('app-assets/images/logo/pcf.png') }}" alt="" style="width: 70px; height:80px">
                </td>
                <td style="text-align: center">
                    <h3 style="font-family:'Cambria'; line-height:0px">CV PACIFIC & CV MAKMUR PERMATA</h3>
                    <span style="font-size: 1.2rem"><i>Factory / Head Office</i></span><br>
                    <span style="font-family:'Times New Roman'">Jl. Perintis Kemerdekaan No. 160 Tasikmalaya</span><br>
                    <span style="font-size: 12px">Telp (0265) 336794 Fax (0265) 332329</span><br>
                    <span style="font-size: 11px">e-mail : pacific.tasikmalaya@gmail.com</span>
                </td>
                <td>
                    <img src="{{ asset('app-assets/images/logo/mp.png') }}" alt="" style="width: 80px; height:80px">
                </td>
            </tr>
        </table>
        <hr>
        <h3 style="text-align:center"><u>KESEPAKATAN BERSAMA</u></h3>
        <p>Yang bertandatangan dibawah ini :</p>
        @php
        $pihak_satu = explode("-",$approve->pihak_satu);
        $nama_pihaksatu = $pihak_satu[0];
        $jabatan_pihaksatu = $pihak_satu[1];
        @endphp
        <table>
            <tr>
                <td style="width: 30px">I.</td>
                <td style="width:100px">Nama</td>
                <td>:</td>
                <td>{{ $nama_pihaksatu }}</td>
            </tr>
            <tr>
                <td></td>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $jabatan_pihaksatu }}</td>
            </tr>
            <tr>
                <td></td>
                <td>Alamat</td>
                <td>:</td>
                <td>Jl. Perintis Kemerdekaan No.160 Tasikmalaya</td>
            </tr>
        </table>
        <p style="text-indent:1cm; text-align:justify">
            Untuk selanjutnya disebut PIHAK PERTAMA ( I ) dan bertindak atas nama CV. Makmur Permata dan CV Pacific yang beralamat di Jl. Perintis Kemerdekaan No. 160 Tasikmalaya.
        </p>
        <table>
            <tr>
                <td style="width: 30px">II.</td>
                <td style="width:100px">Nama</td>
                <td>:</td>
                <td>{{ $kb->nama_karyawan }}</td>
            </tr>
            <tr>
                <td></td>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $kb->nama_jabatan }}</td>
            </tr>
            <tr>
                <td></td>
                <td>NIK</td>
                <td>:</td>
                <td>{{ $kb->nik }}</td>
            </tr>
            <tr>
                <td></td>
                <td>Masa Kerja</td>
                <td>:</td>
                <td>
                    @php
                    $awal = date_create($kb->tgl_masuk);
                    $akhir = date_create($kb->tgl_kb); // waktu sekarang
                    $diff = date_diff( $awal, $akhir );
                    echo $diff->y . ' tahun, '.$diff->m.' bulan, '.$diff->d.' Hari'
                    @endphp


                </td>
            </tr>
            <tr>
                <td></td>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $kb->alamat }}</td>
            </tr>
            <tr>
                <td></td>
                <td>No. Identitas</td>
                <td>:</td>
                <td>{{ $kb->no_ktp }}</td>
            </tr>
        </table>
        <p style="text-indent:1cm; text-align:justify">
            Untuk selanjutnya disebut PIHAK KEDUA ( II) atau pekerja.<br>
            Pada tanggal {{ DateToIndo2($kontrak->sampai)}} PIHAK PERTAMA ( I ) dan PIHAK KEDUA ( II ) bertempat di CV Makmur Permata Tasikmalaya telah mengadakan perundingan atau musyawarah mufakat yang mendalam secara kekeluargaan dengan menghasilkan kesepakatan sebagai berikut :
            <br>
            <ol>
                <li>
                    PIHAK PERTAMA (I) dan PIHAK KEDUA (II) telah sepakat terkait kontrak kerja yang diputihkanmulai tanggal
                    {{ DateToIndo2($kontrak->sampai)}}
                </li>
                <li>
                    PIHAK PERTAMA ( I ) bersedia untuk memberikan kompensasi atau kebijakan kepada PIHAK KEDUA ( II ) yang besarnya sebagai berikut :
                </li>
            </ol>
        </p>
        <table class="datatable3">
            <tr>
                <th>Rincian Upah</th>
                <th>
                    Perhitungan Besaran Uang Masa Kerja <br>
                    Masa Kerja :
                    @php
                    $tanggal = $cekjmk->tgl_pembayaran;
                    $nextmonth = date('Y-m-d', strtotime('+1 month', strtotime($tanggal)));
                    @endphp
                    @php
                    $awalmasakerja = date_create($nextmonth);
                    $akhirmasakerja = date_create($kontrak->sampai); // waktu sekarang
                    $diffmasakerja = date_diff( $awalmasakerja, $akhirmasakerja );
                    echo $diffmasakerja->y . ' tahun, '.$diffmasakerja->m.' bulan, '.$diffmasakerja->d.' Hari'
                    @endphp
                </th>
            </tr>
            <tr>
                <td>
                    <table class="datatable4">
                        <tr>
                            <td>Gaji Pokok</td>
                            <td>:</td>
                            <td>Rp.</td>
                            <td style="text-align: right">{{ rupiah($kb->gaji_pokok) }}</td>
                        </tr>
                        <tr>
                            <td>Tj. Jabatan</td>
                            <td>:</td>
                            <td>Rp.</td>
                            <td style="text-align: right">{{ rupiah($kb->t_jabatan) }}</td>
                        </tr>
                        <tr>
                            <td>Tj.Tanggung Jawab</td>
                            <td>:</td>
                            <td>Rp.</td>
                            <td style="text-align: right">{{ rupiah($kb->t_tanggungjawab) }}</td>
                        </tr>
                        <tr>
                            <td>Uang Makan</td>
                            <td>:</td>
                            <td>Rp.</td>
                            <td style="text-align: right">{{ rupiah($kb->t_makan) }}</td>
                        </tr>
                        <tr>
                            <td>Skill Khusus</td>
                            <td>:</td>
                            <td>Rp.</td>
                            <td style="text-align: right">{{ rupiah($kb->t_skill) }}</td>
                        </tr>
                    </table>
                </td>
                <td valign="top" rowspan="2">
                    <table class="datatable4">
                        <tr>
                            <?php
                            $masakerja = $diffmasakerja->y;
                            if($masakerja >= 3 && $masakerja < 6){
                                $jmlkali=2;
                            }else if($masakerja >= 6 && $masakerja < 9 ){
                                $jmlkali =3;
                            }else if($masakerja >= 9 && $masakerja < 12 ){
                                $jmlkali =4;
                            }else if($masakerja >= 12 && $masakerja < 15 ){
                                $jmlkali =5;
                            }else if($masakerja >= 15 && $masakerja < 18 ){
                                $jmlkali =6;
                            }else if($masakerja >= 18 && $masakerja < 21 ){
                                $jmlkali =7;
                            }else if($masakerja >= 21 && $masakerja < 24 ){
                                $jmlkali =8;
                            }else if($masakerja >= 24 ){
                                $jmlkali =10;
                            }else{
                                $jmlkali = 0.5;
                            }

                            if($masakerja <= 2){
                                $totalupah = $kb->gaji_pokok ;
                            }else{
                                $totalupah = $kb->gaji_pokok + $kb->t_tanggungjawab + $kb->t_makan + $kb->t_skill + $kb->t_jabatan;
                            }

                            ?>
                            @if ($cekjmk != null)
                            @php
                            $persentasejmk = 15;
                            @endphp
                            @else
                            @php
                            $persentasejmk = 25;
                            @endphp
                            @endif
                            @php
                            $totalpemutihan = ($persentasejmk/100) * $totalupah;
                            @endphp
                            <td style="width: 2px">1.</td>
                            <td>Jasa Masa Kerja</td>
                            <td>{{ $persentasejmk }}%</td>
                            <td>x</td>
                            <td>Rp. {{ rupiah($totalupah) }}</td>
                            <td>Rp.</td>
                            <td style="text-align:right">{{ rupiah($totalpemutihan) }}</td>
                        </tr>
                        <tr>

                            <td style="width: 2px; border-bottom:1px solid black">2.</td>
                            <td style="border-bottom:1px solid black">Uang Pengganti Hak</td>
                            <td style="border-bottom:1px solid black">0%</td>
                            <td style="border-bottom:1px solid black">x</td>
                            <td style="border-bottom:1px solid black">Rp. {{ rupiah($totalpemutihan) }}</td>
                            <td style="border-bottom:1px solid black">Rp.</td>
                            <td style="border-bottom:1px solid black; text-align:right">
                                @php
                                $uph = (0/100) * ($jmlkali * $totalupah);
                                @endphp
                                {{ rupiah($uph) }}
                            </td>
                        </tr>
                        <tr style="font-weight:bold">
                            <td colspan="5">Jumlah Uang Jasa Masa Kerja</td>
                            <td>Rp.</td>
                            <td style="text-align:right; font-weight:bold">
                                @php
                                $jml_ujmk = (($persentasejmk/100) * $totalupah) + $uph;
                                @endphp
                                {{ rupiah($jml_ujmk) }}
                            </td>
                        </tr>
                        @php
                        $totalpotongan = 0;
                        @endphp
                        @foreach ($potongan as $d)
                        @php
                        $totalpotongan += $d->jumlah;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}.</td>
                            <td colspan="4">{{ $d->keterangan }}</td>
                            <td>Rp.</td>
                            <td style="text-align: right">{{ rupiah($d->jumlah)  }}</td>
                        </tr>
                        @endforeach
                        <tr style="font-weight:bold">
                            <td colspan="5" style="border-bottom:1px solid black">Jumlah Potongan</td>
                            <td style="border-bottom:1px solid black">Rp.</td>
                            <td style="border-bottom:1px solid black; text-align:right">{{ rupiah($totalpotongan) }}</td>
                        </tr>
                        <tr style="font-weight:bold">
                            <td colspan="5">Jumlah Uang Yang Diterima Karyawan</td>
                            <td>Rp.</td>
                            <td style="text-align:right">
                                @php
                                $totalditerima = $jml_ujmk - $totalpotongan;
                                @endphp
                                {{ rupiah($totalditerima) }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td>
                    <table class="datatable4">
                        <tr>
                            <td style="font-weight:bold">Total Upah</td>
                            <td style="font-weight:bold; text-align:right">

                                {{ rupiah($totalupah) }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <p>
            <ol start="3">
                <li>
                    PIHAK KEDUA ( II ) dapat menerima dengan baik kompensasi atau kebijakan dari PIHAK PERTAMA (I) seperti tersebut di atas.
                </li>
                <li>
                    Dengan ditandatanganinya kesepakatan bersama ini oleh kedua belah pihak, PIHAK PERTAMA ( I ) dan PIHAK KEDUA ( II ) menyatakan permasalahan telah selesai dan tidak ada saling menuntut apapun dikemudian hari.
                </li>
            </ol>
            Demikian Kesepakatan Bersama ini dibuat dan ditandatangani oleh kedua belah pihak.
        </p>
        <table class="datatable4">
            <tr>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td style="text-align:center">PIHAK KEDUA</td>
                <td style="text-align:center">PIHAK PERTAMA</td>
                <td style="text-align:center">MENYETUJUI</td>
            </tr>
            <tr>
                <td style="height: 80px"></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="text-align:center">
                    <u>{{ $kb->nama_karyawan }}</u><br>
                    Karyawan
                </td>
                <td style="text-align:center">
                    <u>{{ $nama_pihaksatu }}</u><br>
                    {{ $jabatan_pihaksatu }}
                </td>
                <td style="text-align:center">
                    <u>{{ $approve->direktur }}</u><br>
                    Direktur
                </td>

            </tr>
        </table>
    </section>

</body>

</html>
