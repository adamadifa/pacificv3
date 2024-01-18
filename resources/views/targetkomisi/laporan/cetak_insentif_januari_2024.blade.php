<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Analisa Umur Piutang (AUP) {{ date('d-m-y') }}</title>
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

        a {
            color: white;
        }

        .table-scroll {
            position: relative;
            max-width: 100%;
            margin: auto;
            overflow: hidden;

        }

        .table-wrap {
            width: 100%;
            overflow: auto;
        }

        .table-scroll table {
            width: 100%;
            margin: auto;
            border-collapse: separate;
            border-spacing: 0;
        }


        .clone {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
        }

        .clone th,
        .clone td {
            visibility: hidden
        }

        .clone td,
        .clone th {
            border-color: transparent
        }

        .clone tbody th {
            visibility: visible;
            color: red;
        }

        .clone .fixed-side {
            border: 1px solid #000;
            background: #eee;
            visibility: visible;
        }
    </style>
</head>

<body>
    <b style="font-size:14px;">
        @if ($cabang != null)
            @if ($cabang->kode_cabang == 'PST')
                PACIFIC PUSAT
            @else
                PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
            @endif
        @else
            PACIFC ALL CABANG
        @endif
        <br>
        LAPORAN INSENTIF KEPALA ADMIN<br>
        {{ $namabulan[$bulan] }} {{ $tahun }}
    </b>
    <br>

    @php
        function getreward($realisasi, $jenis_reward)
        {
            if ($realisasi >= 60 && $realisasi <= 65) {
                if ($jenis_reward == 'kendaraan') {
                    $reward = 25000;
                }
            } elseif ($realisasi > 65 && $realisasi <= 70) {
                if ($jenis_reward == 'kendaraan') {
                    $reward = 50000;
                }
            } elseif ($realisasi > 70 && $realisasi <= 75) {
                if ($jenis_reward == 'kendaraan') {
                    $reward = 75000;
                }
            } elseif ($realisasi > 75 && $realisasi <= 80) {
                if ($jenis_reward == 'kendaraan') {
                    $reward = 100000;
                }
            } elseif ($realisasi > 80 && $realisasi <= 85) {
                if ($jenis_reward == 'kendaraan') {
                    $reward = 125000;
                }
            } elseif ($realisasi > 85 && $realisasi <= 90) {
                if ($jenis_reward == 'kendaraan') {
                    $reward = 150000;
                }
            } elseif ($realisasi > 90 && $realisasi <= 95) {
                if ($jenis_reward == 'kendaraan') {
                    $reward = 175000;
                }
            } elseif ($realisasi > 95) {
                if ($jenis_reward == 'kendaraan') {
                    $reward = 200000;
                }
            } else {
                $reward = 0;
            }

            return $reward;
        }
    @endphp
    <table class="datatable3">
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">CABANG</th>
                <th colspan="2">KENDARAAN</th>
            </tr>
            <tr>
                <th>REALISASI</th>
                <th>REWARD</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($insentif as $d)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $d->nama_cabang }}</td>
                    <td align="center">{{ !empty($d->ratio_kendaraan) ? $d->ratio_kendaraan . '%' : '' }}</td>
                    <td>
                        @php
                            $rewardkendaraan = getreward($d->ratio_kendaraan, 'kendaraan');
                        @endphp
                        {{ rupiah($rewardkendaraan) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
</body>

</html>
