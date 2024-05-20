<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <title>Laporan Rekap Persediaan Gudang Cabang {{ date('d-m-y') }}</title>
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
      PACIFIC CABANG {{ $cabang->nama_cabang }}<br>
      REKAPITULASI PERSEDIAAN BARANG<br>
      PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
      <br>
   </b>
   <br>
   <table class="datatable3" style="width:100%;  margin-bottom: 30px" border="1">
      <thead bgcolor="#024a75" style="color:white;">
         <tr>
            <th rowspan="2" bgcolor="#024a75" style="font-size:10px !important">KODE PRODUK</th>
            <th rowspan="2" bgcolor="#024a75" style="font-size:10px !important">PRODUK</th>
            <th rowspan="2" bgcolor="#024a75" style="font-size:10px !important">SALDO AWAL</th>
            <th colspan="6" bgcolor="#22a538" style="font-size:10px !important">PENERIMAAN</th>
            <th colspan="8" bgcolor="#c7473a" style="font-size:10px !important">PENGELUARAN</th>
            <th rowspan="2" bgcolor="#024a75" style="font-size:10px !important">SALDO AKHIR</th>
            <th colspan="3" bgcolor="#024a75" style="font-size:10px !important">SALDO AKHIR</th>
         </tr>
         <tr>
            <th bgcolor="#22a538" style="font-size:10px !important">PUSAT</th>
            <th bgcolor="#2e73c6" style="font-size:10px !important">TRANSIT IN</th>
            <th bgcolor="#22a538" style="font-size:10px !important">RETUR</th>
            <th bgcolor="#22a538" style="font-size:10px !important">LAIN LAIN</th>
            <th bgcolor="#22a538" style="font-size:10px !important">REPACK</th>
            <th bgcolor="#22a538" style="font-size:10px !important">PENYESUAIAN</th>
            <th bgcolor="#c7473a" style="font-size:10px !important">PENJUALAN</th>
            <th bgcolor="#c7473a" style="font-size:10px !important">PROMOSI</th>
            <th bgcolor="#c7473a" style="font-size:10px !important">REJECT PASAR</th>
            <th bgcolor="#c7473a" style="font-size:10px !important">REJECT MOBIL</th>
            <th bgcolor="#c7473a" style="font-size:10px !important">REJECT GUDANG</th>
            <th bgcolor="#2e73c6" style="font-size:10px !important">TRANSIT OUT</th>
            <th bgcolor="#c7473a" style="font-size:10px !important">LAIN LAIN</th>
            <th bgcolor="#c7473a" style="font-size:10px !important">PENYESUAIAN</th>
            <th bgcolor="#024a75" style="font-size:10px !important">DUS</th>
            <th bgcolor="#024a75" style="font-size:10px !important">PACK</th>
            <th bgcolor="#024a75" style="font-size:10px !important">PCS</th>
         </tr>
      </thead>
      <tbody>
         <?php
            foreach ($rekap as $key => $d) {
                $kode_cabang = @$rekap[$key + 1]->kode_cabang;
                $saldoawal_gs = ($d->saldo_awal_gs + $d->sisamutasi) / $d->isipcsdus;
                //$saldoawal_gs = ($d->saldo_awal_gs) / $d->isipcsdus;
                $pusat = $d->pusat / $d->isipcsdus;
                $transit_in = $d->transit_in / $d->isipcsdus;
                $retur = $d->retur / $d->isipcsdus;
                $lainlain_in = $d->lainlain_in / $d->isipcsdus;
                $repack = $d->repack / $d->isipcsdus;
                $penyesuaian_in = $d->penyesuaian_in / $d->isipcsdus;
                $penjualan = $d->penjualan / $d->isipcsdus;
                $promosi = $d->promosi / $d->isipcsdus;
                $reject_pasar = $d->reject_pasar / $d->isipcsdus;
                $reject_mobil = $d->reject_mobil / $d->isipcsdus;
                $reject_gudang = $d->reject_gudang / $d->isipcsdus;
                $transit_out = $d->transit_out / $d->isipcsdus;
                $lainlain_out = $d->lainlain_out / $d->isipcsdus;
                $penyesuaian_out = $d->penyesuaian_out / $d->isipcsdus;

                $sisamutasi = ($saldoawal_gs + $pusat + $transit_in + $retur + $lainlain_in + $repack + $penyesuaian_in) - ($penjualan + $promosi + $reject_pasar + $reject_mobil + $reject_gudang + $transit_out + $lainlain_out + $penyesuaian_out);


                $realsaldoakhir = (($d->saldo_awal_gs + $d->sisamutasi) + $d->pusat + $d->transit_in + $d->retur + $d->lainlain_in + $d->repack + $d->penyesuaian_in) -
                ($d->penjualan + $d->promosi + $d->reject_pasar + $d->reject_mobil + $d->reject_gudang + $d->transit_out + $d->lainlain_out + $d->penyesuaian_out);


                if($realsaldoakhir < 0){
                    $realsaldoakhir = $realsaldoakhir * -1;
                }else{
                    $realsaldoakhir = $realsaldoakhir;
                }
                if ($realsaldoakhir != 0) {
				$jmldus    = floor($realsaldoakhir / $d->isipcsdus);
				$sisadus   = $realsaldoakhir % $d->isipcsdus;
				if ($d->isipack == 0) {
					$jmlpack    = 0;
					$sisapack   = $sisadus;
				} else {
					$jmlpack   = floor($sisadus / $d->isipcs);
					$sisapack   = $sisadus % $d->isipcs;
				}
				$jmlpcs = $sisapack;
				if ($d->satuan == 'PCS') {
					$jmldus = 0;
					$jmlpack = 0;
					$jmlpcs = $realsaldoakhir;
				}
			} else {
				$jmldus 	= 0;
				$jmlpack	= 0;
				$jmlpcs 	= 0;
			}
            ?>
         <tr>
            <td><?php echo $d->kode_produk; ?></td>
            <td><?php echo $d->nama_barang; ?></td>
            <td align="right"><?php if (!empty($saldoawal_gs)) {
                echo desimal($saldoawal_gs);
            } ?></td>
            <td align="right"><?php if (!empty($pusat)) {
                echo desimal($pusat);
            } ?></td>
            <td align="right">
               <?php
               if (!empty($transit_in)) {
                   echo desimal($transit_in);
               }
               ?>
            </td>
            <td align="right">
               <?php
               if (!empty($retur)) {
                   echo desimal($retur);
               }
               ?>
            </td>
            <td align="right">
               <?php
               if (!empty($lainlain_in)) {
                   echo desimal($lainlain_in);
               }
               ?>
            </td>
            <td align="right">
               <?php
               if (!empty($repack)) {
                   echo desimal($repack);
               }
               ?>
            </td>
            <td align="right">
               <?php
               if (!empty($penyesuaian_in)) {
                   echo desimal($penyesuaian_in);
               }
               ?>
            </td>
            <td align="right">
               <?php
               if (!empty($penjualan)) {
                   echo desimal($penjualan);
               }
               ?>
            </td>
            <td align="right">
               <?php
               if (!empty($promosi)) {
                   echo desimal($promosi);
               }
               ?>
            </td>
            <td align="right">
               <?php
               if (!empty($reject_pasar)) {
                   echo desimal($reject_pasar);
               }
               ?>
            </td>
            <td align="right">
               <?php
               if (!empty($reject_mobil)) {
                   echo desimal($reject_mobil);
               }
               ?>
            </td>
            <td align="right">
               <?php
               if (!empty($reject_gudang)) {
                   echo desimal($reject_gudang);
               }
               ?>
            </td>
            <td align="right">
               <?php
               if (!empty($transit_out)) {
                   echo desimal($transit_out);
               }
               ?>
            </td>
            <td align="right">
               <?php
               if (!empty($lainlain_out)) {
                   echo desimal($lainlain_out);
               }
               ?>
            </td>
            <td align="right">
               <?php
               if (!empty($penyesuaian_out)) {
                   echo desimal($penyesuaian_out);
               }
               ?>
            </td>
            <td align="right">
               <?php
               if (!empty($sisamutasi)) {
                   echo desimal($sisamutasi);
               }
               ?>
            </td>
            <td align="right">{{ !empty($jmldus) ? $jmldus : '' }}</td>
            <td align="right">{{ !empty($jmlpack) ? $jmlpack : '' }}</td>
            <td align="right">{{ !empty($jmlpcs) ? $jmlpcs : '' }}</td>
         </tr>
         <?php
            }
            ?>
      </tbody>
   </table>
   <br>
   <b style="font-size:14px;">
      PACIFIC CABANG {{ $cabang->nama_cabang }}<br>
      REKAPITULASI BAD STOK BARANG<br>
      PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
      <br>
   </b>
   <table class="datatable3" style="width:100%;  margin-bottom: 30px" border="1">
      <thead bgcolor="#024a75" style="color:white;">
         <tr>
            <th rowspan="2" bgcolor="#024a75" style="font-size:10px !important">NO</th>
            <th rowspan="2" bgcolor="#024a75" style="font-size:10px !important">PRODUK</th>
            <th rowspan="2" bgcolor="#024a75" style="font-size:10px !important">SALDO AWAL</th>
            <th colspan="4" bgcolor="#22a538" style="font-size:10px !important">PENERIMAAN</th>
            <th colspan="3" bgcolor="#c7473a" style="font-size:10px !important">PENGELUARAN</th>
            <th rowspan="2" bgcolor="#024a75" style="font-size:10px !important">SALDO AKHIR</th>
            <th colspan="3" bgcolor="#024a75" style="font-size:10px !important">SALDO AKHIR</th>
         </tr>
         <tr>
            <th bgcolor="#22a538" style="font-size:10px !important">REJECT PASAR</th>
            <th bgcolor="#22a538" style="font-size:10px !important">REJECT MOBIL</th>
            <th bgcolor="#22a538" style="font-size:10px !important">REJECT GUDANG</th>
            <th bgcolor="#22a538" style="font-size:10px !important">PENYESUAIAN</th>
            <th bgcolor="#c7473a" style="font-size:10px !important">KIRIM PUSAT</th>
            <th bgcolor="#c7473a" style="font-size:10px !important">REPACK</th>
            <th bgcolor="#c7473a" style="font-size:10px !important">PENYESUAIAN</th>
            <th bgcolor="#024a75" style="font-size:10px !important">DUS</th>
            <th bgcolor="#024a75" style="font-size:10px !important">PACK</th>
            <th bgcolor="#024a75" style="font-size:10px !important">PCS</th>
      </thead>
      <tbody>
         <?php
		$no = 1;
		foreach ($rekap as $d) {

			$sabs = ($d->saldo_awal_bs + $d->sisamutasibad) /$d->isipcsdus;
			$repack = $d->repack / $d->isipcsdus;
			$rejectpasar = $d->reject_pasar / $d->isipcsdus;
			$rejectmobil = $d->reject_mobil / $d->isipcsdus;
			$rejectgd   = $d->reject_gudang / $d->isipcsdus;
			$penybad_in = $d->penyesuaianbad_in / $d->isipcsdus;

			$kirimpusat = $d->kirim_pusat / $d->isipcsdus;
			$penybad_out = $d->penyesuaianbad_out / $d->isipcsdus;

			$sisamutasibad = ($sabs + $rejectpasar + $rejectmobil + $rejectgd + $penybad_in) - ($kirimpusat + $repack + $penybad_out);
			$realsaldobad = ($d->saldo_awal_bs + $d->reject_pasar + $d->reject_mobil + $d->reject_gudang + $d->penyesuaianbad_in) -
				($d->kirim_pusat + $d->repack + $d->penyesuaianbad_out);
            $ceksaldobad = $realsaldobad;
            if($realsaldobad < 0){
                $realsaldobad = $realsaldobad * -1;
            }
			if ($realsaldobad != 0) {
				$jmldus    = floor($realsaldobad / $d->isipcsdus);
				$sisadus   = $realsaldobad % $d->isipcsdus;
				if ($d->isipack == 0) {
					$jmlpack    = 0;
					$sisapack   = $sisadus;
				} else {
					$jmlpack   = floor($sisadus / $d->isipcs);
					$sisapack   = $sisadus % $d->isipcs;
				}
				$jmlpcs = $sisapack;
				if ($d->satuan == 'PCS') {
					$jmldus = 0;
					$jmlpack = 0;
					$jmlpcs = $realsaldobad;
				}
			} else {


				$jmldus 	= 0;
				$jmlpack	= 0;
				$jmlpcs 	= 0;
			}

            if($ceksaldobad < 0){
                $jmldus = $jmldus * -1;
                $jmlpack = $jmlpack * -1;
                $jmlpcs = $jmlpcs * -1;
            }

		?>
         <tr>
            <td align="center"><?php echo $no; ?></td>
            <td><?php echo $d->nama_barang; ?></td>
            <td align="right"><?php if (!empty($sabs)) {
                echo desimal($sabs);
            } ?></td>
            <td align="right"><?php if (!empty($rejectpasar)) {
                echo desimal($rejectpasar);
            } ?></td>
            <td align="right"><?php if (!empty($rejectmobil)) {
                echo desimal($rejectmobil);
            } ?></td>
            <td align="right"><?php if (!empty($rejectgd)) {
                echo desimal($rejectgd);
            } ?></td>
            <td align="right"><?php if (!empty($penybad_in)) {
                echo desimal($penybad_in);
            } ?></td>
            <td align="right"><?php if (!empty($kirimpusat)) {
                echo desimal($kirimpusat);
            } ?></td>
            <td align="right"><?php if (!empty($repack)) {
                echo desimal($repack);
            } ?></td>
            <td align="right"><?php if (!empty($penybad_out)) {
                echo desimal($penybad_out);
            } ?></td>
            <td align="right"><?php if (!empty($sisamutasibad)) {
                echo desimal($sisamutasibad);
            } ?></td>
            <td align="right">{{ !empty($jmldus) ? $jmldus : '' }}</td>
            <td align="right">{{ !empty($jmlpack) ? $jmlpack : '' }}</td>
            <td align="right">{{ !empty($jmlpcs) ? $jmlpcs : '' }}</td>
         </tr>
         <?php
			$no++;
		}
		?>
      </tbody>
   </table>
</body>

</html>
