<div class="row">
    <div class="col-12">
        <table class="table table-bordedanger">
            <tr>
                <td>No. fpb</td>
                <td>{{ $fpb->no_fpb }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>{{ DateToIndo2($fpb->tgl_permintaan) }}</td>
            </tr>
            <tr>
                <td>Nama Salesman</td>
                <td>{{ $fpb->id_karyawan }} - {{ $fpb->nama_karyawan }}</td>
            </tr>
            <tr>
                <td>Cabang</td>
                <td>{{ $fpb->kode_cabang }}</td>
            </tr>
            <tr>
                <td>No. Kendaraan</td>
                <td>{{ $fpb->no_kendaraan }}</td>
            </tr>
            <tr>
                <td>Tujuan</td>
                <td>{{ $fpb->tujuan }}</td>
            </tr>
            <tr>
                <td>Driver</td>
                <td>{{ $fpb->nama_driver }}</td>
            </tr>
            <tr>
                <td>Helper</td>
                <td>
                    {{ !empty($fpb->nama_helper_1) ? '(1) '.$fpb->nama_helper_1 : '' }}
                    {{ !empty($fpb->nama_helper_2) ? '(2) '.$fpb->nama_helper_2 : '' }}
                    {{ !empty($fpb->nama_helper_3) ? '(3) '.$fpb->nama_helper_3 : '' }}
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th rowspan="2" align="">Kode</th>
                    <th rowspan="2" style="text-align:center">Nama Barang</th>
                    <th colspan="2" style="text-align:center;">Jumlah</th>
                </tr>
                <tr>
                    <th style="text-align:center">Permintaan</th>
                    <th style="text-align:center">Realisasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detail as $d)
                @php
                $isipcsdus = $d->isipcsdus;
                $isipack = $d->isipack;
                $isipcs = $d->isipcs;
                $jmlpermintaan = $d->jml_permintaan / $isipcsdus;
                // $jmlpermintaan = $d->jml_permintaan;
                // $jmlpermintaan_dus = floor($jmlpermintaan / $isipcsdus);
                // if ($jmlpermintaan != 0) {
                // $sisadus_permintaan = $jmlpermintaan % $isipcsdus;
                // } else {
                // $sisadus_permintaan = 0;
                // }
                // if ($isipack == 0) {
                // $jmlpack_permintaan = 0;
                // $sisapack_permintaan = $sisadus_permintaan;
                // } else {
                // $jmlpack_permintaan = floor($sisadus_permintaan / $isipcs);
                // $sisapack_permintaan = $sisadus_permintaan % $isipcs;
                // }

                // $jmlpcs_permintaan = $sisapack_permintaan;
                @endphp

                <tr>
                    <td>{{ $d->kode_produk }}</td>
                    <td>{{ $d->nama_barang }}</td>
                    <td align="right">{{ number_format($jmlpermintaan, '3', ',', '.'); }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
