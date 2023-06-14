@foreach ($gantishift as $d)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->nik }}</td>
    <td>{{ $d->nama_karyawan }}</td>
    <td>{{ $d->nama_group }}</td>
    <td>{{ $d->nama_jadwal }}</td>
    <td>
        <a href="#" class="hapus" kode_gs="{{ $d->kode_gs }}" kode_setjadwal="{{ $d->kode_setjadwal }}"><i class="feather icon-trash danger"></i></a>
    </td>
</tr>
@endforeach
<script>
    $(function() {

        function showgantishift(kode_setjadwal) {
            $("#showgantishift").load('/konfigurasijadwal/' + kode_setjadwal + '/showgantishift');
        }

        $(".hapus").click(function(e) {
            e.preventDefault();
            var kode_gs = $(this).attr('kode_gs');
            var kode_setjadwal = $(this).attr('kode_setjadwal');
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
                            , url: '/konfigurasijadwal/deletegantishift'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , kode_gs: kode_gs
                            }
                            , cache: false
                            , success: function(respond) {
                                if (respond == 0) {
                                    swal(
                                        'Deleted!'
                                        , 'Data Berhasil Dihapus'
                                        , 'success'
                                    )
                                    showgantishift(kode_setjadwal);
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
