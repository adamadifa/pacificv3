<div class="row">
    <div class="col-12">
        <table class="table">
            <tr>
                <td>No. Permintaan</td>
                <td>{{$permintaan->no_permintaan}}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>{{DateToIndo2($permintaan->tgl_permintaan)}}</td>
            </tr>
            <tr>
                <td>No. Order</td>
                <td>{{$permintaan->no_order}}</td>
            </tr>
            <tr>
                <td>Bulan</td>
                <td>{{$bulan[$permintaan->bulan]}}</td>
            </tr>
            <tr>
                <td>Tahun</td>
                <td>{{$permintaan->tahun}}</td>
            </tr>
        </table>

    </div>
</div>

<div class="row">
    <div class="col-12">
        <table class="table table-hover-animation">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Produk</th>
                    <th>Nama Barang</th>
                    <th>Oman Mkt</th>
                    <th>Stok Gudang</th>
                    <th>Buffer Stok</th>
                    <th>Total Permintaan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detail as $d)
                @php
                $totalpermintaan = $d->oman_mkt - $d->stok_gudang + $d->buffer_stok;
                @endphp
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$d->kode_produk}}</td>
                    <td>{{$d->nama_barang}}</td>
                    <td class="text-right">{{rupiah($d->oman_mkt)}}</td>
                    <td class="text-right">{{rupiah($d->stok_gudang)}}</td>
                    <td class="text-right">{{rupiah($d->buffer_stok)}}</td>
                    <td class="text-right">{{rupiah($totalpermintaan)}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
