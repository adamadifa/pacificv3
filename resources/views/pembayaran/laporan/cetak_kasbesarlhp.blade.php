<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kas Besar LHP</title>
    <style>
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
        LAPORAN KAS BESAR<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        @if ($salesman != null)
        SALESMAN {{ strtoupper($salesman->nama_karyawan) }}
        @else
        SEMUA SALESMAN
        @endif
        <br />
        @if ($pelanggan != null)
        PELANGGAN {{ strtoupper($pelanggan->nama_pelanggan) }}
        @else
        SEMUA PELANGGAN
        @endif
    </b>

    <table class="datatable3">

        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th rowspan="2">Tanggal</th>
                <th rowspan="2">No Faktur</th>
                <th rowspan="2">Kode Pel.</th>
                <th rowspan="2">Nama Pelanggan</th>
                <th rowspan="2">TUNAI</th>
                <th rowspan="2">TAGIHAN</th>
                <th rowspan="2">GANTI GIRO KE CASH</th>

            </tr>
        </thead>
        <tbody>
            @php
            $totaltagihan = 0;
            $totaltunai = 0;
            $totalgirotocash = 0;
            @endphp
            @foreach ($kasbesar as $d)
            @php
            if($d->jenistransaksi=="tunai"){
            $tunai = $d->bayar;
            $tagihan = 0;
            $girotocash =0;
            }else{
            if($d->girotocash==1){
            $tunai = 0;
            $tagihan = 0;
            $girotocash = $d->bayar;
            }else{
            $tunai = 0;
            $tagihan = $d->bayar;
            $girotocash = 0;
            }
            }


            $totaltagihan += $tagihan;
            $totaltunai += $tunai;
            $totalgirotocash += $girotocash;
            @endphp
            <tr>
                <td>{{ date("d-m-Y",strtotime($d->tglbayar)) }}</td>
                <td>{{ $d->no_fak_penj }}</td>
                <td>{{ $d->kode_pelanggan }}</td>
                <td>{{ $d->nama_pelanggan }}</td>
                <td style="text-align: right">{{ !empty($tunai) ? rupiah($tunai) : '' }}</td>
                <td style="text-align: right">{{ !empty($tagihan) ? rupiah($tagihan) : '' }}</td>
                <td style="text-align: right">{{ !empty($girotocash) ? rupiah($girotocash) : '' }}</td>
            </tr>
            @endforeach
            <tr>
                <th colspan="4">TOTAL</th>
                <th style="text-align: right">{{ rupiah($totaltunai) }}</th>
                <th style="text-align: right">{{ rupiah($totaltagihan) }}</th>
                <th style="text-align: right">{{ rupiah($totalgirotocash) }}</th>

            </tr>
        </tbody>
        <tbody>
        </tbody>
    </table>
    <br>
    <h4>LIST GIRO <br> TANGGAL <?php echo DateToIndo2($dari); ?> s/d <?php echo $sampai; ?><br></h4>
    <table class="datatable3">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>No Giro</th>
                <th style="padding:10px !important">Tgl Giro</th>
                <th>No Faktur</th>
                <th>Kode Pel.</th>
                <th>Nama Pelanggan</th>
                <th>Nama Bank</th>
                <th>Jumlah</th>
                <th>Jatuh tempo</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php
            $totalgiro = 0;
            @endphp
            @foreach ($listgiro as $d)
            @php
            $totalgiro += $d->jumlah;
            if ($d->status == 0) {
            $status = "Pending";
            $color = "yellow";
            $textcolor = "black";
            } else if ($d->status == 1) {
            $status = "Diterima";
            $color = "Green";
            $textcolor = "white";
            } else if ($d->status == 2) {
            $status = "Ditolak";
            $color = "red";
            $textcolor = "white";
            }
            @endphp
            <tr style="background-color:{{ $color }}; color:{{ $textcolor }}">
                <td>{{ $d->no_giro }}</td>
                <td>{{ date("d-m-Y",strtotime($d->tgl_giro)) }}</td>
                <td>{{ $d->no_fak_penj }}</td>
                <td>{{ $d->kode_pelanggan }}</td>
                <td>{{ $d->nama_pelanggan }}</td>
                <td>{{ $d->namabank }}</td>
                <td style="text-align: right">{{ rupiah($d->jumlah) }}</td>
                <td style="text-align: center">{{ date("d-m-Y",strtotime($d->tglcair)) }}</td>
                <td>{{ $status }}</td>
            </tr>
            @endforeach
            <tr>
                <th colspan="6">TOTAL</th>
                <th style="text-align: right">{{ rupiah($totalgiro) }}</th>
                <th></th>
                <th></th>
            </tr>
        </tbody>
    </table>
    <br>
    <h4>LIST TRANSFER <br> TANGGAL <?php echo DateToIndo2($dari); ?> s/d <?php echo $sampai; ?><br></h4>
    <table class="datatable3">

        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>Kode Transfer</th>
                <th style="padding:10px !important">Tanggal</th>
                <th>No Faktur</th>
                <th>Kode Pel.</th>
                <th>Nama Pelanggan</th>
                <th>Nama Bank</th>
                <th>Jatuh tempo</th>
                <th>Jumlah</th>
                <th>Ganti Giro Ke Transfer</th>
                <th>Status</th>
                <th>Ket</th>
            </tr>
        </thead>
        <tbody>
            @php
            $totaltransfer = 0;
            $totalgirototransfer = 0;
            @endphp
            @foreach ($listtransfer as $d)
            @php

            if ($d->status == 0) {
            $status = "Pending";
            $color = "yellow";
            $textcolor = "black";
            } else if ($d->status == 1) {
            $status = "Diterima";
            $color = "Green";
            $textcolor = "white";
            } else if ($d->status == 2) {
            $status = "Ditolak";
            $color = "red";
            $textcolor = "white";
            }

            if($d->girotocash==1){
            $girototransfer = $d->jumlah;
            $transfer = 0;
            }else{
            $transfer= $d->jumlah;
            $girototransfer = 0;
            }

            $totaltransfer += $transfer;
            $totalgirototransfer += $girototransfer;
            @endphp
            <tr style="background-color:{{ $color }}; color:{{ $textcolor }}">
                <td>{{ $d->kode_transfer }}</td>
                <td>{{ date("d-m-Y",strtotime($d->tgl_transfer)) }}</td>
                <td>{{ $d->no_fak_penj }}</td>
                <td>{{ $d->kode_pelanggan }}</td>
                <td>{{ $d->nama_pelanggan }}</td>
                <td>{{ $d->namabank }}</td>
                <td>{{ date("d-m-Y",strtotime($d->tglcair)) }}</td>
                <td style="text-align: right">{{ rupiah($transfer) }}</td>
                <td style="text-align: right">{{ rupiah($girototransfer) }}</td>
                <td>{{ $status }}</td>
                <td></td>

            </tr>
            @endforeach
            <tr>
                <th colspan="7">TOTAL</th>
                <th style="text-align: right">{{ rupiah($totaltransfer) }}</th>
                <th style="text-align: right">{{ rupiah($totalgirototransfer) }}</th>
                <th></th>
                <th></th>

            </tr>
        </tbody>
    </table>
    <h4>SUMMARY <br> TANGGAL <?php echo DateToIndo2($dari); ?> s/d <?php echo $sampai; ?><br></h4>
    <?php
                    $totalsummary = $totaltunai + $totaltagihan + $totalgiro + $totaltransfer - $totalgirotocash - $totalgirototransfer;
                    ?>
    <table class="datatable3">

        <tr>
            <th bgcolor="#024a75" style="color:white; font-size:12; padding:5px !important">Penjualan Tunai</th>
            <td style="text-align: right; font-size:12px; font-weight:bold"><?php echo number_format($totaltunai, '0', '', '.');  ?></td>
        </tr>
        <tr>
            <th bgcolor="#024a75" style="color:white; font-size:12; padding:5px !important">Tagihan</th>
            <td style="text-align: right; font-size:12px; font-weight:bold"><?php echo number_format($totaltagihan, '0', '', '.');  ?></td>
        </tr>
        <tr>
            <th bgcolor="#024a75" style="color:white; font-size:12; padding:5px !important">Giro</th>
            <td style="text-align: right; font-size:12px; font-weight:bold"><?php echo number_format($totalgiro, '0', '', '.');  ?></td>
        </tr>
        <tr>
            <th bgcolor="#024a75" style="color:white; font-size:12; padding:5px !important">Transfer</th>
            <td style="text-align: right; font-size:12px; font-weight:bold"><?php echo number_format($totaltransfer, '0', '', '.');  ?></td>
        </tr>
        <tr>
            <th bgcolor="#024a75" style="color:white; font-size:12; padding:5px !important">Ganti Giro Ke Cash</th>
            <td style=" background-color:red; color:white; text-align: right; font-size:12px; font-weight:bold"><?php echo number_format($totalgirotocash, '0', '', '.');  ?></td>
        </tr>
        <tr>
            <th bgcolor="#024a75" style="color:white; font-size:12; padding:5px !important">Ganti Giro Ke Transfer</th>
            <td style=" background-color:red; color:white; text-align: right; font-size:12px; font-weight:bold"><?php echo number_format($totalgirototransfer, '0', '', '.');  ?></td>
        </tr>
        <tr>
            <th bgcolor="#024a75" style="color:white; font-size:12; padding:5px !important">TOTAL</th>
            <td style="background-color:green; color:white; text-align: right; font-size:12px; font-weight:bold"><?php echo number_format($totalsummary, '0', '', '.');  ?></td>
        </tr>

    </table>
</body>
</html>
