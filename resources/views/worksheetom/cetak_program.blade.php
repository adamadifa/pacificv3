<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Monitoring Program {{ date('d-m-y') }}</title>
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
    <b style="font-size:14px;">
        MONITORING PROGRAM <br>
        PERIODE
    </b>
    <table class="datatable3">
        <tr>
            <th>Kode Program</th>
            <td>{{ $program->kode_program }}</th>
        </tr>
        <tr>
            <th>Tanggal</th>
            <td>{{ DateToIndo2($program->tanggal) }}</th>
        </tr>
        <tr>
            <th>Nama Program</th>
            <td>{{ $program->nama_program }}</td>
        </tr>
        <tr>
            <th>Produk</th>
            <td>
                @php
                    $produk = unserialize($program->kode_produk);
                @endphp

                @foreach ($produk as $d)
                    {{ $d }},
                @endforeach

            </td>
        </tr>
        <tr>
            <th>Jml Target</th>
            <td>{{ rupiah($program->jml_target) }}</td>
        </tr>
        <tr>
            <th>Periode</th>
            <td>
                {{ date('d-m-Y', strtotime($program->dari)) }} s/d
                {{ date('d-m-Y', strtotime($program->sampai)) }}
                @php
                    $start_date = date_create($program->dari); //Tanggal Masuk Kerja
                    $end_date = date_create($program->sampai); // Tanggal Presensi
                    $diff = date_diff($start_date, $end_date); //Hitung Masa Kerja
                    $lama = ROUND($diff->days / 30);
                @endphp
            </td>
        </tr>
        <tr>
            <th>Reward</th>
            <td>{{ $program->nama_reward }}</td>
        </tr>
    </table>
    <br>
    <br>
    <table class="datatable3" style="width:50%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th rowspan="2">No.</th>
                <th rowspan="2">Kode Pelanggan</th>
                <th rowspan="2">Nama Pelanggan</th>
                <th rowspan="2">Cabang</th>
                <th rowspan="2">Salesman</th>
                <th rowspan="2">Start</th>
                <th rowspan="2">End</th>
                <th colspan="{{ $jmlbln }}">Bulan</th>
            </tr>
            <tr>

                @for ($bl = $start_month; $bl <= $end_month; $bl++)
                    {{-- {{ $bln }} --}}
                    @if ($bl <= 12)
                        @php
                            $bln = $bl;
                            $thn = $start_year;
                        @endphp
                    @else
                        @php
                            $bln = $bl - 12;
                            $thn = $start_year + 1;
                        @endphp
                    @endif
                    <th>{{ $bln }}</th>
                @endfor

            </tr>
        </thead>
        <tbody>
            @foreach ($peserta as $d)
                @php
                    $bulanmulai = date('m', strtotime($d->tgl_mulai));
                    $tahunmulai = date('Y', strtotime($d->tgl_mulai));

                    $bulanakhir = $bulanmulai + $lama - 1 > 12 ? $bulanmulai + $lama - 1 - 12 : $bulanmulai + $lama - 1;
                    if ($bulanakhir < 9) {
                        $bulanakhir = '0' . $bulanakhir;
                    }
                    $tahunakhir = $bulanakhir < $bulanmulai ? $tahunmulai + 1 : $tahunmulai;

                    $tanggal_start_akhir = $tahunakhir . '-' . $bulanakhir . '-01';
                    $tanggal_end_akhir = date('Y-m-t', strtotime($tanggal_start_akhir));
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $d->kode_pelanggan }}</td>
                    <td>{{ $d->nama_pelanggan }}</td>
                    <td>{{ $d->kode_cabang }}</td>
                    <td>{{ $d->nama_karyawan }}</td>
                    <td>{{ date('d-m-Y', strtotime($d->tgl_mulai)) }}</td>
                    <td>{{ date('d-m-Y', strtotime($tanggal_end_akhir)) }}</td>
                    @for ($bl = $start_month; $bl <= $end_month; $bl++)
                        {{-- {{ $bln }} --}}
                        @if ($bl <= 12)
                            @php
                                $bln = $bl;
                                $thn = $start_year;
                            @endphp
                        @else
                            @php
                                $bln = $bl - 12;
                                $thn = $start_year + 1;
                            @endphp
                        @endif

                        @if ($d->{"jml_$bln$thn"} >= $program->jml_target)
                            @php
                                $bgcolor = 'green';
                            @endphp
                        @else
                            @php
                                $bgcolor = '';
                            @endphp
                        @endif

                        @php

                            $tgl_mulai_perhitungan = $thn . '-' . $bln . '-01';

                        @endphp
                        @if ($tgl_mulai_perhitungan >= $d->tgl_mulai)
                            <td align="center"
                                style="background-color: {{ $bgcolor }}; color:{{ !empty($bgcolor) ? 'white' : '' }} ">
                                {{-- {{ $bl }} {{ $thn }} --}}
                                {{-- {{ 'jml_' . $bln . $thn }} --}}
                                {{-- {{ $tgl_mulai_perhitungan }} {{ $test }} --}}

                                {{ !empty($d->{"jml_$bln$thn"}) ? $d->{"jml_$bln$thn"} : '' }}

                            </td>
                        @else
                            <td></td>
                        @endif
                    @endfor
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
