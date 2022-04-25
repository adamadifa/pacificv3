@foreach ($detail as $d)
<tr>
    <td>{{ $d->kode_produk }}</td>
    <td>{{ $d->nama_barang }}</td>
    <td>{{ $d->shift }}</td>
    <td class="text-right">{{ rupiah($d->jumlah) }}</td>
    <td>
        <a href="#" kode_produk="{{ $d->kode_produk }}" shift="{{ $d->shift }}" unit="{{ $d->unit }}" class="hapus"><i class="feather icon-trash danger"></i></a>
    </td>
</tr>
@endforeach

<script>
    $(function() {

        function loadFsthp() {
            var kode_produk = $("#kode_produk").val();
            var unit = $("#unit").val();
            var shift = $("#shift").val();
            $("#loadfsthp").load('/fsthp/' + kode_produk + '/' + unit + '/' + shift + '/showtemp');
            cekfsthptemp();
        }

        function cekfsthptemp() {
            var kode_produk = $("#kode_produk").val();
            var unit = $("#unit").val();
            var shift = $("#shift").val();
            $.ajax({
                type: 'POST'
                , url: '/fsthp/cekfsthptemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_produk: kode_produk
                    , unit: unit
                    , shift: shift
                }
                , cache: false
                , success: function(respond) {
                    $("#cekfsthptemp").val(respond);
                }
            });
        }


        $(".hapus").click(function(e) {
            e.preventDefault();
            var kode_produk = $(this).attr("kode_produk");
            var shift = $(this).attr("shift");
            var unit = $(this).attr("unit");
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
                            , url: '/fsthp/deletetemp'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , kode_produk: kode_produk
                                , shift: shift
                                , unit: unit
                            }
                            , cache: false
                            , success: function(respond) {
                                if (respond == 0) {
                                    swal(
                                        'Deleted!'
                                        , 'Data Berhasil Dihapus'
                                        , 'success'
                                    )
                                    loadFsthp();
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
