<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Setoran Penjualan {{ date("d-m-y") }}</title>
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
        REKAP HARIAN KAS BESAR LAPORAN HARIAN PENJUALAN
        <br>
        @if ($cabang->kode_cabang=="PST")
        PACIFIC PUSAT
        @else
        PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
        @endif
        <br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        <table class="datatable3" border="1">
            <thead style=" background-color:#dce6f1; font-size:12;">
                <tr style=" background-color:#dce6f1; font-size:12;">
                    <th rowspan="2" style="color:red">TGL LHP</th>
                    <th rowspan="2">SALES</th>
                    <th rowspan="2">PENJUALAN TUNAI</th>
                    <th rowspan="2">TAGIHAN</th>
                    <th rowspan="2" style="color:red">TOTAL LHP</th>
                    <th colspan="4">SETORAN</th>
                    <th rowspan="2" style="color:red">TOTAL SETORAN</th>
                    <th rowspan="2" style="background-color:red; color:white">SELISIH</th>
                    <th rowspan="2">KETERANGAN</th>
                </tr>
                <tr style=" background-color:#dce6f1; font-size:12;">
                    <th>U.KERTAS</th>
                    <th>U.LOGAM</th>
                    <th>TRANSFER</th>
                    <th>BG/CEK</th>
                </tr>
                <tr style=" background-color:#31869b;">
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @php
                $totaltunai = 0;
                $totaltagihan = 0;
                $totallhppertgl = 0;
                $totalsetorankertas = 0;
                $totalsetoranlogam = 0;
                $totalsetoranbg = 0;
                $totalsetorantransfer = 0;
                $totalsetoranpertgl = 0;
                $totalselisih = 0;

                $grandtotaltunai = 0;
                $grandtotaltagihan = 0;
                $grandtotallhppertgl = 0;
                $grandtotalsetorankertas = 0;
                $grandtotalsetoranlogam = 0;
                $grandtotalsetoranbg = 0;
                $grandtotalsetorantransfer = 0;
                $grandtotalsetoranpertgl = 0;
                $grandtotalselisih = 0;
                $grandtotalsetoran =0;
                $grandtotalselisih =0;

                @endphp
                @foreach ($setoranpenjualan as $key => $d)
                @php
                $totaltunai = $totaltunai + $d->lhp_tunai;
                $totaltagihan = $totaltagihan +$d->lhp_tagihan;
                $tglcek = @$setoranpenjualan[$key + 1]->tgl_lhp;
                $tanggal = explode("-", $d->tgl_lhp);
                $ceksetorantunai = $d->cektunai;
                $setorantagihan = $d->cekkredit;
                $setorangiro = $d->ceksetorangiro;
                $setorantransfer = $d->ceksetorantransfer;
                $setoranalltagihan = $d->cekkredit + $d->ceksetorangiro + $d->ceksetorantransfer;
                $girotocash = $d->cekgirotocash;
                $convertgiro = $d->girotocash + $d->girototransfer;
                //echo $girotocash;
                //Penyelesaian Kurang lebih Setor
                $uk = $d->kurangsetorkertas - $d->lebihsetorkertas;
                $ul = $d->kurangsetorlogam - $d->lebihsetorlogam;
                $totallhp = $d->lhp_tunai + $d->lhp_tagihan;
                if ($uk > 0) {
                $opkertas = "+";
                } else {
                $opkertas = "+";
                }

                if ($ul > 0) {
                $oplogam = "+";
                } else {
                $oplogam = "+";
                }

                $totalsetoran = $d->setoran_kertas + $uk + $d->setoran_logam + $ul + $d->setoran_bg + $d->setoran_transfer;
                $selisih = $totalsetoran - $totallhp;
                $kontenkertas = number_format($d->setoran_kertas, '0', '', '.') . $opkertas . number_format($uk, '0', '', '.');
                $kontenlogam = number_format($d->setoran_logam, '0', '', '.') . $oplogam . number_format($ul, '0', '', '.');


                if ($d->cektunai == $d->lhp_tunai) {
                $colorsetorantunai = "bg-success text-white";
                } else {
                $colorsetorantunai = "bg-danger text-white";
                }

                if ($setoranalltagihan == $d->lhp_tagihan) {
                $colorsetorantagihan = "bg-success text-white";
                } else {
                $colorsetorantagihan = "bg-danger text-white";
                }

                if ($d->cektunai == $d->lhp_tunai && $setoranalltagihan == $d->lhp_tagihan && $girotocash == $convertgiro) {
                $colortotallhp = "bg-success text-white";
                } else {
                $colortotallhp = "bg-danger text-white";
                }

                $totallhppertgl = $totallhppertgl + $totallhp;
                $totalsetorankertas = $totalsetorankertas + ($d->setoran_kertas + $uk);
                $totalsetoranlogam = $totalsetoranlogam + ($d->setoran_logam + $ul);
                $totalsetoranbg = $totalsetoranbg + $d->setoran_bg;
                $totalsetorantransfer = $totalsetorantransfer + $d->setoran_transfer;
                $totalsetoranpertgl = $totalsetoranpertgl + $totalsetoran;

                if($loop->iteration % 2){
                $position = "right";
                }else{
                $position = "left";
                }

                $selisih = $totalsetoran-$totallhp;
                $totalselisih = $totalselisih + $selisih;
                $grandtotalselisih += $selisih;
                $grandtotaltunai = $grandtotaltunai + $d->lhp_tunai;
                $grandtotaltagihan = $grandtotaltagihan +$d->lhp_tagihan;
                $grandtotallhppertgl = $grandtotallhppertgl + $totallhp;
                $grandtotalsetorankertas += ($d->setoran_kertas + $uk);
                $grandtotalsetoranlogam +=($d->setoran_logam + $ul);
                $grandtotalsetoranbg +=$d->setoran_bg;
                $grandtotalsetorantransfer +=$d->setoran_transfer;
                $grandtotalsetoran += $totalsetoran;
                @endphp
                <tr>
                    <td>{{ date("d-m-Y",strtotime($d->tgl_lhp))  }}</td>
                    <td>{{ ucwords(strtolower($d->nama_karyawan)) }}</td>
                    <td style="text-align:right">{{ rupiah($d->lhp_tunai) }}</td>
                    <td style="text-align:right">{{ rupiah($d->lhp_tagihan) }}</td>
                    <td style="text-align:right"><u><a class="text-white" target="_blank" href=" /setoranpenjualan/detailsetoran?kode_cabang={{ $d->kode_cabang }}&tgl_lhp={{ $d->tgl_lhp }}&id_karyawan={{ $d->id_karyawan }}">{{ rupiah($totallhp) }}</a></u></td>
                    <td style="text-align:right"><a href="#" class="detailkertas" data-toggle="popover" data-placement="{{ $position }}" data-container="body" data-original-title="Keterangan" data-content="{{ $kontenkertas }}">{{ !empty($d->setoran_kertas + $uk) ? rupiah($d->setoran_kertas + $uk) : '' }}</a></td>
                    <td style="text-align:right"><a href="#" class="detaillogam" data-toggle="popover" data-placement="{{ $position }}" data-container="body" data-original-title="Keterangan" data-content="{{ $kontenlogam }}">{{ !empty($d->setoran_logam + $ul) ? rupiah($d->setoran_logam + $ul) : '' }}</a></td>
                    <td style="text-align:right">{{ !empty($d->setoran_transfer) ? rupiah($d->setoran_transfer) : '' }}</td>
                    <td style="text-align:right">{{ !empty($d->setoran_bg) ? rupiah($d->setoran_bg) : '' }}</td>
                    <td style="text-align:right">{{ rupiah($totalsetoran) }}</td>
                    <td style="text-align:right">{{ rupiah($selisih) }}</td>
                    <td>{{ $d->keterangan }}</td>
                </tr>
                @php
                if ($tglcek != $d->tgl_lhp) {

                echo "<tr bgcolor='#31869b' style='color:white; font-weight:bold'>
                    <td colspan='2'>TOTAL</td>
                    <td style='text-align:right'>" . number_format($totaltunai, '0', '', '.') . "</td>
                    <td style='text-align:right'>" . number_format($totaltagihan, '0', '', '.') . "</td>
                    <td style='text-align:right'>" . number_format($totallhppertgl, '0', '', '.') . "</td>
                    <td style='text-align:right'>" . number_format($totalsetorankertas, '0', '', '.') . "</td>
                    <td style='text-align:right'>" . number_format($totalsetoranlogam, '0', '', '.') . "</td>
                    <td style='text-align:right'>" . number_format($totalsetorantransfer, '0', '', '.') . "</td>
                    <td style='text-align:right'>" . number_format($totalsetoranbg, '0', '', '.') . "</td>
                    <td style='text-align:right'>" . number_format($totalsetoranpertgl, '0', '', '.') . "</td>
                    <td style='text-align:right'>" . number_format($totalselisih, '0', '', '.') . "</td>
                    <td></td>
                </tr>";

                $totaltunai = 0;
                $totaltagihan = 0;
                $totallhppertgl = 0;
                $totalsetorankertas = 0;
                $totalsetoranlogam = 0;
                $totalsetoranbg = 0;
                $totalsetorantransfer = 0;
                $totalsetoranpertgl = 0;
                $totalselisih = 0;
                }
                @endphp
                @endforeach
                <tr bgcolor='#31869b' style='color:white; font-weight:bold'>
                    <td colspan='2'>TOTAL</td>
                    <td style='text-align:right'>{{ rupiah($grandtotaltunai) }}</td>
                    <td style='text-align:right'>{{ rupiah($grandtotaltagihan) }}</td>
                    <td style='text-align:right'>{{ rupiah($grandtotallhppertgl) }}</td>
                    <td style='text-align:right'>{{ rupiah($grandtotalsetorankertas) }}</td>
                    <td style='text-align:right'>{{ rupiah($grandtotalsetoranlogam) }}</td>
                    <td style='text-align:right'>{{ rupiah($grandtotalsetoranbg) }}</td>
                    <td style='text-align:right'>{{ rupiah($grandtotalsetorantransfer) }}</td>
                    <td style='text-align:right'>{{ rupiah($grandtotalsetoran) }}</td>
                    <td style='text-align:right'>{{ rupiah($grandtotalselisih) }}</td>
                    <td></td>

                </tr>
            </tbody>
        </table>
</body>
