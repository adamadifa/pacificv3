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
        REKAPITULASI PERSEDIAAN BARANG GUDANG<br>
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
    <table class="datatable3">
        <thead>
            <tr>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">TANGGAL</th>
                <th colspan="3" bgcolor="#024a75" style="color:white; font-size:12;">BUKTI</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">KETERANGAN</th>
                <th colspan="2" bgcolor="#28a745" style="color:white; font-size:12;">PENERIMAAN</th>
                <th colspan="2" bgcolor="#c7473a" style="color:white; font-size:12;">PENGELUARAN</th>
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
                <th bgcolor="#c7473a" style="color:white; font-size:12;">DPB</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">TRANSIT OUT</th>
            </tr>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th colspan="4"></th>
                <th>SALDO AWAL</th>
                <th colspan="4"></th>
                <th style="text-align: right"></th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">DUS</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">PACK</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">PCS</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mutasi as $m)
            @php
            if($m->jenis_mutasi=="SURAT JALAN"){
            $ket = "PENERIMAAN PUSAT";
            }elseif($m->jenis_mutasi=="TRANSIT IN"){
            $ket = "TRANSIT IN <b style='color:#23a7e0'>" . $m->no_mutasi_gudang_cabang . "</b>";
            }elseif($m->jenis_mutasi=="TRANSIT OUT"){
            $ket = "TRANSIT OUT <b style='color:#23a7e0'>" . $m->no_mutasi_gudang_cabang . "</b>";
            }
            @endphp
            <td>{{ DateToIndo2($m->tgl_mutasi_gudang_cabang) }}</td>
            <td>
                @if ($m->jenis_mutasi=='SURAT JALAN')
                {{ $m->no_mutasi_gudang_cabang."/".$m->no_dok }}
                @elseif ($m->jenis_mutasi=="TRANSIT IN" OR $m->jenis_mutasi=="TRANSIT OUT")
                {{ !empty($m->no_dok) ? $m->no_suratjalan."/".$m->no_dok : $m->no_suratjalan }}
                @endif
            </td>
            @endforeach
        </tbody>
    </table>
</body>
</html>
