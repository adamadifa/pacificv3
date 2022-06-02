<table class="table table-striped card-table table-bordered">
    <thead class="thead-dark">
        <tr>
            <th colspan="9">REKAP PENJUALAN</th>
        </tr>
        <tr class="text-right">
            <th class="text-left">CABANG</th>
            <th>TOTAL BRUTO</th>
            <th>TOTAL RETUR</th>
            <th>PENYESUAIAN</th>
            <th>POTONGAN</th>
            <th>POTONGAN ISTIMEWA</th>
            <th>TOTAL NETTO</th>
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
    $grandnetto       = 0;
    $grandnettopending = 0;
    $grandnettoreguler = 0;

    foreach ($rekappenjualancabang as $r) {
        $totalbruto  = $totalbruto + $r->totalbruto;
        $totalretur = $totalretur + $r->totalretur;
        $totalpenyharga= $totalpenyharga + $r->totalpenyharga;
        $totalpotongan = $totalpotongan + $r->totalpotongan;
        $totalpotistimewa= $totalpotistimewa + $r->totalpotistimewa;

        $totalnetto = $r->totalbruto - $r->totalretur - $r->totalpenyharga - $r->totalpotongan - $r->totalpotistimewa;
        $totalnettopending  = $r->totalbrutopending - $r->totalreturpending - $r->totalpenyhargapending - $r->totalpotonganpending - $r->totalpotistimewapending;

        $grandnetto  = $grandnetto + $totalnetto;
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
        <td style="text-align:right; font-weight:bold"><?php echo rupiah($totalnetto - $totalnettopending); ?></td>
    </tr>

    <?php } ?>
    <tr style="font-size:12">
        <?php
            $totalnettotsm = $rekappenjualantsm->totalbruto - $rekappenjualantsm->totalretur - $rekappenjualantsm->totalpenyharga - $rekappenjualantsm->totalpotongan - $rekappenjualantsm->totalpotistimewa;
            $totalnettopendingtsm  = $rekappenjualantsm->totalbrutopending - $rekappenjualantsm->totalreturpending - $rekappenjualantsm->totalpenyhargapending - $rekappenjualantsm->totalpotonganpending - $rekappenjualantsm->totalpotistimewapending;
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
        <td class="cabang" style="font-weight:bold">GARUT</td>
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
    </tbody>
    <tfoot class="thead-dark">
        <tr>
            <th style="font-weight:bold">TOTAL</th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalbruto); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalretur); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalpenyharga); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalpotongan); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalpotistimewa); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($grandnetto); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($grandnettopending); ?></th>
            <th style="text-align:right; font-weight:bold"><?php echo rupiah($grandnetto - $grandnettopending); ?></th>
        </tr>
    </tfoot>
</table>
<script>
    $(function() {
        $(".showpending").click(function(e) {
            var form = $(this).closest("form");
            form.submit();
        });
    });

</script>
