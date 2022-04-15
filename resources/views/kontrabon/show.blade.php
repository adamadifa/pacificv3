<table class="table">
    <tr>
        <td>No. Kontrabon</td>
        <td>{{ $kontrabon->no_kontrabon }}</td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>{{ DateToIndo2($kontrabon->tgl_kontrabon) }}</td>
    </tr>
    <tr>
        <td>Terima Dari</td>
        <td>{{ $kontrabon->nama_supplier }}</td>
    </tr>
</table>
<table class="table table-hover-animation">
    <thead class="thead-dark">
        <tr>
            <th>No.</th>
            <th>Tanggal</th>
            <th>No. Bukti</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @php
        $totalkontrabon = 0;
        @endphp
        @foreach ($detailkontrabon as $d)
        @php
        $totalkontrabon += $d->jmlbayar;
        @endphp
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ date("d-m-Y",strtotime($d->tgl_pembelian)) }}</td>
            <td><a href="#" class="detailpembelian" nobukti_pembelian="{{ $d->nobukti_pembelian }}">{{ $d->nobukti_pembelian }}</a></td>
            <td class="text-right">{{ desimal($d->jmlbayar) }}</td>
        </tr>
        @endforeach
        <tr class="thead-dark">
            <th colspan="3">TOTAL</th>
            <th class="text-right">{{ desimal($totalkontrabon) }}</th>
        </tr>
    </tbody>
</table>
<table class="table table-hover-animation">
    <thead class="thead-dark">
        <tr>
            <th colspan="9">Data Pembelian <span id="nobuktipembelian"></span></th>
        </tr>
        <tr>
            <th>No</th>
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
    <tbody id="loaddetailpembelian"></tbody>
</table>
<script>
    $(function() {
        function loaddetailpembelian(nobukti_pembelian) {
            $.ajax({
                type: 'POST'
                , url: '/pembelian/showdetailpembeliankontrabon'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , nobukti_pembelian: nobukti_pembelian
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailpembelian").html(respond);
                }
            });
        }

        $(".detailpembelian").click(function() {
            var nobukti_pembelian = $(this).attr("nobukti_pembelian");
            $("#nobuktipembelian").text(nobukti_pembelian);
            loaddetailpembelian(nobukti_pembelian);
        });

    });

</script>
