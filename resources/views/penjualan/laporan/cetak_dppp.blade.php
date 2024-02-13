<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Analisa Umur Piutang (AUP) {{ date('d-m-y') }}</title>
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

        a {
            color: white;
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
        @if ($cabang != null)
            @if ($cabang->kode_cabang == 'PST')
                PACIFIC PUSAT
            @else
                PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
            @endif
        @else
            PACIFC ALL CABANG
        @endif
        <br>
        LAPORAN DATA PERTUMBUHAN PRODUK (DPPP)<br>
        {{ $namabulan }} {{ $tahun }}
    </b>
    <br>
    <div id="table-scroll" class="table-scroll">
        <div class="table-wrap">
            <table class="datatable3">
                <thead bgcolor="#295ea9" style="color:white; font-size:14;">
                    <tr>
                        <td rowspan="4" style="background-color:#295ea9; color:white" class="fixed-side"
                            scope="col">#</td>
                        <td rowspan="4" style="background-color:#295ea9; color:white;" class="fixed-side"
                            scope="col">Cabang</td>
                        <td colspan="{{ count($produk) * 10 }}">Produk</td>
                    </tr>
                    <tr style="text-align:center">
                        @foreach ($produk as $p)
                            <td colspan="10">{{ $p->kode_produk }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach ($produk as $p)
                            <td colspan="5" style="background-color: #25b70a;">
                                {{ $namabulan }}
                            </td>
                            <td colspan="5">s/d
                                {{ $namabulan }}
                            </td>
                        @endforeach
                    </tr>
                    <tr style="text-align: center;">
                        @foreach ($produk as $p)
                            <td style="background-color: #25b70a;">Real
                                <?php echo $tahun - 1; ?>
                            </td>
                            <td style="background-color: #25b70a;">Target</td>
                            <td style="background-color: #25b70a;">Realisasi</td>
                            <td style="background-color: #25b70a;">Ach(%)</td>
                            <td style="background-color: #25b70a;">Grw(%)</td>
                            <td>Real
                                <?php echo $tahun - 1; ?>
                            </td>
                            <td>Target</td>
                            <td>Realisasi</td>
                            <td>Ach(%)</td>
                            <td>Grw(%)</td>
                        @endforeach

                    </tr>
                </thead>
                <tbody style="font-size:12;">
                    @foreach ($produk as $p)
                        @php
                            $kode_produk = strtolower($p->kode_produk);
                            ${"total_reallastbulanini_$kode_produk"} = 0;
                            ${"total_reallastsampaibulanini_$kode_produk"} = 0;
                            ${"total_realbulanini_$kode_produk"} = 0;
                            ${"total_realsampaibulanini_$kode_produk"} = 0;
                            ${"total_targetbulanini_$kode_produk"} = 0;
                            ${"total_targetsampaibulanini_$kode_produk"} = 0;
                        @endphp
                    @endforeach
                    @foreach ($dppp as $d)
                        @foreach ($produk as $p)
                            @php
                                $kode_produk = strtolower($p->kode_produk);
                                ${"reallastbulanini_$kode_produk"} = $d->{"reallastbulanini_$kode_produk"};
                                ${"reallastsampaibulanini_$kode_produk"} = $d->{"reallastsampaibulanini_$kode_produk"};
                                ${"realbulanini_$kode_produk"} = $d->{"realbulanini_$kode_produk"};
                                ${"realsampaibulanini_$kode_produk"} = $d->{"realsampaibulanini_$kode_produk"};
                                ${"targetbulanini_$kode_produk"} = $d->{$kode_produk . '_bulanini'};
                                ${"targetsampaibulanini_$kode_produk"} = $d->{$kode_produk . '_sampaibulanini'};

                                ${"total_reallastbulanini_$kode_produk"} += ${"reallastbulanini_$kode_produk"};
                                ${"total_reallastsampaibulanini_$kode_produk"} += ${"reallastsampaibulanini_$kode_produk"};
                                ${"total_realbulanini_$kode_produk"} += ${"realbulanini_$kode_produk"};
                                ${"total_realsampaibulanini_$kode_produk"} += ${"realsampaibulanini_$kode_produk"};
                                ${"total_targetbulanini_$kode_produk"} += ${"targetbulanini_$kode_produk"};
                                ${"total_targetsampaibulanini_$kode_produk"} += ${"targetsampaibulanini_$kode_produk"};

                                if (${"targetbulanini_$kode_produk"} == 0) {
                                    ${'ach_' . $kode_produk . '_bulanini'} = 0;
                                } else {
                                    ${'ach_' . $kode_produk . '_bulanini'} = (${"realbulanini_$kode_produk"} / ${"targetbulanini_$kode_produk"}) * 100;
                                }

                                if (${"reallastbulanini_$kode_produk"} == 0) {
                                    ${'grw_' . $kode_produk . '_bulanini'} = 0;
                                } else {
                                    ${'grw_' . $kode_produk . '_bulanini'} = ((${"realbulanini_$kode_produk"} - ${"reallastbulanini_$kode_produk"}) / ${"reallastbulanini_$kode_produk"}) * 100;
                                }

                                if (${"targetsampaibulanini_$kode_produk"} == 0) {
                                    ${'ach_' . $kode_produk . '_sampaibulanini'} = 0;
                                } else {
                                    ${'ach_' . $kode_produk . '_sampaibulanini'} = (${"realsampaibulanini_$kode_produk"} / ${"targetsampaibulanini_$kode_produk"}) * 100;
                                }

                                if (${"reallastsampaibulanini_$kode_produk"} == 0) {
                                    ${'grw_' . $kode_produk . '_sampaibulanini'} = 0;
                                } else {
                                    ${'grw_' . $kode_produk . '_sampaibulanini'} = ((${"realsampaibulanini_$kode_produk"} - ${"reallastsampaibulanini_$kode_produk"}) / ${"reallastsampaibulanini_$kode_produk"}) * 100;
                                }

                                // if (empty(${"reallastsampaibulanini_$kode_produk"})) {
                                //     ${'grw_' . $kode_produk . '_sampaibulanini'} = 0;
                                // } else {
                                //     ${'grw_' . $kode_produk . '_sampaibulanini'} = ((${"realsampaibulanini_$kode_produk"} - ${"reallastsampaibulanini_$kode_produk"}) / ${"reallastsampaibulanini_$kode_produk"}) * 100;
                                // }

                            @endphp
                        @endforeach
                        <tr>
                            <td class="fixed-side" scope="col">
                                <?php echo $loop->iteration; ?>
                            </td>
                            <td class="fixed-side" scope="col">
                                <?php echo strtoupper($d->nama_cabang); ?>
                            </td>
                            @foreach ($produk as $p)
                                @php
                                    $kode_produk = strtolower($p->kode_produk);
                                @endphp
                                <td align="right">
                                    {{ desimal(${"reallastbulanini_$kode_produk"}) }}
                                </td>
                                <td align="right">{{ desimal(${"targetbulanini_$kode_produk"}) }}</td>
                                <td align="right">{{ desimal(${"realbulanini_$kode_produk"}) }}</td>
                                <td align="right">{{ desimal(${'ach_' . $kode_produk . '_bulanini'}) }}</td>
                                <td align="right">{{ desimal(${'grw_' . $kode_produk . '_bulanini'}) }}</td>
                                <td align="right" style="background-color: #e2e2e2;">
                                    {{ desimal(${"reallastsampaibulanini_$kode_produk"}) }}
                                </td>
                                <td align="right" style="background-color: #e2e2e2;">
                                    {{ desimal(${"targetsampaibulanini_$kode_produk"}) }}
                                </td>
                                <td align="right" style="background-color: #e2e2e2;">
                                    {{ desimal(${"realsampaibulanini_$kode_produk"}) }}
                                </td>
                                <td align="right" style="background-color: #e2e2e2;">
                                    {{ desimal(${'ach_' . $kode_produk . '_sampaibulanini'}) }}
                                </td>
                                <td align="right">
                                    {{ desimal(${'grw_' . $kode_produk . '_sampaibulanini'}) }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
                <tfoot style="font-size:16px; font-weight:bold">
                    <tr>
                        <th colspan="2" class="fixed-side">TOTAL</th>
                        {{-- ${"total_reallastbulanini_$kode_produk"} = 0;
                            ${"total_reallastsampaibulanini_$kode_produk"} = 0;
                            ${"total_realbulanini_$kode_produk"} = 0;
                            ${"total_realsampaibulanini_$kode_produk"} = 0;
                            ${"total_targetbulanini_$kode_produk"} = 0;
                            ${"total_targetsampaibulanini_$kode_produk"} = 0; --}}
                        @foreach ($produk as $p)
                            @php
                                $kode_produk = strtolower($p->kode_produk);
                                if (${"total_targetbulanini_$kode_produk"} == 0) {
                                    ${'total_ach_' . $kode_produk . '_bulanini'} = 0;
                                } else {
                                    ${'total_ach_' . $kode_produk . '_bulanini'} = (${"total_realbulanini_$kode_produk"} / ${"total_targetbulanini_$kode_produk"}) * 100;
                                }

                                if (${"total_reallastbulanini_$kode_produk"} == 0) {
                                    ${'total_grw_' . $kode_produk . '_bulanini'} = 0;
                                } else {
                                    ${'total_grw_' . $kode_produk . '_bulanini'} = ((${"total_realbulanini_$kode_produk"} - ${"total_reallastbulanini_$kode_produk"}) / ${"total_reallastbulanini_$kode_produk"}) * 100;
                                }

                                if (${"total_targetsampaibulanini_$kode_produk"} == 0) {
                                    ${'total_ach_' . $kode_produk . '_sampaibulanini'} = 0;
                                } else {
                                    ${'total_ach_' . $kode_produk . '_sampaibulanini'} = (${"total_realsampaibulanini_$kode_produk"} / ${"total_targetsampaibulanini_$kode_produk"}) * 100;
                                }

                                if (${"total_reallastsampaibulanini_$kode_produk"} == 0) {
                                    ${'total_grw_' . $kode_produk . '_sampaibulanini'} = 0;
                                } else {
                                    ${'total_grw_' . $kode_produk . '_sampaibulanini'} = ((${"total_realsampaibulanini_$kode_produk"} - ${"total_reallastsampaibulanini_$kode_produk"}) / ${"total_reallastsampaibulanini_$kode_produk"}) * 100;
                                }

                            @endphp
                            <th align="right">{{ desimal(${"total_reallastbulanini_$kode_produk"}) }}</th>
                            <th align="right">{{ desimal(${"total_targetbulanini_$kode_produk"}) }}</th>
                            <th align="right">{{ desimal(${"total_realbulanini_$kode_produk"}) }}</th>
                            <th align="right">{{ desimal(${'total_ach_' . $kode_produk . '_bulanini'}) }}</th>
                            <th align="right">{{ desimal(${'total_grw_' . $kode_produk . '_bulanini'}) }}</th>






                            <th align="right">{{ desimal(${"total_reallastsampaibulanini_$kode_produk"}) }}</th>
                            <th align="right">{{ desimal(${"total_targetsampaibulanini_$kode_produk"}) }}</th>
                            <th align="right">{{ desimal(${"total_realsampaibulanini_$kode_produk"}) }}</th>

                            <th align="right">{{ desimal(${'total_ach_' . $kode_produk . '_sampaibulanini'}) }}</th>
                            <th align="right">{{ desimal(${'total_grw_' . $kode_produk . '_sampaibulanini'}) }}</th>
                        @endforeach
                    </tr>
            </table>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script>
        // requires jquery library
        jQuery(document).ready(function() {
            jQuery(".datatable3").clone(true).appendTo('#table-scroll').addClass('clone');
        });
    </script>
</body>
