<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Retur</title>
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
        LAPORAN RETUR<br>
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
    <table class="datatable3" style="width:130%">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td rowspan="2">No</td>
                <td rowspan="2">Tanggal Retur</td>
                <td rowspan="2">No Retur</td>
                <td rowspan="2">No Faktur</td>
                <td rowspan="2">Kode Pel.</td>
                <td rowspan="2">Nama Pelanggan</td>
                <td rowspan="2">Pasar/Daerah</td>
                <td rowspan="2">Hari</td>
                <td rowspan="2">Nama Barang</td>
                <td colspan="7" align="center">QTY</td>
                <td rowspan="2">Retur PF</td>
                <td rowspan="2">Retur GB</td>
                <td rowspan="2">Retur Netto</td>
                <td rowspan="2">TUNAI/KREDIT</td>
                <td rowspan="2">Tanggal Input</td>
                <td rowspan="2">Tanggal Update</td>
            </tr>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td>DUS</td>
                <td>Harga</td>
                <td>PACK</td>
                <td>Harga</td>
                <td>PCS</td>
                <td>Harga</td>
                <td>Subtotal</td>
            </tr>
        </thead>
        <tbody>
            @php
            $no = 1;
            $totaldus = 0;
            $totalpack = 0;
            $totalpcs = 0;
            $totaldus2 = 0;
            $totalpack2 = 0;
            $totalpcs2 = 0;
            $returpf = 0;
            $returgb = 0;
            $netto = 0;
            @endphp

            @foreach ($retur as $r)
            @php

            $jmlbarang = DB::table('detailretur')->where('no_retur_penj',$r->no_retur_penj)->count();
            $barang1 = DB::table('detailretur')
            ->selectRaw('no_fak_penj,detailretur.kode_barang,nama_barang,jumlah,isipcsdus,
            isipack,isipcs,detailretur.harga_dus,detailretur.harga_pack,detailretur.harga_pcs,subtotal ')
            ->join('barang','detailretur.kode_barang','=','barang.kode_barang')
            ->where('no_retur_penj',$r->no_retur_penj)
            ->orderBy('detailretur.kode_barang')
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
            $nama_barang = $barang1->nama_barang;
            $subtotal_1 = $barang1->subtotal;
            }else{
            $jmldus = 0;
            $jmlpack = 0;
            $jmlpcs = 0;
            $nama_barang = "";
            $subtotal_1 = 0;
            }

            $totaldus = $totaldus + $jmldus;
            $totalpack = $totalpack + $jmlpack;
            $totalpcs = $totalpcs + $jmlpcs;
            $returpf = $returpf + $r->subtotal_pf;
            $returgb = $returgb + $r->subtotal_gb;
            $netto = $netto + $r->total;
            @endphp
            <tr>
                <td rowspan="{{ $jmlbarang}}">{{ $loop->iteration}}</td>
                <td rowspan="{{ $jmlbarang }}">{{ DateToIndo2($r->tglretur) }}</td>
                <td rowspan="{{ $jmlbarang }}">{{ $r->no_ref }}</td>
                <td rowspan="{{ $jmlbarang }}">{{ $r->no_fak_penj }}</td>
                <td rowspan="{{ $jmlbarang }}">{{ $r->kode_pelanggan }}</td>
                <td rowspan="{{ $jmlbarang }}">{{ $r->nama_pelanggan }}</td>
                <td rowspan="{{ $jmlbarang }}">{{ $r->pasar }}</td>
                <td rowspan="{{ $jmlbarang }}">{{ $r->hari }}</td>


                <td>
                    {{ $nama_barang }}
                </td>
                <td align="center">
                    @if ($jmldus != 0 )
                    {{ $jmldus }}
                    @endif
                </td>
                <td align="right">
                    @if ($jmldus != 0 )
                    {{ rupiah($barang1->harga_dus) }}
                    @endif
                </td>
                <td align="center">
                    @if ($jmlpack != 0 )
                    {{ $jmlpack }}
                    @endif
                </td>
                <td align="right">
                    @if ($jmlpack != 0 )
                    {{ rupiah($barang1->harga_pack) }}
                    @endif

                </td>
                <td align="center">
                    @if ($jmlpcs !=0 )
                    {{ $jmlpcs }}
                    @endif
                </td>
                <td align="right">
                    @if ($jmlpcs !=0 )
                    {{ rupiah($barang1->harga_pcs) }}
                    @endif
                </td>
                <td align="right">
                    {{ rupiah($subtotal_1) }}
                </td>
                <td align="right" rowspan="{{ $jmlbarang}}">{{ rupiah($r->subtotal_pf)}}</td>
                <td align="right" rowspan="{{ $jmlbarang}}">{{ rupiah($r->subtotal_gb)}}</td>
                <td align="right" rowspan="{{ $jmlbarang}}">{{ rupiah($r->total)}}</td>
                <td align="right" rowspan="{{ $jmlbarang}}">{{ strtoupper($r->jenistransaksi)}}</td>
                <td align="right" rowspan="{{ $jmlbarang}}">{{ $r->date_created}}</td>
                <td align="right" rowspan="{{ $jmlbarang}}">{{ $r->date_updated}}</td>
            </tr>
            @if($jmlbarang > 1)
            @php
            $barang2 = DB::table('detailretur')
            ->selectRaw('no_fak_penj,detailretur.kode_barang,nama_barang,jumlah,isipcsdus,
            isipack,isipcs,detailretur.harga_dus,detailretur.harga_pack,detailretur.harga_pcs,subtotal ')
            ->join('barang','detailretur.kode_barang','=','barang.kode_barang')
            ->where('no_retur_penj',$r->no_retur_penj)
            ->skip(1)->take($jmlbarang)
            ->orderBy('detailretur.kode_barang')
            ->get();
            @endphp
            @else
            @php
            $barang2 = null;
            @endphp
            @endif

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
            @endphp
            <tr>
                <td><?php echo $b2->nama_barang; ?></td>
                <td align='center'><?php if ($jmldus != 0) {echo $jmldus;} ?></td>
                <td align='right'><?php if ($jmldus != 0) {echo number_format($b2->harga_dus, '0', '', '.');} ?></td>
                <td align='center'><?php if ($jmlpack != 0) { echo $jmlpack;} ?></td>
                <td align='right'><?php if ($jmlpack != 0) {echo number_format($b2->harga_pack, '0', '', '.');} ?></td>
                <td align='center'><?php if ($jmlpcs != 0) {echo $jmlpcs;} ?></td>
                <td align='right'><?php if ($jmlpcs != 0) {echo number_format($b2->harga_pcs, '0', '', '.');} ?></td>
                <td align='right'><?php echo number_format($b2->subtotal, '0', '', '.'); ?></td>
            </tr>
            @endforeach
            @endif
            @endforeach
            <tr bgcolor="#024a75" style="color:white; font-weight:bold">
                <td colspan="9">TOTAL</td>
                <td align="center"><?php echo $totaldus + $totaldus2; ?></td>
                <td></td>
                <td align="center"><?php echo $totalpack + $totalpack2; ?></td>
                <td></td>
                <td align="center"><?php echo $totalpcs + $totalpcs2; ?></td>
                <td></td>
                <td></td>
                <td align="right"><?php echo number_format($returpf, '0', '', '.'); ?></td>
                <td align="right"><?php echo number_format($returgb, '0', '', '.'); ?></td>
                <td align="right"><?php echo number_format($netto, '0', '', '.'); ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
