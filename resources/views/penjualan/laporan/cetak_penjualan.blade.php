<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Penjualan {{ date("d-m-y") }}</title>
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
        LAPORAN PENJUALAN<br>
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
    <br>
    <table class="datatable3" style="width:150%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td style="width:1%" rowspan="2">No</td>
                <td rowspan="2">Tanggal</td>
                <td rowspan="2">No Faktur</td>
                <td rowspan="2">Kode Pel.</td>
                <td rowspan="2">Nama Pelanggan</td>
                <td rowspan="2">Nama Sales</td>
                <td rowspan="2">Pasar/Daerah</td>
                <td rowspan="2">Hari</td>
                <td rowspan="2">Nama Barang</td>
                <td colspan="7" align="center">QTY</td>
                <td rowspan="2">Total</td>
                <td rowspan="2" style="background-color:#10a743">Retur</td>

                <td rowspan="2" style="background-color:#10a743">Penyesuaian</td>
                <td colspan="5" style="background-color:#a71048">Potongan</td>
                <td rowspan="2" style="background-color:#10a743">Potongan Istimewa</td>
                <td rowspan="2" style="background-color:#10a743">Penjualan Netto</td>
                <td rowspan="2" style="background-color:#10a743">TUNAI/KREDIT</td>
                <td rowspan="2" style="background-color:#10a743">Tanggal Input</td>
                <td rowspan="2" style="background-color:#10a743">Tanggal Update</td>
            </tr>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td>DUS</td>
                <td>Harga</td>
                <td>PACK</td>
                <td>Harga</td>
                <td>PCS</td>
                <td>Harga</td>
                <td>Subtotal</td>
                <td style="background-color: #a71048;">AIDA</td>
                <td style="background-color: #a71048;">SWAN</td>
                <td style="background-color: #a71048;">STICK</td>
                <td style="background-color: #a71048;">SP</td>
                <td style="background-color: #a71048;">TOTAL</td>
            </tr>
        </thead>
        <tbody>
            @php
            $no = 1;
            $totaldus = 0;
            $totalpack = 0;
            $totalpcs = 0;
            $total = 0;
            $totalretur = 0;
            $totalpenyharga = 0;
            $totalpotaida = 0;
            $totalpotswan = 0;
            $totalpotstick = 0;
            $totalpotsp = 0;
            $totalpotongan = 0;
            $totalpotist = 0;
            $netto = 0;
            $totaldus2 = 0;
            $totalpack2 = 0;
            $totalpcs2 = 0;
            $subtotaldus = 0;
            $subtotaldus2 = 0;
            $subtotalpack = 0;
            $subtotalpack2 = 0;
            $subtotalpcs = 0;
            $subtotalpcs2 = 0;
            $subtotal = 0;
            $subtotalretur = 0;
            $subtotalpenyharga = 0;
            $subtotalpotongan = 0;
            $subtotalpotist = 0;
            $subtotalnetto = 0;
            $color = "";
            @endphp
            @foreach ($penjualan as $key => $p)
            @php
            $pel = @$penjualan[$key + 1]->kode_pelanggan;
            $jmlbarang = DB::table('detailpenjualan')->where('no_fak_penj',$p->no_fak_penj)->count();
            if (empty($jmlbarang)) {
            $jmlbarang = 1;
            } else {
            $jmlbarang = $jmlbarang;
            }

            $barang1 = DB::table('detailpenjualan')
            ->selectRaw('no_fak_penj,detailpenjualan.kode_barang,nama_barang,jumlah,isipcsdus,
            isipack,isipcs,detailpenjualan.harga_dus,detailpenjualan.harga_pack,detailpenjualan.harga_pcs
            ,subtotal,promo')
            ->join('barang','detailpenjualan.kode_barang','=','barang.kode_barang')
            ->where('no_fak_penj',$p->no_fak_penj)
            ->orderBy('detailpenjualan.kode_barang')
            ->first();


            if($barang1!=null){
            $jmldus = floor($barang1->jumlah / $barang1->isipcsdus);

            if ($barang1->jumlah != 0) {
            $sisadus = $barang1->jumlah % $barang1->isipcsdus;
            } else {
            $sisadus = 0;
            }
            if ($barang1->isipack == 0) {
            $jmlpack = 0;
            $sisapack = $sisadus;
            } else {
            $jmlpack = floor($sisadus / $barang1->isipcs);
            $sisapack = $sisadus % $barang1->isipcs;
            }
            $jmlpcs = $sisapack;
            $promo = $barang1->promo;
            $nama_barang = $barang1->nama_barang;
            $subtotal_1 = $barang1->subtotal;
            }else{
            $jmldus = 0;
            $jmlpack = 0;
            $jmlpcs = 0;
            $promo = 0;
            $nama_barang = "";
            $subtotal_1 = 0;
            }


            $totaldus = $totaldus + $jmldus;
            $totalpack = $totalpack + $jmlpack;
            $totalpcs = $totalpcs + $jmlpcs;
            $total = $total + $p->subtotal;
            $totalretur = $totalretur + $p->totalretur;
            $totalpenyharga = $totalpenyharga + $p->penyharga;
            $totalpotaida = $totalpotaida + $p->potaida;
            $totalpotswan = $totalpotswan + $p->potswan;
            $totalpotstick = $totalpotstick + $p->potstick;
            $totalpotsp = $totalpotsp + $p->potsp;
            $totalpotongan = $totalpotongan + $p->potongan;
            $totalpotist = $totalpotist + $p->potistimewa;
            $netto = $netto + $p->totalpiutang;

            //Subtotal
            $subtotal = $subtotal + $p->total;;
            $subtotaldus = $subtotaldus + $jmldus;
            $subtotalpack = $subtotalpack + $jmlpack;
            $subtotalpcs = $subtotalpcs + $jmlpcs;
            $subtotalretur = $subtotalretur + $p->totalretur;
            $subtotalpenyharga = $subtotalpenyharga + $p->penyharga;
            $subtotalpotongan = $subtotalpotongan + $p->potongan;
            $subtotalpotist = $subtotalpotist + $p->potistimewa;
            $subtotalnetto = $subtotalnetto + $p->totalpiutang;
            if ($p->status == "1") {
            $bgcolor = "orange";
            } else {
            $bgcolor = "";
            }
            @endphp
            <tr bgcolor="<?php echo $bgcolor; ?>">
                <td rowspan="{{ $jmlbarang }}">{{ $no }}</td>
                <td rowspan="{{ $jmlbarang }}">{{ DateToIndo2($p->tgltransaksi) }}</td>
                <td rowspan="{{ $jmlbarang }}">{{ $p->no_fak_penj }}</td>
                <td rowspan="{{ $jmlbarang }}">{{ $p->kode_pelanggan }}</td>
                <td rowspan="{{ $jmlbarang }}">{{ $p->nama_pelanggan }}</td>
                <td rowspan="{{ $jmlbarang }}">{{ $p->nama_karyawan }}</td>
                <td rowspan="{{ $jmlbarang }}">{{ $p->pasar }}</td>
                <td rowspan="{{ $jmlbarang }}">{{ $p->hari }}</td>
                <td @if ($promo==1) style="background-color: yellow" @endif>
                    {{ $nama_barang }}
                </td>
                <td @if ($promo==1) style="background-color: yellow" @endif align="center">
                    @if ($jmldus != 0 )
                    {{ $jmldus }}
                    @endif
                </td>
                <td @if ($promo==1) style="background-color: yellow" @endif align="right">
                    @if ($jmldus != 0 )
                    {{ rupiah($barang1->harga_dus) }}
                    @endif
                </td>
                <td @if ($promo==1) style="background-color: yellow" @endif align="center">
                    @if ($jmlpack != 0 )
                    {{ $jmlpack }}
                    @endif
                </td>
                <td @if ($promo==1) style="background-color: yellow" @endif align="right">
                    @if ($jmlpack != 0 )
                    {{ rupiah($barang1->harga_pack) }}
                    @endif

                </td>
                <td @if ($promo==1) style="background-color: yellow" @endif align="center">
                    @if ($jmlpcs !=0 )
                    {{ $jmlpcs }}
                    @endif
                </td>
                <td @if ($promo==1) style="background-color: yellow" @endif align="right">
                    @if ($jmlpcs !=0 )
                    {{ rupiah($barang1->harga_pcs) }}
                    @endif
                </td>
                <td @if ($promo==1) style="background-color: yellow" @endif align="right">
                    {{ rupiah($subtotal_1) }}
                </td>
                <td align="right" rowspan="{{ $jmlbarang }}">{{ rupiah($p->subtotal) }}</td>
                <td align="right" rowspan="{{ $jmlbarang }}">
                    @if (!empty($p->totalretur))
                    {{ rupiah($p->totalretur) }}
                    @endif
                </td>
                <td align="right" rowspan="{{ $jmlbarang }}">
                    @if (!empty($p->penyharga))
                    {{ rupiah($p->penyharga) }}
                    @endif
                </td>
                <td align="right" rowspan="{{ $jmlbarang }}">
                    @if (!empty($p->potaida))
                    {{ rupiah($p->potaida) }}
                    @endif
                </td>
                <td align="right" rowspan="{{ $jmlbarang }}">
                    @if (!empty($p->potswan))
                    {{ rupiah($p->potswan) }}
                    @endif
                </td>
                <td align="right" rowspan="{{ $jmlbarang }}">
                    @if (!empty($p->potstick))
                    {{ rupiah($p->potstick) }}
                    @endif
                </td>
                <td align="right" rowspan="{{ $jmlbarang }}">
                    @if (!empty($p->potsp))
                    {{ rupiah($p->potsp) }}
                    @endif
                </td>
                <td align="right" rowspan="{{ $jmlbarang }}">
                    @if (!empty($p->potongan))
                    {{ rupiah($p->potongan) }}
                    @endif
                </td>
                <td align="right" rowspan="{{ $jmlbarang }}">
                    @if (!empty($p->potistimewa))
                    {{ rupiah($p->potistimewa) }}
                    @endif
                </td>
                <td align="right" rowspan="{{ $jmlbarang }}">
                    @if (!empty($p->totalpiutang))
                    {{ rupiah($p->totalpiutang) }}
                    @endif
                </td>
                <td align="right" rowspan="{{ $jmlbarang }}">{{ strtoupper($p->jenistransaksi) }}</td>
                <td align="right" rowspan="{{ $jmlbarang }}">{{ date("d-m-y H:i:s",strtotime($p->date_created)) }}</td>
                <td align="right" rowspan="{{ $jmlbarang }}">{{ date("d-m-y H:i:s",strtotime($p->date_updated)) }}</td>
                @php

                if ($jmlbarang > 1) {
                $barang2 = DB::table('detailpenjualan')
                ->selectRaw('no_fak_penj,detailpenjualan.kode_barang,nama_barang,jumlah,isipcsdus,
                isipack,isipcs,detailpenjualan.harga_dus,detailpenjualan.harga_pack,detailpenjualan.harga_pcs
                ,subtotal,promo')
                ->join('barang','detailpenjualan.kode_barang','=','barang.kode_barang')
                ->where('no_fak_penj',$p->no_fak_penj)
                ->skip(1)->take($jmlbarang)
                ->orderBy('detailpenjualan.kode_barang')
                ->get();
                }else{
                $barang2 = null;
                }
                @endphp
                @if ($barang2 != null)
                @foreach ($barang2 as $b2)
                @php
                $jmldus = floor($b2->jumlah / $b2->isipcsdus);
                $sisadus = $b2->jumlah % $b2->isipcsdus;
                if ($b2->isipack == 0) {
                $jmlpack = 0;
                $sisapack = $sisadus;
                } else {

                $jmlpack = floor($sisadus / $b2->isipcs);
                $sisapack = $sisadus % $b2->isipcs;
                }

                $jmlpcs = $sisapack;


                $totaldus2 = $totaldus2 + $jmldus;
                $totalpack2 = $totalpack2 + $jmlpack;
                $totalpcs2 = $totalpcs2 + $jmlpcs;

                $subtotaldus2 = $subtotaldus2 + $jmldus;
                $subtotalpack2 = $subtotalpack2 + $jmlpack;
                $subtotalpcs2 = $subtotalpcs2 + $jmlpcs;

                if ($b2->promo == 1) {
                $color = "background-color:yellow";
                } else {
                if ($p->status == 1) {
                $color = "background-color:orange";
                } else {
                $color = "background-color:white";
                }
                }
                @endphp
            <tr style="<?php echo $color; ?>">
                <td><?php echo $b2->nama_barang; ?></td>
                <td align='center'><?php if ($jmldus != 0) {
                                          echo $jmldus;
                                        } ?></td>
                <td align='right'><?php if ($jmldus != 0) {
                                        echo number_format($b2->harga_dus, '0', '', '.');
                                      } ?></td>
                <td align='center'><?php if ($jmlpack != 0) {
                                          echo $jmlpack;
                                        } ?></td>
                <td align='right'><?php if ($jmlpack != 0) {
                                        echo number_format($b2->harga_pack, '0', '', '.');
                                      } ?></td>
                <td align='center'><?php if ($jmlpcs != 0) {
                                          echo $jmlpcs;
                                        } ?></td>
                <td align='right'><?php if ($jmlpcs != 0) {
                                        echo number_format($b2->harga_pcs, '0', '', '.');
                                      } ?></td>
                <td align='right'><?php echo number_format($b2->subtotal, '0', '', '.'); ?></td>
            </tr>
            @endforeach
            @endif
            </tr>
            @php
            $no++;
            @endphp
            @endforeach
            <tr bgcolor="#024a75" style="color:white; font-weight:bold">
                <td colspan="9">TOTAL</td>
                <td align="center"><?php //echo $totaldus + $totaldus2;
                                    ?></td>
                <td></td>
                <td align="center"><?php //echo $totalpack + $totalpack2;
                                    ?></td>
                <td></td>
                <td align="center"><?php //echo $totalpcs + $totalpcs2;
                                    ?></td>
                <td></td>
                <td align="right"><?php echo number_format($total, '0', '', '.'); ?></td>
                <td align="right"><?php echo number_format($total, '0', '', '.'); ?></td>
                <td align="right"><?php echo number_format($totalretur, '0', '', '.'); ?></td>

                <td align="right"><?php echo number_format($totalpenyharga, '0', '', '.'); ?></td>
                <td align="right"><?php echo number_format($totalpotaida, '0', '', '.'); ?></td>
                <td align="right"><?php echo number_format($totalpotswan, '0', '', '.'); ?></td>
                <td align="right"><?php echo number_format($totalpotstick, '0', '', '.'); ?></td>
                <td align="right"><?php echo number_format($totalpotsp, '0', '', '.'); ?></td>
                <td align="right"><?php echo number_format($totalpotongan, '0', '', '.'); ?></td>
                <td align="right"><?php echo number_format($totalpotist, '0', '', '.'); ?></td>
                <td align="right"><?php echo number_format($netto, '0', '', '.'); ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
