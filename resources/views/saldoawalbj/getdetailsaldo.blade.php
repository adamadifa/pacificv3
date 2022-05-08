<?php
  $no = 1;
  foreach ($detail as $b) {

    //RETUR
    $jmldus = floor($b->saldoakhir/ $b->isipcsdus);
    if($b->saldoakhir !=0 ){
      $sisadus   = $b->saldoakhir % $b->isipcsdus;
    }else{
      $sisadus = 0;
    }
    if($b->isipack == 0){
      $jmlpack    = 0;
      $sisapack   = $sisadus;
      $s          = "A";
    }else{
      $jmlpack    = floor($sisadus / $b->isipcs);
      $sisapack   = $sisadus % $b->isipcs;
      $s          = "B";
    }
    $jmlpcs = $sisapack;

    // echo $sisadus."-".$s."-".$sisapack."-".$jmlpcs."<br>";

?>
<tr>
    <td style="width:10px"><?php echo $no; ?></td>
    <td style="width:200px">
        <input type="hidden" name="kode_produk[]" value="<?php echo $b->kode_produk;?>">
        <input type="hidden" name="isipcsdus[]" value="<?php echo $b->isipcsdus;?>">
        <input type="hidden" name="isipack[]" value="<?php echo $b->isipack;?>">
        <input type="hidden" name="isipcs[]" value="<?php echo $b->isipcs;?>">
        <?php echo $b->nama_barang; ?>
    </td>
    <td style="width:100px" class="text-right">
        <input type="hidden" style="text-align:right" value="<?php if(!empty($jmldus)){ echo $jmldus; } ?>" id="jmldus" name="jmldus[]" class="form-control" data-error=".errorTxt19" />
        {{ !empty($jmldus) ? rupiah($jmldus) : '' }}
    </td>
    <td style="width:50px"><?php echo $b->satuan; ?></td>
    <td style="width:100px" class="text-right">
        <?php if(!empty($b->isipack)){ ?>
        <input type="hidden" style="text-align:right" value="<?php if(!empty($jmlpack)){ echo $jmlpack; } ?>" id="jmlpack" name="jmlpack[]" class="form-control" data-error=".errorTxt19" />
        {{ !empty($jmlpack) ? rupiah($jmlpack) : '' }}
        <?php } ?>
    </td>
    <td style="width:50px">Pack</td>
    <td style="width:100px" class="text-right">
        <input type="hidden" style="text-align:right" value="<?php if(!empty($jmlpcs)){ echo $jmlpcs; } ?>" id="jmlpcs" name="jmlpcs[]" class="form-control" data-error=".errorTxt19" />
        {{ !empty($jmlpcs) ? rupiah($jmlpcs) : '' }}
    </td>
    <td style="width:50px">Pcs</td>
</tr>
<?php
    $no++;
    $jumproduk = $no-1;
  }
?>
<input type="hidden" value="<?php echo $jumproduk; ?>" name="jumproduk">
