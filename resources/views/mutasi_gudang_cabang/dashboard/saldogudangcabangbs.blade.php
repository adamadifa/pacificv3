<table class="table table-hover-animation">
    <thead>
        <tr>
            <th>Nama Barang</th>
            <th style="text-align:right !important">Stok Cabang</th>
        </tr>
    </thead>

    <?php
    foreach ($saldo as $r) {
    $saldoakhir = $r->saldoakhir/$r->isipcsdus;
    if ($saldoakhir <= 0) {
    $color = "bg-danger";
    } else {
    $color = "bg-success";
    }
    ?>
    <tr>
        <td><?php echo $r->nama_barang; ?></td>
        <td align="right"><span class="badge <?php echo $color; ?>"><?php echo number_format($saldoakhir, '2', ',', '.'); ?></span></td>
    </tr>
    <?php } ?>
</table>
