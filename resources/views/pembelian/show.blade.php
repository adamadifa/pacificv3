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
        <td>{{ $pembelian->kode_supplier  }} - {{ $pembelian->nama_supplier }}</td>
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
            <th>Harga</th>
            <th>Subtotal</th>
            <th>Penyesuaian</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @php
        $totalpembelian = 0;
        @endphp
        @foreach ($detailpembelian as $d)
        @php
        $total = ($d->qty * $d->harga) + $d->penyesuaian;
        $totalpembelian += $total;
        @endphp
        <tr>
            <td>{{ $d->kode_barang }}</td>
            <td>{{ $d->nama_barang }}</td>
            <td>{{ $d->keterangan }}</td>
            <td class="text-center">{{ desimal($d->qty) }}</td>
            <td class="text-right">{{ desimal($d->harga) }}</td>
            <td class="text-right">{{ desimal($d->harga * $d->qty) }}</td>
            <td class="text-right">{{ desimal($d->penyesuaian) }}</td>
            <td class="text-right">{{ desimal($total) }}</td>
        </tr>
        @endforeach
        <tr class="thead-dark">
            <th colspan="7">TOTAL</th>
            <th class="text-righ">{{ desimal($totalpembelian) }}</th>
        </tr>
    </tbody>
</table>

<table class="table table-hover-animation">
    <thead class="thead-danger">
        <tr>
            <th colspan="4">POTONGAN</th>
        </tr>
        <tr>
            <th>Keterangan</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Total</th>
        </tr>
    </thead>
    @php
    $totalpenjualan =0;
    @endphp
    @foreach ($detailpenjualan as $d)
    @php
    $total = $d->qty * $d->harga;
    $totalpenjualan += $total;
    @endphp
    <tr>
        <td>{{ $d->ket_penjualan }}</td>
        <td class="text-center">{{ desimal($d->qty) }}</td>
        <td class="text-right">{{ desimal($d->harga) }}</td>
        <td class="text-right">{{ desimal($total) }}</td>
    </tr>
    @endforeach
    <tr class="thead-danger">
        <th colspan="3">TOTAL POTONGAN</th>
        <th class="text-right">{{ desimal($totalpenjualan) }}</th>
    </tr>
    <tr class="thead-info">
        <th colspan="3">GRAND TOTAL</th>
        <th class="text-right">{{ desimal($totalpembelian - $totalpenjualan) }}</th>
    </tr>
</table>
<table class="table table-hover-animation">
    <thead class="thead-success">
        <tr>
            <th colspan="6">HISTORI KONTRA BON</th>
        </tr>
        <tr>
            <th>No Kontra BON</th>
            <th>Tanggal</th>
            <th>Jumlah</th>
            <th>Jenis Pengajuan</th>
            <th>Tgl Cair</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($kontrabon as $d)
        @if ($d->kategori=="TN")
        @php
        $kategori = "TUNAI";
        @endphp
        @else
        @php
        $kategori = $d->kategori;
        @endphp
        @endif
        <tr>
            <td>{{ $d->no_kontrabon }}</td>
            <td>{{ DateToIndo2($d->tgl_kontrabon) }}</td>
            <td>{{ desimal($d->jmlbayar) }}</td>
            <td>{{ $kategori }}</td>
            <td>
                @if (empty($d->tglbayar))
                <span class="badge bg-danger">Belum Bayar</span>
                @else
                <span class="badge bg-success">{{ DateToIndo2($d->tglbayar) }}</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
