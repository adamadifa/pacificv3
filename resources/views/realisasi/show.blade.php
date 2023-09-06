@foreach ($realisasi as $d)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $d->kode_cabang }}</td>
        <td>{{ $d->im }}</td>
        <td>{{ $d->ajuan }}</td>
        <td>{{ $d->keterangan }}</td>
        <td>{{ $d->nama_pelanggan }}</td>
        <td>{{ number_format($d->nominal) }}</td>
        <td>{{ $d->bentuk_hadiah }}</td>
        <td>
            <a class="ml-1 edit" href="#" id="{{ $d->id }}"><i class="feather icon-edit success"></i></a>
            <a class="ml-1 hapus" href="#" id="{{ $d->id }}"><i class="feather icon-trash danger"></i></a>
        </td>
    </tr>
@endforeach

<script>
    $(function() {

        function loadrealisasi() {
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            $.ajax({
                type: 'POST',
                url: '{{ route('realisasi.show') }}',
                data: {
                    _token: "{{ csrf_token() }}",
                    bulan: bulan,
                    tahun: tahun
                },
                cache: false,
                success: function(respond) {
                    $("#loadrealisasi").html(respond);
                }
            });
        }

        $(".hapus").click(function(e) {
            e.preventDefault();
            var id = $(this).attr("id");
            event.preventDefault();
            swal({
                    title: `Anda Yakin Data ini Akan Dihapus ?`,
                    text: "Jika dihapus Data Ini Akan Hilang Dari Keranjang",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type: 'POST',
                            url: '{{ route('realisasi.delete') }}',
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: id
                            },
                            cache: false,
                            success: function(respond) {
                                if (respond == 0) {
                                    swal(
                                        'Deleted!', 'Data Berhasil Dihapus',
                                        'success'
                                    )

                                    loadrealisasi();
                                } else {
                                    swal(
                                        'Failed!', 'Data Gagal Dihapus', 'danger'
                                    )
                                }

                            }
                        });
                    }
                });
        });

        $(".edit").click(function(e) {
            var id = $(this).attr("id");
            $.ajax({
                type: 'POST',
                url: '/realisasi/edit',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                cache: false,
                success: function(respond) {
                    $('#mdleditrealisasi').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $("#loadeditrealisasi").html(respond);

                }
            });
        });



    });
</script>
