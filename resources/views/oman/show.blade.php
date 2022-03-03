<table class="table">
    <tr>
        <td>No. Order</td>
        <td>{{ $dataoman->no_order }}</td>
    </tr>
    <tr>
        <td>Bulan</td>
        <td>{{ $bulan[$dataoman->bulan] }}</td>
    </tr>
    <tr>
        <td>Tahun</td>
        <td>{{ $dataoman->tahun }}</td>
    </tr>
</table>
<table class="table table-bordered">
    <thead class="thead-dark">
        <tr>
            <th width="10px" rowspan="3" style="vertical-align: middle;">No</th>
            <th rowspan="3" style="vertical-align: middle; text-align: center;">Produk</th>
            <th colspan="12" style="text-align: center">Jumlah Permintaan</th>
            <th rowspan="3" style="vertical-align: middle;">Total</th>
        </tr>
        <tr>
            <th colspan="3" style="text-align:center">M1</th>
            <th colspan="3" style="text-align:center">M2</th>
            <th colspan="3" style="text-align:center">M3</th>
            <th colspan="3" style="text-align:center">M4</th>
        </tr>
        <tr>
            <th>{{ substr($m1->dari, 8, 2)  }}</th>
            <th style="width:10px;vertical-align: middle;">s/d</th>
            <th>{{ substr($m1->sampai, 8, 2)  }}</th>
            <th>{{ substr($m2->dari, 8, 2)  }}</th>
            <th style="width:10px;vertical-align: middle;">s/d</th>
            <th>{{ substr($m2->sampai, 8, 2)  }}</th>
            <th>{{ substr($m3->dari, 8, 2)  }}</th>
            <th style="width:10px;vertical-align: middle;">s/d</th>
            <th>{{ substr($m3->sampai, 8, 2)  }}</th>
            <th>{{ substr($m4->dari, 8, 2)  }}</th>
            <th style="width:10px;vertical-align: middle;">s/d</th>
            <th>{{ substr($m4->sampai, 8, 2)  }}</th>
        </tr>
    </thead>
    <tbody>
        @php
        $no=1;
        @endphp
        @foreach ($produk as $d)
        @php
        $subtotal= $d->mingguke_1 + $d->mingguke_2 + $d->mingguke_3 + $d->mingguke_4;
        @endphp
        <tr>
            <td>{{ $loop->iteration; }}</td>
            <td>{{ $d->kode_produk }}</td>
            <td align="right" colspan="3">{{ rupiah($d->mingguke_1) }}</td>
            <td align="right" colspan="3">{{ rupiah($d->mingguke_2) }}</td>
            <td align="right" colspan="3">{{ rupiah($d->mingguke_3) }}</td>
            <td align="right" colspan="3">{{ rupiah($d->mingguke_4) }}</td>
            <td align="right" colspan="3">{{ rupiah($subtotal)}}</td>
        </tr>
        @php
        $no++;
        @endphp
        @endforeach
    </tbody>
</table>
