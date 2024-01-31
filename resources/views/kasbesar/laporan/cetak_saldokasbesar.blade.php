<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Saldo Kas Besar {{ $cabang->nama_cabang }} {{ date('d-m-y') }}</title>
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
        @if ($cabang->kode_cabang == 'PST')
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
                <th colspan="6">PENERIMAAN LHP</th>
                <th>TOTAL</th>
                <th colspan="4">SETORAN KE BANK</th>
                <th colspan="2">TOTAL</th>
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
                <th>LAINNYA</th>
                <th>PENERIMAAN</th>
                <th>UANG KERTAS</th>
                <th>LOGAM</th>
                <th>GIRO</th>
                <th>TRANSFER</th>
                <th>SETORAN KE BANK</th>
                <th>LAINNYA</th>
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
                <th colspan="13">SALDO AWAL</th>
                <th>
                    @php
                        if ($saldokasbesar != null) {
                            $saldoawal_kasbesar = $saldokasbesar->uang_kertas + $saldokasbesar->uang_logam + $saldokasbesar->giro + $saldokasbesar->transfer;
                        } else {
                            $saldoawal_kasbesar = 0;
                        }
                    @endphp
                    {{ rupiah($saldoawal_kasbesar) }}
                </th>
                <th style="border:none; background-color:white;"></th>
                <th style=" text-align:right; background-color:orange; color:white; font-size:12;">
                    {{ !empty($saldokasbesar->uang_kertas) ? rupiah($saldokasbesar->uang_kertas) : '' }}</th>
                <th style=" text-align:right; background-color:orange; color:white; font-size:12;">
                    {{ !empty($saldokasbesar->uang_logam) ? rupiah($saldokasbesar->uang_logam) : '' }}</th>
                <th style=" text-align:right; background-color:orange; color:white; font-size:12;">
                    {{ !empty($saldokasbesar->giro) ? rupiah($saldokasbesar->giro) : '' }}</th>
                <th style=" text-align:right; background-color:orange; color:white; font-size:12;">
                    {{ !empty($saldokasbesar->transfer) ? rupiah($saldokasbesar->transfer) : '' }}</th>


                <th style=" text-align:right; background-color:orange; color:white; font-size:12;"></th>
                <th style=" text-align:right; background-color:orange; color:white; font-size:12;"></th>
                <th style=" text-align:right; background-color:orange; color:white; font-size:12;"></th>
                <th style=" text-align:right; background-color:orange; color:white; font-size:12;"></th>
            </tr>
        </thead>
        <tbody>
            @php
                $saldo = $saldoawal_kasbesar;
                $totallhpkertas = 0;
                $totallhplogam = 0;
                $totallhpgiro = 0;
                $totallhptransfer = 0;
                $totallhplainnya = 0;
                $totalsetorankertas = 0;
                $totalsetoranlogam = 0;
                $totalsetorangiro = 0;
                $totalsetorantransfer = 0;
                $totalsetoranlainnya = 0;
                $grandtotallhp = 0;
                $grandtotalsetoranbank = 0;
                $grandtotalsetoranbanklainnya = 0;
                $totalrinciankertas = $saldokasbesar != null ? $saldokasbesar->uang_kertas : 0;
                $totalrincianlogam = $saldokasbesar != null ? $saldokasbesar->uang_logam : 0;
                $totalrinciangiro = $saldokasbesar != null ? $saldokasbesar->giro : 0;
                $totalrinciantransfer = $saldokasbesar != null ? $saldokasbesar->transfer : 0;
            @endphp
            <?php
            while (strtotime($dari) <= strtotime($sampai)) {
                //Penerimaan
                $penerimaan = DB::table('setoran_penjualan')
                ->selectRaw('SUM(setoran_logam) as lhplogam,
                SUM(setoran_kertas) as lhpkertas,
                SUM(setoran_bg) as lhpgiro,
                SUM(setoran_transfer) as lhptransfer,
                SUM(setoran_lainnya) as lhplainnya,
                SUM(girotocash) as lhpgirotocash,
                SUM(girototransfer) as lhpgirototransfer')
                ->where('tgl_lhp',$dari)
                ->where('tgl_lhp','<=',$tgl_akhirsetoran)
                ->where('tgl_lhp','>',$daripenerimaan)
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

                $pengeluaran = DB::table('setoran_pusat')
                ->selectRaw("SUM(uang_logam) as setoranlogam,
                SUM(IF(bank = 'LAINNYA',uang_kertas,0)) as setoranlainnya,
                SUM(IF(bank != 'LAINNYA',uang_kertas,0)) as setorankertas,
                SUM(giro) as setorangiro,
                SUM(transfer) as setorantransfer")
                ->where('tgl_setoranpusat',$dari)
                ->where('kode_cabang',$kode_cabang)
                ->where('status',1)
                ->where('omset_bulan',$bulan)
                ->where('omset_tahun',$tahun)
                ->groupBy('tgl_setoranpusat')
                ->first();

                $logamtokertas = DB::table('logamtokertas')
                ->selectRaw("SUM(jumlah_logamtokertas) as jmlgantikertas")
                ->where('tgl_logamtokertas',$dari)
                ->where('tgl_logamtokertas','<=',$tgl_akhirsetoran)
                ->where('kode_cabang',$kode_cabang)
                ->whereRaw('MONTH(tgl_logamtokertas)="'.$bulan.'"')
                ->whereRaw('YEAR(tgl_logamtokertas)="'.$tahun.'"')
                ->groupBy('tgl_logamtokertas')
                ->first();

                if($penerimaan != null){
                    $setoran_kertas = $penerimaan->lhpkertas;
                    $setoran_logam = $penerimaan->lhplogam;
                    $setoran_giro = $penerimaan->lhpgiro;
                    $setoran_transfer = $penerimaan->lhptransfer;
                    $setoran_lainnya = $penerimaan->lhplainnya;
                    $girotocash = $penerimaan->lhpgirotocash;
                    $girototransfer = $penerimaan->lhpgirototransfer;
                }else{
                    $setoran_kertas = 0;
                    $setoran_logam = 0;
                    $setoran_giro = 0;
                    $setoran_transfer=0;
                    $setoran_lainnya=0;
                    $girotocash=0;
                    $girototransfer = 0;
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

                if($pengeluaran != null){
                    $setoranbank_kertas = $pengeluaran->setorankertas;
                    $setoranbank_lainnya = $pengeluaran->setoranlainnya;
                    $setoranbank_logam = $pengeluaran->setoranlogam;
                    $setoranbank_transfer = $pengeluaran->setorantransfer;
                    $setoranbank_giro = $pengeluaran->setorangiro;
                }else{
                    $setoranbank_kertas = 0;
                    $setoranbank_lainnya = 0;
                    $setoranbank_logam = 0;
                    $setoranbank_transfer = 0;
                    $setoranbank_giro = 0;
                }

                if($logamtokertas != null){
                    $gantilogamtokertas = $logamtokertas->jmlgantikertas;
                }else{
                    $gantilogamtokertas = 0;
                }
                $lhpkertas = $setoran_kertas + $kurang_kertas - $lebih_kertas;
                $lhplogam = $setoran_logam + $kurang_logam - $lebih_logam;
                $lhpgiro = $setoran_giro;
                $lhptransfer = $setoran_transfer;
                $lhplainnya = $setoran_lainnya;
                $totallhp = $lhpkertas + $lhplogam + $lhpgiro + $lhptransfer + $lhplainnya;
                $totalsetoranbank = $setoranbank_kertas + $setoranbank_logam + $setoranbank_transfer + $setoranbank_giro ;
                $totalsetoranbanklainnya =  $setoranbank_lainnya;
                $mutasi = $totallhp - $totalsetoranbank - $totalsetoranbanklainnya;
                $saldo += $mutasi;

                $totallhpkertas += $lhpkertas;
                $totallhplogam += $lhplogam;
                $totallhpgiro += $lhpgiro;
                $totallhptransfer += $lhptransfer;
                $totallhplainnya += $lhplainnya ;
                $grandtotallhp += $totallhp;

                $totalsetorankertas += $setoranbank_kertas;
                $totalsetoranlainnya += $setoranbank_lainnya;
                $totalsetoranlogam += $setoranbank_logam;
                $totalsetorantransfer += $setoranbank_transfer;
                $totalsetorangiro += $setoranbank_giro;
                $grandtotalsetoranbank += $totalsetoranbank;
                $grandtotalsetoranbanklainnya += $totalsetoranbanklainnya;

                $rinciankertas = ($lhpkertas - $setoranbank_kertas - $setoranbank_lainnya) + $gantilogamtokertas + $girotocash;
                $totalrinciankertas = $totalrinciankertas + $rinciankertas;

                $rincianlogam = ($lhplogam - $setoranbank_logam) - $gantilogamtokertas;
                $totalrincianlogam += $rincianlogam;

                $rinciangiro = ($lhpgiro - $setoranbank_giro) - $girotocash - $girototransfer;
                $totalrinciangiro += $rinciangiro;

                $rinciantransfer = $lhptransfer - $setoranbank_transfer + $girototransfer;
                $totalrinciantransfer  += $rinciantransfer;

                $totalrincianfisik = $totalrinciankertas + $totalrincianlogam + $totalrinciangiro + $totalrinciantransfer;
            ?>
            <tr>
                <td bgcolor="#ba0e0e" style="color:white"><?php echo DateToIndo2($dari); ?></td>
                <td style="text-align:right; color:green; font-weight:bold">
                    {{ !empty($lhpkertas) ? rupiah($lhpkertas) : '' }}</td>
                <td style="text-align:right; color:green; font-weight:bold">
                    {{ !empty($lhplogam) ? rupiah($lhplogam) : '' }}</td>
                <td style="text-align:right; color:green; font-weight:bold">
                    {{ !empty($lhpgiro) ? rupiah($lhpgiro) : '' }}</td>
                <td style="text-align:right; color:green; font-weight:bold">
                    {{ !empty($lhptransfer) ? rupiah($lhptransfer) : '' }}</td>
                <td style="text-align:right; color:green; font-weight:bold">
                    {{ !empty($lhplainnya) ? rupiah($lhplainnya) : '' }}</td>
                <td style="text-align:right; color:green; font-weight:bold">
                    {{ !empty($totallhp) ? rupiah($totallhp) : '' }}</td>
                <td style="text-align:right; color:red; font-weight:bold">
                    {{ !empty($setoranbank_kertas) ? rupiah($setoranbank_kertas) : '' }}</td>
                <td style="text-align:right; color:red; font-weight:bold">
                    {{ !empty($setoranbank_logam) ? rupiah($setoranbank_logam) : '' }}</td>
                <td style="text-align:right; color:red; font-weight:bold">
                    {{ !empty($setoranbank_giro) ? rupiah($setoranbank_giro) : '' }}</td>
                <td style="text-align:right; color:red; font-weight:bold">
                    {{ !empty($setoranbank_transfer) ? rupiah($setoranbank_transfer) : '' }}</td>
                <td style="text-align:right; color:red; font-weight:bold">
                    {{ !empty($totalsetoranbank) ? rupiah($totalsetoranbank) : '' }}</td>
                <td style="text-align:right; color:red; font-weight:bold">
                    {{ !empty($totalsetoranlainnya) ? rupiah($totalsetoranlainnya) : '' }}</td>
                <td style="text-align:right; color:rgb(3, 19, 241); font-weight:bold">
                    {{ !empty($saldo) ? rupiah($saldo) : '' }}</td>
                <td style="border:none; background-color:white;"></td>
                <td style="text-align:right; color:rgb(12, 9, 9); font-weight:bold">
                    {{ !empty($totalrinciankertas) ? rupiah($totalrinciankertas) : '' }}</td>
                <td style="text-align:right; color:rgb(12, 9, 9); font-weight:bold">
                    {{ !empty($totalrincianlogam) ? rupiah($totalrincianlogam) : '' }}</td>
                <td style="text-align:right; color:rgb(12, 9, 9); font-weight:bold">
                    {{ !empty($totalrinciangiro) ? rupiah($totalrinciangiro) : '' }}</td>
                <td style="text-align:right; color:rgb(12, 9, 9); font-weight:bold">
                    {{ !empty($totalrinciantransfer) ? rupiah($totalrinciantransfer) : '' }}</td>
                <td style="text-align:right; color:rgb(3, 19, 241); font-weight:bold">
                    {{ !empty($totalrincianfisik) ? rupiah($totalrincianfisik) : '' }}</td>
                <td style="text-align:right; color:rgb(1, 1, 10); font-weight:bold">
                    {{ !empty($gantilogamtokertas) ? rupiah($gantilogamtokertas) : '' }}</td>
                <td style="text-align:right; color:rgb(1, 1, 10); font-weight:bold">
                    {{ !empty($girotocash) ? rupiah($girotocash) : '' }}</td>
                <td style="text-align:right; color:rgb(1, 1, 10); font-weight:bold">
                    {{ !empty($girototransfer) ? rupiah($girototransfer) : '' }}</td>
            </tr>
            <?php
             $dari = date("Y-m-d", strtotime("+1 day", strtotime($dari))); //looping tambah 1 date
            }
            ?>
            <tr style=" background-color:#31869b; font-weight:bold; color:white; font-size:12;">
                <th>TOTAL</th>
                <th style="text-align:right; color:rgb(255, 255, 255); font-weight:bold">
                    {{ !empty($totallhpkertas) ? rupiah($totallhpkertas) : '' }}</th>
                <th style="text-align:right; color:rgb(255, 255, 255); font-weight:bold">
                    {{ !empty($totallhplogam) ? rupiah($totallhplogam) : '' }}</th>
                <th style="text-align:right; color:rgb(255, 255, 255); font-weight:bold">
                    {{ !empty($totallhpgiro) ? rupiah($totallhpgiro) : '' }}</th>
                <th style="text-align:right; color:rgb(255, 255, 255); font-weight:bold">
                    {{ !empty($totallhptransfer) ? rupiah($totallhptransfer) : '' }}</th>
                <th style="text-align:right; color:rgb(255, 255, 255); font-weight:bold">
                    {{ !empty($totallhplainnya) ? rupiah($totallhplainnya) : '' }}</th>
                <th style="text-align:right; color:rgb(255, 255, 255); font-weight:bold">
                    {{ !empty($grandtotallhp) ? rupiah($grandtotallhp) : '' }}</th>
                <th style="text-align:right; color:rgb(255, 255, 255); font-weight:bold">
                    {{ !empty($totalsetorankertas) ? rupiah($totalsetorankertas) : '' }}</th>
                <th style="text-align:right; color:rgb(255, 255, 255); font-weight:bold">
                    {{ !empty($totalsetoranlogam) ? rupiah($totalsetoranlogam) : '' }}</th>
                <th style="text-align:right; color:rgb(255, 255, 255); font-weight:bold">
                    {{ !empty($totalsetorangiro) ? rupiah($totalsetorangiro) : '' }}</th>
                <th style="text-align:right; color:rgb(255, 255, 255); font-weight:bold">
                    {{ !empty($totalsetorantransfer) ? rupiah($totalsetorantransfer) : '' }}</th>
                <th style="text-align:right; color:rgb(255, 255, 255); font-weight:bold">
                    {{ !empty($grandtotalsetoranbank) ? rupiah($grandtotalsetoranbank) : '' }}</th>
                <th style="text-align:right; color:rgb(255, 255, 255); font-weight:bold">
                    {{ !empty($grandtotalsetoranbanklainnya) ? rupiah($grandtotalsetoranbanklainnya) : '' }}</th>
                <th style="text-align:right; color:rgb(255, 255, 255); font-weight:bold">
                    {{ !empty($saldo) ? rupiah($saldo) : '' }}</th>

            </tr>
        </tbody>
    </table>
</body>

</html>
