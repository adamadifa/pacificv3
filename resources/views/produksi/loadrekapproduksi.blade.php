<?php
$no = 1;
foreach ($detail as $d) {
?>
<tr>
    <td><?php echo $no; ?></td>
    <td><?php echo $d->kode_produk; ?></td>
    <td align="right"><?php echo number_format($d->januari, '0', '', '.'); ?></td>
    <td align="right">
        <?php
        if ($d->februari > $d->januari) {
            $color = 'success';
            $icon = 'fa-arrow-up';
        } elseif ($d->februari < $d->januari) {
            $color = 'danger';
            $icon = 'fa-arrow-down';
        } else {
            $color = 'info';
            $icon = 'fa-arrow-right';
        }
        ?>
        <span class="badge bg-<?php echo $color; ?>" style="margin-right:10px;">
            <i class="fa <?php echo $icon; ?> mr-2"></i>
            <?php echo number_format($d->februari, '0', '', '.'); ?>
        </span>
    </td>
    <td align="right">
        <?php
        if ($d->maret > $d->februari) {
            $color = 'success';
            $icon = 'fa-arrow-up';
        } elseif ($d->maret < $d->februari) {
            $color = 'danger';
            $icon = 'fa-arrow-down';
        } else {
            $color = 'info';
            $icon = 'fa-arrow-right';
        }
        ?>
        <span class="badge bg-<?php echo $color; ?>" style="margin-right:10px;">
            <i class="fa <?php echo $icon; ?> mr-2"></i>
            <?php echo number_format($d->maret, '0', '', '.'); ?>
        </span>
    </td>
    <td align="right">
        <?php
        if ($d->april > $d->maret) {
            $color = 'success';
            $icon = 'fa-arrow-up';
        } elseif ($d->april < $d->maret) {
            $color = 'danger';
            $icon = 'fa-arrow-down';
        } else {
            $color = 'info';
            $icon = 'fa-arrow-right';
        }
        ?>
        <span class="badge bg-<?php echo $color; ?>" style="margin-right:10px;">
            <i class="fa <?php echo $icon; ?> mr-2"></i>
            <?php echo number_format($d->april, '0', '', '.'); ?>
        </span>
    </td>
    <td align="right">
        <?php
        if ($d->mei > $d->april) {
            $color = 'success';
            $icon = 'fa-arrow-up';
        } elseif ($d->mei < $d->april) {
            $color = 'danger';
            $icon = 'fa-arrow-down';
        } else {
            $color = 'info';
            $icon = 'fa-arrow-right';
        }
        ?>
        <span class="badge bg-<?php echo $color; ?>" style="margin-right:10px;">
            <i class="fa <?php echo $icon; ?> mr-2"></i>
            <?php echo number_format($d->mei, '0', '', '.'); ?>
        </span>
    </td>
    <td align="right">
        <?php
        if ($d->juni > $d->mei) {
            $color = 'success';
            $icon = 'fa-arrow-up';
        } elseif ($d->juni < $d->mei) {
            $color = 'danger';
            $icon = 'fa-arrow-down';
        } else {
            $color = 'info';
            $icon = 'fa-arrow-right';
        }
        ?>
        <span class="badge bg-<?php echo $color; ?>" style="margin-right:10px;">
            <i class="fa <?php echo $icon; ?> mr-2"></i>
            <?php echo number_format($d->juni, '0', '', '.'); ?>
        </span>
    </td>

    <td align="right">
        <?php
        if ($d->juli > $d->juni) {
            $color = 'success';
            $icon = 'fa-arrow-up';
        } elseif ($d->juli < $d->juni) {
            $color = 'danger';
            $icon = 'fa-arrow-down';
        } else {
            $color = 'info';
            $icon = 'fa-arrow-right';
        }
        ?>
        <span class="badge bg-<?php echo $color; ?>" style="margin-right:10px;">
            <i class="fa <?php echo $icon; ?> mr-2"></i>
            <?php echo number_format($d->juli, '0', '', '.'); ?>
        </span>
    </td>
    <td align="right">
        <?php
        if ($d->agustus > $d->juli) {
            $color = 'success';
            $icon = 'fa-arrow-up';
        } elseif ($d->agustus < $d->juli) {
            $color = 'danger';
            $icon = 'fa-arrow-down';
        } else {
            $color = 'info';
            $icon = 'fa-arrow-right';
        }
        ?>
        <span class="badge bg-<?php echo $color; ?>" style="margin-right:10px;">
            <i class="fa <?php echo $icon; ?> mr-2"></i>
            <?php echo number_format($d->agustus, '0', '', '.'); ?>
        </span>
    </td>
    <td align="right">
        <?php
        if ($d->september > $d->agustus) {
            $color = 'success';
            $icon = 'fa-arrow-up';
        } elseif ($d->september < $d->agustus) {
            $color = 'danger';
            $icon = 'fa-arrow-down';
        } else {
            $color = 'info';
            $icon = 'fa-arrow-right';
        }
        ?>
        <span class="badge bg-<?php echo $color; ?>" style="margin-right:10px;">
            <i class="fa <?php echo $icon; ?> mr-2"></i>
            <?php echo number_format($d->september, '0', '', '.'); ?>
        </span>
    </td>
    <td align="right">
        <?php
        if ($d->oktober > $d->september) {
            $color = 'success';
            $icon = 'fa-arrow-up';
        } elseif ($d->oktober < $d->september) {
            $color = 'danger';
            $icon = 'fa-arrow-down';
        } else {
            $color = 'info';
            $icon = 'fa-arrow-right';
        }
        ?>
        <span class="badge bg-<?php echo $color; ?>" style="margin-right:10px;">
            <i class="fa <?php echo $icon; ?> mr-2"></i>
            <?php echo number_format($d->oktober, '0', '', '.'); ?>
        </span>
    </td>
    <td align="right">
        <?php
        if ($d->november > $d->oktober) {
            $color = 'success';
            $icon = 'fa-arrow-up';
        } elseif ($d->november < $d->oktober) {
            $color = 'danger';
            $icon = 'fa-arrow-down';
        } else {
            $color = 'info';
            $icon = 'fa-arrow-right';
        }
        ?>
        <span class="badge bg-<?php echo $color; ?>" style="margin-right:10px;">
            <i class="fa <?php echo $icon; ?> mr-2"></i>
            <?php echo number_format($d->november, '0', '', '.'); ?>
        </span>
    </td>
    <td align="right">
        <?php
        if ($d->desember > $d->november) {
            $color = 'success';
            $icon = 'fa-arrow-up';
        } elseif ($d->desember < $d->november) {
            $color = 'danger';
            $icon = 'fa-arrow-down';
        } else {
            $color = 'info';
            $icon = 'fa-arrow-right';
        }
        ?>
        <span class="badge bg-<?php echo $color; ?>" style="margin-right:10px;">
            <i class="fa <?php echo $icon; ?> mr-2"></i>

            <?php echo number_format($d->desember, '0', '', '.'); ?>
        </span>
    </td>
</tr>
<?php
  $no++;
}
?>
