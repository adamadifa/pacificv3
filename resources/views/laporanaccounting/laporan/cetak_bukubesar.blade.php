<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Buku Besar {{ date("d-m-y") }}</title>
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
        BUKU BESAR<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }} <br>
        AKUN {{ $dariakun->kode_akun }} {{ $dariakun->nama_akun }}
        @if ($sampaiakun->kode_akun != $dariakun->kode_akun)
        s/d
        {{ $sampaiakun->kode_akun }} {{ $sampaiakun->nama_akun }}
        @endif
        <br>
        <table class="datatable3" style="width:90%" border="1">
            <thead>
                <tr>
                    <th style="font-size:12;">TGL</th>
                    <th style="font-size:12;">NO BUKTI</th>
                    <th style="font-size:12;">SUMBER</th>
                    <th style="font-size:12;">KETERANGAN</th>
                    <th style="font-size:12;">DEBET</th>
                    <th style="font-size:12;">KREDIT</th>
                    <th style="font-size:12;">SALDO</th>
                </tr>
            </thead>
            <tbody>
                @php
                $totaldebet = 0;
                $totalkredit = 0;
                $saldo = 0;
                $kode_akun = "";
                @endphp
                @foreach ($bukubesar as $key => $d)
                @php
                $totaldebet = $totaldebet + $d->debet;
                $totalkredit = $totalkredit + $d->kredit;
                $akun = @$bukubesar[$key + 1]->kode_akun;

                if ($kode_akun != $d->kode_akun) {
                $saldo = 0;

                $saldoawal = DB::table('detailsaldoawal_bb')
                ->join('saldoawal_bb','detailsaldoawal_bb.kode_saldoawal_bb','=','saldoawal_bb.kode_saldoawal_bb')
                ->where('kode_akun',$d->kode_akun)->where('bulan', $bulan)->where('tahun', $tahun)->first();
                if ($saldoawal != null) {
                $sa = $saldoawal->jumlah;
                $tgl_mulai = $saldoawal->tahun . "-" . $saldoawal->bulan . "-01";
                } else {
                $sa = 0;
                $tgl_mulai = $tahun."-".$bulan."-01";
                }

                if (!empty($dari)) {
                $mutasi = DB::table('buku_besar')
                ->selectRaw("SUM(IFNULL(debet,0) - IFNULL(kredit,0)) as sisamutasi")
                ->where('kode_akun', $d->kode_akun)
                ->where('tanggal', '>=', $tgl_mulai)
                ->where('tanggal', '<', $dari) ->first();
                    $saldo_awal = $sa + $mutasi->sisamutasi;
                    } else {
                    $saldo_awal = 0;
                    }

                    $saldo = $saldo_awal;
                    echo '
                    <tr style="background-color:rgba(116, 170, 227, 0.465);">
                        <th style="text-align: left" colspan="7">Akun : '.$d->kode_akun.' '. $d->nama_akun.'
                            '.$d->jenis_akun.'</th>
                    </tr>
                    <tr style="background-color:rgba(116, 170, 227, 0.465);">
                        <th style="text-align: left" colspan="6">SALDO AWAL</th>
                        <th style="text-align: right">'.desimal($saldo_awal).'</th>
                    </tr>';
                    }
                    if($d->jenis_akun==1){
                    $saldo = $saldo + $d->kredit - $d->debet;
                    }else{
                    $saldo = $saldo + $d->debet - $d->kredit;
                    }
                    @endphp
                    {{-- <tr>
                    <td>{{ $d->kode_akun }}</td>
                    </tr> --}}
                    @if ($d->tanggal != null)


                    <tr>
                        <td>{{ DateToIndo2($d->tanggal) }}</td>
                        <td>{{ $d->nobukti_transaksi }}</td>
                        <td>
                            <?php
                        $smbr = strtolower($d->sumber);
                        if ($smbr == "ledger") {
                            $ledger = DB::table('ledger_bank')->where('no_bukti',$d->nobukti_transaksi)
                            ->leftJoin('master_bank','ledger_bank.bank','=','master_bank.kode_bank')->first();
                            $ledger != null ? $sumber = $ledger->nama_bank : 'Ledger';

                        } else if($smbr=="kas kecil"){
                            $c = ['TSM','TGL','BDG','SKB','BGR','PST','PWT','KLT','SMR','SBY','GRT'];

                            $kodecbg = substr($d->nobukti_transaksi,0,3);

                            if(in_array($kodecbg,$c)){
                                $kodecbg = $kodecbg;
                            }else{
                                $kodecbg = "TGL";
                            }
                            $sumber = "Kas Kecil ". $kodecbg;
                        }else{
                            $sumber =  $d->sumber;
                        }
                        // $sumber = $d->sumber;
                        echo $sumber;
                        ?>
                        </td>
                        <td>{{ $d->keterangan }}</td>
                        <td align="right">{{ !empty($d->debet) ? desimal($d->debet) : '' }}</td>
                        <td align="right">{{ !empty($d->kredit) ? desimal($d->kredit) : '' }}</td>
                        <td align="right">{{ desimal($saldo) }}</td>
                    </tr>
                    @endif
                    @php
                    if ($akun != $d->kode_akun) {
                    echo
                    '<tr style="background-color:rgb(195, 195, 195);">
                        <th colspan="4">TOTAL</th>
                        <th style="text-align: right">'.desimal($totaldebet).'</th>
                        <th style="text-align: right">'.desimal($totalkredit).'</th>
                        <th style="text-align: right">'.desimal($saldo).'</th>
                    </tr>';

                    $totaldebet =0;
                    $otalkredit = 0;
                    }

                    $kode_akun = $d->kode_akun;

                    @endphp
                    @endforeach
            </tbody>
        </table>

</body>

</html>
