<table class="table table-striped card-table table-bordered" style="font-size:12px !important">
    <thead class="thead-dark">
        <tr>
            <th colspan="11">REKAP PENJUALAN</th>
        </tr>
        <tr class="text-right">
            <th class="text-left">CABANG</th>
            <th>TOTAL BRUTO</th>
            <th>TOTAL RETUR</th>
            <th>PENYESUAIAN</th>
            <th>POTONGAN</th>
            <th>POTONGAN ISTIMEWA</th>
            <th>DPP</th>
            <th>PPN</th>
            <th>NETTO</th>
            <th>PENDING</th>
            <th>REGULER</th>
        </tr>
    </thead>
    <?php

    $totalbruto       = 0;
    $totalretur       = 0;
    $totalpenyharga   = 0;
    $totalpotongan    = 0;
    $totalpotistimewa  = 0;
    $totalppn = 0;
    $grandnetto       = 0;
    $grandwithppn = 0;
    $grandnettopending = 0;
    $grandnettoreguler = 0;

    foreach ($rekappenjualancabang as $r) {
        $totalbruto  = $totalbruto + $r->totalbruto;
        $totalretur = $totalretur + $r->totalretur;
        $totalpenyharga= $totalpenyharga + $r->totalpenyharga;
        $totalpotongan = $totalpotongan + $r->totalpotongan;
        $totalpotistimewa= $totalpotistimewa + $r->totalpotistimewa;
        $totalppn = $totalppn + $r->totalppn;

        $totalnetto = $r->totalbruto - $r->totalretur - $r->totalpenyharga - $r->totalpotongan - $r->totalpotistimewa;
        $totalwithppn = $totalnetto + $r->totalppn;


        $totalnettopending  = $r->totalbrutopending - $r->totalreturpending - $r->totalpenyhargapending - $r->totalpotonganpending - $r->totalpotistimewapending + $r->totalppnpending;

        $grandnetto  = $grandnetto + $totalnetto;
        $grandwithppn  = $grandwithppn + $totalwithppn;
        $grandnettopending  = $grandnettopending + $totalnettopending;

    ?>
    <tr style="font-size:12">
        <td class="cabang" style="font-weight:bold"><?php echo strtoUpper($r->nama_cabang); ?></td>
        <td style="text-align:right; font-weight:"><?php echo rupiah($r->totalbruto); ?></td>
        <td style="text-align:right; font-weight:"><?php echo rupiah($r->totalretur); ?></td>
        <td style="text-align:right; font-weight:"><?php echo rupiah($r->totalpenyharga); ?></td>
        <td style="text-align:right; font-weight:"><?php echo rupiah($r->totalpotongan); ?></td>
        <td style="text-align:right; font-weight:"><?php echo rupiah($r->totalpotistimewa); ?></td>
        <td style="text-align:right; font-weight:bold"><?php echo rupiah($totalnetto); ?></td>
        <td style="text-align:right; font-weight:"><?php echo rupiah($r->totalppn); ?></td>
        <td style="text-align:right; font-weight:"><?php echo rupiah($totalwithppn); ?></td>
        <td style="text-align:right; font-weight:bold">
            <form action="/laporanpenjualan/cetak" method="post" class="frmpending" target="_blank">
                @csrf
                <input type="hidden" name="kode_cabang" value="{{ $r->kode_cabang }}">
                <input type="hidden" name="dari" value="{{ $dari }}">
                <input type="hidden" name="sampai" value="{{ $sampai }}">
                <input type="hidden" name="jenislaporan" value="standar">
                <input type="hidden" name="status" value="pending">
                <a href="#" class="warning showpending">{{ rupiah($totalnettopending) }}</a>
            </form>
        </td>
        <td style="text-align:right; font-weight:bold"><?php echo rupiah($totalwithppn - $totalnettopending); ?></td>
    </tr>

    <?php }
     if ($bulan < 9 && $tahun <= 2022) {
    ?>
    <tr style="font-size:12">
        <?php
            $totalnettotsm = $rekappenjualantsm != null ? $rekappenjualantsm->totalbruto - $rekappenjualantsm->totalretur - $rekappenjualantsm->totalpenyharga - $rekappenjualantsm->totalpotongan - $rekappenjualantsm->totalpotistimewa : 0;
            $totalnettopendingtsm  = $rekappenjualantsm != null ? $rekappenjualantsm->totalbrutopending - $rekappenjualantsm->totalreturpending - $rekappenjualantsm->totalpenyhargapending - $rekappenjualantsm->totalpotonganpending - $rekappenjualantsm->totalpotistimewapending : 0;
        ?>
        <td class="cabang" style="font-weight:bold"><?php echo strtoUpper($rekappenjualantsm->nama_cabang); ?></td>
        <td style="text-align:right; font-weight:"><?php echo rupiah($rekappenjualantsm->totalbruto); ?></td>
        <td style="text-align:right; font-weight:"><?php echo rupiah($rekappenjualantsm->totalretur); ?></td>
        <td style="text-align:right; font-weight:"><?php echo rupiah($rekappenjualantsm->totalpenyharga); ?></td>
        <td style="text-align:right; font-weight:"><?php echo rupiah($rekappenjualantsm->totalpotongan); ?></td>
        <td style="text-align:right; font-weight:"><?php echo rupiah($rekappenjualantsm->totalpotistimewa); ?></td>
        <td style="text-align:right; font-weight:bold"><?php echo rupiah($totalnettotsm); ?></td>
        <td style="text-align:right; font-weight:bold">
            <form action="/laporanpenjualan/cetak" method="post" class="frmpending" target="_blank">
                @csrf
                <input type="hidden" name="kode_cabang" value="{{ $rekappenjualantsm->kode_cabang }}">
                <input type="hidden" name="dari" value="{{ $dari }}">
                <input type="hidden" name="sampai" value="{{ $sampai }}">
                <input type="hidden" name="jenislaporan" value="standar">
                <input type="hidden" name="status" value="pending">
                <a href="#" class="warning showpending">{{ rupiah($totalnettopendingtsm ) }}</a>
            </form>
        </td>
        <td style="text-align:right; font-weight:bold">{{ rupiah($totalnettotsm - $totalnettopendingtsm ) }}</td>
    </tr>
    <tr style="font-size:12">
        <?php
            $totalnettogrt = $rekappenjualangrt->totalbruto - $rekappenjualangrt->totalretur - $rekappenjualangrt->totalpenyharga - $rekappenjualangrt->totalpotongan - $rekappenjualangrt->totalpotistimewa;
            $totalnettopendinggrt  = $rekappenjualangrt->totalbrutopending - $rekappenjualangrt->totalreturpending - $rekappenjualangrt->totalpenyhargapending - $rekappenjualangrt->totalpotonganpending - $rekappenjualangrt->totalpotistimewapending;
        ?>
        <td class="cabang" style="font-weight:bold"><?php echo strtoUpper($rekappenjualantsm->nama_cabang); ?> (GRT)</td>
        <td style="text-align:right; font-weight:"><?php echo rupiah($rekappenjualangrt->totalbruto); ?></td>
        <td style="text-align:right; font-weight:"><?php echo rupiah($rekappenjualangrt->totalretur); ?></td>
        <td style="text-align:right; font-weight:"><?php echo rupiah($rekappenjualangrt->totalpenyharga); ?></td>
        <td style="text-align:right; font-weight:"><?php echo rupiah($rekappenjualangrt->totalpotongan); ?></td>
        <td style="text-align:right; font-weight:"><?php echo rupiah($rekappenjualangrt->totalpotistimewa); ?></td>
        <td style="text-align:right; font-weight:bold"><?php echo rupiah($totalnettogrt); ?></td>
        <td style="text-align:right; font-weight:bold">
            <form action="/laporanpenjualan/cetak" method="post" class="frmpending" target="_blank">
                @csrf
                <input type="hidden" name="kode_cabang" value="{{ $rekappenjualangrt->kode_cabang }}">
                <input type="hidden" name="dari" value="{{ $dari }}">
                <input type="hidden" name="sampai" value="{{ $sampai }}">
                <input type="hidden" name="jenislaporan" value="standar">
                <input type="hidden" name="status" value="pending">
                <a href="#" class="warning showpending">{{ rupiah($totalnettopendinggrt ) }}</a>
            </form>
        </td>
        <td style="text-align:right; font-weight:bold">{{ rupiah($totalnettogrt - $totalnettopendinggrt ) }}</td>
    </tr>
    <?php } ?>
    </tbody>
    <?php
     if ($bulan < 9 && $tahun <= 2022) {
    ?>
    <tfoot class="thead-dark">
        <tr>
            <th style="font-weight:bold">TOTAL</th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalbruto + $rekappenjualantsm->totalbruto + $rekappenjualangrt->totalbruto); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalretur + $rekappenjualantsm->totalretur + $rekappenjualangrt->totalretur); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalpenyharga + $rekappenjualantsm->totalpenyharga + $rekappenjualangrt->totalpenyharga ); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalpotongan + $rekappenjualantsm->totalpotongan + $rekappenjualangrt->totalpotongan); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalpotistimewa + $rekappenjualantsm->totalpotistimewa + $rekappenjualangrt->totalpotistimewa); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($grandnetto + $totalnettotsm + $totalnettogrt); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($grandnettopending + $totalnettopendingtsm + $totalnettopendinggrt); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah(($grandnetto + $totalnettotsm + $totalnettogrt) - ($grandnettopending + $totalnettopendingtsm + $totalnettopendinggrt)); ?></th>
        </tr>
    </tfoot>
    <?php }else{ ?>
    <tfoot class="thead-dark">
        <tr>
            <th style="font-weight:bold">TOTAL</th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalbruto); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalretur); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalpenyharga ); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalpotongan); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalpotistimewa); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($grandnetto); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalppn); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($grandwithppn); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($grandnettopending); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah(($grandwithppn) - ($grandnettopending)); ?></th>
        </tr>
    </tfoot>
    <?php } ?>

</table>
<script>
    $(function() {
        $(".showpending").click(function(e) {
            var form = $(this).closest("form");
            form.submit();
        });
    });

</script>
