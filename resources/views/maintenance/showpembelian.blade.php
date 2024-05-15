<table class="table table-bordered">
    <tr>
        <td>No. Bukti</td>
        <td>{{ $pembelian->nobukti_pembelian }}</td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>{{ DateToIndo2($pembelian->tgl_pembelian) }}</td>
    </tr>
    <tr>
        <td>Supplier</td>
        <td>{{ $pembelian->kode_supplier }} - {{ $pembelian->nama_supplier }}</td>
    </tr>
    <tr>
        <td>Departemen</td>
        <td>{{ $pembelian->nama_dept }}</td>
    </tr>
    <tr>
        <td>PPN</td>
        <td class="success">
            @if (!empty($pembelian->ppn))
                <i class="fa fa-check"></i> {{ $pembelian->no_fak_pajak }}
            @endif
        </td>
    </tr>
</table>
<table class="table table-hover-animation">
    <thead class="thead-dark">
        <tr>
            <th colspan="8">Data Pembelian</th>
        </tr>
        <tr>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Keterangan</th>
            <th>Qty</th>
            {{-- <th>Harga</th>
            <th>Subtotal</th>
            <th>Penyesuaian</th>
            <th>Total</th> --}}
        </tr>
    </thead>
    <tbody>
        @php
            $totalpembelian = 0;
        @endphp
        @foreach ($detailpembelian as $d)
            @php
                $total = $d->qty * $d->harga + $d->penyesuaian;
                $totalpembelian += $total;
            @endphp
            <tr>
                <td>{{ $d->kode_barang }}</td>
                <td>{{ $d->nama_barang }}</td>
                <td>{{ $d->keterangan }}</td>
                <td class="text-center">{{ desimal($d->qty) }}</td>
                {{-- <td class="text-right">{{ desimal($d->harga) }}</td>
                <td class="text-right">{{ desimal($d->harga * $d->qty) }}</td>
                <td class="text-right">{{ desimal($d->penyesuaian) }}</td>
                <td class="text-right">{{ desimal($total) }}</td> --}}
            </tr>
        @endforeach
        {{-- <tr class="thead-dark">
            <th colspan="">TOTAL</th>
            <th class="text-righ">{{ desimal($totalpembelian) }}</th>
        </tr> --}}
    </tbody>
</table>
<form action="/maintenance/{{ Crypt::encrypt($pembelian->nobukti_pembelian) }}/storepembelian" method="post">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block"><i class="fa fa-check mr-1"></i>Approve</button>
            </div>
        </div>
    </div>
</form>
