<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Cetak Surat Izin Absen</title>

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
<body class="A4 landscape">

    <!-- Each sheet element should have the class "sheet" -->
    <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
    <section class="sheet padding-10mm">
        <h2 style="text-align: center">SURAT KETERANGAN ABSEN</h2>
        <table class="datatable3" style="font-size: 16px">
            <tr>
                <td>Nama : {{ $izin->nama_karyawan }}</td>
                <td>Jabatan : {{ $izin->nama_jabatan }} </td>
            </tr>
            <tr>
                <td>Departemen : {{ $izin->kode_dept }}</td>
                <td>Tgl Pengisian Form : {{ DateToIndo2($izin->dari) }}</td>
            </tr>
            <tr>
                <td>
                    <p>Tandai Absensi : </p>
                    <p>
                        Jenis Keterangan (&#10003; salah satu)
                        <br>
                        <br>
                        {!! $izin->status=="i" && $izin->jenis_izin=="TL" ? "&#9745;" : "&#9634" !!} Datang Terlambat (>10 Menit) Pukul {!! $izin->status=="i" && $izin->jenis_izin=="TL" ? $izin->jam_terlambat : "..............." !!}<br>
                        {!! $izin->status=="k" ? "&#9745;" : "&#9634" !!} Lupa Absensi (Datang / Pulang) Pukul {!! $izin->status=="k" ? $izin->jam_masuk."s/d".$izin->jam_pulang : "......." !!} <br>
                        &#9634; Tugas Dinas Dalam Kota Pukul<br>
                        {!! $izin->status=="i" && $izin->jenis_izin=="KL" ? "&#9745;" : "&#9634" !!} Izin Meninggalkan Kantor Untuk Urusan Pribadi Pukul {!! $izin->status=="i" && $izin->jenis_izin=="KL" ? $izin->jam_keluar." s/d ".$izin->jam_masuk : " .........s/d.........." !!}<br>
                        {!! $izin->status=="s" ? "&#9745;" : "&#9634" !!} Sakit<br>
                        {!! $izin->status=="i" && $izin->jenis_izin=="TM" ? "&#9745;" : "&#9634" !!}; Izin Tidak Masuk Kantor<br>
                        &#9634; .....................................<br>

                    </p>
                </td>
                <td valign="top">
                    Keterangan : {{ $izin->keterangan }}
                </td>
            </tr>
        </table>
        <br>
        <table class="datatable3" style="text-align: center; font-size:16px">
            <tr>
                <td>Dibuat Oleh</td>
                <td>Disetujui Oleh</td>
                <td>Diperiksa Oleh</td>
                <td>Diketahui</td>
            </tr>
            <tr>
                <td style="height: 70px"></td>
                <td style="height: 40px"></td>
                <td style="height: 40px"></td>
                <td style="height: 40px"></td>
            </tr>
            <tr>
                <td>Karyawan</td>
                <td>Manager Ybs</td>
                <td>Personalia HRD</td>
                <td>General Manager</td>
            </tr>
        </table>
    </section>
</body>

</html>
