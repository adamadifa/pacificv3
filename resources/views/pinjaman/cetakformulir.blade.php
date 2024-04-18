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
            font-size: 14px;
        }

        .datatable4 td {
            border: 0px solid #000000;
            padding: 5px;
        }

        hr {
            display: block;
            height: 1px;
            background: transparent;
            width: 100%;
            border: none;
            border-top: solid 2px #101010;
        }

        li {
            line-height: 20px;
        }
    </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->

<body class="A4">
    <section class="sheet padding-10mm">
        <table style="width: 100%">
            <tr>
                <td style="text-align: center">
                    <h3 style="font-family:'Cambria'; line-height:0px">PINJAMAN KARYAWAN</h3>
                    <h3 style="font-family:'Cambria'; line-height:0px">CV. PACIFIC & CV. MAKMUR PERMATA</h3>
                    <span style="font-family:'Times New Roman'">Jl. Perintis Kemerdekaan No. 160 Tasikmalaya</span><br>
                    <span style="font-size: 12px">Telp (0265) 336794 Fax (0265) 332329</span><br>
                    <span style="font-size: 11px">e-mail : pacific.tasikmalaya@gmail.com</span>
                </td>
            </tr>
        </table>
        <hr>
        <u>
            <h3 style="font-family:'Cambria'; line-height:0px; text-align:center">FORMULIR PENGAJUAN PINJAMAN KARYAWAN
            </h3>
        </u>
        <br>
        <table class="datatable4">
            <tr>
                <td style="width: 200px">NIK</td>
                <td>:</td>
                <td>{{ $pinjaman->nik }}</td>
            </tr>
            <tr>
                <td>Nama Karyawan</td>
                <td>:</td>
                <td>{{ $pinjaman->nama_karyawan }}</td>
            </tr>
            <tr>
                <td>Status Karyawan</td>
                <td>:</td>
                <td>{{ $pinjaman->status_karyawan == 'T' ? 'Tetap' : 'Kontrak' }}</td>
            </tr>
            <tr>
                <td>Jabatan/Posisi Kerja</td>
                <td>:</td>
                <td>{{ ucwords(strtolower($pinjaman->nama_jabatan)) }}</td>
            </tr>
            <tr>
                <td>Fasilitas Pinjaman</td>
                <td>:</td>
                <td>Pinjaman Jangka Panjang (PJP)</td>
            </tr>
            <tr>
                <td>Acc. Pencairan</td>
                <td>:</td>
                <td>{{ rupiah($pinjaman->jumlah_pinjaman) }}</td>
            </tr>
            <tr>
                <td>Terbilang</td>
                <td>:</td>
                <td>{{ ucwords(terbilang($pinjaman->jumlah_pinjaman)) }} Rupiah</td>
            </tr>
            <tr>
                <td>Jangka Waktu</td>
                <td>:</td>
                <td>{{ $pinjaman->angsuran }} bulan</td>
            </tr>
            <tr>
                <td> Cicilan/Bulan </td>
                <td>:</td>
                <td>{{ rupiah($pinjaman->jumlah_angsuran) }}</td>
            </tr>
        </table>
        <ol>
            <li>
                Apabila di kemudian hari saya tidak lagi bekerja di perusahaan ini, maka sisa cicilan pinjaman
                yang belum lunas akan di selesaikan dan diperhitungkan dari uang yang saya terima saat saya
                berhenti kerja
            </li>
            <li>
                Apabila uang yang diperoleh saat saya berhenti bekerja tersebut tidak mencukupi maka saya
                akan menyelesaikan sisa cicilan pinjaman ini secara pribadi.
            </li>

        </ol>
        <p>
            Demikian pengajuan ini saya buat dengan sebenarnya dan atas keinginan sendiri
        </p>
        <br>
        <table style="width: 100% !important">
            <tr>
                <td colspan="2"></td>
                <td style="text-align: left">Tasikmalaya, {{ DateToIndo2($pinjaman->tgl_pinjaman) }}</td>
            </tr>
            <tr>
                <td style="text-align: center; width:30%;"">Pemohon,
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <b>{{ ucwords(strtolower($pinjaman->nama_karyawan)) }}</b>
                </td>
                <td style=" text-align: center; width:30%; vertical-align:top">Diverifikasi Oleh,
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <b>Head Departemen</b>
                </td>
                <td style=" text-align: center; width:30%; vertical-align:top">Menyetujui,
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <b>Panitia Kredit / Keuangan</b>
                </td>
            </tr>
        </table>
    </section>
    <section class="sheet padding-10mm">
        <table style="width: 100%">
            <tr>
                <td style="text-align: left">
                    <h3 style="font-family:'Cambria'; line-height:0px">MAKMUR PERMATA GROUP</h3>
                    <i style="font-family:'Cambria';">Factory / Head Office :</i>
                    <br>
                    <span style="font-family:'Times New Roman'">Jl. Perintis Kemerdekaan No. 160 Tasikmalaya</span><br>
                    <span style="font-size: 12px">Telp (0265) 336794 Fax (0265) 332329</span><br>
                    <span style="font-size: 11px">e-mail : pacific.tasikmalaya@gmail.com</span>
                </td>
            </tr>
        </table>
        <h3 style="font-family:'Cambria'; line-height:0px; text-align:center; margin-top:40px">
            SURAT PERNYATAAN PERSETUJUAN PASANGAN PEKERJA
        </h3>
        <h4 style="font-family:'Cambria'; line-height:0px; text-align:center; margin-top:20px">
            Nomor : {{ $pinjaman->no_pinjaman }}
        </h4>
        <p style="margin-top:20px">
            Saya yang bertanda tangan di bawah ini :
        </p>
        <table class="datatable4">
            <tr>
                <td style="width: 200px">NIK</td>
                <td>:</td>
                <td></td>
            </tr>
            <tr>
                <td>Nama </td>
                <td>:</td>
                <td></td>
            </tr>
            <tr>
                <td>No. KTP</td>
                <td>:</td>
                <td></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Pekerjaan</td>
                <td>:</td>
                <td></td>
            </tr>
            <tr>
                <td>No. Handphone</td>
                <td>:</td>
                <td></td>
            </tr>
        </table>
        <p>
            Adalah benar pasangan sah sebagai suami istri dari pekerja :
        </p>
        <table class="datatable4">
            <tr>
                <td>Nama Karyawan</td>
                <td>:</td>
                <td>{{ $pinjaman->nama_karyawan }}</td>
            </tr>
            <tr>
                <td style="width: 200px">NIK</td>
                <td>:</td>
                <td>{{ $pinjaman->nik }}</td>
            </tr>
            <tr>
                <td>Departemen</td>
                <td>:</td>
                <td>{{ ucwords(strtolower($pinjaman->nama_dept)) }}</td>
            </tr>
            <tr>
                <td>Jabatan/Posisi Kerja</td>
                <td>:</td>
                <td>{{ ucwords(strtolower($pinjaman->nama_jabatan)) }}</td>
            </tr>
        </table>
        <p style="line-height: 1.5rem">
            Dengan ini menyatakan memberikan persetujuan sepenuhnya kepada pasangan saya untuk menggunakan fasilitas
            pinjaman di perusahaan MAKMUR PERMATA GROUP sebesar Rp {{ rupiah($pinjaman->jumlah_pinjaman) }} dengan
            kewajiban angsuran Rp {{ rupiah($pinjaman->jumlah_angsuran) }} selama {{ $pinjaman->angsuran }} kali
            dipotong dari
            gaji setiap bulan.
        </p>
        <p style="line-height: 1.5rem">
            Demikian persetujuan ini diberikan untuk dipergunakan sebagaimana mestinya.
        </p>
        <table style="width: 100% !important">
            <tr>
                <td colspan="2"></td>
                <td style="text-align: left">Tasikmalaya, {{ DateToIndo2($pinjaman->tgl_pinjaman) }}
                    <br>
                    Yang Membuat Pernyataan
                </td>
            </tr>
            <tr>
                <td style="text-align: center; width:30%;"">
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <b></b>
                </td>
                <td style=" text-align: center; width:30%; vertical-align:top">
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>

                </td>
                <td style=" text-align: center; width:30%; vertical-align:top; border-bottom:1px solid #000">
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <b></b>
                </td>
            </tr>
        </table>
    </section>
</body>
