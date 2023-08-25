<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Pinjaman {{ date("d-m-y") }}</title>
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

        .text-right: {
            text-align: right !important;
        }

    </style>
</head>
<body>
    <b style="font-size:14px;">
        @if ($kantor != null)
        @if ($kantor->kode_cabang=="PST")
        PACIFIC PUSAT
        @else
        PACIFIC CABANG {{ strtoupper($kantor->nama_cabang) }}
        @endif
        <br>
        @endif

        LAPORAN PINJAMAN<br>
        BULAN {{ strtoupper($namabulan[$bulan]) }} TAHUN {{ $tahun }}
    </b>
    <br>
    <table class="datatable3">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th rowspan="4">NO</th>
                <th rowspan="4">NIK</th>
                <th rowspan="4">NAMA KARYAWAN</th>
                <th colspan="7">PJP</th>
                <th colspan="7">KASBON</th>
                <th colspan="8">PIUTANG KARYAWAN</th>
            </tr>
            <tr>
                <!-- PJP-->
                <th rowspan="3">SALDO AWAL</th>
                <th colspan="2">PENAMBAHAN</th>
                <th colspan="3">PEMBAYARAN</th>
                <th rowspan="3">SALDO AKHIR</th>

                <!--- KASBON-->

                <th rowspan="3">SALDO AWAL</th>
                <th colspan="2">PENAMBAHAN</th>
                <th colspan="3">PEMBAYARAN</th>
                <th rowspan="3">SALDO AKHIR</th>


                <!--- PIUTANG KARYAWAN-->

                <th rowspan="3">SALDO AWAL</th>
                <th colspan="2">PENAMBAHAN</th>
                <th colspan="4">PEMBAYARAN</th>
                <th rowspan="3">SALDO AKHIR</th>
            </tr>
            <tr>
                <!-- PJP-->
                <th rowspan="2">PINJAMAN</th>
                <th rowspan="2">LAIN LAIN</th>
                <th colspan="2">CICILAN</th>
                <th rowspan="2">LAIN LAIN</th>

                <!--- KASBON-->

                <th rowspan="2">KASBON</th>
                <th rowspan="2">LAIN LAIN</th>
                <th colspan="2">CICILAN</th>
                <th rowspan="2">LAIN LAIN</th>

                <!--- PIUTANG-->

                <th rowspan="2">KASBON</th>
                <th rowspan="2">LAIN LAIN</th>
                <th colspan="3">CICILAN</th>
                <th rowspan="2">LAIN LAIN</th>
            </tr>
            <tr>
                <th>GAJI</th>
                <th>CASH</th>
                <th>GAJI</th>
                <th>CASH</th>
                <th>GAJI</th>
                <th>POT. KOMISI</th>
                <th>TITIPAN</th>
            </tr>
        </thead>

        <tbody>

            @php
            //Pinjaman
            $pinjaman_totalsaldoawal = 0;
            $pinjaman_totalpenambahan = 0;
            $pinjaman_totalpembayaran = 0;
            $pinjaman_totalsaldoakhir = 0;
            $pinjaman_totalpmbnow = 0;
            $pinjaman_totalplnow = 0;

            //Kasbon
            $kasbon_totalsaldoawal = 0;
            $kasbon_totalpenambahan = 0;
            $kasbon_totalpembayaran = 0;
            $kasbon_totalsaldoakhir = 0;
            $kasbon_totalpmbnow = 0;
            $kasbon_totalplnow = 0;

            $piutang_totalsaldoawal = 0;
            $piutang_totalpenambahan = 0;
            $piutang_totalpembayaran = 0;
            $piutang_totalsaldoakhir = 0;
            $piutang_totalpmbnow = 0;
            $piutang_totalpotongkomisi = 0;
            $piutang_totaltitipan = 0;
            $piutang_totalplnow = 0;
            @endphp
            @foreach ($piutangkaryawan as $d)

            @php
            $pinjaman_jumlah_pinjamanlast = $d->pinjaman_jumlah_pinjamanlast;
            $pinjaman_jumlah_pelunasanlast = $d->pinjaman_total_pelunasanlast;
            $pinjaman_jumlah_pembayaranlast = $d->pinjaman_total_pembayaranlast;

            $pinjaman_jumlah_pinjamannow = $d->pinjaman_jumlah_pinjamannow;
            $pinjaman_jumlah_pembayarannow = $d->pinjaman_total_pembayarannow;
            $pinjaman_jumlah_pelunasannow = $d->pinjaman_total_pelunasannow;

            $pinjaman_saldoawal = $pinjaman_jumlah_pinjamanlast - $pinjaman_jumlah_pembayaranlast - $pinjaman_jumlah_pelunasanlast ;

            $pinjaman_totalpembayarannow = $pinjaman_jumlah_pembayarannow + $pinjaman_jumlah_pelunasannow;
            $pinjaman_totalpmbnow += $pinjaman_jumlah_pembayarannow;
            $pinjaman_totalplnow += $pinjaman_jumlah_pelunasannow;
            $pinjaman_saldoakhir = $pinjaman_saldoawal + $pinjaman_jumlah_pinjamannow - $pinjaman_totalpembayarannow ;


            $pinjaman_totalsaldoawal += $pinjaman_saldoawal;
            $pinjaman_totalsaldoakhir += $pinjaman_saldoakhir;
            $pinjaman_totalpembayaran += $pinjaman_totalpembayarannow;
            $pinjaman_totalpenambahan += $pinjaman_jumlah_pinjamannow;

            //KASBON

            $kasbon_jumlah_kasbonlast = $d->kasbon_jumlah_kasbonlast;
            $kasbon_jumlah_pelunasanlast = $d->kasbon_total_pelunasanlast;
            $kasbon_jumlah_pembayaranlast = $d->kasbon_total_pembayaranlast;

            $kasbon_jumlah_kasbonnow = $d->kasbon_jumlah_kasbonnow;
            $kasbon_jumlah_pembayarannow = $d->kasbon_total_pembayarannow;
            $kasbon_jumlah_pelunasannow = $d->kasbon_total_pelunasannow;

            $kasbon_saldoawal = $kasbon_jumlah_kasbonlast - $kasbon_jumlah_pembayaranlast - $kasbon_jumlah_pelunasanlast ;

            $kasbon_totalpembayarannow = $kasbon_jumlah_pembayarannow + $kasbon_jumlah_pelunasannow;
            $kasbon_totalpmbnow += $kasbon_jumlah_pembayarannow;
            $kasbon_totalplnow += $kasbon_jumlah_pelunasannow;
            $kasbon_saldoakhir = $kasbon_saldoawal + $kasbon_jumlah_kasbonnow - $kasbon_totalpembayarannow ;


            $kasbon_totalsaldoawal += $kasbon_saldoawal;
            $kasbon_totalsaldoakhir += $kasbon_saldoakhir;
            $kasbon_totalpembayaran += $kasbon_totalpembayarannow;
            $kasbon_totalpenambahan += $kasbon_jumlah_kasbonnow;


            //PIUTANG
            $piutang_jumlah_pinjamanlast = $d->piutang_jumlah_pinjamanlast;
            $piutang_jumlah_pelunasanlast = $d->piutang_total_pelunasanlast;
            $piutang_jumlah_pembayaranlast = $d->piutang_total_pembayaranlast;

            $piutang_jumlah_pinjamannow = $d->piutang_jumlah_pinjamannow;
            $piutang_jumlah_pembayarannow = $d->piutang_total_pembayarannow + $d->piutang_total_pembayaranpotongkomisi + $d->piutang_total_pembayarantitipan ;
            $piutang_jumlah_pembayaranpotongkomisi = $d->piutang_total_pembayaranpotongkomisi;
            $piutang_jumlah_pembayarantitipan = $d->piutang_total_pembayarantitipan;
            $piutang_jumlah_pelunasannow = $d->piutang_total_pelunasannow;

            $piutang_saldoawal = $piutang_jumlah_pinjamanlast - $piutang_jumlah_pembayaranlast - $piutang_jumlah_pelunasanlast ;

            $piutang_totalpembayarannow = $piutang_jumlah_pembayarannow + $piutang_jumlah_pelunasannow ;
            $piutang_totalpmbnow += $piutang_jumlah_pembayarannow;
            $piutang_totalplnow += $piutang_jumlah_pelunasannow;

            $piutang_totalpotongkomisi += $piutang_jumlah_pembayaranpotongkomisi;
            $piutang_totaltitipan += $piutang_jumlah_pembayarantitipan;

            $piutang_saldoakhir = $piutang_saldoawal + $piutang_jumlah_pinjamannow - $piutang_totalpembayarannow ;

            $piutang_totalsaldoawal += $piutang_saldoawal;
            $piutang_totalsaldoakhir += $piutang_saldoakhir;
            $piutang_totalpembayaran += $piutang_totalpembayarannow;
            $piutang_totalpenambahan += $piutang_jumlah_pinjamannow;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->nik }}</td>
                <td>{{ $d->nama_karyawan }}</td>
                <td align="right">{{ !empty($pinjaman_saldoawal) ?  rupiah($pinjaman_saldoawal) : "" }}</td>
                <td style="text-align: right">{{ !empty($pinjaman_jumlah_pinjamannow) ?  rupiah($pinjaman_jumlah_pinjamannow) : '' }}</td>
                <td></td>
                <td style="text-align: right">{{ !empty($pinjaman_jumlah_pembayarannow) ?  rupiah($pinjaman_jumlah_pembayarannow) : '' }}</td>
                <td style="text-align: right">{{ !empty($pinjaman_jumlah_pelunasannow) ?  rupiah($pinjaman_jumlah_pelunasannow) : '' }}</td>
                <td></td>
                <td style="text-align: right">{{ !empty($pinjaman_saldoakhir) ?  rupiah($pinjaman_saldoakhir) : '' }}</td>

                <!-- KASBON -->
                <td align="right">{{ !empty($kasbon_saldoawal) ?  rupiah($kasbon_saldoawal) : "" }}</td>
                <td style="text-align: right">{{ !empty($kasbon_jumlah_kasbonnow) ?  rupiah($kasbon_jumlah_kasbonnow) : '' }}</td>
                <td></td>
                <td style="text-align: right">{{ !empty($kasbon_jumlah_pembayarannow) ?  rupiah($kasbon_jumlah_pembayarannow) : '' }}</td>
                <td style="text-align: right">{{ !empty($kasbon_jumlah_pelunasannow) ?  rupiah($kasbon_jumlah_pelunasannow) : '' }}</td>
                <td></td>
                <td style="text-align: right">{{ !empty($kasbon_saldoakhir) ?  rupiah($kasbon_saldoakhir) : '' }}</td>

                <!-- PIUTANG-->
                <td style="text-align: right">{{ !empty($piutang_saldoawal) ?  rupiah($piutang_saldoawal) : '' }}</td>
                <td style="text-align: right">{{ !empty($piutang_jumlah_pinjamannow) ?  rupiah($piutang_jumlah_pinjamannow) : '' }}</td>
                <td></td>
                <td style="text-align: right">{{ !empty($d->piutang_total_pembayarannow) ?  rupiah($d->piutang_total_pembayarannow) : '' }}</td>
                <td style="text-align: right">{{ !empty($piutang_jumlah_pembayaranpotongkomisi) ?  rupiah($piutang_jumlah_pembayaranpotongkomisi) : '' }}</td>
                <td style="text-align: right">{{ !empty($piutang_jumlah_pembayarantitipan) ?  rupiah($piutang_jumlah_pembayarantitipan) : '' }}</td>
                <td></td>
                <td style="text-align: right">{{ !empty($piutang_saldoakhir) ?  rupiah($piutang_saldoakhir) : '' }}</td>
            </tr>

            @endforeach
            <tr bgcolor=" #024a75" style=" color:white; font-size:12;">
                <th colspan="3">TOTAL</th>
                <th style="text-align: right">{{ rupiah($pinjaman_totalsaldoawal) }}</th>
                <th style="text-align: right">{{ rupiah($pinjaman_totalpenambahan) }}</th>
                <th></th>
                <th style="text-align: right">{{ rupiah($pinjaman_totalpmbnow) }}</th>
                <th style="text-align: right">{{ rupiah($pinjaman_totalplnow) }}</th>
                <th></th>
                <th style="text-align: right">{{ rupiah($pinjaman_totalsaldoakhir) }}</th>

                <!-- KASBON -->

                <th style="text-align: right">{{ rupiah($kasbon_totalsaldoawal) }}</th>
                <th style="text-align: right">{{ rupiah($kasbon_totalpenambahan) }}</th>
                <th></th>
                <th style="text-align: right">{{ rupiah($kasbon_totalpmbnow) }}</th>
                <th style="text-align: right">{{ rupiah($kasbon_totalplnow) }}</th>
                <th></th>
                <th style="text-align: right">{{ rupiah($kasbon_totalsaldoakhir) }}</th>

                <!-- PIUTANG-->
                <th style="text-align: right">{{ rupiah($piutang_totalsaldoawal) }}</th>
                <th style="text-align: right">{{ rupiah($piutang_totalpenambahan) }}</th>
                <th></th>
                <th style="text-align: right">{{ rupiah($piutang_totalpmbnow) }}</th>
                <th style="text-align: right">{{ rupiah($piutang_totalpotongkomisi) }}</th>
                <th style="text-align: right">{{ rupiah($piutang_totaltitipan) }}</th>
                <th></th>
                <th style="text-align: right">{{ rupiah($piutang_totalsaldoakhir) }}</th>
            </tr>
            </tr>

        </tbody>

</body>
