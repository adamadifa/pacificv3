<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <tr>
                <td>Kode Saldo Awal</td>
                <td>{{ $saldoawal->kode_saldoawal }}</td>
            </tr>
            <tr>
                <td>Bulan</td>
                <td>{{ $bulan[$saldoawal->bulan] }}</td>
            </tr>
            <tr>
                <td>Tahun</td>
                <td>{{ $saldoawal->tahun }}</td>
            </tr>
            <tr>
                <td>Cabang</td>
                <td>{{ $saldoawal->kode_cabang }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>{{ $saldoawal->status == "GS" ? 'Good Stok' : 'Bad Stok' }}</td>
            </tr>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th rowspan="3" align="">No</th>
                    <th rowspan="3" style="text-align:center">Nama Barang</th>
                    <th colspan="3" style="text-align:center">Penjualan</th>
                    <th rowspan="2" colspan="2" style="text-align:center">Total</th>
                </tr>
                <tr>
                    <th colspan="3" style="text-align:center">Kuantitas</th>
                </tr>
                <tr>
                    <th>DUS</th>
                    <th>PACK</th>
                    <th>PCS</th>
                    <th style="text-align:center">Jumlah</th>
                    <th style="text-align:center">Satuan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($detail as $d) {
                    $jumlah = $d->jumlah / $d->isipcsdus;
                    $jmldus = floor($d->jumlah / $d->isipcsdus);
                    if ($d->jumlah != 0) {
                        $sisadus   = $d->jumlah % $d->isipcsdus;
                    } else {
                        $sisadus = 0;
                    }
                    if ($d->isipack == 0) {
                        $jmlpack    = 0;
                        $sisapack   = $sisadus;
                        $s          = "A";
                    } else {
                        $jmlpack    = floor($sisadus / $d->isipcs);
                        $sisapack   = $sisadus % $d->isipcs;
                        $s          = "B";
                    }
                    $jmlpcs = $sisapack;
                ?>
                <tr>
                    <td><?php echo $no; ?></td>
                    <td><?php echo $d->nama_barang; ?></td>
                    <td class="text-right"><?php if (!empty($jmldus)) {
                                    echo rupiah($jmldus);
                                } ?></td>
                    <td class="text-right"><?php if (!empty($jmlpack)) {
                                    echo $jmlpack;
                                } ?></td>
                    <td class="text-right"><?php if (!empty($jmlpcs)) {
                                    echo $jmlpcs;
                                } ?></td>
                    <td class="text-right"><?php echo desimal($jumlah); ?></td>
                    <td><?php echo $d->satuan; ?></td>

                </tr>
                <?php
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
