@php
$grandtotal=0;
@endphp
@foreach ($split as $d)
@php
$subtotal = $d->qty * $d->harga;
$total = $subtotal + $d->penyesuaian;
$grandtotal += $total;
@endphp
<tr style="font-size: 12px">
    <td>{{ $d->kode_barang }}</td>
    <td>{{ $d->nama_barang }}</td>
    <td>{{ $d->keterangan }}</td>
    <td>{{ desimal($d->qty) }}</td>
    <td>{{ desimal($d->harga) }}</td>
    <td>{{ desimal($subtotal) }}</td>
    <td>{{ desimal($d->penyesuaian) }}</td>
    <td>{{ $d->kode_akun }} {{ $d->nama_akun }}</td>
    <td>{{ desimal($total) }}</td>
    <td>{{ $d->kode_cabang }}</td>
    <td>
        <a href="#" class="hapus" data-id="{{ $d->id }}"><i class="feather icon-trash danger"></i></a>
    </td>
</tr>
@endforeach
<tr>
    <th colspan="8">TOTAL</th>
    <th style="text-align: right">{{ rupiah($grandtotal) }}
        <input type="hidden" id="totalsplit" value="{{ $grandtotal }}">
    </th>
    <th colspan="2"></th>
</tr>

<script>
    $(function() {
        function loadsplit() {
            var no_bukti = $("#no_bukti").val();
            var kode_barang_old = $("#frmEditbarang").find("#kode_barang").val();
            $("#loadsplit").load('/pembelian/' + no_bukti + '/' + kode_barang_old + '/showsplit');
        }

        $(".hapus").click(function(e) {
            e.preventDefault();
            let id = $(this).attr("data-id");
            $.ajax({
                type: 'POST'
                , url: '/pembelian/deletesplit'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , id: id
                }
                , cache: false
                , success: function(respond) {
                    loadsplit();
                }
            });
        });
    });

</script>
