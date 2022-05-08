<div class="row">
    <div class="col-12">
        <table class="table table-bordedanger">
            <tr>
                <td>No. DPB</td>
                <td>{{ $dpb->no_dpb }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>{{ DateToIndo2($dpb->tgl_pengambilan) }}</td>
            </tr>
            <tr>
                <td>Nama Salesman</td>
                <td>{{ $dpb->id_karyawan }} - {{ $dpb->nama_karyawan }}</td>
            </tr>
            <tr>
                <td>Cabang</td>
                <td>{{ $dpb->kode_cabang }}</td>
            </tr>
            <tr>
                <td>No. Kendaraan</td>
                <td>{{ $dpb->no_kendaraan }}</td>
            </tr>
            <tr>
                <td>Tujuan</td>
                <td>{{ $dpb->tujuan }}</td>
            </tr>
            <tr>
                <td>Driver</td>
                <td>{{ $dpb->nama_driver }}</td>
            </tr>
            <tr>
                <td>Helper</td>
                <td>
                    1. {{ $dpb->nama_helper_1 }}<br>
                    2. {{ $dpb->nama_helper_2 }}<br>
                    3. {{ $dpb->nama_helper_3 }}<br>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table class="table table-bordedanger">
            <thead class="thead-dark">
                <tr>
                    <th rowspan="4" align="">No</th>
                    <th rowspan="4" style="text-align:center;">Nama Barang</th>
                    <th colspan="2" style="text-align:center">Pengambilan</th>
                    <th colspan="2" style="text-align:center">Pengembalian</th>
                    <th rowspan="4" style="text-align:center">Barang Keluar</th>
                </tr>
                <tr>
                    <th colspan="2" style="text-align:center">
                        <?php echo $dpb->tgl_pengambilan; ?>
                    </th>
                    <th colspan="2" style="text-align:center">
                        <?php echo $dpb->tgl_pengembalian; ?>
                    </th>
                </tr>
                <tr>
                    <th colspan="2" style="text-align:center">Kuantitas</th>
                    <th colspan="2" style="text-align:center">Kuantitas</th>
                </tr>
                <tr>
                    <th style="text-align:center">Jumlah</th>
                    <th style="text-align:center">Satuan</th>
                    <th style="text-align:center">Jumlah</th>
                    <th style="text-align:center">Satuan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($detail as $d) {
                    $jmlambil = number_format($d->jml_pengambilan, '3', ',', '.');
                    $jmlpengambilan = explode(",", $jmlambil);

                    $jmlkembali = number_format($d->jml_pengembalian, '3', ',', '.');
                    $jmlpengembalian = explode(",", $jmlkembali);
                    // if (empty(floatval($d->jml_pengembalian))) {
                    // 	echo "Kosong";
                    // } else {
                    // 	echo "ada";
                    // }
                    $jmlkembali = str_replace(",", ".", $jmlkembali);
                    $jmlambil = str_replace(",", ".", $jmlambil);

                ?>
                <tr>
                    <td><?php echo $no; ?></td>
                    <td><?php echo $d->nama_barang; ?></td>
                    <td align="right">
                        <?php if (!empty(floatval($jmlambil))) { ?>
                        <b>
                            <font color="black"><?php echo $jmlpengambilan[0]; ?></font>
                        </b>
                        <?php if (!empty($jmlpengambilan[1])) { ?>
                        ,<font color="red"><?php echo $jmlpengambilan[1]; ?></font>
                        <?php } ?>
                        <?php } ?>
                    </td>
                    <td align="center"><?php echo $d->satuan; ?></td>
                    <td align="right">
                        <?php if (!empty(floatval($jmlkembali))) { ?>
                        <b>
                            <font color="black"><?php echo $jmlpengembalian[0]; ?></font>
                        </b>
                        <?php if (!empty($jmlpengembalian[1])) { ?>
                        ,<font color="red"><?php echo $jmlpengembalian[1]; ?></font>
                        <?php } ?>
                        <?php } ?>
                    </td>
                    <td align="center"><?php echo $d->satuan; ?></td>
                    <td align="right"><?php echo number_format($d->jml_penjualan, '3', ',', '.'); ?></td>
                </tr>
                <?php
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table class="table table-bordedanger">
            <thead class="thead-dark">
                <tr>
                    <th colspan="9">Detail DPB</th>
                </tr>
                <tr>
                    <th rowspan="3">Nama Produk</th>
                    <th class="text-center" colspan="9">Mutasi</th>
                </tr>
                <tr>
                    <th class="text-center" colspan="3">IN</th>
                    <th class="text-center" colspan="6">OUT</th>
                </tr>
                <tr>
                    <th>RETUR</th>
                    <th>PL TTR</th>
                    <th>HK</th>
                    <th>PENJUALAN</th>
                    <th>PL HK</th>
                    <th>PROMO</th>
                    <th>TTR</th>
                    <th>GB</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($mutasidpb as $m) {
                    $retur = $m->retur / $m->isipcsdus;
                    $plttr = $m->pelunasanttr / $m->isipcsdus;
                    $pnj 	 = $m->penjualan / $m->isipcsdus;
                    $hk 	 = $m->hutangkirim / $m->isipcsdus;
                    $plhk  = $m->plhutangkirim / $m->isipcsdus;
                    $promo = $m->promosi / $m->isipcsdus;
                    $ttr   = $m->ttr / $m->isipcsdus;
                    $gb  	 = $m->gantibarang / $m->isipcsdus;
                ?>
                <tr>
                    <td><?php echo $m->nama_barang; ?></td>
                    <td align="right">
                        <?php if(!empty($retur)){ ?>
                        <span class="badge bg-success">{{ desimal3($retur) }}</span>
                        <?php } ?>
                    </td>
                    <td align="right">
                        <?php if(!empty($plttr)){ ?>
                        <span class="badge bg-success">{{ desimal3($plttr) }}</span>
                        <?php } ?>
                    </td>
                    <td align="right">
                        <?php if(!empty($hk)){ ?>
                        <span class="badge bg-success">{{ desimal3($hk) }}</span>
                        <?php } ?>
                    </td>
                    <td align="right">
                        <?php if(!empty($pnj)){ ?>
                        <span class="badge bg-danger">{{ desimal3($pnj) }}</span>
                        <?php } ?>
                    </td>
                    <td align="right">
                        <?php if(!empty($plhk)){ ?>
                        <span class="badge bg-danger">{{ desimal3($plhk) }}</span>
                        <?php } ?>
                    </td>
                    <td align="right">
                        <?php if(!empty($promo)){ ?>
                        <span class="badge bg-danger">{{ desimal3($promo) }}</span>
                        <?php } ?>
                    </td>
                    <td align="right">
                        <?php if(!empty($ttr)){ ?>
                        <span class="badge bg-danger">{{ desimal3($ttr) }}</span>
                        <?php } ?>
                    </td>
                    <td align="right">
                        <?php if(!empty($gb)){ ?>
                        <span class="badge bg-danger">{{ desimal3($gb) }}</span>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
