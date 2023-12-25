<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Kebutuhan Cabang {{ date('d-m-y') }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&display=swap');

        body {
            font-family: 'Poppins'
        }

        .datatable3 {
            border: 2px solid #D6DDE6;
            border-collapse: collapse;
            font-size: 11px;
        }

        .datatable3 td {
            border: 1px solid #000000;
            padding: 6px;
        }

        .datatable3 th {
            border: 2px solid #828282;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <p>
        <b style="font-size:14px;">
            ESTIMASI KEBUTUHAN CABANG <br>
            CABANG {{ !empty($kode_cabang) ? $kode_cabang : Auth::user()->kode_cabang }}
        </b>
    </p>
    <table class="datatable3">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr>
                <th>No.</th>
                <th>Jenis Kebutuhan</th>
                <th>Uraian</th>
                <th>Periode Akhir</th>
                <th>Sisa Waktu</th>
                <th>Cabang</th>
            </tr>
        </thead>
        @foreach ($kc as $d)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->jenis_kebutuhan }}</td>
                <td>{!! $d->uraian_kebutuhan !!}</td>
                <td>{{ DateToIndo2($d->periode_akhir) }}</td>
                <td>
                    @php
                        $start = date_create(date('Y-m-d')); //Tanggal Masuk Kerja
                        $end = date_create($d->periode_akhir); // Tanggal Presensi
                        $diff = date_diff($start, $end); //Hitung Masa Kerja
                        $sisahari = $diff->days; // Value Masa Kerja
                    @endphp
                    {{ $sisahari }}
                </td>
                <td>{{ $d->kode_cabang }}</td>

            </tr>
        @endforeach
    </table>
</body>

</html>
