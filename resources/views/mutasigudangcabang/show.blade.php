<table class="table">
    <tr>
        <td>No. Mutasi</td>
        <td>{{ $mutasi->no_mutasi_gudang_cabang }}</td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>{{ DateToIndo2($mutasi->tgl_mutasi_gudang_cabang) }}</td>
    </tr>
</table>
<table class="table">
    <tr>
        <th>No. DPB</th>
        <th>Nama Salesman</th>
        <th>Tujuan</th>
        <th>No.Kendaraan</th>
    </tr>
    <tr>
        <td>{{ $mutasi->no_dpb }}</td>
        <td>{{ $mutasi->nama_karyawan }}</td>
        <td>{{ $mutasi->tujuan }}</td>
        <td>{{ $mutasi->no_kendaraan }}</td>
    </tr>
    <tr>
        <th colspan="2">Driver</th>
        <th colspan="2">Helper</th>
    </tr>
    <tr>
        <td colspan="2">{{ $mutasi->nama_driver; }}</td>
        <td colspan="2">
            {{ !empty($mutasi->nama_helper_1) ? '(1) '.$mutasi->nama_helper_1 : '' }}
            {{ !empty($mutasi->nama_helper_2) ? '(2) '.$mutasi->nama_helper_2 : '' }}
            {{ !empty($mutasi->nama_helper_3) ? '(3) '.$mutasi->nama_helper_3 : '' }}
        </td>
    </tr>
</table>
<table class="table table-bordered">
    <thead class="thead-dark">
        <tr>
            <th rowspan="3" align="">No</th>
            <th rowspan="3" style="text-align:center">Nama Barang</th>
            <th colspan="3" style="text-align:center">{{ ucwords(strtolower($mutasi->jenis_mutasi)) }}</th>
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
        @foreach ($detail as $d)
        @php
        $jumlah = $d->jumlah / $d->isipcsdus;
        $jmldus = floor($d->jumlah / $d->isipcsdus);
        if ($d->jumlah != 0) {
        $sisadus = $d->jumlah % $d->isipcsdus;
        } else {
        $sisadus = 0;
        }
        if ($d->isipack == 0) {
        $jmlpack = 0;
        $sisapack = $sisadus;
        $s = "A";
        } else {
        $jmlpack = floor($sisadus / $d->isipcs);
        $sisapack = $sisadus % $d->isipcs;
        $s = "B";
        }
        $jmlpcs = $sisapack;
        @endphp
        <tr>
            <td>{{ $d->kode_produk }}</td>
            <td>{{ $d->nama_barang }}</td>
            <td><?php if (!empty($jmldus)) {
                echo $jmldus;
            } ?></td>
            <td><?php if (!empty($jmlpack)) {
                echo $jmlpack;
            } ?></td>
            <td><?php if (!empty($jmlpcs)) {
                echo $jmlpcs;
            } ?></td>
            <td align="right"><?php echo desimal($jumlah); ?></td>
            <td><?php echo $d->satuan; ?></td>
        </tr>
        @endforeach
    </tbody>
</table>
