@foreach ($visit as $d)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $d->kode_cabang }}</td>
        <td>{{ date('d-m-Y', strtotime($d->tgl_visit)) }}</td>
        <td>{{ $d->nama_pelanggan }}</td>
        <td>{{ $d->nama_karyawan }}</td>
        <td>{{ $d->pasar }}</td>
        <td>{{ date('d-m-Y', strtotime($d->tgltransaksi)) }}</td>
        <td>{{ $d->no_fak_penj }}</td>
        <td>{{ number_format($d->nominal) }}</td>
        <td>{{ $d->jenistransaksi }}</td>
        <td>
            <a class="ml-1 edit" href="#" id="{{ $d->id }}"><i class="feather icon-edit success"></i></a>
            <a class="ml-1 hapus" href="#" id="{{ $d->id }}"><i class="feather icon-trash danger"></i></a>
        </td>
    </tr>
@endforeach

<script>
    $(function() {
        function loadvisit() {
            var tanggal = $("#tanggal").val();
            $.ajax({
                type: 'POST',
                url: '{{ route('visit.show') }}',
                data: {
                    _token: "{{ csrf_token() }}",
                    tanggal: tanggal
                },
                cache: false,
                success: function(respond) {
                    $("#loadvisit").html(respond);
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
                            url: '{{ route('visit.delete') }}',
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

                                    loadvisit();
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
                url: '/visit/edit',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                cache: false,
                success: function(respond) {
                    $('#mdleditvisit').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $("#loadeditvisit").html(respond);

                }
            });
        });



    });
</script>
