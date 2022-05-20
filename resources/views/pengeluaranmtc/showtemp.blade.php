@foreach ($detail as $d)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->kode_barang }}</td>
    <td>{{ $d->nama_barang }}</td>
    <td>{{ $d->keterangan }}</td>
    <td class="text-right">{{ desimal($d->qty) }}</td>
    <td>
        <a href="#" kode_barang="{{ $d->kode_barang }}" class="hapus"><i class="feather icon-trash danger"></i></a>
    </td>
</tr>
@endforeach
<script>
    $(function() {

        function cektemp() {
            $.ajax({
                type: 'POST'
                , url: '/pengeluaranmaintenance/cektemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                }
                , cache: false
                , success: function(respond) {
                    $("#cektemp").val(respond);
                }
            });
        }

        function loaddetail() {
            $("#loaddetailpengeluaran").load("/pengeluaranmaintenance/showtemp");
            cektemp();
        }

        $(".hapus").click(function(e) {
            e.preventDefault();
            var kode_barang = $(this).attr("kode_barang");
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
                            , url: '/pengeluaranmaintenance/deletetemp'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , kode_barang: kode_barang
                            }
                            , cache: false
                            , success: function(respond) {
                                if (respond == 0) {
                                    swal(
                                        'Deleted!'
                                        , 'Data Berhasil Dihapus'
                                        , 'success'
                                    )
                                    loaddetail();
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
