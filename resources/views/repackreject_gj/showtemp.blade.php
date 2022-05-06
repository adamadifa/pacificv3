@foreach ($detailtemp as $d)
<tr>
    <td>{{$loop->iteration}}</td>
    <td>{{$d->kode_produk}}</td>
    <td>{{ucwords(strtolower($d->nama_barang))}}</td>
    <td class="text-right">{{rupiah($d->jumlah)}}</td>
    <td>
        <a href="#" class="hapus" kode_produk="{{$d->kode_produk}}" jenis_mutasi="{{$d->jenis_mutasi}}"><i class="feather icon-trash danger"></i></a>
    </td>
</tr>
@endforeach
<script>
    $(function() {

        function cektemp() {
            var jenis_mutasi = $("#jenis_mutasi").val();
            $.ajax({
                type: 'POST'
                , url: "/repackrejectgj/" + jenis_mutasi + "/cektemp"
                , data: {
                    _token: "{{ csrf_token() }}"

                }
                , cache: false
                , success: function(respond) {
                    $("#cektemp").val(respond);
                }
            });
        }

        function loadrepackreject() {
            var jenis_mutasi = $("#jenis_mutasi").val();
            $("#loadrepackreject").load("/repackrejectgj/" + jenis_mutasi + "/showtemp");
            cektemp();
        }


        $(".hapus").click(function(e) {
            e.preventDefault();
            var kode_produk = $(this).attr("kode_produk");
            var jenis_mutasi = $(this).attr("jenis_mutasi");
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
                            , url: '/repackrejectgj/deletetemp'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , kode_produk: kode_produk
                                , jenis_mutasi: jenis_mutasi
                            }
                            , cache: false
                            , success: function(respond) {
                                if (respond == 0) {
                                    swal(
                                        'Deleted!'
                                        , 'Data Berhasil Dihapus'
                                        , 'success'
                                    )
                                    loadrepackreject();
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
