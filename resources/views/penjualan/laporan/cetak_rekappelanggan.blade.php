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
        @if ($cabang!=null)
        @if ($cabang->kode_cabang=="PST")
        PACIFIC PUSAT
        @else
        PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
        @endif
        @else
        PACIFC ALL CABANG
        @endif
        <br>
        LAPORAN DATA PENGAMBILAN PELANGGAN<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>
        @if ($salesman != null)
        SALESMAN {{ strtoupper($salesman->nama_karyawan) }}
        @else
        SEMUA SALESMAN
        @endif
        <br />
        @if ($pelanggan != null)
        PELANGGAN {{ strtoupper($pelanggan->nama_pelanggan) }}
        @else
        SEMUA PELANGGAN
        @endif
    </b>
    <table class="datatable3" style="width:80%" border="1">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td rowspan="2">No</td>
                <td rowspan="2">Kode Pel.</td>
                <td rowspan="2">Nama Pelanggan</td>
                <td rowspan="2">Pasar</td>
                <td colspan="16">Produk</td>
            </tr>
            <tr>
                <td>AB</td>
                <td>AR</td>
                <td>AS</td>
                <td>BB</td>
                <td>CG</td>
                <td>CGG</td>
                <td>DB</td>
                <td>DEP</td>
                <td>DK</td>
                <td>DS</td>
                <td>SP</td>
                <td>BBP</td>
                <td>SPP</td>
                <td>CG5</td>
                <td>SC</td>
                <td>SP8</td>
            </tr>
        </thead>
        <tbody>
            <?php
			$no = 1;
			$totalAB 	= 0;
			$totalAR 	= 0;
			$totalASE 	= 0;
			$totalBB 	= 0;
			$totalCG 	= 0;
			$totalCGG 	= 0;
			$totalDB 	= 0;
			$totalDEP 	= 0;
			$totalDK 	= 0;
			$totalDS 	= 0;
			$totalSP 	= 0;
			$totalBBP 	= 0;
			$totalSPP 	= 0;
			$totalCG5 	= 0;
			$totalSC 	= 0;
			$totalSP8 	= 0;

			foreach ($rekappelanggan as $p) {

				$totalAB 	= $totalAB + $p->AB;
				$totalAR 	= $totalAR + $p->AR;
				$totalASE 	= $totalASE + $p->ASE;
				$totalBB 	= $totalBB + $p->BB;
				$totalCG 	= $totalCG + $p->CG;
				$totalCGG	= $totalCGG + $p->CGG;
				$totalDB 	= $totalDB + $p->DB;
				$totalDEP 	= $totalDEP + $p->DEP;
				$totalDK 	= $totalDK + $p->DK;
				$totalDS 	= $totalDS + $p->DS;
				$totalSP 	= $totalSP + $p->SP;
				$totalBBP 	= $totalBBP + $p->BBP;
				$totalSPP	= $totalSPP + $p->SPP;
				$totalCG5 	= $totalCG5 + $p->CG5;
				$totalSC 	= $totalSC + $p->SC;
				$totalSP8 	= $totalSP8 + $p->SP8;

			?>
            <tr>
                <td><?php echo $no; ?></td>
                <td><?php echo $p->kode_pelanggan; ?></td>
                <td><?php echo $p->nama_pelanggan; ?></td>
                <td><?php echo $p->pasar; ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->AB)) {
																		echo desimal($p->AB);
																	} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->AR)) {
																		echo desimal($p->AR);
																	} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->ASE)) {
																		echo desimal($p->ASE);
																	} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->BB)) {
																		echo desimal($p->BB);
																	} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->CG)) {
																		echo desimal($p->CG);
																	} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->CGG)) {
																		echo desimal($p->CGG);
																	} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->DB)) {
																		echo desimal($p->DB);
																	} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->DEP)) {
																		echo desimal($p->DEP);
																	} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->DK)) {
																		echo desimal($p->DK);
																	} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->DS)) {
																		echo desimal($p->DS);
																	} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->SP)) {
																		echo desimal($p->SP);
																	} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->BBP)) {
																		echo desimal($p->BBP);
																	} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->SPP)) {
																		echo desimal($p->SPP);
																	} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->CG5)) {
																		echo desimal($p->CG5);
																	} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->SC)) {
																		echo desimal($p->SC);
																	} ?></td>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($p->SP8)) {
																		echo desimal($p->SP8);
																	} ?></td>

            </tr>

            <?php
				$no++;
			} ?>
        </tbody>
        <tfoot>
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <td style="text-align:center; font-weight:bold" colspan="4">TOTAL</td>
                <td align="right"><?php if (!empty($totalAB)) {
										echo desimal($totalAB);
									} ?></td>
                <td align="right"><?php if (!empty($totalAR)) {
										echo desimal($totalAR);
									} ?></td>
                <td align="right"><?php if (!empty($totalASE)) {
										echo desimal($totalASE);
									} ?></td>
                <td align="right"><?php if (!empty($totalBB)) {
										echo desimal($totalBB);
									} ?></td>
                <td align="right"><?php if (!empty($totalCG)) {
										echo desimal($totalCG);
									} ?></td>
                <td align="right"><?php if (!empty($totalCGG)) {
										echo desimal($totalCGG);
									} ?></td>
                <td align="right"><?php if (!empty($totalDB)) {
										echo desimal($totalDB);
									} ?></td>
                <td align="right"><?php if (!empty($totalDEP)) {
										echo desimal($totalDEP);
									} ?></td>
                <td align="right"><?php if (!empty($totalDK)) {
										echo desimal($totalDK);
									} ?></td>
                <td align="right"><?php if (!empty($totalDS)) {
										echo desimal($totalDS);
									} ?></td>
                <td align="right"><?php if (!empty($totalSP)) {
										echo desimal($totalSP);
									} ?></td>
                <td align="right"><?php if (!empty($totalBBP)) {
										echo desimal($totalBBP);
									} ?></td>
                <td align="right"><?php if (!empty($totalSPP)) {
										echo desimal($totalSPP);
									} ?></td>
                <td align="right"><?php if (!empty($totalCG5)) {
										echo desimal($totalCG5);
									} ?></td>
                <td align="right"><?php if (!empty($totalSC)) {
										echo desimal($totalSC);
									} ?></td>
                <td align="right"><?php if (!empty($totalSP8)) {
										echo desimal($totalSP8);
									} ?></td>

            </tr>
        </tfoot>
    </table>
</body>
</html>
