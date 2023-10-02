<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan karyawan</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&display=swap');

        body {
            font-family: 'Poppins'
        }

        .datatable3 {
            border: 1px solid #000000;
            border-collapse: collapse;
            font-size: 11px;
        }

        .datatable3 td {
            border: 1px solid #000000;
            padding: 6px;
        }

        .datatable3 th {
            border: 1px solid #000000;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }

        tr:nth-child(even) {
            background-color: #c7c7c7c2;
        }
    </style>
</head>

<body>
    <b style="font-size:20px;">
        LAPORAN KARYAWAN<br>
        {{ $cabang != '' ? 'CABANG ' . $cabang : 'SEMUA CABANG' }}
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:200%" border="1">
        <thead>
            <tr>
                <th bgcolor="#024a75" style="color:white; font-size:14;">NO</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">NIK</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">NIK KTP</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">NAMA LENGKAP</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">TGL MASUK</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">MASA KERJA</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">JABATAN</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">DEPARTEMEN</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">GROUP</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">KANTOR</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">KLASIFIKASI</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">TEMPAT LAHIR</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">TANGGAL LAHIR</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">ALAMAT</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">NO HP</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">PENDIDIKAN</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">JK</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">STATUS KAWIN</th>
                <th bgcolor="#024a75" style="color:white; font-size:14;">STATUS KARYAWAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($karyawan as $v)
                <tr>
                    <td align="center">{{ $loop->iteration }}</td>
                    <td>{{ $v->nik }}</td>
                    <td>{{ $v->no_ktp }}</td>
                    <td>{{ $v->nama_karyawan }}</td>
                    <td>{{ DateToIndo2($v->tgl_masuk) }}</td>
                    <td>{{ DateToIndo2($v->tgl_masuk) }}</td>
                    <td>{{ $v->nama_jabatan }}</td>
                    <td>{{ $v->kode_dept }}</td>
                    <td>{{ $v->nama_group }}</td>
                    <td>{{ $v->id_kantor }}</td>
                    <td>{{ $v->klasifikasi }}</td>
                    <td>{{ $v->tempat_lahir }}</td>
                    <td>{{ DateToIndo2($v->tgl_lahir) }}</td>
                    <td>{{ $v->alamat }}</td>
                    <td>{{ $v->no_hp }}</td>
                    <td>{{ $v->pendidikan_terakhir }}</td>
                    <td>{{ $v->jenis_kelamin == '1' ? 'Laki-laki' : 'Perempuan' }}</td>
                    <td>
                        @if ($v->jenis_kelamin == '1')
                            Belum Menikah
                        @elseif ($v->jenis_kelamin == '2')
                            Menikah
                        @elseif ($v->jenis_kelamin == '3')
                            Janda
                        @elseif ($v->jenis_kelamin == '4')
                            Duda
                        @endif
                    </td>
                    <td>{{ $v->status_karyawan == 'T' ? 'Tetap' : 'Kontrak' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
