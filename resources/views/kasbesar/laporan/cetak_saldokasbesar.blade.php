<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Saldo Kas Besar {{ $cabang->nama_cabang }} {{ date("d-m-y") }}</title>
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
            border: 1px solid #4d4d4d;
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
        SALDO KAS BESAR
        <br>
        @if ($cabang->kode_cabang=="PST")
        PACIFIC PUSAT
        @else
        PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
        @endif
        <br>
        PERIODE BULAN {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }}
    </b>
    <br>
    <table class="datatable3" border="1" style="width:160%">
        <thead style=" background-color:#31869b; color:white; font-size:12;">
            <tr style=" background-color:orange; color:white; font-size:12;">
                <th colspan="5">PENERIMAAN LHP</th>
                <th>TOTAL</th>
                <th colspan="4">SETORAN KE BANK</th>
                <th>TOTAL</th>
                <th>SALDO</th>
                <th style="border:none; background-color:white; width:100px"></th>
                <th colspan="8">RINCIAN UANG PADA KAS BESAR</th>
            </tr>
            <tr style=" background-color:#31869b; color:white; font-size:12;">
                <th>TGL</th>
                <th>UANG KERTAS</th>
                <th>LOGAM</th>
                <th>GIRO</th>
                <th>TRANSFER</th>
                <th>PENERIMAAN</th>
                <th>UANG KERTAS</th>
                <th>LOGAM</th>
                <th>GIRO</th>
                <th>TRANSFER</th>
                <th>SETORAN KE BANK</th>
                <th>KAS BESAR</th>
                <th style="border:none; background-color:white; width:100px"></th>
                <th>UANG KERTAS</th>
                <th>LOGAM</th>
                <th>GIRO</th>
                <th>TRANSFER</th>
                <th>TOTAL UANG FISIK</th>
                <th style="width:8%">PENUKARAN LOGAM JADI KERTAS</th>
                <th style="width:8%">PENUKARAN GIRO JADI KERTAS</th>
                <th style="width:8%">PENUKARAN GIRO JADI TRANSFER</th>
            </tr>
            <tr style=" background-color:white; color:black; font-size:12;">
                <th colspan="11">SALDO AWAL</th>
                <th>
                    @php
                    if($saldokasbesar != null){
                    $saldoawal_kasbesar = $saldokasbesar->uang_kertas + $saldokasbesar->uang_logam + $saldokasbesar->giro + $saldokasbesar->transfer;
                    }else{
                    $saldoawal_kasbesar = 0;
                    }
                    @endphp
                    {{ rupiah($saldoawal_kasbesar) }}
                </th>
                <th style="border:none; background-color:white;"></th>
                <th style=" text-align:right; background-color:orange; color:white; font-size:12;">{{ !empty($saldokasbesar->uang_kertas) ? rupiah($saldokasbesar->uang_kertas) : '' }}</th>
                <th style=" text-align:right; background-color:orange; color:white; font-size:12;">{{ !empty($saldokasbesar->uang_logam) ? rupiah($saldokasbesar->uang_logam) : '' }}</th>
                <th style=" text-align:right; background-color:orange; color:white; font-size:12;">{{ !empty($saldokasbesar->giro) ? rupiah($saldokasbesar->giro) : '' }}</th>
                <th style=" text-align:right; background-color:orange; color:white; font-size:12;">{{ !empty($saldokasbesar->transfer) ? rupiah($saldokasbesar->transfer) : '' }}</th>


                <th style=" text-align:right; background-color:orange; color:white; font-size:12;"></th>
                <th style=" text-align:right; background-color:orange; color:white; font-size:12;"></th>
                <th style=" text-align:right; background-color:orange; color:white; font-size:12;"></th>
                <th style=" text-align:right; background-color:orange; color:white; font-size:12;"></th>
            </tr>
        </thead>
        <tbody>
            @php
            $saldo = $saldoawal_kasbesar;
            $totalterimakertas = 0;
            $totalterimalogam = 0;
            $totalterimagiro = 0;
            $totalterimatransfer = 0;
            $totalkeluarkertas = 0;
            $totalkeluarlogam = 0;
            $totalkeluargiro = 0;
            $totalkeluartransfer = 0;
            $totalallpenerimaan = 0;
            $totalallpengeluaran = 0;
            $totalrinciankertas = $saldokasbesar->uang_kertas;
            $totalrincianlogam = $saldokasbesar->uang_logam;
            $totalrinciangiro = $saldokasbesar->giro;
            $totalrinciantrf = $saldokasbesar->transfer;
            @endphp
            <?php
            while (strtotime($dari) <= strtotime($sampai)) {
                //Penerimaan
                $penerimaan = DB::table('setoran_penjualan')
                ->selectRaw('SUM(setoran_logam) as lhplogam,
                SUM(setoran_kertas) as lhpkertas,
                SUM(setoran_bg) as lhpgiro,
                SUM(setoran_transfer) as lhptransfer,
                SUM(girotocash) as lhpgirotocash,
                SUM(girototransfer) as lhpgirototransfer')
                ->where('tgl_lhp',$dari)
                ->where('tgl_lhp','<=',$tgl_akhirsetoran)
                ->where('kode_cabang',$kode_cabang)
                ->groupBy('tgl_lhp')
                ->first();

                $kuranglebihsetor = DB::table('kuranglebihsetor')
                ->selectRaw("SUM(IF(pembayaran=1,uang_logam,0)) as kurang_logam,
                SUM(IF(pembayaran=1,uang_kertas,0)) as kurang_kertas,
                SUM(IF(pembayaran=2,uang_logam,0)) as lebih_logam,
                SUM(IF(pembayaran=2,uang_kertas,0)) as lebih_kertas")
                ->where('tgl_kl',$dari)
                ->where('kode_cabang',$kode_cabang)
                ->groupBy('tgl_kl')
                ->first();

                if($penerimaan != null){
                    $setoran_kertas = $penerimaan->lhpkertas;
                    $setoran_logam = $penerimaan->lhplogam;
                    $setoran_giro = $penerimaan->lhpgiro;
                    $setoran_transfer = $penerimaan->lhptransfer;
                }else{
                    $setoran_kertas = 0;
                    $setoran_logam = 0;
                    $setoran_giro = 0;
                    $setoran_transfer=0;
                }

                if($kuranglebihsetor != null){
                    $kurang_kertas = $kuranglebihsetor->kurang_kertas;
                    $kurang_logam = $kuranglebihsetor->kurang_logam;
                    $lebih_kertas = $kuranglebihsetor->lebih_kertas;
                    $lebih_logam = $kuranglebihsetor->lebih_logam;
                }else{
                    $kurang_kertas = 0;
                    $kurang_logam = 0;
                    $lebih_kertas = 0;
                    $lebih_logam = 0;
                }

                $lhpkertas = $setoran_kertas + $kurang_kertas - $lebih_kertas;
                $lhplogam = $setoran_logam + $kurang_logam - $lebih_logam;
                $lhpgiro = $setoran_giro;
                $lhptransfer = $setoran_transfer;
                $totallhp = $lhpkertas + $lhplogam + $lhpgiro + $lhptransfer;
            ?>
            <tr>
                <td bgcolor="#ba0e0e" style="color:white"><?php echo DateToIndo2($dari); ?></td>
                <td style="text-align:right; color:green; font-weight:bold">{{ !empty($lhpkertas) ? rupiah($lhpkertas) : '' }}</td>
                <td style="text-align:right; color:green; font-weight:bold">{{ !empty($lhplogam) ? rupiah($lhplogam) : '' }}</td>
                <td style="text-align:right; color:green; font-weight:bold">{{ !empty($lhpgiro) ? rupiah($lhpgiro) : '' }}</td>
                <td style="text-align:right; color:green; font-weight:bold">{{ !empty($lhptransfer) ? rupiah($lhptransfer) : '' }}</td>
                <td style="text-align:right; color:green; font-weight:bold">{{ !empty($totallhp) ? rupiah($totallhp) : '' }}</td>
            </tr>
            <?php
             $dari = date("Y-m-d", strtotime("+1 day", strtotime($dari))); //looping tambah 1 date
            }
            ?>
        </tbody>
    </table>
</body>
</html>
