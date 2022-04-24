@foreach ($detail as $d)
<tr>
    <td>{{ $d->kode_produk }}</td>
    <td>{{ $d->nama_barang }}</td>
    <td>{{ $d->shift }}</td>
    <td class="text-right">{{ rupiah($d->jumlah) }}</td>
    <td>
        <a href="#" kode_produk="{{ $d->kode_produk }}" shift="{{ $d->shift }}" class="hapus"><i class="feather icon-trash danger"></i></a>
    </td>
</tr>
@endforeach

<script>
    $(function() {

        function loadBpbj() {
            var kode_produk = $("#kode_produk").val();
            $("#loadbpbj").load('/bpbj/' + kode_produk + '/showtemp');
            cekbpbjtemp();
        }

        function cekbpbjtemp() {
            var kode_produk = $("#kode_produk").val();
            $.ajax({
                type: 'POST'
                , url: '/bpbj/cekbpbjtemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_produk: kode_produk
                }
                , cache: false
                , success: function(respond) {
                    $("#cekbpbjtemp").val(respond);
                }
            });
        }
        $(".hapus").click(function(e) {
            e.preventDefault();
            var kode_produk = $(this).attr("kode_produk");
            var shift = $(this).attr("shift");
            event.preventDefault();
            swal({
                    title: `Anda Yakin Data ini Akan Dihapus ?`
                    , text: "Jika dihapus Data Ini Akan Hilang "
                    , icon: "warning"
                    , buttons: true
                    , dangerMode: true
                , })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type: 'POST'
                            , url: '/bpbj/deletetemp'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , kode_produk: kode_produk
                                , shift: shift
                            }
                            , cache: false
                            , success: function(respond) {
                                if (respond == 0) {
                                    swal(
                                        'Deleted!'
                                        , 'Data Berhasil Dihapus'
                                        , 'success'
                                    )
                                    loadBpbj();
                                } else {
                                    swal(
                                        'Deleted!'
                                        , 'Data Gagal Dihapus'
                                        , 'danger'
                                    )
                                }
                            }
                        });
                    }
                });
        });
    });

</script>
