<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Routing Salesman {{ date("d-m-y") }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&display=swap');

        body {
            font-family: 'Poppins'
        }

        .datatable3 {
            border: 2px solid #D6DDE6;
            border-collapse: collapse;
            font-size: 14px;
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
        @if ($cabang!=null)
        @if ($cabang->kode_cabang=="PST")
        PACIFIC PUSAT
        @else
        PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
        @endif
        @else
        PACIFC ALL CABANG
        @endif
        <br>
        LAPORAN ROUTING SALESMAN<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        @if ($salesman != null)
        SALESMAN {{ strtoupper($salesman->nama_karyawan) }}
        @else
        SEMUA SALESMAN
        @endif
        <br />
    </b>
    <br>
    <table class="datatable3">
        <thead>
            <tr bgcolor="#295ea9" style="color:white;">
                <th>No.</th>
                <th>Tanggal</th>
                <th>Kode Pelanggan</th>
                <th>Nama Pelanggan</th>
                <th>Jenis Transaksi</th>
                <th>Total Penjualan</th>
                <th>Tunai/Tagihan</th>
                <th>Jadwal Kunjungan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
            $no =1;
            @endphp
            @foreach ($penjualan as $d)
            <?php
                $day = date('D', strtotime($d->tgltransaksi));
                $dayList = array(
                    'Sun' => 'Minggu',
                    'Mon' => 'Senin',
                    'Tue' => 'Selasa',
                    'Wed' => 'Rabu',
                    'Thu' => 'Kamis',
                    'Fri' => 'Jumat',
                    'Sat' => 'Sabtu'
                );

                if($dayList[$day] != $d->hari){
                    $bgcolor = "red";
                    $color="white";
                }else{
                    $bgcolor="";
                    $color="";
                }
            ?>

            <tr style="background-color:{{ $bgcolor }}; color:{{ $color }}">
                <td>{{ $no }}</td>
                <td>{{ $dayList[$day] }}, {{ date("d-m-Y",strtotime($d->tgltransaksi)) }}</td>
                <td>{{ $d->kode_pelanggan }}</td>
                <td>{{ $d->nama_pelanggan }}</td>
                <td>{{ ucwords(strtolower($d->jenistransaksi ))}}</td>
                <td style="text-align: right">{{ rupiah($d->totalpenjualan) }}</td>
                <td style="text-align: right">{{ rupiah($d->totalbayar) }}</td>
                <td>{{ $d->hari }}</td>
                <td>{{ $dayList[$day] != $d->hari ? 'Tidak Sesuai Jadwal' : '' }}</td>
            </tr>
            @php
            $no++;
            @endphp
            @endforeach
            @foreach ($historibayar as $d)
            <?php
                $day = date('D', strtotime($d->tglbayar));
                $dayList = array(
                    'Sun' => 'Minggu',
                    'Mon' => 'Senin',
                    'Tue' => 'Selasa',
                    'Wed' => 'Rabu',
                    'Thu' => 'Kamis',
                    'Fri' => 'Jumat',
                    'Sat' => 'Sabtu'
                );

                if($dayList[$day] != $d->hari){
                    $bgcolor = "red";
                    $color="white";
                }else{
                    $bgcolor="";
                    $color="";
                }
            ?>
            <tr style="background-color:{{ $bgcolor }}; color:{{ $color }}">
                <td>{{ $no }}</td>
                <td>{{ $dayList[$day] }}, {{ date("d-m-Y",strtotime($d->tglbayar)) }}</td>
                <td>{{ $d->kode_pelanggan }}</td>
                <td>{{ $d->nama_pelanggan }}</td>
                <td>{{ ucwords(strtolower($d->jenistransaksi ))}}</td>
                <td></td>
                <td style="text-align: right">{{ rupiah($d->totalbayar) }}</td>
                <td>{{ $d->hari }}</td>
                <td>{{ $dayList[$day] != $d->hari ? 'Tidak Sesuai Jadwal' : '' }}</td>
            </tr>
            @php
            $no++;
            @endphp
            @endforeach
        </tbody>
    </table>

</body>
</html>
