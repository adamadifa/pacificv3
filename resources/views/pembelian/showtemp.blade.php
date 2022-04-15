@php
$grandtotal = 0;
$jmldata = 1;
@endphp
@foreach ($detailtemp as $d)
@php
$subtotal = $d->harga * $d->qty;
$total = $subtotal + $d->penyesuaian;
$grandtotal += $total;
@endphp
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->kode_barang }}</td>
    <td>{{ $d->nama_barang }}</td>
    <td>{{ $d->keterangan }}</td>
    <td class="text-center">{{ desimal($d->qty) }}</td>
    <td class="text-right">{{ desimal($d->harga) }}</td>
    <td class="text-right">{{ desimal($subtotal) }}</td>
    <td class="text-right">{{ desimal($d->penyesuaian) }}</td>
    <td class="text-right">{{ desimal($total) }}</td>
    <td class="text-center">{{ $d->kode_akun }}</td>
    <td class="text-center">{{ $d->kode_cabang }}</td>
    <td>
        <a href="#" class="danger hapus" data-id="{{ $d->id }}"><i class="feather icon-trash"></i></a>
    </td>
</tr>
@php
$jmldata++;
@endphp
@endforeach
<tr>
    <th colspan="8" style="font-size: 14px">TOTAL</th>
    <th class="text-right" style="font-size: 14px" id="grandtotaltemp">{{ desimal($grandtotal) }}<input type="hidden" id="jmldata" value="{{ $jmldata -1 }}"></th>
    <th colspan="3"></th>
</tr>

<script>
    $(function() {

        function loadtotal() {
            var grandtotal = $("#grandtotaltemp").text();
            $("#grandtotal").text(grandtotal);
        }

        function loaddetailpembeliantemp() {
            var kode_dept = $("#kode_dept").val();
            $.ajax({
                type: 'POST'
                , url: '/pembelian/showtemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_dept: kode_dept
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailpembelian").html(respond);
                    loadtotal();
                }
            });
        }
        $(".hapus").click(function(e) {
            e.preventDefault();
            var id = $(this).attr("data-id");
            event.preventDefault();
            swal({
                    title: `Anda Yakin Data ini Akan Dihapus ?`
                    , text: "Jika dihapus Data Ini Akan Hilang Dari Keranjang"
                    , icon: "warning"
                    , buttons: true
                    , dangerMode: true
                , })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type: 'POST'
                            , url: '/pembelian/deletetemp'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , id: id
                            }
                            , cache: false
                            , success: function(respond) {
                                if (respond == 0) {
                                    swal(
                                        'Deleted!'
                                        , 'Data Berhasil Dihapus'
                                        , 'success'
                                    )
                                } else {
                                    swal(
                                        'Deleted!'
                                        , 'Data Gagal Dihapus'
                                        , 'danger'
                                    )
                                }

                                loaddetailpembeliantemp();
                            }
                        });
                    }
                });
        });
    });

</script>
