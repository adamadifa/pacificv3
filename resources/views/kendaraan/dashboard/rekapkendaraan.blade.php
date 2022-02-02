<table class="table table-bordered table-striped table-hover">
    <thead class="thead-dark">
        <tr style="text-align: center;">
            <th>#</th>
            <th>No Polisi</th>
            <th>Jenis</th>
            <th>Kbrgktn</th>
            <th>Penjualan</th>
            <th>Rata Rata</th>
        </tr>
    </thead>
    <tbody>
        @php
        $no=1;
        @endphp
        @foreach ($rekapkendaraan as $d)
        @php
        if (!empty($d->jml_berangkat)) {
        $jmlberangkat = $d->jml_berangkat . " x";
        $rataratapnj = ROUND($d->jmlpenjualan / $d->jml_berangkat, 2);
        } else {
        $jmlberangkat = "";
        $rataratapnj = "";
        }
        @endphp
        <tr>
            <td>{{ $no; }}</td>
            <td>{{ $d->no_polisi }}</td>
            <td>{{ $d->model }}</td>
            <td align="right">{{ $jmlberangkat }}</td>
            <td align="right">{{ $d->jmlpenjualan }}</td>
            <td align="right">{{ $rataratapnj }}</td>
        </tr>
        @php
        $no++;
        @endphp
        @endforeach
    </tbody>
</table>
