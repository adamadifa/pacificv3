@foreach ($temp as $d)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->kode_item }}</td>
    <td>{{ $d->nama_item }}</td>
    <td>{{ $d->qty }}</td>
    <td style="text-align:right">{{ rupiah($d->harga) }}</td>
    <td style="text-align:right">{{ rupiah($d->qty * $d->harga) }}</td>
    <td>
        <a href="#" class="danger hapus" no_invoice={{ $d->no_invoice }} kode_item="{{ $d->kode_item }}"><i class="feather icon-trash"></i></a>
    </td>
</tr>
@endforeach


<script>
    $(function() {
        function loadtemp() {
            var no_invoice = $("#no_invoice").val();
            $.ajax({
                type: 'POST'
                , url: '/servicekendaraan/showtemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_invoice: no_invoice
                }
                , cache: false
                , success: function(respond) {
                    $("#loadtemp").html(respond);
                }
            });
        }
        $('.hapus').click(function(e) {
            e.preventDefault();
            var no_invoice = $(this).attr("no_invoice");
            var kode_item = $(this).attr("kode_item");
            $.ajax({
                type: 'POST'
                , url: '/servicekendaraan/deletetemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_invoice: no_invoice
                    , kode_item: kode_item
                }
                , cache: false
                , success: function(respond) {
                    loadtemp();
                }
            });
        });
    });

</script>
