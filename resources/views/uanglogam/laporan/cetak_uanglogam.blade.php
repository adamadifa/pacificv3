<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mutasi Uang Logam {{ date("d-m-y") }}</title>
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
        MUTASI UANG LOGAM
        <br>
        PERIODE BULAN {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }}
    </b>
    <br>
    <table class="datatable3" border="1">
        <thead style=" background-color:#31869b; color:white; font-size:12;">
            <tr style=" background-color:#31869b; color:white; font-size:12;">
                <th>TGL</th>
                <th>PENERIMAAN LHP</th>
                <th>PENGELUARAN</th>
                <th>SALDO</th>
            </tr>
            <tr style=" background-color:orange; color:white; font-size:12;">
                <th colspan="3">SALDO AWAL</th>
                <th><?php if (!empty($saldologam)) {
                  echo rupiah($saldologam);
                } ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $saldo = $saldologam;
            $totalterima = 0;
            $totalkeluar = 0;
            $totalpenerimaan = 0;
            $totalpengeluaran = 0;
            while (strtotime($dari) <= strtotime($end)) {
                $penerimaan = DB::table('setoran_penjualan')
                ->selectRaw('SUM(setoran_logam) as ul_setoranpenjualan')
                ->where('tgl_lhp',$dari)
                ->where('kode_cabang',$kode_cabang)
                ->groupBy('tgl_lhp')->first();

                $pengeluaran = DB::table('setoran_pusat')
                ->selectRaw('SUM(uang_logam) as ul_setoranpusat')
                ->where('tgl_setoranpusat',$dari)
                ->where('kode_cabang',$kode_cabang)
                ->where('omset_bulan',$bulan)
                ->where('omset_tahun',$tahun)
                ->groupBy('tgl_setoranpusat')->first();

                $kuranglebihsetor = DB::table('kuranglebihsetor')
                ->selectRaw("SUM(IF(pembayaran=1,uang_logam,0)) as kurang_logam,SUM(IF(pembayaran=2,uang_logam,0)) as lebih_logam")
                ->where('tgl_kl',$dari)
                ->where('kode_cabang',$kode_cabang)
                ->groupBy('tgl_kl')
                ->first();

                $gantilogamtokertas = DB::table('logamtokertas')
                ->selectRaw('SUM(jumlah_logamtokertas) as ul_gantikertas')
                ->where('tgl_logamtokertas',$dari)
                ->where('kode_cabang',$kode_cabang)
                ->groupBy('tgl_logamtokertas')->first();

                if($gantilogamtokertas==null){
                    $ul_gantikertas = 0;
                }else{
                    $ul_gantikertas = $gantilogamtokertas->ul_gantikertas;
                }

                if($kuranglebihsetor == null){
                    $lebih_logam = 0;
                    $kurang_logam = 0;
                }else{
                    $lebih_logam = $kuranglebihsetor->lebih_logam;
                    $kurang_logam = $kuranglebihsetor->kurang_logam;
                }


                if($penerimaan == null){
                    $ul_setoranpenjualan = 0;
                }else{
                    if($dari <= $sampai){
                        $ul_setoranpenjualan = $penerimaan->ul_setoranpenjualan ;
                    }else{
                        $ul_setoranpenjualan = 0;
                    }
                }

                if($pengeluaran == null){
                    $ul_setoranpusat = 0;
                }else{
                    $ul_setoranpusat = $pengeluaran->ul_setoranpusat;
                }

                $terima = $ul_setoranpenjualan + $kurang_logam - $lebih_logam;
                $keluar = $ul_setoranpusat + $ul_gantikertas;

                $saldo += ($terima-$keluar);

                $totalpenerimaan += $terima;
                $totalpengeluaran +=$keluar;
            ?>
            <tr>
                <td>{{ date("d-m-Y",strtotime($dari)) }}</td>
                <td style="text-align: right; color:red; font-weight:bold">{{ !empty($terima) ? rupiah($terima) : '' }}</td>
                <td style="text-align: right; color:green; font-weight:bold">{{ !empty($keluar) ? rupiah($keluar) : '' }}</td>
                <td style="text-align: right; font-weight:bold">{{ !empty($saldo) ? rupiah($saldo) : '' }}</td>
            </tr>
            <?php
            $dari = date("Y-m-d", strtotime("+1 day", strtotime($dari))); //looping tambah 1 date
            } ?>

            <tr style=" background-color:#31869b; font-weight:bold; color:white; font-size:12;">
                <td>TOTAL</td>
                <td align="right"><?php echo rupiah($totalpenerimaan); ?></td>
                <td align="right"><?php echo rupiah($totalpengeluaran); ?></td>
                <td align="right"><?php echo rupiah($saldo); ?></td>
            </tr>
        </tbody>
    </table>

</body>
</html>
