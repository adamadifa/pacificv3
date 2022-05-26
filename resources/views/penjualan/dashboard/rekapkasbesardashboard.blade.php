<div class="row">
    <div class="col-6">
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
            </tbody>
            <tfoot class="thead-dark">
                <tr>
                    <th style="font-weight:bold">TOTAL</th>
                    <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalcashin); ?></th>
                    <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalvoucher); ?></th>
                    <th style="text-align:right; font-weight:bold"><?php echo rupiah($totalvoucher + $totalcashin); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
