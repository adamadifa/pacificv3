@foreach ($bbm as $d)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $d->tanggal }}</td>
        <td>{{ $d->no_polisi }}</td>
        <td>{{ $d->nama_driver_helper }}</td>
        <td>{{ $d->tujuan }}</td>
        <td align="right">{{ number_format($d->saldo_awal) }}</td>
        <td align="right">{{ number_format($d->saldo_akhir) }}</td>
        <td align="right">{{ number_format($d->jumlah_liter, 2) }}</td>
        <td>{{ number_format($d->saldo_akhir - $d->saldo_awal, 2) }}</td>
        <td>{{ number_format(($d->saldo_akhir - $d->saldo_awal) / $d->jumlah_liter, 2) }}</td>
        <td>{{ $d->keterangan }}</td>
        <td>{{ $d->kode_cabang }}</td>
        <td>
            <a class="ml-1 edit" href="#" id="{{ $d->id }}"><i class="feather icon-edit success"></i></a>
            <a class="ml-1 hapus" href="#" id="{{ $d->id }}"><i class="feather icon-trash danger"></i></a>
        </td>
    </tr>
@endforeach

<script>
    $(function() {

        function loadbbm() {
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            $.ajax({
                type: 'POST',
                url: '{{ route('bbm.show') }}',
                data: {
                    _token: "{{ csrf_token() }}",
                    bulan: bulan,
                    tahun: tahun
                },
                cache: false,
                success: function(respond) {
                    $("#loadbbm").html(respond);
                }
            });
        }

        $(".hapus").click(function(e) {
            e.preventDefault();
            var id = $(this).attr("id");
            event.preventDefault();
            swal({
                    title: 'Anda Yakin Data ini Akan Dihapus ?',
                    text: "Jika dihapus Data Ini Akan Hilang Dari Keranjang",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type: 'POST',
                            url: '{{ route('bbm.delete') }}',
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
                                    loadbbm();
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
                url: '/bbm/edit',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                cache: false,
                success: function(respond) {
                    $('#mdleditbbm').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $("#loadeditbbm").html(respond);

                }
            });
        });

    });
</script>
