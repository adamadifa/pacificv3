<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Ajuan Limit Kredit {{ date('d-m-y') }}</title>
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
    <table class="datatable3" style="width:150%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th style="width:1%" rowspan="2">No</th>
                <th rowspan="2">Tanggal</th>
                <th rowspan="2">Salesman</th>
                <th rowspan="2">Pelanggan</th>
                <th rowspan="2">Alamat</th>
                <th rowspan="2">Titik Koordinat</th>
                <th colspan="9" style="background-color: #ac7302; color:white">Kuantitatif</th>
                <th colspan="3" style="background-color: #03b73c; color:white">Ajuan Limit</th>
                <th rowspan="2">Uraian Analisa</th>
            </tr>
            <tr>
                <th style="background-color: #ac7302; color:white">Status<br>Pelanggan</th>
                <th style="background-color: #ac7302; color:white">Cara<br>Pembayaran</th>
                <th style="background-color: #ac7302; color:white">Histori<br>Pembayan</th>
                <th style="background-color: #ac7302; color:white">Terakhir<br>TOP UP</th>
                <th style="background-color: #ac7302; color:white">Lama<br>Usaha</th>
                <th style="background-color: #ac7302; color:white">TOP</th>
                <th style="background-color: #ac7302; color:white">Tempat Usaha</th>
                <th style="background-color: #ac7302; color:white">Omset Toko</th>
                <th style="background-color: #ac7302; color:white">Type Outlet</th>
                <th style="background-color: #03b73c; color:white">Limit Kredit <br> Sebelumnya</th>
                <th style="background-color: #03b73c; color:white">Pengajuan <br> Tambahan</th>
                <th style="background-color: #03b73c; color:white">Total Limit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($limitkredit as $d)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ date('d-m-Y', strtotime($d->tgl_pengajuan)) }}</td>
                    <td>{{ $d->nama_karyawan }}</td>
                    <td>{{ $d->kode_pelanggan . '-' . $d->nama_pelanggan }}</td>
                    <td>{!! $d->alamat_pelanggan !!}</td>
                    <td>{{ $d->latitude . ',' . $d->longitude }}</td>
                    <td>{{ $d->status == 1 ? 'New Outlet' : 'Existing Outlet' }}</td>
                    <td>
                        @if ($d->cara_pembayaran == 1)
                            Bank Transfer
                        @elseif($d->cara_pembayaran == 2)
                            Advance Cash
                        @elseif($d->cara_pembayaran == 3)
                            Billyet Giro / Cheque
                        @endif
                    </td>
                    <td>{{ $d->histori_transaksi }}</td>
                    <td>{{ date('d-m-Y', strtotime($d->topup_terakhir)) }}</td>
                    <td>{{ $d->lama_usaha }}</td>
                    <td>{{ $d->jatuhtempo }}</td>
                    <td>{{ $d->kepemilikan }}</td>
                    <td style="text-align: right">{{ rupiah($d->omset_toko) }}</td>
                    <td>{{ $d->status == 1 ? 'Grosir' : 'Retail' }}</td>
                    <td style="text-align: right">{{ !empty($d->last_limit) ? rupiah($d->last_limit) : '' }}</td>
                    <td style="text-align: right">
                        @php
                            $limittambahan = $d->jumlah - $d->last_limit;
                        @endphp
                        {{ !empty($limittambahan) ? rupiah($limittambahan) : '' }}
                    </td>
                    <td style="text-align: right">{{ !empty($d->jumlah) ? rupiah($d->jumlah) : '' }}</td>
                    <td style="width:10%">{{ $d->analisa }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
