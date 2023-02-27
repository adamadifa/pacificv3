<table class="table table-bordered">
    <tr>
        <th>No. Invoice</th>
        <td>{{ $service->no_invoice }}</td>
    </tr>
    <tr>
        <th>Tanggal</th>
        <td>{{ DateToIndo2($service->tgl_service) }}</td>
    </tr>
    <tr>
        <th>No. Polisi</th>
        <td>{{ $service->no_polisi }}</td>
    </tr>
    <tr>
        <th>Bengkel</th>
        <td>{{ $service->nama_bengkel }}</td>
    </tr>
    <tr>
        <th>Keterangan</th>
        <td>{{ $service->keterangan }}</td>
    </tr>
</table>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>No.</th>
            <th>Kode</th>
            <th>Nama Item</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @php
        $total = 0;
        @endphp
        @foreach ($detail as $d)
        @php
        $total += $d->qty * $d->harga;
        @endphp
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $d->kode_item }}</td>
            <td>{{ $d->nama_item }}</td>
            <td>{{ $d->qty }}</td>
            <td style="text-align: right">{{ rupiah($d->harga) }}</td>
            <td style="text-align: right">{{ rupiah($d->harga * $d->qty) }}</td>
        </tr>
        @endforeach
        <tr>
            <th colspan="5">Total</th>
            <th style="text-align: right">{{ rupiah($total) }}</th>
        </tr>
    </tbody>
</table>
