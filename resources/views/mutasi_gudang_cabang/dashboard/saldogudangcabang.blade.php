<table class="table table-hover-animation">
    <thead>
        <tr>
            <th>Nama Barang</th>
            <th style="text-align:right !important">Buffer Stok</th>
            <th style="text-align:right !important">Max Stok</th>
            <th style="text-align:right !important">Stok Cabang</th>
        </tr>
    </thead>

    <?php
    foreach ($saldo as $r) {
    $saldoakhir = ROUND(($r->saldoakhir / $r->isipcsdus) - $r->totalpengambilan + $r->totalpengembalian, 2);
    if ($saldoakhir <= $r->buffer) {
    $color = "bg-danger";
    }else if($saldoakhir >= $r->max_stok){
        $color = "bg-warning";
    } else {
    $color = "bg-success";
    }
    ?>
    <tr>
        <td><?php echo $r->nama_barang; ?></td>
        <td align="right"><span class="badge bg-warning"><?php echo number_format($r->buffer, '2', ',', '.'); ?></span></td>
        <td align="right"><span class="badge bg-info"><?php echo number_format($r->max_stok, '2', ',', '.'); ?></span></td>
        <td align="right"><span class="badge <?php echo $color; ?>"><?php echo number_format($saldoakhir, '2', ',', '.'); ?></span></td>
    </tr>
    <?php } ?>
</table>
