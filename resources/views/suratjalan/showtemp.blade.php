@foreach ($detail as $d)
<tr>
    <td>{{ $d->kode_produk }}</td>
    <td>{{ ucwords(strtolower($d->nama_barang)) }}</td>
    <td class="text-right" style="font-weight: bold">{{ $d->jumlah }}</td>
    <td>
        <a href="#" class="hapus" no_permintaan_pengiriman="{{ $d->no_permintaan_pengiriman }}" kode_produk="{{ $d->kode_produk }}"><i class="feather icon-trash danger"></i></a>
    </td>
</tr>
@endforeach

<script>
    $(function() {

        function cektemp() {
            var no_permintaan_pengiriman = $("#no_permintaan_pengiriman_sj").val();
            $.ajax({
                type: 'POST'
                , url: '/suratjalan/cektemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_permintaan_pengiriman: no_permintaan_pengiriman

                }
                , cache: false
                , success: function(respond) {
                    $("#cektempsj").val(respond);
                }
            });
        }

        function loadrealisasipermintaan() {
            var no_permintaan_pengiriman = $("#no_permintaan_pengiriman_sj").val();
            $("#loadrealisasipermintaan").load("/suratjalan/" + no_permintaan_pengiriman + "/showtemp");
            cektemp();
        }
        $(".hapus").click(function(e) {
            e.preventDefault();
            var kode_produk = $(this).attr("kode_produk");
            var no_permintaan_pengriman = $(this).attr("no_permintaan_pengiriman");
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
                            , url: '/suratjalan/deletetemp'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , no_permintaan_pengiriman: no_permintaan_pengriman
                                , kode_produk: kode_produk
                            }
                            , cache: false
                            , success: function(respond) {
                                if (respond == 0) {
                                    swal(
                                        'Deleted!'
                                        , 'Data Berhasil Dihapus'
                                        , 'success'
                                    )
                                    loadrealisasipermintaan();
                                } else {
                                    swal(
                                        'Deleted!'
                                        , 'Data Gagal Dihapus'
                                        , 'error'
                                    )
                                }
                            }
                        });
                    }
                });
        });
    });

</script>
