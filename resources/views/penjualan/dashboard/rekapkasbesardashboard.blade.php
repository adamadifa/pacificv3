<div class="row">
    <div class="col-8">
        <table class="table table-striped card-table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th colspan="4">REKAP KAS BESAR</th>
                </tr>
                <tr>
                    <th>CABANG</th>
                    <th>Cash IN</th>
                    <th>Voucher</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalvoucher = 0;
                $totalcashin =  0;
                foreach ($kasbesar as $r) {
                    $totalcashin = $totalcashin + $r->cashin;
                    $totalvoucher = $totalvoucher + $r->voucher ?>
                <tr style="font-size:12">
                    <td style="font-weight:bold"><?php echo strtoUpper($r->nama_cabang); ?></td>
                    <td style="text-align:right; font-weight:bold"><?php echo rupiah($r->cashin); ?></td>
                    <td style="text-align:right; font-weight:bold"><?php echo rupiah($r->voucher); ?></td>
                    <td style="text-align:right; font-weight:bold"><?php echo rupiah($r->voucher + $r->cashin); ?></td>
                </tr>
                <?php } ?>
                <tr style="font-size:12">
                    <td style="font-weight:bold"><?php echo strtoUpper($kasbesartsm->nama_cabang); ?></td>
                    <td style="text-align:right; font-weight:bold"><?php echo rupiah($kasbesartsm->cashin); ?></td>
                    <td style="text-align:right; font-weight:bold"><?php echo rupiah($kasbesartsm->voucher); ?></td>
                    <td style="text-align:right; font-weight:bold"><?php echo rupiah($kasbesartsm->voucher + $kasbesartsm->cashin); ?></td>
                </tr>
                <tr style="font-size:12">
                    <td style="font-weight:bold"><?php echo strtoUpper($kasbesargrt->nama_cabang); ?> (GRT)</td>
                    <td style="text-align:right; font-weight:bold"><?php echo rupiah($kasbesargrt->cashin); ?></td>
                    <td style="text-align:right; font-weight:bold"><?php echo rupiah($kasbesargrt->voucher); ?></td>
                    <td style="text-align:right; font-weight:bold"><?php echo rupiah($kasbesargrt->voucher + $kasbesargrt->cashin); ?></td>
                </tr>
            </tbody>
            <tfoot class="thead-dark">
                <?php
                    $totalcashintsm = $kasbesartsm->cashin + $kasbesargrt->cashin;
                    $totalvouchertsm = $kasbesartsm->voucher + $kasbesargrt->voucher;
                    $totaltsm = $totalcashintsm + $totalvouchertsm;
                ?>
                <tr>
                    <th style="font-weight:bold">TOTAL</th>
                    <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalcashin + $totalcashintsm); ?></th>
                    <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalvoucher + $totalvouchertsm); ?></th>
                    <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalvoucher + $totalcashin + $totaltsm); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
