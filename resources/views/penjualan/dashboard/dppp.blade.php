<table class="table table-bordered table-striped table-hover">
    <thead class="thead-dark">
        <tr style="text-align: center;">
            <th rowspan="2">#</th>
            <th rowspan="2">Produk</th>
            <th colspan="5">Per Bulan</th>
            <th colspan="5">Sampai Dengan Bulan</th>
        </tr>
        <tr>
            <th>Real <?php echo $tahunlalu; ?></th>
            <th>Target</th>
            <th>Real <?php echo $tahunini; ?></th>
            <th>Ach %</th>
            <th>Grw %</th>
            <th>Real <?php echo $tahunlalu; ?></th>
            <th>Target</th>
            <th>Real <?php echo $tahunini; ?></th>
            <th>Ach %</th>
            <th>Grw %</th>
        </tr>
    </thead>
    <tbody>
        @php
        $no =1;
        @endphp

        @foreach ($dppp as $d)
        @php
        $realisasi_bulanini_tahunlalu = ROUND($d->realisasi_bulanini_tahunlalu / $d->isipcsdus, 2);
        $realisasi_bulanini_tahunini = ROUND($d->realisasi_bulanini_tahunini / $d->isipcsdus, 2);
        $realisasi_sampaibulanini_tahunlalu = ROUND($d->realisasi_sampaibulanini_tahunlalu / $d->isipcsdus, 2);
        $realisasi_sampaibulanini_tahunini = ROUND($d->realisasi_sampaibulanini_tahunini / $d->isipcsdus, 2);
        $cek = ($d->realisasi_bulanini_tahunini / $d->isipcsdus);
        //echo $d->realisasi_bulanini_tahunini . "/" . $d->isipcsdus . "=" . $cek . "<br>";
        if (!empty($d->jmltarget)) {
        $ach_bulanini = ($realisasi_bulanini_tahunini / $d->jmltarget) * 100;
        } else {
        $ach_bulanini = 0;
        }

        if (!empty($d->jmltarget_sampaibulanini)) {
        $ach_sampaibulanini = ($realisasi_sampaibulanini_tahunini / $d->jmltarget_sampaibulanini) * 100;
        } else {
        $ach_sampaibulanini = 0;
        }

        if (!empty($realisasi_bulanini_tahunlalu)) {
        $grw_bulanini = ($realisasi_bulanini_tahunini / $realisasi_bulanini_tahunlalu) * 100;
        } else {
        $grw_bulanini = 0;
        }

        if (!empty($realisasi_sampaibulanini_tahunlalu)) {
        $grw_sampaibulanini = ($realisasi_sampaibulanini_tahunini / $realisasi_sampaibulanini_tahunlalu) * 100;
        } else {
        $grw_sampaibulanini = 0;
        }
        @endphp
        <tr>
            <td>{{ $no; }}</td>
            <td>{{ $d->kode_produk }}</td>
            <td align="right">{{desimal($realisasi_bulanini_tahunlalu)}}</td>
            <td align="right">{{desimal($d->jmltarget)}}</td>
            <td align="right">{{desimal($realisasi_bulanini_tahunini)}}</td>
            <td align="right" class="{{ ($ach_bulanini < 100 ? 'text-danger' : 'text-info') }} font-weight-bold">{{desimal($ach_bulanini)}}</td>
            <td align="right" class="text-success font-weight-bold">{{desimal($grw_bulanini)}}</td>
            <td align="right">{{desimal($realisasi_sampaibulanini_tahunlalu)}}</td>
            <td align="right">{{desimal($d->jmltarget_sampaibulanini)}}</td>
            <td align="right">{{desimal($realisasi_sampaibulanini_tahunini)}}</td>
            <td align="right" class="{{ ($ach_sampaibulanini < 100 ? 'text-danger' : 'text-info') }}  font-weight-bold">{{desimal($ach_sampaibulanini)}}</td>
            <td align="right" class="text-success font-weight-bold">{{desimal($grw_sampaibulanini)}}</td>

        </tr>
        @php
        $no++;
        @endphp
        @endforeach
    </tbody>
</table>
