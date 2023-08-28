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
                <th rowspan="2">NO</th>
                <th rowspan="2">NIK</th>
                <th rowspan="2">NAMA KARYAWAN</th>
                <th rowspan="2">SALDOAWAL</th>
                <th colspan="3">PENAMBAHAN</th>
                <th colspan="5">PEMBAYARAN</th>
                <th rowspan="2">SALDO AKHIR</th>
            </tr>
            <tr>
                <th>PJP</th>
                <th>KASBON</th>
                <th>PIUTANG</th>
                <th>POT. UPAH</th>
                <th>CASH</th>
                <th>POT. KOMISI</th>
                <th>TITIPAN PELANGGAN</th>
                <th>LAINNYA</th>
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
            $piutang_totallainnya = 0;
            $piutang_totalplnow = 0;


            $total_all_saldoawal = 0;
            $total_all_upah = 0;
            $total_all_cash = 0;
            $total_all_saldoakhir = 0;

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
            $piutang_jumlah_pembayaranlainnya = $d->piutang_total_pembayaranlainnya;
            $piutang_jumlah_pelunasannow = $d->piutang_total_pelunasannow;

            $piutang_saldoawal = $piutang_jumlah_pinjamanlast - $piutang_jumlah_pembayaranlast - $piutang_jumlah_pelunasanlast ;

            $piutang_totalpembayarannow = $piutang_jumlah_pembayarannow + $piutang_jumlah_pelunasannow ;
            $piutang_totalpmbnow += $piutang_jumlah_pembayarannow;
            $piutang_totalplnow += $piutang_jumlah_pelunasannow;

            $piutang_totalpotongkomisi += $piutang_jumlah_pembayaranpotongkomisi;
            $piutang_totaltitipan += $piutang_jumlah_pembayarantitipan;
            $piutang_totallainnya += $piutang_jumlah_pembayaranlainnya;

            $piutang_saldoakhir = $piutang_saldoawal + $piutang_jumlah_pinjamannow - $piutang_totalpembayarannow ;

            $piutang_totalsaldoawal += $piutang_saldoawal;
            $piutang_totalsaldoakhir += $piutang_saldoakhir;
            $piutang_totalpembayaran += $piutang_totalpembayarannow;
            $piutang_totalpenambahan += $piutang_jumlah_pinjamannow;

            $all_saldoawal = $pinjaman_saldoawal + $kasbon_saldoawal + $piutang_saldoawal;
            $upah_all = $pinjaman_jumlah_pembayarannow + $kasbon_jumlah_pembayarannow + $d->piutang_total_pembayarannow;
            $cash_all = $pinjaman_jumlah_pelunasannow + $kasbon_jumlah_pelunasannow;
            $all_saldoakhir = $all_saldoawal + $pinjaman_jumlah_pinjamannow + $kasbon_jumlah_kasbonnow + $piutang_jumlah_pinjamannow - $upah_all - $cash_all - $piutang_jumlah_pembayaranpotongkomisi - $piutang_jumlah_pembayarantitipan - $piutang_jumlah_pembayaranlainnya;

            $total_all_saldoawal += $all_saldoawal;
            $total_all_upah += $upah_all;
            $total_all_cash += $cash_all;
            $total_all_saldoakhir += $all_saldoakhir;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->nik }}</td>
                <td>{{ $d->nama_karyawan }}</td>
                <td align="right">
                    {{ !empty($all_saldoawal) ? rupiah($all_saldoawal) : "" }}
                </td>
                <td style="text-align: right">{{ !empty($pinjaman_jumlah_pinjamannow) ?  rupiah($pinjaman_jumlah_pinjamannow) : '' }}</td>
                <td style="text-align: right">{{ !empty($kasbon_jumlah_kasbonnow) ?  rupiah($kasbon_jumlah_kasbonnow) : '' }}</td>
                <td style="text-align: right">{{ !empty($piutang_jumlah_pinjamannow) ?  rupiah($piutang_jumlah_pinjamannow) : '' }}</td>
                <td style="text-align: right">{{ !empty($upah_all) ?  rupiah($upah_all) : '' }}</td>
                <td style="text-align: right">{{ !empty($cash_all) ?  rupiah($cash_all) : '' }}</td>
                <td style="text-align: right">{{ !empty($piutang_jumlah_pembayaranpotongkomisi) ?  rupiah($piutang_jumlah_pembayaranpotongkomisi) : '' }}</td>
                <td style="text-align: right">{{ !empty($piutang_jumlah_pembayarantitipan) ?  rupiah($piutang_jumlah_pembayarantitipan) : '' }}</td>
                <td style="text-align: right">{{ !empty($piutang_jumlah_pembayaranlainnya) ?  rupiah($piutang_jumlah_pembayaranlainnya) : '' }}</td>
                <td style="text-align: right">{{ !empty($all_saldoakhir) ?  rupiah($all_saldoakhir) : '' }}</td>
            </tr>
            @endforeach
            <tr bgcolor=" #024a75" style=" color:white; font-size:12;">
                <th colspan="3">TOTAL</th>
                <th style="text-align: right">{{ rupiah($total_all_saldoawal) }}</th>
                <th style="text-align: right">{{ rupiah($pinjaman_totalpenambahan) }}</th>
                <th style="text-align: right">{{ rupiah($kasbon_totalpenambahan) }}</th>
                <th style="text-align: right">{{ rupiah($piutang_totalpenambahan) }}</th>
                <th style="text-align: right">{{ rupiah($total_all_upah) }}</th>
                <th style="text-align: right">{{ rupiah($total_all_cash) }}</th>
                <th style="text-align: right">{{ rupiah($piutang_totalpotongkomisi) }}</th>
                <th style="text-align: right">{{ rupiah($piutang_totaltitipan) }}</th>
                <th style="text-align: right">{{ rupiah($piutang_totallainnya) }}</th>
                <th style="text-align: right">{{ rupiah($total_all_saldoakhir) }}</th>
            </tr>
        </tbody>


</body>
