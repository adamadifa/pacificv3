<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Pengeluaran {{ date("d-m-y") }}</title>
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

    </style>
</head>
<body>
    <b style="font-size:14px;">
        REALISASI OMAN<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
    </b>
    <br>
    <?php
        foreach ($produk as $p) {
    ?>
    <table class="datatable3" style="width:30%">
        <tr bgcolor="#024a75" style="color:white; font-size:14px;">
            <td colspan="6"><?php echo $p->nama_barang; ?></td>
        </tr>
        <tr bgcolor="#024a75" style="color:white; font-size:14px;">
            <td>No</td>
            <td>Cabang</td>
            <td>OMAN</td>
            <td>Realisasi</td>
            <td>Sisa</td>
            <td>%</td>
        </tr>
        <?php
        $no = 1;
        foreach ($cabang as $c) {

            $oman = DB::table('detail_oman_cabang')
            ->selectRaw("detail_oman_cabang.kode_produk,SUM(jumlah) as jumlah")
            ->join('oman_cabang','detail_oman_cabang.no_order','=','oman_cabang.no_order')
            ->where('oman_cabang.kode_cabang',$c->kode_cabang)
            ->where('detail_oman_cabang.kode_produk',$p->kode_produk)
            ->where('bulan',$bulan)
            ->where('tahun',$tahun)
            ->groupByRaw('detail_oman_cabang.kode_produk,kode_cabang')
            ->first();

            $realisasi = DB::table('detail_mutasi_gudang')
            ->selectRaw("detail_mutasi_gudang.kode_produk,SUM(jumlah) as jumlah")
            ->join('mutasi_gudang_jadi','detail_mutasi_gudang.no_mutasi_gudang','=','mutasi_gudang_jadi.no_mutasi_gudang')
            ->join('permintaan_pengiriman','mutasi_gudang_jadi.no_permintaan_pengiriman','=','permintaan_pengiriman.no_permintaan_pengiriman')
            ->where('kode_cabang',$c->kode_cabang)
            ->where('detail_mutasi_gudang.kode_produk',$p->kode_produk)
            ->whereBetween('tgl_mutasi_gudang',[$dari,$sampai])
            ->groupByRaw('kode_cabang,detail_mutasi_gudang.kode_produk')
            ->first();

            if($oman != null){
                $jmloman = $oman->jumlah;
            }else{
                $jmloman = 0;
            }

            if($realisasi != null){
                $jmlrealisasi = $realisasi->jumlah;
            }else{
                $jmlrealisasi = 0;
            }


            $sisa = $jmloman - $jmlrealisasi;
            if (!empty($jmloman)) {
                $persen = $jmlrealisasi / $jmloman * 100;
            } else {
                $persen = $jmlrealisasi / 100 * 100;
            }
        ?>
        <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $c->nama_cabang; ?></td>
            <td align="right">{{ !empty($jmloman) ? rupiah($jmloman) : '' }}</td>
            <td align="right">{{ !empty($jmlrealisasi) ? rupiah($jmlrealisasi) : '' }}</td>
            <td align="right"><?php if ($sisa < 0) {
                echo str_replace("-", "> ", $sisa);
              } else {
                echo rupiah($sisa);
              } ?></td>
            <td>{{ round($persen,2) }} %</td>
        </tr>
        <?php
        $no++;
        } ?>
    </table>
    <?php } ?>
</body>
</html>
