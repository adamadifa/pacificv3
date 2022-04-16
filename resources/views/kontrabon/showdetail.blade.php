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
        <div class="btn-group" role="group" aria-label="Basic example">
            <a href="#" class="success edit" nobukti_pembelian="{{ $d->nobukti_pembelian }}" jmlbayar="{{ desimal($d->jmlbayar) }}"><i class="feather icon-edit"></i></a>
            <a href="#" nobukti_pembelian="{{ $d->nobukti_pembelian }}" no_kontrabon="{{ $d->no_kontrabon }}" class="danger ml-1 hapus"><i class="feather icon-trash"></i></a>
        </div>
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

        function loaddetailkontrabon() {
            var no_kontrabon = $("#no_kontrabon").val();
            $.ajax({
                type: 'GET'
                , url: '/kontrabon/showdetail'
                , data: {
                    no_kontrabon: no_kontrabon
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
            var no_kontrabon = $(this).attr("no_kontrabon");
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
                            , url: '/kontrabon/deletedetail'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , no_kontrabon: no_kontrabon
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

                                loaddetailkontrabon();
                            }
                        });
                    }
                });
        });

        $(".edit").click(function(e) {
            e.preventDefault();
            var no_kontrabon = $("#no_kontrabon").val();
            var nobukti_pembelian = $(this).attr("nobukti_pembelian");
            var jmlbayar = $(this).attr("jmlbayar");
            $('#mdleditdetail').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#nobukti_pembelian_edit").val(nobukti_pembelian);
            $("#jmlbayar_edit").val(jmlbayar);
        });
    })

</script>
