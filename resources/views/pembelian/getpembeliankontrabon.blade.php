<table class="table" id="tabelpembelian">
    <thead class="thead-dark">
        <tr>
            <th width="10px">No</th>
            <th>No Bukti</th>
            <th>Tanggal</th>
            <th>Dept</th>
            <th>PPn</th>
            <th>Total</th>
            <th>JT</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pembelian as $d)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $d->nobukti_pembelian }}</td>
            <td>{{ date("d-m-Y",strtotime($d->tgl_pembelian)) }}</td>
            <td>{{ $d->kode_dept }}</td>
            <td>
                @if (!empty($d->ppn))
                <i class="fa fa-check success"></i>
                @endif
            </td>
            <td class="text-right">{{ desimal($d->totalpembelian) }}</td>
            <td>{{ ucwords($d->jenistransaksi) }}</td>
            <td>
                <a href="#" nobukti_pembelian="{{ $d->nobukti_pembelian }}" totalpembelian="{{ desimal($d->totalpembelian) }}" jmlbayar="{{ $d->jmlbayar }}" class="pilih success"><i class="feather icon-external-link"></i></a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $("#tabelpembelian").DataTable();
        $('#tabelpembelian tbody').on('click', '.pilih', function() {
            var nobukti_pembelian = $(this).attr('nobukti_pembelian');
            var totalpembelian = $(this).attr('totalpembelian');
            var jmlbayar = $(this).attr('jmlbayar');
            $("#nobukti_pembelian").val(nobukti_pembelian);
            $("#totalpembelian").val(totalpembelian);
            $("#totalbayar").val(jmlbayar);
            $("#mdlpembelian").modal('hide');
        });
    });

</script>
