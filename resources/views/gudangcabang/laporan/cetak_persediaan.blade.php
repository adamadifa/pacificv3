<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Persediaan Gudang Cabang {{ date("d-m-y") }}</title>
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
        PACIFIC CABANG {{ $cabang->nama_cabang }}
        REKAPITULASI PERSEDIAAN BARANG<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        <table>
            <tr>
                <td>KODE PRODUK</td>
                <td>{{ $produk->kode_produk }}</td>
            </tr>
            <tr>
                <td>NAMA PRODUK</td>
                <td>{{ $produk->nama_barang }}</td>
            </tr>
        </table>
        <br>
    </b>
    <br>
    <table class="datatable3" style="width:150%" border="1">
        <thead>
            <tr>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">TANGGAL</th>
                <th colspan="3" bgcolor="#024a75" style="color:white; font-size:12;">BUKTI</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">SALESMAN</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">PELANGGAN</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">KETERANGAN</th>
                <th colspan="6" bgcolor="#28a745" style="color:white; font-size:12;">PENERIMAAN</th>
                <th colspan="8" bgcolor="#c7473a" style="color:white; font-size:12;">PENGELUARAN</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">SALDO AKHIR</th>
                <th colspan="3" rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">SALDO AKHIR</th>
                <th rowspan="3" bgcolor="#024a75" style="color:white; font-size:12;">TANGGAL INPUT</th>
                <th rowspan="3" bgcolor="#024a75" style="color:white; font-size:12;">TANGGAL UPDATE</th>
            </tr>
            <tr>
                <th bgcolor="#024a75" style="color:white; font-size:12;">SURAT JALAN / NO FAKTUR</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">TGL KIRIM</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">NO BUKTI</th>
                <th bgcolor="#28a745" style="color:white; font-size:12;">PUSAT</th>
                <th bgcolor="#28a745" style="color:white; font-size:12;">TRANSIT IN</th>
                <th bgcolor="#28a745" style="color:white; font-size:12;">RETUR</th>
                <th bgcolor="#28a745" style="color:white; font-size:12;">LAIN LAIN</th>
                <th bgcolor="#28a745" style="color:white; font-size:12;">REPACK</th>
                <th bgcolor="#28a745" style="color:white; font-size:12;">PENYESUAIAN</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">PENJUALAN</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">PROMOSI</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">REJECT PASAR</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">REJECT MOBIL</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">REJECT GUDANG</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">TRANSIT OUT</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">LAIN LAIN</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">PENYESUAIAN</th>
            </tr>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th colspan="6"></th>
                <th>SALDO AWAL</th>
                <th colspan="14"></th>
                <th style="text-align: right"><?php echo desimal($saldoawal); ?></th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">DUS</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">PACK</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">PCS</th>
            </tr>
        </thead>
        <tbody>
            @php
            $saldoakhir = $saldoawal;
            $realsaldoakhir = $realsaldoawal;
            $totalpenerimaan = 0;
            $totaltransit_in =0;
            $totalretur = 0;
            $totallainlain_in = 0;
            $totalrepack = 0 ;
            $totalpeny_in = 0;
            $totalpenjualan = 0;
            $totalpromosi = 0;
            $totalreject_pasar =0;
            $totalreject_mobil = 0;
            $totalreject_gudang = 0;
            $totaltransit_out =0;
            $totallainlain_out =0;
            $totalpeny_out = 0;
            $jmldus = 0;
            $jmlpack = 0;
            $jmlpcs = 0;
            @endphp
            @foreach ($mutasi as $m)
            @php
            if($m->jenis_mutasi=="SURAT JALAN"){
            $ket = "PENERIMAAN PUSAT";
            }elseif($m->jenis_mutasi=="TRANSIT IN"){
            $ket = "TRANSIT IN <b style='color:#23a7e0'>" . $m->no_mutasi_gudang_cabang . "</b>";
            }elseif($m->jenis_mutasi=="TRANSIT OUT"){
            $ket = "TRANSIT OUT <b style='color:#23a7e0'>" . $m->no_mutasi_gudang_cabang . "</b>";
            }elseif($m->jenis_mutasi=="PL TTR"){
            $ket = "PELUNASAN TTR";
            }elseif($m->jenis_mutasi=="PENYESUAIAN BAD"){
            $ket = "PENERIMAAN LAIN LAIN DARI BAD STOK";
            }elseif($m->jenis_mutasi=="REJECT GUDANG"){
            $ket = "REJECT GUDANG <b style='color:#7d0303'>" . $m->no_suratjalan . "</b>";
            }else{
            $ket=$m->jenis_mutasi;
            }

            if($m->jenis_mutasi=="HUTANG KIRIM"){
            $jmllainlain_in = $m->hutangkirim;
            $jmllainlain_in_pcs = $m->hutangkirim;
            }elseif($m->jenis_mutasi=="PL TTR"){
            $jmllainlain_in = $m->plttr;
            $jmllainlain_in_pcs = $m->plttr;
            }elseif($m->jenis_mutasi=="PENYESUAIAN BAD"){
            $jmllainlain_in = $m->penyesuaian_bad;
            $jmllainlain_in_pcs = $m->penyesuaian_bad;
            }else{
            $jmllainlain_in = 0;
            $jmllainlain_in_pcs = 0;
            }

            if($m->jenis_mutasi=="TTR"){
            $jmllainlain_out = $m->ttr;
            $jmllainlain_out_pcs = $m->ttr;
            }elseif($m->jenis_mutasi=="GANTI BARANG"){
            $jmllainlain_out = $m->ganti_barang;
            $jmllainlain_out_pcs = $m->ganti_barang;
            }elseif($m->jenis_mutasi=="PL HUTANG KIRIM"){
            $jmllainlain_out = $m->plhutangkirim;
            $jmllainlain_out_pcs = $m->plhutangkirim;
            }else{
            $jmllainlain_out = 0;
            $jmllainlain_out_pcs = 0;
            }

            if($m->jenis_mutasi=="PENYESUAIAN"){
            if($m->inout_good=="OUT"){
            $jmlpeny_out = $m->penyesuaian;
            $jmlpeny_out_pcs = $m->penyesuaian;
            $jmlpeny_in = 0;
            $jmlpeny_in_pcs = 0;
            }else{
            $jmlpeny_out = 0;
            $jmlpeny_out_pcs = 0;
            $jmlpeny_in = $m->penyesuaian;
            $jmlpeny_in_pcs = $m->penyesuaian;
            }
            }else{
            $jmlpeny_out = 0;
            $jmlpeny_out_pcs = 0;
            $jmlpeny_in = 0;
            $jmlpeny_in_pcs = 0;
            }

            if($m->jenis_mutasi=="HUTANG KIRIM"){
            $color_lainlain_in = "#ba1d1d";
            }elseif($m->jenis_mutasi=="PL TTR"){
            $color_lainlain_in = "#e59a04";
            }else{
            $color_lainlain_in = "";
            }


            if($m->jenis_mutasi=="PL HUTANG KIRIM"){
            $color_lainlain_out = "#ba1d1d";
            }elseif($m->jenis_mutasi=="TTR"){
            $color_lainlain_out = "#e59a04";
            }else{
            $color_lainlain_out = "";
            }


            if($m->jenis_mutasi=="TRANSIT IN"){
            $color_transit_in = "#23a7e0";
            }else{
            $color_transit_in = "";
            }

            if($m->jenis_mutasi=="TRANSIT OUT"){
            $color_transit_out = "#23a7e0";
            }else{
            $color_transit_out = "";
            }

            if($m->jenis_mutasi=="PROMOSI"){
            $color_promosi = "yellow";
            }else{
            $color_promosi = "";
            }

            $penerimaanpusat = $m->penerimaanpusat / $m->isipcsdus;
            $transit_in = $m->transit_in / $m->isipcsdus;
            $retur = $m->retur / $m->isipcsdus;
            $jmllainlain_in = $jmllainlain_in / $m->isipcsdus;
            $repack = $m->repack / $m->isipcsdus;
            $jmlpeny_in = $jmlpeny_in / $m->isipcsdus;

            $jmlpeny_out = $jmlpeny_out / $m->isipcsdus;
            $penjualan = $m->penjualan / $m->isipcsdus;
            $promosi = $m->promosi / $m->isipcsdus;
            $reject_pasar = $m->reject_pasar / $m->isipcsdus;
            $reject_mobil = $m->reject_mobil / $m->isipcsdus;
            $reject_gudang = $m->reject_gudang / $m->isipcsdus;
            $transit_out = $m->transit_out / $m->isipcsdus;
            $jmllainlain_out = $jmllainlain_out / $m->isipcsdus;

            $totalpenerimaan += $penerimaanpusat;
            $totaltransit_in += $transit_in;
            $totalretur += $retur;
            $totallainlain_in += $jmllainlain_in;
            $totalrepack += $repack ;
            $totalpeny_in += $jmlpeny_in;
            $totalpenjualan += $penjualan;
            $totalpromosi += $promosi;
            $totalreject_pasar += $reject_pasar;
            $totalreject_mobil += $reject_mobil;
            $totalreject_gudang += $reject_gudang;
            $totaltransit_out += $transit_out;
            $totallainlain_out += $jmllainlain_out;
            $totalpeny_out = $jmlpeny_out;

            $penerimaan = $penerimaanpusat + $transit_in + $retur + $jmllainlain_in + $repack + $jmlpeny_in;
            $pengeluaran = $jmlpeny_out + $penjualan + $promosi + $reject_pasar + $reject_gudang + $transit_out + $jmllainlain_out + $reject_mobil;
            $saldoakhir = $saldoakhir + $penerimaan - $pengeluaran;

            $penerimaan_pcs = $m->penerimaanpusat + $m->transit_in + $m->retur + $jmllainlain_in_pcs + $m->repack + $jmlpeny_in_pcs;
            $pengeluaran_pcs = $jmlpeny_out_pcs + $m->penjualan + $m->promosi + $m->reject_pasar + $m->reject_gudang + $m->transit_out + $jmllainlain_out_pcs + $m->reject_mobil;
            $realsaldoakhir = $realsaldoakhir + $penerimaan_pcs - $pengeluaran_pcs;
            $cek = $realsaldoakhir;


            $saldoakhirfix = $realsaldoakhir < 0 ? $realsaldoakhir * -1 : $realsaldoakhir; if ($m->inout_good == 'IN') {
                $color_sa = "#28a745";
                } else {
                $color_sa = "#c7473a";
                }


                if ($saldoakhirfix !=0) { $jmldus=floor($saldoakhirfix / $m->isipcsdus);
                $sisadus = $saldoakhirfix % $m->isipcsdus;
                if ($m->isipack == 0) {
                $jmlpack = 0;
                $sisapack = $sisadus;
                } else {
                $jmlpack = floor($sisadus / $m->isipcs);
                $sisapack = $sisadus % $m->isipcs;
                }
                $jmlpcs = $sisapack;
                if ($m->satuan == 'PCS') {

                $jmldus = 0;
                $jmlpack = 0;
                $jmlpcs = $saldoakhirfix;
                }
                } else {
                $jmldus = 0;
                $jmlpack = 0;
                $jmlpcs = 0;
                }


                @endphp
                <tr>
                    <td>{{ DateToIndo2($m->tgl_mutasi_gudang_cabang) }}</td>
                    <td>
                        @if ($m->jenis_mutasi=='SURAT JALAN')
                        {{ $m->no_mutasi_gudang_cabang."/".$m->no_dok }}
                        @elseif ($m->jenis_mutasi=="REJECT GUDANG" OR $m->jenis_mutasi=="TRANSIT IN" OR $m->jenis_mutasi=="TRANSIT OUT")
                        {{ !empty($m->no_dok) ? $m->no_suratjalan."/".$m->no_dok : $m->no_suratjalan }}
                        @endif
                    </td>
                    <td>
                        @if (!empty($m->tgl_kirim))
                        {{ DateToIndo2($m->tgl_kirim) }}
                        @endif
                    </td>
                    <td>
                        @if ($m->jenis_mutasi=="REJECT GUDANG" OR $m->jenis_mutasi=="REPACK" OR $m->jenis_mutasi=="PENYESUAIAN" OR $m->jenis_mutasi=="PENYESUAIAN BAD")
                        {{ $m->no_mutasi_gudang_cabang }}
                        @else
                        {{ $m->no_dpb }}
                        @endif
                    </td>
                    <td>{{ $m->nama_karyawan }}</td>
                    <td></td>
                    <td>{!! $ket !!}</td>
                    <td align="right">{{ !empty($penerimaanpusat) ?desimal($penerimaanpusat): '' }}</td>
                    <td align="right" style="background-color: {{ $color_transit_in }}">{{ !empty($transit_in) ? desimal($transit_in) : '' }}</td>
                    <td align="right">{{ !empty($retur) ? desimal($retur) : '' }}</td>
                    <td align="right" style="background-color: {{ $color_lainlain_in }}">{{ !empty($jmllainlain_in) ? desimal($jmllainlain_in) : '' }}</td>
                    <td align="right">{{ !empty($repack) ? desimal($repack) : '' }}</td>
                    <td align="right">{{ !empty($jmlpeny_in) ? desimal($jmlpeny_in) : '' }}</td>
                    <td align="right">{{ !empty($penjualan) ? desimal($penjualan) : '' }}</td>
                    <td align="right" style="background-color: {{ $color_promosi }}">{{ !empty($promosi) ? desimal($promosi) : '' }}</td>
                    <td align="right">{{ !empty($reject_pasar) ? desimal($reject_pasar) : '' }}</td>
                    <td align="right">{{ !empty($reject_mobil) ? desimal($reject_mobil) : '' }}</td>
                    <td align="right">{{ !empty($reject_gudang) ? desimal($reject_gudang) : '' }}</td>
                    <td align="right" style="background-color: {{ $color_transit_out }}">{{ !empty($transit_out) ? desimal($transit_out) : '' }}</td>
                    <td align="right" style="background-color: {{ $color_lainlain_out }}">{{ !empty($jmllainlain_out) ? desimal($jmllainlain_out) : '' }}</td>
                    <td align="right">{{ !empty($jmlpeny_out) ? desimal($jmlpeny_out) : '' }}</td>
                    <td align="right" style="background-color:{{ $color_sa }}">{{ desimal($saldoakhir) }}</td>
                    <td align="right" style="background-color:{{ $color_sa }}">{{ rupiah($jmldus) }}</td>
                    <td align="right" style="background-color:{{ $color_sa }}">{{ rupiah($jmlpack) }}</td>
                    <td align="right" style="background-color:{{ $color_sa }}">{{ rupiah($jmlpcs) }}</td>
                    <td>{{ date("d-m-y H:i:s",strtotime($m->date_created)) }}</td>
                    <td>{{ !empty($m->date_updated) ? date("d-m-y H:i:s",strtotime($m->date_updated)) : '' }}</td>

                </tr>
                @endforeach
        </tbody>
        <tfoot>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th colspan="7">TOTAL</th>
                <th style="text-align: right"><?php echo desimal($totalpenerimaan); ?></th>
                <th style="text-align: right"><?php echo desimal($totaltransit_in); ?></th>
                <th style="text-align: right"><?php echo desimal($totalretur); ?></th>
                <th style="text-align: right"><?php echo desimal($totallainlain_in); ?></th>
                <th style="text-align: right"><?php echo desimal($totalrepack); ?></th>
                <th style="text-align: right"><?php echo desimal($totalpeny_in); ?></th>
                <th style="text-align: right"><?php echo desimal($totalpenjualan); ?></th>
                <th style="text-align: right"><?php echo desimal($totalpromosi); ?></th>
                <th style="text-align: right"><?php echo desimal($totalreject_pasar); ?></th>
                <th style="text-align: right"><?php echo desimal($totalreject_mobil); ?></th>
                <th style="text-align: right"><?php echo desimal($totalreject_gudang); ?></th>
                <th style="text-align: right"><?php echo desimal($totaltransit_out); ?></th>
                <th style="text-align: right"><?php echo desimal($totallainlain_out); ?></th>
                <th style="text-align: right"><?php echo desimal($totalpeny_out); ?></th>
                <th style="text-align: right"><?php echo desimal($saldoakhir); ?></th>
                <th style="text-align: right"><?php echo rupiah($jmldus); ?></th>
                <th style="text-align: right"><?php echo rupiah($jmlpack); ?></th>
                <th style="text-align: right"><?php echo rupiah($jmlpcs); ?></th>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
