<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Persentase SFA</title>
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
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <b style="font-size:14px;">
        PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
        <br>
        LAPORAN DATA ACTIVITY SMM<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br />
    </b>
    <table class="datatable3" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>No</th>
                <th>Tanggal</th>
                <th>Aktifitas</th>
                <th>Jarak</th>
                <th>Jarak Waktu</th>
                <th>Foto</th>
            </tr>
        </thead>
        <tbody>
            @php
                $lat_start = $lokasi[0];
                $long_start = $lokasi[1];
                $start_time = '';
                //$tanggal = '';
            @endphp

            @foreach ($smmactivity as $key => $d)
                @php
                    $tanggal = date('Y-m-d', strtotime(@$smmactivity[$key + 1]->tanggal));
                    $jarak = hitungjarak($lat_start, $long_start, $d->latitude, $d->longitude);
                    $totaljarak = round(round($jarak['meters']) / 1000);
                    $totalwaktu = !empty($start_time) ? hitungjamdesimal($start_time, $d->tanggal) : hitungjamdesimal(date('Y-m-d', strtotime($d->tanggal)) . ' 08:00', $d->tanggal);
                @endphp

                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ date('d-m-Y H:i:s', strtotime($d->tanggal)) }}</td>
                    <td>{{ $d->aktifitas }}</td>
                    <td>
                        {{ $totaljarak }} KM
                    </td>
                    <td>{{ $totalwaktu }} Jam</td>
                    <td>
                        @if (!empty($d->foto))
                            @php
                                $path = Storage::url('uploads/smactivity/' . $d->foto);
                            @endphp
                            <img class="media-object rounded-circle" src="{{ url($path) }}" alt="Avatar"
                                height="30" width="30">
                        @endif
                    </td>
                </tr>
                @php
                    $lat_start = $d->latitude;
                    $long_start = $d->longitude;
                    $start_time = $d->tanggal;
                @endphp
                @if ($tanggal != date('Y-m-d', strtotime($d->tanggal)))
                    <tr>
                        <td colspan="6" style="background-color: #024a75"></td>
                    </tr>
                    @php
                        $lat_start = $lokasi[0];
                        $long_start = $lokasi[1];
                        $start_time = '';
                    @endphp
                @endif
                @php
                    $tanggal = date('Y-m-d', strtotime($d->tanggal));
                @endphp
            @endforeach
        </tbody>


    </table>
</body>

</html>
