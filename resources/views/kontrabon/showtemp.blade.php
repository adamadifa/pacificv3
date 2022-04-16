@php
$grandtotal = 0;
$jmldata = 1;
@endphp
@foreach ($detail as $d)
@php
$grandtotal += $d->jmlbayar;
@endphp
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->nobukti_pembelian }}</td>
    <td class="text-right">{{ desimal($d->jmlbayar) }}</td>
    <td>{{ $d->keterangan }}</td>
    <td>
        <a href="#" nobukti_pembelian="{{ $d->nobukti_pembelian }}" class="danger hapus"><i class="feather icon-trash"></i></a>
    </td>
</tr>
@php
$jmldata++;
@endphp
@endforeach
<tr>
    <th colspan="2" style="font-size: 14px">TOTAL</th>
    <th class="text-right" style="font-size: 14px" id="grandtotaltemp">{{ desimal($grandtotal) }}<input type="hidden" id="jmldata" value="{{ $jmldata -1 }}"></th>
    <th></th>
    <th></th>
</tr>
<script>
    $(function() {

        function loadtotal() {
            var grandtotal = $("#grandtotaltemp").text();
            $("#grandtotal").text(grandtotal);
        }

        loadtotal();

        function loaddetailkontrabontemp() {
            var kode_supplier = $("#kode_supplier").val();
            $.ajax({
                type: 'GET'
                , url: '/kontrabon/showtemp'
                , data: {
                    kode_supplier: kode_supplier
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailkontrabon").html(respond);
                    loadtotal();
                }
            });

        }
        $(".hapus").click(function(e) {
            e.preventDefault();
            var nobukti_pembelian = $(this).attr("nobukti_pembelian");
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
                            , url: '/kontrabon/deletetemp'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , nobukti_pembelian: nobukti_pembelian
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

                                loaddetailkontrabontemp();
                            }
                        });
                    }
                });
        });
    })

</script>
