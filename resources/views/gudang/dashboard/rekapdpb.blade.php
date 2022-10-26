@php
$hariini = date("Y-m-d");
@endphp
@foreach ($barang as $p)
@php
if ($p->kode_produk == "AB") {
$isipcs_ab = $p->isipcsdus;
}
if ($p->kode_produk == "AR") {
$isipcs_ar = $p->isipcsdus;
}
if ($p->kode_produk == "AS") {
$isipcs_as = $p->isipcsdus;
}

if ($p->kode_produk == "BB") {
$isipcs_bb = $p->isipcsdus;
}

if ($p->kode_produk == "BBP") {
$isipcs_bbp = $p->isipcsdus;
}

if ($p->kode_produk == "CG") {
$isipcs_cg = $p->isipcsdus;
}

if ($p->kode_produk == "CGG") {
$isipcs_cgg = $p->isipcsdus;
}

if ($p->kode_produk == "CG5") {
$isipcs_cg5 = $p->isipcsdus;
}

if ($p->kode_produk == "DEP") {
$isipcs_dep = $p->isipcsdus;
}

if ($p->kode_produk == "DS") {
$isipcs_ds = $p->isipcsdus;
}

if ($p->kode_produk == "SP") {
$isipcs_sp = $p->isipcsdus;
}

if ($p->kode_produk == "SPP") {
$isipcs_spp = $p->isipcsdus;
}

if ($p->kode_produk == "SC") {
$isipcs_sc = $p->isipcsdus;
}

if ($p->kode_produk == "SP8") {
$isipcs_sp8 = $p->isipcsdus;
}

if ($p->kode_produk == "SP8-P") {
$isipcs_sp8p = $p->isipcsdus;
}
@endphp
@endforeach
<div class="table-responsive">

    <table class="table table-hover-animation">
        <thead>
            <tr>
                <th>Nama Cabang</th>
                <th>AB</th>
                <th>AR</th>
                <th>AS</th>
                <th>BB</th>
                <!-- <th>BBP</th> -->
                <!-- <th>CG</th> -->
                <!-- <th>CG5</th> -->
                <!-- <th>CGG</th> -->
                <th>DEP</th>
                <!-- <th>DK</th> -->
                {{-- <th>DS</th> --}}
                <th>SP</th>
                {{-- <th>SPP</th> --}}
                <th>SC</th>
                <th>SP8</th>
                <th>SP8-P</th>
            </tr>
        </thead>
        <tbody class="font-medium-2">
            @php
            $gAB = 0;
            $gAR = 0;
            $gAS = 0;
            $gBB = 0;
            $gBBP = 0;
            $gCG = 0;
            $gCG5 = 0;
            $gCGG = 0;
            $gDEP = 0;
            $gDK = 0;
            $gDS = 0;
            $gSP = 0;
            $gSPP = 0;
            $gSC = 0;
            $gSP8 = 0;
            $gSP8P = 0;
            @endphp
            @foreach ($rekapgudang as $g)
            @php
            if ($g->kode_produk == "AB") {
            $gAB = $gAB + $g->saldoakhir;
            }

            if ($g->kode_produk == "AR") {
            $gAR = $gAR + $g->saldoakhir;
            }

            if ($g->kode_produk == "AS") {
            $gAS = $gAS + $g->saldoakhir;
            }

            if ($g->kode_produk == "BB") {
            $gBB = $gBB + $g->saldoakhir;
            }

            if ($g->kode_produk == "BBP") {
            $gBBP = $gBBP + $g->saldoakhir;
            }

            // if ($g->kode_produk == "CG") {
            // $gCG = $gCG + $g->saldoakhir;
            // }

            // if ($g->kode_produk == "CG5") {
            // $gCG = $gCG + $g->saldoakhir;
            // }

            // if ($g->kode_produk == "CGG") {
            // $gCGG = $gCGG + $g->saldoakhir;
            // }

            if ($g->kode_produk == "DEP") {
            $gDEP = $gDEP + $g->saldoakhir;
            }

            // if ($g->kode_produk == "DK") {
            // $gDK = $gDK + $g->saldoakhir;
            // }

            if ($g->kode_produk == "DS") {
            $gDS = $gDS + $g->saldoakhir;
            }

            if ($g->kode_produk == "SP") {
            $gSP = $gSP + $g->saldoakhir;
            }

            if ($g->kode_produk == "SPP") {
            $gSPP = $gSPP + $g->saldoakhir;
            }

            if ($g->kode_produk == "SC") {
            $gSC = $gSC + $g->saldoakhir;
            }

            if ($g->kode_produk == "SP8") {
            $gSP8 = $gSP8 + $g->saldoakhir;
            }

            if ($g->kode_produk == "SP8-P") {
            $gSP8P = $gSP8P + $g->saldoakhir;
            }
            @endphp
            @endforeach
            <?php
            if ($gAB <= 0) {
                $colorgAB = "bg-danger";
            } else {
                $colorgAB = "bg-success";
            }
            if ($gAS <= 0) {
                $colorgAS = "bg-danger";
            } else {
                $colorgAS = "bg-success";
            }

            if ($gAR <= 0) {
                $colorgAR = "bg-danger";
            } else {
                $colorgAR = "bg-success";
            }

            if ($gAS <= 0) {
                $colorgAS = "bg-danger";
            } else {
                $colorgAS = "bg-success";
            }

            if ($gBB <= 0) {
                $colorgBB = "bg-danger";
            } else {
                $colorgBB = "bg-success";
            }

            if ($gBBP <= 0) {
                $colorgBBP = "bg-danger";
            } else {
                $colorgBBP = "bg-success";
            }

            // if ($gCG <= 0) {
            //   $colorgCG = "bg-danger";
            // } else {
            //   $colorgCG = "bg-success";
            // }

            // if ($gCG5 <= 0) {
            //   $colorgCG5 = "bg-danger";
            // } else {
            //   $colorgCG5 = "bg-success";
            // }

            // if ($gCGG <= 0) {
            //   $colorgCGG = "bg-danger";
            // } else {
            //   $colorgCGG = "bg-success";
            // }

            if ($gDEP <= 0) {
                $colorgDEP = "bg-danger";
            } else {
                $colorgDEP = "bg-success";
            }

            // if ($gDK <= 0) {
            //   $colorgDK = "bg-danger";
            // } else {
            //   $colorgDK = "bg-success";
            // }

            if ($gDS <= 0) {
                $colorgDS = "bg-danger";
            } else {
                $colorgDS = "bg-success";
            }

            if ($gSP <= 0) {
                $colorgSP = "bg-danger";
            } else {
                $colorgSP = "bg-success";
            }

            if ($gSPP <= 0) {
                $colorgSPP = "bg-danger";
            } else {
                $colorgSPP = "bg-success";
            }


            if ($gSC <= 0) {
                $colorgSC = "bg-danger";
            } else {
                $colorgSC = "bg-success";
            }

            if ($gSP8 <= 0) {
                $colorgSP8 = "bg-danger";
            } else {
                $colorgSP8 = "bg-success";
            }

            if ($gSP8P <= 0) {
                $colorgSP8P = "bg-danger";
            } else {
                $colorgSP8P = "bg-success";
            }
           ?>
            <tr>
                <td>Gudang Pusat</td>
                <td><span class="badge <?php echo $colorgAB; ?>"><?php echo number_format(floor($gAB), '0', ',', '.'); ?></span></td>
                <td><span class="badge <?php echo $colorgAR; ?>"><?php echo number_format(floor($gAR), '0', ',', '.'); ?></span></td>
                <td><span class="badge <?php echo $colorgAS; ?>"><?php echo number_format(floor($gAS), '0', ',', '.'); ?></span></td>
                <td><span class="badge <?php echo $colorgBB; ?>"><?php echo number_format(floor($gBB), '0', ',', '.'); ?></span></td>

                <td><span class="badge <?php echo $colorgDEP; ?>"><?php echo number_format(floor($gDEP), '0', ',', '.'); ?></span></td>

                {{-- <td><span class="badge <?php echo $colorgDS; ?>"><?php echo number_format(floor($gDS), '0', ',', '.'); ?></span></td> --}}
                <td><span class="badge <?php echo $colorgSP; ?>"><?php echo number_format(floor($gSP), '0', ',', '.'); ?></span></td>
                {{-- <td><span class="badge <?php echo $colorgSPP; ?>"><?php echo number_format(floor($gSPP), '0', ',', '.'); ?></span></td> --}}
                <td><span class="badge <?php echo $colorgSC; ?>"><?php echo number_format(floor($gSC), '0', ',', '.'); ?></span></td>
                <td><span class="badge <?php echo $colorgSP8; ?>"><?php echo number_format(floor($gSP8), '0', ',', '.'); ?></span></td>
                <td><span class="badge <?php echo $colorgSP8; ?>"><?php echo number_format(floor($gSP8P), '0', ',', '.'); ?></span></td>
            </tr>
            @foreach ($rekapdpb as $d)
            <?php
                $sab = $d['mg_ab'] + (ROUND($d['saldo_ab'] / 30, 2)) + (ROUND($d['mutasi_ab'] / 30, 2)) - $d['ab_ambil'] + $d['ab_kembali'];
                $sar = $d['mg_ar'] + (ROUND($d['saldo_ar'] / 240, 2)) + (ROUND($d['mutasi_ar'] / 240, 2)) - $d['ar_ambil'] + $d['ar_kembali'];
                $sas = $d['mg_as'] + (ROUND($d['saldo_as'] / 36, 2)) + (ROUND($d['mutasi_as'] / 36, 2)) - $d['as_ambil'] + $d['as_kembali'];
                $sbb = $d['mg_bb'] + (ROUND($d['saldo_bb'] / 20, 2)) + (ROUND($d['mutasi_bb'] / 20, 2)) - $d['bb_ambil'] + $d['bb_kembali'];
                $sdep = $d['mg_dep'] + (ROUND($d['saldo_dep'] / 20, 2)) + (ROUND($d['mutasi_dep'] / 20, 2)) - $d['dep_ambil'] + $d['dep_kembali'];
                $sds  = $d['mg_ds'] + (ROUND($d['saldo_ds'] / 504, 2)) + (ROUND($d['mutasi_ds'] / 504, 2)) - $d['ds_ambil'] + $d['ds_kembali'];
                $ssp  = $d['mg_sp'] + (ROUND($d['saldo_sp'] / 12, 2)) + (ROUND($d['mutasi_sp'] / 12, 2)) - $d['sp_ambil'] + $d['sp_kembali'];
                $sspp  = $d['mg_spp'] + (ROUND($d['saldo_spp'] / 1, 2)) + (ROUND($d['mutasi_spp'] / 1, 2)) - $d['spp_ambil'] + $d['spp_kembali'];
                $ssc  = $d['mg_sc'] + (ROUND($d['saldo_sc'] / 24, 2)) + (ROUND($d['mutasi_sc'] / 24, 2)) - $d['sc_ambil'] + $d['sc_kembali'];
                $ssp8  = $d['mg_sp8'] + (ROUND($d['saldo_sp8'] / 480, 2)) + (ROUND($d['mutasi_sp8'] / 480, 2)) - $d['sp8_ambil'] + $d['sp8_kembali'];
                $ssp8p  = $d['mg_sp8p'] + (ROUND($d['saldo_sp8p'] / 480, 2)) + (ROUND($d['mutasi_sp8p'] / 480, 2)) - $d['sp8p_ambil'] + $d['sp8p_kembali'];

                if ($sab <= 0) {
                $colorab = "bg-danger";
                } else {
                $colorab = "bg-success";
                }

                if ($sar <= 0) {
                $colorar = "bg-danger";
                } else {
                $colorar = "bg-success";
                }

                if ($sas <= 0) {
                $coloras = "bg-danger";
                } else {
                $coloras = "bg-success";
                }

                if ($sbb <= 0) {
                $colorbb = "bg-danger";
                } else {
                $colorbb = "bg-success";
                }


                if ($sdep <= 0) {
                $colorsdep = "bg-danger";
                } else {
                $colorsdep = "bg-success";
                }


                if ($sds <= 0) {
                $colorsds = "bg-danger";
                } else {
                $colorsds = "bg-success";
                }

                if ($ssp <= 0) {
                $colorssp = "bg-danger";
                } else {
                $colorssp = "bg-success";
                }

                if ($sspp <= 0) {
                $colorsspp = "bg-danger";
                } else {
                $colorsspp = "bg-success";
                }

                if ($ssc <= 0) {
                $colorssc = "bg-danger";
                } else {
                $colorssc = "bg-success";
                }

                if ($ssp8 <= 0) {
                $colorsp8 = "bg-danger";
                } else {
                $colorsp8 = "bg-success";
                }

                if ($ssp8p <= 0) {
                $colorsp8p = "bg-danger";
                } else {
                $colorsp8p = "bg-success";
                }

                if ($sab < 0) {
                $sab = 0;
                }

                if ($sar < 0) {
                $sar = 0;
                }

                if ($sas < 0) {
                $sas = 0;
                }

                if ($sbb < 0) {
                $sbb = 0;
                }

                if ($sdep < 0) {
                $sdep = 0;
                }

                if ($sds < 0) {
                $sds = 0;
                }

                if ($ssp < 0) {
                $ssp = 0;
                }

                if ($sspp < 0) {
                $sspp = 0;
                }

                if ($ssc < 0) {
                $ssc = 0;
                }

                if ($ssp8 < 0) {
                $ssp8 = 0;
                }

                if ($ssp8p < 0) {
                $ssp8p = 0;
                }
            ?>
            <tr>
                <td><?php echo ucwords($d['nama_cabang']); ?></td>
                <td><span class="badge <?php echo $colorab; ?>"><?php echo number_format(floor($sab), '0', ',', '.'); ?></span></td>
                <td><span class="badge <?php echo $colorar; ?>"><?php echo number_format(floor($sar), '0', ',', '.'); ?></span></td>
                <td><span class="badge <?php echo $coloras; ?>"><?php echo number_format(floor($sas), '0', ',', '.'); ?></span></td>
                <td><span class="badge <?php echo $colorbb; ?>"><?php echo number_format(floor($sbb), '0', ',', '.'); ?></span></td>
                <td><span class="badge <?php echo $colorsdep; ?>"><?php echo number_format(floor($sdep), '0', ',', '.'); ?></span></td>
                {{-- <td><span class="badge <?php echo $colorsds; ?>"><?php echo number_format(floor($sds), '0', ',', '.'); ?></span></td> --}}
                <td><span class="badge <?php echo $colorssp; ?>"><?php echo number_format(floor($ssp), '0', ',', '.'); ?></span></td>
                {{-- <td><span class="badge <?php echo $colorsspp; ?>"><?php echo number_format(floor($sspp), '0', ',', '.'); ?></span></td> --}}
                <td><span class="badge <?php echo $colorssc; ?>"><?php echo number_format(floor($ssc), '0', ',', '.'); ?></span></td>
                <td><span class="badge <?php echo $colorsp8; ?>"><?php echo number_format(floor($ssp8), '0', ',', '.'); ?></span></td>
                <td><span class="badge <?php echo $colorsp8p; ?>"><?php echo number_format(floor($ssp8p), '0', ',', '.'); ?></span></td>
            </tr>
            @endforeach
    </table>
</div>
