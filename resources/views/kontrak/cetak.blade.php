<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Cetak Kontrak PKWT</title>

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

        hr {
            display: block;
            height: 1px;
            background: transparent;
            width: 100%;
            border: none;
            border-top: solid 2px #101010;
        }

    </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="A4">

    <!-- Each sheet element should have the class "sheet" -->
    <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
    <section class="sheet padding-10mm">
        @if ($kontrak->id_perusahaan=="MP")
        <table style="width: 100%">
            <tr>
                <td style="width: 20%; text-align:center">
                    <img src="{{ asset('app-assets/images/logo/mp.png') }}" alt="" style="width: 80px; height:80px">
                </td>
                <td style="text-align: left">
                    <h3 style="font-family:'Cambria'; line-height:0px">CV MAKMUR PERMATA</h3>
                    <span style="font-size: 1.2rem"><i>Factory / Head Office</i></span><br>
                    <span style="font-family:'Times New Roman'">Jl. Perintis Kemerdekaan No. 160 Tasikmalaya</span><br>
                    <span style="font-size: 12px">Telp (0265) 336794 Fax (0265) 332329</span><br>
                    <span style="font-size: 11px">e-mail : pacific.tasikmalaya@gmail.com</span>
                </td>
                <td>

                </td>
            </tr>
        </table>
        @else
        <table style="width: 100%">
            <tr>
                <td style="width: 20%; text-align:center">
                    <img src="{{ asset('app-assets/images/logo/pcf.png') }}" alt="" style="width: 80px; height:80px">
                </td>
                <td style="text-align: left">
                    <h3 style="font-family:'Cambria'; line-height:0px">CV PACIFIC TASIKMALAYA</h3>
                    <span style="font-size: 1.2rem"><i>Factory / Head Office</i></span><br>
                    <span style="font-family:'Times New Roman'">Jl. Perintis Kemerdekaan No. 160 Tasikmalaya</span><br>
                    <span style="font-size: 12px">Telp (0265) 336794 Fax (0265) 332329</span><br>
                    <span style="font-size: 11px">e-mail : pacific.tasikmalaya@gmail.com</span>
                </td>
                <td>

                </td>
            </tr>
        </table>
        @endif

        <hr>
        <h3 style="text-align: center">
            <u>PERJANJIAN KERJA</u>
            <br>
            WAKTU TERTENTU
        </h3>
        <p>
            Yang bertanda tangan dibawah ini :
            @php
            $pihak_satu = explode("-",$approve->pihak_satu);
            $nama_pihaksatu = $pihak_satu[0];
            $jabatan_pihaksatu = $pihak_satu[1];
            @endphp
            <table>
                <tr>
                    <td style="width: 120px">Nama</td>
                    <td>:</td>
                    <td>{{ $nama_pihaksatu }}</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>{{ $jabatan_pihaksatu }}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>Jl. Perintis Kemerdekaan No.160 Tasikmalaya</td>
                </tr>
            </table>
        </p>

        <p>
            Bertindak untuk dan atas nama {{ $kontrak->id_perusahaan=="MP" ? "CV Makmur Permata" : "CV. Pacific" }} berkedudukan di Tasikmalaya selanjutnya disebut <b>pihak kesatu.</b>
        </p>

        <p>
            <table>
                <tr>
                    <td style="width: 120px">Nama</td>
                    <td>:</td>
                    <td>{{ ucwords(strtolower($kontrak->nama_karyawan)) }}</td>
                </tr>
                <tr>
                    <td>Tempat, Tgl Lahir</td>
                    <td>:</td>
                    <td>{{ $kontrak->tempat_lahir }}, {{ !empty($kontrak->tgl_lahir) ? DateToIndo2($kontrak->tgl_lahir) : '' }}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ $kontrak->alamat }}</td>
                </tr>
                <tr>
                    <td>No. Identitas</td>
                    <td>:</td>
                    <td>{{ $kontrak->no_ktp }}</td>
                </tr>
            </table>
            Bertindak atas nama diri sendiri selanjutnya disebut <b>pihak kedua.</b><br>
            Pihak kesatu dan pihak kedua telah mengadakan kesepakatan Perjanjian Kerja Waktu Tertentu dengan ketentuan sebagai berikut:

        </p>
        <p>
            <h4 style="text-align: center">
                PASAL 1<br>
                MASA BERLAKU

            </h4>

            @php
            $date1 = date_create($kontrak->dari);
            $date2 = date_create($kontrak->sampai);

            $interval = date_diff($date1, $date2);



            @endphp
            <ol>
                <li>
                    Perjanjian kerja ini berlaku
                    {{-- {{ !empty($interval->y) ? $interval->y. "(".terbilang($interval->y).") Tahun" : "" }}
                    {{ !empty($interval->m) && empty($interval->y) ? $interval->m.  "(".terbilang($interval->m).") Bulan" : "" }}
                    {{ !empty($interval->d) && empty($interval->m) ? $interval->d. " Hari" : "" }} --}}
                    @php
                    $start = date_create($kontrak->dari);
                    $end = date_create($kontrak->sampai);

                    echo diffInMonths($start, $end). " bulan";
                    @endphp
                    , terhitung dari tanggal {{ DateToIndo2($kontrak->dari) }} sampai dengan tanggal {{ DateToIndo2($kontrak->sampai) }}
                </li>
                <li>
                    Perjanjian ini dapat diperpanjang untuk waktu yang disepakati dan untuk perpanjangan perjanjian kerja waktu tertentu ini pihak kesatu akan memberitahukan terlebih dahulu kepada pihak kedua dalam waktu 1 (satu) minggu sebelum perjanjian kerja waktu tertentu ini berakhir.
                </li>
            </ol>
        </p>

        <p>
            <h4 style="text-align: center">
                PASAL 2<br>
                STATUS DAN PENDAPATAN
            </h4>
            @php
            $totalgaji = $kontrak->gaji_pokok + $kontrak->t_jabatan + $kontrak->t_masakerja + $kontrak->t_tanggungjawab + $kontrak->t_makan + $kontrak->t_istri + $kontrak->t_skill;
            @endphp
            <ol>
                <li>
                    Pihak Kedua menerima pekerjaan yang diberikan pihak CV Makmur Permata dengan jabatan sebagai {{ $kontrak->nama_jabatan }} yang berlokasi di PUSAT serta bersedia ditempatkan diluar lokasi dan departemen tersebut bila Perusahaan memerlukan.
                </li>
                <li>
                    Pihak kedua setuju menerima upah sebesar Rp {{ rupiah($totalgaji) }} ,- dengan rincian sebagai berikut :
                    <table>
                        <tr>
                            <td>a.</td>
                            <td style="width:140px">Gaji Pokok</td>
                            <td>:</td>
                            <td>Rp. {{ rupiah($kontrak->gaji_pokok) }},-</td>
                        </tr>

                        <tr>
                            <td>b.</td>
                            <td style="width:140px">Tj. Jabatan</td>
                            <td>:</td>
                            <td>Rp. {{ rupiah($kontrak->t_jabatan) }},-</td>
                        </tr>
                        <tr>
                            <td>c.</td>
                            <td style="width:140px">Tj. Tanggungjawab</td>
                            <td>:</td>
                            <td>Rp. {{ rupiah($kontrak->t_tanggungjawab) }},-</td>
                        </tr>
                        <tr>
                            <td>d.</td>
                            <td style="width:140px">Tj. Makan</td>
                            <td>:</td>
                            <td>Rp. {{ rupiah($kontrak->t_makan) }},-</td>
                        </tr>

                        <tr>
                            <td>e.</td>
                            <td style="width:140px">Skill Khusus</td>
                            <td>:</td>
                            <td>Rp. {{ rupiah($kontrak->t_skill) }},-</td>
                        </tr>
                    </table>
                </li>
            </ol>

        </p>
        <p>
            <h4 style="text-align: center">
                PASAL 3<br>
                JANGKA WAKTU DAN WAKTU KERJA
            </h4>
            <ol>
                <li>
                    Jam kerja adalah 8 jam sehari (termasuk istirahat 1 jam) atau 40 Jam seminggu Senin s/d Jumat 07.00 – 15.00 WIB dan Sabtu Jam 07.00 – 12.00 WIB. Atau sesuai jadwal kerja yang disepakati bersama.
                </li>
                <li>
                    Untuk lokasi cabang, hari dan jam kerja akan dilaksanakan dengan ketentuan yang telah disepakati oleh masing-masing cabang.
                </li>
            </ol>
        </p>

    </section>
    <section class="sheet padding-10mm">
        <p>
            <h4 style="text-align: center">
                PASAL 4<br>
                PEMUTUSAN HUBUNGAN KERJA
            </h4>
            <ol>
                <li>
                    Perjanjian kerja ini dapat terputus dan berakhir sebelum masa berlakunya, apabila :
                    <ol type="a">
                        <li>Hasil Evaluasi Pekerja dinilai tidak mampu dan tidak cakap melaksanakan tugasnya</li>
                        <li>
                            Pekerja tidak hadir selama 5 (lima) hari secara berurutan dalam 1 (satu) bulan, tanpa izin atau tanpa alasan yang bisa dipertanggungjawabkan.
                        </li>
                        <li>
                            Pekerja mengajukan pengunduran diri.
                        </li>
                    </ol>
                </li>
                <li>
                    Dalam hal pekerja diberhentikan karena kesalahan pekerja atau pengunduran diri maka Pekerja hanya akan menerima pendapatan atau upah sampai saat tanggal pemutusan perjanjian kerja tersebut.
                </li>
                <li>
                    Dalam hal pihak kesatu atau pihak kedua melakukan pemutusan perjanjian kerja sebagaimana dimaksud diatas maka pihak kedua tidak berhak menuntut ganti rugi.
                </li>
                <li>
                    Apabila pihak kedua habis kontrak dan tidak diperpanjang, maka pihak kesatu tidak wajib memberikan alasan tentang tidak diperpanjangnya.
                </li>
                <li>
                    Untuk hal-hal yang belum tercantum dalam syarat-syarat kerja ini berlaku ketentuan-ketentuan umum pada PKB.
                </li>
                <li>
                    Apabila dikemudian hari terdapat kekeliruan pada surat perjanjian kerja bersama ini maka akan ditinjau kembali dan diperbaiki sebagaimana mestinya.
                </li>
            </ol>
        </p>
        <p>
            <h4 style="text-align: center">
                PASAL 5<br>
                TATA TERTIB DAN DISIPLIN KERJA
            </h4>
            <ol>

                <li>
                    Tata tertib dan disiplin kerja berlaku ketentuan Peraturan Perusahaan yang tercantum dalam PKB (Perjanjian Kerja Bersama)
                </li>
                <li>
                    Pelanggaran tata tertib PKB (Perjanjian Kerja Bersama) oleh pihak kedua dapat diberikan peringatan baik lisan maupun tulisan dan bila terpaksa berlaku scorsing sampai pemutusan hubungan kerja dengan landasan hukum yang dipergunakan oleh pihak kesatu adalah PKB (Perjanjian Kerja Bersama) dan peraturan ketenagakerjaan yang berlaku.
                </li>
                <li>
                    Izin tidak masuk kerja terlebih dahulu meminta izin tertulis kepada pimpinan.
                </li>
                <li>
                    Pihak kesatu berhak memindahkan / menempatkan pihak kedua dari pekerjaan yang dianggap perlu oleh pihak kesatu dan pihak kedua wajib mematuhi dan melaksanakannya dengan penuh tanggung jawab.
                </li>
            </ol>
        </p>
        <p>
            <h4 style="text-align: center">
                PASAL 6<br>
                KETENTUAN SANKSI
            </h4>
            <ol>
                <li>Pihak kedua wajib bertanggungjawab terhadap tugas yang diberikan oleh pimpinan.</li>
                <li>Pihak kedua wajib mengganti kerugian apabila pihak kedua merusak barang atau peralatan lainnya baik disengaja ataupun tidak disengaja milik perusahaan sehingga menyebabkan kerugian bagi perusahaan.</li>
                <li>Pihak kedua akan dituntut secara hukum apabila pihak kedua melakukan pencurian milik perusahaan baik dilakukan secara individu atau bekerjasama dengan pihak lain atau pihak ketiga.</li>
                <li>Pihak kedaua akan di scorsing sesuai dengan peraturan perusahaan yang berlaku, yaitu PKB (Perjanjian Kerja Bersama) apabila pihak kedua mangkir dari tugas dan tanggungjawabnya.</li>
            </ol>
        </p>
        <p>
            <h4 style="text-align: center">
                PASAL 7<br>
                JAMINAN SOSIAL
            </h4>
            <ol>
                <li>Seragam diatur di Peraturan Perusahaan.</li>
                <li>Cuti diberikan setelah masa kerja satu tahun dan pengambilan cutinya jatuh pada bulan ketiga belas.</li>
                <li>Cuti dalam kasus meninggalnya istri, ayah/ibu kandung, dan anak kandung diberikan cuti selama dua hari berturut turut.</li>

            </ol>
        </p>
        <p style="text-align: center">
            <h4 style="text-align: center">
                PASAL 8<br>
                PENUTUP
            </h4>
            Demikian perjanjian kerja bersama waktu tertentu ini dibuat dan ditandatangani oleh kedua belah pihak dalam keadaan sehat walafiat, sadar, mengerti tanpa ada paksaan dari siapapun atau pihak manapun.
        </p>
        <table class="datatable4">
            <tr>
                <td colspan="3" style="text-align: center">Tasikmalaya,{{ DateToIndo2(date('Y-m-d')) }}</td>
            </tr>
            <tr>
                <td style="text-align:center">PIHAK KEDUA</td>
                <td style="text-align:center">PIHAK PERTAMA</td>
            </tr>
            <tr>
                <td style="height: 80px"></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="text-align:center">
                    <u>{{ $kontrak->nama_karyawan }}</u><br>
                    Karyawan
                </td>
                <td style="text-align:center">
                    <u>{{ $nama_pihaksatu }}</u><br>
                    {{ $jabatan_pihaksatu }}
                </td>
            </tr>
        </table>
    </section>
</body>

</html>
