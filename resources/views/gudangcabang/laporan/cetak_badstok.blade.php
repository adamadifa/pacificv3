<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Bad Stok Gudang Cabang {{ date('d-m-y') }}</title>
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
        REKAPITULASI BAD STOK BARANG<br>
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
    <table class="datatable3" style="width:100%" border="1">
        <thead>
            <tr>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">TANGGAL</th>
                <th colspan="2" bgcolor="#024a75" style="color:white; font-size:12;">BUKTI</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">KETERANGAN</th>
                <th colspan="4" bgcolor="#28a745" style="color:white; font-size:12;">PENERIMAAN</th>
                <th colspan="3" bgcolor="#c7473a" style="color:white; font-size:12;">PENGELUARAN</th>
                <th rowspan="2" bgcolor="#024a75" style="color:white; font-size:12;">SALDO AKHIR</th>
                <th rowspan="3" bgcolor="#024a75" style="color:white; font-size:12;">TANGGAL INPUT</th>
                <th rowspan="3" bgcolor="#024a75" style="color:white; font-size:12;">TANGGAL UPDATE</th>
            </tr>
            <tr>
                <th bgcolor="#024a75" style="color:white; font-size:12;">REPACK/REJECT</th>
                <th bgcolor="#024a75" style="color:white; font-size:12;">LAIN LAIN</th>
                <th bgcolor="#28a745" style="color:white; font-size:12;">REJECT PASAR</th>
                <th bgcolor="#28a745" style="color:white; font-size:12;">REJECT MOBIL</th>
                <th bgcolor="#28a745" style="color:white; font-size:12;">REJECT GUDANG</th>
                <th bgcolor="#28a745" style="color:white; font-size:12;">PENYESUAIAN/LAINLAIN</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">KIRIM KE PUSAT</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">REPACK</th>
                <th bgcolor="#c7473a" style="color:white; font-size:12;">PENYESUAIAN/LAINLAIN</th>
            </tr>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th colspan="4"></th>
                <th>SALDO AWAL</th>
                <th colspan="6"></th>
                <th style="text-align: right"><?php echo desimal($saldoawal); ?></th>
            </tr>
        </thead>
        <tbody>
            @php
                $saldoakhir = $saldoawal;
                $totalreject_pasar = 0;
                $totalreject_gudang = 0;
                $totalreject_mobil = 0;
                $total_penybad_in = 0;
                $total_penybad_out = 0;
                $totalkirimpusat = 0;
                $totalrepack = 0;

            @endphp
            @foreach ($mutasi as $d)
                @php
                    if (
                        $d->jenis_mutasi == 'REPACK' or
                        $d->jenis_mutasi == 'REJECT GUDANG' or
                        $d->jenis_mutasi == 'REJECT PASAR' or
                        $d->jenis_mutasi == 'REJECT MOBIL'
                    ) {
                        $no_repackreject = $d->no_mutasi_gudang_cabang;
                        $no_dpb = $d->no_dpb;
                    } else {
                        $no_repackreject = '';
                        $no_dpb = $d->no_mutasi_gudang_cabang;
                    }

                    if ($d->jenis_mutasi == 'KIRIM PUSAT') {
                        $ket = 'PENYERAHAN BS KE PUSAT';
                    } elseif ($d->jenis_mutasi == 'PENYESUAIAN BAD') {
                        $ket = $d->keterangan;
                    } else {
                        $ket = $d->jenis_mutasi;
                    }

                    if ($d->jenis_mutasi == 'PENYESUAIAN BAD') {
                        if ($d->inout_bad == 'OUT') {
                            $jml_penybad_out = $d->penyesuaian_bad;
                            $jml_penybad_in = 0;
                        } else {
                            $jml_penybad_out = 0;
                            $jml_penybad_in = $d->penyesuaian_bad;
                        }
                    } else {
                        $jml_penybad_out = 0;
                        $jml_penybad_in = 0;
                    }
                    $reject_pasar = $d->reject_pasar / $d->isipcsdus;
                    $reject_mobil = $d->reject_mobil / $d->isipcsdus;
                    $reject_gudang = $d->reject_gudang / $d->isipcsdus;
                    $jml_penybad_in = $jml_penybad_in / $d->isipcsdus;
                    $jml_penybad_out = $jml_penybad_out / $d->isipcsdus;
                    $kirim_pusat = $d->kirim_pusat / $d->isipcsdus;
                    $repack = $d->repack / $d->isipcsdus;

                    $penerimaan = $reject_pasar + $reject_mobil + $reject_gudang + $jml_penybad_in;
                    $pengeluaran = $jml_penybad_out + $kirim_pusat + $repack;
                    $saldoakhir = $saldoakhir + $penerimaan - $pengeluaran;

                    $totalreject_pasar += $reject_pasar;
                    $totalreject_gudang += $reject_gudang;
                    $totalreject_mobil += $reject_mobil;
                    $total_penybad_in += $jml_penybad_in;
                    $total_penybad_out += $jml_penybad_out;
                    $totalkirimpusat += $kirim_pusat;
                    $totalrepack += $repack;
                    if ($d->inout_bad == 'IN') {
                        $color_sa = '#28a745';
                    } else {
                        $color_sa = '#c7473a';
                    }
                @endphp
                <tr>
                    <td>{{ DateToIndo2($d->tgl_mutasi_gudang_cabang) }}</td>
                    <td>{{ $no_repackreject }}</td>
                    <td>{{ $no_dpb }}</td>
                    <td>{{ $ket }}</td>
                    <td align="right">{{ !empty($reject_pasar) ? desimal($reject_pasar) : '' }}</td>
                    <td align="right">{{ !empty($reject_mobil) ? desimal($reject_mobil) : '' }}</td>
                    <td align="right">{{ !empty($reject_gudang) ? desimal($reject_gudang) : '' }}</td>
                    <td align="right">{{ !empty($jml_penybad_in) ? desimal($jml_penybad_in) : '' }}</td>
                    <td align="right">{{ !empty($kirim_pusat) ? desimal($kirim_pusat) : '' }}</td>
                    <td align="right">{{ !empty($repack) ? desimal($repack) : '' }}</td>
                    <td align="right">{{ !empty($jml_penybad_out) ? desimal($jml_penybad_out) : '' }}</td>
                    <td align="right" style="background-color:{{ $color_sa }}">{{ desimal($saldoakhir) }}</td>
                    <td>{{ date('d-m-y H:i:s', strtotime($d->date_created)) }}</td>
                    <td>{{ !empty($d->date_updated) ? date('d-m-y H:i:s', strtotime($d->date_updated)) : '' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th colspan="4">TOTAL</th>
                <th style="text-align: right"><?php echo desimal($totalreject_pasar); ?></th>
                <th style="text-align: right"><?php echo desimal($totalreject_mobil); ?></th>
                <th style="text-align: right"><?php echo desimal($totalreject_gudang); ?></th>
                <th style="text-align: right"><?php echo desimal($total_penybad_in); ?></th>
                <th style="text-align: right"><?php echo desimal($totalkirimpusat); ?></th>
                <th style="text-align: right"><?php echo desimal($totalrepack); ?></th>
                <th style="text-align: right"><?php echo desimal($total_penybad_out); ?></th>
                <th style="text-align: right"><?php echo desimal($saldoakhir); ?></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>

</body>

</html>
