<table class="table">
    <tr>
        <td>No. Permintaan Pengiriman</td>
        <td>{{ $pp->no_permintaan_pengiriman }}</td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>{{ date("d-m-Y",strtotime($pp->tgl_permintaan_pengiriman)) }}</td>
    </tr>
    <tr>
        <td>Kode Cabang</td>
        <td>{{ strtoupper($pp->kode_cabang) }}</td>
    </tr>
    <tr>
        <td>Keterangan</td>
        <td>{{ $pp->keterangan }}</td>
    </tr>
    <tr>
        <td>Status</td>
        <td>
            @if ($pp->status==1)
            <span class="badge bg-success"><i class="fa fa-check mr-1"></i> Sudah Di Proses</span>
            @else
            <span class="badge bg-danger"><i class="fa fa-history mr-1"></i> Belum Di Proses</span>
            @endif
        </td>
    </tr>
</table>
<table class="table">
    <thead>
        <tr>
            <th>Kode Produk</th>
            <th>Nama Produk</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detail as $d)
        <tr>
            <td>{{ $d->kode_produk }}</td>
            <td>{{ ucwords(strtolower($d->nama_barang)) }}</td>
            <td style="width:20%" class="text-right">
                @if ($pp->status==1)
                {{ rupiah($d->jumlah) }}
                @else
                <input type="text" class="form-control update text-right" kodeproduk="{{ $d->kode_produk }}" value="{{ rupiah($d->jumlah) }}">
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@if ($sj != null)
<table class="table">
    <tr>
        <th colspan="2">Data Surat Jalan</th>
    </tr>
    <tr>
        <td>No. Surat Jalan</td>
        <td>{{ $sj->no_mutasi_gudang }}</td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>{{ date("d-m-Y",strtotime($sj->tgl_mutasi_gudang)) }}</td>
    </tr>
    <tr>
        <td>Status</td>
        <td>
            @if ($sj->status_sj==1)
            <span class="badge bg-success"><i class="fa fa-check mr-1"></i> Sudah Diterima Cabang</span>
            @else
            <span class="badge bg-danger"><i class="fa fa-history mr-1"></i> Belum Diterima Cabang</span>
            @endif
        </td>
    </tr>
</table>

<table class="table">
    <thead>
        <tr>
            <th colspan="3">Realisasi Permintaan</th>
        </tr>
        <tr>
            <th>Kode Produk</th>
            <th>Nama Produk</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detailsj as $d)
        <tr>
            <td>{{ $d->kode_produk }}</td>
            <td>{{ ucwords(strtolower($d->nama_barang)) }}</td>
            <td style="width:20%" class="text-right">
                {{ rupiah($d->jumlah) }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<script>
    $(function() {

        $('.update').maskMoney();

        $('.update').keyup(function() {
            var no_permintaan_pengiriman = "{{ $pp->no_permintaan_pengiriman }}";
            var kode_produk = $(this).attr('kodeproduk');
            var jumlah = $(this).val();
            $.ajax({
                type: 'POST'
                , url: '/permintaanpengiriman/updatedetail'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_permintaan_pengiriman: no_permintaan_pengiriman
                    , kode_produk: kode_produk
                    , jumlah: jumlah
                }
                , cache: false
                , success: function(respond) {

                }
            });
        });
    });

</script>
