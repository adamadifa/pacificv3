@foreach ($kb as $d)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->keterangan }}</td>
    <td class="text-right">{{ rupiah($d->jumlah) }}</td>
    <td>
        <a href="#" class="danger hapus" no_kb="{{ $d->no_kb }}" no_urut="{{ $d->no_urut }}"><i class="feather icon-trash"></i></a>
    </td>
</tr>
@endforeach
<script>
    $(function() {
        function loadpotongan() {
            var no_kb = "{{ $no_kb }}";
            $.ajax({
                type: 'POST'
                , url: '/kesepakatanbersama/getpotongan'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_kb: no_kb
                }
                , cache: false
                , success: function(respond) {
                    $("#loadpotongan").html(respond);
                }
            });
        }
        $(".hapus").click(function(e) {
            e.preventDefault();
            var no_kb = $(this).attr("no_kb");
            var no_urut = $(this).attr("no_urut");
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
                            , url: '/kesepakatanbersama/deletepotongan'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , no_kb: no_kb
                                , no_urut: no_urut
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
                                        'Error!'
                                        , 'Data Gagal Dihapus'
                                        , 'error'
                                    )
                                }

                                loadpotongan();
                            }
                        });
                    }
                });
        });
    });

</script>
