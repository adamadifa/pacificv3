@foreach ($peserta as $d)
    @php
        $sisa = $program->jml_target - $d->jmldus;
    @endphp
    <tr>
        <td>{{ $d->kode_pelanggan }}</td>
        <td>{{ $d->nama_pelanggan }}</td>
        <td>{{ $d->kode_cabang }}</td>
        <td>{{ $d->nama_karyawan }}</td>
        <td style="text-align: center">{{ rupiah($d->jmldus) }}</td>
        <td style="text-align: center">{{ $sisa > 0 ? rupiah($sisa) : 0 }}</td>
        <td>
            <a href="#" kode_program="{{ $d->kode_program }}" kode_pelanggan="{{ $d->kode_pelanggan }}"
                class="hapus">
                <i class="feather icon-trash danger"></i>
            </a>
        </td>
    </tr>
@endforeach
<script>
    $(function() {
        function loadpeserta() {
            var kode_program = "{{ $program->kode_program }}";
            $("#loadpeserta").load('/worksheetom/' + kode_program + '/getpeserta');
        }

        $(".hapus").click(function(e) {
            e.preventDefault();
            var kode_program = $(this).attr('kode_program');
            var kode_pelanggan = $(this).attr("kode_pelanggan");

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
                            url: '/worksheetom/deletepeserta',
                            data: {
                                _token: "{{ csrf_token() }}",
                                kode_program: kode_program,
                                kode_pelanggan: kode_pelanggan
                            },
                            cache: false,
                            success: function(respond) {
                                if (respond == 0) {
                                    swal('Deleted!', 'Data Berhasil Dihapus', 'success')
                                } else {
                                    swal('Oops!', 'Data Gagal Dihapus', 'error')
                                }
                                loadpeserta();
                            }
                        });
                    }
                });
        });
    });
</script>
