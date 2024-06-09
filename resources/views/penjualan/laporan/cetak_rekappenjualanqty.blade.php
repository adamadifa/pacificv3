<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <title>Cetak Laporan Data Pengambilan Pelanggan</title>
   <style>
      @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&display=swap');

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
   </style>
</head>

<body>
   <b style="font-size:14px;">

      <br>
      REKAP PENJUALAN QTY ALL CABANG<br>
      PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
      <br>

      <br />

   </b>
   <table class="datatable3">
      <thead>
         <tr bgcolor="#024a75" style="color:white; font-size:12;">
            <th rowspan="2">No</th>
            <th rowspan="2">Nama Produk</th>
            <th colspan="15">Cabang</th>
            <th rowspan="2">Total</th>
         </tr>
         <tr bgcolor="#024a75" style="color:white; font-size:12;">
            <th>Bandung</th>
            <th>Bogor</th>
            <th>Sukabumi</th>
            <th>Purwokerto</th>
            <th>Tegal</th>
            <th>Tasik</th>
            <th>Semarang</th>
            <th>Surabaya</th>
            <th>Klaten</th>
            <th>Pusat</th>
            <th>Purwakarta</th>
            <th>Garut</th>
            <th>Bekasi</th>
            <th>Tangerang</th>
            <th>Banten</th>
         </tr>
      </thead>
      <tbody>
         <?php
                $no=1; foreach($rekap as $r){

                    $bandung 	= $r->BDG / $r->isipcsdus;
                    $bogor 		= $r->BGR / $r->isipcsdus;
                    $sukabumi 	= $r->SKB / $r->isipcsdus;
                    $purwokerto = $r->PWT / $r->isipcsdus;
                    $tegal 		= $r->TGL / $r->isipcsdus;
                    $tasik 		= $r->TSM / $r->isipcsdus;
                    $semarang 	= $r->SMR / $r->isipcsdus;
                    $surabaya 	= $r->SBY / $r->isipcsdus;
                    $pusat 		= $r->PST / $r->isipcsdus;
                    $klaten 	= $r->KLT / $r->isipcsdus;
                    $purwakarta 	= $r->PWK / $r->isipcsdus;
                    $garut 	= $r->GRT / $r->isipcsdus;
                    $bekasi 	= $r->BKI / $r->isipcsdus;
                    $tangerang 	= $r->TGR / $r->isipcsdus;
                    $banten 	= $r->BTN / $r->isipcsdus;
                    $totalqty 	= $r->totalqty / $r->isipcsdus;

            ?>
         <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $r->nama_barang; ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($bandung)) {
                echo desimal($bandung);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($bogor)) {
                echo desimal($bogor);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($sukabumi)) {
                echo desimal($sukabumi);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($purwokerto)) {
                echo desimal($purwokerto);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($tegal)) {
                echo desimal($tegal);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($tasik)) {
                echo desimal($tasik);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($semarang)) {
                echo desimal($semarang);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($surabaya)) {
                echo desimal($surabaya);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($klaten)) {
                echo desimal($klaten);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($pusat)) {
                echo desimal($pusat);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($purwakarta)) {
                echo desimal($purwakarta);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($garut)) {
                echo desimal($garut);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($bekasi)) {
                echo desimal($bekasi);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($tangerang)) {
                echo desimal($tangerang);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($banten)) {
                echo desimal($banten);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($totalqty)) {
                echo desimal($totalqty);
            } ?></td>
         </tr>
         <?php $no++; } ?>
      </tbody>
   </table>
   <br>
   <br>
   <table class="datatable3">
      <thead>
         <tr bgcolor="#024a75" style="color:white; font-size:12;">
            <th rowspan="2">No</th>
            <th rowspan="2">Nama Produk</th>
            <th colspan="15">Cabang</th>
            <th rowspan="2">Total</th>
         </tr>
         <tr bgcolor="#024a75" style="color:white; font-size:12;">
            <th>Bandung</th>
            <th>Bogor</th>
            <th>Sukabumi</th>
            <th>Purwokerto</th>
            <th>Tegal</th>
            <th>Tasik</th>
            <th>Semarang</th>
            <th>Surabaya</th>
            <th>Klaten</th>
            <th>Pusat</th>
            <th>Purwakarta</th>
            <th>Garut</th>
            <th>Bekasi</th>
            <th>Tangerang</th>
            <th>Banten</th>
         </tr>
      </thead>
      <tbody>
         <?php
                $no=1; foreach($rekap as $r){


                    if($r->BDG > 0){

                        $bandung 	= $r->JML_BDG/($r->BDG / $r->isipcsdus);
                    }else{
                        $bandung = 0;
                    }

                    if($r->BGR > 0){

                        $bogor 		= $r->JML_BGR/($r->BGR / $r->isipcsdus);
                    }else{
                        $bogor = 0;
                    }

                    if($r->SKB > 0){

                        $sukabumi 	= $r->JML_SKB/($r->SKB / $r->isipcsdus);
                    }else{
                        $sukabumi = 0;
                    }

                    if($r->PWT > 0){

                        $purwokerto = $r->JML_PWT/($r->PWT / $r->isipcsdus);
                    }else{
                        $purwokerto = 0;
                    }

                    if($r->TGL > 0){

                        $tegal 		= $r->JML_TGL/($r->TGL / $r->isipcsdus);
                    }else{
                        $tegal = 0;
                    }

                    if($r->TSM > 0){

                        $tasik 		= $r->JML_TSM/($r->TSM / $r->isipcsdus);
                    }else{
                        $tasik 		= 0;
                    }

                    if($r->SMR > 0){

                        $semarang 		= $r->JML_SMR/($r->SMR / $r->isipcsdus);
                    }else{
                        $semarang 		= 0;
                    }

                    if($r->SBY > 0){

                        $surabaya 		= $r->JML_SBY/($r->SBY / $r->isipcsdus);
                    }else{
                        $surabaya 		= 0;
                    }

                    if($r->KLT > 0){

                        $klaten 		= $r->JML_KLT/($r->KLT / $r->isipcsdus);
                    }else{
                        $klaten 		= 0;
                    }

                    if($r->PST > 0){

                        $pusat 		= $r->JML_PST/($r->PST / $r->isipcsdus);
                    }else{
                        $pusat 		= 0;
                    }

                    if($r->PWK > 0){

                        $purwakarta 		= $r->JML_PWK/($r->PWK / $r->isipcsdus);
                    }else{
                        $purwakarta 		= 0;
                    }

                    if($r->GRT > 0){
                    $garut 		= $r->JML_GRT/($r->GRT / $r->isipcsdus);
                    }else{
                    $garut 		= 0;
                    }


                    if($r->BKI > 0){
                    $bekasi 		= $r->JML_BKI/($r->BKI / $r->isipcsdus);
                    }else{
                    $bekasi 		= 0;
                    }


                    if($r->TGR > 0){
                    $tangerang 		= $r->JML_TGR/($r->TGR / $r->isipcsdus);
                    }else{
                    $tangerang 		= 0;
                    }

                    if($r->BTN > 0){
                    $banten 		= $r->JML_BTN/($r->BTN / $r->isipcsdus);
                    }else{
                    $banten 		= 0;
                    }

                    $totalqty 	= ($bandung+$bogor+$sukabumi+$purwokerto+$tegal+$tasik+$semarang+$surabaya+$pusat+$purwakarta)/10;

            ?>
         <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $r->nama_barang; ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($r->BDG)) {
                echo rupiah($bandung);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($r->BGR)) {
                echo rupiah($bogor);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($r->SKB)) {
                echo rupiah($sukabumi);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($r->PWT)) {
                echo rupiah($purwokerto);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($r->TGL)) {
                echo rupiah($tegal);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($r->TSM)) {
                echo rupiah($tasik);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($r->TSM)) {
                echo rupiah($semarang);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($r->TSM)) {
                echo rupiah($surabaya);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($r->KLT)) {
                echo rupiah($klaten);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($r->PST)) {
                echo rupiah($pusat);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($r->PWK)) {
                echo rupiah($purwakarta);
            } ?></td>

            <td style="text-align:right; font-weight:bold"><?php if (!empty($r->GRT)) {
                echo rupiah($garut);
            } ?></td>

            <td style="text-align:right; font-weight:bold"><?php if (!empty($r->BKI)) {
                echo rupiah($bekasi);
            } ?></td>

            <td style="text-align:right; font-weight:bold"><?php if (!empty($r->TGR)) {
                echo rupiah($tangerang);
            } ?></td>

            <td style="text-align:right; font-weight:bold"><?php if (!empty($r->BTN)) {
                echo rupiah($banten);
            } ?></td>
            <td style="text-align:right; font-weight:bold"><?php if (!empty($totalqty)) {
                echo rupiah($totalqty);
            } ?></td>
         </tr>
         <?php $no++; } ?>
      </tbody>
   </table>
</body>

</html>
